<?php
/*
* MyUCP
*/

class View {
	private $registry;
	private $Zara;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->Zara = new Zara;
	}
	
	public function load($name, $vars = array()) {
		return $this->Zara->compile($name, $vars, new ZaraFactory)->getCompiled();
	}
}

function view($name, $vars = array()){
	global $registry;
	return $registry->view->load($name, $vars);
}