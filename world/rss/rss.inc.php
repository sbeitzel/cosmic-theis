<?php
// $Id: rss.inc.php,v 1.4 2004/02/27 20:00:59 phil Exp $
//RSS include headers

define ("__RSSCLEANFUNC__", "htmlspecialchars");
define ("__OBJECTCACHE_DIR__", "./cache");
define ("__OBJECTCACHE_TIMEOUT__", (240) );

$RSShtmlcleaner = __RSSCLEANFUNC__;
$RSStzabbr 	= "GMT";
$RSStzoffset  	= 0;

?>
