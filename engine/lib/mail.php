<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class mailLibrary {
	protected $to;
	protected $from;
	protected $sender;
	protected $subject;
	protected $text;
	
	public function setTo($to) {
		$this->to = $to;
	}
	
	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function setSender($sender) {
		$this->sender = $sender;
	}
	
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	public function setText($text) {
		$this->text = $text;
	}
	
	public function send() {
		if (!$this->to) {
			exit("Error: E-Mail to required!");
		}
		
		if (!$this->from) {
			exit("Error: E-Mail from required!");
		}
		
		if (!$this->sender) {
			exit("Error: E-Mail sender required!");
		}
		
		if (!$this->subject) {
			exit("Error: E-Mail subject required!");
		}
		
		if (!$this->text) {
			exit("Error: E-Mail message required!");
		}
		
		if (is_array($this->to)) {
			$this->to = implode(',', $this->to);
		}
		
		$header = "";
		
		$header .= "MIME-Version: 1.0\n";
		
		$header .= "From: " . $this->sender . "<" . $this->from . ">\n";
		$header .= "Reply-To: " . $this->sender . "\n";
		$header .= "X-Mailer: PHP Mailer\n";
		$header .= "Return-Path: " . $this->sender . "\n";
		$header .= "Content-Type: text/plain; charset=\"utf-8\"\n";
		/*
			Äëÿ òåñòèğîâàíèÿ áåç ñåğâåğîíîé ÷àñòè çàêîììåíòèğîâàòü
		*/
		return mail($this->to, $this->subject, $this->text, $header);
	}
}
?>
