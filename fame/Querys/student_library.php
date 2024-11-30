<?php
  	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_log = ValidaSesion(False,0, True);

  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario_log);

  # Recibe Parametros 
  $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp', true);
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp', true);
  $fl_alumno = RecibeParametroNumerico('fl_alumno', true);
  $fl_maestro = RecibeParametroNumerico('fl_teacher', true);

  #Verificamos si existe la pag del programa.
  $Query="SELECT cl_pagina_sp FROM c_pagina_sp WHERE fl_programa_sp=$fl_programa_sp ";
  $row=RecuperaValor($Query);
  $cl_pagina_creada=$row['cl_pagina_sp'];


  
  # Consulta para el listado
  $Query  = "SELECT fl_arch_student_library, nb_archivo,ds_titulo,fe_file,ds_descripcion FROM k_arch_student_library WHERE 1=1 AND fl_programa_sp=$fl_programa_sp  ";
  if(!empty($cl_pagina_creada))
      $Query .="AND cl_pagina=$cl_pagina_creada  "; 
  $Query .="ORDER BY fl_arch_student_library DESC ";
  

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
"data": [

<?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
      $fl_arch_student_library = $row['fl_arch_student_library'];
      $nb_archivo = str_texto($row['nb_archivo']);
      $ds_titulo = str_texto($row['ds_titulo']);
      $ds_descripcion = str_texto($row['ds_descripcion']);
      $fe_file = $row['fe_file'];
   
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
      $btn = "<a href='".$download."' class='btn btn-xs btn-default' data-original-title='Download' download><i class='fa fa-download'></i></a>&nbsp;";
     
      
      echo '
      {
       "name": "<a href=\''.$download.'\' download>'.$nb_archivo.'</a>",
       "version": "<a href=\''.$download.'\' download>'.$ds_titulo.'</a>",
       "descr": "<a href=\''.$download.'\' download>'.$ds_descripcion.'</a>",
       ';

      if(($fg_tipo_archivo=='png')||($fg_tipo_archivo=='jpg')||($fg_tipo_archivo=='jpeg')){
          echo'"tipo_archivo": "<td align=\'center\'><a class=\'thumbnail\' style=\'margin:auto;border: solid 0px; background: transparent;\' data-toggle=\'popover\' href=\'javascript:void(0);\' data-placement=\'top\' data-full=\''.$archivo.'\'>  <img src=\''.$archivo.'\' width=\'35\' height=\'35\'>  </a></td>", ';
          
      }else{
          echo'"tipo_archivo": "<td align=\'center\' style=\'margin:auto;\'>'.$ruta_archivo.'</td>", '; 
      }

      echo'
       "date": "<a href=\''.$download.'\' download>'.$fe_file.'</a>",
       "user": "<div class=\'project-members\'><a href=\''.$download.'\'  rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombres.'\' download><img src=\''.$ds_avatar.'\' width=\'35\' height=\'35\'/></a></div>",
      "btns": "'.$btn.'"
      }';
       if($registros>1 && $i<=($registros-1))
        echo ",";
      else
        echo "";
  }
?>
 ]
}