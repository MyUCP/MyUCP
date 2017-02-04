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
	
	public function load($name, $vars = array(), $exception) {
		return $this->Zara->compile($name, $vars, new ZaraFactory, $exception)->getCompiled();
	}
}