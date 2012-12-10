<?php
// $Id: schedule_list.php,v 1.5 2004/01/09 03:33:48 admin Exp $
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
/* page for listing schedules to edit */


include("../lib/config.inc.php");
include("../lib/dblib.inc.php");
include("../lib/usrlib.inc.php");

import("org/wprb/ScheduleList.php");
import("org/wprb/schedule.php");

checkAdminUser();

if (isset($_POST[delete]) && $_POST[delete]=='delete' )	{
	$schedule=new Schedule($_POST[schedID]);
	$schedule->delete();
	unset($schedule);
}

if ( isset($_POST[setcurrent]) && $_POST[setcurrent]=='current' )	{
	$schedule = new Schedule($_POST[schedID]);
	$schedule->setCurrent();
	unset($schedule);
}

$s_list = new ScheduleList();
$s_list->reset();

?>
<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>Schedule Manager: List Schedules</title>
<link href="../css/wprb.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php include("adminnav.inc"); ?>
<p>
<h2>List of Schedules</h2>

<form method='POST'>
<table>
<?php
	while ($s_list->hasNext())	{
		$row = $s_list->next();
		$seasons = array("Fall", "Spring", "Summer");
		foreach ($seasons as $key=>$season)	{
			if ($row->season == $key) {
				$row->season = $season;
			}
		}
		print "<tr>\n";
		if ($row->current)	{
			print "\t<td width='200'><b><a href=\"schedule_edit.php?id=$row->ID\">$row->season $row->year</a>
					<i>(current)</i></b></td>\n";
		}
		else	{
			print "\t<td width='200'><span class='text'>
					<a href=\"schedule_edit.php?id=$row->ID\">$row->season $row->year</a></span></td>\n";
		}
		print "\t<td><input type='checkbox' name='schedID' value='$row->ID'></td></tr>\n";
	}
?>
	<tr><td colspan='2' align='right'><input type='submit' name='delete' value='delete'></td></tr>
	<tr><td colspan='2' align='right'><input type='submit' name='setcurrent' value='current'></td></tr>
</table>
</form>

</body>
</html>