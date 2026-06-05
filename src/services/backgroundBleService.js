import { NativeModules } from 'react-native';
import { manager, subscribeToMicaData } from './bluetoothService';
import { triggerBatteryAlert } from './notificationService';

const { MicaServiceModule } = NativeModules;

let backgroundSubscription = null;
let activeDeviceId = null;
let hasNotifiedInBg = false;
let simulationInterval = null;

export const startBackgroundBle = (deviceId) => {
  activeDeviceId = deviceId;
  if (MicaServiceModule) {
    MicaServiceModule.startService();
  }
};
export const stopBackgroundBle = () => {
  if (backgroundSubscription) {
    backgroundSubscription.remove();
    backgroundSubscription = null;
  }
  if (simulationInterval) {
    clearInterval(simulationInterval);
    simulationInterval = null;
  }
  if (MicaServiceModule) {
    MicaServiceModule.stopService();
  }
};

/**
 * Esta función es ejecutada por el Headless JS Task de Android.
 * Corre en un hilo de JavaScript dedicado en segundo plano,
 * incluso si la app está minimizada o la pantalla apagada.
 */
export const backgroundTaskDefinition = async (taskData) => {
  console.log("====================================================");
  console.log("🤖 Headless JS: Hilo de segundo plano MICA inicializado.");
  console.log("====================================================");

  if (!activeDeviceId) return;

  // --- CONFIGURACIÓN DEL MODO SIMULACIÓN EN SEGUNDO PLANO ---
  if (activeDeviceId === "SIMULADO_123") {
    // Empezamos en 17% para que baje rápido al umbral crítico (15%)
    let fakeBgBattery = 17; 

    if (!simulationInterval) {
      console.log("🚀 Iniciando bucle de simulación forzado en Headless JS...");
      
      simulationInterval = setInterval(() => {
        fakeBgBattery -= 1;
        console.log(`[Headless JS Hilo Oculto] Batería disminuyendo: ${fakeBgBattery}%`);

        if (fakeBgBattery <= 15) {
          if (!hasNotifiedInBg) {
            console.log("🚨 ¡Umbral crítico alcanzado en Background! Disparando notificación push...");
            
            // Invocamos directamente el servicio de notificaciones sin depender de React
            triggerBatteryAlert(fakeBgBattery); 
            hasNotifiedInBg = true;
          }
        }

        // Reseteamos la simulación si baja demasiado para poder probar múltiples veces
        if (fakeBgBattery <= 11) {
          fakeBgBattery = 17;
          hasNotifiedInBg = false;
        }
      }, 3000); // Resta 1% cada 3 segundos
    }
    return;
  }

  // --- FLUJO REAL (Hardware MICA físico conectado) ---
  try {
    const connectedDevices = await manager.connectedDevices([]);
    const device = connectedDevices.find(d => d.id === activeDeviceId);

    if (device && !backgroundSubscription) {
      backgroundSubscription = subscribeToMicaData(
        device,
        (data) => {
          const level = Number(data.battery);
          const alertThreshold = 15;
          const recoveryThreshold = alertThreshold + 5;
          if (!isNaN(level) && level > 0) {
            if (level <= alertThreshold) {
              if (!hasNotifiedInBg) {
                triggerBatteryAlert(level);
                hasNotifiedInBg = true;
              }
            } else if (level > recoveryThreshold) {
              hasNotifiedInBg = false;
            }
          }
        },
        (error) => {
          console.error("Error en Headless JS con BLE real:", error);
        }
      );
    }
  } catch (err) {
    console.error("Error en Headless JS manejando dispositivos:", err);
  }
};