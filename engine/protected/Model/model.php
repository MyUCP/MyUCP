<?php
/*
* MyUCP
*/

class Model {

	private $registry;
    public $table;
    private $Builder;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function __get($key) {
		return $this->registry->$key;
	}
	
	public function __set($key, $value) {
		$this->registry->$key = $value;
	}

	public function table($name) {
		$this->table = $name;
		$this->Builder = Builder::table($name);

		return $this;
	}

	public function create($data = []){

		return $this->Builder->create($data);;
	}

	public function where(){
		$this->Builder->where(func_get_args());

		return $this;
	}

	public function orWhere(){
		$this->Builder->orWhere(func_get_args());
		
		return $this;
	}

	public function whereBetween($row, $condition = []){
		$this->Builder->whereBetween($row, $condition);
		
		return $this;
	}

	public function whereNotBetween($row, $condition = []){
		$this->Builder->whereNotBetween($row, $condition);
		
		return $this;
	}

	public function whereIn($row, $condition = []){
		$this->Builder->whereIn($row, $condition);
		
		return $this;
	}

	public function whereNotIn($row, $condition = []){
		$this->Builder->whereNotIn($row, $condition);
		
		return $this;
	}

	public function whereNull($row = null){
		$this->Builder->whereNull($row);
		
		return $this;
	}

	public function whereNotNull($row = null){
		$this->Builder->whereNotNull($row);
		
		return $this;
	}

	public function order($row, $type){
		$this->Builder->order($row, $type);
		return $this;
	}

	public function select($row){
		$this->Builder->select($row);

		return $this;
	}

	public function addSelect($row){
		$this->Builder->addSelect($row);
		
		return $this;
	}

	public function limit(){
		$this->Builder->limit(func_get_args());
		
		return $this;
	}

	public function get(){

		return $this->Builder->get();
	}

	public function first(){

		return $this->Builder->first();
	}

	public function value($value){
		
		return $this->Builder->value($value);
	}

	public function count(){

		return $this->Builder->count();
	}

	public function max($row = null){

		return $this->Builder->max($row);
	}

	public function min($row = null){
		
		return $this->Builder->min($row);
	}

	public function avg($row = null){
		
		return $this->Builder->avg($row);
	}

	public function sum($row = null){
		
		return $this->Builder->sum($row);
	}

	public function groupBy($row) {
		$this->Builder->groupBy($row);

		return $this;
	}

	public function join() {
		$this->Builder->join(func_get_args());

		return $this;
	}

	public function leftJoin() {
		$this->Builder->leftJoin(func_get_args());

		return $this;
	}

	public function rightJoin() {
		$this->Builder->rightJoin(func_get_args());

		return $this;
	}

	public function crossJoin($table = null) {
		$this->Builder->crossJoin(func_get_args());

		return $this;
	}

	public function set(){
		$this->Builder->set(func_get_args());

		return $this;
	}

	public function update(){

		return $this->Builder->update();
	}

	public function delete(){
		
		return $this->Builder->delete();
	}
}
?>
