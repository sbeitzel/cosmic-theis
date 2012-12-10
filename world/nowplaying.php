<?php
// $Id: nowplaying.php,v 1.12 2004/03/01 20:57:46 admin Exp $
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

/********************************************************
 put this file wherever you want to see the current song
*********************************************************/

/* always include this first */
include("config.inc.php");

/* we are going to use the playlist class */
import("org/wprb/NowPlaying.php");
import("org/wprb/ObjectCache.php");
define("__OBJECTCACHE_DIR__", "./cache");
define("__OBJECTCACHE_TIMEOUT__", (30));

$debug = $_GET["debug"];

$pcache = new ObjectCache ("nowplaying.cache", 30);
if ($debug) print "<pre>cache:new  -- $pcache->error </pre><br />";

$nowplay = $pcache->load($playlist);
if ($debug) print "<pre>cache:load -- $pcache->error </pre><br />";

if ( !$nowplay ) { 

	$nowplay = "empty";

	$play = new NowPlaying();
	
	if ( ! empty( $play->artist ) )	{
		$nowplay = $play;
	}

	$pcache->store($nowplay);
	if ($debug) print "<pre>cache:store-- $pcache->error </pre><br />";
} 

if ( $nowplay != "empty" ) { 
	$str = "$nowplay->artist - $nowplay->song";
	if ( strlen( $str ) > 80 )
		$str = substr( $str, 0, 77 ) . "...";
	
	print "now playing: $str &nbsp;";
}
?>
