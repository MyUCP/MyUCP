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
     * @return RouteHelper
     */
    function route($name = null){
		return new RouteHelper($name);
	}
}

if(!function_exists('redirect')) {
    /**
     * @param RouteHelper|string $value
     * @param array $parameters if $path is a route
     * @return Redirect
     */
    function redirect($path = null, $parameters = []) {
        $redirect = new Redirect();

        if(is_null($path)) {
            return $redirect;
        } elseif($path instanceof RouteHelper) {
            return $redirect->route($path);
        }

        return $redirect->to($path);
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

        return registry()->session->get($name, $value);
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


if(!function_exists('redirect_url')) {
    /**
     * @param $name
     * @param array $args
     * @return RouteHelper
     */
    function redirect_url($name, $args = []){
        return (new RouteHelper($name))->getRedirectURL($args);
    }
}

if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed   $target
     * @param  string|array  $key
     * @param  mixed   $default
     * @return mixed
     */
    function data_get($target, $key = null, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        while (! is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (! is_array($target)) {
                    return value($default);
                }

                $result = Arr::pluck($target, $key);

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }

            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (! function_exists('array_wrap')) {
    /**
     * If the given value is not an array, wrap it in one.
     *
     * @param  mixed  $value
     * @return array
     */
    function array_wrap($value)
    {
        return Arr::wrap($value);
    }
}

if(!function_exists('request')) {

    /**
     * @return Request
     */
    function request() {

        return registry()->request;
    }
}

if(!function_exists('response')) {

    /**
     * @return Response
     */
    function response() {

        return registry()->response;
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}