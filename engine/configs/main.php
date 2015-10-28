<?php
/*
* MyUCP
* File Version 4.1
* Date: 27.10.2015
* Developed by Maksa988
*/

$config = array(

	// Название сайта
	// Например: My First Website
	'title'		=>		'My First Website',
	
	// Описание сайта (meta description).
	// Пример: My personal website
	'description'	=>		'My personal website',
	
	// Ключевые слова (meta keywords).
	// Пример: website, first
	'keywords'		=>		'website, first',
	
	// URL панели управления.
	// Обратите внимание на то, что сайт должен располагаться в корне (под)домена.
	// http://example.com/, http://ucp.example.com/ - правильно.
	// http://example.com/ucp/ - неправильно.
	'url'			=>		'http://example.com/',
	
	// Токен.
	// Используется для запуска скриптов из Cron`а.
	'token'			=>		'mytoken123',
	
	// Данные Базы Данных
	'db'		=>		array(
		
		// Драйвер для работы с БД.
		// По умолчанию MySQL (mysqli).
		'db_driver'		=>		'mysql',

		// Тип СУБД.
		// По умолчанию поддерживается только СУБД MySQL (mysql).
		'db_type'		=>		'mysql',
		
		// Хост БД.
		// Пример: localhost, 127.0.0.1, db.example.com и пр.
		'db_hostname'	=>		'localhost',
		
		// Имя пользователя СУБД.
		'db_username'	=>		'root',
		
		// Пароль пользователя СУБД.
		'db_password'	=>		'',
		
		// Название БД.
		'db_database'	=>		'myucp',
		
	),
	
	
	// Настройки почты
	'mail' 		=> 		array(
		
		// E-Mail отправителя.
		// Пример: support@example.com, noreply@example.com
		'mail_from'		=>		'support@example.com',
		
		// Имя отправителя.
		// Пример: Ivan Petrov
		'mail_sender'		=>		'Ivan Petrov',
	),
	
	// Настройки UnitPay
	'unitpay' 		=> 		array(
		
		// Публичный ключ
		'public_key'		=>		'',
		
		// Секретный ключ
		'secret_key'		=>		''
	),
	
	//Подключение доп. конфиг. файлов
	'configs' => array(
		
		//Список доп. конфиг. файлов для подключения
		'rows'
	)
);