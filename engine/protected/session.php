<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Session {
	public $data = array();
			
  	public function __construct() {
		if(!session_id()) session_start();
		$this->data = &$_SESSION;
	}
}
?>
