<?php
/*
* MyUCP
*/

final class mysqlDriver {
	private $mysql;

	public function __construct($options) {
    @$this->mysql = mysqli_connect($options['hostname'], $options['username'], $options['password'], $options['database']);
		
    if (!$this->mysql) {
		  new Debug(mysqli_connect_errno()." ".mysqli_connect_error(), "1");
		}

		mysqli_set_charset($this->mysql, $options['charset']) or exit(mysqli_error($this->mysql));
  	}
		
	public function query($sql) {
		return mysqli_query($this->mysql, $sql);
	}
	
	public function fetch($result, $mode){
		return mysqli_fetch_array($result, $mode);
	}

	public function affected_rows(){
		return mysqli_affected_rows($this->mysql);
	}

	public function num_rows($result){
		return mysqli_num_rows($result);
	}

	public function free($result){
		mysqli_free_result($result);
	}

	public function escape($value) {
		return mysqli_real_escape_string($this->mysql, $value);
	}

	public function getLastId() {
    	return mysqli_insert_id($this->mysql);
	}	

	public function getError(){
		return mysqli_error($this->mysql);
	}

	public function getErrno(){
		return mysqli_errno($this->mysql);
	}

	public function __destruct() {
		@mysqli_close($this->mysql);
	}
}
?>
