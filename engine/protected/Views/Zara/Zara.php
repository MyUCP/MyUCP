<?php 

class Zara {

	protected $filename;
	protected $path;
	protected $vars;
	private $compiler;
	private $compiled = false;

	public function compile($filename, $vars = []){
		$this->vars = $vars;
		$this->filename = $filename;
		$this->compiler = new ZaraCompiler;
		$this->searchFile();

		return $this;
	}

	public function getCompiled(){
		extract($this->vars);

		ob_start();
		include($this->path);
  		$contents = ob_get_contents();
  		ob_end_clean();
		
  		return $contents;
	}

	private function searchFile(){
		if(file_exists(THEME_DIR . $this->filename . '.zara.php')){
			$this->path = "./assets/cache/".md5(THEME_DIR . $this->filename . ".zara.php");
			$this->compiler->compile(THEME_DIR . $this->filename . '.zara.php');
			$this->compiled = true;
		} elseif(file_exists(THEME_DIR . $this->filename . '.php')){
			$this->path = THEME_DIR . $this->filename . '.php';
		} else {
			new Debug('Ошибка: Не удалось загрузить шаблон ' . $name . '!');
		}
	}
}

function e($val){
	return $val;
}