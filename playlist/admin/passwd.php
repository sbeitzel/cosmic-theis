<?php
// $Id: passwd.php,v 1.4 2004/01/09 03:32:31 admin Exp $
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

checkAdminuser();

$message = "";
if ( isset($_POST[action]) && $_POST[action] == "change" )	{
	if ( empty($_POST[oldpw]) || empty($_POST[newpw1]) 
					|| empty($_POST[newpw2]) )
		$message .= "Please fill out form completely<br>\n";
	$l_row = getRow("logins", "login", "admin", 's');
	if ( $l_row[password] != md5($_POST[oldpw]) )
		$message .= "Old password did not match<br>\n";
	if ($_POST[newpw1] != $_POST[newpw2])
		$message .= "New password did not match<br>\n";
	if ( $message == "")	{
		changeAdminPassword($_POST[newpw1]);
		header( "Location: adminmenu.php" );
	}
}


?>


<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>Change Admin Password</title>
<link rel='Stylesheet' href="../css/wprb.css" type="text/css">
</head>
<?php
if ( $message != "" )
	print "<b>$message</b><br>\n";
include("adminnav.inc");
?>
<h2>Change administrator password</h2>

<form method="POST">
<input type="hidden" name="action" value="change">
<p class="text">Old password: <br>
<input type="password" name="oldpw" maxlength="20">
</p>
<p class="text">
New password: <br>
<input type="password" name="newpw1" maxlength="20">
</p>
<p class="text">
Confirm new password: <br>
<input type="password" name="newpw2" maxlength="20">
</p>
<p>
<input type="submit" value="change">
</p></form>
