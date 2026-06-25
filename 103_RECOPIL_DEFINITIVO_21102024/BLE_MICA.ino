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
extern int f;
extern bool connected;
extern char ssid1[32];
extern char password1[32];
extern char ssid2[32];
extern char password2[32];
extern int muestreo;
extern bool enMediciones;
extern bool configReceived;
extern void actualizarLedModo();
extern int paginaActual;
extern unsigned long tiempoInicio;
extern unsigned long tiempo_B;
extern unsigned long tiempo_C;
extern unsigned long intervalo_B;
extern unsigned long intervalo_C;
extern unsigned long PRIMEROS_MINUTOS;


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
bool hasSecondaryWifi = false;
String receivedSSID2 = "";
String receivedPASS2 = "";

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

        // ====================================================================
        // CASO 1: COMANDOS DE CONFIGURACIÓN WI-FI
        // ====================================================================
        if (rxStr.startsWith("WIFI:") || rxStr.indexOf('|') > 0 || rxStr.indexOf(',') > 0) {
          String creds = rxStr;
          if (rxStr.startsWith("WIFI:")) {
            creds = rxStr.substring(5);
          }

          // Sub-caso: Apagado explícito de Wi-Fi
          if (creds == "OFF") {
            adq = 0;
            connected = false;
            f = 0;
            WiFi.disconnect(true);
            
            muestreo = (g == 0) ? 3 : 4;
            actualizarLedModo();
            Serial.println("BLE: WiFi desactivado por comando.");
            
            lcd.backlight();
            lcd.clear();
            lcd.setCursor(1, 1);
            lcd.print("WiFi Desactivado");
            
            if (bleDeviceConnected && pDataChar != NULL) {
              pDataChar->setValue("WIFI_DISABLED");
              pDataChar->notify();
            }
            delay(1500);
            
            if (enMediciones) {
              unsigned long ahora = millis();
              if (ahora < PRIMEROS_MINUTOS) {
                tiempo_B = ahora - intervalo_B;
              } else {
                if (g == 0) {
                  tiempo_C = ahora - intervalo_C;
                } else {
                  tiempo_B = ahora - intervalo_B;
                }
              }
            }
          }
          // Sub-caso: Encendido explícito (Intenta reconectar a las guardadas previamente)
          else if (creds == "ON") {
            adq = 1;
            muestreo = (g == 0) ? 1 : 2;
            actualizarLedModo();
            Serial.println("BLE: WiFi activado por comando.");
            
            lcd.backlight();
            lcd.clear();
            lcd.setCursor(1, 1);
            lcd.print("WiFi Activado");
            lcd.setCursor(1, 2);
            lcd.print("Buscando red...");
            delay(1500);
            
            WiFi.mode(WIFI_STA);
            if (strlen(ssid1) > 0) {
              WiFi.disconnect();
              WiFi.begin(ssid1, password1);
            }
            
            if (enMediciones) {
              unsigned long ahora = millis();
              if (ahora < PRIMEROS_MINUTOS) {
                tiempo_B = ahora - intervalo_B;
              } else {
                if (g == 0) {
                  tiempo_C = ahora - intervalo_C;
                } else {
                  tiempo_B = ahora - intervalo_B;
                }
              }
            }
          }
          // Sub-caso: Procesamiento y validación de NUEVAS credenciales enviadas
          else {
            int sep1 = creds.indexOf('|');
            if (sep1 < 0) {
              sep1 = creds.indexOf(',');
            }
            if (sep1 > 0) {
              receivedSSID = creds.substring(0, sep1);
              String remaining = creds.substring(sep1 + 1);
              int sep2 = remaining.indexOf('|');
              if (sep2 > 0) {
                receivedPASS = remaining.substring(0, sep2);
                String remaining2 = remaining.substring(sep2 + 1);
                int sep3 = remaining2.indexOf('|');
                if (sep3 > 0) {
                  receivedSSID2 = remaining2.substring(0, sep3);
                  receivedPASS2 = remaining2.substring(sep3 + 1);
                  hasSecondaryWifi = true;
                } else {
                  if (remaining2.length() > 0) {
                    receivedSSID2 = remaining2;
                    receivedPASS2 = "";
                    hasSecondaryWifi = true;
                  } else {
                    hasSecondaryWifi = false;
                  }
                }
              } else {
                receivedPASS = remaining;
                hasSecondaryWifi = false;
              }
              
              wifiCredentialsReceived = true;
              adq = 1; 
              Serial.println("BLE: Credenciales parseadas (SSID=" + receivedSSID + ", hasSecondary=" + String(hasSecondaryWifi ? "SI" : "NO") + ")");
              
              // 反 反 反 VALIDACIÓN SECUENCIAL DE AMBAS REDES 反 反 反
              lcd.backlight();
              WiFi.mode(WIFI_STA);
              
              // PRUEBA 1: Red Principal
              lcd.clear();
              lcd.setCursor(1, 1);
              lcd.print("Probando WiFi 1...");
              Serial.println("BLE: Intentando conectar a Red Principal: " + receivedSSID);
              
              WiFi.disconnect();
              WiFi.begin(receivedSSID.c_str(), receivedPASS.c_str());
              
              unsigned long startConnect = millis();
              while (WiFi.status() != WL_CONNECTED && millis() - startConnect < 10000) {
                delay(500);
                enviarDatosBLE(); 
              }
              
              // PRUEBA 2: Red Secundaria (Si la principal falló y fue enviada una de respaldo)
              if (WiFi.status() != WL_CONNECTED && hasSecondaryWifi && receivedSSID2.length() > 0) {
                lcd.clear();
                lcd.setCursor(1, 1);
                lcd.print("Probando WiFi 2...");
                Serial.println("BLE: Red principal falló. Intentando Red Secundaria: " + receivedSSID2);
                
                WiFi.disconnect();
                WiFi.begin(receivedSSID2.c_str(), receivedPASS2.c_str());
                
                startConnect = millis();
                while (WiFi.status() != WL_CONNECTED && millis() - startConnect < 10000) {
                  delay(500);
                  enviarDatosBLE(); 
                }
              }
              
              // EVALUACIÓN DE RESULTADOS
              if (WiFi.status() == WL_CONNECTED) {
                connected = true;
                f = 1;
                
                // Si la red 1 falló pero la red 2 se conectó, hacemos un "swap" o guardamos la Red 2 como la principal activa
                if (WiFi.SSID() == receivedSSID2) {
                  // La Red 2 fue la ganadora: la guardamos en la posición principal de la EEPROM
                  strncpy(ssid1, receivedSSID2.c_str(), 32);
                  strncpy(password1, receivedPASS2.c_str(), 32);
                  
                  // Dejamos la Red 1 (incorrecta) en el slot secundario por si acaso, o lo vaciamos
                  strncpy(ssid2, receivedSSID.c_str(), 32);
                  strncpy(password2, receivedPASS.c_str(), 32);
                } else {
                  // La Red 1 se conectó normalmente
                  strncpy(ssid1, receivedSSID.c_str(), 32);
                  strncpy(password1, receivedPASS.c_str(), 32);
                  
                  if (hasSecondaryWifi) {
                    strncpy(ssid2, receivedSSID2.c_str(), 32);
                    strncpy(password2, receivedPASS2.c_str(), 32);
                  }
                }

                // Guardar definitivamente en la EEPROM
                EEPROM.begin(EEPROM_SIZE);
                EEPROM.put(0, ssid1);
                EEPROM.put(32, password1);
                EEPROM.put(64, ssid2);
                EEPROM.put(96, password2);
                EEPROM.commit();
                
                Serial.println("BLE: Conexión exitosa. Credenciales almacenadas.");
                lcd.clear();
                lcd.setCursor(1, 1);
                lcd.print("WiFi Conectado!");
                
                if (bleDeviceConnected && pDataChar != NULL) {
                  pDataChar->setValue("WIFI_OK");
                  pDataChar->notify();
                }
                delay(1500);
              } 
              else {
                // Ambas contraseñas fallaron o no se alcanzó cobertura
                connected = false;
                f = 0;
                WiFi.disconnect(true); 
                
                wifiCredentialsReceived = false; 
                adq = 0;

                Serial.println("BLE: Error de autenticación. Ambas redes fallaron.");
                lcd.clear();
                lcd.setCursor(1, 1);
                lcd.print("Error de Clave!");
                lcd.setCursor(1, 2);
                lcd.print("Ambas fallaron");
                
                if (bleDeviceConnected && pDataChar != NULL) {
                  pDataChar->setValue("WIFI_BAD_PASSWORD");
                  pDataChar->notify();
                }
                delay(2000);
                
                if (!enMediciones) {
                  pixels.fill(pixels.Color(0, 255, 255)); // Volver a Celeste de espera
                  pixels.show();
                  lcd.clear();
                  lcd.setCursor(1, 1);
                  lcd.print(" BLE Conectado! ");
                  lcd.setCursor(1, 2);
                  lcd.print(" Corrija en la App");
                }
              }
            }
          }
        } 
        // ====================================================================
        // CASO 2: COMANDO START
        // ====================================================================
        else if (rxStr == "START") {
          configReceived = true;
          Serial.println("BLE: Configuración inicial confirmada (START).");
        }
        // ====================================================================
        // CASO 3: COMANDOS DE CAMBIO DE MODO
        // ====================================================================
        else if (rxStr.startsWith("MODE:")) {
          String modeStr = rxStr.substring(5);
          int newMode = modeStr.toInt();
          g = newMode;
          
          if (adq == 1) {
            muestreo = (g == 0) ? 1 : 2;
          } else {
            muestreo = (g == 0) ? 3 : 4;
          }
          estado = g;
          Serial.println("BLE: Cambio de modo a " + String(g == 0 ? "ESTACION" : "EXPERIMENTO"));
          
          lcd.backlight();
          lcd.clear();
          lcd.setCursor(1, 1);
          lcd.print(" Modo cambiado: ");
          lcd.setCursor(1, 2);
          lcd.print(g == 0 ? "ESTACION" : "EXPERIMENTO");
          delay(1500);
          
          actualizarLedModo();
          
          if (enMediciones) {
            paginaActual = 1;
            tiempoInicio = millis();
            unsigned long ahora = millis();
            if (ahora < PRIMEROS_MINUTOS) {
              tiempo_B = ahora - intervalo_B;
            } else {
              if (g == 0) {
                tiempo_C = ahora - intervalo_C;
              } else {
                tiempo_B = ahora - intervalo_B;
              }
            }
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
  static unsigned long ultimoEnvioBLE = 0;
  unsigned long ahora = millis();

  if (bleDeviceConnected && pDataChar != NULL) {
    if (ahora - ultimoEnvioBLE >= 2000) {
      ultimoEnvioBLE = ahora;
      
      // Construir JSON de telemetria
      String wifiSSID = (WiFi.status() == WL_CONNECTED) ? WiFi.SSID() : "Desconectado";
      int wifiStatus = (WiFi.status() == WL_CONNECTED) ? 1 : 0;
      
      String jsonPayload = "{\"battery\":" + String(percentage) + 
                           ",\"mode\":" + String(g) + 
                           ",\"wifi\":" + String(wifiStatus) + 
                           ",\"ssid\":\"" + wifiSSID + "\"" +
                           ",\"configured\":" + String((configReceived || enMediciones) ? 1 : 0) + "}";
                           
      pDataChar->setValue(jsonPayload.c_str());
      pDataChar->notify();
      Serial.print("BLE: Enviando telemetria -> ");
      Serial.println(jsonPayload);
    }
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
