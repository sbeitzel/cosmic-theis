<?php
// $Id: PlaylistSession.php,v 1.4 2003/12/23 05:50:56 admin Exp $
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
	
	/**********************
	*     CONSTRUCTORS     * 
	***********************/

	function PlaylistSession($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->PlaylistSession" . $i . "(" . $args . ");");
	}

	function PlaylistSession0()	{}
	
	// check for active sessions
	function PlaylistSession1(&$link)	{
		$this->link = $link;
		$this->timeout = 5*60;
		$this->active_show = $this->_checkActiveSessions();
	}
	
	function PlaylistSession2()	{}
	
	function PlaylistSession3($show_ID, $userID, &$link)	{
		$this->shID = $show_ID;
		$this->uID = $userID;
		$this->link = $link;
		$this->timeout = 5*60;
		if (! $this->_checkActiveSessions() )	{
			$this->_setActive()
				or die("<b> Sorry this playlist has expired </b>\n");
		}
		elseif ($this->_sessionActive())	{
			$this->_renew();
		}
		else
			die("<b>Sorry, you may not create a playlist while someone else is working on one");
	}

	/************************
	*    PUBLIC METHODS     *
	*************************/
	
	/* destroy this session */
	function destroy()	{
		$query1 = "UPDATE shows SET active=-1 where ID=$this->shID";
		mysql_query($query1, $this->link)
			or die("PlaylistSession->destroy fatal error: ".mysql_error());
		$query2 = "UPDATE shows SET active=0, lastrenewed=0
					WHERE active!=-1";
		mysql_query($query2, $this->link)
			or die("PlaylistSession fatal error: ".mysql_error());
	}
	
	/* clear all sessions.  admin use only */
	function clearAll()	{
		$query = "UPDATE shows SET active=0, lastrenewed=0 WHERE active!=-1";
		mysql_query($query, $this->link)
			or die("PlaylistSession->clearAll fatal error: ".mysql_error());
		return;
	}
	

	/************************
	*    PRIVATE METHODS    *
	*************************/
	
	/* check if there are any sessions active */
	function _checkActiveSessions()	{
		$time = time() - $this->timeout;
		$query = "SELECT * FROM shows WHERE active=1 
			AND lastrenewed > $time";
		$result = mysql_query($query, $this->link);
		if (! $result)
			die("PlaylistSession fatal error: ".mysql_error());
		$row = mysql_fetch_assoc($result);
		if (! empty($row))	{
			$this->active_show = $row;
			return true;
		}
		else
			return false;
	}

	/* initialize a new session */
	function _setActive()	{
		$query1 = "UPDATE shows SET active=0, lastrenewed=0
					WHERE active != -1 OR active IS NULL";
		mysql_query($query1, $this->link) 
			or die("PlaylistSession fatal error; ".mysql_error());
		$time = time();
		$query2 = "UPDATE shows SET active=1, lastrenewed='$time' 
			WHERE ID=$this->shID AND userID=$this->uID AND active != -1";
		mysql_query($query2, $this->link) 
			or die("PlaylistSession fatal error: ".mysql_error());
		return mysql_affected_rows($this->link);
	}

	/* check if _this_ is the active session */
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
		$time = time();
		$query = "UPDATE shows SET lastrenewed=$time 
				WHERE ID=$this->shID AND userID=$this->uID AND active=1";
		$result = mysql_query($query, $this->link);
		if (! $result)
			die("PlaylistSession fatal error: ".mysql_error());
		if (mysql_affected_rows($this->link))
			return true;
	}

}
?>
