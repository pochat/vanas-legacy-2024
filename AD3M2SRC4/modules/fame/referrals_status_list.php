<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  # Consulta para el listado
  
  $Query  = "(SELECT b.fl_envio_correo, b.ds_email, nb_programa, a.fe_alta,a.fg_confirmado,b.fl_invitado_por_usuario, CONCAT(u.ds_nombres,' ',u.ds_apaterno) AS nombre, CASE WHEN r.fg_autorizado='0' THEN 'FA' ELSE r.fg_autorizado END fg_autorizado,'' fl_pais  ";
  $Query .= "FROM k_envio_email_reg_selfp a
				JOIN c_desbloquear_curso_alumno b ON a.fl_envio_correo=b.fl_envio_correo
				JOIN c_programa_sp c ON b.fl_programa_sp=c.fl_programa_sp 
				JOIN c_usuario u  ON u.fl_usuario=b.fl_invitado_por_usuario 
				LEFT JOIN k_responsable_alumno r ON r.fl_envio_correo=a.fl_envio_correo 
   ";
  $Query .= "WHERE  fg_desbloquear_curso='1' ORDER BY b.fl_envio_correo DESC  )UNION( ";

  $Query .= "   
             SELECT a.fl_envio_correo,a.ds_email, '".ObtenEtiqueta(2150)."' nb_programa ,a.fe_alta,a.fg_confirmado,a.fl_usu_invita fl_invitado_por_usuario,CONCAT(a.ds_first_name,' ',a.ds_last_name)AS nombre,CASE WHEN r.fg_autorizado ='0' THEN 'FA' ELSE  r.fg_autorizado END fg_autorizado  ,a.fl_pais      
			   FROM k_envio_email_reg_selfp a
            left JOIN k_responsable_alumno r ON r.fl_envio_correo =a.fl_envio_correo 
				WHERE a.fg_desbloquear_curso='1' AND a.fl_usu_invita=642   ORDER BY a.fl_envio_correo DESC
  
  )  ORDER BY fl_envio_correo DESC	 ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
   
            $fl_envio_correo = $row[0];
			$ds_email = $row[1];
			$nb_programa = $row[2];
			$fe_alta = $row[3];
            $fl_pais=$row['fl_pais'];
			
			#DAMOS FORMATO DIA,MES, ANÑO
            $date = date_create($fe_alta);
            $fe_alta=date_format($date,'F j, Y');
			$fe_hora=date_format($date,'g:i a');
			$fe_alta=$fe_alta." at ".$fe_hora." <i>(Pacific time)</i>";
			$fe_alta_order=str_texto($row['fe_alta']);
            
            
			$fg_confirmado=$row[4];
			$ds_nombre_usuario=str_texto($row[6]);
            $fg_autorizado=str_texto($row[7]);
            
            
            $Query="SELECT ds_pais FROM c_pais WHERE fl_pais=$fl_pais  ";
            $rowpai=RecuperaValor($Query);
            $nb_pais=str_texto(!empty($rowpai[0])?$rowpai[0]:NULL);
            
            
            
			if($fg_confirmado==1){
               
                if($fg_autorizado=='FA'){
		          $color="danger";
		          $etq=ObtenEtiqueta(2126);//falta condfirmacion del papa
                  $status_student="Inactive";
                  
		        }else{
			      $color="success";
			      $etq=ObtenEtiqueta(2207);
                  
                }
              
                $Querym="SELECT fl_usuario,ds_login FROM c_usuario WHERE ds_email='$ds_email'  ";
                $ro=RecuperaValor($Querym);
                $fl_usu=!empty($ro['fl_usuario'])?$ro['fl_usuario']:NULL;
                $ds_login=str_texto(!empty($ro['ds_login'])?$ro['ds_login']:NULL);
                $id_student="B2C-".$ds_login;
                
                if(empty($fl_pais)){
                    
                    $Query="SELECT p.ds_pais 
                            FROM k_usu_direccion_sp a 
                            JOIN c_pais p on a.fl_pais=p.fl_pais WHERE fl_usuario_sp=$fl_usu "; 
                    $rowpa=RecuperaValor($Query);
                    $nb_pais=str_texto(!empty($rowpa[0])?$rowpa[0]:NULL);
                    
                }
                
              
			}else{
			  $color="danger";
			  $etq=ObtenEtiqueta(2102);
              
                 $id_student="B2C-".$fl_envio_correo;
              
			}
		  
           
		  
            #Recuperamos si aun no ha sido confirmado autorizado por el papa.
            //if($fg_autorizado=='FA'){ 
            //    $etq_autorizado=ObtenEtiqueta(2126);
            //}else
            //    $etq_autorizado="";
            
		  
		  
		
			$fl_course_code=isset($row['fl_course_code'])?$row['fl_course_code']:NULL;
      $cl_course_code=str_texto(isset($row['cl_course_code'])?$row['cl_course_code']:NULL);
			$nb_course_code=str_texto(isset($row['nb_course_code'])?$row['nb_course_code']:NULL);	
      $ds_level=str_texto(isset($row['ds_level'])?$row['ds_level']:NULL);
      $ds_descripcion=str_texto(isset($row['ds_descripcion'])?$row['ds_descripcion']:NULL);
      $ds_prerequisito=str_texto(isset($row['ds_prerequisito'])?$row['ds_prerequisito']:NULL);
      $ds_pais=str_texto(isset($row['ds_pais'])?$row['ds_pais']:NULL);
			$nb_estado=str_texto(isset($row['ds_provincia'])?$row['ds_provincia']:NULL);

		                 
          // "action": "<a href=\'javascript:Borra(\"course_code_del.php\",'.$fl_course_code.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>"  
      echo '
        {
		   "name_user": "'.$ds_nombre_usuario.'<br><i><small class=\'text-muted\'>'.$id_student.'</small></i>",
           "email": "'.$ds_email.'", 
           "programa": "'.$nb_programa.'",  
           "status": "<span class=\'label label-'.$color.'\'>'.$etq.'</span>",
           "pais":"'.$nb_pais.'",
		   "fecha": "'.$fe_alta.'", 
                 
           "action": "<span style=\'color:#fff0;\'>'.$fe_alta_order.'</span>   &nbsp;"
           
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
