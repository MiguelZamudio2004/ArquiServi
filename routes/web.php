<?php

use Illuminate\Support\Facades\Route;

Route::get('/menu', function () {
    return view('menu');
});
Route::get('/login', function () {
    return view('login');
});

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

Route::get('/register', function () {
    return view('register');
});

Route::get('/newpassword', function () {
    return view('newpassword');
});

