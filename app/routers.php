<?php
/*
|--------------------------------------------------------------------------
| Маршрутизация
|--------------------------------------------------------------------------
|
| Укажите пути маршрутизации и какие контроллеры будут выполняться вместе
| с их параметрами и так же действиями которые они должны выполнять
|
*/

Router::any("test-{id:[0-9]+}-{name:[a-z]+}", "HomeController@welcome");

Router::any("/", function() {
	return view("welcome");
});