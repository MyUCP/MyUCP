<?php

namespace MyUCP\Extension;

use MyUCP\Foundation\Application;

abstract class BaseExtension extends ExtensionHelpers
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->setApplication($app);
    }

    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    abstract public function run();
}
