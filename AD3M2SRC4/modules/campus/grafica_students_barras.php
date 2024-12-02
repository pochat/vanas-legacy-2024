<?php
  
  # Libreria general de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  $fg_students=RecibeParametroNumerico('fg_students');
  $fe_ini=RecibeParametroFecha('fe_ini');
  $fe_fin=RecibeParametroFecha('fe_fin');

  $fg_mostrar_etiquetas=RecibeParametroBinario('fg_mostrar_etiquetas');
  $fg_mostrar_diplomas=RecibeParametroBinario('fg_mostrar_diplomas');
  $fg_mostrar_certificados=RecibeParametroBinario('fg_mostrar_certificados');

  
  if(!empty($fe_ini))
  $fe_ini = "'".ValidaFecha($fe_ini)."'";
  if(!empty($fe_fin))
  $fe_fin = "'".ValidaFecha($fe_fin)."'";

     
	# Number of students per program - bar by Program Start Date
	  $Query3  = "SELECT ";
	  $Query3 .= "  nb_programa, ";
	  $Query3 .= "  CASE ";
	  $Query3 .= "    WHEN MONTH(start_date) IN (12, 1, 2) THEN CONCAT('Winter ', YEAR(start_date)) ";
	  $Query3 .= "    WHEN MONTH(start_date) IN (3, 4, 5) THEN CONCAT('Spring ', YEAR(start_date)) ";
	  $Query3 .= "    WHEN MONTH(start_date) IN (6, 7, 8) THEN CONCAT('Summer ', YEAR(start_date)) ";
	  $Query3 .= "    WHEN MONTH(start_date) IN (9, 10, 11) THEN CONCAT('Fall ', YEAR(start_date)) ";
	  $Query3 .= "  END AS periods, ";
	  $Query3 .= "  COUNT(1) ";
	  $Query3 .= "FROM ( ";
	  $Query3 .= "  SELECT a.fl_usuario, b.fl_periodo, c.nb_programa, ";
	  $Query3 .= "  (SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 ";
      
      if(  (!empty($fe_ini)) &&(!empty($fe_fin)) )
      $Query3 .="  AND fe_inicio>=$fe_ini and fe_inicio<=$fe_fin  ";

      $Query3 .="LIMIT 1) AS start_date ";
	  $Query3 .= "  FROM c_usuario a ";
	  $Query3 .= "  LEFT JOIN k_ses_app_frm_1 b ON b.cl_sesion=a.cl_sesion ";
	  $Query3 .= "  LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	  $Query3 .= "  WHERE fl_perfil=3 ";
      if($fg_mostrar_diplomas){
      $Query3 .=" AND c.nb_programa like'%Diploma%' ";
      }else if($fg_mostrar_certificados){
      $Query3 .=" AND c.nb_programa not like'%Diploma%' ";  
      }else if( ($fg_mostrar_certificados)&&($fg_mostrar_diplomas) ){
      $Query3 .=" ";   
      }


	  $Query3 .= ") AS program_table ";
	  $Query3 .= "WHERE program_table.start_date IS NOT NULL ";
	  $Query3 .= "GROUP BY periods, nb_programa ";
	  $rs1 = EjecutaQuery($Query3);
	  $rs2 = EjecutaQuery($Query3);
      $rs3 = EjecutaQuery($Query3);
      
      # Variable initialization to avoid error
      $total_cantidad = 0;

    for($i=0; $row1=RecuperaRegistro($rs1); $i++){

          $cantidad=$row1[2];
          $total_cantidad +=$cantidad;

    }
	  $entro=2;
	  
      
      


	for($i=0; $row2=RecuperaRegistro($rs2); $i++){
			
			 $nb_programa = $row2[0];
			 $fg_periodo= $row2[1];
			 $count = $row2[2];
			 
			 $periodTable_period[] = $fg_periodo;
			 $nameTable_program[] = $nb_programa;
			 // Table data
			 $periodTable[$fg_periodo][$nb_programa] = (int)$count;

	}
	  // Remove array keys, the keys are stored in xLabels and xInnerLabels
	  $periodTable = array_values($periodTable);

	  // Create the labels for the bar chart
	  $xperiodo = array_values(array_unique($periodTable_period));
	  $logintudxPeriodo=count($xperiodo);
	  $longitudPeridoTable=count($nameTable_program);
	  $xprogramas = array_values(array_unique($nameTable_program));
	  $longitud_programas=count($xprogramas);
    
 
 
 



?>
<canvas id="students_barras" height="120"></canvas>

<script>


new Chart(document.getElementById("students_barras"), {
    type: 'bar',
    data: {
      labels: [
	  
	  //"1900", "1950", "1999", "2050"
	  <?php
      for($i=0; $i<$logintudxPeriodo;$i++){

               $fg_fecha=$xperiodo[$i];
               
      ?>
     
	
          '<?php echo $fg_fecha ?>',


      <?php } 
      
      ?>
	
	  
	  
	  ],
      datasets: [
      /*  {
          label: "Africa 1eunen",
          backgroundColor: "#3e95cd",
          data: [133,221,783,2478]
        }, {
          label: "Europe ueueu",
          backgroundColor: "#8e5ea2",
          data: [408,547,675,734]
        }
	*/	
	<?php
    
      for($i=0; $i<$longitud_programas;$i++){

          $nb_programa=$xprogramas[$i];

          $name=$nb_programa;

          $cantidad=!empty($periodTable[$i][$nb_programa])?$periodTable[$i][$nb_programa]:NULL;
          
          //$olor=Color();

          if ($i%2==0){
              $color="#348FDB";
          }else{
              $color="#C84CFD";
          }

          $porcentaje=number_format((1000 * $cantidad / $total_cantidad ) / 10,2);
          


    ?>
	
		{
          label: " <?php echo $nb_programa." ".$porcentaje."%"; ?>",
		    backgroundColor: "<?php echo $color; ?>",
          data: [
					<?php 
						  for($m=0;$m<$logintudxPeriodo;$m++){
							  
							  $periodo=$xperiodo[$m];
							  $cantidad=!empty($periodTable[$m][$nb_programa])?$periodTable[$m][$nb_programa]:NULL;
						  
							  if(empty($cantidad))
								  $cantidad=0;

							  if($m<=($logintudxPeriodo-1))
								  echo $cantidad.",";
							  else
								  echo $cantidad;
						  }  
                    ?>
					
					
					
					]
			,

		    <?php if($i<=5){ ?>

		         hidden: false, // 
		    <?php }else{ ?>
		         hidden: true, // 
            <?php 
                  }
            ?>

		    
            

        },
	       
	
	
	
	<?php 
	  }
    ?>
	
	
	
	
		
		
		
      ]
    },
    options: {
      title: {
        display: true,
        text: '',
        scaleShowLabels: false
      },
	  
	  legend: {
	      position: 'bottom',
          <?php
            if($fg_mostrar_etiquetas){
          ?>
                display:true,
          <?php 
            }else{
          ?>
	            display:false,
          <?php } ?>

				
            },
	  
	  
    }
});



</script>







  
  	    <!-- Vector Maps Plugin: Vectormap engine, Vectormap language -->
		<script src="js/plugin/vectormap/jquery-jvectormap-1.2.2.min.js"></script>
		<script src="js/plugin/vectormap/jquery-jvectormap-world-mill-en.js"></script>
		
  
  
  
  
  
  
 