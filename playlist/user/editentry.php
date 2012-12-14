<?php
// $Id: editentry.php,v 1.7 2004/03/10 18:55:58 admin Exp $
/******************************************************************************
 * Theis Playlist Manager -- An interactive web application for creating,     *
 * editing, and publishing radio playlists.                                   *
 *                                                                            *
 * Copyright (C) 2003, 2004  Aaron Forrest                                    *
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

// use PlaylistLine class
import("org/wprb/PlaylistLine.php");

checkUser();
checkGuestUser();
$ref_arr = explode("?", $_SERVER['HTTP_REFERER']);

$line = new PlaylistLine($id);

// run validity tests and submit line to db
if ( isset($action) && $action == "update" )
	{
	$message = "";
	if (empty($form[artist]))
		$message .= "You must fill in an artist<Br>\n";
	if (empty($form[song]))
		$message .= "You must fill in a song title.<br>\n";
	if (empty($form[album]) && $_GET[genre] != "Classical")
		$message .= "You must fill in an album title. For albums with no title type \"s/t\".<br>\n";
	if ($message == "")	{  // no errors
		// update the line and go back to the current playlist
		// or edit playlist page.
		if ($_GET[genre] != "Classical")
			$line->updatePlaylistLine( $form[artist], $form[song],
					$form[album], $form[label], $form[comments],
					$form[emph], $form[request], $form[comp] );
		else
			$line->updateClassicalPlaylistLine($form[artist], $form[song],
					$form[ensemble], $form[conductor], $form[performer], 
					$form[label], $form[comments], $form[emph]);
		header ( "Location: $_POST[referer]" );
	}
}
?>
<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>Edit playlist entry</title>
<link rel='stylesheet' type='text/css' href='../css/wprb.css'>
</head>
<body>
<?php
if ($message != "")
	print "<b>$message</b><br>\n";
	
if ( ! empty($id) )	{
	$row = getRow("playlist", "ID", $id, 'i');
}
// determine the height of the comments textarea
$height = ((strlen($line->comments)>20) ? intval(strlen($line->comments)/20) : 1);
?>
<h2>Edit entry</h2>
<p>
<form method="POST">
<input type="hidden" name="action" value="update">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="show_id" value="<?= $line->showID ?>">
<input type="hidden" name="referer" value="<?= $ref_arr[0] ?>?show_id=<?= $line->showID ?>">
<?php
if ( $_GET[genre] == "Classical" )	{
	$headers = array("artist"=>"Composer", "song"=>"Piece", 
			"ensemble"=>"Ensemble/<br />Orchestra", "conductor"=>"Conductor",
			"performer"=>"Performer/<br />Soloist", "label"=>"Label",
			"comments"=>"Comments", "emph"=>"Recent<br />Release" );
}
else	{
	$headers = array("artist"=>"Artist", "song"=>"Song", 
			"album"=>"Album", "label"=>"Label", "comments"=>"Comments", 
			"emph"=>"Emph", "request"=>"R", "comp"=>"C");
}
?>
<table border=0 cellspacing=2 cellpadding=5>
	<tr>
	 <td colspan='8' align='right'><font size='-2'>R = request, C = Compilation</font></td>
	</tr>
	<tr>
<?php
foreach ( $headers as $key=>$header )	{
	print "\t\t<th bgcolor=\"#CCCCCC\">$header</th>\n";
}
?>		
	</tr>
	<tr>
<?php
if ($_GET[genre] != "Classical")	{
	?>
		<td bgcolor="#DDDDDD"><input type="text" name="form[artist]" value="<?= $line->artist ?>"></td>
		<td bgcolor="#DDDDDD"><input type="text" name="form[song]" value="<?= $line->song ?>"></td>
		<td bgcolor="#DDDDDD"><input type="text" name="form[album]" value="<?= $line->album ?>"></td>
		<td bgcolor="#DDDDDD"><input type="text" name="form[label]" value="<?= $line->label ?>"></td>
		<td bgcolor="#DDDDDD"><textarea name="form[comments]" cols="20" rows="<?=$height?>"><?= $line->comments ?></textarea></td>
		<td bgcolor="#DDDDDD"><select name="form[emph]">
			<?php writeEmphOptions($line->emph) ?></select></td>
		<td bgcolor="#DDDDDD"><input type="checkbox" name="form[request]" value="1"
			<?php print (($line->request)?" CHECKED":"") ?> ></td>
		<td bgcolor="#DDDDDD"><input type="checkbox" name="form[comp]" value="1"
			<?php print (($line->comp)?" CHECKED":"") ?> ></td>
<?php
}
else	{
?>
		<td bgcolor="#DDDDDD"><input type="text" name="form[artist]" value="<?= $line->artist ?>"></td>
		<td bgcolor="#DDDDDD"><input type="text" name="form[song]" value="<?= $line->song ?>"></td>
		<td bgcolor="#DDDDDD"><input type="text" name="form[ensemble]" value="<?= $line->ensemble ?>"></td>
		<td bgcolor="#DDDDDD"><input type="text" name="form[conductor]" value="<?= $line->conductor ?>"></td>
		<td bgcolor="#DDDDDD"><input type="text" name="form[performer]" value="<?= $line->performer ?>"></td>
		<td bgcolor="#DDDDDD"><input type="text" name="form[label]" value="<?= $line->label ?>"></td>
		<td bgcolor="#DDDDDD"><textarea name="form[comments]" cols="20" rows="<?=$height?>"><?= $line->comments ?></textarea></td>
		<td bgcolor="#DDDDDD"><input type="checkbox" value="NE" name="form[emph]" <?= ($line->emph=="NE") ? "CHECKED":""?> ></td>
<?php
}
?>
	</tr>
</table>
</p>
<p>
<input type="submit" value="Make changes">
</p><p>
<input type="reset" value="Reset form"></p>
</form>
<p class='text'>
<a href="<?= $ref_arr[0] ?>?show_id=<?= $line->showID ?>">Return to playlist</a></p>
</p>
</body>
</html>
