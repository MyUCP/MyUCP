<?php

class Redirect
{
    private $url;

    public function __construct($value, $params = [])
    {
        if(gettype($value) == "object") {
            $this->url = $value->getRedirectURL($params);
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