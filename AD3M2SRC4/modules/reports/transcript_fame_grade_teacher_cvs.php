<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../classes/Excel.php';

  
  $clave = RecibeParametroNumerico('c', True);#id del tabla k_usuario_programa
  $fl_usuario=RecibeParametroNumerico('u',True);
  $fl_instituto=RecibeParametroNumerico('i',True);

  #Recuperamos datos del studiante
  $Query="SELECT ds_nombres,ds_apaterno,ds_email, ";
  $Query .="fe_nacimiento, ";
  $Query .="ds_login FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $ds_nombres=$row['ds_nombres'];
  $ds_apaterno=$row['ds_apaterno'];
  $ds_email=$row['ds_email'];
  $fe_nacimiento=$row['fe_nacimiento'];
  $ds_login=$row['ds_login'];
  
  $fe_nacimiento=strtotime('+0 day',strtotime($fe_nacimiento));
  $fe_nacimiento= date('Y-m-d',$fe_nacimiento);
  #DAMOS FORMATO DIA,MES, AÑO.
  $date = date_create($fe_nacimiento);
  $fe_nacimiento=date_format($date,'F j, Y');
  
  
  #Recuperamos datos del curso/Programa.
  $Query="SELECT fl_usu_pro,P.nb_programa,P.fl_programa_sp,K.fe_entregado,fe_inicio_programa,fe_final_programa,fe_creacion,fl_maestro, no_creditos  
            FROM k_usuario_programa K
            JOIN c_programa_sp P ON P.fl_programa_sp=K.fl_programa_sp "; 
  $Query.="WHERE fl_usu_pro=$clave ";
  $row=RecuperaValor($Query);
  $fl_usuario_pro=$row['fl_usu_pro'];
  $nb_programa=str_texto($row['nb_programa']);
  $fl_programa_sp=$row['fl_programa_sp'];
  $fe_entregado=$row['fe_entregado'];
  $fe_inicio_curso=$row['fe_inicio_programa'];
  $fe_fin_curso=$row['fe_final_programa'];
  $fl_maestro=$row['fl_maestro'];
  $no_creditos=$row['no_creditos'];
  
  
  #Recuperamos el nombre del istituto:
  $Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $nb_instituto=$row[0];
  
  
  
  
  #Recuperamos quien es el maestro.
  $Query="SELECT U.ds_nombres,U.ds_apaterno FROM c_maestro_sp M 
            JOIN c_usuario U ON U.fl_usuario=M.fl_maestro_sp
            WHERE M.fl_maestro_sp=$fl_maestro ";
  $row=RecuperaValor($Query);
  $ds_nombre_teacher=$row[0];    
  $ds_apaterno_teacher=$row[1];
  
  
  if(!empty($fe_inicio_curso)){    
      #Damos formato alas fechas.
      $fe_inicio_curso=strtotime('+0 day',strtotime($fe_inicio_curso));
      $fe_inicio_curso= date('Y-m-d',$fe_inicio_curso);
      $fe_inicio_curso=GeneraFormatoFecha($fe_inicio_curso);
  }
  
  
  if(!empty($fe_fin_curso)){
      $fe_fin_curso=strtotime('+0 day',strtotime($fe_fin_curso));
      $fe_fin_curso= date('Y-m-d',$fe_fin_curso);
      $fe_fin_curso=GeneraFormatoFecha($fe_fin_curso);
  }
  
  
  
  $fe_entregado=strtotime('+0 day',strtotime($fe_entregado));
  $fe_entregado= date('Y-m-d',$fe_entregado);
  
  
  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
  $fe_actual= date('Y-m-d',$fe_actual);
  $fe_emision=GeneraFormatoFecha($fe_actual);
  
  
  
  $left_footer=ObtenEtiqueta(1614);
  $right_footer=ObtenEtiqueta(1615); 


  
  
  $studen_name="";
  $student_lname="";
  
  
			

  $objPHPExcel = new PHPExcel();
  
  
 $objPHPExcel->
    getProperties()
        ->setCreator('FAME')
        ->setLastModifiedBy('FAME')
        ->setTitle('Transcript_'.$nb_programa.'_'.$ds_nombres.'_'.$ds_apaterno.'')
        ->setSubject('')
        ->setDescription("")
        ->setKeywords("")
        ->setCategory("");

