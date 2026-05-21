void BATERIA()
{
/*
 voltajeBateria = leerVoltajeBateria();
 porcentajeBateria = calcularPorcentajeBateria(voltajeBateria);
 percentage = porcentajeBateria;  */

     voltajeBateria = leerVoltajeBateria();
    porcentajeBateria = calcularPorcentajeBateria(voltajeBateria);
    
    // Añadir la nueva lectura al arreglo de lecturas
    lecturasBateria[indiceLectura] = porcentajeBateria;
    indiceLectura++;

    // Reiniciar el índice cuando se complete una vuelta
    if (indiceLectura >= NUM_LECTURAS) {
        indiceLectura = 0;
        lecturasCompletadas = true;
    }

    // Calcular el promedio solo si se han completado todas las lecturas
    float promedioBateria = 0;
    int numLecturasActuales = lecturasCompletadas ? NUM_LECTURAS : indiceLectura;
    for (int i = 0; i < numLecturasActuales; i++) {
        promedioBateria += lecturasBateria[i];
    }
    promedioBateria /= numLecturasActuales;

    // Asignar el promedio al porcentaje estabilizado
    percentage = promedioBateria;
}
