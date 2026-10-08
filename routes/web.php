<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUsuarioController;
use App\Http\Controllers\Admin\AdminProfesionalController;
use App\Http\Controllers\Admin\AdminProveedorController;
use App\Http\Controllers\Admin\AdminAprobacionController;
use App\Http\Controllers\Admin\AdminSolicitudController;
use App\Http\Controllers\Admin\AdminCalificacionController;

use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RecuperacionController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RegistroProfesionalController;
use App\Http\Controllers\RegistroProveedorController;


// Solicitudes
Route::middleware('auth')->group(function () {
    Route::get('/solicitudes/mias', [SolicitudController::class, 'mias'])
        ->name('solicitudes.mias');

    Route::get('/solicitudes/recibidas', [SolicitudController::class, 'recibidas'])
        ->name('solicitudes.recibidas');

    Route::get('/solicitudes/crear/{destinatario}', [SolicitudController::class, 'crear'])
        ->name('solicitudes.crear');

    Route::post('/solicitudes', [SolicitudController::class, 'guardar'])
        ->name('solicitudes.guardar');

    Route::get('/solicitudes/{solicitud}/calificar', [SolicitudController::class, 'calificar'])
        ->name('solicitudes.calificar');

    Route::post('/solicitudes/{solicitud}/calificar', [SolicitudController::class, 'guardarCalificacion'])
        ->name('solicitudes.calificar.guardar');

    Route::get('/solicitudes/{solicitud}', [SolicitudController::class, 'mostrar'])
        ->name('solicitudes.mostrar');

    Route::patch('/solicitudes/{solicitud}/aceptar', [SolicitudController::class, 'aceptar'])
        ->name('solicitudes.aceptar');

    Route::patch('/solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])
        ->name('solicitudes.rechazar');

    Route::patch('/solicitudes/{solicitud}/cancelar', [SolicitudController::class, 'cancelar'])
        ->name('solicitudes.cancelar');

    Route::patch('/solicitudes/{solicitud}/terminar', [SolicitudController::class, 'terminar'])
        ->name('solicitudes.terminar');
});


// Administración
Route::middleware(['auth', 'administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])
            ->name('dashboard');

        // Usuarios
        Route::get('/usuarios', [AdminUsuarioController::class, 'index'])
            ->name('usuarios.index');

        Route::get('/usuarios/{usuario}', [AdminUsuarioController::class, 'mostrar'])
            ->name('usuarios.mostrar');

        Route::patch('/usuarios/{usuario}/estado', [AdminUsuarioController::class, 'actualizarEstado'])
            ->name('usuarios.estado');

        Route::delete('/usuarios/{usuario}', [AdminUsuarioController::class, 'eliminar'])
            ->name('usuarios.eliminar');

        // Profesionales
        Route::get('/profesionales', [AdminProfesionalController::class, 'index'])
            ->name('profesionales.index');

        Route::get('/profesionales/{profesional}', [AdminProfesionalController::class, 'mostrar'])
            ->name('profesionales.mostrar');

        // Proveedores
        Route::get('/proveedores', [AdminProveedorController::class, 'index'])
            ->name('proveedores.index');

        Route::get('/proveedores/{proveedor}', [AdminProveedorController::class, 'mostrar'])
            ->name('proveedores.mostrar');

        // Aprobaciones
        Route::get('/aprobaciones', [AdminAprobacionController::class, 'index'])
            ->name('aprobaciones.index');

        Route::get('/aprobaciones/{solicitud}', [AdminAprobacionController::class, 'mostrar'])
            ->name('aprobaciones.mostrar');

        Route::patch('/aprobaciones/{solicitud}/aprobar', [AdminAprobacionController::class, 'aprobar'])
            ->name('aprobaciones.aprobar');

        Route::patch('/aprobaciones/{solicitud}/rechazar', [AdminAprobacionController::class, 'rechazar'])
            ->name('aprobaciones.rechazar');

        // Solicitudes
        Route::get('/solicitudes', [AdminSolicitudController::class, 'index'])
            ->name('solicitudes.index');

        Route::get('/solicitudes/{solicitud}', [AdminSolicitudController::class, 'mostrar'])
            ->name('solicitudes.mostrar');

        // Calificaciones
        Route::get('/calificaciones', [AdminCalificacionController::class, 'index'])
            ->name('calificaciones.index');

        Route::get('/calificaciones/{calificacion}', [AdminCalificacionController::class, 'mostrar'])
            ->name('calificaciones.mostrar');
    });


// Catálogo y perfiles públicos
Route::get('/usuarios', [PerfilController::class, 'buscar'])
    ->name('usuarios.buscar');

Route::get('/usuarios/{usuario}', [PerfilController::class, 'publico'])
    ->name('perfil.publico');


// Registro profesional
Route::get('/register/profesional', [RegistroProfesionalController::class, 'mostrar'])
    ->name('registro.profesional');

Route::post('/register/profesional', [RegistroProfesionalController::class, 'guardar'])
    ->name('registro.profesional.guardar');


// Registro proveedor
Route::get('/register/proveedor', [RegistroProveedorController::class, 'mostrar'])
    ->name('registro.proveedor');

Route::post('/register/proveedor', [RegistroProveedorController::class, 'guardar'])
    ->name('registro.proveedor.guardar');


// Perfil autenticado
Route::middleware('auth')->group(function () {
    Route::get('/perfil', [PerfilController::class, 'mostrar'])
        ->name('perfil');

    Route::get('/perfil/editar', [PerfilController::class, 'editar'])
        ->name('perfil.editar');

    Route::put('/perfil', [PerfilController::class, 'actualizar'])
        ->name('perfil.actualizar');
});


// Notificaciones
Route::post('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida'])
    ->middleware('auth')
    ->name('notificaciones.leer');


// Inicio
Route::get('/', function () {
    return view('menu');
})->name('menu');


// Acerca de
Route::get('/acerca-de', function () {
    return view('acerca');
})->name('acerca');


// Inicio y cierre de sesión
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.auth');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');


// Recuperación de contraseña
Route::get('/recuperation', [RecuperacionController::class, 'mostrarCorreo'])
    ->name('recuperacion');

Route::post('/recuperation', [RecuperacionController::class, 'enviarCodigo'])
    ->name('recuperacion.enviar');

Route::post('/reenviar-codigo', [RecuperacionController::class, 'reenviarCodigo'])
    ->middleware('throttle:1,1')
    ->name('recuperacion.reenviar');

Route::get('/validar', [RecuperacionController::class, 'mostrarCodigo'])
    ->name('recuperacion.codigo');

Route::post('/validar', [RecuperacionController::class, 'validarCodigo'])
    ->name('recuperacion.validar');


// Registro general
Route::get('/register', [RegistroController::class, 'create'])
    ->name('register');

Route::post('/register', [RegistroController::class, 'store'])
    ->name('register.store');


// Nueva contraseña
Route::get('/newpassword', [RecuperacionController::class, 'mostrarNuevaPassword'])
    ->name('recuperacion.password');

Route::post('/newpassword', [RecuperacionController::class, 'cambiarPassword'])
    ->name('recuperacion.cambiar');