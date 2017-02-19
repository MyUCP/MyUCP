<?php

class Redirect
{
    private $url;
    private $route = false;

    public function __construct($value)
    {
        if(gettype($value) == "object") {
            $this->url = $value->getRedirectURL($value->rule);
        } else {
            $this->url = $value;
        }

        return $this;
    }

    public function with($name, $value = null)
    {
        flash($name, $value);
    }

    public function __destruct()
    {
        return registry()->response->redirect($this->url);
    }
}