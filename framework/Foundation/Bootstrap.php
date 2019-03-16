<?php

namespace MyUCP\Foundation;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;
use Exception;
use MyUCP\Config\Config;
use MyUCP\Database\DB;
use MyUCP\Debug\HandleExceptions;
use MyUCP\Extension\Extension;
use MyUCP\Load;
use MyUCP\Localization\LocalizationLoader;
use MyUCP\Localization\Translator;
use MyUCP\Request\Request;
use MyUCP\Response\Response;
use MyUCP\Routing\CsrfToken;
use MyUCP\Routing\Router;
use MyUCP\Routing\UrlGenerator;
use MyUCP\Session\Session;
use MyUCP\Views\ViewCompiler;
use MyUCP\Views\ViewFactory;
use MyUCP\Views\ViewFileFinder;
use MyUCP\Views\Zara\Zara;
use MyUCP\Views\Zara\ZaraFactory;

trait Bootstrap
{
    /**
     * @param string $basePath
     * @param Container|null $container
     * @return Application
     * @throws Exception
     */
    public static function bootstrap($basePath = __DIR__, Container $container = null)
    {
        if(is_null($container)) {
            $container = Container::getInstance();
        }

        $container->make(Container::class, $container);
        $container->singleton(Application::class, [$container])
                    ->alias('app', Application::class);

        return $container->make('app', [$container])
            ->setBasePath($basePath);
    }

    /**
     * Initialization of the main classes for the project
     *
     * @return $this
     * @throws Exception
     */
    public function init()
    {
        if(!file_exists(ENV . DIRECTORY_SEPARATOR . ".env")) {
            if(!copy(ENV . DIRECTORY_SEPARATOR . ".env.example", ENV . DIRECTORY_SEPARATOR . ".env")) {
                throw new Exception("Doest not exists [.env] or [.env.example] files.");
            }
        }

        $this->loadEnvironment();

        $this->make(Config::class, [$this]);

        $this->call(HandleExceptions::class, "make");

        if(env("APP_DB", false)) {
            $this->makeWith(DB::class, [$this->make("config")->db]);
        }

        $this->make(Session::class);
        $this->make(Request::class);
        $this->make(Response::class);
        $this->make(CsrfToken::class);
        $this->make(Load::class);
        $this->makeWith(Translator::class, [
            new LocalizationLoader(config()->locale,
                config()->fallback_locale),
            config()->locale
        ]);
        $this->make(Zara::class);
        $this->make(ViewFactory::class);
        $this->make(Router::class);
        $this->make(UrlGenerator::class);

        $this->make(Extension::class);

        $this->initialized = true;

        return $this;
    }

    /**
     * @param string $path
     * @param string $file
     */
    public function loadEnvironment($path = ENV, $file = '.env')
    {
        try {
            $this->make(Dotenv::class, [$path, $file])->load();
        } catch (InvalidPathException $e) {
            //
        } catch (InvalidFileException $e) {
            echo 'The environment file is invalid: '.$e->getMessage();
            die(1);
        }
    }

    /**
     * Application launch
     */
    public function run()
    {
        $this->make("extension")->boot();
        $this->make("router")->loadRouteService();
        $this->make("router")->loadRoutes($this->appPath('routers.php'));
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
        $aliases = [
            "dotenv" => Dotenv::class,
            "config" => Config::class,
            "handleException" => HandleExceptions::class,
            "db" => DB::class,
            "session" => Session::class,
            "request" => Request::class,
            "response" => Response::class,
            "csrftoken" => CsrfToken::class,
            "load" => Load::class,
            "lang" => Translator::class,
            "view" => ViewFactory::class,
            "router" => Router::class,
            "url" => UrlGenerator::class,
            "extension" => Extension::class,
        ];

        foreach ($aliases as $alias => $abstract) {
            $this->container->alias($alias, $abstract)
                            ->singleton($alias);
        }
    }
}