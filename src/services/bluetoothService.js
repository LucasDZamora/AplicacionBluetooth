import { BleManager } from 'react-native-ble-plx';

// Instancia única de BleManager
export const manager = new BleManager();

// UUIDs del servicio y características BLE definidos en el ESP32
export const SERVICE_UUID = "4fafc201-1fb5-459e-8fcc-c5c9c331914b";
export const CMD_CHAR_UUID = "beb5483e-36e1-4688-b7f5-ea07361b26a8";
export const DATA_CHAR_UUID = "beb5483e-36e1-4688-b7f5-ea07361b26a9";

// Helper simple para codificar a Base64 en React Native sin dependencias externas
const base64Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

export const encodeBase64 = (str) => {
  let result = '';
  let i = 0;
  while (i < str.length) {
    const c1 = str.charCodeAt(i++) || 0;
    const c2 = str.charCodeAt(i++) || 0;
    const c3 = str.charCodeAt(i++) || 0;
    
    const byte1 = c1 >> 2;
    const byte2 = ((c1 & 3) << 4) | (c2 >> 4);
    const byte3 = ((c2 & 15) << 2) | (c3 >> 6);
    const byte4 = c3 & 63;
    
    result += base64Chars.charAt(byte1) + 
              base64Chars.charAt(byte2) + 
              (i - 2 < str.length ? base64Chars.charAt(byte3) : '=') + 
              (i - 1 < str.length ? base64Chars.charAt(byte4) : '=');
  }
  return result;
};

// Helper simple para decodificar de Base64 en React Native sin dependencias externas
export const decodeBase64 = (base64) => {
  let result = '';
  let i = 0;
  const cleanBase64 = base64.replace(/[^A-Za-z0-9+/]/g, '');
  while (i < cleanBase64.length) {
    const byte1 = base64Chars.indexOf(cleanBase64.charAt(i++));
    const byte2 = base64Chars.indexOf(cleanBase64.charAt(i++));
    const byte3 = base64Chars.indexOf(cleanBase64.charAt(i++));
    const byte4 = base64Chars.indexOf(cleanBase64.charAt(i++));
    
    const c1 = (byte1 << 2) | (byte2 >> 4);
    const c2 = ((byte2 & 15) << 4) | (byte3 >> 2);
    const c3 = ((byte3 & 3) << 6) | byte4;
    
    result += String.fromCharCode(c1);
    if (byte3 !== 64 && cleanBase64.charAt(i - 2) !== '=') {
      result += String.fromCharCode(c2);
    }
    if (byte4 !== 64 && cleanBase64.charAt(i - 1) !== '=') {
      result += String.fromCharCode(c3);
    }
  }
  return result;
};

/**
 * Inicia el escaneo de dispositivos BLE buscando EMAs que contengan 'MICA' en el nombre.
 */
export const scanForDevices = (onDeviceFound, onError) => {
  console.log("BLE: Iniciando escaneo de dispositivos...");
  manager.startDeviceScan(null, null, (error, device) => {
    if (error) {
      console.error("BLE: Error al escanear dispositivos:", error);
      onError && onError(error);
      return;
    }
    if (device && device.name && device.name.toLowerCase().includes('mica')) {
      console.log(`BLE: Dispositivo MICA encontrado -> ${device.name} (${device.id})`);
      onDeviceFound(device);
    }
  });
};

/**
 * Detiene el escaneo activo de dispositivos BLE.
 */
export const stopScanning = () => {
  console.log("BLE: Deteniendo escaneo.");
  manager.stopDeviceScan();
};

/**
 * Se conecta a un dispositivo MICA por BLE y descubre sus servicios y características.
 */
