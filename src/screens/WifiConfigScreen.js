import React, { useState } from 'react';
import { View, Text, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert, Platform } from 'react-native';
import { MaterialCommunityIcons, Feather } from '@expo/vector-icons';

export default function WifiConfigScreen({ onBack, onConnectAction, networks, isLoadingNetworks, onRefreshNetworks }) {
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

  const handleSubmit = async () => {
    if (!ssid.trim()) {
      Alert.alert('Atención', 'Por favor, ingresa o selecciona la red Wi-Fi principal.');
      return;
    }
    if (!password.trim()) {
      Alert.alert('Atención', 'Por favor, ingresa la contraseña de la red principal.');
      return;
    }
    if (showBackupWifi && (!ssid2.trim() || !password2.trim())) {
      Alert.alert('Atención', 'Por favor, completa los datos de la red de respaldo.');
      return;
    }

    setSending(true);
    try {
      // Argumentos: ssid1, password1, ssid2, password2
      await onConnectAction(
        ssid.trim(), 
        password, 
        showBackupWifi ? ssid2.trim() : "", 
        showBackupWifi ? password2 : ""
      );
    } catch (error) {
      Alert.alert('Error', 'No se pudo enviar la configuración Wi-Fi.');
    } finally {
      setSending(false);
    }
  };

  const isFormValid = ssid.trim() !== '' && password.trim() !== '' && (!showBackupWifi || (ssid2.trim() !== '' && password2.trim() !== ''));

  return (
    <ScrollView 
      style={{ flex: 1, backgroundColor: '#f8fafc', paddingHorizontal: 24, paddingTop: 60 }}
      contentContainerStyle={{ flexGrow: 1, paddingBottom: 100 }} // 💡 Aumentado para dar total libertad de scroll pasando el menú
      bounces={false}
      keyboardShouldPersistTaps="handled"
    >
      
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
          disabled={sending}
        >
          <Feather name="chevron-left" size={24} color="#1e293b" />
        </TouchableOpacity>
        <Text style={{ fontSize: 22, fontWeight: '850', color: '#0f172a' }}>Configurar Wi-Fi</Text>
      </View>

      {/* SECCIÓN RED PRINCIPAL */}
      <View style={{ marginBottom: 24 }}>
        <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
          <Text style={{ fontSize: 11, fontWeight: '850', color: '#475569', letterSpacing: 0.5 }}>
            RED WI-FI PRINCIPAL
          </Text>
          {!manualMode && Platform.OS === 'android' && (
            <TouchableOpacity 
              onPress={onRefreshNetworks} 
              disabled={isLoadingNetworks || sending} 
              style={{ flexDirection: 'row', alignItems: 'center' }}
            >
              <MaterialCommunityIcons name="refresh" size={16} color="#3b82f6" style={{ marginRight: 4 }} />
              <Text style={{ fontSize: 13, color: '#3b82f6', fontWeight: '600' }}>Recargar</Text>
            </TouchableOpacity>
          )}
        </View>

        {!manualMode && Platform.OS === 'android' ? (
          <View style={{ backgroundColor: '#ffffff', borderRadius: 20, padding: 16, borderWidth: 1, borderColor: '#e2e8f0' }}>
            
            {isLoadingNetworks ? (
              <View style={{ paddingVertical: 20, alignItems: 'center', justifyContent: 'center' }}>
                <ActivityIndicator size="small" color="#3b82f6" />
                <Text style={{ marginTop: 8, fontSize: 13, color: '#64748b', fontWeight: '500' }}>Buscando redes...</Text>
              </View>
            ) : (
              <>
                <Text style={{ fontSize: 13, color: '#64748b', marginBottom: 12 }}>Selecciona una red disponible:</Text>

                {networks && networks.length > 0 ? (
                  networks.map((item, index) => (
                    <TouchableOpacity
                      key={index}
                      onPress={() => setSsid(item.ssid)}
                      disabled={sending}
                      style={{
                        flexDirection: 'row',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        paddingVertical: 14,
                        borderBottomWidth: 1,
                        borderBottomColor: '#f1f5f9'
                      }}
                    >
                      <Text style={{ fontSize: 14, fontWeight: ssid === item.ssid ? '700' : '500', color: ssid === item.ssid ? '#3b82f6' : '#334155' }}>
                        {item.ssid}
                      </Text>
                      <MaterialCommunityIcons 
                        name={ssid === item.ssid ? "radiobox-marked" : "radiobox-blank"} 
                        size={20} 
                        color={ssid === item.ssid ? "#3b82f6" : "#cbd5e1"} 
                      />
                    </TouchableOpacity>
                  ))
                ) : (
                  <Text style={{ textAlign: 'center', color: '#94a3b8', marginVertical: 10, fontSize: 13 }}>
                    No se encontraron redes.
                  </Text>
                )}
              </>
            )}

            <TouchableOpacity 
              onPress={() => { setManualMode(true); setSsid(''); }}
              style={{ flexDirection: 'row', alignItems: 'center', paddingVertical: 14, borderTopWidth: isLoadingNetworks ? 0 : 1, borderTopColor: '#f1f5f9' }}
              disabled={sending}
            >
              <MaterialCommunityIcons name="keyboard-outline" size={18} color="#3b82f6" style={{ marginRight: 8 }} />
              <Text style={{ fontSize: 14, color: '#3b82f6', fontWeight: '600' }}>Ingresar red manualmente...</Text>
            </TouchableOpacity>
          </View>
        ) : (
          <View style={{ backgroundColor: '#ffffff', borderRadius: 20, borderWidth: 1, borderColor: '#e2e8f0', paddingHorizontal: 16, height: 56, flexDirection: 'row', alignItems: 'center' }}>
            <MaterialCommunityIcons name="wifi" size={22} color="#94a3b8" style={{ marginRight: 12 }} />
            <TextInput
              style={{ flex: 1, fontSize: 15, color: '#0f172a' }}
              placeholder="Nombre de la red Wi-Fi"
              placeholderTextColor="#cbd5e1"
              value={ssid}
              onChangeText={setSsid}
              editable={!sending}
            />
            {Platform.OS === 'android' && (
              <TouchableOpacity onPress={() => setManualMode(false)} disabled={sending}>
                <Text style={{ fontSize: 13, color: '#3b82f6', fontWeight: '600' }}>Ver lista</Text>
              </TouchableOpacity>
            )}
          </View>
        )}

        {ssid !== '' && (
          <View style={{ flexDirection: 'row', alignItems: 'center', backgroundColor: '#ffffff', borderRadius: 20, paddingHorizontal: 16, height: 56, borderWidth: 1, borderColor: '#e2e8f0', marginTop: 12 }}>
            <MaterialCommunityIcons name="lock-outline" size={22} color="#94a3b8" style={{ marginRight: 12 }} />
            <TextInput
              style={{ flex: 1, fontSize: 15, color: '#0f172a' }}
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
      </View>

      {/* SWITCH DE RESPALDO */}
      <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', backgroundColor: '#ffffff', borderRadius: 20, padding: 16, borderWidth: 1, borderColor: '#e2e8f0', marginBottom: 24 }}>
        <View style={{ flex: 1, marginRight: 8 }}>
          <Text style={{ fontSize: 15, fontWeight: '850', color: '#1e293b', marginBottom: 4 }}>Red de respaldo opcional</Text>
          <Text style={{ fontSize: 12, color: '#64748b' }}>Permitirá que el dispositivo se conecte a una segunda red alternativa si la primera falla.</Text>
        </View>
        <TouchableOpacity
          onPress={() => !sending && setShowBackupWifi(!showBackupWifi)}
          disabled={sending}
          style={{
            width: 50,
            height: 28,
            borderRadius: 14,
            backgroundColor: showBackupWifi ? '#3b82f6' : '#cbd5e1',
            padding: 2,
            justifyContent: 'center',
            alignItems: showBackupWifi ? 'flex-end' : 'flex-start',
          }}
        >
          <View style={{ width: 24, height: 24, borderRadius: 12, backgroundColor: '#ffffff' }} />
        </TouchableOpacity>
      </View>

      {/* SECCIÓN RED SECUNDARIA */}
      {showBackupWifi && (
        <View style={{ marginBottom: 24 }}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
            <Text style={{ fontSize: 11, fontWeight: '850', color: '#475569', letterSpacing: 0.5 }}>
              RED WI-FI SECUNDARIA (RESPALDO)
            </Text>
          </View>

          {!manualMode2 && Platform.OS === 'android' ? (
            <View style={{ backgroundColor: '#ffffff', borderRadius: 20, padding: 16, borderWidth: 1, borderColor: '#e2e8f0' }}>
              
              {isLoadingNetworks ? (
                <View style={{ paddingVertical: 20, alignItems: 'center', justifyContent: 'center' }}>
                  <ActivityIndicator size="small" color="#3b82f6" />
                  <Text style={{ marginTop: 8, fontSize: 13, color: '#64748b', fontWeight: '500' }}>Buscando redes...</Text>
                </View>
              ) : (
                <>
                  <Text style={{ fontSize: 13, color: '#64748b', marginBottom: 12 }}>Selecciona la red secundaria:</Text>

                  {networks && networks.length > 0 ? (
                    networks.map((item, index) => (
                      <TouchableOpacity
                        key={index}
                        onPress={() => setSsid2(item.ssid)}
                        disabled={sending}
                        style={{
                          flexDirection: 'row',
                          alignItems: 'center',
                          justifyContent: 'space-between',
                          paddingVertical: 14,
                          borderBottomWidth: 1,
                          borderBottomColor: '#f1f5f9'
                        }}
                      >
                        <Text style={{ fontSize: 14, fontWeight: ssid2 === item.ssid ? '700' : '500', color: ssid2 === item.ssid ? '#3b82f6' : '#334155' }}>
                          {item.ssid}
                        </Text>
                        <MaterialCommunityIcons 
                          name={ssid2 === item.ssid ? "radiobox-marked" : "radiobox-blank"} 
                          size={20} 
                          color={ssid2 === item.ssid ? "#3b82f6" : "#cbd5e1"} 
                        />
                      </TouchableOpacity>
                    ))
                  ) : (
                    <Text style={{ textAlign: 'center', color: '#94a3b8', marginVertical: 10, fontSize: 13 }}>
                      No se encontraron redes.
                    </Text>
                  )}
                </>
              )}

              <TouchableOpacity 
                onPress={() => { setManualMode2(true); setSsid2(''); }}
                style={{ flexDirection: 'row', alignItems: 'center', paddingVertical: 14, borderTopWidth: isLoadingNetworks ? 0 : 1, borderTopColor: '#f1f5f9' }}
                disabled={sending}
              >
                <MaterialCommunityIcons name="keyboard-outline" size={18} color="#3b82f6" style={{ marginRight: 8 }} />
                <Text style={{ fontSize: 14, color: '#3b82f6', fontWeight: '600' }}>Ingresar red manualmente...</Text>
              </TouchableOpacity>
            </View>
          ) : (
            <View style={{ backgroundColor: '#ffffff', borderRadius: 20, borderWidth: 1, borderColor: '#e2e8f0', paddingHorizontal: 16, height: 56, flexDirection: 'row', alignItems: 'center' }}>
              <MaterialCommunityIcons name="wifi" size={22} color="#94a3b8" style={{ marginRight: 12 }} />
              <TextInput
                style={{ flex: 1, fontSize: 15, color: '#0f172a' }}
                placeholder="Nombre de la red secundaria"
                placeholderTextColor="#cbd5e1"
                value={ssid2}
                onChangeText={setSsid2}
                editable={!sending}
              />
              {Platform.OS === 'android' && (
                <TouchableOpacity onPress={() => setManualMode2(false)} disabled={sending}>
                  <Text style={{ fontSize: 13, color: '#3b82f6', fontWeight: '600' }}>Ver lista</Text>
                </TouchableOpacity>
              )}
            </View>
          )}

          {ssid2 !== '' && (
            <View style={{ flexDirection: 'row', alignItems: 'center', backgroundColor: '#ffffff', borderRadius: 20, paddingHorizontal: 16, height: 56, borderWidth: 1, borderColor: '#e2e8f0', marginTop: 12 }}>
              <MaterialCommunityIcons name="lock-outline" size={22} color="#94a3b8" style={{ marginRight: 12 }} />
              <TextInput
                style={{ flex: 1, fontSize: 15, color: '#0f172a' }}
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

      {/* Añadimos padding y margen inferior explícito para ganarle espacio a la barra física o virtual del celular */}
      <View style={{ marginTop: 24, paddingBottom: 40 }}>
        <TouchableOpacity
          onPress={handleSubmit}
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
              ENVIAR CREDENCIALES
            </Text>
          )}
        </TouchableOpacity>
      </View>

    </ScrollView>
  );
}