<?php
// $Id: RSSfeed.php,v 1.5 2004/03/01 16:22:04 phil Exp $
 /*****************************************************************************
 * Theis Playlist Manager -- An interactive web application for creating,     *
 * editing, and publishing radio playlists.                                   *
 *                                                                            *
 * Copyright (C) 2003  Aaron Forrest                                          *
 * RSSFeed 	 2004  Philip Davidson                                                                           *
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



function RSSFormatLocalDate ($date, $format="") { 
	//RFC822

	global $RSStzabbr;
	global $RSStzoffset;

	if ( $date == "" ) return "";
	if ( $date == 0  ) $date = strtotime($date);
	$rfcdate;
	if ( $format == "" ) { 
		$rfcdate 	= gmdate( "D, d M Y H:i:s", $date + $RSStzoffset);
		$rfcdate       .= " $RSStzabbr";
	}
	else { 
		$rfcdate	= gmdate( $format, $date + $RSStzoffset);
	}
	return $rfcdate;

}

class RSSItem { 

	var $standard;
	var $custom;
	
	function RSSItem($title="", $pubdate="", $link="", $descript="", $standard=array(), $custom = array()) {
		$this->standard = array();
		$this->standard[title] 		= $title;
		$this->standard[pubDate]	= $pubdate;
		$this->standard[link] 	 	= $link;
		$this->standard[description]  	= $descript;
		$this->standard 		= array_merge($this->standard, $standard );

		$this->custom = array();
		$this->custom 			= array_merge($this->morekeys, $morekeys );
	}	

	function printme() { 

		global $RSShtmlcleaner;

		//format values
		if ( $this->standard[title] == "" && $this->standard[description] == "" ) { 
			$this->standard[title]		= "error: see description";
			$this->standard[description]	= "error: well-formed items must have either title, description, or both";
		}
		$fstandard 	= "";
		foreach ($this->standard as $key=>$val ) {
			if ( $key == "pubDate" ) { 
				$val = RSSFormatLocalDate($val); 
			}
			if ( $val != "" ) { 
				$fval 		= $RSShtmlcleaner($val);
				$fstandard 	.= "\t<$key>$fval</$key>\n";
			}
		}

		$fcustom 	= "";
		foreach ($this->custom as $key=>$val ) {
			$fval 		= $RSShtmlcleaner($val); 
			$fcustom 	.= "\t<$key>$fval</$key>\n";
		}

		//build item
		$itemdata  =		"<item>\n";
		$itemdata .=		$fstandard;
		$itemdata .= 		$fcustom; //already tagged
		$itemdata .=		"</item>\n";	

		echo $itemdata;
	}
}

class RSSImage { 

	var $imgdata;

	function RSSImage($url="",$title="",$w=0,$h=0,$link=""){ 
		$this->imgdata  = array();		
		$this->imgdata[url] 	= $url;
		$this->imgdata[title]	= $title;
		$this->imgdata[width] 	= max(0,min(144,$w));
		$this->imgdata[height]	= max(0,min(144,$h));
		$this->imgdata[link]	= $link;
	}	

	function printme() { 
		//build item
		global $RSShtmlcleaner;
		$itemdata =		"<image>\n";
		foreach ($this->imgdata as $key=>$val) { 
			if ( $val != "" )	{ 
				$fval 		= $RSShtmlcleaner($val); 
				$itemdata      .= "\t<$key>$fval</$key>\n";
			}
		}
		$itemdata .=		"</image>\n";	

		echo $itemdata;
	}
}

class RSSChannel { 

//blogs.law.harvard.edu/tech/rss

/** required in channel header
* 	title, link, description
*
*** optional data
* 	language, copyright, managingEditor, webMaster
* 	pubDate, lastBuildDate, category, generator, docs, cloud
* 	ttl, rating, textInput, skipHours, skipDays
****************************/

	var $reqinfo;
	var $optinfo;
	var $image;

//channel content
	var $items;

	function RSSChannel($title="Radioware RSS feed",$link="http://veldt.slabofsound.com/xfeed.php",$desc="generic rss feed software",$optinfo= array()) {

		$this->reqinfo 		= array();
		$this->reqinfo[title]	= $title;
		$this->reqinfo[link] 	= $link;
		$this->reqinfo[description] = $desc;
		$this->optinfo 		= array();
		$this->optinfo[lastBuildDate] = time();
		$this->optinfo 		= array_merge( $this->optinfo, $optinfo);
		$this->image 	 	= "";
		$this->items 	 	= array();
	}

	function printme() { 

		global $RSShtmlcleaner;

		echo   "<channel>\n";
		foreach ( $this->reqinfo as $key=>$val ) { 	//salad
			if ( $val ) { 
				$fval 	= $RSShtmlcleaner($val); 
				echo "<$key>$fval</$key>\n";
			}
			else {
				echo "<$key />\n";
			}
		}
		foreach ( $this->optinfo as $key=>$val ) { 	//soup
			if ( $key == "lastBuildDate" ) $val = RSSFormatLocalDate($val); 
			$fval 	= $RSShtmlcleaner($val);
			echo "<$key>$fval</$key>\n";
		}
		if ( $this->image ) { 				//bread
			$this->image->printme();
		}

		foreach ( $this->items as $item ) {   		//main course
			$item->printme();
		}
		echo   "</channel>\n";
	}

	function setImage($url="",$title="",$link="",$w=0,$h=0) { 
		$this->image = new RSSImage($url, $title,$w,$h,$link);
	}

	function pushItemFront($title = "",$date="",$link="",$descript="", $morekeys = array()) { 
		$itm = new RSSItem($title,$date,$link,$descript,$morekeys);
		array_unshift ( $this->items, $itm );
	}

	function pushItemBack ($title = "",$date="",$link="",$descript="", $morekeys = array()) {  
		$itm = new RSSItem($title,$date,$link,$descript,$morekeys);
		array_push($this->items, $itm);
	}

	function clearItems()  {
		$items = array();
	}	
}

class RSSFeed { 

	var $channel;

	function RSSFeed($title="Radioware RSS feed",$link="http://veldt.slabofsound.com/xfeed.php",$desc="generic rss feed software",$extrainfo= array()) { 
		$this->channel = new RSSChannel($title,$link,$desc,$extrainfo);
	}	

	function send_http_header() { 
		//http content type header
		header('Content-type: text/xml');
	}

	function write() { 
		//xml info
		echo 	"<?xml version='1.0' encoding='utf-8' ?>\n";

		/***********************
		* DOCTYPE GOES HERE!   *
		* once i figure it out *
		***********************/

		//rss data
		echo 	"<rss version='2.0'>\n";

		//channel data
		$this->channel->printme();
		
		//close rss
		echo	"</rss>\n";
	}

}

?>
