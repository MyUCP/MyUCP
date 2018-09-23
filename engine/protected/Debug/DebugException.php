<?php
/*
* MyUCP
*/

class DebugException extends Exception
{
    /**
     * @param $message
     * @param int $code
     * @param Exception|null $previous
     */
	public function error($message, $code = 0, Exception $previous = null) {
        
        parent::__construct($message, $code, $previous);
    }
} 