<?php
// $Id: dblib.inc.php,v 1.14 2004/10/23 19:50:04 admin Exp $
/******************************************************************************
 * Theis Playlist Manager -- An interactive web application for creating,     *
 * editing, and publishing radio playlists.                                   *
 *                                                                            *
 * Copyright (C) 2003  Aaron Forrest                                          *
 *                                                                            *
 * This program is free software; you can redistribute it and/or              *
 * modify it under the terms of the GNU General Public License                *
 * as published by the Free Software Foundation; either version 2             *
 * of the License, or (at your option) any later version.                     *
 *                                                                            *
 * This program is distributed in the hope that it will be useful,            *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of             *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the              *
 * GNU General Public License for more details.                               *
 *                                                                            *
 * You should have received a copy of the GNU General Public License          *
 * along with this program; if not, write to the Free Software                *
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.*
 *****************************************************************************/

// connect to database
$link;
connectToDB();
function connectToDB()
 	{
	global $link;
	$link = mysql_connect( __DBHOST__, __DBUSER__, __DBPASSWORD__);
	if (! $link )
		die( "Couldn't connect to MySQL");
	mysql_select_db( __DBNAME__, $link )
		or die ("Couldn't open ultramo_wprb: ".mysql_error() );
	}

$dbLink = connectMysqli();

function connectMysqli() {
    $dbLink = new mysqli(__DBHOST__, __DBUSER__, __DBPASSWORD__, __DBNAME__);
    if ($dbLink->connect_errno) {
        die("Couldn't connect to MySQL using mysqli: ".$dbLink->connect_errno.": ".$dbLink->connect_error);
    }
    return $dbLink;
}

// function to get a row of table values according
// to input criteria

function getRow( $table, $fnm, $fval )
	{
	global $link;
	$result = mysql_query( "SELECT * FROM $table WHERE $fnm='$fval'", $link);
	if ( ! $result )
		die ("getRow fatal error: ".mysql_error() );
	return mysql_fetch_array( $result );
	}


// function to get previous row of table values according
// to input criteria

function getPrevRow( $table, $fnm, $fval, $fnm2=null, $fval2=null )
	{
	global $link;
	// first get the ID of the previous row
	$query0 = "SELECT MAX($fnm) FROM $table WHERE $fnm < $fval";
	if ($fnm2 && $fval2)
		$query0 .= " AND $fnm2 = $fval2";
	$result0 = mysql_query($query0, $link);
	if (! $result0)
		die( "getPrevRow() result0 fatal error: ".mysql_error() );
	$prev_array = mysql_fetch_array( $result0 );
	$prev_id = $prev_array[0];
	// now get all values from that row
	$query = "SELECT * FROM $table WHERE $fnm = '$prev_id'";
	$result = mysql_query( $query, $link );
	if ( ! $result )
		die( "getPrevRow() fatal error: ".mysql_error() );
	return mysql_fetch_array( $result );
	}

function getNextRow( $table, $fnm, $fval, $fnm2=null, $fval2=null )
	{
	global $link;
	// first get the ID of the next row
	$query0 = "SELECT MIN($fnm) FROM $table WHERE $fnm > $fval";
	if ($fnm2 && $fval2)
		$query0 .= " AND $fnm2 = $fval2";
	$result0 = mysql_query($query0, $link);
	if (! $result0)
		die( "getPrevRow() result0 fatal error: ".mysql_error() );
	$next_array = mysql_fetch_array( $result0 );
	$next_id = $next_array[0];
	// now get all values from that row
	$query = "SELECT * FROM $table WHERE $fnm = '$next_id'";
	$result = mysql_query( $query, $link );
	if ( ! $result )
		die( "getPrevRow() fatal error: ".mysql_error() );
	return mysql_fetch_array( $result );
	}
	
	
// function to convert string output to html

function html( $str)
	{
	if ( is_array($str) )
		{
		foreach ( $str as $key=>$val)
			$str[$key] = htmlstr( $val);
		return $str;
		}
	return htmlstr($str );
	}
function htmlstr( $str )
	{
	$str = htmlspecialchars($str );
	$str = nl2br($str);
	return $str;
	}


// function to write color options to selector form

function writeColorOptions($selected)
	{
	$colors = array("000000"=>"black", "FFFFFF"=>"white", "EEEEEE"=>"grey",
			"FF9999"=>"red", "9999FF"=>"blue", "99FF99"=>"green");
	foreach ($colors as $hexcode=>$color)
		{
		print "\t<option value=\"$hexcode\" ";
		print (($selected == $hexcode)?"SELECTED":"");
		print ">$color\n";
		}
	}

	
//functions to write days, hours, minutes, etc.

function writeWeekdayOptions($defday='')
	{
	$weekdays = array("Sunday", "Monday", "Tuesday", "Wednesday", 
				"Thursday", "Friday", "Saturday");
	$x = 0;
	foreach ($weekdays as $weekday)
		{
		print "\t\t<option value=\"$x\" ";
		print (($x == $defday)?"SELECTED":"");
		print ">$weekday\n";
		$x++;
		}
	}
	
