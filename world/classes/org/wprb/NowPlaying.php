<?php
// $Id: NowPlaying.php,v 1.6 2004/02/28 05:13:19 admin Exp $
/******************************************************************************
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

// class to grab the latest song in the active playlist
import("org/wprb/WPRBDB.php");
import("org/erat/data/DataObject.php");

class NowPlaying extends DataObject {
	
	var $db;

	/****************************************************************
	 *	CONSTRUCTORS
	 *	The main constructor routes the parameters to the constructor
	 *	designed to handle that many arguments
	 ****************************************************************/
	function NowPlaying($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->NowPlaying" . $i . "(" . $args . ");");
	}

	/* load the last played song */
	function NowPlaying0() {
		$this->db = new WPRBDB();
		$max = new DataObject("getMaxVal", $this->db);
		parent::DataObject("getLatestSong", $this->db, array(intval($max->ID)));
	}

	function NowPlaying1( $a1 ) {}
	function NowPlaying2( $a1, $a2 ) {}
	function NowPlaying3( $a1, $a2, $a3 ) {}

}


?>
