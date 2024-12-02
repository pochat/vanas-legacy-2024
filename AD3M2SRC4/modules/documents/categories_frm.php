<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametro
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else 
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CATEGORIAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT nb_categoria, ds_categoria FROM c_categoria_doc WHERE fl_categoria=$clave");
      $nb_categoria = str_texto($row[0]);
      $ds_categoria = str_texto($row[1]);
    }
    else 
    {
      $nb_categoria = "";
      $ds_categoria = "";
    }
    $nb_categoria_err = "";
    $ds_categoria_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_categoria = RecibeParametroHTML('nb_categoria');
    $nb_categoria_err = RecibeParametroNumerico('nb_categoria_err');
    $ds_categoria = RecibeParametroHTML('ds_categoria');
    $ds_categoria_err = RecibeParametroNumerico('ds_categoria_err');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CATEGORIAS);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(574), True, 'nb_categoria', $nb_categoria, 255, 60, $nb_categoria_err);
  Forma_CampoTexto(ObtenEtiqueta(19), True, 'ds_categoria', $ds_categoria, 255, 60, $ds_categoria_err);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CATEGORIAS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>