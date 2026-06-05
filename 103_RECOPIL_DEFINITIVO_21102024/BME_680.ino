void BME680()
{
  unsigned long endTime = bme.beginReading();  // Tell BME680 to begin measurement.
  if (endTime == 0) 
  {
  //  Serial.println(F("Failed to begin reading :("));
    return;
  }
 // Serial.print(F("Reading started at ")); Serial.print(millis()); Serial.print(F(" and will finish at ")); Serial.println(endTime);
//  Serial.println(F("You can do other work during BME680 measurement."));
  delay(50); 
  if (!bme.endReading()) {
 //   Serial.println(F("Failed to complete reading :("));
    return;
  }
  
/*  Serial.print(F("Reading completed at "));
  Serial.println(millis());

  Serial.print(F("Temperature = ")); Serial.print(bme.temperature); Serial.println(F(" *C"));
  Serial.print(F("Pressure = ")); Serial.print(bme.pressure / 100.0); Serial.println(F(" hPa"));
*/
 
  //Serial.print(F("| Humedad = ")); Serial.print(bme.humidity); Serial.print(F(" %"));
 // Serial.print(F("Gas = ")); Serial.print(bme.gas_resistance / 1000.0); Serial.println(F(" KOhms"));

 
 /* Serial.print(F("Approx. Altitude = ")); Serial.print(bme.readAltitude(SEALEVELPRESSURE_HPA)); Serial.println(F(" m"));
  Serial.println();*/
  

  //---------------------------------------BME680
  
  humedad= bme.humidity;
  presion= bme.pressure / 100.0; // acá está el hPa
  presion=presion/1013.25; //101325.0;  RESTRINGIR (AGRANDAR A 4 DECIMALES)
  VOC=bme.gas_resistance/1000.0;
  temperatura= bme.temperature;
  
}
