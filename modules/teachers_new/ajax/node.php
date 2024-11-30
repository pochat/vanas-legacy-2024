<?php 
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion(False);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibe parametros
  $nodo = RecibeParametroNumerico('node', True);
  
  # Revisa que se recibio un nodo
  if(empty($nodo)) {
    $direccion = "Location: ".INICIO_W;
    header($direccion);
    exit;
  }

  # Recupera las caracteristicas de la seccion
  $Query  = "SELECT fl_funcion, fg_multiple, fg_tipo_orden, cl_tipo_contenido ";
  $Query .= "FROM c_funcion ";
  $Query .= "WHERE fl_funcion=$nodo";
  $row = RecuperaValor($Query);
  $fl_funcion = $row[0];
  $fg_multiple = $row[1];
  $fg_tipo_orden = $row[2];
  $cl_tipo_contenido = $row[3];

  # Revisa que existe el nodo
  if(empty($fl_funcion)) {
    $direccion = "Location: ".INICIO_W;
    header($direccion);
    exit;
  }

  # Recupera los datos del contenido inicial para el nodo (Autorizados y en fecha de publicacion)
  $Query  = "SELECT fl_contenido, cl_template, nb_titulo, tr_titulo, ds_resumen, tr_resumen, fe_evento, ";
  $Query .= "CASE WHEN cl_template IN(".TMPL_CARATULA.") THEN 1 ELSE 0 END 'fg_caratula' ";
  $Query .= "FROM c_contenido ";
  $Query .= "WHERE fl_funcion=$fl_funcion ";
  $Query .= "AND fg_activo='1' ";
  $Query .= "AND (fe_ini IS NULL OR fe_ini <= CURRENT_TIMESTAMP) ";
  $Query .= "AND (fe_fin IS NULL OR DATE_ADD(fe_fin, INTERVAL 1 DAY) >= CURRENT_TIMESTAMP) ";
  $Query .= "ORDER BY fg_caratula DESC, ";
  if($fg_multiple == 1) { // Si la seccion permite multiples contenidos aplica el tipo de ordenamiento de la seccion
    switch($fg_tipo_orden) {
      case 'A': $Query .= "fe_evento"; break;      // Fecha ascendente
      case 'D': $Query .= "fe_evento DESC"; break; // Fecha descendente
      case 'T': $Query .= "nb_titulo"; break;      // Titulo
      default: $Query .= "no_orden";               // Numero de orden
    }
  }
  else // Si la seccion permite solo un contenido y hay mas de uno, se muestra el que tenga fecha de publicacion mas reciente
    $Query .= "fe_ini DESC";
    
  $row = RecuperaValor($Query);
  $fl_contenido = $row[0];
  $cl_template = $row[1];
  $nb_titulo = str_uso_normal($row[2]);
  $tr_titulo = str_uso_normal($row[3]);
  $ds_resumen = str_uso_normal($row[4]);
  $tr_resumen = str_uso_normal($row[5]);
  $fe_evento = $row[6];

  # Revisa que es un contenido valido
  if(empty($fl_contenido)) {
    $direccion = "Location: ".INICIO_W;
    header($direccion);
    exit;
  }

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
  $row = RecuperaValor("SELECT fl_seccion, cl_pagina, ds_ruta, tr_ruta, fg_ventana FROM k_liga WHERE fl_contenido=$fl_contenido");
    $nodo = $row[0];
    $pagina = $row[1];
    $ds_ruta = str_uso_normal(EscogeIdioma($row[2], $row[3]));
    $fg_ventana = $row[4];
  }

  # Tratamiento para cada template
  switch($cl_template) {
    case TMPL_LIGA:
      if(!empty($nodo)) {
        if($fg_ventana) {
          /*echo "
            <html>
              <head>
                <script type='text/javascript'>
                  //<!--
                  window.open(\"".PATH_N_ALU_PAGES."/node.php?node=$nodo\");
                  window.history.go(-2);
                  //-->
                </script>
              </head>
            </html>";*/
        }
        else {
          echo 
            "<script type='text/javascript'>
              location.hash = '#ajax/node.php?node=$nodo';
            </script>";
        }
      }
      elseif(!empty($pagina)) {
        if($fg_ventana) {
          /*echo "
            <html><head>
              <script type='text/javascript'>
               <!--
                    window.open(\"".PAGINA_CON_ALU."?page=$pagina\");
                    history.go(-1);
                    //-->
                </script>
          </head></html>";*/
        }
        else {
          echo 
            "<script type='text/javascript'>
              location.hash = '#ajax/content.php?page=$pagina';
            </script>";
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
          echo 
            "<script type='text/javascript'>
              location.hash = '#ajax/$ds_ruta';
            </script>";
        } 
      }
      else {
        $direccion = "Location: ".INICIO_W;
        header($direccion);
        exit;
      }
    break;
    default:
      $direccion = "Location: ".INICIO_W;
      header($direccion);
      exit;
  }

?>