<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CRITERIO_FAME, $permiso)) {
   MuestraPaginaError(ERR_SIN_PERMISO);
   exit;
  }
  
  # Recibe parametros
  $nb_criterio=RecibeParametroHTML('nb_criterio');
  $nb_criterio_esp=RecibeParametroHTML('nb_criterio_esp');
  $nb_criterio_fra=RecibeParametroHTML('nb_criterio_fra');
  $no_porcentaje = RecibeParametroHTML('no_porcentaje');
 
  $_POST[''];
  
  if(empty($clave)) {
 
  	$nb_archvio1=RecibeParametroHTML('nb_archivo_1');
  	$nb_archvio2=RecibeParametroHTML('nb_archivo_2');
  	$nb_archvio3=RecibeParametroHTML('nb_archivo_3');
  	$nb_archvio4=RecibeParametroHTML('nb_archivo_4');
  	$nb_archvio5=RecibeParametroHTML('nb_archivo_5');
    $nb_archvio6=RecibeParametroHTML('nb_archivo_6');
    
  }
  
  # Inserta o actualiza el registro
  if(empty($clave)) {

    $Query="INSERT INTO c_criterio (nb_criterio, nb_criterio_esp, nb_criterio_fra) ";
    $Query.="VALUES('$nb_criterio', '$nb_criterio_esp', '$nb_criterio_fra') ";
    $fl_criterio= EjecutaInsert($Query);
    
    #Actualiza los criterios y sus decripciones    
    $Query="UPDATE k_criterio_fame SET  fl_criterio=$fl_criterio WHERE fl_criterio IS NULL ";
    EjecutaQuery($Query);
    
    
  //Verificamos si existe comentarios por criterio.
    $Query="SELECT count(*) FROM k_criterio_fame WHERE fl_criterio=$fl_criterio ";
    $row=RecuperaValor($Query);
    $tot=$row[0];

    if($tot==0){

      #Recuperamos las calificacnoes existentes en c_calificacion_criterio_fame
      $Query="SELECT fl_calificacion_criterio FROM c_calificacion_criterio ORDER BY fl_calificacion_criterio DESC ";
      $rs_1=EjecutaQuery($Query);
      $_POST[''];
        #No existen comntarios entonces se insertan criterios sin comentarios
        $tot_criterios = RecibeParametroNumerico('tot_registros');
        $contador=0;
        for($i = 0; $i < $tot_criterios; $i++){
          $contador1++;
          $contador2=0;
          foreach($rs_1 as $data){
            $fl_calificacion_criterio=$data[0];
            $contador2++;
            if($contador1==$contador2){
               $ds_descripcion=RecibeParametroHTML('desc'.$fl_calificacion_criterio);
              $ds_descripcion_esp=RecibeParametroHTML('desc_esp'.$fl_calificacion_criterio);
              $ds_descripcion_fra=RecibeParametroHTML('desc_fra'.$fl_calificacion_criterio);
              $Query="INSERT INTO k_criterio_fame (fl_criterio,fl_calificacion_criterio,ds_descripcion, ds_descripcion_esp, ds_descripcion_fra) VALUES ($fl_criterio,$fl_calificacion_criterio,'$ds_descripcion','$ds_descripcion_esp', '$ds_descripcion_fra')";
              $fl_criterio_fame=EjecutaInsert($Query);
            }    
          }
        }   
      }

      #para los archivos.
	  $src_img="../../images/rubrics/".$nb_archvio1;      
      $file_exist=file_exists("../../images/rubrics",$nb_archvio1);    
      if($file_exist) {

	  }
 
    if(!empty($nb_archvio1)) {
        
      #Recupera registro dela img 1.
      $Query="SELECT fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=5  ";
      $row=RecuperaValor($Query);
      $fl_criterio_fame=$row[0];  
      #Actualiza registro
      $Query="UPDATE c_archivo_criterio SET nb_archivo='$nb_archvio1', fl_criterio_fame=$fl_criterio_fame WHERE  nb_archivo='$nb_archvio1' ";
      EjecutaQuery($Query);

    }

    if(!empty($nb_archvio2)) {
        
      #Recupera registro dela img 1.
      $Query="SELECT fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=4  ";
      $row=RecuperaValor($Query);
      $fl_criterio_fame=$row[0];  
      #Actualiza registro
      $Query="UPDATE c_archivo_criterio SET nb_archivo='$nb_archvio2', fl_criterio_fame=$fl_criterio_fame WHERE  nb_archivo='$nb_archvio2' ";
      EjecutaQuery($Query);

    }

    if(!empty($nb_archvio3)) {

      #Recupera registro dela img 1.
      $Query="SELECT fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=3  ";
      $row=RecuperaValor($Query);
      $fl_criterio_fame=$row[0];  
      #Actualiza registro
      $Query="UPDATE c_archivo_criterio SET nb_archivo='$nb_archvio3', fl_criterio_fame=$fl_criterio_fame WHERE  nb_archivo='$nb_archvio3' ";
      EjecutaQuery($Query);
        
    }

    if(!empty($nb_archvio4)) {
        
      #Recupera registro dela img 1.
      $Query="SELECT fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=2  ";
      $row=RecuperaValor($Query);
      $fl_criterio_fame=$row[0];  
      #Actualiza registro
      $Query="UPDATE c_archivo_criterio SET nb_archivo='$nb_archvio4', fl_criterio_fame=$fl_criterio_fame WHERE  nb_archivo='$nb_archvio4' ";
      EjecutaQuery($Query);
    }

    if(!empty($nb_archvio5)) {

      #Recupera registro dela img 1.
      $Query="SELECT fl_criterio_fame FROM k_criterio_fame WHERE fl_criterio=$fl_criterio AND fl_calificacion_criterio=1  ";
      $row=RecuperaValor($Query);
      $fl_criterio_fame=$row[0];  
      #Actualiza registro
      $Query="UPDATE c_archivo_criterio SET nb_archivo='$nb_archvio5', fl_criterio_fame=$fl_criterio_fame WHERE  nb_archivo='$nb_archvio5' ";
      EjecutaQuery($Query);

    }
    
  }else{

    $Query="UPDATE c_criterio SET nb_criterio='$nb_criterio', nb_criterio_esp='$nb_criterio_esp', nb_criterio_fra='$nb_criterio_fra' WHERE fl_criterio=$clave  ";
    EjecutaQuery($Query);

    #Recuperamos las calificacnoes existentes en c_calificacion_criterio_fame
    $Query="SELECT fl_calificacion_criterio FROM c_calificacion_criterio ORDER BY fl_calificacion_criterio DESC ";
    $rs_1=EjecutaQuery($Query);
    $_POST[''];
    #No existen comntarios entonces se insertan criterios sin comentarios
    $tot_criterios = RecibeParametroNumerico('tot_registros');
    $contador=0;
    for($i = 0; $i < $tot_criterios; $i++){
        $contador1++;
        $contador2=0;
        foreach($rs_1 as $data){
            $fl_calificacion_criterio=$data[0];
            $contador2++;
            if($contador1==$contador2){
                $ds_descripcion=RecibeParametroHTML('desc'.$fl_calificacion_criterio);
                $ds_descripcion_esp=RecibeParametroHTML('desc_esp'.$fl_calificacion_criterio);
                $ds_descripcion_fra=RecibeParametroHTML('desc_fra'.$fl_calificacion_criterio);
                $Query="UPDATE k_criterio_fame SET ";
                
                if($ds_descripcion_esp)
                 $Query.="ds_descripcion_esp='$ds_descripcion_esp',";
                if($ds_descripcion_fra)
                 $Query.="ds_descripcion_fra='$ds_descripcion_fra',"; 
                if($ds_descripcion)
                 $Query.="ds_descripcion='$ds_descripcion' ,";

                $Query.="fl_calificacion_criterio=$fl_calificacion_criterio ";
                $Query.="WHERE fl_criterio=$clave AND fl_calificacion_criterio=$fl_calificacion_criterio ";
                //$fl_criterio_fame=
                EjecutaQuery($Query);
            }    
        }
    } 








  }

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
?>