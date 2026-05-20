import React, { useState, useEffect } from 'react';
import { View, Text, TouchableOpacity, FlatList, TextInput, ActivityIndicator, Animated, Alert } from 'react-native';

export default function WifiConfigScreen({ onBack, onConnectAction, networks, isLoadingNetworks }) {
  // 'list' maneja las redes encontradas, 'input' el formulario de la clave
  const [viewState, setViewState] = useState('list'); 
  const [selectedSsid, setSelectedSsid] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [sending, setSending] = useState(false);

  // Instancia para controlar la animación de rotación
  const spinValue = new Animated.Value(0);

  // CONTROL ÚNICO DEL SPINNER: Gira obedeciendo el estado del App.js
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

  // Interpolación matemática limpia para evitar errores en el parseador de Babel
  const spin = spinValue.interpolate({
    inputRange: [0, 1],
    outputRange: ['0deg', '360deg'],
  });

  const handleSubmit = async () => {
    if (!password.trim()) {
      Alert.alert('Atención', 'Por favor, ingresa la contraseña de la red.');
      return;
    }
    
    setSending(true);
    try {
      await onConnectAction(selectedSsid, password);
    } catch (error) {
      Alert.alert('Error de transmisión', error.message);
    } finally {
      setSending(false);
    }
  };

  const handleBack = () => {
    if (sending) return; // Bloquear si se están enviando datos por BT
    if (viewState === 'input') {
      setViewState('list');
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
          <Text style={{ fontSize: 18, color: '#1e293b', fontWeight: 'bold' }}>←</Text>
        </TouchableOpacity>
        <Text style={{ fontSize: 20, fontWeight: '900', color: '#0f172a', fontStyle: 'italic', textTransform: 'uppercase' }}>
          Configurar WIFI
        </Text>
      </View>

      {/* RENDERIZADO DEPENDIENTE DE LA CARGA DE MAESTRA */}
      {isLoadingNetworks ? (
        <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', paddingBottom: 100 }}>
          <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '800', letterSpacing: 1, marginBottom: 40, position: 'absolute', top: 0, alignSelf: 'flex-start' }}>
            REDES DISPONIBLES
          </Text>
          <Animated.Text style={{ fontSize: 60, color: '#3b82f6', transform: [{ rotate: spin }] }}>
            ↻
          </Animated.Text>
          <Text style={{ fontSize: 14, fontWeight: '900', color: '#94a3b8', marginTop: 24, letterSpacing: 1 }}>
            BUSCANDO REDES DESDE EL CELULAR...
          </Text>
        </View>
      ) : (
        <>
          {/* VISTA A: LISTADO DE REDES DISPONIBLES */}
          {viewState === 'list' && (
            <View style={{ flex: 1 }}>
              <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '800', letterSpacing: 1, marginBottom: 24 }}>
                REDES AL ALCANCE DEL TELÉFONO
              </Text>
              <FlatList
                data={networks || []}
                keyExtractor={(item) => item.id}
                renderItem={({ item }) => (
                  <TouchableOpacity 
                    onPress={() => {
                      setSelectedSsid(item.ssid);
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
                        <Text style={{ fontSize: 22 }}>📶</Text>
                      </View>
                      <Text style={{ fontSize: 17, fontWeight: '700', color: '#0f172a' }}>{item.ssid}</Text>
                    </View>
                    {item.secured && <Text style={{ color: '#cbd5e1', fontSize: 18 }}>🔒</Text>}
                  </TouchableOpacity>
                )}
                ListEmptyComponent={
                  <Text style={{ textAlign: 'center', color: '#64748b', marginTop: 40, fontSize: 15 }}>
                    No se detectaron redes Wi-Fi activas a tu alrededor.
                  </Text>
                }
              />
            </View>
          )}

          {/* VISTA B: FORMULARIO DE CREDENCIALES */}
          {viewState === 'input' && (
            <View style={{ flex: 1 }}>
              <Text style={{ fontSize: 12, color: '#94a3b8', fontWeight: '800', letterSpacing: 1, marginBottom: 8 }}>
                CONECTANDO A
              </Text>
              <Text style={{ fontSize: 32, fontWeight: '900', color: '#0f172a', fontStyle: 'italic', marginBottom: 40 }}>
                {selectedSsid}
              </Text>

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
                  <Text style={{ fontSize: 20 }}>{showPassword ? '👁️' : '👁️‍🗨️'}</Text>
                </TouchableOpacity>
              </View>

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
                    ENVIAR CREDENCIALES AL MICA
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