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
  if(!ValidaPermiso(FUNC_MAESTROS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_login = RecibeParametroHTML('ds_login');
  $ds_password = RecibeParametroHTML('ds_password');
  $ds_password_conf = RecibeParametroHTML('ds_password_conf');
  
  $mn_hour_rate_group_global=RecibeParametroFlotante('mn_hour_rate_group_global');
  $mn_hour_rate=RecibeParametroFlotante('mn_hour_rate');
  $fg_hour_rate=RecibeParametroBinario('fg_hour_rate');
  $fg_hour_rate_group=RecibeParametroBinario('fg_hour_rate_group');
  $mn_hour_rate_global_class=RecibeParametroFlotante('mn_hour_rate_global_class');
  $fg_hour_rate_global=RecibeParametroBinario('fg_hour_rate_global');
  $fe_periodo=$_POST['fe_periodo'];
  #Obtenemos mes anio
  $date=explode("-",$fe_periodo);
  $mes=$date[0];
  $anio=$date[1];


  $ds_nombres = RecibeParametroHTML('ds_nombres');
  $ds_apaterno = RecibeParametroHTML('ds_apaterno');
  $ds_amaterno = RecibeParametroHTML('ds_amaterno');
  $fg_genero = RecibeParametroHTML('fg_genero');
  $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
  $ds_email = RecibeParametroHTML('ds_email');
  $ds_number = RecibeParametroHTML('ds_number');
  $fl_perfil = RecibeParametroNumerico('fl_perfil');
  $nb_perfil = RecibeParametroHTML('nb_perfil');
  $fg_activo = RecibeParametroNumerico('fg_activo');
  $fe_alta = RecibeParametroFecha('fe_alta');
  $fe_ultacc = RecibeParametroFecha('fe_ultacc');
  $no_accesos = RecibeParametroNumerico('no_accesos');
  $fl_pais = RecibeParametroNumerico('fl_pais');
  $fl_zona_horaria = RecibeParametroNumerico('fl_zona_horaria');
  # Recibimos cada uno de los grupos y programas 
/*  $total = RecibeParametroNumerico('total');
  for($i=0;$i<$total;$i++){
    $fl_grupo[$i] = RecibeParametroNumerico('fl_grupo_'.$i);
    $fl_programa[$i] = RecibeParametroNumerico('fl_programa_'.$i);
    $mn_lecture_fee[$i] = RecibeParametroHTML('mn_lecture_fee_'.$i);
    $mn_extra_fee[$i] = RecibeParametroHTML('mn_extra_fee_'.$i);
    $fg_update[$i]=RecibeParametroBinario('fg_update_'.$i);
  }
  */
  # Recibimos las clases globales
 /* $total_globales = RecibeParametroNumerico('total_globales');
  for($j=0;$j<$total_globales;$j++){
    $fl_clase_global[$j] = RecibeParametroNumerico('fl_clase_global'.$j);
    $mn_cglobal_fee[$j] = RecibeParametroHTML('mn_cglobal_fee_'.$j);    
  }
  $total_grupales = RecibeParametroNumerico('total_grupales');
  for($k=0;$k<$total_grupales;$k++){
      $fl_clase_grupal[$k] = RecibeParametroNumerico('fl_clase_grupo'.$k);
      $mn_grupal_fee[$k] = RecibeParametroHTML('mn_grupal_fee_'.$k);
      $fg_update[$k]=RecibeParametroBinario('fg_updateg_'.$k);
  }
  */


  # Valida campos obligatorios
  if(empty($ds_login))
    $ds_login_err = ERR_REQUERIDO;
  if(empty($clave) AND empty($ds_password))
    $ds_password_err = ERR_REQUERIDO;
  if(empty($clave) AND empty($ds_password_conf))
    $ds_password_conf_err = ERR_REQUERIDO;
  if(empty($ds_nombres))
    $ds_nombres_err = ERR_REQUERIDO;
  if(empty($ds_apaterno))
    $ds_apaterno_err = ERR_REQUERIDO;
  if(empty($ds_email))
    $ds_email_err = ERR_REQUERIDO;
  if(empty($fl_perfil))
    $fl_perfil_err = ERR_REQUERIDO;
  if(empty($fe_nacimiento))
    $fe_nacimiento_err = ERR_REQUERIDO;
    
  # Verifica que tenga algun monto en teachers fee
  /*for($i=0;$i<$total;$i++){
    if($mn_lecture_fee[$i]=='')
      $mn_lecture_fee_err[$i] = ERR_REQUERIDO;
    if($mn_extra_fee[$i]=='')
      $mn_extra_fee_err[$i] = ERR_REQUERIDO;
  }
  */
  
  # Verifica que tenga algun monto en clases globales
  /*for($j=0;$j<$total_globales;$j++){
    if($mn_cglobal_fee[$j]<=0)
      $mn_cglobal_fee_err[$j] = ERR_REQUERIDO;    
  }
  */
  # Valida que no exista el registro
  if(empty($clave) AND !empty($ds_login) AND ExisteEnTabla('c_usuario', 'ds_login', $ds_login))
    $ds_login_err = ERR_DUPVAL;
  
  # Valida confirmacion de la contrasenia
  if((empty($clave)) AND ((!empty($ds_password) OR !empty($ds_password_conf)) AND $ds_password <> $ds_password_conf))
    $ds_password_conf_err = 101; // La contrase&ntilde; y su confirmaci&oacutE;n no coinciden.
  
  # Verifica que el formato de la fecha sea valido
  if(!empty($fe_nacimiento) AND !ValidaFecha($fe_nacimiento))
    $fe_nacimiento_err = ERR_FORMATO_FECHA;
  
  # Verifica que el formato del email sea valido
  if(!empty($ds_email) AND !ValidaEmail($ds_email))
    $ds_email_err = ERR_FORMATO_EMAIL;
  
  # Regresa a la forma con error
  $fg_error = $ds_login_err || $ds_password_err || $ds_password_conf_err || $ds_nombres_err || $ds_apaterno_err || $fe_nacimiento_err 
              || $ds_email_err || $fl_perfil_err;
  for($i=0;$i<$total;$i++){
    $fg_error .= $mn_lecture_fee_err[$i] || $mn_extra_fee_err[$i];    
  }
  # Regresa error de las clases globales si no tiene monto
  for($j=0;$j<$total;$j++){
    $fg_error .= $mn_cglobal_fee_err[$j];
  }
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_login' , $ds_login);
    Forma_CampoOculto('ds_login_err' , $ds_login_err);
    Forma_CampoOculto('ds_password_err' , $ds_password_err);
    Forma_CampoOculto('ds_password_conf_err' , $ds_password_conf_err);
    Forma_CampoOculto('ds_nombres' , $ds_nombres);
    Forma_CampoOculto('ds_nombres_err' , $ds_nombres_err);
    Forma_CampoOculto('ds_apaterno' , $ds_apaterno);
    Forma_CampoOculto('ds_apaterno_err' , $ds_apaterno_err);
    Forma_CampoOculto('ds_amaterno' , $ds_amaterno);
    Forma_CampoOculto('fg_genero' , $fg_genero);
    Forma_CampoOculto('fe_nacimiento' , $fe_nacimiento);
    Forma_CampoOculto('fe_nacimiento_err' , $fe_nacimiento_err);
    Forma_CampoOculto('ds_email' , $ds_email);    
    Forma_CampoOculto('ds_email_err' , $ds_email_err);
    Forma_CampoOculto('ds_number' , $ds_number);
    Forma_CampoOculto('fl_perfil' , $fl_perfil);
    Forma_CampoOculto('fl_perfil_err' , $fl_perfil_err);
    Forma_CampoOculto('nb_perfil' , $nb_perfil);
    Forma_CampoOculto('fg_activo' , $fg_activo);
    Forma_CampoOculto('fe_alta' , $fe_alta);
    Forma_CampoOculto('fe_ultacc' , $fe_ultacc);
    Forma_CampoOculto('no_accesos' , $no_accesos);
    Forma_CampoOculto('fl_pais' , $fl_pais);
    Forma_CampoOculto('fl_zona_horaria' , $fl_zona_horaria);
    Forma_CampoOculto('mn_hour_rate_group_global' , $mn_hour_rate_group_global);
    Forma_CampoOculto('mn_hour_rate' , $mn_hour_rate);
    Forma_CampoOculto('total', $total);
  /*  for($i=0;$i<$total;$i++){
      Forma_CampoOculto('mn_lecture_fee_'.$i, $mn_lecture_fee[$i]);
      Forma_CampoOculto('mn_lecture_fee_err_'.$i, $mn_lecture_fee_err[$i]);
      Forma_CampoOculto('mn_extra_fee_'.$i,$mn_extra_fee[$i] );
      Forma_CampoOculto('mn_extra_fee_err_'.$i, $mn_extra_fee_err[$i]);
      
    }
    Forma_CampoOculto('total_globales', $total_globales);
    for($j=0;$j<$total_globales;$j++){
      Forma_CampoOculto('mn_cglobal_fee_'.$j, $mn_cglobal_fee[$j]);
      Forma_CampoOculto('mn_cglobal_fee_err'.$j, $mn_cglobal_fee_err[$j]);
    }
    */
    /*for($k=0;$k<$total_grupales;$k++){
        Forma_CampoOculto('mn_grupal_fee_'.$k, $mn_grupal_fee_[$k]);
        Forma_CampoOculto('mn_grupal_fee__err'.$k, $mn_cglobal_fee_err[$k]);
    }
    */
    echo "\n</form>
<script>
 document.datos.submit();
</script></body></html>";
    exit;
  }

  # Prepara fechas en formato para insertar
  if(!empty($fe_nacimiento))
    $fe_nacimiento = "'".ValidaFecha($fe_nacimiento)."'";
  else
    $fe_nacimiento = "NULL";
  
  # Verifica si se esta insertando
  if(empty($clave)) {
      
      # Genera un identificador de sesion
      $cl_sesion_nueva = sha256($ds_login.$ds_nombres.$ds_apaterno.$ds_password);
      
      # Inserta el usuario
      $Query  = 'INSERT INTO c_usuario(ds_login, ds_password, cl_sesion, fg_activo, fe_alta, no_accesos, ';
      $Query .= 'ds_nombres, ds_apaterno, ds_amaterno, fg_genero, fe_nacimiento, ds_email, fl_perfil) ';
      $Query .= 'VALUES("'.$ds_login.'", "'.sha256($ds_password).'", "'.$cl_sesion_nueva.'", "'.$fg_activo.'", CURRENT_TIMESTAMP, 0, ';
      $Query .= '"'.$ds_nombres.'", "'.$ds_apaterno.'", "'.$ds_amaterno.'", "'.$fg_genero.'", '.$fe_nacimiento.', "'.$ds_email.'", '.$fl_perfil.') ';
      $fl_usuario = EjecutaInsert($Query);
      
      # Inserta el maestro
      $Query  = 'INSERT INTO c_maestro(fl_maestro, fl_pais, fl_zona_horaria,mn_hour_rate_group_global,mn_hour_rate) ';
      $Query .= 'VALUES('.$fl_usuario.', '.$fl_pais.', '.$fl_zona_horaria.','.$mn_hour_rate_group_global.','.$mn_hour_rate.') ';
      EjecutaQuery($Query);
  }
  else{
      
      # Actualiza los datos del usuario
      $Query  = 'UPDATE c_usuario SET fl_perfil='.$fl_perfil.', fg_activo="'.$fg_activo.'", ds_nombres="'.$ds_nombres.'", ds_apaterno="'.$ds_apaterno.'", ';
      $Query .= 'ds_amaterno="'.$ds_amaterno.'", fg_genero="'.$fg_genero.'", fe_nacimiento='.$fe_nacimiento.', ds_email="'.$ds_email.'" ';
      $Query .= 'WHERE fl_usuario='.$clave.' ';
      EjecutaQuery($Query);
      
      # Actualiza los datos del maestro
      $Query  = "UPDATE c_maestro SET fl_pais=$fl_pais, fl_zona_horaria=$fl_zona_horaria, ds_number='$ds_number',mn_hour_rate_group_global=$mn_hour_rate_group_global,mn_hour_rate=$mn_hour_rate,mn_hour_rate_global_class=$mn_hour_rate_global_class ";
      $Query .= "WHERE fl_maestro=$clave";
      EjecutaQuery($Query);
      

      #MJD por extraña razon se pierde la tarifa en los pagos del teacher.
      #verificamos los pagos del teacher y actualizamos.
      $year_current_actu = date('Y-m');
      $actual = strtotime($year_current_actu);
      $mesactual = date("Y-m", strtotime("-0 month", $actual));

      $Queryd="SELECT DISTINCT DATE_FORMAT(fe_periodo,'%M, %Y'), DATE_FORMAT(fe_periodo,'%Y-%m')  FROM k_maestro_pago
                            WHERE  DATE_FORMAT(fe_periodo,'%Y-%m') ='".$mesactual."' ";
      $rowd=RecuperaValor($Queryd);
      if(empty($rowd[0])){
          $year_current_actu = date('Y-m');
          $actual = strtotime($year_current_actu);
          $mesactual = date("Y-m", strtotime("-1 month", $actual));

      }

      $Query="SELECT fl_maestro_pago FROM k_maestro_pago where fl_maestro=$clave and DATE_FORMAT(fe_pagado,'%Y-%m')>='$mesactual' ";
      $row=RecuperaValor($Query);
      $fl_maestro_pago=$row['fl_maestro_pago'];
      if($fg_hour_rate){
          if(!empty($fl_maestro_pago))
              EjecutaQuery("UPDATE k_maestro_pago_det SET mn_tarifa_hr=$mn_hour_rate , mn_subtotal=$mn_hour_rate  where fl_maestro_pago=$fl_maestro_pago AND fg_tipo='A' AND fg_tipo='ACG' ");
      }

      #Update a todas las clases del periodo elegido.
      if($fg_hour_rate){
          
          $Query =" SELECT  d.fl_clase  
		            FROM c_grupo a, k_clase d, c_programa e, k_term f ,k_semana b 
		            LEFT JOIN c_leccion c ON(c.fl_leccion=b.fl_leccion) 
		            WHERE a.fl_term = b.fl_term 
		            AND a.fl_grupo=d.fl_grupo 
		            AND b.fl_semana=d.fl_semana 
		            AND c.fl_programa = e.fl_programa 
		            AND c.fl_programa=e.fl_programa 
		            AND a.fl_term = f.fl_term 
		            AND b.fl_term = f.fl_term 
		            AND DATE_FORMAT(d.fe_clase,'%m-%Y')='".$mes."-".$anio."' 
		            AND a.fl_maestro=$clave  
		            ORDER BY d.fe_clase  ";
          $rs = EjecutaQuery($Query);
          $total = CuentaRegistros($rs);
          for($i=0;$row= RecuperaRegistro($rs);$i++){

              $fl_clase=$row['fl_clase'];

              $Query="UPDATE k_clase SET mn_rate=$mn_hour_rate WHERE fl_clase=$fl_clase ";
              EjecutaQuery($Query);


          }




      }
      
      if($fg_hour_rate_global){
          
          $Querycg  = "SELECT  ";
          $Querycg .= "kcg.fl_clase_cg ";
          $Querycg .= "FROM c_clase_global cg ";
          $Querycg .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_maestro=$clave) ";
          $Querycg .= "WHERE DATE_FORMAT(kcg.fe_clase,'%m-%Y')='".$mes."-".$anio."' ";
          $rcg = EjecutaQuery($Querycg);
          for($j=$i+1;$row=RecuperaRegistro($rcg);$j++){

              $fl_clase=$row['fl_clase_cg'];

              $Query="UPDATE k_clase_cg SET mn_rate=$mn_hour_rate_global_class WHERE fl_clase_cg=$fl_clase ";
              EjecutaQuery($Query);

          }

      }

      if($fg_hour_rate_group){
          
          $Querygg  = "SELECT  a.fl_clase_grupo FROM k_clase_grupo a JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo JOIN c_grupo c ON c.fl_grupo=a.fl_grupo WHERE a.fl_maestro=$clave AND DATE_FORMAT(a.fe_clase,'%m-%Y')='".$mes."-".$anio."' ";
          $rgg = EjecutaQuery($Querygg);
          for($k=$j+1;$row=RecuperaRegistro($rgg);$k++){

              $fl_clase = $row['fl_clase_grupo'];

              $Query="UPDATE k_clase_grupo SET mn_rate=$mn_hour_rate_group_global WHERE fl_clase_grupo=$fl_clase ";
              EjecutaQuery($Query);



          }


      }

      
  }

