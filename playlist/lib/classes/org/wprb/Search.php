<?php
/*****************************************************************************
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

/* class to search the database *
 * child of DataObjectList      */

import("org/erat/data/DataObjectList.php");
import("org/wprb/WPRBDB.php");

class Search extends DataObjectList	{
	
	var $db;
	var $query;
	
	function Search($param1 = null, $param2 = null, $param3 = null) {
   		$arg_list = func_get_args();
   		$numargs = sizeof($arg_list);
   		$args = "";
   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {
     		if ($i != 0) $args .= ", ";
	    	$args .= "\$param" . ($i + 1);
   		}
   		eval("\$this->Search" . $i . "(" . $args . ");");
	}

	function Search0() {}
	function Search1( $search_params ) {
		$this->db = new WPRBDB();
		$this->buildQuery($search_params);
	}

	function Search2() {}

	function Search3() {}
	
	/******************
	 * PUBLIC METHODS *
	 ******************/
	 
	 
	function searchDB()	{
		$this->DataObjectList("searchDatabase", $this->db);
		$this->load(null);
		return true;
	}
	
	function printHeaderedHTMLTable($headers, $colors='')	{
		if (! is_array($colors))	{
			$colors = array("tablecolor" => "EEEEEE", "tabletext" => "000000", "tablehead" => "CCCCCC");
		}
		print "<table cellpadding='3'>\n";
		print "\t<tr>\n";
		foreach ($headers as $header)
			print "\t\t<th bgcolor=\"#$colors[tablehead]\"><font color='#$colors[tabletext]'>$header</font></th>\n";
		print "\t</tr>\n";
		$this->printTableElements($headers, $colors);
		print "</font></table>\n";
	}
	
	function printTableElements($headers, $colors)	{
		$this->reset();
		$keys = array_keys($headers);
		while ($this->hasNext())	{
			$row = $this->next();
			print "\t<tr>\n";
			foreach ($keys as $key)	{
				$func = $row->$key;
				if ($key=='comp' || $key=='request')	
{
					($key == 'comp') ? (($func)? $func="C": $func='') : (($func)? $func="R": $func='');
				}
				print "\t\t<td bgcolor=\"#$colors[tablecolor]\"><font color='#$colors[tabletext]'>".$func."</td>\n";
			}
			print "\t</tr>\n";
		}
	}
	
	/*******************
	 * PRIVATE METHODS *
	 *******************/
	 
	 // builds a custom query depending on complex html form choices
	 // takes an associative array containing the named 
	 // values of those choices
	 function buildQuery($arr)	{
		 if ($arr[searchfield] == "all")
			$arr[searchfield] = 
				'artist, song, album, label, ensemble, conductor, performer';  // searches all relevant fields
			
		// ******* build queries depending on whether or not fuzzy search option is checked *******
		$searchstrict = "MATCH ($arr[searchfield]) AGAINST ('$arr[searchstring]') ";
		if ($arr[fuzzy] == 'indeed')	{
			if ($arr[searchfield] == 
				'artist, song, album, label, ensemble, conductor, performer')	{
				$searchstrict = "(artist LIKE '%$arr[searchstring]%' OR 
									song LIKE '%$arr[searchstring]%' OR 
									album LIKE '%$arr[searchstring]%' OR 
									label LIKE '%$arr[searchstring]%' OR 
									ensemble LIKE '%$arr[searchstring]%' OR
									conductor LIKE '%$arr[searchstring]%' OR
									performer LIKE '%$arr[searchstring]%')";
			}
			else
				$searchstrict = "$arr[searchfield] LIKE '%$arr[searchstring]%' ";
		}
			
		// ******* BUILD THE QUERY *******
		$query = "SELECT artist, song, album, label, emph, comp, request, djname, userID, shows.ID, starttime ";
		$query .= "FROM shows, playlist WHERE $searchstrict ";
		$query .= "AND playlist.showID = shows.ID AND artist!='*****' ";
		$query .= "AND shows.starttime >= $arr[start] AND shows.starttime<=$arr[end] ";
		if ($arr[users][0] != '')	{
			$query .= "AND (userID='";
			$query .= implode("' OR userID='", $arr[users]);
			$query .= "') ";
		}
		if ($arr[genres][0] != '')	{
			$query .= "AND (genre='";
			$query .= implode("' OR genre='", $arr[genres]);
			$query .= "') ";
		}
		if ($arr[emphs][0] != '')	{
			$query .= "AND (emph='";
			$query .= implode("' OR emph='", $arr[emphs]);
			$query .= "') ";
		}
		if ($arr[comp] == 'checked')
			$query .= "AND comp=1 ";
		if ($arr[req] == 'checked')
			$query .= "AND request=1 ";
		// $query .= "IN BOOLEAN MODE";  // uncomment line when mysql 4.0.1 becomes avail.
		$query .= "ORDER BY $arr[orderby] $arr[dirx] LIMIT $arr[page], 100";
		$this->query = $query; // store the query in memory
		$this->db->queries["searchDatabase"] = $query;
	 }
	 
}


?>
