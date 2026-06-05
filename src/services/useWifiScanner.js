import { useState, useEffect, useRef } from 'react';
import { Alert, PermissionsAndroid, Platform } from 'react-native';
import WifiReborn from 'react-native-wifi-reborn';

export default function useWifiScanner(currentScreen, fallbackScreenCallback) {
  const [cellphoneNetworks, setCellphoneNetworks] = useState([]);
  const [loadingWifi, setLoadingWifi] = useState(false);

  // Candado para evitar hilos nativos duplicados en el chip de Wi-Fi
  const isScanningRef = useRef(false); 
  // Rastreador síncrono para saber si el usuario sigue dentro de la pantalla
  const isScreenMountedRef = useRef(false);

  // Control de ciclo de vida interno supeditado a la navegación de la App
  useEffect(() => {
    if (currentScreen === 'wifi_config' || currentScreen === 'initial_config') {
      isScreenMountedRef.current = true;
      
      if (!isScanningRef.current) {
        scanCellphoneWifi();
      } else {
        setLoadingWifi(true);
      }
    } else {
      isScreenMountedRef.current = false;
    }
  }, [currentScreen]);

  const scanCellphoneWifi = async () => {
    isScanningRef.current = true;
    setLoadingWifi(true); 
    const startTime = Date.now(); 

    try {
      if (Platform.OS === 'android') {
        const granted = await PermissionsAndroid.request(
          PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION
        );
        if (granted !== PermissionsAndroid.RESULTS.GRANTED) {
          throw new Error('Se requiere el permiso de ubicación para escanear redes Wi-Fi.');
        }
      }

      const wifiList = await WifiReborn.reScanAndLoadWifiList();
      
      if (!wifiList || !Array.isArray(wifiList)) {
        console.log("Hardware saturado o respuesta inválida. Inicializando arreglo vacío.");
        setCellphoneNetworks([]);
      } else {
        const uniqueNetworks = [];
        const seenSSIDs = new Set();
        
        wifiList.forEach((net) => {
          const ssid = net?.SSID;
          if (ssid && ssid.trim() !== "" && !seenSSIDs.has(ssid)) {
            seenSSIDs.add(ssid);
            uniqueNetworks.push({
              id: String(uniqueNetworks.length + 1),
              ssid: ssid,
              secured: net?.capabilities && !net.capabilities.includes('[OPEN]')
            });
          }
        });
        
        setCellphoneNetworks(uniqueNetworks);
      }

    } catch (error) {
      console.error("Excepción controlada en el chip Wi-Fi:", error);
      setCellphoneNetworks([]); 
      if (error.message && error.message.includes('permiso')) {
        Alert.alert("Error de Permisos", error.message);
        if (typeof fallbackScreenCallback === 'function') {
          fallbackScreenCallback();
        }
      }
    } finally {
      const endTime = Date.now();
      const timeElapsed = endTime - startTime;
      const minimumDuration = 5000; 

      if (timeElapsed < minimumDuration) {
        const remainingTime = minimumDuration - timeElapsed;
        setTimeout(() => {
          if (isScreenMountedRef.current) {
            setLoadingWifi(false);
          }
          isScanningRef.current = false;
        }, remainingTime);
      } else {
        if (isScreenMountedRef.current) {
          setLoadingWifi(false);
        }
        isScanningRef.current = false;
      }
    }
  };

  // Exponemos únicamente lo que WifiConfigScreen necesita leer
  return {
    cellphoneNetworks,
    loadingWifi
  };
}