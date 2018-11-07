<?php
/*
|--------------------------------------------------------------------------
| Маршрутизация Авторизации
|--------------------------------------------------------------------------
*/

use Extensions\Auth\Auth;

Router::condition(!Auth::check(), function() {
    Router::view('login', 'auth.login')->name('login');
    Router::view('register', 'auth.register');

    Router::post('login', function () {

        $auth = ext('Auth');

        // Вызываем функцию авторизации, передав данные из формы
        return $auth->login(Request::post('email'), Request::post('password'));
    })->name('login')->csrfVerify();

    Router::post('register', function () {

        $auth = ext('Auth');

        // Вызываем функцию авторизации, передав данные из формы
        return $auth->register(Request::post('email'), Request::post('password'), Request::post('password_repeat'));
    })->name('register')->csrfVerify();
});

Router::condition(Auth::check(), function() {
    Router::any("logout", function() {

        $auth = ext('Auth');

        return $auth->logout();
    })->name('logout');
});