<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_maestro = RecibeParametroNumerico('profile_id');
	if(empty($fl_maestro))
    $fl_maestro = RecibeParametroNumerico('profile_id', True);
	if(empty($fl_maestro))
    $fl_maestro = $fl_usuario;
  
  # Inicializa variables
  $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno, fg_genero, DATE_FORMAT(fe_nacimiento, '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT(fe_nacimiento, '%e') 'fe_dia_anio', ds_email, fl_pais, fl_zona_horaria, ds_ruta_avatar, ds_ruta_foto, ";
  $Query .= "ds_empresa, ds_website, ds_gustos, ds_pasatiempos, ds_biografia ";
  $Query .= "FROM c_usuario a, c_maestro b ";
  $Query .= "WHERE a.fl_usuario=b.fl_maestro ";
  $Query .= "AND fl_usuario=$fl_maestro";
  $row = RecuperaValor($Query);
  $ds_nombres = str_uso_normal($row[0]);
  $ds_apaterno = str_uso_normal($row[1]);
  $ds_amaterno = str_uso_normal($row[2]);
  $fg_genero = $row[3];
  $fe_nacimiento = ObtenNombreMes($row[4])." ".$row[5];
  $ds_email = str_uso_normal($row[6]);
  $fl_pais = $row[7];
  $fl_zona_horaria = $row[8];
  $ds_ruta_avatar = ObtenAvatarUsuario($fl_maestro);
  $ds_ruta_foto = str_uso_normal($row[10]);
  $ds_empresa = str_uso_normal($row[11]);
  $ds_website = str_uso_normal($row[12]);
  $ds_gustos = str_uso_normal($row[13]);
  $ds_pasatiempos = str_uso_normal($row[14]);
  $ds_biografia = str_uso_normal($row[15]);
  
  # Presenta datos del maestro
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
  Forma_Inicia($fl_maestro);
  
  # Avatar
  Forma_Sencilla_Ini( );
  echo "<img src='$ds_ruta_avatar' border='none'>";
  Forma_Sencilla_Fin( );
  
  # Datos generales
  Forma_Seccion(ObtenEtiqueta(419));
  Forma_CampoInfo(ObtenEtiqueta(490), ObtenEtiqueta(421));
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
    echo "<img src='".PATH_MAE_IMAGES."/pictures/$ds_ruta_foto' border='none'>";
    Forma_Doble_Fin( );
  }
  
  # Informacion profesional
  Forma_Seccion(ObtenEtiqueta(408));
  Forma_CampoInfo(ObtenEtiqueta(418), $ds_empresa);
  Forma_CampoInfo(ObtenEtiqueta(414), "<a href='http://$ds_website' target='_blank'>$ds_website</a>");
  Forma_CampoInfo(ObtenEtiqueta(415), $ds_gustos);
  Forma_CampoInfo(ObtenEtiqueta(416), $ds_pasatiempos);
  Forma_CampoInfo(ObtenEtiqueta(417), $ds_biografia);
  
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