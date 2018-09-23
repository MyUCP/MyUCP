<?php
/*
* MyUCP
*/

class Builder {

	private $db;

	private $sql;
	private $select = "*";
	private $set;
	private $order;
	private $group;
	private $limit;
	private $join;
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
	protected $functions = [
		'NOW()', 'CURDATE()', 'RAND()',
	];
	private $presence = false;
	public static $table;

	public function __construct() {
		$this->db = registry()->db;
	}

	public static function table($name) {
		self::$table = $name;

		return new self;
	}

	public function from($name) {
		$this->table($name);

		return $this;
	}

	public function create($data = []){
		$this->sql = "INSERT INTO `". self::$table ."`";
		$count = count($data);
		foreach($data as $key => $value){
			$this->key .= "{$key}";

			if(!in_array($value, $this->functions)){
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

	public function insert($data = []) {

		return $this->create($data);
	}

	public function where(){
		$condition = (is_array(func_get_args()[0])) ? func_get_args()[0] : func_get_args();

		if($this->presence === false)
			$this->sql .= "WHERE ";

        if(!isset($condition[1])) {
            if($this->presence === false){
                $this->sql .= $condition[0];
                $this->presence = true;
            } else {
                $this->sql .= " AND ". $condition[0];
            }

            return $this;
        }

		if(!isset($condition[2])) {
			$value = (in_array($condition[1], $this->functions)) ? $condition[1] : "'{$condition[1]}'";

			if($this->presence === false){
				$this->sql .= "{$condition[0]} = ".$value;
				$this->presence = true;
			} else {
				$this->sql .= " AND {$condition[0]} = ".$value;
			}
		} else {
			if(in_array($condition[1], $this->operators)){
				$value = (in_array($condition[2], $this->functions)) ? $condition[2] : "'{$condition[2]}'";

				if($this->presence === false){
					$this->sql .= "{$condition[0]} {$condition[1]} ".$value;
					$this->presence = true;
				} else {
					$this->sql .= " AND {$condition[0]} {$condition[1]} ".$value;
				}
			} else {
				throw new DebugException("Оператор <b>{$condition[1]}</b> не найден!", 1);
			}
		}

		return $this;
	}

	public function orWhere(){
		$condition = (is_array(func_get_args()[0])) ? func_get_args()[0] : func_get_args();

		if($this->presence === false)
			throw new DebugException("Использование метода orWhere() без метода where() невозможно", 1);

        if(!isset($condition[1])) {
            $this->sql .= " OR ". $condition[0];

            return $this;
        }

		if(!isset($condition[2])) {
			$this->sql .= " OR {$condition[0]} = '{$condition[1]}'";
		} else {
			if(in_array($condition[1], $this->operators)){
				$this->sql .= " OR {$condition[0]} {$condition[1]} '{$condition[2]}'";
			} else {
				throw new DebugException("Оператор <b>{$condition[1]}</b> не найден!", 1);
			}
		}

		return $this;
	}

	public function whereBetween($row, $condition = []){

		if($this->presence === false)
			$this->sql .= "WHERE ";

		if(!is_array($condition))
			throw new DebugException("В качестве второго аргумента метод whereBetween() ожидает массив", 1);

		if($this->presence === false){
			$this->sql .= "{$row} BETWEEN '{$condition[0]}' AND '{$condition[1]}'";
			$this->presence = true;
		} else {
			$this->sql .= " AND {$row} BETWEEN '{$condition[0]}' AND '{$condition[1]}'";
		}

		return $this;
	}

	public function whereNotBetween($row, $condition = []){

		if($this->presence === false)
			$this->sql .= "WHERE ";

		if(!is_array($condition))
			throw new DebugException("В качестве второго аргумента метод whereNotBetween() ожидает массив", 1);

		if($this->presence === false){
			$this->sql .= "{$row} NOT BETWEEN '{$condition[0]}' AND '{$condition[1]}'";
			$this->presence = true;
		} else {
			$this->sql .= " AND {$row} NOT BETWEEN '{$condition[0]}' AND '{$condition[1]}'";
		}

		return $this;
	}

	public function whereIn($row, $condition = []){

		if($this->presence === false)
			$this->sql .= "WHERE ";

		if(!is_array($condition))
			throw new DebugException("В качестве второго аргумента метод whereIn() ожидает массив", 1);

		$values = "(";

		$count = count($condition);
		foreach ($condition as $value) {
			$values .= "'{$value}'";

			$count--;
			if($count > 0) $values .= ", ";
		}

		if($this->presence === false){
			$this->sql .= "{$row} IN {$values})";
			$this->presence = true;
		} else {
			$this->sql .= " AND {$row} IN {$values})";
		}

		return $this;
	}

	public function whereNotIn($row, $condition = []){

		if($this->presence === false)
			$this->sql .= "WHERE ";

		if(!is_array($condition))
			throw new DebugException("В качестве второго аргумента метод whereNotIn() ожидает массив", 1);

		$values = "(";

		$count = count($condition);
		foreach ($condition as $value) {
			$values .= "'{$value}'";

			$count--;
			if($count > 0) $values .= ", ";
		}

		if($this->presence === false){
			$this->sql .= "{$row} NOT IN {$values})";
			$this->presence = true;
		} else {
			$this->sql .= " AND {$row} NOT IN {$values})";
		}

		return $this;
	}

	public function whereNull($row = null){

		if($this->presence === false)
			$this->sql .= "WHERE ";

		if(empty($row))
			throw new DebugException("В качестве аргумента метод whereNull() ожидает название поля", 1);

		if($this->presence === false){
			$this->sql .= "ISNULL({$row})";
			$this->presence = true;
		} else {
			$this->sql .= " AND ISNULL({$row})";
		}

		return $this;
	}

	public function whereNotNull($row = null){

		if($this->presence === false)
			$this->sql .= "WHERE ";

		if(empty($row))
			throw new DebugException("В качестве аргумента метод whereNotNull() ожидает название поля", 1);

		if($this->presence === false){
			$this->sql .= "NOT ISNULL({$row})";
			$this->presence = true;
		} else {
			$this->sql .= " AND NOT ISNULL({$row})";
		}

		return $this;
	}

	public function order($row, $type){
		$this->order = " ORDER BY $row {$type} ";
		return $this;
	}

	public function select($row){
		$this->select = "{$row}";

		return $this;
	}

	public function addSelect($row){
		$this->select .= ", {$row}";

		return $this;
	}

	public function limit(){
		$limit = (is_array(func_get_args()[0])) ? func_get_args()[0] : func_get_args();

		$this->limit = (!empty($limit[1])) ? " LIMIT {$limit[0]}, {$limit[1]}" : " LIMIT {$limit[0]}";

		return $this;
	}

	public function get(){

		$result = $this->db->getAll("SELECT {$this->select} FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);
		$this->clear();

		return $result;
	}

	public function first(){

		$result = $this->db->getRow("SELECT {$this->select} FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);
		$this->clear();

		return $result;
	}

	public function firstOrError(){

		$result = $this->db->getRow("SELECT {$this->select} FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);

		if($this->db->affectedRows() == 0)
			return new HttpException(404, "Страница не найдена");

		$this->clear();

		return $result;
	}

	public function value($value){
		$result = $this->db->getOne("SELECT {$value} FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);

		$this->clear();

		return $result;
	}

	public function count(){

		$result = $this->db->getOne("SELECT COUNT(*) FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);
		$this->clear();

		return $result;
	}

	public function max($row = null){
		if(empty($row)) {
			throw new DebugException("Для метода max() необходимо указать параметр с названием поля", 1);
		}

		$result = $this->db->getOne("SELECT MAX({$row}) FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);
		$this->clear();

		return $result;
	}

	public function min($row = null){
		if(empty($row)) {
			throw new DebugException("Для метода min() необходимо указать параметр с названием поля", 1);
		}

		$result = $this->db->getOne("SELECT MIN({$row}) FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);
		$this->clear();

		return $result;
	}

	public function avg($row = null){
		if(empty($row)) {
			throw new DebugException("Для метода avg() необходимо указать параметр с названием поля", 1);
		}

		$result = $this->db->getOne("SELECT AVG({$row}) FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);
		$this->clear();

		return $result;
	}

	public function sum($row = null){
		if(empty($row)) {
			throw new DebugException("Для метода sum() необходимо указать параметр с названием поля", 1);
		}

		$result = $this->db->getOne("SELECT SUM({$row}) FROM ". self::$table ." ".$this->join.$this->sql.$this->group.$this->order.$this->limit);
		$this->clear();

		return $result;
	}

	public function groupBy($row) {
		$this->group = "GROUP BY {$row}";

		return $this;
	}

	public function join() {
		$params = (is_array(func_get_args()[0])) ? func_get_args()[0] : func_get_args();

		if(empty($params[0]))
			throw new DebugException("В качестве первого параметра метода join() ожидается название таблицы", 1);

		if(empty($params[2]))
			throw new DebugException("Не указано условие для метода join()", 1);

		if(empty($params[1]) or empty($params[3]))
			throw new DebugException("Не указаны поля для метода join()", 1);

		if(!empty($this->join))
			throw new DebugException("В запросе не может присутствовать больше одного объеденения строк JOIN", 1);

		$this->join = "INNER JOIN {$params[0]} ON {$params[1]} {$params[2]} {$params[3]} ";

		return $this;
	}

	public function leftJoin() {
		$params = (is_array(func_get_args()[0])) ? func_get_args()[0] : func_get_args();

		if(empty($params[0]))
			throw new DebugException("В качестве первого параметра метода leftJoin() ожидается название таблицы", 1);

		if(empty($params[2]))
			throw new DebugException("Не указано условие для метода leftJoin()", 1);

		if(empty($params[1]) or empty($params[3]))
			throw new DebugException("Не указаны поля для метода leftJoin()", 1);

		if(!empty($this->join))
			throw new DebugException("В запросе не может присутствовать больше одного объеденения строк JOIN", 1);

		$this->join = "LEFT JOIN {$params[0]} ON {$params[1]} {$params[2]} {$params[3]} ";

		return $this;
	}

	public function rightJoin() {
		$params = (is_array(func_get_args()[0])) ? func_get_args()[0] : func_get_args();

		if(empty($params[0]))
			throw new DebugException("В качестве первого параметра метода rightJoin() ожидается название таблицы", 1);

		if(empty($params[2]))
			throw new DebugException("Не указано условие для метода rightJoin()", 1);

		if(empty($params[1]) or empty($params[3]))
			throw new DebugException("Не указаны поля для метода rightJoin()", 1);

		if(!empty($this->join))
			throw new DebugException("В запросе не может присутствовать больше одного объеденения строк JOIN", 1);

		$this->join = "RIGHT JOIN {$params[0]} ON {$params[1]} {$params[2]} {$params[3]} ";

		return $this;
	}

	public function crossJoin($table = null) {

		if(empty($table))
			throw new DebugException("В метод crossJoin() не передан параметр с названием таблицы", 1);

		if(!empty($this->join))
			throw new DebugException("В запросе не может присутствовать больше одного объеденения строк JOIN", 1);

		$this->join = "CROSS JOIN {$table} ";

		return $this;
	}

	public function set(){
		$params = (is_array(func_get_args()[0])) ? func_get_args()[0] : func_get_args();

		if(is_array($params)){
			$count = count($params);
			foreach($params as $key => $value){
				if(!in_array($value, $this->operators) && !in_array($value, $this->functions)){
					$this->set .= "{$key} = '{$value}'";
				} else {
					$this->set .= "{$key} = {$value}";
				}

				$count--;
				if($count > 0) $this->set .= ", ";
			}
		} else {
            if(!isset($params[1])) {
                $this->set .= $params[1];

                return $this;
            }

			$this->set .= "{$params[0]} = '{$params[1]}'";
		}

		return $this;
	}

	public function update(){

		if(!empty($this->sql)){
			$this->sql = " ".$this->join.$this->sql;
		}
		$result = $this->db->query("UPDATE ". self::$table ." SET {$this->set} {$this->sql} {$this->limit}");
		$this->clear();

		return $result;
	}

	public function delete(){
		if(!empty($this->sql)){
			$this->sql = " ".$this->join.$this->sql;
		}
		$result = $this->db->query("DELETE FROM ". self::$table ." {$this->sql}");
		$this->clear();

		return $result;
	}

	public function increment($num) {

		if($num < 1)
			throw new DebugException("Метод increment() в качестве аргумента может принять только число", 1);

		return $this->db->query("ALTER TABLE ". self::$table ." AUTO_INCREMENT = {$num}");
	}

	public function truncate() {

		return $this->db->query("TRUNCATE TABLE ". ((self::$table != null) ? self::$table : $this->table));
	}

	private function clear() {
		$this->limit = null;
		$this->sql = null;
		$this->set = null;
		$this->select = "*";
		$this->order = null;
		$this->presence = false;
		$this->key = null;
		$this->value = null;
		$this->group = null;
		$this->join = null;
		self::$table = null;
	}
}