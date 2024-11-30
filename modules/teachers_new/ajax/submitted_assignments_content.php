<?php
	# Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  
  ?>
  
  <!----Librerias para que funcione el datatable vista------>
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/lib_css_original/smartadmin-production-plugins.min.css">
        <style>
            div.dataTables_filter {
                top: -44px !important;
				 position: ineri !important;
            }
			
   
        </style>
		
		
		
  <?php
  
  
  # Recibe parametros
  $no_tab = RecibeParametroNumerico('tab');
  if(empty($no_tab) OR $no_tab >4 )
    $no_tab = 1;
  if($no_tab==1)
    $tab = "p_grade";
  if($no_tab==2)
    $tab = "p_history";
  if($no_tab==3)
    $tab = "p_incomplete";
  if($no_tab==4)
    $tab = "p_assignment_grade";
  $fe_actual = ObtenFechaActual( );
  $Query  = "SELECT a.fl_grupo, a.nb_grupo, b.fl_semana, DATE_FORMAT(b.fe_entrega, '%c') 'fe_entrega_m', DATE_FORMAT(b.fe_entrega, '%e, %Y') "; 
  $Query .= "'fe_entrega_da', DATE_FORMAT(b.fe_calificacion, '%c') 'fe_calificacion_m', DATE_FORMAT(b.fe_calificacion, '%e, %Y') 'fe_calificacion_da', ";
  $Query .= "c.no_grado, c.no_semana, c.ds_titulo, c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, d.nb_programa, ";
  $Query .= "DATEDIFF(b.fe_entrega, '".$fe_actual."') no_dias, d.ds_tipo ds_tipo_programa ";
  $Query .= "FROM c_grupo a LEFT JOIN k_semana b ON(b.fl_term = a.fl_term) LEFT JOIN c_leccion c ON(c.fl_leccion = b.fl_leccion) ";
  $Query .= "LEFT JOIN c_programa d ON(d.fl_programa = c.fl_programa) ";
  $Query .= "WHERE a.fl_maestro=".$fl_maestro." AND (c.fg_animacion = '1' OR c.fg_ref_animacion = '1' OR c.no_sketch > 0 OR c.fg_ref_sketch = '1') ";
  $Query .= "AND c.no_semana <= (SELECT MAX(f.no_semana) FROM k_semana e, c_leccion f WHERE e.fl_leccion = f.fl_leccion ";
  $Query .= "AND TO_DAYS(e.fe_publicacion) <= TO_DAYS('".$fe_actual."') AND f.fl_programa = c.fl_programa AND f.no_grado = c.no_grado AND "; $Query .= "e.fl_term = a.fl_term) ";
  $Query .= "AND EXISTS (SELECT 1 FROM k_alumno_grupo g
  JOIN c_usuario h ON(h.fl_usuario=g.fl_alumno) WHERE h.fg_activo = '1' AND g.fl_grupo =  a.fl_grupo) AND a.no_alumnos>0 ";  
  
  $Query .= "ORDER BY d.no_orden, c.no_grado, c.no_semana DESC, a.nb_grupo ";
  $rs = EjecutaQuery($Query);
  $tot_grupos = CuentaRegistros($rs);
  
  if($no_tab < 4){
  
          while($row=RecuperaRegistro($rs)) {  
            if($no_tab==1 || $no_tab==2){
              # Verirficamos si el usuario esta activo y ya entrego su trabajos para los tabs ASSIGMENT TO GRADE AND GRADING HISTORY
              $Query3  = "SELECT 1 FROM k_entrega_semanal i LEFT JOIN k_entregable k ON(k.fl_entrega_semanal=i.fl_entrega_semanal) ";
              $Query3 .= "JOIN c_usuario j ON(j.fl_usuario=i.fl_alumno) WHERE  i.fl_grupo = ".$row[0]." AND i.fl_semana = ".$row[2]." ";
              if($no_tab==1)
                $Query3 .= "AND i.fl_promedio_semana IS NULL";
              if($no_tab==2)
                $Query3 .= "AND i.fl_promedio_semana IS NOT NULL";
            }
            else{
              # Verifica si el usuario no ha entregado trabajo y no tiene calificado
              $Query3  = "SELECT 1 FROM k_alumno_grupo k WHERE k.fl_grupo=".$row[0]." ";
              $Query3 .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal m JOIN k_entregable n ON(n.fl_entrega_semanal=m.fl_entrega_semanal) WHERE m.fl_alumno=k.fl_alumno AND m.fl_semana=".$row[2]." AND m.fl_promedio_semana IS NOT NULL) ";
              $Query3 .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal o WHERE o.fl_alumno=k.fl_alumno AND o.fl_semana=".$row[2]." ";
              $Query3 .= "AND o.fl_promedio_semana IS NOT NULL) ";
            }
            $row3 = RecuperaValor($Query3);    
            if(!empty($row3[0])){
            $fl_grupo = $row[0];
            $nb_grupo = str_uso_normal($row[1]);
            $fl_semana = $row[2];
            $fe_entrega = ObtenNombreMes($row[3])." ".$row[4];
            $fe_calificacion = ObtenNombreMes($row[5])." ".$row[6];
            $no_grado = $row[7];
            $no_semana = $row[8];
            $ds_titulo = str_uso_normal($row[9]);
            $fg_animacion = $row[10];
            $fg_ref_animacion = $row[11];
            $no_sketch = $row[12];
            $fg_ref_sketch = $row[13];
            $nb_programa = str_uso_normal($row[14]);
            $no_dias = $row[15];
            $ds_tipo_programa = $row[16];
  
            # Requerimientos de la leccion
            $ds_animacion = "No assignment";
            if($fg_animacion == '1')
              $ds_animacion = "Assignment";
            $ds_ref_animacion = "No assignment reference";
            if($fg_ref_animacion == '1')
              $ds_ref_animacion = "Assignment reference";
            if($no_sketch == '0')
              $ds_sketch = "No sketches";
            elseif($no_sketch == '1')
              $ds_sketch = "1 sketch";
            else
              $ds_sketch = "$no_sketch sketches";
            $ds_ref_sketch = "No sketch reference";
            if($fg_ref_sketch == '1')
              $ds_ref_sketch = "Sketch reference";
    
            // # Inicia bloque de Programa - Grado
            // if($nb_programa <> $nb_programa_ant OR $no_grado <> $no_grado_ant OR $no_semana <> $no_semana_ant) {
              // echo "
                // <tr><td colspan='5' class='text-center' style='font-size:16px; font-weight:600;'>$nb_programa, Term $no_grado<br>Week $no_semana: $ds_titulo</td></tr>
                // <tr>
                  // <td colspan='5' class='text-center'>
                    // Submission due date is <b>$fe_entrega</b>, Evaluation due date is <b>$fe_calificacion</b>
                    // <br>
                    // <b>This lesson requires:</b> $ds_animacion, $ds_ref_animacion, $ds_sketch, $ds_ref_sketch
                  // </td>
                // </tr>";

              // $nb_programa_ant = $nb_programa;
              // $no_grado_ant = $no_grado;
              // $no_semana_ant = $no_semana;
            // }
    
            # Inicia bloque de Grupo
            // echo 
     	        // "<tr><td colspan='5' class='text-center' style='font-size:14px; font-weight:600;'>Group $nb_grupo</td></tr>";
    
            // # Presenta registros para el tab seleccionado
            // if($no_tab == 1 OR $no_tab == 2)
              // require("sa_pending.inc.php");
            // if($no_tab == 3)
              // require("sa_p_submission.inc.php");
            }
          }
  
  }
  
  if($no_tab == 1)
     require("view_asigment_grade.php");
  if($no_tab == 2)
   require("view_history_grade.php");
  if($no_tab == 3)
     require("view_incomplete_grade.php");
  
  
  if($no_tab == 4 ){
      require("sa_transcript.inc.php");
  }
  
  if($tot_grupos == 0){
    switch($no_tab) {
      case 1: $no_hay = "You have no assignments to grade."; break;
      case 2: $no_hay = "You have no graded active students."; break;
      case 3: $no_hay = "You have no students pending upload."; break;
      default: $no_hay = ""; break;
    }
  	echo "
      <tr>
        <td class='text-center' style='font-size:16px; font-weight:600;'>
          {$no_hay}
        </td>
      </tr>";
  }

?>

