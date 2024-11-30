<?php

	# Libreria de funciones
	require("../lib/self_general.php");

  # Recibe categoria seleccionada
  $categoria = RecibeParametroHTML('categoria');
  $fg_principal = RecibeParametroNumerico('principal');
  $fl_usuario_sp = ValidaSesion(False,0, True);
  
  # Insertamos valor actual 
  if($fg_principal)
    $posicion = 1;
  else{
    $posicion_p = RecuperaValor("SELECT no_filtro, fl_fto_cat_sp FROM k_filtro_categoria_fame  WHERE fl_usuario_sp=$fl_usuario_sp ORDER BY no_filtro DESC LIMIT 1");
    $posicion = ($posicion_p[0] + 1);
  }
 
  # Si es nueva busqueda borramos los filtros anteriores hechos por el usuario
  if($posicion == 1){
    $rs = EjecutaQuery("SELECT fl_fto_cat_sp FROM k_filtro_categoria_fame a WHERE fl_usuario_sp = $fl_usuario_sp");
    for($i=0;$row = RecuperaRegistro($rs);$i++){  
      EjecutaQuery("DELETE FROM k_filtro_sugerencia_fame WHERE fl_fto_cat_sp = $row[0]");
    }
    EjecutaQuery("DELETE FROM k_filtro_categoria_fame WHERE fl_usuario_sp = $fl_usuario_sp");
    EjecutaQuery("DELETE FROM k_filtro_elimina_fame WHERE fl_usuario_sp = $fl_usuario_sp");
  }
    
  #Definimos tipo de categoria
  if(is_numeric($categoria)){ #viene de la tabla categoria_programa
    $fg_tipo = 'CAT';
  }else{
    if((substr($categoria, 0, 2))=="G-"){ #Grado, school level
      $fg_tipo = 'GDO';
    }elseif((substr($categoria, 0, 2))=="P-"){ #codigo oprograma course-code
      $fg_tipo = 'PRO';
    }else{
        if((substr($categoria,0,2))=="K-"){#PARA MAPING CURRIULUM
            $fg_tipo="MAP";
        }else
            $fg_tipo = 'LVL';#Level.
    }    
  }
  
  $fl_fto_cat_sp = EjecutaInsert("INSERT INTO k_filtro_categoria_fame (fl_cat_prog_sp, fl_usuario_sp, no_filtro, fg_tipo) VALUES ('$categoria', '$fl_usuario_sp', $posicion, '$fg_tipo')");  
  
  /***** Para sugerencias *****/
  if($fg_tipo=='MAP'){#CUrriculum maping
      $categoria = substr($categoria, 2);
      
        if($posicion == 1)
            $Query="SELECT fl_programa_sp FROM k_course_code_prog_fame WHERE fl_course_code =$categoria ";#se busca en primera instancia el filtro
        else    
            $Query="";#Se buscan en los filtros posterioes a un intento
  
       $rs = EjecutaQuery($Query);
       for($i=0;$row = RecuperaRegistro($rs);$i++){ 
           $fl_programa_sp = str_texto($row[0]);
           
           $cont_lecciones = RecuperaValor("SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa_sp");
           
           if($cont_lecciones[0] > 0){
               EjecutaQuery("INSERT INTO k_filtro_sugerencia_fame (fl_programa_sp, fl_fto_cat_sp) VALUES ($fl_programa_sp, $fl_fto_cat_sp)");  
           }
           
           
       }
        
        
        
  }  
  elseif($fg_tipo == 'CAT'){
    if($posicion == 1)
      $Query = "SELECT fl_programa_sp FROM k_categoria_programa_sp WHERE fl_cat_prog_sp = $categoria ";
    else
      $Query = "SELECT a.fl_programa_sp FROM k_categoria_programa_sp a, k_filtro_sugerencia_fame b WHERE b.fl_fto_cat_sp = $posicion_p[1] AND a.fl_cat_prog_sp = $categoria AND a.fl_programa_sp = b.fl_programa_sp ";
    
    $rs = EjecutaQuery($Query);
    for($i=0;$row = RecuperaRegistro($rs);$i++){
        $fl_programa_sp = str_texto($row[0]);
        $cont_lecciones = RecuperaValor("SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa_sp");
        if($cont_lecciones[0] > 0){
            EjecutaQuery("INSERT INTO k_filtro_sugerencia_fame (fl_programa_sp, fl_fto_cat_sp) VALUES ($fl_programa_sp, $fl_fto_cat_sp)");
        }
    }
  }
  elseif($fg_tipo == 'GDO'){
    $categoria = substr($categoria, 2);
    if($posicion == 1)
      $Query = "SELECT fl_programa_sp FROM k_grade_programa_sp WHERE fl_grado = $categoria ";
    else
      $Query = "SELECT a.fl_programa_sp FROM k_grade_programa_sp a, k_filtro_sugerencia_fame b WHERE b.fl_fto_cat_sp = $posicion_p[1] AND a.fl_grado = $categoria AND a.fl_programa_sp = b.fl_programa_sp ";    
    $rs = EjecutaQuery($Query);
    for($i=0;$row = RecuperaRegistro($rs);$i++){
      $fl_programa_sp = str_texto($row[0]);      
      $cont_lecciones = RecuperaValor("SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa_sp");
       
      if($cont_lecciones[0] > 0){
        EjecutaQuery("INSERT INTO k_filtro_sugerencia_fame (fl_programa_sp, fl_fto_cat_sp) VALUES ($fl_programa_sp, $fl_fto_cat_sp)");  
      }
    }  
  }
  elseif($fg_tipo == 'PRO'){
    $categoria = substr($categoria, 2);
    $Query = "SELECT fl_programa_sp FROM c_programa_sp WHERE fl_programa_sp = $categoria AND fg_publico='1' ";
    $rs = EjecutaQuery($Query);
    for($i=0;$row = RecuperaRegistro($rs);$i++){
      $fl_programa_sp = str_texto($row[0]);
      
      $cont_lecciones = RecuperaValor("SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa_sp");
       
      if($cont_lecciones[0] > 0){
        EjecutaQuery("INSERT INTO k_filtro_sugerencia_fame (fl_programa_sp, fl_fto_cat_sp) VALUES ($fl_programa_sp, $fl_fto_cat_sp)");      
      }
    }
  }
  else{
    if($posicion == 1)
      $Query = "SELECT fl_programa_sp FROM c_programa_sp WHERE fg_level = '$categoria' AND fg_publico='1' ";
    else
      $Query = "SELECT a.fl_programa_sp FROM c_programa_sp a, k_filtro_sugerencia_fame b WHERE b.fl_fto_cat_sp = $posicion_p[1] AND a.fg_level = '$categoria' AND a.fl_programa_sp = b.fl_programa_sp AND a.fg_publico='1' ";    
    $rs = EjecutaQuery($Query);
    for($i=0;$row = RecuperaRegistro($rs);$i++){
      $fl_programa_sp = str_texto($row[0]);
      
      $cont_lecciones = RecuperaValor("SELECT COUNT(1) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa_sp");
       
      if($cont_lecciones[0] > 0){
        EjecutaQuery("INSERT INTO k_filtro_sugerencia_fame (fl_programa_sp, fl_fto_cat_sp) VALUES ($fl_programa_sp, $fl_fto_cat_sp)");      
      }
    }
  }
  
  
  
  
  
  
  
  
  
  
  
?>