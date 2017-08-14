<?php
/*
* MyUCP
*/

class DebugException extends Exception {

	public function error($message, $code = 0, Exception $previous = null) {
        
        parent::__construct($message, $code, $previous);
    }
} 