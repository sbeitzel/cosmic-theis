<?php
// $Id: options.inc.php,v 1.6 2004/08/09 00:14:07 admin Exp $
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


// functions to write select menu options

// function to write color options to selector form

function writeColorOptions($selected)
	{
	$colors = array("000000"=>"black", "FFFFFF"=>"white", "EEEEEE"=>"grey",
			"FF9999"=>"red", "9999FF"=>"blue", "99FF99"=>"green");
	foreach ($colors as $hexcode=>$color)
		{
		print "\t<option value=\"$hexcode\" ";
		print (($selected == $hexcode)?"SELECTED":"");
		print ">$color\n";
		}
	}
	
//functions to write days, hours, minutes, etc.
function writeWeekdayOptions($defday)
	{
	$weekdays = array("Sunday", "Monday", "Tuesday", "Wednesday", 
				"Thursday", "Friday", "Saturday");
	$x = 0;
	foreach ($weekdays as $weekday)
		{
		print "\t\t<option value=\"$x\" ";
		print (($x == $defday)?"SELECTED":"");
		print ">$weekday\n";
		$x++;
		}
	}
	
function writeHourOptions($defhour)
	{
	for ($x=0; $x<24; $x++)
		{
		print "\t\t<option ";
		print (($x == $defhour)?"SELECTED":"");
		print ">$x\n";
		}
	}
	
function writeMinuteOptions($defmin)
	{
	$minutes = array("00", "30");
	foreach ($minutes as $minute)
		{
		print "\t\t<option ";
		print (($minute == $defmin)?"SELECTED":"");
		print ">$minute\n";
		}
	}

function writeMonthOptions($defmonth)
	{
	for ($x=1; $x<13; $x++)
		{
		print "\t\t<option ";
		print (($x == $defmonth)?"SELECTED":"");
		print ">$x\n";
		}
	}

function writeDayOptions($defday)
	{
	for ($x=1; $x<=31; $x++)
		{
		print "\t\t<option ";
		print (($x == $defday)?"SELECTED":"");
		print ">$x\n";
		}
	}

function writeYearOptions($defyear, $month, $day)
	{
	print "\t\t<option SELECTED>$defyear\n";
	if ($month == "12" && $day =="31")
		print "\t\t<option>".($defyear+1)."\n";
	}

function writeYearOptions2($defyear, $yearsback, $yearsfor)	{
	for ( $year = $defyear - $yearsback; $year <= $defyear + $yearsfor; $year++)	{
		print "\t\t<option ";
		print (($year == $defyear) ? "SELECTED" : "");
		print ">$year\n";
	}
}

function writeDurationOptions($defduration)
	{
	for ($x=0; $x<=24; $x++)
		{
		for ($y=0; $y<=0.5; $y+=.5)
			{
			$val = ($x + $y);
			print "<option value=\"$val\" ";
			print (($val==$defduration)?"SELECTED":"");
			print (($y == 0)?">$x:00\n":">$x:30\n");
			}
		}
	}

function writeGenreOptions($defgenre)
	{
	$genres = array("Rock/etc", "Classical", "Jazz", "Specialty");
	foreach ($genres as $genre)
		{
		print "\t\t<option ";
		print (($genre == $defgenre)?"SELECTED":"");
		print ">$genre\n";
		}
	}
	
function writeEmphOptions($emph="")
	{
	$emph_array = array("NE", "N", "OE");
	print "<option value=\"\">none\n";
	foreach ($emph_array as $option)
		{
		print "<option ";
		print (($option == $emph)?"SELECTED":"");
		print ">$option\n";
		}
	}

function writeSearchOptions($default='')	{
	$fields = array("all", "artist", "song", "album", "label", "ensemble", 
					"conductor", "performer");
	foreach ($fields as $field)	{
		print "<option ";
		print (($field == $default)?"SELECTED":"");
		print ">$field\n";
	}
}

// Users class, found in classes/org/wprb/Users.php , must be included

function writeDJOptions($default='')	{
	$users = new Users();
	$users->load(null);
	$users->reset();
	while ($users->hasNext())	{
		$row=$users->next();
		print "<option value='$row->ID' ";
		if ( ! empty($row->defdjname) )	{
			print (($row->ID== $default) ? "SELECTED":"");
			print ">$row->defdjname\n";
		}
		else	{
			print (($row->ID== $default) ? "SELECTED":"");
			print ">$row->firstname\n";
		}
	}
}
	

?>
