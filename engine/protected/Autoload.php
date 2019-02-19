<?php

namespace MyUCP;

use MyUCP\AutoLoader\AutoLoader;

class AutoLoad
{
    /**
     * @param $className
     * @return AutoLoader
     * @throws Debug\DebugException
     */
	public static function getLoader($className) {

		require_once(ENGINE_DIR . "protected/AutoLoader/AutoLoader.php");

		return new AutoLoader($className);
	}
} 