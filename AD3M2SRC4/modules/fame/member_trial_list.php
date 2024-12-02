<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
  $fl_instituto= $_POST['extra_filters']['fl_instituto'];

 
  
  $Query  = "(  "; 
  $Query  .= "  SELECT U.ds_nombres,U.ds_apaterno,U.ds_email,U.fl_perfil_sp,U.fg_activo, ''fg_confirmado,
			    CASE WHEN 
				R.fg_autorizado='0' THEN 'FA' ELSE fg_autorizado END fg_autorizado,''confirmar  
			    FROM c_instituto I  ";              
  $Query .= "   JOIN c_usuario U ON I.fl_instituto=U.fl_instituto 
			    LEFT JOIN k_responsable_alumno R ON R.fl_usuario=U.fl_usuario 
                LEFT JOIN  k_instituto_alumno IA ON IA.fl_usuario_sp=U.fl_usuario AND IA.fl_instituto<>$fl_instituto  
                WHERE I.fl_instituto=$fl_instituto and I.fg_tiene_plan='0' AND ( fl_perfil_sp <> ".PFL_ADMINISTRADOR." AND fl_perfil_sp <> ".PFL_ADM_CSF." ) ";
  $Query .= ")UNION(
			    SELECT A.ds_first_name ds_nombres ,A.ds_last_name ds_apaterno,A.ds_email,'' fl_perfil_sp,''fg_activo,A.fg_confirmado,''fg_autorizado,'FC'confirmar  
			    FROM k_envio_email_reg_selfp A
			    LEFT JOIN k_responsable_alumno B ON B.fl_envio_correo=A.fl_envio_correo
			    WHERE fl_invitado_por_instituto=$fl_instituto and fg_confirmado='0' AND fg_tipo_registro='S'
			
			)UNION(
               SELECT U.ds_nombres,U.ds_apaterno,U.ds_email,U.fl_perfil_sp,U.fg_activo,''fg_confirmado,''fg_autorizado,''confirmar   			
			   FROM k_instituto_alumno K
			   JOIN c_usuario U ON U.fl_usuario=K.fl_usuario_sp AND U.fl_instituto<>$fl_instituto			
			   WHERE K.fl_instituto=$fl_instituto 

            )
	";			 
			 
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
   
        $ds_fname=$row['ds_nombres'];
        $ds_lname=$row['ds_apaterno'];
        $ds_email=$row['ds_email'];
        $fl_perfil=$row['fl_perfil_sp'];
        $fg_activo=$row['fg_activo'];
		$nb_nombre=$ds_fname." ".$ds_lname;
		$confirmar=$row['confirmar'];
		
        
        if($fl_perfil==PFL_MAESTRO_SELF){  
            $perfil = "Teacher";  
        }
		if($fl_perfil==PFL_ESTUDIANTE_SELF){
            $perfil="Student";
            
        }
        
        
		if($fg_activo==0){
		    $color = "danger";
		    $status="Inactive";
		}else{
		    $color = "success";
		    $status="Active";
		}
		
		if($confirmar=='FC'){
			
			    $color = "danger";
				$status=ObtenEtiqueta(1092);
			
		}
      
      
    
            
      echo '
        {
           
            "name": "'.$nb_nombre.' ",
            "profile": "<td class=\"text-right\"><small class=\"text-muted\"> '.$perfil.'</small></td>",
            "estatus": "<td class=\"text-right\"><span class=\"label label-'.$color.'\">'.$status.'</span>  </td>",
            "espacio": "<td class=\"text-right\"> </td>"
            
            
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
