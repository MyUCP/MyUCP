<?php
/*
* MyUCP
*/

function getError($errno, $errstr, $errfile, $errline){
    new Debug([$errno, $errstr, $errfile, $errline]);
    return true;
}