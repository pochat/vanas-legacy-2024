<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_COURSESCODE, $permiso)) {
   MuestraPaginaError(ERR_SIN_PERMISO);
   exit;
  }
  
  # Recibe parametros
  $fl_pais=RecibeParametroNumerico('fl_pais');
  $fl_estado=RecibeParametroNumerico('fl_estado');
  $cl_course_code=RecibeParametroHTML('cl_course_code');
  $nb_course_code=RecibeParametroHTML('nb_course_code');
  $ds_level=RecibeParametroHTML('ds_level');
  $ds_descripcion=RecibeParametroHTML('ds_descripcion');
  $ds_prerequisito=RecibeParametroHTML('ds_prerequisito');
  
  
   # Valida las prefencias
  if(empty($ds_course_code))
    $ds_course_code_err = ERR_REQUERIDO;
  
  
  
  
 if(empty($clave)){
 
	 #Verifica si hay un codigo existente
	 $Query="SELECT COUNT(*) FROM c_course_code WHERE ds_course_code='$ds_course_code' ";
	 $row=RecuperaValor($Query);
	 
	 if($row[0]>0){
	 
	 
	 
	 }
	 
 
 }
 
 
  if(empty($fl_estado))
   $fl_estado="NULL";
 
  
  # Inserta o actualiza el registro
  if(empty($clave)) {

      $Query="INSERT INTO c_course_code (fl_pais,fl_estado,nb_course_code,cl_course_code,ds_level,ds_descripcion,ds_prerequisito)";
      $Query.="VALUES($fl_pais,$fl_estado,'$nb_course_code','$cl_course_code','$ds_level','$ds_descripcion','$ds_prerequisito')";
      $fl_course_code= EjecutaInsert($Query);

  
        
  }else{
  
      
      $Query="UPDATE c_course_code SET fl_pais=$fl_pais,fl_estado=$fl_estado,  nb_course_code='$nb_course_code',cl_course_code='$cl_course_code',ds_level='$ds_level',ds_descripcion='$ds_descripcion',ds_prerequisito='$ds_prerequisito'   WHERE fl_course_code=$clave  ";
      EjecutaQuery($Query);
  
  
  }
 
  
  

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
?>