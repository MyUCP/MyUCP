<?php

namespace MyUCP\Views\Zara;

use MyUCP\Support\App;
use MyUCP\Views\View;
use MyUCP\Views\Zara\Interfaces\ZaraService;

class Zara
{
    /**
     * @var ZaraCompiler
     */
    protected $compiler;

    /**
     * @var ZaraFactory
     */
	protected $factory;

    /**
     * @var ZaraService
     */
    protected $service;

    /**
     * Zara constructor.
     *
     * @param ZaraFactory $factory
     */
	public function __construct(ZaraFactory $factory)
    {
        $this->factory = $factory;
        $this->compiler = new ZaraCompiler($this->factory);
        $this->service = App::make(config('services.' . ZaraService::class));
    }

    /**
     * @param View $view
     * @param $compiledPath
     *
     * @return Zara
     */
	public function compile(View $view, $compiledPath)
    {
        $this->service->compile($view->getName());

        $this->compiler->compile($view->getContents(), $compiledPath);

		return $this;
	}

    /**
     * @return ZaraCompiler
     */
	public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * @return ZaraFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Register a handler for custom directives.
     *
     * @param  string  $name
     * @param  callable  $handler
     * @return void
     */
    public static function directive($name, callable $handler)
    {
        app("view")->getZara()->getCompiler()->directive($name, $handler);
    }

    /**
     * Register an "if" statement directive.
     *
     * @param  string  $name
     * @param  callable  $callback
     * @return void
     */
    public static function if($name, callable $callback)
    {
        app("view")->getZara()->getCompiler()->if($name, $callback);
    }

    /**
     * Check the result of a condition.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return bool
     */
    public static function check($name, ...$parameters)
    {
        return app("view")->getZara()->getCompiler()->check($name, $parameters);
    }

    /**
     * @param $view
     * @param array $data
     * @return mixed
     */
    public static function include($view, $data = [])
    {
        return view($view, $data);
    }
}