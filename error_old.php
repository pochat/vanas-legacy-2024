<?php
  
  # Libreria de funciones
	require 'lib/sp_general.inc.php';
  
	# Recupera el usuario de la sesion
  $fl_usuario = ObtenUsuario(False);
  if(empty($fl_usuario))
    $fl_usuario = ObtenUsuario( );
  if(empty($fl_usuario))
    $fl_usuario = 0;
  
  # Recupera el mensaje a mostrar al usuario
  $row = RecuperaValor("SELECT cl_mensaje FROM c_usuario WHERE fl_usuario=$fl_usuario");
  $cl_mensaje = $row[0];
  if(empty($cl_mensaje))
    $cl_mensaje = ERR_SIN_PERMISO;
  
  # Obtiene el mensaje
	$Query  = "SELECT ds_titulo, tr_titulo, ds_mensaje, tr_mensaje, fg_severidad, fg_tipo ";
  $Query .= "FROM c_mensaje ";
  $Query .= "WHERE cl_mensaje=$cl_mensaje";
	$row = RecuperaValor($Query);
  if($row) {
    $ds_titulo = EscogeIdioma($row[0], $row[1]);
    $ds_mensaje = EscogeIdioma($row[2], $row[3]);
    $fg_severidad = $row[4];
    $fg_tipo = $row[5];
	}
  else {
    $ds_titulo = "Internal Error";
    $ds_mensaje = "Undefined error code.";
    $fg_tipo = 1;
  }
  
	# Establece el titulo del mensaje y el icono dependiendo de la severidad
  switch($fg_severidad) {
    case 'I':
      $imgSeveridad = PATH_ADM_IMAGES."/".IMG_INFO;
      $ds_titulo = ETQ_TIT_INFO." - ".$ds_titulo;
      break;
    case 'W':
      $imgSeveridad = PATH_ADM_IMAGES."/".IMG_WARNING;
      $ds_titulo = ETQ_TIT_WARN." - ".$ds_titulo;
      break;
    case 'P':
      $imgSeveridad = PATH_ADM_IMAGES."/".IMG_HELP;
      $ds_titulo = ETQ_TIT_CONFIRM." - ".$ds_titulo;
      break;
    default :
      $imgSeveridad = PATH_ADM_IMAGES."/".IMG_ERROR;
      $ds_titulo = ETQ_TIT_ERROR." - ".$ds_titulo;
  }
  
  # Presenta pagina con el mensaje
  PresentaInicioPagina( );
  echo "
<center>
  <TABLE width='300' border='".D_BORDES."' cellPadding='0' cellSpacing='0'>
    <TR>
      <TD colapsn='2' height='100'></TD>
    </TR>
    <TR>
      <TD width='20%' align='center' valign='middle'><img src='$imgSeveridad' border='0'></TD>
      <TD width='80%' align='left' valign='middle' class='css_default'>
        <br><b>$ds_titulo</b><br>
        <br>$ds_mensaje<br><br>
        <a href='javascript:history.back()'>".ETQ_REGRESAR."</a>
        <br><br>	
      </TD>
    </TR>
  </TABLE>
</center>
</body>
</html>";
  
?>