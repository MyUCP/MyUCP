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
     *
     * @var bool
     */
    protected $initialized = false;

    /**
     * Application boot status
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $basePath = __DIR__;

    /**
     * Application constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->makeAliases();
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws \ReflectionException
     */
    public function __set($name, $value)
    {
        $this->container->make($name, $value);
    }

    /**
     * @param $name
     *
     * @return bool|mixed
     *
     * @throws \ReflectionException
     */
    public function __get($name)
    {
        return $this->container->make($name);
    }

    /**
     * @param $instance
     * @param $method
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function call($instance, $method, $parameters = [])
    {
        return $this->container->callMethod($instance, $method, $parameters);
    }

    /**
     * @param $name
     * @param null|object|string|\Closure $instance
     *
     * @return bool|mixed|null
     *
     * @throws \ReflectionException
     */
    public function make($name, $instance = null)
    {
        return $this->container->make($name, $instance);
    }

    /**
     * Make new instance with parameters
     *
     * @param $name
     * @param array $parameters
     *
     * @return bool|mixed|null
     *
     * @throws \ReflectionException
     */
    public function makeWith($name, $parameters = [])
    {
        return $this->container->makeWith($name, $parameters);
    }

    /**
     * Make alias for instance or only name
     *
     * @param $alias
     * @param null $name
     * @param null $instance
     *
     * @return mixed|Container
     *
     * @throws \ReflectionException
     */
    public function alias($alias, $name = null, $instance = null)
    {
        return $this->container->alias($alias, $name, $instance);
    }

    /**
     * Make alias for new instance with parameters
     *
     * @param $alias
     * @param $name
     * @param array $parameters
     *
     * @return bool|mixed|null
     *
     * @throws \ReflectionException
     */
    public function aliasWith($alias, $name, $parameters = [])
    {
        return $this->alias($alias, $name, $this->makeWith($name, $parameters));
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        if(isset($this->alias[$name]))
            return true;

        if($this->container->$name !== false)
            return true;

        return false;
    }

    /**
     * Service method
     *
     * @param $value
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function escape($value)
    {
        if(! env('APP_DB', false)) {
            return $value;
        }

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
        return $this->container->$key !== false;
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string $key
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return void
     *
     * @throws \ReflectionException
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
        $this->container->remove($key);
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