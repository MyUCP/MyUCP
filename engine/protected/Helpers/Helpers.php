<?php
/*
* MyUCP
*/

if(!function_exists('registry')) {

    /**
     * @return Registry
     */
    function registry()
    {
	    global $registry;

	    return $registry;
	}

}

if(!function_exists('app')) {

    /**
     * @param null|mixed $name
     * @return Application|object
     */
    function app($name = null)
    {
        global $app;

        if(is_null($name))
            return $app;

        return $app->make($name);
    }

}

if(!function_exists('dd')) {

    /**
     * @param $value
     * @param bool $die
     * @return Dumper
     */
    function dd($value, $die = true)
    {
	    return new Dumper($value, $die);
	}

}
 
if(!function_exists('ci')) {

    /**
     * @param $value
     * @return Dumper
     */
    function ci($value)
    {
	    return new Dumper($value, false, "ci");
	}

}

if(!function_exists('view')) {

    /**
     * @return mixed
     */
    function view($name, $vars = [], $exception = true)
    {
        return app('view')->load($name, $vars, $exception);
    }

}

if(!function_exists('model')) {

    /**
     * @return mixed
     */
    function model()
    {
		return app('load')->model(func_get_args());
	}

}

if(!function_exists('library')) {

    /**
     * @return mixed
     */
    function library()
    {
		return app('load')->library(func_get_args());
	}

}

if(!function_exists('inject')) {

    /**
     * @return mixed
     */
    function inject()
    {
		return app('load')->inject(func_get_args());
	}

}

if(!function_exists('route')) {

    /**
     * @param null $name
     * @return Route|null
     */
    function route($name = null)
    {
        if(is_null($name))
            return app("router")->getCurrentRoute();

        if(!app("router")->has($name))
            return null;

        return app("router")->getRouteWithName($name);
	}

}

if(!function_exists('redirect')) {

    /**
     * @param Route|string $value
     * @param array $parameters if $path is a route
     * @param int $status
     * @return Redirect
     */
    function redirect($path = null, $parameters = [], $status = 302)
    {
        $redirect = new Redirect();

        if(is_null($path)) {
            return $redirect;
        } elseif($path instanceof Route) {
            return $redirect->route($path, $parameters, $status);
        }

        return $redirect->to($path, $status);
	}

}

if(!function_exists('refresh')) {

    /**
     * @return mixed
     */
    function refresh()
    {
		return redirect(route());
	}

}

if(!function_exists('cookie')) {

    /**
     * @param string $name
     * @param string $value
     * @param int $minutes
     * @return Cookie|string
     */
    function cookie($name, $value = null, $minutes = null)
    {
        if(is_null($value)) {
            return request()->cookie->get($name);
        }

		return new Cookie($name, $value, time() + ($minutes * 60));
	}

}

if(!function_exists('config')) {

    /**
     * @param null $config
     * @return bool|mixed
     */
    function config($config = null)
    {
		if(!empty($config))
			return app()->config->$config;

		return app()->config;
	}

}


if(!function_exists('abort')) {

    /**
     * @param $code
     * @throws HttpException
     */
    function abort($code = 404, $message = "Страница не найдена")
    {
		throw new HttpException($code, $message);
	}

}

if(!function_exists('session')) {

    /**
     * @param $name
     * @param null $value
     * @return mixed|Session
     */
    function session($name = null, $value = null)
    {
        if(is_null($name))
            return app("session");

        return app('session')->get($name, $value);
    }

}

if(!function_exists('flash')) {

    /**
     * @param $name
     * @param null $value
     * @return mixed
     */
    function flash($name, $value = null)
    {
        return session()->flash($name, $value);
    }

}

if(!function_exists('lang')) {

    /**
     * @param $key
     * @param array $replace
     * @return mixed
     */
    function lang($key, $replace = [])
    {
        return Lang::get($key, $replace);
    }

}

if(!function_exists('redirect_url')) {

    /**
     * @param Route $name
     * @param array $args
     * @return string
     */
    function redirect_url($name, $args = [])
    {
        return app("url")->route($name, $args);
    }

}

if(!function_exists('url')) {

    /**
     * @param Route|string $path
     * @param array $args
     * @return string
     */
    function url($path = null, $args = [])
    {
        if($path instanceof Route)
            return app("url")->route($path, $args);

        if(!is_null(app("routes")->getByName($path)))
            return app("url")->route($path, $args);

        if(is_null($path))
            return app("url");

        return app("url")->to($path);
    }

}

if(!function_exists('asset')) {

    /**
     * @param string $path
     * @return string
     */
    function asset($path)
    {
        return app("url")->asset($path);
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
    function request($key = null)
    {
        if(is_null($key))
            return app('request');

        return app('request')->input($key);
    }

}

if(!function_exists('response')) {

    /**
     * @return Response
     */
    function response($content = null)
    {
        if(is_null($content))
            return app('response');

        return app('response')->setContent($content);
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

if (! function_exists('method_field')) {
    /**
     * Generate a form field to spoof the HTTP verb used by forms.
     *
     * @param  string  $method
     * @return string
     */
    function method_field($method)
    {
        return '<input type="hidden" name="_method" value="'.$method.'">';
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Retrieves the value of the current CSRF token
     *
     * @return string
     */
    function csrf_token()
    {
        return app("csrftoken")->token();
    }
}

if (! function_exists('csrf_field')) {
    /**
     * Generates an HTML hidden input field containing the value of the CSRF token
     *
     * @return string
     */
    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="'. app("csrftoken")->token() .'">';
    }
}

if (! function_exists('extension')) {
    /**
     * Run extension
     *
     * @return \MyUCP\Extension\Extensionable
     */
    function extension($extension, ...$args)
    {
        return app("extension")->run($extension, ...$args);
    }
}

if (! function_exists('ext')) {
    /**
     * Run extension
     *
     * @return \MyUCP\Extension\Extensionable
     */
    function ext($extension, ...$args)
    {
        return extension($extension, ...$args);
    }
}

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return null;
        }

        if (Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}