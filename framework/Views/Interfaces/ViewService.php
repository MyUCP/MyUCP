<?php

namespace MyUCP\Views\Interfaces;

interface ViewService
{
    /**
     * ViewService constructor.
     */
    public function __construct();

    /**
     * @param $viewName
     * @param array $vars
     * @return mixed
     */
    public function render($viewName, array $vars = []);
}