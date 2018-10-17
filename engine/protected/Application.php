<?php

class Application implements ArrayAccess
{
    const VERSION = "5.7.1";

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
     *
     * @return $this
     * @throws DebugException|Exception
     */
    public function init()
    {
        $this->make("dotenv", new \MyUCP\Dotenv\Dotenv(ENV));
        $this->make("dotenv")->load();

        $this->make("config", new Config());

        $this->make("handleException", new HandleExceptions())->make($this);

        if(env("APP_DB", false)) {
            $this->make("db", new DB($this->make("config")->db));
        }

        $this->make("session", new Session());
        $this->make("request", new Request());
        $this->make("response", new Response());
        $this->make("csrftoken", new CsrfToken($this['request']));
        $this->make("load", new Load());
        $this->make("lang", new Translator(new LocalizationLoader(config()->locale, config()->fallback_locale), config()->locale));
        $this->make("view", new View());
        $this->make("router", new Router());
        $this->make("url", new UrlGenerator($this["routes"], $this["request"]));

        $this->make("extension", new \MyUCP\Extension\Extension($this));

        $this->initialized = true;

        return $this;
    }

    /**
     * Application launch
     */
    public function run()
    {
        $this->make("extension")->boot();
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
        if(!is_null($this->db) && $this->db !== false)
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

    /**
     * Get path to the app directory.
     *
     * @param null|string $path
     * @return string
     */
    public function appPath($path = null)
    {
        if(is_null($path)) {
            return APP_DIR;
        }

        return APP_DIR . $path;
    }

    /**
     * Get path to the engine directory.
     *
     * @param null|string $path
     * @return string
     */
    public function enginePath($path = null)
    {
        if(is_null($path)) {
            return ENGINE_DIR;
        }

        return ENGINE_DIR . $path;
    }

    /**
     * Get path to the resources directory.
     *
     * @param null|string $path
     * @return string
     */
    public function resourcesPath($path = null)
    {
        if(is_null($path)) {
            return RESOURCES_DIR;
        }

        return RESOURCES_DIR . $path;
    }

    /**
     * Get path to the views directory.
     *
     * @param null|string $path
     * @return string
     */
    public function viewsPath($path = null)
    {
        if(is_null($path)) {
            return VIEWS_DIR;
        }

        return VIEWS_DIR . $path;
    }

    /**
     * Get path to the assets directory.
     *
     * @param null|string $path
     * @return string
     */
    public function assetsPath($path = null)
    {
        if(is_null($path)) {
            return ASSETS_DIR;
        }

        return ASSETS_DIR . $path;
    }

    /**
     * Get path to the config directory.
     *
     * @param null|string $path
     * @return string
     */
    public function configPath($path = null)
    {
        if(is_null($path)) {
            return CONFIG_DIR;
        }

        return CONFIG_DIR . $path;
    }
}