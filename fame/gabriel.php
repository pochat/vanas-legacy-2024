<?php
  # Libreria de funciones
  require ('../AD3M2SRC4/lib/general.inc.php');
  echo "<!DOCTYPE html>
  <html lang='en-us' >
    <head>
    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src='http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>

		<script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js'></script>

  </head>
  <body>";
  # Buscamos todas las lecciones que tenga video
  $Query = "SELECT fl_leccion_sp, ds_vl_ruta FROM c_leccion_sp WHERE ds_vl_ruta<>'' OR ds_vl_ruta<>NULL ";
  $rs = EjecutaQuery($Query);
  for($r=0;$row=RecuperaRegistro($rs);$r++){
    $fl_leccion_sp = $row[0];
    $ds_vl_ruta = $row[1];
    echo 
    "<script>
     $.ajax({
      type: 'GET',
      url : '".PATH_MODULOS."/self_paced/progreso_comando.php',
      data: 'clave=".$fl_leccion_sp."'+
            '&archivo=".$ds_vl_ruta."'
    }).done(function(result){
      // var content, tabContainer;
      // content = JSON.parse(result);
      // progress = content.progress;
      // if(!content.error){
        // if(progress<=100){
          // $('#duration').empty().append(content.duration + '&nbsp;Mins');
          // $('#grl_progress').attr('data-progressbar-value', progress);
          // $('#progress_hls').empty().append(progress + '%');
          // $('#camp_progreso_hls').empty().val(progress);
          // $('#total_convertido').empty().val(progress);
        // }
      // }
      // else{
        // $('#grl_progress1').empty().append('Error upload');
      // }
    });
    </script>"; 
  }

// obtener_estructura_directorios("/var/www/html/vanas/dev/self_pace/site/uploads/2");
// $peso=filesize("/var/www/html/vanas/dev/self_pace/site/uploads/2");



function obtener_estructura_directorios($ruta, $delete=false){
		
		// Se comprueba que realmente sea la ruta de un directorio
		if (is_dir($ruta)){
			// Abre un gestor de directorios para la ruta indicada
			$gestor = opendir($ruta);
			echo "<ul>";
 
			// Recorre todos los elementos del directorio
			while (($archivo = readdir($gestor)) !== false)  {
				
				$ruta_completa = $ruta . "/" . $archivo;
 
				// Se muestran todos los archivos y carpetas excepto "." y ".."
				if ($archivo != "." && $archivo != "..") {
					// Si es un directorio se recorre recursivamente
					if (is_dir($ruta_completa)) {
						echo "<li>" . $archivo . "</li>";
						obtener_estructura_directorios($ruta_completa);  
            chmod($ruta_completa, 0777);
            // unlink($ruta_completa);
					} else {
						echo "<li>" . $archivo . "</li>";
            chmod($ruta_completa, 0777);
            // unlink($ruta_completa);
					}          
				}       
			}
			// Cierra el gestor de directorios
			closedir($gestor);
			echo "</ul>";
		} else {
			echo "No es una ruta de directorio valida<br/>";
		}
		
	}

function tamano_archivo($peso , $decimales = 2 ) {
$clase = array(" Bytes", " KB", " MB", " GB", " TB"); 
return round($peso/pow(1024,($i = floor(log($peso, 1024)))),$decimales ).$clase[$i];
}

echo "
  </body>
</html>";
// echo tamano_archivo($peso);  // mostramos su peso ya modificado   
  
?>
