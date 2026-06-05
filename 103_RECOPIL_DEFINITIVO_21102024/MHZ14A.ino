void MHZ14A()
{
SerialCom.begin(9600);
SerialCom.write(addArray, 9);
SerialCom.readBytes(dataValue, 9);
resHigh = (int) dataValue[2];
resLow = (int) dataValue[3];
pulse = (256*resHigh)+resLow;
dataString = String(pulse);
//Serial.print("PPM : ");
//Serial.println(pulse);


//delay(1000); 
}
