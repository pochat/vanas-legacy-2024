<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  require("../lib/sp_forms.inc.php");
  require("app_form.inc.php");
  
  # Recupera sesion del cookie
  $clave = SP_RecuperaSesion( );
  
  # Si no es una sesion valida redirige a la forma inicial
  if(empty($clave)) {
    header("Location: ABSP4MDSFSDF8V_frm.php");
    exit;
  }
  
  # Reinicia la sesion
  SP_ActualizaSesion($clave);
  
  # Recibe parametro
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, revisa si ya hay datos para la sesion
    $Query  = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
    $Query .= "FROM k_ses_app_frm_3 ";
    $Query .= "WHERE cl_sesion='$clave'";
    $row = RecuperaValor($Query);
    $ds_resp_1 = str_texto($row[0]);
    $ds_resp_2_1 = str_texto($row[1]);
    $ds_resp_2_2 = str_texto($row[2]);
    $ds_resp_2_3 = str_texto($row[3]);
    $ds_resp_3 = str_texto($row[4]);
    $ds_resp_4 = str_texto($row[5]);
    $ds_resp_5 = str_texto($row[6]);
    $ds_resp_6 = str_texto($row[7]);
    $ds_resp_7 = str_texto($row[8]);
    $ds_resp_8 = str_texto($row[9]);
    $ds_resp_1_err = "";
    $ds_resp_2_1_err = "";
    $ds_resp_2_2_err = "";
    $ds_resp_2_3_err = "";
    $ds_resp_3_err = "";
    $ds_resp_4_err = "";
    $ds_resp_5_err = "";
    $ds_resp_6_err = "";
    $ds_resp_7_err = "";
    $ds_resp_8_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_resp_1 = RecibeParametroHTML('ds_resp_1');
    $ds_resp_1_err = RecibeParametroNumerico('ds_resp_1_err');
    $ds_resp_2_1 = RecibeParametroHTML('ds_resp_2_1');
    $ds_resp_2_1_err = RecibeParametroNumerico('ds_resp_2_1_err');
    $ds_resp_2_2 = RecibeParametroHTML('ds_resp_2_2');
    $ds_resp_2_2_err = RecibeParametroNumerico('ds_resp_2_2_err');
    $ds_resp_2_3 = RecibeParametroHTML('ds_resp_2_3');
    $ds_resp_2_3_err = RecibeParametroNumerico('ds_resp_2_3_err');
    $ds_resp_3 = RecibeParametroHTML('ds_resp_3');
    $ds_resp_3_err = RecibeParametroNumerico('ds_resp_3_err');
    $ds_resp_4 = RecibeParametroHTML('ds_resp_4');
    $ds_resp_4_err = RecibeParametroNumerico('ds_resp_4_err');
    $ds_resp_5 = RecibeParametroHTML('ds_resp_5');
    $ds_resp_5_err = RecibeParametroNumerico('ds_resp_5_err');
    $ds_resp_6 = RecibeParametroHTML('ds_resp_6');
    $ds_resp_6_err = RecibeParametroNumerico('ds_resp_6_err');
    $ds_resp_7 = RecibeParametroHTML('ds_resp_7');
    $ds_resp_7_err = RecibeParametroNumerico('ds_resp_7_err');
    $ds_resp_8 = RecibeParametroHTML('ds_resp_8');
    $ds_resp_8_err = RecibeParametroNumerico('ds_resp_8_err');
  }
  
  # Header
  PresentaHeaderAF( );
  
  # Cuerpo de la pagina
  echo "
    <table border='".D_BORDES."' width='100%' height='584' valign='top' cellspacing='0' cellpadding='0' class='app_form'>
      <tr>
        <td width='20' height='20'>&nbsp;</td>
        <td>&nbsp;</td>
        <td width='20'>&nbsp;</td>
      </tr>
      <tr>
        <td height='30'>&nbsp;</td>
        <td><b>".ObtenEtiqueta(57)."</b></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><br>".ObtenEtiqueta(71)."</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td valign='top'>\n";
  
  # Inicia la forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoOculto('direccion', 'N');
  Forma_Espacio( );
  
  # Preguntas
  Forma_CampoTextArea(ObtenEtiqueta(308), True, 'ds_resp_1', $ds_resp_1, 50, 3, $ds_resp_1_err, True, False);
  Forma_PromptDoble(ObtenEtiqueta(309));
  Forma_CampoTexto('1', True, 'ds_resp_2_1', $ds_resp_2_1, 100, 50, $ds_resp_2_1_err);
  Forma_CampoTexto('2', True, 'ds_resp_2_2', $ds_resp_2_2, 100, 50, $ds_resp_2_2_err);
  Forma_CampoTexto('3', True, 'ds_resp_2_3', $ds_resp_2_3, 100, 50, $ds_resp_2_3_err);
  Forma_CampoTextArea(ObtenEtiqueta(310), True, 'ds_resp_3', $ds_resp_3, 50, 3, $ds_resp_3_err, True, False);
  Forma_CampoTextArea(ObtenEtiqueta(311), True, 'ds_resp_4', $ds_resp_4, 50, 3, $ds_resp_4_err, True, False);
  Forma_CampoTextArea(ObtenEtiqueta(312), True, 'ds_resp_5', $ds_resp_5, 50, 3, $ds_resp_5_err, True, False);
  Forma_PromptDoble(ObtenEtiqueta(313), True);
  Forma_Error($ds_resp_6_err);
  Forma_CampoRadio('', 'ds_resp_6', 'A', $ds_resp_6, ObtenEtiqueta(314));
  Forma_CampoRadio('', 'ds_resp_6', 'B', $ds_resp_6, ObtenEtiqueta(315));
  Forma_CampoRadio('', 'ds_resp_6', 'C', $ds_resp_6, ObtenEtiqueta(316));
  Forma_PromptDoble(ObtenEtiqueta(317), True);
  Forma_Error($ds_resp_7_err);
  Forma_CampoRadio('', 'ds_resp_7', 'A', $ds_resp_7, ObtenEtiqueta(318));
  Forma_CampoRadio('', 'ds_resp_7', 'B', $ds_resp_7, ObtenEtiqueta(319));
  Forma_CampoRadio('', 'ds_resp_7', 'C', $ds_resp_7, ObtenEtiqueta(320));
  Forma_CampoRadio('', 'ds_resp_7', 'D', $ds_resp_7, ObtenEtiqueta(321));
  Forma_CampoRadio('', 'ds_resp_7', 'E', $ds_resp_7, ObtenEtiqueta(322));
  Forma_CampoTextArea(ObtenEtiqueta(323), True, 'ds_resp_8', $ds_resp_8, 50, 3, $ds_resp_8_err, True, False);
  Forma_Espacio( );
  
  # Cierra la forma de captura
  Forma_Sencilla_Ini( );
  echo "<script language='javascript'>
    function EnviaForma(opc) {
      document.datos.direccion.value = opc;
      document.datos.submit();
    }
  </script>
  <button type='button' id='buttons' OnClick=\"javascript:EnviaForma('P');\">".ObtenEtiqueta(40)."</button>&nbsp;&nbsp;&nbsp;<button type='button' id='buttons' OnClick=\"javascript:EnviaForma('N');\">".ObtenEtiqueta(41)."</button>\n";
  Forma_Sencilla_Fin( );
  Forma_Termina( );
  echo "
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
    </table>";
  
  # Footer
  PresentaFooterAF( );
  
?>