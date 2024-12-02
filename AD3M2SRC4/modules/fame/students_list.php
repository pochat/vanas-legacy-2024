<?php

  # Librerias
  require '../../lib/general.inc.php';

  # Variable initializtion to avoid errors
  $etq_quiz=NULL;

  # Recibe Parametros  
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += $advanced_search;
  $fl_instituto_params = $_POST['fl_instituto_params'];
  $fl_programa_params = $_POST['fl_programa_params'];

   function ObtenPromedioPrograma($p_programa, $p_alumno){
      
      # Buscamos folio del programa y alumno
      $row0 = RecuperaValor("SELECT fl_usu_pro FROM k_usuario_programa WHERE fl_usuario_sp=$p_alumno and fl_programa_sp=$p_programa");
      $fl_usu_pro = $row0[0];
      
      # Buscamos si el teacher lo califica, si tiene permisos.
      $row00 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$fl_usu_pro");
      $fg_quizes = $row00[0];
      $fg_grade_tea = $row00[1];
      
      $contador_califi_finales=0;      
      #Recuperamos todas las lecciones del programa
      $Query2="SELECT fl_leccion_sp,no_semana,ds_titulo,nb_quiz,no_valor_quiz  FROM 
               c_leccion_sp WHERE fl_programa_sp=$p_programa ";
      $rs2 = EjecutaQuery($Query2);
      $contador2=0;
      for($tot2=0;$row2=RecuperaRegistro($rs2);$tot2++) {
        $fl_leccion_sp=$row2[0];
        # Verifica si tiene quiz la leccion y ha realizado intentos
        if(ExisteEnTabla('k_quiz_pregunta', 'fl_leccion_sp', $fl_leccion_sp) AND ExisteEnTabla('k_quiz_calif_final', 'fl_leccion_sp', $fl_leccion_sp, 'fl_usuario', $fl_alumno, true)){      
          #Recuperamos los quizes por cada leccion del programa.
          echo $Query3 = "
          SELECT max(no_intento), no_calificacion,no_intento  
          FROM k_quiz_calif_final 
          WHERE fl_leccion_sp=$fl_leccion_sp 
          AND fl_usuario=$fl_alumno ORDER BY no_intento DESC ";
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
      $tot_pro= $tot_cal_quiz;
      
      EjecutaQuery("UPDATE k_details_usu_pro SET no_prom_quiz=$promedio_quizes WHERE fl_usu_pro=$fl_usu_pro");

      # SI el maestro lo califica obtiene datos
      if(!empty($fg_grade_tea)){
       
          #Realizamos 
          $row0 = RecuperaValor("SELECT SUM( no_calificacion), COUNT(*) FROM k_calificacion_teacher WHERE fl_programa_sp=$p_programa AND fl_alumno=$p_alumno");
          $sum_weeks = $row0[0] / $row0[1];
          $tot_tea = $sum_weeks ;
          $tot_pro = ($tot_cal_quiz + $tot_tea)/2;
          EjecutaQuery("UPDATE k_details_usu_pro SET no_prom_teacher=$tot_tea WHERE fl_usu_pro=$fl_usu_pro");
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

#LISTADO SOLO PARA ESTUDIATES QUE TIENE ASIGNADO UN CURSO.
  
  #muestra los institutos que ya se encuentran registrados
  $Query="
  SELECT P.fl_usu_pro,A.ds_ruta_avatar,U.ds_nombres,U.ds_apaterno,I.ds_instituto,
  R.nb_programa,U.fg_activo,U.fe_ultacc,P.ds_progreso,P.no_promedio_t,
  U.fe_sesion,S.ds_pais,P.no_promedio_t,
  U.fl_instituto, U.fl_usuario,R.fl_programa_sp,P.fl_usu_pro,I.fg_tiene_plan,
  fg_quizes, fg_grade_tea,
  CASE fg_grade_tea WHEN '1' THEN ROUND((no_prom_quiz+no_prom_teacher)/2,1) ELSE no_prom_quiz  END no_promedio , U.ds_login,U.ds_email
  ,U.ds_login 
  FROM c_alumno_sp A
  LEFT JOIN c_usuario U ON U.fl_usuario=A.fl_alumno_sp
  LEFT JOIN c_instituto I ON I.fl_instituto=U.fl_instituto
  LEFT JOIN k_usuario_programa P ON P.fl_usuario_sp=U.fl_usuario
  LEFT JOIN k_details_usu_pro DUP ON DUP.fl_usu_pro=P.fl_usu_pro
  LEFT JOIN c_programa_sp R ON R.fl_programa_sp=P.fl_programa_sp
  LEFT JOIN k_usu_direccion_sp d ON(d.fl_usuario_sp=U.fl_usuario)   
  LEFT JOIN c_pais S ON S.fl_pais=d.fl_pais
  WHERE 1=1 AND U.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." ";
  if(!empty($fl_instituto_params))
    $Query .= " AND U.fl_instituto=".$fl_instituto_params;
  if(!empty($fl_programa_params))
    $Query .= " AND R.fl_programa_sp=".$fl_programa_params;
  //$Query.=" AND U.fl_usuario=6897 ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $fl_alumno=$row['fl_usu_pro'];
      $ds_ruta_avatar=$row['ds_ruta_avatar'];
      if(!empty($ds_ruta_avatar))
        $ruta_avatar = SP_HOME_W.'/fame/site/uploads/'.$row['fl_instituto'].'/USER_'.$row['fl_usuario'].'/'.$ds_ruta_avatar;
      else
        $ruta_avatar = SP_IMAGES_W.'/avatar_default.jpg';
                           
      $ds_nombres=$row['ds_nombres']." ".$row['ds_apaterno'];
      $ds_instituto=$row['ds_instituto'];
      $nb_curso=$row['nb_programa'];
      $fg_activo=$row['fg_activo'];
      $fe_sesion=$row['fe_sesion'];
      $ds_pais=$row['ds_pais'];
      $no_promedio=$row['no_promedio_t'];
      $fl_programa=$row['fl_programa_sp'];
      $fl_usuario=$row['fl_usuario'];
      $fl_usu_pro=$row['fl_usu_pro'];
      $ds_login=$row['ds_login'];
      if(empty($fl_alumno)){
        $fl_alumno = $fl_usuario."a";
      }
      $fg_tiene_plan=$row['fg_tiene_plan'];
      $fl_instituto=$row['fl_instituto'];
      $fg_quizes = $row['fg_quizes'];
      $fg_grade_tea = $row['fg_grade_tea'];
      $no_prom_quiz = !empty($row['no_prom_quiz'])?$row['no_prom_quiz']:NULL;
      $no_prom_teacher = !empty($row['no_prom_teacher'])?$row['no_prom_teacher']:NULL;
      # NOT USE< VARIABLE OVERWRITES THE PREVIOUS VALUE *****21-02-2020*****
      //$no_promedio = $row['no_promedio'];
      if(empty($no_promedio))
        $no_promedio = 0;
      
      if((!empty($fg_quizes))&&(empty($fg_grade_tea))){

          $row1 = RecuperaValor("SELECT no_prom_quiz FROM k_details_usu_pro WHERE fl_usu_pro=" . $fl_usu_pro);
          $no_promedio=$row1['no_prom_quiz'];

      }
         

      $ds_progreso=$row['ds_progreso'];
      
      $ds_login=str_texto($row['ds_login']);
      $ds_email=str_texto($row['ds_email']);
      
      if($fl_instituto==4){
      
          $Querym="SELECT fl_envio_correo,fg_desbloquear_curso FROM k_envio_email_reg_selfp WHERE ds_email='$ds_email'  ";
          $ro=RecuperaValor($Querym);
          $fg_desbloquear_curso=!empty($ro[1])?$ro[1]:NULL;
          
          if($fg_desbloquear_curso==1)
          $id_student="B2C-".$ds_login;
		  else
		  $id_student=$ds_login;
      
      }else{
	  
	      $id_student=$ds_login;
	  }

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
        $fe_expiracion_plan=$row[0];

        if($fe_expiracion_plan < $fe_actual)
            $etq='Expired';
        else  
            $etq='Member';
      }
      if($fg_tiene_plan==0){            
        #Verificmos la fecha de expiracion de su plan actual
        $Query="SELECT fe_trial_expiracion FROM c_instituto WHERE fl_instituto=$fl_instituto ";
        $row=RecuperaValor($Query);
        $fe_expiracion_trial=$row[0];

        if($fe_expiracion_trial < $fe_actual)
          $etq='Expired';
        else
          $etq='Trial';
      } 
      #Calucla el pormedio incluido quizes y teacher grade      
      //$no_promedio = ObtenPromedioPrograma($fl_programa, $fl_usuario);

      # Obtenemos el GPA
      $Query = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion_sp WHERE no_min <= ROUND($no_promedio) AND no_max >= ROUND($no_promedio)";
      $prom_t = RecuperaValor($Query);
      $cl_calificacion = $prom_t[0];
      $fg_aprbado_grl = $prom_t[1];
      if(!empty($fg_aprbado_grl))
        $GPA = "success";		  
      else
        $GPA = "danger";         
      
      # Enviamos que tiempo hay desde su ultima conexion
      $fe_sesion = time_elapsed_string($fe_sesion);

	    if($fg_activo=='0'){         
        // $nb_curso="Unassigned";
        $fe_sesion="inactive";
        // $ds_progreso="0";         
      }

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
 
      # Buscamos si el teacher lo califica, si tiene permisos.
      $row00 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$fl_usu_pro");
      $fg_quizes = !empty($row00[0])?$row00[0]:NULL;
      $fg_grade_tea = !empty($row00[1])?$row00[1]:NULL;
      if($fg_quizes==1){
          $etq_quiz=ObtenEtiqueta(1916);
		  $transcript='<span class=\'label label-default\' style=\'background-color: #fff0;line-height:3 !important;\'><a   rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'Download Quiz Grade\'   href=\'../reports/transcript_fame_quiz_rpt.php?c='.$fl_usu_pro.'&u='.$fl_usuario.'&i='.$fl_instituto.'\' style=\'color:#000;font-size:17px;\'><i class=\'fa fa-file-pdf-o\' aria-hidden=\'true\'> </i> </a></span>';
      }else{
          $transcript="";
      }

	  if($fg_grade_tea==1){
          $etq_=ObtenEtiqueta(1917);
		  $transc_grade='<span class=\'label label-default\' style=\'background-color:#fff0;line-height:3 !important;\'><a   rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'Download Teacher Grade\'   href=\'../reports/transcript_fame_quiz_teacher_rpt.php?c='.$fl_usu_pro.'&u='.$fl_usuario.'&i='.$fl_instituto.'\' style=\'color:#000;font-size:17px;\'><i class=\'fa fa-file-pdf-o\' aria-hidden=\'true\'> </i> </a></span>';
		  
	  }else
		  $transc_grade="";
	   
      $icono_pul = '<i class=\'fa fa-plus\'></i>';
         
      # Por defaul estara quiz
      if(!empty($fg_quizes) && !empty($fg_grade_tea)){
        $assessment = $etq_quiz.' '.$icono_pul.' '.$etq_;
        
      }else{
        $assessment = $etq_quiz;
       }
      if($ds_progreso==100){
          $certificado='<span class=\'label label-default\' style=\'background-color: #fff0;line-height:2 !important;\'><a    rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'Download Transcript\'  href=\'javascript:certificado(1,'.$fl_usuario.','.$fl_programa.');\'  style=\'color:#000;font-size:17px;\'><i class=\'fa fa-file-pdf-o\' aria-hidden=\'true\'> </i></a></span>';
      }else
         $certificado=' ';    
      
      #Identificamos el maestro y en base a eso determinamos el instituto al que pertencece.
      $Query="SELECT C.ds_instituto FROM k_usuario_programa A 
                JOIN c_usuario B ON A.fl_maestro=B.fl_usuario
                JOIN c_instituto C ON C.fl_instituto=B.fl_instituto 
                WHERE fl_usu_pro=$fl_usu_pro ";
      $rop=RecuperaValor($Query);
      $ds_instituto=!empty($rop[0])?$rop[0]:'';

    echo '
    {
        "checkbox": " ",
        "avatar": "<div class=\'project-members\'><a href=\"javascript:void(0)\" rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombres.'\' ><img src=\"'.$ruta_avatar.'\" class=\"online\" alt=\"user\" style=\'width:25px;\'></a></div> ",
        "name": "<a href=\'javascript:Envia(\"students_frm.php\",\"'.$fl_alumno.'\");\'>'.$ds_nombres.'<br><small class=\'text-muted\'><i>ID: '.$id_student.'</i></small></a><br><small class=\'text-muted\'></small>",
        "name_school": "<td><a href=\'javascript:Envia(\"students_frm.php\",\"'.$fl_alumno.'\");\'>'.$ds_instituto.'<br><small class=\'text-muted\'><i>'.$ds_pais.'</i></small></a></td>",           
        "course": "<td><a href=\'javascript:Envia(\"students_frm.php\",\"'.$fl_alumno.'\");\'>'.str_texto($nb_curso).'</a></td>", 
        "age": "<td class=\'text-center\'><a href=\'javascript:Envia(\"students_frm.php\",\"'.$fl_alumno.'\");\' class=\'text-center\'><span class=\'label label-'.$color_label.'\'>'.$status.'</span> </a><br/><small class=\'text-muted\'><i>'.$etq.'</i></small></td>",
        "ide": "<a href=\'javascript:Envia(\"students_frm.php\",\"'.$fl_alumno.'\");\'><td><small class=\'text-muted\'><i>'.$fe_sesion.' </i></small></a></td>",
        "progress": "<td><div class=\'progress progress-xs\' data-progressbar-value=\''.$ds_progreso.'\'><div class=\'progress-bar\'></div></div><span class=\'hidden\'>'.$ds_progreso.'</span> '.$certificado.' </td>", 
        "assesment":"<span class=\'label label-success\'> '.$assessment.' </span><br>'.$transcript.'  '.$transc_grade.'",         
        "estatus": "<span class=\'label label-'.$GPA.'\'>'.$cl_calificacion.' ('.(round($no_promedio)).'%)</span>"
    }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]
}
