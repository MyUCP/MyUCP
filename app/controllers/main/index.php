<?php
/*
* MyUCP
* File Version 4.1
* Date: 27.10.2015
* Developed by Maksa988
*/

class indexController extends Controller {
	public function index() {

		return $this->load->view("welcome");
	}
}