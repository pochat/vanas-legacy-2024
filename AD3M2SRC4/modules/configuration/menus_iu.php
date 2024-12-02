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
  if(!ValidaPermiso(FUNC_MENUS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$nb_modulo = RecibeParametroHTML('nb_modulo');
	$tr_modulo = RecibeParametroHTML('tr_modulo');
  $ds_modulo = RecibeParametroHTML('ds_modulo');
  
  # Submenus
  $regs_ini_submenus = RecibeParametroNumerico('regs_ini_submenus');
  $tot_regs_submenus = RecibeParametroNumerico('tot_regs_submenus');
  $regs_borrar_submenus = RecibeParametroHTML('regs_borrar_submenus');
  for($i = 1; $i <= $tot_regs_submenus; $i++) {
    $fl_submenu[$i] = RecibeParametroNumerico('fl_submenus_'.$i);
    $nb_submenu[$i] = RecibeParametroHTML('submenus_1_reg_'.$i);
    $tr_submenu[$i] = RecibeParametroHTML('submenus_2_reg_'.$i);
    $ds_submenu[$i] = RecibeParametroHTML('submenus_3_reg_'.$i);
    $no_orden[$i] = RecibeParametroNumerico('submenus_4_reg_'.$i);
    $ds_fg_menu[$i] = RecibeParametroHTML('submenus_5_reg_'.$i);
    if($ds_fg_menu[$i] == ETQ_NO)
      $fg_menu[$i] = 0;
    else
      $fg_menu[$i] = 1;
    $fl_parent[$i] = RecibeParametroNumerico('submenus_7_reg_'.$i);
  }
  
  # Valida campos obligatorios
  if(empty($nb_modulo))
    $nb_modulo_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $nb_modulo_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_modulo' , $nb_modulo);
    Forma_CampoOculto('nb_modulo_err' , $nb_modulo_err);
    Forma_CampoOculto('tr_modulo' , $tr_modulo);
    Forma_CampoOculto('ds_modulo' , $ds_modulo);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_modulo ";
    $Query .= "SET nb_modulo='$nb_modulo', tr_modulo='$tr_modulo', ds_modulo='$ds_modulo' ";
    $Query .= "WHERE fl_modulo=$clave";
    EjecutaQuery($Query);
  }
  else {
    $Query  = "INSERT INTO c_modulo (nb_modulo, tr_modulo, ds_modulo, fg_menu) ";
    $Query .= "VALUES('$nb_modulo', '$tr_modulo', '$ds_modulo', '0')";
    $clave = EjecutaInsert($Query);
	}
  
  # Actualiza los submenus
  for($i = 1; $i <= $regs_ini_submenus; $i++) {
    if(!empty($nb_submenu[$i])) { // Si no fue eliminado
      $Query  = "UPDATE c_modulo SET nb_modulo='$nb_submenu[$i]', tr_modulo='$tr_submenu[$i]', ds_modulo='$ds_submenu[$i]', ";
      $Query .= "fg_menu='$fg_menu[$i]', no_orden=$no_orden[$i] ";
      $Query .= "WHERE fl_modulo=$fl_submenu[$i]";
      EjecutaQuery($Query);
    }
  }
  
  # Inserta los nuevos submenus
  for($i = $regs_ini_submenus+1; $i <= $tot_regs_submenus; $i++) {
    if(!empty($nb_submenu[$i])) { // Si no fue eliminado
      if($fl_parent[$i] == '0') // Nuevo submenus de primer nivel
        $fl_modulo_padre = $clave;
      else // Nuevo submenu de segundo nivel o mayor
        $fl_modulo_padre = $fl_submenu[$fl_parent[$i]];
      if(empty($fl_modulo_padre)) // Si es un submenu de segundo o mayor nivel pero se elimino el padre, se convierte de primer nivel
        $fl_modulo_padre = $clave;
      $Query  = "INSERT INTO c_modulo(fl_modulo_padre, nb_modulo, tr_modulo, ds_modulo, fg_menu, no_orden, fg_fijo) ";
      $Query .= "VALUES ($fl_modulo_padre, '$nb_submenu[$i]', '$tr_submenu[$i]', '$ds_submenu[$i]', '$fg_menu[$i]', $no_orden[$i], '0') ";
      $fl_submenu[$i] = EjecutaInsert($Query);
    }
  }
  
	# Borra los submenus que fueron eliminados por el usuario
  if(!empty($regs_borrar_submenus)) {
    $regs_borrar = explode(",", $regs_borrar_submenus);
    $tot_borrar = count($regs_borrar)-1;
    for($i = 0; $i < $tot_borrar; $i++) {
      if(!empty($regs_borrar[$i])) {
        $row = RecuperaValor("SELECT fl_modulo_padre, fg_fijo FROM c_modulo WHERE fl_modulo=$regs_borrar[$i]");
        $fl_modulo_padre = $row[0];
        $fg_fijo = $row[1];
        if($fg_fijo == '0') {
          EjecutaQuery("UPDATE c_modulo SET fl_modulo_padre=$fl_modulo_padre WHERE fl_modulo_padre=$regs_borrar[$i]");
          EjecutaQuery("DELETE FROM c_modulo WHERE fl_modulo=$regs_borrar[$i]");
        }
      }
    }
  }
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>