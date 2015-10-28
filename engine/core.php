<?php
/*
* MyUCP
*/

set_error_handler(array('Logs', "getError"));

$registry = new Registry();
$registry->config = new Config();
$registry->request = new Request();
$registry->session = new Session();
$registry->response = new Response();
$registry->document = new Document();
$registry->db = new DB(
	$registry->config->db['db_driver'], 
	$registry->config->db['db_hostname'], 
	$registry->config->db['db_username'], 
	$registry->config->db['db_password'], 
	$registry->config->db['db_database'], 
	$registry->config->db['db_type']
);
$registry->load = new Load($registry);
$registry->router = new Router($registry);
$registry->response->output($registry->router->make());