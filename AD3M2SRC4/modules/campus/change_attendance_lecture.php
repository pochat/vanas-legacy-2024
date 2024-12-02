<?php

	# La libreria de funciones
	require '../../lib/general.inc.php';

	# Recupera el usuario actual
  	ValidaSesion();

  	# Recibe Parametros Numericos
	$live_session = RecibeParametroNumerico('live_session');
	$fl_maestro = RecibeParametroNumerico('fl_maestro');
	$cl_estatus_asistencia = RecibeParametroNumerico('option');
    $fl_clase=$_POST['fl_clase'];

	$Query_fecha = "SELECT B.fe_clase FROM k_live_session A JOIN k_clase B ON A.fl_clase=B.fl_clase WHERE fl_live_session=$live_session ";
	$result = RecuperaValor($Query_fecha);
	$fe_asistencia = $result[0];

	$Query_usr = "SELECT fl_usuario FROM k_live_session_asistencia WHERE fl_live_session=$live_session AND fl_usuario=$fl_maestro";
	$result = RecuperaValor($Query_usr);
	$fl_usuario =$result['fl_usuario'];

	if (!empty($fl_usuario)) {
		$Query = "UPDATE k_live_session_asistencia SET cl_estatus_asistencia = '$cl_estatus_asistencia' WHERE fl_live_session = '$live_session' AND fl_usuario = '$fl_maestro'";
		EjecutaQuery($Query);
	} else {
		$Query = "INSERT INTO k_live_session_asistencia(fl_live_session, fl_usuario, cl_estatus_asistencia, fe_asistencia) VALUES ($live_session, $fl_maestro, $cl_estatus_asistencia, NULL)";
		EjecutaQuery($Query);
	}
    if($cl_estatus_asistencia==1){
        $Query="UPDATE k_clase SET mn_rate=0 WHERE fl_clase=$fl_clase ";
        EjecutaQuery($Query);
    }
    if($cl_estatus_asistencia==2){

        $Query  = "SELECT mn_hour_rate,mn_hour_rate_group_global,mn_hour_rate_global_class FROM c_maestro ";
        $Query .= "where  fl_maestro=$fl_maestro ";
        $row = RecuperaValor($Query);
        $mn_hour_rate=$row['mn_hour_rate'];

        $Query="UPDATE k_clase SET mn_rate=$mn_hour_rate WHERE fl_clase=$fl_clase ";
        EjecutaQuery($Query);
    }

	//echo $Query;
?>
