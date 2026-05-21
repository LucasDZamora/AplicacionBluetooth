/*  
(21-10-24 21.24hrs) 
1.Esta versión tiene ordenados los datos que se muestran en el monitor serial
2. Además está configurado el paquete de datos,chequear si el string llega correctamente
3. Datos en monitor serial ordenados
4. Datos en lcd ordenados
5. Incorporación sensor de CO2
6. tiempos de guardado, pantalla y servidor definidos por separado. Confirmar con equipo.
7. Almacena hasta 2 redes wifi
8. crea el archivo con la medicion en la SD
9. Alerta de extracción de SD OK
10. Alerta de desconección wifi OK
11. prueba de casos OK
12. delay de la visualización de pantalla eliminados, reemplazados con millis
13. se corrigío el tiempo de muestreo mostrado en pantalla
14. se corrigió como se muestra la hora y fecha en caso de que el valor sea menor a la decena
15. NO GUARDA EN LA SD/ MUESTRA EL COLOR QUE DEBERIA, GPS funcionando
16. Se corrigió el almacenado en la SD.
17. se cambia la medición de DB por "niveles de ruido de 0 (minimo) a 5 (maximo)
18. Programa con apagado después de los 8 minutos. Después de esto, si se presiona, la luz dura 15 seg
19. Con nueva versión para sensor ML8511
20. Cambio de lógica de entrada a wifi. Por defecto entra a wifi (línea 384 app). Si se desconecta el wifi, busca por 10 min la misma red. O la segunda red guardada en le eeprom. Si no la encuentra
declara la NO conexión.
21. Con nueva versión de medición UV
22. muestra en pantalla más decimales para la presion atmosférica





    LCD   |     SD     |  Conexión BME680| Conexión ML8511         |  NEO 6M   | MICS5524(no usado) | MAX9814 MIC |    RTC    | MEDIDOR_CARGA_BAT | turbidez           | CO2 NDIR
VCC - 3v3 | VCC - 5v   |  VCC- 3.3       | 3v3 - 3.3               | vcc - 5v  | 5v-5v              | vdd-5v      | vcc - 5v  |                   |                    | vcc     - 5v
GND - GND | GND - GND  |  GND- GND       | gnd-gnd                 | gnd - gnd | gnd-gnd            | gnd-gnd     | gnd - gnd |                   |                    | gnd     - gnd
SDA - 21  | CS - 5     |  SDA- 21        | out - 32                | TX - 16   | A0-12              | out-35      | SDA - 21  |  33               |  gpio36            | tx sens - 1
SCL - 22  | MOSI - 23  |  SCL- 22        | EN - GPIO39(sn) y 3.3   | RX - 17   | EN-14              |             | SCL - 22  |                   |                    | rx sens - 3
          | SCK - 18   |                 | VIN - No conectado
          | MISO - 19  |

GY-30     |
vcc-3.3   |
gnd - gnd |
sda - 21  |
scl - 22  | 
dir 0x23  |


*/



//Lib para millis
#include<Arduino.h>

unsigned long tiempo_A = 0;
unsigned long tiempo_B = 0;
unsigned long tiempo_C = 0;
unsigned long tiempo_D = 0;
unsigned long tiempo_E = 0;
unsigned long intervalo_A = 1000;   //5000=5 segundos en milisegundos TIEMPO DE PANTALLA
unsigned long intervalo_B = 29000; // 30000=30 segundos en milisegundos, se ha restado el offset calculado en llegar al servidor
unsigned long intervalo_C = 1800000; // 1800000= 30 minutos en milisegundos
unsigned long intervalo_D = 800;
unsigned long intervalo_E = 3000; // tiempo de guardado en SD
 
unsigned long tiempopantalla=4000; //3000
unsigned long tiempo_actual;
unsigned long PRIMEROS_MINUTOS;
unsigned long previoMillis = 0;

// tiempos backlight LCD
unsigned long tiempo_luzLCD = 120000; // 480000=8 minutos en milisegundos
unsigned long tiempo_apagado = 60000; // 60 segundos en milisegundos
unsigned long tiempo_encendido = 0; // Para controlar el tiempo de encendido del backlight tras pulsar el botón

//-----------------------------------------  CO2 
#include <SoftwareSerial.h>
SoftwareSerial SerialCom (3,1); //RXpin=3,TXpin=1  rx=9 tx=10


byte addArray[] = {0xFF, 0x01, 0x86,0x00, 0x00, 0x00,0x00, 0x00, 0x79};
char dataValue[9];
String dataString = "";
int resHigh;
int resLow;
int pulse;
//  ---------------------------------------SD
#include "FS.h"
#include "SD.h"
#include "SPI.h"
char filename[64];
File dataFile;
char modoOperacion[32];
char submodoOperacion[32];
int CONTADOR=0;
bool x;

