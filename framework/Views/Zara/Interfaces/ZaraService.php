<?php

namespace MyUCP\Views\Zara\Interfaces;

interface ZaraService
{
    /**
     * @param string $viewName
     */
    public function compile(string $viewName);
}
