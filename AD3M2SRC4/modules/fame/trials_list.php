<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  #Obtenemos dias que llevo de mi plan actual. y para obtener los dias faltantes solo hay que invertir fechas.
  function ObtenDiasRestantesTrial($fe_final,$fe_inicial){

      $Query="SELECT DATEDIFF('$fe_final','$fe_inicial')";
      $row=RecuperaValor($Query);
      $no_dias=$row[0];
      
      
      
      return $no_dias;
  }   
  
  
  
  
  
  
  
  # Recibe Parametros
  $criterio = RecibeParametroHTML('criterio');
  
 
  
  $Query="SELECT I.fl_instituto,I.fl_usuario_sp,I.ds_instituto,I.no_usuarios,I.ds_codigo_area,I.no_telefono,P.ds_pais ,U.ds_nombres,U.ds_apaterno,I.fe_creacion,U.fg_activo,I.fe_trial_expiracion,I.cl_tipo_instituto,I.fl_instituto_rector,I.school_id           
            FROM c_instituto I 
            JOIN c_pais P ON P.fl_pais=I.fl_pais
            JOIN c_usuario U ON U.fl_usuario=I.fl_usuario_sp 
            WHERE 1=1 AND I.fl_instituto<>1 AND I.fg_tiene_plan='0' ORDER BY I.fl_instituto DESC  ";
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
         $fg_activo=$row['fg_activo'];
         $fe_expiraion_trial=$row['fe_trial_expiracion'];
         $cl_tipo_instituto=$row['cl_tipo_instituto'];
		 $fl_instituto_rector=$row['fl_instituto_rector'];
		 $school_id=$row['school_id'];

         if($cl_tipo_instituto==2){
             $nb_rector='<small class=\'text-muted\'><i>'.ObtenEtiqueta(2524).'</i></small>';
         }else{
             $nb_rector="";
         }

         #Obtenemos el numero de usuarios del instituto. sin contar el administrador
         $Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND ( fl_perfil_sp <> ".PFL_ADMINISTRADOR." AND fl_perfil_sp<>".PFL_ADM_CSF." ) AND fg_activo='1'  ";
         $row=RecuperaValor($Query);
         $total_user=$row[0];
         
         
         #Obtenemos cuantos teacher tiene el instituto que sean activos
         $Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND fl_perfil_sp =".PFL_MAESTRO_SELF." AND fg_activo='1' ";
         $row=RecuperaValor($Query);
         $total_teachers=$row[0];
         
         #Obtenemos cuantos students tiene el isntituto que sean activos.
         $Query="SELECT COUNT(*) FROM c_usuario WHERE fl_instituto=$fl_instituto AND fl_perfil_sp =".PFL_ESTUDIANTE_SELF." AND fg_activo='1' ";
         $row=RecuperaValor($Query);
         $total_estudiantes=$row[0];
         
         #Recuperamos datos del isntituto Rector.
		 if($fl_instituto_rector){
			$Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto_rector ";
			$ro=RecuperaValor($Query);
			$nb_instituto_rector='<small class=\'text-muted\' ><i>'.$ro['ds_instituto'].'</i></small>';
         }else{
			$nb_instituto_rector=""; 
		 }
         
         
         #DAMOS FORMATO DIA,MES,AÑO
         $date=date_create($fe_creacion);
         $miembro_desde=date_format($date,'F j, Y');
         
        
         #Obtenemos la fecha actual.
         $Query = "Select CURDATE() ";
         $row = RecuperaValor($Query);
         $fe_actual = str_texto($row[0]);
         $fe_actual=strtotime('+0 day',strtotime($fe_actual));
         $fe_actual= date('Y-m-d',$fe_actual);
         
         $fe_formato_creacion=strtotime('+0 day',strtotime($fe_creacion));
         $fe_formato_creacion= date('Y-m-d',$fe_formato_creacion);
		 
         $no_dias_faltan_terminar_plan=ObtenDiasRestantesTrial($fe_expiraion_trial,$fe_actual);
         $no_dias_trial=ObtenConfiguracion(101);      
         /*if($fl_instituto==9) 
         $no_dias_trial=29;
		 if($fl_instituto==12) 
         $no_dias_trial=15;
		 */
		 $no_dias_trial=ObtenDiasRestantesTrial($fe_expiraion_trial,$fe_formato_creacion); 
		 
		 
         if($fg_activo=='1'){
         
         
                 if($no_dias_faltan_terminar_plan==0){
                     $color_label = "warning";
                     $status="Expired";
                 }
                 if($no_dias_faltan_terminar_plan < 0 ){
                     $color_label = "warning";
                     $status="Expired";
                 }
                 if(($no_dias_faltan_terminar_plan>0)&&($no_dias_faltan_terminar_plan <= $no_dias_trial)){
                     $color_label="success";
                     $status= $no_dias_faltan_terminar_plan." days left";
             
                 }
         
         }else{
         
             $color_label = "danger";
             $status="Cancelled"; 
         }
         
         
         #Recuperamos el princing del Instituto.
         
         $Query2="SELECT fl_princing, no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia  
                        FROM c_princing 
                        WHERE fl_instituto=$fl_instituto 
                        ORDER BY fl_princing ASC ";
         $rs2 = EjecutaQuery($Query2);
         $tot_registros = CuentaRegistros($rs2);
         
         $arma ="<tr>";
         $arma.="<td width='5%'></td>";
         $arma.="<td width='15%' class='text-center'>".ObtenEtiqueta(1501)."<p><em style='color:#888686;'>".ObtenEtiqueta(1504)."</em> </p></td>";
         $arma.="<td width='15%' class='text-center'>".ObtenEtiqueta(1749)."</td>";
         $arma.="<td width='15%' class='text-center'>".ObtenEtiqueta(1551)."</td>";
         $arma.="<td width='20%' class='text-center'>Monthly - Flexible payments<p><em style='color:#888686;'>".ObtenEtiqueta(1505)."</td>";
         $arma.="<td width='20%' class='text-center'>".ObtenEtiqueta(1503)."<p><em style='color:#888686;'>".ObtenEtiqueta(1506)."</em></p></td>";
         $arma.="<td></td>";
         $arma.="</tr>";
         
         for($m=1;$row2=RecuperaRegistro($rs2);$m++){
                                                
            $mn_rango_ini= $row2['no_ini'];
            $mn_rango_fin= $row2['no_fin'];
            $mn_descuento_licencia= number_format($row2['mn_descuento_licencia']);
            $mn_mensual= $row2['mn_mensual'];
            $mn_descuento_mensual= number_format($row2['ds_descuento_mensual']);
            $mn_anual= $row2['mn_anual'];
         
            $arma.="<tr>";
            $arma.="<td></td>";
            $arma.="<td class='text-center'>$mn_rango_ini - $mn_rango_fin </td>";
            $arma.="<td class='text-center'>$mn_descuento_licencia% </td>";
            $arma.="<td class='text-center'>$mn_descuento_mensual%</td>";
            $arma.="<td class='text-center'>$ $mn_mensual</td>";
            $arma.="<td class='text-center'>$ $mn_anual</td>";
            $arma.="<td></td>";
            $arma.="</tr>";
            
          
            
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
           
            "name": "<a href=\'javascript:Envia(\"trials_frm.php\",'.$fl_instituto.');\'>'.str_texto($nb_instituto).'</a><br>'.$nb_rector.' '.$nb_instituto_rector.' <br><small class=\'text-muted\'><i>Account: '.$school_id.'</i></small> ",
            "name_school": "<td><a href=\'javascript:Envia(\"trials_frm.php\",'.$fl_instituto.');\'>'.str_texto($nb_pais).'<br><small class=\'text-muted\'>'.$ds_codigo_area.' </small><br><small class=\'text-muted\'>'.$no_telefono.' </small></a></td>",           
            "course": "<td><a href=\'javascript:Envia(\"trials_frm.php\",'.$fl_instituto.');\'>'.str_texto($nb_admin).'</a></td>", 
            "age": "<td class=\'text-center\'><a href=\'javascript:Envia(\"trials_frm.php\",'.$fl_instituto.');\' class=\'text-center\'>'.$total_user.'</a></td>",
            "ide": "<a href=\'javascript:Envia(\"trials_frm.php\",'.$fl_instituto.');\'><td><small class=\'text-muted\'><i>Students: '.$total_estudiantes.' </i></small><br><small class=\'text-muted\'><i>Teachers: '.$total_teachers.'</i></small></a></td>",
            "progress": "<td>'.$miembro_desde.'</td>",  
            "estatus": "<td><a href=\'javascript:Envia(\"trials_frm.php\",'.$fl_instituto.');\'><span class=\'label label-'.$color_label.'\'>'.$status.'</span>   </a></td>",           
            "del": "<td><a href=\'javascript:Borra(\"trials_del.php\",'.$fl_instituto.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a></td>"
              
                             
           
 
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
