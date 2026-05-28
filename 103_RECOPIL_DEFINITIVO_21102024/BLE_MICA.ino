#include <BLEDevice.h>
#include <BLEServer.h>
#include <BLEUtils.h>
#include <BLE2902.h>
#include <LiquidCrystal_I2C.h>
#include <Adafruit_NeoPixel.h>
#include <WiFi.h>
#include <EEPROM.h>

extern LiquidCrystal_I2C lcd;
extern Adafruit_NeoPixel pixels;
extern int g;
extern int percentage;
extern int adq;
extern bool connected;
extern char ssid1[32];
extern char password1[32];
extern char ssid2[32];
extern char password2[32];
extern int muestreo;
extern int estado;
extern bool enMediciones;
extern void actualizarLedModo();

// UUIDs del servicio y características BLE
#define SERVICE_UUID           "4fafc201-1fb5-459e-8fcc-c5c9c331914b"
#define CMD_CHAR_UUID          "beb5483e-36e1-4688-b7f5-ea07361b26a8"
#define DATA_CHAR_UUID         "beb5483e-36e1-4688-b7f5-ea07361b26a9"

BLEServer* pServer = NULL;
BLECharacteristic* pCmdChar = NULL;
BLECharacteristic* pDataChar = NULL;
bool bleDeviceConnected = false;
bool bleOldDeviceConnected = false;

// Flags de configuración para Wi-Fi
bool wifiCredentialsReceived = false;
String receivedSSID = "";
String receivedPASS = "";

class MyServerCallbacks: public BLEServerCallbacks {
    void onConnect(BLEServer* pServer) {
      bleDeviceConnected = true;
      Serial.println("BLE: Cliente conectado.");
      
      // Solo alteramos LED y LCD si no estamos en fase de mediciones/bucle principal
      if (!enMediciones) {
        // Feedback visual LED: AZUL
        pixels.fill(pixels.Color(0, 0, 255));
        pixels.show();
        
        lcd.clear();
        lcd.setCursor(1, 1);
        lcd.print(" BLE Conectado! ");
        lcd.setCursor(1, 2);
        lcd.print(" Esperando wifi...");
      }
    };

    void onDisconnect(BLEServer* pServer) {
      bleDeviceConnected = false;
      Serial.println("BLE: Cliente desconectado.");
      
      // Solo alteramos LED y LCD si no estamos en fase de mediciones/bucle principal
      if (!enMediciones) {
        // Feedback visual LED: ROJO (o apagar/restaurar)
        pixels.fill(pixels.Color(255, 0, 0));
        pixels.show();
        
        lcd.clear();
        lcd.setCursor(1, 1);
        lcd.print("BLE Desconectado");
        lcd.setCursor(1, 2);
        lcd.print("Publicitando... ");
      }
    }
};

