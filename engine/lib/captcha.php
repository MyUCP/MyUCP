<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class captchaLibrary {
	public $width = 80;
	public $height = 20;
	public $font = 'tahoma.ttf';
	private $code;
	
	function __construct() { 
		$this->code = substr(md5(mt_rand()), 10, 6); 
	}

	function getCode(){
		return $this->code;
	}

	function showImage() {
		$font = ENGINE_DIR . 'fonts/' . $this->font;
		$img = imagecreatetruecolor($this->width, $this->height);
		
		$width = imagesx($img);
		$height = imagesy($img);
		
		$white = imagecolorallocate($img, 255, 255, 255);
		$grey = imagecolorallocate($img, 128, 128, 128);
		
		$white = imagecolorallocate($img, 255, 255, 255);
		$grey = imagecolorallocate($img, 128, 128, 128);
		imagefill($img, 0, 0, $white);
		
		for($i = 0; $i < 3; $i++) {
			imageline($img, rand(1, $width/2), rand(1, $height), rand($width/2, $width), rand(1, $height), $grey);
		}
		
		imagettftext($img, 16, 0, 10, 18, $grey, $font, $this->code);
		
		header('Content-type: image/jpeg');
		
		imagejpeg($img);
		
		imagedestroy($img);		
	}
}
?>
