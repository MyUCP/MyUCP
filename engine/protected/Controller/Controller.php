<?php
/*
* MyUCP
*/

abstract class Controller
{
    /**
     * @var Application
     */
	private $app;

    /**
     * Controller constructor.
     * @param $app
     */
	public function __construct($app)
    {
		$this->app = $app;
	}

    /**
     * @param $key
     * @return bool|mixed
     */
	public function __get($key)
    {
		return $this->app->make($key);
	}

    /**
     * @param $key
     * @param $value
     */
	public function __set($key, $value)
    {
		$this->app->make($key, $value);
	}

    /**
     * @param $name
     * @param array $paramters
     * @return mixed
     */
    public function view($name, $paramters = [])
    {
        return view($name, $paramters);
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
     * Load controller file
     *
     * @param $name
     * @return mixed
     * @throws DebugException
     */
    public static function load($name)
    {
        return ControllerLoader::load($name);
    }

    /**
     * Get the path of controller
     *
     * @param $controllerName
     * @return mixed
     */
    public static function path($controllerName)
    {
        return ControllerLoader::path($controllerName);
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