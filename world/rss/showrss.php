<?php
// $Id: showrss.php,v 1.8 2004/03/01 16:21:03 phil Exp $

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

define("DAYS", (24*60*60));
define("EST", (-5 * 3600));

/* always include this first */
include("../config.inc.php");
include("rss.inc.php");

/* we are going to use the playlist class */
import("org/wprb/Playlist.php");
import("org/wprb/RSSfeed.php");
import("org/wprb/ObjectCache.php");

define("DAYS", (24*60*60));

//PRB is in eastern time...format our news links as such

$eastern_time_offset 	= ( -5 + date("I")) * 3600;	//gmt to eastern...
$eastern_time_abbr   	= ( date("I") ) ? "EDT" : "EST" ;

$RSStzoffset 	= $eastern_time_offset;
$RSStzabbr 	= $eastern_time_abbr;


$RSStzoffset 	= $eastern_time_offset;
$RSStzabbr 	= $eastern_time_abbr;

$showid = $_GET["show_id"];

$rss = null;

if ( ! $showid ) { 
	$cache = new ObjectCache("showrss.data", 60 );
	$rss = $cache->load($rss);
}

if ( !$rss ) { 
	$channelTitle = __STATION__." - shows";
	$channelLink  = __ROOT__."printplaylist.php";
	$channelDesc  = __STATION__." - realtime playlist for the current show";
	
	$rss = new RSSFeed($channelTitle, $channelLink, $channelDesc);
	$channeldocs  = __ROOT__."docs/";
	$rss->channel->optinfo[language]	= "en-us";
	$rss->channel->optinfo[docs] 		= $channeldocs;
	$rss->channel->optinfo[generator] 	= "Theis Radioware";
	
	$rss->channel->setImage("http://www.wprb.com/images/home/home_images/1B.gif", "turntable", 144,144,"http://www.wprb.com");
	
	if ( $showid ) { 
		$playlist = new Playlist( $showid );
	}
	else { 
		$playlist = new Playlist();
	}

	$playlist->getPlaylistInfo();
	$playlistinfo = $playlist->info;
	$cbit = $playlist->cbits;

	$rss->channel->reqinfo[title] = "$playlistinfo->title with $playlistinfo->djname";

	$playlist->reset();
	while ( $playlist->hasNext() ) {
		$row = $playlist->next();
		writePlayListItem($row);
	}

	if ( !$showid ) $cache->store($rss);
}

$rss->send_http_header();
$rss->write();


function writePlayListItem($row) { 

	global $rss;
	global $cbit;
	
	$headers = array( "artist"=>"Artist", "song"=>"Song", "album"=>"Album", 
				"label"=>"Label", "comments"=>"Comments", 
				"emph"=>"New", "request"=>"Request", "comp"=>"Comp");
	if ( ! ( $row->song == "BREAK" && $row->artist == "*****" && $row->album == "*****"   ) ) { 

		$title = "$row->song - $row->artist";

		$webroot = __ROOT__;
		$link = $webroot."printplaylist.php?show_id=$row->showID";
	
		$request = ( $row->request ) ? "yes" : "";
		$comp 	 = ( $row->comp ) ? "yes" : "";
		$description .= "<table border=\"0\" cellpadding=\"5\"cellspacing=\"0\">\n";

		foreach ( array_keys($cbit) as  $key ) {
			$data = $row->$key;
			if ( $key == "request" ) $data = ( $data ) ? "yes" : ""; 
			if ( $key == "comp" ) $data = ( $data ) ? "yes" : ""; 
			if ( $key == "emph" ) $data = ( $data == "NE" || $data == "N" ) ? "*" : ""; 
			$description .= "<tr><td><strong>$headers[$key]</strong></td><td>$data</td></tr>\n"; 
		}

		$description .= "</table>\n";

		$rss->channel->pushItemFront($title, "", $link, $description);

	}
}

?>