function writeHourOptions($defhour = null)
	{
	for ($x=0; $x<24; $x++)
		{
		print "\t\t<option ";
		print (($x == $defhour)?"SELECTED":"");
		print ">$x\n";
		}
	}
	
function writeMinuteOptions($defmin = null)
	{
	$minutes = array("00", "30");
	$x = 0;
	foreach ($minutes as $minute)
		{
		print "\t\t<option ";
		print (($minute == $defmin)?"SELECTED":"");
		print ">$minute\n";
		$x++;
		}
	}
	
function writeMonthOptions($defmonth=null)
	{
	for ($x=1; $x<13; $x++)
		{
		print "\t\t<option ";
		print (($x == $defmonth)?"SELECTED":"");
		print ">$x\n";
		}
	}
function writeDayOptions($defday=null)
	{
	for ($x=1; $x<=31; $x++)
		{
		print "\t\t<option ";
		print (($x == $defday)?"SELECTED":"");
		print ">$x\n";
		}
	}
function writeYearOptions($defyear, $month, $day)
	{
	print "\t\t<option SELECTED>$defyear\n";
	if ($month == "12" && $day =="31")
		print "\t\t<option>".($defyear+1)."\n";
	}
	
function writeYearOptions2($defyear, $yearsback, $yearsfor)	{
	for ( $year = $defyear - $yearsback; $year <= $defyear + $yearsfor; $year++)	{
		print "\t\t<option ";
		print (($year == $defyear) ? "SELECTED" : "");
		print ">$year\n";
	}
}

function writeDurationOptions($defduration)
	{
	for ($x=0; $x<=24; $x++)
		{
		for ($y=0; $y<=0.5; $y+=.5)
			{
			$val = ($x + $y);
			print "<option value=\"$val\" ";
			print (($val==$defduration)?"SELECTED":"");
			print (($y == 0)?">$x:00\n":">$x:30\n");
			}
		}
	}

function writeGenreOptions($defgenre)
	{
	$genres = array("Rock/etc", "Classical", "Jazz", "Specialty");
	foreach ($genres as $genre)
		{
		print "\t\t<option ";
		print (($genre == $defgenre)?"SELECTED":"");
		print ">$genre\n";
		}
	}
	
function writeEmphOptions($emph="")
	{
	$emph_array = array("NE", "N", "OE");
	print "<option value=\"\">none\n";
	foreach ($emph_array as $option)
		{
		print "<option ";
		print (($option == $emph)?"SELECTED":"");
		print ">$option\n";
		}
	}

function writeSearchOptions($default='')	{
	$fields = array("all", "artist", "song", "album", "label", "ensemble", 
					"conductor", "performer");
	foreach ($fields as $field)	{
		print "<option ";
		print (($field == $default)?"SELECTED":"");
		print ">$field\n";
	}
}

function writeSeasonOptions($default='')	{
	$months = array("September"=>0, "October"=>0, "November"=>0, "December"=>0, "January"=>0,
					"February"=>1, "March"=>1, "April"=>1, "May"=>1, "June"=>2, "July"=>2,
					"August"=>2);
	$current = $months[$default];
	$seasons = array("Fall", "Spring", "Summer");
	foreach($seasons as $key=>$season)	{
		print "\t<option value='$key' ";
		print (($key == $current) ? "SELECTED":"");
		print ">$season\n";
	}
}


// write form to input show details

