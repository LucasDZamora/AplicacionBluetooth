import React, { useState, useEffect, useRef } from 'react';
import { View, Text, TouchableOpacity, Animated, FlatList, Alert, ActivityIndicator } from 'react-native';
import { scanForDevices, stopScanning, connectToDevice } from '../services/bluetoothService';
import { MaterialCommunityIcons, Feather } from '@expo/vector-icons';

const getSignalPercentage = (rssi) => {
  if (!rssi) return 0;
  if (rssi >= -40) return 100;
  if (rssi <= -100) return 0;
  return Math.round(((rssi - (-100)) / (-40 - (-100))) * 100);
};

const getSignalIconConfig = (percentage) => {
  if (percentage <= 30) {
    return { icon: "signal-cellular-1", color: "#ef4444" };
  } else if (percentage <= 70) {
    return { icon: "signal-cellular-2", color: "#f59e0b" };
  } else {
    return { icon: "signal-cellular-3", color: "#10b981" };
  }
};

export default function NewEmaScreen({ onBack, onConnectionSuccess }) {
  const [scanState, setScanState] = useState('idle');
  const [devices, setDevices] = useState([]);
  const [connectingId, setConnectingId] = useState(null);
  const fadeAnim = useRef(new Animated.Value(0.3)).current;
  const scanTimeoutRef = useRef(null);

  useEffect(() => {
    if (scanState === 'scanning') {
      Animated.loop(
        Animated.sequence([
          Animated.timing(fadeAnim, { toValue: 0.8, duration: 1000, useNativeDriver: true }),
          Animated.timing(fadeAnim, { toValue: 0.3, duration: 1000, useNativeDriver: true }),
        ])
      ).start();

      setDevices([]);
      executeDeviceDiscovery();
    } else {
      fadeAnim.setValue(1);
      stopScanning();
      if (scanTimeoutRef.current) {
        clearTimeout(scanTimeoutRef.current);
      }
    }
    return () => {
      stopScanning();
      if (scanTimeoutRef.current) {
        clearTimeout(scanTimeoutRef.current);
      }
    };
  }, [scanState]);

  const executeDeviceDiscovery = () => {
    try {
      console.log("App: Iniciando escaneo de dispositivos MICA por BLE...");
      
      scanForDevices(
        (device) => {
          setDevices(prev => {
            if (prev.some(d => d.id === device.id)) return prev;
            return [...prev, device];
          });
        },
        (error) => {
          console.error("App: Error escaneando BLE:", error);
          Alert.alert('Error', 'Hubo un problema al buscar dispositivos BLE.');
          setScanState('idle');
        }
      );

      scanTimeoutRef.current = setTimeout(() => {
        console.log("App: Escaneo finalizado automáticamente.");
        stopScanning();
        setScanState('results');
      }, 6000);

    } catch (error) {
      console.error("Error al iniciar escaneo de dispositivos:", error);
      Alert.alert('Error', 'Hubo un problema al buscar dispositivos Bluetooth.');
      setScanState('idle');
    }
  };

  const handleConnect = async (rawDevice) => {
    try {
      setConnectingId(rawDevice.id);
      console.log(`App: Intentando establecer conexión BLE con: ${rawDevice.name} (${rawDevice.id})`);
      
      const connectedDevice = await connectToDevice(rawDevice);
      if (connectedDevice) {
        console.log("App: ¡Conectado a MICA por BLE exitosamente! (ID: " + connectedDevice.id + ")");
        
        const name = connectedDevice.name || 'Estación MICA';
        const mappedDevice = {
          id: connectedDevice.id,
          name: name,
          type: 'Estación BLE',
          initial: name.charAt(0).toUpperCase(),
          rawDevice: connectedDevice
        };

        onConnectionSuccess(mappedDevice);
      }
    } catch (err) {
      console.error("App: Fallo de conexión BLE ->", err);
      Alert.alert('Error de conexión', err.message || 'No se pudo conectar al dispositivo.');
    } finally {
      setConnectingId(null);
    }
  };

  const handleBack = () => {
    if (scanState === 'scanning' || scanState === 'results') {
      setScanState('idle');
    } else {
      onBack();
    }
  };

  return (
    <View style={{ flex: 1, backgroundColor: '#f8fafc', paddingHorizontal: 24, paddingTop: 60 }}>
      
      <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 40 }}>
        <TouchableOpacity 
          onPress={handleBack}
          style={{
            width: 44,
            height: 44,
            borderRadius: 22,
            backgroundColor: '#ffffff',
            justifyContent: 'center',
            alignItems: 'center',
            borderWidth: 1,
            borderColor: '#e2e8f0',
            marginRight: 16
          }}
        >
          <Feather 
            name="chevron-left" 
            size={24} 
            color="#1e293b" 
          />
        </TouchableOpacity>
        <Text style={{ fontSize: 22, fontWeight: '900', color: '#0f172a', fontStyle: 'italic' }}>
          NUEVA EMA
        </Text>
      </View>

      {scanState === 'idle' && (
        <>
          <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', paddingBottom: 100 }}>
            <View style={{ width: 180, height: 180, borderRadius: 48, backgroundColor: '#eff6ff', justifyContent: 'center', alignItems: 'center', marginBottom: 40 }}>
              <MaterialCommunityIcons 
                name="bluetooth" 
                size={90}       
                color="#3b82f6" 
              />
            </View>
            <Text style={{ fontSize: 24, fontWeight: '900', color: '#0f172a', fontStyle: 'italic', marginBottom: 16 }}>
              BÚSQUEDA BLUETOOTH
            </Text>
            <Text style={{ fontSize: 15, color: '#64748b', textAlign: 'center', lineHeight: 22, paddingHorizontal: 16 }}>
              Asegúrate de que tu estación EMA esté encendida y dentro del rango de alcance antes de iniciar el escaneo.
            </Text>
          </View>

          <View style={{ paddingBottom: 40 }}>
            <TouchableOpacity 
              onPress={() => setScanState('scanning')}
              style={{ backgroundColor: '#3b82f6', borderRadius: 20, paddingVertical: 18, alignItems: 'center' }}
            >
              <Text style={{ color: 'white', fontSize: 15, fontWeight: '700' }}>INICIAR ESCANEO</Text>
            </TouchableOpacity>
          </View>
        </>
      )}

      {scanState === 'scanning' && (
        <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', paddingBottom: 160 }}>
          <Animated.View style={{ width: 180, height: 180, borderRadius: 48, backgroundColor: '#eff6ff', justifyContent: 'center', alignItems: 'center', marginBottom: 40, opacity: fadeAnim }}>
            <MaterialCommunityIcons name="bluetooth-transfer" size={90} color="#3b82f6" />
          </Animated.View>
          <Text style={{ fontSize: 16, fontWeight: '700', color: '#64748b', letterSpacing: 0.8, textTransform: 'uppercase' }}>
            ESCANEANDO DISPOSITIVOS...
          </Text>
        </View>
      )}

      {scanState === 'results' && (
        <FlatList
          data={devices || []}
          keyExtractor={(item) => item.id}
          contentContainerStyle={{ paddingBottom: 40 }}
          ListHeaderComponent={
            <Text style={{ fontSize: 13, color: '#0f172a', fontWeight: '700', marginBottom: 20, textTransform: 'uppercase' }}>
              Dispositivos No Vinculados
            </Text>
          }
          renderItem={({ item }) => (
            <TouchableOpacity 
              onPress={() => handleConnect(item)}
              disabled={connectingId !== null}
              style={{
                backgroundColor: '#ffffff',
                borderRadius: 24,
                padding: 16,
                marginBottom: 14,
                flexDirection: 'row',
                alignItems: 'center',
                justifyContent: 'space-between',
                borderWidth: 1,
                borderColor: '#f1f5f9',
              }}
            >
              <View style={{ flexDirection: 'row', alignItems: 'center', flex: 1 }}>
                <View style={{ width: 52, height: 52, borderRadius: 16, backgroundColor: '#3b82f6', justifyContent: 'center', alignItems: 'center', marginRight: 16 }}>
                  <MaterialCommunityIcons name="bluetooth" size={26} color="white" />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={{ fontSize: 16, fontWeight: '700', color: '#0f172a' }}>{item.name || 'Dispositivo desconocido'}</Text>
                  <Text style={{ fontSize: 12, color: '#94a3b8', marginTop: 4 }}>{item.id}</Text>
                </View>
              </View>

              <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                {connectingId === item.id ? (
                  <ActivityIndicator size="small" color="#3b82f6" />
                ) : (
                  (() => {
                    const hasSignal = item.rssi !== undefined && item.rssi !== null;
                    const percentage = hasSignal ? getSignalPercentage(item.rssi) : 0;
                    const config = hasSignal 
                      ? getSignalIconConfig(percentage)
                      : { icon: "signal-cellular-outline", color: "#94a3b8" };

                    return (
                      <>
                        <MaterialCommunityIcons 
                          name={config.icon} 
                          size={18} 
                          color={config.color} 
                          style={{ marginRight: 6 }} 
                        />
                        <Text style={{ fontSize: 13, color: config.color, fontWeight: '700' }}>
                          {hasSignal ? `${percentage}%` : '--'}
                        </Text>
                      </>
                    );
                  })()
                )}
              </View>
            </TouchableOpacity>
          )}
          ListEmptyComponent={
            <Text style={{ textAlign: 'center', color: '#64748b', marginTop: 40, fontSize: 15, paddingHorizontal: 16, lineHeight: 22 }}>
              No se encontraron estaciones Bluetooth nuevas en el entorno o todas las cercanas ya se encuentran vinculadas.
            </Text>
          }
          ListFooterComponent={
            <TouchableOpacity 
              onPress={() => setScanState('scanning')}
              style={{ backgroundColor: '#f1f5f9', borderRadius: 20, paddingVertical: 18, alignItems: 'center', marginTop: 10 }}
            >
              <Text style={{ color: '#0f172a', fontSize: 14, fontWeight: '700', textTransform: 'uppercase' }}>
                Escanear Nuevamente
              </Text>
            </TouchableOpacity>
          }
        />
      )}

    </View>
  );
}