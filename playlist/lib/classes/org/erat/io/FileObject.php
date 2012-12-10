<?php
// $Id: FileObject.php,v 1.2 2003/12/23 01:30:57 admin Exp $

/***************************************************************************
 *
 *	Basic File Object
 *	basically encapsulates information about a file
 *	
 *	FileObject($a) - valid path to a file
 *	FileObject($a,$b) - "a" is path to file, "b" is filename
 *
 ***************************************************************************/

class FileObject {

	var $path;
	var $name;
	var $displayName;
	var $extension;
	var $size;
	var $created;
	var $lastModified;

	/**************************
	 *	Constructors
	 **************************/
	function FileObject($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->FileObject" . $i . "(" . $args . ");");
	}

	function FileObject0() {}
	
	function FileObject1( $fullPath ) {
		if ( $filename=strrchr($fullPath, "/") != FALSE ) {
			$lastPart = strrchr($fullPath, "/");
			$path = str_replace ( $lastPart, "/", $fullPath );
			$name = substr( $lastPart, 0 , strlen($lastPart)-1 );
			$this->FileObject2($path,$name);
		} else {
			$this->FileObject2("",$fullPath);
		}
	}

	function FileObject2( $path, $name ) {
		$this->path = $path;
		$this->name = "";
		$parts = explode(".",$name);
		$count= count($parts);
		if ($count>1) {
			for ( $i=0; $i<$count-1; $i++ ) $this->name .= ($i>0) ? ".".$parts[$i] : $parts[$i];
			$this->extension = ".".$parts[$count-1];
		} else {
			$this->name = $name;
			$this->extension = "";
		}
		$this->refresh();
	}

	function FileObject3( $a, $b, $c ) { }


	/*********************************
	 *	PRIVATE FUNCTIONS
	 *********************************/
	function refresh() {
		$filename = $this->path . $this->name . $this->extension;
		if ( file_exists($filename) ) {
			$this->size = "0";
			$this->created = "";
			$this->lastModified = "";
		} else {
			$this->size = filesize($filename);
			$this->created = filectime($filename);
			$this->lastModified = filemtime($filename);
		}
	}

	/** sorting functions **/
	function sort_name($a, $b) {
        $al = strtolower($a->name);
        $bl = strtolower($b->name);
        if ($al == $bl) return 0;
        return ($al > $bl) ? +1 : -1;
    }
    function rsort_name($a, $b) {
        $al = strtolower($a->name);
        $bl = strtolower($b->name);
        if ($al == $bl) return 0;
        return ($al > $bl) ? -1 : +1;
    }
	function sort_display($a, $b) {
        $al = strtolower($a->displayName);
        $bl = strtolower($b->displayName);
        if ($al == $bl) return 0;
        return ($al > $bl) ? +1 : -1;
    }
    function rsort_display($a, $b) {
        $al = strtolower($a->displayName);
        $bl = strtolower($b->displayName);
        if ($al == $bl) return 0;
        return ($al > $bl) ? -1 : +1;
    }
	function sort_ext($a, $b) {
        $al = strtolower($a->extension);
        $bl = strtolower($b->extension);
        if ($al == $bl) return 0;
        return ($al > $bl) ? +1 : -1;
    }
    function rsort_ext($a, $b) {
        $al = strtolower($a->extension);
        $bl = strtolower($b->extension);
        if ($al == $bl) return 0;
        return ($al > $bl) ? -1 : +1;
    }
	function sort_size($a, $b) {
        $al = $a->size;
        $bl = $b->size;
        if ($al == $bl) return 0;
        return ($al > $bl) ? +1 : -1;
    }
    function rsort_size($a, $b) {
        $al = $a->size;
        $bl = $b->size;
        if ($al == $bl) return 0;
        return ($al > $bl) ? -1 : +1;
    }
}