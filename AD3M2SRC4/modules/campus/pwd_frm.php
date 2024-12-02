<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_actual = ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  $funcion_origen=RecibeParametroNumerico('funcion');
  
  # Si no se envio usuario, cambia el password al usuario actual
  if(empty($clave))
    $clave = $fl_usuario_actual;
  
  # Revisa si se esta cambiando el password propio o a otro usuario
  if($clave == $fl_usuario_actual) {
    $funcion = FUNC_PWD;
    $fg_otro = False;
  }
  else {
    $funcion = FUNC_PWD_OTROS;
    $fg_otro = True;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso($funcion, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if($fg_error) {
    $ds_password_act_err = RecibeParametroNumerico('ds_password_act_err');
    $ds_password_err = RecibeParametroNumerico('ds_password_err');
    $ds_password_conf_err = RecibeParametroNumerico('ds_password_conf_err');
  }
  else {
    $ds_password_act_err = "";
    $ds_password_err = "";
    $ds_password_conf_err = "";
  }
  
  # Recupera datos del usuario
  $row = RecuperaValor("SELECT ds_login FROM c_usuario WHERE fl_usuario=$clave");
  $ds_login = str_texto($row[0]);
  
  if($funcion_origen==FUNC_MAESTROS){
      # Presenta forma de captura
     // PresentaHeader( );
     // PresentaEncabezado($funcion);
     // Forma_Inicia($clave);
     // if($fg_error)
     //     Forma_PresentaError( );
     
      Forma_CampoInfo(ETQ_USUARIO, $ds_login);
      Forma_Espacio( );
  }
 
  # Solo pide la contrasenia actual si es el mismo usuario
  if(!$fg_otro) {
    Forma_CampoTexto(ObtenEtiqueta(123), True, 'ds_password_act', '', 16, 16, $ds_password_act_err, True);    
  }
  Forma_CampoTexto(ObtenEtiqueta(125), True, 'ds_password', '', 16, 16, $ds_password_err, True);
  Forma_CampoTexto(ObtenEtiqueta(124), True, 'ds_password_conf', '', 16, 16, $ds_password_conf_err, True);
  
  echo "
  <div id='pwd_mensaje'></div>
  <div class='modal-footer'>
    <input class='btn btn-default' value='".ObtenEtiqueta(14)."' onclick='closed_pwd();' type='submit'>
    <input class='btn btn-primary' value='".ObtenEtiqueta(126)."' onclick='chager_pwd_validar();' type='submit'>
  </div>";
  //Forma_Termina(True, $pag_cancelar);
  
  
  # Pie de Pagina
  //PresentaFooter( );
  echo "
  <script type='text/javascript'>
  function chager_pwd_validar(){
    // Variables
    var clave = ".$clave.", pwd_act = $('#ds_password_act').val(), pwd = $('#ds_password').val(), pwd_conf = $('#ds_password_conf').val(),funcion_origen=".$funcion_origen.";  
    $.ajax({
      type: 'POST',
      url : 'pwd_iu.php',
      data: 'clave='+clave+'&ds_password_act='+pwd_act+'&ds_password='+pwd+'&ds_password_conf='+pwd_conf+'&funcion_origen='+funcion_origen,
      async: false,
      success: function(html) {
        $('#pwd_mensaje').html(html);        
      }
    });    
  }  
  
  </script>";
 
   if($funcion_origen==FUNC_MAESTROS){
	   
	   echo"<a class='btn btn-success btn-sm hidden' id='btntest'>test</a>";
	   echo"
	   <script type='text/javascript'>
	   $('#select2-drop').addClass('hidden');
	   document.getElementById('btntest').click();
	   pageSetUp();    
	   
	   function closed_pwd(){
			
			$('#exampleModal').modal('hide');
		}
	   </script>
	   
	   
	   ";
	   
   }
  
?>