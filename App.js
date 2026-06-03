import React, { useState, useEffect, useRef } from 'react';
import { Alert } from 'react-native';
import HomeScreen from './src/screens/HomeScreen';
import NewEmaScreen from './src/screens/NewEmaScreen';
import DetailsEmaScreen from './src/screens/DetailsEmaScreen';
import WifiConfigScreen from './src/screens/WifiConfigScreen';
import InitialConfigScreen from './src/screens/InitialConfigScreen';
import { 
  sendWifiCredentials, 
  subscribeToMicaData, 
  changeOperatingMode, 
  changeWifiState,
  sendStartCommand,
  connectToDevice, 
  manager 
} from './src/services/bluetoothService';

// IMPORTACIÓN DEL NUEVO HOOK MODULAR
import useWifiScanner from './src/services/useWifiScanner';

export default function App() {
  const [currentScreen, setCurrentScreen] = useState('home');
  const currentScreenRef = useRef('home');
  const [selectedDevice, setSelectedDevice] = useState(null);
  const [wifiOrigin, setWifiOrigin] = useState('new_ema'); 

  // Sincronizar el ref con la pantalla actual
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

  // Callback de emergencia si fallan los permisos dentro del módulo
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
          Alert.alert(
            "Conexión Perdida",
            `Se ha interrumpido la conexión Bluetooth con ${selectedDevice.name || 'el EMA'}.`
          );
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

  const handleBluetoothConnected = (device) => {
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
        
        // Solicitar preventivamente MTU de 512 bytes por si acaso
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
      
      setSelectedDevice(mappedDevice);
      setCurrentScreen('details');
    } catch (error) {
      console.error("App.js: Error al seleccionar y conectar el dispositivo ->", error);
      Alert.alert(
        'Error de Conexión',
        'No se pudo comunicar por BLE con el dispositivo MICA. Asegúrese de que esté encendido y dentro del rango de señal.'
      );
    }
  };

  const handleWifiConfigured = async (ssid, password) => {
    try {
      const rawDeviceInstance = selectedDevice?.rawDevice;
      await sendWifiCredentials(rawDeviceInstance, ssid, password);
      
      Alert.alert('Éxito', '¡Credenciales de Wi-Fi enviadas correctamente al MICA!');
      setRefreshTrigger(prev => prev + 1);
      // Redirigir a detalles para monitorear la conexión en tiempo real
      setCurrentScreen('details');
    } catch (error) {
      Alert.alert('Error', error.message || 'No se pudieron enviar las credenciales.');
    }
  };

  const handleChangeOperatingMode = async (modeCode) => {
    try {
      const rawDeviceInstance = selectedDevice?.rawDevice;
      await changeOperatingMode(rawDeviceInstance, modeCode);
      // Actualizamos el modo de manera optimista en la UI mientras MICA procesa e informa
      setTelemetry(prev => ({ ...prev, mode: parseInt(modeCode, 10) }));
    } catch (error) {
      Alert.alert('Error de Configuración', error.message || 'No se pudo cambiar el modo del EMA.');
    }
  };

  const handleChangeWifiState = async (enabled) => {
    try {
      const rawDeviceInstance = selectedDevice?.rawDevice;
      await changeWifiState(rawDeviceInstance, enabled);
      // Actualizamos el estado de wifi de manera optimista en la UI
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
      
      // 1. Enviar modo de operación
      await changeOperatingMode(rawDeviceInstance, config.mode.toString());
      
      // 2. Enviar WiFi (credenciales o desactivado)
      if (config.wifiEnabled) {
        await sendWifiCredentials(rawDeviceInstance, config.ssid, config.password);
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
      
      Alert.alert('Éxito', '¡Configuración inicial enviada correctamente al MICA!');
      // Redirigir a Detalles EMA
      setCurrentScreen('details');
    } catch (error) {
      console.error("App.js: Error en handleSendInitialConfig ->", error);
      Alert.alert('Error', error.message || 'No se pudo enviar la configuración inicial.');
    }
  };

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
}