#define CS_PIN 5
bool sdPresent = false;


unsigned long previousMillisSD = 0;
//String CADENA;
// ----------------------------------------GPS
#include <TinyGPS++.h>
#include <HardwareSerial.h>
#include <TimeLib.h> // include Arduino time library

// Definición del puerto serial
HardwareSerial SerialGPS(2); // Usar Serial2 para el GPS

// Definición de pines para el GPS
#define RXD2 16
#define TXD2 17

// Inicialización de TinyGPS++
TinyGPSPlus gps;

#define time_offset -14400 // define a clock offset of 3600 seconds (1 hour) ==> UTC -4 para Chile (10800 segundos)
#define daylight_offset_sec 3600 // offset adicional para el horario de verano

float altitud;
bool isDaylightSavings(int gpsDay, int gpsMonth) {
  // Aquí puedes agregar la lógica para determinar si es horario de verano
  // En Chile, el horario de verano típicamente comienza el primer domingo de septiembre y termina el primer domingo de abril
  if ((gpsMonth > 9) || (gpsMonth < 4)) {
    return true;
  } else if (gpsMonth == 9 && gpsDay >= 7) {
    return true;
  } else if (gpsMonth == 4 && gpsDay < 7) {
    return false;
  }
  return false;
}
//-----------------------------------------------
#include <WiFiManager.h> // https://github.com/tzapu/WiFiManager
#include <Adafruit_NeoPixel.h>//Incluimos la librería en el código
#include <Wifi.h>
#include "time.h"

#include <EEPROM.h>
#define EEPROM_SIZE 128

char ssid1[32] = {0};
char password1[32] = {0};
char ssid2[32] = {0};
char password2[32] = {0};
//------------------------------------------BME680
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include "Adafruit_BME680.h"
//--------------------------------------------TURBIDEZ
#define sensor_pin_turb 36 
int read_ADC;
int ntu;
//-------------------------------------------bateria
#define BATTERY_PIN 33
// Definición de constantes
const float R1 = 4700.0;     // Resistencia de 4.7kΩ
const float R2 = 10000.0;    // Resistencia de 10kΩ
const float Vref = 3.3;      // Voltaje de referencia del ADC
const int ADC_max = 4095;    // Máximo valor del ADC de 12 bits
float voltajeBateria;
int porcentajeBateria;
int percentage; 

#define NUM_LECTURAS 10  // Número de lecturas a promediar
float lecturasBateria[NUM_LECTURAS];
int indiceLectura = 0;
bool lecturasCompletadas = false;



// Función para leer el voltaje de la batería
float leerVoltajeBateria() {
  int lecturaADC = analogRead(BATTERY_PIN);  // Leer valor ADC del pin GPIO33
  float Vout = (lecturaADC / (float)ADC_max) * Vref;  // Calcular Vout
  float Vin = Vout * (R1 + R2) / R2;  // Calcular Vin (voltaje de la batería)
  return Vin;
}

// Función para calcular el porcentaje de batería
int calcularPorcentajeBateria(float Vin) {
  if (Vin >= 4.0) {   //4.2
    return 100;
  } else if (Vin <= 3.0) {
    return 0;
  } else {
    return (int)(((Vin - 3.0) / (4.2 - 3.0)) * 100);
  }
}

//------------------------------------------LCD
#include <LiquidCrystal_I2C.h>
LiquidCrystal_I2C lcd(0x27,20,4); 

byte customChar,customChar1,customChar2,customChar3,customChar4; 

unsigned long tiempoInicio = 0;
int paginaActual = 1;  // Iniciar en la primera página


//---------------------------------------------------------GY-30
#include <BH1750.h>
BH1750 lightMeter(0x23);
float lux;
// -------------------------------------------RTC
#include <RTClib.h>
RTC_DS3231 rtc;
byte HORA,MINUTO,SEGUNDO,DIA,MES;
int ANO;
//-------------------------------------------led neopixel
#define PIN        2 
#define NUMPIXELS 1 //Modifica este npumero según el arreglo de LEDs con el que cuentes
Adafruit_NeoPixel pixels(NUMPIXELS, PIN, NEO_GRB + NEO_KHZ800);
#define DELAYVAL 500 //timpo de espera en ms 

//-------------------------------------------PARA SUBIDA DE DATOS
#include <WiFi.h>
#include <HTTPClient.h>
WiFiClient client;

