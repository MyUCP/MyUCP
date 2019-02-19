<?php

namespace MyUCP\Extension;

use MyUCP\Application;

abstract class BootExtension extends BaseExtension
{
    public abstract function bootstrap(Application $app);
}