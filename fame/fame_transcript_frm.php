<?php

# Libreria de funciones
require("../lib/sp_general.inc.php");

# Recibe parametros
$fg_error = RecibeParametroNumerico('fg_error');
$fg_exito = RecibeParametroNumerico('success', True);

if (!$fg_error) {

  // set Variables
  $isSuccessPassword = false;
  $clave = RecibeParametroHTML('c', True, True);
  $fl_usuario = RecibeParametroHTML('u', True, True);
  $fl_institute = RecibeParametroHTML('i', True, True);
  $fl_program = RecibeParametroHTML('p', True, True);
  $fl_program = substr($fl_program, 0, -12);

  # Recibimos los parametros
  $hasPassword = RecibeParametroHTML('aGFzUGFzc3dvcmQ',true,true);
  if (empty($hasPassword)){
    $hasPassword = 0;
  }

  // Get ds_login
  $Query = "SELECT ds_login FROM c_usuario WHERE fl_usuario='$fl_usuario' ";
  $row = RecuperaValor($Query);

  $ds_login = $row[0];
}


$row00 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$clave");
$fg_quizes = $row00[0];
$fg_grade_teacher = $row00[1];


# Inicia cuerpo de la pagina
echo "
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es'>
<head>
<title>FAME</title>
<meta http-equiv='cache-control' content='max-age=0'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<link type='text/css' href='" . PATH_CSS . "/vanas.css' rel='stylesheet' />
<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
</head>
<body class='css_fondo'>
<center>
  <table border='" . D_BORDES . "' width='760' cellspacing='0' cellpadding='0'>
    <tr>
      <td align='left'>";

// set header and footer
$header = "
<table border='" . D_BORDES . "' width='100%' cellPadding='0' cellSpacing='0' class='css_default'>
  <tr>
    <td colspan='3' align='center'>
      <img src='" . SP_IMAGES . "/" . ObtenNombreImagen(312) . "' border='0'>
      <br>
      <h1>Vancouver Animation School</h1>
    </td>
  </tr>
  ";


//   show label 
$header .= "<tr 
      id='label-1'
      align='center'>
      <td>
      " . ObtenEtiqueta(2673) . "
      </td>
    </tr>";

$header .= "
  <tr>
    <td align='center'>  
      <p class='css_default'>";


// build footer
$footer  = "
        <br><br>
      </p>
    </td>
  </tr>";

//   download link

$footer .= "
      <tr id='download-link' align='center'> 
        <td>
          <a href='site/certificado_pdf.php?u=" . $fl_usuario . "&p=" . $fl_program . "&fg_tipo=2'>
            <img src='" . SP_IMAGES . "/certificate_example.png' border='0'></a><br>
          <a href='site/certificado_pdf.php?u=" . $fl_usuario . "&p=" . $fl_program . "&fg_tipo=2'><h3>Download a copy of your certificate.</h3></a>
        </td>

        <td>";

        if($fg_grade_teacher == '1'){
          $footer .= "<a href='" . PATH_ADM . "/modules/reports/transcript_fame_quiz_teacher_rpt.php?c=" . $clave . "&u=" . $fl_usuario . "&i=" . $fl_institute . "'>
            <img src='" . SP_IMAGES . "/transcript_logo.jpg' border='0'></a><br>
          <a href='" . PATH_ADM . "/modules/reports/transcript_fame_quiz_teacher_rpt.php?c=" . $clave . "&u=" . $fl_usuario . "&i=" . $fl_institute . "'><h3>Download a copy of your transcripts (by Teacher)</h3></a>";
        }else{
          $footer .= "&nbsp;";
        }
          
          
          
        $footer .= "</td>
        
        <td>
          <a href='" . PATH_ADM . "/modules/reports/transcript_fame_quiz_rpt.php?c=" . $clave . "&u=" . $fl_usuario . "&i=" . $fl_institute . "'>
            <img src='" . SP_IMAGES . "/transcript_logo.jpg' border='0'></a><br>
          <a href='" . PATH_ADM . "/modules/reports/transcript_fame_quiz_rpt.php?c=" . $clave . "&u=" . $fl_usuario . "&i=" . $fl_institute . "'><h3> Download a copy of your transcripts (by Quiz)</h3></a>
        </td>
      </tr>";

$footer .= "
      <tr align='center' >
        <td id='password-section'>
          <span>Password: </span>
            <label class='input'>
                <input type='text' class='form-control' id='password' name='password' value='' maxlength='100' size='0'>
                <button onclick='validatePassword();'>Validate</button>
            </label><br>
            <small id='wrong-message' style='color:red;'>
                Wrong password
            </small>
            <br><br>
        </td>
      </tr>";


$footer .= "
  <tr>
    <td align='center'>
      <a href='" . INICIO_W . "'>Go to FAME website</a>
    </td>
  </tr>
</table>      
</td>
</tr>
</table>
</center>
</body>
<script language='javascript'>

// Ready document
document.addEventListener('DOMContentLoaded', function(event) {
    // show enter Password Label
    var enterPasswordLabel = document.getElementById('label-1');
    var downloadSection = document.getElementById('download-link');
    var passwordSection = document.getElementById('password-section');
    var wrongMessage = document.getElementById('wrong-message');

    // enable disable sections
    if(".$hasPassword." == 0){
      enterPasswordLabel.style.display = 'block';
      passwordSection.style.display = 'block';
      downloadSection.style.display = 'none';
      wrongMessage.style.display = 'none';
    }else{
      // hide first label
      var enterPasswordLabel = document.getElementById('label-1');
      enterPasswordLabel.style.display = 'none';
      // hide input
      var passwordSection = document.getElementById('password-section');
      passwordSection.style.display = 'none';
      // show download section
      var downloadSection = document.getElementById('download-link');
      downloadSection.style.display = 'block';
    }
});


    function validatePassword(){
        // get value of the input
        let value = document.getElementById('password').value;
        let correctPassword = '$ds_login';
        if(value == correctPassword){

            // hide first label
            var enterPasswordLabel = document.getElementById('label-1');
            enterPasswordLabel.style.display = 'none';
            // hide input
            var passwordSection = document.getElementById('password-section');
            passwordSection.style.display = 'none';
            // show download section
            var downloadSection = document.getElementById('download-link');
            downloadSection.style.display = 'block';
            

        }else{
            // wrong message
            var wrongMessage = document.getElementById('wrong-message');
            wrongMessage.style.display = 'block';
        }
    }
</script>
</html>";

echo $header . $footer;
