<?php
/*
* MyUCP
*/

class AutoLoader {

	private $path;
	private $className;

	/**
	 * @param [type] $className [description]
	 */
	public function __construct($className){
		$this->getPaths();
		$this->className = $className;
		$this->loadClass();
	}

	/**
	 * [Получение массива со списком путей и классов]
	 * @return [type] [description]
	 */
	public function getPaths(){
		$this->path = array_merge(
				require("./engine/protected/AutoLoader/autoload_classes.php"), 
				require("./configs/autoload_classes.php")
			);
	}

	/**
	 * [Получение пути определённого класса]
	 * @return [type] [description]
	 */
	public function getPath(){
		if(!empty($this->path[$this->className])){
			return $this->path[$this->className];
		} else {
			new Debug("Не найден путь автозагрузки файла для класса: ".$this->className);
		}
	}

	/**
	 * [Загрузка файла класса]
	 * @return [type] [description]
	 */
	public function loadClass(){
		$path = $this->getPath();
		if(!file_exists($path)) {
			new Debug("Неудалось загрузить файл для класса: ".$this->className);
		}

		return require_once($path);
	}
} 