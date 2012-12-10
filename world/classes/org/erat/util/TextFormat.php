<?php
// $Id: TextFormat.php,v 1.2 2003/12/23 01:31:20 admin Exp $
/******************************************************************************
 **	Formats text in all kinds of ways
 **
 **	CropAtWord($a,$b,$c) - crops "a" after "b" letters using "c" as crop mark
 ******************************************************************************/

class TextFormat {
	
	/**************************
	 *	Constructors
	 **************************/
	function TextFormat() {}
	
	function CropAtWord( $phrase, $chars, $ellipse ) {
		if ( strlen($phrase) < $chars ) return $phrase;
		$charCount = 0;
		$croppedPhrase = "";
		$words = explode(" ",$phrase);
		foreach ( $words as $key=>$word ) {
			$croppedPhrase .= ( $croppedPhrase=="") ? $word : " ".$word;
			if ( strlen($croppedPhrase) > $chars ) {
				return $croppedPhrase . $ellipse;
			}
		}
		return $croppedPhrase;
	}

}
	
/*
// test script
$df = new DateFormat();
echo $df->MySQLTimestamp();
*/
?>