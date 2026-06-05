package com.mica.bluetooth;

import android.content.Intent;
import android.os.Build;
import com.facebook.react.bridge.ReactApplicationContext;
import com.facebook.react.bridge.ReactContextBaseJavaModule;
import com.facebook.react.bridge.ReactMethod;

public class MicaServiceModule extends ReactContextBaseJavaModule {
    private final ReactApplicationContext reactContext;

    public MicaServiceModule(ReactApplicationContext reactContext) {
        super(reactContext);
        this.reactContext = reactContext;
    }

    @Override
    public String getName() {
        return "MicaServiceModule";
    }

    @ReactMethod
    public void startService() {
        Intent intent = new Intent(this.reactContext, MicaForegroundService.class);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            this.reactContext.startForegroundService(intent);
        } else {
            this.reactContext.startService(intent);
        }
    }

    @ReactMethod
    public void stopService() {
        Intent intent = new Intent(this.reactContext, MicaForegroundService.class);
        this.reactContext.stopService(intent);
    }
}