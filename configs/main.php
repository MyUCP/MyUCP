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
return [

	// Режим откладки, рекомендуется использовать во время разработки
	'debug_mode'	=>		env("APP_DEBUG", true),

	// URL вашего сайта.
	// Будет использовано при формировании URL адреса
	'url'			=>		env("APP_URL", "http://localhost/"),
	
	// Ключ приложения
	// Будет использоват в шифровке данных
	'app_key'		=>		env("APP_KEY", "empty"),

    // Язык локализации который бдет использоватся на сайте
    'locale'        =>      'ru',

    // Резервный язык локализации
    'fallback_locale'=>     'ru',

	// Данные Базы Данных
	'db'			=>		[
		// Драйвер для работы с БД.
		// По умолчанию MySQL (mysqli).
		'driver'		=>		env("DB_DRIVER", "driver"),

		// Тип СУБД.
		// По умолчанию поддерживается только СУБД MySQL (mysql).
		'type'			=>		env("DB_CONNECTION", "mysql"),

		// Хост БД.
		// Пример: localhost, 127.0.0.1, db.example.com и пр.
		'hostname'		=>		env("DB_HOST", "localhost"),

		// Имя пользователя СУБД.
		'username'		=>		env("DB_USERNAME", "root"),

		// Пароль пользователя СУБД.
		'password'		=>		env("DB_PASSWORD", "secret"),

		// Название БД.
		'database'		=>		env("DB_DATABASE", "myucp"),

		// Испльзуемая кодировка
		'charset'   	=> 		env("DB_CHARSET", "utf8"),
	],

    // Список пользовательских файлов для загрузки
    'load_files'          =>      [
        //
    ],

    'services'  =>  [
        \MyUCP\Routing\Interfaces\RouteService::class => \App\Services\RouteService::class,
        \MyUCP\Views\Interfaces\ViewService::class => \App\Services\ViewService::class,
        \MyUCP\Views\Zara\Interfaces\ZaraService::class => \App\Services\ZaraService::class,
    ],

    // Список расширений
    'extensions'   =>  [
        "Example" => \Extensions\Example\Example::class,
        "Validate" => \Extensions\Validate\Validate::class,


        // Список расширений которые будут инициализированы при запуске приложения
        // Обязательно должны реализовывать класс BootExtensionable
        'boot'  =>  [
            "ExampleBoot" => \Extensions\ExampleBoot\ExampleBoot::class,

            //
            "Auth" => \Extensions\Auth\Auth::class,
        ],
    ],

];