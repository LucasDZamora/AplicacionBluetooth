import React, { useState, useEffect } from 'react';
import { View, Text, TouchableOpacity, FlatList, TextInput, ActivityIndicator, Animated, Alert, Platform } from 'react-native';
import { MaterialCommunityIcons, Feather } from '@expo/vector-icons';

export default function WifiConfigScreen({ onBack, onConnectAction, networks, isLoadingNetworks }) {
  const [viewState, setViewState] = useState('list'); 
  const [selectedSsid, setSelectedSsid] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [ssid2, setSsid2] = useState('');
  const [password2, setPassword2] = useState('');
  const [showPassword2, setShowPassword2] = useState(false);
  const [showBackupWifi, setShowBackupWifi] = useState(false);
  const [sending, setSending] = useState(false);
  const [manualMode, setManualMode] = useState(Platform.OS === 'ios');

  const spinValue = new Animated.Value(0);

  useEffect(() => {
    if (isLoadingNetworks) {
      Animated.loop(
        Animated.timing(spinValue, {
          toValue: 1,
          duration: 1500,
          useNativeDriver: true,
        })
      ).start();
    }
  }, [isLoadingNetworks]);

  const spin = spinValue.interpolate({
    inputRange: [0, 1],
    outputRange: ['0deg', '360deg'],
  });

  const handleSubmit = async () => {
    if (manualMode && !selectedSsid.trim()) {
      Alert.alert('Atención', 'Por favor, ingresa el nombre de la red (SSID).');
      return;
    }
    if (!password.trim()) {
      Alert.alert('Atención', 'Por favor, ingresa la contraseña de la red.');
      return;
    }
    
    setSending(true);
    try {
      const backupSsid = showBackupWifi ? ssid2 : '';
      const backupPass = showBackupWifi ? password2 : '';
      await onConnectAction(selectedSsid.trim(), password, backupSsid, backupPass);
    } catch (error) {
      Alert.alert('Error de transmisión', error.message);
    } finally {
      setSending(false);
    }
  };

  const handleBack = () => {
    if (sending) return;
    if (viewState === 'input') {
      setViewState('list');
      setManualMode(Platform.OS === 'ios');
    } else {
      onBack();
    }
  };

  return (
    <View style={{ flex: 1, backgroundColor: '#ffffff', paddingHorizontal: 24, paddingTop: 60 }}>
      
      {/* HEADER */}
      <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 40 }}>
        <TouchableOpacity 
          onPress={handleBack}
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
          <Feather name="chevron-left" size={24} color="#1e293b" />
        </TouchableOpacity>
        <Text style={{ fontSize: 20, fontWeight: '900', color: '#0f172a', fontStyle: 'italic', textTransform: 'uppercase' }}>
          Configurar WIFI
        </Text>
      </View>

      {isLoadingNetworks ? (
        <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', paddingBottom: 100 }}>
          <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '800', letterSpacing: 1, marginBottom: 40, position: 'absolute', top: 0, alignSelf: 'flex-start' }}>
            REDES DISPONIBLES
          </Text>
          <Animated.View style={{ transform: [{ rotate: spin }] }}>
            <MaterialCommunityIcons name="loading" size={60} color="#3b82f6" />
          </Animated.View>
          <Text style={{ fontSize: 14, fontWeight: '900', color: '#94a3b8', marginTop: 24, letterSpacing: 1 }}>
            BUSCANDO REDES...
          </Text>
        </View>
      ) : (
        <>
          {viewState === 'list' && (
            <View style={{ flex: 1 }}>
              {Platform.OS === 'ios' && (
                <View style={{
                  backgroundColor: '#fffbeb',
                  borderRadius: 20,
                  padding: 16,
                  marginBottom: 20,
                  borderWidth: 1,
                  borderColor: '#fef3c7',
                  flexDirection: 'row',
                  alignItems: 'center'
                }}>
                  <MaterialCommunityIcons name="information" size={24} color="#d97706" style={{ marginRight: 12 }} />
                  <Text style={{ fontSize: 13, color: '#b45309', fontWeight: '700', flex: 1, lineHeight: 18 }}>
                    Nota: iOS no permite escanear redes Wi-Fi por motivos de seguridad. Por favor, ingresa el nombre de tu red manualmente a continuación.
                  </Text>
                </View>
              )}

              <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '800', letterSpacing: 1, marginBottom: 24 }}>
                {Platform.OS === 'ios' ? 'CONFIGURAR CONEXIÓN' : 'REDES AL ALCANCE'}
              </Text>
              <FlatList
                data={networks || []}
                keyExtractor={(item) => item.id}
                renderItem={({ item }) => (
                  <TouchableOpacity 
                    onPress={() => {
                      setSelectedSsid(item.ssid);
                      setManualMode(false);
                      setViewState('input');
                    }}
                    style={{
                      backgroundColor: '#f8fafc',
                      borderRadius: 32,
                      padding: 20,
                      marginBottom: 16,
                      flexDirection: 'row',
                      alignItems: 'center',
                      justifyContent: 'space-between',
                    }}
                  >
                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                      <View style={{ width: 50, height: 50, borderRadius: 25, backgroundColor: '#ffffff', justifyContent: 'center', alignItems: 'center', marginRight: 16 }}>
                        <MaterialCommunityIcons name="wifi" size={26} color="#3b82f6" />
                      </View>
                      <Text style={{ fontSize: 17, fontWeight: '700', color: '#0f172a' }}>{item.ssid}</Text>
                    </View>
                    {item.secured && <MaterialCommunityIcons name="lock" size={20} color="#cbd5e1" />}
                  </TouchableOpacity>
                )}
                ListFooterComponent={
                  <TouchableOpacity
                    onPress={() => {
                      setSelectedSsid('');
                      setManualMode(true);
                      setViewState('input');
                    }}
                    style={{
                      flexDirection: 'row',
                      alignItems: 'center',
                      backgroundColor: '#f8fafc',
                      borderRadius: 32,
                      padding: 20,
                      marginBottom: 16,
                      borderWidth: 1,
                      borderColor: '#3b82f6',
                      borderStyle: 'dashed',
                      justifyContent: 'center'
                    }}
                  >
                    <MaterialCommunityIcons name="plus" size={24} color="#3b82f6" style={{ marginRight: 10 }} />
                    <Text style={{ fontSize: 17, fontWeight: '700', color: '#3b82f6' }}>
                      Ingresar red manualmente
                    </Text>
                  </TouchableOpacity>
                }
                ListEmptyComponent={
                  Platform.OS === 'ios' ? null : (
                    <Text style={{ textAlign: 'center', color: '#64748b', marginTop: 40, fontSize: 15, marginBottom: 20 }}>
                      No se detectaron redes Wi-Fi.
                    </Text>
                  )
                }
              />
            </View>
          )}

          {viewState === 'input' && (
            <View style={{ flex: 1 }}>
              <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '800', letterSpacing: 1, marginBottom: 8 }}>
                {manualMode ? 'INGRESAR RED WIFI' : 'CONECTANDO A'}
              </Text>
              
              {manualMode ? (
                <View style={{
                  backgroundColor: '#f8fafc',
                  borderRadius: 24,
                  paddingHorizontal: 20,
                  height: 70,
                  justifyContent: 'center',
                  borderWidth: 1,
                  borderColor: '#3b82f6',
                  marginBottom: 40
                }}>
                  <TextInput
                    style={{ fontSize: 20, fontWeight: '900', color: '#0f172a', fontStyle: 'italic' }}
                    placeholder="Nombre de red (SSID)"
                    placeholderTextColor="#cbd5e1"
                    value={selectedSsid}
                    onChangeText={setSelectedSsid}
                    editable={!sending}
                    autoCapitalize="none"
                    autoCorrect={false}
                  />
                </View>
              ) : (
                <Text style={{ fontSize: 32, fontWeight: '900', color: '#0f172a', fontStyle: 'italic', marginBottom: 40 }}>
                  {selectedSsid}
                </Text>
              )}

              <View style={{
                backgroundColor: '#f8fafc',
                borderRadius: 24,
                flexDirection: 'row',
                alignItems: 'center',
                paddingHorizontal: 20,
                height: 70,
                marginBottom: 30
              }}>
                <TextInput
                  style={{ flex: 1, fontSize: 16, fontWeight: '600', color: '#0f172a' }}
                  placeholder="Contraseña de Red"
                  placeholderTextColor="#cbd5e1"
                  secureTextEntry={!showPassword}
                  value={password}
                  onChangeText={setPassword}
                  editable={!sending}
                />
                <TouchableOpacity onPress={() => setShowPassword(!showPassword)} disabled={sending}>
                  <MaterialCommunityIcons 
                    name={showPassword ? "eye" : "eye-off"} 
                    size={24} 
                    color="#64748b" 
                  />
                </TouchableOpacity>
              </View>

              {/* WIFI RESPALDO OPCIONAL */}
              <TouchableOpacity
                onPress={() => setShowBackupWifi(!showBackupWifi)}
                disabled={sending}
                style={{
                  flexDirection: 'row',
                  alignItems: 'center',
                  marginTop: -10,
                  marginBottom: 20,
                  padding: 14,
                  backgroundColor: showBackupWifi ? '#eff6ff' : '#f8fafc',
                  borderRadius: 20,
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

              {showBackupWifi && (
                <View style={{ paddingLeft: 8, marginBottom: 24 }}>
                  <Text style={{ fontSize: 11, color: '#64748b', fontWeight: '800', letterSpacing: 0.5, marginBottom: 8, textTransform: 'uppercase' }}>
                    Red WiFi Secundaria
                  </Text>
                  
                  <View style={{
                    backgroundColor: '#f8fafc',
                    borderRadius: 24,
                    paddingHorizontal: 20,
                    height: 60,
                    justifyContent: 'center',
                    borderWidth: 1,
                    borderColor: '#e2e8f0',
                    marginBottom: 12
                  }}>
                    <TextInput
                      style={{ fontSize: 16, fontWeight: '600', color: '#0f172a' }}
                      placeholder="SSID de red secundaria (ej: MiWifiDeRespaldo)"
                      placeholderTextColor="#cbd5e1"
                      value={ssid2}
                      onChangeText={setSsid2}
                      editable={!sending}
                    />
                  </View>

                  <View style={{
                    backgroundColor: '#f8fafc',
                    borderRadius: 24,
                    flexDirection: 'row',
                    alignItems: 'center',
                    paddingHorizontal: 20,
                    height: 60,
                    borderWidth: 1,
                    borderColor: '#e2e8f0'
                  }}>
                    <TextInput
                      style={{ flex: 1, fontSize: 16, fontWeight: '600', color: '#0f172a' }}
                      placeholder="Contraseña de red secundaria"
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
                </View>
              )}

              <TouchableOpacity 
                onPress={handleSubmit}
                disabled={sending}
                style={{
                  backgroundColor: sending ? '#cbd5e1' : '#3b82f6',
                  borderRadius: 20,
                  height: 60,
                  alignItems: 'center',
                  justifyContent: 'center'
                }}
              >
                {sending ? (
                  <ActivityIndicator size="small" color="#ffffff" />
                ) : (
                  <Text style={{ color: 'white', fontSize: 14, fontWeight: '900', letterSpacing: 1 }}>
                    ENVIAR CREDENCIALES
                  </Text>
                )}
              </TouchableOpacity>
            </View>
          )}
        </>
      )}
    </View>
  );
}