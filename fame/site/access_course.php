<?php
  # Libreria de funciones
  require("../lib/self_general.php");

	$sufix=langSufix();

  $fl_user_actual=ValidaSesion(False, 0, True);
  $fl_programa = RecibeParametroNumerico("fl_programa");
  $fl_usuario = RecibeParametroNumerico("fl_usuario");
  $accion = RecibeParametroNumerico("accion");
  $ds_mensaje_teacher=RecibeParametroHTML('mensaje');
  $fg_ejecucion=RecibeParametroNumerico('fg_ejecucion');
  
  $from=ObtenConfiguracion(107);

  #Recuperamos el nombre del programa
  $Query="SELECT nb_programa".$sufix." FROM c_programa_sp WHERE fl_programa_sp=$fl_programa ";
  $row=RecuperaValor($Query);
  $nb_programa=$row[0];

  #Recuperamos dtos genrales del teacher y estudent.
  $Query1="SELECT ds_email,ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $rou=RecuperaValor($Query1);
  $ds_email_alumno=$rou['ds_email'];
  $fname_alumno=str_texto($rou[1]);
  $lname_alumno=str_texto($rou[2]);

  #Recuperamos dtos genrales del teacher y estudent.
  $Query1="SELECT ds_email,ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_user_actual ";
  $rou=RecuperaValor($Query1);
  $ds_email_teaacher=$rou['ds_email'];
  $fname_teacher=str_texto($rou[1]);
  $lname_teacher=str_texto($rou[2]);

  
 
   switch ($accion) {
	//acceso dengado   
    case '1':
	    
		  if($fg_ejecucion==1){
	      EjecutaQuery("UPDATE k_request_access_course SET fg_denegado='1' WHERE fl_programa_sp=$fl_programa AND fl_usuario_sp=$fl_usuario ");
		  
			//mandamos notificacion al student indicado que esta denegado
			echo"<script>
				var nb_programa='$nb_programa';
				var etq_titulo='".ObtenEtiqueta(2600)."';
				var accion=1;
				socket.emit('RequestAaccessProgramStudent',accion, $fl_programa,$fl_usuario, nb_programa, etq_titulo);

	  
			 </script>";

            #Enviamos email de notificacion.
            # Obtenmos el template para decirle que alguien comento su post.
            $ds_header = genera_documento_sp('', 1, 179);
            $ds_body = genera_documento_sp('', 2, 179);
            $ds_footer = genera_documento_sp('', 3, 179);
            $dominio_campus = ObtenConfiguracion(116);
            $src_redireccion=$dominio_campus;#bueno
            
            $ruta_avatar_comment=ObtenAvatarUsuario($fl_usuario);
            $ds_mensaje=$ds_header.$ds_body.$ds_footer;
            $ds_mensaje = str_replace("#fame_fname#", $fname_alumno, $ds_mensaje);
            $ds_mensaje = str_replace("#fame_lname#", $lname_alumno, $ds_mensaje);
            $ds_mensaje = str_replace("#fame_te_fname#", $fname_teacher, $ds_mensaje);
            $ds_mensaje = str_replace("#fame_te_lname#", $lname_teacher, $ds_mensaje);
            $ds_mensaje = str_replace("#fame_pg_name#", $nb_programa, $ds_mensaje);
            $ds_mensaje = str_replace("#message_teacher#", $ds_mensaje_teacher, $ds_mensaje);

            # Nombre del template
            $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=179 AND fg_activo='1'";
            $template = RecuperaValor($Query0);
            $nb_template = str_uso_normal($template[0]);

            # Email al alumno
            EnviaMailHTML($from, $from,$ds_email_alumno, $nb_template, $ds_mensaje);





		  }else{
			  


         //Abrimos modal que contiene el mesaje final.
          echo'
              <!-- Modal -->
                <div class="modal fade" id="ModalAccesCourse" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document" style="width:60%;margin: 120px auto auto;">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><b><i class="fa fa-book" aria-hidden="true"></i> '.ObtenEtiqueta(2601).'</b></h5>
                        <button type="button" style="margin-top: -39px;" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
							<div class="col-md-12">
							    <h2>'.ObtenEtiqueta(2602).'</h2>
								 <div class="smart-form">
								 
									<section>
										<label class="textarea"> 										
											<textarea name="ds_mesaje_declined" id="ds_mesaje_declined" style="line-height: 32px;padding: 5px 10px !important;border-color: #BDBDBD !important;border-width: 1px !important;border-style: solid !important;" rows="3" class="custom-scroll"></textarea> 
										</label>
										
									</section>
										
								</div>
							</div>
						
						</div>
                      </div>
                      <div class="modal-footer text-center">
                        <button type="button"  class="btn btn-default " style="border-radius: 10px;" data-dismiss="modal">'.ObtenEtiqueta(14).'</button>
                        <button type="button" class="btn btn-primary"  style="border-radius: 10px;" data-dismiss="modal" onclick="PermitirCourse(1,'.$fl_usuario.','.$fl_programa.',1);">'.ObtenEtiqueta(2605).'</button>
                      </div>
                    </div>
                  </div>
                </div>
                   
              ';
          echo"<script>
                    $('#ModalAccesCourse').modal('show')

                </script>";

		  }


	
	break;
		//acesso permitido
	case '2':	
		 //marcamos como visto el registro. filtrado por programa y usario ya que puede tener varios teachers.
         EjecutaQuery("UPDATE k_request_access_course set fg_revisado='1' WHERE fl_programa_sp=$fl_programa  AND fl_usuario_sp=$fl_usuario  ");	 
	    

         $Query="select count(*) from k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa  ";
         $ro=RecuperaValor($Query);

         if(empty($ro[0])){

             EjecutaInsert("INSERT INTO k_usuario_programa (fl_usuario_sp,fl_maestro,fl_programa_sp,fe_creacion,fg_status) values($fl_usuario,$fl_user_actual,$fl_programa,CURRENT_TIMESTAMP,'RD') ");
         }        
         
         //mandamos notificacion al student indicado
		echo"<script>
              
              var nb_programa='$nb_programa';
			  var etq_titulo='".ObtenEtiqueta(2599)."';
			  var accion=2;
			  socket.emit('curso-asignado', $fl_usuario,nb_programa);		  
			  socket.emit('RequestAaccessProgramStudent',accion,$fl_programa,$fl_usuario, nb_programa, etq_titulo);
			  
         </script>";


        #Enviamos email de notificacion.
        # Obtenmos el template para decirle que alguien comento su post.
        $ds_header = genera_documento_sp('', 1, 179);
        $ds_body = genera_documento_sp('', 2, 179);
        $ds_footer = genera_documento_sp('', 3, 179);
        $dominio_campus = ObtenConfiguracion(116);
        $src_redireccion=$dominio_campus;#bueno
        
        $ruta_avatar_comment=ObtenAvatarUsuario($fl_usuario);
        $ds_mensaje=$ds_header.$ds_body.$ds_footer;
        $ds_mensaje = str_replace("#fame_fname#", $fname_alumno, $ds_mensaje);
        $ds_mensaje = str_replace("#fame_lname#", $lname_alumno, $ds_mensaje);
        $ds_mensaje = str_replace("#fame_te_fname#", $fname_teacher, $ds_mensaje);
        $ds_mensaje = str_replace("#fame_te_lname#", $lname_teacher, $ds_mensaje);
        $ds_mensaje = str_replace("#fame_pg_name#", $nb_programa, $ds_mensaje);
       

        # Nombre del template
        $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=121 AND fg_activo='1'";
        $template = RecuperaValor($Query0);
        $nb_template = str_uso_normal($template[0]);

        # Email al alumno
        EnviaMailHTML($from, $from,$ds_email_alumno, $nb_template, $ds_mensaje);



	
	break;
	
   }
 
 
  
  
?>