<?php

class HttpException {

	private $message;
	private $code;

	public function __construct($code = 0, $message = null) {

		$this->code = $code;
		$this->message = $message;

		if(!$this->loadView()) {
			return new Debug($code . '. '. $message .'.');
		}
	}

	public function loadView(){
		return view("errors/".$this->code, ['message' => $this->message], false);
	}
}
?>
