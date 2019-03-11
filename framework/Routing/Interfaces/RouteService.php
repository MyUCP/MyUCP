<?php

namespace MyUCP\Routing\Interfaces;

use MyUCP\Routing\Router;

interface RouteService
{
    /**
     * RouteService constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router);
}