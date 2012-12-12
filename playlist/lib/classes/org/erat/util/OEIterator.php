<?php
// $Id: OEIterator.phphp,v 1.3 2004/01/09 18:10:52 admin Exp $
/**********************************************
 **	Mimicks Java-style iterator
 **
 ** reset() - sets the iterator back to zero
 ** isEmpty() - is this iterator empty?
 ** size() - returns no. of elements in iterator
 ** hasNext() - is there a next element?
 ** next() - returns next element
 ** flush() - gets rid of data
 ** add($a) - adds an element "a" to iterator
 ** remove() - removes element at pointer
 ** sort($a,$b) - sort objects of type "a" by function "b"
 ***********************************************/

class OEIterator {
	var $dataArray;
	var $pointer;
	
	/**************************
	 *	Constructors
	 **************************/
	function OEIterator($param1 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->OEIterator" . $i . "(" . $args . ");");
	}
	
	function OEIterator0() {
		$this->dataArray = array();
		$this->pointer = 0;
	}
	
	function OEIterator1(&$param) {
		$this->dataArray = $param;
		$this->pointer = 0;
	}
	
	/*************************************
	 * Public functions
	 ************************************/
	
	function size()	{
		return ( sizeof($this->dataArray) );
	}

	function isEmpty() {
		return ( sizeof($this->dataArray) == 0 ) ? true : false;
	}
	
	function reset() {
		$this->pointer = 0;
	}
	
	function hasNext() {
		return ( $this->pointer < sizeof($this->dataArray) ) ? true : false;
	}
	
	function next() {
		$count = 0;
		foreach ( $this->dataArray as $key => $value ) {
			if ($count == $this->pointer) {
				$this->pointer++;
				return $value;
			} else $count++;
		}
		return null;
	}
	
	function flush() {
		$this->dataArray = null;
		$this->pointer = 0;
	}
	
	function add( $value , $key = null ) {
		if ($key != null) $this->dataArray[$key] = $value;
		else array_push($this->dataArray,$value);
	}

	function remove() {
		$count = 0;
		foreach ( $this->dataArray as $key => $value ) {
			$count++;
			if ($count == $this->pointer) {
				unset($this->dataArray[$key]);
				$this->pointer = $this->pointer - 1;
				return;
			}
		}
	}

	function sort( $class_name, $function_name ) {
		uasort( $this->dataArray , array($class_name, $function_name));
	}
}

/*
// test script
$arr = array("a","b","CCC","zyx");
$iterator = new OEIterator($arr);
$iterator->add("hello");
$iterator->add("world","next");
$iterator->reset();
while ($iterator->hasNext()) {
	echo $iterator->next() . "<br>";
}
$iterator->flush();
*/
?>
