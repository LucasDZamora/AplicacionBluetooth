void welcome_mica()
{
  lcd.setCursor(1, 0);
  lcd.print("------------------");
  lcd.setCursor(1, 1);
  lcd.print("|     -MICA-     |");
  lcd.setCursor(1, 2);
  lcd.print("|   Bienvenido   |");
  lcd.setCursor(1, 3); 
  lcd.print("------------------");
  delay(2000);   
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("      Mantenga    ");
  lcd.setCursor(0, 1);
  lcd.print("  boton presionado");
  lcd.setCursor(1, 2);
  lcd.print("   para sin WIFI"); //sin WIFI
  lcd.setCursor(1, 3);
  lcd.print("     en 4 seg");
  delay(1000);
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("      Mantenga    ");
  lcd.setCursor(0, 1);
  lcd.print("  boton presionado");
  lcd.setCursor(1, 2);
  lcd.print("   para sin WIFI"); //sin WIFI
  lcd.setCursor(1, 3);
  lcd.print("     en 3 seg");
  delay(1000);
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("      Mantenga    ");
  lcd.setCursor(0, 1);
  lcd.print("  boton presionado");
  lcd.setCursor(1, 2);
  lcd.print("   para sin WIFI"); //sin WIFI
  lcd.setCursor(1, 3);
  lcd.print("     en 2 seg");
  delay(1000);
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("      Mantenga    ");
  lcd.setCursor(0, 1);
  lcd.print("  boton presionado");
  lcd.setCursor(1, 2);
  lcd.print("   para sin WIFI"); //sin WIFI
  lcd.setCursor(1, 3);
  lcd.print("     en 1 seg");
  delay(1000);
}
