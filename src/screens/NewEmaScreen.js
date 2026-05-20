import React, { useState, useEffect, useRef } from 'react';
import { View, Text, TouchableOpacity, Animated, FlatList, Alert, ActivityIndicator } from 'react-native';
import RNBluetoothClassic from 'react-native-bluetooth-classic';
import { scanForDevices, connectToDevice } from '../services/bluetoothService';

export default function NewEmaScreen({ onBack, onConnectionSuccess }) {
  const [scanState, setScanState] = useState('idle'); // 'idle', 'scanning', 'results'
  const [devices, setDevices] = useState([]);
  const [connectingId, setConnectingId] = useState(null);
  const fadeAnim = useRef(new Animated.Value(0.3)).current;

  useEffect(() => {
    if (scanState === 'scanning') {
      // Animación intermitente/bucle mientras se escanea
      Animated.loop(
        Animated.sequence([
          Animated.timing(fadeAnim, { toValue: 0.8, duration: 1000, useNativeDriver: true }),
          Animated.timing(fadeAnim, { toValue: 0.3, duration: 1000, useNativeDriver: true }),
        ])
      ).start();

      executeDeviceDiscovery();
    } else {
      fadeAnim.setValue(1);
    }
  }, [scanState]);

  const executeDeviceDiscovery = async () => {
    try {
      // 1. Descubrir dispositivos Bluetooth en el entorno
      const discovered = await scanForDevices();
      
      // 2. Obtener los dispositivos que ya están vinculados/emparejados en el teléfono
      const bonded = await RNBluetoothClassic.getBondedDevices();
      const bondedAddresses = bonded.map(d => d.address);

      // 3. Filtrar para dejar ÚNICAMENTE los dispositivos que NO están vinculados
      const nonBondedDevices = discovered.filter(device => !bondedAddresses.includes(device.address));

      setDevices(nonBondedDevices);
      setScanState('results');
    } catch (error) {
      console.error("Error al escanear dispositivos:", error);
      Alert.alert('Error', 'Hubo un problema al buscar dispositivos Bluetooth.');
      setScanState('idle');
    }
  };

  const handleConnect = async (rawDevice) => {
    try {
      setConnectingId(rawDevice.address);
      const success = await connectToDevice(rawDevice);
      if (success) {
        // Mapeamos el dispositivo al formato de la interfaz visual
        const name = rawDevice.name || 'Estación MICA';
        const mappedDevice = {
          id: rawDevice.address,
          name: name,
          type: 'Estación Bluetooth',
          initial: name.charAt(0).toUpperCase(),
          rawDevice: rawDevice
        };

        // Redirige automáticamente a la pantalla de configuración de Wi-Fi
        onConnectionSuccess(mappedDevice);
      }
    } catch (err) {
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
      
      {/* HEADER */}
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
          <Text style={{ fontSize: 18, color: '#1e293b', fontWeight: 'bold' }}>←</Text>
        </TouchableOpacity>
        <Text style={{ fontSize: 22, fontWeight: '900', color: '#0f172a', fontStyle: 'italic' }}>
          NUEVA EMA
        </Text>
      </View>

      {/* ESTADO INICIAL (IDLE) */}
      {scanState === 'idle' && (
        <>
          <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', paddingBottom: 100 }}>
            <View style={{ width: 180, height: 180, borderRadius: 48, backgroundColor: '#eff6ff', justifyContent: 'center', alignItems: 'center', marginBottom: 40 }}>
              <Text style={{ fontSize: 80, color: '#3b82f6', fontWeight: '300' }}>ᛒ</Text>
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

      {/* ESTADO ESCANEANDO (SCANNING) */}
      {scanState === 'scanning' && (
        <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', paddingBottom: 160 }}>
          <Animated.View style={{ width: 180, height: 180, borderRadius: 48, backgroundColor: '#eff6ff', justifyContent: 'center', alignItems: 'center', marginBottom: 40, opacity: fadeAnim }}>
            <Text style={{ fontSize: 80, color: '#3b82f6', fontWeight: '300' }}>ᛒ</Text>
          </Animated.View>
          <Text style={{ fontSize: 16, fontWeight: '700', color: '#64748b', letterSpacing: 0.8, textTransform: 'uppercase' }}>
            ESCANEANDO DISPOSITIVOS...
          </Text>
        </View>
      )}

      {/* ESTADO RESULTADOS (RESULTS) */}
      {scanState === 'results' && (
        <FlatList
          data={devices}
          keyExtractor={(item) => item.address}
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
                  <Text style={{ color: 'white', fontSize: 24 }}>ᛒ</Text>
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={{ fontSize: 16, fontWeight: '700', color: '#0f172a' }}>{item.name || 'Dispositivo desconocido'}</Text>
                  <Text style={{ fontSize: 12, color: '#94a3b8', marginTop: 4 }}>{item.address}</Text>
                </View>
              </View>

              <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                {connectingId === item.address ? (
                  <ActivityIndicator size="small" color="#3b82f6" />
                ) : (
                  <>
                    <Text style={{ color: '#10b981', fontSize: 16, marginRight: 6 }}>📶</Text>
                    <Text style={{ fontSize: 13, color: '#10b981', fontWeight: '700' }}>100%</Text>
                  </>
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