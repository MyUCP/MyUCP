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

    public function model(...$models)
    {
        return model(...$models);
    }
}