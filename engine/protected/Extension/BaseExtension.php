<?php
/**
 * MyUCP
 */

namespace MyUCP\Extension;

abstract class BaseExtension extends ExtensionHelpers
{
    protected $app;

    public function __construct(\Application $app)
    {
        $this->setApplication($app);
    }

    public function setApplication(\Application $app)
    {
        $this->app = $app;
    }

    public abstract function run();
}