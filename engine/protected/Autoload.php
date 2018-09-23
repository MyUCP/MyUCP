<?php
/*
* MyUCP
*/

class AutoLoad
{
    /**
     * @param $className
     * @return AutoLoader
     * @throws DebugException
     */
	public static function getLoader($className){
		require_once(ENGINE_DIR . "protected/AutoLoader/AutoLoader.php");
		return new AutoLoader($className);
	}
} 