<?php
// $Id: editplaylist.php,v 1.8 2004/08/31 15:20:38 admin Exp $
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

include("../lib/config.inc.php");
include("../lib/dblib.inc.php");
include("../lib/usrlib.inc.php");

import("org/wprb/Playlist.php");
import("org/wprb/PlaylistLine.php");

checkUser();
checkGuestUser();

// check that the right person is editing this show
$showrow = getRow("shows", "ID", $show_id);
if ($showrow[userID] != fetchUserID())
	header( "Location: oldplaylists.php" );

// run validity tests and submit line to db
if ( isset($submitLine) || (isset($def_action) && $def_action == "newline" &&
		! isset($break) && ! isset($_POST[flag])))	{
	$message = "";
	if (empty($_POST[artist]))
		$message .= "You must fill in an artist<Br>\n";
	if (empty($_POST[song]))
		$message .= "You must fill in a song title.<br>\n";
	if (empty($_POST[album]) && $showrow[genre] != "Classical")
		$message .= "You must fill in an album title. For albums with no title type \"s/t\".<br>\n";
	if ($message == "")	{ // no errors
		if ( $showrow[genre] != "Classical" )
			writePlaylistLine($show_id, $_POST[artist], $_POST[song],
						$_POST[album], $_POST[label], $_POST[comments],
						$_POST[emph], $_POST[request], $_POST[comp]);
		else
			writeClassicalPlaylistLine($show_id, $_POST[artist], $_POST[song],
					$_POST[ensemble], $_POST[conductor], $_POST[performer], 
					$_POST[label], $_POST[comments], $_POST[emph] );
	}
}

// write a set break line
if ( isset($break))
	writePlaylistLine($show_id, "*****", "BREAK", "*****", 
									null,$_POST[comments],null,null,null);

// manipulate line
if (isset($manipulate) && isset($_POST[flag]))	{
	if ( $_POST[action] == "Edit")	{
		$loc = (($showrow[genre]=="Classical") ? "&genre=Classical" : "");
		header ( "Location: editentry.php?id=$_POST[flag]".$loc );
	}
	
	$line = new PlaylistLine( $_POST[flag] );
	if ( $_POST[action] == "Delete")	{
		$line->delete();
	}
	if ( $_POST[action] == "Shift up" )	{
		if (! empty($_POST[shift_lines]) && is_numeric($_POST[shift_lines]) )	{
			while ( (int) $_POST[shift_lines]-- > 0 && $line->shiftUp() ){}
		}
		else $message .= "Number of lines must be greater than 0";
	}
	if ( $_POST[action] == "Shift down")	{
		if (! empty($_POST[shift_lines]) && is_numeric($_POST[shift_lines]) )	{
			while ( (int) $_POST[shift_lines]-- > 0 && $line->shiftDown() ){}
		}
		else $message .= "Number of lines must be greater than 0";
	}
}

// instantiate a Playlist object to fetch the already-entered playlist data
$playlist = new Playlist( $show_id );
$playlist->reset();
?>
<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>Playlist editor</title>
<link href="../css/wprb.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
if ($message != "" )
	print "<b>$message</b><p />";
include("usernav.inc");
print "<form method=\"POST\">\n";
print "<input type=\"hidden\" name=\"show_id\" value=\"$show_id\" />\n";
print "<input type=\"hidden\" name=\"def_action\" value=\"newline\" />\n";
if ( $showrow[genre] != "Classical" )
	$headers = array("artist"=>"Artist", "song"=>"Song", "album"=>"Album", 
				"label"=>"Label", "comments"=>"Comments", "emph"=>"Emph",
				"request"=>"R", "comp"=>"C");
else $headers = array("artist"=>"Composer", "song"=>"Piece", 
				"ensemble"=>"Ensemble/<br>Orchestra", "conductor"=>"Conductor", 
				"performer"=>"Soloist/<br>Performer", "label"=>"Label", 
				"comments"=>"Comments", "emph"=>"Recently<br>Released");

print "<table width='100%' cellpadding='3' cellspacing='2'>\n";
print "\t<tr>\n";
print "\t\t<th bgcolor=\"#FFFFFF\">&nbsp;</th>\n";

