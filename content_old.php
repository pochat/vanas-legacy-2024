<?php
  
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  
  # Recibe parametros
  $contenido = RecibeParametroNumerico('contenido', True);
  if(empty($contenido))
    $contenido = RecibeParametroNumerico('contenido');
  
  # Revisa que se recibio un contenido
  if(empty($contenido)) {
    $direccion = "Location: ".INICIO_W;
    header($direccion);
    exit;
  }
  
  # Recupera los datos del contenido (Autorizado y en fecha de publicacion)
  $Query  = "SELECT fl_contenido, fl_funcion, cl_template, nb_titulo, tr_titulo, ds_resumen, tr_resumen, fe_evento ";
  $Query .= "FROM c_contenido ";
  $Query .= "WHERE fl_contenido=$contenido ";
  $Query .= "AND fg_activo='1' ";
  $Query .= "AND (fe_ini IS NULL OR fe_ini <= CURRENT_TIMESTAMP) ";
  $Query .= "AND (fe_fin IS NULL OR DATE_ADD(fe_fin, INTERVAL 1 DAY) >= CURRENT_TIMESTAMP)";
  $row = RecuperaValor($Query);
  $fl_contenido = $row[0];
  $fl_funcion =  $row[1];
  $cl_template = $row[2];
  $nb_titulo = str_uso_normal($row[3]);
  $tr_titulo = str_uso_normal($row[4]);
  $ds_resumen = str_uso_normal($row[5]);
  $tr_resumen = str_uso_normal($row[6]);
  $fe_evento = $row[7];
  
  # Revisa que es un contenido valido
  if(empty($fl_contenido)) {
    $direccion = "Location: ".INICIO_W;
    header($direccion);
    exit;
  }
  
  # Recupera el tipo de contenido de la seccion
  $row = RecuperaValor("SELECT cl_tipo_contenido, fg_multiple, fg_tipo_orden FROM c_funcion WHERE fl_funcion=$fl_funcion");
  $cl_tipo_contenido = $row[0];
  $fg_multiple = $row[1];
  $fg_tipo_orden = $row[2];
  
  # Recupera caracteristicas del template
  $Query  = "SELECT fg_titulo, fg_resumen, fg_fecha_evento, no_texto, no_imagen_dinamica, no_flash, no_tabla ";
  $Query .= "FROM c_template ";
  $Query .= "WHERE cl_template=$cl_template";
  $row = RecuperaValor($Query);
  $fg_titulo = $row[0];
  $fg_resumen = $row[1];
  $fg_fecha_evento = $row[2];
  $no_texto = $row[3];
  $no_imagen_dinamica = $row[4];
  $no_flash = $row[5];
  $no_tabla = $row[6];
  
  # Recupera textos asociados al contenido
  if($no_texto > 0) {
    $rs = EjecutaQuery("SELECT no_orden, fl_texto, ds_contenido, tr_contenido FROM k_texto WHERE fl_contenido=$fl_contenido");
    while($row = RecuperaRegistro($rs)) {
      $no_reg = $row[0];
      $fl_texto[$no_reg] = $row[1];
      $ds_contenido[$no_reg] = str_uso_normal($row[2]);
      $tr_contenido[$no_reg] = str_uso_normal($row[3]);
    }
  }
  
  # Recupera imagenes dinamicas asociadas al contenido
  if($no_imagen_dinamica > 0) {
    $Query  = "SELECT no_orden, fl_imagen_dinamica, ds_caption, tr_caption, nb_archivo, tr_archivo, ds_alt, tr_alt, ds_liga ";
    $Query .= "FROM k_imagen_dinamica WHERE fl_contenido=$fl_contenido";
    $rs = EjecutaQuery($Query);
    while($row = RecuperaRegistro($rs)) {
      $no_reg = $row[0];
      $fl_imagen_dinamica[$no_reg] = $row[1];
      $ds_caption_i[$no_reg] = str_uso_normal($row[2]);
      $tr_caption_i[$no_reg] = str_uso_normal($row[3]);
      $nb_archivo_i[$no_reg] = str_uso_normal($row[4]);
      $tr_archivo_i[$no_reg] = str_uso_normal($row[5]);
      $ds_alt_i[$no_reg] = str_uso_normal($row[6]);
      $tr_alt_i[$no_reg] = str_uso_normal($row[7]);
      $ds_liga_i[$no_reg] = str_uso_normal($row[8]);
    }
  }
  
  # Recupera archivos de flash asociados al contenido
  if($no_flash > 0) {
    $rs = EjecutaQuery("SELECT no_orden, fl_flash, nb_archivo, tr_archivo, no_width, no_height FROM k_flash WHERE fl_contenido=$fl_contenido");
    while($row = RecuperaRegistro($rs)) {
      $no_reg = $row[0];
      $fl_flash[$no_reg] = $row[1];
      $nb_archivo_f[$no_reg] = $row[2];
      $tr_archivo_f[$no_reg] = $row[3];
      $no_width_f[$no_reg] = $row[4];
      $no_height_f[$no_reg] = $row[5];
    }
  }
  
  # Recupera tablas asociadas al contenido
  if($no_tabla > 0) {
    $rs = EjecutaQuery("SELECT no_orden, fl_tabla FROM k_tabla WHERE fl_contenido=$fl_contenido");
    while($row = RecuperaRegistro($rs)) {
      $no_reg = $row[0];
      $fl_tabla[$no_reg] = $row[1];
    }
  }
  
  # Ligas
  if($cl_tipo_contenido == TC_LIGA) {
	$row = RecuperaValor("SELECT fl_seccion, ds_ruta, tr_ruta, fg_ventana FROM k_liga WHERE fl_contenido=$fl_contenido");
    $nodo = $row[0];
    $ds_ruta = str_uso_normal(EscogeIdioma($row[1], $row[2]));
    $fg_ventana = $row[3];
  }
  
  # Tratamiento para cada template
  switch($cl_template) {
    case TMPL_LIGA:
      if(!empty($nodo)) {
        if($fg_ventana) {
          echo "
<html><head>
<script type='text/javascript'>
<!--
window.open(\"".PAGINA_NODO."?nodo=$nodo\");
history.go(-1);
//-->
</script>
</head></html>";
        }
        else {
          $direccion = "Location: ".PAGINA_NODO."?nodo=$nodo";
          header($direccion);
          exit;
        }
      }
      elseif(!empty($ds_ruta)) {
        if($fg_ventana) {
          echo "
<html><head>
<script type='text/javascript'>
<!--
window.open(\"$ds_ruta\");
history.go(-1);
//-->
</script>
</head></html>";
        }
        else {
          $direccion = "Location: $ds_ruta";
          header($direccion);
          exit;
        }
      }
      else {
        $direccion = "Location: ".INICIO_W;
        header($direccion);
        exit;
      }
      break;
    case TMPL_NOTICIA_00: require(LIB_TMPL_NOTICIA_00); break;
    case TMPL_NOTICIA_01: require(LIB_TMPL_NOTICIA_01); break;
    case TMPL_NODO_01: require(LIB_TMPL_NODO_01); break;
    default:
      $direccion = "Location: ".INICIO_W;
      header($direccion);
      exit;
  }
  
?>