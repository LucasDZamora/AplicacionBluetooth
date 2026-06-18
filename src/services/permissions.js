import { PermissionsAndroid, Platform } from 'react-native';
import * as Notifications from 'expo-notifications';
import { Alert, Linking } from 'react-native';

const NOTIFICATION_CHANNEL_CONFIG = {
  name: 'default',
  importance: Notifications.AndroidImportance.MAX,
  vibrationPattern: [0, 250, 250, 250],
  lightColor: '#ef4444',
};
export const requestBluetoothPermissions = async () => {
  // ... (tu código se queda igual) ...
  if (Platform.OS === 'android') {
    try {
      if (Platform.Version >= 31) {
        const granted = await PermissionsAndroid.requestMultiple([
          PermissionsAndroid.PERMISSIONS.BLUETOOTH_CONNECT,
          PermissionsAndroid.PERMISSIONS.BLUETOOTH_SCAN,
          PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION,
        ]);
        return (
          granted['android.permission.BLUETOOTH_CONNECT'] === PermissionsAndroid.RESULTS.GRANTED &&
          granted['android.permission.BLUETOOTH_SCAN'] === PermissionsAndroid.RESULTS.GRANTED &&
          granted['android.permission.ACCESS_FINE_LOCATION'] === PermissionsAndroid.RESULTS.GRANTED
        );
      } else {
        const granted = await PermissionsAndroid.request(PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION);
        return granted === PermissionsAndroid.RESULTS.GRANTED;
      }
    } catch (err) {
      console.warn("Error solicitando permisos nativos:", err);
      return false;
    }
  }
  return true;
};

export const setupNotificationChannel = async () => {
  if (Platform.OS === 'android') {
    await Notifications.setNotificationChannelAsync('default', NOTIFICATION_CHANNEL_CONFIG);
  }
};

export const requestNotificationPermissions = async () => {
  try {
    const current = await Notifications.getPermissionsAsync();

    console.log("Estado actual:", current);

    if (current.granted) {
      await setupNotificationChannel();
      return true;
    }

    if (!current.canAskAgain) {
      Alert.alert(
        'Notificaciones desactivadas',
        'Las alertas del dispositivo requieren permisos de notificación. Por favor actívalas en la configuración de la app.',
        [
          {
            text: 'Cancelar',
            style: 'cancel',
          },
          {
            text: 'Configuración',
            onPress: () => Linking.openSettings(),
          },
        ]
      );
      return false;
    }

    const requested = await Notifications.requestPermissionsAsync();

    console.log("Resultado solicitud:", requested);

    if (requested.granted) {
      await setupNotificationChannel();
      return true;
    }

    Alert.alert(
      'Notificaciones desactivadas',
      'Las alertas del dispositivo requieren permisos de notificación.',
      [
        {
          text: 'Cancelar',
          style: 'cancel',
        },
        {
          text: 'Configuración',
          onPress: () => Linking.openSettings(),
        },
      ]
    );

    return false;
  } catch (error) {
    console.error('Error al solicitar permisos:', error);
    return false;
  }
};