void verificarWifi() {
    // Si el WiFi está desconectado
    if (WiFi.status() != WL_CONNECTED) 
    {   lcd.backlight();
        lcd.clear();
        lcd.setCursor(1, 1);
        lcd.print("   WiFi perdida");
        lcd.setCursor(1, 2);
        lcd.print("   buscando....");
        delay(1000);
        
        // Tiempo total de reconexión (60 segundos)
        unsigned long tiempoBusqueda = 600000;  // tarda 5 minutos en buscar, si no encuentra nada dentro de 10 minutos, declara la NO CONEXIÓN.
        unsigned long tiempoInicio = millis();

        // Intentar reconectar a la primera red guardada
        WiFi.begin(ssid1, password1);
        unsigned long startTime = millis();
        while (WiFi.status() != WL_CONNECTED && millis() - startTime < tiempoBusqueda/2)  
        {
            delay(500);
        }

        // Si no se conecta a la primera red, intentar la segunda
        if (WiFi.status() != WL_CONNECTED) 
        {
            WiFi.begin(ssid2, password2);
            startTime = millis();
            while (WiFi.status() != WL_CONNECTED && millis() - startTime < tiempoBusqueda/2) 
            {  
                delay(500);
            }
        }
        
        // Si no se conecta a ninguna red
        if (WiFi.status() != WL_CONNECTED) {
            lcd.clear();
            lcd.setCursor(1, 1);
            lcd.print("sin WiFi. Reinicie");
            while(1);  // Queda en bucle infinito esperando reinicio
        } else {
            lcd.clear();
            lcd.setCursor(1, 1);
            lcd.print(" WiFi reconectado");
            delay(2000);  // Mensaje temporal para mostrar la reconexión exitosa
        }
    }
}
