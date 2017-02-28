<?php
/*
* MyUCP
*/


if(!function_exists('registry')) {

    /**
     * @return Registry
     */
    function registry(){
	    global $registry;

	    return $registry;
	}

}

if(!function_exists('dd')) {

    /**
     * @param $value
     * @param bool $die
     */
    function dd($value, $die = true){
	    new Dumper($value, $die);
	}

}
 
if(!function_exists('ci')) {

    /**
     * @param $value
     */
    function ci($value) {
	    new Dumper($value, false, "ci");
	}
	
}

if(!function_exists('view')) {

    /**
     * @return mixed
     */
    function view($name, $vars = [], $exception = true){
        return registry()->view->load($name, $vars, $exception);
    }
}

if(!function_exists('model')) {

    /**
     * @return mixed
     */
    function model(){
		return registry()->load->model(func_get_args());
	}
}

if(!function_exists('library')) {


    /**
     * @return mixed
     */
    function library(){
		return registry()->load->library(func_get_args());
	}

}

if(!function_exists('inject')) {

    /**
     * @return mixed
     */
    function inject(){
		return registry()->load->inject(func_get_args());
	}

}

if(!function_exists('route')) {

    /**
     * @param null $name
     * @return mixed
     */
    function route($name = null){
		return new RouteHelper($name);
	}

}

if(!function_exists('redirect')) {

    /**
     * @param $value
     * @return mixed
     */
    function redirect($value){
		return new Redirect($value);
	}

}

if(!function_exists('refresh')) {

    /**
     * @return mixed
     */
    function refresh(){
		return redirect(route());
	}

}

if(!function_exists('cookie')) {

    /**
     * @param $name
     * @param null $value
     * @param null $time
     * @return Cookie
     */
    function cookie($name, $value = null, $time = null) {
		return new Cookie($name, $value, $time);
	}

}

if(!function_exists('config')) {

    /**
     * @param null $config
     * @return bool|mixed
     */
    function config($config = null) {
		if(!empty($config))
			return registry()->config->$config;

		return registry()->config;
	}

}


if(!function_exists('abort')) {

    /**
     * @param $code
     * @return HttpException
     */
    function abort($code) {
		return new HttpException($code);
	}

}

if(!function_exists('session')) {

    /**
     * @param $name
     * @param null $value
     * @return mixed
     */
    function session($name, $value = null) {

        return registry()->session->get($name, $va);
    }

}

if(!function_exists('flash')) {

    /**
     * @param $name
     * @param null $value
     * @return mixed
     */
    function flash($name, $value = null) {

        return registry()->session->flash($name, $value);
    }

}

if(!function_exists('lang')) {

    /**
     * @param $key
     * @param array $replace
     * @return mixed
     */
    function lang($key, $replace = []) {
        return Lang::get($key, $replace);
    }

}