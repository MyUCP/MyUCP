<?php

namespace App\Services;

use MyUCP\Routing\Router;
use MyUCP\Routing\Interfaces\RouteService as ServiceContract;

class RouteService implements ServiceContract
{
    /**
     * RouteService constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        //
    }
}