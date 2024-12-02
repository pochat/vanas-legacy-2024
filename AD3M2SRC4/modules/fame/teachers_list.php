<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  # Recibe Parametros  
  parse_str((isset($_POST['extra_filters']['advanced_search'])?$_POST['extra_filters']['advanced_search']:NULL), $advanced_search);
  $_POST += $advanced_search;
  $fl_instituto_params = isset($_POST['fl_instituto_params'])?$_POST['fl_instituto_params']:NULL;
  $fl_programa_params = isset($_POST['fl_programa_params'])?$_POST['fl_programa_params']:NULL;
  
  # Obtener le promedio general
  function ObtenPromedioPrograma($p_programa, $p_alumno){
    
    # Buscamos folio del programa y alumno
    $row0 = RecuperaValor("SELECT fl_usu_pro FROM k_usuario_programa WHERE fl_usuario_sp=$p_alumno and fl_programa_sp=$p_programa");
    $fl_usu_pro = $row0[0];
      
    # Buscamos si el teacher lo califica, si tiene permisos.
    $row00 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$fl_usu_pro");
    $fg_quizes = $row00[0];
    $fg_grade_tea = $row00[1];
      
      
    $contador_califi_finales=0;
    #Recupermaos datos claves  del programa.
    $Query="SELECT  P.fl_programa_sp,P.fl_usuario_sp,C.nb_programa
          FROM k_usuario_programa P 
          LEFT JOIN c_programa_sp C ON C.fl_programa_sp=P.fl_programa_sp  
          WHERE P.fl_programa_sp=$p_programa ";
    $rs = EjecutaQuery($Query);
    for($tot=0;$row=RecuperaRegistro($rs);$tot++){#datos claves del programa.
      $fl_programa_sp=$row[0];
      #Recuperamos todas las lecciones del programa
      $Query2="SELECT fl_leccion_sp,no_semana,ds_titulo,nb_quiz,no_valor_quiz  FROM 
              c_leccion_sp WHERE fl_programa_sp=$fl_programa_sp ";
      $rs2 = EjecutaQuery($Query2);
      $contador2=0;
      for($tot2=0;$row2=RecuperaRegistro($rs2);$tot2++) {
        $fl_leccion_sp=$row2[0];

        #Recupermaos todas las calificaciones del quiz, perteneceinetes a ea leccion y al alumno
        $Query="SELECT ";

        #Recuperamos los quizes por cada leccion del programa.
        $Query3="SELECT no_calificacion,no_intento  
                  FROM k_quiz_calif_final 
                  WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario=$p_alumno ORDER BY no_intento DESC ";
        $row3 = RecuperaValor($Query3);
        $no_intento=$row3['no_intento'];
        $no_total_calif=$row3['no_calificacion'];
        
        if(!empty($no_total_calif)){      
              $suma_quizes_calific += $no_total_calif;      
              $contador_califi_finales ++;  
        }
      }
    }
      
    #Calculo de los promedios:     
    $promedio_quizes=$suma_quizes_calific/$contador_califi_finales;
    $tot_cal_quiz=$promedio_quizes; 
      
    # SI el maestro lo califica obtiene datos
    if(!empty($fg_grade_tea)){
     
        #Realizamos 
        $row0 = RecuperaValor("SELECT SUM( no_calificacion), COUNT(*) FROM k_calificacion_teacher WHERE fl_programa_sp=$p_programa AND fl_alumno=$p_alumno ");
        $sum_weeks = $row0[0] / $row0[1];
        $tot_tea = $sum_weeks ;
        $tot_pro = ($tot_cal_quiz + $tot_tea)/2;
    }  
      
    return number_format($tot_pro);
  }
  
  
  # Funcion para obtener tiempo desde su ultima sesion
  function time_elapsed_string($datetime, $full = false){
    $now = new DateTime;
    $then = new DateTime( $datetime );
    $diff = (array) $now->diff( $then );

    $diff['w']  = floor( $diff['d'] / 7 );
    $diff['d'] -= $diff['w'] * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    foreach( $string as $k => & $v )
    {
        if ( $diff[$k] )
        {
            $v = $diff[$k] . ' ' . $v .( $diff[$k] > 1 ? 's' : '' );
        }
        else
        {
            unset( $string[$k] );
        }
    }

    if ( ! $full ) $string = array_slice( $string, 0, 1 );
    return $string ? implode( ', ', $string ) . ' ago' : 'just now';
  }
  
  # Obtiene los teachers
  $Query  = "SELECT m.fl_maestro_sp, us.ds_nombres, us.ds_apaterno, i.ds_instituto, us.fg_activo, us.fe_sesion, p.ds_pais, i.fl_pais, m.ds_ruta_avatar, i.fl_instituto, ";
  $Query .= "pro.nb_programa, pr.ds_progreso, pr.fl_programa_sp, pr.fl_usu_pro,i.fg_tiene_plan,i.fl_instituto,us.ds_login ";
  $Query .= "FROM c_maestro_sp m ";
  $Query .= "LEFT JOIN c_usuario us ON(us.fl_usuario=m.fl_maestro_sp) ";
  $Query .= "LEFT JOIN k_usuario_programa pr ON(pr.fl_usuario_sp=m.fl_maestro_sp) ";
  $Query .= "LEFT JOIN c_programa_sp pro ON(pro.fl_programa_sp=pr.fl_programa_sp) ";
  $Query .= "LEFT JOIN c_instituto i ON(i.fl_instituto=us.fl_instituto) ";
  $Query .= "LEFT JOIN k_usu_direccion_sp d ON(d.fl_usuario_sp=m.fl_maestro_sp) ";
  $Query .= "LEFT JOIN c_pais p ON(p.fl_pais = d.fl_pais ) ";
  $Query .= "WHERE 1=1 AND us.fl_perfil_sp=".PFL_MAESTRO_SELF." ";
   if(!empty($fl_instituto_params))
    $Query .= " AND i.fl_instituto=".$fl_instituto_params;
  if(!empty($fl_programa_params))
    $Query .= " AND pr.fl_programa_sp=".$fl_programa_params;  
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $fl_maestro_sp = $row[0];
      $ds_nombres = str_texto($row[1]);
      $ds_apaterno = str_texto($row[2]);          
      $ds_instituto = str_texto($row[3]);
      $fg_activo = $row[4];
      $fe_sesion = time_elapsed_string($row[5]);
      $ds_pais = $row[6];
      $ds_login=$row['ds_login'];
      // avatar
      $ds_avatar= $row[8];
      if(!empty($row[8]))
        $ruta_avatar = SP_HOME_W.'/fame/site/uploads/'.$row[9].'/USER_'.$row[0].'/'.$row[8];
      else
        $ruta_avatar = SP_IMAGES_W.'/avatar_default.jpg';
      $nb_programa = $row[10];
      if(empty($nb_programa))
        $nb_programa = "Unassigned";
      $ds_progreso = $row[11];
      $fl_programa_sp = $row[12];
      // if(empty($ds_progreso)){
        // $ds_promedio = ObtenPromedioPrograma($fl_programa_sp, $fl_maestro_sp);
      // }
      $fl_usu_pro = $row[13];
	  $fg_tiene_plan=$row['fg_tiene_plan'];
	  $fl_instituto=$row['fl_instituto'];
	  
	  
	 
	  #Obtenemos fecha actual :
	$Query = "Select CURDATE() ";
	$row = RecuperaValor($Query);
	$fe_actual = str_texto($row[0]);
	$fe_actual=strtotime('+0 day',strtotime($fe_actual));
	$fe_actual= date('Y-m-d',$fe_actual);
	  
	  
	  #Identificamos si esta en Trial/Con pLAN
	  if($fg_tiene_plan==1){
	 
		#Verificmos la fecha de expiracion de su plan actual
		$Query="SELECT fe_periodo_final FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
		$row=RecuperaValor($Query);
	    $fe_expiracion_plan=!empty($row[0])?$row[0]:NULL;
	 
	    if($fe_expiracion_plan < $fe_actual)
	      $etq='Expired';
		else  
	      $etq='Member';
	 
	 
	    
      	  
	  }
	  if($fg_tiene_plan==0){
	  
  		#Verificmos la fecha de expiracion de su plan actual
  		$Query="SELECT fe_trial_expiracion, ds_codigo_pais FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  		$row=RecuperaValor($Query);
  	    
      $fe_expiracion_trial=!empty($row[0])?$row[0]:NULL;
  	  $ds_codigo_pais=!empty($row[1])?$row[1]:0;
  	  
  		if($fe_expiracion_trial < $fe_actual)
  			$etq='Expired';
  			else  
  			  $etq='Trial';


	    
	  }
	  
	   $ds_codigo_pais=0;
	  
      # Si el maestro no esta cursando enviamos el fl_usuario
      if(empty($fl_usu_pro))
        $fl_usu_pro = $fl_maestro_sp;
      
      # Obtenemos el pais del instituto
      $row1 = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais=".$ds_codigo_pais);
      $ds_pais_inst = !empty($row1[0])?$row1[0]:NULL;
      // Por defaul es el pais del intituto
      if(empty($ds_pais)){
        $ds_pais = $ds_pais_inst;
      }
      
      # activo inactivo
      switch($fg_activo) {
        case "0": 
          $color_label = "danger";
          $status="Inactive"; 
          break;
        case "1": 
          $color_label="success";
          $status="Active";
          break;
      }
      
      # Obtenemos le numero de alumnos que tiene
      $row1 = RecuperaValor("SELECT COUNT(*) FROM ( 
                                SELECT DISTINCT fl_usuario_sp FROM k_usuario_programa A 
                                JOIN c_usuario U ON U.fl_usuario=A.fl_usuario_sp 
                                WHERE A.fl_maestro=$fl_maestro_sp ) C ");
      $no_students = $row1[0];
      
    echo '
    {      
      "avatar": "<div class=\'project-members\'><a href=\"javascript:void(0)\" rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombres.'\' ><img src=\"'.$ruta_avatar.'\" class=\"online\" alt=\"user\" style=\'width:25px;\'></a></div> ",
      "name": "<a href=\'javascript:EnviaFame(\"teachers_frm.php\",'.$fl_usu_pro.', '.$fl_maestro_sp.');\'>'.$ds_nombres.' '.$ds_apaterno.'<br><small class=\'text-muted\'><i>'.$ds_pais.'</i></small></a><br><small class=\'text-muted\'>ID: '.$ds_login.'</small>",      
      "name_school": "<td><a href=\'javascript:EnviaFame(\"teachers_frm.php\",'.$fl_usu_pro.', '.$fl_maestro_sp.');\'>'.$ds_instituto.'<br><small class=\'text-muted\'><i>'.$ds_pais_inst.'</i></small></a></td>",                   
      "course": "<td><a href=\'javascript:EnviaFame(\"teachers_frm.php\",'.$fl_usu_pro.', '.$fl_maestro_sp.');\'>'.str_texto($nb_programa).'</a></td>", 
      "active": "<td class=\'text-center\'><a href=\'javascript:EnviaFame(\"teachers_frm.php\",'.$fl_usu_pro.', '.$fl_maestro_sp.');\' class=\'text-center\'><span class=\'label label-'.$color_label.'\'>'.$status.'</span></a><br/><small class=\'text-muted\'><i>'.$etq.'</i></small>  </td>",
      "sesion": "<a href=\'javascript:EnviaFame(\"teachers_frm.php\",'.$fl_usu_pro.', '.$fl_maestro_sp.');\'><td><small class=\'text-muted\'><i>'.$fe_sesion.' </i></small></a></td>",
      "progress": "<td><div class=\'progress progress-xs\' data-progressbar-value=\''.$ds_progreso.'\'><div class=\'progress-bar\'></div></div><span class=\'hidden\'>'.$ds_progreso.'</span></td>",
      "no_students": "<div>'.$no_students.'</div>"
    }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
