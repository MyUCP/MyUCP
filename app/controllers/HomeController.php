<?php
/*
* MyUCP
*/

class HomeController extends Controller {

	public function welcome() {
		dd($this->router->route());
		return view("welcome");
	}
}