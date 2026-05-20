import React, { useState } from 'react';
import HomeScreen from './src/screens/HomeScreen';
import NewEmaScreen from './src/screens/NewEmaScreen';
import DetailsEmaScreen from './src/screens/DetailsEmaScreen';
import WifiConfigScreen from './src/screens/WifiConfigScreen';

export default function App() {
  const [currentScreen, setCurrentScreen] = useState('home');
  const [selectedDevice, setSelectedDevice] = useState(null);
  const [refreshTrigger, setRefreshTrigger] = useState(0);

  // Paso 1: Bluetooth conectado -> Ir a Wifi
  const handleBluetoothConnected = (device) => {
    setSelectedDevice(device);
    setCurrentScreen('wifi_config');
  };

  // Paso 2: Wifi configurado -> Volver al Home
  const handleWifiConfigured = () => {
    setRefreshTrigger(prev => prev + 1);
    setCurrentScreen('home');
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
        onBack={() => setCurrentScreen('new_ema')}
        onSelectNetwork={(ssid) => {
          console.log("Configurando red:", ssid);
          handleWifiConfigured();
        }}
      />
    );
  }

  if (currentScreen === 'details') {
    return <DetailsEmaScreen device={selectedDevice} onBack={() => setCurrentScreen('home')} />;
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