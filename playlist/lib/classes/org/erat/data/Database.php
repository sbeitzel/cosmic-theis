<?php
// $Id: Database.php,v 1.5 2004/03/12 00:28:32 admin Exp $

/**************************************************************************************************
 *	classes extending this one will want to define a set of queries, eg:
 *	$this->queries["getSomeData"] = "SELECT * FROM table WHERE field1='%01' AND field2=%02";
 *	
 *	Database() - constructor with usual parameters
 *	connect() - establish a link to the database
 *	close() - close the link to the database
 *	doQuery($a,$b) - do the named query "a", with array of parameters "b"; returns result set
 *	getQueryString($a,$b) - same as above, but returns what the sent query looks like, not results
 *
 **************************************************************************************************/
class Database {
	var $link;
	var $queries;
	var $result;
	
	var $username;
	var $password;
	var $dbHost;
	var $dbName;
	
	var $suppressEscapingSingleQuotes; // hack! sometimes we want to not backslash though
	
	function Database($username = "", $password = "", $dbHost = "", $dbName = "") {
		$this->queries = array();
		$this->username = $username;
		$this->password = $password;
		$this->dbHost = $dbHost;
		$this->dbName = $dbName;
		$this->escapeSingleQuotes = false;
	}

	function connect() {
		$this->link = mysql_connect($this->dbHost,$this->username,$this->password) or die("Could not connect");
	    mysql_select_db($this->dbName) or die("Could not select database");
	}

	function close() {
		mysql_close($this->link);
	}

	function query($str) {
		$result = mysql_query($str) or die("Query failed ".mysql_error());
		return $result;
	}

	function doQuery($str,$arr) {
		$thequery = $this->queries[$str];
		for ($i=0; $i<sizeof($arr); $i++) {
			$j = $i+1;
			$replace_str = ($j<10) ? "%0".$j : "%".$j;
			$thequery = str_replace($replace_str,$this->escapeSingleQuotes($arr[$i]),$thequery);
		}
		return $this->query($thequery);
	}

	function escapeSingleQuotes($val) {
		if ($this->escapeSingleQuotes) return str_replace("'","\'",$val);
		else return $val;
	}

	function getQueryString($str,$arr) {
		$thequery = $this->queries[$str];
		for ($i=0; $i<sizeof($arr); $i++) {
			$j = $i+1;
			$replace_str = ($j<10) ? "%0".$j : "%".$j;
			$thequery = str_replace($replace_str,$this->escapeSingleQuotes($arr[$i]),$thequery);
		}
		return $thequery;
	}

	function getFields($table_name) {
		$fields = mysql_list_fields(__DBNAME__,$table_name);
		$columns = mysql_num_fields($fields);
		$retarr = array();
		for ($i = 0; $i < $columns; $i++) {
		    $retarr[mysql_field_name($fields, $i)] = mysql_field_type($fields, $i);
		}
		return $retarr;
	}
	
	function insertID()	{
		return mysql_insert_id($this->link);
	}
	
	function affectedRows()	{
		return mysql_affected_rows( $this->link );
	}
}

/*
// testing... an example subclass with script below
include("../util/OEIterator.phphp");
include("DataObjectList.php");
include("DataObject.php");
class EratDB extends Database {
	function EratDB() {
		parent::Database("rat","rat","localhost","erator_arch");
		$this->queries["getSubcategory"] = "SELECT * FROM subcategory";
		$this->connect();
	}
}

$db = new EratDB();
$res = new DataObjectList("getSubcategory",$db);
$res->load(array());
$res->reset();
while ($res->hasNext()) {
	$obj = &$res->next(); // need reference, or else change won't work
	$obj->label = $obj->label . " XXX";
}
$res->reset();
while ($res->hasNext()) {
	$obj = $res->next();
	echo $obj->label . "<br>";
}
*/

?>