const char* serverName = "http://......php"; //acá va la ruta del servidor
String apiKeyValue = ""; //acá va la clave
String httpRequestData;
String CADENA;
boolean conwifi = false;
bool connected = false;
bool res;
bool marca_tiempo;
//--------------------------------------------MICS5524 ----NO USADO
#include "DFRobot_MICS.h"
#define CALIBRATION_TIME   3                      // Default calibration time is three minutes
#define ADC_PIN   12// A0
#define POWER_PIN 14  //10
DFRobot_MICS_ADC mics(/*adcPin*/ADC_PIN, /*powerPin*/POWER_PIN);
float gasdata;

#define SEALEVELPRESSURE_HPA (1013.25)
Adafruit_BME680 bme; // I2C
float temperatura;
float humedad;
float presion;
float VOC;
//-----------------------------------Variables auxiliares
int adq=0;
int f=0; //variable verificadora de estado wifi
int g=0; //variable verificadora de modo EXPERIMENTO o ESTACIÓN
int pulsadorPin = 34;  //pin pulsador
int valorPulsador = 0; 
int tiempo_refresco_pantalla=3000;   //cada 3 seg

int muestreo=0;         //tiempo de muestreo segun el modo
int estado; // 0=estacion 1=experimento
int y;

//--------------------------------------------------------Sensor UV ML8511

float uvIntensity;
int uvIndex;
int uvLevel;
int refLevel;
float outputVoltage;


int UVOUT = 32; //Output from the sensor pin15
int REF_3V3 = 39; //3.3V power on the ESP32 board pin4

//---------------------------------------------------------Sensor Ruido MAX9814
const int MIC = 35; //the microphone amplifier output is connected to pin A0
// Inicializar variables
int sensorValue;
int minValue = 4095; // Valor inicial máximo posible para un ADC de 12 bits
int maxValue = 0;    // Valor inicial mínimo
int mappedValue;
// Configuración para el cálculo del promedio
const int numSamples = 10;   // Número de muestras para el promedio
int valueBuffer[numSamples]; // Buffer para almacenar los últimos valores de mappedValue
int bufferIndex = 0;         // Índice actual en el buffer
int sum = 0;                 // Suma de los valores en el buffer
int averageValue;
// --------------------------------------------------------variables del GPS
char Time[]  = "00:00";
char Date[]  = "00-00-2000";
byte last_second, Second, Minute, Hour, Day, Month;
int Year;
//------------------------------------------------------------
const char* ntpServer = "south-america.pool.ntp.org";
const long  gmtOffset_sec = -3*60*60; //UTC-4 zona horaria actual 1/oct/2023 chile -4*60*60
const int   daylightOffset_sec = 3600; //3600 si es horario de verano, sino 0 (cero)

void printLocalTime(){
  struct tm timeinfo;
  if(!getLocalTime(&timeinfo)){
   // Serial.println("Failed to obtain time");
    return;
  }
 // Serial.print(&timeinfo, "%A, %B %d %Y %H:%M");
  lcd.setCursor(4,3);
  lcd.print(&timeinfo, "%d:%m:%y %H:%M"); 
  lcd.noAutoscroll();
}


