<?php
// $Id: schedule.php,v 1.6 2004/10/16 19:36:27 admin Exp $
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

/*

class to manage on-air schedules information
child of DataObjectList

*/

import("org/wprb/WPRBDB.php");
import("org/erat/data/DataObjectList.php");

class Schedule extends DataObjectList	{
	
	var $db;
	var $id;
	var $info;
	var $overlap;
	
	function Schedule($param1 = null, $param2 = null, $param3 = null)	{
		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->Schedule" . $i . "(" . $args . ");");
	}
	
	// load the most recent schedule
	function Schedule0()	{
		$this->db = new WPRBDB();
		$result = $this->db->query("SELECT ID FROM schedules WHERE current=1");
		if (is_array( $row = mysql_fetch_row($result)) )	
{
			$this->id = mysql_result($result, 0);
			$this->DataObjectList("getSchedule", $this->db, array($this->id));
		}
	}
	
	// load a past schedule
	function Schedule1($id)	{
		$this->id = $id;
		$this->db = new WPRBDB();
		$this->DataObjectList("getSchedule", $this->db, array($this->id));
	}
	
	function Schedule2($start, $end) 	{}
	
	/**************************
	 * PUBLIC METHODS		  *
	 **************************/
	
	function getScheduleInfo()	{
		$this->info = new DataObject("getScheduleInfo", $this->db, array($this->id));
		$this->setSeason();
		return $this->info;
	}
	
	function newSchedule($season, $year)	{
		$this->db->doQuery("newSchedule", array($season, $year));
		return $this->db->insertID();
	}
	
	function deleteRow($row_id)	{
		$this->db->doQuery("deleteScheduleRow", array($row_id));
	}
	
	function checkTimeOverlap($day, $start, $end)	{
		$this->overlap = new DataObject("findTimeOverlap", $this->db,
							array($this->id, $day, $start, $start, $end, $end,
							$start, $end));
		if ($this->overlap->ID)
			return 1;
		return 0;
	}
	
	function verifyTimeOverlap ($line_id)	{
		if ($this->overlap->ID == $line_id)
			return false;
		else
			return true;
	}
	
	function addRow($user, $title, $day, $start, $end, $genre)	{
		$result1 = $this->db->query("SELECT defgenre FROM users WHERE ID='$user'");
		$userrow = mysql_fetch_array($result1);
		$this->db->doQuery("addScheduleRow", array($this->id, $user, $title, $start, $end, $day));
		if ( empty ($userrow[genre]))
			$this->db->doQuery ("genreIntervention", array ($genre, $user));
	}
	
	function updateRow ($id, $user, $title, $day, $start, $end, $genre)	{
		$this->db->doQuery ("updateScheduleRow", array ($user, $title, $start, $end, $day, $id));
	}
	
	function setCurrent()	{
		$this->db->query("UPDATE schedules SET current=0");
		$this->db->doQuery("setScheduleCurrent", array($this->id));
	}
	
	function delete()	{
		$this->db->doQuery("deleteSchedule", array($this->id));
	}
	
	/**************************
	 * PRIVATE METHODS		  *
	 **************************/
	 
	function setSeason()	{
		$seasons = array("Fall", "Spring", "Summer");
		foreach ($seasons as $key=>$season)	{
			if ($this->info->season == $key) {
				$this->info->season = $season;
			}
		}
	}
	
}
?>
