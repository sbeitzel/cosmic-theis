<?php
// $Id: DataObjectList.php,v 1.2 2003/12/23 01:30:52 admin Exp $

/****************************************************************************
 *
 *	DataObjectList represents a result set from an SQL query composed of
 *	data objects, from which one can get field values from field names.
 *	This class also takes care of actually getting the data from the database.
 *
 *	size()
 *	load($a) - if a query hasn't been loaded yet, do it here with array of parameters
 *  elementAt($a) - gratuitous. gets element at "a"
 *	printQuery()
 *
 ****************************************************************************/

import("org/erat/util/OEIteratoror.php");
import("org/erat/data/DataObject.php");

class DataObjectList extends OEIterator {

	var $qName;
	var $items;
	var $databaseToUse;
	
	/**************************
	 *	Constructors
	 **************************/
	function DataObjectList($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->DataObjectList" . $i . "(" . $args . ");");
	}

	function DataObjectList0() {}
	function DataObjectList1( &$row ) {}

	function DataObjectList2( $query_name, &$db ) {
		$this->qName=$query_name;
		$this->databaseToUse = $db;
		parent::OEIterator();
	}

	function DataObjectList3( $query_name, &$db, $arr ) {
		$this->qName=$query_name;
		$this->databaseToUse = $db;
		parent::OEIterator();
		//$this->printQuery($arr);
		$this->load($arr);
	}

	/********************************************
	 *	PUBLIC FUNCTIONS
	 ********************************************/

	function printQuery($arr) {
		$db = $this->databaseToUse;
		echo "query : " . $db->getQueryString($this->qName,$arr);
	}

		function size() {
			return sizeof($this->dataArray);
		}

	function elementAt( $idx ) {
		if ( $idx > 0 && $idx < sizeof($this->dataArray) ) {
			return $this->dataArray[$idx];
		} else if ( $this->size() > 0 ) {
		 	return $this->dataArray[0];
		} else return null;
	}

	function load($arr) {
		$db = &$this->databaseToUse;
		//echo "query : " . $db->getQueryString($this->qName,$arr);
		$result = $db->doQuery($this->qName,$arr);
		while ($row = mysql_fetch_assoc($result)) {
        	$obj = new DataObject($row);
        	$this->add($obj);
	    }
		mysql_free_result($result);
	}

	/********************************************
	 *	PRIVATE FUNCTIONS
	 ********************************************/

}

?>