function setShowDetails($id=null)
	{
	global $session;
	if (! $id)	{
		$def = getRow("users", "loginsID", $session[id]);
		$vals = array("djname"=>$def[defdjname], "title"=>$def[deftitle],
				"subt"=>$def[defsubtitle], "genre"=>$def[defgenre],
				"othgenre"=>$def[defothergenre], "start"=>time(),
				"duration"=>2);
	}
	else	{
		$def = getRow("shows", "ID", $id);
		$vals = array("djname"=>$def[djname], "title"=>$def[title],
				"subt"=>$def[subtitle], "genre"=>$def[genre],
				"othgenre"=>$def[othergenre], "start"=>$def[starttime],
				"duration"=>$def[duration]);
	}
	$date = getDate($vals[start]);
	print "<FORM method=\"POST\" name=\"the_form\">\n";
	print " <input type=\"hidden\" name=\"action\" value=\"start\">\n";
	print "<p><table border=\"0\" cellspacing=\"5\">\n";
	print "\t<tr>\n";
	print "\t\t<td class='mid'>DJ Name:</td>\n";
	print "\t\t<td class='mid'><input type=\"text\" name=\"form[djname]\" maxlength=\"50\" value=\"$vals[djname]\"></td>\n";
	print "\t</tr><tr>\n";
	print "\t\t<td class='mid'>Show title:</td>\n";
	print "\t\t<td class='mid'><input type=\"text\" name=\"form[title]\" value=\"$vals[title]\"></td>\n";
	print "\t</tr><tr>\n";
	print "\t\t<td class='mid'>Subtitle [optional]:</td>\n";
	print "\t\t<td class='mid'><input type=\"text\" name=\"form[subtitle]\" value=\"$vals[subt]\"></td>\n";
	print "\t</tr><tr>\n";
	print "\t\t<td class='mid'>Genre:</td>\n";
	print "\t\t<td class='mid'><select name=\"form[genre]\" onChange=\"document.the_form.othergenre.value=this.value;\">";
			writeGenreOptions($vals[genre]);
	print "</select></td>\n";
	print "\t</tr><tr>\n";
	print "\t\t<td class='mid'>&nbsp;&nbsp;&nbsp;&nbsp;Sub-genre:</td>";
	print "\t\t<td class='mid'><input type=\"text\" name=\"othergenre\" value=\"$vals[othgenre]\"></td>\n";
	print "\t</tr><tr>\n";
	print "\t\t<td class='mid' colspan=\"2\">Start time:<br>\n";
	print "\t\t\t<table cellspacing=\"5\" cellpadding=\"3\"><tr>
			<td class='mid'>Hour</td><td class='mid'>Minute</td><td class='mid'>Month / Day / Year</td></tr>\n";
	print "\t\t\t<tr><td><select name=\"form[hour]\">";
			writeHourOptions($date[hours]);
	print "</select> :</td>\n";
	print "\t\t\t<td><select name=\"form[minute]\">";
			writeMinuteOptions($date[minutes]);
	print "</select></td>\n";
	print "\t\t\t<td><select name=\"form[month]\">";
			writeMonthOptions($date[mon]);
		print "</select> / <select name=\"form[day]\">";
			writeDayOptions($date[mday]);
		print "</select> / <select name=\"form[year]\">";
			if ( basename($_SERVER['PHP_SELF']) == "newplaylist.php")
				writeYearOptions($date[year], $date[mon], $date[mday]);
			else
				writeYearOptions2($date[year], $date[year]+1, $date[year]-2003);
		print "</select></td></tr></table</td>\n";
	print "\t</tr><tr>\n";
	print "\t\t<td class='mid'>Duration:&nbsp;&nbsp;\n";
	print "\t\t<select name=\"form[duration]\">";
			writeDurationOptions($vals[duration]);
	print "</select></td>\n";
	print "\t</tr>\n";
	print "</table></p>\n";
	print "<p>\n";
	print " <input type=\"submit\" value=\"Submit\"></p><p>\n";
	print " <input type=\"reset\" value=\"Reset form\"</br></p>\n";
	print "</form>\n";
	
	print "<p>&nbsp;</p><p class='text'>NOTE: Information entered on this page will be published to the internet</p>";
	}


// function to send new show details to db

function newShow($users_id, $starttime, $duration, $djname, $title,
				$subtitle, $genre, $othergenre)
	{
	global $link;
	if (empty($othergenre))
		$othergenre = $genre;
	if (empty($djname))
		{
		$query = "SELECT firstname FROM users WHERE loginsID=$users_id";
		$result = mysql_query($query, $link);
		if ( ! $result )
			die( "newShow() fatal error: ".mysql_error() );
		$array = mysql_fetch_row($result);
		$djname = $array[0];
		}
	$query = "INSERT INTO shows (userID, starttime, duration,
			djname, title, subtitle, genre, othergenre)";
	$query .= "VALUES ('$users_id', $starttime, $duration,
			'$djname', '$title', '$subtitle', '$genre', '$othergenre')";
	$result = mysql_query($query, $link);
	if ( ! $result )
		die ("newShow() fatal error: ".mysql_error() );
	return mysql_insert_id($link);
	}


// function to start new show with default settings

function defaultShow($id)
	{
	global $link;
	$defaults = getRow("users", "loginsID", $id);
	if (empty($defaults[othergenre]))
		$defaults[othergenre] = $defaults[genre];
	if (empty($defaults[defdjname]))
		{
		$defaults[defdjname] = $defaults[firstname];
		}
	// check if the default show time is today
	if (! $starttime = nextDefaultDay($defaults[defday], $defaults[defhour], $defaults[defmin]))	{
		return false;
	}
	$query = "INSERT INTO shows (userID, starttime, duration,
			djname, title, subtitle, genre, othergenre)";
	$query .= "VALUES ('$defaults[ID]', '$starttime',
				'$defaults[defduration]', '$defaults[defdjname]',
				'$defaults[deftitle]', '$defaults[defsubtitle]',
				'$defaults[defgenre]', '$defaults[defothergenre]')";
	$result = mysql_query($query, $link);
	if (! $result)
		die( "defaultShow() fatal error: ".mysql_error() );
	return mysql_insert_id($link);
	}


