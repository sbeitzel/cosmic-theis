<?php
// $Id: playrss.php,v 1.6 2004/03/01 16:21:03 phil Exp $

 /*****************************************************************************
 * Theis Playlist Manager -- An interactive web application for creating,     *
 * editing, and publishing radio playlists.                                   *
 *                                                                            *
 * Copyright (C) 2003  Aaron Forrest                                          *
 * this file 	 2004  Philip Davidson                                                                           *
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
include("../config.inc.php");
include("rss.inc.php");

/* we are going to use the showlist class */
import("org/wprb/ShowList.php");
import("org/wprb/RSSfeed.php");
import("org/wprb/ObjectCache.php");
define("DAYS", (24*60*60));

//PRB is in eastern time...format our news links as such



$eastern_time_offset 	= ( -5 + date("I")) * 3600;	//gmt to eastern...
$eastern_time_abbr   	= ( date("I") ) ? "EDT" : "EST" ;

$RSStzoffset 	= $eastern_time_offset;
$RSStzabbr 	= $eastern_time_abbr;

$cache = new ObjectCache("playrss.data", 60 );
$rss = $cache->load($rss);

if ( !$rss ) { 
	
	$channelTitle = __STATION__." - shows";
	$channelLink  = __ROOT__."last10.php";
	$channelDesc  = __STATION__." - station schedule for the last 4 days.";
	
	$rss = new RSSFeed($channelTitle, $channelLink, $channelDesc);
	$channeldocs  = __ROOT__."docs/";
	$rss->channel->optinfo[language]	= "en-us";
	$rss->channel->optinfo[docs] 		= $channeldocs;
	$rss->channel->optinfo[generator] 	= "Theis Radioware";

	$rss->channel->setImage("http://www.wprb.com/images/home/home_images/1B.gif", "turntable", 144,144,"http://www.wprb.com");
	
	$today = time();

	$start = $today-DAYS*3;

	$shows= new ShowList($start, $today);
	$shows->reset(); 

	while ( $shows->hasNext() ) {
		$row = $shows->next(); 
		writeShowItem($row);
	}

	$cache->store($rss);
}

$rss->send_http_header();
$rss->write();

function writeShowItem($row) { 

	global $rss;

	$title 	 = $row->title;
	$pubdate = $row->starttime;

	$webroot = __ROOT__;
	$link = $webroot."printplaylist.php?show_id=$row->ID";
	$djlink = $webroot."djplaylists.php?id=$row->userID";

	$start = RSSFormatLocalDate( $row->starttime, "D, M j H:i"); 
		// , $row->starttime + $eastern_time_offset);
	$end   = RSSFormatLocalDate( $row->starttime + $row->duration * 3600, "H:i" ); 
		// , ( $row->starttime + $row->duration * 3600) + $eastern_time_offset );

	$description = "<table border=\"0\">\n";
	
	if ( $row->active > 0 ) { 
		$description .= "<tr><td colspan=\"2\">NOW PLAYING</td></tr>\n";
	}
	$description .= "<tr><td>DJ</td><td><a href=\"$djlink\">$row->djname</a></td></tr>\n";
	$description .= "<tr><td>time</td><td>$start - $end</td></tr>\n";
	$description .= "<tr><td>genre</td><td>$row->genre</td></tr>\n";
	$description .= "</table>\n";

	$rss->channel->pushItemBack($title, $pubdate, $link, $description);
}
?>

