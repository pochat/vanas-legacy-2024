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

  $sufix = langSufix();
  
//  $langselect = $_COOKIE[IDIOMA_NOMBRE];
//
//    switch ($langselect) {
//      case '1': $sufix = '_esp';
//        break;
//
//      case '2': $sufix = '';
//        break;
//
//      case '3': $sufix = '_fra';
//        break;
//
//      default: $sufix = '';
//        break;
//    }

  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

 
  # Consulta para el listado
  $Query="SELECT fl_criterio, nb_criterio".$sufix." ,fl_usuario_creacion   
            FROM c_criterio
          WHERE fl_instituto=$fl_instituto ";
  if($fl_perfil_sp==PFL_MAESTRO_SELF)
  $Query.=" AND fl_usuario_creacion=$fl_usuario ";
  $Query .=" ORDER BY fl_criterio DESC ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
      $fl_criterio=$row['fl_criterio'];
      $nb_criterio=$row['nb_criterio'.$sufix.''];
      $fl_usuario_creacion=$row['fl_usuario_creacion'];

      $nombre_creador=ObtenNombreUsuario($fl_usuario_creacion);

    echo '
    {
	';	
				
	echo'	
      "nb_criterio": "<div class=\'project-members\'><a href=\'index.php#site/criterion_details.php?clave='.$fl_criterio.'\' >'.str_texto($nb_criterio).'<br> <small class=\'text-muted\'>'.ObtenEtiqueta(2626).': '.$nombre_creador.'</small> </div> ",
      "programas":" ",
	';
	
		 $Query2="SELECT fl_calificacion_criterio,no_min,no_max,cl_calificacion,ds_calificacion FROM c_calificacion_criterio WHERE fl_instituto=$fl_instituto ORDER BY no_equivalencia ASC ";
		 $rm=EjecutaQuery($Query2);
		 $registros2 = CuentaRegistros($rm);
		 
		 for($m=1;$rowm=RecuperaRegistro($rm);$m++){
				$fl_calificacion_criterio=$rowm['fl_calificacion_criterio'];
				$no_min=$rowm['no_min'];
				$no_max=$rowm['no_max'];
				$cl_calificacion=$rowm['cl_calificacion'];
				$ds_calificacion=$rowm['ds_calificacion'];
				$calificacion=$no_min.'-'.$no_max.'% ('.$cl_calificacion.')';
				
				#Recuperamos la descripcion.
				$Query="SELECT ds_descripcion".$sufix." FROM k_criterio_fame WHERE fl_calificacion_criterio=$fl_calificacion_criterio AND fl_criterio=$fl_criterio  ";
				$row=RecuperaValor($Query);				
                $ds_descripcion=!empty($row['ds_descripcion'])?$row['ds_descripcion']:NULL;;




            echo'
				 "data_'.$m.'": " '.$ds_calificacion.'<br><small class=\'text-muted\'><i>'.$ds_descripcion.'</i></small> ",
	  
			';  
		 }
		
		 
		 
	echo'
      "delete": "<a href=\'javascript:Delete('.$fl_criterio.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a> "
      
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>
  ]
}