<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Response {
	private $headers = array();
	
	public function addHeader($header) {
		$this->headersarray[] = $header;
	}

	public function redirect($url) {
		header('Location: ' . $url);
		exit;
	}
	
	public function output($content) {
		if ($content) {
			if (!headers_sent()) {
				foreach($this->headers as $header) {
					header($header, true);
				}
			}
			echo $content;
		}
	}
}
?>
