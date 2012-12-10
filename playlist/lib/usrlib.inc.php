<?php
// $Id: usrlib.inc.php,v 1.6 2004/08/08 05:16:55 admin Exp $
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

// library to store user-related functions

// start or resume session, with session variables registered
// in the $session[] array.
session_start();
session_register("session");


// function to check password on login

function checkPass( $login, $password )
	{
	global $link;
	// uncomment if not using jscript md5 passwords
	// $password = md5($password);
	$result = mysql_query( "SELECT ID, login, password FROM logins
				WHERE login='$login' and password='$password'", $link);
	if ( ! $result )
		die ( "checkPass fatal error: ".mysql_error() );
	if ( mysql_num_rows( $result ) )
		return mysql_fetch_array( $result );
	return false;
	}
	
	
//function to create array storing session variables

function cleanMemberSession( $id, $login, $pass )
	{
	global $session;
	$session[id] = $id;
	$session[login] = $login;
	$session[password] = $pass;
	$session[logged_in] = true;
	}

	
// function to check admin user's session data against db

function checkAdminUser()
	{
	global $session, $logged_in;
	$session[logged_in] = false;
	$logins_row = getRow( "logins", "id", $session[id] );
	if ( ! $logins_row || $logins_row[login] != $session[login] || $logins_row[password] != $session[password] || $logins_row[login] != "admin" )
		{
		header( "Location: ../login.php" );
		exit;
		}
	$session[logged_in] = true;
	return $logins_row;
	}

	
//function to check user's session data against database

function checkUser()
	{
	global $session, $logged_in;
	$session[logged_in] = false;
	$logins_row = getRow( "logins", "ID", $session[id] );
	$users_row = getRow( "users", "loginsID", $session[id] );
	if ( ! $logins_row || $logins_row[login] != $session[login] || $logins_row[password] != $session[password] 
		|| $session[login] == "admin" || $users_row[active]!=1)
		{
		header( "Location: ../login.php" );
		exit;
		}
	$session[logged_in] = true;
	return $logins_row;
	}

function checkGuestUser()
	{
	global $session;
	// check if guest user is operating from local machine
	// uncomment on installation:
	if ( $session[login] == 'guest' && 
	     $_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'] )	{
		header( "Location: ../lib/logout.php" );
		exit;
	}
	if ( $session[login] == 'guest' )
		return true;
	else
		return false;
}
	
function fetchUserID()	{
	global $session, $link;
	$result = mysql_query("SELECT ID FROM users WHERE loginsID=$session[id]", $link)
			or die("fetchUserID fatal error: ".mysql_error());
	$id = mysql_fetch_row($result);
	return $id[0];
}
	
// function to create a new user in DB

function newUser( $login, $pass, $lastname, $firstname, $email )
	{
	global $link;
	$pass = md5($pass);
	$result = mysql_query( "INSERT INTO logins (login, password)
						VALUES('$login', '$pass')", $link);
	$loginsID = mysql_insert_id($link);
	$result2 = mysql_query( "INSERT INTO users (loginsID, lastname, firstname, email, 
					bgcolor, tablecolor, textcolor, tablehead, tabletext, active)
					VALUES('$loginsID', '$lastname', '$firstname', 
					'$email', 'FFFFFF', 'EEEEEE', '000000', 'DDDDDD', '000000', 1)", $link);
	return mysql_insert_id($link );
	}
	

// function to delete a user

function deleteUser ($id_delete)
	{
	global $link;
	if (is_array($id_delete))
		{
		foreach ($id_delete as $id)
			{
			$query1 = "DELETE FROM logins WHERE ID=$id";
			$result1 = mysql_query($query1, $link);
			if ( ! $result1 )
				die( "deleteUser fatal error: ".mysql_error() );
			$query2 = "DELETE FROM users WHERE loginsID=$id";
			$result2 = mysql_query($query2, $link);
			if ( ! $result2)
				die ( "deleteUser fatal error: ".mysql_error() );
			}
		}
	else
		{
		$query1 = "DELETE FROM logins WHERE ID=$id_delete";
		$result1 = mysql_query($query1, $link);
		if ( ! $result1 )
			die( "deleteUser fatal error: ".mysql_error() );
		$query2 = "DELETE FROM users WHERE loginsID=$id_delete";
		$result2 = mysql_query($query2, $link);
		if ( ! $result2)
			die ( "deleteUser fatal error: ".mysql_error() );
		}
	return true;
	}


// function to edit user info

function editUser ($login, $pass, $lastname, $firstname, $email, $id, $active )
	{
	global $link;
	if (! empty($pass) )	{
		$pass = md5($pass);
		$query1 = "UPDATE logins SET login='$login',
				password='$pass' WHERE ID=$id";
	}
	else
		$query1 = "UPDATE logins SET login='$login' WHERE ID=$id";
	$result1 = mysql_query($query1, $link);
	if (! $result1)
		die("editUser fatal error: ".mysql_error() );
	$query2 = "UPDATE users SET lastname='$lastname',
			firstname='$firstname', email='$email', 
			active='$active' WHERE loginsID=$id";
	$result2 = mysql_query($query2, $link);
	if (! $result2 )
		die( "editUser fatal error: ".mysql_error() );
	return mysql_affected_rows($link);
	}


// function to update user preferences

function updateUserPrefs( $email, $publish, $homepage, $offsite, $bgcolor, $tablehead, $tablecolor,
			$textcolor, $tabletextcolor, $weekday, $hour, $min, $duration, $genre,
			$othergenre, $djname, $title, $subtitle, $desc, $id, $pass)
	{
	global $link;
	if (empty($othergenre))
		$othergenre = $genre;
	// set new preferences [except password] in db
	$query = "UPDATE users SET
			email='$email', emailpublish='$publish', link='$homepage', offsite='$offsite',
			bgcolor='$bgcolor', tablehead='$tablehead', tablecolor='$tablecolor',
			textcolor='$textcolor', tabletext='$tabletextcolor', defday='$weekday', defhour='$hour',
			defmin='$min', defduration='$duration', defgenre='$genre', defothergenre='$othergenre',
			defdjname='$djname', deftitle='$title', defsubtitle='$subtitle', defdesc='$desc' ";
	$query .= "WHERE loginsID='$id'";
	$result = mysql_query($query, $link);
	if (! $result)
		die ("updateUserPrefs() fatal error: ".mysql_error() );
		
	// set new password in db
	if (! empty($pass))
		{
		$pass = md5($pass);
		$query2 = "UPDATE logins SET password='$pass' WHERE ID='$id'";
		$result2 = mysql_query ($query2, $link);
		if (! $result2)
			die("updateUserPrefs() fatal error: ".mysql_error() );
		global $session;
		$session[password] = $pass; // sets session password to $pass so user need not login again.
		}
	return mysql_affected_rows($link);
	}
	
function changeAdminPassword($pw)	{
	global $link;
	$pw = md5($pw);
	$query = "UPDATE logins set password='$pw' where login='admin'";
	mysql_query($query, $link)
		or die("changeAdminPassword fatal error: ".mysql_error());
	return mysql_affected_rows($link);
}

// function to create a list of usernames as options in a select form

function writeUsernameOptions($def=null)	{
	global $link;
	$query = "SELECT login, users.ID FROM logins, users WHERE login!='admin' AND loginsID=logins.ID ORDER BY login";
	$result = mysql_query($query, $link);
	if (! $result)
		die("writeUsernameOptions() fatal error: ".mysql_error());
	while ($row = mysql_fetch_row($result))	{
		print "<option value=$row[1]";
		print (($def==$row[1])?" SELECTED":"");
		print ">$row[0]\n";
	}
}
?>
