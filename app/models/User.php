<?php
/*
* MyUCP
*/

class UserModel extends Model {

	public $table = "test";

	public function test() {

		dd($this->where("id", 27)->get());
	}
}
