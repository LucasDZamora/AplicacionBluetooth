void ML8511()
{
  uvLevel = averageAnalogRead(UVOUT);
  refLevel = averageAnalogRead(REF_3V3);
  
  //Use the 3.3V power pin as a reference to get a very accurate output value from sensor
  outputVoltage = 3.3 / refLevel * uvLevel;
  
  uvIntensity = mapfloat(outputVoltage, 0.99, 2.8, 0.0, 15.0); //Convert the voltage to a UV intensity level
  if (uvIntensity < 0) uvIntensity = 0; // Asegurar que no haya valores negativos
  uvIndex = calcularUVIndex(uvIntensity);
  
}
