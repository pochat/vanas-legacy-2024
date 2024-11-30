<?php
	#  Libreria de funciones	
	require("../lib/self_general.php");
  
	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibe parametros
  $no_tab = RecibeParametroNumerico('tab');
  if(empty($no_tab) OR $no_tab > 4)
    $no_tab = 1;
  
  /*# Obtenemos los programas que tiene asignado el maestro
  # con los alumnos que podria asignar calificacion
  $Query  = "SELECT c.no_semana, c.ds_titulo, c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, ";
  $Query .= "e.nb_programa, e.ds_tipo ds_tipo_programa, c.fl_leccion_sp, a.fl_usuario_sp, f.nb_grupo, a.fl_programa_sp ";
  $Query .= "FROM k_usuario_programa a, k_details_usu_pro b, c_leccion_sp c, c_programa_sp e, c_alumno_sp f ";
  $Query .= "WHERE a.fl_maestro=$fl_maestro AND b.fl_usu_pro= a.fl_usu_pro AND a.fl_programa_sp=c.fl_programa_sp AND a.fl_programa_sp=e.fl_programa_sp ";
  $Query .= "AND a.fl_usuario_sp=f.fl_alumno_sp ";
  $Query .= "AND ";
  if($no_tab==1 || $no_tab==2){
    $Query .= "EXISTS( ";
    $Query .= "SELECT 1 FROM k_entrega_semanal_sp d WHERE d.fl_alumno=a.fl_usuario_sp ";
    if($no_tab==1){
      $Query .= "AND d.fl_promedio_semana IS NULL ";
      $Query .= "AND EXISTS(SELECT 1 FROM k_entregable_sp k WHERE k.fl_entrega_semanal_sp=d.fl_entrega_semanal_sp) ";
    }
    else{
      $Query .= "AND d.fl_promedio_semana IS NOT NULL ";
    }
    $Query .= ") ";
  }
  else{
    $Query .= "NOT EXISTS ( ";
    $Query .= "SELECT 1 FROM k_entrega_semanal_sp m, k_entregable_sp n ";
    $Query .= "WHERE m.fl_entrega_semanal_sp=n.fl_entrega_semanal_sp AND m.fl_alumno=a.fl_usuario_sp ";
    $Query .= "AND m.fl_leccion_sp=c.fl_leccion_sp ";
    $Query .= ") ";
  }
  $Query .= "AND (c.fg_animacion='1' OR c.fg_ref_animacion='1' OR c.no_sketch > 0 OR c.fg_ref_sketch='1') AND b.fg_grade_tea='1' ";
  $Query .= "ORDER BY a.fl_usuario_sp ";
  $rs = EjecutaQuery($Query);
  $tot_programs = CuentaRegistros($rs);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $no_semana = $row[0];
    $ds_titulo = $row[1];
    $fg_animacion = $row[2];
    $fg_ref_animacion = $row[3];
    $no_sketch = $row[4];
    $fg_ref_sketch = $row[5];
    $nb_programa = str_texto($row[6]);
    $ds_tipo_programa = $row[7];
    $fl_leccion_sp = $row[8];
    $fl_alumno = $row[9];
    $nb_grupo = $row[10];
    $fl_programa_sp = $row[11];
    
     # Requerimientos de la leccion
    $ds_animacion = ObtenEtiqueta(1950);
    if($fg_animacion == '1')
      $ds_animacion = ObtenEtiqueta(1951);
    $ds_ref_animacion = ObtenEtiqueta(1952);
    if($fg_ref_animacion == '1')
      $ds_ref_animacion = ObtenEtiqueta(1953);
    if($no_sketch == '0')
      $ds_sketch = ObtenEtiqueta(1954);
    elseif($no_sketch == '1')
      $ds_sketch = ObtenEtiqueta(1955);
    else
      $ds_sketch = "$no_sketch ".ObtenEtiqueta(1956);
    $ds_ref_sketch = ObtenEtiqueta(1957);
    if($fg_ref_sketch == '1')
      $ds_ref_sketch = ObtenEtiqueta(1958);
    
    # Presenta registros para el tab seleccionado
    //if($no_tab == 1 OR $no_tab == 2)
    //  require("sa_pending.inc.php");
    //if( $no_tab == 3 ) 
    //  require("sa_p_submission.inc.php");

  }*/
  
  if($no_tab==1)
      require('view_assigment_grade.php');
  if($no_tab==2)
      require('view_history_grade.php');
  if($no_tab==3)
      require('view_incomplete_grade.php');
   if($no_tab == 4 ) 
      require("sa_transcript.inc.php");
  # Si no tiene asigando algun curso muestra mensajes
  /*if(($tot_programs==0)&&($no_tab<>4)){
    switch($no_tab) {
      case 1: $no_hay = ObtenEtiqueta(1959); break;
      case 2: $no_hay = ObtenEtiqueta(1960); break;
      case 3: $no_hay = ObtenEtiqueta(1961); break;
	 
    }
  	echo "
    <div class='row padding-10'>
      <div  class='col col-sm-12 col-lg-12 col-xs-12 padding-10 text-center' style='font-size:16px; font-weight:600;'>
        {$no_hay}
      </div>
    </div>";
  }*/
?>