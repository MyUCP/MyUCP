<?php
/*
* MyUCP
*/

spl_autoload_register(array("AutoLoad", "getLoader"));

$registry = new Registry();
$registry->config = new Config();
$registry->request = new Request();
$registry->session = new Session();
$registry->response = new Response();
$registry->document = new Document();
$registry->db = new DB($registry->config->db);
$registry->load = new Load($registry);
$registry->router = new Router($registry);
$registry->view = new View($registry);
$registry->response->output($registry->router->make());