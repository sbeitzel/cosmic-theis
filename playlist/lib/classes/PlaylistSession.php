<?php
// $Id: PlaylistSession.php,v 1.3 2003/12/23 01:31:08 admin Exp $
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
PlaylistSession.php

Handles sessions for playlist

requires: dblib.inc.php

*/

class PlaylistSession	{
	var $link;
	var $timeout;
	var $active_show;
	var $uID;
	var $shID;
	
	function PlaylistSession($show_ID, $userID, &$link)	{
		$this->shID = $show_ID;
		$this->uID = $userID;
		$this->link = $link;
		$this->timeout = 30*60;
		if (! $this->_checkActiveSessions())	{
			$this->_setActive();
		}
		elseif ($this->_sessionActive())	{
			$this->_renew();
		}
		else
			die("<b>Sorry, you may not create a playlist while someone else is working on one");
	}

	/* initialize a new session */
	function _setActive()	{
		$query = "UPDATE shows SET active=0";
		mysql_query($query, $link) or die("PlaylistSession fatal error; ".mysql_error());

		$time = date();
		$query = "UPDATE shows SET active=1, lastrenewed='$time' 
			WHERE ID = $this->shID AND userID=$this->uID";
		mysql_query($query, $this->link) 
			or die("PlaylistSession fatal error: ".mysql_error());
	}
	
	/* destroy this session */
	function destroy()	{
		$query = "UPDATE shows SET active=0, lastrenewed=0";
		mysql_query($query, $this->link)
			or die("PlaylistSession fatal error: ".mysql_error());
	}
	
	/* check if there are any sessions active */
	function _checkActiveSessions()	{
		$time = date() - $this->TIMEOUT;
		$query = "SELECT * FROM shows WHERE active=1 
			AND lastrenewed > $time";
		$result = mysql_query($query, $this->link);
		if (! $result)
			die("PlaylistSession fatal error: ".mysql_error());
		$row = mysql_fetch_assoc($result);
		if (! empty($row))	{
			$this->active_show= $row;
			return true;
		}
		else
			return false;
	}

	/* check if THIS is the active session */
	function _sessionActive()	{
		if ($this->active_show[ID] == $this->shID &&
			$this->active_show[userID] == $this->uID)	{
			return true;
		}
		else
			return false;
	}
	
	/* renew the session */
	function _renew()	{
		$time = date();
		$query = "UPDATE shows SET lastrenewed=$time 
				WHERE ID=$this->shID AND userID=$this->uID";
		$result = mysql_query($query, $this->link);
		if (! $result)
			die("PlaylistSession fatal error: ".mysql_error());
		if (mysql_affected_rows($result))
			return true;
	}

}
