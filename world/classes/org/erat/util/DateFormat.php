<?php
// $Id: DateFormat.php,v 1.2 2003/12/23 01:30:54 admin Exp $
/**********************************************
 **	Formats dates in all kinds of ways
 **
 **	MySQLTimestamp() - returns a mysql style
 ***********************************************/

class DateFormat {
	
	/**************************
	 *	Constructors
	 **************************/
	function DateFormat() {}
	
	function MySQLTimestamp() {
		return date("Y-m-d H:i:s",time());
	}

	function getTimeBetweenDates($timeA, $timeB) {
		$res = $this->addTimeUntilASurpassesB($timeA,$timeB);
		return $res;
	}

	/**************************
	 *	Private Functions
	 **************************/

	function getIncrementedTime ( $timeToIncrement, $partToIncrement, $howMuch ) {
		if ( $partToIncrement == "year" ) {
			return mktime (date("G",$timeToIncrement),date("i",$timeToIncrement),date("S",$timeToIncrement),date("m",$timeToIncrement),date("d",$timeToIncrement),date("Y",$timeToIncrement)+$howMuch);
		} else if ( $partToIncrement == "month" ) {
			return mktime (date("G",$timeToIncrement),date("i",$timeToIncrement),date("S",$timeToIncrement),date("m",$timeToIncrement)+$howMuch,date("d",$timeToIncrement),date("Y",$timeToIncrement));
		} else if ( $partToIncrement == "day" ) {
			return mktime (date("G",$timeToIncrement),date("i",$timeToIncrement),date("S",$timeToIncrement),date("m",$timeToIncrement),date("d",$timeToIncrement)+$howMuch,date("Y",$timeToIncrement));
		} else if ( $partToIncrement == "hour" ) {
			return mktime (date("G",$timeToIncrement)+$howMuch,date("i",$timeToIncrement),date("S",$timeToIncrement),date("m",$timeToIncrement),date("d",$timeToIncrement),date("Y",$timeToIncrement));
		} else if ( $partToIncrement == "minute" ) {
			return mktime (date("G",$timeToIncrement),date("i",$timeToIncrement)+$howMuch,date("S",$timeToIncrement),date("m",$timeToIncrement),date("d",$timeToIncrement),date("Y",$timeToIncrement));
		} else if ( $partToIncrement == "second" ) {
			return mktime (date("G",$timeToIncrement),date("i",$timeToIncrement),date("S",$timeToIncrement)+$howMuch,date("m",$timeToIncrement),date("d",$timeToIncrement),date("Y",$timeToIncrement));
		}
	}

	function addTimeUntilASurpassesB ($a,$b) {
		$retarray = array();
		$retarray["year"] = 0;
		$retarray["month"] = 0;
		$retarray["day"] = 0;
		$retarray["hour"] = 0;
		$retarray["minute"] = 0;
		$retarray["second"] = 0;
		foreach ( $retarray as $key=>$value ) {
			$candidate = $a;
			while ( $candidate < $b ) {
				$candidate = $this->getIncrementedTime($candidate,$key,1);
				if ( $candidate <= $b ) {
					$retarray[$key]++;
					$a = $candidate;
				}
			}
		}
		return $retarray;
	}

}
/*
// test script
$df = new DateFormat();
$start = mktime("0","0","0","6","2","1977"); // format first time
$end = mktime("5","59","0","5","29","2003"); // format end time
$res = $df->getTimeBetweenDates($start,$end);
foreach ( $res as $key=>$val ) {
	echo $val . " " . $key . "<br>";
}
*/
?>