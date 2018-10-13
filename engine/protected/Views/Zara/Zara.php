<?php 
/*
  * MyUCP
  */

class Zara
{
    /**
     * @var string
     */
	protected $filename;

    /**
     * @var string
     */
	protected $path;

    /**
     * @var array
     */
	protected $vars;

    /**
     * @var ZaraCompiler
     */
	private $compiler;

    /**
     * @var ZaraFactory
     */
	private $factory;

    /**
     * @var bool
     */
	private $compiled = false;

    /**
     * @var bool
     */
	private $exception = true;

    /**
     * @var array
     */
	private $preLoadPaths = [];

    /**
     * @param string $filename
     * @param array $vars
     * @param ZaraFactory $factory
     * @param bool $exception
     * @return $this
     * @throws DebugException
     */
	public function compile($filename, $vars = [], ZaraFactory $factory = null, $exception = true)
    {
		$this->vars = $vars;
		$this->vars["zara"] = $this;
		$this->filename = $filename;
		$this->compiler = new ZaraCompiler;
		$this->factory = $factory;
		$this->exception = $exception;

		if($this->searchFile())
			return $this;

		return $this;
	}

    /**
     * @return bool|string
     */
	public function getCompiled()
    {
		if($this->exception) {
			extract($this->vars);

			ob_start();
			include($this->path);
	  		$contents = ob_get_contents();
	  		ob_end_clean();
			
	  		return $contents;
	  	}
	  	return false;
	}

    /**
     * @return bool
     * @throws DebugException
     */
	private function searchFile()
    {
		if(file_exists(VIEWS_DIR . $this->filename . '.zara.php')) {
			$this->path = ASSETS_DIR . "cache" . DIRECTORY_SEPARATOR . md5(VIEWS_DIR . $this->filename . ".zara.php");
			$this->compiler->compile(VIEWS_DIR . $this->filename . '.zara.php', $this->factory);
			$this->compiled = true;

			return true;
		} elseif(file_exists(VIEWS_DIR . $this->filename . '.php')) {
            $this->path = VIEWS_DIR . $this->filename . '.php';

            return true;
        } elseif(key_exists($this->filename, $this->preLoadPaths)) {
            if(file_exists($this->preLoadPaths[$this->filename])) {
                if(mb_stripos($this->preLoadPaths[$this->filename], ".zara.php") === false) {
                    $this->path = $this->preLoadPaths[$this->filename];

                    return true;
                } else {
                    $this->path = ASSETS_DIR . "cache" . DIRECTORY_SEPARATOR . md5($this->preLoadPaths[$this->filename]);
                    $this->compiler->compile($this->preLoadPaths[$this->filename], $this->factory);
                    $this->compiled = true;

                    return true;
                }
            }
		}

        if($this->exception) {
            throw new DebugException('Ошибка: Не удалось загрузить шаблон [' . $this->filename . ']');
        }

        return false;
	}

    /**
     * @return ZaraCompiler
     */
	public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * Register a handler for custom directives.
     *
     * @param  string  $name
     * @param  callable  $handler
     * @return void
     */
    public static function directive($name, callable $handler)
    {
        app("view")->getZara()->getCompiler()->directive($name, $handler);
    }

    /**
     * Register an "if" statement directive.
     *
     * @param  string  $name
     * @param  callable  $callback
     * @return void
     */
    public static function if($name, callable $callback)
    {
        app("view")->getZara()->getCompiler()->if($name, $callback);
    }

    /**
     * Check the result of a condition.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return bool
     */
    public static function check($name, ...$parameters)
    {
        return app("view")->getZara()->getCompiler()->check($name, $parameters);
    }

    /**
     * @param string $name
     * @param string $path
     */
    public function addPreLoadPath(string $name, string $path)
    {
        $this->preLoadPaths[$name] = $path;
    }

    /**
     * @param string $name
     * @param string $path
     * @return mixed
     */
    public static function preLoad(string $name, string $path)
    {
        return app("view")->getZara()->addPreLoadPath($name, $path);
    }
}