<?php
/*
* MyUCP
*/

if(!function_exists('dd')) {

	function dd($value, $die = true){
	    new Dumper($value, $die);
	}

}
 
if(!function_exists('ci')) {

	function ci($value) {
	    new Dumper($value, false, "ci");
	}
	
}