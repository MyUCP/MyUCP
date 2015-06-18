<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Logs {
	static public $log = array();
	
	function getLogFile(){
		static::$log['file']	=	"./assets/log/". static::$log['date'] .".log";
	}
	
	function getLogs(){
		static::$log['logs']	=	@file_get_contents(static::$log['file']);
	}
	
	function getDateLog(){
		static::$log['date']	=	date("d-m-Y");
	}
	
	function getTimeLog(){
		static::$log['time']	=	date("H:i:s");
	}
	
	function createLogFile($text){
		self::getDateLog();
		self::getLogFile();
		self::getLogs();
		file_put_contents(static::$log['file'], static::$log['logs'].$text);
	}
	
	static function getError($errno, $errstr, $errfile, $errline)
	{	
		self::getTimeLog();
		switch ($errno) {
			case E_USER_ERROR:
				$log .= "(".static::$log['time'].") ERROR: [$errno] $errstr\r\n";
				$log .= "	В строке $errline файла $errfile";
				$log .= " (PHP " . PHP_VERSION . ")\r\n";
				$log .= "-----------------------------------------\r\n";
				$this->createLogFile($log);
				break;

			case E_USER_WARNING:
				$log .= "(".static::$log['time'].") WARNING: [$errno] $errstr\r\n";
				$log .= "	В строке $errline файла $errfile";
				$log .= " (PHP " . PHP_VERSION . ")\r\n";
				$log .= "-----------------------------------------\r\n";
				self::createLogFile($log);
				break;

			case E_USER_NOTICE:
				$log .= "(".static::$log['time'].") NOTICE: [$errno] $errstr\r\n";
				$log .= "	В строке $errline файла $errfile";
				$log .= " (PHP " . PHP_VERSION . ")\r\n";
				$log .= "-----------------------------------------\r\n";
				self::createLogFile($log);
				break;

			default:
				$log .= "(".static::$log['time'].") Неизвестная ошибка: [$errno] $errstr\r\n";
				$log .= "	В строке $errline файла $errfile";
				$log .= " (PHP " . PHP_VERSION . ")\r\n";
				$log .= "-----------------------------------------\r\n";
				self::createLogFile($log);
				break;
		}
		return true;
	}
}
?>
