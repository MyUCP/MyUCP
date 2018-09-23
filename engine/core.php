<?php
/*
* MyUCP
*/

spl_autoload_register(array("AutoLoad", "getLoader"));

$registry = new Registry();

$app = new Application($registry);

$app->init()->run();