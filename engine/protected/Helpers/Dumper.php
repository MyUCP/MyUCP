<?php
/*
* MyUCP
*/

class Dumper
{
    /**
     * Dumper constructor.
     * @param $value
     * @param $die
     * @param string $func
     */
	public function __construct($value, $die, $func = "dd")
    {
        if($func == "dd"){

            $this->dd($value, $die);

        } elseif($func == "ci") {

            $this->ci($value, $die);

        }
    }

    /**
     * @param $value
     * @param $die
     */
    private function dd($value, $die)
    {

        $this->output($value);

        if($die)
            die();
    }

    /**
     * @param $value
     * @param $die
     */
    private function ci($value, $die)
    {
        $className = get_class($value);
        $methods = get_class_methods($className);

        echo "Class: ";
        $this->dd($className, false);

        echo "<br>Methods: ";
        $this->dd($methods, true);
    }

    /**
     * @param $value
     */
    private function output($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }
} 