//-----------------------------------------------------------------------------------------------------------------------------------------------------SETUP
void setup() 
{  
 
  //Serial.begin(115200); //115200
   // Iniciar la comunicación serial con el GPS
  SerialGPS.begin(9600, SERIAL_8N1, RXD2, TXD2);
  disableSleepMode(); // Enviar comando para desactivar el modo de sueño
  
  
  //Serial.println("14CORE | MH-Z14 C02 Sensor Test Code");
  //Serial.println("————————————");

  //-------------------------------------------del MIC
   // Inicializar el buffer con ceros
  for (int i = 0; i < numSamples; i++) 
  {
    valueBuffer[i] = 0;
  }
  //------------------------------------------  
  //Serial.println(F("ESP32 - GPS module"));
  iniciacion_SD();

  //--------------------------------------------------------------------ML8511
  
  //startTime = millis(); // Inicia el conteo de tiempo
  
  pinMode(UVOUT, INPUT);
  pinMode(REF_3V3, INPUT);
  
  

  //------------------------------------------
 
  lcd.init();
  lcd.backlight();
  //Wire.begin(25,26); //configura sda 25 y scl 26
//  Wire.begin(32,33);
  //---------------------------------------------------------------------GY30
  if (lightMeter.begin(BH1750::CONTINUOUS_HIGH_RES_MODE)) {
   // Serial.println("Sensor GY-30 iniciado correctamente en la dirección 0x23");
  } else {
    //Serial.println("Error al iniciar el sensor GY-30 en la dirección 0x23");
    //while (1); // Detener el programa si hay un error
  }
  Wire.begin();
  //Serial.println(F("BH1750 Test"));
  //------------------------------------------------------TURBIDEZ
  pinMode(sensor_pin_turb, INPUT);
  //----------------------------------------------------RTC
  if (!rtc.begin()) 
  {
   // Serial.println("No se pudo encontrar RTC");
    //while (1);
  }
  if (rtc.lostPower()) 
  {
    //Serial.println("RTC perdio energia, seteando tiempo");
    lcd.clear();
    lcd.setCursor(2, 1);
    lcd.print("RTC sin energía,setting time");
    delay(2000); 
    rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));
  }
    
  welcome_mica();
  //-----------------------------------------------------lectura botón---------------------------     
  pinMode(pulsadorPin, INPUT_PULLUP); 
  valorPulsador = digitalRead(pulsadorPin);  // Lectura digital de pulsadorPin
  if(valorPulsador==HIGH){valorPulsador=0;}else{valorPulsador=1;}     //1 Y 0-------------------------------------------------------------TEST DE PRUEBA CAMBIO DE LÓGICA
  
  pixels.begin(); // Inicializamos el objeto "pixeles"
  pixels.setBrightness( (255 / 100) * 60 ); // define el brillo del led 60%
  pixels.fill((0, 0, 0)); //apagamos para setear
  pixels.show();   
  delay(DELAYVAL);  
  for(int i=0; i<NUMPIXELS; i++)  // Modificamos el LED #i
    { 
    pixels.setPixelColor(i, pixels.Color(255, 0, 0)); //rojo 
    pixels.show();   // Mandamos todos los colores con la actualización hecha
    delay(DELAYVAL);
    } 
      
 // valorPulsador = digitalRead(pulsadorPin);  // Lectura digital de pulsadorPin
 
  if (valorPulsador == 1) 
  {
   adq=1; //----------------------------------variable para saber si el dispositivo se conectó a wifi
   //Serial.println("MODO CONEXION");
   lcd.clear();
   lcd.setCursor(2, 1);
   lcd.print("Modo WIFIconfig");
   delay(2000); 
   lcd.clear();
   lcd.setCursor(2, 1);
   lcd.print("  Buscando red");
   delay(2000); 
   
   
   
   // Inicialización de la memoria EEPROM
    EEPROM.begin(EEPROM_SIZE);
  //BUSCA LAS REDES. Leer SSID y contraseña de las dos redes de la memoria EEPROM
    EEPROM.get(0, ssid1);
    EEPROM.get(32, password1);
    EEPROM.get(64, ssid2);
    EEPROM.get(96, password2); 

    // Configurar el modo WiFi en modo estación (STA)
  WiFi.mode(WIFI_STA);  
    
  // Intentar conectar a la primera red guardada
  if (strlen(ssid1) > 0) 
  {
    WiFi.begin(ssid1, password1);
    //Serial.print("Conectando a la primera red guardada: ");
    //Serial.println(ssid1);

    unsigned long startTime = millis();
    while (WiFi.status() != WL_CONNECTED && millis() - startTime < 10000) 
    { // 10 segundos de tiempo de espera
          delay(500);
        //  Serial.print(".");
    }

    if (WiFi.status() == WL_CONNECTED) 
    {
        //  Serial.println("\nConexión exitosa!");
        //  Serial.print("Dirección IP: ");
        //  Serial.println(WiFi.localIP());
         lcd.clear();
         lcd.setCursor(2, 1);
         lcd.print(" Red encontrada");
         delay(2000); 
          connected = true;
    } 
  }

  // Si la conexión a la primera red falla, intentar conectar a la segunda red guardada
  if (!connected && strlen(ssid2) > 0) {
    WiFi.begin(ssid2, password2);
    //Serial.print("Conectando a la segunda red guardada: ");
    //Serial.println(ssid2);
    unsigned long startTime = millis();
    while (WiFi.status() != WL_CONNECTED && millis() - startTime < 10000) 
    {      // 10 segundos de tiempo de espera
        delay(500);
      //  Serial.print(".");
    }

    if (WiFi.status() == WL_CONNECTED) {
      //Serial.println("\nConexión exitosa!");
      //Serial.print("Dirección IP: ");
      //Serial.println(WiFi.localIP());
     lcd.clear();
     lcd.setCursor(2, 1);
     lcd.print(" Red encontrada");
     delay(2000); 
      connected = true;
    }
  }   
 if (!connected) 
 {  
   lcd.clear();
   lcd.setCursor(1, 1);
   lcd.print("Red Mica en redes");
   lcd.setCursor(1, 2);
   lcd.print(" WIFI del celular");
   lcd.setCursor(1, 3);
   lcd.print("  ya disponible");
   delay(8000);
    
   WiFi.mode(WIFI_STA); // explicitly set mode, esp defaults to STA+AP MODO ESTACION Y MODO AP
                        //BUSCAR ALGUNA RED CONFIGURADA DESDE EL PROGRAMA SINO, LUEGO BUSCA OTRA RED            
   WiFiManager wm; //WiFiManager, Local intialization. Once its business is done, there is no need to keep it around
    // reset settings - wipe stored credentials for testing, these are stored by the esp library
     wm.resetSettings(); //SOLO PARA EJEMPLO permite resetear los parametros siempre q se desconocte la energia
    /* Automatically connect using saved credentials,
     if connection fails, it starts an access point with the specified name ( "AutoConnectAP"),
     if empty will auto generate SSID, if password is blank it will be anonymous AP (wm.autoConnect())
     then goes into a blocking loop awaiting configuration and will return success result */
    
    // res = wm.autoConnect(); // auto generated AP name from chipid BUSCAR UNA RED
    // res = wm.autoConnect("AutoConnectAP"); // anonymous ap RED SIN CLAVE
    res = wm.autoConnect("MICAv3","12345678"); // password protected ap RED CON CLAVE
 } 
    if(!res) 
    {
      f=0;
      //Serial.println("Failed to connect wifi");
      // ESP.restart();
      pixels.clear();
      pixels.show();
      for(int i=0; i<NUMPIXELS; i++)  // Para cada pixel...  
        {            
          pixels.setPixelColor(i, pixels.Color(255, 0, 0)); //Modificamos el LED #i (255,0,0)=rojo
          pixels.show();   // Mandamos todos los colores con la actualización hecha
          delay(DELAYVAL); // Pausa antes de modificar el color del siguiente LED
        }
    } 
    else 
    {          
      //Serial.println("connected...yeey :)");  //if you get here you have connected to the WiFi  
      f=1; 
      // Obtener los nuevos credenciales
      
      

      
      strcpy(ssid1, WiFi.SSID().c_str());
      strcpy(password1, WiFi.psk().c_str());

      // Mover la red 1 a la red 2
      strcpy(ssid2, ssid1);
      strcpy(password2, password1);

      // Guardar los nuevos credenciales en la red 1
      strcpy(ssid1, WiFi.SSID().c_str());
      strcpy(password1, WiFi.psk().c_str());

      // Guardar los credenciales en la EEPROM
      EEPROM.put(0, ssid1);
      EEPROM.put(32, password1);
      EEPROM.put(64, ssid2);
      EEPROM.put(96, password2);
      EEPROM.commit();     
    }
    //Si no logra conectarse, no entra al loop. Si no encuentra una red configurada, se pone en en modo AP. Crea una red y se hace por cel
    //init and get the time
    //configTime(gmtOffset_sec, daylightOffset_sec, ntpServer);
    //printLocalTime();
  }
  else
  {
       f=0;
       adq=2;
      // Serial.println("No conectado a wifi");
   }  


  //----------------------------------------------------------------------------------------------------------------
  //----------------------------------------------------------------------------------------------------------------
  lcd.clear();  
  MICA_bienve();
  delay(1000);  
  //------------------------------------Inicialización BME680
  bme.begin();
 /* while (!Serial);
 // Serial.println(F("BME680 async test"));
  if (!bme.begin()) {
    //Serial.println(F("Could not find a valid BME680 sensor, check wiring!"));
    //while (1);
  }*/
  // Set up oversampling and filter initialization
  bme.setTemperatureOversampling(BME680_OS_8X);
  bme.setHumidityOversampling(BME680_OS_2X);
  bme.setPressureOversampling(BME680_OS_4X);
  bme.setIIRFilterSize(BME680_FILTER_SIZE_3);
  bme.setGasHeater(320, 150); // 320*C for 150 ms

  lcd.clear();
  lcd.setCursor(0, 1);
  lcd.print(" Espere calibracion");
  delay(2000);
