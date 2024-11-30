<?php
 # Librerias
 require("../lib/self_general.php");

 # Verifica que exista una sesion valida en el cookie y la resetea
 $fl_usuario = ValidaSesion(False,0, True);
  
 # Recibe parametros
 $clave = RecibeParametroNumerico('clave',True);
 $fg_error = RecibeParametroNumerico('fg_error'); 

 # Verifica que el usuario tenga permiso de usar esta funcion
 if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
 }
 
  # Intituto del usuario
  
  $clave=RecibeParametroNumerico('clave');
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  if(empty($clave)){
	  $clave=RecibeParametroNumerico('fl_programa_nuevo_creado');
  }
  $tab = RecibeParametroHTML('tab');
  
  if($tab==1){	  
		  $nb_programa = RecibeParametroHTML('nb_programa');
          $nb_programa_esp = RecibeParametroHTML('nb_programa_esp');
          $nb_programa_fra = RecibeParametroHTML('nb_programa_fra');
		  $no_semanas = RecibeParametroNumerico('no_semanas');
		  $no_creditos = RecibeParametroFlotante('no_creditos');
		  $no_horas = RecibeParametroFlotante('no_horas');
		  $workload = RecibeParametroHTML('workload');
		  $cl_delivery = RecibeParametroHTML('cl_delivery');
          $cl_delivery_esp = RecibeParametroHTML('cl_delivery_esp');
          $cl_delivery_fra = RecibeParametroHTML('cl_delivery_fra');
		  $ds_credential = RecibeParametroHTML('ds_credential');
          $ds_credential_esp = RecibeParametroHTML('ds_credential_esp');
          $ds_credential_fra = RecibeParametroHTML('ds_credential_fra');
		  $cl_type = RecibeParametroNumerico('cl_type');
		  $ds_language = RecibeParametroHTML('ds_language');
          $ds_language_esp = RecibeParametroHTML('ds_language_esp');
          $ds_language_fra = RecibeParametroHTML('ds_language_fra');
		  $fl_programa = RecibeParametroNumerico('fl_programa');
          $nb_thumb_load=RecibeParametroHTML('nb_thumb_load');
          $no_orden=RecibeParametroNumerico('no_orden');
          
          

          
		   
		  switch ($cl_delivery){
			case 'O': $ds_tipo = "Online"; break;
			case 'S': $ds_tipo = "On-Site"; break;
			case 'C': $ds_tipo = "Combined"; break;
			case 'OB': $ds_tipo = "Online &#47; Blended"; break;
			default:  $ds_tipo = ""; break;
		  }  


		  
		  $nb_thumb = ($_FILES['thumb']['name']);
		  $tamano = $_FILES["thumb"]['size'];
		  $tipo = $_FILES["thumb"]['type'];
		  $archivo = $_FILES["thumb"]['name'];
		  $ext = strtolower(ObtenExtensionArchivo($_FILES['thumb']['name']));
		  
		  if( (!empty($nb_thumb_load)) AND (empty($nb_thumb)) ){
              $nb_thumb = $nb_thumb_load;
		  }

		   # Subimos el archivo antes del error para no perderlo a pesar de de que exista error
		  if(!empty($_FILES['thumb']['tmp_name'][0])) {
			$ruta = PATH_ADM_HOME."/modules/fame/uploads";
			$ext = strtolower(ObtenExtensionArchivo($_FILES['thumb']['name']));
			$ds_ruta_foto = $_FILES['thumb']['name'];
			move_uploaded_file($_FILES['thumb']['tmp_name'], $ruta."/".$ds_ruta_foto);
			
			CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 185, 205);
		  }
		  
		  if(empty($clave)){
			  
			  $Query  = "INSERT INTO c_programa_sp (nb_programa, ds_tipo, no_creditos, nb_thumb, no_orden,fl_template,ds_duracion,ds_course_code,fg_taxes,fg_fulltime,fg_obligatorio,fl_instituto,fl_usuario_creacion) ";
			  $Query .= "VALUES ('$nb_programa', '$ds_tipo', $no_creditos, '$nb_thumb',0,NULL,'','','0','0','0',$fl_instituto,$fl_usuario)";    
			  $clave=EjecutaInsert($Query);
              $result["Query"]=$Query;
			  $row = RecuperaValor("SELECT MAX(fl_programa_sp) FROM c_programa_sp");
			  $clave = $row[0];
			
			  $Query  = "INSERT INTO k_programa_detalle_sp (fl_programa_sp, no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_workload, fg_board) ";
			  $Query .= "VALUES ($clave, $no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, '$workload', '1')";
			  EjecutaQuery($Query);

			  $result["fl_programa_nuevo_creado"] = $clave;
              
			  
			  
			
		  }else{
			  
			  
			  $query="UPDATE c_programa_sp SET  nb_programa='$nb_programa',nb_programa_esp='$nb_programa_esp',nb_programa_fra='$nb_programa_fra',ds_tipo='$ds_tipo',no_orden=$no_orden,no_creditos=$no_creditos,nb_thumb='$nb_thumb', fl_instituto=$fl_instituto 
                      WHERE fl_programa_sp=$clave ";
              EjecutaQuery($query);
              $result["Query"]=$query;
              #Verifica que exista y si no inserta
              $Query="SELECT COUNT(*) FROM k_programa_detalle_sp WHERE fl_programa_sp=$clave ";
              $row=RecuperaValor($Query);
              if($row[0]>=1){

                  $Query="UPDATE k_programa_detalle_sp SET no_horas=$no_horas, no_semanas=$no_semanas, ds_credential='$ds_credential',
                                                       cl_delivery='$cl_delivery', ds_language='$ds_language', cl_type=$cl_type,ds_credential_esp='$ds_credential_esp',ds_credential_fra='$ds_credential_fra',
                                                       ds_language_esp='$ds_language_esp',ds_language_fra='$ds_language_fra', 
                                                       no_workload='$workload' WHERE fl_programa_sp=$clave  ";
                  EjecutaQuery($Query);
              }else{
                  
                  $Query  = "INSERT INTO k_programa_detalle_sp (fl_programa_sp, no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_workload, fg_board,ds_language_esp,ds_language_fra,ds_credential_esp,ds_credential_fra) ";
                  $Query .= "VALUES ($clave, $no_horas, $no_semanas, '$ds_credential', '$cl_delivery', '$ds_language', $cl_type, '$workload', '1','$ds_language_esp','$ds_language_fra','$ds_credential_esp','$ds_credential_fra')";
			  EjecutaQuery($Query);
              }
			  
			  $result["fl_programa_nuevo_creado"] = $clave;
			  $result["Query2"]=$Query;
			  
		  }
		 
          $result['ruta_foto']="/AD3M2SRC4/modules/fame/uploads/".$ds_ruta_foto;
		  
  }

  if($tab==2){
	  
	  #Realizamos las validaciones de los tags.
	  $nb_fos=RecibeParametroHTML('nb_fos');
	  $nb_tags=RecibeParametroHTML('nb_tags');
	  $fl_programa_nuevo=RecibeParametroHTML('fl_programa_nuevo');
	  $nb_har=RecibeParametroHTML('nb_har');
	  $nb_sof=RecibeParametroHTML('nb_sof');
	  $nb_lvl=RecibeParametroHTML('nb_lvl');
	  $ds_course_code=RecibeParametroHTML('ds_course_code');
	  $fl_programa_pre=RecibeParametroHTML('fl_programa_pre');
	  $fl_programa_sig=RecibeParametroHTML('fl_programa_sig');
	  $fl_course_code=RecibeParametroHTML('fl_course_code');
	  $ds_contenido_curso=RecibeParametroHTML('ds_contenido_curso');
	  
	  
	  $nb_fos = ( explode( ',', $nb_fos ) );
      $nb_tags = ( explode( ',', $nb_tags ) ); 	
      $fl_programa_nuevo = ( explode(',',$fl_programa_nuevo)); 	  
	  $nb_har = ( explode( ',', $nb_har ) ); 
      $nb_sof = ( explode( ',', $nb_sof ) );
	  $fl_programa_pre = ( explode(',',$fl_programa_pre)); 	
	  $fl_programa_sig = ( explode(',',$fl_programa_sig)); 
	  $fl_course_code = ( explode(',',$fl_course_code)); 
	  
	  # Borramos todas sus categorias para insertarlas de nuevo
      EjecutaQuery("DELETE FROM k_categoria_programa_sp WHERE fl_programa_sp = $clave");
      EjecutaQuery("DELETE FROM k_relacion_programa_sp WHERE fl_programa_sp_act = $clave");
      EjecutaQuery("DELETE FROM k_grade_programa_sp WHERE fl_programa_sp = $clave");
  
      EjecutaQuery("DELETE FROM k_course_code_prog_fame WHERE fl_programa_sp=$clave ");
  
  
	    # Categorias tipo software
	  foreach($nb_fos as $id=>$nb_fos){
		if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_fos)){
		  if(!empty($nb_fos)){
			$fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_fos', 'FOS')");
			EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
		  }
		}else{
			
		  $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_fos' AND fg_categoria = 'FOS'");
		  for($i=0;$row=RecuperaRegistro($rs);$i++) {
			$fl_cat_prog_sp = $row[0];
			EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
		  }
		}
	 }
	 
	 
	 # Categorias principales
     foreach($nb_tags as $id=>$nb_tags){
		if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_tags)){
			  if(!empty($nb_tags)){
				$fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_tags', 'CAT')");
				EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
			  }
		}else{
		  $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_tags' AND fg_categoria = 'CAT'");
		  for($i=0;$row=RecuperaRegistro($rs);$i++) {
			$fl_cat_prog_sp = $row[0];
			EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
		  }
		}
	}

	 ##grade
	 # Grados relacionados con el curso
	 foreach($fl_programa_nuevo as $id=>$fl_programa_nuevo){
		
		EjecutaQuery("INSERT INTO k_grade_programa_sp(fl_programa_sp, fl_grado) VALUES($clave, ".$fl_programa_nuevo.")");
	
	 }
	 
	 
	 # Categorias tipo hardwate
    foreach($nb_har as $id=>$nb_har){
    if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_har)){
      if(!empty($nb_har)){
        $fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_har', 'HAR')");
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }else{
      $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_har' AND fg_categoria = 'HAR'");
      for($i=0;$row=RecuperaRegistro($rs);$i++) {
        $fl_cat_prog_sp = $row[0];
        EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
      }
    }
	}
 
	 
	 
	 
	 
	
    # Categorias tipo software
    foreach($nb_sof as $id=>$nb_sof){
		if(!ExisteEnTabla('c_categoria_programa_sp', 'nb_categoria', $nb_sof)){
		  if(!empty($nb_sof)){
			$fl_cat_prog_sp = EjecutaInsert("INSERT INTO c_categoria_programa_sp (nb_categoria, fg_categoria) VALUES ('$nb_sof', 'SOF')");
			EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
		  }
		}else{
		  $rs = EjecutaQuery("SELECT fl_cat_prog_sp FROM c_categoria_programa_sp  WHERE nb_categoria = '$nb_sof' AND fg_categoria = 'SOF'");
		  for($i=0;$row=RecuperaRegistro($rs);$i++) {
			$fl_cat_prog_sp = $row[0];
			EjecutaQuery("INSERT INTO k_categoria_programa_sp (fl_cat_prog_sp, fl_programa_sp) VALUES ($fl_cat_prog_sp, $clave)");
		  }
		}
	}

    $Query="UPDATE c_programa_sp SET fg_level='$nb_lvl',ds_contenido='$ds_contenido_curso' WHERE fl_programa_sp=$clave ";
    EjecutaQuery($Query); 
 
	
	  #error que ya existe el codigo. que sea diferente a eeste programa actya
    $Query="SELECT count(1) FROM c_programa_sp WHERE ds_course_code='$ds_course_code' AND fl_programa_sp<>$clave ";
    $rowp=RecuperaValor($Query);
    $xiste=$rowp[0];

      if(!empty($xiste)){
          //error que ya existe un codig asi.
		  $result["err_ds_course_code"] = ObtenEtiqueta(2351);
          $result["err_ds_cour_code"]=1;
	  }else{		  
          $Query="UPDATE c_programa_sp SET ds_course_code='$ds_course_code' WHERE fl_programa_sp=$clave ";
          EjecutaQuery($Query); 	
		  
	  }

	 

	 # Prerequisitos
	 foreach($fl_programa_pre as $id=>$fl_programa_pre){
		
		 EjecutaQuery("INSERT INTO k_relacion_programa_sp(fl_programa_sp_act, fl_programa_sp_rel, fg_puesto) VALUES($clave, ".$fl_programa_pre.", 'ANT')");
 
		
	 }
	 
	 # Courses series
	 $contador_coruses=0;
	 foreach($fl_programa_sig as $id=>$fl_programa_sig){
		
		   $contador_coruses++;
		
		
		 EjecutaQuery("INSERT INTO k_relacion_programa_sp(fl_programa_sp_act, fl_programa_sp_rel, fg_puesto,no_orden) VALUES($clave, ".$fl_programa_sig.", 'SIG',".$contador_coruses.")");
  
		
	 }
	 # Courses series
	 foreach($fl_course_code as $id=>$fl_course_code){
		
	   EjecutaQuery("INSERT INTO  k_course_code_prog_fame (fl_programa_sp,fl_course_code)VALUES($clave,".$fl_course_code.") ");
 
	 }
	 
  
	  
  }


  if($tab==3){
	  
	  
      $ds_programa=RecibeParametroHTML('ds_programa');
      $ds_programa_esp=RecibeParametroHTML('ds_programa_esp');
      $ds_programa_fra=RecibeParametroHTML('ds_programa_fra');
	  $ds_learning=RecibeParametroHTML('ds_learning');
	  $ds_metodo=RecibeParametroHTML('ds_metodo');
	  $ds_requerimiento=RecibeParametroHTML('ds_requerimiento');
      $ds_learning_esp=RecibeParametroHTML('ds_learning_esp');
      $ds_learning_fra=RecibeParametroHTML('ds_learning_fra');
      $ds_metodo_esp=RecibeParametroHTML('ds_metodo_esp');
      $ds_metodo_fra=RecibeParametroHTML('ds_metodo_fra');
      $ds_requerimiento_esp=RecibeParametroHTML('ds_requerimiento_esp');
      $ds_requerimiento_fra=RecibeParametroHTML('ds_requerimiento_fra');
	  
	  $Query='UPDATE c_programa_sp SET   ds_programa="'.$ds_programa.'",ds_programa_esp="'.$ds_programa_esp.'",ds_programa_fra="'.$ds_programa_fra.'",  ds_learning="'.$ds_learning.'",ds_learning_esp="'.$ds_learning_esp.'",ds_learning_fra="'.$ds_learning_fra.'",  
             ds_metodo="'.$ds_metodo.'",ds_metodo_esp="'.$ds_metodo_esp.'",ds_metodo_fra="'.$ds_metodo_fra.'", 
             ds_requerimiento="'.$ds_requerimiento.'",ds_requerimiento_esp="'.$ds_requerimiento_esp.'" ,ds_requerimiento_fra="'.$ds_requerimiento_fra.'"     
             WHERE fl_programa_sp='.$clave.' ';
	  EjecutaQuery($Query); 
	  
	  
	  
	  
	  
	  
  }


  if($tab==4){
	  
	    
	  $nb_pagina=RecibeParametroHTML('nb_pagina');
	  $ds_pagina=RecibeParametroHTML('ds_pagina');
	  $ds_titulo=RecibeParametroHTML('ds_titulo');
	  
	  

	   # If NO exist fixed page with program insert new page
	  $row = RecuperaValor("SELECT cl_pagina_sp FROM c_pagina_sp WHERE fl_programa_sp=$clave");
	  $cl_pagina_sp = $row[0];
	  if(empty($cl_pagina_sp)){
		$Query  = "INSERT INTO c_pagina_sp (fl_programa_sp,nb_pagina,ds_pagina,ds_titulo,tr_titulo,ds_contenido,tr_contenido,fg_fijo) ";
		$Query .= "VALUES ($clave, '$nb_pagina', '$ds_pagina', '$ds_titulo', '$tr_titulo', '$ds_contenido', '$tr_contenido', '0') ";
		$cl_pagina_sp = EjecutaInsert($Query);
	  }
	  # If exist fixed page with program update
	  else{
		$Query  = "UPDATE c_pagina_sp SET nb_pagina = '$nb_pagina',ds_pagina = '$ds_pagina',ds_titulo = '$ds_titulo',tr_titulo = '$tr_titulo' ";
		$Query .= ",ds_contenido = '$ds_contenido',tr_contenido = '$tr_contenido' ,fg_fijo = '0' WHERE cl_pagina_sp = $cl_pagina_sp AND fl_programa_sp = $clave ";    
		EjecutaQuery($Query);
	  }  
	  
	  $result['cl_pagina_sp']=$cl_pagina_sp;
	  $result["Query"]=$Query;
	  
  }


  if($tab==5){
	  
      
      $fg_publicar=RecibeParametroBinario('fg_publicar');
      $fg_nuevo_programa=RecibeParametroBinario('fg_nuevo_programa');
      $fg_board = RecibeParametroBinario('fg_board');
      $fg_obligatorio = RecibeParametroBinario('fg_obligatorio');
      $fg_compartir_curso=RecibeParametroBinario('fg_compartir_curso');

      $Query="UPDATE c_programa_sp SET fg_publico='$fg_publicar',fg_compartir_curso='$fg_compartir_curso', fg_obligatorio='$fg_obligatorio',  fg_nuevo_programa='$fg_nuevo_programa'  WHERE fl_programa_sp=$clave ";
      EjecutaQuery($Query);

      $Query="UPDATE k_programa_detalle_sp SET fg_board='$fg_board' WHERE fl_programa_sp=$clave ";
      EjecutaQuery($Query);



  }


 echo json_encode((Object) $result);
  
 ?>


