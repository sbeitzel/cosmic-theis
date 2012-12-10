<?php
// $Id: ObjectCache.php,v 1.1 2004/02/27 20:00:02 phil Exp $
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

class ObjectCache { 

	var $base_cache_dir;
	var $cache_file;
	var $time_out;
	var $error;

	function ObjectCache($cache_file="foo.bar",$time_out=0) { 
		$this->base_cache_dir	= __OBJECTCACHE_DIR__;
		$this->time_out		= __OBJECTCACHE_TIMEOUT__;
		if ( $time_out ) $this->time_out = $time_out;
		$this->cache_file = $cache_file;
		$this->error = "no error\n";
		if ( !file_exists ( $this->base_cache_dir ) ) { 
			$stat = @mkdir( $this->base_cache_dir, 0755 );
			if ( ! $stat ) {
				$this->error = "cache- couldn't make directory : $this->base_cache_dir \n";
			} 
			else { 
				$this->error = "made new directory : $this->base_cache_dir\n";
			}
		}
	}

	function load($rss) {

		$this->error = "no error\n";
		$fname = $this->base_cache_dir."/".$this->cache_file;

 		if ( ! file_exists ( $fname ) ) { 
			$this->error= "$fname :: no file!\n";
			return 0;
		}

		$modtime = filemtime( $fname ) ;
		$age = time() - $modtime;
		if ( $this->time_out < $age ) { 
			$this->error= "$fname too old - $this->time_out\n";
			return 0;
		} 

		$fp = @fopen($fname,'r');
		if ( ! $fp ) { 
			$this->error = "$fname :: file can't be opened!\n";
			return 0;
		}
		$data = fread ( $fp, filesize($fname) );
		$rss  = unserialize( $data );
		fclose($fp);
		return $rss;
	}

	function store($rss) {

		$this->error = "no error\n";

		$fname = $this->base_cache_dir."/".$this->cache_file;
		$fp = @fopen( $fname, 'w');

		if ( ! $fp ) { 
			$this->error = "$fname :: couldn't write!\n";
			return 0;
		} 

		$data = serialize($rss);
		fwrite($fp, $data);
		fclose($fp);

		return $fname;
	}

}

?>
