<?php
/**
 * MyUCP:Auth.
 */

namespace Extensions\Auth\controllers;

use MyUCP\Request\Request;

class UserController
{
    /**
     * @return mixed
     */
    public function login()
    {
        $auth = ext('Auth');

        // Вызываем функцию авторизации, передав данные из формы
        return $auth->login(Request::post('email'), Request::post('password'));
    }

    /**
     * @return mixed
     */
    public function register()
    {
        $auth = ext('Auth');

        // Вызываем функцию авторизации, передав данные из формы
        return $auth->register(Request::post('email'),
                                Request::post('password'),
                                Request::post('password_repeat'));
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        $auth = ext('Auth');

        $auth->logout();

        return redirect('/');
    }
}
