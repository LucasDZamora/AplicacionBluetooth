import React, { useEffect } from 'react';
import { View, Text } from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { startBackgroundBle, stopBackgroundBle } from '../services/backgroundBleService';

export default function BatteryIndicator({ batteryLevel, deviceId }) {

  const getBatteryStatus = (level) => {
    if (level === null || level === undefined) {
      return { icon: "battery-off-outline", color: "#64748b" };
    }
    if (level === 0) {
      return { icon: "battery-outline", color: "#ef4444" };
    }
    let color = "#10b981"; // Verde por defecto
    if (level <= 15) {
      color = "#ef4444"; // Rojo crítico
    } else if (level <= 50) {
      color = "#f59e0b"; // Naranja precaución
    }

    // Mapeo preciso de los 10 niveles
    if (level <= 10) return { icon: "battery-10", color };
    if (level <= 20) return { icon: "battery-20", color };
    if (level <= 30) return { icon: "battery-30", color };
    if (level <= 40) return { icon: "battery-40", color };
    if (level <= 50) return { icon: "battery-50", color };
    if (level <= 60) return { icon: "battery-60", color };
    if (level <= 70) return { icon: "battery-70", color };
    if (level <= 80) return { icon: "battery-80", color };
    if (level <= 90) return { icon: "battery-90", color };

    return { icon: "battery", color }; // Para > 90%
  };

  const batteryInfo = getBatteryStatus(batteryLevel);

  return (
    <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 6 }}>
      <MaterialCommunityIcons 
        name={batteryInfo.icon} 
        size={18} 
        color={batteryInfo.color} 
        style={{ 
          marginRight: 6,
          transform: [{ rotate: '270deg' }]
        }} 
      />
      <Text style={{ color: batteryInfo.color, fontSize: 13, fontWeight: '700' }}>
        {batteryLevel !== null && batteryLevel !== undefined 
          ? `${batteryLevel}% Batería` 
          : '---% Batería'}
      </Text>
    </View>
  );
}