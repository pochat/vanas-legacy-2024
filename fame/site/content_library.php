<?php
	# Libreria de funciones
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
	
	# Receive Parameters
  $fl_programa = RecibeParametroNumerico('fl_programa');
  
  # Obtenemos si el usuario esta en trial
  $no_dias = ObtenDiasTrial($fl_instituto);
  $fg_plan = Obten_Status_Trial($fl_instituto);  
  $result['no_dias'] = $no_dias;  

  #Verifica si es b2c.
  #Verifica que el instituto sea b2c.
  $Query="SELECT fg_b2c,no_tot_licencias_b2c FROM c_instituto WHERE fl_instituto=$fl_instituto "; 
  ////$row=RecuperaValor($Query);
  $fg_b2c=$row['fg_b2c'];
  if($fg_b2c==1){
      $fg_plan=1;
 }


  if(!empty($fg_plan)){
    if(empty($fl_programa)) {
      $result['program'] = 
        "<div class='text-center error-box'>
          <h3 class='error-text tada animated' style='font-size: 50px;'><i class='fa fa-times-circle text-danger error-icon-shadow'></i>".ObtenEtiqueta(1980)."</h3>
        </div>";
    }
    else{		
        
      #Verificamos si el programa fue creado por Instituto.
      $Queryp="SELECT fl_instituto,fl_programa_sp FROM c_programa_sp WHERE fl_programa_sp=$fl_programa ";
      $rop=RecuperaValor($Queryp);
      $fl_instituto_existe=$rop['fl_instituto'];


      # Recupera contenido de la pagina fija
      $Query  = "SELECT ds_titulo, tr_titulo, ds_contenido, tr_contenido, fg_fijo, cl_pagina_sp ";
      $Query .= "FROM c_pagina_sp ";
      $Query .= "WHERE  fl_programa_sp=$fl_programa ";
      $Query .= "ORDER BY fl_programa_sp DESC";

      $row = RecuperaValor($Query);
      $titulo = str_uso_normal(EscogeIdioma($row[0], $row[1]));
      $contenido = str_uso_normal(EscogeIdioma(html_entity_decode($row[2]), $row[3]));
      $cl_pagina_sp = $row[5];
      
      # Verificamos si tiene video
      if(ExisteEnTabla('k_video_contenido_sp', 'fl_programa_sp', $fl_programa)){
        $contenido .= "<div class='row text-align-center'>";
          $contenido .= "<div clas='txt-align-center'><hr><p><h1>Videos</h1></p><hr></div>";
          # Videos
          $ruta = ObtenConfiguracion(116)."/vanas_videos/fame/library/video_".$cl_pagina_sp."_".$fl_programa;
          $Query  = "SELECT ds_ruta_video, fl_video_contenido_sp ";
          $Query .= "FROM k_video_contenido_sp ";
          $Query .= "WHERE fl_programa_sp=$fl_programa ";
          $Query .= "ORDER BY fl_programa_sp DESC";
          $rsv = EjecutaQuery($Query);
          $videos=NULL;
          for($i=0;$row=RecuperaRegistro($rsv);$i++){
            $ds_ruta_video = $row[0];
            $fl_video_contenido = $row[1];
            # Videos    
            $videos .= "<div class='col-sm-12 col-md-12 col-lg-3 padding-5 cursor-pointer' onclick='ShowVideos(".$fl_video_contenido.")'><div id='myCarousel-2' class='carousel slide'><div class='carousel-inner'> ";
            $videos .= "<div class='item active' style='width:auto;'>";
            $videos .= "<img class='cursor-pointer' src='".$ruta."/video_".$fl_video_contenido."/video_".$fl_video_contenido."_hd/img_1.png' alt=''>";
            $videos .= "<div class='carousel-caption caption-center'><span class='glyphicon glyphicon-play-circle' style='font-size:80px;'></div>";
            $videos .= "</div>";
            $videos .= "</div></div></div>";
          }
  
        $contenido .= $videos."</div>";
      }

      if(!empty($fl_instituto_existe)){
          #Recuperamos la tabla del student library
          # Consulta para el listado
          $Query  = "SELECT fl_arch_student_library, nb_archivo,ds_titulo,fe_file,ds_descripcion FROM k_arch_student_library WHERE 1=1 AND fl_programa_sp=$fl_programa_sp  ";
          if(!empty($cl_pagina_sp)){
              $Query .="AND cl_pagina=$cl_pagina_sp  ";
          }
          $Query .="ORDER BY fl_arch_student_library DESC ";

          

      }
      
      $result['program'] = $contenido;
    }
  }
  else{
    $result['program'] = 
        "<div class='text-center error-box'>
          <h3 class='error-text tada animated'  style='font-size: 50px;'><i class='fa fa-times-circle text-danger error-icon-shadow'></i> ".ObtenEtiqueta(1981)."</h3>
        </div>";
  }

	echo json_encode((Object) $result);
?>
