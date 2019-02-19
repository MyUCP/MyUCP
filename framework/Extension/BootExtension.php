<?php

namespace MyUCP\Extension;

use MyUCP\Foundation\Application;

abstract class BootExtension extends BaseExtension
{
    public abstract function bootstrap(Application $app);
}