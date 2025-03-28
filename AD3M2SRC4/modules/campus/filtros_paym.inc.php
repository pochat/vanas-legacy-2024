<?php
  # Recupera el usuario actual
  $fl_usuario = ValidaSesion( );
  
  if(!empty($nuevo)) {     
  
    # fechas
    $dates = RecibeParametroHTML('dates');
    $fpagos1 = RecibeParametroHTML('fpagos1'); 
    $fpagos2 = RecibeParametroHTML('fpagos2'); 
    $fpagos3 = RecibeParametroHTML('fpagos3'); 
    $fpagos4 = RecibeParametroHTML('fpagos4'); 
    $fpagos5 = RecibeParametroHTML('fpagos5'); 
    $fpagos6 = RecibeParametroHTML('fpagos6'); 
    
    $Query_del= "DELETE FROM k_usu_parametro WHERE fl_usuario=$fl_usuario ";
    $Query_del .= "AND EXISTS(SELECT 1 FROM k_parametro_funcion b WHERE fl_funcion=4 AND b.fl_parametro_funcion=k_usu_parametro.fl_parametro_funcion) ";
    EjecutaQuery($Query_del);
    $Query2 = "INSERT INTO k_usu_parametro (fl_parametro_funcion, fl_usuario, ds_valor) VALUES";
    $parametro = 1;
      $Query2 .= "(".$parametro++.", $fl_usuario, '$dates'),";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos1'),";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos2'),";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos3'),";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos4'),";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos5')";
      $Query2 .= "(".$parametro++.", $fl_usuario, '$fpagos6')";
    EjecutaQuery($Query2);
  }
  else {
    $parametro = 1;
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $dates = $row[0];
  }
	
	# Armado de query para campo 1
	$Query .= condiciones_busqueda($campo[0], $tipo[0], $filtro[0], $fecha1[0], $fecha2[0], $crit[0]);
	
	# Armado de query para campo 2
	$Query .= condiciones_busqueda($campo[1], $tipo[1], $filtro[1], $fecha1[1], $fecha2[1], $crit[1]);
	
	# Armado de query para campo 3
	$Query .= condiciones_busqueda($campo[2], $tipo[2], $filtro[2], $fecha1[2], $fecha2[2], $crit[2]);
	
	# Armado de query para campo 4
	$Query .= condiciones_busqueda($campo[3], $tipo[3], $filtro[3], $fecha1[3], $fecha2[3], $crit[3]);
  
  # Armado de query para campo 5
	$Query .= condiciones_busqueda($campo[4], $tipo[4], $filtro[4], $fecha1[4], $fecha2[4], $crit[4]);
  
  # Armado de query para campo 6
	$Query .= condiciones_busqueda($campo[5], $tipo[5], $filtro[5], $fecha1[5], $fecha2[5], $crit[5]);
  
  # Armado de query para campo 7
	$Query .= condiciones_busqueda($campo[6], $tipo[6], $filtro[6], $fecha1[6], $fecha2[6], $crit[6]);
	
  
	Function condiciones_busqueda($campo, $tipo, $filtro, $fecha1, $fecha2, $criterio){
    
		if (!empty($campo)){
			
			switch ($tipo){
				
				# Tipo flag
				case 1:
					if($campo=='fg_genero') {						//Si el campo es fg_genero
						if($filtro==1) {
              $Query = "AND $campo LIKE 'M' ";		
						} 
						else {
							$Query = "AND $campo LIKE 'F' ";	
						}		
					} 
					else {
						if($filtro==2) {
							$Query = "AND $campo LIKE '0' ";		
						} 
						else {
							$Query = "AND $campo LIKE '1' ";	
						}
					}
					break;
				
				# Tipo fecha	
				case 2:
          $Query = "AND $campo >= STR_TO_DATE('$fecha1', '%d-%m-%Y') AND $campo <= STR_TO_DATE('$fecha2', '%d-%m-%Y') ";
					break;
				
				# Tipo descripciones o numeros	
				case 3:
          switch ($filtro){
            case 1:
              $Query = "AND $campo LIKE '%$criterio%' ";			//Contains
              break;
            
            case 2:
              $Query = "AND $campo NOT LIKE '%$criterio%' ";	//Does not contains
              break;
              
            case 3:
              $Query = "AND $campo LIKE '$criterio' ";				//Is
              break;
              
            case 4:
              $Query = "AND $campo NOT LIKE '$criterio' ";		//Is not
              break;
              
            case 5:
              $Query = "AND $campo LIKE '$criterio%' ";			//Starts with
              break;
              
            case 6:
              $Query = "AND $campo LIKE '%$criterio' ";	//Ends with
              break;
          }
          break;
      }
      return $Query;
    }
  }
?>