<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit();
  }
  
  # Recibe Parametro
  $fl_instituto = ObtenInstituto($fl_usuario);;
  
  # Modo trial
  $fg_modo = ObtenPlanActualInstituto($fl_instituto);  
  
  # Si esta en trail mostrara el limite de licencias que tiene por usar en el trial
  if($fg_modo==""){

      #Verifica que el instituto sea b2c.
      $Query="SELECT fg_b2c,no_tot_licencias_b2c FROM c_instituto WHERE fl_instituto=$fl_instituto ";
      $row=RecuperaValor($Query);
      $fg_b2c=$row['fg_b2c'];

      if($fg_b2c==1){
          $tot_licencias=$row['no_tot_licencias_b2c'];
          
      }else{
          $tot_licencias = ObtenConfiguracion(102);
      }

    
    # Licencias activadas sin contar al administrador
    $avaible = ObtenNumeroUserInst($fl_instituto);
    $no_usuarios = $tot_licencias - $avaible;
  }
  else{
    $tot_licencias =  ObtenNumLicencias($fl_instituto);
    # Obtenemos el numero de licencias
    $no_usuarios =ObtenNumLicenciasDisponibles($fl_instituto);
    # Licencias no viables
    $avaible = $tot_licencias - $no_usuarios;      
  }
  if(empty($avaible))$avaible=0; 
  $result['valores']= array(
  "content" =>
    "<!-- sparks -->
    <ul id='sparks' class='padding-bottom-5'>
      <li class='sparks-info'>
        <h5> ".ObtenEtiqueta(1050).": ".$tot_licencias."</h5>
      </li>
      <li class='sparks-info'>
        <h5> ".ObtenEtiqueta(1051).": ".$avaible."</h5>
      </li>
      <li class='sparks-info'>
        <h5> ".ObtenEtiqueta(1052).": ".$no_usuarios."
        </h5>
      </li>
    </ul>
    <!-- end sparks -->", 
  "modo"=>$fg_modo,
  "tot_licencias" => $tot_licencias,
  "usadas" => $avaible, 
  "disponibles"=>$no_usuarios, 
  "instituto" => $fl_instituto);
  
  echo json_encode((Object) $result);
  
?>
