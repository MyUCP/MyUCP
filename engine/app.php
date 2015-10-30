<?php

/*
 * Загрузка autoload класса для подгрузки других классов
 */
require_once(ENGINE_DIR . '/protected/autoload.php');

function dd($value){
	echo "<pre>";
	var_dump($value);
	echo "</pre>";
	// die();
}