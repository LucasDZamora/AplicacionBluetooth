# Guía de Entorno: Depuración (Android)

## Modo Depuración
### Requisitos Previos
* **Android Studio**: Debe estar instalado con el **Android SDK** configurado correctamente.
* **Variables de Entorno**: `ANDROID_HOME` debe apuntar a tu SDK y las `platform-tools` deben estar añadidas al `PATH` de tu sistema.
* **Opciones de desarrollador en el celular**: Ve a *Ajustes > Acerca del teléfono* y presiona **7 veces** el *Número de compilación*. Luego, entra al nuevo menú de desarrollador y activa:
  * **Depuración USB**
  * **Instalar vía USB**
  * **Depuración USB (Ajustes de seguridad)**

### Secuencia de Comandos

1. **Instalar las dependencias del proyecto:**
   (Obligatorio la primera vez que clonas el repositorio o si se han subido cambios recientes en el archivo package.json).
   
   ```bash
   npm install

2. **Verificar que el dispositivo esté correctamente conectado mediante ADB:**
   ```bash
   adb devices

3. **(Si aparece como unauthorized, desbloquea el celular y acepta el permiso emergente marcando "Permitir siempre desde esta computadora").

      Si tienes problemas de conexión persistentes, puedes reiniciar el puente ADB con:**

      ```bash
      adb kill-server
      adb devices
      ```

4. **Compilar e instalar la aplicación nativa en el dispositivo:
(Obligatorio la primera vez que se descarga el proyecto o cuando se agregan/modifican librerías que tocan código nativo en Java/Kotlin).**

      ```bash
      npx expo run:android
      ```
5. **Iniciar el servidor de desarrollo en el día a día limpiando el caché:
(Una vez que la app ya está instalada en el celular, usa este comando para levantar el Metro Bundler de forma rápida sin volver a compilar todo desde cero).**

      ```bash
      npx expo start --clear
      ```
