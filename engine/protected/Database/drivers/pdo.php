<?php
/*
* MyUCP
*/

final class pdoDriver {
	private $pdo;

	public function __construct($options) {
	  @$this->pdo = new PDO("$options['type']:host={$options['hostname']};dbname={$options['database']};charset={$options['charset']}", $options['username'], $options['password']);
		if (!$this->pdo){
			new Debug($this->pdo->errorCode()." ".$this->pdo->errorInfo(), 1);
		}
	}
	
	public function query($sql) {
	  return $this->pdo->query($sql);
	}

	public function fetch($result, $mode = ""){
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	public function affected_rows(){
		return $this->pdo->exec;
	}

	public function num_rows($result){
		return false;
	}

	public function free($result){
		unset($result);
	}

  public function escape($value) {
	  return $this->pdo->quote($value);
  }

	public function getLastId() {
	  return $this->pdo->lastInsertId();
	}	

	public function getError(){
		return $this->pdo->errorInfo();
	}

	public function getErrno(){
		return $this->pdo->errorCode();
	}
  
  public function __destruct() {
	  unset($this->pdo);  
  }
}
?>
