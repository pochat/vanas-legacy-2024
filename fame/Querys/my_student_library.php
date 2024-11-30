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

  parse_str(!empty($_POST['extra_filters']['advanced_search'])?$_POST['extra_filters']['advanced_search']:NULL, $advanced_search);
  $_POST += $advanced_search;
  $fl_programa_sp=$_POST['extra_filters']['fl_programa_sp'];
  $cl_pagina_creada=$_POST['extra_filters']['cl_pagina_creada'];
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
 
 
  # Consulta para el listado
  $Query  = "SELECT fl_arch_student_library, nb_archivo,ds_titulo,fe_file,ds_descripcion FROM k_arch_student_library WHERE 1=1 AND fl_programa_sp=$fl_programa_sp  ";
  if(!empty($cl_pagina_creada))
  $Query .="AND cl_pagina=$cl_pagina_creada  ";
  
  $Query .="ORDER BY fl_arch_student_library DESC ";	
  // echo $Query;

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [

  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
   

      $fl_arch_student_library=$row[0];
      $nb_archivo=str_texto($row['nb_archivo']);
      $ds_titulo=str_texto($row['ds_titulo']);
      $fe_subida=GeneraFormatoFecha($row['fe_file']);
	  $ds_descripcion=str_texto($row['ds_descripcion']);
	  
      $fg_tipo_archivo=ObtenExtensionArchivo($nb_archivo);
	  
	  
	  
     
	  if(($fg_tipo_archivo=='jpg')||($fg_tipo_archivo=='png')||($fg_tipo_archivo=='jpeg')){		  
          $archivo=PATH_SELF_UPLOADS."/".$fl_instituto."/attachments/student_library/".$nb_archivo;	
		  $ruta_archivo="<img src='".$archivo."' width='35' height='35' >"; 
          $ruta_archivo_zoom="<img class='superbox-current-img' style='' src='".$archivo."'  >"; 		  
	  }else if($fg_tipo_archivo=='pdf'){		  
          $archivo="<i class='fa fa-file-pdf-o' aria-hidden='true'></i>";
          $ruta_archivo=$archivo;		  
		  $ruta_archivo_zoom=$archivo;
	  }else if(($fg_archivo=='xls')||($fg_archivo=='csv')){
	       $archivo="<i class='fa fa-file-excel-o' aria-hidden='true'></i>";
           $ruta_archivo=$archivo;	
		   $ruta_archivo_zoom=$archivo;
	  }else{		  
          $archivo="<i class='fa fa-file' aria-hidden='true'></i>";
          $ruta_archivo=$archivo;			
	  }
		  
	   

   
      $download = PATH_SELF_UPLOADS."/".$fl_instituto."/attachments/student_library/".$nb_archivo;
      $btn = "<a href='".$download."'  class='btn btn-xs btn-default' data-original-title='Download' download><i class='fa fa-download'></i></a>&nbsp;";
    
 
    // $ds_titulo="";
    // $nb_archivo="";
   //  $ds_descripcion="";
   //  $fe_subida="";
  //   $btn="";

    /** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
    echo '
    {
      "nb_archivo": "<div class=\'project-members\'><a href=\''.$download.'\' download>'.$nb_archivo.' </a></div> ",
      "ds_titulo": "<a href=\''.$download.'\' download>'.$ds_titulo.'</a>",
	  "ds_descripcion":"<a href=\''.$download.'\' download>'.$ds_descripcion.'</a>",
	  "ordera":"<span class=\'hidden\'>'.$fl_arch_student_library.'</span>",
	';
    if(($fg_tipo_archivo=='png')||($fg_tipo_archivo=='jpg')||($fg_tipo_archivo=='jpeg')){
        echo'"tipo_archivo": "<td align=\'center\'><a class=\'thumbnail\' style=\'margin:auto;border: solid 0px; background: transparent;\' data-toggle=\'popover\' href=\'javascript:void(0);\' data-placement=\'top\' data-full=\''.$archivo.'\'>  <img src=\''.$archivo.'\' width=\'35\' height=\'35\'>  </a></td>", ';
        
    }else{
        echo'"tipo_archivo": "<td align=\'center\' style=\'margin:auto;\'>'.$ruta_archivo.'</td>", '; 
    }
	echo'  
	  "fecha_creacion": "<a href=\''.$download.'\' download>'.$fe_subida.'</a>",
	  "delete": "'.$btn.' <a href=\'javascript:ElimnarArchivo('.$fl_arch_student_library.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a> "
      
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>

  ]
}