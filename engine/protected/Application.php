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
     * @var array
     */
    protected $alias = [];

    /**
     * Application constructor.
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;

        $this->makeAliases();
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
        if(isset($this->alias[$name])) {
            $alias = $this->alias[$name];

            return $this->registry->$alias;
        }

        if($this->registry->$name !== false)
            return $this->registry->$name;

        return false;
    }

    /**
     * @param $name
     * @param null $instance
     * @return bool|mixed|null
     */
    public function make($name, $instance = null)
    {
        if($instance == null) {
            if(!$this->has($name)) {
                return $this->make($name, new $name);
            }

            return $this->$name;
        }

        return $this->$name = $instance;
    }

    /**
     * Make new instance with parameters
     *
     * @param $name
     * @param array $parameters
     * @return bool|mixed|null
     */
    public function makeWith($name, $parameters = [])
    {
        if($this->has($name))
            return $this->make($name);

        return $this->make($name, new $name(...$parameters));
    }

    /**
     * Make alias for instance or only name
     *
     * @param $alias
     * @param null $name
     * @param null $instance
     * @return bool|mixed|null
     */
    public function alias($alias, $name = null, $instance = null)
    {
        if(is_null($name)) {
            return $this->make($alias);
        }

        $this->alias[$alias] = $name;

        if(is_null($instance))
            return $this->make($name);

        return $this->make($name, $instance);
    }

    /**
     * Make alias for new instance with parameters
     *
     * @param $alias
     * @param $name
     * @param array $parameters
     * @return bool|mixed|null
     */
    public function aliasWith($alias, $name, $parameters = [])
    {
        return $this->alias($alias, $name, $this->makeWith($name, $parameters));
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
        if(!file_exists(ENV . DIRECTORY_SEPARATOR . ".env")) {
            if(!copy(ENV . DIRECTORY_SEPARATOR . ".env.example", ENV . DIRECTORY_SEPARATOR . ".env")) {
                throw new Exception("Doest not exists [.env] or [.env.example] files.");
            }
        }

        $this->makeWith(\MyUCP\Dotenv\Dotenv::class, [ENV]);
        $this->make("dotenv")->load();

        $this->make(Config::class);

        $this->make(HandleExceptions::class)->make($this);

        if(env("APP_DB", false)) {
            $this->makeWith(DB::class, [$this->make("config")->db]);
        }

        $this->make(Session::class);
        $this->make(Request::class);
        $this->make(Response::class);
        $this->makeWith(CsrfToken::class,[$this['request']]);
        $this->make(Load::class);
        $this->makeWith(Translator::class, [new LocalizationLoader(config()->locale, config()->fallback_locale), config()->locale]);
        $this->make(View::class);
        $this->make(Router::class);
        $this->makeWith(UrlGenerator::class, [$this["routes"], $this["request"]]);

        $this->makeWith(\MyUCP\Extension\Extension::class, [$this]);

        $this->initialized = true;

        return $this;
    }

    /**
     * Application launch
     */
    public function run()
    {
        $this->make("extension")->boot();
        $this->make("router")->loadRouteService();
        $this->make("router")->loadRoutes(APP_DIR . 'routers.php');
        $this->make("router")->make();
        $this->make("response")->prepare($this->make("request"));
        $this->make("response")->send();
        $this->make("session")->unsetFlash();
    }

    /**
     * Make default aliases
     */
    protected function makeAliases()
    {
        $this->alias = [
            "dotenv" => \MyUCP\Dotenv\Dotenv::class,
            "config" => Config::class,
            "handleException" => HandleExceptions::class,
            "db" => DB::class,
            "session" => Session::class,
            "request" => Request::class,
            "response" => Response::class,
            "csrftoken" => CsrfToken::class,
            "load" => Load::class,
            "lang" => Translator::class,
            "view" => View::class,
            "router" => Router::class,
            "url" => UrlGenerator::class,
            "extension" => \MyUCP\Extension\Extension::class,
        ];
    }

    public function has($name)
    {
        if(isset($this->alias[$name]))
            return true;

        if($this->registry->$name !== false)
            return true;

        return false;
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
        return $this->registry->$key !== false;
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