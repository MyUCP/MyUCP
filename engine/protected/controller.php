<?php
/*
* MyUCP
*/

abstract class Controller {
	private $registry;
	protected $data = array();
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->$key;
	}
	
	public function __set($key, $value) {
		$this->registry->$key = $value;
	}
	
	public function extendViews($child = array()) {
		foreach($child as $item) {			
			$this->data[$this->registry->router->getMethod($item)] = $this->registry
												->router
												->loadControler(
														$this->registry->router->getController($item), 
														$this->registry->router->getMethod($item)
													);
		}
	}

	public function extend($child = array()) {
		$this->extendViews($child);
	}
}
?>