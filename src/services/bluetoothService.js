import RNBluetoothClassic from 'react-native-bluetooth-classic';

export const scanForDevices = async () => {
  try {
    // 1. Obtener los dispositivos que ya están vinculados/emparejados en el teléfono
    const paired = await RNBluetoothClassic.getBondedDevices();
    const pairedAddresses = paired.map(d => d.address);

    let unpaired = [];
    try {
      // 2. Descubrir los dispositivos Bluetooth visibles en el entorno
      unpaired = await RNBluetoothClassic.startDiscovery();
    } catch (e) {
      console.log("No se descubrieron dispositivos nuevos libres o discovery ya activo:", e);
    }
    
    // 3. Filtrar los descubiertos para EXCLUIR los que ya están vinculados
    const strictlyUnpaired = unpaired.filter(device => !pairedAddresses.includes(device.address));
    
    // 4. Eliminar duplicados eventuales del escaneo usando su dirección física MAC única
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