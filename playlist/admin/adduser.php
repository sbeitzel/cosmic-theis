<?php
// $Id: adduser.php,v 1.7 2004/07/22 18:49:33 admin Exp $
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
checkAdminUser();

// adds user if user information is input properly
if ( isset( $action ) && $action == "adduser" )
	{
	$message = "";
	if (empty( $form[username] ) || empty( $form[password1] ) ||
		empty( $form[lastname]) || empty( $form[firstname]) ||
		empty( $form[email]) )
		$message .= "Please fill out the form completely<br>\n";
	if ( strlen($form[username]) > 20 )
		$message .= "Please enter a username between 1 and 20 characters long<br>\n";
	if ( strlen($form[password1]) > 20 )
		$message .= "Please enter a password between 1 and 20 characters long<br>\n";
	if ( $form[password1] != $form[password2] )
		$message .= "Your passwords did not match<br>\n";
	if ( getRow( "logins", "login", $form[username], 's' ))
		$message .= "Login \"$form[username]\" already exists.  Try another<br>\n";

	if ( $message == "" ) // no errors	
		{
		$id = newUser($form[username], $form[password1], $form[lastname],
					$form[firstname], $form[email]);
		}
	}

// deletes user selected in checkbox at right of users list
if ( isset($action) && $action == "delete" )
	{
	deleteUser($id_delete);
	}

// function to write a table of djs
function writeUsersList()
	{
	global $link;
	$query = "SELECT logins.ID, logins.login, users.lastname, users.firstname, users.email, users.active FROM logins,
			users WHERE users.loginsID=logins.ID ORDER BY users.active DESC, logins.login";
	$result = mysql_query( $query, $link )
		or die( "Couldn't perform query: ".mysql_error() );
	print "<FORM action=\"$PHP_SELF\" method=\"POST\">\n";
	print " <input type=\"hidden\" name=\"action\" value=\"delete\">\n";
	print "<table border=0 cellspacing=2 cellpadding = 2>\n";
	 print "\t<tr>\n";
	  print "\t\t<th bgcolor=\"#CCCCCC\">Active</th>\n";
	  print "\t\t<th bgcolor=\"#CCCCCC\">Login Name</th>\n";
	  print "\t\t<th bgcolor=\"#CCCCCC\">Last Name</th>\n";
	  print "\t\t<th bgcolor=\"#CCCCCC\">First Name</th>\n";
	  print "\t\t<th bgcolor=\"#CCCCCC\">Email Address</th>\n";
	  print "\t\t<th bgcolor=\"#CCCCCC\"></th>\n";
	  print "\t\t<th bgcolor=\"#CCCCCC\"></th>\n";
	 print "\t</tr>\n";
	$dj_array = array();
	while ($row = mysql_fetch_array($result))
		array_push($dj_array, $row);
	foreach ( $dj_array as $row )
			{
			print "\t<tr>\n";
			print "\t\t<td bgcolor=\"#DDDDDD\">";
			  print (($row[active]) ? "<b>*</b>" : "&nbsp");
			  print "</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#DDDDDD\">$row[login]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#DDDDDD\">$row[lastname]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#DDDDDD\">$row[firstname]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#DDDDDD\">$row[email]</td>\n";
			print "\t\t<td class='mid' bgcolor=\"#DDDDDD\"><a href=\"edituser.php?edit_id=$row[ID]\">Edit</a></td>";
			print "\t\t<td class='mid' bgcolor=\"#DDDDDD\" align=\"right\"><input type=\"checkbox\"
						value=\"$row[ID]\" name=\"id_delete[]\"></td>\n";
			print "\t</tr>\n";
			}
	print "\t<tr>\n\t\t<td></td><td></td><td></td><td></td><td></td>
		<td></td><td>
		<input type=\"submit\" value=\"Delete\"></td>\n\t</tr>";
	print "</table>\n";
	print "</FORM>\n";
	}


?>
<html>
<head>
<!-- This page generated by Theis Playlist Manager -->
<title>Add or delete a user</title>
<link href="../css/wprb.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
include("adminnav.inc");
?>
<h2>Add or Delete a DJ</h2>
<?php
// print error messages
if ( $message != "" )
	{
	print "<b>$message</b><p>";
	}

?>
<p>&nbsp</p>
<form action="<?= $PHP_SELF ?>" method="POST">
<input type="hidden" name="action" value="adduser">
<table cellpadding="4">
 <tr>
  <td class='mid' align="right">
   Login name:
  </td>
  <td class='mid' >
   <input type="text" name="form[username]" maxlength="20">
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   Password:
  </td>
  <td class='mid' >
   <input type="password" name="form[password1]" value="" maxlength='20'>
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   Confirm password:
  </td>
  <td class='mid' >
   <input type="password" name="form[password2]" value="" maxlength='20'>
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   User's last name:
  </td>
  <td class='mid' >
   <input type="text" name="form[lastname]">
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   User's first name:
  </td>
  <td class='mid' >
   <input type="text" name="form[firstname]">
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   User's email address:
  </td>
  <td class='mid' >
   <input type="text" name="form[email]">
  </td>
 </tr>
 <tr>
  <td>&nbsp</td>
  <td align="right">
   <p>
   <input type="submit" value="submit">
   </p>
  </td>
 </tr>
</table>

</form>
<p>&nbsp;</p>
<?php
writeUsersList();
?>

</body>
</html>
