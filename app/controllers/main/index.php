<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class indexController extends Controller {
	public function index() {
		echo "<pre>";
		echo "Отправлено письм: 23560";
		echo "<hr>";
		echo "Время выполнения задачи: 1.356 сек.";
		echo "<hr>";
		print_r($this->config);
	}
}