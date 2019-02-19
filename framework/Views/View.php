<?php

namespace MyUCP\Views;

use MyUCP\Debug\DebugException;
use MyUCP\Views\Zara\Zara;
use MyUCP\Views\Zara\ZaraFactory;

class View
{
    /**
     * @var Zara
     */
	protected $Zara;

    /**
     * @var array
     */
	protected $share = [];

    /**
     * @var \App\Services\ViewService
     */
	protected $service = null;

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
     * @param bool $exception
     * @return bool|string
     * @throws DebugException
     */
	public function load($name, $vars = [], $exception = true)
    {
        if(is_null($this->service))
            $this->service = new \App\Services\ViewService();

        $this->service->render($name, $vars);

        $vars = array_merge($vars, $this->share);

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

    /**
     * @param array $vars
     */
    public function shareData($vars = [])
    {
        $this->share = $vars;
    }

    /**
     * Static alias: shareData()
     *
     * @param array $vars
     */
    public static function share($vars = [])
    {
        app('view')->shareData($vars);
    }
}