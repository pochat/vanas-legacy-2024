<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_BREAKS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_break = RecibeParametroHTML('ds_break');
  $fe_ini = RecibeParametroFecha('fe_ini');
  $fe_fin = RecibeParametroFecha('fe_fin');
  $no_days = RecibeParametroNumerico('no_days');
  
  # Valida campos obligatorios
  if(empty($ds_break))
    $ds_break_err = ERR_REQUERIDO;
  if(empty($fe_ini))
    $fe_ini_err = ERR_REQUERIDO;
  if(empty($fe_fin))
    $fe_fin_err = ERR_REQUERIDO;
  
  # Verifica que el formato de la fecha sea valido
  if(!empty($fe_ini) AND !ValidaFecha($fe_ini))
    $fe_ini_err = ERR_FORMATO_FECHA;
  if(!empty($fe_fin) AND !ValidaFecha($fe_fin))
    $fe_fin_err = ERR_FORMATO_FECHA;
    
  # Verifica que la fecha inicial no sea mayor a la final
	if(compararFechas($fe_ini,$fe_fin))
		$fe_ini_err = ERR_FECHA_MAYOR;
  
	# Regresa a la forma con error
  $fg_error = $ds_break_err || $fe_ini_err || $fe_fin_err || $no_days_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('ds_break', $ds_break);
    Forma_CampoOculto('ds_break_err', $ds_break_err);
    Forma_CampoOculto('fe_ini', $fe_ini);
    Forma_CampoOculto('fe_ini_err', $fe_ini_err);
    Forma_CampoOculto('fe_fin', $fe_fin);
    Forma_CampoOculto('fe_fin_err', $fe_fin_err);
    Forma_CampoOculto('no_days', $no_days);
    Forma_CampoOculto('no_days_err', $no_days_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Cuenta el numero de dias entre las fechas inicial y final
	$no_days = contarDias($fe_ini,$fe_fin);
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_ini))
    $fe_ini = "'".ValidaFecha($fe_ini)."'";
  else
    $fe_ini = "NULL";
  if(!empty($fe_fin))
    $fe_fin = "'".ValidaFecha($fe_fin)."'";
  else
    $fe_fin = "NULL";
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_break (ds_break, fe_ini, fe_fin, no_days) ";
    $Query .= "VALUES('$ds_break', $fe_ini, $fe_fin, $no_days) ";
  }
  else {
    $Query  = "UPDATE c_break SET ds_break='$ds_break', fe_ini=$fe_ini, fe_fin=$fe_fin, no_days=$no_days ";
    $Query .= "WHERE fl_break=$clave";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
  # AGRV 07/ENE/2014
  # Funcion para comparar dos fechas y 
  # saber si la primera es mayor a la segunda 
  # Parametros requeridos: Dos fechas validas
  # Devuelve true si la primer fecha es mayor que la segunda o si alguna de las dos no es una fecha valida
  function compararFechas($primera, $segunda)
   {							
   
    # Se usa el separador "-" para descomponer ambas fechas en dia mes y año y se almacenan en un arreglo
    $valoresPrimera = explode ("-", $primera);   
    $valoresSegunda = explode ("-", $segunda); 

    $diaPrimera    = $valoresPrimera[0];  
    $mesPrimera  = $valoresPrimera[1];  		# Descomposicion de fechas
    $anioPrimera   = $valoresPrimera[2]; 

    $diaSegunda   = $valoresSegunda[0];  
    $mesSegunda = $valoresSegunda[1];  
    $anioSegunda  = $valoresSegunda[2];
    
    # Se transforman las fechas gregorianas a fechas julianas
    $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anioPrimera);  
    $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anioSegunda);     

    # Se valida que las fechas sean validas
    if(!checkdate($mesPrimera, $diaPrimera, $anioPrimera)){
      return true;
    
    }elseif(!checkdate($mesSegunda, $diaSegunda, $anioSegunda)){
      return true;
    
    # Se verifica que la primer fecha sea mayor que la segunda
    }elseif($diasPrimeraJuliano > $diasSegundaJuliano){
      return  true;
    } 
  }
  
  # AGRV 24/03/2014
  # Funcion para conocer los dias entre una fecha inicial y final
  # Parametros requeridos: Dos fechas validas
  # Devuelve el numero de dias
  function contarDias($primera, $segunda) {
    
    # Se usa el separador "-" para descomponer ambas fechas en dia mes y año y se almacenan en un arreglo
    $valoresPrimera = explode ("-", $primera);   
    $valoresSegunda = explode ("-", $segunda); 
    
    # Se obtienen los segundos de cada fecha 
    $ini = mktime(12,0,0,$valoresPrimera[1],$valoresPrimera[0],$valoresPrimera[2]);
    $fin = mktime(12,0,0,$valoresSegunda[1],$valoresSegunda[0],$valoresSegunda[2]);
    
    # Se realiza la operacion para conocer el numero de dias
    $no_dias = (floor(($fin - $ini)/60/60/24))+1;
    
    return $no_dias;
  }

?>