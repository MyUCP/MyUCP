<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Document {
	private $title;
	private $activeSection;
	private $activeItem;
	private $scripts = array();
	
	/* Заголовок страницы */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	/* Активный раздел меню */
	public function setActiveSection($section) {
		$this->activeSection = $section;
	}
	
	public function getActiveSection() {
		return $this->activeSection;
	}
	
	/* Активный элемент меню */
	public function setActiveItem($item) {
		$this->activeItem = $item;
	}
	
	public function getActiveItem() {
		return $this->activeItem;
	}
	
	/* Скрипты */
	public function addScript($script) {
		$this->scriptsarray[] = $script;
	}
	
	public function getScripts() {
		return $this->scripts;
	}
}
?>
