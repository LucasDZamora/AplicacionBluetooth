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
# Guía de Entorno: Depuración (Android)

## Modo Depuración

### Requisitos Previos

- Tener instalado:
  - Node.js
  - Android Studio
  - Android SDK correctamente configurado

- Tener las variables de entorno configuradas:
  - `ANDROID_HOME`
  - `platform-tools` agregado al `PATH`

- Celular Android y computador conectados a la **misma red WiFi**

---

# Activar Opciones de Desarrollador en Android

Ir a:

```text
Ajustes > Acerca del teléfono
```

Presionar **7 veces** sobre:

```text
Número de compilación
```

Luego entrar a:

```text
Ajustes > Opciones de desarrollador
```

Y activar:

- Depuración inalámbrica
- Instalar vía USB (si aparece)
- Depuración USB (opcional)
- Permitir depuración por red local

---

# Instalación del Proyecto

## 1. Clonar el repositorio

```bash
git clone https://github.com/LucasDZamora/AplicacionBluetooth.git
```

Entrar al proyecto:

```bash
cd AplicacionBluetooth
```

---

## 2. Instalar dependencias

(Este paso es obligatorio la primera vez que se descarga el proyecto o cuando cambian dependencias del `package.json`).

```bash
npm install
```

---

## 3. Conectar el dispositivo mediante depuración inalámbrica

### En Android Studio

Abrir:

```text
Device Manager
```

Luego:

```text
Pair Devices Using Wi-Fi
```

### En el celular

Ir a:

```text
Opciones de desarrollador > Depuración inalámbrica
```

Seleccionar:

```text
Vincular dispositivo con código
```

Ingresar el IP, puerto y código solicitado por Android Studio.

---

## 4. Compilar e instalar la aplicación

(Este paso es obligatorio la primera vez o cuando se agregan/modifican librerías nativas).

```bash
npx expo run:android
```

Esto:

- Compila el proyecto Android
- Instala automáticamente la app en el celular
- Genera el cliente de desarrollo necesario

---

## 6. Iniciar el servidor de desarrollo

Una vez instalada la aplicación:

```bash
npx expo start --dev-client --tunnel
```

Esto abrirá Metro Bundler y mostrará un QR.

---

# Uso de la Aplicación

## Modo Escuela (Con WiFi)

Si el celular tiene conexión WiFi:

1. Ejecutar:

```bash
npx expo start --dev-client --tunnel
```

2. Escanear el QR generado por Expo

3. La aplicación se conectará al servidor automáticamente

4. Se utilizará el modo conectado / escuela

---

## Modo Terreno (Sin WiFi)

Si el celular NO tiene conexión WiFi:

- Abrir la aplicación normalmente
- La aplicación funcionará en modo terreno
- Se utilizarán funcionalidades offline y conexión BLE local

---

# Desarrollo Diario

Después de que la app ya está instalada:

```bash
npx expo start --dev-client --tunnel
```

No es necesario volver a ejecutar:

```bash
npx expo run:android
```

excepto cuando:

- Se agregan librerías nativas
- Se modifica código Android nativo
- Se limpia completamente el proyecto

---

## Limpiar caché de Expo

```bash
npx expo start --clear
```

