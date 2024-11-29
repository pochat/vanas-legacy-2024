<?php

  # Libreria de funciones
  require("modules/common/lib/cam_general.inc.php");

  # Recibe parametros
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
	$cl_sesion = RecibeParametroHTML('cl_sesion');
	$fl_perfil = RecibeParametroNumerico('fl_perfil');
	$fg_rm = RecibeParametroBinario('fg_rm');
	$fg_campus = RecibeParametroNumerico('fg_campus');
  $ori = RecibeParametroHTML('ori');

$Query = "SELECT fg_activo FROM c_usuario WHERE fl_usuario=$fl_usuario  ";
$row = RecuperaValor($Query);
$fg_activo = $row['fg_activo'];

	# Verificamos si es campus o FAME
  $Fame = 0;
  if($fl_perfil == PFL_ADMINISTRADOR || $fl_perfil == PFL_MAESTRO_SELF || $fl_perfil == PFL_ESTUDIANTE_SELF || $fl_perfil==PFL_ADM_CSF )
    $Fame = 1;
	# Genera una nueva sesion
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
  $id_sesion = "";
  for($i = 0; $i < 32; $i++)
    $id_sesion .= substr($str, rand(0,62), 1);
  $id_sesion = sha256("form_sess para ".$id_sesion.$fl_usuario);

	# Actualiza estadisticas de acceso del usuario
  EjecutaQuery("UPDATE c_usuario SET fe_ultacc=CURRENT_TIMESTAMP, no_accesos=no_accesos+1, cl_sesion='$id_sesion', fg_remember_me='0' WHERE cl_sesion='$cl_sesion'");
	EjecutaQuery("UPDATE c_sesion SET cl_sesion='$id_sesion' WHERE cl_sesion='$cl_sesion'");
	EjecutaQuery("UPDATE k_ses_app_frm_1 SET cl_sesion='$id_sesion' WHERE cl_sesion='$cl_sesion'");
	EjecutaQuery("UPDATE k_ses_app_frm_2 SET cl_sesion='$id_sesion' WHERE cl_sesion='$cl_sesion'");
	EjecutaQuery("UPDATE k_ses_app_frm_3 SET cl_sesion='$id_sesion' WHERE cl_sesion='$cl_sesion'");
	EjecutaQuery("UPDATE k_ses_app_frm_4 SET cl_sesion='$id_sesion' WHERE cl_sesion='$cl_sesion'");
	EjecutaQuery("UPDATE k_app_contrato SET cl_sesion='$id_sesion' WHERE cl_sesion='$cl_sesion'");
	EjecutaQuery("UPDATE k_presponsable set cl_sesion='$id_sesion' WHERE cl_sesion='$cl_sesion'");
  EjecutaQuery("INSERT INTO k_usu_login (fl_usuario, fe_login) VALUES($fl_usuario, CURRENT_TIMESTAMP)");

	# Valida si se selecciono Remember me
	if(!empty($fg_rm)) {
		setcookie(SESION_RM, $id_sesion, time( )+SESION_VIGENCIA_RM, "/");
		EjecutaQuery("UPDATE c_usuario SET fg_remember_me='1' WHERE cl_sesion='$id_sesion'");
	}
	else
		setcookie(SESION_RM, '', time( )+SESION_VIGENCIA_RM, "/");

  # Redirige a la pagina inicial de acuerdo al perfil del usuario
  # Redireccionamos a fame o campus
  if(empty($Fame)){
    if($fl_perfil == PFL_ESTUDIANTE){
      if(!empty($fg_campus) && $fg_campus == 1){

           #si es alumno inactivo se redirige al paymentHistory
            if ($fg_activo == '1') {
                $pag = PATH_N_ALU . "/index.php#ajax/desktop.php";
            } else {
                $pag = PATH_N_ALU . "/index.php#ajax/home.php";
            }






        # Si viene de algn otro lado y no tiene sesion activada
        if(!empty($ori))
          $pag = PATH_N_ALU."/index.php#$ori";
      } else {
        $pag = PAGINA_INI_ALU;
      }
    }
    else{
      if(!empty($fg_campus) && $fg_campus == 1){
        $pag = PATH_N_MAE."/index.php#ajax/home.php";
        # Si viene de algn otro lado y no tiene sesion activada
        if(!empty($ori))
          $pag = PATH_N_MAE."/index.php#$ori";
      } else {
        $pag = PAGINA_INI_MAE;
      }
    }
    $p_self = False;
  }
  else{
    $p_self = True;
    $pag = PATH_SELF."/index.php#site/fame_feed.php";
  }
	ActualizaDiferenciaGMT($fl_perfil, $fl_usuario);

  # Crea cookie con identificador de sesion y redirige al home del sistema
  ActualizaSesion($id_sesion, False, 0, $p_self);

  header("Location: ".$pag);

?>