/*
    # Inserta o actualiza las tarifas 
    for($i=0;$i<$total;$i++){
      if(ExisteEnTabla('k_maestro_tarifa','fl_maestro',$clave) AND ExisteEnTabla('k_maestro_tarifa','fl_programa',$fl_programa[$i]) 
      AND ExisteEnTabla('k_maestro_tarifa','fl_grupo',$fl_grupo[$i])){

          //si estan vacios tomara el default.
          if(empty($mn_lecture_fee[$i])){
              $mn_lecture_fee_=$mn_hour_rate;
          }else{
              $mn_lecture_fee_=$mn_lecture_fee[$i];
          }
          if(empty($mn_extra_fee[$i])){
              $mn_extra_fee_=$mn_hour_rate;
          }else{
              $mn_extra_fee_=$mn_extra_fee[$i];
          }

          if($fg_hour_rate){//aplica el default
              $mn_lecture_fee_=$mn_hour_rate;
              $mn_extra_fee_=$mn_hour_rate;
          }


         
           $Query ="UPDATE k_maestro_tarifa SET mn_lecture_fee=$mn_lecture_fee_,mn_extra_fee=$mn_extra_fee_ ";
           $Query.="WHERE fl_maestro=$clave AND fl_programa=$fl_programa[$i] AND fl_grupo=$fl_grupo[$i] ";
           EjecutaQuery($Query);
          


      }
      else{

          //si estan vacios tomara el default.
          if(empty($mn_lecture_fee[$i])){
              $mn_lecture_fee_=$mn_hour_rate;
          }else{
              $mn_lecture_fee_=$mn_lecture_fee[$i];
          }
          if(empty($mn_extra_fee[$i])){
              $mn_extra_fee_=$mn_hour_rate;
          }else{
              $mn_extra_fee_=$mn_extra_fee[$i];
          }

          if($fg_hour_rate){//aplica el default
              $mn_lecture_fee_=$mn_hour_rate;
              $mn_extra_fee_=$mn_hour_rate;
          }

          $Query="INSERT INTO k_maestro_tarifa (fl_maestro,fl_programa,fl_grupo,mn_lecture_fee,mn_extra_fee) ";
          $Query.="VALUES ($clave,$fl_programa[$i],$fl_grupo[$i],$mn_lecture_fee_,$mn_extra_fee_) ";
          EjecutaQuery($Query);
      }
      
    }
    # Inserta o actualiza las tarifas de las clases globales
    for($j=0;$j<$total_globales;$j++){
      if(ExisteEnTabla('k_maestro_tarifa_cg','fl_maestro',$clave, 'fl_clase_global', $fl_clase_global[$j], True)){


          if($fg_hour_rate_global){//aplica el default
              $mn_global=$mn_hour_rate_global_class;
          }else{
              
              $mn_global=$mn_cglobal_fee[$j];

          }
        
            $Query ="UPDATE k_maestro_tarifa_cg SET mn_cglobal_fee=$mn_global ";
            $Query.="WHERE fl_maestro=$clave AND fl_clase_global=$fl_clase_global[$j] ";
            EjecutaQuery($Query);
        
      }
      else{

          if($fg_hour_rate_global){//aplica el default
              $mn_global=$mn_hour_rate_global_class;
          }else{
              
              $mn_global=$mn_cglobal_fee[$j];

          }


        $Query="INSERT INTO k_maestro_tarifa_cg (fl_maestro,fl_clase_global,mn_cglobal_fee) ";
        $Query.="VALUES ($clave,$fl_clase_global[$j],$mn_global) ";
        EjecutaQuery($Query);
      }
      
    } 
    
    #Inserta clases grupales.
    # Inserta o actualiza las tarifas de las clases globales grupales
    for($k=0;$k<$total_grupales;$k++){
        if(ExisteEnTabla('k_maestro_tarifa_gg','fl_maestro',$clave, 'fl_clase_grupo', $fl_clase_grupal[$k], True)){

            //si estan vacios tomara el default.
            if(empty($mn_grupal_fee[$k])){
                $mn_grupal_fee_=$mn_hour_rate_group_global;
            }else{
                $mn_grupal_fee_=$mn_grupal_fee[$k];
            }
            #if(empty($mn_grupal_fee[$i])){
           #     $mn_grupal_fee_=$mn_hour_rate_group_global;
          #  }else{
         #       $mn_grupal_fee_=$mn_grupal_fee[$k];
        #    }

            if($fg_hour_rate_group){//aplica el default
                $mn_grupal_fee_=$mn_hour_rate_group_global;
               
            }

            if($fg_update[$k]){

                $Query=" UPDATE k_maestro_tarifa_gg  SET mn_cgrupo=$mn_grupal_fee_ ";
                $Query.=" WHERE fl_maestro=$clave and  fl_clase_grupo =$fl_clase_grupal[$k] ";
                EjecutaQuery($Query);
            }

            //$Query  = "UPDATE k_maestro_tarifa_cg SET mn_cgrupo=$mn_grupal_fee_ ";
            //$Query .= "WHERE fl_maestro=$clave AND fl_clase_grupo=$fl_clase_grupal[$k]";
        }
        else{

            //si estan vacios tomara el default.
            if(empty($mn_grupal_fee[$k])){
                $mn_grupal_fee_=$mn_hour_rate_group_global;
            }else{
                $mn_grupal_fee_=$mn_grupal_fee[$k];
            }
           # if(empty($mn_grupal_fee[$i])){
          #      $mn_grupal_fee_=$mn_hour_rate_group_global;
         #   }else{
        #        $mn_grupal_fee_=$mn_grupal_fee[$k];
         #   }

            if($fg_hour_rate_group){//aplica el default
                $mn_grupal_fee_=$mn_hour_rate_group_global;
                
            }

            $Query="INSERT INTO k_maestro_tarifa_gg (fl_maestro,fl_clase_grupo,mn_cgrupo) ";
            $Query.="VALUES ($clave,$fl_clase_grupal[$k],$mn_grupal_fee_) ";
            EjecutaQuery($Query);
        }
       
    } 
  }
  */
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>
