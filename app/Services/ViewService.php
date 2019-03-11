<?php

namespace App\Services;

use MyUCP\Views\Interfaces\ViewService as ServiceContact;

class ViewService implements ServiceContact
{

    /**
     * ViewService constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $viewName
     * @param array $vars
     * @return mixed
     */
    public function render($viewName, array $vars = [])
    {
        //
    }
}