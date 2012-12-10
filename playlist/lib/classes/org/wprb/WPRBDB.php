<?php
// $Id: WPRBDB.php,v 1.17 2004/10/16 19:36:26 admin Exp $
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


/************************************************************
 *	Database for  archive, where queries are specified.
 *	Connection parameters already set as constants.
 ***********************************************************/

import("org/erat/data/Database.php");

class WPRBDB extends Database {
	function WPRBDB() {
		parent::Database(__DBUSER__,__DBPASSWORD__,__DBHOST__,__DBNAME__);
		$this->initializeQueries();
		$this->connect();
	}
	
	function initializeQueries() {
		/* videos */
		$this->queries["getPlaylist"] = "SELECT * FROM playlist WHERE showID = '%01' ORDER BY orderkey";
		$this->queries["getPlaylistInfo"] = "SELECT * FROM shows where ID = '%01'";
		$this->queries["getUserInfo"] = "SELECT * FROM users WHERE ID = '%01'";
		$this->queries["updateUserPrefs"] = "UPDATE users SET
			email='%01', emailpublish='%02', link='%03',
			bgcolor='%04', tablehead='%05', tablecolor='%06',
			textcolor='%07', tabletext='%08', defday='%09', defhour='%10',
			defmin='%11', defduration='%12', defgenre='%13', defothergenre='%14',
			defdjname='%15', deftitle='%16', defsubtitle='%17' 
			WHERE loginsID=%18";
		$this->queries["updatePass"] = "UPDATE logins SET password='%01' WHERE ID=%02";
		$this->queries["getSchedule"] = "SELECT schedule_data.*, link, offsite FROM schedule_data, users WHERE schedulesID = %01 AND userID=users.ID ORDER BY start";
		$this->queries["getScheduleInfo"] = "SELECT * FROM schedules WHERE ID='%01'";
		$this->queries["getPlaylists"] = "SELECT * FROM shows WHERE userID=%01 
											ORDER BY starttime DESC";
		$this->queries["getListOfUsers"] = "SELECT users.*, login FROM users, logins WHERE users.loginsID = logins.ID ORDER BY defgenre, defdjname";
		$this->queries["newSchedule"] = "INSERT INTO schedules (season, year) values(%01, %02)";
		$this->queries["addScheduleRow"] = "INSERT INTO schedule_data (schedulesID, userID, title, start, end, day) VALUES (%01, '%02', \"%03\", %04, %05, %06)";
		$this->queries["updateScheduleRow"] = "UPDATE schedule_data SET 
						userID=%01, title=\"%02\", start=%03, end=%04, day=%05 
						WHERE ID=%06";
		$this->queries["genreIntervention"] = "UPDATE users SET defgenre='%01'
											WHERE ID='%02'";
		$this->queries["deleteScheduleRow"] = "DELETE FROM schedule_data WHERE ID=%01";
		$this->queries["setScheduleCurrent"] = "UPDATE schedules SET current='1' WHERE ID=%01";
		$this->queries["getScheduleList"] = "SELECT * FROM schedules ORDER BY current DESC, year DESC, season DESC";
		$this->queries["deleteSchedule"] = "DELETE FROM schedules WHERE ID=%01";
		$this->queries["getActiveID"] = "SELECT * FROM shows WHERE active=1";
		$this->queries["findTimeOverlap"] = "SELECT ID FROM schedule_data WHERE 
										schedulesID=%01 AND day=%02 AND 
										( (start<%03 AND %04<end) OR (start<%05 AND %06<end)
										OR (start=%07 AND end=%08) )";
		$this->queries["getShowList"] = "SELECT * FROM shows WHERE starttime >= %01 
											AND starttime <= %02 ORDER BY starttime DESC";
		$this->queries["getMaxVal"] = "SELECT MAX(ID) AS ID FROM playlist WHERE
									artist<>\"*****\"";
		$this->queries["getLatestSong"] = "SELECT artist, song FROM playlist,
					shows WHERE playlist.ID=%01 AND playlist.showID=shows.ID AND 
					shows.active=1";
		$this->queries["getPlaylistLine"] = "SELECT * FROM playlist 
										WHERE ID=%01";
		$this->queries["getPrevOrderkey"] = "SELECT MAX(orderkey) AS orderkey 
									FROM playlist 
									WHERE showID = %01 AND orderkey < %02";
		$this->queries["getNextOrderkey"] = "SELECT MIN(orderkey) AS orderkey
											FROM playlist
											WHERE showID=%01 AND orderkey>%02";
		$this->queries["getIDByOrderkey"] = "SELECT ID from playlist WHERE
											orderkey='%01'";
		
		$this->queries["updatePlaylistLine"] = "UPDATE playlist SET artist='%01',
					song='%02', album='%03', label='%04', comments='%05', emph='%06',
					request='%07', comp='%08' WHERE ID=%09";
		$this->queries["updateClassicalPlaylistLine"] = "UPDATE playlist SET
					artist='%01', song='%02', ensemble='%03', conductor='%04',
					performer='%05', label='%06', comments='%07', emph='%08'
					WHERE ID='%09'";
		$this->queries["setOrderKey"] = "UPDATE playlist SET orderkey='%01'
											WHERE ID='%02'";
					
		$this->queries["deleteLine"] = "DELETE FROM %01 WHERE ID=%02";
	
		$this->queries["deletePlaylist"] = "DELETE FROM shows WHERE ID='%01'";
		$this->queries["changePlaylistOwner"] = "UPDATE shows SET userID='%01' WHERE ID='%02'";
	}

}
?>
