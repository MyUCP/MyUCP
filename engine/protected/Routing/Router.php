<?php

class Router
{
    /**
     * The route collection instance.
     *
     * @var RouteCollection
     */
    protected $routes;

    /**
     * The currently dispatched route instance.
     *
     * @var Route
     */
    protected $current;

    /**
     * The request currently being dispatched.
     *
     * @var Request
     */
    protected $currentRequest;

    /**
     * All of the verbs supported by the router.
     *
     * @var array
     */
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * The route group attribute stack.
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * Create a new Router instance.
     *
     * @return void
     */
    public function __construct(Request $request = null)
    {
        $this->routes = new RouteCollection;
        $this->currentRequest = $request ?: app("request");

        app()->make("routes", $this->routes);
    }

    /**
     * Register a new GET route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function get($uri, $action = null)
    {
        return app("router")->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    /**
     * Register a new POST route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function post($uri, $action = null)
    {
        return app("router")->addRoute('POST', $uri, $action);
    }

    /**
     * Register a new PUT route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function put($uri, $action = null)
    {
        return app("router")->addRoute('PUT', $uri, $action);
    }
    /**
     * Register a new PATCH route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */

    public static function patch($uri, $action = null)
    {
        return app("router")->addRoute('PATCH', $uri, $action);
    }

    /**
     * Register a new DELETE route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function delete($uri, $action = null)
    {
        return app("router")->addRoute('DELETE', $uri, $action);
    }

    /**
     * Register a new OPTIONS route with the router.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function options($uri, $action = null)
    {
        return app("router")->addRoute('OPTIONS', $uri, $action);
    }

    /**
     * Register a new route responding to all verbs.
     *
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function any($uri, $action = null)
    {
        $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];

        return app("router")->addRoute($verbs, $uri, $action);
    }

    /**
     * Register a new route with the given verbs.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function match($methods, $uri, $action = null)
    {
        return app("router")->addRoute(array_map('strtoupper', (array) $methods), $uri, $action);
    }

    /**
     * Register a new route responding a view.
     *
     * @param  string  $uri
     * @param  string  $viewName
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function view($uri, $viewName, $action = null)
    {
        return self::any($uri, $action)->uses(function () use ($viewName) {
            return view($viewName);
        });
    }

    /**
     * Register a new route responding a redirect.
     *
     * @param  string  $uri
     * @param  Route|string  $to
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    public static function redirect($uri, $to, $action = null)
    {
        return self::any($uri, $action)->uses(function () use ($to) {
            return redirect($to);
        });
    }

    /**
     * Register a new routes group
     *
     * @param string $domain
     * @param  \Closure|string  $routes
     * @return void
     */
    public static function domain($domain, $routes)
    {
        app("router")->group(["domain" => $domain], $routes);
    }

    /**
     * Register a new routes group
     *
     * @param string $name
     * @param  \Closure|string  $routes
     * @return void
     */
    public static function name($name, $routes)
    {
        app("router")->group(["as" => $name], $routes);
    }

    /**
     * Register a new routes group
     *
     * @param string $prefix
     * @param  \Closure|string  $routes
     * @return void
     */
    public static function prefix($prefix, $routes)
    {
        app("router")->group(["prefix" => $prefix], $routes);
    }

