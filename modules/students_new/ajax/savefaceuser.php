<?php
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  #Recibe Parametros
  $id_face_user = RecibeParametroHTML('id_face_user');
  $type = RecibeParametroHTML('type');
  $entregable = RecibeParametroNumerico('entregable');
  $name = RecibeParametroHTML('name');
  $update = RecibeParametroBinario('update');
 
 if(empty($update)){
    # Inserta los perfiles paginas y post de los usuarios
    if(!ExisteEnTabla('k_use_facebook','fl_facebook',$id_face_user, 'fl_usuario', $fl_alumno,True) AND ($type=="PF" OR $type=="PG")){
      $Query = "INSERT INTO k_use_facebook VALUES(".$fl_alumno.",".$id_face_user.", '".$name."', '".$type."')";    
    }
    else{
      $Query  = "INSERT INTO k_share(fl_share_face,fl_entregable,fl_alumno,no_share,no_visto, fe_share, fg_type) ";
      $Query .= "VALUES('".$id_face_user."',".$entregable.",".$fl_alumno.",1,0, NOW(),'F')";
    }
    EjecutaQuery($Query);  
  }
  else
    EjecutaQuery("DELETE FROM k_use_facebook WHERE fl_usuario = $fl_alumno");

?>