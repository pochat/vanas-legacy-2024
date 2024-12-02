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
  if(!ValidaPermiso(FUNC_FLUJOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT nb_flujo, ds_flujo, tr_flujo, fg_default FROM c_flujo WHERE fl_flujo=$clave");
      $nb_flujo = str_texto($row[0]);
      $ds_flujo = str_texto($row[1]);
      $tr_flujo = str_texto($row[2]);
      $fg_default = str_texto($row[3]);
      $rs = EjecutaQuery("SELECT no_nivel, ds_nivel, tr_nivel FROM k_flujo_nivel WHERE fl_flujo=$clave AND no_nivel<=".MAX_NIVELES_AUT." ORDER BY no_nivel");
      while($row = RecuperaRegistro($rs)) {
        $no_nivel = $row[0]-1;
        $ds_nivel[$no_nivel] = $row[1];
        $tr_nivel[$no_nivel] = $row[2];
      }
      $Query  = "SELECT no_nivel, a.fl_perfil, nb_perfil ";
      $Query .= "FROM k_nivel_perfil a, c_perfil b ";
      $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
      $Query .= "AND fl_flujo=$clave ";
      $Query .= "AND no_nivel<=".MAX_NIVELES_AUT;
      $rs = EjecutaQuery($Query);
      while($row = RecuperaRegistro($rs)) {
        $no_nivel = $row[0]-1;
        if(!empty($fl_perfil[$no_nivel])) {
          $fl_perfil[$no_nivel] .= ','.$row[1];
          $nb_perfil[$no_nivel] .= ','.$row[2];
        }
        else {
          $fl_perfil[$no_nivel] = $row[1];
          $nb_perfil[$no_nivel] = $row[2];
        }
      }
      $Query  = "SELECT no_nivel, a.fl_usuario, ds_login ";
      $Query .= "FROM k_nivel_usuario a, c_usuario b ";
      $Query .= "WHERE a.fl_usuario=b.fl_usuario ";
      $Query .= "AND fl_flujo=$clave ";
      $Query .= "AND no_nivel<=".MAX_NIVELES_AUT;
      $rs = EjecutaQuery($Query);
      while($row = RecuperaRegistro($rs)) {
        $no_nivel = $row[0]-1;
        if(!empty($fl_usuario[$no_nivel])) {
          $fl_usuario[$no_nivel] .= ','.$row[1];
          $nb_usuario[$no_nivel] .= ','.$row[2];
        }
        else {
          $fl_usuario[$no_nivel] = $row[1];
          $nb_usuario[$no_nivel] = $row[2];
        }
      }
    }
    else { // Alta, inicializa campos
      $nb_flujo = "";
      $ds_flujo = "";
      $tr_flujo = "";
      $fg_default = "";
      for($i = 0; $i < MAX_NIVELES_AUT; $i++) {
        $ds_nivel[$i] = "";
        $tr_nivel[$i] = "";
        $fl_perfil[$i] = "";
        $nb_perfil[$i] = "";
        $fl_usuario[$i] = "";
        $nb_usuario[$i] = "";
      }
    }
    $nb_flujo_err = "";
    $ds_flujo_err = "";
    for($i = 0; $i < MAX_NIVELES_AUT; $i++)
      $ds_nivel_err[$i] = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_flujo = RecibeParametroHTML('nb_flujo');
    $nb_flujo_err = RecibeParametroNumerico('nb_flujo_err');
    $ds_flujo = RecibeParametroHTML('ds_flujo');
    $ds_flujo_err = RecibeParametroNumerico('ds_flujo_err');
    $tr_flujo = RecibeParametroHTML('tr_flujo');
    $fg_default = RecibeParametroNumerico('fg_default');
    for($i = 0; $i < MAX_NIVELES_AUT; $i++) {
      $ds_nivel[$i] = RecibeParametroHTML('ds_nivel_'.$i);
      $ds_nivel_err[$i] = RecibeParametroNumerico('ds_nivel_err_'.$i);
      $tr_nivel[$i] = RecibeParametroHTML('tr_nivel_'.$i);
      $fl_perfil[$i] = RecibeParametroNumerico('fl_perfil_'.$i);
      $nb_perfil[$i] = RecibeParametroHTML('nb_perfil_'.$i);
      $fl_usuario[$i] = RecibeParametroNumerico('fl_usuario_'.$i);
      $nb_usuario[$i] = RecibeParametroHTML('nb_usuario_'.$i);
    }
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_FLUJOS);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ObtenEtiqueta(140), True, 'nb_flujo', $nb_flujo, 50, 60, $nb_flujo_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, True, 'ds_flujo', $ds_flujo, 255, 60, $ds_flujo_err);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_flujo', $tr_flujo, 255, 60);
  Forma_CampoCheckbox(ObtenEtiqueta(143), 'fg_default', $fg_default, ObtenEtiqueta(144));
  Forma_Espacio( );
  
  # Niveles de autorizacion
  if(FG_TRADUCCION) {
    $tit = array(ObtenEtiqueta(142).'|center', '* '.ETQ_DESCRIPCION, ETQ_TRADUCCION, ObtenEtiqueta(110), ETQ_USUARIO);
    $ancho_col = array('5%', '24%', '24%', '24%', '23%');
  }
  else {
    $tit = array(ObtenEtiqueta(142).'|center', '* '.ETQ_DESCRIPCION, ObtenEtiqueta(110), ETQ_USUARIO);
    $ancho_col = array('5%', '28%', '34%', '33%');
  }
  Forma_Tabla_Ini('100%', $tit, $ancho_col);
  for($i = 0; $i < MAX_NIVELES_AUT; $i++) {
    if($i % 2 == 0)
      $clase = "css_tabla_detalle";
    else
      $clase = "css_tabla_detalle_bg";
    echo "
  <tr class='$clase'>
    <td align='center'>".($i+1)."</td>
    <td>";
    if($ds_nivel_err[$i])
      $ds_clase = 'css_input_error';
    else
      $ds_clase = 'form-control';
    CampoTexto("ds_nivel_$i", isset($ds_nivel[$i])?$ds_nivel[$i]:NULL, 50, 25, $ds_clase);
    echo "</td>\n";
    if(FG_TRADUCCION) {
      echo "<td>";
      CampoTexto("tr_nivel_$i", $tr_nivel[$i], 50, 25, 'form-control');
      echo "</td>\n";
    }
    else
      Forma_CampoOculto("tr_nivel_$i", isset($tr_nivel[$i])?$tr_nivel[$i]:NULL);
    echo "<td>";
    CampoLOV("fl_perfil_$i", isset($fl_perfil[$i])?$fl_perfil[$i]:NULL, "nb_perfil_$i", isset($nb_perfil[$i])?$nb_perfil[$i]:NULL, 20, LOV_PERFILES, ETQ_SELECCIONAR.' '.ObtenEtiqueta(110), LOV_TIPO_CHKBOX, LOV_MEDIANO, '', 'form-control');
    echo "</td>
    <td>";
    CampoLOV("fl_usuario_$i", isset($fl_usuario[$i])?$fl_usuario[$i]:NULL, "nb_usuario_$i", isset($nb_usuario[$i])?$nb_usuario[$i]:NULL, 20, LOV_USUARIOS, ETQ_SELECCIONAR.' '.ETQ_USUARIO, LOV_TIPO_CHKBOX, LOV_GRANDE, "fl_perfil_$i", 'form-control');
    echo "</td>
  </tr>\n";
  }
  $fg_error = False;
  for($i = 0; $i < MAX_NIVELES_AUT; $i++)
    $fg_error = $fg_error || $ds_nivel_err[$i];
  if($fg_error)
    Forma_Tabla_Error(5, ERR_REQUERIDO);
  Forma_Tabla_Fin( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_FLUJOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>