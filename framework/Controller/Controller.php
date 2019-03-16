<?php

namespace MyUCP\Controller;

use MyUCP\Foundation\Application;
use MyUCP\Views\View;

abstract class Controller
{
    /**
     * @var Application
     */
	private $app;

    /**
     * Controller constructor.
     *
     * @param Application $application
     */
	public function __construct(Application $application)
    {
		$this->app = $application;
	}

    /**
     * @param $key
     *
     * @return bool|mixed
     *
     * @throws \ReflectionException
     */
	public function __get($key)
    {
		return $this->app->make($key);
	}

    /**
     * @param $key
     *
     * @param $value
     *
     * @throws \ReflectionException
     */
	public function __set($key, $value)
    {
		$this->app->make($key, $value);
	}

    /**
     * @param $name
     * @param array $parameters
     *
     * @return View
     */
    public function view($name, $parameters = [])
    {
        return view($name, $parameters);
    }

    /**
     * @param mixed ...$models
     * @return mixed
     */
    public function model(...$models)
    {
        return model(...$models);
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function callAction($method, $parameters)
    {
        $this->callingAction($method, $parameters);

        $response = call_user_func_array([$this, $method], $parameters);

        $this->calledAction($method, $parameters, $response);

        return $response;
    }

    /**
     * @param $name
     * @param array $parameters
     */
    protected function callingAction($name, $parameters = [])
    {
        //
    }

    /**
     * @param $name
     * @param array $parameters
     * @param null $response
     */
    protected function calledAction($name, $parameters = [], $response = null)
    {
        //
    }

    /**
     * Get the name of controller from path
     *
     * @param $controllerName
     * @return mixed
     */
    public static function name($controllerName)
    {
        return ControllerLoader::name($controllerName);
    }

    /**
     * Set the alias of controller
     *
     * @param $name
     * @param null $path
     * @return null
     */
    public static function alias($name, $path = null)
    {
        return ControllerLoader::alias($name, $path);
    }
}