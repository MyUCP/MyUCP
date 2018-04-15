<?php

class RouteCompiler
{
    use RouteDependencyResolverTrait;

    /**
     * The route instance.
     *
     * @var Route
     */
    protected $route;

    /**
     * The container instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * Create a new Route compiler instance.
     *
     * @param  Route $route
     * @param  Application $app
     * @return void
     */
    public function __construct(Route $route, Application $app)
    {
        $this->route = $route;
        $this->app = $app;
    }

    /**
     * Compile the route.
     *
     * @return CompiledRoute
     *
     * @throws HttpException
     */
    public function compile()
    {
        try {
            if ($this->isControllerAction()) {
                $controller = $this->route->getController();
                $method = $this->route->getControllerMethod();

                $this->bindModels($this->route->models);

                return $this->runController($controller, $method);
            }

            return $this->runCallable();
        } catch (HttpException $e) {

            return $e->getResponse();
        }
    }

    /**
     * Bind models to route
     *
     * @param array $models
     * @return void
     */
    public function bindModels($models)
    {
        if(!empty($models))
            call_user_func_array("model", $models);
    }

    /**
     * Get the key / value list of parameters without null values.
     *
     * @return array
     */
    public function parametersWithoutNulls()
    {
        return array_filter($this->route->parameters(), function ($p) {
            return ! is_null($p);
        });
    }

    /**
     * Checks whether the route's action is a controller.
     *
     * @return bool
     */
    protected function isControllerAction()
    {
        return is_string($this->route->action['uses']);
    }

    /**
     * Run the route action and return the response.
     *
     * @return CompiledRoute
     */
    protected function runCallable()
    {
        $callable = $this->route->action['uses'];

        return $this->getCompiledResponse($callable(...array_values($this->resolveMethodDependencies(
            $this->parametersWithoutNulls(), new ReflectionFunction($this->route->action['uses'])
        ))));
    }

    /**
     * Run the route action and return the response.
     *
     * @param object $controller
     * @param string $method
     *
     * @return CompiledRoute
     *
     * @throws HttpException
     */
    protected function runController($controller, $method)
    {
        $parameters = $this->resolveClassMethodDependencies(
            $this->route->parametersWithoutNulls(), $controller, $method
        );

        if (method_exists($controller, 'callAction')) {
            return $this->getCompiledResponse($controller->callAction($method, $parameters));
        }

        return $this->getCompiledResponse($controller->{$method}(...array_values($parameters)));
    }

    /**
     * Get Response
     *
     * @param $content
     * @return CompiledRoute
     */
    public function getCompiledResponse($content)
    {
        return (new CompiledRoute(
            $this->route,
            $this->app,
            $content
        ));
    }
}