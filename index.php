<?php
/*
* MyUCP
*/

mb_internal_encoding('UTF-8');

define('ENV', __DIR__);

/**
 * Loading autoloader.
 */
require_once __DIR__.'/vendor/autoload.php';

/**
 * Boot the application.
 */
$app = \MyUCP\Foundation\Application::bootstrap(__DIR__);

/*
 * Run application
 */
$app->init()->run();
