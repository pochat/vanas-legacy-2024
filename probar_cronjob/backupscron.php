<?php

  # Este es el script para borrar los archivos que se han entregado
  # estos archivos deben haber sido entregado de la fecha actual a 7 meses atras
  # Librerias
  require '../lib/com_func.inc.php';
  require '../lib/sp_config.inc.php';
  require '../lib/zipfile.php';
  //require '/mnt/data/home/vanas/public_html/lib/com_func.inc.php';
  //require '/mnt/data/home/vanas/public_html/lib/sp_config.inc.php';
  //require '/mnt/data/home/vanas/public_html/lib/zipfile.php';
  
  # Instancia de la libreria
  $zipTest = new zipfile(); 
  
  # Apartir de la fecha que corran el script se restaran ObtenConfiguracion(69) meses
  # Apartir de esa fecha hacia atras borrara los archivos
  # Ejemplo  ejecutan script 2015-07-06 menos 6 meses igual a 2015-01-06 apartir de esta ultima fecha hacia atras se borraran archivos y BD
  # Sempre queda vva la nformacon de los ultmos ObtenConfiguracion(69) meses
  $months_vivos = ObtenConfiguracion(69);
  $fecha_actual = date('Y-m-d');
  # Restamos a la fecha el numero de meses
  $fe_ini_back = date('Y-m-d',strtotime('-'.$months_vivos.' month '.$fecha_actual.''));
  # El numero de meses qy fecha  hasta donde se borran los archvos BD
  $months_back = ObtenConfiguracion(75); 
  $fe_fin_back = strtotime ( '-'.$months_back.' month' , strtotime ($fe_ini_back)) ;
  $fe_fin_back = date ( 'Y-m-d' , $fe_fin_back );
   
  # Buscamos los registros en la BD  que han sudido los alumnos 
  $Query  = "SELECT a.fl_entrega_semanal,b.fl_entregable,c.nb_archivo,a.fe_entregado,b.fe_entregado,fe_post FROM k_entrega_semanal a, k_entregable b, k_gallery_post c ";
  $Query .= "WHERE a.fl_entrega_semanal = b.fl_entrega_semanal AND b.fl_entregable = c.fl_entregable ";
  $Query .= "AND a.fe_entregado <'".$fe_ini_back."' AND a.fe_entregado > '".$fe_fin_back."'";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
  # Si no hay registros no genera el zip
  if(!empty($registros)){
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      $fl_gallery_post = $row[0];
      $fl_entregble = $row[1];
      $nb_archivo = $row[2];
      
      # Buscamos las rutas dependiendo de la extencion del archivos
      # creamos las carpetas de los archivos 
      $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
      switch($ext){
        case "jpg";
        case "jpeg":
          $sketches = $_SERVER['DOCUMENT_ROOT'].PATH_ALU."/sketches";
          $original = $sketches."/original";
          $regular = $sketches."/regular";
          $thumbs = $sketches."/thumbs";
          $board_thumbs = $sketches."/board_thumbs";
        break;
        default: $videos = $_SERVER['DOCUMENT_ROOT'].$videos =  PATH_ALU."/videos";
        
      }
      
      # Si los archivos existen en la carpeta se comprimen y poesteriormente lo eliminar
      if($ext=='jpg' OR $ext=='jpeg'){
        if(file_exists($sketches."/".$nb_archivo)){
          $zipTest->add_file("$sketches/$nb_archivo", "sketches/$nb_archivo"); 
          unlink($sketches."/".$nb_archivo);
        }
        
        if(file_exists($original."/".$nb_archivo)){
          $zipTest->add_file("$original/$nb_archivo", "original/$nb_archivo");
          unlink($original."/".$nb_archivo);
        }
        
        if(file_exists($regular."/".$nb_archivo)){
          $zipTest->add_file("$regular/$nb_archivo", "regular/$nb_archivo");
          unlink($regular."/".$nb_archivo);
        }
        
        if(file_exists($thumbs."/".$nb_archivo)){
          $zipTest->add_file("$thumbs/$nb_archivo", "thumbs/$nb_archivo");
          unlink($thumbs."/".$nb_archivo);
        }
        
        if(file_exists($board_thumbs."/".$nb_archivo)){
          $zipTest->add_file("$board_thumbs/$nb_archivo", "board_thumbs/$nb_archivo");
          unlink($board_thumbs."/".$nb_archivo);
        }
      }
      
      # buscar s tene crtero
      if(ExisteEnTabla('k_record_critique_audio','fl_entrega_semanal',$fl_entrega_semanal)){
        $row = RecuperaValor("SELECT nb_archivo_video FROM k_record_critique_audio WHERE fl_entrega_semanal=$fl_entrega_semanal");
        $nb_archivo_video = $row[0];
        
        //vdeos
        if(file_exists($videos."/".$nb_archivo_video) AND $ext=='ogg'){
          $zipTest->add_file("$videos/$nb_archivo_video", "videos/$nb_archivo"); 
          unlink($videos."/".$nb_archivo);
        }
      }
    }
    
    # Todos los archivos que se generaron con su respectiva carpeta se comprimiran
    
    $ds_archivo = date('Y-Md',strtotime ( '-'.$months_back.' month' , strtotime ($fe_ini_back)))."-".date('Y-Md',strtotime('-'.$months_vivos.' month '.$fecha_actual.'')).".zip";
    $fd = fopen ($_SERVER['DOCUMENT_ROOT'].PATH_ALU."/".$ds_archivo, "wb"); 
    $out = fwrite ($fd, $zipTest -> file()); 
    fclose ($fd);
    
    # Insertamos el registro del zip que se crea.
    EjecutaQuery("INSERT INTO c_backups(ds_archivo,fe_ini_back,fe_fin_back) VALUES('$ds_archivo','$fe_ini_back','$fe_fin_back')");
    
  }
  
?>
