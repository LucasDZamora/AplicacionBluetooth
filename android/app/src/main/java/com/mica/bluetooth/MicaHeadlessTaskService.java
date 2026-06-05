package com.mica.bluetooth;

import android.content.Intent;
import android.os.Bundle;
import androidx.annotation.Nullable;
import com.facebook.react.HeadlessJsTaskService;
import com.facebook.react.bridge.Arguments;
import com.facebook.react.jstasks.HeadlessJsTaskConfig;

public class MicaHeadlessTaskService extends HeadlessJsTaskService {
    @Nullable
    @Override
    protected HeadlessJsTaskConfig getTaskConfig(Intent intent) {
        Bundle extras = intent.getExtras();
        if (extras != null) {
            return new HeadlessJsTaskConfig(
                "MicaBackgroundBleTask", // Este nombre debe coincidir en JS
                Arguments.fromBundle(extras),
                5000, // Timeout en ms para la tarea (0 para infinito, pero requiere cuidado)
                true  // Permitir ejecuciones en primer plano
            );
        }
        return null;
    }
}