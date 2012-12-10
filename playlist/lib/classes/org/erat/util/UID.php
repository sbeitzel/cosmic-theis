<?php
// $Id: UID.php,v 1.2 2003/12/23 01:31:20 admin Exp $
/**********************************************
 **	Mimicks Java-style UID generator
 **	generates things like 726fce:85b7449bbe:-44df
 **
 **	generate() - returns a random id
 ***********************************************/

class UID {
	var $classCreationTime;
	var $lastExecutionTime;
	
	/**************************
	 *	Constructors
	 **************************/
	function UID() {
   		$this->classCreationTime = microtime();
   		$this->lastExecutionTime = microtime();
	}
	
	function generate() {
		$executionTime = microtime();
		$this->mt_seed($this->classCreationTime);
		$part1 = dechex(mt_rand(1048576,16777215));
		$this->mt_seed($this->lastExecutionTime);
		$part2 = dechex(mt_rand(65536,1048575));
		$this->mt_seed($this->classCreationTime);
		$part3 = dechex(mt_rand(65536,1048575));
		$this->mt_seed($executionTime);
		$part4 = dechex(mt_rand(4096,65535));
		$this->lastExecutionTime = $executionTime;
		return $part1.":".$part2.$part3.":-".$part4;
	}
	
	function mt_seed( $microtime ){
		if ($already_random) {
			list($usec, $sec) = explode(' ', $microtime );
			mt_srand((float) $sec + ((float) $usec * 100000));
		}
	} 

}

/*
// test script
$uid = new UID();
echo $uid->generate();
*/
?>