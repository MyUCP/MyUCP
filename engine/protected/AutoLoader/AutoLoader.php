<?php
/*
* MyUCP
*/

class AutoLoader
{
    /**
     * @var array
     */
    protected $path = [];

    /**
     * @var string
     */
	protected $className;

    /**
     * AutoLoader constructor.
     *
     * @param $className
     * @throws DebugException
     */
	public function __construct($className)
    {
		$this->getPaths();

		$this->className = $className;

		$this->loadClass();
	}

    /**
     * Получение массива со списком путей и классов
     */
	public function getPaths()
    {
		$this->path = array_merge(
            require(ENGINE_DIR . "protected/AutoLoader/autoload_classes.php"),
            require(CONFIG_DIR . "autoload_classes.php")
        );
	}

    /**
     * Получение пути определённого класса
     *
     * @return mixed
     * @throws DebugException
     */
	public function getPath()
    {
		if(isset($this->path[$this->className])) {
			return $this->path[$this->className];
		} else {
			throw new DebugException("Не найден путь автозагрузки файла для класса: ".$this->className);
		}
	}

    /**
     * Загрузка файла класса
     *
     * @return mixed
     * @throws DebugException
     */
	public function loadClass()
    {
		$path = $this->getPath();

		if(!file_exists($path)) {
			throw new DebugException("Неудалось загрузить файл для класса: ".$this->className);
		}

		return require_once($path);
	}
} 