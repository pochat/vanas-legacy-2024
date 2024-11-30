<?php   
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
 
 
  # Consulta para el listado
  # Consulta para el listado  
  $Query  = " SELECT fl_calificacion,cl_calificacion,ds_calificacion,ds_calificacion_esp,ds_calificacion_fra,fg_aprobado,no_equivalencia,no_min,no_max ";
  $Query .= "FROM c_calificacion_sp WHERE fl_instituto=$fl_instituto ";
  $rs = EjecutaQuery($Query);	
  // echo $Query;
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [

  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
     $fl_calificacion=$row['fl_calificacion'];
	 $cl_calificacion=$row['cl_calificacion'];
	 $ds_calificacion=$row['ds_calificacion'];
	 $ds_calificacion_esp=$row['ds_calificacion_esp'];
	 $ds_calificacion_fra=$row['ds_calificacion_fra'];
	 $fg_aprobado=$row['fg_aprobado'];
	 $no_equivalencia=$row['no_equivalencia'];
	 $no_min=$row['no_min'];
	 $no_max=$row['no_max'];
   
   
      
      if($fg_aprobado=='1') {
        $color = "success";
        $etq=ObtenEtiqueta(16);
      }else{
        $color ="danger";
        $etq=ObtenEtiqueta(17);
      }
      
      
    echo '
        {
          "checkbox": "<!--<div class=\'checkbox \'><label><input class=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$row[0].'\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>-->",
          "grade": " '.str_texto($cl_calificacion).' ",
          "description": "<td>'.$ds_calificacion.'</td>",           
          "aproving": "<td><span class=\'label label-'.$color.'\'>'.$etq.'</span> </td>",          
          "no_min": "<td>'.$no_min.'</td>", 
          "no_max": "<td>'.$no_max.'</td>",
          "equivalence": "<td>'.$no_equivalencia.'</td>",                 
          "delete": "<a href=\'javascript:Borra(\"grading_del.php\",'.$fl_calificacion.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>"
 
        }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>

  ]
}