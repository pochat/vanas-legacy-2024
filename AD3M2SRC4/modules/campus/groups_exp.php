<?php

	# Funcion para quitar caracteres especiales y saltos de linea
  function getStrParaCSV($str) {
	$str_aux = $str;
	$str_aux = str_replace(",", " ", $str_aux);
	$str_aux = str_replace("\n", " ", $str_aux);
	$str_aux = str_replace("\r", " ", $str_aux);
  
	return $str_aux;  
  }
?>
<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, PERMISO_EJECUCION)) {    
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
 
 
  $fl_param = $_POST['fl_param'];
  $fe_ini = $_POST['fe_uno'];
  $fe_dos = $_POST['fe_dos'];
  $fl_programa=$_POST['fl_programa'];
  
  
  if(empty($fl_programa)){
  
      if($fe_ini){
          #Damos formato de fecha alos parametros recibidos.
          $fe_ini =strtotime('0 days',strtotime($fe_ini)); 
          $fecha1= date('Y-m-d',$fe_ini);
      }
      if($fe_dos){
          $fe_dos=strtotime('0 days',strtotime($fe_dos)); 
          $fecha2= date('Y-m-d',$fe_dos);
      }
  
  }

  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  
  $Query = Query_Adv_Search($fl_param,$fecha1,$fecha2,$fl_programa);
  
    
    function Query_Adv_Search($fl_param='',$fecha1='',$fecha2='',$fl_programa='') {
    
      #Muestra resultados de la busqueda.
      $Query="SELECT fl_grupo,nb_programa AS Course, nb_periodo AS Cycle,no_grado AS 'Level  / Term', nb_grupo AS 'Group Name', ";
      $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
      $Query .= ConcatenaBD($concat)." '".ObtenEtiqueta(421)."', ";
      $Query .= "(SELECT COUNT(1) FROM k_alumno_grupo f WHERE a.fl_grupo=f.fl_grupo) AS Student ";
      $Query .= "FROM c_grupo a, k_term b, c_programa c, c_periodo d, c_usuario e ";
      $Query .= "WHERE a.fl_term=b.fl_term ";
      $Query .= "AND b.fl_programa=c.fl_programa ";
      $Query .= "AND b.fl_periodo=d.fl_periodo ";
      $Query .= "AND a.fl_maestro=e.fl_usuario  AND c.fg_archive='0'  ";
      
      if($fl_param=='Start Date'){
          if($fecha1)
              $Query.="AND d.fe_inicio >= '$fecha1'  ";
          if($fecha2)
              $Query.="AND d.fe_inicio <= '$fecha2' ";  
          
          
      }
      
      if(!empty($fl_programa)){
          $Query.="AND c.fl_programa=$fl_programa ";
          
      }
      $Query .= "ORDER BY no_orden, fe_inicio DESC, no_grado, nb_grupo ";
    
    # Exporta el resultado a CSV
    $nom_arch = PATH_EXPORT."/groups_".date('Ymd')."_".rand(1000,9000).".csv";
   
     # Abre archivo de salida
    if(!$archivo = fopen($_SERVER[DOCUMENT_ROOT].$nom_arch, "wb")) {
      MuestraPaginaError(ERR_EXPORTAR);
      exit;
    }
    
    # Exporta los datos
    $rs = EjecutaQuery($Query);
    $tot_campos = CuentaCampos($rs);
    for($i = 1; $i < $tot_campos; $i++)
      fwrite($archivo, str_replace(",", " ", str_ascii(NombreCampo($rs, $i, True))).",");
    fwrite($archivo, "\n");
    while($row = RecuperaRegistro($rs)) {
      for($i = 1; $i < $tot_campos; $i++)
        fwrite($archivo, str_replace(",", " ", getStrParaCSV(str_ascii(DecodificaEscogeIdiomaBD($row[$i])))).",");
      fwrite($archivo, "\n");
    }
  
  # Cierra el archivo
  fclose($archivo);
  
  # Descarga el archivo
  echo $nom_arch;
  }
  
  
	
	
?>