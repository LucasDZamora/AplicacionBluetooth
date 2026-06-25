import React, { useState } from 'react';
import { View, Text, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert, Platform } from 'react-native';
import { MaterialCommunityIcons, Feather } from '@expo/vector-icons';

export default function InitialConfigScreen({ onSendConfig, onBack, networks, isLoadingNetworks, onRefreshNetworks }) {
  const [selectedMode, setSelectedMode] = useState(null); // 0 = Estacion, 1 = Experimento
  const [wifiEnabled, setWifiEnabled] = useState(null); // null, true, false
  const [ssid, setSsid] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  
  // Estados para la red secundaria
  const [ssid2, setSsid2] = useState('');
  const [password2, setPassword2] = useState('');
  const [showPassword2, setShowPassword2] = useState(false);
  const [showBackupWifi, setShowBackupWifi] = useState(false);
  
  const [sending, setSending] = useState(false);
  const [manualMode, setManualMode] = useState(Platform.OS === 'ios');
  const [manualMode2, setManualMode2] = useState(Platform.OS === 'ios');

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
      Alert.alert('Atención', 'Por favor, ingresa o selecciona una red WiFi principal.');
      return;
    }
    if (wifiEnabled && showBackupWifi && !ssid2.trim()) {
      Alert.alert('Atención', 'Por favor, ingresa o selecciona una red WiFi secundaria.');
      return;
    }

    setSending(true);
    try {
      await onSendConfig({
        mode: selectedMode,
        wifiEnabled: wifiEnabled,
        ssid: wifiEnabled ? ssid : '',
        password: wifiEnabled ? password : '',
        ssid2: (wifiEnabled && showBackupWifi) ? ssid2 : '',
        password2: (wifiEnabled && showBackupWifi) ? password2 : '',
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
          }}
        >
          <View style={{ width: 42, height: 42, borderRadius: 14, backgroundColor: selectedMode === 0 ? 'rgba(255,255,255,0.2)' : '#eff6ff', justifyContent: 'center', alignItems: 'center', marginBottom: 12 }}>
            <MaterialCommunityIcons name="layers-outline" size={24} color={selectedMode === 0 ? '#ffffff' : '#3b82f6'} />
          </View>
          <Text style={{ fontSize: 13, fontWeight: '800', color: selectedMode === 0 ? '#ffffff' : '#0f172a', letterSpacing: 0.5 }}>
            ESTACIÓN
          </Text>
          <Text style={{ fontSize: 10, fontWeight: '500', color: selectedMode === 0 ? 'rgba(255, 255, 255, 0.8)' : '#64748b', marginTop: 4, textAlign: 'center' }}>
            Se envían datos cada 30 minutos
          </Text>
        </TouchableOpacity>

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
          }}
        >
          <View style={{ width: 42, height: 42, borderRadius: 14, backgroundColor: selectedMode === 1 ? 'rgba(255,255,255,0.2)' : '#f3e8ff', justifyContent: 'center', alignItems: 'center', marginBottom: 12 }}>
            <MaterialCommunityIcons name="flask-outline" size={24} color={selectedMode === 1 ? '#ffffff' : '#a855f7'} />
          </View>
          <Text style={{ fontSize: 13, fontWeight: '800', color: selectedMode === 1 ? '#ffffff' : '#0f172a', letterSpacing: 0.5 }}>
            EXPERIMENTO
          </Text>
          <Text style={{ fontSize: 10, fontWeight: '500', color: selectedMode === 1 ? 'rgba(255, 255, 255, 0.8)' : '#64748b', marginTop: 4, textAlign: 'center' }}>
            Se envían datos cada 30 segundos
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
              setSsid2('');
              setPassword2('');
              setManualMode(false);
              setManualMode2(false);
              setShowBackupWifi(false);
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

      {/* CREDENCIALES WIFI */}
      {wifiEnabled === true && (
        <View style={{ marginBottom: 28 }}>
          {Platform.OS === 'ios' && (
            <View style={{ backgroundColor: '#fffbeb', borderRadius: 20, padding: 16, marginBottom: 16, borderWidth: 1, borderColor: '#fef3c7', flexDirection: 'row', alignItems: 'center' }}>
              <MaterialCommunityIcons name="information" size={24} color="#d97706" style={{ marginRight: 12 }} />
              <Text style={{ fontSize: 13, color: '#b45309', fontWeight: '700', flex: 1, lineHeight: 18 }}>
                Nota: iOS no permite escanear redes Wi-Fi por motivos de seguridad. Por favor, ingresa las redes manualmente.
              </Text>
            </View>
          )}

          {/* ----- RED PRINCIPAL ----- */}
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
            <Text style={{ fontSize: 11, color: '#0f172a', fontWeight: '800', letterSpacing: 0.5, textTransform: 'uppercase' }}>
              {Platform.OS === 'ios' ? 'Ingresa la red WiFi Principal' : 'Selecciona la Red WiFi Principal'}
            </Text>
            
            {Platform.OS !== 'ios' && onRefreshNetworks && (
              <TouchableOpacity 
                onPress={onRefreshNetworks} 
                disabled={isLoadingNetworks || sending}
                style={{ flexDirection: 'row', alignItems: 'center', gap: 4, paddingVertical: 4, paddingHorizontal: 8, backgroundColor: '#f1f5f9', borderRadius: 12 }}
              >
                <MaterialCommunityIcons name="refresh" size={16} color={isLoadingNetworks ? "#94a3b8" : "#3b82f6"} />
                <Text style={{ fontSize: 11, fontWeight: '700', color: isLoadingNetworks ? "#94a3b8" : "#3b82f6" }}>
                  {isLoadingNetworks ? 'Buscando...' : 'Escanear'}
                </Text>
              </TouchableOpacity>
            )}
          </View>

          {isLoadingNetworks ? (
            <View style={{ padding: 24, backgroundColor: '#ffffff', borderRadius: 24, alignItems: 'center', borderWidth: 1, borderColor: '#e2e8f0', marginBottom: 16 }}>
              <ActivityIndicator size="large" color="#3b82f6" style={{ marginBottom: 16 }} />
              <Text style={{ fontSize: 14, color: '#94a3b8', fontWeight: '700', letterSpacing: 0.5 }}>BUSCANDO REDES WIFI...</Text>
            </View>
          ) : (
            <View style={{ maxHeight: 200, marginBottom: 16 }}>
              <ScrollView nestedScrollEnabled={true} style={{ borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 24, backgroundColor: '#ffffff', padding: 12 }}>
                {networks && networks.length > 0 ? (
                  networks.map((item) => {
                    const isSelected = ssid === item.ssid && !manualMode;
                    return (
                      <TouchableOpacity
                        key={`prim-${item.id}`}
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
                          <Text style={{ fontSize: 15, fontWeight: isSelected ? '700' : '600', color: '#0f172a' }}>{item.ssid}</Text>
                        </View>
                        {item.secured && <MaterialCommunityIcons name="lock" size={16} color="#cbd5e1" />}
                      </TouchableOpacity>
                    );
                  })
                ) : (
                  Platform.OS === 'ios' ? null : <Text style={{ textAlign: 'center', color: '#94a3b8', padding: 16, fontSize: 14 }}>No se detectaron redes.</Text>
                )}

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
                  <Text style={{ fontSize: 14, fontWeight: '700', color: manualMode ? '#3b82f6' : '#64748b' }}>Ingresar red manualmente</Text>
                </TouchableOpacity>
              </ScrollView>
            </View>
          )}

          {manualMode && (
            <View style={{ backgroundColor: '#ffffff', borderRadius: 20, paddingHorizontal: 16, height: 56, justifyContent: 'center', borderWidth: 1, borderColor: '#3b82f6', marginBottom: 14 }}>
              <TextInput
                style={{ fontSize: 15, fontWeight: '600', color: '#0f172a' }}
                placeholder="Nombre de Red Principal (SSID)"
                placeholderTextColor="#cbd5e1"
                value={ssid}
                onChangeText={setSsid}
                editable={!sending}
                autoCapitalize="none"
                autoCorrect={false}
              />
            </View>
          )}

          {ssid.trim().length > 0 && (
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
                placeholder={`Contraseña para ${ssid}`}
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
          )}

          {/* ----- BOTÓN ACTIVAR WIFI RESPALDO ----- */}
          {ssid.trim().length > 0 && (
            <TouchableOpacity
              onPress={() => setShowBackupWifi(!showBackupWifi)}
              disabled={sending}
              style={{
                flexDirection: 'row',
                alignItems: 'center',
                marginTop: 10,
                marginBottom: 16,
                padding: 12,
                backgroundColor: showBackupWifi ? '#eff6ff' : '#f8fafc',
                borderRadius: 16,
                borderWidth: 1,
                borderColor: showBackupWifi ? '#3b82f6' : '#e2e8f0'
              }}
            >
              <MaterialCommunityIcons
                name={showBackupWifi ? "checkbox-marked" : "checkbox-blank-outline"}
                size={22}
                color={showBackupWifi ? "#3b82f6" : "#64748b"}
                style={{ marginRight: 8 }}
              />
              <Text style={{ fontSize: 13, fontWeight: '700', color: showBackupWifi ? '#1e40af' : '#475569' }}>
                Configurar red WiFi secundaria (Opcional)
              </Text>
            </TouchableOpacity>
          )}

          {/* ----- SECCIÓN RED SECUNDARIA ----- */}
          {wifiEnabled === true && showBackupWifi && (
            <View style={{ paddingLeft: 8, marginTop: 10, marginBottom: 16 }}>
              <Text style={{ fontSize: 11, color: '#0f172a', fontWeight: '800', letterSpacing: 0.5, marginBottom: 12, textTransform: 'uppercase' }}>
                {Platform.OS === 'ios' ? 'Ingresa la red WiFi Secundaria' : 'Selecciona la Red WiFi Secundaria'}
              </Text>

              {!isLoadingNetworks && (
                <View style={{ maxHeight: 200, marginBottom: 16 }}>
                  <ScrollView nestedScrollEnabled={true} style={{ borderWidth: 1, borderColor: '#e2e8f0', borderRadius: 24, backgroundColor: '#ffffff', padding: 12 }}>
                    {networks && networks.length > 0 ? (
                      networks.map((item) => {
                        const isSelected2 = ssid2 === item.ssid && !manualMode2;
                        return (
                          <TouchableOpacity
                            key={`sec-${item.id}`}
                            onPress={() => {
                              if (!sending) {
                                setSsid2(item.ssid);
                                setManualMode2(false);
                              }
                            }}
                            style={{
                              backgroundColor: isSelected2 ? '#f3e8ff' : '#ffffff',
                              borderRadius: 16,
                              padding: 14,
                              marginBottom: 8,
                              flexDirection: 'row',
                              alignItems: 'center',
                              justifyContent: 'space-between',
                              borderWidth: 1,
                              borderColor: isSelected2 ? '#a855f7' : '#f1f5f9',
                            }}
                          >
                            <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                              <MaterialCommunityIcons name="wifi" size={20} color="#64748b" style={{ marginRight: 10 }} />
                              <Text style={{ fontSize: 15, fontWeight: isSelected2 ? '700' : '600', color: '#0f172a' }}>{item.ssid}</Text>
                            </View>
                            {item.secured && <MaterialCommunityIcons name="lock" size={16} color="#cbd5e1" />}
                          </TouchableOpacity>
                        );
                      })
                    ) : (
                      Platform.OS === 'ios' ? null : <Text style={{ textAlign: 'center', color: '#94a3b8', padding: 16, fontSize: 14 }}>No se detectaron redes.</Text>
                    )}

                    <TouchableOpacity
                      onPress={() => {
                        if (!sending) {
                          setManualMode2(true);
                          setSsid2('');
                        }
                      }}
                      style={{
                        backgroundColor: manualMode2 ? '#f8fafc' : '#ffffff',
                        borderRadius: 16,
                        padding: 14,
                        marginBottom: 8,
                        flexDirection: 'row',
                        alignItems: 'center',
                        borderWidth: 1,
                        borderColor: manualMode2 ? '#a855f7' : '#f1f5f9',
                        borderStyle: 'dashed'
                  }}
                    >
                      <MaterialCommunityIcons name="plus" size={20} color="#64748b" style={{ marginRight: 10 }} />
                      <Text style={{ fontSize: 14, fontWeight: '700', color: manualMode2 ? '#a855f7' : '#64748b' }}>Ingresar red manualmente</Text>
                    </TouchableOpacity>
                  </ScrollView>
                </View>
              )}

              {manualMode2 && (
                <View style={{ backgroundColor: '#ffffff', borderRadius: 20, paddingHorizontal: 16, height: 56, justifyContent: 'center', borderWidth: 1, borderColor: '#a855f7', marginBottom: 14 }}>
                  <TextInput
                    style={{ fontSize: 15, fontWeight: '600', color: '#0f172a' }}
                    placeholder="Nombre de Red Secundaria (SSID)"
                    placeholderTextColor="#cbd5e1"
                    value={ssid2}
                    onChangeText={setSsid2}
                    editable={!sending}
                    autoCapitalize="none"
                    autoCorrect={false}
                  />
                </View>
              )}

              {ssid2.trim().length > 0 && (
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
                    placeholder={`Contraseña para ${ssid2}`}
                    placeholderTextColor="#cbd5e1"
                    secureTextEntry={!showPassword2}
                    value={password2}
                    onChangeText={setPassword2}
                    editable={!sending}
                  />
                  <TouchableOpacity onPress={() => setShowPassword2(!showPassword2)} disabled={sending}>
                    <MaterialCommunityIcons name={showPassword2 ? "eye" : "eye-off"} size={22} color="#64748b" />
                  </TouchableOpacity>
                </View>
              )}
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