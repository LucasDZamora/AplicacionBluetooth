void alanube()
{
  if(WiFi.status()== WL_CONNECTED)
  {
     WiFiClient client;
     HTTPClient http;
     http.begin(client, serverName);
     http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
    int httpResponseCode = http.POST(httpRequestData);        // enviar datos   

    http.end();       // Free resources
   // WiFi.disconnect();
   // Serial.println("paquete entregado");       
  }
  else  
    {  
       lcd.clear();
       lcd.setCursor(1, 1);
       lcd.print("sin wifi.Reinicie");      
    }
  
}
