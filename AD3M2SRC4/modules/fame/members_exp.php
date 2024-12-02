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




  
  $Query="SELECT I.fl_instituto,I.fl_usuario_sp,I.ds_instituto,I.no_usuarios,I.ds_codigo_area,I.no_telefono,P.ds_pais ,U.ds_nombres,U.ds_apaterno,I.fe_creacion,U.fg_activo,I.fe_trial_expiracion,U.ds_email 
            
            FROM c_instituto I  
            JOIN c_pais P ON P.fl_pais=I.fl_pais
            JOIN c_usuario U ON U.fl_usuario=I.fl_usuario_sp 
            WHERE 1=1 AND I.fl_instituto<>1 AND I.fg_tiene_plan='1'  ";

    # Exporta los datos
    $rs = EjecutaQuery($Query);
    $tot_campos = CuentaCampos($rs);
	
	// Agregar Informacion
    $objPHPExcel->setActiveSheetIndex(0)
	
	    ->setCellValue('A1', ObtenEtiqueta(933))
		->setCellValue('B1', ObtenEtiqueta(934))
		->setCellValue('C1', "First Name")
		->setCellValue('D1', "Last Name")
		->setCellValue('E1', 'Email')
		->setCellValue('F1', ObtenEtiqueta(1559))
		->setCellValue('G1', ObtenEtiqueta(1579))
		->setCellValue('H1', ObtenEtiqueta(1582))
		->setCellValue('I1', ObtenEtiqueta(1581));
	
		
	
		$num=1;
	for($i=1;$row=RecuperaRegistro($rs);$i++) {
    
		$num++;
	  
		 $fl_instituto=$row['fl_instituto'];
         $fl_usuario=$row['fl_usuario_sp'];
         $nb_instituto=str_texto($row['ds_instituto']);
         $no_usuarios=$row['no_usuarios'];
         $ds_codigo_area=str_texto($row['ds_codigo_area']);
         $no_telefono=$row['no_telefono'];
         $nb_pais=$row['ds_pais'];
         $nb_admin=str_texto($row['ds_nombres']);
		 $ds_apaterno = str_texto($row['ds_apaterno']); 
         $fe_creacion=str_texto($row['fe_creacion']);
         $fg_activo=$row['fg_activo'];
         $fe_final_periodo_prueba=str_texto($row['fe_trial_expiracion']);
         $ds_email=str_texto($row['ds_email']);
		 
         
         #Obtenemos el numero de usuarios del instituto. sin contar el administrador
         //$Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND fl_perfil_sp <>12  ";
         $Query="SELECT  no_total_licencias,no_licencias_usadas,no_licencias_disponibles FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
		 $row=RecuperaValor($Query);
         $total_user=$row[0];
         $no_licencia_usadas=$row[1];
         $no_licencias_disponibles=$row[2];
         
		 
		 
         
         #Obtenemos cuantos teacher tiene el instituto que sean activos
         $Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND fl_perfil_sp =".PFL_MAESTRO_SELF." AND fg_activo='1' ";
         $row=RecuperaValor($Query);
         $total_teachers=$row[0];
         
         #Obtenemos cuantos students tiene el isntituto que sean activos.
         $Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND fl_perfil_sp =".PFL_ESTUDIANTE_SELF." AND fg_activo='1' ";
         $row=RecuperaValor($Query);
         $total_estudiantes=$row[0];
         
      
         #DAMOS FORMATO DIA,MES,AÑO
         $date=date_create($fe_creacion);
         $miembro_desde=date_format($date,'F j, Y');
         
         
         $fe_registro=strtotime('+0 day',strtotime($fe_creacion));
         $fe_registro= date('d-m-Y',$fe_registro);
         
         $fe_final_periodo_prueba=strtotime('+0 day',strtotime($fe_final_periodo_prueba));
         $fe_final_periodo_prueba= date('d-m-Y',$fe_final_periodo_prueba);
         
         
         $fe_periodo_trial=$fe_registro." - ".$fe_final_periodo_prueba;
         
         
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
         
	
	
        // Agregar Informacion
        $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$num, $nb_instituto)
		->setCellValue('B'.$num, $nb_pais)
		->setCellValue('C'.$num, $nb_admin)
		->setCellValue('D'.$num, $ds_apaterno)
		->setCellValue('E'.$num, $ds_email)
		->setCellValue('F'.$num, $total_user." ".ObtenEtiqueta(989).": ".$no_licencia_usadas." ".ObtenEtiqueta(990).": ".$no_licencias_disponibles)
		->setCellValue('G'.$num, "Students: ".$total_estudiantes."  Teacher: ".$total_teachers)
		->setCellValue('H'.$num, $miembro_desde."  Trial date: ".$fe_periodo_trial)
		->setCellValue('I'.$num, $status);
		

	}

		
    




	// Renombrar Hoja
	$objPHPExcel->getActiveSheet()->setTitle('FAME');

	// Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
	$objPHPExcel->setActiveSheetIndex(0);

    $random=date('Ymd')."".rand(1000,9000);
    
	// Redirect output to a client’s web browser (Excel5)
    header('Content-type: text/csv');
    header('Content-Disposition: attachment;filename="FAME_Members'.$random.'.csv"');
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

    
    #Obtenemos dias que llevo de mi plan actual. y para obtener los dias faltantes solo hay que invertir fechas.
    function ObtenDiasRestantesTrial($fe_final,$fe_inicial){

        $Query="SELECT DATEDIFF('$fe_final','$fe_inicial')";
        $row=RecuperaValor($Query);
        $no_dias=$row[0];
        
        
        
        return $no_dias;
    }   
    
?>