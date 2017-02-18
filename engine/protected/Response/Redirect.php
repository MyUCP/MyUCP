<?php

class Redirect
{
    public function __construct($value)
    {
        if(gettype($value) == "object") {
            return $value->redirect();
        }

        return registry()->response->redirect($value);
    }
}