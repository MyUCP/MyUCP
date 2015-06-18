<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

final class mysqlDriver {
	private $mysql;
	private $count = 0;
	public function __construct($hostname, $username, $password, $database) {
		if (!$this->mysql = new mysqli($hostname, $username, $password, $database)) {
	  		exit('Ошибка: Не удалось соединиться с базой данных!');
		}
		
		$this->mysql->set_charset("utf8"); 
  	}
		
  	public function query($sql) {
		$resource = $this->mysql->query($sql);
		
		$this->count++;
		
		if ($resource) {
			if(preg_match("/SELECT/i", $sql)){	
				$i = 0;
				$data = array();
				
				while($result = @$resource->fetch_assoc()) {
					$data[$i] = $result;
					$i++;
				}
				
				$resource->free();
				
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
			exit('Ошибка: ' . $this->mysql->error . '<br>Номер ошибки: ' . $this->mysql->errno . '<br>' . $sql);
		}
  	}
	
	public function escape($value) {
		return $this->mysql->real_escape_string($value);
	}
	
  	public function countAffected() {
		return $this->mysql->affected_rows;
  	}

  	public function getLastId() {
		return $this->mysql->insert_id;
  	}	
	
  	public function getCount() {
		return $this->count;
  	}
	
	public function __destruct() {
		$this->mysql->close();
	}
}
?>
