<?php
// $Id: PlaylistLine.php,v 1.6 2004/03/12 00:28:33 admin Exp $
/******************************************************************************
 * Theis Playlist Manager -- An interactive web application for creating,     *
 * editing, and publishing radio playlists.                                   *
 *                                                                            *
 * Copyright (C) 2003, 2004  Aaron Forrest                                    *
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


import("org/wprb/WPRBDB.php");
import("org/erat/data/DataObject.php");

class PlaylistLine extends DataObject	{

	var $db;
	var $id;

	function PlaylistLine($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->PlaylistLine" . $i . "(" . $args . ");");
	}

	function PlaylistLine0()	{}

	function PlaylistLine1($id)	{
		$this->db = new WPRBDB();
		$this->id = $id;
		parent::DataObject("getPlaylistLine", $this->db, array( $this->id ));
	}

	function PlaylistLine2()	{}
	
	function updateClassicalPlaylistLine($artist, $song, $ensemble,
						$conductor, $performer, $label, $comments, $emph)	{
		parent::update("updateClassicalPlaylistLine", $this->db, array( $artist, 
				$song, $ensemble, $conductor, $performer, $label, $comments, 
				$emph, $this->id));
	}
	
	function updatePlaylistLine($artist, $song, $album, $label,
						$comments, $emph, $request, $comp)	{
		parent::update("updatePlaylistLine", $this->db, array($artist,
				$song, $album, $label, $comments, $emph, $request, 
				$comp, $this->id));
	}
	
	function delete()	{
		parent::update("deleteLine", $this->db, array("playlist", $this->id));
		return $this->db->affectedRows();
	}
	
	function shiftUp()	{
		$arr_prevID = $this->_getPrevID();
		if (! is_array($arr_prevID))	return false;
		
		parent::update("setOrderKey", $this->db, 
						array($this->orderkey, $arr_prevID[1]));
		parent::update("setOrderKey", $this->db,
						array($arr_prevID[0], $this->id));
		
		$this->orderkey = $arr_prevID[0];
		return true;
	}
	
	function shiftDown()	{
		$arr_nextID = $this->_getNextID();
		if ( ! is_array($arr_nextID) )	return false;

		parent::update("setOrderKey", $this->db, 
						array($this->orderkey, $arr_nextID[1]));
		parent::update("setOrderKey", $this->db, 
						array($arr_nextID[0], $this->id));
		
		$this->orderkey = $arr_nextID[0];
		return true;
	}


	/*******************
	 * PRIVATE METHODS *
	 *******************/
	
	function _getPrevID()	{
		$ord = new DataObject("getPrevOrderkey", $this->db, 
								array($this->showID, $this->orderkey));
		if ( empty($ord->orderkey) ) return false;
		
		$prev = new DataObject("getIDByOrderkey", $this->db, 
									array($ord->orderkey));
		if ( empty($prev->ID) ) return false;
		
		return array($ord->orderkey, $prev->ID);
	}

	function _getNextID()	{
		$ord = new DataObject("getNextOrderkey", $this->db,
								array($this->showID, $this->orderkey));
		if ( empty($ord->orderkey) ) return false;

		$next = new DataObject("getIDByOrderkey", $this->db,
									array($ord->orderkey));
		if ( empty($next->ID) ) return false;
		
		return array($ord->orderkey, $next->ID);
	}
}
?>
