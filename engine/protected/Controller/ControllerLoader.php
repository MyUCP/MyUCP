<?php
/**
 * MyUCP
 */

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
     * @param null $path
     * @return null
     */
    public static function alias($name, $path = null)
    {
        return self::$alias[$name] = (is_null($path)) ? $name : $path;
    }

    /**
     * Load controller file
     *
     * @param $name
     * @return mixed
     * @throws DebugException
     */
    public static function load($name)
    {
        if(isset(self::$alias[$name])) {
            $file = self::$alias[$name];

            require_once $file;

            return $name;
        }

        $about = self::about($name);

        if(!file_exists($about['path']))
            throw new DebugException("Cannot load controller [{$about['name']}]");

        require_once $about['path'];

        return $about['name'];
    }

    /**
     * Get the path of controller
     *
     * @param $controllerName
     * @return mixed
     */
    public static function path($controllerName)
    {
        return self::about($controllerName)['path'];
    }

    /**
     * Get the name of controller
     *
     * @param $controllerName
     * @return mixed
     */
    public static function name($controllerName)
    {
        return self::about($controllerName)['name'];
    }

    /**
     * Get name and path of controller
     *
     * @param $controllerName
     * @return array
     */
    public static function about($controllerName)
    {
        if(strpos($controllerName, ".")){
            $path = explode(".", $controllerName);

            $controller = array_shift(array_reverse($path));

            array_pop($path);

            $folder = implode( DIRECTORY_SEPARATOR, $path);
        } else {
            $controller = $controllerName;
        }

        if(!isset($folder)) {
            $controllerFile = APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';
        } else {
            $controllerFile = APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $controller . '.php';
        }

        return [
            "path" => $controllerFile,
            "name" => $controller,
        ];
    }
}