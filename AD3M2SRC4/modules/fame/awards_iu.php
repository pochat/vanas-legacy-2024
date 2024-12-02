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
  if(!ValidaPermiso(FUNC_FREE_TRIAL, $permiso)) {
   MuestraPaginaError(ERR_SIN_PERMISO);
   exit;
  }
  
  # Recibe parametros
  $ds_titulo=RecibeParametroHTML('ds_titulo');
  $ck1=RecibeParametroBinario('ck1');
  $ck2=RecibeParametroBinario('ck2');
  $ck3=RecibeParametroBinario('ck3');
  $fl_perfil=RecibeParametroNumerico('fl_perfil');

  $ruta_img = SP_HOME."/fame/site/uploads/awards";
  $date=date("Ymd His");
  $file_name_ori=$_FILES['nb_imagen']['name'];

  # Obtenemos la extension del archivo
  $ext = ObtenExtensionArchivo($file_name_ori);     
  # Nombre para todos los archivos
  $name_ori = explode(".", $file_name_ori);
  $name_main = $name_ori[0];
  $name_main=$name_main.$date;
  $filename=$name_main.".".$ext;

  # Cambiamos los permisos de la carpeta 
  chmod($ruta_img, 0777); 

  # Verifica que el tipo de archivo para avatar sea JPG
  $ext = strtolower(ObtenExtensionArchivo($_FILES['nb_imagen']['name']));
  move_uploaded_file($_FILES['nb_imagen']['tmp_name'], "../../../fame/site/uploads/awards/".$filename); 
  
  
   
  # Inserta o actualiza el registro
  if(!empty($clave)) {

      $Query="UPDATE k_awards SET ds_titulo='$ds_titulo', fl_perfil=$fl_perfil ";
      if(!empty($_FILES['nb_imagen']['name'])){
          $Query.=",nb_imagen='$filename' ";
      }
      $Query.="WHERE fl_awards=$clave ";
      EjecutaQuery($Query);
       
  }
 
  
  

 
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
?>