//------------------------------------------------------------------------------------------------------------CALIB_MICS5524(); //3 mins:(
  Wire.begin();
  lcd.clear();
  lcd.setCursor(0, 1);
  lcd.print(" Autocalibracion ok");
  delay(2000);
  in_experimento_mode();
  pinMode(pulsadorPin, INPUT_PULLUP); 
  valorPulsador = digitalRead(pulsadorPin);  // Lectura digital de pulsadorPin
  
  if(valorPulsador==HIGH){valorPulsador=1;}
  else{valorPulsador=0;}
  
  if (valorPulsador == 1) 
  {   
 //  Serial.println("MODO EXPERIMENTO");
   lcd.clear();
   lcd.setCursor(2, 1);
   lcd.print("Modo EXPERIMENTO");
   delay(2000); 
   lcd.clear();
   g=1;
  } 
  else
  {   
  // Serial.println("MODO ESTACION");
   lcd.clear();
   lcd.setCursor(2, 1);
   lcd.print(" Modo ESTACION");
   delay(2000); 
   lcd.clear();
   g=0;
  }
  
//-----------------------------se crea el archivo en la SD con el nombre de FECHA_HORA.txt
   if (adq == 1) 
   {
      strcpy(modoOperacion, "Escuela_");
      if (g == 0) {
        strcat(submodoOperacion, "Estacion");
      } else {
        strcat(submodoOperacion, "Experimento");
      }
   } 
  else 
  {
    strcpy(modoOperacion, "Terreno_");
    if (g == 0) 
    {
      strcat(submodoOperacion, "Estacion");
    } 
    else 
    {
      strcat(submodoOperacion, "Experimento");
    }
  }
   // Inicializar la SD
  if (!SD.begin(CS_PIN)) {
    //Serial.println("Error, SD Initialization Failed");
    sdPresent = false;
  } else {
    //Serial.println("SD Initialization Successful");
    sdPresent = true;
  }
  
  DateTime now = rtc.now();
  int DIA = now.day();
  int MES = now.month();
  int ANO = now.year();
  int HORA = now.hour();
  int MINUTO = now.minute();
  int SEGUNDO = now.second();
  
  // Formatear la fecha y hora en una cadena para el nombre del archivo  
 snprintf(filename, sizeof(filename), "/%s%02d%02d%02d_%s%02d%02d%02d_%s_%s.txt","f",DIA,MES,ANO % 100,"h", HORA,MINUTO,SEGUNDO, modoOperacion,submodoOperacion);
  // Crear y abrir el archivo en la SD
  //dataFile = SD.open(filename, FILE_WRITE);
  //dataFile = SD.open("med.txt", FILE_WRITE);

 
   //entra con f=1 si se conecta // f=0 si no se conecta          
  pixels.clear(); // Apagamos todos los LEDs  
  //----------------------------------------------------Desde acá se define en que modo trabaja y cada cuanto muestrea
  if(adq==1 && y==0)   // -------------------------------------------------------------------------------------SI se conecta al WIFI  --> SACAR LA HORA DE RTC QUE FUE CONFIGURADO DESDE WIFI
  {
    pixels.clear();
    pixels.show();
    for(int i=0; i<NUMPIXELS; i++)                                                           // BLANCO
    {   
        pixels.setPixelColor(i, pixels.Color(255, 225, 255));
        pixels.show();   // Mandamos todos los colores con la actualización hecha
        delay(DELAYVAL); // Pausa antes de modificar el color del siguiente LED
    }   
    MICA_bienve();
    delay(500);  
    lcd.clear();
    lcd.setCursor(3, 1);
    lcd.print(" Modo Resumen ");
    if(g==0)  //--------------------------ESTACION
     {
      muestreo=1; 
      lcd.setCursor(1, 2);lcd.print(" Escuela+Estacion");//Serial.println("Modo ESCUELA+ESTACION");
      pixels.clear();
      pixels.show();
      for(int i=0; i<NUMPIXELS; i++)                                                         // VERDE
      {   
            pixels.setPixelColor(i, pixels.Color(0, 255, 0)); 
            pixels.show();   // Mandamos todos los colores con la actualización hecha
            delay(DELAYVAL); // Pausa antes de modificar el color del siguiente LED
      }
     }
     else if(g==1) //-------------------------EXPERIMENTO
     {
      muestreo=2; 
      lcd.setCursor(1, 2);lcd.print("Escuela+Experimento");//Serial.println("Modo ESCUELA+EXPERIMENTO");
      pixels.clear();
      pixels.show();
      for(int i=0; i<NUMPIXELS; i++)                                                         // AMARILLO
      {   
        pixels.setPixelColor(i, pixels.Color(255,255,0)); 
        pixels.show();   // Mandamos todos los colores con la actualización hecha
        delay(DELAYVAL); // Pausa antes de modificar el color del siguiente LED
      }
      
     }
     delay(2000);
     //adq=3;  
     y=1;      
  }  
  if(adq==0 && y==0)   //f=0  //----------------------------------------------------------------------------------------------NO CONECTADO  ---> SACAR LA HORA DEL RTC
  {
    pixels.clear();
    pixels.show();
    for(int i=0; i<NUMPIXELS; i++)  // 
    {         
    pixels.setPixelColor(i, pixels.Color(255, 0, 0)); //Modificamos el LED #i (255,0,0)=---------------rojo
    pixels.show();   // Mandamos todos los colores con la actualización hecha
    delay(DELAYVAL); // Pausa antes de modificar el color del siguiente LED
    }     
    lcd.clear();
    lcd.setCursor(3,1);
    lcd.print(" Modo Resumen");  
    
    if(g==0)   //---------------------------estación
    {
      muestreo=3;
      lcd.setCursor(1, 2);lcd.print(" Terreno+Estacion");//Serial.println("Modo TERRENO+ESTACION");
      pixels.clear();
      pixels.show();
      for(int i=0; i<NUMPIXELS; i++)                                                         //   AZUL
      {   
        pixels.setPixelColor(i, pixels.Color(255,0,0)); 
        pixels.show();   // Mandamos todos los colores con la actualización hecha
        delay(DELAYVAL); // Pausa antes de modificar el color del siguiente LED
      }
     }
    if(g==1)   //-------------------------experimento
    {
      muestreo= 4; 
      lcd.setCursor(0, 2);lcd.print("Terreno+Experimento");//Serial.println("Modo TERRENO+EXPERIMENTO");
      for(int i=0; i<NUMPIXELS; i++)                                                         //   VIOLETA
      {   
        pixels.setPixelColor(i, pixels.Color(255, 0, 255)); 
        pixels.show();   // Mandamos todos los colores con la actualización hecha
        delay(DELAYVAL); // Pausa antes de modificar el color del siguiente LED
      }
     }
    delay(2000);
    y=1;
  }  
 alertaSD();
 
  
}
//----------------------------------------------------------------------------------------------------------- subrutina ML8511
// Función para convertir intensidad UV a índice UV
int calcularUVIndex(float uvIntensity) {
  if (uvIntensity <= 0.05) return 0; // Bajo
  if (uvIntensity <= 0.15) return 2; // Moderado
  if (uvIntensity <= 0.25) return 5; // Alto
  if (uvIntensity <= 0.35) return 7; // Muy alto
  return 11; // Extremo
}


