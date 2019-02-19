<?php

namespace Extensions\Auth;

use MyUCP\Foundation\Application;
use MyUCP\Extension\BootExtension;
use Extensions\Auth\controllers\UserController;
use MyUCP\Response\Redirect;
use MyUCP\Routing\Router;
use MyUCP\Session\Session;

class Auth extends BootExtension
{
    /**
     * @var
     */
    protected $config;

    /**
     * @param Application $app
     * @throws \ReflectionException
     */
    public function bootstrap(Application $app)
    {
        $this->app = $app;

        $this->config = config('auth');

        // Определение путей для шаблонов расширения
        $this->view("auth.common", $this->path('resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'auth.common.zara.php'));
        $this->view("auth.login", $this->path('resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'auth.login.zara.php'));
        $this->view("auth.register", $this->path('resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'auth.register.zara.php'));

        // Определение контроллера

        $this->controller(UserController::class, $this->path('controllers/UserController.php'));

        // Определение маршрутов для расширения

        Router::name('auth.', $this->path('routes.php'));
    }

    /**
     *
     */
    public function run()
    {
        //
    }

    /**
     * Метод для авторизации пользователя
     *
     * @param $email
     * @param $password
     * @return Redirect
     */
    public function login($email, $password)
    {
        model("User");

        $this->app->User->table($this->config->table);
        $this->app->User->primary_key = $this->config->rows['id'];

        if(!($user = $this->app->User->where($this->config->rows['email'], $email)->first())) {
            return redirect('/login')->with('error', 'Email введен неверно 1');
        }

        if(!password_verify($password, $user[$this->config->rows['password']]))
            return redirect('/login')->with('error', 'Пароль введен неверно 2');

        app('session')->put('_u_id', $user[$this->config->rows['id']]);

        return redirect($this->config->redirect);
    }

    /**
     * Метод для регистрации нового пользователя
     *
     * @param $email
     * @param $password
     * @param $password_repeat
     * @return \Redirect
     */
    public function register($email, $password, $password_repeat)
    {
        model("User");

        $this->app->User->table($this->config->table);
        $this->app->User->primary_key = $this->config->rows['id'];

        $validate = ext("Validate");

        if(!$validate->email($email))
            return redirect('/register')->with('error', 'Email введен неверно');

        if($password != $password_repeat)
            return redirect('/register')->with('error', 'Пароли не совпадают');

        $hash = password_hash($password, PASSWORD_DEFAULT);

        if(!is_null($this->app->User->where($this->config->rows['email'], '=', $email)->first()))
            return redirect('/register')->with('error', 'Введенный вами Email занят');

        $this->app->User->create([
            $this->config->rows['email'] => $email,
            $this->config->rows['password'] => $hash,
        ]);

        return redirect('/login')->with('success', 'Вы успешно зарегестрировались');
    }

    /**
     * Деавторизировать пользователя
     *
     * @return Session
     */
    public function logout()
    {
        return session()->forget("_u_id");
    }

    /**
     * Получить данные пользователя, если авторизирован
     */
    public function auth()
    {
        if(!Auth::check())
            return false;

        model("User");

        $this->app->User->table($this->config->table);
        $this->app->User->primary_key = $this->config->rows['id'];

        return $this->app->User->first(session('_u_id'));
    }

    /**
     * Получить данные пользователя, если авторизирован
     * Аналогично методу auth()
     */
    public static function user()
    {
        return ext("Auth")->auth();
    }

    /**
     * Проверить авторизирован ли пользователь
     */
    public static function check()
    {
        return app('session')->has("_u_id");
    }
}