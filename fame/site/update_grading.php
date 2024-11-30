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
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  $_POST[''];
  
  $fl_calificacion=$_POST['pk'];
  $columna=$_POST['name'];
  $valor=$_POST['value'];
  




  switch ($columna) {
    case 'cl_calificacion':
        EjecutaQuery('UPDATE c_calificacion_criterio SET cl_calificacion="'.$valor.'"  WHERE fl_calificacion_criterio='.$fl_calificacion.' AND fl_instituto='.$fl_instituto.' ');
      break;

    case 'ds_calificacion':
        EjecutaQuery("UPDATE c_calificacion_criterio SET ds_calificacion='$valor' WHERE fl_calificacion_criterio=$fl_calificacion AND fl_instituto=$fl_instituto");
      break;
    case 'ds_calificacion_esp':
        EjecutaQuery("UPDATE c_calificacion_criterio SET ds_calificacion_esp='$valor' WHERE fl_calificacion_criterio=$fl_calificacion AND fl_instituto=$fl_instituto");
        break;
    case 'ds_calificacion_fra':
        EjecutaQuery("UPDATE c_calificacion_criterio SET ds_calificacion_fra='$valor' WHERE fl_calificacion_criterio=$fl_calificacion AND fl_instituto=$fl_instituto");
        break;
    case 'fg_aprobado':
        EjecutaQuery("UPDATE c_calificacion_criterio SET fg_aprobado='$valor' WHERE fl_calificacion_criterio=$fl_calificacion AND fl_instituto=$fl_instituto");
      break;
	  case 'no_equivalencia':
          EjecutaQuery("UPDATE c_calificacion_criterio SET no_equivalencia=$valor WHERE fl_calificacion_criterio=$fl_calificacion AND fl_instituto=$fl_instituto");
      break;
	  case 'no_min':
          EjecutaQuery("UPDATE c_calificacion_criterio SET no_min=$valor  WHERE fl_calificacion_criterio=$fl_calificacion AND fl_instituto=$fl_instituto");
      break;
	case 'no_max':
        EjecutaQuery("UPDATE c_calificacion_sp SET no_max=$valor WHERE fl_calificacion_criterio=$fl_calificacion AND fl_instituto=$fl_instituto ");
      break;
    default:
        EjecutaQuery("");
      break;
  }












  
  
?>
   
