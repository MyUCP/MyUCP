<?php

class HttpException {
	private $registry;

	private $message;
	private $code;

	public function __construct($code = 0, $message = null) {
		global $registry;
		$this->registry = $registry;

		$this->code = $code;
		$this->message = $message;

		if(!$this->loadView()) {
			return new Debug('404. Страница не найдена.');
		}
	}

	public function loadView(){
		return view("errors/".$this->code, ['message' => $this->message], false);
	}
}
?>
