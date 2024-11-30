<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_alumno = RecibeParametroNumerico('profile_id');
  if(empty($fl_alumno))
    $fl_alumno = RecibeParametroNumerico('profile_id', True);
	if(empty($fl_alumno))
    $fl_alumno = $fl_usuario;
  
  # Revisa si el alumno esta inscrito en un grupo
  $fl_gurpo = ObtenGrupoAlumno($fl_alumno);
  
  # Inicializa variables
  $Query  = "SELECT ds_login, ds_nombres, ds_apaterno, fg_genero, DATE_FORMAT(fe_nacimiento, '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT(fe_nacimiento, '%e') 'fe_dia_anio', a.ds_email, ds_add_country, fl_zona_horaria, ds_ruta_avatar, ";
  $Query .= "ds_ruta_foto, ds_website, ds_gustos, ds_pasatiempos, a.cl_sesion ";
  $Query .= "FROM c_usuario a, c_alumno b, k_ses_app_frm_1 c ";
  $Query .= "WHERE a.fl_usuario=b.fl_alumno ";
  $Query .= "AND a.cl_sesion=c.cl_sesion ";
  $Query .= "AND fl_usuario=$fl_alumno";
  $row = RecuperaValor($Query);
  $ds_login = str_texto($row[0]);
  $ds_nombres = str_texto($row[1]);
  $ds_apaterno = str_texto($row[2]);
  $fg_genero = $row[3];
  $fe_nacimiento = ObtenNombreMes($row[4])." ".$row[5];
  $ds_email = str_texto($row[6]);
  $fl_pais = $row[7];
  $fl_zona_horaria = $row[8];
  $ds_ruta_avatar = ObtenAvatarUsuario($fl_alumno);
  $ds_ruta_foto = str_texto($row[10]);
  $ds_website = str_texto($row[11]);
  $ds_gustos = str_texto($row[12]);
  $ds_pasatiempos = str_texto($row[13]);
  $cl_sesion = $row[14];
  
  # Presenta datos del alumno
  $titulo = "$ds_nombres $ds_apaterno";
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
  Forma_Inicia($fl_alumno);
  
  # Avatar
  Forma_Sencilla_Ini( );
  echo "<img src='$ds_ruta_avatar' border='none'>";
  Forma_Sencilla_Fin( );
  if($fl_usuario <> $fl_alumno AND !empty($fl_gurpo))
    Forma_CampoInfo("", "<a href='desktop.php?student=$fl_alumno'>$ds_nombres's Desktop</a>");
  
  # Datos generales
  Forma_Seccion(ObtenEtiqueta(419));
  Forma_CampoInfo(ObtenEtiqueta(490), ObtenEtiqueta(424));
  if($fg_genero == 'M')
    Forma_CampoInfo(ObtenEtiqueta(114), ObtenEtiqueta(115));
  else
    Forma_CampoInfo(ObtenEtiqueta(114), ObtenEtiqueta(116));
  Forma_CampoInfo(ObtenEtiqueta(491), $fe_nacimiento);
  Forma_CampoInfo(ObtenEtiqueta(121), "<a href='mailto: $ds_email'>$ds_email</a>");
  $row  = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais=$fl_pais");
  $ds_pais = str_uso_normal($row[0]);
  Forma_CampoInfo(ObtenEtiqueta(287), $ds_pais);
  
  # Foto
  if(!empty($ds_ruta_foto)) {
    Forma_Doble_Ini( );
    echo "<img src='".PATH_ALU_IMAGES."/pictures/$ds_ruta_foto' border='none'>";
    Forma_Doble_Fin( );
  }
  
  # Configuracion personal
  Forma_Seccion(ObtenEtiqueta(408));
  Forma_CampoInfo(ObtenEtiqueta(414), "<a href='http://$ds_website' target='_blank'>$ds_website</a>");
  Forma_CampoInfo(ObtenEtiqueta(415), $ds_gustos);
  Forma_CampoInfo(ObtenEtiqueta(416), $ds_pasatiempos);
  
  # Datos del alumno
  Forma_Seccion(ObtenEtiqueta(406));
  if(!empty($fl_gurpo)) {
    $fl_programa = ObtenProgramaAlumno($fl_alumno);
    $nb_programa = ObtenNombreProgramaAlumno($fl_alumno);
    $nb_gurpo = ObtenNombreGrupoAlumno($fl_alumno);
    $nb_maestro = ObtenNombreMaestroAlumno($fl_alumno);
    $no_grado = ObtenGradoAlumno($fl_alumno);
    $no_semana = ObtenSemanaActualAlumno($fl_alumno);
    $ds_titulo = ObtenTituloLeccion($fl_programa, $no_grado, $no_semana);
    Forma_CampoInfo(ObtenEtiqueta(380), $nb_programa);
    Forma_CampoInfo(ObtenEtiqueta(426), $nb_gurpo);
    Forma_CampoInfo(ObtenEtiqueta(421), $nb_maestro);
    Forma_CampoInfo(ObtenEtiqueta(422), $no_grado);
    Forma_CampoInfo(ObtenEtiqueta(390), $no_semana);
    Forma_CampoInfo(ObtenEtiqueta(493), $ds_titulo);
  }
  else
    Forma_CampoInfo(ObtenEtiqueta(426), 'Any group yet.');
  Forma_Espacio( );
  
  # Boton para regresar
  echo "
                          <tr>
                            <td colspan='2' align='center'>
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