// function to find the next default day and insert it into
// current playlist

function nextDefaultDay($day, $hour, $min)
	{
	$current = getdate();
	if ($current[wday] == $day)
		$starttime = mktime($hour, $min, 0, $current[mon],
							$current[mday], $current[year]);
	elseif ($current[wday] < $day && ($day - $current[wday]) == 1) {
		$add = ($current[wday] + 7 ) - $day;
		$starttime = mktime($hour, $min, 0, $current[mon], 
					($current[mday] + $add), $current[year]);
		}
	elseif ($current[wday] > $day && ($current[wday] - $day) == 1)	{
		$sub = $current[wday] - $day;
		$starttime = mktime($hour, $min, 0, $current[mon],
							($current[mday] - $sub),$current[year]);
		}
	else		{
		return false;
		}
	return $starttime;
	}


// function to write playlist display/enter form in 
// html table format

function writePlaylistForm($show_id, $cell="DDDDDD", $nonew="") {
	//get the data
	global $dbLink;
	$query = "SELECT * FROM playlist WHERE showID = ? ORDER BY orderkey"; // TODO replace select * with explicit column list
    $stmt = $dbLink->prepare($query);
    $stmt->bind_param('i', $show_id);
    $stmt->execute() || dieFromSQLError("writePlaylistForm()", $stmt->errno, $stmt->error);

	$result = $stmt->get_result();
	$played = array();
	while ($row = $result->fetch_array()) {
		array_push($played, $row);
    }
	// print the table headers
	print "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"2\">\n";
	print "\t<tr>\n\t\t<td colspan='9' align='right'><font size='-2'>R = request, C = Compilation</font></td>\n\t</tr>\n";
	print "\t<tr>\n";
	print "\t\t<th bgcolor=\"#FFFFFF\">&nbsp;</th>\n";
	print "\t\t<th bgcolor=\"#CCCCCC\">Artist</th>\n";
	print "\t\t<th bgcolor=\"#CCCCCC\">Song</th>\n";
	print "\t\t<th bgcolor=\"#CCCCCC\">Album</th>\n";
	print "\t\t<th bgcolor=\"#CCCCCC\">Label</th>\n";
	print "\t\t<th bgcolor=\"#CCCCCC\">Comments</th>\n";
	print "\t\t<th bgcolor=\"#CCCCCC\">Emph</th>\n";
	print "\t\t<th bgcolor=\"#CCCCCC\">R</th>\n";
	print "\t\t<th bgcolor=\"#CCCCCC\">C</th>\n";
	print "\t</tr>\n";
	//print the data
	foreach ($played as $row)
		{
			print "\t<tr>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\"><input type=\"checkbox\" name=\"form[flag]\" value=\"$row[ID]\"></td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\">$row[artist]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\">$row[song]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\">$row[album]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\">$row[label]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\" width=\"150\">$row[comments]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\">$row[emph]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\">";
			print (($row[request])?"R":"");
			print "</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$cell\">";
			print (($row[comp])?"C":"");
			print "</td>\n";
			print "\t</tr>\n";
		}
	// print the entry form
	if ( $nonew != "nonew")
		{
		print "\t<tr>\n";
		print "\t\t<td bgcolor=\"#$cell\">&nbsp;</td>\n";
		print "\t\t<td bgcolor=\"#$cell\"><input type=\"text\" name=\"artist\"></td>\n";
		print "\t\t<td bgcolor=\"#$cell\"><input type=\"text\" name=\"form[song]\"></td>\n";
		print "\t\t<td bgcolor=\"#$cell\"><input type=\"text\" name=\"form[album]\"></td>\n";
		print "\t\t<td bgcolor=\"#$cell\"><input type=\"text\" name=\"form[label]\" size=\"10\"></td>\n";
		print "\t\t<td bgcolor=\"#$cell\"><input type=\"text\" name=\"form[comments]\"></td>\n";
		print "\t\t<td bgcolor=\"#$cell\"><select name=\"form[emph]\"><option value=\"\">none<option>NE<option>N<option>OE</select></td>\n";
		print "\t\t<td class='mid' bgcolor=\"#$cell\">R: <input type=\"checkbox\" name=\"form[request]\" value=\"1\"></td>\n";
		print "\t\t<td class='mid' bgcolor=\"#$cell\">C: <input type=\"checkbox\" name=\"form[comp]\" value=\"1\"></td>\n";
		print "\t</tr>\n";
		}
	print "</table>\n";
	}


// function to add line to playlist table in db

