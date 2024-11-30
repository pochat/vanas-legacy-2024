<?php
  defined( '_JEXEC' ) or die( 'Restricted access' );

  if(version_compare(JVERSION,'1.6.0','ge')) {
    include("universal_ajax_live_search16.php");
	} else {
    include("universal_ajax_live_search15.php");
	}
?>