<?php

require("../lib/self_general.php");
$fl_usuario_actual= ValidaSesion(False,0, True);

$fl_criterio=RecibeParametroNumerico('fl_criterio');
$ds_comentarios=$_POST['ds_descripcion'];
$no_calificacion=RecibeParametroNumerico('no_calificacion');
$fl_alumno=RecibeParametroNumerico('fl_alumno');
$fl_leccion_sp=RecibeParametroNumerico('fl_leccion_sp');
$fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');
$es_comentario_final= RecibeParametroNumerico('comen_final');
$fg_comentario_criterio=RecibeParametroNumerico('fg_comentario_crietrio');
$fg_guardar_todo=RecibeParametroNumerico('fg_guardar_todo');
$ds_comentario_final_teacher=$_POST['ds_comentarios'];
$rangeInput=RecibeParametroNumerico('rangeInput');
$increse_grade = RecibeParametroNumerico('increse_grade');
$fg_increase_grade = RecibeParametroBinario('fg_increase_grade');
$fl_entrega_semanal_sp = RecibeParametroNumerico('fl_entrega_semanal_sp');
$fg_calificado=RecibeParametroHTML('fg_calificado');





   

   

    if($fg_guardar_todo==1){#Quiere decir que se guarda ya todo la calificacion asignada del teacher

        
        #Verificamos si existen comentarios del teacher por cada criterio., entonces recorremos todas las calificaciones asignadas. 
        $Queryc="SELECT fl_criterio,no_porcentaje_real FROM c_calculo_criterio_temp WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp  ORDER BY fl_criterio ASC  ";
        $rs2 = EjecutaQuery($Queryc);
        for($i=1;$row2=RecuperaRegistro($rs2);$i++){
                    
              $fl_criterio_in=$row2['fl_criterio'];
              $no_porcentaje_in=$row2['no_porcentaje_real'];
             
              
                 #Verificamos si existe un comentario asignado del teacher por cada calificacion(rangeInput).
                 $Queryb="SELECT fl_comentario_teacher FROM c_com_criterio_teacher WHERE fl_criterio=$fl_criterio_in AND  fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp  ";
                 $rowb=RecuperaValor($Queryb);
                 $ds_comentario_teacher=$rowb[0];

                 #Si no existe el cometario inserta un comentario vacio
                 if(empty($ds_comentario_teacher)){
                     
                
                     #Inserta comentarios de cada criterio.
                     $Query="INSERT INTO c_com_criterio_teacher (fl_criterio,no_porcentaje_equivalente,ds_comentarios,fl_alumno,fl_programa_sp,fl_leccion_sp,fe_creacion,fe_modificacion)";
                     $Query.="VALUES ($fl_criterio_in,$no_porcentaje_in,'',$fl_alumno,$fl_programa_sp,$fl_leccion_sp,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP )";
                     $fl_comentario=EjecutaInsert($Query);

                 }
                          
        }
        
        
        #Verificamos si existe un comentario general del  teacher.
        $Queryc="SELECT fl_comentario_teacher FROM c_com_criterio_teacher WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp AND fg_com_final='1' ";
        $rowc=RecuperaValor($Queryc);
        $ds_comentario_final_teacher=$rowc[0];
        if(empty($ds_comentario_final_teacher)){
        
            #Inserta comentario final del teacher.
            $Query="INSERT INTO c_com_criterio_teacher (ds_comentarios,fl_alumno,fl_programa_sp,fl_leccion_sp,fe_creacion,fe_modificacion,fg_com_final)";
            $Query.="VALUES ('',$fl_alumno,$fl_programa_sp,$fl_leccion_sp,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'1' )";
            $fl_comentario_final_teacher=EjecutaInsert($Query);
        
        }
        
        
       
        #Eliminamos el comentario del teacher.
        EjecutaQuery("DELETE FROM k_calificacion_teacher WHERE  fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp  ");
        
        
        
        
        
        #Sumamos todos los que pernetencen al alumno, programa y leccion.
        $Query ="SELECT SUM(no_porcentaje) FROM c_calculo_criterio_temp  WHERE fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp ";
        $row=RecuperaValor($Query);
        $no_calificacion=$row[0];

        
	
		#Veificamos que si se haya insertado la calificacion del teacher
        $Query="SELECT MAX(fl_calificacion_teacher) FROM k_calificacion_teacher ";
        $rop=RecuperaValor($Query);
        $fl_califi_max=$rop[0];
		

		
		
    
            $Query='INSERT INTO k_calificacion_teacher (fl_alumno,fl_programa_sp,fl_leccion_sp,ds_comentarios,no_calificacion,fe_creacion,fl_usuario_creacion) ';
            $Query.='VALUES('.$fl_alumno.','.$fl_programa_sp.','.$fl_leccion_sp.',"'.$ds_comentario_final_teacher.'",'.$no_calificacion.',CURRENT_TIMESTAMP,'.$fl_usuario_actual.')';
            $fl_calificacion_teacher=EjecutaInsert($Query);

			
            #Verificamos en que rango se encuentra y se le asigna su promedio 
            $Query="SELECT fl_calificacion, no_min,no_max  FROM c_calificacion_sp WHERE 1=1 ";
            $rs=EjecutaQuery($Query);
			
			
			
            #Se utiliza solo para evitar errores de calificacion.
            $no_calificaicon_comparar=number_format($no_calificacion);
            for($i=1;$row=RecuperaRegistro($rs);$i++){
                $no_min=$row[1];
                $no_max=$row[2];

                if( ($no_calificaicon_comparar>=$no_min) && ($no_calificaicon_comparar<=$no_max)){
                
                        $fl_promedio=$row[0];
                
                } 
            
            }
            
            #Actualizamos el promedio 
            $Query="UPDATE k_entrega_semanal_sp SET fl_promedio_semana=$fl_promedio, fg_increase_grade='$fg_increase_grade' WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";     
            EjecutaQuery($Query);
			
			#Actualizamos el promedio
            $Query="UPDATE k_usuario_programa SET fg_calificado_teacher='1',no_promedio_t='$no_calificacion',fe_final_programa=CURRENT_TIMESTAMP WHERE fl_usuario_sp=$fl_alumno AND fl_programa_sp=$fl_programa_sp ";     
            EjecutaQuery($Query);
            
			#Actualizamos la fecha de odificacion 
			$Query="UPDATE c_com_criterio_teacher SET fe_modificacion=CURRENT_TIMESTAMP WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa_sp AND fg_com_final='1' ";
			EjecutaQuery($Query);
			
			#Actualizamos la notificacion del estudiante
			$Query="UPDATE k_entrega_semanal_sp SET  fg_revisado_alumno='0' WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";
			EjecutaQuery($Query);
			

            
            /**
             *MJD los criterios pasara a ser solo registro especifico por alumno , ya que el ADMIN puede elimnar criterios del programa y volverlos a restructurar, es por estarazon que se guardade como se califico al alumno(se congela calif del alumno). 
             *
             */ 
            /**********Guardamos la calificacion congelada del alumno.****************/
            
            if($fg_calificado==1){
                
            }else{
            
                
                    #Volvemos a generar nuevos registros.
                    $QueryM="SELECT fl_criterio,fl_programa_sp,no_valor,no_orden FROM k_criterio_programa_fame WHERE fl_programa_sp=$fl_leccion_sp ";
                    $rsM=EjecutaQuery($QueryM);
                    for($m=1;$rowM=RecuperaRegistro($rsM);$m++){
                    
                        #Genera registros por alumno y programa.
                        $QueryN="INSERT INTO k_criterio_programa_alumno_fame (fl_criterio,fl_programa_sp,no_valor,no_orden,fl_usuario_sp)";
                        $QueryN.="VALUES(".$rowM['fl_criterio'].",".$rowM['fl_programa_sp'].",".$rowM['no_valor'].",".$rowM['no_orden'].",".$fl_alumno.")";
                        EjecutaQuery($QueryN);
                    }
                
            
            
            }
            
             #Condicion que se agrega para verificar que efectivamente se haya calificado.
            $Query="SELECT fl_promedio_semana FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";
            $rop=RecuperaValor($Query);
            $fl_promedio_semana_insert=$rop[0];
            
          
           /*********End Save****************/
            
	        if( ($fl_calificacion_teacher>$fl_califi_max) && (!empty($fl_promedio_semana_insert)) ){
				
				  # Actualizamos los promedios de quizes y teachers
                  SavePromedio_Q_T($fl_programa_sp,$fl_alumno);
				
			
			 echo"<a class='btn btn-success btn-sm hidden' href='index.php#site/node.php?node=172' id='redirigir_billing'><i class='fa fa-upload'></i> redirige</a>";

            
			
			
			
				
			   
				  #Se envia notificacion via email al estudiante y teacher.
										# fl_usuario | opc_template|
				  $nb_template_encabezado=genera_documento_sp($fl_alumno,1,123,$fl_programa_sp);
				  $nb_template_cuerpo=genera_documento_sp($fl_alumno,2,123,$fl_programa_sp);
				  $nb_template_pie=genera_documento_sp($fl_alumno,3,123,$fl_programa_sp);
				  
				  
				  $Query="SELECT no_semana,ds_titulo FROM c_leccion_sp WHERE fl_leccion_sp=$fl_leccion_sp ";
				  
				  $row=RecuperaValor($Query);
				  $no_semana=$row[0];
				  $ds_leccion=$row[1];

				  $ds_contenido= $nb_template_encabezado.$nb_template_cuerpo.$nb_template_pie;
				  
				  #Varibales para sustituir para leccion.
				  $ds_contenido = str_replace("#no_week#", $no_semana, $ds_contenido);  #plan actual/mont/anual
				  $ds_contenido = str_replace("#ds_leccion#", $ds_leccion, $ds_contenido);  #plan actual/mont/anual
				  
				  
				  
				  #Email destinatario.
				  $Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_alumno ";
				  $row=RecuperaValor($Query);
				  $ds_email_des=str_texto($row[0]);
				  $ds_email_destinatario=$ds_email_des;
				  
				  $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);  
				  
				  $nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje   Vacouver Anitaio SCHOLL
					
				  
				  
				 
				  
				  # Inicializa variables de ambiente para envio de correo
				  ini_set("SMTP", MAIL_SERVER);
				  ini_set("smtp_port", MAIL_PORT);
				  ini_set("sendmail_from", MAIL_FROM); 
				  $message  = $ds_contenido;
				  $message = utf8_decode(str_ascii(str_uso_normal($message)));
				  
				  $bcc = ObtenConfiguracion(107);
			   
				  $ds_titulo=ObtenEtiqueta(1690);#etiqueta de asunto del mensjae para el anunciante
				  $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
				  
				  $rows = RecuperaValor("SELECT no_assigments FROM k_usu_notify WHERE fl_usuario=$fl_alumno");
				  $tot_assi = $rows[0] + 1;
				  EjecutaQuery("UPDATE k_usu_notify SET no_assigments=".$tot_assi." WHERE fl_usuario=$fl_alumno"); 
				  # Update grade grl
				  ObtenPromedioPrograma($fl_programa_sp, $fl_alumno);
				  
				  
				 
				   #Redirige a tab1.
			  echo"<script>
					 $(document).ready(function () {
					  
						//	$('#tab_1').removeClass('active');
						//	$('#tab_2').removeClass('active');
						//	$('#tab_3').addClass('hidden');
					//		$('#tab_0').addClass('active');
							  
						//	$('#p_incomplete').removeClass('active');
						//	$('#p_history').removeClass('active');
					//		$('#p_assignment_grade').removeClass('active');  
					//		$('#p_grade').addClass('active');
							
							var total_asigments= $('#noti_assi_$fl_usuario_actual').text();
						   
							$('#noti_assi_$fl_usuario_actual').empty();
							var new_total = parseInt(total_asigments) - 1;
							$('#noti_assi_$fl_usuario_actual').append(new_total);
							
							
							 document.getElementById('redirigir_billing').click();//clic au
							
						   
						});

			
				</script>
				";
				  
				  
				  
				  
				  
			}else{#end
				echo"
				<script>				
						$('#boton_final').removeClass('disabled');

						$.smallBox({
							title : '<i class=\"fa fa-times-circle\" aria-hidden=\"true\"></i> ".ObtenEtiqueta(25)."',
							//content : 'Lorem ipsum dolor sit amet',
							color : '#a90329',
							timeout: 4000,
							iconSmall : 'fa fa-times-circle',
							
						});
						
				</script>
				";

			}

    }





?>
<h2 style="margin: 2px 0; line-height: 60%;font-size:20px;"><i><small><?php echo ObtenEtiqueta(1680)." :<br/>".$fe_modificacion; ?></small></i></h2>
