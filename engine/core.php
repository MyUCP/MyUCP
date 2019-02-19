<?php
/*
* MyUCP
*/

use MyUCP\AutoLoad;
use MyUCP\Registry;
use MyUCP\Application;

spl_autoload_register(array(AutoLoad::class, "getLoader"));

$registry = new Registry();

$app = new Application($registry);

$app->init()->run();