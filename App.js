import React, { useState, useEffect } from 'react';
import { StyleSheet, Text, View, TextInput, TouchableOpacity, FlatList, ActivityIndicator, PermissionsAndroid, Platform, Alert } from 'react-native';
import RNBluetoothClassic from 'react-native-bluetooth-classic';

export default function App() {
  const [devices, setDevices] = useState([]);
  const [discovering, setDiscovering] = useState(false);
  const [connectedDevice, setConnectedDevice] = useState(null);
  const [ssid, setSsid] = useState('');
  const [password, setPassword] = useState('');
  const [sending, setSending] = useState(false);

  useEffect(() => {
    requestAccess();
  }, []);

  useEffect(() => {
    let disconnectSubscription;
    if (connectedDevice) {
      disconnectSubscription = RNBluetoothClassic.onDeviceDisconnected(event => {
        if (event.device.address === connectedDevice.address) {
          setConnectedDevice(null);
          Alert.alert("Desconectado", "El MICA cerró la conexión (probablemente para conectar el WiFi).");
        }
      });
    }
    
    return () => {
      if (disconnectSubscription) {
        disconnectSubscription.remove();
      }
    };
  }, [connectedDevice]);

  const requestAccess = async () => {
    if (Platform.OS === 'android' && Platform.Version >= 31) {
      await PermissionsAndroid.requestMultiple([
        PermissionsAndroid.PERMISSIONS.BLUETOOTH_CONNECT,
        PermissionsAndroid.PERMISSIONS.BLUETOOTH_SCAN,
        PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION,
      ]);
    } else if (Platform.OS === 'android') {
      await PermissionsAndroid.request(PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION);
    }
  };

  const startDiscovery = async () => {
    try {
      setDiscovering(true);
      setDevices([]);
      
      // Obtener dispositivos emparejados (si el MICA_BT fue emparejado en ajustes)
      const paired = await RNBluetoothClassic.getBondedDevices();
      
      // Buscar dispositivos nuevos
      let unpaired = [];
      try {
        unpaired = await RNBluetoothClassic.startDiscovery();
      } catch(e) {
        console.log("No se encontraron dispositivos nuevos", e);
      }
      
      const allDevices = [...paired, ...unpaired];
      // Filtrar para no tener repetidos
      const uniqueDevices = Array.from(new Set(allDevices.map(a => a.address)))
        .map(address => {
          return allDevices.find(a => a.address === address);
        });
        
      setDevices(uniqueDevices);
    } catch (err) {
      Alert.alert('Error', err.message);
    } finally {
      setDiscovering(false);
    }
  };

  const connectToDevice = async (device) => {
    try {
      let connection = await device.isConnected();
      if (!connection) {
        connection = await device.connect({
          CONNECTOR_TYPE: "rfcomm",
          DELIMITER: "\n",
          DEVICE_CHARSET: "utf-8",
        });
      }
      
      if (connection) {
        setConnectedDevice(device);
        Alert.alert('Conectado', `Conectado a ${device.name || 'Dispositivo'}`);
      }
    } catch (err) {
      Alert.alert('Error de conexión', err.message);
    }
  };

  const sendCredentials = async () => {
    if (!connectedDevice) {
      Alert.alert('Error', 'Primero debes conectarte al MICA_BT');
      return;
    }
    if (!ssid || !password) {
      Alert.alert('Error', 'Por favor ingresa la Red y Contraseña');
      return;
    }

    try {
      setSending(true);
      // El formato que el ESP32 está esperando: "SSID,PASSWORD"
      const data = `${ssid},${password}`;
      await connectedDevice.write(data);
      Alert.alert('Éxito', '¡Datos enviados correctamente al MICA!');
    } catch (err) {
      Alert.alert('Error al enviar', err.message);
    } finally {
      setSending(false);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>MICA Config BT</Text>
      
      {!connectedDevice ? (
        <>
          <TouchableOpacity style={styles.button} onPress={startDiscovery}>
            <Text style={styles.buttonText}>{discovering ? 'Buscando...' : 'Buscar Dispositivos Bluetooth'}</Text>
          </TouchableOpacity>
          {discovering && <ActivityIndicator size="large" color="#007BFF" style={{marginTop: 20}} />}
          <FlatList
            data={devices}
            keyExtractor={item => item.address}
            renderItem={({ item }) => (
              <TouchableOpacity style={styles.deviceItem} onPress={() => connectToDevice(item)}>
                <Text style={styles.deviceName}>{item.name || 'Dispositivo Bluetooth'}</Text>
                <Text style={styles.deviceMac}>{item.address}</Text>
              </TouchableOpacity>
            )}
            style={styles.list}
            ListEmptyComponent={
              !discovering ? <Text style={styles.emptyText}>No hay dispositivos cerca.</Text> : null
            }
          />
        </>
      ) : (
        <View style={styles.configContainer}>
          <Text style={styles.subtitle}>Conectado a: {connectedDevice.name}</Text>
          
          <Text style={styles.label}>Nombre de la Red (WiFi)</Text>
          <TextInput
            style={styles.input}
            placeholder="Ej: MiWiFiCasa"
            value={ssid}
            onChangeText={setSsid}
          />
          
          <Text style={styles.label}>Contraseña</Text>
          <TextInput
            style={styles.input}
            placeholder="Tu contraseña"
            value={password}
            onChangeText={setPassword}
            secureTextEntry
          />
          
          <TouchableOpacity style={[styles.button, styles.sendButton]} onPress={sendCredentials}>
            <Text style={styles.buttonText}>{sending ? 'Enviando...' : 'Enviar al MICA'}</Text>
          </TouchableOpacity>
          
          <TouchableOpacity style={[styles.button, styles.disconnectButton]} onPress={async () => {
            await connectedDevice.disconnect();
            setConnectedDevice(null);
          }}>
            <Text style={styles.buttonText}>Desconectar</Text>
          </TouchableOpacity>
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f0f4f8', paddingTop: 60, paddingHorizontal: 20 },
  title: { fontSize: 28, fontWeight: 'bold', color: '#1a1a1a', textAlign: 'center', marginBottom: 20 },
  subtitle: { fontSize: 18, color: '#4a4a4a', textAlign: 'center', marginBottom: 30, fontWeight: '500' },
  label: { fontSize: 14, color: '#333', marginBottom: 8, fontWeight: 'bold', marginLeft: 4 },
  button: { backgroundColor: '#007BFF', padding: 16, borderRadius: 12, alignItems: 'center', marginVertical: 10, shadowColor: '#007BFF', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.3, shadowRadius: 5, elevation: 4 },
  sendButton: { backgroundColor: '#28a745', shadowColor: '#28a745', marginTop: 20 },
  disconnectButton: { backgroundColor: '#dc3545', shadowColor: '#dc3545', marginTop: 10 },
  buttonText: { color: '#fff', fontSize: 16, fontWeight: 'bold', letterSpacing: 0.5 },
  list: { marginTop: 20 },
  deviceItem: { backgroundColor: '#fff', padding: 18, borderRadius: 12, marginBottom: 12, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.05, shadowRadius: 3, elevation: 2, borderWidth: 1, borderColor: '#eaeaea' },
  deviceName: { fontSize: 16, fontWeight: 'bold', color: '#2c3e50' },
  deviceMac: { fontSize: 12, color: '#7f8c8d', marginTop: 6 },
  configContainer: { flex: 1, justifyContent: 'center' },
  input: { backgroundColor: '#fff', padding: 16, borderRadius: 12, fontSize: 16, marginBottom: 20, borderWidth: 1, borderColor: '#e0e0e0', color: '#333' },
  emptyText: { textAlign: 'center', color: '#888', marginTop: 40, fontSize: 16 }
});
