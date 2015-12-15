<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/
//ini_set('display_errors', 0);
//error_reporting(E_ALL);
mb_internal_encoding("UTF-8");

define('ENGINE_DIR', dirname(__FILE__) . '/engine/');
define('APP_DIR', dirname(__FILE__) . '/app/');
define('THEME_DIR', dirname(__FILE__) . '/theme/');

require_once(ENGINE_DIR . 'app.php');