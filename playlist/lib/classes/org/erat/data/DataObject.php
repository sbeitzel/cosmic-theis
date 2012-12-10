<?php
// $Id: DataObject.php,v 1.2 2003/12/23 01:30:52 admin Exp $

/***************************************************************************
 *
 *	Basic Data Object
 *	basically holds a single row returned from SQL query
 *	
 *	BasicDataObject($a,$b) - constructor, "a" is result set, "b" is the row
 *	BasicDataObject($a,$b) - "a" is queryname, "b" is database, no query params
 *	BasicDataObject($a,$b,c) - "a" is queryname, "b" is database, "c" is params
 *
 *	public fields will correspond to column names from query.
 *	update($a,$b) - "a" is queryname, "b" is database, no params
 *	update($a,$b,$c) - "a" is queryname, "b" is database, "c" is params
 *
 ***************************************************************************/

class DataObject {

	/**************************
	 *	Constructors
	 **************************/
	function DataObject($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->DataObject" . $i . "(" . $args . ");");
	}

	function DataObject0() {}
	
	function DataObject1( &$row ) {
		foreach ( $row as $key => $value ) {
        	$this->{$key} = $value;
        }
	}

	function DataObject2( $query_name, &$db ) {
		$this->getRowFromDatabase($query_name,$db,array());
	}

	function DataObject3( $query_name, &$db, &$arr ) {
		$this->getRowFromDatabase($query_name,$db,$arr);
	}
	
	/********************************************
	 *	PUBLIC FUNCTIONS
	 ********************************************/
	
	function update ( $query_name, &$db, $params = null ) {
		if ( $params != null ) $db->doQuery($query_name,&$params);
		else $db->doQuery($query_name,array());
	}
	
	function getQueryString ( $query_name, &$db, $params = null ) {
		if ( $params != null ) return $db->getQueryString($query_name,&$params);
		else return $db->getQueryString($query_name,array());
	}
	
	/********************************************
	 *	PRIVATE FUNCTIONS
	 ********************************************/
	
	function getRowFromDatabase($query_name,&$db,$arr) {
		$result = $db->doQuery($query_name,$arr);
		if ($row = mysql_fetch_assoc($result)) {
        	foreach ( $row as $key => $value ) {
        		$this->{$key} = $value;
        	}
	    }
		mysql_free_result($result);
	}
}

?>