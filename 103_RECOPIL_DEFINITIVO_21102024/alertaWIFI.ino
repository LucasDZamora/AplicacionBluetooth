
void alertaWIFI()
{
  if(WiFi.SSID() == "")
  {
     lcd.clear();
     lcd.setCursor(1, 1);
     lcd.print("sin WiFi. Reinicie");
     //delay(200);
     while(1);      
  }
}
