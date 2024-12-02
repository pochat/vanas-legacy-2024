<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_FORO, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_tema, fg_tipo, no_orden, ds_ruta_imagen, no_posts ";
      $Query .= "FROM c_f_tema ";
      $Query .= "WHERE fl_tema=$clave";
      $row = RecuperaValor($Query);
      $nb_tema = str_texto($row[0]);
      $fg_tipo = str_texto($row[1]);
      $no_orden = $row[2];
      $ds_ruta_imagen = str_texto($row[3]);
      $no_posts = $row[4];
    }
    else { // Alta, inicializa campos
      $nb_tema = "";
      $fg_tipo = "";
      $no_orden = "0";
      $ds_ruta_imagen = "";
      $no_posts = "0";
    }
    $nb_tema_err = "";
    $no_orden_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_tema = RecibeParametroHTML('nb_tema');
    $nb_tema_err = RecibeParametroNumerico('nb_tema_err');
    $fg_tipo = RecibeParametroHTML('fg_tipo');
    $no_orden = RecibeParametroNumerico('no_orden');
    $no_orden_err = RecibeParametroNumerico('no_orden_err');
    $ds_ruta_imagen = RecibeParametroHTML('ds_ruta_imagen');
    $no_posts = RecibeParametroNumerico('no_posts');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_FORO);
  
  # Ventana para preview de archivos de flash
  require 'preview.inc.php';
  
  # Inicia forma para captura de datos
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ETQ_NOMBRE, True, 'nb_tema', $nb_tema, 100, 50, $nb_tema_err);
  Forma_Espacio( );

  # New fg_tipo of stream, 'F' = Forum, 'P' = Program
  $opc = array('Forum', 'Program', 'School News');
  $val = array('F', 'P', 'S');
  Forma_CampoSelect(ObtenEtiqueta(44), False, 'fg_tipo', $opc, $val, $fg_tipo);
  Forma_Espacio( );

  Forma_CampoTexto(ETQ_ORDEN, False, 'no_orden', $no_orden, 3, 5, $no_orden_err);
  Forma_Espacio( );
  if(!empty($ds_ruta_imagen)) {
    Forma_CampoPreview(ObtenEtiqueta(184), 'ds_ruta_imagen', $ds_ruta_imagen, SP_IMAGES_W);
    Forma_CampoArchivo(ObtenEtiqueta(216), False, 'archivo_f', 70);
  }
  else
    Forma_CampoArchivo(ObtenEtiqueta(184), False, 'archivo_f', 70);
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(560), $no_posts);
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_FORO, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>