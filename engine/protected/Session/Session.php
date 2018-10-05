<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Session
{
	public $data = [];
    private $reflash = false;

  	public function __construct()
	{
		if(!session_id())
		    session_start();

		$this->data = &$_SESSION;

        return $this;
	}

	public function all()
    {
        return $this->data;
    }

	public function get($key, $value = null)
    {
        $this->key = $key;

        if($value != null) {
            if ($value instanceof Closure) {
                $this->put($key, $value());
            } else {
                $this->put($key, $value);
            }
        }

        return $this->data[$key];
    }

	public function put($key, $value = null)
    {
        $this->data[$key] = $value;

        return $this;
    }

	public function flash($name, $value = null)
    {
        if($value != null)
            $this->data['flash'][$name] = $value;

        return $this->data['flash'][$name];
    }

    public function has($name = null)
    {

        if(isset($this->data[$name]))
            return true;

        return false;
    }

    public function forget($name = null)
    {
        unset($this->data[$name]);

        return $this;
    }

    public function reflash()
    {
        $this->reflash = true;
    }

    public function unsetFlash()
    {
        if($this->reflash == false)
            $this->forget("flash");
    }
}
