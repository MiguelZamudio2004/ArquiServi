<?php

use Illuminate\Support\Facades\Route;

Route::get('/menu', function () {
    return view('menu');
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

