<?php

class Redirect
{
    /**
     * @var int
     */
    private $statusCode = 302;

    /**
     * @var array|Collection
     */
    private $headers;

    /**
     * @var string
     */
    private $url = null;

    /**
     * @var bool
     */
    private $secure;

    /**
     * Redirect constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param string $path
     * @param int $status
     * @param array $headers
     * @param bool $secure
     *
     * @return Redirect
     */
    public function to($path, $status = 302, $headers = [], $secure = null)
    {
        $this->url = $path;
        $this->statusCode = $status;
        $this->headers = $headers;
        $this->secure = $secure;

        return $this;
    }

    /**
     * @param string|Route $route
     * @param array $parameters
     * @param int $status
     * @param array $headers
     *
     * @return Redirect
     */
    public function route($route, $parameters = [], $status = 302, $headers = [])
    {
        if(is_string($route)) {
            $route = route($route);
        }

        return $this->to(app("url")->toRoute($route, $parameters), $status, $headers);
    }

    /**
     * @param string $action
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return mixed
     */
    public function action($action, $parameters = [], $status = 302, $headers = [])
    {
        $route = app("routes")->getByAction($action);

        return $this->to(app("url")->toRoute($route, $parameters), $status, $headers);
    }

    /**
     * @param string $url
     * @return Redirect
     */
    public function away($url)
    {
        return $this->to($url);
    }

    /**
     * @param string $path
     * @param int $status
     * @param array $headers
     *
     * @return Redirect
     */
    public function secure($path, $status = 302, $headers = [])
    {
        return $this->to($path, $status, $headers, true);
    }

    /**
     * @param int $status
     * @return Redirect
     */
    public function home($status = 302)
    {
        return $this->route("home", [], $status);
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return Redirect
     */
    public function flash($name, $value)
    {
        flash($name, $value);

        return $this;
    }

    /**
     * @param array|string $name
     * @param mixed $value
     *
     * @return Redirect
     */
    public function with($name, $value)
    {
        if(is_array($name))
            return $this->flash($name, $value);

        foreach ($name as $key => $value)
        {
            $this->flash($key, $value);
        }

        return $this;
    }

    /**
     * @return Collection
     * @throws HttpException
     */
    public function createRedirect()
    {
        if(is_null($this->url)) {
            throw new HttpException("Не указан адрес для переадресации");
        }

        $result = new Collection();

        $result->offsetSet("status", $this->statusCode);
        $result->offsetSet("url", $this->url);
        $result->offsetSet("headers", $this->headers);

        return $result;
    }
}