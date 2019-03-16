<?php

namespace MyUCP\Config;

use MyUCP\Collection\Arrayable;
use MyUCP\Collection\Collection;
use MyUCP\Collection\Jsonable;
use MyUCP\Debug\DebugException;
use MyUCP\Foundation\Application;

class Config implements Arrayable, Jsonable
{
    /**
     * @var Collection
     */
    protected $data;

    /**
     * Config constructor.
     *
     * @param Application $application
     *
     * @throws DebugException
     */
    public function __construct(Application $application)
    {
        if (is_readable($application->configPath('main.php'))) {
            $config = require_once $application->configPath('main.php');

            $this->data = new Collection($config);

            $this->loadConfigs();
            $this->loadCustomFiles();

            return true;
        }

        throw new DebugException('Cannot load main configuration file [main.php]');
    }

    /**
     * @param $key
     * @param $val
     */
    public function __set($key, $val)
    {
        $this->data->put($key, $val);
    }

    /**
     * @param mixed $key
     *
     * @return mixed|null
     */
    public function __get($key)
    {
        return $this->data->get($key);
    }

    /**
     * @throws DebugException
     *
     * @return bool
     */
    public function loadConfigs()
    {
        $configs = scandir(app()->configPath());
        array_shift($configs);
        array_shift($configs);

        foreach ($configs as $item) {
            if ($item != 'main.php') {
                if (is_readable(app()->configPath($item))) {
                    $config = require_once app()->configPath($item);

                    $configName = substr($item, 0, -4);

                    $this->data->put($configName, $config);
                } else {
                    throw new DebugException("Cannot load [{$item}] configuration file");
                }
            }
        }

        return true;
    }

    /**
     * @return void
     */
    private function loadCustomFiles()
    {
        foreach ($this->data->get('load_files', []) as $path) {
            if (file_exists($path) && is_readable($path)) {
                require_once $path;
            }
        }
    }

    /**
     * @return array|Collection
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->data->toJson($options);
    }

    /**
     * Convert the config data to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->data->__toString();
    }
}
