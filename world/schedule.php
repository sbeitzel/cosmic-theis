<?php
// $Id: schedule.php,v 1.7 2004/03/02 00:59:28 admin Exp $
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

/* always include this first */
include("config.inc.php");

/* we are going to use the schedule class */
import("org/wprb/schedule.php");

$schedule = new Schedule();
$info = $schedule->getScheduleInfo();
?>
<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>WPRB Schedule for <?= $info->season ?> <?= $info->year ?></title>
<link href="wprb.css" rel='styleSheet' type="text/css">
</head>
<body>
<!-- ************ REMOVABLE ************ -->
<?php include("worldnav.html"); ?>
<!-- ********** END REMOVABLE ********** -->
<p>
<table width="535">
<?php
$days = array("sundays", "mondays", "tuesdays", "wednesdays", "thursdays", 
				"fridays", "saturdays");

// loop through the schedule and print shows in the appropriate spots
for ($i=0; $i<7; $i++)	{
	$schedule->reset();
	print "\t<tr><td  class='table_heading' colspan='2' BGCOLOR='#FF6600' height='14'><tt>  $days[$i]</tt></td></tr>\n";
	while ($schedule->hasNext())	{
		$row = $schedule->next();
		if ( $row->day == $i )	{
			(($row->start > 2400) ? $row->start = $row->start-2400 : $row);
			(($row->end > 2400) ? $row->end = $row->end-2400 : $row);
			$starthour = intval($row->start/100);
			$startmin = $row->start - ($starthour*100);
			(($startmin==0) ? $startmin="00": $startmin);
			$endhour = intval($row->end/100);
			$endmin = $row->end - ($endhour*100);
			(($endmin==0) ? $endmin="00": $endmin);
			
			print "\t<tr height='20'>\n";
			print "\t\t<td class='table_text' bgcolor='#EEEEEE' nowrap>$starthour:$startmin - $endhour:$endmin</td>\n";
			print "\t\t<td class='table_text' bgcolor='#EEEEEE'><a href=";
			// use offsite link, if specified in preferences
			print (($row->offsite) ? "'$row->link' target='_blank'>" : "'djplaylists.php?id=$row->userID'>");
			print stripslashes($row->title)."</a></td>\n";
			print "\t</tr>\n";
		}
	}
	print "<tr><td>&nbsp;</td></tr>\n";
}
?>
</table>
<p class='text'>
<a href="schedulelist.php"><b>old schedules</b></a>
</p>
</body>
</html>
