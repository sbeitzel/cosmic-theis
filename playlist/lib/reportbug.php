<?php
// $Id: reportbug.php,v 1.5 2004/08/06 23:47:17 admin Exp $
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

include("config.inc.php");
include("dblib.inc.php");
include("usrlib.inc.php");

if ( isset($action) && $action == 'send' )	{
	$success = mail(__BUGREPORTS__, 
				"[".__STATION__."Theis Playlist Manager Bug Report", 
				$form[body], "From: $form[from]");
	if ($success)	{
		print "<script language='Javascript' type='text/javascript'>window.close();</script>";
	}
	else
		$message .= "Your bug report failed to send";
}


$usrinfo = getRow('users','loginsID',$session[id], 'i');
if (empty($usrinfo))	{
	$usrinfo[firstname] = "Anonymous";
	$usrinfo[email] = "playlist@wprb.com";
}

?>

<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>Send Bug Report</title>
<link href="css/wprb.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
if ( $message != "" )
	print "<b>$message</b><br>\n";
?>

<form method='POST'>
<input type='hidden' name='action' value='send'>
<p class='text'>
From: <br>
<input type='text' size='50' name='form[from]' value='<?="$usrinfo[firstname] $usrinfo[lastname] <$usrinfo[email]>"?>'>
</p><p class='text'>
Type your message here:<br>
<textarea name='form[body]' rows='8' cols='50'>
Enter your bug report here.  Try to be as detailed as possible,
noting the page on which the error occurred, the action which produced the
error, and whatever other information seems relevant.
</textarea>
</p>

<input type='submit' value='send'>
</form>
</body>
</html>
