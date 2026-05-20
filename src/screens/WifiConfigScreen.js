import React from 'react';
import { View, Text, TouchableOpacity, FlatList } from 'react-native';

export default function WifiConfigScreen({ onBack, onSelectNetwork }) {
  const networks = [
    { id: '1', ssid: 'Laboratorio_Principal', secured: true },
    { id: '2', ssid: 'Invitados_Escuela', secured: false },
    { id: '3', ssid: 'Red_Docentes_5G', secured: true },
    { id: '4', ssid: 'TP-Link_Guest', secured: true },
  ];

  return (
    <View style={{ flex: 1, backgroundColor: '#f8fafc', paddingHorizontal: 24, paddingTop: 60 }}>
      
      {/* HEADER */}
      <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 40 }}>
        <TouchableOpacity 
          onPress={onBack}
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
        <Text style={{ fontSize: 20, fontWeight: '900', color: '#0f172a', fontStyle: 'italic', textTransform: 'uppercase' }}>
          Configurar WIFI
        </Text>
      </View>

      <Text style={{ fontSize: 12, color: '#64748b', fontWeight: '800', letterSpacing: 0.5, marginBottom: 24, textTransform: 'uppercase' }}>
        Redes Disponibles
      </Text>

      <FlatList
        data={networks}
        keyExtractor={(item) => item.id}
        renderItem={({ item }) => (
          <TouchableOpacity 
            onPress={() => onSelectNetwork(item.ssid)}
            style={{
              backgroundColor: '#ffffff',
              borderRadius: 24,
              padding: 16,
              marginBottom: 14,
              flexDirection: 'row',
              alignItems: 'center',
              justifyContent: 'space-between',
              shadowColor: '#94a3b8',
              shadowOffset: { width: 0, height: 4 },
              shadowOpacity: 0.05,
              shadowRadius: 10,
              elevation: 2,
            }}
          >
            <View style={{ flexDirection: 'row', alignItems: 'center' }}>
              <View style={{
                width: 48,
                height: 48,
                borderRadius: 16,
                backgroundColor: '#eff6ff',
                justifyContent: 'center',
                alignItems: 'center',
                marginRight: 16
              }}>
                <Text style={{ fontSize: 20, color: '#3b82f6' }}>📶</Text>
              </View>
              <Text style={{ fontSize: 16, fontWeight: '700', color: '#0f172a' }}>
                {item.ssid}
              </Text>
            </View>

            {item.secured && (
              <Text style={{ fontSize: 16, color: '#cbd5e1' }}>🔒</Text>
            )}
          </TouchableOpacity>
        )}
      />
    </View>
  );
}