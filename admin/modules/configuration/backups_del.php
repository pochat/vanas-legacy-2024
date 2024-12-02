<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MAESTROS, PERMISO_BAJA)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica que se haya recibido la clave
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Consutamos la tabla para obtener el nombre del archvo y descargarlo
  $row = RecuperaValor("SELECT ds_archivo,fe_ini_back,fe_fin_back FROM c_backups WHERE fl_backups=$clave");
  $ds_archivo = str_texto($row[0]);
  $fe_ini_back = $row[1];
  $fe_fin_back = $row[2];
  $ruta = "../../../modules/students/".$ds_archivo;
  # S exste el archvo borra los datos de la BD
  if(file_exists($ruta)){
    
    # Recupera los datos
    $Query  = "SELECT a.fl_entrega_semanal,b.fl_entregable,c.fl_gallery_post FROM k_entrega_semanal a, k_entregable b, k_gallery_post c ";
    $Query .= "WHERE a.fl_entrega_semanal = b.fl_entrega_semanal AND b.fl_entregable = c.fl_entregable ";
    $Query .= "AND a.fe_entregado <'".$fe_ini_back."' AND a.fe_entregado > '".$fe_fin_back."'";
    for($i=0;$row= RecuperaRegistro($rs);$i++){
      $fl_entrega_semanal = $row[0];
      $fl_entregable = $row[1];
      $fl_gallery_post = $row[2];
      
      # Eliminamos cada una de los registros que esten conectado con el fl_entrega_semanal
      if(ExisteEnTabla('k_record_critique_session','fl_entrega_semanal',$fl_entrega_semanal))
        EjecutaQuery("DELETE FROM k_record_critique_session WHERE fl_entrega_semanal=".$fl_entrega_semanal."");
      if(ExisteEnTabla('k_record_critique_audio','fl_entrega_semanal',$fl_entrega_semanal))
        EjecutaQuery("DELETE FROM k_record_critique_audio WHERE fl_entrega_semanal=".$fl_entrega_semanal."");
      if(ExisteEnTabla('k_com_entregable','fl_entrega_semanal',$fl_entrega_semanal))
        EjecutaQuery("DELETE FROM k_com_entregable WHERE fl_entrega_semanal=".$fl_entrega_semanal."");
      if(ExisteEnTabla('k_gallery_comment','fl_gallery_post',$fl_entrega_semanal))
        EjecutaQuery("DELETE FROM k_gallery_comment WHERE fl_gallery_post=".$fl_gallery_post."");
    
      EjecutaQuery("DELETE FROM k_gallery_post WHERE fl_entrega_semanal=".$fl_entrega_semanal."");
      EjecutaQuery("DELETE FROM k_entregable WHERE fl_entrega_semanal=".$fl_entrega_semanal."");
      EjecutaQuery("DELETE FROM k_entrega_semanal WHERE fl_entrega_semanal=".$fl_entrega_semanal."");
      
    }
    
    //Eliminamos el zip
    unlink($ruta);
    
    # Eliminamos el regitros del zip
    EjecutaQuery("DELETE FROM c_backups WHERE fl_backups=".$clave."");
    
  }
  
  # No hubo errores
  header("Location: ".ObtenProgramaBase( ));
  
?>
