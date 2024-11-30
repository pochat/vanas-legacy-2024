<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);

  
  # Recibe parametros
  $clave=RecibeParametroNumerico('clave');
  $nb_criterio=RecibeParametroHTML('nb_criterio');
  $nb_criterio_esp=RecibeParametroHTML('nb_criterio_esp');
  $nb_criterio_fra=RecibeParametroHTML('nb_criterio_fra');
  $no_porcentaje = RecibeParametroHTML('no_porcentaje');
  $tot_registros=RecibeParametroNumerico('tot_registros');


  
  $Query='UPDATE c_criterio SET nb_criterio="'.$nb_criterio.'", nb_criterio_esp="'.$nb_criterio_esp.'", nb_criterio_fra="'.$nb_criterio_fra.'" WHERE fl_criterio='.$clave.' AND fl_instituto='.$fl_instituto.'  ';
  EjecutaQuery($Query);


  for($x=1; $x<=$tot_registros; $x++){

      $nb_archivo = RecibeParametroHTML("nb_archivo_$x");
      $fl_criterio_fame=RecibeParametroNumerico("fl_criterio_1_$x");


      if(!empty($nb_archivo)){

          $Query="UPDATE c_archivo_criterio SET fl_criterio_fame=$fl_criterio_fame WHERE nb_archivo='$nb_archivo' ";
          EjecutaQuery($Query);

          ////$Query2.=$Query."<br>";
      }


  }

  echo json_encode((Object)array(
      'fg_correcto' => true,
      //'Query'=>$_POST['tot_registros']
    ));
  




?>
   
