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
  require '../../AD3M2SRC4/lib/general.inc.php';
  require_once '../../AD3M2SRC4/lib/PHPExcel-1.8/PHPExcel.php';
  
 
  
  # Recibe parametros
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $fl_instituto = RecibeParametroNumerico('fl_instituto');
  
  
  
  
  
  
  // Crea un nuevo objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Establecer propiedades
$objPHPExcel->getProperties()
->setCreator("FAME")
->setLastModifiedBy("FAME")
->setTitle("FAME Students");





    $Query="SELECT b.fl_usuario,b.ds_nombres,b.ds_apaterno,b.ds_email,b.fe_nacimiento,b.fl_instituto, b.fl_perfil_sp FROM c_followers a 
		   JOIN c_usuario b ON b.fl_usuario =a.fl_usuario_destino 
           WHERE fl_usuario_origen =$fl_usuario ";        
    # Exporta los datos
    $rs = EjecutaQuery($Query);
    $tot_campos = CuentaCampos($rs);
	
	// Agregar Informacion
    $objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A1', "First Name")
		->setCellValue('B1', "Last Name")
		->setCellValue('C1', "Role")
		->setCellValue('D1', 'Email')
		->setCellValue('E1', 'Grade')
		->setCellValue('F1', 'Date of Birth')
		->setCellValue('G1', 'School')
		->setCellValue('H1', 'Teacher First Name')
		->setCellValue('I1', 'Teacher Last Name')
		->setCellValue('J1', 'Teacher email')
		->setCellValue('K1', 'Parent first name')
        ->setCellValue('L1','Parent last name')
        ->setCellValue('M1','Parent email')
        ->setCellValue('N1','Relationship');
		$num=1;
	for($i=1;$row=RecuperaRegistro($rs);$i++) {
	  $num++;
	  $fl_usuario=$row['fl_usuario'];            
      $ds_nombres=str_texto($row['ds_nombres']);
	  $ds_apaterno=str_texto($row['ds_apaterno']);
	  $ds_email=$row['ds_email'];
      $fe_nacimiento=str_texto($row['fe_nacimiento']);
      $fl_instituto=$row['fl_instituto'];
      $fl_perfil_sp=$row['fl_perfil_sp'];
	  
	  #rECUPERAMOS EL GRADO
	  $Query0="SELECT b.nb_grado from c_alumno_sp a join k_grado_fame b on a.fl_grado=b.fl_grado where fl_alumno_sp=$fl_usuario ";
	  $row0=RecuperaValor($Query0);
	  $grade=$row0['nb_grado'];
	  
	  
	  #Recuperamos el instituto.
	  $Query1="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
	  $row1=RecuperaValor($Query1);
	  $nb_instituto=str_texto($row1['ds_instituto']);
	  
	  #Recupermaos el teacher.
	  $Query2="SELECT MAX(fl_maestro) FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario ";
	  $row2=RecuperaValor($Query2);
	  $fl_maestro=$row2[0];
	  
	  #Recuperamos datos del teacher.
	  $Query3="SELECT ds_nombres,ds_apaterno,ds_email FROM c_usuario WHERE fl_usuario=$fl_maestro ";
	  $row3=RecuperaValor($Query3);
	  $nb_teacher=str_texto($row3['ds_nombres']);
	  $nb_apaterno_teacher=str_texto($row3['ds_apaterno']);
	  $ds_email_teacher=$row3['ds_email'];
	  	  
	  #Recuperamos datos del responsable.
	  $Query4="SELECT a.ds_fname,a.ds_lname,a.ds_email,nb_parentesco FROM k_responsable_alumno a join c_parentesco b on b.cl_parentesco=a.cl_parentesco WHERE fl_usuario=$fl_usuario ";
	  $row4=RecuperaValor($Query4);
	  $fname_responsable=str_texto($row4['ds_fname']);
	  $lname_responsable=str_texto($row4['ds_lname']);
	  $email_responsable=$row4['ds_email'];
	  $nb_parentesco=$row4['nb_parentesco'];
	  
	  if($fl_perfil_sp==13)
          $role="Admin";
      if($fl_perfil_sp==14)
          $role="Teacher";
	  if($fl_perfil_sp==15)
		  $role="Student";

      #Obtenemos fecha actual :
      $Query = "Select CURDATE() ";
      $row = RecuperaValor($Query);
      $fe_actual = str_texto($row[0]);
      $fe_actual=strtotime('+0 day',strtotime($fe_actual));
      $fe_actual= date('Y-m-d',$fe_actual);
      
	  
	  
      // Agregar Informacion
      $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A'.$num, $ds_nombres)
      ->setCellValue('B'.$num, $ds_apaterno)
	  ->setCellValue('C'.$num, $role)
      ->setCellValue('D'.$num, $ds_email)
	  ->setCellValue('E'.$num, $grade)
      ->setCellValue('F'.$num, $fe_nacimiento)
      ->setCellValue('G'.$num, $nb_instituto)
      ->setCellValue('H'.$num, $nb_teacher)
      ->setCellValue('I'.$num, $nb_apaterno_teacher)
      ->setCellValue('J'.$num, $ds_email_teacher)
      ->setCellValue('K'.$num, $fname_responsable)
      ->setCellValue('L'.$num, $lname_responsable)
      ->setCellValue('M'.$num, $email_responsable)
      ->setCellValue('N'.$num, $nb_parentesco);
   

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