<?php
/*
* MyUCP
*/

class View
{
    /**
     * @var Zara
     */
	private $Zara;

    /**
     * View constructor.
     */
	public function __construct()
    {
		$this->Zara = new Zara;
	}

    /**
     * @return Zara
     */
	public function getZara()
    {
        return $this->Zara;
    }

    /**
     * @param string $name
     * @param array $vars
     * @param $exception
     * @return bool|string
     * @throws DebugException
     */
	public function load($name, $vars = [], $exception = true)
    {
		return $this->Zara->compile($name, $vars, new ZaraFactory, $exception)->getCompiled();
	}

    /**
     * @param string $name
     * @param string $path
     * @return mixed
     */
    public static function preLoad(string $name, string $path)
    {
        return Zara::preLoad($name, $path);
    }
}