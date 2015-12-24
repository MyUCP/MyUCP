<?php

/*
 * Основной файл для подключения основных файлов для работы
 */

require_once(ENGINE_DIR . '/protected/autoload.php');

/*
 * Преопределяем функция для перехвата ошибок
 */
require_once(ENGINE_DIR . './protected/Debug/DebugErrorHandler.php');
set_error_handler("getError");

require_once(ENGINE_DIR . 'core.php');

/*
 * Временная функция
 */

function dd($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}