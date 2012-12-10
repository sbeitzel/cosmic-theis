<?php
// $Id: importer.php,v 1.2 2003/12/23 01:33:46 admin Exp $

/**********************************************************
 *	only call this function!  needs to have __CLASSBASE__
 *	defined already (usually by config.inc.php)
 *********************************************************/

function import($filename) {
	if (!is_included($filename)) {
		include_once(__CLASSBASE__.$filename);
	}
}

/*****************************************
 *	called by import function
 ****************************************/

function is_included($seek_file) {
	$included = get_included_files();
	$is_included = false;
	reset($included);
	while((list($i,$file) = each($included)) && !$is_included) {
		$is_included = (basename($file) == $seek_file); 
	} 
	return $is_included;
}

?>