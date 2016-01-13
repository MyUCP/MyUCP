<?php
/*
* MyUCP
*/

class AutoLoader {

	private $path;
	private $className;

	/**
	 * [__construct description]
	 * @param [type] $className [description]
	 */
	public function __construct($className){
		$this->getPaths();
		$this->className = $className;
		$this->loadClass();
	}

	/**
	 * [getPaths description]
	 * @return [type] [description]
	 */
	public function getPaths(){
		$this->path = require("./engine/protected/AutoLoader/autoload_classes.php");
	}

	/**
	 * [getPath description]
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
	 * [loadClass description]
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