<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  $ord = $_REQUEST['ord'];
  $no_tab = $_REQUEST['no_tab'];
  $clave = $_REQUEST['clave'];
  $editar = $_REQUEST['editar'];
  
  $ds = DIRECTORY_SEPARATOR;  //1
  $storeFolder = 'uploads';   //2
 
  if (!empty($_FILES)) {
      $tempFile = $_FILES['file']['tmp_name'];          //3             
      $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
      # Agreamos un random al nombre del archivo para diferencialos
      $name_original = $_FILES['file']['name']; 
      $ext = ObtenExtensionArchivo($name_original);
      $name_new = "quiz_".rand(5000,1000)."_id".$clave.".".$ext;
      $targetFile =  $targetPath.$name_new;  //5
      
      # Movemos el archivo
      $move = move_uploaded_file($tempFile,$targetFile); //6
    
      if(empty($editar)){
        $Query  = "INSERT INTO k_quiz_respuesta (no_tab, no_orden, ds_respuesta) ";
        $Query .= " VALUES ($no_tab, $ord, '".$name_new."')";
        EjecutaQuery($Query);
      }else{
        $Query = "UPDATE k_quiz_respuesta SET ds_respuesta = '".$name_new."' WHERE no_tab = '$no_tab' AND no_orden = '$ord' AND fl_quiz_pregunta = '$clave'";
        EjecutaQuery($Query);
      }
      $success = true;
  }
  else{
    $success = false;
    $move = "error";
  }
  
  $result['valores'] = 
  array(
  "status" => $success,
  "query" => $Query,
  "tempFile" => $tempFile,
  "targetPath" => $targetPath,
  "targetFile" => $targetFile,
  "move_file" => $move,
  "file_name" => $name_new
  );
  echo json_encode((Object) $result);
?>