<?php
	//ribbon breadcrumbs config e.g. array("Display Name" => "URL");


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
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  $page_nav = CreateMenuSP($fl_usuario);
	
	//configuration variables
	$page_title = "";
	$page_css = array();
	$no_main_header = false; //set true for lock.php and login.php
	$page_body_prop = array(); //optional properties for <body>
?>