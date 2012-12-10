<!-- Theis Playlist Manager dj navigation bar -->

<table width='100%'>
 <tr><td class='text'>
   <a href="usermenu.php">Main menu</a> |
<?php
if ( ! checkGuestUser() )	{	?>
   <a href="userprefs.php">Edit Preferences</a> |
<?php	}	?>
   <a href="newplaylist.php">Start new playlist</a> |
   <a href="newoldplaylist.php">Enter old playlist</a> |
   <a href="oldplaylists.php">Edit a playlist</a> |
   <a href="../lib/logout.php">Logout</a>
 </td><td class='none' align='right'>
   <a href='#' onClick="window.open('../lib/reportbug.php','bug_window','width=500,height=300')">
		<b>send bug report</b></a>
 </td></tr>
</table>
  <p>
