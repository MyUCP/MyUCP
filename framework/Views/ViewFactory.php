<?php

namespace MyUCP\Views;

use MyUCP\Support\App;
use MyUCP\Support\Str;
use MyUCP\Views\Interfaces\ViewService;

class ViewFactory
{
    /**
     * @var ViewFileFinder
     */
    protected $fileFinder;

    /**
     * @var ViewCompiler
     */
    protected $compiler;

    /**
     * @var ViewService
     */
    protected $service;

    /**
     * @var array
     */
    protected $share = [];

    /**
     * ViewFactory constructor.
     * @param ViewFileFinder $fileFinder
     * @param ViewCompiler $compiler
     */
    public function __construct(ViewFileFinder $fileFinder, ViewCompiler $compiler)
    {
        $this->fileFinder = $fileFinder;
        $this->compiler = $compiler;
        $this->service = App::make(config('services.' . ViewService::class));

        $this->shareData('__view', $this);
    }

    /**
     * @param $view
     * @param array|mixed $data
     *
     * @return View
     */
    public function make($view, $data = [])
    {
        $path = $this->fileFinder->find(
            $this->normalize($view)
        );

        return $this->createView($view, $path, array_merge($data, $this->share));
    }

    /**
     * @param View $view
     *
     * @param array|mixed $data
     * @return string
     */
    public function render($view, $data = [])
    {
        if($view instanceof View) {
            return $view->render();
        }

        return $this->make($view, $data)->render();
    }

    /**
     * @param $key
     * @param null $value
     *
     * @return ViewFactory
     */
    public function shareData($key, $value = null)
    {
        if(is_array($key)) {
            $this->share = array_merge($this->share, $key);
        } else {
            $this->share[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function normalize($name)
    {
        if(! Str::contains($name, '/')) {
            $name = str_replace('/', '.', $name);
        }

        return $name;
    }

    /**
     * Create View instance
     *
     * @param $view
     * @param $path
     * @param $data
     *
     * @return View
     */
    protected function createView($view, $path, $data)
    {
        return new View($this, $view, $path, $data);
    }

    /**
     * @return ViewFileFinder
     */
    public function getFileFinder()
    {
        return $this->fileFinder;
    }

    /**
     * @return ViewService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return ViewCompiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }
}