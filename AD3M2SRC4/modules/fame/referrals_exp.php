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
  
  
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  
  
  
  
  // Crea un nuevo objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Establecer propiedades
$objPHPExcel->getProperties()
->setCreator("FAME")
->setLastModifiedBy("FAME")
->setTitle("FAME Referrals");





$Query  = "(SELECT b.fl_envio_correo, b.ds_email, nb_programa, a.fe_alta,a.fg_confirmado,b.fl_invitado_por_usuario, u.ds_nombres nombre,u.ds_apaterno nb_paterno, CASE WHEN r.fg_autorizado='0' THEN 'FA' ELSE r.fg_autorizado END fg_autorizado,'' fl_pais  ";
$Query .= "FROM k_envio_email_reg_selfp a
				JOIN c_desbloquear_curso_alumno b ON a.fl_envio_correo=b.fl_envio_correo
				JOIN c_programa_sp c ON b.fl_programa_sp=c.fl_programa_sp 
				JOIN c_usuario u  ON u.fl_usuario=b.fl_invitado_por_usuario 
				LEFT JOIN k_responsable_alumno r ON r.fl_envio_correo=a.fl_envio_correo 
   ";
$Query .= "WHERE  fg_desbloquear_curso='1' ORDER BY b.fl_envio_correo DESC  )UNION( ";

$Query .= "   
             SELECT a.fl_envio_correo,a.ds_email, '".ObtenEtiqueta(2150)."' nb_programa ,a.fe_alta,a.fg_confirmado,a.fl_usu_invita fl_invitado_por_usuario,a.ds_first_name nombre,a.ds_last_name nb_paterno,CASE WHEN r.fg_autorizado ='0' THEN 'FA' ELSE  r.fg_autorizado END fg_autorizado  ,a.fl_pais      
			   FROM k_envio_email_reg_selfp a
            left JOIN k_responsable_alumno r ON r.fl_envio_correo =a.fl_envio_correo 
				WHERE a.fg_desbloquear_curso='1' AND a.fl_usu_invita=642   ORDER BY a.fl_envio_correo DESC
  
  )  ORDER BY fl_envio_correo DESC	 ";
    # Exporta los datos
    $rs = EjecutaQuery($Query);
    $tot_campos = CuentaCampos($rs);
	
	// Agregar Informacion
    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A1', 'ID')
        ->setCellValue('B1', 'First Name')
        ->setCellValue('C1', 'Last Name')
		->setCellValue('D1', ObtenEtiqueta(2203))
		->setCellValue('E1', ObtenEtiqueta(2204))
		->setCellValue('F1', ObtenEtiqueta(2205))
		->setCellValue('G1', 'Country')
		->setCellValue('H1',  ObtenEtiqueta(2206));
		
		
	
		$num=1;
	for($i=1;$row=RecuperaRegistro($rs);$i++) {
    
		$num++;
	  
        $fl_envio_correo = $row[0];
        $ds_email = $row[1];
        $nb_programa = $row[2];
        $fe_alta = $row[3];
        $fl_pais=$row['fl_pais'];
        
        #DAMOS FORMATO DIA,MES, ANÑO
        $date = date_create($fe_alta);
        $fe_alta=date_format($date,'F j, Y');
        $fe_hora=date_format($date,'g:i a');
        $fe_alta=$fe_alta." at ".$fe_hora."(Pacific time)";
        
        $fg_confirmado=$row[4];
        $ds_fname=str_texto($row['nombre']);
        $ds_lname=str_texto($row['nb_paterno']);
       
        $fg_autorizado=str_texto($row[7]);
        
        
        $Query="SELECT ds_pais FROM c_pais WHERE fl_pais=$fl_pais  ";
        $rowpai=RecuperaValor($Query);
        $nb_pais=str_texto($rowpai[0]);
        
        
        
        if($fg_confirmado==1){
            
            if($fg_autorizado=='FA'){
                $color="danger";
                $etq=ObtenEtiqueta(2126);//falta condfirmacion del papa
                $status_student="Inactive";
                
            }else{
                $color="success";
                $etq=ObtenEtiqueta(2207);
                
            }
            
            $Querym="SELECT fl_usuario,ds_login FROM c_usuario WHERE ds_email='$ds_email'  ";
            $ro=RecuperaValor($Querym);
            $fl_usu=$ro['fl_usuario'];
            $ds_login=str_texto($ro['ds_login']);
            $id_student="B2C-".$ds_login;
            
            if(empty($fl_pais)){
                
                $Query="SELECT p.ds_pais 
                            FROM k_usu_direccion_sp a 
                            JOIN c_pais p on a.fl_pais=p.fl_pais WHERE fl_usuario_sp=$fl_usu "; 
                $rowpa=RecuperaValor($Query);
                $nb_pais=str_texto($rowpa[0]);
                
            }
            
            
        }else{
            $color="danger";
            $etq=ObtenEtiqueta(2102);
            
            $id_student="B2C-".$fl_envio_correo;
            
        }
        
        
     
	
	
	
        // Agregar Informacion
        $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$num, $id_student)
        ->setCellValue('B'.$num, $ds_fname)
        ->setCellValue('C'.$num, $ds_lname)
		->setCellValue('D'.$num, $ds_email)
		->setCellValue('E'.$num, $nb_programa)
		->setCellValue('F'.$num, $etq)
		->setCellValue('G'.$num, $nb_pais)
		->setCellValue('H'.$num, $fe_alta);

	}

		
    




	// Renombrar Hoja
	$objPHPExcel->getActiveSheet()->setTitle('FAME');

	// Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
	$objPHPExcel->setActiveSheetIndex(0);

	$random=date('Ymd')."".rand(1000,9000);
    
    
    // Redirect output to a client’s web browser (Excel5)
    header('Content-type: text/csv');
    header('Content-Disposition: attachment;filename="FAME_Referrals'.$random.'.csv"');
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