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
     * @param $view
     * @param array $data
     * @return mixed|void
     */
    public function render($view, array $data = [])
    {
        //
    }
}