class MyCmdCallbacks: public BLECharacteristicCallbacks {
    void onWrite(BLECharacteristic *pChar) {
      String rxStr = String(pChar->getValue().c_str());
      rxStr.trim();
      if (rxStr.length() > 0) {
        Serial.print("BLE: Recibido comando -> ");
        Serial.println(rxStr);

        if (rxStr.startsWith("WIFI:")) {
          // Formato WIFI:ssid|password o WIFI:ssid,password
          String creds = rxStr.substring(5);
          int sepIdx = creds.indexOf('|');
          if (sepIdx < 0) {
            sepIdx = creds.indexOf(',');
          }
          if (sepIdx > 0) {
            receivedSSID = creds.substring(0, sepIdx);
            receivedPASS = creds.substring(sepIdx + 1);
            wifiCredentialsReceived = true;
            Serial.println("BLE: Credenciales parseadas (WIFI: SSID=" + receivedSSID + ")");
            
            // Solo alteramos LED y LCD si no estamos en fase de mediciones/bucle principal
            if (!enMediciones) {
              // LED: CIAN/CELESTE
              pixels.fill(pixels.Color(0, 255, 255));
              pixels.show();
              
              lcd.clear();
              lcd.setCursor(1, 1);
              lcd.print("Credenciales OK");
              lcd.setCursor(1, 2);
              lcd.print("Conectando WiFi...");
            }
          }
        } 
        else if (rxStr.startsWith("MODE:")) {
          String modeStr = rxStr.substring(5);
          int newMode = modeStr.toInt();
          g = newMode;
          // Actualizar modo de muestreo
          if (adq == 1) {
            muestreo = (g == 0) ? 1 : 2;
          } else {
            muestreo = (g == 0) ? 3 : 4;
          }
          estado = g;
          Serial.println("BLE: Cambio de modo a " + String(g == 0 ? "ESTACION" : "EXPERIMENTO"));
          
          if (enMediciones) {
            actualizarLedModo();
          } else {
            lcd.clear();
            lcd.setCursor(1, 1);
            lcd.print("Modo cambiado:");
            lcd.setCursor(1, 2);
            lcd.print(g == 0 ? "ESTACION" : "EXPERIMENTO");
            delay(1500);
          }
        }
        else if (rxStr.indexOf('|') > 0 || rxStr.indexOf(',') > 0) {
          // Formato simple retrocompatible: ssid|password o ssid,password
          int sepIdx = rxStr.indexOf('|');
          if (sepIdx < 0) {
            sepIdx = rxStr.indexOf(',');
          }
          receivedSSID = rxStr.substring(0, sepIdx);
          receivedPASS = rxStr.substring(sepIdx + 1);
          wifiCredentialsReceived = true;
          Serial.println("BLE: Credenciales parseadas (SSID=" + receivedSSID + ")");
          
          // Solo alteramos LED y LCD si no estamos en fase de mediciones/bucle principal
          if (!enMediciones) {
            // LED: CIAN/CELESTE
            pixels.fill(pixels.Color(0, 255, 255));
            pixels.show();
            
            lcd.clear();
            lcd.setCursor(1, 1);
            lcd.print("Credenciales OK");
            lcd.setCursor(1, 2);
            lcd.print("Conectando WiFi...");
          }
        }
      }
    }
};

void inicializarBLE() {
  BLEDevice::init("MICA_BLE");
  pServer = BLEDevice::createServer();
  pServer->setCallbacks(new MyServerCallbacks());

  BLEService *pService = pServer->createService(SERVICE_UUID);

  pCmdChar = pService->createCharacteristic(
               CMD_CHAR_UUID,
               BLECharacteristic::PROPERTY_WRITE | 
               BLECharacteristic::PROPERTY_WRITE_NR
             );
  pCmdChar->setCallbacks(new MyCmdCallbacks());

  pDataChar = pService->createCharacteristic(
                DATA_CHAR_UUID,
                BLECharacteristic::PROPERTY_READ | 
                BLECharacteristic::PROPERTY_NOTIFY
              );
  pDataChar->addDescriptor(new BLE2902());

  pService->start();

  BLEAdvertising *pAdvertising = BLEDevice::getAdvertising();
  pAdvertising->addServiceUUID(SERVICE_UUID);
  pAdvertising->setScanResponse(true);
  pAdvertising->setMinPreferred(0x06);  // funciones de conexión para iOS
  pAdvertising->setMinPreferred(0x12);
  BLEDevice::startAdvertising();
  Serial.println("BLE: Inicializado y publicitando como MICA_BLE...");
}

void enviarDatosBLE() {
  if (bleDeviceConnected && pDataChar != NULL) {
    // Construir JSON de telemetria
    String wifiSSID = (WiFi.status() == WL_CONNECTED) ? WiFi.SSID() : "Desconectado";
    int wifiStatus = (WiFi.status() == WL_CONNECTED) ? 1 : 0;
    
    String jsonPayload = "{\"battery\":" + String(percentage) + 
                         ",\"mode\":" + String(g) + 
                         ",\"wifi\":" + String(wifiStatus) + 
                         ",\"ssid\":\"" + wifiSSID + "\"}";
                         
    pDataChar->setValue(jsonPayload.c_str());
    pDataChar->notify();
    Serial.print("BLE: Enviando telemetria -> ");
    Serial.println(jsonPayload);
  }

  // Si se desconecta, reiniciar publicidad
  if (!bleDeviceConnected && bleOldDeviceConnected) {
    delay(500); // Dar tiempo al stack bluetooth
    pServer->startAdvertising();
    Serial.println("BLE: Reiniciada publicidad por desconexión.");
    bleOldDeviceConnected = bleDeviceConnected;
  }
  
  if (bleDeviceConnected && !bleOldDeviceConnected) {
    bleOldDeviceConnected = bleDeviceConnected;
  }
}
