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
  if(!ValidaPermiso(FUNC_FLUJOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$nb_flujo = RecibeParametroHTML('nb_flujo');
	$ds_flujo = RecibeParametroHTML('ds_flujo');
  $tr_flujo = RecibeParametroHTML('tr_flujo');
  $fg_default = RecibeParametroNumerico('fg_default');
  if(!empty($fg_default))
    $fg_default = "1";
  for($i = 0; $i < MAX_NIVELES_AUT; $i++) {
    $ds_nivel[$i] = RecibeParametroHTML('ds_nivel_'.$i);
    $tr_nivel[$i] = RecibeParametroHTML('tr_nivel_'.$i);
    $fl_perfil[$i] = RecibeParametroHTML('fl_perfil_'.$i);
    $nb_perfil[$i] = RecibeParametroHTML('nb_perfil_'.$i);
    $fl_usuario[$i] = RecibeParametroHTML('fl_usuario_'.$i);
    $nb_usuario[$i] = RecibeParametroHTML('nb_usuario_'.$i);
  }
  
  # Valida campos obligatorios
  if(empty($nb_flujo))
    $nb_flujo_err = ERR_REQUERIDO;
  if(empty($ds_flujo))
    $ds_flujo_err = ERR_REQUERIDO;
  if(empty($ds_nivel[0]))
    $ds_nivel_err[0] = ERR_REQUERIDO;
  for($i = 1; $i < MAX_NIVELES_AUT; $i++) {
    if((empty($ds_nivel[$i])) AND (!empty($tr_nivel[$i]) OR !empty($fl_perfil[$i]) OR !empty($fl_usuario[$i])))
      $ds_nivel_err[$i] = ERR_REQUERIDO;
  }
  
  # Regresa a la forma con error
  $fg_error = $nb_flujo_err || $ds_flujo_err;
  for($i = 0; $i < MAX_NIVELES_AUT; $i++)
    $fg_error = $fg_error || $ds_nivel_err[$i];
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_flujo' , $nb_flujo);
    Forma_CampoOculto('nb_flujo_err' , $nb_flujo_err);
    Forma_CampoOculto('ds_flujo' , $ds_flujo);
    Forma_CampoOculto('ds_flujo_err' , $ds_flujo_err);
    Forma_CampoOculto('tr_flujo' , $tr_flujo);
    Forma_CampoOculto('fg_default' , $fg_default);
    for($i = 0; $i < MAX_NIVELES_AUT; $i++) {
      Forma_CampoOculto('ds_nivel_'.$i, $ds_nivel[$i]);
      Forma_CampoOculto('ds_nivel_err_'.$i, $ds_nivel_err[$i]);
      Forma_CampoOculto('tr_nivel_'.$i, $tr_nivel[$i]);
      Forma_CampoOculto('fl_perfil_'.$i, $fl_perfil[$i]);
      Forma_CampoOculto('nb_perfil_'.$i, $nb_perfil[$i]);
      Forma_CampoOculto('fl_usuario_'.$i, $fl_usuario[$i]);
      Forma_CampoOculto('nb_usuario_'.$i, $nb_usuario[$i]);
    }
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Revisa si es la definicion default
  if($fg_default)
    EjecutaQuery("UPDATE c_flujo SET fg_default=0");
  
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_flujo ";
    $Query .= "SET nb_flujo='$nb_flujo', ds_flujo='$ds_flujo', tr_flujo='$tr_flujo', fg_default=$fg_default ";
    $Query .= "WHERE fl_flujo=$clave";
    EjecutaQuery($Query);
  }
  else {
    $Query  = "INSERT INTO c_flujo (nb_flujo, ds_flujo, tr_flujo, fg_default) ";
    $Query .= "VALUES('$nb_flujo', '$ds_flujo', '$tr_flujo', $fg_default)";
    $clave = EjecutaInsert($Query);
	}
  
  # Reinicializa los niveles
  EjecutaQuery("DELETE FROM k_nivel_perfil WHERE fl_flujo=$clave");
  EjecutaQuery("DELETE FROM k_nivel_usuario WHERE fl_flujo=$clave");
  $no_nivel = 0;
  for($i = 0; $i < MAX_NIVELES_AUT; $i++) {
    if(!empty($ds_nivel[$i])) {
      $no_nivel++;
      $no_nivel_publica = $no_nivel;
      $row = RecuperaValor("SELECT 1 FROM k_flujo_nivel WHERE fl_flujo=$clave AND no_nivel=$no_nivel");
      if($row[0] == 1) {
        $Query  = "UPDATE k_flujo_nivel SET ds_nivel='".$ds_nivel[$i]."', tr_nivel='".$tr_nivel[$i]."', fg_publica='0' ";
        $Query .= "WHERE fl_flujo=$clave AND no_nivel=$no_nivel";
      }
      else {
        $Query  = "INSERT INTO k_flujo_nivel (fl_flujo, no_nivel, ds_nivel, tr_nivel, fg_publica) ";
        $Query .= "VALUES($clave, $no_nivel, '".$ds_nivel[$i]."', '".$tr_nivel[$i]."', '0')";
      }
      EjecutaQuery($Query);
      if(!empty($fl_perfil[$i])) {
        $perfiles = explode(",", $fl_perfil[$i]);
        $tot = count($perfiles);
        for($j = 0; $j < $tot; $j++) {
          $Query  = "INSERT INTO k_nivel_perfil (fl_flujo, no_nivel, fl_perfil) ";
          $Query .= "VALUES($clave, $no_nivel, ".$perfiles[$j].")";
          EjecutaQuery($Query);
        }
      }
      if(!empty($fl_usuario[$i])) {
        $usuarios = explode(",", $fl_usuario[$i]);
        $tot = count($usuarios);
        for($j = 0; $j < $tot; $j++) {
          $Query  = "INSERT INTO k_nivel_usuario (fl_flujo, no_nivel, fl_usuario) ";
          $Query .= "VALUES($clave, $no_nivel, ".$usuarios[$j].")";
          EjecutaQuery($Query);
        }
      }
    }
  }
  EjecutaQuery("UPDATE k_flujo_nivel SET fg_publica=1 WHERE fl_flujo=$clave AND no_nivel=$no_nivel_publica");
  EjecutaQuery("DELETE FROM k_flujo_nivel WHERE fl_flujo=$clave AND no_nivel>$no_nivel_publica");
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>