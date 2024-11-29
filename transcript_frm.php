<?php

# Libreria de funciones
require("lib/sp_general.inc.php");

# Recibe parametros
$fg_error = RecibeParametroNumerico('fg_error');
$fg_exito = RecibeParametroNumerico('success', True);

if (!$fg_error) {

    // set Variables
    $isSuccessPassword = false;
    $clave = RecibeParametroHTML('c', True, True);
    $clave = substr($clave, 0, -12);


    // Get ds_login
    $Query = "SELECT ds_login FROM c_usuario WHERE fl_usuario='$clave' ";
    $row = RecuperaValor($Query);

    $ds_login = $row[0];
}

# Inicia cuerpo de la pagina
echo "
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es'>
<head>
<title>Vanas Transcript</title>
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
    <td align='center'>
      <img src='" . SP_IMAGES . "/login.jpg' border='0'>
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
      <tr align='center'> 
        <td id='download-link' >
          <a href='" . PATH_ADM . "/modules/reports/pctia_rpt.php?clave=$clave'>
            <img src='" . SP_IMAGES . "/transcript_logo.jpg' border='0'></a><br>
          <a href='" . PATH_ADM . "/modules/reports/pctia_rpt.php?clave=$clave'><h3>" . ObtenEtiqueta(2672) . "</h3></a>
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
      <a href='" . INICIO_W . "'>Go to Vanas website</a>
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
    enterPasswordLabel.style.display = 'block';
    passwordSection.style.display = 'block';
    downloadSection.style.display = 'none';
    wrongMessage.style.display = 'none';
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
