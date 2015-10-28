<?php
/*
* MyUCP
*/

function __autoload($сlassName) {
    $filename = strtolower($сlassName) . '.php';
	$file = ENGINE_DIR . 'protected/' . $filename;

	if(!file_exists($file)) {
		return false;
	}

	require_once($file);
}