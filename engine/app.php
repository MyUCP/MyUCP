<?php

/*
 * Основной файл для подключения основных файлов для работы
 */
require_once(ENGINE_DIR . '/protected/autoload.php');

/*
 * Преопределяем функцию для перехвата ошибок
 */
require_once(ENGINE_DIR . './protected/Debug/DebugErrorHandler.php');
set_error_handler("getError");

/* 
 * Загрузка хелперов
 */
require_once(ENGINE_DIR . './protected/Helpers/Helpers.php');

require_once(ENGINE_DIR . 'core.php');