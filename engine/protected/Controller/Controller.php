<?php
/*
* MyUCP
*/

abstract class Controller
{
    /**
     * @var Registry
     */
	private $registry;

    /**
     * Controller constructor.
     * @param $registry
     */
	public function __construct($registry)
    {
		$this->registry = $registry;
	}

    /**
     * @param $key
     * @return bool|mixed
     */
	public function __get($key)
    {
		return $this->registry->$key;
	}

    /**
     * @param $key
     * @param $value
     */
	public function __set($key, $value)
    {
		$this->registry->$key = $value;
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