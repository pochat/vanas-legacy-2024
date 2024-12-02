<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametro
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_USUARIOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ds_login, fg_activo, ".ConsultaFechaBD('fe_alta', FMT_FECHA)." fe_alta, ";
      $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
      $Query .= "(".ConcatenaBD($concat).") 'fe_ultacc', ";
      $Query .= "no_accesos, ds_nombres, ds_apaterno, ds_amaterno, ds_email, a.fl_perfil, b.nb_perfil, fg_genero, ";
      $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento ";
      $Query .= "FROM c_usuario a, c_perfil b ";
      $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
      $Query .= "AND fl_usuario=$clave";
      $row = RecuperaValor($Query);
      $ds_login = str_texto($row[0]);
      $fg_activo = $row[1];
      $fe_alta = $row[2];
      $fe_ultacc = $row[3];
      $no_accesos = $row[4];
      $ds_nombres = str_texto($row[5]);
      $ds_apaterno = str_texto($row[6]);
      $ds_amaterno = str_texto($row[7]);
      $ds_email = str_texto($row[8]);
      $fl_perfil = $row[9];
      $nb_perfil = $row[10];
      $fg_genero = $row[11];
      $fe_nacimiento = $row[12];
    }
    else { // Alta, inicializa campos
      $ds_login = "";
      $fg_activo = "1";
      $fe_alta = "";
      $fe_ultacc = "";
      $no_accesos = "";
      $ds_nombres = "";
      $ds_apaterno = "";
      $ds_amaterno = "";
      $ds_email = "";
      $fl_perfil = "";
      $nb_perfil = "";
      $fg_genero = "";
      $fe_nacimiento = "";
    }
    $ds_login_err = "";
    $ds_password_err = "";
    $ds_password_conf_err = "";
    $ds_nombres_err = "";
    $ds_apaterno_err = "";
    $ds_email_err = "";
    $fl_perfil_err = "";
    $fe_nacimiento_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_login = RecibeParametroHTML('ds_login');
    $ds_login_err = RecibeParametroNumerico('ds_login_err');
    $ds_password_err = RecibeParametroNumerico('ds_password_err');
    $ds_password_conf_err = RecibeParametroNumerico('ds_password_conf_err');
    $fg_activo = RecibeParametroBinario('fg_activo');
    $fe_alta = RecibeParametroFecha('fe_alta');
    $fe_ultacc = RecibeParametroFecha('fe_ultacc');
    $no_accesos = RecibeParametroNumerico('no_accesos');
    $ds_nombres = RecibeParametroHTML('ds_nombres');
    $ds_nombres_err = RecibeParametroNumerico('ds_nombres_err');
    $ds_apaterno = RecibeParametroHTML('ds_apaterno');
    $ds_apaterno_err = RecibeParametroNumerico('ds_apaterno_err');
    $ds_amaterno = RecibeParametroHTML('ds_amaterno');
    $ds_email = RecibeParametroHTML('ds_email');
    $ds_email_err = RecibeParametroNumerico('ds_email_err');
    $fl_perfil = RecibeParametroNumerico('fl_perfil');
    $fl_perfil_err = RecibeParametroNumerico('fl_perfil_err');
    $nb_perfil = RecibeParametroHTML('nb_perfil');
    $fg_genero = RecibeParametroHTML('fg_genero');
    $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
    $fe_nacimiento_err = RecibeParametroNumerico('fe_nacimiento_err');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_USUARIOS);
  
  # Forma para cambiar contrasena a otros usuarios
  if(ValidaPermiso(FUNC_PWD_OTROS, PERMISO_EJECUCION)) {
    $ds_cambiar_pwd = "&nbsp;&nbsp;&nbsp;<a href='javascript:cambio_pwd_otros.submit();'>".ObtenEtiqueta(126)."</a>";
    echo "
  <form name='cambio_pwd_otros' method='post' action='pwd_frm.php'>
    <input type='hidden' name='clave' value='$clave'>
  </form>\n";
  }
  else
    $ds_cambiar_pwd = "";
  
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Revisa si es un registro nuevo
  if(empty($clave)) {
    Forma_CampoTexto(ETQ_USUARIO, True, 'ds_login', $ds_login, 16, 16, $ds_login_err);
    Forma_CampoTexto(ObtenEtiqueta(123), True, 'ds_password', '', 16, 16, $ds_password_err, True);
    Forma_CampoTexto(ObtenEtiqueta(124), True, 'ds_password_conf', '', 16, 16, $ds_password_conf_err, True);
  }
  else {
    Forma_CampoInfo(ETQ_USUARIO, $ds_login.$ds_cambiar_pwd);
    Forma_CampoOculto('ds_login' , $ds_login);
  }
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_nombres', $ds_nombres, 100, 32, $ds_nombres_err);
  Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_apaterno', $ds_apaterno, 50, 32, $ds_apaterno_err);
  Forma_CampoTexto(ObtenEtiqueta(119), False, 'ds_amaterno', $ds_amaterno, 50, 32, '');
  Forma_Espacio( );
  $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116)); // Marculino, Femenino
  $val = array('M', 'F');
  Forma_CampoSelect(ObtenEtiqueta(114), False, 'fg_genero', $opc, $val, $fg_genero);
  Forma_CampoTexto(ObtenEtiqueta(120).' '.ETQ_FMT_FECHA, False, 'fe_nacimiento', $fe_nacimiento, 10, 10, $fe_nacimiento_err);
  Forma_Calendario('fe_nacimiento');
  Forma_CampoTexto(ObtenEtiqueta(121), True, 'ds_email', $ds_email, 64, 32, $ds_email_err);
  Forma_Espacio( );
  Forma_CampoLOV(ObtenEtiqueta(110), True, 'fl_perfil', $fl_perfil, 'nb_perfil', $nb_perfil, 30, 
    LOV_PERFILES, LOV_TIPO_RADIO, LOV_MEDIANO, '', $fl_perfil_err);
  Forma_CampoCheckbox(ObtenEtiqueta(113), 'fg_activo', $fg_activo);
  
  # Estadisticas del usuario, solo en modo de edicion
  if(!empty($clave)) {
    Forma_Espacio( );
    Forma_CampoInfo(ObtenEtiqueta(111), $fe_alta);
    Forma_CampoOculto('fe_alta', $fe_alta);
    Forma_CampoInfo(ObtenEtiqueta(112), $fe_ultacc);
    Forma_CampoOculto('fe_ultacc', $fe_ultacc);
    Forma_CampoInfo(ObtenEtiqueta(122), $no_accesos);
    Forma_CampoOculto('no_accesos', $no_accesos);
  }
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_USUARIOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>