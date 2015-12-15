<?php
/*
* MyUCP
*/

class View {
	private $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function load($name, $vars = array()) {
		$file = THEME_DIR . $name . '.php';
		if(is_readable($file)){
			extract($vars);

			ob_start();
			include($file);
	  		$content = ob_get_contents();
	  		ob_end_clean();
			
	  		return $content;
		}
		new Debug('Ошибка: Не удалось загрузить шаблон ' . $name . '!');
	}
}

function view($name, $vars = array()){
	global $registry;
	return $registry->view->load($name, $vars);
}