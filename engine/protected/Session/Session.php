<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Session
{
    /**
     * @var array
     */
	public $data = [];

    /**
     * @var array|mixed
     */
	private $flash = [];

    /**
     * @var bool
     */
    private $reflash = false;

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return (isset($this->data[$name])) ? $this->data[$name] : null;
    }

    /**
     * Session constructor.
     */
  	public function __construct()
	{
		if(!session_id())
		    session_start();

		$this->data = &$_SESSION;

		$this->flash = &$this->data['_flash'];

        return $this;
	}

    /**
     * @return array
     */
	public function all()
    {
        return $this->data;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
	public function get($key, $default = null)
    {
        if($this->has($key))
            return $this->data[$key];

        return $default;
    }

    /**
     * @param $key
     * @param $value
     * @return Session
     */
    public function set($key, $value)
    {
        return $this->put($key, $value);
    }

    /**
     * @param $key
     * @param null $value
     * @return Session
     */
	public function put($key, $value = null)
    {
        if ($value instanceof Closure) {
            $this->data[$key] = $value();
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $name
     * @param null $value
     * @return mixed
     */
	public function flash($name, $value = null)
    {
        if(!is_null($value)) {
            $this->flash[$name] = $value;
            $this->reflash();
        }

        if(isset($this->flash[$name]))
            return $this->flash[$name];

        return null;
    }

    /**
     * @param null $name
     * @return bool
     */
    public function has($name = null)
    {
        if(isset($this->data[$name]))
            return true;

        return false;
    }

    /**
     * @param null $name
     * @return Session
     */
    public function forget($name = null)
    {
        unset($this->data[$name]);

        return $this;
    }

    /**
     * @return void
     */
    public function reflash()
    {
        $this->reflash = true;
    }

    /**
     * @return void
     */
    public function unsetFlash()
    {
        if($this->reflash == false)
            $this->forget("_flash");
    }
}
