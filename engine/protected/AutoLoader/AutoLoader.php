<?php
/*
* MyUCP
*/

class AutoLoader {

	private $path;
	private $className;

	// Загрузка списка классов и их путей
	// Загрузка файла класса
	public function __construct($className){
		$this->getPaths();
		$this->className = $className;
		$this->loadClass();
	}

	// Получения списка классов и их путей для загрузки файлов
	public function getPaths(){
		$this->path = require("./engine/protected/AutoLoader/autoload_classes.php");
	}

	// Получения пути для загрузки определённого класса
	public function getPath(){
		if(!empty($this->path[$this->className])){
			return $this->path[$this->className];
		} else {
			new Debug("Не найден путь автозагрузки файла для класса: ".$this->className);
		}
	}

	// Загрузка файла класса
	public function loadClass(){
		$path = $this->getPath();
		if(!file_exists($path)) {
			new Debug("Неудалось загрузить файл для класса: ".$this->className);
		}

		return require_once($path);
	}
} 