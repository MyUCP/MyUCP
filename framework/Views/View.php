<?php

namespace MyUCP\Views;

use MyUCP\Collection\Arrayable;
use MyUCP\Collection\Renderable;
use MyUCP\Debug\DebugException;
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

    public static function preLoad($name, $path = null)
    {
        // TODO
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
}