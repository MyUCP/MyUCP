<?php 

class Zara {

	protected $filename;
	protected $path;
	protected $vars;
	private $compiler;
	private $factory;
	private $compiled = false;
	private $exception = true;

	public function compile($filename, $vars = [], ZaraFactory $factory, $exception){
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

	public function getCompiled(){
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

	private function searchFile(){
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
}