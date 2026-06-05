void MEDICIONES()
{ 
   
  for(int i=0; i<10; i++)
  {
  GPS();
  }
  BME680();
  ML8511();
  GY30();
  //MICS5524(); //DESCARTADO
  RUIDO();
  MHZ14A();
  TURB();
  BATERIA();
  if(adq==1){  verificarWifi();}
  //alertaWIFI();
  //alertaSD();
  
  valorPulsador = digitalRead(pulsadorPin);  // Lectura digital de pulsadorPin
  if(valorPulsador==HIGH){valorPulsador=1;}else{valorPulsador=0;}  

  DateTime now = rtc.now();
    int HORA = now.hour();
    int MINUTO = now.minute();
    int SEGUNDO = now.second();
    int DIA = now.day();
    int MES = now.month();
    int ANO = now.year();
    
              
   httpRequestData = "api_key="+apiKeyValue +
                        "&S1_h="+String(humedad) +
                        "&S1_p="+String(presion,6) + //muestra 6 decimales
                        "&S1_v="+String(VOC) +
                        "&S1_t="+String(temperatura) +
                        "&S2_r="+String(uvIntensity) +
                        "&S2_n="+String(uvIndex) +
                        "&S3_n="+String(lux) +
                        "&S4_long="+String(gps.location.lng(),4) +
                        "&S4_lat="+String(gps.location.lat(),4) +
                        "&S4_a="+String(altitud,1) +
                        "&S4_v="+String(gps.speed.kmph()) +
                        "&S4_h="+String(String(hour())+":"+String(minute())+":"+String(second())) +
                        "&S5_i="+String(averageValue) +
                        "&S6_t="+String(ntu) +
                        "&S7_c02="+String(pulse) +
                        "&S8_n="+String(percentage) +
                        "&S9_rtc="+String(String(HORA)+":"+String(MINUTO)+":"+String(SEGUNDO))+
                        "&S10_f="+String(String(DIA)+":"+String(MES)+":"+String(ANO))+
                        "&nodo=" + WiFi.macAddress() +
                        "&estado=" + String(estado);
                        
        /*      CADENA = "ID="+ String(CONTADOR) +
                    " S1_h="+String(humedad)+
                    " S1_p="+String(presion)+
                    " S1_v="+String(VOC)+
                    " S1_t="+String(temperatura)+
                     " S2_r="+String(uvIntensity)+
                     " S2_n="+String(uvIndex)+
                     " S3_n="+String(lux)+
                     " S4_long="+String(gps.location.lng(),4)+
                     " S4_lat="+String(gps.location.lat(),4)+
                     " S4_a="+String(gps.altitude.meters())+
                     " S4_v="+String(gps.speed.kmph())+
                     " S4_h="+String(String(hour())+":"+String(minute())+":"+String(second()))+
                     " S5_i="+String(dB)+
                     " S6_t="+String("Turbidez")+
                     " S7_c02="+String(pulse)+
                     " S8_n="+String("BAT")+
                     " S9_rtc="+String(String(HORA)+":"+String(MINUTO)+":"+String(SEGUNDO))+
                     " S10_f="+String(String(DIA)+":"+String(MES)+":"+String(ANO))+
                     " nodo=" + WiFi.macAddress() +
                     " estado=" + String(estado);
                     ""; */
  
    
}
