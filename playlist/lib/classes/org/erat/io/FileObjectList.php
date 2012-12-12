<?php
// $Id: FileObjectList.php,v 1.2 2003/12/23 01:30:57 admin Exp $

/*********************************************************
 * 	Convenience class for opening directories and listing
 *	their contents.
 *********************************************************/

import("org/erat/util/OEIteratoror.php");
import("org/erat/io/FileObject.php");

class FileObjectList extends OEIterator {
	var $path;
	var $filter;
	
	function FileObjectList($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->FileObjectList" . $i . "(" . $args . ");");
	}

	function FileObjectList0() {}
	function FileObjectList1( $pathToDir ) {
		$extra=strrchr($pathToDir, "/");
		$this->path = ( ($extra==FALSE)||(strlen($extra)>1) ) ? $pathToDir."/" : $pathToDir;
		$this->filter = array();
		parent::OEIterator();
	}
	function FileObjectList2( $a, $b ) {}
	function FileObjectList3( $a, $b, $c ) {}

	/***********************************
	 *	PUBLIC FUNCTIONS
 	 ************************************/
	function size() {
		return sizeof($this->dataArray);
	}

	/************************************
	 *	PUBLIC FUNCTIONS
 	 ************************************/
	function filter ( $extension ) {
		array_push($this->filter,$extension);
		$this->reset();
		while ( $this->hasNext() ) {
			$obj = $this->next();
			if (!in_array($obj->extension,$this->filter)) $this->remove();
		}
	}
	
	function sortBy ( $command, $direction = "asc" ) {
		if ( $command=="name" ) {
			if ( $direction=="asc" ) $this->sort("FileObject","sort_name");
			else $this->sort("FileObject","rsort_name");
		} else if ( $command=="displayName" ) {
			if ( $direction=="asc" ) $this->sort("FileObject","sort_display");
			else $this->sort("FileObject","rsort_display");
		} else if ( $command=="extension" ) {
			if ( $direction=="asc" ) $this->sort("FileObject","sort_ext");
			else $this->sort("FileObject","rsort_ext");
		} else if ( $command=="size" ) {
			if ( $direction=="asc" ) $this->sort("FileObject","sort_size");
			else $this->sort("FileObject","rsort_size");
		}
	}

	/************************************
	 *	PRIVATE FUNCTIONS
 	 ************************************/
	function load() {
		$dir = opendir($this->path);
		$basename = basename($this->path);
		//$fileArr = array(); 
		while ( $file_name = readdir($dir) ) {
			if (($file_name !=".") && ($file_name != "..")) {
				$this->add( new FileObject($this->path,$file_name) );
			} 
		}
		closedir ($dir); 
	}
}