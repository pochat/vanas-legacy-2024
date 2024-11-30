<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = RecibeParametroNumerico('fg_error');
	
  # Inicializa variables
  if(!$fg_error) { // Sin error, entra por primera vez
    $Query  = "SELECT ds_login, ds_nombres, ds_apaterno, ds_amaterno, fg_genero, ";
    $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, ds_email, fl_pais, fl_zona_horaria, ds_ruta_avatar, ds_ruta_foto, ";
    $Query .= "ds_empresa, ds_website, ds_gustos, ds_pasatiempos, ds_biografia ";
    $Query .= "FROM c_usuario a, c_maestro b ";
    $Query .= "WHERE a.fl_usuario=b.fl_maestro ";
    $Query .= "AND fl_usuario=$fl_maestro";
    $row = RecuperaValor($Query);
    $ds_login = str_texto($row[0]);
    $ds_nombres = str_texto($row[1]);
    $ds_apaterno = str_texto($row[2]);
    $ds_amaterno = str_texto($row[3]);
    $fg_genero = $row[4];
    $fe_nacimiento = $row[5];
    $ds_email = str_texto($row[6]);
    $fl_pais = $row[7];
    $fl_zona_horaria = $row[8];
    $ds_ruta_avatar = str_texto($row[9]);
    $ds_ruta_foto = str_texto($row[10]);
    $ds_empresa = str_texto($row[11]);
    $ds_website = str_texto($row[12]);
    $ds_gustos = str_texto($row[13]);
    $ds_pasatiempos = str_texto($row[14]);
    $ds_biografia = str_texto($row[15]);
    $ds_nombres_err = "";
    $ds_apaterno_err = "";
    $fe_nacimiento_err = "";
    $ds_email_err = "";
    $ds_ruta_avatar_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_login = RecibeParametroHTML('ds_login');
    $ds_nombres = RecibeParametroHTML('ds_nombres');
    $ds_nombres_err = RecibeParametroNumerico('ds_nombres_err');
    $ds_apaterno = RecibeParametroHTML('ds_apaterno');
    $ds_apaterno_err = RecibeParametroNumerico('ds_apaterno_err');
    $ds_amaterno = RecibeParametroHTML('ds_amaterno');
    $ds_password_err = RecibeParametroNumerico('ds_password_err');
    $fg_genero = RecibeParametroHTML('fg_genero');
    $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
    $fe_nacimiento_err = RecibeParametroNumerico('fe_nacimiento_err');
    $ds_email = RecibeParametroHTML('ds_email');
    $ds_email_err = RecibeParametroNumerico('ds_email_err');
    $fl_pais = RecibeParametroNumerico('fl_pais');
    $fl_zona_horaria = RecibeParametroNumerico('fl_zona_horaria');
    $ds_ruta_avatar = RecibeParametroHTML('ds_ruta_avatar');
    $ds_ruta_avatar_err = RecibeParametroHTML('ds_ruta_avatar_err');
    $ds_ruta_foto = RecibeParametroHTML('ds_ruta_foto');
    $ds_ruta_foto_err = RecibeParametroHTML('ds_ruta_foto_err');
    $ds_empresa = RecibeParametroHTML('ds_empresa');
    $ds_website = RecibeParametroHTML('ds_website');
    $ds_gustos = RecibeParametroHTML('ds_gustos');
    $ds_pasatiempos = RecibeParametroHTML('ds_pasatiempos');
    $ds_biografia = RecibeParametroHTML('ds_biografia');
  }
  
  # Presenta datos del maestro
  $titulo = "My Profile";
  PresentaHeader($titulo);
  echo "
              <tr>
                <td colspan='2' valign='top' class='blank_cells'>&nbsp;</td>
              </tr>
              <tr>
                <td colspan='2' height='568' valign='top' class='division_line'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%' class='blank_cells'>
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td>";
  
  # Inicia forma de captura
  Forma_Inicia($fl_maestro, True, 'profile_iu.php');
  Forma_CampoInfo('', "<a href='profile_teacher.php?profile_id=$fl_maestro'>View my Profile</a>");
  
  # Presenta mensaje de error
  if($fg_error)
    Forma_PresentaError( );
  
  # Datos generales
  Forma_Seccion(ObtenEtiqueta(419));
  Forma_CampoInfo(ETQ_USUARIO, $ds_login);
  Forma_CampoOculto('ds_login', $ds_login);
  Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_nombres', $ds_nombres, 100, 40, $ds_nombres_err);
  Forma_CampoTexto(ObtenEtiqueta(119), False, 'ds_amaterno', $ds_amaterno, 50, 40);
  Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_apaterno', $ds_apaterno, 50, 40, $ds_apaterno_err);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(125), False, 'ds_password', '', 100, 40, $ds_password_err, True);
  Forma_CampoTexto(ObtenEtiqueta(124), False, 'ds_password_conf', '', 100, 40, '', True);
  Forma_Espacio( );
  $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116)); // Marculino, Femenino
  $val = array('M', 'F');
  Forma_CampoSelect(ObtenEtiqueta(114), False, 'fg_genero', $opc, $val, $fg_genero);
  Forma_CampoTexto(ObtenEtiqueta(120).' '.ETQ_FMT_FECHA, False, 'fe_nacimiento', $fe_nacimiento, 10, 10, $fe_nacimiento_err);
  Forma_Calendario('fe_nacimiento');
  Forma_CampoTexto(ObtenEtiqueta(121), True, 'ds_email', $ds_email, 64, 40, $ds_email_err);
  
  # Configuracion personal
  Forma_Seccion(ObtenEtiqueta(410));
  $Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
  Forma_CampoSelectBD(ObtenEtiqueta(287), False, 'fl_pais', $Query, $fl_pais);
  $concat = array('nb_zona_horaria', "' (GMT '", no_gmt, "')'");
  $Query  = "SELECT (".ConcatenaBD($concat).") 'ds_zona', fl_zona_horaria FROM c_zona_horaria ORDER BY nb_zona_horaria";
  Forma_CampoSelectBD(ObtenEtiqueta(411), False, 'fl_zona_horaria', $Query, $fl_zona_horaria);
  $avatar_size = ObtenConfiguracion(30);
  $desc = "JPEG ".$avatar_size."x".$avatar_size;
  $ruta = PATH_MAE_IMAGES."/avatars";
  Forma_CampoUpload(ObtenEtiqueta(412), $desc, 'ds_ruta_avatar', $ds_ruta_avatar, $ruta, False, 'avatar', 60, $ds_ruta_avatar_err, 'jpg|jpeg');
  $desc = "JPEG";
  $ruta = PATH_MAE_IMAGES."/pictures";
  Forma_CampoUpload(ObtenEtiqueta(413), $desc, 'ds_ruta_foto', $ds_ruta_foto, $ruta, False, 'foto', 60, $ds_ruta_foto_err, 'jpg|jpeg');
  
  # Informacion profesional
  Forma_Seccion(ObtenEtiqueta(408));
  Forma_CampoTexto(ObtenEtiqueta(418), False, 'ds_empresa', $ds_empresa, 255, 40);
  Forma_CampoTexto(ObtenEtiqueta(414), False, 'ds_website', $ds_website, 255, 40);
  Forma_CampoTextArea(ObtenEtiqueta(415), False, 'ds_gustos', $ds_gustos, 80, 3);
  Forma_CampoTextArea(ObtenEtiqueta(416), False, 'ds_pasatiempos', $ds_pasatiempos, 80, 3);
  Forma_CampoTextArea(ObtenEtiqueta(417), False, 'ds_biografia', $ds_biografia, 80, 3);
  
  # Programas del maestro
  Forma_Seccion(ObtenEtiqueta(409));
  Forma_Sencilla_Ini( );
  $Query  = "SELECT DISTINCT c.nb_programa, a.nb_grupo ";
  $Query .= "FROM c_grupo a, k_term b, c_programa c ";
  $Query .= "WHERE a.fl_term=b.fl_term ";
  $Query .= "AND b.fl_programa=c.fl_programa ";
  $Query .= "AND a.fl_maestro=$fl_maestro ";
  $rs = EjecutaQuery($Query);
  echo "<table border='".D_BORDES."' cellpadding='3' cellspacing='0' width='80%'>";
  while($row = RecuperaRegistro($rs)) {
    echo "
      <tr>
        <td>".str_uso_normal($row[0])."</td>
        <td>".str_uso_normal($row[1])."</td>
      </tr>";
  }
  echo "</table>";
  Forma_Sencilla_Fin( );
  Forma_Espacio( );
  
  # Boton para guardar cambios
  echo "
                          <tr>
                            <td colspan='2' align='center'>
                              <button type='button' id='buttons' OnClick='javascript:document.datos.submit();'>
                              &nbsp;&nbsp;".ObtenEtiqueta(13)."&nbsp;&nbsp;
                              </button>
                              &nbsp;&nbsp;
                              <button type='button' id='buttons' OnClick='javascript:history.go(-1);'>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              </button>
                            </td>
                          </tr>";
  
  # Cierra la forma de captura
  Forma_Termina( );
  echo "
                      </td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' height='10'>&nbsp;</td>
                    </tr>";
  
  PresentaFooter( );
  
?>