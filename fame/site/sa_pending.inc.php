<?php
  
  # Recupera asignaciones pendientes por calificar
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
  $Query  = "SELECT a.fl_entrega_semanal_sp, a.fl_alumno, a.fg_entregado, a.fl_promedio_semana, ";
  $Query .= ConcatenaBD($concat)." 'ds_nombre' ";
  $Query .= "FROM k_entrega_semanal_sp a, c_usuario b ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario ";
  $Query .= "AND a.fl_leccion_sp=$fl_leccion_sp AND a.fl_alumno=$fl_alumno ";
  if($no_tab == 1) { // Assignments to grade - Aqui ves los estudiantes que por lo menos han subido un archivo
    $Query .= "AND a.fl_promedio_semana IS NULL ";
    $Query .= "AND EXISTS(SELECT 1 FROM k_entregable_sp c WHERE c.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp) ";
  }
  if($no_tab == 2) // Grading History - Aqui esta la historia de los estudiantes ya calificados, esto es para consulta, para re-hacer critiques y para cambiar calificaciones
    $Query .= "AND a.fl_promedio_semana IS NOT NULL ";
  $Query .= "ORDER BY ds_nombres ";
  $rs2 = EjecutaQuery($Query);
  while($row2 = RecuperaRegistro($rs2)) {
  
   
  
    $fl_entrega_semanal_sp = $row2[0];
    $fl_alumno = $row2[1];
    $fg_entregado = $row2[2];
    $fl_promedio_semana = $row2[3];
    if(empty($fl_promedio_semana))
      $fl_promedio_semana = 0;
    $ds_nombre = str_uso_normal($row2[4]);
    if($fg_entregado == '1')
      $ds_status = "<span class='label label-success'><i class='fa fa-thumbs-up'></i> ".ObtenEtiqueta(1972)." </span>";
    else
      $ds_status = "<span class='label label-danger'><i class='fa fa-exclamation-triangle'></i>  ".ObtenEtiqueta(1973)."</span>";
    
    # Inicia registro
    echo "
      <div class='row padding-10'>
        <div class='col col-sm-12 col-lg-2 col-xs-12 texta-align-center padding-10'>
          <div class='project-members'>
            <a href='#site/profile.php?profile_id=$fl_alumno&otro=1' rel='tooltip' data-placement='top' data-html='true' data-original-title='".$ds_nombre."'>
              <img src='".ObtenAvatarUsuario($fl_alumno)."' class='online' alt='".$ds_nombre."' width='70' height='70'>
            </a>
          </div>
          <a href='#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1' title='View $ds_nombre desktop'>$ds_nombre  </a>
          <br>
          $ds_status<br />
        </div>
        <div class='col col-sm-12 col-lg-3 col-xs-12 padding-10'>
        <b>".ObtenEtiqueta(1964)."</b> $nb_programa <br>
        <b>".ObtenEtiqueta(1965)."</b> $nb_grupo <br>
        <b>".ObtenEtiqueta(1966)."</b> $ds_titulo <br>
        <b>".ObtenEtiqueta(1967)." </b> $no_semana 
        </div>
        <div class='col col-sm-12 col-lg-5 col-xs-12 padding-10'>";
        
    # Obtenemos cuanto fg_animation ha subido
    $rwe1 = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='A'");
    $animation_tot = $rwe1[0];
    # Obtenemos cuanto fg_ref_animation ha subido
    $rwe2 = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='AR'");
    $ref_animation_tot = $rwe2[0];
    # Obtenemos cuanto sketch ha subido
    $rwe3 = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='S'");
    $sketch_tot = $rwe3[0];
    # Obtenemos cuanto sketch ha subido
    $rwe4 = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='SR'");
    $ref_sketch_tot = $rwe4[0];  
    
    # Recupera los entregables del alumno
    $Query  = "SELECT fl_entregable_sp, fg_tipo, no_orden, ds_comentario, DATE_FORMAT(fe_entregado, '%Y-%m-%d %H:%i:%s') ";
    $Query .= "FROM k_entregable_sp ";
    $Query .= "WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp ";
    $Query .= "GROUP BY fg_tipo ORDER BY fg_tipo, no_orden";
    $rs3 = EjecutaQuery($Query);
    $tot_entregables = CuentaRegistros($rs3);
    $animation = "<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1968)."  ".ObtenEtiqueta(1993)." ($animation_tot/1) <br></strong>";    
    $ref_animation = "<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1969)." ".ObtenEtiqueta(1993)." ($ref_animation_tot/1)<br></strong>";    
    $sketchs = "<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1970)." ".ObtenEtiqueta(1993)."  ($sketch_tot/$no_sketch)<br></strong>";    
    $ref_sketchs = "<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1970)." ".ObtenEtiqueta(1993)." ($ref_sketch_tot/1)<br></strong>";    
    while($row3 = RecuperaRegistro($rs3)) {
      $fl_entregable = $row3[0];
      $fg_tipo = $row3[1];
      $no_orden = $row3[2];
      $ds_comentario = str_uso_normal($row3[3]);
      // $fe_entregado=ObtenFechaFormatoDiaMesAnioHora(str_uso_normal($row3[4]));
      $fe_entregado= time_elapsed_string($row3[4], false);
      
      #asignamos formato de entregable
      
      
      
      $ds_orden = "";
      switch($fg_tipo) {
        case 'A':  
          $ds_tipo = ObtenEtiqueta(1968);     
          $nb_tab = "assignment"; 
          $entregar = "$animation_tot/1";
          $animation = "
          <div class='row'>
            <div class='col-sm-12 col-lg-12 col-xs-12'>
              <a class='txt-color-green' href='index.php#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1'><i class='fa fa-check'></i> <strong>$ds_tipo$ds_orden</strong> ($entregar)&nbsp;&nbsp;<b>".ObtenEtiqueta(1677).":</b> $fe_entregado</a>
            </div>
            <div class='col-sm-12 col-lg-12 col-xs-12'>$ds_comentario</div>
          </div>";
        break;
        case 'AR': 
          $ds_tipo = ObtenEtiqueta(1969);     
          $nb_tab = "assignment_ref";  
          $entregar = "$ref_animation_tot/1";
          $ref_animation = "
          <div class='row'>
            <div class='col-sm-12 col-lg-12 col-xs-12'>
              <a class='txt-color-green' href='#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1'><i class='fa fa-check'></i> <strong>$ds_tipo$ds_orden</strong> ($entregar)&nbsp;&nbsp;<b>".ObtenEtiqueta(1677).":</b> $fe_entregado</a>
            </div>
            <div class='col-sm-12 col-lg-12 col-xs-12'>$ds_comentario</div>
          </div>";
        break;
        case 'S':  
          $ds_tipo = ObtenEtiqueta(1970);     
          $nb_tab = "sketch"; 
          $entregar = "$sketch_tot/$no_sketch";
          $ds_orden = " $no_orden";
          if($no_sketch == $sketch_tot)
            $sketchs = "
            <div class='row'>
              <div class='col-sm-12 col-lg-12 col-xs-12'>
              <a class='txt-color-green' href='#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1'><i class='fa fa-check'></i> <strong>$ds_tipo</strong> ($entregar)&nbsp;&nbsp;<b>".ObtenEtiqueta(1677).":</b> $fe_entregado</a>
              </div>
              <div class='col-sm-12 col-lg-12 col-xs-12 padding-10'>$ds_comentario</div>
            </div>";
        break;
        case 'SR': 
          $ds_tipo = ObtenEtiqueta(1971);
          $nb_tab = "sketch_ref";
          $entregar = "$ref_sketch_tot/1";
          $ref_sketchs = "
          <div class='row'>
            <div class='col-sm-12 col-lg-12 col-xs-12'>
              <a class='txt-color-green'  href='#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1'><i class='fa fa-check'></i> <strong>$ds_tipo$ds_orden</strong> ($entregar)&nbsp;&nbsp;<b>".ObtenEtiqueta(1677).":</b> $fe_entregado</a>
            </div>
            <div class='col-sm-12 col-lg-12 col-xs-12'>$ds_comentario</div>
          </div>";
        break;
      }      
      // echo "<a  href='#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1'><strong>$ds_tipo$ds_orden</strong></a> ($entregar) $ds_animacion<br>";
    }
    if($fg_animacion=='1')
      echo $animation;
    if($fg_ref_animacion=='1')
      echo $ref_animation;
    if($no_sketch>=1)
      echo $sketchs;
    if($fg_ref_sketch=='1')
      echo $ref_sketchs;
    if($tot_entregables == 0)
      echo ObtenEtiqueta(1972);
    echo "</div>";
    
    echo "<div class='col col-sm-12 col-lg-2 col-xs-12 texta-align-center padding-10'>";
    if(!empty($fl_promedio_semana)) {
      $Query  = "SELECT cl_calificacion, ds_calificacion, fg_aprobado FROM c_calificacion WHERE fl_calificacion=$fl_promedio_semana";
      $row4 = RecuperaValor($Query);
      $cl_calificacion = $row4[0];
      $ds_calificacion = str_uso_normal($row4[1]);

      #Recuperamos la califiacion asignada.
      $Queryc="SELECT no_calificacion FROM k_calificacion_teacher WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";
      $rowc=RecuperaValor($Queryc); 
      $no_calificacion=$rowc[0];  
 
      
      
      
      if($row4[2] <> '1'){
        $ds_aprobado = "<span class='label label-danger'><i class='fa fa-thumbs-down'></i> ".ObtenEtiqueta(1973)."</span>";
        $color = "danger";
      }
      else{
        $ds_aprobado = "<span class='label label-success'><i class='fa fa-thumbs-up'></i>  ".ObtenEtiqueta(1974)."</span>";
        $color = "success";
      }
      echo "<strong>$cl_calificacion ($no_calificacion%) $ds_calificacion </strong><br>$ds_aprobado <br><br>";
      $ds_calificar = ObtenEtiqueta(1975);    
    }
    else {
      echo "<span class='label label-warning'><i class='fa fa-exclamation-triangle'></i>".ObtenEtiqueta(1977)."</span><br><br>";
      $ds_calificar = ObtenEtiqueta(1976);
      $color = "warning";
    }
	
    $contador ++;
	
	
	if($no_tab==1){
	
			echo "
				<a data-toggle='tab' href='javascript:void(0);'  OnClick='AsignarCalificacion$contador();window.scrollTo(0,0)' id='tab5' name='tab5' class='btn btn-primary'><i class='fa fa-pencil'></i> $ds_calificar  </a><br />
				 <script>
			function AsignarCalificacion$contador(){
			
			$('#presenta_calificacion').empty();
			$('#tab_3').removeClass('hidden');
			$('#tab_0').removeClass('active');
			$('#tab_1').removeClass('active');
			$('#tab_2').removeClass('active');
			$('#tab_3').addClass('active');
			
			$('#p_grade').removeClass('active');
			$('#p_incomplete').removeClass('active');
			$('#p_history').removeClass('active');
			$('#p_assignment_grade').addClass('active');
			
			var fl_alumno=$fl_alumno;
			var fl_leccion_sp=$fl_leccion_sp;
			var fl_programa_sp=$fl_programa_sp;
			var nb_grupo='$nb_grupo';
			var no_semana='$no_semana';
			var fl_entrega_semanal_sp=$fl_entrega_semanal_sp;
			 $.ajax({
							type: 'POST',
							url: 'site/presenta_rubric.php',
						   data: 'fl_alumno='+fl_alumno+
								  '&fl_leccion_sp='+fl_leccion_sp+
								  '&no_semana='+no_semana+
								  '&fl_entrega_semanal_sp='+fl_entrega_semanal_sp+
								  '&nb_grupo='+nb_grupo+
								  '&fl_programa_sp='+fl_programa_sp,
																						  
							async: false,
							success: function (html) {
								 $('#presenta_calificacion').html(html);

							}
						});

				//alert('entro');
			}
			</script>
		
			  	
				";
		
	}
	
	if($no_tab==2){
	
				echo "
				<a data-toggle='tab' href='javascript:void(0);'  OnClick='AsignarCalificacionS$contador();window.scrollTo(0,0)' id='tab5' name='tab5' class='btn btn-primary'><i class='fa fa-pencil'></i> $ds_calificar  </a><br />
				 <script>
			function AsignarCalificacionS$contador(){
			
			$('#presenta_calificacion').empty();
			$('#tab_3').removeClass('hidden');
			  
			$('#tab_0').removeClass('active');
			$('#tab_1').removeClass('active');
			$('#tab_2').removeClass('active');
			$('#tab_3').addClass('active');
			
			$('#p_grade').removeClass('active');
			$('#p_incomplete').removeClass('active');
			$('#p_history').removeClass('active');
			$('#p_assignment_grade').addClass('active');
			
			var fl_alumno=$fl_alumno;
			var fl_leccion_sp=$fl_leccion_sp;
			var fl_programa_sp=$fl_programa_sp;
			var nb_grupo='$nb_grupo';
			var no_semana='$no_semana';
			var fg_calificado=1;
			var fl_entrega_semanal_sp=$fl_entrega_semanal_sp;
			 $.ajax({
							type: 'POST',
							url: 'site/presenta_rubric.php',
						   data: 'fl_alumno='+fl_alumno+
								  '&fl_leccion_sp='+fl_leccion_sp+
								  '&no_semana='+no_semana+
								  '&fl_entrega_semanal_sp='+fl_entrega_semanal_sp+
								  '&nb_grupo='+nb_grupo+
								  '&fg_calificado='+fg_calificado+
								  '&fl_programa_sp='+fl_programa_sp,
																						  
							async: false,
							success: function (html) {
								 $('#presenta_calificacion').html(html);

							}
						});
			//alert('entro');
			
			}
			</script>
				
				";
	
	
	
	
	
	
	}
	
	
    
    
		
		
		
    echo "
      </div>
    </div>
    <hr/>";
  }
?>