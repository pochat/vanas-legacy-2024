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
  if(!ValidaPermiso(FUNC_CONTENIDOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_tipo_contenido, ds_tipo_contenido, tr_tipo_contenido ";
      $Query .= "FROM c_tipo_contenido ";
      $Query .= "WHERE cl_tipo_contenido=$clave";
      $row = RecuperaValor($Query);
      $nb_tipo_contenido = str_texto($row[0]);
      $ds_tipo_contenido = str_texto($row[1]);
      $tr_tipo_contenido = str_texto($row[2]);
    }
    else { // Alta, inicializa campos
      MuestraPaginaError(ERR_SIN_PERMISO);
      exit;
    }
    $nb_tipo_contenido_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_tipo_contenido = RecibeParametroHTML('nb_tipo_contenido');
    $nb_tipo_contenido_err = RecibeParametroNumerico('nb_tipo_contenido_err');
    $ds_tipo_contenido = RecibeParametroHTML('ds_tipo_contenido');
    $tr_tipo_contenido = RecibeParametroHTML('tr_tipo_contenido');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CONTENIDOS);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ETQ_NOMBRE, True, 'nb_tipo_contenido', $nb_tipo_contenido, 50, 60, $nb_tipo_contenido_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_tipo_contenido', $ds_tipo_contenido, 100, 60);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_tipo_contenido', $tr_tipo_contenido, 100, 60);
  Forma_Espacio( );
  
  # Templates
  if($clave != TC_PROGRAMA) {
    $tit = array(ObtenEtiqueta(152).'|center', ObtenEtiqueta(153), ETQ_DESCRIPCION);
    $ancho_col = array('15%', '25%', '60%');
    Forma_Tabla_Ini('80%', $tit, $ancho_col);
    $Query  = "SELECT a.cl_template, nb_template, ".EscogeIdioma('ds_template', 'tr_template').", cl_tipo_contenido ";
    $Query .= "FROM c_template a LEFT JOIN k_tipo_contenido_template b ";
    $Query .= "ON (a.cl_template=b.cl_template AND b.cl_tipo_contenido=$clave) ";
    $Query .= "ORDER BY a.cl_template ";
    $rs = EjecutaQuery($Query);
    for($tot_templates = 0; $row = RecuperaRegistro($rs); $tot_templates++) {
      if($tot_templates % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      if($row[3])
        $incluido = 1;
      else
        $incluido = 0;
      echo "
      <tr class='$clase'>
        <td align='center'>";
      CampoCheckbox('cl_template_'.$tot_templates, $incluido, '', $row[0]);
      echo "</td>
        <td>$row[1]</td>
        <td>$row[2]</td>
      </tr>\n";
    }
    Forma_Tabla_Fin( );
    Forma_CampoOculto('tot_templates', $tot_templates);
    Forma_Espacio( );
  }
  else
    Forma_CampoOculto('tot_templates', 0);
  
  # Secciones
  $Query  = "SELECT a.fl_funcion, ".EscogeIdioma('nb_modulo', 'tr_modulo')." '".ObtenEtiqueta(164)."', ";
  $Query .= EscogeIdioma('nb_funcion', 'tr_funcion')." '".ObtenEtiqueta(154)."', ";
  $Query .= "CASE fg_admon WHEN 1 THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END '".ETQ_TITULO_PAGINA."|center' ";
  $Query .= "FROM c_funcion a, c_modulo b ";
  $Query .= "WHERE a.fl_modulo=b.fl_modulo ";
  $Query .= "AND cl_tipo_contenido=$clave ";
  $Query .= "ORDER BY fg_admon, b.nb_modulo, a.no_orden";
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
    $fg_guardar = ValidaPermiso(FUNC_CONTENIDOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>