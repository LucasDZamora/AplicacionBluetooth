void CALIB_MICS5524()
{
   while(!Serial);
  while(!mics.begin()){
   // Serial.println("NO Devices MICS!");
    delay(1000);
  }// Serial.println("Device connected successfully MICS!");

  uint8_t mode = mics.getPowerState();
  if(mode == SLEEP_MODE){
    mics.wakeUpMode();
   // Serial.println("wake up sensor success!");
  }else{
   // Serial.println("The sensor is wake up mode");
  }
  while(!mics.warmUpTime(CALIBRATION_TIME)){
   // Serial.println("Espere hasta que el calentamiento esté ok!");
    delay(1000);
  }
}
