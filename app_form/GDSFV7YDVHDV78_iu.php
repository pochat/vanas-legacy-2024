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
  $fg_resp_1_1 = RecibeParametroHTML('fg_resp_1_1');
  $fg_resp_1_2 = RecibeParametroHTML('fg_resp_1_2');
  $fg_resp_1_3 = RecibeParametroHTML('fg_resp_1_3');
  $fg_resp_1_4 = RecibeParametroHTML('fg_resp_1_4');
  $fg_resp_1_5 = RecibeParametroHTML('fg_resp_1_5');
  $fg_resp_1_6 = RecibeParametroHTML('fg_resp_1_6');
  $fg_resp_2_1 = RecibeParametroHTML('fg_resp_2_1');
  $fg_resp_2_2 = RecibeParametroHTML('fg_resp_2_2');
  $fg_resp_2_3 = RecibeParametroHTML('fg_resp_2_3');
  $fg_resp_2_4 = RecibeParametroHTML('fg_resp_2_4');
  $fg_resp_2_5 = RecibeParametroHTML('fg_resp_2_5');
  $fg_resp_2_6 = RecibeParametroHTML('fg_resp_2_6');
  $fg_resp_2_7 = RecibeParametroHTML('fg_resp_2_7');
  $fg_resp_3_1 = RecibeParametroHTML('fg_resp_3_1');
  $fg_resp_3_2 = RecibeParametroHTML('fg_resp_3_2');
  
  # Valida campos obligatorios
  if($fg_resp_1_1 == "")
    $fg_resp_1_1_err = ERR_REQUERIDO;
  if($fg_resp_1_2 == "")
    $fg_resp_1_2_err = ERR_REQUERIDO;
  if($fg_resp_1_3 == "")
    $fg_resp_1_3_err = ERR_REQUERIDO;
  if($fg_resp_1_4 == "")
    $fg_resp_1_4_err = ERR_REQUERIDO;
  if($fg_resp_1_5 == "")
    $fg_resp_1_5_err = ERR_REQUERIDO;
  if($fg_resp_1_6 == "")
    $fg_resp_1_6_err = ERR_REQUERIDO;
  if($fg_resp_2_1 == "")
    $fg_resp_2_1_err = ERR_REQUERIDO;
  if($fg_resp_2_2 == "")
    $fg_resp_2_2_err = ERR_REQUERIDO;
  if($fg_resp_2_3 == "")
    $fg_resp_2_3_err = ERR_REQUERIDO;
  if($fg_resp_2_4 == "")
    $fg_resp_2_4_err = ERR_REQUERIDO;
  if($fg_resp_2_5 == "")
    $fg_resp_2_5_err = ERR_REQUERIDO;
  if($fg_resp_2_6 == "")
    $fg_resp_2_6_err = ERR_REQUERIDO;
  if($fg_resp_2_7 == "")
    $fg_resp_2_7_err = ERR_REQUERIDO;
  if($fg_resp_3_1 == "")
    $fg_resp_3_1_err = ERR_REQUERIDO;
  if($fg_resp_3_2 == "")
    $fg_resp_3_2_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $fg_resp_1_1_err || $fg_resp_1_2_err || $fg_resp_1_3_err || $fg_resp_1_4_err || $fg_resp_1_5_err || $fg_resp_1_6_err;
  $fg_error = $fg_error || $fg_resp_2_1_err || $fg_resp_2_2_err || $fg_resp_2_3_err || $fg_resp_2_4_err || $fg_resp_2_5_err || $fg_resp_2_6_err || $fg_resp_2_7_err;
  $fg_error = $fg_error || $fg_resp_3_1_err || $fg_resp_3_2_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('fg_resp_1_1' , $fg_resp_1_1);
    Forma_CampoOculto('fg_resp_1_1_err' , $fg_resp_1_1_err);
    Forma_CampoOculto('fg_resp_1_2' , $fg_resp_1_2);
    Forma_CampoOculto('fg_resp_1_2_err' , $fg_resp_1_2_err);
    Forma_CampoOculto('fg_resp_1_3' , $fg_resp_1_3);
    Forma_CampoOculto('fg_resp_1_3_err' , $fg_resp_1_3_err);
    Forma_CampoOculto('fg_resp_1_4' , $fg_resp_1_4);
    Forma_CampoOculto('fg_resp_1_4_err' , $fg_resp_1_4_err);
    Forma_CampoOculto('fg_resp_1_5' , $fg_resp_1_5);
    Forma_CampoOculto('fg_resp_1_5_err' , $fg_resp_1_5_err);
    Forma_CampoOculto('fg_resp_1_6' , $fg_resp_1_6);
    Forma_CampoOculto('fg_resp_1_6_err' , $fg_resp_1_6_err);
    Forma_CampoOculto('fg_resp_2_1' , $fg_resp_2_1);
    Forma_CampoOculto('fg_resp_2_1_err' , $fg_resp_2_1_err);
    Forma_CampoOculto('fg_resp_2_2' , $fg_resp_2_2);
    Forma_CampoOculto('fg_resp_2_2_err' , $fg_resp_2_2_err);
    Forma_CampoOculto('fg_resp_2_3' , $fg_resp_2_3);
    Forma_CampoOculto('fg_resp_2_3_err' , $fg_resp_2_3_err);
    Forma_CampoOculto('fg_resp_2_4' , $fg_resp_2_4);
    Forma_CampoOculto('fg_resp_2_4_err' , $fg_resp_2_4_err);
    Forma_CampoOculto('fg_resp_2_5' , $fg_resp_2_5);
    Forma_CampoOculto('fg_resp_2_5_err' , $fg_resp_2_5_err);
    Forma_CampoOculto('fg_resp_2_6' , $fg_resp_2_6);
    Forma_CampoOculto('fg_resp_2_6_err' , $fg_resp_2_6_err);
    Forma_CampoOculto('fg_resp_2_7' , $fg_resp_2_7);
    Forma_CampoOculto('fg_resp_2_7_err' , $fg_resp_2_7_err);
    Forma_CampoOculto('fg_resp_3_1' , $fg_resp_3_1);
    Forma_CampoOculto('fg_resp_3_1_err' , $fg_resp_3_1_err);
    Forma_CampoOculto('fg_resp_3_2' , $fg_resp_3_2);
    Forma_CampoOculto('fg_resp_3_2_err' , $fg_resp_3_2_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza los datos de la forma para la sesion
  $Query  = "UPDATE c_sesion SET fg_app_4='1', fe_ultmod=CURRENT_TIMESTAMP ";
  $Query .= "WHERE cl_sesion='$clave'";
  EjecutaQuery($Query);
  
  # Verifica si se esta insertando
  $row = RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_4 WHERE cl_sesion='$clave'");
  if(empty($row[0]))
    $fg_nueva = True;
  else
    $fg_nueva = False;
  
  # Inserta o actualiza los datos de la forma para la sesion
  if($fg_nueva) {
    $Query  = "INSERT INTO k_ses_app_frm_4 ";
    $Query .= "(cl_sesion, fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, fg_resp_2_1, fg_resp_2_2, ";
    $Query .= "fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, fg_resp_3_1, fg_resp_3_2, fe_ultmod) ";
    $Query .= "VALUES ('$cl_sesion', ";
    $Query .= "'$fg_resp_1_1', '$fg_resp_1_2', '$fg_resp_1_3', '$fg_resp_1_4', '$fg_resp_1_5', '$fg_resp_1_6', '$fg_resp_2_1', '$fg_resp_2_2', ";
    $Query .= "'$fg_resp_2_3', '$fg_resp_2_4', '$fg_resp_2_5', '$fg_resp_2_6', '$fg_resp_2_7', '$fg_resp_3_1', '$fg_resp_3_2', ";
    $Query .= "CURRENT_TIMESTAMP)";
  }
  else {
    $Query  = "UPDATE k_ses_app_frm_4 SET fg_resp_1_1='$fg_resp_1_1', fg_resp_1_2='$fg_resp_1_2', fg_resp_1_3='$fg_resp_1_3', ";
    $Query .= "fg_resp_1_4='$fg_resp_1_4', fg_resp_1_5='$fg_resp_1_5', fg_resp_1_6='$fg_resp_1_6', fg_resp_2_1='$fg_resp_2_1', ";
    $Query .= "fg_resp_2_2='$fg_resp_2_2', fg_resp_2_3='$fg_resp_2_3', fg_resp_2_4='$fg_resp_2_4', fg_resp_2_5='$fg_resp_2_5', ";
    $Query .= "fg_resp_2_6='$fg_resp_2_6', fg_resp_2_7='$fg_resp_2_7', fg_resp_3_1='$fg_resp_3_1', fg_resp_3_2='$fg_resp_3_2', ";
    $Query .= "fe_ultmod=CURRENT_TIMESTAMP ";
    $Query .= "WHERE cl_sesion='$clave'";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  if($direccion == 'P')
    $pagina = "BDARC876XCS2FU9_frm.php";
  else
    $pagina = "CHDSF776RSDV85_frm.php";
  header("Location: ".$pagina);
  
?>