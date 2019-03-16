<?php

namespace MyUCP\Support;

class App
{
    /**
     * Make or get instance from container.
     *
     * @param $name
     * @param null|object|string|\Closure $instance
     *
     * @throws \ReflectionException
     *
     * @return bool|mixed|null
     */
    public static function make($name, $instance = null)
    {
        return app()->make($name, $instance);
    }

    /**
     * Call method with dependencies.
     *
     * @param $instance
     * @param $method
     * @param array $parameters
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    public static function call($instance, $method, $parameters = [])
    {
        return app()->call($instance, $method, $parameters);
    }

    /**
     * Make new instance with parameters.
     *
     * @param $name
     * @param array $parameters
     *
     * @throws \ReflectionException
     *
     * @return bool|mixed|null
     */
    public static function makeWith($name, $parameters = [])
    {
        return app()->makeWith($name, $parameters);
    }

    /**
     * Make alias for instance or only name.
     *
     * @param $alias
     * @param null $name
     * @param null $instance
     *
     * @throws \ReflectionException
     *
     * @return bool|mixed|null
     */
    public static function alias($alias, $name = null, $instance = null)
    {
        return app()->alias($alias, $name, $instance);
    }

    /**
     * Make alias for new instance with parameters.
     *
     * @param $alias
     * @param $name
     * @param array $parameters
     *
     * @throws \ReflectionException
     *
     * @return bool|mixed|null
     */
    public static function aliasWith($alias, $name, $parameters = [])
    {
        return app()->aliasWith($alias, $name, $parameters);
    }

    /**
     * @param null $path
     *
     * @return string
     */
    public static function basePath($path = null)
    {
        return app()->basePath($path);
    }

    /**
     * Get path to the app directory.
     *
     * @param null|string $path
     *
     * @return string
     */
    public static function appPath($path = null)
    {
        return app()->appPath($path);
    }

    /**
     * Get path to the engine directory.
     *
     * @param null|string $path
     *
     * @return string
     */
    public static function frameworkPath($path = null)
    {
        return app()->frameworkPath($path);
    }

    /**
     * Get path to the resources directory.
     *
     * @param null|string $path
     *
     * @return string
     */
    public static function resourcesPath($path = null)
    {
        return app()->resourcesPath($path);
    }

    /**
     * Get path to the views directory.
     *
     * @param null|string $path
     *
     * @return string
     */
    public static function viewsPath($path = null)
    {
        return app()->viewsPath($path);
    }

    /**
     * Get path to the assets directory.
     *
     * @param null|string $path
     *
     * @return string
     */
    public static function assetsPath($path = null)
    {
        return app()->assetsPath($path);
    }

    /**
     * Get path to the config directory.
     *
     * @param null|string $path
     *
     * @return string
     */
    public static function configPath($path = null)
    {
        return app()->configPath($path);
    }
}
