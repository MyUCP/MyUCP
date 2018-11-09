<?php

namespace Extensions\Auth;

use Application;
use Controller;
use Extensions\Auth\controllers\TestController;
use Extensions\Auth\controllers\UserController;
use MyUCP\Extension\BootExtensionable;
use Router;
use View;

class Auth implements BootExtensionable
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var
     */
    protected $config;

    /**
     * @param Application $app
     */
    public function bootstrap(Application $app)
    {
        $this->app = $app;

        $this->config = config('auth');

        // Определение путей для шаблонов расширения
        View::preLoad("auth.common", $this->path('resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'auth.common.zara.php'));
        View::preLoad("auth.login", $this->path('resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'auth.login.zara.php'));
        View::preLoad("auth.register", $this->path('resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'auth.register.zara.php'));

        // Определение контроллера

        Controller::alias(UserController::class, $this->path('controllers/UserController.php'));

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
     * @return \Redirect
     */
    public function login($email, $password)
    {
        model("User");

        $this->app->User->table($this->config->table);
        $this->app->User->primary_key = $this->config->rows['id'];

        if(!($user = $this->app->User->where($this->config->rows['email'], $email)->first())) {
            return redirect('/login?error=true')->with('error', 'Email введен неверно');
        }

        if(!password_verify($password, $user[$this->config->rows['password']]))
            return redirect('/login?error=true')->with('error', 'Пароль введен неверно');

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
            return redirect('/register?error=true')->with('error', 'Email введен неверно');

        if($password != $password_repeat)
            return redirect('/register?error=true')->with('error', 'Пароли не совпадают');

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->app->User->create([
            $this->config->rows['email'] => $email,
            $this->config->rows['password'] => $hash,
        ]);

        return redirect('/login')->with('success', 'Вы успешно зарегестрировались');
    }

    /**
     * Деавторизировать пользователя
     *
     * @return \Session
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

    /**
     * Получить путь к расширению
     *
     * @param $path
     * @return string
     */
    public function path($path)
    {
        return EXTENSIONS_DIR . 'Auth' . DIRECTORY_SEPARATOR . $path;
    }
}