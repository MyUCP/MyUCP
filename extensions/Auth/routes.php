<?php
/*
|--------------------------------------------------------------------------
| Маршрутизация Авторизации
|--------------------------------------------------------------------------
*/

use Extensions\Auth\Auth;
use Extensions\Auth\controllers\UserController;

Router::condition(!Auth::check(), function() {
    Router::view('login', 'auth.login')->name('login');
    Router::view('register', 'auth.register');

    Router::post('login', UserController::class . "@login")->name('login')->csrfVerify();

    Router::post('register', UserController::class . "@register")->name('register')->csrfVerify();
});

Router::condition(Auth::check(), function() {
    Router::any("logout", UserController::class . "@logout")->name('logout');
});