function writePlaylistLine($id, $artist, $song, $album, $label,
                                $comments, $emph, $request, $comp) {
    global $dbLink;
    $query = "INSERT INTO playlist (showID, artist, song, album, label, comments, emph, request, comp)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // prepare the statement
    $stmt = $dbLink->prepare($query);

    // bind in the variables
    $is_request = $request ? 1 : 0;
    $is_comp = $comp ? 1 : 0;
    $stmt->bind_param('issssssii', $id, $artist, $song, $album, $label, $comments, $emph, $is_request, $is_comp);

    // execute the query
    $stmt->execute() || die("writePlaylistLine() Error inserting row: ".$stmt->errno.": ".$stmt->error);
    // get the key
    $k = $stmt->insert_id;
    // update the fresh row with the key
    $stmt = $dbLink->prepare("UPDATE playlist SET orderkey=? WHERE ID=?");
    $stmt->bind_param('ii', $k, $k);
    $stmt->execute() || die("writePlaylistLine() Error updating row: ".$stmt->errno.": ".$stmt->error);
    // return the key
    return $k;
}

function writeClassicalPlaylistLine($id, $composer, $song, $ensemble, 
					$conductor, $performer, $label, $comments, $emph)	{
    global $dbLink;
	$query = "INSERT INTO playlist (showID, artist, song, ensemble, conductor, performer, label, comments, emph)
	          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $dbLink->prepare($query);

    $stmt->bind_param('issssssss', $id, $composer, $song, $ensemble, $conductor, $performer, $label, $comments, $emph);

    $stmt->execute() || die("writeClassicalPlaylistLine() Error inserting row: ".$stmt->errno.": ".$stmt->error);

	$k = $stmt->insert_id;

    $stmt = $dbLink->prepare("UPDATE playlist SET orderkey=? WHERE ID=?");
    $stmt->bind_param('ii', $k, $k);
    $stmt->execute() || die("writeClassicalPlaylistLine() Error updating row: ".$stmt->errno.": ".$stmt->error);
	return $k;
}

// function to update show details
function updateDetails($showID, $starttime, $dur, $djname, $title, $subt,
									$genre, $othgenre)	{
	global $dbLink;
	$query = "UPDATE shows SET starttime=?, duration=?, djname=?, title=?, subtitle=?, genre=?, othergenre=? WHERE ID=?";
    $stmt = $dbLink->prepare($query);
    $stmt->bind_param('iisssssi', $starttime, $dur, $djname, $title, $subt, $genre, $othgenre, $showID);
    $stmt->execute() || dieFromSQLError("updateDetails()", $stmt->errno, $stmt->error);
	return;
}

// function to move a playlist entry up one row
function shiftUp($id)
	{
	global $link;
	$row = getRow("playlist", "ID", $id);
	$prev_row = getPrevRow("playlist", "ID", $id, "showID", $row[showID]);
	// check if there even _is_ a previous row
	if ( empty($prev_row))	{
		$message = "Already at the top<br>\n";
		return $message;
	}
	foreach ($row as $key=>$val)
		$row[$key] = addslashes($val);
	foreach ($prev_row as $key=>$val)
		$prev_row[$key] = addslashes($val);	
	$query1 = "UPDATE playlist SET artist=\"$prev_row[artist]\",
			song=\"$prev_row[song]\", album=\"$prev_row[album]\",
			label=\"$prev_row[label]\", comments=\"$prev_row[comments]\",
			emph=\"$prev_row[emph]\", request=\"$prev_row[request]\",
			comp=\"$prev_row[comp]\" WHERE ID=\"$id\"";
	$result1 = mysql_query( $query1, $link );
	if (! $result1)
		die( "shiftUp() [result1] fatal error: ".mysql_error() );
	$query2 = "UPDATE playlist SET artist=\"$row[artist]\",
			song=\"$row[song]\", album=\"$row[album]\",
			label=\"$row[label]\", comments=\"$row[comments]\",
			emph=\"$row[emph]\", request=\"$row[request]\",
			comp=\"$row[comp]\" WHERE ID=\"$prev_row[ID]\"";
	$result2 = mysql_query( $query2, $link);
	if (! $result2)
		die( "shiftUp() [result2] fatal error: ".mysql_error() );
	}
	
// function to move playlist entry down one row

function shiftDown($id)
	{
	global $link;
	$row = getRow("playlist", "ID", $id);
	$next_row = getNextRow("playlist", "ID", $id, "showID", $row[showID]);
	// check if there even _is_ a next row
	if ( empty($next_row) )	{
		$message = "Already at the bottom<br>\n";
		return $message;
	}
	foreach ($row as $key=>$val)
		$row[$key] = addslashes($val);	
	foreach ($next_row as $key=>$val)
		$next_row[$key] = addslashes($val);	
	$query1 = "UPDATE playlist SET artist=\"$next_row[artist]\",
			song=\"$next_row[song]\", album=\"$next_row[album]\",
			label=\"$next_row[label]\", comments=\"$next_row[comments]\",
			emph=\"$next_row[emph]\", request=\"$next_row[request]\",
			comp=\"$next_row[comp]\" WHERE ID=\"$id\"";
	$result1 = mysql_query( $query1, $link);
	if (! $result1)
		die( "shiftDown() [result1] fatal error: ".mysql_error() );
	$query2 = "UPDATE playlist SET artist=\"$row[artist]\",
			song=\"$row[song]\", album=\"$row[album]\",
			label=\"$row[label]\", comments=\"$row[comments]\",
			emph=\"$row[emph]\", request=\"$row[request]\",
			comp=\"$row[comp]\" WHERE ID=\"$next_row[ID]\"";
	$result2 = mysql_query( $query2, $link);
	if (! $result2)
		die( "shiftDown() [result2] fatal error: ".mysql_error() );
	}


