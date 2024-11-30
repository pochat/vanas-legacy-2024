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

  # ATTENTION Variable does not exist, initialized to avoid errors
  $fg_aprobado=NULL;

  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
 
 # Consulta para el listado
  $Query  = " SELECT fl_course_code,cl_course_code,nb_course_code,ds_level,ds_descripcion,ds_prerequisito,P.ds_pais,E.ds_provincia,fl_usuario_creacion 
			  FROM  c_course_code C
			  JOIN c_pais P ON P.fl_pais=C.fl_pais
			  LEFT JOIN k_provincias E ON E.fl_provincia=C.fl_estado
			  WHERE fl_instituto=$fl_instituto ";
  if($fl_perfil_sp==PFL_MAESTRO_SELF)
  $Query .="AND fl_usuario_creacion=$fl_usuario ";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [

  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
    	$fl_course_code=$row['fl_course_code'];
            $cl_course_code=str_texto($row['cl_course_code']);
			$nb_course_code=str_texto($row['nb_course_code']);	
            $ds_level=str_texto($row['ds_level']);
            $ds_descripcion=str_texto($row['ds_descripcion']);
            $ds_prerequisito=str_texto($row['ds_prerequisito']);
            $ds_pais=str_texto($row['ds_pais']);
			$nb_estado=str_texto($row['ds_provincia']);
            $fl_usuario_creacion=$row['fl_usuario_creacion'];

            $nombre_creador=ObtenNombreUsuario($fl_usuario_creacion);
   
      
      if($fg_aprobado=='1') {
        $color = "success";
        $etq=ObtenEtiqueta(16);
      }else{
        $color ="danger";
        $etq=ObtenEtiqueta(17);
      }
      
	  
	  #Recupermoas el level
	  $Query="SELECT nb_grado,fl_grado FROM k_grado_fame a 
			 JOIN c_clasificacion_grado b ON a.cl_clasificacion_grado=b.cl_clasificacion_grado WHERE a.fl_grado=$ds_level ";
	  $ro=RecuperaValor($Query);
	  $ds_level=$ro[0];
      
    echo '
        {
          "checkbox": "<!--<div class=\'checkbox \'><label><input class=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$row[0].'\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>-->",
          "programas":"",
		  "cl_clave": "<a href=\'index.php#site/curriculum_mapping_frm.php?c='.$fl_course_code.'\'>'.$cl_course_code.'</a><br> <small class=\'text-muted\'>'.ObtenEtiqueta(2626).': '.$nombre_creador.'</small>",
          "name": "<td>'.$nb_course_code.'</td>",           
          "level": "<td>'.$ds_level.'</td>",          
          "pais": "<td>'.$ds_pais.'</a><br><small class=\'text-muted\'>'.$nb_estado.'</small></td>", 
          "descrip": "<td>'.$ds_descripcion.'</td>",
          "prerequisito": "<td>'.$ds_prerequisito.'</td>",                 
          "delete": "<a href=\'javascript:Delete('.$fl_course_code.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>"
 
        }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>

  ]
}