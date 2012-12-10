<?php
// $Id: fullrss.php,v 1.6 2004/03/01 16:21:03 phil Exp $

 /*****************************************************************************
 * Theis Playlist Manager -- An interactive web application for creating,     *
 * editing, and publishing radio playlists.                                   *
 *                                                                            *
 * Copyright (C) 2003  Aaron Forrest                                          *
 * this file 	 2004  Philip Davidson                                        *
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
 
/* we are going to use the playlist class */
import("org/wprb/Playlist.php");
import("org/wprb/ShowList.php");
import("org/wprb/RSSfeed.php");
import("org/wprb/ObjectCache.php");

define("DAYS", (24*60*60));

//timezone to use...
$eastern_time_offset 	= ( -5 + date("I")) * 3600;	//gmt to eastern...
$eastern_time_abbr   	= ( date("I") ) ? "EDT" : "EST" ;

//use this timezone setting in RSS timestamps as well..
$RSStzoffset 	= $eastern_time_offset;
$RSStzabbr 	= $eastern_time_abbr;

$channelTitle = __STATION__." - shows";
$channelLink  = __ROOT__."last10.php";
$channelDesc  = __STATION__." - full playlist info for the last 24 hour period.";

$cache = new ObjectCache( "fullrss.data", 60 );
$rss = $cache->load($rss);

if ( !$rss ) {

     $rss = new RSSFeed($channelTitle, $channelLink, $channelDesc);
     $channeldocs  = __ROOT__."docs/";
     $rss->channel->optinfo[language]	= "en-us";
     $rss->channel->optinfo[docs] 	= $channeldocs;
     $rss->channel->optinfo[generator] 	= "Theis Radioware";

     $rss->channel->setImage("http://www.wprb.com/images/home/home_images/1B.gif", "turntable", 144,144,"http://www.wprb.com");

     $today = time();
     $start = $today-DAYS*1;

     $shows= new ShowList($start, $today);
     $shows->reset(); 

     while ( $shows->hasNext() ) {	
	   $row = $shows->next(); 
	   writeShowItem($row);
     }

     $cache->store($rss);
//     echo "cache:store - $cache->error\n";

}

$rss->send_http_header();
$rss->write();

function writeShowItem($row) { 

	global $rss;
	global $eastern_time_offset;

	$title = $row->title;

	$webroot = __ROOT__;
	$link = $webroot."printplaylist.php?show_id=$row->ID";
	$djlink = $webroot."djplaylists.php?id=$row->userID";

	$pubdate = $row->starttime;

	$start = RSSFormatLocalDate( $row->starttime, "D, M j H:i");
		//, $row->starttime + $eastern_time_offset);
	$end   = RSSFormatLocalDate( $row->starttime + $row->duration * 3600, "H:i" );

	$genre = $row->genre;
	if ( $row->genre != $row->othergenre ) { 
		$genre .= " ($row->othergenre)"; 
	} 

	$description = "<table border=\"0\" width=\"100%\" cellpadding=\"8\" cellspacing=\"0\">\n";

	if ( $row->active > 0 ) { 
		$description .= "<tr><td colspan=\"2\"><strong>NOW PLAYING<strong></td></tr>\n";
	}

	$description .= "<tr><td><strong>DJ</strong></td><td><a href=\"$djlink\">$row->djname</a></td></tr>\n";
	$description .= "<tr><td><strong>Showtime</strong></td><td><a href=\"$showlink\">$start - $end</a></td></tr>\n";
	$description .= "<tr><td><strong>Genre</strong></td><td>$genre</td></tr>\n";
	$description .= "</table>\n";


	$description .= "<table border=\"0\" cellpadding=\"8\" cellspacing=\"0\">\n";
	$description .= "<tr><td colspan=4><strong>P L A Y L I S T</strong></td></tr>";
	$description .= "<tr>";
	$description .= "<td><strong>Artist</strong></td>";
	$description .= "<td><strong>Title</strong></td>";
	$description .= "<td><strong>Album</strong></td>";
	$description .= "<td><strong>Label</strong></td>";
	$description .= "</tr>\n";



	$playlist = new Playlist($row->ID);
	$playlist->reset();
	$rl = "";
	while ( $playlist->hasNext() ) { 
		$prow = $playlist->next();
		if ( !( $prow->artist == "*****" && $prow->song == "BREAK" && $prow->album="*****" ) ) { 
			$r = "<tr>";
			$r .= "<td>$prow->artist</td>";
			$r .= "<td>$prow->song</td>";
			$r .= "<td>$prow->album</td>";
			$r .= "<td>$prow->label</td>";
			$r .="</tr>\n";
			$rl = $r.$rl;
		}
	}	
	$description .= $rl;
	$description .= "</table>\n";

	$rss->channel->pushItemBack($title, $pubdate, $link, $description );
}


?>
