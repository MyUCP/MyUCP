<?php

namespace MyUCP\Foundation;

use ArrayAccess;

class Application implements ArrayAccess
{
    use Bootstrap;

    //

    const VERSION = "5.8.1";

    /**
     * Application status
     * @var bool
     */
    private $initialized = false;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @var string
     */
    protected $basePath = __DIR__;

    /**
     * Application constructor.
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;

        $this->makeAliases();
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->registry->$name = $value;
    }

    /**
     * @param $name
     * @return bool|mixed
     */
    public function __get($name)
    {
        if(isset($this->alias[$name])) {
            $alias = $this->alias[$name];

            return $this->registry->$alias;
        }

        if($this->registry->$name !== false)
            return $this->registry->$name;

        return false;
    }

    /**
     * @param $name
     * @param null|object|string|\Closure $instance
     * @return bool|mixed|null
     */
    public function make($name, $instance = null)
    {
        if(is_null($instance)) {
            if(!$this->has($name)) {
                if($instance instanceof \Closure) {
                    return $this->make($name, $instance());
                }

                return $this->make($name, new $name);
            }

            return $this->$name;
        }

        return $this->$name = $instance;
    }

    /**
     * Make new instance with parameters
     *
     * @param $name
     * @param array $parameters
     * @return bool|mixed|null
     */
    public function makeWith($name, $parameters = [])
    {
        if($this->has($name) && empty($parameters))
            return $this->make($name);

        return $this->make($name, new $name(...$parameters));
    }

    /**
     * Make alias for instance or only name
     *
     * @param $alias
     * @param null $name
     * @param null $instance
     * @return bool|mixed|null
     */
    public function alias($alias, $name = null, $instance = null)
    {
        if(is_null($name)) {
            return $this->make($alias);
        }

        $this->alias[$alias] = $name;

        if(is_null($instance))
            return $this->make($name);

        return $this->make($name, $instance);
    }

    /**
     * Make alias for new instance with parameters
     *
     * @param $alias
     * @param $name
     * @param array $parameters
     * @return bool|mixed|null
     */
    public function aliasWith($alias, $name, $parameters = [])
    {
        return $this->alias($alias, $name, $this->makeWith($name, $parameters));
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        if(isset($this->alias[$name]))
            return true;

        if($this->registry->$name !== false)
            return true;

        return false;
    }

    /**
     * Service method
     *
     * @param $value
     * @return mixed
     */
    public function escape($value)
    {
        if($this->has('db') && $this->make('db') !== false)
            return $this->make('db')->escape($value);

        return $value;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->registry->$key !== false;
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->make($key, $value);
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->registry->$key);
    }

    /**
     * @param $path
     * @return $this
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;

        return $this;
    }

    /**
     * @param null $path
     * @return string
     */
    public function basePath($path = null)
    {
        return $this->basePath . (DIRECTORY_SEPARATOR . ($path ?? ''));
    }

    /**
     * Get path to the app directory.
     *
     * @param null|string $path
     * @return string
     */
    public function appPath($path = null)
    {
        if(is_null($path)) {
            return $this->basePath('app');
        }

        return $this->basePath('app') . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Get path to the engine directory.
     *
     * @param null|string $path
     * @return string
     */
    public function frameworkPath($path = null)
    {
        if(is_null($path)) {
            return $this->basePath('framework');
        }

        return $this->basePath('framework') . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Get path to the resources directory.
     *
     * @param null|string $path
     * @return string
     */
    public function resourcesPath($path = null)
    {
        if(is_null($path)) {
            return $this->basePath('resources');
        }

        return $this->basePath('resources') . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Get path to the views directory.
     *
     * @param null|string $path
     * @return string
     */
    public function viewsPath($path = null)
    {
        if(is_null($path)) {
            return $this->resourcesPath('views');
        }

        return $this->resourcesPath('views') . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Get path to the assets directory.
     *
     * @param null|string $path
     * @return string
     */
    public function assetsPath($path = null)
    {
        if(is_null($path)) {
            return $this->basePath('assets');
        }

        return $this->basePath('assets') . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Get path to the config directory.
     *
     * @param null|string $path
     * @return string
     */
    public function configPath($path = null)
    {
        if(is_null($path)) {
            return $this->basePath('configs');
        }

        return $this->basePath('configs') . DIRECTORY_SEPARATOR . $path;
    }
}