<?php

namespace MyUCP\Extension;

use MyUCP\Foundation\Application;

abstract class BootExtension extends BaseExtension
{
    abstract public function bootstrap(Application $app);
}
