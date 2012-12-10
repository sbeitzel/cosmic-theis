<?php
/*****************************************************************************
* Theis Playlist Manager -- An interactive web application for creating,     
*
* editing, and publishing radio playlists.                                   
*
*                                                                            
*
* Copyright (C) 2003  Aaron Forrest                                          
*
*                                                                            
*
* This program is free software; you can redistribute it and/or              
*
* modify it under the terms of the GNU General Public License                
*
* as published by the Free Software Foundation; either version 2             
*
* of the License, or (at your option) any later version.                     
*
*                                                                            
*
* This program is distributed in the hope that it will be useful,            
*
* but WITHOUT ANY WARRANTY; without even the implied warranty of             
*
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the              
*
* GNU General Public License for more details.                               
*
*                                                                            
*
* You should have received a copy of the GNU General Public License          
*
* along with this program; if not, write to the Free Software                
*
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, 
USA.*
*****************************************************************************/

/* class to control lists of schedules */

import("org/wprb/WPRBDB.php");
import("org/erat/data/DataObjectList.php");

class ScheduleList extends DataObjectList	{
	
	var $db;
	
	function ScheduleList ($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->ScheduleList" . $i . "(" . $args . ");");
	}

	function ScheduleList0() {
		$this->db = new WPRBDB();
		$this->DataObjectList("getScheduleList", $this->db, array(null));
		$this->setSeason();
	}
	
	function ScheduleList1()	{}
	
	function ScheduleList2()	{}
	
	
	
	/**************************
	 * PRIVATE METHODS		  *
	 **************************/
	 
	function setSeason()	{
		$seasons = array("Fall", "Spring", "Summer");
		$this->reset();
		while ($this->hasNext() )	{
			$row = $this->next();
			foreach ($seasons as $key=>$season)	{
				if ($row->season == $key) {
					$row->season = $season;
				}
			}
		}
	}
	
}




?>
