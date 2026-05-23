<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/crear_experimento', [App\Http\Controllers\HomeController::class, 'crearExperimento'])->name('crear_experimento');
Route::get('/get_experimento/{id_establecimiento}', [App\Http\Controllers\HomeController::class, 'getExperimento'])->name('get_experimento');
Route::get('/ver-grafico/{id_experimento}', [App\Http\Controllers\HomeController::class, 'verGrafico'])->name('ver-grafico');
Route::get('/get-grafico/{id_experimento}', [App\Http\Controllers\HomeController::class, 'getGrafico']);
Route::get('/graficos/{id}', [App\Http\Controllers\GraficosController::class, 'graficos']);
Route::get('/get_data_ema01/{id}', [App\Http\Controllers\GraficosController::class, 'getDataEMA1'])->name('get_data_ema01');
Route::get('/get_data_ema02/{id}', [App\Http\Controllers\GraficosController::class, 'getDataEMA2'])->name('get_data_ema02');
Route::get('/comparativa', [App\Http\Controllers\GraficosController::class, 'comparativa']);
Route::post('/cargar-grafico', [App\Http\Controllers\GraficosController::class, 'cargarGrafico'])->name('cargar-grafico');
Route::get('/ticket-salida', [App\Http\Controllers\GraficosController::class, 'ticketSalida']);
Route::post('/guardar-ticket', [App\Http\Controllers\GraficosController::class, 'guardarTicket'])->name('guardar-ticket');
Route::post('/guardar_nombre', [App\Http\Controllers\HomeController::class, 'guardarNombre'])->name('guardar_nombre');
Route::post('/cambiar_contrasena', [App\Http\Controllers\HomeController::class, 'cambiarContrasena'])->name('cambiar_contrasena');
Route::post('/eliminar_experimento', [App\Http\Controllers\HomeController::class, 'eliminarExperimento'])->name('eliminar_experimento');
Route::post('/datos_experimento', [App\Http\Controllers\HomeController::class, 'datosExperimento'])->name('datos_experimento');
Route::post('/editar_experimento', [App\Http\Controllers\HomeController::class, 'editarExperimento'])->name('editar_experimento');
Route::post('/datos_ema', [App\Http\Controllers\HomeController::class, 'datosEma'])->name('datos_ema');
Route::post('/editar_dato', [App\Http\Controllers\HomeController::class, 'editarDato'])->name('editar_dato');
Route::get('/get_data_comp/{id}', [App\Http\Controllers\GraficosController::class, 'getDataEMAComp'])->name('get_data_comp');
Route::post('/importar_txt', [App\Http\Controllers\HomeController::class, 'importarTxt'])->name('importar_txt');
Route::post('/importar_txt_experimentos', [App\Http\Controllers\HomeController::class, 'importarTxtExperimentos'])->name('importar_txt_experimentos');


