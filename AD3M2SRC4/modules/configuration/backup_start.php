<?php
  # Este es el script para borrar los archivos que se han entregado
  # estos archivos deben haber sido entregado de la fecha actual a 7 meses atras
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require SP_HOME.'/lib/zipfile.php';
  
  # Recibe parametros
  $fl_backup = RecibeParametroNumerico('fl_backup');
  
  # alumnos fisica
  $url_vanas = SP_HOME."/backups";
  $url_students = $_SERVER['DOCUMENT_ROOT'].PATH_ALU;
  # url students 
  $students = "modules/students/";
  # Ruta donde se guardar los zip
  $urlsave_zip  = SP_HOME."/backups/";
  
  # Instancia de la libreria
  $zipTest = new zipfile();
  
  # Mese que se van a respaldar
  $fecha_actual = date('Y-m-d');
  $months_back = ObtenConfiguracion(75); 
  $fe_fin_back1 = strtotime ( '-'.$months_back.' month' , strtotime ($fecha_actual)) ;
  $fe_fin_back = date ( 'Y-m-d' , $fe_fin_back1 );
  
  
  # Buscamos los registros en la BD  que han sudido los alumnos 
  $Query  = "SELECT a.fl_entrega_semanal,b.fl_entregable,c.nb_archivo,a.fe_entregado,b.fe_entregado,fe_post, a.ds_critica_animacion ";
  $Query .= "FROM k_entrega_semanal a, k_entregable b, k_gallery_post c ";
  $Query .= "WHERE a.fl_entrega_semanal = b.fl_entrega_semanal AND b.fl_entregable = c.fl_entregable ";
  $Query .= "AND a.fe_entregado <'".$fecha_actual."' AND a.fe_entregado > '".$fe_fin_back."' ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  # Verificamos que no hay un registros con la fecha actual
  $row_dias = RecuperaValor("SELECT COUNT(1), DATE_FORMAT(fe_creado, '%H:%i:%s'), ds_archivo FROM c_backups WHERE DATE_FORMAT(fe_creado,'%Y-%m-%d')='".$fecha_actual."' AND fg_tipo='".$fl_backup."' GROUP BY fe_creado");
  $backup_day = $row_dias[0];
  $fe_creado_backup = $row_dias[1];
  $ds_archivo_bd = $row_dias[2];
  
  # Valida
  # No hay registros
  if(empty($registros))
    $ds_error = ObtenEtiqueta(857);
  # El dia de hoy se realizo in backup
  if(!empty($backup_day))
    $ds_error = ObtenEtiqueta(858).': '.$ds_archivo_bd.' '.$fe_creado_backup;
  
  # Si hay erro no creara nada y enviara mensage
  if(!empty($ds_error)){
    echo "
    <html><body><form name='datos' method='post' action='backups.php'>";
    Forma_CampoOculto('ds_error',$ds_error);  
    echo "  
    </form>
      <script>
        document.datos.submit();
      </script></body>
    </html>";
    exit;
  }
  
  # Si no hay registros no genera el zip
  if(!empty($registros) AND empty($row_dias[0])){
    if($fl_backup==1){
      for($i=0;$row=RecuperaRegistro($rs);$i++){
      $fl_gallery_post = $row[0];
      $fl_entregable = $row[1];
      $nb_archivo = $row[2];
      
      # Buscamos las rutas dependiendo de la extencion del archivos
      # creamos las carpetas de los archivos 
      $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
      switch($ext){
        case "jpg";
        case "jpeg":
          $sketches = $url_students."/sketches";
          $original = $sketches."/original";
          $regular = $sketches."/regular";
          $thumbs = $sketches."/thumbs";
          $board_thumbs = $sketches."/board_thumbs";
        break;
        default: $videos = $url_students."/videos";
        
      }
            
      # Si los archivos existen en la carpeta se comprimen y poesteriormente lo eliminar
      if($ext=='jpg' OR $ext=='jpeg'){
        /*if(file_exists($sketches."/".$nb_archivo)){
          $zipTest->add_file("$sketches/$nb_archivo", $students."sketches/$nb_archivo"); 
        }*/
        
        if(file_exists($original."/".$nb_archivo)){
          $zipTest->add_file("$original/$nb_archivo", $students."sketches/original/$nb_archivo");
        }      
        /*if(file_exists($regular."/".$nb_archivo)){
          $zipTest->add_file("$regular/$nb_archivo", $students."sketches/regular/$nb_archivo");
        }
        
        if(file_exists($thumbs."/".$nb_archivo)){
          $zipTest->add_file("$thumbs/$nb_archivo", $students."sketches/thumbs/$nb_archivo");
        }
        
        if(file_exists($board_thumbs."/".$nb_archivo)){
          $zipTest->add_file("$board_thumbs/$nb_archivo", $students."sketches/board_thumbs/$nb_archivo");         
        }*/
      }
      else{
        //videos
        if(file_exists($videos."/".$nb_archivo)){
          $zipTest->add_file("$videos/$nb_archivo", $students."videos/$nb_archivo");         
        }
      }      
    }
    }
    /* Se comenta por si en algun momento se generan  quieren guardar las critiques
    # Si tiene critique esta tambien se va respaldar
    $Query2 = $Query." GROUP BY a.fl_entrega_semanal ";
    $rs2 = EjecutaQuery($Query2);
    for($j=0;$row2=RecuperaRegistro($rs2);$j++){
      $ds_critica_animacion = $row2[6];
      $ds_critica_animacion_cam1 = substr($ds_critica_animacion, 0, -4); // Trae la primera parte
      $ds_critica_animacion_cam2 = substr($ds_critica_animacion, -4); // trae la segunda parte
      $ds_critica_animacion_cam = $ds_critica_animacion_cam1."_cam".$ds_critica_animacion_cam2;            
      
      $ruta_critique = $url_students."/critiques";
      // critica cam
      if(!empty($ds_critica_animacion_cam1) AND !empty($ds_critica_animacion_cam2)){ 
        if(file_exists($ruta_critique."/".$ds_critica_animacion)){
          $zipTest->add_file("$ruta_critique/$ds_critica_animacion_cam", $students."critiques/$ds_critica_animacion_cam");           
        }
      }
      // critica
      if(!empty($ds_critica_animacion)){
        if(file_exists($ruta_critique."/".$ds_critica_animacion)){
          $zipTest->add_file("$ruta_critique/$ds_critica_animacion", $students."critiques/$ds_critica_animacion");           
        }
      }
    }*/
  
    # Respaldamos la BD que se genera del diario
    if($fl_backup==1 OR $fl_backup==2){
      if(file_exists($url_vanas."/backup_db.sql.gz")){
        $zipTest->add_file($url_vanas."/backup_db.sql.gz", "backups/backup_db.sql.gz");  
      }
    }
    
    # Todos los archivos que se generaron con su respectiva carpeta se comprimiran   
    $ds_archivo = date ( 'Ymd' , $fe_fin_back1 )."-".date('Ymd').".zip";
    if($fl_backup==2)
      $ds_archivo = "BD_".$ds_archivo;

    # Esta Ruta hay que cambiarl y preguntarle a Marc donde se guardaran estos backups
    $fd = fopen ($urlsave_zip.$ds_archivo, "wb"); 
    $out = fwrite ($fd, $zipTest -> file()); 
    fclose ($fd);    
    
    # Tama?o del archivo
    $ds_filesize = filesize($urlsave_zip.$ds_archivo);    
    $clase = array(" Bytes", " KB", " MB", " GB", " TB"); 
    $ds_filesize = round($ds_filesize/pow(1024,($i = floor(log($ds_filesize, 1024)))),2 ).$clase[$i];
        
    # Insertamos el registro del zip que se crea. 
    if(file_exists($urlsave_zip.$ds_archivo))
      EjecutaQuery("INSERT INTO c_backups(ds_archivo,fe_ini_back,fe_fin_back, fg_tipo, ds_size, fe_creado) VALUES('$ds_archivo','$fe_fin_back','".$fecha_actual."', '".$fl_backup."', '".$ds_filesize."', NOW())");       
  }
  # Regresa al listado
  header("Location: backups.php");
?>