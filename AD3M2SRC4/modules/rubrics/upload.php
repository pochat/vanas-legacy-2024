<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  //$ord = $_REQUEST['ord'];
  //$no_tab = $_REQUEST['no_tab'];
  //$clave = $_REQUEST['clave'];
  //$editar = $_REQUEST['editar'];
  $clave=  $_POST['fl_criterio_fame'];
  $ds = DIRECTORY_SEPARATOR;  //1
  $storeFolder = 'uploads';   //2
  $directorio_images='images'.$ds.'rubrics'.$ds;
  $fg_creado_instituto=$_POST['fg_creado_instituto'];

  $fl_criterio=$_POST['fl_criterio'];
  $fl_calificacion_criterio=$_POST['fl_calificacion_criterio'];


  if($fg_creado_instituto==1){
      
      if(empty($clave)){

          #Verificamos si existe y si no la crea.
          $Query="SELECT count(*) FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=$fl_calificacion_criterio  ";
          $row=RecuperaValor($Query);
          if(empty($row[0])){
              
              $Query="INSERT INTO k_criterio_fame(fl_criterio,fl_calificacion_criterio)VALUES($fl_criterio,$fl_calificacion_criterio)";
              $clave=EjecutaInsert($Query);
          }

      }


  }



  
  if (!empty($_FILES)) {
      
             
                  $tempFile = $_FILES['file']['tmp_name'];          //3             
                  $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
                  $targetPath=PATH_ADM_HOME.$ds.$directorio_images;
                  $targetFile =  $targetPath. $_FILES['file']['name'];  //5
   
                 #verificamos que no exista el fichero.
                 
                  $exists=file_exists($targetPath.$ds.$_FILES['file']['name']);
          
                  if($exists){
                      

                             $success = false;
                  }else{
                  
                      
                             move_uploaded_file($tempFile,$targetFile); //6
                      
                             #Obteemos la extension del archivo
                             $ext=ObtenExtensionArchivo($_FILES['file']['name']);
                      
                             #Descomponemos y obtenemosel nombre del archivo
                             list($nombre_img, $ext) = explode(".", $_FILES['file']['name']);  
                      

                              if(empty($clave)){
                                  
                                  #Inserta el criterio FAME.
                                  $Query="INSERT INTO k_criterio_fame()";


                                  $Query  = "INSERT INTO c_archivo_criterio (nb_archivo) ";
                                  $Query .= " VALUES ('".$_FILES['file']['name']."')";
                                  $fl_archivo=EjecutaInsert($Query);
                          
                          
                              }else{#viene una clave asignada
                          
                                  #eLIMINAMOS EL CRITERIO
                                  $Query="DELETE FROM c_archivo_criterio WHERE fl_criterio_fame=$clave ";
                                  EjecutaQuery($Query);
                          
                          
                                  $Query  = "INSERT INTO c_archivo_criterio (fl_criterio_fame,nb_archivo) ";
                                  $Query .= " VALUES ($clave,'".$_FILES['file']['name']."')";
                                  $fl_archivo=EjecutaInsert($Query); 
                          
                          
                              }
                              
                              
                              #Generamos el nuevo nombre y renombramos
                              $nb_nuevo_imagen =$nombre_img."_".$fl_archivo;
                              $nombre_nuevo_img = "$nb_nuevo_imagen"."."."$ext";
                              
                              #Renombramos la imagen
                              rename($targetPath.$ds.$_FILES['file']['name'],$targetPath.$ds.$nombre_nuevo_img);
                              
                              #Actualizamo la base de datos
                              $Query="UPDATE c_archivo_criterio SET nb_archivo='$nombre_nuevo_img' WHERE fl_archivo_criterio=$fl_archivo ";
                              EjecutaQuery($Query);
                      
                              $success = true;
                  
                  }
                  
                  
                
                  
      
  }else{
      $success = false;
  
  }
  
  
  
  
  
  
  $result['valores'] =
   array(
  "status" => $success,
  "fl_criterio_fame"=>$clave,
  "nb_archivo"=>$nombre_nuevo_img
 
  );
  echo json_encode((Object) $result);
  
  
?>