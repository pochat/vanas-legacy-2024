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
  if(!ValidaPermiso(FUNC_ETIQUETAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT nb_etiqueta, ds_etiqueta, tr_etiqueta FROM c_etiqueta WHERE cl_etiqueta=$clave");
      $nb_etiqueta = str_texto($row[0]);
      $ds_etiqueta = str_texto($row[1]);
      $tr_etiqueta = str_texto($row[2]);
    }
    else { // Alta, inicializa campos
      $nb_etiqueta = "";
      $ds_etiqueta = "";
      $tr_etiqueta = "";
    }
    $cl_etiqueta_nueva = "";
    $cl_etiqueta_err = "";
    $nb_etiqueta_err = "";
    $ds_etiqueta_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $cl_etiqueta_nueva = RecibeParametroNumerico('cl_etiqueta_nueva');
    $cl_etiqueta_err = RecibeParametroNumerico('cl_etiqueta_err');
    $nb_etiqueta = RecibeParametroHTML('nb_etiqueta');
    $nb_etiqueta_err = RecibeParametroNumerico('nb_etiqueta_err');
    $ds_etiqueta = RecibeParametroHTML('ds_etiqueta');
    $ds_etiqueta_err = RecibeParametroNumerico('ds_etiqueta_err');
    $tr_etiqueta = RecibeParametroHTML('tr_etiqueta');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_ETIQUETAS);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Si se esta editando
  if(!empty($clave)) {
    Forma_CampoInfo(ETQ_CLAVE, $clave);
    Forma_CampoOculto('cl_etiqueta_nueva', $cl_etiqueta_nueva);
  }
  else
    Forma_CampoTexto(ETQ_CLAVE, True, 'cl_etiqueta_nueva', $cl_etiqueta_nueva, 5, 10, $cl_etiqueta_err);
  
  Forma_Espacio( );
  Forma_CampoTexto(ETQ_NOMBRE, True, 'nb_etiqueta', $nb_etiqueta, 50, 60, $nb_etiqueta_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, True, 'ds_etiqueta', $ds_etiqueta, 1000, 60, $ds_etiqueta_err);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_etiqueta', $tr_etiqueta, 1000, 60);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_ETIQUETAS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>