int averageAnalogRead(int pinToRead)
{
  byte numberOfReadings = 8;
  unsigned int runningValue = 0; 
 
  for(int x = 0 ; x < numberOfReadings ; x++)
    runningValue += analogRead(pinToRead);
  runningValue /= numberOfReadings;
 
  return(runningValue);
}
 
float mapfloat(float x, float in_min, float in_max, float out_min, float out_max)
{
  return (x - in_min) * (out_max - out_min) / (in_max - in_min) + out_min;
}
/*
 * 
 * ---------------------------------------------------------------------------------------------------------------------------------------------FIN SETUP
 * 
 */



/*
 * 
 * ----------------------------------------------------------------------------------------------------------------------------------------------------------LOOP
 */

void loop() 
{ 
  tiempo_actual = millis(); //siempre debe estar en el loop
    
   MEDICIONES();     
   CASOS();
   //GUARDAR_SD();

   MOSTRAR_EN_PANTALLA();

}

//---------------------------------------------------------------------------------------------------------------------------------------------------FIN LOOP
/*
 *  
 * 
 * 
 * 
 * 
 */

// GPS  function for displaying day of the week
void print_wday(byte wday)
{  
 /* switch(wday)
  {
    case 1:  Serial.print(" Domingo  ");   break;
    case 2:  Serial.print(" Lunes ");   break;
    case 3:  Serial.print(" Martes ");   break;
    case 4:  Serial.print(" Miercoles ");   break;
    case 5:  Serial.print(" Jueves ");   break;
    case 6:  Serial.print(" Viernes  ");   break;
    default: Serial.print(" Sabado ");
  }*/

}

