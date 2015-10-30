<?php
/*
* MyUCP
*/

class AutoLoad {

	private $path;

	public static function getLoader($className){
		require_once("./engine/protected/AutoLoader/AutoLoader.php");
		return new AutoLoader($className);
	}

} 

// function __autoload($сlassName) {
//     $filename = strtolower($className) . '.php';
// 	$file = ENGINE_DIR . 'protected/' . $filename;

// 	if(!file_exists($file)) {
// 		return false;
// 	}

// 	require_once($file);
// }