<?php
/**
 * MyUCP
 */

namespace MyUCP\Extension;


abstract class BootExtension extends BaseExtension
{
    public abstract function bootstrap(\Application $app);
}