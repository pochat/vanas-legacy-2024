<?php 
	# Libreria de funciones
	// require("../../modules/common/lib/cam_general.inc.php");
	require("/../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  // $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  // if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    // MuestraPaginaError(ERR_SIN_PERMISO);
    // exit;
  // }
  # Obtenemos el perfil del usuario
  # 11 Administrador
  # 2 Teachers
  # 3 Students
  // $fl_usuario = 1;
  // PresentaHeaderNav($fl_usuario);
  $fl_usuario = 1;
  PresentaHeaderNav($fl_usuario);  
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  if($fl_perfil==1)
    $fl_perfil=11;
  
?>
<div id="main" role="main">
<?php
// include('/../lib/ribbon.php');
PresentaRibbon();
?>
<div id="content">
<!-- Muestra las funciones de cada usuario dependiendo del perfil -->
<div class="row" style="padding:30px;">
  <?php
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  if($fl_perfil==1)
    $fl_perfil = 11;
  switch($fl_perfil){
    case PFL_ADMINISTRADOR: $menu = MENU_ADMIN_SELF;  break;
    case PFL_MAESTRO: $menu = MENU_MAESTRO_SELF;  break;
    case PFL_ESTUDIANTE: $menu = MENU_ALUMNO_SELF;  break;
  }
  
  $Query  = "SELECT fn.fl_funcion, fn.nb_funcion, fn.nb_flash_default, cn.ds_resumen, cn.tr_resumen, lg.ds_ruta, fn.ds_icono_bootstrap ";
  $Query .= "FROM c_modulo md, c_funcion fn LEFT JOIN c_contenido cn on(cn.fl_funcion=fn.fl_funcion) ";
  $Query .= "LEFT JOIN k_liga lg ON(lg.fl_contenido=cn.fl_contenido) ";
  $Query .= "WHERE fn.fl_modulo=md.fl_modulo AND md.fl_modulo_padre=$menu ORDER BY fn.fl_funcion";
  $rs = EjecutaQuery($Query);
  for($i=0;$row = RecuperaRegistro($rs);$i++){
    $fl_funcion = $row[0];
    $nb_funcion = str_texto($row[1]);
    $nb_flash_default = $row[2];
    $ds_resumen = str_uso_normal(EscogeIdioma($row[3], $row[4]));
    $ds_ruta = $row[5];
    $ds_icono_bootstrap = $row[6];
    ?>
    <div class="col-sm-6 col-md-2 col-lg-2 text-align-center">
      <i class="fa <?php echo $ds_icono_bootstrap; ?> fa-5x" style="color:#0071BD;"></i><br/>
      <h4><a href="<?php echo "index.php#public/".$ds_ruta; ?>" style="color:#000;">
       <strong><?php echo $nb_funcion; ?></strong>
      </a><br/>
        <small><?php echo $ds_resumen; ?></small>
      </h4>
    </div>
    <?php
  }
  ?>
</div>
</div>
</div>
<?php

// include('/../lib/scripts.php');
// include('/../lib/footer.php');
PresentaFooter();
?>