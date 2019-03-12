<?php

namespace MyUCP\Views;

use InvalidArgumentException;

class ViewFileFinder
{
    /**
     * @var array
     */
    protected $preloaded = [];

    /**
     * @var array
     */
    protected $extensions = ['zara.php', 'php'];

    /**
     * @var array
     */
    protected $views = [];

    /**
     * ViewFileFinder constructor.
     *
     * @param array $preloaded
     * @param array|null $extensions
     */
    public function __construct(array $preloaded = [], array $extensions = null)
    {
        $this->preloaded = array_map([$this, 'resolvePath'], $preloaded);

        if(! is_null($extensions)) {
            $this->extensions = $extensions;
        }
    }

    /**
     * Find the view
     *
     * @param string $name
     * @return string
     */
    public function find($name)
    {
        if(isset($this->views[$name])) {
            return $this->views[$name];
        }

        return $this->views[$name] = $this->getFilePath($name);
    }

    /**
     * @param array|string $viewName
     * @param string|null $path
     *
     * @return ViewFileFinder
     */
    public function addPreload($viewName, $path = null)
    {
        if(is_array($viewName) && is_null($path)) {
            $this->preloaded = array_merge($this->preloaded, $viewName);
        } else {
            $this->preloaded[$viewName] = $path;
        }

        return $this;
    }

    /**
     * Register an extension with the view finder.
     *
     * @param  string  $extension
     * @return void
     */
    public function addExtension($extension)
    {
        if (($index = array_search($extension, $this->extensions)) !== false) {
            unset($this->extensions[$index]);
        }

        array_unshift($this->extensions, $extension);
    }

    /**
     * Flush the cache of located views.
     *
     * @return void
     */
    public function flush()
    {
        $this->views = [];
    }

    /**
     * Get the active preloaded views.
     *
     * @return array
     */
    public function getPreloaded()
    {
        return $this->preloaded;
    }

    /**
     * Get registered extensions.
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Resolve the path.
     *
     * @param  string  $path
     * @return string
     */
    protected function resolvePath($path)
    {
        return realpath($path) ?: $path;
    }

    /**
     * @param $name
     * @return mixed|string
     */
    protected function getFilePath($name)
    {
        foreach ($this->getPossibleViewFiles($name) as $file) {
            if(file_exists(app()->viewsPath($file))) {
                return app()->viewsPath($file);
            }
        }

        if(isset($this->preloaded[$name])) {
            if(file_exists($this->preloaded[$name])) {
                return $this->preloaded[$name];
            }
        }

        throw new InvalidArgumentException("View [{$name}] not found.");
    }

    /**
     * Get an array of possible view files.
     *
     * @param  string  $name
     * @return array
     */
    protected function getPossibleViewFiles($name)
    {
        return array_map(function ($extension) use ($name) {
            return $name . '.' . $extension;
        }, $this->extensions);
    }
}