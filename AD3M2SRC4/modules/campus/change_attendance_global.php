<?php

	# La libreria de funciones
	require '../../lib/general.inc.php';

	# Recupera el usuario actual
  	ValidaSesion();

  	# Recibe Parametros Numericos
	$live_session_gg = RecibeParametroNumerico('live_session_gg');
	$fl_maestro_gg = RecibeParametroNumerico('fl_maestro_gg');
	$cl_estatus_asistencia_gg = RecibeParametroNumerico('option_gg');
    $fl_clase=$_POST['fl_clase'];

	$Query_fecha_gg = "SELECT B.fe_clase FROM k_live_session_grupal A JOIN k_clase_grupo B ON A.fl_clase_grupo=B.fl_clase_grupo WHERE fl_live_session_grupal=$live_session_gg ";
	$result = RecuperaValor($Query_fecha_gg);
	$fe_asistencia_gg = $result[0];

	$Query_usr = "SELECT fl_usuario FROM k_live_session_asistencia_gg WHERE fl_live_session_gg=$live_session_gg AND fl_usuario=$fl_maestro_gg";
	$result = RecuperaValor($Query_usr);
	$fl_usuario_gg =$result['fl_usuario'];

	if (!empty($fl_usuario_gg)) {
		$Query = "UPDATE k_live_session_asistencia_gg SET cl_estatus_asistencia_gg = '$cl_estatus_asistencia_gg' WHERE fl_live_session_gg = '$live_session_gg' AND fl_usuario = '$fl_maestro_gg'";
		EjecutaQuery($Query);
	} else {
		$Query = "INSERT INTO k_live_session_asistencia_gg(fl_live_session_gg, fl_usuario, cl_estatus_asistencia_gg, fe_asistencia_gg) VALUES ($live_session_gg, $fl_maestro_gg, $cl_estatus_asistencia_gg, NULL)";
		EjecutaQuery($Query);
	}

    if($cl_estatus_asistencia_gg==1){
        
        $Query="UPDATE k_clase_grupo SET mn_rate=0 WHERE fl_clase_grupo=$fl_clase ";
        EjecutaQuery($Query);

    }
    if($cl_estatus_asistencia_gg==2){

        $Query  = "SELECT mn_hour_rate,mn_hour_rate_group_global,mn_hour_rate_global_class FROM c_maestro ";
        $Query .= "where  fl_maestro=$fl_maestro_gg ";
        $row = RecuperaValor($Query);
        $mn_hour_rate=$row['mn_hour_rate_group_global'];
        
        $Query="UPDATE k_clase_grupo SET mn_rate=$mn_hour_rate WHERE fl_clase_grupo=$fl_clase ";
        EjecutaQuery($Query);

    }


	//echo $Query;
?>
