<?php 
	# Libreria de funciones
	require("../lib/self_general.php");

	
	
	
	
	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);

  
    $fl_perfil= ObtenPerfilUsuario($fl_usuario);
  
  #Verificamos si, se ecnuantra en modo trial o en plan
  $fl_instituto=ObtenInstituto($fl_usuario);
  $Query="SELECT fg_tiene_plan FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_tiene_plan=$row[0];
  #Es para usuarios qu ya vencieron su plan y que a futuro pueden adquirir un plan.
  if($fl_perfil==PFL_ADMINISTRADOR){
      
     
      
      #Obtenemos fecha actual :
      $Query = "Select CURDATE() ";
      $row = RecuperaValor($Query);
      $fe_actual = str_texto($row[0]);
      $fe_actual=strtotime('+0 day',strtotime($fe_actual));
      $fe_actual= date('Y-m-d',$fe_actual);
      
      #Institutos que ya tuvieron plan
      if($fg_tiene_plan==1){ 
          $fe_terminacion= ObtenFechaFinalizacionContratoPlan($fl_instituto);
      }else{
          #Institutos que se quedaron en modo de prueba.                      
          $fe_terminacion=ObtenFechaFinalizacionTrial($fl_instituto); 
      }
      
      if($fe_terminacion < $fe_actual)
          $ya_expiro_fecha=1;
      else
          $ya_expiro_fecha=0;
      
      
      
      
  }
  
  
  
  
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
?>
  <div class="row padding-10">
  <?php
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  switch($fl_perfil){
    case PFL_ADMINISTRADOR: $menu = MENU_ADMIN_SELF;  break;
    case PFL_MAESTRO_SELF: $menu = MENU_MAESTRO_SELF;  break;
    case PFL_ESTUDIANTE_SELF: $menu = MENU_ALUMNO_SELF;  break;
  }  
  $Query  = "SELECT fn.fl_funcion, fn.nb_funcion, fn.nb_flash_default, cn.ds_resumen, cn.tr_resumen, lg.ds_ruta, fn.ds_icono_bootstrap ";
  $Query .= "FROM c_modulo md, c_funcion fn LEFT JOIN c_contenido cn on(cn.fl_funcion=fn.fl_funcion) ";
  $Query .= "LEFT JOIN k_liga lg ON(lg.fl_contenido=cn.fl_contenido) ";
  $Query .= "WHERE fn.fl_modulo=md.fl_modulo AND md.fl_modulo_padre=$menu ";
  if($ya_expiro_fecha == 1 )#MJD si el istituto ya vecio su fecha sea  trial o con plan solo mostrar billing.
      $Query .="AND fn.fl_funcion=155  ";
  
 
       if($fl_perfil==PFL_ESTUDIANTE_SELF){
       $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);	   
       if(empty($fg_puede_liberar_curso)){#MJD Solo mostrar el menu billing cuando el studiante sea de vanas
            $Query.="AND fn.fl_funcion<>181   AND fn.fl_funcion<>182  ";				
	   }

       }
			
  
  $Query .="ORDER BY fn.fl_funcion";
  $rs = EjecutaQuery($Query);
  for($i=0;$row = RecuperaRegistro($rs);$i++){
    $fl_funcion = $row[0];
    $nb_funcion = str_texto($row[1]);
    $nb_flash_default = $row[2];
    $ds_resumen = str_uso_normal(EscogeIdioma($row[3], $row[4]));
    $ds_ruta = $row[5];
    $ds_icono_bootstrap = $row[6];
    ?>
	
	<style>
		.po {
		margin:15 0 9px !important;
		line-height:1.3 !important;
		}
		</style>
	
    <a href="<?php echo "index.php#site/".$ds_ruta; ?>">
	<div class="col-sm-5 col-md-3 offset-mn-0 text-align-center cursor-pointer" style="height: 180px;">
      <i class="fa <?php echo $ds_icono_bootstrap; ?> fa-5x"></i><br/>
      <h4 style="line-height:1.3 !important;">
        <strong ><font color="black"><?php echo $nb_funcion; ?></font></strong>
		
        <small class="po" ><p style='margin:15 0 9px !important;line-height:1.3 !important;' class="po"><?php echo $ds_resumen; ?></p></small>
      </h4>
    </div></a>
    <?php
  }
  
  if($fg_tiene_plan==1){
  
  
  }else{
        if($ya_expiro_fecha == 0 )
         Info_Trial($fl_usuario);
  }
  
  
  if( ($fl_perfil==PFL_ESTUDIANTE_SELF) ){
      NotificacionCalificacionTeacher($fl_usuario);
  }
  ?>

  </div>
  

  