void listDir(fs::FS &fs, const char * dirname, uint8_t levels){
   // Serial.printf("Listing directory: %s\n", dirname);

    File root = fs.open(dirname);
    if(!root){
      //  Serial.println("Failed to open directory");
        return;
    }
    if(!root.isDirectory()){
        //Serial.println("Not a directory");
        return;
    }

    File file = root.openNextFile();
    while(file){
        if(file.isDirectory()){
          //  Serial.print("  DIR : ");
          //  Serial.println(file.name());
            if(levels){
                listDir(fs, file.path(), levels -1);
            }
        } else {
          //  Serial.print("  FILE: ");
          //  Serial.print(file.name());
          //  Serial.print("  SIZE: ");
          //  Serial.println(file.size());
        }
        file = root.openNextFile();
    }
}

void createDir(fs::FS &fs, const char * path){
    //Serial.printf("Creating Dir: %s\n", path);
    if(fs.mkdir(path)){
       // Serial.println("Dir created");
    } else {
       // Serial.println("mkdir failed");
    }
}

void removeDir(fs::FS &fs, const char * path){
    //Serial.printf("Removing Dir: %s\n", path);
    if(fs.rmdir(path)){
      //  Serial.println("Dir removed");
    } else {
      //  Serial.println("rmdir failed");
    }
}

