<?php
/*
* MyUCP
* File Version 4.0
* Date: 30.03.2015
* Developed by Maksa988
*/

class Model {
	private $registry;
	private $sql;
	private $select;
	private $set;
	private $order;
	private $limit;
	private $key;
	private $value;
	protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to',
    ];
    private $presence = false;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->$key;
	}
	
	public function __set($key, $value) {
		$this->registry->$key = $value;
	}

	public function create($data = []){
		$this->sql = "INSERT INTO `{$this->table}`";
		$count = count($data);
		foreach($data as $key => $value){
			$this->key .= "`{$key}`";

			if($value != 'NOW()'){
				$this->value .= "'{$value}'";
			} else {
				$this->value .= "{$value}";
			}
			
			$count--;
				if($count > 0) $this->key .= ", ";
				if($count > 0) $this->value .= ", ";
		}
		$this->db->query($this->sql."(".$this->key.") VALUES (".$this->value.")");
		$result = (!$this->db->insertId()) ? $this->db->error : $this->db->insertId();
		$this->clear();
		return $result;
	}

	public function where(){
		$condition = func_get_args();
		$this->sql .= "WHERE ";
		if(in_array($condition[1], $this->operators)){
			if($this->presence === false){
				$this->sql .= "`{$condition[0]}` {$condition[1]} '{$condition[2]}'";
				$this->presence = true;
			} else {
				$this->sql .= " AND `{$condition[0]}` {$condition[1]} '{$condition[2]}'";
			}
		} else {
			new Debug("Оператор <b>{$condition[1]}</b> не найден!", 1);
		}
		
		return $this;
	}

	public function set(){
		$params = func_get_args();
		if(is_array($params[0])){
			$count = count($params[0]);
			foreach($params[0] as $key => $value){
				if($value != 'NOW()'){
					$this->set .= "`{$key}` = '{$value}'";
				} else {
					$this->set .= "`{$key}` = {$value}";
				}
				
				$count--;
					if($count > 0) $this->set .= ", ";
			}
		} else {
			$this->set .= "`{$params[0]}` = '{$params[1]}'";
		}
		return $this;
	}

	public function order($row, $type){
		$this->order = " ORDER BY `$row` {$type} ";
		return $this;
	}

	public function select($row){
		$this->select = "{$row}";
		return $this;
	}

	public function limit(){
		$limit = func_get_args();
		$this->limit = (!empty($limit[1])) ? " LIMIT {$limit[0]}, {$limit[1]}" : " LIMIT {$limit[0]}";
		return $this;
	}

	public function get(){
		$select = (!empty($this->select)) ? $this->select : "*";
		$result = $this->db->getAll("SELECT {$select} FROM `{$this->table}` WHERE ".$this->sql.$this->order.$this->limit);
		$result = (count($result) >= 2) ? $result : $result[0];
		$this->clear();
		return $result;
	}

	public function update(){
		if(!empty($this->sql)){
			$this->sql = "WHERE ".$this->sql;
		}
		$result = $this->db->query("UPDATE `{$this->table}` SET {$this->set} {$this->sql}");
		$this->clear();
		return $result;
	}

	public function delete(){
		if(!empty($this->sql)){
			$this->sql = "WHERE ".$this->sql;
		}
		$result = $this->db->query("DELETE FROM `{$this->table}` {$this->sql}");
		$this->clear();
		return $result;
	}

	private function clear() {
		$this->limit = null;
		$this->sql = null;
		$this->set = null;
		$this->select = null;
		$this->order = null;
		$this->presence = false;
		$this->key = null;
		$this->value = null;
	}
}
?>
