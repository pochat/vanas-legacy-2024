<?php

	# La libreria de funciones
	require '../../lib/general.inc.php';

	# Recupera el usuario actual
  	ValidaSesion();

  	# Recibe Parametros Numericos
	$live_session_cg = RecibeParametroNumerico('live_session_cg');
	$fl_maestro_cg = RecibeParametroNumerico('fl_maestro_cg');
	$cl_estatus_asistencia_cg = RecibeParametroNumerico('option_cg');
    $fl_clase=$_POST['fl_clase'];

	$Query_fecha_cg = "SELECT B.fe_clase FROM k_live_sesion_cg A JOIN k_clase_cg B ON A.fl_clase_cg=B.fl_clase_cg WHERE fl_live_session_cg=$live_session_cg ";
	$result = RecuperaValor($Query_fecha_cg);
	$fe_asistencia = $result[0];

	$Query_usr = "SELECT fl_usuario FROM k_live_session_asistencia_cg WHERE fl_live_session_cg=$live_session_cg AND fl_usuario=$fl_maestro_cg";
	$result = RecuperaValor($Query_usr);
	$fl_usuario_cg =$result['fl_usuario'];

	if (!empty($fl_usuario_cg)) {
		$Query = "UPDATE k_live_session_asistencia_cg SET cl_estatus_asistencia_cg = '$cl_estatus_asistencia_cg' WHERE fl_live_session_cg = '$live_session_cg' AND fl_usuario = '$fl_maestro_cg'";
		EjecutaQuery($Query);
	} else {
		$Query = "INSERT INTO k_live_session_asistencia_cg(fl_live_session_cg, fl_usuario, cl_estatus_asistencia_cg) VALUES ($live_session_cg, $fl_maestro_cg, $cl_estatus_asistencia_cg)";
		EjecutaQuery($Query);
	}

    if($cl_estatus_asistencia_cg==1){
        
        $Query="UPDATE k_clase_cg SET mn_rate=0 WHERE fl_clase_cg=$fl_clase ";
        EjecutaQuery($Query);

    }
    if($cl_estatus_asistencia_cg==2){

        $Query  = "SELECT mn_hour_rate,mn_hour_rate_group_global,mn_hour_rate_global_class FROM c_maestro ";
        $Query .= "where  fl_maestro=$fl_maestro_cg ";
        $row = RecuperaValor($Query);
        $mn_hour_rate=$row['mn_hour_rate_global_class'];
        
        $Query="UPDATE k_clase_cg SET mn_rate=$mn_hour_rate WHERE fl_clase_cg=$fl_clase ";
        EjecutaQuery($Query);

    }



?>
