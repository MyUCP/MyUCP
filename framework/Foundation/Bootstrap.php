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
use MyUCP\Views\View;
use MyUCP\Views\ViewCompiler;
use MyUCP\Views\ViewFactory;
use MyUCP\Views\ViewFileFinder;
use MyUCP\Views\Zara\Zara;
use MyUCP\Views\Zara\ZaraFactory;

trait Bootstrap
{
    /**
     * @param string $basePath
     * @param Registry|null $registry
     * @return Application
     * @throws Exception
     */
    public static function bootstrap($basePath = __DIR__, Registry $registry = null)
    {
        if(is_null($registry)) {
            $registry = new Registry();
        }

        return (new Application($registry))
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
        $this->makeWith(Translator::class, [
            new LocalizationLoader(config()->locale,
                config()->fallback_locale),
            config()->locale
        ]);

        $this->makeWith(Zara::class, [
            $this->make(ZaraFactory::class)
        ]);

        $this->makeWith(ViewFactory::class, [
            $this->make(ViewFileFinder::class),
            $this->make(ViewCompiler::class)
        ]);

        $this->make(Router::class);
        $this->makeWith(UrlGenerator::class, [$this["routes"], $this["request"]]);

        $this->makeWith(Extension::class, [$this]);

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
            (new Dotenv($path, $file))->load();
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
        $this->alias = [
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
    }
}