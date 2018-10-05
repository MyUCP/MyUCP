<?php
/**
 * MyUCP
 */

namespace MyUCP\Extension;


interface Extensionable
{
    function run(\Application $app, ...$args);
}