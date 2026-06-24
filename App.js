import React, { useState, useEffect, useRef } from 'react';
import { Alert, AppState, Modal, View, Text, ActivityIndicator } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import HomeScreen from './src/screens/HomeScreen';
import NewEmaScreen from './src/screens/NewEmaScreen';
import DetailsEmaScreen from './src/screens/DetailsEmaScreen';
import WifiConfigScreen from './src/screens/WifiConfigScreen';
import InitialConfigScreen from './src/screens/InitialConfigScreen';
import { triggerBatteryAlert, resetBatteryNotificationFlag } from './src/services/notificationService';
import { startBackgroundBle, stopBackgroundBle } from './src/services/backgroundBleService';
import * as Notifications from 'expo-notifications';
import { requestBluetoothPermissions, requestNotificationPermissions, setupNotificationChannel } from './src/services/permissions';
import {
  sendWifiCredentials,
  subscribeToMicaData,
  changeOperatingMode,
  changeWifiState,
  sendStartCommand,
  connectToDevice,
  manager
} from './src/services/bluetoothService';

import useWifiScanner from './src/services/useWifiScanner';

Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true, // Esto obliga a que aparezca aunque la app esté abierta
    shouldPlaySound: true,
    shouldSetBadge: false,
  }),
});

export default function App() {
  const [currentScreen, setCurrentScreen] = useState('home');
  const currentScreenRef = useRef('home');
  const [selectedDevice, setSelectedDevice] = useState(null);
  const [wifiOrigin, setWifiOrigin] = useState('new_ema');
  const hasNotifiedRef = useRef(false);
  const isExpectingDisconnectRef = useRef(false);
  const [showEmaWaitingOverlay, setShowEmaWaitingOverlay] = useState(false);
  const isWaitingForTelemetryRef = useRef(false);
  const expectingWifiRef = useRef(false);
  const initialConfigTimeoutRef = useRef(null);

  // EFECTO: Inicialización y solicitud de permisos al arrancar la app
  useEffect(() => {
    const initPermissions = async () => {
      await requestBluetoothPermissions();
      await requestNotificationPermissions();
    };
    initPermissions();
    return () => {
      if (initialConfigTimeoutRef.current) {
        clearTimeout(initialConfigTimeoutRef.current);
      }
    };
  }, []);

  useEffect(() => {
    const subscription = AppState.addEventListener(
      'change',
      async (nextState) => {
        if (nextState !== 'active') {
          return;
        }

        if (!selectedDevice?.rawDevice) {
          return;
        }

        const hasNotificationPerms =
          await requestNotificationPermissions();

        if (!hasNotificationPerms) {
          return;
        }

        startBackgroundBle();
      }
    );

    return () => subscription.remove();
  }, [selectedDevice]);

  useEffect(() => {
    currentScreenRef.current = currentScreen;
  }, [currentScreen]);

  const [refreshTrigger, setRefreshTrigger] = useState(0);

  // Estado de telemetría global en tiempo real
  const [telemetry, setTelemetry] = useState({
    battery: null,
    mode: 0,
    wifi: 0,
    ssid: 'Desconectado'
  });

  // EFECTO: Monitoreo del nivel crítico de batería con histéresis
  useEffect(() => {
    const level = telemetry?.battery;

    if (level === null || level === undefined) {
      return;
    }

    const numericLevel = Number(level);
    const alertThreshold = 15;
    const recoveryThreshold = alertThreshold + 5; // Histéresis: requiere subir más del límite para recuperar

    if (!isNaN(numericLevel) && numericLevel > 0) {
      if (numericLevel <= alertThreshold) {
        if (!hasNotifiedRef.current) {
          console.log("App.js disparando alerta global");
          triggerBatteryAlert(numericLevel);
          hasNotifiedRef.current = true;
        }
      } else if (numericLevel > recoveryThreshold) {
        // Histéresis: Solo reseteamos el flag si sube significativamente del umbral
        if (hasNotifiedRef.current) {
          console.log("Batería recuperada, reseteando flag");
          hasNotifiedRef.current = false;
          resetBatteryNotificationFlag();
        }
      }
    }
  }, [telemetry.battery]);

  // Callback de emergencia si fallan los permisos dentro del módulo de Wi-Fi
  const handlePermissionFallback = () => {
    setCurrentScreen(wifiOrigin === 'details' ? 'details' : 'new_ema');
  };

  // INVOCACIÓN DEL MODULO DE WI-FI
  const { cellphoneNetworks, loadingWifi } = useWifiScanner(currentScreen, handlePermissionFallback);

  // EFECTO: Suscripción automática a telemetría al conectar/seleccionar un dispositivo
  useEffect(() => {
    let telemetrySubscription = null;
    let disconnectSubscription = null;

    if (selectedDevice && selectedDevice.rawDevice) {
      console.log(`App.js: Iniciando monitoreo BLE y listeners para: ${selectedDevice.name}`);

      // Suscribirse al canal de notificaciones de telemetría
      telemetrySubscription = subscribeToMicaData(
        selectedDevice.rawDevice,
        (data) => {
          console.log("App.js: Telemetría en tiempo real recibida ->", data);

          if (isWaitingForTelemetryRef.current) {
            // Si esperamos Wi-Fi, comprobamos si la telemetría ya reporta conexión activa (wifi === 1)
            // de lo contrario, seguimos esperando a que se complete la conexión o se agote el tiempo de espera.
            const hasWifi = Number(data.wifi) === 1;
            const shouldNavigate = !expectingWifiRef.current || hasWifi;

            if (shouldNavigate) {
              console.log("App.js: Condiciones de telemetría de configuración inicial cumplidas. Ocultando overlay...");
              if (initialConfigTimeoutRef.current) {
                clearTimeout(initialConfigTimeoutRef.current);
                initialConfigTimeoutRef.current = null;
              }
              isWaitingForTelemetryRef.current = false;
              expectingWifiRef.current = false;
              isExpectingDisconnectRef.current = false;
              setShowEmaWaitingOverlay(false);
              setCurrentScreen('details');
            }
          }

          // Si el dispositivo reporta que no está configurado, forzar redirección
          if (data.configured === 0 && currentScreenRef.current !== 'initial_config') {
            console.log("App.js: El MICA no está configurado. Redirigiendo a InitialConfigScreen...");
            setCurrentScreen('initial_config');
          }

          setTelemetry({
            battery: data.battery,
            mode: data.mode,
            wifi: data.wifi,
            ssid: data.ssid || (data.wifi ? 'Conectado' : 'Desconectado'),
            configured: data.configured
          });
        },
        (error) => {
          console.error("App.js: Error en receptor de telemetría BLE:", error);
        }
      );

      // Suscribirse al evento de desconexión no deseada
      disconnectSubscription = manager.onDeviceDisconnected(
        selectedDevice.id,
        (error, device) => {
          console.warn("App.js: Dispositivo BLE desconectado físicamente.");

          // 🚀 SEGUNDO PLANO: Frenamos el servicio nativo dado que ya no hay hardware vinculado
          console.log("App.js: Deteniendo Foreground Service por desconexión física.");
          stopBackgroundBle();

          if (initialConfigTimeoutRef.current) {
            clearTimeout(initialConfigTimeoutRef.current);
            initialConfigTimeoutRef.current = null;
          }
          if (isExpectingDisconnectRef.current) {
            console.log("App.js: Desconexión esperada por configuración/cambio de Wi-Fi.");
            isExpectingDisconnectRef.current = false;
            setShowEmaWaitingOverlay(false);
            isWaitingForTelemetryRef.current = false;
            expectingWifiRef.current = false;
            Alert.alert(
              "Aplicando Parámetros",
              "El MICA se ha desconectado temporalmente para aplicar la configuración y conectarse al Wi-Fi. Por favor, vuelve a conectarlo en unos momentos desde el inicio."
            );
          } else {
            if (isWaitingForTelemetryRef.current) {
              setShowEmaWaitingOverlay(false);
              isWaitingForTelemetryRef.current = false;
              expectingWifiRef.current = false;
            }
            Alert.alert(
              "Conexión Perdida",
              `Se ha interrumpido la conexión Bluetooth con ${selectedDevice.name || 'el EMA'}.`
            );
          }
          setSelectedDevice(null);
          setCurrentScreen('home');
        }
      );
    } else {
      // Reiniciar telemetría si no hay dispositivo seleccionado
      setTelemetry({
        battery: null,
        mode: 0,
        wifi: 0,
        ssid: 'Desconectado'
      });
    }

    return () => {
      if (telemetrySubscription) {
        console.log("App.js: Limpiando suscripción de telemetría BLE.");
        telemetrySubscription.remove();
      }
      if (disconnectSubscription) {
        console.log("App.js: Limpiando listener de desconexión BLE.");
        disconnectSubscription.remove();
      }
    };
  }, [selectedDevice]);

  const handleBluetoothConnected = async (device) => {
    try {
      const stored = await AsyncStorage.getItem('LINKED_DEVICES');
      const list = stored ? JSON.parse(stored) : [];
      if (!list.includes(device.id)) {
        list.push(device.id);
        await AsyncStorage.setItem('LINKED_DEVICES', JSON.stringify(list));
      }
    } catch (e) {
      console.warn("App.js: Error saving linked device ID:", e);
    }
    setSelectedDevice(device);
    setWifiOrigin('new_ema');
    setCurrentScreen('initial_config');
  };

  const handleSelectDevice = async (device) => {
    try {
      if (!device || !device.rawDevice) {
        throw new Error("Dispositivo no válido.");
      }

      console.log(`App.js: Validando conexión para dispositivo seleccionado: ${device.name}`);
      const rawDeviceInstance = device.rawDevice;
      const isConnected = await rawDeviceInstance.isConnected();

      let activeDevice = rawDeviceInstance;
      if (!isConnected) {
        console.log("App.js: Dispositivo no conectado de forma activa. Conectando BLE...");
        activeDevice = await connectToDevice(rawDeviceInstance);
      } else {
        console.log("App.js: Dispositivo ya conectado. Asegurando servicios y características...");

        try {
          console.log("App.js: Solicitando MTU de 512 bytes preventivo...");
          await rawDeviceInstance.requestMTU(512);
          console.log("App.js: MTU negociado correctamente.");
        } catch (mtuErr) {
          console.warn("App.js: No se pudo negociar MTU (normal en iOS/algunos dispositivos):", mtuErr.message);
        }

        await rawDeviceInstance.discoverAllServicesAndCharacteristics();
      }

      const mappedDevice = {
        id: activeDevice.id,
        name: activeDevice.name || device.name,
        type: 'Estación BLE',
        initial: (activeDevice.name || device.name).charAt(0).toUpperCase(),
        rawDevice: activeDevice
      };

      try {
        const stored = await AsyncStorage.getItem('LINKED_DEVICES');
        const list = stored ? JSON.parse(stored) : [];
        if (!list.includes(mappedDevice.id)) {
          list.push(mappedDevice.id);
          await AsyncStorage.setItem('LINKED_DEVICES', JSON.stringify(list));
        }
      } catch (e) {
        console.warn("App.js: Error saving linked device ID on select:", e);
      }

      setSelectedDevice(mappedDevice);
      const hasNotificationPerms =
        await requestNotificationPermissions();

      if (!hasNotificationPerms) {
        console.warn(
          "App.js: Sin permisos de notificaciones."
        );
      } else {
        console.log(
          "App.js: Activando Foreground Service tras selección exitosa del dispositivo."
        );

        startBackgroundBle();
      }

      setCurrentScreen('details');
    } catch (error) {
      console.error("App.js: Error al seleccionar y conectar el dispositivo ->", error);
      Alert.alert(
        'Error de Conexión',
        'No se pudo comunicar por BLE con el dispositivo MICA. Asegúrese de que esté encendido y dentro del rango de señal.'
      );
    }
  };

  const handleWifiConfigured = async (ssid, password, ssid2 = null, password2 = null) => {
    try {
      const rawDeviceInstance = selectedDevice?.rawDevice;
      if (!rawDeviceInstance) {
        throw new Error("No hay dispositivo conectado.");
      }

      console.log(`App.js: Enviando configuración Wi-Fi desde Detalles. SSID: ${ssid}`);

      // Activar el overlay de espera
      setShowEmaWaitingOverlay(true);
      isWaitingForTelemetryRef.current = true;
      expectingWifiRef.current = true;
      isExpectingDisconnectRef.current = true;

      // Limpiar cualquier timeout previo por seguridad
      if (initialConfigTimeoutRef.current) {
        clearTimeout(initialConfigTimeoutRef.current);
      }

      // Configurar timeout de 20s
      initialConfigTimeoutRef.current = setTimeout(() => {
        if (isWaitingForTelemetryRef.current) {
          console.log("App.js: Timeout de espera de configuración Wi-Fi alcanzado.");
          isWaitingForTelemetryRef.current = false;
          expectingWifiRef.current = false;
          isExpectingDisconnectRef.current = false;
          setShowEmaWaitingOverlay(false);
          Alert.alert(
            "Configuración Enviada",
            "La configuración de red se envió correctamente."
          );
          setCurrentScreen('details');
        }
      }, 20000);

      await sendWifiCredentials(rawDeviceInstance, ssid, password, ssid2, password2);

      // Actualizar estado de telemetría local de forma optimista
      setTelemetry(prev => ({
        ...prev,
        wifi: 1,
        ssid: ssid
      }));

      setRefreshTrigger(prev => prev + 1);
    } catch (error) {
      if (initialConfigTimeoutRef.current) {
        clearTimeout(initialConfigTimeoutRef.current);
        initialConfigTimeoutRef.current = null;
      }
      setShowEmaWaitingOverlay(false);
      isWaitingForTelemetryRef.current = false;
      expectingWifiRef.current = false;
      isExpectingDisconnectRef.current = false;
      console.error("App.js: Error en handleWifiConfigured ->", error);
      Alert.alert('Error', error.message || 'No se pudieron enviar las credenciales.');
    }
  };

  const handleDeleteDevice = async () => {
    if (!selectedDevice) return;

    Alert.alert(
      "Olvidar EMA",
      "¿Estás seguro de que deseas olvidar este dispositivo EMA? Se desconectará y se eliminará de la lista de dispositivos vinculados.",
      [
        { text: "Cancelar", style: "cancel" },
        {
          text: "Aceptar",
          style: "destructive",
          onPress: async () => {
            try {
              const deviceIdToDelete = selectedDevice.id;
              console.log(`App.js: Eliminando dispositivo EMA: ${deviceIdToDelete}`);

              // 1. Detener segundo plano si corresponde
              stopBackgroundBle();

              // 2. Desconectar físicamente el dispositivo BLE
              if (selectedDevice.rawDevice) {
                try {
                  const isConnected = await selectedDevice.rawDevice.isConnected();
                  if (isConnected) {
                    await selectedDevice.rawDevice.cancelConnection();
                    console.log("App.js: Conexión cancelada físicamente.");
                  }
                } catch (connErr) {
                  console.warn("App.js: Error al intentar desconectar físicamente:", connErr.message);
                }
              }

              // 3. Eliminar de la lista de dispositivos vinculados en AsyncStorage
              const stored = await AsyncStorage.getItem('LINKED_DEVICES');
              if (stored) {
                const list = JSON.parse(stored);
                const updatedList = list.filter(id => id !== deviceIdToDelete);
                await AsyncStorage.setItem('LINKED_DEVICES', JSON.stringify(updatedList));
                console.log("App.js: Dispositivo eliminado de AsyncStorage.");
              }

              // 4. Limpiar estados y volver a home
              setSelectedDevice(null);
              setRefreshTrigger(prev => prev + 1);
              setCurrentScreen('home');
            } catch (err) {
              console.error("App.js: Error al olvidar el dispositivo:", err);
              Alert.alert("Error", "No se pudo olvidar el dispositivo correctamente.");
            }
          }
        }
      ]
    );
  };

  const handleChangeOperatingMode = async (modeCode) => {
    try {
      const rawDeviceInstance = selectedDevice?.rawDevice;
      await changeOperatingMode(rawDeviceInstance, modeCode);
      setTelemetry(prev => ({ ...prev, mode: parseInt(modeCode, 10) }));
    } catch (error) {
      Alert.alert('Error de Configuración', error.message || 'No se pudo cambiar el modo del EMA.');
    }
  };

  const handleChangeWifiState = async (enabled) => {
    try {
      const rawDeviceInstance = selectedDevice?.rawDevice;
      await changeWifiState(rawDeviceInstance, enabled);
      setTelemetry(prev => ({
        ...prev,
        wifi: enabled ? 1 : 0,
        ssid: enabled ? 'Conectando...' : 'Desconectado'
      }));
    } catch (error) {
      Alert.alert('Error de Configuración', error.message || 'No se pudo cambiar el estado de WiFi del EMA.');
    }
  };

  const handleSendInitialConfig = async (config) => {
    try {
      const rawDeviceInstance = selectedDevice?.rawDevice;
      if (!rawDeviceInstance) {
        throw new Error("No hay dispositivo conectado.");
      }

      console.log(`App.js: Enviando configuración inicial. Modo: ${config.mode}, WiFi: ${config.wifiEnabled}`);

      // Activar el overlay de espera
      setShowEmaWaitingOverlay(true);
      isWaitingForTelemetryRef.current = true;
      expectingWifiRef.current = !!config.wifiEnabled;

      // Limpiar cualquier timeout previo por seguridad
      if (initialConfigTimeoutRef.current) {
        clearTimeout(initialConfigTimeoutRef.current);
      }

      // Si configuramos WiFi, ponemos un timeout de 20s. Si no, 10s es suficiente para recibir la primera telemetría.
      const timeoutLimit = config.wifiEnabled ? 20000 : 10000;
      initialConfigTimeoutRef.current = setTimeout(() => {
        if (isWaitingForTelemetryRef.current) {
          console.log("App.js: Timeout de espera de configuración inicial alcanzado.");
          isWaitingForTelemetryRef.current = false;
          expectingWifiRef.current = false;
          setShowEmaWaitingOverlay(false);
          Alert.alert(
            "EMA Configurado",
            "La configuración fue enviada exitosamente. Accediendo a los detalles."
          );
          setCurrentScreen('details');
        }
      }, timeoutLimit);

      // 1. Enviar modo de operación
      await changeOperatingMode(rawDeviceInstance, config.mode.toString());

      // 2. Enviar WiFi (credenciales o desactivado)
      if (config.wifiEnabled) {
        isExpectingDisconnectRef.current = true;
        await sendWifiCredentials(rawDeviceInstance, config.ssid, config.password, config.ssid2, config.password2);
      } else {
        await changeWifiState(rawDeviceInstance, false); // Enviar WIFI:OFF
      }

      // 3. Enviar confirmación START
      await sendStartCommand(rawDeviceInstance);

      // 4. Actualizar estado de telemetría local de forma optimista
      setTelemetry({
        battery: null,
        mode: config.mode,
        wifi: config.wifiEnabled ? 1 : 0,
        ssid: config.wifiEnabled ? config.ssid : 'Desconectado'
      });

      const hasNotificationPerms =
        await requestNotificationPermissions();

      if (!hasNotificationPerms) {
        console.warn(
          "App.js: Sin permisos de notificaciones."
        );
      } else {
        console.log(
          "App.js: Activando Foreground Service tras selección exitosa del dispositivo."
        );

        startBackgroundBle();
      }

      // NOTA: No hacemos Alert.alert ni redirección inmediata a 'details' aquí.
      // Esperamos que la telemetría empiece a fluir (en el callback de telemetría) 
      // o que el MICA se desconecte intencionalmente si el WiFi se activó.

    } catch (error) {
      if (initialConfigTimeoutRef.current) {
        clearTimeout(initialConfigTimeoutRef.current);
        initialConfigTimeoutRef.current = null;
      }
      setShowEmaWaitingOverlay(false);
      isWaitingForTelemetryRef.current = false;
      expectingWifiRef.current = false;
      isExpectingDisconnectRef.current = false;
      console.error("App.js: Error en handleSendInitialConfig ->", error);
      Alert.alert('Error', error.message || 'No se pudo enviar la configuración inicial.');
    }
  };

  // NAVEGACIÓN Y RENDERIZADO DE PANTALLAS
  return (
    <View style={{ flex: 1 }}>
      {(() => {
        if (currentScreen === 'new_ema') {
          return (
            <NewEmaScreen
              onBack={() => setCurrentScreen('home')}
              onConnectionSuccess={handleBluetoothConnected}
            />
          );
        }

        if (currentScreen === 'initial_config') {
          return (
            <InitialConfigScreen
              onBack={() => setCurrentScreen('new_ema')}
              onSendConfig={handleSendInitialConfig}
              networks={cellphoneNetworks}
              isLoadingNetworks={loadingWifi}
            />
          );
        }

        if (currentScreen === 'wifi_config') {
          return (
            <WifiConfigScreen
              networks={cellphoneNetworks}
              isLoadingNetworks={loadingWifi}
              onBack={() => {
                setCurrentScreen(wifiOrigin === 'details' ? 'details' : 'new_ema');
              }}
              onConnectAction={async (ssid, password) => {
                await handleWifiConfigured(ssid, password);
              }}
            />
          );
        }

        if (currentScreen === 'details') {
          return (
            <DetailsEmaScreen
              device={selectedDevice}
              telemetry={telemetry}
              onChangeMode={handleChangeOperatingMode}
              onChangeWifiState={handleChangeWifiState}
              onBack={() => {
                setSelectedDevice(null);
                setCurrentScreen('home');
              }}
              onConfigWifi={() => {
                setWifiOrigin('details');
                setCurrentScreen('wifi_config');
              }}
              onDeleteDevice={handleDeleteDevice}
            />
          );
        }

        return (
          <HomeScreen
            onNavigateToNewEma={() => setCurrentScreen('new_ema')}
            activeTrigger={refreshTrigger}
            onSelectDevice={handleSelectDevice}
          />
        );
      })()}

      <Modal
        transparent={true}
        visible={showEmaWaitingOverlay || (currentScreen === 'details' && (telemetry.battery === null || telemetry.battery === undefined || telemetry.battery === 0))}
        animationType="fade"
        onRequestClose={() => { }}
      >
        <View style={{
          flex: 1,
          backgroundColor: 'rgba(15, 23, 42, 0.8)',
          justifyContent: 'center',
          alignItems: 'center',
          padding: 24
        }}>
          <View style={{
            backgroundColor: '#ffffff',
            borderRadius: 28,
            padding: 32,
            alignItems: 'center',
            shadowColor: '#000000',
            shadowOffset: { width: 0, height: 10 },
            shadowOpacity: 0.25,
            shadowRadius: 15,
            elevation: 10,
            width: '100%',
            maxWidth: 320
          }}>
            <ActivityIndicator size="large" color="#3b82f6" style={{ marginBottom: 24 }} />
            <Text style={{
              fontSize: 18,
              fontWeight: '900',
              color: '#0f172a',
              textAlign: 'center',
              marginBottom: 12,
              letterSpacing: 0.5
            }}>
              {currentScreen === 'details' && (telemetry.battery === null || telemetry.battery === undefined || telemetry.battery === 0)
                ? "Enviando credenciales de Wi-Fi al EMA"
                : "En espera del EMA"}
            </Text>
            <Text style={{
              fontSize: 14,
              fontWeight: '700',
              color: '#ef4444',
              textAlign: 'center',
              textTransform: 'uppercase',
              letterSpacing: 1
            }}>
              no desconectes
            </Text>
          </View>
        </View>
      </Modal>
    </View>
  );
}