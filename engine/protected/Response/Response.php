<?php
/*
* MyUCP
*/

class Response {
	
	private $headers = [];
	
	public function addHeader($header) {
		$this->headers[] = $header;
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
