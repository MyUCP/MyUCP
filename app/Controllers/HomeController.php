<?php

namespace App\Controllers;

use MyUCP\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @return mixed
     */
    public function welcome()
    {
        return view('welcome');
    }
}
