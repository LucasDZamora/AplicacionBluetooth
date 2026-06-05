void MOSTRAR_SERIAL()
{
/*S1_h=humedad S1_p=presion  S1_v=voc S1_t=temperatura S2_r=radiacion S2_n=nivelUV
       S3_n=nivelLUZ S4_long=longitud  S4_lat=latitud  S4_a=altura  S4_v=velocidad
       S4_h=horaGPS  S5_i=intensidad  S6_t=tubidez  S7_c02=co2  S8_n=nBateria
       S9_rtc=rtc  S10_f=wifiFecha
       
id  S1_h  S1_p  S1_v  S1_t  S2_r  S2_n  S3_n  S4_long S4_lat  S4_a  S4_v  S4_h  S5_i  S6_t  S7_c02  S8_n  S9_rtc  S10_f nodo 
adq==1 indica conectado a WIFI
*/ 
Serial.println("~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ RESUMEN ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~");  
Serial.print("Modo: ");if(adq==1){Serial.print("Escuela");if(g==0){Serial.print("+Estación");}else{Serial.print("+Experimento");}}
else{Serial.print("Terreno");if(g==0){Serial.print("+Estación");}else{Serial.print("+Experimento");}}
Serial.print("| RED: "); if(adq==1){Serial.print(WiFi.SSID());}else{Serial.print("sin wifi");}
Serial.print("| Adquisición: ");if(adq==1){Serial.print("wifi ");Serial.print(muestreo);Serial.print("min");} else{Serial.print("SD ");Serial.print(muestreo);Serial.print("min");}

Serial.print(F("| Humedad = ")); Serial.print(bme.humidity); Serial.print(F(" %"));
Serial.print(F("| Presión = ")); Serial.print(presion); Serial.print(F(" atm"));
Serial.print(F("| VOC = ")); Serial.print(VOC); Serial.print(F(" PPM"));
Serial.print(F("| Temperatura = ")); Serial.print(temperatura); Serial.println(F(" °C |"));
Serial.print(F("Radiación = ")); Serial.print(uvIntensity); Serial.print(F(" mW/cm2"));
Serial.print(F("| Indice UV = ")); Serial.print(uvIndex); Serial.print(F(" "));
Serial.print(F("| Nivel luz = ")); Serial.print(lux); Serial.print(F(" lux"));
Serial.print(F(" | Long: "));Serial.print(gps.location.lng());
Serial.print(F("| Lat: "));Serial.print(gps.location.lat());
Serial.print(F("| Alt: ")); Serial.print(gps.altitude.meters()); Serial.print(F(" m"));
Serial.print(F("| Velocidad: "));Serial.print(gps.speed.kmph());Serial.println(F(" km/h |"));
Serial.print(F("Hora GPS = ")); Serial.print(Time); Serial.print(F(" "));
Serial.print(F("| Nivel ruido = ")); Serial.print(averageValue); Serial.print(F(""));
Serial.print(F("| Turbidez = ")); Serial.print(ntu); Serial.print(F(" NTU"));
Serial.print(F("| CO2 = ")); Serial.print(pulse); Serial.print(F(" PPM"));
Serial.print(F("| Nivel Bat = ")); if(percentage>=25){Serial.print(percentage); Serial.print(F(" %"));}if(percentage<25){Serial.print("CARGAR");}
DateTime now = rtc.now();
    HORA= now.hour();
    MINUTO= now.minute();
    SEGUNDO= now.second();
    DIA=now.day();
    MES=now.month();
    ANO=now.year();

Serial.print("| Fecha RTC:"); Serial.print(DIA); Serial.print("-"); Serial.print(MES);Serial.print("-");Serial.print(ANO); 
Serial.print(" | Hora RTC:");Serial.print(HORA);Serial.print(":");Serial.print(MINUTO);
Serial.println(" ");

}
