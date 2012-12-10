<?php
// $Id: table.php,v 1.3 2003/12/23 01:35:46 admin Exp $
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



/* class to print a multidimensional object array to an HTML table

*  Table0()

*  Table1()	will print a table of the dataobjectlist with the assoc array's keys as headers

*  Table2()	will print a table of the dataobjectlist with the headers array's items as headers.  its keys must match those

*			of dataobjectlist

			 */

			

import("org/wprb/Playlist.php");



class Table	{

	function Table($param1 = null, $param2 = null, $param3 = null) {

   		$arg_list = func_get_args();

   		$numargs = sizeof($arg_list);

   		$args = "";

   		for ($i = 0; $i < $numargs && $arg_list[$i] != null; $i++) {

     		if ($i != 0) $args .= ", ";

	    	$args .= "\$param" . ($i + 1);

   		}

   		eval("\$this->Table" . $i . "(" . $args . ");");

	}

	function Table0()	{}

	function Table1($data_list)	{

		$this->printHTMLTable($data_list);

	}

	function Table2( $data_list, $headers)	{

		$this->printHeaderedHTMLTable($datalist, $headers);

	}

		

	function printHTMLTable($rows)	{

		$rows->reset();

		$a_row = get_object_vars($rows->next);

		$headers = array_keys($a_row);

		print "<table>\n";

		print "\t<tr>\n";

		foreach ($headers as $header)

			print "\t\t<td bgcolor=\"#CCCCCC\">$header</td>\n";

		print "\t</tr>\n";

		printTableElements($rows, $headers);

		print "</table>\n";

	}

		

	function printHeaderedHTMLTable($rows, $headers)	{

		$rows->reset();

		print "<table>\n";

		print "\t<tr>\n";

		foreach ($headers as $header)

			print "\t\t<td bgcolor=\"#CCCCCC\">$header</td>\n";

		print "\t</tr>\n";

		printTableElements($rows, $headers);

		print "</table>\n";

	}

	

	function printTableElements($rows, $headers)	{

		$rows->reset();

		$keys = array_keys($headers);

		while ($rows->hasNext())	{

			$row = $rows->next();

			print "\t<tr>\n";

			foreach ($keys as $key)	{

				print "\t\t<td bgcolor=\"#EEEEEE\">$row->($key)</td>\n";

			}

			print "\t</tr>\n";

		}

	}

}

?>

