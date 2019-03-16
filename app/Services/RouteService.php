<?php

namespace App\Services;

use MyUCP\Routing\Interfaces\RouteService as ServiceContract;
use MyUCP\Routing\Router;

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
