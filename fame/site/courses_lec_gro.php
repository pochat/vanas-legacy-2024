<?php
  # Libreria de funciones
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_programa_sp = RecibeParametroNumerico('fl_programa');
  # Si es 2 muestra las categorias
  # Si es 3 muestra Course outline
  $type = RecibeParametroHTML('type');
  # Obtenemos los grupos
  if($type=="G"){
    $append1 = "";
    $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
    $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
    $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." ";
    $Queryg .= "AND b.fl_programa_sp=".$fl_programa_sp." AND nb_grupo<>'' GROUP BY c.nb_grupo ";
    $rsg = EjecutaQuery($Queryg);                      
    $tot_groups = CuentaRegistros($rsg);
    if(!empty($tot_groups)){
    for($m=0;$rowm=RecuperaRegistro($rsg);$m){
    $nb_grupo = str_texto($rowm[0]);
    # Obtenemos alumnos de este programa en este curso
    $Queryj = "SELECT fl_alumno_sp, ds_nombres, nb_grupo, nb_programa, fg_activo, fe_ultacc, ds_progreso, no_promedio_t FROM ( ";
    $Queryj .= "(SELECT a.fl_alumno_sp, CONCAT(ds_nombres,' ', ds_apaterno) ds_nombres, a.nb_grupo, d.nb_programa, c.fg_activo, ";
    $Queryj .= "DATE_FORMAT(fe_ultacc, '%Y-%m-%d %H:%i:%s') fe_ultacc, b.ds_progreso, b.no_promedio_t ";
    $Queryj .= "FROM c_alumno_sp a LEFT JOIN c_usuario c ON(c.fl_usuario=a.fl_alumno_sp) ";
    $Queryj .= "LEFT JOIN k_usuario_programa b ON(b.fl_usuario_sp=a.fl_alumno_sp) LEFT JOIN c_programa_sp d ON(d.fl_programa_sp=b.fl_programa_sp) ";
    $Queryj .= "WHERE nb_grupo='".$nb_grupo."' AND b.fl_programa_sp=".$fl_programa_sp." AND c.fl_instituto=".$fl_instituto." ) UNION ";
    $Queryj .= "(SELECT a.fl_alumno_sp, CONCAT(ds_nombres,' ', ds_apaterno) ds_nombres, 'Unassigned' nb_grupo, 'Unassigned' nb_programa, c.fg_activo, ";
    $Queryj .= "DATE_FORMAT(fe_ultacc, '%Y-%m-%d %H:%i:%s') fe_ultacc, '0' ds_progreso, '0' no_promedio_t ";
    $Queryj .= "FROM c_alumno_sp a LEFT JOIN c_usuario c ON(c.fl_usuario=a.fl_alumno_sp) ";
    $Queryj .= "WHERE NOT EXISTS (SELECT 1 FROM k_usuario_programa d WHERE d.fl_usuario_sp= c.fl_usuario) AND a.nb_grupo='' AND c.fl_instituto=".$fl_instituto." ) ";
    $Queryj .= ") as students ";                            
    $rsj = EjecutaQuery($Queryj);
    $rsx = EjecutaQuery($Queryj);
    $tot_alumnos_grupo = CuentaRegistros($rsj);
    $no_unassigned=0;
    $no_assigned=0;
    for($x=0;$rowx=RecuperaRegistro($rsx);$x++){
      $asi_course = $rowx[2];
      $asi_group = $rowx[3];
      if($asi_course=="Unassigned" && $asi_group=="Unassigned")
        $no_unassigned++;
      else
        $no_assigned++;
    }                      
    $append .= "
    <div class='panel panel-default'>
      <div class='panel-heading'>
        <h4 class='panel-title'>
          <a data-toggle='collapse' data-parent='#accordion' href='#".$nb_grupo."_".$fl_programa_sp_i."' aria-expanded='false' class='collapsed'>
            <i class='fa fa-lg fa-fw fa-plus-circle txt-color-green pull-left' style='padding-top:5px;'></i>
            <i class='fa fa-lg fa-fw fa-minus-circle txt-color-red pull-left'  style='padding-top:5px;'></i>
            <strong>".$nb_grupo."</strong> has ".$tot_alumnos_grupo." students ( $no_assigned assigned - $no_unassigned unassigned) 
          </a>
        </h4>
      </div>
      <div id='".$nb_grupo."_".$fl_programa_sp_i."' class='panel-collapse collapse' aria-expanded='false'>
        <div class='panel-body' style='margin: 2px;'>
          <table class='table table-bordered table-condensed padding-10' id='tbl_usergupo' >
            <thead>
              <tr>
                <th></th>
                <th>".ObtenEtiqueta(1054)."</th>
                <th>".ObtenEtiqueta(1055)."</th>
                <th>".ObtenEtiqueta(1075)."</th>
                <th>".ObtenEtiqueta(1217)."</th>
                <th>".ObtenEtiqueta(1057)."</th>
                <th>".ObtenEtiqueta(1150)."</th>
                <th>".ObtenEtiqueta(1077)."</th>
                <th>".ObtenEtiqueta(1078)."</th>
              </tr>
            </thead>
            <tbody>";                            
        for($j=0;$rowj=RecuperaRegistro($rsj);$j++){
          $tot_alumnos_grupo = $tot_alumnos_grupo;
          $fl_alumno_sp = $rowj[0];                                
          $ds_nombres = str_texto($rowj[1]);
          $nb_grupo_p = str_texto($rowj[2]);
          $nb_programa_p = str_texto($rowj[3]);
          $fg_status = $rowj[4];
          if(!empty($fg_status)){
            $status = ObtenEtiqueta(1041);
            $color_sts = "success";
          }
          else{
            $status = ObtenEtiqueta(1042);
            $color_sts = "danger";
          }
          $fe_ultacc = $rowj[5];
          $fe_ultacc = time_elapsed_string($fe_ultacc, true);
          $ds_progreso = $rowj[6];
          $no_promedio_t = $rowj[7];
          # Obtenemos el GPA
          $Queryp = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)";
          $prom_t = RecuperaValor($Queryp);
          $cl_calificacion = $prom_t[0];
          $fg_aprbado_grl = $prom_t[1];
          if(!empty($fg_aprbado_grl))
            $color_gpa = "success";
          else
            $color_gpa = "danger";
          if(!empty($ds_progreso))
            $gpa = "<span class='label label-".$GPA."' style='padding: 3px;'>".$cl_calificacion." (".$no_promedio_t."%)</span>";
          else
            $gpa = ObtenEtiqueta(1039);
          $append .= "
          <tr>
            <td>";
            if($nb_grupo_p!='Unassigned' && $nb_programa!='Unassigned'){
              $append .= "
              <label class='checkbox no-padding no-margin'>
                <input class='checkbox' disabled id='ch_".$fl_alumno_sp."' value='".$fl_alumno_sp."' type='checkbox'><span style='left:20px;'></span>
              </label>";
            }
            else{
               $append .= "
              <label class='checkbox no-padding no-margin'>
                <input class='checkbox' id='ch_".$fl_alumno_sp."' value='".$fl_alumno_sp."' type='checkbox' 
                onchange=\"Assign_Grp_Crs($fl_alumno_sp, $fl_programa_sp_i, '$nb_grupo');\">
                <span style='left:20px;'></span>
              </label>";
            }
          $append .= "
            </td>
            <td>
              <div class='project-members'>                                      
                <a href='javascript:void(0)' rel='tooltip' data-placement='top' data-html='true' data-original-title='".$ds_nombres."'>
                  <img src='".ObtenAvatarUsuario($fl_alumno_sp)."' class='online' alt='".$ds_nombres."' style='width:25px;'>
                </a>
              </div>
            </td>
            <td>".$ds_nombres."</td>
            <td>".$nb_grupo_p."</td>
            <td>".$nb_programa_p."</td>
            <td  class='text-align-center'><span class='label label-".$color_sts."' style='padding: 3px;'>".$status."</span></td>
            <td>".$fe_ultacc."</td>
            <td>
              <div class='progress progress-xs' data-progressbar-value='".$ds_progreso."'><div class='progress-bar'></div></div>
            </td>
            <td>".$gpa."</td>
          </tr>";
          
        }
    $append .= "  </tbody>
          </table>
        </div>
      </div>
    </div>";
  }
    $append1 .= $append ;
  }
  else{
    $append1 = "
    <div class='row padding-10  bg-color-white' style='font-size:5px;'>
      <h4 class='h4'><i class='fa fa-warning txt-color-red'></i> <strong>".ObtenEtiqueta(2066)."</strong></h4>
		</div>";
  }
  $result["result"] = array(
  "append" => $append1,
  "tot_groups" => $tot_groups
  );
  }
  else{
    $append = 
    "<table class='table table-bordered table-condensed'>
      <thead>
        <tr>
          <th><center>".ObtenEtiqueta(1230)."</center></th>
          <th>".ObtenEtiqueta(1234)."</th>
          <th>".ObtenEtiqueta(1297)."</th>
          <th><center>".ObtenEtiqueta(1219)."</center></th>
          <th><center>".ObtenEtiqueta(1252)."</center></th>
        </tr>
      </thead>
      <tbody>";
      # Query para las lecciones de cada programa
      $Query_l  = " SELECT fl_leccion_sp, no_semana, ds_titulo, a.ds_vl_duracion, a.ds_tiempo_tarea,a.ds_learning ";
      $Query_l .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c ";
      $Query_l .= " WHERE a.fl_programa_sp = $fl_programa_sp AND a.fl_programa_sp = b.fl_programa_sp  AND a.fl_programa_sp = c.fl_programa_sp ";
      $Query_l .= " ORDER BY no_orden, no_semana ";
      $rs_l = EjecutaQuery($Query_l);
      for($i_l=0;$row_l=RecuperaRegistro($rs_l);$i_l++) {
        $fl_leccion_sp = $row_l[0];
        $no_semana = $row_l[1];
        $ds_titulo = str_texto($row_l[2]); 
        $ds_vl_duracion = $row_l[3];
        $ds_tiempo_tarea = str_texto($row_l[4]); 
        $ds_learning_2 = str_texto($row_l[5]); 
        $append .= "<tr>
          <td><center>{$no_semana}</center></td>
          <td>{$ds_titulo}</td>
          <td>{$ds_learning_2}</td>
          <td><center>{$ds_vl_duracion} ".ObtenEtiqueta(1240)."</center></td>
          <td><center>{$ds_tiempo_tarea}</center></td>
        </tr>";
      }
    $append .= "
      </tbody>
    </table>";
  $result["result"] = array(
  "append" => $append
  );
  }
  
	echo json_encode((Object) $result);
?>