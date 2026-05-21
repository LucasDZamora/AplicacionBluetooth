void TURB()
{
read_ADC = analogRead(sensor_pin_turb);
if(read_ADC>208)read_ADC=208;

ntu = map(read_ADC, 0, 208, 300, 0); 
 
/*lcd.setCursor(0,0);
lcd.print("Turbidity: ");
lcd.print(ntu);
lcd.print("  "); */
/*
lcd.setCursor(0,1);//set cursor (colum by row) indexing from 0
if(ntu<10)            lcd.print("Water Very Clean");
if(ntu>=10 && ntu<30) lcd.print("Water Norm Clean");
if(ntu>=30)           lcd.print("Water Very Dirty");
*/

}
  
