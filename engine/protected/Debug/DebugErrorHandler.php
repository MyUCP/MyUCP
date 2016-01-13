<?php
/*
* MyUCP
*/

function getError($errno, $errstr, $errfile, $errline){
    new Debug([$errno, $errstr, $errfile, $errline]);
    return true;
}

function dd($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}

function d($value = null) {
   dd($value);
   die('__END__');
}
 
function ci($value) {
    $className = get_class($value);
    $methods = get_class_methods($className);
    dd($className);
    d($methods);
}