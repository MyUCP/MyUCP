<?php
/**
 * MyUCP
 */

namespace MyUCP\Extension;


interface BootExtensionable extends Extensionable
{
    function bootstrap(\Application $app);
}