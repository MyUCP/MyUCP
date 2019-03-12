<?php

namespace MyUCP\Views;

use MyUCP\Collection\Arrayable;
use MyUCP\Collection\Renderable;
use MyUCP\Debug\DebugException;
use MyUCP\Support\App;
use MyUCP\Views\Zara\Zara;
use MyUCP\Views\Zara\ZaraFactory;
use MyUCP\Views\Interfaces\ViewService;

class View implements Renderable
{
    /**
     * @var ViewFactory
     */
    protected $factory;

    /**
     * View name
     *
     * @var string
     */
    protected $view;

    /**
     * View file path
     *
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $data;

    /**
     * View constructor.
     *
     * @param ViewFactory $factory
     * @param $view
     * @param $path
     * @param array $data
     */
    public function __construct(ViewFactory $factory, $view, $path, $data = [])
    {
        $this->factory = $factory;
        $this->view = $view;
        $this->path = $path;

        $this->data = $data instanceof Arrayable ? $data->toArray() : (array) $data;
    }

    /**
     * @param callable|null $callback
     *
     * @return string
     */
    public function render(callable $callback = null)
    {
        $this->factory->getService()->render($this->view, $this->data);

        return $this->factory->getCompiler()->compile($this);
    }

    /**
     * @return string
     */
    public function getContents()
    {
        $this->mergeData($this->factory->getShareData());

        ob_start();

        extract($this->data, EXTR_SKIP);

        include $this->path;

        return ltrim(ob_get_clean());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function with($key, $value)
    {
        if(is_array($key)) {
            $this->mergeData($key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $name
     * @param null $path
     *
     * @return ViewFactory
     */
    public static function preload($name, $path = null)
    {
        App::make('view')->getFileFinder()->addPreload($name, $path);

        return App::make('view');
    }

    /**
     * @param $name
     *
     * @return ViewFactory
     */
    public static function extension($name)
    {
        App::make('view')->getFileFinder()->addExtension($name);

        return App::make('view');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->view;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function mergeData($data = [])
    {
        $data = $data instanceof Arrayable ? $data->toArray() : (array) $data;

        $this->data = array_merge($this->data, $data);
    }

    /**
     * @param $key
     * @param null $value
     *
     * @return ViewFactory
     */
    public static function share($key, $value = null)
    {
        return App::make(ViewFactory::class)->shareData($key, $value);
    }
}