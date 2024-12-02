<?php
# Libreria de funciones
require '../../lib/general.inc.php';

$comments=RecibeParametroHTML('comments');
$fl_sesion=RecibeParametroNumerico('fl_sesion');

$Query='SELECT cl_sesion FROM c_sesion WHERE fl_sesion='.$fl_sesion.'';
$row=RecuperaValor($Query);
$cl_sesion = $row[0];


$Query  = "UPDATE k_ses_app_frm_1 ";
$Query .= "SET comments='$comments' ";
$Query .= "WHERE cl_sesion='$cl_sesion' ";
EjecutaQuery($Query);





?>