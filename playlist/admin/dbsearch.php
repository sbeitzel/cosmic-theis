<?php
// $Id: dbsearch.php,v 1.9 2004/08/09 03:28:30 admin Exp $
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

import("org/wprb/Users.php");
import("org/wprb/Search.php");

checkAdminUser();

if (! isset($form['startmon']) || ! isset($form['endmon']))	{
	$zerotime = getdate(0);
	$nowtime = getdate();
	$form['startmon'] = 1;
	$form['startmday'] = 1;
	$form['startyear'] = 2003; // year playlist db was implemented
	$form['endmon'] = $nowtime['mon'];
	$form['endmday'] = $nowtime['mday'];
	$form['endyear'] = $nowtime['year'];
	$form['endhour'] = $nowtime['hours'];
	$form['endmin'] = $nowtime['minutes'];
}

$message = "";
// send query to db after form has been submitted
if (isset($form['action']) && $form['action'] == "search")	{
	if ( strlen($form['searchstring']) < 3 )
		$message .= "Your search string must be at least 3 characters long";
	if ($message == "")	{
		$starttime = mktime($form['starthour'],$form['startmin'],0, $form['startmon'],
							$form['startmday'], $form['startyear']);
		$endtime = mktime($form['endhour'], $form['endmin'], 59, $form['endmon'],
							$form['endmday'], $form['endyear']);
		
		if ( ! isset( $_GET['orderby'] ) )	{
			$_GET['orderby'] = "ID";
			$_GET['dirx'] = "DESC";
		}
		
		$search = new Search( array("searchstring"=>$form['searchstring'],
							"searchfield"=>$form['searchfield'],
							"start"=>$starttime, 
							"end"=>$endtime, 
							"users"=>$_GET['form']['users'],
							"genres"=>$form['genres'],
							"emphs"=>$_GET['form']['emph'],
							"comp"=>$form['comp'],
							"req"=>$form['req'],
							"fuzzy"=>$form['fuzzy'],
							"orderby"=>$_GET['orderby'],
							"dirx"=>$_GET['dirx'],
							"page"=>$page));
		$success = $search->searchDB();
	}
}
	
// email a user's playlist:
if (isset($form['action']) && $form['action']=="send")	{
	$show_array = getRow('shows', 'ID', $show_id, 'i');
	if ( empty($show_array['title']) )
		$show_array['title'] = "$show_array[genre] with $show_array[djname]";
	$date = getdate($show_array['starttime']);
	$subject = "$show_array[title] | ".date("l, d M Y, H E\S\T", $date);
	mailQueryResults($form['email'], $subject, $emailquery, null, null,
			$show_array['genre'], $form['from'], $form['comments']);
}
	
?>
<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>Search the playlist database</title>
<link href="../css/wprb.css" rel="stylesheet" type="text/css">
</head>
<body>
<?
include("adminnav.inc");
if ($message != "")
	print "<b>$message</b><br>\n";
?>
<h2>Search the database</h2>
<?php

if ($success)	{
	$search->reset();
	print "<hr noshade size='1'>\n";
	print "<p><i><b class='black'>Found ".$search->size()." results:</b></i><br>\n";
	if ($search->size() > 0)	{	// results returned
		$headers = array("artist"=>"Artist", "song"=>"Song", 
					"album"=>"Album", "label"=>"Label", 
					"djname"=>"Played by", "starttime"=>"Date");
		print "<table cellpadding='3'>\n";
		print "\t<tr>\n";
		
		$querystring = trimEnd($_SERVER['QUERY_STRING'], "orderby");
		foreach ($headers as $key=>$header)	{
			print "\t\t<th bgcolor='#DDDDDD'><a href=\"dbsearch.php?";
			print $querystring ."&orderby=$key";
			if ($_GET[orderby]==$key && $_GET[dirx]=="ASC")
				print "&dirx=DESC";
			else
				print "&dirx=ASC";
			print "\"><b class=\"black\">$header</b></a></th>\n";
		}
		print "\t</tr>\n";
		while ($search->hasNext())	{
			$row = $search->next();
			$insound_artist = urlencode($row->artist);
			$insound_album = urlencode($row->album);
			$insound_label = urlencode($row->label);
			print "\t<tr>\n";
			print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row->artist</td>\n";
			print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row->song</td>\n";
			print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row->album</td>\n";
			print "\t\t<td class='mid' bgcolor='#EEEEEE'>$row->label</td>\n";
			print "\t\t<td class='mid' bgcolor='#EEEEEE'>
				<a href='djplaylists.php?id=$row->userID'>
				$row->djname</a></td>\n";
			print "\t\t<td class='mid' bgcolor='#EEEEEE'>
				<a href='printplaylist.php?show_id=$row->ID'>" .
				date("l, F j, Y, H:i", $row->starttime) . "</a></td>\n";
			print "\t</tr>\n";
		}
		print "</table>\n";
	}
	// add links to next and previous pages of results if there are more than 100
	print "<span class='text'>\n";
	if ($page>0)	{
		$prev_page = $page-100;
		print "<a href=\"dbsearch.php?".$_SERVER['QUERY_STRING']."&page=$prev_page\">prev</a>\n";
	}
	print (($page>0 && count($search_results)>=100)?" | ":"");
	if (count($search_results)>=100)	{
		$page += 100;
		print "<a href=\"dbsearch.php?".$_SERVER['QUERY_STRING']."&page=$page\">next</a>\n";
	}
	print "</span>\n<hr noshade size='1'>\n";
}
?>
<p>

