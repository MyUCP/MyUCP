<?php
/*
* MyUCP
*/

class View {
    /**
     * @var Zara
     */
	private $Zara;

    /**
     * View constructor.
     */
	public function __construct()
    {
		$this->Zara = new Zara;
	}

    /**
     * @return Zara
     */
	public function getZara()
    {
        return $this->Zara;
    }

    /**
     * @param string $name
     * @param array $vars
     * @param $exception
     * @return bool|string
     */
	public function load($name, $vars = array(), $exception)
    {
		return $this->Zara->compile($name, $vars, new ZaraFactory, $exception)->getCompiled();
	}
}