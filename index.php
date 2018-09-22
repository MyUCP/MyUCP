<?php
/*
* MyUCP
*/

mb_internal_encoding("UTF-8");

define('ENGINE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'engine' . DIRECTORY_SEPARATOR);
define('APP_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
define('CONFIG_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR);
define('ASSETS_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR);
define('RESOURCES_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR);
define('VIEWS_DIR', RESOURCES_DIR . 'views' . DIRECTORY_SEPARATOR);
define('THEME_DIR', RESOURCES_DIR . 'views' . DIRECTORY_SEPARATOR); /* Will be deprecated in future versions */

require_once(ENGINE_DIR . 'app.php');