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

    // Extensions
    \Extensions\ExampleBoot\ExampleBoot::class => EXTENSIONS_DIR . 'ExampleBoot/ExampleBoot.php',
    \Extensions\Example\Example::class => EXTENSIONS_DIR . 'Example/Example.php',

    //
    \Extensions\Validate\Validate::class => EXTENSIONS_DIR . 'Validate/Validate.php',
    \Extensions\Auth\Auth::class => EXTENSIONS_DIR . 'Auth/Auth.php',
];