<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
 
  #muestra los institutos que ya se encuentran registrados
  $Query="SELECT I.fl_instituto,I.fl_usuario_sp,I.ds_instituto,I.no_usuarios,I.ds_codigo_area,I.no_telefono,P.ds_pais ,U.ds_nombres,U.ds_apaterno,I.fe_creacion,U.fg_activo,I.fe_trial_expiracion,I.cl_tipo_instituto,I.fl_instituto_rector,U.ds_login,I.school_id   
             
            FROM c_instituto I 
            JOIN c_pais P ON P.fl_pais=I.fl_pais
            JOIN c_usuario U ON U.fl_usuario=I.fl_usuario_sp 
            WHERE 1=1 AND I.fl_instituto<>1  AND I.fg_tiene_plan='1'  ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
         $fl_instituto=$row['fl_instituto'];
         $fl_usuario=$row['fl_usuario_sp'];
         $nb_instituto=$row['ds_instituto'];
         $no_usuarios=$row['no_usuarios'];
         $ds_codigo_area=$row['ds_codigo_area'];
         $no_telefono=$row['no_telefono'];
         $nb_pais=$row['ds_pais'];
         $nb_admin=$row['ds_nombres']." ".$row['ds_apaterno'];
         $fe_creacion=$row['fe_creacion'];
         $fe_final_periodo_prueba=$row['fe_trial_expiracion'];
         $fg_activo=$row['fg_activo'];
         $cl_tipo_instituto=$row['cl_tipo_instituto'];
		 $fl_instituto_rector=$row['fl_instituto_rector'];
         $ds_login=$row['ds_login'];
		 $school_id=$row['school_id'];
        

         if($cl_tipo_instituto==2){
             $nb_rector=ObtenEtiqueta(2524);
         }else{
             $nb_rector="";
         }

        #Recuperamos datos del isntituto Rector.
		 if(!empty($fl_instituto_rector)){
			$Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto_rector ";
			$ro=RecuperaValor($Query);
			$nb_instituto_rector='<small class=\'text-muted\' ><i>'.ObtenEtiqueta(2524).':'.$ro['ds_instituto'].'</i></small>';
         }else{
			$nb_instituto_rector=""; 
		 }

		
		
         #Obtenemos el numero de usuarios del instituto. sin contar el administrador
         //$Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND fl_perfil_sp <>12  ";
         $Query="SELECT  no_total_licencias,no_licencias_usadas,no_licencias_disponibles,fe_periodo_final,fg_pago_manual FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
		 $row=RecuperaValor($Query);
         $total_user=!empty($row[0])?$row[0]:NULL;
         $no_licencia_usadas=!empty($row[1])?$row[1]:NULL;
         $no_licencias_disponibles=!empty($row[2])?$row[2]:NULL;
		 $fe_periodo_vigencia=!empty($row[3])?$row[3]:NULL;
		 $fg_pago_manual=!empty($row[4])?$row[4]:NULL;
         
		 
		 if($fg_pago_manual)
			 $pag="<span class='label label-success'>Manual payment</span>";
		 else
			 $pag="";
		 
		 #se calcula su proximo pago
		$fe_final_periodo=strtotime('+1 day',strtotime($fe_periodo_vigencia));
		$fe_final_periodo= date('Y-m-d',$fe_final_periodo);
		
		#DAMOS FORMATO DIA,MES, ANÑO
		$date = date_create($fe_final_periodo);
		$fe_proximo_pago=date_format($date,'F j , Y');
		 
		 
         
         #Obtenemos cuantos teacher tiene el instituto que sean activos
         $Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND fl_perfil_sp =".PFL_MAESTRO_SELF." AND fg_activo='1' ";
         $row=RecuperaValor($Query);
         $total_teachers=$row[0];
         
         #Obtenemos cuantos students tiene el isntituto que sean activos.
         $Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND fl_perfil_sp =".PFL_ESTUDIANTE_SELF." AND fg_activo='1' ";
         $row=RecuperaValor($Query);
         $total_estudiantes=$row[0];
         
         #Obtenemos cuantos alumnos estan ligados a este instituto, Un alumno puede estar mas de dos institutos.
         $Query="SELECT * FROM k_instituto_alumno A JOIN c_usuario B ON B.fl_usuario=A.fl_usuario_sp WHERE A.fl_instituto=$fl_instituto AND B.fl_instituto<>$fl_instituto ";
         $rop=RecuperaValor($Query);
         $total_estudiantes=$total_estudiantes + (!empty($rop[0])?$rop[0]:0);
      
         #DAMOS FORMATO DIA,MES,AÑO
         $date=date_create($fe_creacion);
         $miembro_desde=date_format($date,'F j, Y');
         
         
         $fe_registro=strtotime('+0 day',strtotime($fe_creacion));
         $fe_registro= date('d-m-Y',$fe_registro);
         
         $fe_final_periodo_prueba=strtotime('+0 day',strtotime($fe_final_periodo_prueba));
         $fe_final_periodo_prueba= date('d-m-Y',$fe_final_periodo_prueba);
         
         
         $fe_periodo_trial=$fe_registro." - ".$fe_final_periodo_prueba;
         
         
         switch($fg_activo) {
             case "0": 
                 $color_label = "danger";
                 $status="Inactive"; 
                 break;
             case "1": 
                 $color_label="success";
                 $status="Active";
                 break;

         } 
         
         #verifica si existe su id o se genera uno nuevo.
		 if(empty($school_id)){
			 
			#gENERAMOS EL ID
			$school_id = substr(strtolower($nb_instituto), 0, 2);
			$len = 10 ;
			$m="";
			for ($z = 0 ; $z < $len ; $z ++)
			{
				$m .= intval(rand(1,9));
			}
			$school_id = $school_id .$m;
			 
			 
			 $Query="UPDATE c_instituto SET  school_id='$school_id' WHERE fl_instituto=$fl_instituto ";
			 EjecutaQuery($Query);
			 
			 $Query="SELECT school_id FROM c_instituto WHERE fl_instituto=$fl_instituto ";
			 $row=RecuperaValor($Query);
			 $school_id=$row['school_id'];
			 
		 }
         
         
         
         
            
      echo '
        {
           "checkbox": "<!--<div class=\'checkbox \'><label><input class=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$fl_instituto.'\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>-->",
            "name": "<a href=\'javascript:Envia(\"members_frm.php\",'.$fl_instituto.');\'>'.str_texto($nb_instituto).'</a><br> <small class=\'text-muted\'><i>'.$nb_rector.'</i></small><br>'.$nb_instituto_rector.'<br><small class=\'text-muted\'><i>Account: '.$school_id.'</i></small>",
            "name_school": "<td><a href=\'javascript:Envia(\"members_frm.php\",'.$fl_instituto.');\'>'.str_texto($nb_pais).'<br><small class=\'text-muted\'>'.$ds_codigo_area.'</small><br><small class=\'text-muted\'>'.$no_telefono.'</small></a></td>",           
            "course": "<td><a href=\'javascript:Envia(\"members_frm.php\",'.$fl_instituto.');\'>'.str_texto($nb_admin).'</a></td>", 
            "age": "<td class=\'text-center\'><a href=\'javascript:Envia(\"members_frm.php\",'.$fl_instituto.');\' class=\'text-left\'><small class=\'text-muted\'> '.ObtenEtiqueta(988).': '.$total_user.'</a></small><br><small class=\'text-muted\'><i>'.ObtenEtiqueta(989).': '.$no_licencia_usadas.' </i></small> <br><small class=\'text-muted\'><i>'.ObtenEtiqueta(990).': '.$no_licencias_disponibles.' </i></small>    </td>",
            "ide": "<a href=\'javascript:Envia(\"members_frm.php\",'.$fl_instituto.');\'><td><small class=\'text-muted\'><i>Students: '.$total_estudiantes.' </i></small><br><small class=\'text-muted\'><i>Teachers: '.$total_teachers.'</i></small></a></td>",
            "progress": "<td>'.$miembro_desde.'<br><small class=\'text-muted\'><i>Trial date: '.$fe_periodo_trial.' </i></small></td>", 
            "vigencia":"<td> '.$pag.' <br><small class=\"text-muted\"><i>Renew '.$fe_proximo_pago.'</i></small></td>",  
            "estatus": "<td><a href=\'javascript:Envia(\"members_frm.php\",'.$fl_instituto.');\'><span class=\'label label-'.$color_label.'\'>'.$status.'</span>   </a></td>"           
                        
           
 
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
