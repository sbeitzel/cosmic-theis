<?php
// $Id: UserManager.php,v 1.2 2003/12/23 01:31:25 admin Exp $

/************************************************************
 **	Simple user object for simple authentication
 **	Another application should extend this class.
 **
 ** UserManager() - constructor
 ** addUser($a,$b) - adds a user with "a" and "b" as un/pw
 **	check($a,$b) - checks "a" and "b" against all users
 ** get($a,$b) - gets user with "a"/"b" as un/pw
 *************************************************************/

import("org/erat/util/OEIterator.php");

class UserManager extends OEIterator {

	function UserManager () {
		parent::OEIterator();
	}

	function check($un,$pw) {
		$this->reset();
		while ( $this->hasNext() ) {
			$user = $this->next();
			if ( $user->check($un,$pw) ) return true;
		}
		return false;
	}

	function getUser($un,$pw) {
		$this->reset();
		while ( $this->hasNext() ) {
			$user = $this->next();
			if ( $user->check($un,$pw) ) return $user;
		}
		return null;
	}
	
	function addUser($un,$pw) {
		$user = new User($un,$pw);
		$this->add($user);
	}
}


/************************************************************
 **	Simple user object for simple authentication
 **	Another application should extend this class.
 **
 ** User($a,$b) - creates a user named "a" with password "b"
 ** check($a,$b) - does "a" and "b" match this user?
 *************************************************************/

class User {
	var $un;
	var $pw;

	function User($un,$pw) {
		$this->un = $un;
		$this->pw = $pw;
	}
		
	function check($un,$pw) {
		if (($this->un==$un)&&($this->pw==$pw)) return true;
		else return false;
	}
}

?>