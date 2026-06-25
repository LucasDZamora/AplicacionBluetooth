# Guía Paso a Paso: Compilar y Ejecutar en iOS (iPhone Físico) desde macOS

Esta guía está diseñada para que puedas compilar, instalar y probar la aplicación en cualquier iPhone físico utilizando tu MacBook de manera sencilla, paso a paso y sin tecnicismos complejos.

---

## Requisitos Iniciales

Antes de comenzar, asegúrate de tener:
1. **Una MacBook** con la última versión de **Xcode** instalada (disponible gratis en la App Store).
2. **Un iPhone físico** y su respectivo cable USB para conectarlo a la Mac.
3. **Tu cuenta de Apple ID** (la misma que usas en tu celular o iCloud).

---

## PASO 1: Preparar tu iPhone (Activar Modo Desarrollador)

Apple requiere activar una opción de seguridad especial en el teléfono antes de instalar cualquier aplicación de prueba.

1. Desconecta tu iPhone de la Mac (por ahora).
2. En tu iPhone, ve a:
   ```text
   Ajustes > Privacidad y seguridad
   ```
3. Desplázate hacia el final de la pantalla y busca la opción **Modo de desarrollo** (o *Developer Mode*).
4. Activa el interruptor.
5. El iPhone te pedirá **reiniciar** el teléfono para confirmar.
6. Al encenderse, desbloquea el iPhone y presiona **Permitir / Confiar** en el mensaje que aparecerá en pantalla. Introduce tu código de desbloqueo.

---

## PASO 2: Misma Red Wi-Fi

Para que tu iPhone pueda comunicarse con tu MacBook y cargar los cambios del código en tiempo real (Hot Reload):

1. **Conecta tu MacBook y tu iPhone a la misma red Wi-Fi exacta**.
2. *Nota importante:* Si tu router tiene redes 2.4 GHz y 5 GHz, asegúrate de que ambos dispositivos estén conectados a la **misma** frecuencia (ej. ambos en la 2.4 GHz o ambos en la 5 GHz).

---

## PASO 3: Instalar Dependencias en la MacBook

Abre la aplicación **Terminal** en tu Mac y dirígete a la carpeta de tu proyecto. Luego ejecuta los siguientes comandos ordenadamente:

1. **Instalar dependencias del proyecto:**
   ```bash
   npm install
   ```

2. **Instalar dependencias nativas de iOS (CocoaPods):**
   *(Este comando prepara los conectores Bluetooth de iOS)*
   ```bash
   cd ios && pod install && cd ..
   ```

---

## PASO 4: Firmar y Compilar la App para tu iPhone (Primera Vez o Celular Nuevo)

Para que la aplicación se pueda instalar en tu iPhone, Xcode necesita "firmarla" con tu cuenta de Apple.

1. **Conecta tu iPhone a la MacBook** usando el cable USB.
2. Si te aparece una alerta en la Mac o el celular preguntando si deseas **Confiar en este ordenador**, presiona **Confiar** e ingresa tu clave.
3. Ejecuta el comando de compilación automática en la terminal de la Mac:
   ```bash
   npx expo run:ios --device
   ```
4. La terminal te preguntará en qué dispositivo quieres instalar la app. Selecciona el nombre de tu iPhone físico en la lista (ej. `iPhone Kavon`).
5. **Si la compilación se completa sin problemas:** La aplicación se abrirá sola en tu celular y ya puedes saltar al **Paso 5**.

### ⚠️ ¿Qué hacer si sale un error de Firma (Signing/Provisioning)?
Si la terminal arroja un error indicando que no se puede firmar la aplicación (muy común con celulares nuevos), sigue estos pasos sencillos:

1. Abre la carpeta del proyecto en tu Mac y entra a la carpeta `ios`.
2. Busca el archivo que termina en `.xcworkspace` (tiene un ícono azul con una "X") y ábrelo con doble clic. Esto abrirá Xcode automáticamente.
3. En la barra lateral izquierda de Xcode, haz clic sobre el ícono raíz del proyecto (el que se llama **EMAPP**).
4. En el panel del centro, ve a la pestaña **Signing & Capabilities** (Firma y Capacidades).
5. Busca el apartado **Team** (Equipo) y selecciona tu Apple ID en la lista desplegable.
   * *Si no aparece tu Apple ID:* Haz clic en "Add an Account...", ingresa tu correo y contraseña de Apple, y luego selecciónalo.
6. En **Bundle Identifier**, si Xcode muestra un error en rojo, simplemente cambia el texto a algo único añadiendo tu nombre al final (ej. `com.tuusuario.aplicacionbluetooth`).
7. Presiona el botón de **Play** (triángulo arriba a la izquierda en Xcode) para compilar e instalar la app directamente desde allí.

---

## PASO 5: Confiar en el Desarrollador (Solo la primera vez en el iPhone)

Al abrir la aplicación en tu iPhone por primera vez, es probable que veas un aviso de **Desarrollador no fiable**. Esto es normal en Apple:

1. En tu iPhone, ve a:
   ```text
   Ajustes > General > Gestión de dispositivos y VPN
   ```
2. Debajo de "App de desarrollador", verás tu correo de Apple ID. Toca sobre él.
3. Presiona **Confiar en [Tu Correo]**.
4. ¡Listo! Ya puedes pulsar el ícono de la app en la pantalla de inicio del celular y se abrirá correctamente.

---

## PASO 6: Desarrollo Diario (Trabajar sin cables)

Una vez que la aplicación ya está instalada en tu iPhone físico, **no es necesario** volver a compilar con Xcode ni usar cables USB a menos que instales una librería nueva en el archivo `package.json`.

Para el trabajo diario, haz lo siguiente:

1. Asegúrate de estar en la misma red Wi-Fi (Paso 2).
2. En la terminal de tu Mac, corre el servidor de desarrollo:
   ```bash
   npx expo start --dev-client
   ```
3. Abre la aplicación en tu iPhone. Esta detectará automáticamente el servidor Metro de tu Mac y cargará el código.
4. Cada vez que hagas un cambio y guardes un archivo en tu editor, verás reflejados los cambios al instante en tu iPhone.

---

## Resolución de Problemas

* **La app se queda en blanco o dice "No connection to bundle":** Comprueba el Paso 2. Es 100% seguro que el teléfono y la Mac perdieron la conexión en la misma red Wi-Fi, o que tu firewall de red está bloqueando la comunicación.
* **Limpiar memoria caché de Expo:** Si notas comportamientos extraños en el código nuevo que no se actualiza, detén la terminal con `Ctrl + C` y ejecuta:
  ```bash
  npx expo start --clear
  ```
