<?php

class Application implements ArrayAccess
{
    const VERSION = "5.6.1";

    /**
     * Application status
     * @var bool
     */
    private $initialized = false;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Application constructor.
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->registry->$name = $value;
    }

    /**
     * @param $name
     * @return bool|mixed
     */
    public function __get($name)
    {
        return $this->registry->$name;
    }

    /**
     * @param $name
     * @param null $value
     * @return bool|mixed|null
     */
    public function make($name, $value = null)
    {
        if($value == null) {
            return $this->$name;
        }

        return $this->$name = $value;
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Initialization of the main classes for the project
     */
    public function init()
    {
        $this->make("config", new Config());
        $this->make("session", new Session());
        $this->make("request", new Request());
        $this->make("response", new Response());
        $this->make("load", new Load());
        $this->make("lang", new Translator(new LocalizationLoader(config()->locale, config()->fallback_locale), config()->locale));
        $this->make("view", new View());
        $this->make("router", new Router());
        $this->make("url", new UrlGenerator($this["routes"], $this["request"]));

        if(is_array($this->config->db)) {
            $this->make("db", new DB($this->make("config")->db));
        }

        $this->initialized = true;
    }

    /**
     * Application launch
     */
    public function run()
    {
        $this->make("router")->loadRoutes(APP_DIR . 'routers.php');
        $this->make("router")->make();
        $this->make("response")->prepare($this->make("request"));
        $this->make("response")->send();
        $this->make("session")->unsetFlash();
    }

    /**
     * Service method
     *
     * @param $value
     * @return mixed
     */
    public function escape($value)
    {
        if(isset($this->db))
            return $this->db->escape($value);

        return $value;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->registry->$key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->make($key, $value);
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->registry->$key);
    }

}