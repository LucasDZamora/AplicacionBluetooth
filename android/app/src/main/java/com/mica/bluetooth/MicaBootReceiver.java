package com.mica.bluetooth;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Build;

public class MicaBootReceiver extends BroadcastReceiver {
    @Override
    public void onReceive(Context context, Intent intent) {
        if (Intent.ACTION_BOOT_COMPLETED.equals(intent.getAction())) {
            // Esto asegura que si necesitas revivir el servicio al reiniciar el sistema operativo,
            // puedes lanzar un intent aquí. Por ahora, se mantendrá listo y declarado.
            System.out.println("MICA: Teléfono iniciado correctamente.");
        }
    }
}