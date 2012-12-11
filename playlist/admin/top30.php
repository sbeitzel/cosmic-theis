<?php
// $Id: top30.php,v 1.5 2004/01/09 03:32:31 admin Exp $
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

checkAdminUser();

if (isset($form[action]) && $form[action] == "generate")	{
		$message = "";
		$starttime = mktime($form[hour1], $form[min1], 0, $form[month1], $form[day1], $form[year1]);
		$endtime = mktime($form[hour2], $form[min2], 59, $form[month2], $form[day2], $form[year2]);
		if ($endtime - $starttime < 0)
				$message .= "Your end date must be later than your start date<br>";
		if ($message == "")	{ // no errors
			header("Location: printTop30.php?endtime=$endtime&starttime=$starttime&emph=$form[emph]&genre=$form[genre]&sortby=artist");
			}
}
?>
<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>Choose query boundaries</title>
<link href="../css/wprb.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
include("adminnav.inc");
// get timestamps for today and one week ago, to set form default
$today = getdate();
$lastweek = getdate(time() - (60*60*24*7));

if (isset($message))	{
	print "<b>$message</b><br>\n";
	}
?>
<h2>Choose Top 30 Boundary Dates</h2>
<form method="POST">
<input type="hidden" name="form[action]" value="generate">
<p class='text'>Start Date:<br>
 	<select name="form[month1]">
 		<?php
		writeMonthOptions($lastweek[mon]);
		?></select>
 &nbsp;&nbsp;&nbsp;&nbsp;
 	<select name="form[day1]">
		<?php
		writeDayOptions($lastweek[mday]);
		?></select>
 &nbsp;&nbsp;&nbsp;&nbsp;
 	<select name="form[year1]">
 		<?php
		writeYearOptions2($lastweek[year], 5, 1);
		?></select>
 &nbsp;&nbsp;&nbsp;&nbsp;
 	<select name="form[hour1]">
		<?= writeHourOptions($today[hours]); ?>
	</select> : 
	<select name="form[min1]">
		<?= writeMinuteOptions($today[minutes]); ?>
	</select>
</p><p class='text'>
End date:<br>
	<select name="form[month2]">
		<?php
		writeMonthOptions($today[mon]);
		?></select>
 &nbsp;&nbsp;&nbsp;&nbsp;
	<select name="form[day2]">
		<?php
		writeDayOptions($today[mday]);
		?></select>
 &nbsp;&nbsp;&nbsp;&nbsp;
	<select name="form[year2]">
		<?php
		writeYearOptions2($today[year], 5, 1);
		?></select>
 &nbsp;&nbsp;&nbsp;&nbsp;
     <select name="form[hour2]">
		 <?= writeHourOptions($today[hours]); ?>
 	 </select> :
	<select name="form[min2]">
		<?= writeMinuteOptions($today[minutes]); ?>
	</select>

</p>
<p class='text'>
Emphasis: <select name="form[emph]">
				<option value='<?=null?>'>none
				<option>new
				<option>old
			   </select>
<br>
Genre: 
<select name="form[genre]">
	<?php
	writeGenreOptions();
	?>
	<option>any
	</select>
</p>
<p>
<input type="submit" value="Generate!!!">
</form>
</body>
</html>