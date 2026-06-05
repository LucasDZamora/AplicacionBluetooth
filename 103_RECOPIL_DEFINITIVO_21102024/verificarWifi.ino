unsigned long ultimoIntentoReconexion = 0;
const unsigned long intervaloReconexion = 30000; // intentar reconectar cada 30 segundos de fondo

void verificarWifi() {
    // Si el WiFi está desconectado y estábamos en modo con wifi (adq == 1)
    if (WiFi.status() != WL_CONNECTED) 
    {
        if (connected) {
            // Se acaba de perder la conexión
            connected = false;
            f = 0;
            
            // LED en rojo para avisar desconexión de Wi-Fi
            pixels.fill(pixels.Color(255, 0, 0)); 
            pixels.show();
            
            lcd.backlight();
            lcd.clear();
            lcd.setCursor(1, 1);
            lcd.print("  WiFi perdido");
            lcd.setCursor(1, 2);
            lcd.print(" buscando de fondo");
            delay(2000);
        }

        unsigned long actual = millis();
        if (actual - ultimoIntentoReconexion >= intervaloReconexion) 
        {
            ultimoIntentoReconexion = actual;
            Serial.println("WiFi: Intento de reconexión no bloqueante de fondo...");

            // Intentar reconectar a la primera red guardada
            if (strlen(ssid1) > 0) 
            {
                WiFi.disconnect();
                WiFi.begin(ssid1, password1);
                unsigned long waitStart = millis();
                // Espera corta no bloqueante
                while (WiFi.status() != WL_CONNECTED && millis() - waitStart < 2000)  
                {
                    delay(100);
                    enviarDatosBLE(); // Seguir atendiendo BLE
                }
            }

            // Si no se conecta a la primera red, intentar la segunda
            if (WiFi.status() != WL_CONNECTED && strlen(ssid2) > 0) 
            {
                WiFi.disconnect();
                WiFi.begin(ssid2, password2);
                unsigned long waitStart = millis();
                while (WiFi.status() != WL_CONNECTED && millis() - waitStart < 2000) 
                {  
                    delay(100);
                    enviarDatosBLE(); // Seguir atendiendo BLE
                }
            }
            
            // Verificar si logramos reconectar
            if (WiFi.status() == WL_CONNECTED) {
                connected = true;
                f = 1;
                
                // LED en verde indicando conexión recuperada
                pixels.fill(pixels.Color(0, 255, 0));
                pixels.show();
                
                lcd.clear();
                lcd.setCursor(1, 1);
                lcd.print(" WiFi reconectado");
                delay(2000);
            }
        }
    }
}
