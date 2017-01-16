<?php
/*
* MyUCP
*/

if(!function_exists('dd')) {

	function dd($value, $die = true){
	    new Dumper($value, $die);
	}

}
 
if(!function_exists('ci')) {

	function ci($value) {
	    new Dumper($value, false, "ci");
	}
	
}

if(!function_exists('model')) {

	function model(){
		global $registry;
		return $registry->load->model(func_get_args());
	}
}

if(!function_exists('library')) {


	function library(){
		global $registry;
		return $registry->load->library(func_get_args());
	}

}

if(!function_exists('inject')) {

	function inject(){
		global $registry;
		return $registry->load->inject(func_get_args());
	}

}

if(!function_exists('route')) {

	function route($name = null){
		global $registry;
		return $registry->router->route($name);
	}

}

if(!function_exists('redirect')) {

	function redirect($value){
		global $registry;
		
		// If it`s array then it`s maybe router
		if(is_array($value)) {
			$url = (!empty($value['rule'])) ? $value['rule'] : "/";
			return $registry->response->redirect($url);
		}

		return $registry->response->redirect($value);
	}

}

if(!function_exists('refresh')) {

	function refresh(){
		global $registry;
		return redirect(route());
	}

}

if(!function_exists('cookie')) {

	function cookie($name, $value = null, $time = null) {
		return new Cookie($name, $value, $time);
	}

}

if(!function_exists('config')) {

	function config() {
		return new Config();
	}

}