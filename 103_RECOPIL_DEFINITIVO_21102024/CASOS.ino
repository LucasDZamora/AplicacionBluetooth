void CASOS()
{
  //***************************************************  ANTES DE 30 MIN ************************************************************************************************************* 
//unsigned long tiempo_A = 0UL;
//unsigned long tiempo_B = 0UL;
//unsigned long tiempo_C = 0UL;
//unsigned long tiempo_D = 0UL;
//unsigned long tiempo_E = 0UL;
//unsigned long intervalo_A = 1000UL;   //5000=5 segundos en milisegundos TIEMPO DE PANTALLA
//unsigned long intervalo_B = 29000UL; // 30000=30 segundos en milisegundos, se ha restado el offset calculado en llegar al servidor
//unsigned long intervalo_C = 1800000UL; // 1800000= 30 minutos en milisegundos
//unsigned long intervalo_D = 800UL;
//unsigned long intervalo_E = 15000UL; // tiempo de guardado en SD

//tiempo_actual = millis();
PRIMEROS_MINUTOS = 1800000UL; // 1800000= 30 minutos en milisegundos


 //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx 
// Control del backlight
    if (tiempo_actual < tiempo_luzLCD) {
        // Antes de los 8 minutos, el backlight siempre encendido
        lcd.backlight();
    } else {
        // Después de los 8 minutos
        if (valorPulsador == 1) {
            // Si el botón se presiona, encender el backlight y reiniciar el tiempo de encendido
            lcd.backlight();
            tiempo_encendido = tiempo_actual;
        } else if (tiempo_actual - tiempo_encendido < tiempo_apagado) {
            // Mantener el backlight encendido por 15 segundos después de presionar el botón
            lcd.backlight();
        } else {
            // Apagar el backlight después de 15 segundos
            lcd.noBacklight();
        }
    }
 //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  
 if(tiempo_actual<PRIMEROS_MINUTOS) //--------------------------------------------------------------------------ANTES DE LOS 30 MIN
  {   
      if(tiempo_actual - tiempo_B >= intervalo_B)  //----------30 SEG
          {  
           if(muestreo==1){estado=0;} else {estado=1;}
             if(adq==1)   //---------------------------------------ESCUELA WIFI ---
                  {                   
                  alanube();  //MOSTRAR_SERIAL();                  
                  tiempo_B=tiempo_actual;
                  x=1;
                              
                   }             
             else   //--------------------------------------- TERRENO SIN WIFI
                  {
                  tiempo_B=tiempo_actual;  
                  x=1;         
                  } 
            marca_tiempo=0;   //marca de tiempo para mostrar en lcd
            GUARDAR_SD();
           }           
    }
    
 //***************************************************  SOBRE LOS 30 MIN *************************************************************************************************************   

 if(tiempo_actual>=PRIMEROS_MINUTOS)    //----------------------------------------------------------------------------------------------------------------DESPUES DE 30 MIN
  { 
   if(adq==1) //--------------------------------------ESCUELA (CON WIFI)--------------------------------------------------------
    {
      if(g==0)   //g==0 ESTACION
      {
         if(tiempo_actual - tiempo_C >= intervalo_C) //-------------------------30 MIN -----MODO ESTACION
         {
            estado=0;
            alanube();//MOSTRAR_SERIAL();Serial.println("Escuela+Estación"); 
            tiempo_C=tiempo_actual; 
            marca_tiempo=1; // para mostrar en pantalla la frecuencia de muestreo
            x=1;   
            GUARDAR_SD();
         }        
      }  
      else    //g==1 EXPERIMENTO
      {
         if(tiempo_actual - tiempo_B >= intervalo_B) //----------------30 SEG---------MODO EXPERIMENTO
         {
          estado=1;
          alanube(); 
          // MOSTRAR_SERIAL();Serial.println("Escuela+Experimento");
          
          tiempo_B=tiempo_actual;
          marca_tiempo=0; // para mostrar en pantalla la frecuencia de muestreo
          x=1;   
          GUARDAR_SD();
         } 
     }alertaWIFI();
    }
   else  //adq==0----------------------------------------TERRENO (SIN WIFI) SOLO SD ---------------------------------------------------------
     {
         if(g==0) //g==0 ESTACION
         {
          if(tiempo_actual - tiempo_C >= intervalo_C) //-------------------------30 MIN-----------------------ESTACION
           {
            estado=0;
            tiempo_C=tiempo_actual;
            marca_tiempo=1;// para mostrar en pantalla la frecuencia de muestreo
            x=1;
            GUARDAR_SD();   
           } 
          }
         else   //g==1 EXPERIMENTO
         {
           if(tiempo_actual - tiempo_B >= intervalo_B) //------------------------- 30 SEG-----------------EXPERIMENTO
           {
              estado=1;
              tiempo_B=tiempo_actual;
              marca_tiempo=0; // para mostrar en pantalla la frecuencia de muestreo
              x=1;  
              GUARDAR_SD(); 
           } 
         }   
     }
  } 
  
}
