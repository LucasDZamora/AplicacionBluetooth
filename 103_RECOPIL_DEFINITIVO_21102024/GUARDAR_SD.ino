void GUARDAR_SD()
{   
   DateTime now = rtc.now();
    int HORA = now.hour();
    int MINUTO = now.minute();
    int SEGUNDO = now.second();
    int DIA = now.day();
    int MES = now.month();
    int ANO = now.year();
  
  unsigned long startwrite=millis();
  //dataFile = SD.open(path, FILE_APPEND);
  dataFile = SD.open(filename, FILE_APPEND);    //FILE_WRITE 
  
  //delay(1000);
 // if (dataFile) 
 //   {
    //dataFile.print("NUEVA MEDICIÓN: "); 
   // Serial.print(CADENA); 
    //Serial.println(" ");
    int minidelay=10; // (21 delays)
    dataFile.print("ID="+ String(CONTADOR));  //-----------------------------------------------------------el STRING con los datos
    delay(minidelay);
    dataFile.print(" S1_h="+String(humedad));
    delay(minidelay);
    dataFile.print(" S1_p="+String(presion,6)); //muestra 4 decimales
    delay(minidelay);
    dataFile.print(" S1_v="+String(VOC));
    delay(minidelay);
    dataFile.print(" S1_t="+String(temperatura));
    delay(minidelay);
    dataFile.print(" S2_r="+String(uvIntensity));
    delay(minidelay);
    dataFile.print(" S2_n="+String(uvIndex));
    delay(minidelay);
    dataFile.print(" S3_n="+String(lux));
    delay(minidelay);
    dataFile.print(" S4_long="+String(gps.location.lng(),4));
    delay(minidelay);
    dataFile.print(" S4_lat="+String(gps.location.lat(),4));
    delay(minidelay);    
    dataFile.print(" S4_a="+String(altitud,4));
    delay(minidelay);
    dataFile.print(" S4_v="+String(gps.speed.kmph()));
    delay(minidelay);
    dataFile.print(" S4_h="+String(String(hour())+":"+String(minute())+":"+String(second())));
    delay(minidelay);
    dataFile.print(" S5_i="+String("--"));
    delay(minidelay);
    dataFile.print(" S6_t="+String(ntu));
    delay(minidelay);
    dataFile.print(" S7_c02="+String(pulse));
    delay(minidelay);
    dataFile.print(" S8_n="+String(percentage));
    delay(minidelay);
    dataFile.print(" S9_rtc="+String(String(HORA)+":"+String(MINUTO)+":"+String(SEGUNDO)));
    delay(minidelay);
    dataFile.print(" S10_f="+String(String(DIA)+":"+String(MES)+":"+String(ANO)));
    delay(minidelay);
    dataFile.print(" nodo=" + WiFi.macAddress());
    delay(minidelay);
    dataFile.println(" estado=" + String(estado));
    delay(minidelay);
     
    dataFile.close();    
    CONTADOR++;    
    unsigned long endwrite=millis();
    lcd.setCursor(16,0);lcd.print(endwrite-startwrite);lcd.print(" ms"); 
    
    x=0; 
}
