<?php 

class Zara
{

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
     * @param string $filename
     * @param array $vars
     * @param ZaraFactory $factory
     * @param bool $exception
     * @return $this
     */
	public function compile($filename, $vars = [], ZaraFactory $factory, $exception = true)
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
     * @return bool|Debug
     */
	private function searchFile()
    {
		if(file_exists(THEME_DIR . $this->filename . '.zara.php')){
			$this->path = "./assets/cache/".md5(THEME_DIR . $this->filename . ".zara.php");
			$this->compiler->compile(THEME_DIR . $this->filename . '.zara.php', $this->factory);
			$this->compiled = true;

			return true;
		} elseif(file_exists(THEME_DIR . $this->filename . '.php')){
			$this->path = THEME_DIR . $this->filename . '.php';

			return true;
		} else {
			if($this->exception) {
				return new Debug('Ошибка: Не удалось загрузить шаблон ' . $this->filename . '!');
			}
			return false;
		}
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
}