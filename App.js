import React, { useState } from 'react';
import { Alert } from 'react-native';
import HomeScreen from './src/screens/HomeScreen';
import NewEmaScreen from './src/screens/NewEmaScreen';
import DetailsEmaScreen from './src/screens/DetailsEmaScreen';
import WifiConfigScreen from './src/screens/WifiConfigScreen';
import { sendWifiCredentials } from './src/services/bluetoothService';

// IMPORTACIÓN DEL NUEVO HOOK MODULAR
import useWifiScanner from './src/services/useWifiScanner';

export default function App() {
  const [currentScreen, setCurrentScreen] = useState('home');
  const [selectedDevice, setSelectedDevice] = useState(null);
  const [wifiOrigin, setWifiOrigin] = useState('new_ema'); 
  const [refreshTrigger, setRefreshTrigger] = useState(0);
  
  // Callback de emergencia si fallan los permisos dentro del módulo
  const handlePermissionFallback = () => {
    setCurrentScreen(wifiOrigin === 'details' ? 'details' : 'new_ema');
  };

  // INVOCACIÓN DEL MODULO DE WI-FI
  const { cellphoneNetworks, loadingWifi } = useWifiScanner(currentScreen, handlePermissionFallback);

  const handleBluetoothConnected = (device) => {
    setSelectedDevice(device);
    setWifiOrigin('new_ema');
    setCurrentScreen('wifi_config');
  };

  const handleWifiConfigured = async (ssid, password) => {
    try {
      const rawDeviceInstance = selectedDevice?.rawDevice;
      await sendWifiCredentials(rawDeviceInstance, ssid, password);
      
      Alert.alert('Éxito', '¡Credenciales enviadas correctamente al MICA!');
      setRefreshTrigger(prev => prev + 1);
      setCurrentScreen('home');
    } catch (error) {
      Alert.alert('Error', error.message || 'No se pudieron enviar las credenciales.');
    }
  };

  if (currentScreen === 'new_ema') {
    return (
      <NewEmaScreen 
        onBack={() => setCurrentScreen('home')} 
        onConnectionSuccess={handleBluetoothConnected} 
      />
    );
  }

  if (currentScreen === 'wifi_config') {
    return (
      <WifiConfigScreen 
        networks={cellphoneNetworks}
        isLoadingNetworks={loadingWifi}
        onBack={() => {
          setCurrentScreen(wifiOrigin === 'details' ? 'details' : 'new_ema');
        }}
        onConnectAction={async (ssid, password) => {
          await handleWifiConfigured(ssid, password);
        }}
      />
    );
  }

  if (currentScreen === 'details') {
    return (
      <DetailsEmaScreen 
        device={selectedDevice} 
        onBack={() => setCurrentScreen('home')} 
        onConfigWifi={() => {
          setWifiOrigin('details');
          setCurrentScreen('wifi_config');
        }}
      />
    );
  }

  return (
    <HomeScreen 
      onNavigateToNewEma={() => setCurrentScreen('new_ema')} 
      activeTrigger={refreshTrigger}
      onSelectDevice={(device) => {
        setSelectedDevice(device);
        setCurrentScreen('details');
      }}
    />
  );
}