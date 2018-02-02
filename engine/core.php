<?php
/*
* MyUCP
*/

spl_autoload_register(array("AutoLoad", "getLoader"));

$registry = new Registry();
$registry->config = new Config();
$registry->session = new Session();
$registry->db = new DB($registry->config->db);
$registry->request = new Request();
$registry->load = new Load($registry);
$registry->lang = new Translator(new LocalizationLoader(config()->locale, config()->fallback_locale), config()->locale);
$registry->view = new View($registry);
$registry->router = new Router;
$registry->response = new Response();
$registry->router->make();
$registry->response->prepare($registry->request);
$registry->response->send();
$registry->session->unsetFlash();