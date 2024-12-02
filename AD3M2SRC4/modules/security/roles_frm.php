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
  if(!ValidaPermiso(FUNC_PERFILES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT nb_perfil, ds_perfil, fg_admon FROM c_perfil WHERE fl_perfil=$clave");
      $nb_perfil = str_texto($row[0]);
      $ds_perfil = str_texto($row[1]);
      $fg_admon = $row[2];
    }
    else { // Alta, inicializa campos
      $nb_perfil = "";
      $ds_perfil = "";
      $fg_admon = "1";
    }
    $total_permisos = 0;
    $nb_perfil_err = "";
    $ds_perfil_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_perfil = RecibeParametroHTML('nb_perfil');
    $nb_perfil_err = RecibeParametroNumerico('nb_perfil_err');
    $ds_perfil = RecibeParametroHTML('ds_perfil');
    $ds_perfil_err = RecibeParametroNumerico('ds_perfil_err');
    $fg_admon = RecibeParametroBinario('fg_admon');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_PERFILES);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ETQ_NOMBRE, True, 'nb_perfil', $nb_perfil, 32, 20, $nb_perfil_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, True, 'ds_perfil', $ds_perfil, 64, 40, $ds_perfil_err);
  Forma_CampoOculto('fg_admon', $fg_admon); // Acceso al Sistema de Administracion
  Forma_Espacio( );
  
  # Lista de usuarios con este perfil
  if(!empty($clave)) {
    $Query  = "SELECT fl_usuario, ds_login '".ETQ_USUARIO."', ";
    $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
    $Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."', ";
    $Query .= "CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(113)."|center' ";
    $Query .= "FROM c_usuario ";
    $Query .= "WHERE fl_perfil=$clave ";
    $Query .= "AND fl_usuario > ".ADMINISTRADOR." ";
    $Query .= "ORDER BY ds_login";
    Forma_MuestraTabla($Query, TB_LN_NNN, 'usuarios', '', '100%');
    Forma_Espacio( );
  }
  
  # Busca los modulos del sistema
  $Query  = "SELECT a.fl_modulo, a.nb_modulo, a.tr_modulo, b.nb_modulo, b.tr_modulo, a.fl_modulo_padre ";
  $Query .= "FROM c_modulo a, c_modulo b ";
  $Query .= "WHERE a.fl_modulo_padre=b.fl_modulo ";
  $Query .= "AND a.fl_modulo_padre=".ADMINISTRADOR." ";
  $Query .= "AND a.fg_menu='1' ";
  $Query .= "AND EXISTS(SELECT 1 FROM c_funcion c WHERE fg_tipo_seguridad='R' AND c.fl_modulo=a.fl_modulo) ";
  $Query .= "ORDER BY a.fl_modulo_padre, a.no_orden";
  $Rows = EjecutaQuery($Query);
  $fl_modulo_padre = 0;
  while($row = RecuperaRegistro($Rows)) {
    if($row[5] <> $fl_modulo_padre) {
      Forma_Espacio( );
      Forma_Seccion(EscogeIdioma($row[3], $row[4]).' - '.ObtenEtiqueta(100)); // Titulo para seccion Privilegios
      $fl_modulo_padre = $row[5];
    }
    $ds_modulo = EscogeIdioma($row[1], $row[2]);
    echo "
    <div class='row'>
      <div class='col col-sm-12'>
        <div class='col col-sm-5 text-align-right'><strong><h4>$ds_modulo:</h4></strong></div>
        <div class='col col-sm-12 text-align-center'>";
    
    # Busca las funciones del modulo
    $Query  = "SELECT a.fl_funcion, b.fl_funcion, nb_funcion, tr_funcion, ";
    $Query .= "fg_ejecucion, fg_detalle, fg_modificacion, fg_alta, fg_baja, fg_solo_ejecucion ";
    $Query .= "FROM c_funcion a LEFT JOIN k_per_funcion b ";
    $Query .= "ON (a.fl_funcion=b.fl_funcion AND b.fl_perfil=";
    if(!empty($clave))
      $Query .= "$clave";
    else
      $Query .= "0";
    $Query .= ") ";
    $Query .= "WHERE a.fl_modulo=$row[0] ";
    $Query .= "AND a.fg_tipo_seguridad='R' ";
    $Query .= "AND a.no_orden>0 ";
    $Query .= "ORDER BY a.no_orden";
    $Rows2 = EjecutaQuery($Query);
    while($row2 = RecuperaRegistro($Rows2)) {
      $fl_funcion = $row2[0];
      $fl_funcion_fun = $row2[1];
      $ds_funcion = EscogeIdioma($row2[2], $row2[3]);
      $fg_ejecucion = $row2[4];
      $fg_detalle = $row2[5];
      $fg_modificacion = $row2[6];
      $fg_alta = $row2[7];
      $fg_baja = $row2[8];
      $fg_solo_ejecucion = $row2[9];
      $total_permisos++;
      printf("
      <div><div class='col col-sm-7 text-align-right'><strong>$ds_funcion<input type=hidden name=F$total_permisos value='$fl_funcion'></strong></div></div>
      <div><div class='col col-sm-12 text-align-right padding-10'>");
      if($fg_solo_ejecucion == "0") { 
        printf("
          <label><input class='checkbox' type=checkbox name=X$total_permisos");
        if(($fl_funcion == $fl_funcion_fun)&&($fg_ejecucion == 1))
          printf(" checked");
        printf(">%s<span></span></label>
          <label><input class='checkbox' type=checkbox name=D$total_permisos", ObtenEtiqueta(101)); // Ver listado
        if(($fl_funcion == $fl_funcion_fun)&&($fg_detalle == 1))
          printf(" checked");
        printf(">%s<span></span></label>
          <label><input class='checkbox' type=checkbox name=C$total_permisos", ObtenEtiqueta(102)); // Ver detalle
        if(($fl_funcion == $fl_funcion_fun)&&($fg_modificacion == 1))
          printf(" checked");
        printf(">%s<span></span></label>
          <label><input class='checkbox' type=checkbox name=A$total_permisos", ETQ_EDITAR);
        if(($fl_funcion == $fl_funcion_fun)&&($fg_alta == 1))
          printf(" checked");
        printf(">%s<span></span></label>
          <label><input class='checkbox' type=checkbox name=B$total_permisos", ETQ_INSERTAR);
        if(($fl_funcion == $fl_funcion_fun)&&($fg_baja == 1))
          printf(" checked");
        printf(">%s<span></span></label>", ETQ_ELIMINAR);
      }
      else {
        printf("
          <label><input class='checkbox style2' type=checkbox name=X$total_permisos");
        if(($fl_funcion == $fl_funcion_fun)&&($fg_ejecucion == 1))
          printf(" checked");
        printf(">%s
            <input type=hidden name=D$total_permisos value='0'>
            <input type=hidden name=C$total_permisos value='0'>
            <input type=hidden name=A$total_permisos value='0'>
            <input type=hidden name=B$total_permisos value='0'>
          <span></span></label>", ObtenEtiqueta(103)); // Ejecutar
      }
      echo "
      </div>";
    }
    if($total_permisos == 0)
      printf("<div><div class='css_etq_texto'>%s</div></div>", ObtenEtiqueta(104)); // No existen funciones para este m&oacute;dulo.
    echo "
      </div>
    </div>
  </div>";
  }
  Forma_CampoOculto('total_permisos', $total_permisos);
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_PERFILES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>