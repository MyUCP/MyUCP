<?php

class CompiledRoute implements Serializable
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var Application
     */
    private $app;

    /**
     * @var Response
     */
    private $response;

    /**
     * CompiledRoute constructor.
     *
     * @param Route $route
     * @param Application $app
     * @param $compileResult
     *
     * @return CompiledRoute
     */
    public function __construct(Route $route, Application $app, $compileResult)
    {
        $this->uri = $route->uri();
        $this->parameters = $route->parameters();
        $this->container = $app;
        $this->response = $app['response'];

        $this->response->setContent($compileResult);

        return $this;
    }

    /**
     * Get Response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response->send();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            'uri'           =>  $this->uri,
            'parameters'    =>  $this->parameters,
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized, ['allowed_classes' => false]);
        $this->uri = $data['uri'];
        $this->parameters = $data['parameters'];
    }
}