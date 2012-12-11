<?php
// $Id: editdetails.php,v 1.6 2004/03/22 01:17:24 admin Exp $
/*****************************************************************************
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

checkUser();
checkGuestUser();

// verify that user matches selected show
$show = getRow( "shows", "ID", $_GET[show_id] );
if ($show[userID] != ($user = fetchUserID()) )
	header("Location: oldplaylists.php");

if ( isset($action) && $action=="start" )	{
	$message = "";
	if ( empty($form[djname]))
		$message .= "You must fill in your dj name<br>\n";
	if ( ! checkdate($form[month], $form[day], $form[year]) )
		$message .= "You must enter a valid date<br>\n";
	if ($form[duration] == 0)
		$message .= "Show duration must be longer than 0<br>\n";
	if ($message == "")		// all tests are passed
		{
		$starttime = mktime($form[hour], $form[minute], 0, 
					$form[month], $form[day], $form[year]);
		$usersID = fetchUserID();
		updateDetails($_GET[show_id], $starttime, $form[duration], $form[djname],
						$form[title], $form[subtitle], $form[genre],
						$_POST[othergenre]);
		header("Location: oldplaylists.php");
	}
}


?>
<html>
<!-- This page generated by Theis Playlist Software -->
<head>
<title>Edit show details</title>
<link href="../css/wprb.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
include("usernav.inc");
?>
<h2>Edit show details</h2>
<?php
setShowDetails($_GET[show_id]);
?>