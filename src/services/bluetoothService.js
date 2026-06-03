import { BleManager } from 'react-native-ble-plx';
import { Buffer } from 'buffer';

export let manager = null;
try {
  manager = new BleManager();
} catch (e) {
  console.log("ℹ️ BleManager no pudo ser inicializado. Esto es normal en simuladores o Expo Go sin cliente de desarrollo:", e.message);
}

// Constantes de UUIDs para comunicación BLE con tu ESP32 (Modificables si utilizas otros UUIDs)
export const MICA_SERVICE_UUID = '4fafc201-1fb5-459e-8fcc-c5c9c331914b';
export const MICA_CHARACTERISTIC_UUID = 'beb5483e-36e1-4688-b7f5-ea07361b26a8';

// Comprobar si el bridge nativo de BLE está disponible
export const isBleAvailable = () => {
  return manager !== null;
};

// Escaneo asíncrono de BLE
export const scanForDevices = (onDeviceFound, onError) => {
  try {
    if (!manager) {
      throw new Error("BleManager no inicializado");
    }
    manager.startDeviceScan(null, null, (error, device) => {
      if (error) {
        console.warn("⚠️ Error en escaneo BLE:", error);
        if (onError) onError(error);
        return;
      }
      if (device) {
        onDeviceFound(device);
      }
    });
  } catch (e) {
    console.warn("⚠️ Fallo al arrancar escaneo BLE:", e);
    if (onError) onError(e);
  }
};

// Detener el escaneo de BLE
export const stopScanning = () => {
  try {
    if (manager) {
      manager.stopDeviceScan();
    }
  } catch (e) {
    console.warn("⚠️ Fallo al detener escaneo BLE:", e);
  }
};

// Conexión nativa a dispositivo BLE
export const connectToDevice = async (device) => {
  try {
    if (!manager) {
      throw new Error("BleManager no inicializado");
    }
    // Detener escaneo antes de conectar
    stopScanning();

    console.log("Conectando al dispositivo BLE:", device.id);
    const connectedDevice = await manager.connectToDevice(device.id);
    
    console.log("Conectado. Descubriendo servicios y características...");
    await connectedDevice.discoverAllServicesAndCharacteristics();
    
    console.log("Servicios descubiertos con éxito.");
    return connectedDevice;
  } catch (err) {
    console.error("Error al conectar o descubrir servicios:", err);
    throw new Error("No se pudo conectar al dispositivo MICA");
  }
};

// Transmisión de credenciales Wi-Fi mediante escritura de características BLE
export const sendWifiCredentials = async (connectedDevice, ssid, password) => {
  if (!connectedDevice) {
    throw new Error('No hay ningún dispositivo MICA conectado por Bluetooth.');
  }
  if (!ssid || !password) {
    throw new Error('Por favor ingresa la Red y Contraseña');
  }

  try {
    // El formato plano esperado por el protocolo del ESP32 (ssid,password\n)
    const data = `${ssid},${password}\n`;
    const base64Data = Buffer.from(data).toString('base64');

    console.log(`Enviando credenciales a servicio: ${MICA_SERVICE_UUID}, característica: ${MICA_CHARACTERISTIC_UUID}...`);
    
    await connectedDevice.writeCharacteristicWithResponseForService(
      MICA_SERVICE_UUID,
      MICA_CHARACTERISTIC_UUID,
      base64Data
    );
    
    console.log("Credenciales transmitidas correctamente.");
    return true;
  } catch (err) {
    console.error("Error al transmitir credenciales por BLE:", err);
    throw new Error(`Fallo al transmitir datos por BLE: ${err.message}`);
  }
};