<?php

namespace MyUCP\Views;

use MyUCP\Debug\DebugException;
use MyUCP\Support\App;
use MyUCP\Views\Interfaces\ViewService;
use MyUCP\Views\Zara\Zara;
use MyUCP\Views\Zara\ZaraFactory;

class View
{
    /**
     * @var ViewCompiler
     */
	protected $compiler;

    /**
     * @var ViewFileFinder
     */
	protected $fileFinder;

    /**
     * @var array
     */
	protected $share = [];

    /**
     * @var ViewService
     */
	protected $service = null;

    /**
     * View constructor.
     *
     * @param ViewFileFinder $fileFinder
     * @param ViewCompiler $compiler
     */
	public function __construct(ViewFileFinder $fileFinder, ViewCompiler $compiler)
    {
		$this->fileFinder = $fileFinder;
		$this->compiler = $compiler;
		$this->service = App::make(config('services.' . ViewService::class));

		$this->shareData(['__view' => $this]);
	}

    /**
     * @param string $name
     * @param array $vars
     * @param bool $exception
     *
     * @return bool|string
     *
     * @throws DebugException
     */
	public function load($name, $vars = [], $exception = true)
    {
        if(is_null($this->service))
            $this->service = app()->make(config('services.' . ViewService::class));

        $this->service->render($name, $vars);

        $vars = array_merge($vars, $this->share);

		return $this->Zara->compile($name, $vars, new ZaraFactory, $exception)->getCompiled();
	}

	public function make($name, $data = [])
    {
//        dd($name);
    }

    /**
     * @return ViewFileFinder
     */
    public function getFileFinder()
    {
        return $this->fileFinder;
    }

    /**
     * @param string $name
     * @param string $path
     *
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