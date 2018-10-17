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
     */
	public function getPath()
    {
		if(isset($this->path[$this->className])) {
			return $this->path[$this->className];
		}

		return null;
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

		if(is_null($path))
		    $path = $this->loadPsr4();

		if(!file_exists($path)) {
			throw new DebugException("Неудалось загрузить файл для класса: ".$this->className);
		}

		return require_once($path);
	}


	public function loadPsr4()
    {
        $names = [
            "MyUCP\\" => ENGINE_DIR . 'protected' . DIRECTORY_SEPARATOR,
            "Extensions\\" => EXTENSIONS_DIR,
            "App\\" => APP_DIR,
        ];

        $path = str_replace(array_keys($names), array_values($names), $this->className);
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);

        return $path . ".php";
    }
} 