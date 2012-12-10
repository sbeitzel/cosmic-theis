<?php
/*****************************************************************************
 * Theis Playlist Manager -- An interactive web application for creating,    *
 * editing, and publishing radio playlists.                                  *
 *                                                                           *
 * Copyright (C) 2003  Aaron Forrest                                         *
 *                                                                           *
 * This program is free software; you can redistribute it and/or             *
 * modify it under the terms of the GNU General Public License               *
 * as published by the Free Software Foundation; either version 2            *
 * of the License, or (at your option) any later version.                    *
 *                                                                           *
 * This program is distributed in the hope that it will be useful,           *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 * GNU General Public License for more details.                              *
 *                                                                           *
 * You should have received a copy of the GNU General Public License         *
 * along with this program; if not, write to the Free Software               *
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.*
 *****************************************************************************/

/********************************************************************
 *  This is the configuration file that all PHP files should include.
 *  Auto-generated by configure.php.  Edit at your own risk
 ********************************************************************/

/* URL of root, eliminating relative URLs */
define("__HOST__","http://wprb.ultramoderne.net/");
define("__ROOT__",__HOST__."test/");

/* these allow for relative file includes */
define("__BASE__",dirname(__FILE__)."/");
define("__CLASSBASE__",__BASE__."classes/");

/* database configuration stuff */
define("__DBUSER__","db username");
define("__DBPASSWORD__","db password");
define("__DBNAME__","db name");
define("__DBHOST__","localhost");

/* include "importer" for including classes */
include(__CLASSBASE__."importer.php");

/* Host identification stuff */
define("__STATION__", "WPRB");
define("__REAL__", "");
define("__WMP56__", "");
define("__WMP100__", "");
define("__HOMEPAGE__", "");
define("__ETAIL_LINK__", "");
define("__ETAIL_ARTIST__", "");
define("__ETAIL_ALBUM__", "");
define("__ETAIL_LABEL__", "");
			?>