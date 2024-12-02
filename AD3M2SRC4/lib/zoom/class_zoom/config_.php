<?php

require '../autoload.php';
require "class-db.php";

/**
 * Toda esta carpeta class_zoom contiene lo necesario la crear el token y poder crear metings. incluyendo este archivo config_.php
 * 
 * 
 */ 


$CLIENT_ID=ObtenConfiguracion(10);
$CLIENT_SECRET=ObtenConfiguracion(12);
$REDIRECT_URI=ObtenConfiguracion(13);

define('CLIENT_ID', ''.$CLIENT_ID.'');
define('CLIENT_SECRET', ''.$CLIENT_SECRET.'');
define('REDIRECT_URI', ''.$REDIRECT_URI.'');
 
?>