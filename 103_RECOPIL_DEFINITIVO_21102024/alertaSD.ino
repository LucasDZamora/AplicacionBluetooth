void alertaSD()
{
     for(int i=0;i<4;i++)
     {dataFile = SD.open(filename, FILE_WRITE);  }
     if(!dataFile)
     {
     lcd.clear();
     lcd.setCursor(2, 1);
     lcd.print("sin SD. Reinicie");
     //delay(200);
     while(1);
     }
    for(int i=0;i<4;i++)
     {dataFile = SD.open(filename, FILE_WRITE);  }
   // dataFile = SD.open(filename, FILE_WRITE);
     if (!SD.begin(CS_PIN)) 
     {
     lcd.clear();
     lcd.setCursor(2, 1);
     lcd.print("sin SD. Reinicie");
     //delay(200);
     while(1);
      } 
}
