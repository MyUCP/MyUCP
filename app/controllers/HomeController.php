<?php
/*
* MyUCP
*/

class HomeController extends Controller {

	public function welcome() {

        redirect("/admin/test-4-maksa")->with("error", "MAXIM");

		return view("welcome");
	}
}