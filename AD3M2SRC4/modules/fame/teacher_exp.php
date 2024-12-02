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
->setTitle("FAME Teacher");




  # Obtiene los teachers
  $Query  = "SELECT m.fl_maestro_sp, us.ds_nombres, us.ds_apaterno,us.ds_email, i.ds_instituto, us.fg_activo, us.fe_sesion, p.ds_pais, i.fl_pais, m.ds_ruta_avatar, i.fl_instituto, ";
  $Query .= "pro.nb_programa, pr.ds_progreso, pr.fl_programa_sp, pr.fl_usu_pro,i.fg_tiene_plan,i.fl_instituto ";
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
    # Exporta los datos
    $rs = EjecutaQuery($Query);
    $tot_campos = CuentaCampos($rs);
	
	// Agregar Informacion
    $objPHPExcel->setActiveSheetIndex(0)
	
	    ->setCellValue('A1', "First Name")
		->setCellValue('B1', "Last Name")
		->setCellValue('C1', ObtenEtiqueta(1868))
		->setCellValue('D1', 'Email')
		->setCellValue('E1', ObtenEtiqueta(1869))
		->setCellValue('F1', ObtenEtiqueta(1870))
		->setCellValue('G1', ObtenEtiqueta(1871))
		->setCellValue('H1', ObtenEtiqueta(1872))
		->setCellValue('I1', ObtenEtiqueta(1873));
	
		
	
		$num=1;
	for($i=1;$row=RecuperaRegistro($rs);$i++) {
    
		$num++;
	  
	  $fl_maestro_sp = $row[0];
      $ds_nombres = str_texto($row[1]);
      $ds_apaterno = str_texto($row[2]); 
	  $ds_email = str_texto($row[3]);
      $ds_instituto = str_texto($row[4]);
      $fg_activo = $row[5];
      $fe_sesion = time_elapsed_string($row[6]);
      $ds_pais = $row[7];      
      
      $nb_programa = $row[11];
      if(empty($nb_programa))
        $nb_programa = "Unassigned";
      $ds_progreso = $row[12];
      $fl_programa_sp = $row[13];
      // if(empty($ds_progreso)){
        // $ds_promedio = ObtenPromedioPrograma($fl_programa_sp, $fl_maestro_sp);
      // }
      $fl_usu_pro = $row[14];
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
	  
	  
	  
      # Si el maestro no esta cursando enviamos el fl_usuario
      if(empty($fl_usu_pro))
        $fl_usu_pro = $fl_maestro_sp;
      
      # Obtenemos el pais de la instituto
      $row1 = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais=".$row[7]);
      $ds_pais_inst = $row1[0];
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
	
	
	
	
        // Agregar Informacion
        $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$num, $ds_nombres)
		->setCellValue('B'.$num, $ds_apaterno)
		->setCellValue('C'.$num, $ds_instituto)
		->setCellValue('D'.$num, $ds_email)
		->setCellValue('E'.$num, $nb_programa)
		->setCellValue('F'.$num, $status)
		->setCellValue('G'.$num, $fe_sesion)
		->setCellValue('H'.$num, $ds_progreso)
		->setCellValue('I'.$num, $no_students);
		

	}

		
    




	// Renombrar Hoja
	$objPHPExcel->getActiveSheet()->setTitle('FAME');

	// Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
	$objPHPExcel->setActiveSheetIndex(0);

	// Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel en formato xlsx.
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //header('Content-Disposition: attachment;filename="FAME_Teachers_'.date('Ymd').rand(1000,9000).'".xlsx"');
    //header('Cache-Control: max-age=0');
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    //$objWriter->save('php://output');
    
    $random=date('Ymd')."".rand(1000,9000);
    
    
    // Redirect output to a client’s web browser (Excel5)
    header('Content-type: text/csv');
    header('Content-Disposition: attachment;filename="FAME_Teachers'.$random.'.csv"');
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