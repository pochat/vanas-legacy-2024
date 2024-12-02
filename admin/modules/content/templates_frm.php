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
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_TEMPLATES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_template, ds_template, tr_template, fg_titulo, fg_resumen, fg_fecha_evento, ";
      $Query .= "no_texto, no_imagen_dinamica, no_flash, no_tabla, fg_anexo ";
      $Query .= "FROM c_template ";
      $Query .= "WHERE cl_template=$clave";
      $row = RecuperaValor($Query);
      $nb_template = str_texto($row[0]);
      $ds_template = str_texto($row[1]);
      $tr_template = str_texto($row[2]);
      $fg_titulo = $row[3];
      $fg_resumen = $row[4];
      $fg_fecha_evento = $row[5];
      $no_texto = $row[6];
      $no_imagen_dinamica = $row[7];
      $no_flash = $row[8];
      $no_tabla = $row[9];
      $fg_anexo = $row[10];
    }
    else { // Alta, inicializa campos
      MuestraPaginaError(ERR_SIN_PERMISO);
      exit;
    }
    $nb_template_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_template = RecibeParametroHTML('nb_template');
    $nb_template_err = RecibeParametroNumerico('nb_template_err');
    $ds_template = RecibeParametroHTML('ds_template');
    $tr_template = RecibeParametroHTML('tr_template');
    $fg_titulo = RecibeParametroNumerico('fg_titulo');
    $fg_resumen = RecibeParametroNumerico('fg_resumen');
    $fg_fecha_evento = RecibeParametroNumerico('fg_fecha_evento');
    $no_texto = RecibeParametroNumerico('no_texto');
    $no_imagen_dinamica = RecibeParametroNumerico('no_imagen_dinamica');
    $no_flash = RecibeParametroNumerico('no_flash');
    $no_tabla = RecibeParametroNumerico('no_tabla');
    $fg_anexo = RecibeParametroNumerico('fg_anexo');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_TEMPLATES);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ETQ_NOMBRE, True, 'nb_template', $nb_template, 50, 60, $nb_template_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_template', $ds_template, 100, 60);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_template', $tr_template, 100, 60);
  Forma_Espacio( );
  
  # Caracteristicas del template
  if($fg_titulo == 1) 
    $texto = ETQ_SI;
  else
    $texto = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(178), $texto);
  Forma_CampoOculto('fg_titulo', $fg_titulo);
  if($fg_resumen == 1) 
    $texto = ETQ_SI;
  else
    $texto = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(191), $texto);
  Forma_CampoOculto('fg_resumen', $fg_resumen);
  if($fg_fecha_evento == 1) 
    $texto = ETQ_SI;
  else
    $texto = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(193), $texto);
  Forma_CampoOculto('fg_fecha_evento', $fg_fecha_evento);
  if($fg_anexo == 1) 
    $texto = ETQ_SI;
  else
    $texto = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(233), $texto);
  Forma_CampoOculto('fg_anexo', $fg_anexo);
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(234), $no_texto);
  Forma_CampoOculto('no_texto', $no_texto);
  Forma_CampoInfo(ObtenEtiqueta(235), $no_imagen_dinamica);
  Forma_CampoOculto('no_imagen_dinamica', $no_imagen_dinamica);
  Forma_CampoInfo(ObtenEtiqueta(236), $no_flash);
  Forma_CampoOculto('no_flash', $no_flash);
  Forma_CampoInfo(ObtenEtiqueta(237), $no_tabla);
  Forma_CampoOculto('no_tabla', $no_tabla);
  Forma_Espacio( );
  
  # Tipos de contenido
  $Query  = "SELECT a.cl_tipo_contenido, nb_tipo_contenido '".ObtenEtiqueta(232)."', ";
  $Query .= EscogeIdioma('ds_tipo_contenido', 'tr_tipo_contenido')." '".ETQ_DESCRIPCION."' ";
  $Query .= "FROM c_tipo_contenido a, k_tipo_contenido_template b ";
  $Query .= "WHERE a.cl_tipo_contenido=b.cl_tipo_contenido ";
  $Query .= "AND cl_template=$clave ";
  $Query .= "ORDER BY a.cl_tipo_contenido";
  echo "
  <tr>
    <td colspan='2' align='center'>
    <table border='".D_BORDES."' width='80%'>
    <tr>
      <td>\n";
  MuestraTabla($Query, TB_LN_NNN, 'secciones');
  echo "
      </td>
    </tr>
    </table>
    </td>
  </tr>\n";
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_TEMPLATES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>