/**
 * MJD
 * ***********************************INICIA TITULOS DEL REPORTE
 * 
 */ 
  $startRowForInsertData = 1;
  $objPHPExcel->setActiveSheetIndex(0)
      ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
      ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
            
            
  //$startColumn = 'A';
 
  $objPHPExcel->setActiveSheetIndex(0)
     ->setCellValue('A1', 'Oficial Transcript')
     ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
     ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
  
  $startRowForInsertData++;
  $startRowForInsertData++;
  
  
  //nombre
  $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('A3', ObtenEtiqueta(510).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
      ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
  
  
  
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C3', $ds_nombres.' '.$ds_apaterno);
  
   $startRowForInsertData++;
   
   //id
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(511).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
      ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   
   
   
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $ds_login);
   
   
   $startRowForInsertData++;
   
   
   //fe_nacimiento
    $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(120).':')
     ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
      ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   
   
   
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $fe_nacimiento);
   
   
   $startRowForInsertData++;
   
   //nb_programa
   $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(512).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
    ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $nb_programa);
   
   
    $startRowForInsertData++;
	
	
	
	//fe_inicio
   $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(60).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
    ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $fe_inicio_curso);
	
	$startRowForInsertData++;
	
	
	//fe_fin curso
   $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(513).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
    ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $fe_fin_curso);
	
	$startRowForInsertData++;
	
	
	
     //fe_emision
   $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(515).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
    ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $fe_emision);
	
	$startRowForInsertData++;
	
	
		
     //no_creditos
   $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(1639).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
    ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $no_creditos);
	
	$startRowForInsertData++;
	
	
	
	
	
		
     //no_creditos
   $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(1613).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
    ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $ds_nombre_teacher.' '.$ds_apaterno_teacher);
	
	$startRowForInsertData++;
	
	
	
	

	//Instituto
   $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A'.$startRowForInsertData, ObtenEtiqueta(1693).':')
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
    ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('C'.$startRowForInsertData, $nb_instituto);
	
	$startRowForInsertData++;
	
    $startRowForInsertData++;
  
  
  
  
  
  
   $nb_program=strtoupper($nb_programa); 
  
  //Instituto
   $objPHPExcel->setActiveSheetIndex(0)
    ->getStyle('A' . $startRowForInsertData . ':Z' . $startRowForInsertData)
    ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
        )));
   $objPHPExcel->setActiveSheetIndex(0)
   ->setCellValue('A'.$startRowForInsertData, 'OFFICIAL TRANSCRIPT  '.$nb_program);
  
  
  
  
  
  $startRowForInsertData++;
  
  
  $startRowForInsertData++;
  
  /**
   *MJD ********************iNICIA TITULOS DE LA TABLA
   * 
   */ 
  
 
 
  
  $objPHPExcel->setActiveSheetIndex(0) 
      ->getStyle('A' . $startRowForInsertData . ':AL' . $startRowForInsertData)
      ->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => 'FFFFFFFF')
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array(
                            'rgb' => '0092DC'
                        )
                   
        )));
            
            
  $startColumn = 'A';
  
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue($startColumn++ . $startRowForInsertData, ObtenEtiqueta(1605));  
		  
		  $startColumn++;
		 
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue($startColumn++ . $startRowForInsertData, ObtenEtiqueta(1606));
		  
		  $startColumn++;
		  
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue($startColumn++ . $startRowForInsertData, ObtenEtiqueta(1612));
		  
		 $startColumn++;
		 $startColumn++; 
		 $startColumn++;
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue($startColumn++ . $startRowForInsertData, ObtenEtiqueta(1610));
		  
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue($startColumn++ . $startRowForInsertData, ObtenEtiqueta(1611));
 

  $startRowForInsertData++;
  
  
  
  
  
  
  /**
   * 
   * MJD *********************Inicia inserccion de datos
   * 
   */
  
  #1.verificamos cuantas lecciones existen en esete programa(CUANDO EXISTE FL_PROMEDIO QUIERE DECIR QUE YA ESTA CALIFICADA)
           $Query2="SELECT A.fl_alumno,A.fl_leccion_sp,A.fl_promedio_semana,C.nb_programa,B.ds_titulo,B.no_valor_rubric,B.no_semana,D.cl_calificacion,D.no_equivalencia 
						    FROM k_entrega_semanal_sp A
						    JOIN c_leccion_sp B  ON B.fl_leccion_sp=A.fl_leccion_sp 
						    JOIN c_programa_sp C ON C.fl_programa_sp=B.fl_programa_sp
						    JOIN c_calificacion_sp D ON D.fl_calificacion=A.fl_promedio_semana
						    WHERE A.fl_alumno=$fl_usuario AND C.fl_programa_sp=$fl_programa_sp AND fl_promedio_semana IS NOT NULL ORDER BY B.no_semana ASC ";
           $rs2 = EjecutaQuery($Query2);
  
           
  foreach ($rs2 as $data)   {  
      
			$contador2++;
	  
	  
			 $fl_leccion_sp=$data['fl_leccion_sp'];
             $no_session=$data['no_semana'];
             $nb_leccion=$data['ds_titulo'];
             $grade=$data['cl_calificacion'];
	  
			 #Recuperamos la calificacion asignada por el teacher (sin calculos ni equivalencias.)
             $Query2="SELECT no_calificacion FROM k_calificacion_teacher WHERE fl_alumno=$fl_usuario and fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp ";
             $row2=RecuperaValor($Query2);
             $no_calificacion= $row2['no_calificacion'];
			 
			 
			 #Recupermaos la fecha de utima modificacion/creacion
             $Query3 ="SELECT fe_modificacion 
														FROM c_com_criterio_teacher 
														WHERE fl_leccion_sp=$fl_leccion_sp AND fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa_sp  AND fg_com_final='1' ";
             
             $row3=RecuperaValor($Query3);
             $fe_modificacion=GeneraFormatoFecha($row3[0]);
			 
			 
			 $sum_porcentaje += $no_calificacion;
	  

	  
	  
			  $startColumn='A';
			  
			  $objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($startColumn++. $startRowForInsertData, $fe_modificacion);
			
              $startColumn++;      
              $objPHPExcel->setActiveSheetIndex(0)
                   ->setCellValue($startColumn++. $startRowForInsertData, $no_session);
     
              $startColumn++;
			  $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($startColumn++. $startRowForInsertData, $nb_leccion);  
				$startColumn++;
			    $startColumn++;
			    $startColumn++;
			  
              $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($startColumn++. $startRowForInsertData, $grade);  
              
			  $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($startColumn++. $startRowForInsertData, $no_calificacion);				
              
           
      $startRowForInsertData++;
      
  
    
     
      
  }	
  
  
  
  
  
  
  
           #Presenta totales.
  
            $total=$sum_porcentaje/$contador2;
			
			#Buscamos en que rangose encuentra y se recuepra el grado final.
			  $Query="SELECT cl_calificacion,no_min,no_max,no_equivalencia FROM c_calificacion_sp WHERE 1=1 ";
			  $rs4 = EjecutaQuery($Query);
			  $tot_registros = CuentaRegistros($rs4);
			  for($i=1;$row4=RecuperaRegistro($rs4);$i++){
				  $no_min=$row4['no_min'];
				  $no_max=$row4['no_max'];
				  
				  
					  if(( $total >=$no_min)&&($total<=$no_max) ){
					  
						  $grade_final=$row4['cl_calificacion'];
					  
					  }
				  
				  
				}
			
			
			
			
			$startColumn='A';
		    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($startColumn++. $startRowForInsertData, '');
		
			  $startColumn++;      
			  $objPHPExcel->setActiveSheetIndex(0)
				   ->setCellValue($startColumn++. $startRowForInsertData, '');
 
			  $startColumn++;
			  $objPHPExcel->setActiveSheetIndex(0)
				  ->setCellValue($startColumn++. $startRowForInsertData,  ObtenEtiqueta(524).':');  
				$startColumn++;
				$startColumn++;
				$startColumn++;
		  
			  $objPHPExcel->setActiveSheetIndex(0)
				  ->setCellValue($startColumn++. $startRowForInsertData, $grade_final);  
		  
			  $objPHPExcel->setActiveSheetIndex(0)
				  ->setCellValue($startColumn++. $startRowForInsertData, number_format($total));
	  
  
  
  
  
  
  

  
  
  
  $objPHPExcel->getActiveSheet()->setTitle('FAME');
  $objPHPExcel->setActiveSheetIndex(0);


  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="Transcript_'.$nb_programa.'_'.$ds_nombres.'_'.$ds_apaterno.'.xls"');
  header('Cache-Control: max-age=0');
  
  $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
 $objWriter->save('php://output');
  
  //header('Location: ' . ADM_MODULOS . '/' . 'auxiliares' . '/inmuebles.php');
die;