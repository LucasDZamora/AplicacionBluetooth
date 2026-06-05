void RUIDO()
{
  sensorValue = analogRead(35);
  averageValue=sensorValue;
 /* if (sensorValue < minValue) // Actualizar el valor mínimo
  {  
    minValue = sensorValue;
  }  
  if (sensorValue > maxValue)  // Actualizar el valor máximo
  {
    maxValue = sensorValue;
  }  
  mappedValue = map(sensorValue, minValue, maxValue, 0, 5); // Mapear el valor actual al rango de 0 a 5
  // Actualizar el buffer circular con el nuevo valor de mappedValue
  */
 /* sum -= valueBuffer[bufferIndex];    // Restar el valor antiguo en la posición actual
  valueBuffer[bufferIndex] = mappedValue; // Guardar el nuevo valor en el buffer
  sum += mappedValue;                 // Sumar el nuevo valor a la suma total  
  bufferIndex = (bufferIndex + 1) % numSamples;   // Incrementar el índice del buffer
  averageValue = sum / numSamples; // Calcular el promedio de los últimos 5 valores*/







  // Imprimir los valores
 /* Serial.print("Valor leído: ");
  Serial.print(sensorValue);
  Serial.print(" | Mínimo: ");
  Serial.print(minValue);
  Serial.print(" | Máximo: ");
  Serial.print(maxValue);
  Serial.print(" | Nivel de ruido mapeado: ");
  Serial.print(mappedValue);
  Serial.print(" | Promedio (0-5): ");
  Serial.println(averageValue);*/

  // Añadir un pequeño retraso antes de la siguiente lectura
 // delay(100);
}
