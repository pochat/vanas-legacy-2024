<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_TABLAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$nb_tabla = RecibeParametroHTML('nb_tabla');
  $tr_tabla = RecibeParametroHTML('tr_tabla');
  $no_width = RecibeParametroHTML('no_width');
  $ds_caption = RecibeParametroHTML('ds_caption');
  $tr_caption = RecibeParametroHTML('tr_caption');
  $regs_ini_columnas = RecibeParametroNumerico('regs_ini_columnas');
  $tot_regs_columnas = RecibeParametroNumerico('tot_regs_columnas');
  $regs_borrar_columnas = RecibeParametroHTML('regs_borrar_columnas');
  for($i = 0; $i < $tot_regs_columnas; $i++) {
    $reg = $i + 1;
    $fl_columna[$i] = RecibeParametroNumerico('fl_columna_'.$reg);
    $no_columna[$i] = RecibeParametroNumerico('columnas_1_reg_'.$reg);
    $nb_columna[$i] = RecibeParametroHTML('columnas_2_reg_'.$reg);
    $tr_columna[$i] = RecibeParametroHTML('columnas_3_reg_'.$reg);
    $fg_align[$i] = RecibeParametroHTML('columnas_4_reg_'.$reg);
    $no_width_c[$i] = RecibeParametroHTML('columnas_5_reg_'.$reg);
  }
  
  # Valida campos obligatorios
  if(empty($nb_tabla))
    $nb_tabla_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $nb_tabla_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_tabla' , $nb_tabla);
    Forma_CampoOculto('nb_tabla_err' , $nb_tabla_err);
    Forma_CampoOculto('tr_tabla' , $tr_tabla);
    Forma_CampoOculto('no_width' , $no_width);
    Forma_CampoOculto('ds_caption' , $ds_caption);
    Forma_CampoOculto('tr_caption' , $tr_caption);
    Forma_CampoOculto('regs_ini_columnas' , $regs_ini_columnas);
    Forma_CampoOculto('tot_regs_columnas' , $tot_regs_columnas);
    Forma_CampoOculto('regs_borrar_columnas' , $regs_borrar_columnas);
    for($i = 0; $i < $tot_regs_columnas; $i++) {
      $reg = $i+1;
      Forma_CampoOculto('fl_columna_'.$reg, $fl_columna[$i]);
      Forma_CampoOculto('columnas_1_reg_'.$reg, $no_columna[$i]);
      Forma_CampoOculto('columnas_2_reg_'.$reg, $nb_columna[$i]);
      Forma_CampoOculto('columnas_3_reg_'.$reg, $tr_columna[$i]);
      Forma_CampoOculto('columnas_4_reg_'.$reg, $fg_align[$i]);
      Forma_CampoOculto('columnas_5_reg_'.$reg, $no_width_c[$i]);
    }
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_tabla ";
    $Query .= "SET nb_tabla='$nb_tabla', tr_tabla='$tr_tabla', no_width='$no_width', ds_caption='$ds_caption', tr_caption='$tr_caption' ";
    $Query .= "WHERE fl_tabla=$clave";
    EjecutaQuery($Query);
  }
  else {
    $Query  = "INSERT INTO c_tabla (nb_tabla, tr_tabla, no_columnas, no_width, ds_caption, tr_caption) ";
    $Query .= "VALUES('$nb_tabla', '$tr_tabla', 0, '$no_width', '$ds_caption', '$tr_caption')";
    $clave = EjecutaInsert($Query);
	}
  
  # Actualiza las columnas
  for($i = 0; $i < $regs_ini_columnas; $i++) {
    if(!empty($nb_columna[$i])) { // Si no fue eliminado
      $Query  = "UPDATE k_columna_tabla SET nb_columna='$nb_columna[$i]', tr_columna='$tr_columna[$i]', fg_align='$fg_align[$i]', ";
      $Query .= "no_width='$no_width_c[$i]' ";
      $Query .= "WHERE fl_columna=$fl_columna[$i]";
      EjecutaQuery($Query);
    }
  }
  
  # Inserta las nuevas columnas
  for($i = $regs_ini_columnas; $i < $tot_regs_columnas; $i++) {
    if(!empty($nb_columna[$i])) { // Si no fue eliminado
      $Query  = "INSERT INTO k_columna_tabla (fl_tabla, nb_columna, tr_columna, no_orden, fg_align, no_width) ";
      $Query .= "VALUES ($clave, '$nb_columna[$i]', '$tr_columna[$i]', $no_columna[$i], '$fg_align[$i]', '$no_width_c[$i]')";
      EjecutaQuery($Query);
    }
  }
  
	# Borra las columnas que fueron eliminadas por el usuario
  if(!empty($regs_borrar_columnas)) {
    $regs_borrar = explode(",", $regs_borrar_columnas);
    $tot_borrar = count($regs_borrar)-1;
    for($i = 0; $i < $tot_borrar; $i++) {
      if(!empty($regs_borrar[$i])) {
        EjecutaQuery("DELETE FROM k_celda_tabla WHERE fl_columna=$regs_borrar[$i]");
        EjecutaQuery("DELETE FROM k_columna_tabla WHERE fl_columna=$regs_borrar[$i]");
      }
    }
  }
  
  # Renumera las columnas y actualiza total en la tabla
  $rs = EjecutaQuery("SELECT fl_columna FROM k_columna_tabla WHERE fl_tabla=$clave ORDER BY no_orden");
  for($i = 0; $row = RecuperaRegistro($rs); $i++)
    $fl_columna_orden[$i] = $row[0];
  $tot_cols = $i;
  for($i = 0; $i < $tot_cols; $i++) {
    $Query  = "UPDATE k_columna_tabla SET no_orden=". $i+1 ." WHERE fl_columna=$fl_columna_orden[$i]";
    EjecutaQuery($Query);
  }
  EjecutaQuery("UPDATE c_tabla SET no_columnas=$tot_cols WHERE fl_tabla=$clave");
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>