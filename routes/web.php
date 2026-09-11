<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RecuperacionController;
use App\Http\Controllers\NotificacionController;

Route::post('/notificaciones/{id}/leer',[NotificacionController::class,'marcarLeida'])
    ->middleware('auth')
    ->name('notificaciones.leer');

Route::get('/', function () {
    return view('menu');
})->name('menu');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.auth');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/recuperation', [RecuperacionController::class, 'mostrarCorreo'])->name('recuperacion');
Route::post('/recuperation', [RecuperacionController::class, 'enviarCodigo'])->name('recuperacion.enviar');

Route::post('/reenviar-codigo', [RecuperacionController::class, 'reenviarCodigo'] )->name('recuperacion.reenviar') -> middleware('throttle:1,1'); 


Route::get('/registerprof', function () {
    return view('registerprof');
});

Route::get('/registerprov', function () {
    return view('registerprov');
});

Route::get('/validar',[RecuperacionController::class, 'mostrarCodigo'])->name('recuperacion.codigo');
Route::post('/validar',[RecuperacionController::class, 'validarCodigo'])->name('recuperacion.validar');

Route::get('/register',
[RegistroController::class, 'create'])->name('register');

Route::post('/register',
[RegistroController::class, 'store'])->name('register.store');

Route::get('/newpassword', [RecuperacionController::class,'mostrarNuevaPassword'])->name('recuperacion.password');

Route::post('/newpassword', [RecuperacionController::class,'cambiarPassword'])->name('recuperacion.cambiar');

