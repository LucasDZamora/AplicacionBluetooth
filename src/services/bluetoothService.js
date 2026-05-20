import RNBluetoothClassic from 'react-native-bluetooth-classic';

export const scanForDevices = async () => {
  try {
    const paired = await RNBluetoothClassic.getBondedDevices();
    let unpaired = [];
    try {
      unpaired = await RNBluetoothClassic.startDiscovery();
    } catch (e) {
      console.log("No se descubrieron dispositivos nuevos libres:", e);
    }
    
    const allDevices = [...paired, ...unpaired];
    
    // Filtrar duplicados usando su dirección física MAC única
    return Array.from(new Set(allDevices.map(a => a.address)))
      .map(address => allDevices.find(a => a.address === address));
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