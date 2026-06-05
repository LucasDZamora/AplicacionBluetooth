import * as Notifications from 'expo-notifications';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Clave única para guardar el último nivel que ya fue notificado en el disco
const LAST_NOTIFIED_KEY = '@mica_last_notified_battery';

export const triggerBatteryAlert = async (level) => {
  try {
    const numericLevel = Number(level);

    // 1. Leer el disco para ver si ya notificamos este porcentaje exacto (o uno menor)
    const lastNotifiedValue = await AsyncStorage.getItem(LAST_NOTIFIED_KEY);
    if (lastNotifiedValue !== null) {
      const lastLevel = Number(lastNotifiedValue);
      
      // ESCUDO: Si el nivel actual es igual o mayor al que ya notificamos, bloqueamos el spam.
      // Esto evita que si se queda pegado en 14%, o si oscila entre 14% y 15%, vuelva a sonar.
      if (numericLevel >= lastLevel) {
        console.log(`[NotificationService] Alerta omitida. El nivel ${numericLevel}% ya fue cubierto por la alerta previa de ${lastLevel}%`);
        return; 
      }
    }

    // 2. Si pasó el escudo, actualizamos inmediatamente el disco con el nuevo nivel
    await AsyncStorage.setItem(LAST_NOTIFIED_KEY, String(numericLevel));

    // 3. Disparar la notificación física en el teléfono
    console.log(`[NotificationService] Lanzando notificación real para el ${numericLevel}%`);
    await Notifications.scheduleNotificationAsync({
      content: {
        title: "⚠️ Batería Crítica en Estación MICA",
        body: `La batería del dispositivo ha bajado al ${numericLevel}%. Requiere atención inmediata.`,
        sound: true,
        priority: Notifications.AndroidNotificationPriority.MAX,
        channelId: 'default', 
      },
      trigger: null, // Se dispara inmediatamente
    });

  } catch (error) {
    console.error("Error en el control de spam de notificaciones:", error);
  }
};

/**
 * Función auxiliar para cuando el dispositivo se cargue (suba de 15%).
 * Permite limpiar el historial en el disco para que vuelva a alertar en el siguiente ciclo de descarga.
 */
export const resetBatteryNotificationFlag = async () => {
  try {
    await AsyncStorage.removeItem(LAST_NOTIFIED_KEY);
    console.log("[NotificationService] Historial de alertas de batería reseteado (Batería sana).");
  } catch (error) {
    console.error("Error al resetear el flag de batería:", error);
  }
};