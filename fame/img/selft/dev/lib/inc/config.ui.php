<?php
	//ribbon breadcrumbs config e.g. array("Display Name" => "URL");
	/*$breadcrumbs = array(
		"Home" => APP_URL
	);*/

	/*navigation array config
	ex:
	"dashboard" => array(
		"title" => "Display Title",
		"url" => "http://yoururl.com",
		"icon" => "fa-home"
		"label_htm" => "<span>Add your custom label/badge html here</span>",
		"sub" => array() //contains array of sub items with the same format as the parent
	)
	*/

  $page_nav = CreateMenu();
	
	//configuration variables
	$page_title = "";
	$page_css = array();
	$no_main_header = false; //set true for lock.php and login.php
	$page_body_prop = array(); //optional properties for <body>
?>