import RNBluetoothClassic from 'react-native-bluetooth-classic';

export const scanForDevices = async () => {
  try {
    const paired = await RNBluetoothClassic.getBondedDevices();
    const pairedAddresses = paired.map(d => d.address);

    let unpaired = [];
    try {
      unpaired = await RNBluetoothClassic.startDiscovery();
    } catch (e) {
      console.log("No se descubrieron dispositivos nuevos libres:", e);
    }
    
    const strictlyUnpaired = unpaired.filter(device => !pairedAddresses.includes(device.address));
    
    return Array.from(new Set(strictlyUnpaired.map(a => a.address)))
      .map(address => strictlyUnpaired.find(a => a.address === address));
  } catch (e) {
    console.error("Error en escaneo nativo:", e);
    return [];
  }
};

export const connectToDevice = async (device) => {
  try {
    const isConnected = await device.isConnected();
    if (isConnected) return true;
    return await device.connect({
      CONNECTOR_TYPE: "rfcomm",
      DELIMITER: "\n",
      DEVICE_CHARSET: "utf-8",
    });
  } catch (err) {
    throw new Error("No se pudo conectar al dispositivo");
  }
};

export const sendWifiCredentials = async (rawDevice, ssid, password) => {
  if (!rawDevice) {
    throw new Error('No hay ningún dispositivo MICA conectado por Bluetooth.');
  }
  if (!ssid || !password) {
    throw new Error('Por favor ingresa la Red y Contraseña');
  }

  try {
    const isConnected = await rawDevice.isConnected();
    if (!isConnected) {
      await connectToDevice(rawDevice);
    }

    // Enviamos el formato plano esperado por tu protocolo al ESP32
    const data = `${ssid},${password}\n`;
    await rawDevice.write(data);
    return true;
  } catch (err) {
    throw new Error(`Fallo al transmitir datos por BT: ${err.message}`);
  }
};