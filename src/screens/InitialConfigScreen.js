import React, { useState } from 'react';
import { View, Text, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert } from 'react-native';
import { MaterialCommunityIcons, Feather } from '@expo/vector-icons';

export default function InitialConfigScreen({ onSendConfig, onBack, networks, isLoadingNetworks }) {
  const [selectedMode, setSelectedMode] = useState(null); // 0 = Estacion, 1 = Experimento
  const [wifiEnabled, setWifiEnabled] = useState(null); // null, true, false
  const [ssid, setSsid] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [sending, setSending] = useState(false);
  const [manualMode, setManualMode] = useState(false);

  const handleSend = async () => {
    if (selectedMode === null) {
      Alert.alert('Atención', 'Por favor, selecciona un modo de operación.');
      return;
    }
    if (wifiEnabled === null) {
      Alert.alert('Atención', 'Por favor, selecciona si deseas activar el WiFi o no.');
      return;
    }
    if (wifiEnabled && !ssid.trim()) {
      Alert.alert('Atención', 'Por favor, ingresa o selecciona una red WiFi.');
      return;
    }

    setSending(true);
    try {
      await onSendConfig({
        mode: selectedMode,
        wifiEnabled: wifiEnabled,
        ssid: wifiEnabled ? ssid : '',
        password: wifiEnabled ? password : '',
      });
    } catch (error) {
      Alert.alert('Error', 'No se pudo enviar la configuración.');
    } finally {
      setSending(false);
    }
  };

  const isFormValid = selectedMode !== null && wifiEnabled !== null && (!wifiEnabled || ssid.trim().length > 0);
  return (
    <ScrollView style={{ flex: 1, backgroundColor: '#f8fafc', paddingHorizontal: 24, paddingTop: 60 }} contentContainerStyle={{ paddingBottom: 120 }}>
      {/* HEADER */}
      <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 28 }}>
        <TouchableOpacity 
          onPress={onBack}
          disabled={sending}
          style={{
            width: 44,
            height: 44,
            borderRadius: 22,
            backgroundColor: '#ffffff',
            justifyContent: 'center',
            alignItems: 'center',
            borderWidth: 1,
            borderColor: '#e2e8f0',
            marginRight: 16,
            opacity: sending ? 0.5 : 1
          }}
        >
          <Feather name="arrow-left" size={24} color="#1e293b" />
        </TouchableOpacity>
        <Text style={{ fontSize: 20, fontWeight: '900', color: '#0f172a', fontStyle: 'italic' }}>
          CONFIGURACIÓN INICIAL
        </Text>
      </View>

      <Text style={{ fontSize: 14, color: '#64748b', marginBottom: 28, lineHeight: 20 }}>
        Conexión establecida. Define los parámetros iniciales del EMA para iniciar el ciclo de mediciones.
      </Text>

      {/* 1. MODO DE OPERACIÓN */}
      <Text style={{ fontSize: 11, color: '#0f172a', fontWeight: '800', letterSpacing: 0.5, marginBottom: 12, textTransform: 'uppercase' }}>
        1. Modo de Operación
      </Text>

      <View style={{ flexDirection: 'row', gap: 14, marginBottom: 28 }}>
        {/* MODO ESTACIÓN */}
        <TouchableOpacity 
          onPress={() => !sending && setSelectedMode(0)}
          disabled={sending}
          style={{
            flex: 1,
            backgroundColor: selectedMode === 0 ? '#3b82f6' : '#ffffff',
            borderRadius: 24,
            padding: 20,
            alignItems: 'center',
            borderWidth: 1,
            borderColor: selectedMode === 0 ? '#3b82f6' : '#e2e8f0',
            shadowColor: '#3b82f6',
            shadowOffset: { width: 0, height: 6 },
            shadowOpacity: selectedMode === 0 ? 0.15 : 0,
            shadowRadius: 8,
            elevation: selectedMode === 0 ? 3 : 0,
          }}
        >
          <View style={{
            width: 42,
            height: 42,
            borderRadius: 14,
            backgroundColor: selectedMode === 0 ? 'rgba(255,255,255,0.2)' : '#eff6ff',
            justifyContent: 'center',
            alignItems: 'center',
            marginBottom: 12
          }}>
            <MaterialCommunityIcons name="layers-outline" size={24} color={selectedMode === 0 ? '#ffffff' : '#3b82f6'} />
          </View>
          <Text style={{ 
            fontSize: 13, 
            fontWeight: '800', 
            color: selectedMode === 0 ? '#ffffff' : '#0f172a',
            letterSpacing: 0.5
          }}>
            ESTACIÓN
          </Text>
        </TouchableOpacity>

        {/* MODO EXPERIMENTO */}
        <TouchableOpacity 
          onPress={() => !sending && setSelectedMode(1)}
          disabled={sending}
          style={{
            flex: 1,
            backgroundColor: selectedMode === 1 ? '#a855f7' : '#ffffff',
            borderRadius: 24,
            padding: 20,
            alignItems: 'center',
            borderWidth: 1,
            borderColor: selectedMode === 1 ? '#a855f7' : '#e2e8f0',
            shadowColor: '#a855f7',
            shadowOffset: { width: 0, height: 6 },
            shadowOpacity: selectedMode === 1 ? 0.15 : 0,
            shadowRadius: 8,
            elevation: selectedMode === 1 ? 3 : 0,
          }}
        >
          <View style={{
            width: 42,
            height: 42,
            borderRadius: 14,
            backgroundColor: selectedMode === 1 ? 'rgba(255,255,255,0.2)' : '#f3e8ff',
            justifyContent: 'center',
            alignItems: 'center',
            marginBottom: 12
          }}>
            <MaterialCommunityIcons name="flask-outline" size={24} color={selectedMode === 1 ? '#ffffff' : '#a855f7'} />
          </View>
          <Text style={{ 
            fontSize: 13, 
            fontWeight: '800', 
            color: selectedMode === 1 ? '#ffffff' : '#0f172a',
            letterSpacing: 0.5
          }}>
            EXPERIMENTO
          </Text>
        </TouchableOpacity>
      </View>

      {/* 2. ¿CONECTAR A WI-FI? */}
      <Text style={{ fontSize: 11, color: '#0f172a', fontWeight: '800', letterSpacing: 0.5, marginBottom: 12, textTransform: 'uppercase' }}>
        2. ¿Conectar a Wi-Fi?
      </Text>

      <View style={{ flexDirection: 'row', gap: 14, marginBottom: 28 }}>
        <TouchableOpacity 
          onPress={() => !sending && setWifiEnabled(true)}
          disabled={sending}
          style={{
            flex: 1,
            backgroundColor: wifiEnabled === true ? '#10b981' : '#ffffff',
            borderRadius: 20,
            paddingVertical: 14,
            alignItems: 'center',
            borderWidth: 1,
            borderColor: wifiEnabled === true ? '#10b981' : '#e2e8f0',
          }}
        >
          <Text style={{ color: wifiEnabled === true ? 'white' : '#0f172a', fontWeight: '700', fontSize: 14 }}>SÍ</Text>
        </TouchableOpacity>

        <TouchableOpacity 
          onPress={() => {
            if (!sending) {
              setWifiEnabled(false);
              setSsid('');
              setPassword('');
              setManualMode(false);
            }
          }}
          disabled={sending}
          style={{
            flex: 1,
            backgroundColor: wifiEnabled === false ? '#ef4444' : '#ffffff',
            borderRadius: 20,
            paddingVertical: 14,
            alignItems: 'center',
            borderWidth: 1,
            borderColor: wifiEnabled === false ? '#ef4444' : '#e2e8f0',
          }}
        >
          <Text style={{ color: wifiEnabled === false ? 'white' : '#0f172a', fontWeight: '700', fontSize: 14 }}>NO</Text>
        </TouchableOpacity>
      </View>

      {/* CREDENCIALES WIFI (SI SE SELECCIONÓ SÍ) */}
      {wifiEnabled === true && (
        <View style={{ marginBottom: 28 }}>
          <Text style={{ fontSize: 11, color: '#0f172a', fontWeight: '800', letterSpacing: 0.5, marginBottom: 12, textTransform: 'uppercase' }}>
            Selecciona una Red WiFi
          </Text>

          {isLoadingNetworks ? (
            <View style={{ padding: 24, backgroundColor: '#ffffff', borderRadius: 24, alignItems: 'center', borderWidth: 1, borderColor: '#e2e8f0', marginBottom: 16 }}>
              <ActivityIndicator size="large" color="#3b82f6" style={{ marginBottom: 16 }} />
              <Text style={{ fontSize: 14, color: '#94a3b8', fontWeight: '700', letterSpacing: 0.5 }}>
                BUSCANDO REDES WIFI...
              </Text>
            </View>
          ) : (
            <View style={{ maxHeight: 240, marginBottom: 16 }}>
              <ScrollView nestedScrollEnabled={true} style={{ borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 24, backgroundColor: '#ffffff', padding: 12 }}>
                {networks && networks.length > 0 ? (
                  networks.map((item) => {
                    const isSelected = ssid === item.ssid && !manualMode;
                    return (
                      <TouchableOpacity
                        key={item.id}
                        onPress={() => {
                          if (!sending) {
                            setSsid(item.ssid);
                            setManualMode(false);
                          }
                        }}
                        style={{
                          backgroundColor: isSelected ? '#eff6ff' : '#ffffff',
                          borderRadius: 16,
                          padding: 14,
                          marginBottom: 8,
                          flexDirection: 'row',
                          alignItems: 'center',
                          justifyContent: 'space-between',
                          borderWidth: 1,
                          borderColor: isSelected ? '#3b82f6' : '#f1f5f9',
                        }}
                      >
                        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                          <MaterialCommunityIcons name="wifi" size={20} color="#64748b" style={{ marginRight: 10 }} />
                          <Text style={{ fontSize: 15, fontWeight: isSelected ? '700' : '600', color: '#0f172a' }}>
                            {item.ssid}
                          </Text>
                        </View>
                        {item.secured && <MaterialCommunityIcons name="lock" size={16} color="#cbd5e1" />}
                      </TouchableOpacity>
                    );
                  })
                ) : (
                  <Text style={{ textAlign: 'center', color: '#94a3b8', padding: 16, fontSize: 14 }}>
                    No se detectaron redes.
                  </Text>
                )}

                {/* BOTÓN MODO MANUAL */}
                <TouchableOpacity
                  onPress={() => {
                    if (!sending) {
                      setManualMode(true);
                      setSsid('');
                    }
                  }}
                  style={{
                    backgroundColor: manualMode ? '#f8fafc' : '#ffffff',
                    borderRadius: 16,
                    padding: 14,
                    marginBottom: 8,
                    flexDirection: 'row',
                    alignItems: 'center',
                    borderWidth: 1,
                    borderColor: manualMode ? '#3b82f6' : '#f1f5f9',
                    borderStyle: 'dashed'
                  }}
                >
                  <MaterialCommunityIcons name="plus" size={20} color="#64748b" style={{ marginRight: 10 }} />
                  <Text style={{ fontSize: 14, fontWeight: '700', color: manualMode ? '#3b82f6' : '#64748b' }}>
                    Ingresar red oculta manualmente
                  </Text>
                </TouchableOpacity>
              </ScrollView>
            </View>
          )}

          {/* INPUT MANUAL (SI ESTÁ ACTIVO) */}
          {manualMode && (
            <View style={{
              backgroundColor: '#ffffff',
              borderRadius: 20,
              paddingHorizontal: 16,
              height: 56,
              justifyContent: 'center',
              borderWidth: 1,
              borderColor: '#3b82f6',
              marginBottom: 14
            }}>
              <TextInput
                style={{ fontSize: 15, fontWeight: '600', color: '#0f172a' }}
                placeholder="Nombre de Red Oculta (SSID)"
                placeholderTextColor="#cbd5e1"
                value={ssid}
                onChangeText={setSsid}
                editable={!sending}
              />
            </View>
          )}

          {/* RESUMEN DE RED SELECCIONADA Y CONTRASEÑA */}
          {ssid.trim().length > 0 && (
            <View>
              <Text style={{ fontSize: 10, color: '#64748b', fontWeight: '700', letterSpacing: 0.5, marginBottom: 8, textTransform: 'uppercase' }}>
                Red seleccionada: <Text style={{ color: '#0f172a', fontWeight: '800' }}>{ssid}</Text>
              </Text>
              
              <View style={{
                backgroundColor: '#ffffff',
                borderRadius: 20,
                flexDirection: 'row',
                alignItems: 'center',
                paddingHorizontal: 16,
                height: 56,
                borderWidth: 1,
                borderColor: '#e2e8f0',
                marginBottom: 16
              }}>
                <TextInput
                  style={{ flex: 1, fontSize: 15, fontWeight: '600', color: '#0f172a' }}
                  placeholder="Contraseña de WiFi"
                  placeholderTextColor="#cbd5e1"
                  secureTextEntry={!showPassword}
                  value={password}
                  onChangeText={setPassword}
                  editable={!sending}
                />
                <TouchableOpacity onPress={() => setShowPassword(!showPassword)} disabled={sending}>
                  <MaterialCommunityIcons name={showPassword ? "eye" : "eye-off"} size={22} color="#64748b" />
                </TouchableOpacity>
              </View>
            </View>
          )}
        </View>
      )}

      {/* BOTÓN ENVIAR */}
      <View style={{ marginTop: 10 }}>
        <TouchableOpacity 
          onPress={handleSend}
          disabled={!isFormValid || sending}
          style={{
            backgroundColor: !isFormValid ? '#cbd5e1' : sending ? '#94a3b8' : '#3b82f6',
            borderRadius: 20,
            height: 60,
            alignItems: 'center',
            justifyContent: 'center',
            shadowColor: '#3b82f6',
            shadowOffset: { width: 0, height: 6 },
            shadowOpacity: isFormValid && !sending ? 0.2 : 0,
            shadowRadius: 10,
            elevation: isFormValid && !sending ? 3 : 0,
          }}
        >
          {sending ? (
            <ActivityIndicator size="small" color="#ffffff" />
          ) : (
            <Text style={{ color: 'white', fontSize: 15, fontWeight: '850', letterSpacing: 0.5 }}>
              ENVIAR CONFIGURACIÓN AL EMA
            </Text>
          )}
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}