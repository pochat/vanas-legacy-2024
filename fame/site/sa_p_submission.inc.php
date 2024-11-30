<?php
  
  # Recupera los alumnos
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
  $Query  = "SELECT a.fl_usuario_sp, ".ConcatenaBD($concat)." 'ds_nombre' ";
  $Query .= "FROM k_usuario_programa a, c_usuario b ";
  $Query .= "WHERE a.fl_usuario_sp=b.fl_usuario  ";  
  $Query .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal_sp c, k_entregable_sp d WHERE c.fl_entrega_semanal_sp=d.fl_entrega_semanal_sp AND c.fl_alumno=a.fl_usuario_sp 
   AND c.fl_leccion_sp=$fl_leccion_sp  ) ";
  $Query .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal_sp e WHERE e.fl_alumno=a.fl_usuario_sp AND e.fl_leccion_sp=$fl_leccion_sp AND e.fl_promedio_semana IS NOT NULL  ) ";
  $Query .= "AND a.fl_usuario_sp=$fl_alumno AND a.fl_programa_sp=".$fl_programa_sp."  ";
  $Query .= "ORDER BY ds_nombre";
  $rs4 = EjecutaQuery($Query);
  while($row4 = RecuperaRegistro($rs4)) {
   $contador ++;
  
    $fl_alumno = $row4[0];
    $ds_nombre = str_uso_normal($row4[1]);
    $fg_entregado = '0';
    $ds_status = "<span class='label label-danger'><i class='fa fa-times'></i> ".ObtenEtiqueta(1963)."</span>";

    # Inserta los datos de la entrega semanal si no existen aun
    $Query  = "SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
    $row = RecuperaValor($Query);
    $fl_entrega_semanal = $row[0];
    if(empty($fl_entrega_semanal)) {
      $Query  = "INSERT INTO k_entrega_semanal (fl_alumno, fl_grupo, fl_semana) ";
      $Query .= "VALUES($fl_alumno, $fl_grupo, $fl_semana)";
      $fl_entrega_semanal = EjecutaInsert($Query);
    }
    
    # Inicia registro
    echo "
      <div class='row padding-10'>
        <div class='col col-sm-12 col-lg-2 col-xs-12 texta-align-center padding-10'>
          <div class='project-members'>
            <a href='#site/profile.php?profile_id=$fl_alumno&otro=1' rel='tooltip' data-placement='top' data-html='true' data-original-title='".$ds_nombre."'>
              <img src='".ObtenAvatarUsuario($fl_alumno)."' class='online' alt='".$ds_nombre."' width='70' height='70'>
            </a>
          </div>
          <a href='#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1' title='View $ds_nombre desktop'>$ds_nombre</a>
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
    
    # Recupera los entregables del alumno
    $Query  = "SELECT fl_entregable, fg_tipo, no_orden, ds_comentario,fe_entregado ";
    $Query .= "FROM k_entregable ";
    $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
    $Query .= "ORDER BY fg_tipo, no_orden";
    $rs3 = EjecutaQuery($Query);
    $tot_entregables = CuentaRegistros($rs3);
    while($row3 = RecuperaRegistro($rs3)) {
      $fl_entregable = $row3[0];
      $fg_tipo = $row3[1];
      $no_orden = $row3[2];
      $ds_comentario = str_uso_normal($row3[3]);
      $fe_entregado=ObtenFechaFormatoDiaMesAnioHora(str_uso_normal($row3[4]));
      
      $ds_orden = "";
      switch($fg_tipo) {
        case 'A':  $ds_tipo = ObtenEtiqueta(1968);     $nb_tab = "assignment"; break;
        case 'AR': $ds_tipo = ObtenEtiqueta(1969);     $nb_tab = "assignment_ref"; break;
        case 'S':  $ds_tipo = ObtenEtiqueta(1970);     $nb_tab = "sketch";
          $ds_orden = " $no_orden";
          break;
        case 'SR': $ds_tipo = ObtenEtiqueta(1971);     $nb_tab = "sketch_ref"; break;
      }
      echo "<a href='#site/desktop.php?student=$fl_alumno&week=$no_semana&tab=$nb_tab&fl_programa=$fl_programa_sp&t=1'>$ds_tipo$ds_orden</a>&nbsp;&nbsp;$ds_comentario <br>";
    }
    if($tot_entregables == 0)
      echo "<span class='label label-danger'><i class='fa fa-times'></i> ".ObtenEtiqueta(1972)."</span>";
    echo "</div>
      <div class='col col-sm-12 col-lg-2 col-xs-12 padding-10'>";
    if(!empty($fl_promedio_semana)) {
      $Query  = "SELECT cl_calificacion, ds_calificacion, fg_aprobado FROM c_calificacion WHERE fl_calificacion=$fl_promedio_semana";
      $row4 = RecuperaValor($Query);
      $cl_calificacion = $row4[0];
      $ds_calificacion = str_uso_normal($row4[1]);
      if($row4[2] <> '1')
        $ds_aprobado = "<span class='text_unread'>".ObtenEtiqueta(1973)."</span>";
      else
        $ds_aprobado = ObtenEtiqueta(1974);
      echo "$cl_calificacion $ds_calificacion<br>$ds_aprobado<br><br>";
      $ds_calificar = ObtenEtiqueta(1975);
    }
    else {
      echo "<span class='label label-danger'><i class='fa fa-times'></i> ".ObtenEtiqueta(1977)."</span><br><br>";
      $ds_calificar = ObtenEtiqueta(1976);
    }


    echo "
    <a data-toggle='tab' href='javascript:void(0);' OnClick='AsignarCalificacion2$contador();'  id='tab51' name='tab51' class='btn btn-primary'><i class='fa fa-pencil'></i> $ds_calificar </a><br />";
    
	 echo" 
    <script>
    function AsignarCalificacion2$contador(){
    
  //  alert('entro');
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
   
     $.ajax({
                    type: 'POST',
                    url: 'site/presenta_rubric.php',
                    data: 'fl_alumno='+fl_alumno+
                          '&fl_leccion_sp='+fl_leccion_sp+
                          '&fl_programa_sp='+fl_programa_sp,
																				  
                    async: false,
                    success: function (html) {
                        $('#presenta_calificacion').html(html);

                    }
                });

   
    }
    </script>
    ";
	
	
    echo 
      "</div>
    </div>
    <hr/>";
  }
?>