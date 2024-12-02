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
    $Query  = "SELECT fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
    $Query .= "fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, fg_resp_3_1, fg_resp_3_2 ";
    $Query .= "FROM k_ses_app_frm_4 ";
    $Query .= "WHERE cl_sesion='$clave'";
    $row = RecuperaValor($Query);
    $fg_resp_1_1 = $row[0];
    $fg_resp_1_2 = $row[1];
    $fg_resp_1_3 = $row[2];
    $fg_resp_1_4 = $row[3];
    $fg_resp_1_5 = $row[4];
    $fg_resp_1_6 = $row[5];
    $fg_resp_2_1 = $row[6];
    $fg_resp_2_2 = $row[7];
    $fg_resp_2_3 = $row[8];
    $fg_resp_2_4 = $row[9];
    $fg_resp_2_5 = $row[10];
    $fg_resp_2_6 = $row[11];
    $fg_resp_2_7 = $row[12];
    $fg_resp_3_1 = $row[13];
    $fg_resp_3_2 = $row[14];
    $fg_resp_1_1_err = "";
    $fg_resp_1_2_err = "";
    $fg_resp_1_3_err = "";
    $fg_resp_1_4_err = "";
    $fg_resp_1_5_err = "";
    $fg_resp_1_6_err = "";
    $fg_resp_2_1_err = "";
    $fg_resp_2_2_err = "";
    $fg_resp_2_3_err = "";
    $fg_resp_2_4_err = "";
    $fg_resp_2_5_err = "";
    $fg_resp_2_6_err = "";
    $fg_resp_2_7_err = "";
    $fg_resp_3_1_err = "";
    $fg_resp_3_2_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $fg_resp_1_1 = RecibeParametroHTML('fg_resp_1_1');
    $fg_resp_1_1_err = RecibeParametroNumerico('fg_resp_1_1_err');
    $fg_resp_1_2 = RecibeParametroHTML('fg_resp_1_2');
    $fg_resp_1_2_err = RecibeParametroNumerico('fg_resp_1_2_err');
    $fg_resp_1_3 = RecibeParametroHTML('fg_resp_1_3');
    $fg_resp_1_3_err = RecibeParametroNumerico('fg_resp_1_3_err');
    $fg_resp_1_4 = RecibeParametroHTML('fg_resp_1_4');
    $fg_resp_1_4_err = RecibeParametroNumerico('fg_resp_1_4_err');
    $fg_resp_1_5 = RecibeParametroHTML('fg_resp_1_5');
    $fg_resp_1_5_err = RecibeParametroNumerico('fg_resp_1_5_err');
    $fg_resp_1_6 = RecibeParametroHTML('fg_resp_1_6');
    $fg_resp_1_6_err = RecibeParametroNumerico('fg_resp_1_6_err');
    $fg_resp_2_1 = RecibeParametroHTML('fg_resp_2_1');
    $fg_resp_2_1_err = RecibeParametroNumerico('fg_resp_2_1_err');
    $fg_resp_2_2 = RecibeParametroHTML('fg_resp_2_2');
    $fg_resp_2_2_err = RecibeParametroNumerico('fg_resp_2_2_err');
    $fg_resp_2_3 = RecibeParametroHTML('fg_resp_2_3');
    $fg_resp_2_3_err = RecibeParametroNumerico('fg_resp_2_3_err');
    $fg_resp_2_4 = RecibeParametroHTML('fg_resp_2_4');
    $fg_resp_2_4_err = RecibeParametroNumerico('fg_resp_2_4_err');
    $fg_resp_2_5 = RecibeParametroHTML('fg_resp_2_5');
    $fg_resp_2_5_err = RecibeParametroNumerico('fg_resp_2_5_err');
    $fg_resp_2_6 = RecibeParametroHTML('fg_resp_2_6');
    $fg_resp_2_6_err = RecibeParametroNumerico('fg_resp_2_6_err');
    $fg_resp_2_7 = RecibeParametroHTML('fg_resp_2_7');
    $fg_resp_2_7_err = RecibeParametroNumerico('fg_resp_2_7_err');
    $fg_resp_3_1 = RecibeParametroHTML('fg_resp_3_1');
    $fg_resp_3_1_err = RecibeParametroNumerico('fg_resp_3_1_err');
    $fg_resp_3_2 = RecibeParametroHTML('fg_resp_3_2');
    $fg_resp_3_2_err = RecibeParametroNumerico('fg_resp_3_2_err');
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
        <td><b>".ObtenEtiqueta(78)."</b></td>
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
  Forma_PromptDoble(ObtenEtiqueta(79));
  Forma_CampoRadioYN(ObtenEtiqueta(82), True, 'fg_resp_1_1', $fg_resp_1_1, $fg_resp_1_1_err);
  Forma_CampoRadioYN(ObtenEtiqueta(83), True, 'fg_resp_1_2', $fg_resp_1_2, $fg_resp_1_2_err);
  Forma_CampoRadioYN(ObtenEtiqueta(84), True, 'fg_resp_1_3', $fg_resp_1_3, $fg_resp_1_3_err);
  Forma_CampoRadioYN(ObtenEtiqueta(85), True, 'fg_resp_1_4', $fg_resp_1_4, $fg_resp_1_4_err);
  Forma_CampoRadioYN(ObtenEtiqueta(86), True, 'fg_resp_1_5', $fg_resp_1_5, $fg_resp_1_5_err);
  Forma_CampoRadioYN(ObtenEtiqueta(87), True, 'fg_resp_1_6', $fg_resp_1_6, $fg_resp_1_6_err);
  Forma_Espacio( );
  
  Forma_PromptDoble(ObtenEtiqueta(80));
  Forma_CampoRadioYN(ObtenEtiqueta(88), True, 'fg_resp_2_1', $fg_resp_2_1, $fg_resp_2_1_err);
  Forma_CampoRadioYN(ObtenEtiqueta(89), True, 'fg_resp_2_2', $fg_resp_2_2, $fg_resp_2_2_err);
  Forma_CampoRadioYN(ObtenEtiqueta(90), True, 'fg_resp_2_3', $fg_resp_2_3, $fg_resp_2_3_err);
  Forma_CampoRadioYN(ObtenEtiqueta(91), True, 'fg_resp_2_4', $fg_resp_2_4, $fg_resp_2_4_err);
  Forma_CampoRadioYN(ObtenEtiqueta(92), True, 'fg_resp_2_5', $fg_resp_2_5, $fg_resp_2_5_err);
  Forma_CampoRadioYN(ObtenEtiqueta(93), True, 'fg_resp_2_6', $fg_resp_2_6, $fg_resp_2_6_err);
  Forma_CampoRadioYN(ObtenEtiqueta(94), True, 'fg_resp_2_7', $fg_resp_2_7, $fg_resp_2_7_err);
  Forma_Espacio( );
  
  Forma_PromptDoble(ObtenEtiqueta(81));
  Forma_PromptDoble(ObtenEtiqueta(95), True);
  Forma_Error($fg_resp_3_1_err);
  Forma_CampoRadio('', 'fg_resp_3_1', '0', $fg_resp_3_1, ObtenEtiqueta(97));
  Forma_CampoRadio('', 'fg_resp_3_1', '1', $fg_resp_3_1, ObtenEtiqueta(98));
  Forma_CampoRadio('', 'fg_resp_3_1', '2', $fg_resp_3_1, ObtenEtiqueta(99));
  Forma_CampoRadio('', 'fg_resp_3_1', '3', $fg_resp_3_1, ObtenEtiqueta(107));
  Forma_PromptDoble(ObtenEtiqueta(96), True);
  Forma_Error($fg_resp_3_2_err);
  Forma_CampoRadio('', 'fg_resp_3_2', '0', $fg_resp_3_2, ObtenEtiqueta(97));
  Forma_CampoRadio('', 'fg_resp_3_2', '1', $fg_resp_3_2, ObtenEtiqueta(98));
  Forma_CampoRadio('', 'fg_resp_3_2', '2', $fg_resp_3_2, ObtenEtiqueta(99));
  Forma_CampoRadio('', 'fg_resp_3_2', '3', $fg_resp_3_2, ObtenEtiqueta(107));
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