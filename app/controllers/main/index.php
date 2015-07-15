<?php
/*
* MyUCP
* File Version 4.0.0.1
* Date: 15.07.2015
* Developed by Maksa988
*/

class indexController extends Controller {
	public function index() {

		echo "Hello World!";
		echo "<pre>";
		print_r($this->config);
	}
}