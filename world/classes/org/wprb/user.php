<?php
// $Id: user.php,v 1.4 2003/12/23 05:50:56 admin Exp $
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

/* class to store a user's information:: child of DataObject class */

import("org/erat/data/DataObject.php");
import("org/erat/data/DataObjectList.php");
import("org/wprb/WPRBDB.php");


class User extends DataObject	{
	
	var $db;
	var $id;
	
	function User($id)	{
		$this->id = $id;
		$this->db = new WPRBDB;
		$this->DataObject("getUserInfo", $this->db, array($id));
	}
	
	function returnUserInfo()	{
		return array("ID"=>$this->ID, "loginsID"=>$this->loginsID, "lastname"=>$this->lastname,
					"firstname"=>$this->firstname, "email"=>$this->email, "emailpublish"=>$this->emailpublish,
					"link"=>$this->link, "bgcolor"=>$this->bgcolor, "tablecolor"=>$this->tablecolor,
					"textcolor"=>$this->textcolor, "tablehead"=>$this->tablehead,
					"tabletext"=>$this->tabletext, "defduration"=>$this->defduration,
					"defdjname"=>$this->defdjname, "deftitle"=>$this->deftitle,
					"defsubtitle"=>$this->defsubtitle, "defday"=>$this->defday, 
"defhour"=>$this->defhour,
					"defmin"=>$this->defmin, "defgenre"=>$this->defgenre, 
					"defothergenre"=>$this->defothergenre);
	}
	
	function updatePreferences( $email, $publish, $homepage, $bgcolor, $tablehead, $tablecolor,
			$textcolor, $tabletextcolor, $weekday, $hour, $min, $duration, $genre,
			$othergenre, $djname, $title, $subtitle, $pass)	{
		if ($genre != "Specialty")
			$othergenre = $genre;
		$userprefs = array($email, $publish, $homepage, $bgcolor, $tablhead, $tablecolor,
						$textcolor, $tabletextcolor, $weekday, $hour, $min, $duration, $genre, 
						$othergenre, $djname, $title, $subtitle, $this->id);
		// set new preferences [except password] in db
		$this->update("updateUserPrefs", $this->db, $userprefs);
		// set new password in db
		if (! empty($pass))
			{
			$userpass = array($pass, $this->id);
			$this->update("updatePass", $this->db, $userpass);
			global $session;
			$session[password] = $pass; // sets session password to $pass so user need not login again.
			}
		return mysql_affected_rows($link);	
	}
	
	
	function getPlaylists()	{
		$playlists = new DataObjectList("getPlaylists", $this->db, array($this->id));
		return $playlists;
	}
}


?>