void readFile(fs::FS &fs, const char * path){
   // Serial.printf("Reading file: %s\n", path);

    File file = fs.open(path);
    if(!file){
     //   Serial.println("Failed to open file for reading");
        return;
    }

   // Serial.print("Read from file: ");
    while(file.available()){
      //   Serial.write(file.read());
    }
    file.close();
}

void writeFile(fs::FS &fs, const char * path, const char * message){
    //Serial.printf("Writing file: %s\n", path);

    File file = fs.open(path, FILE_WRITE);
    if(!file){
      //  Serial.println("Failed to open file for writing");
        return;
    }
    if(file.print(message)){
       // Serial.println("File written");
    } else {
       //  Serial.println("Write failed");
    }
    file.close();
}

void appendFile(fs::FS &fs, const char * path, const char * message){
    // Serial.printf("Appending to file: %s\n", path);

    File file = fs.open(path, FILE_APPEND);
    if(!file){
       // Serial.println("Failed to open file for appending");
        return;
    }
    if(file.print(message)){
       // Serial.println("Message appended");
    } else {
       // Serial.println("Append failed");
    }
    file.close();
}

void renameFile(fs::FS &fs, const char * path1, const char * path2){
    // Serial.printf("Renaming file %s to %s\n", path1, path2);
    if (fs.rename(path1, path2)) {
        Serial.println("File renamed");
    } else {
        Serial.println("Rename failed");
    }
}

void deleteFile(fs::FS &fs, const char * path){
   // Serial.printf("Deleting file: %s\n", path);
    if(fs.remove(path)){
     //   Serial.println("File deleted");
    } else {
      //  Serial.println("Delete failed");
    }
}

void testFileIO(fs::FS &fs, const char * path){
    File file = fs.open(path);
    static uint8_t buf[512];
    size_t len = 0;
    uint32_t start = millis();
    uint32_t end = start;
    if(file){
        len = file.size();
        size_t flen = len;
        start = millis();
        while(len){
            size_t toRead = len;
            if(toRead > 512){
                toRead = 512;
            }
            file.read(buf, toRead);
            len -= toRead;
        }
        end = millis() - start;
      //  Serial.printf("%u bytes read for %u ms\n", flen, end);
        file.close();
    } else {
      //  Serial.println("Failed to open file for reading");
    }


    file = fs.open(path, FILE_WRITE);
    if(!file){
      //  Serial.println("Failed to open file for writing");
        return;
    }

    size_t i;
    start = millis();
    for(i=0; i<2048; i++){
        file.write(buf, 512);
    }
    end = millis() - start;
   // Serial.printf("%u bytes written for %u ms\n", 2048 * 512, end);
    file.close();

    
} 


void disableSleepMode() { // Nueva función
// Comando UBX para desactivar el modo de sueño (configuración de PMREQ)
  uint8_t disableSleep[] = {
    0xB5, 0x62, // Sync chars
    0x06, 0x3B, // Class and ID
    0x10, 0x00, // Length
    0x00, 0x00, 0x00, 0x00, // version, reserved1, and reserved2
    0x00, 0x00, 0x00, 0x00, // flags, updatePeriod, searchPeriod, gridOffset
    0x00, 0x00, 0x00, 0x00, // onTime, minAcqTime
    0x00, 0x00, // reserved3
    0x00, 0x00 // reserved4
  };
  
  uint8_t ck_a = 0, ck_b = 0;
  for (uint8_t i = 2; i < sizeof(disableSleep); i++) {
    ck_a += disableSleep[i];
    ck_b += ck_a;
  }
  
  // Append checksum
  disableSleep[sizeof(disableSleep) - 2] = ck_a;
  disableSleep[sizeof(disableSleep) - 1] = ck_b;
  
  // Enviar comando al GPS
  SerialGPS.write(disableSleep, sizeof(disableSleep));
}
