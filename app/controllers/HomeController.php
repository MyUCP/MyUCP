<?php
/*
* MyUCP
*/

class HomeController extends Controller {

	public function welcome() {

		dd($this->response);

		return view("welcome");
	}

	public function test() {
		dd(refresh());
		return view("welcome");
	}
}