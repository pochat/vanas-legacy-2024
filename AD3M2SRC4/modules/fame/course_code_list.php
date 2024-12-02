<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  # Consulta para el listado
  $Query  = " SELECT fl_course_code,cl_course_code,nb_course_code,ds_level,ds_descripcion,ds_prerequisito,P.ds_pais,E.ds_provincia 
			  FROM  c_course_code C
			  JOIN c_pais P ON P.fl_pais=C.fl_pais
			  LEFT JOIN k_provincias E ON E.fl_provincia=C.fl_estado
			  WHERE 1=1 ";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
   
          
			$fl_course_code=$row['fl_course_code'];
            $cl_course_code=str_texto($row['cl_course_code']);
			$nb_course_code=str_texto($row['nb_course_code']);	
            $ds_level=str_texto($row['ds_level']);
            $ds_descripcion=str_texto($row['ds_descripcion']);
            $ds_prerequisito=str_texto($row['ds_prerequisito']);
            $ds_pais=str_texto($row['ds_pais']);
			$nb_estado=str_texto($row['ds_provincia']);

		  
      echo '
        {
           "cl_clave": "<a href=\'javascript:Envia(\"course_code_frm.php\",'.$fl_course_code.');\'>'.$cl_course_code.'</a>", 
           "name": "<a href=\'javascript:Envia(\"course_code_frm.php\",'.$fl_course_code.');\'>'.$nb_course_code.'</a>",  
           "level": "<a href=\'javascript:Envia(\"course_code_frm.php\",'.$fl_course_code.');\'>'.$ds_level.'</a>", 
		   "pais": "<a href=\'javascript:Envia(\"course_code_frm.php\",'.$fl_course_code.');\'>'.$ds_pais.'</a><br><small class=\'text-muted\'>'.$nb_estado.'</small>", 
           "descrip": "<a href=\'javascript:Envia(\"course_code_frm.php\",'.$fl_course_code.');\'>'.$ds_descripcion.'</a>", 
           "prerequisito": "<a href=\'javascript:Envia(\"course_code_frm.php\",'.$fl_course_code.');\'>'.$ds_prerequisito.'</a>",        
           "action": "<a href=\'javascript:Borra(\"course_code_del.php\",'.$fl_course_code.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>",
           "nada":"&nbsp;"
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
