<?php
/*
* MyUCP
*/

class indexController extends Controller {
	public function index() {

		return $this->load->view("welcome");
	}
}