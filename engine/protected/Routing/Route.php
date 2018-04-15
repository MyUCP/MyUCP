<?php

class Route
{

    /**
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $regexUri;

    /**
     * @var array
     */
    public  $methods;

    /**
     * @var array
     */
    public $action;

    /**
     * @var mixed
     */
    public $controller;

    /**
     * @var array
     */
    public $parameters;

    /**
     * The parameter names for the route.
     *
     * @var array|null
     */
    public $parameterNames;

    /**
     * The parameter patterns for the route.
     *
     * @var array|null
     */
    public $parameterPatterns;

    /**
     * The compiled version of the route.
     *
     * @var CompiledRoute
     */
    public $compiled;

    /**
     * Create a new Route instance.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array  $action
     * @return void
     */
    public function __construct($methods, $uri, $action)
    {
        $this->uri = $uri;
        $this->regexUri = $this->parseUri();
        $this->methods = (array) $methods;
        $this->action = $this->parseAction($action);
        $this->parameterNames = $this->parseParameterNames();
        $this->parameterPatterns = $this->parseParameterPatterns();
    }

    /**
     * Parse URI to regex pattern
     *
     * @return string
     */
    public function parseUri()
    {
        return RouteMatch::uriToRegex($this);
    }

    /**
     * Parse the route action into a standard array.
     *
     * @param  callable|array|null  $action
     * @return array
     *
     * @throws \UnexpectedValueException
     */
    public function parseAction($action)
    {
        return RouteAction::parse($this->uri, $action);
    }

    /**
     * Parse URI parameter names to array
     *
     * @return array
     */
    public function parseParameterNames()
    {
        return RouteMatch::parseParameterNames($this);
    }
    /**
     * Parse URI parameter patterns to array
     *
     * @return array
     */
    public function parseParameterPatterns()
    {
        return RouteMatch::parseParameterPatterns($this);
    }

    /**
     * Get the domain defined for the route.
     *
     * @return string|null
     */
    public function domain()
    {
        return isset($this->action['domain'])
            ? str_replace(['http://', 'https://'], '', $this->action['domain']) : null;
    }

    /**
     * Get the HTTP verbs the route responds to.
     *
     * @return array
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set a parameter to the given value.
     *
     * @param  string  $name
     * @param  mixed   $value
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->parameters();

        $this->parameters[$name] = $value;
    }

    /**
     * Set a parameters to the given array.
     *
     * @param  array  $parameters
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Unset a parameter on the route if it is set.
     *
     * @param  string  $name
     * @return void
     */
    public function forgetParameter($name)
    {
        $this->parameters();

        unset($this->parameters[$name]);
    }

    /**
     * Get the key / value list of parameters for the route.
     *
     * @return array
     *
     * @throws \LogicException
     */
    public function parameters()
    {
        if (isset($this->parameters)) {
            return $this->parameters;
        }

        throw new LogicException('Route is not bound.');
    }

    /**
     * Get the key / value list of parameters without null values.
     *
     * @return array
     */
    public function parametersWithoutNulls()
    {
        return array_filter($this->parameters(), function ($p) {
            return ! is_null($p);
        });
    }

    /**
     * Set the router instance on the route.
     *
     * @param  Router  $router
     * @return $this
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Set the container instance on the route.
     *
     * @param  Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the name of the route instance.
     *
     * @return string
     */
    public function getName()
    {
        return isset($this->action['as']) ? $this->action['as'] : null;
    }

    /**
     * Determine whether the route's name matches the given name.
     *
     * @param  string  $name
     * @return bool
     */
    public function named($name)
    {
        return $this->getName() === $name;
    }

    /**
     * Get the URI associated with the route.
     *
     * @return string
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Compile the route
     *
     * @param Application $app
     *
     * @return void
     */
    public function compileRoute($app)
    {
        if (! $this->compiled) {
            $this->compiled = (new RouteCompiler($this, $app))->compile();
        }
    }

    /**
     * Get the controller instance for the route.
     *
     * @return mixed
     */
    public function getController()
    {
        $class = $this->loadController();

        if (!$this->controller) {
            $this->controller = app()->make($class, new $class(app()));
        }

        return $this->controller;
    }

    public function loadController()
    {
        $controllerName = $this->parseControllerCallback()[0];

        if(strpos($controllerName, ".")){
            $path = explode(".", $controllerName);

            $controller = array_shift(array_reverse($path));

            array_pop($path);

            $folder = implode("/", $path);
        } else {
            $controller = $controllerName;
        }

        if(!isset($folder)){
            $controllerFile = APP_DIR . 'controllers/' . $controller . '.php';
        } else {
            $controllerFile = APP_DIR . 'controllers/' . $folder . '/' . $controller . '.php';
        }

        require_once($controllerFile);

        return $controller;
    }

    /**
     * Get the controller method used for the route.
     *
     * @return string
     */
    public function getControllerMethod()
    {
        return $this->parseControllerCallback()[1];
    }

    /**
     * Parse the controller.
     *
     * @return array
     */
    protected function parseControllerCallback()
    {
        return Str::parseCallback($this->action['uses']);
    }

    /**
     * Get the compiled version of the route.
     *
     * @return CompiledRoute
     */
    public function getCompiled()
    {
        return $this->compiled;
    }
}