import React from 'react';
import { View, Text, TouchableOpacity, ScrollView, Switch } from 'react-native';
import BatteryIndicator from '../components/BatteryIndicator';

// IMPORTACIÓN DE LOS ÍCONOS VECTORIALES DE EXPO
import { MaterialCommunityIcons, FontAwesome5, Feather, Ionicons } from '@expo/vector-icons';


export default function DetailsEmaScreen({ device, telemetry, onChangeMode, onChangeWifiState, onBack, onConfigWifi }) {
  const opMode = telemetry?.mode === 1 ? 'EXPERIMENTO' : 'ESTACIÓN';
  return (
    <ScrollView style={{ flex: 1, backgroundColor: '#f8fafc', paddingHorizontal: 24, paddingTop: 60 }}>
      
      {/* HEADER */}
      <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 32 }}>
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
          <Feather 
            name="chevron-left" 
            size={24} 
            color="#1e293b" 
          />
        </TouchableOpacity>
        <Text style={{ fontSize: 22, fontWeight: '900', color: '#0f172a', fontStyle: 'italic' }}>
          DETALLES EMA
        </Text>
      </View>

      {/* DISPOSITIVO CARD (Sin botón de edición) */}
      <View style={{
        backgroundColor: '#ffffff',
        borderRadius: 28,
        padding: 20,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        borderWidth: 1,
        borderColor: '#f1f5f9',
        shadowColor: '#94a3b8',
        shadowOffset: { width: 0, height: 10 },
        shadowOpacity: 0.05,
        shadowRadius: 15,
        elevation: 2,
        marginBottom: 32
      }}>
        <View style={{ flexDirection: 'row', alignItems: 'center', flex: 1 }}>
          {/* Avatar Circular con Inicial */}
          <View style={{
            width: 64,
            height: 64,
            borderRadius: 20,
            backgroundColor: '#3b82f6',
            justifyContent: 'center',
            alignItems: 'center',
            marginRight: 16,
          }}>
            <Text style={{ color: 'white', fontSize: 22, fontWeight: '900' }}>
              {device?.initial || 'M'}
            </Text>
          </View>

          <View style={{ flex: 1 }}>
            <Text style={{ fontSize: 18, fontWeight: '800', color: '#0f172a' }}>
              {device?.name || 'Estación MICA'}
            </Text>
            
            {/* 2. REEMPLAZO: Solo llamas al componente modular pasando la prop */}
            <BatteryIndicator 
              batteryLevel={telemetry?.battery} 
              deviceId={device?.id} 
            />
            
          </View>

        </View>
      </View>

      {/* SECCIÓN: MODO DE OPERACIÓN */}
      <Text style={{ fontSize: 12, color: '#0f172a', fontWeight: '800', letterSpacing: 0.5, marginBottom: 16, textTransform: 'uppercase' }}>
        Modo de Operación
      </Text>

      <View style={{ flexDirection: 'row', gap: 16, marginBottom: 32 }}>
        
        {/* MODO ESTACIÓN */}
        <TouchableOpacity 
          onPress={() => onChangeMode && onChangeMode('0')}
          style={{
            flex: 1,
            aspectRatio: 1,
            backgroundColor: opMode === 'ESTACIÓN' ? '#3b82f6' : '#ffffff',
            borderRadius: 28,
            padding: 24,
            justifyContent: 'center',
            alignItems: 'center',
            borderWidth: 1,
            borderColor: opMode === 'ESTACIÓN' ? '#3b82f6' : '#f1f5f9',
            shadowColor: '#3b82f6',
            shadowOffset: { width: 0, height: 8 },
            shadowOpacity: opMode === 'ESTACIÓN' ? 0.2 : 0,
            shadowRadius: 12,
            elevation: opMode === 'ESTACIÓN' ? 4 : 0,
          }}
        >
          <View style={{
            width: 48,
            height: 48,
            borderRadius: 16,
            backgroundColor: opMode === 'ESTACIÓN' ? 'rgba(255,255,255,0.2)' : '#eff6ff',
            justifyContent: 'center',
            alignItems: 'center',
            marginBottom: 16
          }}>
            {/* NUEVO ÍCONO VECTORIAL: LAYERS */}
            <MaterialCommunityIcons 
              name="layers-outline" 
              size={24} 
              color={opMode === 'ESTACIÓN' ? '#ffffff' : '#3b82f6'} 
            />
          </View>
          <Text style={{ 
            fontSize: 13, 
            fontWeight: '800', 
            color: opMode === 'ESTACIÓN' ? '#ffffff' : '#0f172a',
            letterSpacing: 0.5
          }}>
            ESTACIÓN
          </Text>
        </TouchableOpacity>

        {/* MODO EXPERIMENTO */}
        <TouchableOpacity 
          onPress={() => onChangeMode && onChangeMode('1')}
          style={{
            flex: 1,
            aspectRatio: 1,
            backgroundColor: opMode === 'EXPERIMENTO' ? '#a855f7' : '#ffffff',
            borderRadius: 28,
            padding: 24,
            justifyContent: 'center',
            alignItems: 'center',
            borderWidth: 1,
            borderColor: opMode === 'EXPERIMENTO' ? '#a855f7' : '#f1f5f9',
            shadowColor: '#a855f7',
            shadowOffset: { width: 0, height: 8 },
            shadowOpacity: opMode === 'EXPERIMENTO' ? 0.2 : 0,
            shadowRadius: 12,
            elevation: opMode === 'EXPERIMENTO' ? 4 : 0,
          }}
        >
          <View style={{
            width: 48,
            height: 48,
            borderRadius: 16,
            backgroundColor: opMode === 'EXPERIMENTO' ? 'rgba(255,255,255,0.2)' : '#f3e8ff',
            justifyContent: 'center',
            alignItems: 'center',
            marginBottom: 16
          }}>
            {/* NUEVO ÍCONO VECTORIAL: FLASK */}
            <FontAwesome5 
              name="flask" 
              size={20} 
              color={opMode === 'EXPERIMENTO' ? '#ffffff' : '#a855f7'} 
            />
          </View>
          <Text style={{ 
            fontSize: 13, 
            fontWeight: '800', 
            color: opMode === 'EXPERIMENTO' ? '#ffffff' : '#0f172a',
            letterSpacing: 0.5
          }}>
            EXPERIMENTO
          </Text>
        </TouchableOpacity>
      </View>

      {/* SECCIÓN: CONECTIVIDAD */}
      <Text style={{ fontSize: 12, color: '#0f172a', fontWeight: '800', letterSpacing: 0.5, marginBottom: 16, textTransform: 'uppercase' }}>
        Conectividad
      </Text>

      <View style={{
        backgroundColor: '#ffffff',
        borderRadius: 24,
        padding: 16,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        borderWidth: 1,
        borderColor: '#f1f5f9',
        marginBottom: 40
      }}>
        <View style={{ flexDirection: 'row', alignItems: 'center', flex: 1 }}>
          <View style={{
            width: 48,
            height: 48,
            borderRadius: 16,
            backgroundColor: telemetry?.wifi ? '#e6f4ea' : '#fbe9e7',
            justifyContent: 'center',
            alignItems: 'center',
            marginRight: 12
          }}>
            <Feather 
              name={telemetry?.wifi ? "wifi" : "wifi-off"} 
              size={24} 
              color={telemetry?.wifi ? '#137333' : '#c53929'} 
            />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={{ fontSize: 10, color: '#94a3b8', fontWeight: '700', textTransform: 'uppercase', letterSpacing: 0.5 }}>
              Red Wi-Fi
            </Text>
            <Text style={{ fontSize: 15, fontWeight: '700', color: '#0f172a', marginTop: 2 }}>
              {telemetry?.ssid || 'Desconectado'}
            </Text>
          </View>
        </View>

        {/* Control switch y engranaje */}
        <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
          <Switch 
            value={!!telemetry?.wifi}
            onValueChange={(value) => onChangeWifiState && onChangeWifiState(value)}
            trackColor={{ false: '#cbd5e1', true: '#93c5fd' }}
            thumbColor={telemetry?.wifi ? '#3b82f6' : '#94a3b8'}
          />
            <TouchableOpacity 
              onPress={onConfigWifi}
              disabled={!telemetry?.wifi}
              style={{
                width: 40,
                height: 40,
                borderRadius: 20,
                backgroundColor: telemetry?.wifi ? '#f1f5f9' : '#f8fafc',
                justifyContent: 'center',
                alignItems: 'center',
                opacity: telemetry?.wifi ? 1 : 0.4
              }}
            >
              <Feather 
                name="settings" 
                size={18} 
                color="#64748b" 
              />
            </TouchableOpacity>
        </View>
      </View>

    </ScrollView>
  );
}