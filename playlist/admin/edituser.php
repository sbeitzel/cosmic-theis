<?php
// $Id: edituser.php,v 1.6 2004/07/22 18:49:33 admin Exp $
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

// updates user info if input properly
if ( isset($action) && $action == "edit")
	{
	$message = "";
	if (empty( $form[username] ) || 
		empty( $form[lastname]) || empty( $form[firstname]) ||
		empty( $form[email]) )
		$message .= "Please fill out the form completely<br>\n";
	
	if ( getRow("logins", "login", $form[username]) 
				&& $form[username] != $form[currlogin] )
		$message .= "Login \"$form[username]\" already exists.  Try again<br>\n";
	if ( ! empty($_POST[form][password1]) )
		if ( $form[password1] != $form[password2] )
			$message .= "Your passwords did not match<br>\n";
	
	if ( $message == "" ) // no errors
		{
		$id = editUser($form[username], $form[password1], $form[lastname],
					$form[firstname], $form[email], $form[djID], $form[active]);
		header("Location: adduser.php");
		}
	}



?>
<html>
<!-- This page generated by Theis Playlist Manager -->
<head>
 <title>Edit user info</title>
 <link href="../css/wprb.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
include("adminnav.inc");

if ( message != "" )
	{
	print "<b>$message </b><br>\n";
	}

if (! empty( $edit_id))
	{
	$query = "SELECT logins.ID, logins.login, users.lastname, users.firstname, users.email, active
		FROM logins, users WHERE logins.ID=$edit_id
		AND users.loginsID=$edit_id";
	$result = mysql_query($query, $link);
	if (! $result)
		die ("dj_info mysql query fatal error: ".mysql_error() );
	$dj_info_array = array();
	$dj_info_array = mysql_fetch_assoc($result);
	}

?>
<h2>Edit user info</h2>
<form method="POST">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="form[djID]" value="<?= $dj_info_array[ID] ?>">
<input type="hidden" name="form[currlogin]" value="<?=$dj_info_array[login]?>">
<table cellpadding="4">
 <tr>
  <td class='mid' align="right">
   Login name:
  </td>
  <td class='mid' >
   <input type="text" name="form[username]" value="<?= $dj_info_array[login] ?>" maxlength="20">
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   Password:
  </td>
  <td class='mid'>
   <input type="password" name="form[password1]" value="" maxlength="20">
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   Confirm password:
  </td>
  <td class='mid'>
   <input type="password" name="form[password2]" value="" maxlength="20">
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   User's last name:
  </td>
  <td class='mid'>
   <input type="text" name="form[lastname]" value="<?= $dj_info_array[lastname] ?>">
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   User's first name:
  </td>
  <td class='mid'>
   <input type="text" name="form[firstname]" value="<?= $dj_info_array[firstname] ?>">
  </td>
 </tr>
 <tr>
  <td class='mid' align="right">
   User's email address:
  </td>
  <td class='mid'>
   <input type="text" name="form[email]" value="<?= $dj_info_array[email] ?>">
  </td>
 </tr>
 <tr>
  <td class='mid' align='right'>
  	Active DJ?: 
  </td>
  <td class='mid'>
   <input type='checkbox' name="form[active]" value="1" <?= ($dj_info_array[active])?"CHECKED":""; ?>>
   <font size='-2'>warning: unchecking this box will terminate user's ability to log in</font>
  </td>
  	
 </tr>
 <tr>
  <td>&nbsp</td>
  <td align="left">
   <p>
   <input type="submit" value="submit changes">
   </p>
  </td>
 </tr>
</table>

</form>
</body>
</html>
