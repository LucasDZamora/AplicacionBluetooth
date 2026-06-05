void MICS5524()
{
/**!
    Gas type:
    MICS-4514 You can get all gas concentration
    MICS-5524 You can get the concentration of CH4, C2H5OH, H2, NH3, CO
    MICS-2714 You can get the concentration of NO2
      Methane          (CH4)    (1000 - 25000)PPM
      Ethanol          (C2H5OH) (10   - 500)PPM
      Hydrogen         (H2)     (1    - 1000)PPM
      Ammonia          (NH3)    (1    - 500)PPM
      Carbon Monoxide  (CO)     (1    - 1000)PPM
      Nitrogen Dioxide (NO2)    (0.1  - 10)PPM
  */
  gasdata = mics.getGasData(CH4); //1000 - 25000 para el Metano
  //Serial.print("| ");
  //Serial.print(gasdata,1);
  //Serial.print(" PPM de Metano |");
  //delay(100);
  //mics.sleepMode();
  
}
