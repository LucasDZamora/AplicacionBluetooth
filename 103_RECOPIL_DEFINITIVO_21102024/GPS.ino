void GPS()
{

  while (SerialGPS.available() > 0) {
    if (gps.encode(SerialGPS.read())) {
      displayInfo();
    }
  }

  if (millis() > 5000 && gps.charsProcessed() < 10) {
  //  Serial.println("No GPS detected: check wiring.");
    while (true);
  }
}

void displayInfo() {
  // Obtener longitud y latitud
  /*if (gps.location.isValid()) {
    Serial.print("Latitude: ");
    Serial.println(gps.location.lat(), 6);
    Serial.print("Longitude: ");
    Serial.println(gps.location.lng(), 6);
  } else {
    Serial.println("Location: Not Available");
  }*/

 /* // Obtener altitud
  if (gps.altitude.isValid()) {
    Serial.print("Altitude: ");
    Serial.print(gps.altitude.meters());
    Serial.println(" meters");
  } else {
    Serial.println("Altitude: Not Available");
  }*/

 /* // Obtener velocidad
  if (gps.speed.isValid()) {
    Serial.print("Speed: ");
    Serial.print(gps.speed.kmph());
    Serial.println(" km/h");
  } else {
    Serial.println("Speed: Not Available");
  }*/

  altitud = gps.altitude.meters();
  if (altitud < 0) {
    altitud = -1 * altitud;
     }

  // Obtener hora y ajustar la hora
  if (gps.time.isValid() && gps.date.isValid()) 
  {
    int gpsHour = gps.time.hour();
    int gpsMinute = gps.time.minute();
    int gpsSecond = gps.time.second();
    int gpsDay = gps.date.day();
    int gpsMonth = gps.date.month();
    int gpsYear = gps.date.year();

    // Ajustar la hora con el desfase horario de Chile
    adjustTimeForChile(gpsHour, gpsMinute, gpsSecond, gpsDay, gpsMonth, gpsYear);

    // Establecer el tiempo ajustado
    setTime(gpsHour, gpsMinute, gpsSecond, gpsDay, gpsMonth, gpsYear);

   /* Serial.print("Time: ");
    if (hour() < 10) Serial.print(F("0"));
    Serial.print(hour());
    Serial.print(":");
    if (minute() < 10) Serial.print(F("0"));
    Serial.print(minute());
    Serial.print(":");
    if (second() < 10) Serial.print(F("0"));
    Serial.print(second());
    Serial.println(); */
  } /*else {
    Serial.println("Time: Not Available"); 
  }*/
}

void adjustTimeForChile(int &hour, int &minute, int &second, int &day, int &month, int &year) {
  time_t t = makeTime(hour, minute, second, day, month, year);
  t += time_offset;
  if (isDaylightSavings(day, month)) {
    t += daylight_offset_sec;
  }
  breakTime(t, hour, minute, second, day, month, year);
}

time_t makeTime(int hour, int minute, int second, int day, int month, int year) {
  tmElements_t tm;
  tm.Hour = hour;
  tm.Minute = minute;
  tm.Second = second;
  tm.Day = day;
  tm.Month = month;
  tm.Year = year - 1970; // tm.Year es el año desde 1970
  return makeTime(tm);
}

void breakTime(time_t t, int &hour, int &minute, int &second, int &day, int &month, int &year) {
  tmElements_t tm;
  breakTime(t, tm);
  hour = tm.Hour;
  minute = tm.Minute;
  second = tm.Second;
  day = tm.Day;
  month = tm.Month;
  year = tm.Year + 1970;  

}