// first print the headers row
foreach ( $headers as $header )	{
	print "\t\t<th bgcolor=\"#CCCCCC\">$header</th>\n";
}

print "\t</tr>\n";

// now print the existing playlist content

while ( $playlist->hasNext() )	{
	$row = $playlist->next();
	print "\t<tr>\n";
	print "\t\t<td bgcolor=\"#EEEEEE\"><input type='checkbox' name='flag'
			value=\"$row->ID\">\n";
	foreach ( $headers as $key=>$header )	{
		if ( $key == "request" || $key == "comp" )	{
			print "\t\t<td bgcolor=\"EEEEEE\" class=\"text\">";
			print (( ! $row->$key) ? "&nbsp;" : $header );
			print "</td>\n";
			continue;
		}
		print "\t\t<td bgcolor=\"EEEEEE\" class=\"text\">".$row->$key."</td>\n";
	}
	print "\t</tr>\n";
}
?>

<tr>
<?php
if ($showrow[genre] != "Classical")	{
	?>
	<!-- Playlist Entry Form -->
	<td bgcolor="#EEEEEE">&nbsp;</td>
	<td bgcolor="#EEEEEE"><input type="text" name="artist" 
		<?= ((! empty($message) && isset($_POST[artist]))?"value='$_POST[artist]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="song" 
		<?= ((! empty($message) && isset($_POST[song]))?"value='$_POST[song]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="album" 
		<?= ((! empty($message) && isset($_POST[album]))?"value='$_POST[album]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="label" 
		<?= ((! empty($message) && isset($_POST[label]))?"value='$_POST[label]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="comments" 
		<?= ((! empty($message) && isset($_POST[comments]))?"value='$_POST[comments]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><select name="emph">
		<option value="" />none
		<option />NE
		<option />N
		<option />OE
		</select></td>
	<td bgcolor="#EEEEEE" class=\"text\">R: <input type="checkbox" name="request" value="1"
		<?= ((! empty($message) && $_POST[request])?"checked":"") ?> /></td>
	<td bgcolor="#EEEEEE" class=\"text\">C: <input type="checkbox" name="comp" value="1" 
		<?= ((! empty($message) && $_POST[comp])?"checked":"") ?> /></td>
<?php	}
else	{
	?>
	<!-- Playlist Entry Form -->
	<td bgcolor="#EEEEEE">&nbsp;</td>
	<td bgcolor="#EEEEEE"><input type="text" name="artist" 
		<?= ((! empty($message) && isset($_POST[artist]))?"value='$_POST[artist]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="song" 
		<?= ((! empty($message) && isset($_POST[song]))?"value='$_POST[song]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="ensemble" 
		<?= ((! empty($message) && isset($_POST[ensemble]))?"value='$_POST[ensemble]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="conductor" 
		<?= ((! empty($message) && isset($_POST[conductor]))?"value='$_POST[conductor]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="performer" 
		<?= ((! empty($message) && isset($_POST[performer]))?"value='$_POST[performer]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="label" size="10" 
		<?= ((! empty($message) && isset($_POST[label]))?"value='$_POST[label]'":"") ?> /></td>
	<td bgcolor="#EEEEEE"><input type="text" name="comments" 
		<?= ((! empty($message) && isset($_POST[comments]))?"value='$_POST[comments]'":"") ?> /></td>
	<td class='mid' bgcolor="#EEEEEE" class=\"text\">New:<input type="checkbox" 
				name="emph" value="NE" <?= ((! empty($message) && $_POST[emph])?"checked":"") ?> /></td>
<?php	}	?>
</tr>
</table>

<select name="action">
	<option />Edit
	<option />Delete
	<option />Shift up
	<option />Shift down
</select>
&nbsp;&nbsp;
<input type="text" size="2" name="shift_lines" value="1" /> 
lines (for shift operations)<br>
<input type="submit" name="manipulate" value="Go" />

<p>
<input type="submit" name="submitLine" value="Submit" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="break" value="Insert break" /></p>
<p><input type="reset" value="Reset form" /></p>
</form>
<p class='text'><a href="endedit.php?show_id=<?= $show_id ?>">
		<b>Finish editing</b></a></p>

</body>
</html>
