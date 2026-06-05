import { registerRootComponent } from 'expo';
import { AppRegistry } from 'react-native'; // 👈 Importamos AppRegistry de react-native
import App from './App';
import { triggerBatteryAlert } from './src/services/notificationService'; // 👈 Tu servicio de alertas

registerRootComponent(App);


AppRegistry.registerHeadlessTask('MicaBackgroundBleTask', () => async (taskData) => {
    console.log("🤖 [Headless JS] Tarea despertada por Java en segundo plano profundo.");

    if (taskData && taskData.battery) {
        const numericLevel = Number(taskData.battery);
        if (numericLevel <= 15) {
            console.log(`⚠️ [Headless profundo] Batería crítica detectada: ${numericLevel}%`);
            await triggerBatteryAlert(numericLevel);
        }
    }
});