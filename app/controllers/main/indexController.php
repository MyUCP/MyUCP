<?php
/*
* MyUCP
*/

class indexController extends Controller {
	public function index() {

		// dd($this->config);

		return $this->load->view("welcome");
	}
}