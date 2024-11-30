<?php
	# Librerias
	require("../../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  
  
  $QueryG="SELECT COUNT(*) FROM k_entrega_semanal_sp  a WHERE a.fl_alumno=$fl_usuario  and fg_revisado_alumno='0' and fl_promedio_semana is not null ; ";  
  $rowg=RecuperaValor($QueryG);
  $no_assigment_grade = $rowg[0];
  
  
  
  
  
  echo"<script>
  
	  function ColocarNumero(no_actual,fl_usuario){
	  
	   var no_actual=no_actual;
	   var fl_usuario=fl_usuario;
	   //alert(no_actual);
		$('#tot_assigment_'+fl_usuario).html(no_actual);
	  }
	   ColocarNumero($no_assigment_grade,$fl_usuario);
	  
	 </script>
  ";
  
  
  
  
  function GetGradeNotification($fl_usuario){
      
  	
				#Recuperamos todas las leciones que han sido califiacdas por el teacher y que no han sido confirmados por el estudiante
                $Query3="SELECT DISTINCT B.ds_titulo".$sufix.", B.no_semana, C.nb_programa".$sufix.",DATE_FORMAT(D.fe_modificacion , '%Y-%m-%d %H:%i:%s'),C.fl_programa_sp,B.fl_leccion_sp,A.fl_entrega_semanal_sp  
							FROM k_entrega_semanal_sp A
                            JOIN c_leccion_sp B ON A.fl_leccion_sp=B.fl_leccion_sp
                            JOIN c_programa_sp C ON C.fl_programa_sp=B.fl_programa_sp 
                            JOIN  c_com_criterio_teacher D ON D.fl_leccion_sp = A.fl_leccion_sp AND D.fl_alumno=$fl_usuario AND D.fg_com_final='1'
                            WHERE A.fl_alumno=$fl_usuario AND fl_promedio_semana IS NOT NULL AND fg_revisado_alumno='0' ORDER BY fe_modificacion DESC ";
    
                $rs3 = EjecutaQuery($Query3);
                $no_not = CuentaRegistros($rs3);
				
                $result3 = array(); 
    
                 for($k2=0;$row = RecuperaRegistro($rs3);){
                    $contador++;
                    
                    $nb_session=str_texto($row[0]);
                    $no_semana=str_texto($row[1]);
                    $nb_programa=str_texto($row[2]);
                    $fe_modificacion=$row[3];
                    $fl_programa_sp=$row[4];
                    $fl_leccion_sp=$row[5];
					$fl_entrega_semanal_sp=$row[6];
                    $time=time_elapsed_string($fe_modificacion);
                    $avatar="";
                    $nb_archivo=""; 
					
					
					       #Verificamos el utlimo teahcer que asigno calificacion y que el estudiante no ha revisado. 
                           $Query="SELECT fl_maestro FROM k_usuario_programa where fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_usuario ";
                           $row=RecuperaValor($Query);
                           $fl_maestro=$row[0];
					$nb_teacher=ObtenNombreUsuario($fl_maestro);
					$avatar = ObtenAvatarUsuario($fl_maestro);
        
                    $datos_programa="$nb_teacher   <br/> ".ObtenEtiqueta(1964)." $nb_programa <br/>".ObtenEtiqueta(1966)." $nb_session<br/>".ObtenEtiqueta(1967)." $no_semana ";
                    //$nb_tab="assignments_grade";
                    $nb_tab=1;#siempre va ser 1 assignments_grade
                    if($no_not > 0){
					
					
					 $read = "<div class='cursor-pointer text-align-right' id='notis_".$contador."'><small onclick='MarkGrade(1,".$fl_entrega_semanal_sp.",".$contador.",0,".$fl_usuario.");'><i class='fa fa-circle-o'></i> ".ObtenEtiqueta(1833)."</small></div>";     

					 
					 
					  $result["streams".$k2] = array(
                                                
                                                
                     // "url2" => '#site/desktop.php?student='.$fl_alumno.'&week='.$no_semana.'&tab='.$nb_tab.'&fl_programa='.$fl_programa_sp.'&t=1',
                     // "url2"=>'onclick=\"location.href='unit_01.htm'\"',
					  "url2"=>'view_grade('.$fl_usuario.','.$no_semana.' ,'.$nb_tab.','.$fl_programa_sp.','.$fl_leccion_sp.')',
                      "img2" => $avatar,
                      "title2" => 'fua',
                      "time2" => $time,
                      "subject2" => "<style>.notification-body .unread .from {
									font-weight: 400 !important;
									}
									</style><small style=\"font-size:14px;font-weight:none !important;\">".ObtenEtiqueta(1687).":</small>",
                      "abstract2" => " ",
                      "abstract_style2" => '',
                      "comentario2" => $datos_programa,
                      "read2" => $read,
                      "fg_read2" => '1', 
                      "fl_gallery_comment_sp2" => $contador,
                      "img_post2" => " ",
					  "fl_gallery_comment_sp_2" => $contador,
                                                
                                                
                                     );
            
                        $k2++;
					 
					 
					 
					}
	
	
            }
			#pRESENTA Mensaje de que no exixtsen notificaicones
			if($no_not==0){
			
			   
					   $result["streams".$k2] = array(
						  "url2"=>'',
								  
								  "title2" => '',
								  "time2" => '',
								  "subject2" => "<style>.notification-body .unread .from {
												font-weight: 400 !important;
												}
												
												</style>  ",
								  "abstract2" => " ",
								  "abstract_style2" => '',
								  "comentario2" => "<div class=\"alert alert-info\"><small class=\"text-info\" style=\"font-size:14px;\"><i class=\"fa fa-bell\" aria-hidden=\"true\"></i> ".ObtenEtiqueta(1696)."</small></div>",
								  "read2" => '',
								  "fg_read2" => '1', 
								  "fl_gallery_comment_sp2" => '',
								  "img_post2" => "",
								  "fl_gallery_comment_sp_2" => '',
															
															
												 );
						
									$k2++;
					 
				
			
			
			}
			
			
       
    $result["size"] = array("total" => $k2);
  	echo json_encode((Object) $result);
  	//$result["size"] = array("total" => $i);
  	//echo json_encode((Object) $result);
  }

