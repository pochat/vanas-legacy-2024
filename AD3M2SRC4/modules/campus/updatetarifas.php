<?php
# Libreria de funciones
require '../../lib/general.inc.php';

# Verifica que exista una sesion valida en el cookie y la resetea
ValidaSesion( );

# Recibe parametro
$fl_clase = $_POST['fl_clase'];
$tabla = $_POST['tabla'];
$valor = $_POST['valor'];


    switch($tabla){
        

        case 'k_clase':

            $Query="UPDATE k_clase SET mn_rate=$valor WHERE fl_clase=$fl_clase ";
            EjecutaQuery($Query);

            break;
        case 'k_clase_cg':
            $Query="UPDATE k_clase_cg SET mn_rate=$valor WHERE fl_clase_cg=$fl_clase ";
            EjecutaQuery($Query);
            break;

        case 'k_clase_grupo':
            $Query="UPDATE k_clase_grupo SET mn_rate=$valor WHERE fl_clase_grupo=$fl_clase ";
            EjecutaQuery($Query);
            break;

    }



?>