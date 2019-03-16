<?php

namespace MyUCP\Controller;

class ControllerLoader
{
    /**
     * @var array
     */
    protected static $alias = [];

    /**
     * Set the alias of controller
     *
     * @param $name
     * @param $abstract
     * @return null
     */
    public static function alias($name, $abstract)
    {
        return self::$alias[$name] = $abstract;
    }

    /**
     * Get the name of controller
     *
     * @param $controllerName
     *
     * @return string
     */
    public static function name($controllerName)
    {
        if(isset(self::$alias[$controllerName])) {
            return self::$alias = $controllerName;
        }

        return '\App\Controllers\\' . $controllerName;
    }
}