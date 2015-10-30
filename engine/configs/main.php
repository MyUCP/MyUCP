<?php
/*
|--------------------------------------------------------------------------
| Главный файл конфигурации
|--------------------------------------------------------------------------
|
| Указаны основые данны для работы с системой
| Есть возможность подключить сторонние конфигурационные файлы
|
*/
$config = [

	// Режим откладки, рекомендуеться использовать во время разработки
	'debug_mode'	=>		true,

	// URL вашего сайта.
	// Будет использовано при формировании URL адреса
	'url'			=>		'http://example.com/',
	
	// Токен.
	// Используется для запуска скриптов из Cron`а.
	'token'			=>		'mytoken123',
	
	// Данные Базы Данных
	'db'			=>		[
		
		// Драйвер для работы с БД.
		// По умолчанию MySQL (mysqli).
		'driver'		=>		'mysql',

		// Тип СУБД.
		// По умолчанию поддерживается только СУБД MySQL (mysql).
		'type'			=>		'mysql',
		
		// Хост БД.
		// Пример: localhost, 127.0.0.1, db.example.com и пр.
		'hostname'		=>		'localhost',
		
		// Имя пользователя СУБД.
		'username'		=>		'root',
		
		// Пароль пользователя СУБД.
		'password'		=>		'',
		
		// Название БД.
		'database'		=>		'myucp',

		// Испльзуемая кодировка
		'charset'   	=> 		'utf8',
		
	],
	
	
	// Настройки почты
	'mail' 		=> 		[
		
		// E-Mail отправителя.
		// Пример: support@example.com, noreply@example.com
		'mail_from'		=>		'support@example.com',
		
		// Имя отправителя.
		// Пример: Ivan Petrov
		'mail_sender'		=>		'Ivan Petrov',
	],
	
	// Подключение доп. конфиг. файлов
	'configs' 		=> 		[
		
		//Список доп. конфиг. файлов для подключения
		'rows'
	],

	// Адреса директорий для работы autoload
	'path' 	=>	[
		'Logs'	=>	'engine/protected/',
		'Registry'	=>	'engine/protected/',
		'Config'	=>	'engine/protected/',
		'Request'	=>	'engine/protected/',
		'Session'	=>	'engine/protected/',
		'Response'	=>	'engine/protected/',
		'Document'	=>	'engine/protected/',
		'DB'		=>	'engine/protected/',
		'mysqlDriver'=>	'engine/protected/database',
		'Load'		=>	'engine/protected/',
		'Router'	=>	'engine/protected/',
		'Route'		=>	'engine/protected/Router',
	]
];