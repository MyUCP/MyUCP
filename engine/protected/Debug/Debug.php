<?php
/*
* MyUCP
*/

class Debug extends DebugException {

	public $message = "Произошла неизвестная ошибка";
	public $description = ["Ошибка при работе приложения", "Ошибка при работе с базой данных", "Внутреняя ошибка фреймворка"];
	public $code = 0;
	public $file;
	public $line;
	protected $error;

	public function __construct($message, $code = 0, Exception $previous = null) {

        $this->message = $message;
        $this->code = $code;
        new DebugException($message, $code, $previous);
        return $this->showError();
    }

    public function showError(){
        global $registry;
        if($registry->config->debug_mode){
    	   require_once("DebugViewTemplate.php");
        }
    	die();
    }
} 

function dd($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}