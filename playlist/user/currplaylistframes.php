<?php
// $Id: currplaylistframes.php,v 1.6 2004/03/08 02:49:20 admin Exp $
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
include("../lib/PlaylistSession.php");

checkUser();

$userID = fetchUserID($session[id]);
$pl_session = new PlaylistSession($show_id, $userID, $link);

?>

<html>
<head>
<!-- This page generated by Theis Playlist Manager -->
<title>Current Playlist</title>
</head>

<frameset cols="5, *">
	<frame src="pl_refresh.php?show_id=<?= $show_id ?>"
					name="refresh_frame" frameborder='0' noresize>

<?php
// choose playlist form based on genre
if ( $pl_session->active_show[genre] == "Classical" )	{
	print "<frame src=\"classicalplaylist.php?show_id=$show_id#entryform\" frameborder=\"0\" name=\"playlist\">\n";
}
else	{
	print "<frame src=\"currentplaylist.php?show_id=$show_id#entryform\" 
				frameborder=\"0\" name=\"playlist\">\n";
}
?>

</frameset>

</html>
