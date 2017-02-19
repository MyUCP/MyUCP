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
$registry->db = new DB($registry->config->db);
$registry->load = new Load($registry);
$registry->lang = new Translator(new LocalizationLoader(config()->locale, config()->fallback_locale), config()->locale);
$registry->view = new View($registry);
$registry->router = new Router;
$registry->response->output($registry->router->make());
$registry->session->unsetFlash();