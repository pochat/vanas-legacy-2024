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
	
  # Recibe parametros
  #fl_criterio de la tabla c_criterio
  $clave=RecibeParametroHTML("fl_registro");
  $ds_descripcion=nl2br(RecibeParametroHTML("ds_descripcion"));
  $fl_calificacion = RecibeParametroHTML("fl_calificacion");
  $fg_criterio=RecibeParametroNumerico("nb_criterio");
  $ds_descripcion=trim($ds_descripcion);

  #Elimnamos los saltos de linea.
  $ds_descripcion=preg_replace("/[\r\n|\n|\r]+/", " ", $ds_descripcion);


  #verificamos si existe ese xriterio
  if(empty($clave)){

    # Si no existe ELIMINAMOS EL CRITERIO
    $Query="DELETE FROM k_criterio_fame WHERE fl_criterio IS NULL  AND  fl_calificacion_criterio=$fl_calificacion ;";

    EjecutaQuery($Query);

    $Query='INSERT INTO k_criterio_fame (fl_calificacion_criterio, ds_descripcion) ';
		$Query.='VALUES('.$fl_calificacion.', "'.$ds_descripcion.'")';
		$fl_criterio_fame=EjecutaInsert($Query);

  }else{


       #Verificar si existe.
      $Query="SELECT COUNT(*),fl_criterio_fame from k_criterio_fame  WHERE fl_criterio=$clave AND fl_calificacion_criterio=$fl_calificacion "; 
       $ro=RecuperaValor($Query);

       if($ro[0]){
           $fl_criterio_fame=$ro[1];
          $Query ="UPDATE k_criterio_fame SET ds_descripcion='".$ds_descripcion."' WHERE fl_criterio=$clave AND fl_calificacion_criterio=$fl_calificacion ";
          EjecutaQuery($Query);

       }else{
          
          $Query='INSERT INTO k_criterio_fame (fl_criterio,fl_calificacion_criterio, ds_descripcion) ';
          $Query.='VALUES('.$clave.','.$fl_calificacion.', "'.$ds_descripcion.'")';
          $fl_criterio_fame=EjecutaInsert($Query);

       }
       


  }
 

  
  echo json_encode((Object)array(
       'fl_criterio_fame' => $fl_criterio_fame
    ));




?>




 