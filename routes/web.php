<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\LoginController;

Route::get('/menu', function () {
    return view('menu');
});
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.auth');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/recuperation', function () {
    return view('recuperation');
});

Route::get('/registerprof', function () {
    return view('registerprof');
});

Route::get('/registerprov', function () {
    return view('registerprov');
});

Route::get('/validar', function () {
    return view('validar');
});

Route::get('/register',
[RegistroController::class, 'create'])->name('register');

Route::post('/register',
[RegistroController::class, 'store'])->name('register.store');

Route::get('/newpassword', function () {
    return view('newpassword');
});

