<?php
/*
* MyUCP
*/

class Debug extends DebugException {

	public $message = "Произошла неизвестная ошибка";
	public $description = ["Ошибка при работе приложения", 
                            "Ошибка при работе с базой данных", 
                            "Внутреняя ошибка фреймворка"];
	public $code = 0;
	public $file;
	public $line;
	protected $error;

	public function __construct() {
        $args = func_get_args();

        (!empty($args[1])) ? $args[1] : "0";
        (!empty($args[2])) ? $args[2] : null;

        $this->error = $args[0];
        if(is_array($this->error)){
            if($this->error[0] == 8) return true;
            $this->message = $this->error[1];
            $this->file = $this->error[2];
            $this->line = $this->error[3];
        } else {
            $this->message = $this->error;
            $this->code = $args[1];
            new DebugException($args[0], $args[1], $args[2]);
        }
        return $this->showError();
    }

    public function showError(){
        global $registry;
        if($registry->config->debug_mode){
    	   require_once("DebugViewTemplate.php");
           die();
        }
    }
} 