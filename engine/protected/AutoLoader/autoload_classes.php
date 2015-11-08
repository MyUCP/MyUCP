<?php
/*
|--------------------------------------------------------------------------
| Пути к классам
|--------------------------------------------------------------------------
|
| Список классов в виде ключей массива и путей к их файлам в виде значений
|
*/

return [
	'Logs'	=>	'engine/protected/logs.php',
	'Registry'	=>	'engine/protected/registry.php',
	'Config'	=>	'engine/protected/config.php',
	'Request'	=>	'engine/protected/request.php',
	'Session'	=>	'engine/protected/session.php',
	'Response'	=>	'engine/protected/response.php',
	'Document'	=>	'engine/protected/document.php',
	'DB'		=>	'engine/protected/db.php',
	'mysqlDriver'=>	'engine/protected/database/mysql.php',
	'pdoDriver'=>	'engine/protected/database/pdo.php',
	'Load'		=>	'engine/protected/load.php',
	'Router'	=>	'engine/protected/router.php',
	'Route'		=>	'engine/protected/Router/Route.php',
	'Controller'=> 	'engine/protected/controller.php',
	'Model'		=>	'engine/protected/model.php'
];