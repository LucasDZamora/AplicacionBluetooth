import React, { useState, useEffect } from 'react';
import { View, Text, TouchableOpacity, FlatList, Dimensions } from 'react-native';
import RNBluetoothClassic from 'react-native-bluetooth-classic';

const { width } = Dimensions.get('window');

export default function HomeScreen({ onNavigateToNewEma, activeTrigger, onSelectDevice }) {
  const [devices, setDevices] = useState([]);

  // Recargar la lista cuando el componente se monta o cuando cambia activeTrigger (al volver de escanear)
  useEffect(() => {
    fetchBluetoothDevices();
  }, [activeTrigger]);

  const fetchBluetoothDevices = async () => {
    try {
      // 1. Obtener los que ya tienen una conexión RFCOMM abierta
      const connected = await RNBluetoothClassic.getConnectedDevices();
      
      // 2. Obtener los que están vinculados/emparejados en el sistema Android
      const bonded = await RNBluetoothClassic.getBondedDevices();
      
      // Combinar ambas listas
      const allDevices = [...connected, ...bonded];
      
      // Eliminar duplicados por dirección MAC (address)
      const uniqueDevices = Array.from(new Set(allDevices.map(d => d.address)))
        .map(address => allDevices.find(d => d.address === address));

      // Filtrar para dejar únicamente los dispositivos que contengan "MICA" en su nombre
      const micaDevices = uniqueDevices.filter(device => {
        const name = device.name || '';
        return name.toLowerCase().includes('mica');
      });

      // Mapear los datos reales al diseño visual estilizado
      const mapped = micaDevices.map(device => {
        const name = device.name || 'Estación MICA';
        return {
          id: device.address,
          name: name,
          type: 'Estación Bluetooth',
          initial: name.charAt(0).toUpperCase(),
          battery: null, // Se puede implementar más adelante con la lectura de características
          rawDevice: device
        };
      });

      setDevices(mapped);
    } catch (e) {
      console.error("Error cargando dispositivos en Home:", e);
    }
  };

  return (
    <View style={{ flex: 1, backgroundColor: '#f8fafc' }}>
      
      {/* HEADER */}
      <View style={{ paddingTop: 60, paddingHorizontal: 24, marginBottom: 24 }}>
        <Text style={{ fontSize: 26, fontWeight: '900', color: '#1e293b', fontStyle: 'italic' }}>
          MIS ESTACIONES <Text style={{ color: '#3b82f6' }}>EMA</Text>
        </Text>
        <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '800', marginTop: 4, letterSpacing: 1 }}>
          PANEL EDUCATIVO
        </Text>
      </View>

      {/* BIENVENIDA / CARD GLOBAL */}
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

      {/* LISTADO DE ESTACIONES */}
      <FlatList
        data={devices}
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
          <TouchableOpacity 
            onPress={() => onSelectDevice && onSelectDevice(item)}
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
            <View style={{ flexDirection: 'row', alignItems: 'center', flex: 1 }}>
              {/* Avatar Circular con Inicial */}
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

              {/* Textos del Dispositivo */}
              <View style={{ flex: 1, paddingRight: 8 }}>
                <Text style={{ fontSize: 16, fontWeight: '700', color: '#0f172a' }}>{item.name}</Text>
                <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '500', marginTop: 4 }}>
                  {item.type}  •  {item.id}
                </Text>
              </View>
            </View>

            {/* Botón de opciones */}
            <TouchableOpacity style={{ padding: 8 }}>
              <Text style={{ fontSize: 18, color: '#0f172a', fontWeight: '900' }}>•••</Text>
            </TouchableOpacity>
          </TouchableOpacity>
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