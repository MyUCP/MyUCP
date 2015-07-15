<?php
/*
* MyUCP
* File Version 4.0.0.1
* Date: 15.07.2015
* Developed by Maksa988
*/

final class pdoDriver {
	private $pdo;
	private $count = 0;
	public function __construct($hostname, $username, $password, $database, $type) {
		if (!$this->pdo = new PDO("mysql:host={$hostname};dbname={$database};charset=utf8", $username, $password)) {
	  		exit('Ошибка: Не удалось соединиться с базой данных!');
		}
  	}
		
  	public function query($sql) {
		$resource = $this->pdo->query($sql);
		
		$this->count++;
		
		if ($resource) {
			if(preg_match("/SELECT/i", $sql)){	
				$i = 0;
				$data = array();
				
				while($result = @$resource->fetch(PDO::FETCH_ASSOC)) {
					$data[$i] = $result;
					$i++;
				}
				
				$query = new stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;
				
				unset($data);
				return $query;	
			} else {
				return true;
			}
		} else {
			exit('Ошибка: ' . $this->pdo->errorInfo[2] . '<br>Номер ошибки: ' . $this->pdo->errorCode() . '<br>' . $sql);
		}
  	}
	
	public function escape($value) {
		return $this->pdo->quote($value);
	}
	
  	public function countAffected() {
		return $this->pdo->exec;
  	}

  	public function getLastId() {
		return $this->pdo->lastInsertId();
  	}	
	
  	public function getCount() {
		return $this->count;
  	}
	
	public function __destruct() {
		unset($this->pdo);
	}
}
?>
