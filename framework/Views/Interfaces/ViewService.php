<?php

namespace MyUCP\Views\Interfaces;

interface ViewService
{
    /**
     * ViewService constructor.
     */
    public function __construct();

    /**
     * @param $view
     * @param array $data
     *
     * @return mixed
     */
    public function render($view, array $data = []);
}