// function to write show information

function writeShowInfo($id)
	{
	global $link;
	global $session;
	// first get the info from the database
	$showinfo = getRow("shows", "ID", $id);
	$userinfo = getRow("users", "loginsID", $session[id]);
	(! empty($showinfo[title])) ? print "<h2>$showinfo[title]</h2><h3>$showinfo[subtitle]</h3>\n" : 
		print "<h2>$showinfo[genre]</h2>";
	print "<h3> with $showinfo[djname]</h3>\n";
	print "<span class='text'>other shows [link]</span><p>\n";
	$end = $showinfo[starttime] + $showinfo[duration]*60*60;
	print "<span class='text'>";
	print date('l, F j, Y', $showinfo[starttime])."<br>\n";
	print date('H:i', $showinfo[starttime])." to ".date('H:i', $end);
	(! empty($showinfo[genre])) ? print "<br>$showinfo[othergenre]<br>\n":''; 
	print "</span>";
	
	// print out an email link
	($userinfo[emailpublish])? print "<span class='text'>
		$userinfo[email] [link]<br></span>": 
		print '';
	(! empty($userinfo[link])) ? print "<span class='text'>
		$userinfo[link] [link]</text>":
		print '';
	}


// function to write playlist [no form]

function writePlaylist($id, $tblhead="CCCCCC", $tblcolor="DDDDDD", $tbltext="000000") {
	global $dbLink;
	$query = "SELECT * FROM playlist WHERE showID = ? ORDER BY ID";
    $stmt = $dbLink->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute() || dieFromSQLError("writePlaylist()", $stmt->errno, $stmt->error);

	$result = $stmt->get_result();

	$played = array();
	while ($row = $result->fetch_array()) {
		array_push($played, $row);
    }
	// print the table headers
	print "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"2\" style=\"color: #$tbltext\">\n";
	print "\t<tr>\n";
	print "\t\t<th bgcolor=\"#$tblhead\">Artist</th>\n";
	print "\t\t<th bgcolor=\"#$tblhead\">Song</th>\n";
	print "\t\t<th bgcolor=\"#$tblhead\">Album</th>\n";
	print "\t\t<th bgcolor=\"#$tblhead\">Label</th>\n";
	print "\t\t<th bgcolor=\"#$tblhead\">Comments</th>\n";
	print "\t\t<th bgcolor=\"#$tblhead\">Emph</th>\n";
	print "\t\t<th bgcolor=\"#$tblhead\">Request</th>\n";
	print "\t\t<th bgcolor=\"#$tblhead\">Comp</th>\n";
	print "\t</tr>\n";
	//print the data
	foreach ($played as $row)
		{
			print "\t<tr>\n";
			print "\t\t<td class='mid' bgcolor=\"#$tblcolor\">$row[artist]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$tblcolor\">$row[song]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$tblcolor\">$row[album]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$tblcolor\">$row[label]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$tblcolor\" width=\"150\">$row[comments]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$tblcolor\">$row[emph]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#$tblcolor\">";
			print (($row[request])?"R":"");
			print "</td>\n";
			print "\t\t<td bgcolor=\"#$tblcolor\">";
			print (($row[comp])?"C":"");
			print "</td>\n";
			print "\t</tr>\n";
		}
	print "</table>";
	return $query;
}

	
// function to output the top plays in a specified period of time in a table to the browser
function printTopPlays($starttime, $endtime, $genre, $emph, $sortby="artist")	{
	global $link;
	$query = "SELECT artist, song, label, album, emph, comp, starttime, duration, djname ";
	$query .= "FROM playlist, shows WHERE playlist.showID = shows.ID";
	$query .= " AND shows.starttime >= '$starttime' AND shows.starttime <= '$endtime'";
	if ($genre != 'any')
		$query .= " AND genre = '$genre' ";
	if ( ! empty($emph) )
		($emph=='new') ? $query .= " AND (emph='NE' or emph='N')": $query .= " AND emph='OE'";
	$query .= " ORDER BY artist";
	$result = mysql_query($query, $link);
	if (! $result)
		die("printTopPlays() fatal error: ".mysql_error());
	$plays = array();
	while ($row = mysql_fetch_assoc($result))
		array_push($plays, $row);
	$plays = sort_stripThe($plays, $sortby);
	// print the table headers
	
	//print the data
	foreach ($plays as $row)	{
		if ($row[artist] != "*****")	{
			// create a string to display showtime
			$showend = $row[starttime] + $row[duration]*3600;
			$showtime = date('l, F j, Y, H:i', $row[starttime])." - ".date('H:i', $showend);
			print "\t<tr>\n";
			print "\t\t<td class='mid' bgcolor=\"#EEEEEE\">$row[artist]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#EEEEEE\">$row[song]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#EEEEEE\">$row[album]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#EEEEEE\">$row[label]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#EEEEEE\">$row[emph]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#EEEEEE\">";
			print (($row[comp])?"C":"");
			print "\t\t<td class='mid' bgcolor=\"#EEEEEE\">$showtime</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#EEEEEE\">$row[djname]</td>\n";
			print "</td>\n";
			print "\t</tr>\n";
		}
	}
	return $query;
}

