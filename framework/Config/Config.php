<?php

namespace MyUCP\Config;

use MyUCP\Debug\DebugException;

class Config
{
    /**
     * @var array 
     */
	private $data = [];

    /**
     * Config constructor.
     * @throws DebugException
     */
	public function __construct() 
    {
		if(is_readable(app()->configPath('main.php'))) {
			$config = require_once(app()->configPath('main.php'));

			$this->data = array_merge($this->data, $config);

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
		$this->data[$key] = $val;
	}

    /**
     * @param mixed $key
     * @return mixed|null
     */
	public function __get($key)
    {
		if(isset($this->data[$key])) {
			return $this->data[$key];
		}

		return null;
	}

    /**
     * @return bool
     * @throws DebugException
     */
	public function loadConfigs()
    {
		$configs = scandir(app()->configPath());
		array_shift($configs);
		array_shift($configs);

		foreach($configs as $item){
			if($item != "main.php" && $item != "autoload_classes.php"){
				if(is_readable(app()->configPath($item))) {
					$config = require_once(app()->configPath($item));

					$configName = substr($item, 0, -4);

					$this->data[$configName] = (object) $config;
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
        foreach($this->data['load_files'] as $path) {
            if (file_exists($path) && is_readable($path)) {
                require_once($path);
            }
        }
    }
}