<?php
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Ruta registro
Route::post('register', [
    RegisterController::class,
    "register"
]);

//Ruta login
Route::post('login', [
    LoginController::class,
    "login"
]);
