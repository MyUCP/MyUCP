<?php
/*
* MyUCP
*/


class Dumper {

    /**
     * [__construct description]
     * @param [type] $value [description]
     * @param [type] $die   [description]
     * @param string $func  [description]
     */
	public function __construct($value, $die, $func = "dd") {

        if($func == "dd"){

            $this->dd($value, $die);

        } elseif($func == "ci"){

            $this->ci($value, $die);

        }
    }

    /**
     * [dd description]
     * @param  [type] $value [description]
     * @param  [type] $die   [description]
     * @return [type]        [description]
     */
    private function dd($value, $die) {

        $this->output($value);

        if($die)
            die();
    }

    /**
     * [ci description]
     * @param  [type] $value [description]
     * @param  [type] $die   [description]
     * @return [type]        [description]
     */
    private function ci($value, $die) {

        $className = get_class($value);
        $methods = get_class_methods($className);

        echo "Class: ";
        $this->dd($className, false);

        echo "<br>Methods: ";
        $this->dd($methods, true);
    }

    /**
     * [output description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    private function output($value) {

        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }
} 