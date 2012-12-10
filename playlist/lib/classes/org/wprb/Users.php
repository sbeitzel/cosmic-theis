<?php
// $Id: Users.php,v 1.4 2003/12/23 05:50:56 admin Exp $
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

/* class to access information about all users *
 * child of DataObjectList class               */

import("org/erat/data/DataObjectList.php");
import("org/wprb/WPRBDB.php");


class Users extends DataObjectList	{
	
	var $db;
	var $id;
	
	function Users($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->Users" . $i . "(" . $args . ");");
	}

	/* load the list of users */
	function Users0()	{
		$this->id = $id;
		$this->db = new WPRBDB();
		parent::DataObjectList("getListOfUsers",$this->db);
	}
	function Users1( $a1, $a2 ) {}
	function Users2( $a1, $a2, $a3 ) {}
	
	
}




?> 
