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
	'mysqlDriver'=>	'engine/protected/Database/mysql.php',
	'pdoDriver'=>	'engine/protected/Database/pdo.php',
	'Load'		=>	'engine/protected/load.php',
	'Router'	=>	'engine/protected/router.php',
	'Route'		=>	'engine/protected/Router/Route.php',
	'Controller'=> 	'engine/protected/controller.php',
	'Model'		=>	'engine/protected/Model/model.php',
	'View'		=>	'engine/protected/Views/View.php',
	'Zara'		=>	'engine/protected/Views/Zara/Zara.php',
	'ZaraCompiler'=>	'engine/protected/Views/Zara/ZaraCompiler.php',
	'Debug'		=>	'engine/protected/Debug/Debug.php',
	'DebugException'=>	'engine/protected/Debug/DebugException.php',
];