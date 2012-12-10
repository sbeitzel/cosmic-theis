<?php
// $Id: Emailer.php,v 1.2 2003/12/23 01:30:54 admin Exp $

class Emailer {
	var $from;
	var $fromName;
	var $to;
	var $subject;
	var $content;
	
	function Emailer($fromName,$from) {
		$this->fromName = $fromName;
		//$this->from = "From: ".$this->fromName." <".$from.">\r \n";
		$this->from = $from;
	}
	
	function setTo($to) {
		$this->to = $to;
	}
	
	function setSubject($subject) {
		$this->subject = $subject;
	}
	
	function setContent($content) {
		$this->content = $content;
	}
	
	function send() {
		$headers .= "From: ".$this->fromName." <".$this->from.">\r \n";
		$headers .= "Reply-To: <".$this->from.">\n";
		$headers .= "X-Sender: <".$this->from.">\n";
		$headers .= "X-Mailer: PHP4\n"; //mailer
		$headers .= "X-Priority: 3\n"; //1 UrgentMessage, 3 Normal
		$headers .= "Return-Path: <".$this->from.">\n";
		if (($this->to!="")&&($this->subject!="")&&($this->content!="")) {
			mail($this->to, $this->subject, wordwrap(stripslashes($this->content)), $headers);
		}
	}
}

?>