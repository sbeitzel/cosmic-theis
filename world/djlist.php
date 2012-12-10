<?php
// $Id: djlist.php,v 1.6 2003/12/23 05:50:56 admin Exp $
 
/*****************************************************************************
 * Theis Playlist Manager -- An interactive web application for creating,    *
 * editing, and publishing radio playlists.                                  *
 *                                                                           *
 * Copyright (C) 2003  Aaron Forrest                                         *
 *                                                                           *
 * This program is free software; you can redistribute it and/or             *
 * modify it under the terms of the GNU General Public License               *
 * as published by the Free Software Foundation; either version 2            *
 * of the License, or (at your option) any later version.                    *
 *                                                                           *
 * This program is distributed in the hope that it will be useful,           *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 * GNU General Public License for more details.                              *
 *                                                                           *
 * You should have received a copy of the GNU General Public License         *
 * along with this program; if not, write to the Free Software               *
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.*
 ****************************************************************************/

/* always include this first */
include("config.inc.php");

/* we are going to use the Users class */
import("org/wprb/Users.php");

$users = new Users();
$users->load(null);

function printByGenre(&$djobj, $genre)	{
	($genre == "Undesignated") ? $genre = null : $genre;
	if ($djobj->defgenre != $genre)
		return 0;
	if (! empty($djobj->defdjname))	{
		print "<span class='text'><a href=";
		print (($djobj->offsite)?"'$djobj->link' target='_blank'>" : "'djplaylists.php?id=$djobj->ID'>");
		print "$djobj->defdjname</a> ";
	}
	else	{
		print "<span class='text'><a href=";
		print (($djobj->offsite)?"'$djobj->link' target='_blank'>" : "'djplaylists.php?id=$djobj->ID'>");
		print "$djobj->firstname</a> ";
	}
	if (! empty ($djobj->deftitle))
		print "[ $djobj->deftitle ]";
	print "</span><br>\n";
	return 1;
}


?>
<html>
<head>
<title>List of DJs</title>
<link href="wprb.css" rel='styleSheet' type="text/css">
</head>
<body>
<!-- ************ REMOVABLE ************ -->
<?php
include( "worldnav.html" );
print "<p>\n";
?>
<h3>Current DJs</h3>
<!-- ********** END REMOVABLE ********** -->

<?php

$genres = array("Classical", "Jazz", "Rock/etc", "Specialty", "Undesignated");
foreach ($genres as $genre)	{
	$users->reset();
	print "<b>$genre</b><br>\n";
	while ($users->hasNext())	{
		$row = $users->next();
		if ($row->active)	{
			printByGenre($row, $genre);
		}
	}
	print "<br>\n";
}


print "<p><h3>Old DJs</h3>\n";
foreach ($genres as $genre)	{
	$users->reset();
	print "<b>$genre</b><br>\n";
	while ($users->hasNext())	{
		$row = $users->next();
		if (! $row->active)	{
			printByGenre($row, $genre);
		}
	}
	print "<br>\n";
}

?>
</body>
</html>
