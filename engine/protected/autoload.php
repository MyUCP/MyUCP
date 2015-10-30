<?php
/*
* MyUCP
*/

class AutoLoad {

	public static function getLoader($className){
		require_once("./engine/protected/AutoLoader/AutoLoader.php");
		return new AutoLoader($className);
	}

} 