<?php
/*
* MyUCP
*/

class HomeController extends Controller {

	public function welcome() {

	    redirect(route("home"));

		return view("welcome");
	}
}