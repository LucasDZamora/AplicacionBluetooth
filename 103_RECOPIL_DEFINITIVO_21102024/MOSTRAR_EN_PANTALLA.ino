void MOSTRAR_EN_PANTALLA()
{
 switch (paginaActual) {
        case 1:
            mostrarPagina1();            
            break;
        case 2:            
            mostrarPagina2();            
            break;
        case 3:            
            mostrarPagina3();            
            break;
        case 4:            
            mostrarPagina4();            
            break;
        case 5:            
            mostrarPagina5();            
            break;       
        default:
            paginaActual = 1;  // Reiniciar al principio si excede el número de páginas
            break;
    }
     
 if (rtc.lostPower()) 
    {   lcd.setCursor(4,3);lcd.println("bateria RTC"); rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));
    }

}

void mostrarPagina1() 
{
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print("Modo :"); 
    if (adq == 1) {
        lcd.print(" Esc");
        if (g == 0) {
            lcd.print("+Estac");
        } else {
            lcd.print("+Exper");
        }
    } else {
        lcd.print(" Terr");
        if (g == 0) {
            lcd.print("+Estac");
        } else {
            lcd.print("+Exper");
        }
    }
    lcd.setCursor(0, 1); lcd.print("RED  : "); 
    if (adq == 1) {
        lcd.print(WiFi.SSID());
    } else {
        lcd.print("NOwifi");
    }
    lcd.setCursor(0, 2); lcd.print("Adqui: ");
    if (adq == 1) {
        lcd.print("wifi+SD+ ");
        if(marca_tiempo==0){lcd.print("30s");}
        else{lcd.print("30m");}
    } else {
        lcd.print("SD+");
        if(marca_tiempo==0){lcd.print("30s");}
        else{lcd.print("30m");}
    }
    lcd.setCursor(0, 3); lcd.print(F("Humed: "));
    lcd.print(bme.humidity);
    lcd.print(F(" %"));

    PAGINAS_1();

    if (millis() - tiempoInicio >= tiempopantalla) {
        tiempoInicio = millis();
        paginaActual = 2;  // Cambiar a la siguiente página
    }
}
void mostrarPagina2()
{
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("Pres: ")); lcd.print(presion,6); lcd.print(F(" atm"));
    lcd.setCursor(0, 1); lcd.print(F("VOC : ")); lcd.print(VOC); lcd.print(F(" PPM"));
    lcd.setCursor(0, 2); lcd.print(F("Temp: ")); lcd.print(temperatura); lcd.print(F(" C"));
    lcd.setCursor(0, 3); lcd.print(F("Radi: ")); lcd.print(uvIntensity); lcd.print(F(" mW/cm2"));

    PAGINAS_2();

    if (millis() - tiempoInicio >= tiempopantalla) {
        tiempoInicio = millis();
        paginaActual = 3;  // Cambiar a la siguiente página
    }
 }
void mostrarPagina3() 
{
    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("IndUV : ")); lcd.print(uvIndex); lcd.print(F(" [0-11]"));
    lcd.setCursor(0, 1); lcd.print(F("Nivluz: ")); lcd.print(lux); lcd.print(F(" lux"));
    lcd.setCursor(0, 2); lcd.print(F("Long  : ")); lcd.print(gps.location.lng(),4);
    lcd.setCursor(0, 3); lcd.print(F("Lat   : ")); lcd.print(gps.location.lat(),4);

    PAGINAS_3();

    if (millis() - tiempoInicio >= tiempopantalla) {
        tiempoInicio = millis();
        paginaActual = 4;  // Cambiar a la siguiente página
    }
}
  
void mostrarPagina4() 
{
    lcd.clear();    
    lcd.setCursor(0, 0); lcd.print(F("Altur  : ")); lcd.print(altitud); lcd.print(F(" m"));
    lcd.setCursor(0, 1); lcd.print(F("Veloc  : ")); lcd.print(gps.speed.kmph()); lcd.print(F(" km/h"));
    lcd.setCursor(0, 2); lcd.print(F("HoraGPS: ")); lcd.print(String(hour())+":"+String(minute())+":"+String(second())); lcd.print(F(" "));
    //lcd.setCursor(0, 3); lcd.print(F("NivRuid: ")); lcd.print(averageValue); lcd.print(F(" [0-5]"));
    lcd.setCursor(0, 3); lcd.print(F("NivRuid: ")); lcd.print("--"); //lcd.print(F(" [0-5]"));

    PAGINAS_4();

    if (millis() - tiempoInicio >= tiempopantalla) {
        tiempoInicio = millis();
        paginaActual = 5;  // Cambiar a la siguiente página
    }
}

void mostrarPagina5() 
{
    //se lee el rtc para mostrar la hora actual
    DateTime now = rtc.now();
    int HORA = now.hour();
    int MINUTO = now.minute();
    int SEGUNDO = now.second();
    int DIA = now.day();
    int MES = now.month();
    int ANO = now.year(); 

//    char buffer[3]; // Buffer para almacenar el formato con dos dígitos

    lcd.clear();
    lcd.setCursor(0, 0); lcd.print(F("Turb: ")); lcd.print(ntu); lcd.print(F(" NTU")); if(ntu<10)lcd.print(" ML");if(ntu>=10 && ntu<30) lcd.print(" Norm");if(ntu>=30)lcd.print(" MS");
    lcd.setCursor(0, 1); lcd.print(F("CO2 : ")); lcd.print(pulse); lcd.print(F(" PPM"));
    lcd.setCursor(0, 2); lcd.print(F("Bat : ")); if(percentage>=25){lcd.print(percentage); lcd.print(F(" %"));}if(percentage<25){lcd.print("CARGAR");};
    lcd.setCursor(2, 3); 
    
   /* sprintf(buffer, "%02d", DIA);lcd.print(buffer);lcd.print(".");sprintf(buffer, "%02d", MES);lcd.print(buffer);lcd.print(".");sprintf(buffer, "%02d", ANO);lcd.print(buffer); */
    lcd.print(DIA); lcd.print("."); lcd.print(MES); lcd.print("."); lcd.print(ANO);  //desde el RTC
    lcd.print(" "); lcd.print(HORA); lcd.print(":"); lcd.print(MINUTO);

  /*  sprintf(buffer, "%02d", HORA);lcd.print(buffer);
    lcd.print(":");sprintf(buffer, "%02d", MINUTO);
    lcd.print(buffer);lcd.print(":");sprintf(buffer, "%02d", SEGUNDO);lcd.print(buffer); */

    PAGINAS_5();

    if (millis() - tiempoInicio >= tiempopantalla) {
        tiempoInicio = millis();
        paginaActual = 1;  // Volver a la primera página después de la quinta
    }
}


     

    
    
   