<!-- SEARCH FORM -->
<form method="GET" action="dbsearch.php">
<input type="hidden" name="form[action]" value="search">
<input type="hidden" name="page" value="0">
<table border="0" noshade cellpadding="3">
	<tr>
		<th class='ljust' bgcolor="#CCCCCC">Search by:</th>
		<th class='ljust' bgcolor="#CCCCCC">Search string:</th>
		<th class='ljust' colspan="2" bgcolor="#CCCCCC">Boundary dates:</th>
		<th bgcolor="#CCCCCC">&nbsp;</th>
	</tr>
	<tr>
		<td bgcolor="#EEEEEE"><select name="form[searchfield]">
			<?php writeSearchOptions($form['searchfield']); ?>
			</select>
		</td><td bgcolor="#EEEEEE">
			<input type="text" name="form[searchstring]" value="<?= $form['searchstring'] ?>">
		<td class='mid' bgcolor="#EEEEEE">From: 
			<select name="form[startmon]">
				<?php writeMonthOptions($form['startmon']); ?></select>&nbsp;/&nbsp;
			<select name="form[startmday]">
				<?php writeDayOptions($form['startmday']); ?></select>&nbsp;/&nbsp;
			<select name="form[startyear]">
				<?php writeYearOptions2($form['startyear'], $form['startyear']-2003, 1); ?></select>&nbsp;&nbsp;
			<div align="right">
			<select name="form[starthour]">
				<?php writeHourOptions($form['starthour']);
				?></select>&nbsp;:&nbsp;
			<select name="form[startmin]">
				<?php writeMinuteOptions($form['startmin']);
				?></select>
			&nbsp;&nbsp;</div>
		</td><td class='mid' bgcolor="#EEEEEE">To:
			<select name="form[endmon]">
				<?php writeMonthOptions($form['endmon']); ?></select>&nbsp;/&nbsp;
			<select name="form[endmday]">
				<?php writeDayOptions($form['endmday']); ?></select>&nbsp;/&nbsp;
			<select name="form[endyear]">
				<?php writeYearOptions2($form['endyear'], $form['endyear']-2003, 1); ?></select>&nbsp;&nbsp;
			<div align="right">
			<select name="form[endhour]">
				<?php writeHourOptions($form['endhour']);
				?></select>&nbsp;:&nbsp;
			<select name="form[endmin]">
				<?php writeMinuteOptions($form['endmin']);
				?></select>
			&nbsp;&nbsp;</div>
		</td>
		<td align="right" bgcolor="#EEEEEE"><input type="submit" value="search"></td>
	</tr>
</table>

<!-- SEARCH TYPE -->
<div class='text'>Search type: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Strict <input type="radio" name="form[fuzzy]" value="0" 
		<?=(! $_GET['form']['fuzzy'])?"checked":"";?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Fuzzy <input type="radio" name="form[fuzzy]" value="indeed"
		<?=($_GET['form']['fuzzy'])?"checked":"";?>>
</div>

<!-- SEARCH REFINERS -->
<table cellpadding="3">
	<tr height="50" valign="bottom">
		<td colspan="6"><b class='black'>Refine your search:</b></td>
	</tr><tr>
		<th class='ljust' bgcolor="#CCCCCC">username(s)</th>
		<th class='ljust' bgcolor="#CCCCCC">genre</th>
		<th class='ljust' bgcolor="#CCCCCC">emph plays</th>
		<th class='ljust' bgcolor="#CCCCCC">compilation</th>
		<th class='ljust' bgcolor="#CCCCCC">request</th>
	</tr><tr valign="top">
		<td class='norm' bgcolor="#EEEEEE"><select multiple size='5' name="form[users][]">
			<option value=''>all
			<?php writeUsernameOptions($_GET['form']['users'][0]); ?></select></td>
		<td class='norm' bgcolor="#EEEEEE"><select multiple name="form[genres][]">
			<option value=''>all
			<?php writeGenreOptions($_GET['form']['genres'][0]); ?></select></td>
		<td class='norm' bgcolor="#EEEEEE"><select multiple name="form[emph][]">
				<option value=''>none
				<?php
				$arr = array("NE", "N", "OE");
				foreach ($arr as $i)	{
					print "\t<option ";
					print (($_GET['form']['emph'][0]==$i)?"selected":"");
					print ">$i\n";
				}
				?>
				</select></td>
		<td class='norm' bgcolor="#EEEEEE">
			<input name="form[comp]" type="checkbox" value="checked" <?=($_GET['form']['comp'])?"checked":"";?>></td>
		<td class='norm' bgcolor="#EEEEEE">
			<input name="form[req]" type="checkbox" value="checked" <?=($_GET['form']['req'])?"checked":"";?>></td>
	</tr>
</table>
</form>

</body>
</html>