export const connectToDevice = async (device) => {
  const deviceId = typeof device === 'string' ? device : device.id;
  console.log(`BLE: Intentando conectar a dispositivo ${deviceId}...`);
  try {
    const connectedDevice = await manager.connectToDevice(deviceId);
    console.log(`BLE: ¡Conectado exitosamente a ${connectedDevice.name || connectedDevice.id}!`);
    
    // Solicitar MTU de 512 bytes para evitar el truncamiento del JSON de telemetría en Android
    try {
      console.log("BLE: Solicitando MTU de 512 bytes...");
      await connectedDevice.requestMTU(512);
      console.log("BLE: MTU negociado correctamente.");
    } catch (mtuErr) {
      console.warn("BLE: No se pudo negociar MTU (normal en iOS/algunos dispositivos):", mtuErr.message);
    }

    console.log("BLE: Descubriendo servicios y características...");
    const discoveredDevice = await connectedDevice.discoverAllServicesAndCharacteristics();
    console.log("BLE: Servicios y características descubiertos con éxito.");
    return discoveredDevice;
  } catch (err) {
    console.error(`BLE: Error en la conexión al dispositivo ${deviceId}:`, err);
    throw new Error(`No se pudo establecer la conexión BLE: ${err.message}`);
  }
};

/**
 * Envía credenciales Wi-Fi al ESP32 usando la característica de comandos BLE.
 */
export const sendWifiCredentials = async (device, ssid, password) => {
  if (!device) {
    throw new Error('No hay ningún dispositivo MICA conectado por BLE.');
  }
  if (!ssid || !password) {
    throw new Error('Por favor ingresa la Red y Contraseña');
  }

  const payload = `WIFI:${ssid}|${password}`;
  const base64Data = encodeBase64(payload);
  console.log(`BLE: Enviando credenciales Wi-Fi: ${ssid}, [PASSWORD OCULTO]`);
  
  try {
    // Intentar verificar conexión antes de escribir
    const isConnected = await device.isConnected();
    let activeDevice = device;
    if (!isConnected) {
      console.log("BLE: Dispositivo desconectado de forma inesperada. Reconectando...");
      activeDevice = await connectToDevice(device.id);
    }

    await activeDevice.writeCharacteristicWithResponseForService(
      SERVICE_UUID,
      CMD_CHAR_UUID,
      base64Data
    );
    console.log("BLE: ¡Credenciales Wi-Fi enviadas correctamente al MICA!");
    return true;
  } catch (err) {
    console.error("BLE: Error al enviar las credenciales Wi-Fi:", err);
    throw new Error(`Fallo al transmitir datos por BLE: ${err.message}`);
  }
};

/**
 * Cambia el modo de operación (Estación/Experimento) del MICA mediante comando BLE.
 */
export const changeOperatingMode = async (device, modeCode) => {
  if (!device) {
    throw new Error('No hay ningún dispositivo MICA conectado por BLE.');
  }
  
  const payload = `MODE:${modeCode}`;
  const base64Data = encodeBase64(payload);
  console.log(`BLE: Enviando cambio de modo: MODE:${modeCode}`);
  
  try {
    const isConnected = await device.isConnected();
    let activeDevice = device;
    if (!isConnected) {
      console.log("BLE: Dispositivo desconectado de forma inesperada. Reconectando...");
      activeDevice = await connectToDevice(device.id);
    }

    await activeDevice.writeCharacteristicWithResponseForService(
      SERVICE_UUID,
      CMD_CHAR_UUID,
      base64Data
    );
    console.log(`BLE: ¡Modo cambiado exitosamente a MODE:${modeCode}!`);
    return true;
  } catch (err) {
    console.error("BLE: Error al cambiar de modo por BLE:", err);
    throw new Error(`Fallo al transmitir cambio de modo: ${err.message}`);
  }
};

/**
 * Se suscribe a la característica de telemetría del MICA para recibir datos en tiempo real.
 */
export const subscribeToMicaData = (device, onDataReceived, onError) => {
  if (!device) return null;
  console.log("BLE: Suscribiendo listener para telemetría en tiempo real...");
  
  return device.monitorCharacteristicForService(
    SERVICE_UUID,
    DATA_CHAR_UUID,
    (error, characteristic) => {
      if (error) {
        console.error("BLE: Error en monitoreo de telemetría:", error);
        onError && onError(error);
        return;
      }
      if (characteristic && characteristic.value) {
        try {
          const rawString = decodeBase64(characteristic.value).replace(/\0/g, '').trim();
          const dataObj = JSON.parse(rawString);
          console.log("BLE: Telemetría recibida con éxito ->", dataObj);
          onDataReceived(dataObj);
        } catch (e) {
          console.warn("BLE: Error al decodificar o parsear JSON de telemetría:", e);
        }
      }
    }
  );
};