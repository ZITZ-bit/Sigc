<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('portal');
});

Route::view('/login', 'login')->name('login');
Route::view('/registro', 'register')->name('register');
