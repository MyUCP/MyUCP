<?php
/*
* MyUCP
*/

class Model {

    private $registry;
    public $table = null;
    public $primary_key = "id";

    private $Builder;

    public function __construct($registry) {
        $this->registry = $registry;
        $this->Builder = new Builder();
        $this->table = ($this->table == null) ? mb_strtolower(str_replace("Model", "", get_class($this))."s") : $this->table;
        $this->Builder->from($this->table);
    }

    public function __get($key) {
        return $this->registry->$key;
    }

    public function __set($key, $value) {
        $this->registry->$key = $value;
    }

    public function table($name) {
        $this->table = $name;
        $this->Builder->from($this->table);

        return $this;
    }

    public function create($data = []){

        return $this->Builder->from($this->table)->create($data);
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

        return $this->Builder->from($this->table)->get();
    }

    public function find($key)
    {
        return $this->Builder->from($this->table)->where($this->primary_key, $key)->first();
    }

    public function first($key = null){

        if($key == null)
            return $this->Builder->from($this->table)->first();

        return $this->Builder->from($this->table)->where($this->primary_key, $key)->first();
    }

    public function firstOrError($key = null){

        if($key == null)
            return $this->Builder->from($this->table)->firstOrError();

        return $this->Builder->from($this->table)->where($this->primary_key, $key)->firstOrError();
    }

    public function value($value){

        return $this->Builder->from($this->table)->value($value);
    }

    public function count(){

        return $this->Builder->from($this->table)->count();
    }

    public function max($row = null){

        return $this->Builder->from($this->table)->max($row);
    }

    public function min($row = null){

        return $this->Builder->from($this->table)->min($row);
    }

    public function avg($row = null){

        return $this->Builder->from($this->table)->avg($row);
    }

    public function sum($row = null){

        return $this->Builder->from($this->table)->sum($row);
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

    public function crossJoin() {
        $this->Builder->crossJoin(func_get_args());

        return $this;
    }

    public function set(){
        $this->Builder->set(func_get_args()[0]);

        return $this;
    }

    public function update(){

        return $this->Builder->from($this->table)->update();
    }

    public function delete(){

        return $this->Builder->from($this->table)->delete();
    }
}