    /**
     * Register a new routes group
     *
     * @param bool $condition
     * @param  \Closure|string  $routes
     * @return void
     */
    public static function condition($condition, $routes)
    {
        if($condition)
            app("router")->group(["condition" => $condition], $routes);
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  array  $attributes
     * @param  \Closure|string  $routes
     * @return void
     */
    public function group(array $attributes, $routes)
    {
        $this->updateGroupStack($attributes);

        // Once we have updated the group stack, we'll load the provided routes and
        // merge in the group's attributes when the routes are created. After we
        // have created the routes, we will pop the attributes off the stack.
        $this->loadRoutes($routes);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array  $attributes
     * @return void
     */
    protected function updateGroupStack(array $attributes)
    {
        if (! empty($this->groupStack)) {
            $attributes = RouteGroup::merge($attributes, end($this->groupStack));
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given array with the last group stack.
     *
     * @param  array  $new
     * @return array
     */
    public function mergeWithLastGroup($new)
    {
        return RouteGroup::merge($new, end($this->groupStack));
    }

    /**
     * Add a route to the underlying route collection.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return Route
     */
    protected function addRoute($methods, $uri, $action)
    {
        return $this->routes->add($this->createRoute($methods, $uri, $action));
    }

    /**
     * Create a new route instance.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  mixed  $action
     * @return Route
     */
    protected function createRoute($methods, $uri, $action)
    {
        $route = $this->newRoute(
            $methods, $this->getPrefix($uri), $action
        );

        if($this->hasGroupStack()) {
            $this->mergeGroupAttributesIntoRoute($route);
        }

        return $route;
    }

    /**
     * Create a new Route object.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  mixed  $action
     * @return Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action));
    }

    /**
     * Load the provided routes.
     *
     * @param  \Closure|string  $routes
     * @return void
     */
    public function loadRoutes($routes)
    {
        if ($routes instanceof Closure) {
            $routes($this);
        } else {
            require $routes;
        }
    }

    /**
     * Get the underlying route collection.
     *
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Set the route collection instance.
     *
     * @param  RouteCollection  $routes
     * @return void
     */
    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;

        app()->make('routes', $this->routes);
    }

    /**
     * Get the request currently being dispatched.
     *
     * @return Request
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * Get the currently dispatched route instance.
     *
     * @return Route
     */
    public function getCurrentRoute()
    {
        return $this->current();
    }

    /**
     * Get the currently dispatched route instance.
     *
     * @return Route
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Check if a route with the given name exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function has($name)
    {
        return $this->routes->hasNamedRoute($name);
    }

    /**
     * Get a route with the given name
     *
     * @param string $name
     * @return null|Route
     */
    public function getRouteWithName($name)
    {
        return $this->routes->getByName($name);
    }

    /**
     * Get the current route name.
     *
     * @return string|null
     */
    public function currentRouteName()
    {
        return $this->current() ? $this->current()->getName() : null;
    }

    /**
     * Make the request to the application.
     *
     * @return mixed
     */
    public function make()
    {
        $this->currentRequest = app("request");

        $this->routes->refreshNameLookups();

        $route = $this->findRoute();

        if($route instanceof Route) {

            $route->compileRoute(app());

            return $route->getCompiled();

        } else {
            exit($route->getResponse());
        }
    }

    /**
     * Find the route matching a given request.
     *
     * @return Route
     */
    protected function findRoute()
    {
        $this->current = $route = $this->routes->match($this->currentRequest);

        app()->make(Route::class, $route);

        return $route;
    }

    /**
     * Determine if the router currently has a group stack.
     *
     * @return bool
     */
    public function hasGroupStack()
    {
        return ! empty($this->groupStack);
    }

    public function mergeGroupAttributesIntoRoute(Route $route)
    {
        foreach ($this->groupStack as $group)
        {
            if(isset($group['domain'])) {
                $route->action['domain'] = $group['domain'];
            }

            if(isset($group['as'])) {
                $route->name($group['as'].$route->getName());
            }
        }
    }

    /**
     * Get the prefix from the last group on the stack.
     *
     * @return string
     */
    public function getLastGroupPrefix()
    {
        if (! empty($this->groupStack)) {
            $last = end($this->groupStack);
            return $last['prefix'] ?? '';
        }
        return '';
    }

    /**
     * Prefix the given URI with the last prefix.
     *
     * @param  string  $uri
     * @return string
     */
    protected function getPrefix($uri)
    {
        return trim(trim($this->getLastGroupPrefix(), '/').'/'.trim($uri, '/'), '/') ?: '/';
    }
}