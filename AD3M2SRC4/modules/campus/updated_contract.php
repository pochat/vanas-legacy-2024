<?php
# Libreria de funciones
require '../../lib/general.inc.php';


# Verifica que exista una sesion valida en el cookie y la resetea
ValidaSesion();

$ds_header = RecibeParametroHTML('ds_header');
$ds_contrato = RecibeParametroHTML('ds_contrato');
$ds_footer = RecibeParametroHTML('ds_footer');
$clave = RecibeParametroHTML('cl_sesion');


EjecutaQuery("UPDATE k_app_contrato set ds_header='$ds_header', ds_contrato='$ds_contrato',ds_footer='$ds_footer' where cl_sesion='$clave' ");


?>