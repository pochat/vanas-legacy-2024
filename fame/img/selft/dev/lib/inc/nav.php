<?php
	# Left panel : Navigation area

	# Verifica que exista una sesion valida en el cookie y la resetea
  // $fl_usuario = ValidaSesion(False);

  # Verifica que el usuario tenga permiso de usar esta funcion
	// if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
	  // MuestraPaginaError(ERR_SIN_PERMISO);
	  // exit;
	// }
  
  # Obtenemos el perfil del usuario
  # 11 Administrador
  # 2 Teachers
  # 3 Students
  $fl_usuario = 11;
  $fl_perfil = ObtenPerfil($fl_usuario);
  $fl_perfil = 11;  
  switch($fl_perfil){
    case PFL_ADMINISTRADOR: $menu = MENU_ADMIN_SELF;  break;
    case PFL_MAESTRO: $menu = MENU_MAESTRO_SELF;  break;
    case PFL_ESTUDIANTE: $menu = MENU_ALUMNO_SELF;  break;
  }
?>
<aside id="left-panel">
	<div class="login-info">
		<span> 
			<a href="#ajax/profile.php">
				<img src="<?php echo ObtenAvatarUsuario($fl_usuario); ?>"> 
				<span><?php echo ObtenNombreUsuario($fl_usuario); ?></span>
			</a> 
		</span>
	</div>
	<nav> 
    <ul style="display: block;">         
    <?php
    # Menu dependiendo de los perfiles
    /*$Query  = "SELECT md.fl_modulo, nb_modulo, tr_modulo, fn.fl_funcion, fn.nb_funcion, fn.tr_funcion, fn.fl_modulo ";
    $Query .= "FROM c_modulo md LEFT JOIN c_funcion fn ON( fn.fl_modulo=md.fl_modulo ) ";
    $Query .= "WHERE md.fl_modulo_padre=".$menu." AND md.fg_menu='1' ";
    $Query .= "ORDER BY md.no_orden ";*/
    $Query  = "SELECT fl_modulo, nb_modulo, tr_modulo ";
    $Query .= "FROM c_modulo ";
    $Query .= "WHERE fl_modulo_padre=$menu ";
    $Query .= "AND fg_menu='1' ";
    $Query .= "ORDER BY no_orden";
    $rs = EjecutaQuery($Query);
    for($i=0; $row = RecuperaRegistro($rs); $i++){
      $fl_modulo = $row[0];
      $nb_modulo = str_texto(EscogeIdioma($row[1], $row[2]));
    ?>
      <li class="open">
        <a href="#" onclick="return false;" title="Campus">
        <i class="fa fa-lg fa-fw "></i> <span class="menu-item-parent"><?php echo $nb_modulo; ?></span><b class="collapse-sign"><em class="fa fa-expand-o"></em></b></a>
        <ul style="display:block;">
          <?php
          # Funciones
          $Query1  = "SELECT fl_funcion, nb_funcion, tr_funcion, nb_flash_default, tr_flash_default ";
          $Query1 .= "FROM c_funcion ";
          $Query1 .= "WHERE fl_modulo= $fl_modulo ";
          $Query1 .= "AND fg_menu='1' ";
          $Query1 .= "ORDER BY no_orden";
          $rs1 = EjecutaQuery($Query1);
          for($j=0; $row1 = RecuperaRegistro($rs1);$j++){
            $fl_funcion = $row1[0];
            $nb_funcion = str_texto(EscogeIdioma($row1[1], $row1[2]));
            $nb_icono = str_uso_normal(EscogeIdioma($row1[3], $row1[4]));
            ?>
            <li><a href='<?php echo PATH_SELF_PUB."/node.php?node=".$fl_funcion; ?>'><?php echo $nb_funcion; ?></a></li>
            <?php
          }
          ?>
        </ul>
      </li>
    <?php
    }
    ?>
    </ul>
	</nav>
</aside>