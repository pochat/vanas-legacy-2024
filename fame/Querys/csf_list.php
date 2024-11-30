<?php   
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
 
  #Recuperamos el instituto rector.
  $Query="SELECT fl_instituto_rector FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $roe=RecuperaValor($Query);
  $fl_instituto_rector=$roe[0];

 
  $Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
			,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
			TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
			TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds,id,deleted_count,upload_file_name_log 
			FROM stage_uploads WHERE fl_instituto=$fl_instituto /*or fl_instituto=$fl_instituto_rector)*/  ORDER BY id DESC ";
  
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
    $id=$row['id'];
	$user_id=$row['user_id'];
	$upload_file_path=$row['upload_file_path'];
	$upload_file_name=$row['upload_file_name'];
	$upload_type=$row['upload_type'];
	$upload_date=$row['upload_date'];
	$status_cd=$row['status_cd'];
	$start_time=GeneraFormatoFecha($row['start_time']);
	$start_time_=$row['start_time'];
	$end_time=$row['end_time'];
	$proc_status=$row['proc_status'];
	$upload_time_hrs=$row['hrs'];
	$upload_time_minutes=$row['minutes'];
	$upload_time_seconds=$row['seconds'];
	$filename=$row['upload_file_name'];
	$upload_file_name_log=$row['upload_file_name_log'];
	$proc_status=$row['proc_status'];
	 	
		
		
    $path_filename=$upload_file_path."/".$upload_file_name_log;

	$fecha1 = new DateTime($start_time_);//fecha inicial
    $fecha2 = new DateTime($end_time);//fecha de cierre
    $intervalo = $fecha1->diff($fecha2);
	$runtime=$intervalo->format('%Hh %im %ss');
	 
	if($proc_status==1){
		$finish='<i class=\'fa fa-times-circle-o\' style=\'color:#B63C22;\'></i> <span style=\'color:#B63C22;\'>'.ObtenEtiqueta(22).'</span>';
	           
	}else{
		 $finish='<i class=\'fa fa-check-circle\' style=\'color:#226108;\'></i><span style=\'color:#226108;\'> '.ObtenEtiqueta(2532).'</span>'; 
	}


    #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
	$date = date_create($start_time_);
	$start_time_=date_format($date,'F j, Y, g:i:s a');

	$date = date_create($end_time);
	$end_time=date_format($date,'F j, Y, g:i:s a');

   
 
    /** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
    echo '
    {
      "checkbox":"<span class=\'hidden\'>'.$row['start_time'].'</span>",
      "start_time": "<div class=\'project-members text-left\' width=\'40%\' ><a href=\'javascript:MuestraResultados('.$id.');\'> '.$start_time.' </a> </div> ",
      "start_time2": " <div width=\'20%\' ><a href=\'javascript:MuestraResultados('.$id.');\'> '.$start_time_.' </a></div>  ",    
      "end_time": "<div class=\'text-center\'><a href=\'javascript:MuestraResultados('.$id.');\'> '.$end_time.'</a></div>",
      "runtime":"<div class=\'col col-sm-12 col-xs-12 col-md-12\'><a href=\'javascript:MuestraResultados('.$id.');\'> '.$runtime.' </a></div>",
	  "espacio":"<div class=\'col col-sm-12 col-xs-12 col-md-12\'><a href=\''.$path_filename.'\'>  <i class=\'fa fa-file-excel-o\' aria-hidden=\'true\'></i></a><br><small class=\'text-muted\'>'.strtolower($upload_type).'</small> </div>",
      "status":"<a href=\'javascript:MuestraResultados('.$id.');\'> '.$finish.' </a>  ",
	  "extradata":""
      
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>
  ]
}








