import React, { useState, useEffect, useRef } from 'react';
import { View, Text, TouchableOpacity, FlatList, Dimensions, Alert, Image } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { manager, SERVICE_UUID, scanForDevices, stopScanning } from '../services/bluetoothService';
import { requestBluetoothPermissions } from '../services/permissions'; // Importamos tu servicio de permisos
import logoMica from '../../assets/logo_01_mica.png';

const { width } = Dimensions.get('window');

export default function HomeScreen({ onNavigateToNewEma, activeTrigger, onSelectDevice, onTestBackgroundService }) {
  const [devices, setDevices] = useState([]);
  const [hasPermissions, setHasPermissions] = useState(false);
  const scanTimeoutRef = useRef(null);

  // 1. Solicitar permisos al montar el Home por primera vez
  useEffect(() => {
    checkAndRequestPermissions();
    return () => {
      stopScanning(); // Limpieza al desmontar
      if (scanTimeoutRef.current) {
        clearTimeout(scanTimeoutRef.current);
      }
    };
  }, []);

  // 2. Recargar los dispositivos cuando cambie el trigger o cuando se otorguen los permisos
  useEffect(() => {
    if (hasPermissions) {
      fetchBluetoothDevices();
    }
  }, [activeTrigger, hasPermissions]);

  const checkAndRequestPermissions = async () => {
    try {
      const granted = await requestBluetoothPermissions();
      if (granted) {
        setHasPermissions(true);
      } else {
        Alert.alert(
          'Permisos requeridos',
          'La aplicación necesita permisos de Bluetooth y Ubicación para detectar tus estaciones MICA.'
        );
      }
    } catch (error) {
      console.error("Error al solicitar permisos en Home:", error);
    }
  };

  const fetchBluetoothDevices = async () => {
    try {
      console.log("Home: Obteniendo dispositivos BLE conectados...");
      
      // Detener cualquier escaneo previo y limpiar timeout
      stopScanning();
      if (scanTimeoutRef.current) {
        clearTimeout(scanTimeoutRef.current);
      }
      
      // Obtener la lista de dispositivos vinculados desde AsyncStorage
      const stored = await AsyncStorage.getItem('LINKED_DEVICES');
      const linkedIds = stored ? JSON.parse(stored) : [];
      
      // 1. Dispositivos ya conectados por nuestra app (filtrados por vinculación)
      const connected = await manager.connectedDevices([SERVICE_UUID]);
      const mappedConnected = connected
        .filter(device => linkedIds.includes(device.id))
        .map(device => {
          const name = device.name || 'Estación MICA';
          return {
            id: device.id,
            name: name,
            type: 'MICA (Conectada)',
            initial: name.charAt(0).toUpperCase(),
            battery: null,
            rawDevice: device
          };
        });

      setDevices(mappedConnected);

      // 2. Escanear por 3 segundos para descubrir otros MICA cercanos activos (filtrados por vinculación)
      scanForDevices(
        (device) => {
          if (linkedIds.includes(device.id)) {
            setDevices(prev => {
              if (prev.some(d => d.id === device.id)) return prev;
              const name = device.name || 'Estación MICA';
              return [
                ...prev,
                {
                  id: device.id,
                  name: name,
                  type: 'MICA (BLE)',
                  initial: name.charAt(0).toUpperCase(),
                  battery: null,
                  rawDevice: device
                }
              ];
            });
          }
        },
        (error) => {
          console.log("Home: Error no crítico escaneando:", error.message);
        }
      );

      // Detener escaneo tras 3 segundos
      scanTimeoutRef.current = setTimeout(() => {
        stopScanning();
      }, 3000);

    } catch (e) {
      console.error("Error cargando dispositivos en Home:", e);
    }
  };

  const handleForgetPrompt = (item) => {
    Alert.alert(
      'Olvidar Estación',
      `¿Estás seguro de que deseas olvidar la estación "${item.name}"? Se cerrará la conexión Bluetooth y se eliminarán de forma segura las credenciales y registros guardados localmente en el móvil.`,
      [
        { text: 'Cancelar', style: 'cancel' },
        { 
          text: 'Olvidar', 
          style: 'destructive', 
          onPress: () => handleForgetDevice(item) 
        }
      ]
    );
  };

  const handleForgetDevice = async (item) => {
    try {
      console.log(`Home: Olvidando estación ${item.name} (${item.id})...`);
      
      // 0. Detener cualquier escaneo activo inmediatamente
      stopScanning();
      if (scanTimeoutRef.current) {
        clearTimeout(scanTimeoutRef.current);
      }

      // 1. Cancelar la conexión BLE de forma activa
      try {
        await manager.cancelDeviceConnection(item.id);
        console.log("Home: Conexión BLE cerrada para la estación.");
      } catch (bleErr) {
        console.log("Home: La estación ya estaba desconectada o no se pudo cerrar:", bleErr.message);
      }

      // 2. Eliminar el dispositivo de LINKED_DEVICES en AsyncStorage
      const stored = await AsyncStorage.getItem('LINKED_DEVICES');
      if (stored) {
        let linkedIds = JSON.parse(stored);
        linkedIds = linkedIds.filter(id => id !== item.id);
        await AsyncStorage.setItem('LINKED_DEVICES', JSON.stringify(linkedIds));
        console.log("Home: Estación eliminada de LINKED_DEVICES en AsyncStorage.");
      }

      // 3. Quitar el dispositivo inmediatamente del estado local
      setDevices(prev => prev.filter(d => d.id !== item.id));

      Alert.alert(
        'Estación Olvidada',
        `La estación "${item.name}" ha sido olvidada con éxito. Sus credenciales y registros locales han sido eliminados.`
      );

      // 4. Esperar un breve momento y refrescar para sincronizar
      setTimeout(() => {
        fetchBluetoothDevices();
      }, 500);

    } catch (error) {
      console.error("Home: Error al olvidar la estación:", error);
      Alert.alert('Error', 'No se pudo olvidar la estación correctamente.');
    }
  };

  return (
    <View style={{ flex: 1, backgroundColor: '#f8fafc' }}>
      
      {/* HEADER */}
      <View style={{ paddingTop: 60, paddingHorizontal: 24, marginBottom: 24 }}>

        <Image 
          source={logoMica} 
          style={{ 
            width: 140, 
            height: 38, 
            resizeMode: 'contain', 
            alignSelf: 'center' // <--- Esto lo centra perfectamente en el eje horizontal
          }} 
        />

        <Text style={{ fontSize: 26, fontWeight: '900', color: '#1e293b', fontStyle: 'italic' }}>
          MIS ESTACIONES <Text style={{ color: '#3b82f6' }}>EMA</Text>
        </Text>
        <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '800', marginTop: 4, letterSpacing: 1 }}>
          PANEL EDUCATIVO
        </Text>
      </View>

      {/* BIENVENIDA */}
      <View style={{ paddingHorizontal: 24, marginBottom: 32 }}>
        <View style={{
          backgroundColor: '#0f172a',
          borderRadius: 28,
          paddingVertical: 36,
          paddingHorizontal: 24,
          alignItems: 'center',
          justifyContent: 'center',
          shadowColor: '#0f172a',
          shadowOffset: { width: 0, height: 10 },
          shadowOpacity: 0.2,
          shadowRadius: 15,
          elevation: 8,
        }}>

        


          <Text style={{ color: 'white', fontSize: 32, fontWeight: '900', fontStyle: 'italic', letterSpacing: 0.5, marginBottom: 10 }}>
            BIENVENIDO
          </Text>
          <Text style={{ color: '#94a3b8', fontSize: 14, fontWeight: '500', textAlign: 'center', lineHeight: 20 }}>
            Seleccione el EMA a configurar y utilizar abajo
          </Text>
        </View>
      </View>

      {/* LISTADO */}
      <FlatList
        data={devices || []}
        keyExtractor={(item) => item.id}
        contentContainerStyle={{ paddingHorizontal: 24, paddingBottom: 60 }}
        ListHeaderComponent={
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 18 }}>
            <Text style={{ fontSize: 13, color: '#0f172a', fontWeight: '700', letterSpacing: 0.3 }}>
              ESTACIONES VINCULADAS
            </Text>
            <TouchableOpacity 
              onPress={onNavigateToNewEma}
              style={{
                width: 44,
                height: 44,
                borderRadius: 22,
                backgroundColor: '#3b82f6',
                justifyContent: 'center',
                alignItems: 'center',
                shadowColor: '#3b82f6',
                shadowOffset: { width: 0, height: 6 },
                shadowOpacity: 0.3,
                shadowRadius: 8,
                elevation: 4,
              }}
            >
              <Text style={{ color: 'white', fontSize: 24, fontWeight: '400', marginTop: -2 }}>+</Text>
            </TouchableOpacity>
          </View>
        }
        renderItem={({ item }) => (
          <View 
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
              shadowColor: '#94a3b8',
              shadowOffset: { width: 0, height: 4 },
              shadowOpacity: 0.05,
              shadowRadius: 10,
              elevation: 2,
            }}
          >
            <TouchableOpacity 
              onPress={() => onSelectDevice && onSelectDevice(item)}
              style={{ flexDirection: 'row', alignItems: 'center', flex: 1 }}
            >
              <View style={{
                width: 52,
                height: 52,
                borderRadius: 26,
                backgroundColor: '#3b82f6',
                justifyContent: 'center',
                alignItems: 'center',
                marginRight: 16,
              }}>
                <Text style={{ color: 'white', fontSize: 18, fontWeight: '900' }}>{item.initial}</Text>
              </View>

              <View style={{ flex: 1, paddingRight: 8 }}>
                <Text style={{ fontSize: 16, fontWeight: '700', color: '#0f172a' }}>{item.name}</Text>
                <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '500', marginTop: 4 }}>
                  {item.type}  •  {item.id}
                </Text>
              </View>
            </TouchableOpacity>

            <TouchableOpacity 
              onPress={() => handleForgetPrompt(item)}
              style={{ padding: 8 }}
            >
              <Text style={{ fontSize: 18, color: '#64748b', fontWeight: '900' }}>•••</Text>
            </TouchableOpacity>
          </View>
        )}
        ListEmptyComponent={
          <Text style={{ textAlign: 'center', color: '#64748b', marginTop: 30, fontSize: 14, paddingHorizontal: 16, lineHeight: 20 }}>
            No hay estaciones MICA vinculadas en el teléfono. Presiona "+" para buscar y conectar tu EMA por primera vez.
          </Text>
        }
        ListFooterComponent={
          <TouchableOpacity 
            onPress={onNavigateToNewEma}
            style={{
              borderWidth: 1.5,
              borderStyle: 'dashed',
              borderColor: '#cbd5e1',
              borderRadius: 24,
              padding: 20,
              flexDirection: 'row',
              alignItems: 'center',
              justifyContent: 'center',
              marginTop: 8,
              backgroundColor: '#f8fafc'
            }}
          >
            <View style={{ width: 36, height: 36, borderRadius: 18, backgroundColor: '#f1f5f9', justifyContent: 'center', alignItems: 'center', marginRight: 12 }}>
              <Text style={{ color: '#94a3b8', fontSize: 18, fontWeight: '400', marginTop: -2 }}>+</Text>
            </View>
            <Text style={{ color: '#64748b', fontSize: 13, fontWeight: '700', letterSpacing: 0.5 }}>
              AGREGAR NUEVA ESTACIÓN
            </Text>
          </TouchableOpacity>
        }
      />
    </View>
  );
}