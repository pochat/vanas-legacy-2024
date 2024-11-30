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
  $fl_instituto=ObtenInstituto($fl_usuario);
  # Recibe parametros
  $fl_pais=RecibeParametroNumerico('fl_pais');
  $fl_estado=RecibeParametroNumerico('fl_estado');
  $cl_course_code=RecibeParametroHTML('cl_course_code');
  $nb_course_code=RecibeParametroHTML('nb_course_code');
  $ds_level=RecibeParametroHTML('ds_level');
  $ds_descripcion=RecibeParametroHTML('ds_descripcion');
  $ds_prerequisito=RecibeParametroHTML('ds_prerequisito');
  $clave=RecibeParametroNumerico('clave');
 
 
  if(empty($fl_estado))
   $fl_estado="NULL";
   
  # Inserta o actualiza el registro
  if(empty($clave)) {

      $Query="INSERT INTO c_course_code (fl_pais,fl_estado,nb_course_code,cl_course_code,ds_level,ds_descripcion,ds_prerequisito,fl_instituto,fl_usuario_creacion,fe_creacion)";
      $Query.="VALUES($fl_pais,$fl_estado,'$nb_course_code','$cl_course_code','$ds_level','$ds_descripcion','$ds_prerequisito',$fl_instituto,$fl_usuario,CURRENT_TIMESTAMP)";
      $fl_course_code= EjecutaInsert($Query);

      echo json_encode((Object)array(
        'fg_correcto' => 1,
        'clave'=>$fl_course_code
      ));
        
  }else{
  
      
      $Query="UPDATE c_course_code SET fl_pais=$fl_pais,fl_estado=$fl_estado,  nb_course_code='$nb_course_code',cl_course_code='$cl_course_code',ds_level='$ds_level',ds_descripcion='$ds_descripcion',ds_prerequisito='$ds_prerequisito',fl_usuario_creacion=$fl_usuario   WHERE fl_course_code=$clave and fl_instituto=$fl_instituto ";
      EjecutaQuery($Query);
  
      echo json_encode((Object)array(
       'fg_correcto' => 1,
       'clave'=>$clave
     ));
      
  }
 
 

?>