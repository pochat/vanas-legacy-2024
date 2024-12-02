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
  if(!ValidaPermiso(FUNC_TABLAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_tabla, tr_tabla, no_width, ds_caption, tr_caption ";
      $Query .= "FROM c_tabla ";
      $Query .= "WHERE fl_tabla=$clave";
      $row = RecuperaValor($Query);
      $nb_tabla = str_texto($row[0]);
      $tr_tabla = str_texto($row[1]);
      $no_width = $row[2];
      $ds_caption = str_texto($row[3]);
      $tr_caption = str_texto($row[4]);
      $Query  = "SELECT fl_columna, nb_columna, tr_columna, fg_align, no_width ";
      $Query .= "FROM k_columna_tabla ";
      $Query .= "WHERE fl_tabla=$clave ";
      $Query .= "ORDER BY no_orden";
      $rs = EjecutaQuery($Query);
      for($i = 0; $row = RecuperaRegistro($rs); $i++) {
        $fl_columna[$i] = $row[0];
        $no_columna[$i] = $i+1;
        $nb_columna[$i] = str_texto($row[1]);
        $tr_columna[$i] = str_texto($row[2]);
        $fg_align[$i] = $row[3];
        $no_width_c[$i] = $row[4];
      }
      $regs_ini_columnas = $i;
      $tot_regs_columnas = $i;
    }
    else { // Alta, inicializa campos
      $nb_tabla = "";
      $tr_tabla = "";
      $no_width = "";
      $ds_caption = "";
      $tr_caption = "";
      $regs_ini_columnas = 0;
      $tot_regs_columnas = 0;
    }
    $nb_tabla_err = "";
    $regs_borrar_columnas = '';
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_tabla = RecibeParametroHTML('nb_tabla');
    $nb_tabla_err = RecibeParametroNumerico('nb_tabla_err');
    $tr_tabla = RecibeParametroHTML('tr_tabla');
    $no_width = RecibeParametroHTML('no_width');
    $ds_caption = RecibeParametroHTML('ds_caption');
    $tr_caption = RecibeParametroHTML('tr_caption');
    $regs_ini_columnas = RecibeParametroNumerico('regs_ini_columnas');
    $tot_regs_columnas = RecibeParametroNumerico('tot_regs_columnas');
    $regs_borrar_columnas = RecibeParametroHTML('regs_borrar_columnas');
    for($i = 0; $i < $tot_regs_columnas; $i++) {
      $reg = $i+1;
      $fl_columna[$i] = RecibeParametroNumerico('fl_columna_'.$reg);
      $no_columna[$i] = RecibeParametroNumerico('columnas_1_reg_'.$reg);
      $nb_columna[$i] = RecibeParametroHTML('columnas_2_reg_'.$reg);
      $tr_columna[$i] = RecibeParametroHTML('columnas_3_reg_'.$reg);
      $fg_align[$i] = RecibeParametroHTML('columnas_4_reg_'.$reg);
      $no_width_c[$i] = RecibeParametroHTML('columnas_5_reg_'.$reg);
    }
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_TABLAS);
  
  # Forma para columnas
  require 'tables_frm.inc.php';
  
  # Forma para captura de datos
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ETQ_NOMBRE, True, 'nb_tabla', $nb_tabla, 255, 50, $nb_tabla_err);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_tabla', $tr_tabla, 255, 50);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(218), False, 'no_width', $no_width, 4, 5);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(259), False, 'ds_caption', $ds_caption, 255, 50);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_caption', $tr_caption, 255, 50);
  Forma_Espacio( );
  
  # Columnas
  $tit = array(ObtenEtiqueta(252).'|center', ETQ_TITULO, ETQ_TRADUCCION, ObtenEtiqueta(251), ObtenEtiqueta(218).'|center', '', '');
  $ancho_col = array('8%', '30%', '30%', '16%', '10%', '3%', '3%');
  $tot_span = count($tit);
  Forma_Tabla_Ini('100%', $tit, $ancho_col, 'columnas');
  $impar = True;
  for($i = 0; $i < $tot_regs_columnas; $i++) {
    if($fl_columna[$i] <> "") {
      $reg = $i+1;
      if($impar) {
        $clase = "css_tabla_detalle";
        $clase_ico = "css_tabla_detalle_ico";
      }
      else {
        $clase = "css_tabla_detalle_bg";
        $clase_ico = "css_tabla_detalle_ico_bg";
      }
      $impar = !$impar;
      $liga = "<a href=\"javascript:ActualizaEnTabla('columnas', '$reg');\">";
      switch($fg_align[$i]) {
        case 'L': $ds_align = ObtenEtiqueta(253); break;
        case 'C': $ds_align = ObtenEtiqueta(254); break;
        case 'R': $ds_align = ObtenEtiqueta(255); break;
      }
      echo "
    <tr class='$clase' id='reg_columnas_$reg'>
      <td align='center'>$liga$reg</a></td>
      <input type='hidden' name='columnas_1_reg_$reg' id='columnas_1_reg_$reg' value='$reg'>
      <td>$liga$nb_columna[$i]</a></td>
      <input type='hidden' name='columnas_2_reg_$reg' id='columnas_2_reg_$reg' value='$nb_columna[$i]'>
      <td>$liga$tr_columna[$i]</a></td>
      <input type='hidden' name='columnas_3_reg_$reg' id='columnas_3_reg_$reg' value='$tr_columna[$i]'>
      <td>$liga$ds_align</a></td>
      <input type='hidden' name='columnas_4_reg_$reg' id='columnas_4_reg_$reg' value='$fg_align[$i]'>
      <td align='center'>$liga$no_width_c[$i]</a></td>
      <input type='hidden' name='columnas_5_reg_$reg' id='columnas_5_reg_$reg' value='$no_width_c[$i]'>
      <td class='$clase_ico' align='center'>$liga<img src='".PATH_IMAGES."/".IMG_EDITAR."' width=17 height=16 border=0 title='".ETQ_EDITAR."'></a></td>
      <td class='$clase_ico' align='center'><a href=\"javascript:BorraEnTabla('columnas', '$reg');\"><img src='".PATH_IMAGES."/".IMG_BORRAR."' width=17 height=16 border=0 title='".ETQ_ELIMINAR."'></a></td>
    </tr>\n";
      Forma_CampoOculto('fl_columna_'.$reg, $fl_columna[$i]);
    }
  }
  Forma_Tabla_Fin( );
  Forma_Doble_Ini( );
  echo "
<TABLE border='".D_BORDES."' cellPadding='3' cellSpacing='0' width='100%'>
  <tr>
    <td class='css_default'><a href=\"javascript:InsertaEnTabla('columnas');\"><img src='".PATH_IMAGES."/".IMG_NUEVO."' align=top valign=top width=17 height=16 border=0 title='".ETQ_INSERTAR."'> ".ETQ_INSERTAR."</a></td>
  </tr>
</table>\n";
  Forma_Doble_Fin( );
  Forma_CampoOculto('regs_ini_columnas', $regs_ini_columnas);
  Forma_CampoOculto('tot_regs_columnas', $tot_regs_columnas);
  Forma_CampoOculto('regs_borrar_columnas', $regs_borrar_columnas);
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_TABLAS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>