<?php
/*
* MyUCP
* File Version 4.0.0.1
* Date: 15.07.2015
* Developed by Maksa988
*/

set_error_handler(array('Logs', "getError"));

function __autoload($сlassName) {
    $filename = strtolower($сlassName) . '.php';
	$file = ENGINE_DIR . 'protected/' . $filename;

	if(!file_exists($file)) {
		return false;
	}

	require_once($file);
}

$registry = new Registry();

$config = new Config();
$registry->config = $config;

$request = new Request();
$registry->request = $request;

$session = new Session();
$registry->session = $session;

$response = new Response();
$registry->response = $response;

$document = new Document();
$registry->document = $document;

$db = new DB($config->db['db_driver'], $config->db['db_hostname'], $config->db['db_username'], $config->db['db_password'], $config->db['db_database'], $db['db_type']);
$registry->db = $db;

$load = new Load($registry);
$registry->load = $load;

$action = new Action($registry);
$registry->action = $action;

if(isset($request->get['action'])) {
	$action->make($request->get['action']);
} else {
	$action->make($config->index_page);
}

$response->output($action->go());