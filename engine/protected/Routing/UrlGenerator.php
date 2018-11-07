<?php

class UrlGenerator
{
    /**
     * The route collection.
     *
     * @var RouteCollection
     */
    protected $routes;

    /**
     * The Request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * UrlGenerator constructor.
     *
     * @param RouteCollection $routes
     * @param Request $request
     */
    function __construct(RouteCollection $routes, Request $request)
    {
        $this->routes = $routes;

        $this->request = $request;
    }

    /**
     * Get the current URL for the request.
     *
     * @return string
     */
    public static function current()
    {
        return request()->getPathInfo();
    }

    /**
     * Get the current Route instance.
     *
     * @return string
     */
    public static function currentRoute()
    {
        return request()->getRoute();
    }

    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null)
    {
        // First we will check if the URL is already a valid URL. If it is we will not
        // try to generate a new one but will simply return the URL as is, which is
        // convenient since developers do not always have to check if it's valid.
        if ($this->isValidUrl($path)) {
            return $path;
        }

        return rtrim(config()->url, "/") . "/" . trim($path, "/");
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string  $path
     * @return bool
     */
    public function isValidUrl($path)
    {
        if (! preg_match('~^(#|//|https?://|mailto:|tel:)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }

    /**
     * Get the URL to a named route.
     *
     * @param  string  $name
     * @param  mixed   $parameters
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function route($name, $parameters = [])
    {
        if (! is_null($route = $this->routes->getByName($name))) {
            return $this->toRoute($route, $parameters);
        }

        throw new InvalidArgumentException("Route [{$name}] not defined.");
    }

    /**
     * Get the URL to a controller action.
     *
     * @param  string  $action
     * @param  mixed   $parameters
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function action($action, $parameters = [])
    {
        if (is_null($route = $this->routes->getByAction($action))) {
            throw new InvalidArgumentException("Action {$action} not defined.");
        }

        return $this->toRoute($route, $parameters);
    }

    /**
     * Get the URL for a given route instance.
     *
     * @param  Route  $route
     * @param  mixed  $parameters
     * @return string
     */
    public function toRoute(Route $route, $parameters = [])
    {
        return  $this->to($this->getFromRegexUrl($route->uri(), $parameters));
    }

    /**
     * Replace the given parameters on route regex
     *
     * @param string $uri
     * @param array $params
     * @return mixed
     */
    protected function getFromRegexUrl($uri, $params)
    {
        $url = $uri;

        if(!empty($params)) {
            foreach ($params as $key => $value) {
                $url = preg_replace('/\{(['. $key .']+):(.*?)\}/', $value, $url);
            }
        }

        return "/" . trim($url, "/");
    }

    /**
     * Determine if the given path is a local.
     *
     * @param $url
     * @return bool
     */
    protected function isLocalUrl($url)
    {
        return !(Str::startsWith($url, "http://") || Str::startsWith($url, "https://"));
    }

    /**
     * Generate the URL to an application asset.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }

        // Once we get the root URL, we will check to see if it contains an index.php
        // file in the paths. If it does, we will remove it since it is not needed
        // for asset paths, but only for routes to endpoints in the application.

        return rtrim(config()->url, "/") . '/assets/' . trim($path, '/');
    }
}