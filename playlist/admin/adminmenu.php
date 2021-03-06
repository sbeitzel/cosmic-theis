<?php
// $Id: adminmenu.php,v 1.6 2004/10/16 03:09:10 admin Exp $
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

include("../lib/config.inc.php");
include("../lib/dblib.inc.php");
include("../lib/usrlib.inc.php");

//checks to see if logged-in user is administrator, if not returns to login.php
checkAdminUser();


?>
<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
<title>Admin menu</title>
<link href="../css/wprb.css" rel="stylesheet" type="text/css">
</head>
<body>
&nbsp;<p>
<h2>&nbsp;&nbsp;Choose a task:</h2>
<dl>
	<dt><span class='text'>&nbsp;&nbsp;&nbsp;&nbsp;<a href="adduser.php">Add or delete users</a></span>
	<dt><span class='text'>&nbsp;&nbsp;&nbsp;&nbsp;<a href="manageschedules.php">Schedule Manager</a></span><p>
	
	<dt><span class='text'>&nbsp;&nbsp;&nbsp;&nbsp;<a href="plmover.php">Playlist Admin</a></span>
	<dt><span class='text'>&nbsp;&nbsp;&nbsp;&nbsp;<a href="top30.php">Generate Top 30</a></span>
	<dt><span class='text'>&nbsp;&nbsp;&nbsp;&nbsp;<a href="dbsearch.php">Browse Database</a></span><p>
	<dt><span class='text'>&nbsp;&nbsp;&nbsp;&nbsp;<a href="kill.php" onClick="return window.confirm('Are you sure you want to kill any active playlist session?');">Kill Something</a></span>
	<dt><span class="text">&nbsp;&nbsp;&nbsp;&nbsp;<a href="passwd.php">Change Password</a></span><p>
	<dt><span class='text'>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../lib/logout.php">Logout</a></span>
</dl>




</body>
</html>
