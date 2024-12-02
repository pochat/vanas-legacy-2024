<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  require("../lib/sp_forms.inc.php");
  
  # Recupera sesion del cookie
  $cl_sesion = SP_RecuperaSesion( );
  
  # Recibe parametro con la clave de sesion
  $clave = RecibeParametroHTML('clave');
  
  # Si no es una sesion valida redirige a la forma inicial
  if(empty($cl_sesion) OR $cl_sesion <> $clave) {
    header("Location: ".ObtenProgramaNombre(PGM_FORM));
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $direccion = RecibeParametroHTML('direccion');
  $ds_resp_1 = RecibeParametroHTML('ds_resp_1');
  $ds_resp_2 = RecibeParametroHTML('ds_resp_2');
  $ds_resp_3 = RecibeParametroHTML('ds_resp_3');
  $ds_resp_4 = RecibeParametroHTML('ds_resp_4');
  $ds_resp_5 = RecibeParametroHTML('ds_resp_5');
  $ds_resp_6 = RecibeParametroHTML('ds_resp_6');
  $ds_resp_7 = RecibeParametroHTML('ds_resp_7');
  
  # Valida campos obligatorios
  if(empty($ds_resp_1))
    $ds_resp_1_err = ERR_REQUERIDO;
  if(empty($ds_resp_2))
    $ds_resp_2_err = ERR_REQUERIDO;
  if(empty($ds_resp_3))
    $ds_resp_3_err = ERR_REQUERIDO;
  if(empty($ds_resp_4))
    $ds_resp_4_err = ERR_REQUERIDO;
  if(empty($ds_resp_5))
    $ds_resp_5_err = ERR_REQUERIDO;
  if(empty($ds_resp_6))
    $ds_resp_6_err = ERR_REQUERIDO;
  if(empty($ds_resp_7))
    $ds_resp_7_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $ds_resp_1_err || $ds_resp_2_err || $ds_resp_3_err || $ds_resp_4_err || $ds_resp_5_err || $ds_resp_6_err || $ds_resp_7_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_resp_1' , $ds_resp_1);
    Forma_CampoOculto('ds_resp_1_err' , $ds_resp_1_err);
    Forma_CampoOculto('ds_resp_2' , $ds_resp_2);
    Forma_CampoOculto('ds_resp_2_err' , $ds_resp_2_err);
    Forma_CampoOculto('ds_resp_3' , $ds_resp_3);
    Forma_CampoOculto('ds_resp_3_err' , $ds_resp_3_err);
    Forma_CampoOculto('ds_resp_4' , $ds_resp_4);
    Forma_CampoOculto('ds_resp_4_err' , $ds_resp_4_err);
    Forma_CampoOculto('ds_resp_5' , $ds_resp_5);
    Forma_CampoOculto('ds_resp_5_err' , $ds_resp_5_err);
    Forma_CampoOculto('ds_resp_6' , $ds_resp_6);
    Forma_CampoOculto('ds_resp_6_err' , $ds_resp_6_err);
    Forma_CampoOculto('ds_resp_7' , $ds_resp_7);
    Forma_CampoOculto('ds_resp_7_err' , $ds_resp_7_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza los datos de la forma para la sesion
  $Query  = "UPDATE c_sesion SET fg_app_2='1', fe_ultmod=CURRENT_TIMESTAMP ";
  $Query .= "WHERE cl_sesion='$clave'";
  EjecutaQuery($Query);
  
  # Verifica si se esta insertando
  $row = RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_2 WHERE cl_sesion='$clave'");
  if(empty($row[0]))
    $fg_nueva = True;
  else
    $fg_nueva = False;
  
  # Inserta o actualiza los datos de la forma para la sesion
  if($fg_nueva) {
    $Query  = "INSERT INTO k_ses_app_frm_2 ";
    $Query .= "(cl_sesion, ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, fe_ultmod) ";
    $Query .= "VALUES ('$cl_sesion', ";
    $Query .= "'$ds_resp_1', '$ds_resp_2', '$ds_resp_3', '$ds_resp_4', '$ds_resp_5', '$ds_resp_6', '$ds_resp_7', CURRENT_TIMESTAMP)";
  }
  else {
    $Query  = "UPDATE k_ses_app_frm_2 SET ds_resp_1='$ds_resp_1', ds_resp_2='$ds_resp_2', ds_resp_3='$ds_resp_3', ";
    $Query .= "ds_resp_4='$ds_resp_4', ds_resp_5='$ds_resp_5', ds_resp_6='$ds_resp_6', ds_resp_7='$ds_resp_7', fe_ultmod=CURRENT_TIMESTAMP ";
    $Query .= "WHERE cl_sesion='$clave'";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  if($direccion == 'P')
    $pagina = "ABSP4MDSFSDF8V_frm.php";
  else
    $pagina = "GDSFV7YDVHDV78_frm.php";
  header("Location: ".$pagina);
  
?>