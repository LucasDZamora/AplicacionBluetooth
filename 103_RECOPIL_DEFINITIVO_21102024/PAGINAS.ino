void PAGINAS_1()
{
    byte customChar[] = {
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00000,
      B00000
    };
    lcd.createChar(0, customChar); lcd.setCursor(19,0);lcd.write(byte(0)); 
}
void PAGINAS_2()
{
    byte customChar[] = {
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111
    };      
    byte customChar2[] = {
      B00111,
      B00111,
      B00111,
      B00111,
      B00000,
      B00000,
      B00000,
      B00000
    };
  lcd.createChar(0, customChar); // Define el primer carácter en el índice 0
  lcd.createChar(1, customChar2); // Define el segundo carácter en el índice 1
  // Establece el cursor y escribe los caracteres personalizados en la pantalla
  lcd.setCursor(19, 0);
  lcd.write(byte(0));  // Escribe el primer carácter en la posición (19, 0)
  lcd.setCursor(19, 1);
  lcd.write(byte(1));  // Escribe el segundo carácter en la posición (19, 1)
}

void PAGINAS_3()
{
    byte customChar[] = {
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111
    };  

     byte customChar3[] = {
      B00111,
      B00111,
      B00000,
      B00000,
      B00000,
      B00000,
      B00000,
      B00000
    };
  lcd.createChar(0, customChar);  // Define el primer carácter en el índice 0
  lcd.createChar(1, customChar3); // Define el segundo carácter en el índice 1

  // Establece el cursor y escribe los caracteres personalizados en la pantalla
  lcd.setCursor(19, 0);
  lcd.write(byte(0));  // Escribe el primer carácter en la posición (19, 0)

  lcd.setCursor(19, 1);
  lcd.write(byte(0));  // Escribe el primer carácter nuevamente en la posición (19, 1)

  lcd.setCursor(19, 2);
  lcd.write(byte(1));  // Escribe el segundo carácter en la posición (19, 2)
  
}

void PAGINAS_4()
{
 byte customChar[] = {
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111
    };  
  lcd.createChar(0, customChar);

  // Establece el cursor y escribe el carácter personalizado en varias posiciones
  lcd.setCursor(19, 0);
  lcd.write(byte(0));  // Escribe el carácter personalizado en la posición (19, 0)
  lcd.setCursor(19, 1);
  lcd.write(byte(0));  // Escribe el carácter personalizado en la posición (19, 1)
  lcd.setCursor(19, 2);
  lcd.write(byte(0));  // Escribe el carácter personalizado en la posición (19, 2); 
}
void PAGINAS_5()
{
  byte customChar[] = {
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111,
      B00111
    };  

  lcd.createChar(0, customChar);

  // Establece el cursor y escribe el carácter personalizado en varias posiciones
  lcd.setCursor(19, 0);
  lcd.write(byte(0));  // Escribe el carácter personalizado en la posición (19, 0)

  lcd.setCursor(19, 1);
  lcd.write(byte(0));  // Escribe el carácter personalizado en la posición (19, 1)

  lcd.setCursor(19, 2);
  lcd.write(byte(0));  // Escribe el carácter personalizado en la posición (19, 2)

  lcd.setCursor(19, 3);
  lcd.write(byte(0));  // Escribe el carácter personalizado en la posición (19, 3) 
}