?>

<ul class="notification-body" id="notify_comments"></ul>


<script type="text/javascript">
	// VANAS Board
	var result3, board3, notices;
	result3 = <?php GetGradeNotification($fl_usuario); ?>;
	notices = "";
	
	 for(var j=0; j<result3.size.total; j++){
    board3 = result3['streams'+j];
		notices += NoticeBar(board3.url2, board3.img2, board3.title2, board3.time2, board3.subject2, board3.abstract2, board3.abstract_style2, board3.comentario2, board3.read2, 
    board3.fg_read2, board3.fl_gallery_comment_sp2, board3.img_post2, board3.fl_gallery_comment_sp_2);
  }
	$(".notification-body").prepend(notices);

		function NoticeBar(url, img, title, time, subject, abstract, abstractStyle, comentario, read, fg_read, fl_gallery_comment_sp, img_post, fl_gallery_comment_sp_2){
    var read_unread = "unread";
    if(fg_read == 1)
      read_unread = "read";
	  
	  if(img)
	  clas="";
	  else
	  clas="hidden";
	  
		var notice =
      "<li id='comments_fame'><span id='li_"+fl_gallery_comment_sp_2+"' class='"+read_unread+"'>"+
        "<a href='javascript:"+url+"' style='padding-left:50px !important'>"+		
          "<img src='"+img+"' alt='' class='air air-top-left margin-top-5  "+clas+" ' height='40' width='40' />"+
          "<span class='from'>"+subject+"</span>"+
          "<time>"+time+"</time>"+
          "<div class='col-sm-9 no-padding'>"+
          "<span class='msg-body' style='max-height:110px;white-space: normal;'>"+comentario+"</span></div>"+
          "<div class='col-sm-1 text-align-center'>"+
          img_post+
          "</div>"+
        "</a>"+
        read+
      "</span></li>";
		return notice;
	}
	
	  
	   function MarkGrade(mark,fl_entrega_semanal_sp,contador,all_comments=0,fl_usuario){

		
	   
	   
		 // ajax
		  $.ajax({
		  type: "POST",
		  url: "<?php echo PATH_SELF_SITE; ?>/notify/marcar_notifiy.php",
		  data:'fl_entrega_semanal_sp='+fl_entrega_semanal_sp+
		       '&fg_read_r='+mark,
          }).done(function(result){
		  
		        var content, elem = $('#notis_'+contador), elem_il = $("#li_"+contador);
		        content = JSON.parse(result); 
				
				// notificacion
				var tot_notifications = $("#activity>b").text();
				var asigment_grade = $('#tot_assigment_'+fl_usuario).text();
				// Limpaimos las notificaciones
				$("#activity>b").empty();
				//elem.empty(); 
                 $('#tot_assigment_'+fl_usuario).empty();
                // elem.append(content.read_unread);
				
				
				
				
				 if(mark==1){
				    
					elem_il.removeClass('read').addClass('hidden');
					
					var v = parseInt(tot_notifications) - 1;
					var n = parseInt(asigment_grade)-1;
					
					$("#activity>b").append(v);
					$('#tot_assigment_'+fl_usuario).append(n);
					
				    
					
					
				 }else{
					 // cambiamos el estado del la notificacio
                    elem_il.removeClass('unread');
				    
					var s = parseInt(tot_notifications)+1;
					var n = parseInt(asigment_grade)+1;
					$("#activity>b").append(s);           
					$("#no_news").append(n);
 
				 }
				 
				 
				 
				 
				
		  });
        
  
  
  }
	
</script>