// function to compare two multidimensional arrays by their 1nth element
// for use by usort()
function cmp($a, $b)	{
		return strcmp(strtolower($a[1]), strtolower($b[1]));
}

// function to sort an array without any leading 'the'
function sort_stripThe($row_arr, $sortby)	{
	$sort_arr = array();
	foreach($row_arr as $row)	{
		$index = (strpos(strtolower($row[$sortby]), "the ") === 0) ?
				substr($row[$sortby], 4) : $row[$sortby];
		$sort_arr[] = array($row, $index);
	}
	usort($sort_arr, "cmp");
	$ret_arr = array();
	foreach ($sort_arr as $row)
		array_push($ret_arr, $row[0]);
	return $ret_arr;
}

// function to send a comma-delimited list of the top plays to specified email address
function mailQueryResults($to, $subject, $lastquery, $start=null, $end=null,
		$genre='', $from=null,
		$comments=false)	{
	global $link;
	$lastquery = stripcslashes($lastquery);
	$result = mysql_query($lastquery, $link);
	if (! $result)
		die("mailTop30() fatal error: ".mysql_error());
	$plays = array();
	while ($row = mysql_fetch_assoc($result))
		array_push($plays, $row);
	if ( empty($from) )
		$from = "Playlist Email Bot <$to>";
	if ( ! empty( $comments ))	{
		$message = "$comments\n";
		$message .= "********************************************************************************\n";
	}
	if (! empty($start) && ! empty($end))
		$message .= "$genre Playlist for $start[mon]/$start[mday]/$start[year] - $end[mon]/$end[mday]/$end[year]\n\n";
	else $message .= "$subject\n\n";
	
	foreach ($plays as $row)	{
		if ($row[artist] !="*****")	{
			$message .= "$row[artist], $row[song], $row[album], $row[label], $row[emph], ";
			$message .= (($row[comp])?"C":"");
			$message .= "\n";
		}
		}
	mail($to, $subject, $message, "From: $from");
}


// function to search database at large
// TODO find callers, limit searchfield to some constants, escape searchstring
function searchDatabase($searchstring, $searchfield, $start, $end, $users, $genres, $emphs, $comp, $req, $fuzzy, $page)	{
	global $link;
	// section to return list of shows by that dj if 'username' searchfield is chosen
	if ($searchfield == "username")	{
		
		$result = mysql_query("SELECT users.ID FROM users, logins WHERE logins.ID = loginsID
						AND login='$searchstring'", $link);
		if (! $result)
			die("searchDatabase() fatal error: ".mysql_error());
		$login_row = mysql_fetch_row($result);
		return $login_row[0];
	}
	
	if ($searchfield == "all")
		$searchfield = 'artist, song, album, label';  // searches all relevant fields
		
	// ******* build queries depending on whether or not fuzzy search option is checked *******
	$searchstrict = "MATCH ($searchfield) AGAINST ('$searchstring') ";
	if ($fuzzy == 'indeed')	{
		if ($searchfield == 'artist, song, album, label')	{
			$searchstrict = "(artist LIKE '%$searchstring%' OR song LIKE '%$searchstring%' OR 
								album LIKE '%$searchstring%' OR label LIKE '%$searchstring%') ";
		}
		else
			$searchstrict = "$searchfield LIKE '%$searchstring%' ";
	}
		
	// ******* BUILD THE QUERY *******
	$query = "SELECT artist, song, album, label, emph, comp, request, login ";
	$query .= "FROM logins, users, shows, playlist WHERE $searchstrict ";
	$query .= "AND playlist.showID = shows.ID AND shows.userID = users.ID ";
	$query .= "AND users.loginsID = logins.ID AND artist!='*****' ";
	$query .= "AND shows.starttime>=$start AND shows.starttime<=$end ";
	if ($users[0] != '')	{
		$query .= "AND (login='$users[0]' ";
		foreach ($users as $user)
			$query .= "OR login='$user' ";
		$query .= ") ";
	}
	if ($genres[0] != '')	{
		$query .= "AND (genre='$genres[0]' ";
		foreach ($genres as $genre)
			$query .= "OR genre='$genre' ";
		$query .= ") ";
	}
	if ($emphs[0] != '')	{
		$query .= "AND (emph='$emphs[0]' ";
		foreach ($emphs as $emph)
			$query .= "OR emph='$emph' ";
		$query .= ") ";
	}
	if ($comp == 'checked')
		$query .= "AND comp=1 ";
	if ($req == 'checked')
		$query .= "AND request=1 ";
	// $query .= "IN BOOLEAN MODE";  // uncomment line when mysql 4.0.1 becomes avail.
	$query .= "LIMIT $page, 100";
	
	// ******* SEND THE QUERY TO MYSQL *******
	$result = mysql_query($query, $link);
	if (! $result)
		die("searchDatabase() fatal error: ".mysql_error());
	$searchresults = array();
	while ($row = mysql_fetch_assoc($result))	
		array_push($searchresults, $row);
	return $searchresults;
}

