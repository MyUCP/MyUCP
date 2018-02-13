<?php
/*
* MyUCP
*/

class Registry
{
    /**
     * @var array
     */
	private $data = [];

    /**
     * @param $key
     * @param $val
     */
	public function __set($key, $val)
    {
		$this->data[$key] = $val;
	}

    /**
     * @param $key
     * @return bool|mixed
     */
	public function __get($key)
    {
		if(isset($this->data[$key])){
			return $this->data[$key];
		}

		return false;
	}
}