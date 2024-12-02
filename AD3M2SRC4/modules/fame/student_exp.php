<?php

	# Funcion para quitar caracteres especiales y saltos de linea
  function getStrParaCSV($str) {
	$str_aux = $str;
	$str_aux = str_replace(",", " ", $str_aux);
	$str_aux = str_replace("\n", " ", $str_aux);
	$str_aux = str_replace("\r", " ", $str_aux);
  
	return $str_aux;  
  }
?>
<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  require_once '../../lib/PHPExcel-1.8/PHPExcel.php';
  
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, PERMISO_EJECUCION)) {    
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  parse_str($_POST['advanced_search'], $advanced_search);
  $_POST += $advanced_search;
  
  # Recibe parametros
  $fl_instituto_params = RecibeParametroNumerico('fl_instituto');
  $fl_programa_params = RecibeParametroNumerico('fl_programa');
  
  
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  
  
  
  
  // Crea un nuevo objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Establecer propiedades
$objPHPExcel->getProperties()
->setCreator("FAME")
->setLastModifiedBy("FAME")
->setTitle("FAME Students");





   $Query="
          SELECT P.fl_usu_pro,U.ds_nombres,U.ds_apaterno,U.ds_email,I.ds_instituto,
          R.nb_programa,U.fg_activo,U.fe_ultacc,P.ds_progreso,P.no_promedio_t,
          U.fe_sesion,S.ds_pais,P.no_promedio_t,
          U.fl_instituto, U.fl_usuario,R.fl_programa_sp,P.fl_usu_pro,I.fg_tiene_plan,
          fg_quizes, fg_grade_tea,
          CASE fg_grade_tea WHEN '1' THEN ROUND((no_prom_quiz+no_prom_teacher)/2,1) ELSE no_prom_quiz  END no_promedio
          ,AL.nb_grupo,G.nb_grado,P.fl_maestro 
          FROM c_alumno_sp A
          LEFT JOIN c_usuario U ON U.fl_usuario=A.fl_alumno_sp
          LEFT JOIN c_instituto I ON I.fl_instituto=U.fl_instituto
          LEFT JOIN k_usuario_programa P ON P.fl_usuario_sp=U.fl_usuario
          LEFT JOIN k_details_usu_pro DUP ON DUP.fl_usu_pro=P.fl_usu_pro
          LEFT JOIN c_programa_sp R ON R.fl_programa_sp=P.fl_programa_sp 
          LEFT JOIN c_pais S ON S.fl_pais=I.fl_pais
          JOIN c_alumno_sp AL ON AL.fl_alumno_sp= U.fl_usuario 
          JOIN k_grado_fame G ON G.fl_grado=AL.fl_grado
          WHERE 1=1 AND U.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." ";
    if(!empty($fl_instituto_params))
        $Query .= " AND U.fl_instituto=".$fl_instituto_params;
    if(!empty($fl_programa_params))
        $Query .= " AND R.fl_programa_sp=".$fl_programa_params;
    # Exporta los datos
    $rs = EjecutaQuery($Query);
    $tot_campos = CuentaCampos($rs);
	
	// Agregar Informacion
    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A1', "First Name")
		->setCellValue('B1', "Last Name")
		->setCellValue('C1', ObtenEtiqueta(1564))
		->setCellValue('D1', 'Email')
		->setCellValue('E1', ObtenEtiqueta(1565))
		->setCellValue('F1', ObtenEtiqueta(1562))
		->setCellValue('G1', 'Last Login')
		->setCellValue('H1', 'Progrress')
		->setCellValue('I1', ObtenEtiqueta(1908))
		->setCellValue('J1', 'GPA')
        ->setCellValue('K1','Group')
        ->setCellValue('L1','School Level')
        ->setCellValue('M1','Teacher');
		$num=1;
	for($i=1;$row=RecuperaRegistro($rs);$i++) {
    
		$num++;
	  
	
	  $fl_alumno=$row['fl_usu_pro'];            
      $ds_nombres=str_texto($row['ds_nombres']);
	  $ds_apaterno=str_texto($row['ds_apaterno']);
	  $ds_email=$row['ds_email'];
      $ds_instituto=str_texto($row['ds_instituto']);
      $nb_curso=$row['nb_programa'];
      $fg_activo=$row['fg_activo'];
      $fe_sesion=$row['fe_sesion'];
      $ds_pais=$row['ds_pais'];
      $no_promedio=$row['no_promedio_t'];
      $fl_programa=$row['fl_programa_sp'];
      $fl_usuario=$row['fl_usuario'];
      $fl_usu_pro=$row['fl_usu_pro'];
     
      $fg_tiene_plan=$row['fg_tiene_plan'];
      $fl_instituto=$row['fl_instituto'];
      $fg_quizes = $row['fg_quizes'];
      $fg_grade_tea = $row['fg_grade_tea'];
      $no_prom_quiz = $row['no_prom_quiz'];
      $no_prom_teacher = $row['no_prom_teacher'];
      $no_promedio = $row['no_promedio'];
      $nb_grupo=$row['nb_grupo'];
      $nb_grado=$row['nb_grado'];
      $fl_maestro=$row['fl_maestro'];
      $ds_progreso=$row['ds_progreso'];
      #Recuperamos el teacher.
      $Query="SELECT CONCAT(ds_nombres,' ',ds_apaterno)as nb_teacher FROM c_usuario WHERE fl_usuario=$fl_maestro ";
      $roe=RecuperaValor($Query);
      $nb_teacher=str_texto($roe[0]);


      if(empty($no_promedio))
        $no_promedio = 0;
        
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
      $fg_quizes = $row00[0];
      $fg_grade_tea = $row00[1];
      if($fg_quizes==1)
          $etq_quiz=ObtenEtiqueta(1916);
      if($fg_grade_tea==1)
          $etq_=ObtenEtiqueta(1917); 
      $icono_pul = '+';
         
      # Por defaul estara quiz
      if(!empty($fg_quizes) && !empty($fg_grade_tea))
        $assessment = $etq_quiz.' '.$icono_pul.' '.$etq_;
      else
        $assessment = $etq_quiz;
	
	
	
	
	
	
        // Agregar Informacion
      $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A'.$num, $ds_nombres)
      ->setCellValue('B'.$num, $ds_apaterno)
      ->setCellValue('C'.$num, $ds_instituto." (".$ds_pais.")")
      ->setCellValue('D'.$num, $ds_email)
      ->setCellValue('E'.$num, $nb_curso)
      ->setCellValue('F'.$num, $status)
      ->setCellValue('G'.$num, $fe_sesion)
      ->setCellValue('H'.$num, $ds_progreso)
      ->setCellValue('I'.$num, $assessment)
      ->setCellValue('J'.$num, $cl_calificacion."(".$no_promedio." %)")
      ->setCellValue('K'.$num, $nb_grupo)
      ->setCellValue('L'.$num, $nb_grado)
      ->setCellValue('M'.$num, $nb_teacher);

	}

		
    




	// Renombrar Hoja
	$objPHPExcel->getActiveSheet()->setTitle('FAME');

	// Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
	$objPHPExcel->setActiveSheetIndex(0);

	$random=date('Ymd')."".rand(1000,9000);
    
    
    // Redirect output to a client’s web browser (Excel5)
    header('Content-type: text/csv');
    header('Content-Disposition: attachment;filename="FAME_Students'.$random.'.csv"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
    $objWriter->save('php://output');
	exit;

	
    
    
    
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

    
    
?>