// function to print results of searchDatabase as a table
function printSearchResults($results)	{
 	print "<table cellpadding='2'>\n";
	print "\t<tr>\n";
	print "\t\t<th bgcolor='#CCCCCC'>Artist</th>\n";
	print "\t\t<th bgcolor='#CCCCCC'>Song</th>\n";
	print "\t\t<th bgcolor='#CCCCCC'>Album</th>\n";
	print "\t\t<th bgcolor='#CCCCCC'>Label</th>\n";
	print "\t\t<th bgcolor='#CCCCCC'>Emph</th>\n";
	print "\t\t<th bgcolor='#CCCCCC'>Comp</th>\n";
	print "\t\t<th bgcolor='#CCCCCC'>Request</th>\n";
	print "\t\t<th bgcolor='#CCCCCC'>Played by</th>\n";
	print "\t</tr>\n";
	foreach ($results as $row)	{
		print "\t<tr>\n";
		print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row[artist]\n</td>\n";
		print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row[song]\n</td>\n";
		print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row[album]\n</td>\n";
		print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row[label]\n</td>\n";
		print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row[emph]\n</td>\n";
		print "\t\t<td class='mid' bgcolor='#EEEEEE'>";
			print (($row[comp])?"C\n</td>\n":"\n</td>\n");
		print "\t\t<td class='mid' bgcolor='#EEEEEE'>";
			print (($row[request])?"R\n</td>\n":"\n</td>\n");
		print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row[login]\n</td>\n";
		print "\t</tr>\n";
	}
	print "</table>"; 
}

// function to generate a list of playlists for a particular user
function writeListOfPlaylists($id, $target='editplaylist.php?') {
	global $dbLink, $session;
	$query = "SELECT * FROM shows WHERE userID=? ORDER BY starttime DESC";
    $stmt = $dbLink->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute() || dieFromSQLError("writeListOfPlaylists()", $stmt->errno, $stmt->error);

	$result = $stmt->get_result();

	$shows = array();
	while ( $rows = $result->fetch_array())
		array_push( $shows, $rows );
	print "<table>\n";
	foreach ($shows as $row)
		{
		print "\t<tr>\n";
		getdate($row[starttime]);
		print "\t\t<td class='text'><a href=\"".$target."show_id=$row[ID]\">";
		print date('l, F j, Y, H:i', $row[starttime]);
		print "</a></td>\n";
		
		print (($session[login]!='admin')? "\t\t<td class='text' align='right' width='63'>
				<a href='editdetails.php?show_id=$row[ID]'>details</a></td>\n":"");
		print "\t\t<td class='text' align='right' width='63'>
				<a href='deletepl.php?show_id=$row[ID]'
				onClick=\"return window.confirm('Delete this playlist?')\"><b>[delete]</b></a>
				</td>\n";
		print "\t</tr>\n";
		}
	print "</table>\n";
}
	
function deletePlaylist($id)	{
    global $dbLink;
	$query1 = "DELETE FROM playlist WHERE playlist.showID=?";
    $stmt = $dbLink->prepare($query1);
    $stmt->bind_param('i', $id);
    $stmt->execute() || dieFromSQLError("deletePlayList()", $stmt->errno, $stmt->error);

	$query2 = "DELETE FROM shows WHERE ID=?";
    $stmt = $dbLink->prepare($query2);
    $stmt->bind_param('i', $id);
    $stmt->execute() || dieFromSQLError("deletePlayList()", $stmt->errno, $stmt->error);

	return;
}

function dieFromSQLError($functionName, $errno, $errorMessage) {
    die($functionName." fatal error: ".$errno." ".$errorMessage);
}

function is_url($url)	{
	$arr = parse_url($url);
	if (empty($arr[scheme]) || ($arr[scheme] != "http" && $arr[scheme] != "https"))
		return false;
	if (empty($arr[host]))
		return false;
	return true;
}

function trimEnd($str, $tail)	{
	if( $tail_pos = strpos($str, $tail) )	{
		return substr($str, 0, ($tail_pos - 1));
	}
	else return $str;
}
	
?>
