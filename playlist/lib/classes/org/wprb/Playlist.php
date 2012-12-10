<?php
// $Id: Playlist.php,v 1.9 2004/10/16 03:09:10 admin Exp $
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

/*********************************************************
 *	Sample Playlist class.  This is what you'd import into
 *	the PHP file you use to format a playlist.  It has the
 *	capacity for up to 3 arguments in the constructor, but
 *	this example will show what happens when 1 argument is
 *	passed ...
 ********************************************************/

import("org/wprb/WPRBDB.php");
import("org/erat/data/DataObjectList.php");
import("org/erat/data/DataObject.php");

class Playlist extends DataObjectList {
	
	var $db;
	var $id;
	var $info;
	var $cbits;

	/****************************************************************
	 *	CONSTRUCTORS
	 *	The main constructor routes the parameters to the constructor
	 *	designed to handle that many arguments
	 ****************************************************************/
	function Playlist($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->Playlist" . $i . "(" . $args . ");");
	}

	/* load the active playlist */
	function Playlist0() {
		$this->db = new WPRBDB();
		$activeinfo = new DataObject("getActiveID", $this->db);
		if ($activeinfo) 	{
			$this->id = $activeinfo->ID;
			parent::DataObjectList("getPlaylist", $this->db, array($this->id));
			$this->setColBits();
		}
	}
	/* load a playlist from a given id */
	function Playlist1( $id ) {
		$this->id = $id;
		$this->db = new WPRBDB();
		parent::DataObjectList("getPlaylist",$this->db,array($id));
		$this->setColBits();
	}
	function Playlist2( $a1, $a2 ) {}
	function Playlist3( $a1, $a2, $a3 ) {}
	
	function getPlaylistInfo()	{
		$this->info = new DataObject("getPlaylistInfo", $this->db, array($this->id));
	}
		
	function setColBits()	{
		$cols = array_keys($this->db->getFields("playlist"));
		$this->reset();
		while ($this->hasNext())	{
			$row = $this->next();
			foreach ($cols as $key=>$col)	{
				if ( ! empty($row->$col) && $row->$col != "*****"
							&& $row->$col != "OE")	{
					$this->cbits[$col]=1;
					unset($cols[$key]);
				}
			}
			if ( 0 == count( $cols ) )
				break;
		}
	}	
	
	function delete()	{
		$this->db->doQuery ("deletePlaylist", array ($this->id));
	}

	function changeOwner ($newowner)	{
		$this->db->doQuery ("changePlaylistOwner", array ($newowner, $this->id));
	}
		
}


?>
