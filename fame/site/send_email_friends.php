<?php
  
  # Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
  
  #Recuemraos el nombre del estudiante
  $Query="SELECT ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $ds_fname_usuario_actual=str_texto($row[0]);
  $ds_lame_usuario_actual=str_texto($row[1]);
 
  #Recibimos paraemtros 
 
  $fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');
  $total_emails=RecibeParametroNumerico('total_email');
  $fg_esperando_confirmacion=RecibeParametroNumerico('fg_esperando_confirmacion');
  $fg_reenviar=RecibeParametroBinario('fg_reenviar');
  
  for($i = 1; $i <= $total_emails; $i++){
      
      $email=RecibeParametroHTML('email_'.$i);   
      
      $email=strtolower($email);
      
      
      
      #Validamos formato de eemails
      if((!ValidaEmail($email)) ) {
          $error=1;
          echo"<script>$('#error_email_$i').removeClass('hidden');</script> ";
          exit;
      }else{
          echo"<script>$('#error_email_$i').addClass('hidden');</script> ";
      
      }
      
      
      #Verificamos email repetidos
      if($nuevo_email==$email){
          echo"<script>$('#duplicate_email_$i').removeClass('hidden');</script>";
          exit;
      }else
          echo"<script>$('#duplicate_email_$i').addClass('hidden');</script>";
      $nuevo_email=$email;
      
      
      #Verificamos si la cuenta de correo ya esta registrada en FAME, entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
      $Query="SELECT COUNT(1) FROM c_usuario WHERE ds_email='$email'  ";
      $row=RecuperaValor($Query);
      $ds_email_registrado=$row[0];
      
      if($ds_email_registrado){
          #Dialogo indica que ya esta registrado en fame.
          echo "<script>
                             $.smallBox({
                                  title : '<h4 >".ObtenEtiqueta(2083).":</h4>',
                                  content : ' <p class=\"text-align-right\">$email</p>',
                                  color : '#C46A69',
                                  icon : 'fa fa-envelope',
                                  timeout : 4000
                                });
                           $('#otro_email_$i').removeClass('hidden');</script>
                    </script> ";
          exit;
          
      }else
      echo"<script>$('#otro_email_$i').addClass('hidden');</script>";
      
      
    if(empty($fg_esperando_confirmacion)){  
      #Verificamos si ya existe un antecedente de envio de email esa cuenta para desbloqueo de cursos. 
      $Query="SELECT COUNT(*) FROM k_envio_email_reg_selfp WHERE ds_email='$email'  ";
      $row=RecuperaValor($Query);
      $fl_ya_se_envio_email=$row[0];  
      
      if(!empty($fl_ya_se_envio_email)){
          echo "<script>$('#anteriormente_ya_fue_enviado_$i').removeClass('hidden');</script>";
          exit;    
      }else
          echo"<script>$('#anteriormente_ya_fue_enviado_$i').addClass('hidden');</script>";
      
    }
      
    
    
    
      
  }
  
  
  echo"<script>
     
          $('#btn_evio_email').addClass('disabled');
          $('#btn_evio_email').addClass('mikedisabled');
     </script>"; 

               
for($m = 1; $m <= $total_emails; $m++){
           
    $email=RecibeParametroHTML('email_'.$m);    
    $email=strtolower($email);
             
                    
    #Recuperamos el ultimo id del correo para saber y llevar su bitacora.
    $Query="SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
    $row=RecuperaValor($Query);
    $no_envio=$row[0];
    $no_envio=$no_envio + 1 ;
                    
    # Genera una nueva clave para la liga de acceso al contrato
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    $ds_cve="";
    for($i = 0; $i < 40; $i++)
        $ds_cve .= substr($str, rand(0,62), 1);
    $ds_cve .= date("Ymd").$no_envio;
                    
    #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
    $no_codigo_confirmacion = substr("$ds_cve", -30, 30);

    #se genera el cuerpo del documento de email
    $ds_encabezado = genera_documentoSP($clave, 1, True,'','',100,$ds_cve,$email,'');
    $ds_cuerpo = genera_documentoSP($clave, 2, True,'','',100,$ds_cve,$email,'');
    $ds_pie = genera_documentoSP($clave, 3, True,'','',100,$ds_cve,$email,'');
    $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;
    $ds_contenido = str_replace("#fame_fname_invited#", $ds_fname_usuario_actual, $ds_contenido); # first name a quein se le envia el correo
    $ds_contenido = str_replace("#fame_lname_invited#", $ds_lame_usuario_actual, $ds_contenido);  #bont link redireccion 
                    
                    
    $nombre_quien_escribe=$ds_fname_usuario_actual." ".$ds_lname_usuario_actual;
                    
    $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(107);  
    $ds_email_destinatario=$email;
    $nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje
                    
    $message  = $ds_contenido;
    $message = utf8_decode(str_ascii(str_uso_normal($message)));
    $ds_titulo=ObtenEtiqueta(950);#etiqueta de asunto del mensjae para el anunciante
    $bcc = ObtenConfiguracion(107);
    $send_mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
     

	$copy_send_email=ObtenConfiguracion(131);
	if($copy_send_email){
		
	$bcc=$copy_send_email;
	#Se cuelve enviar la invitacion desde otro correo
	$mail = EnviaMailHTML($nb_nombre_dos, $copy_send_email, $ds_email_destinatario, $ds_titulo, $message, $copy_send_email);
	}




	 
                   
                    if($send_mail=1){
                    
                               // if($fg_esperando_confirmacion){
                        
                                    #eliminamos el correo anteiror enviado 
                                    EjecutaQuery("DELETE FROM c_usuario WHERE  AND ds_nombres='$email' AND cl_sesion='$email' ");//se colocal el nombre y cesion para identificar que se trata de un email que fue enviado atraves de este canal.
                                    EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE ds_email='$email' ");
                                    EjecutaQuery("DELETE FROM c_desbloquear_curso_alumno WHERE fl_invitado_por_usuario=$fl_usuario AND fl_programa_sp=$fl_programa_sp  AND ds_email='$email' ");
                               // }
                                 
                                $Query ="INSERT INTO c_usuario(ds_login,ds_password,cl_sesion,ds_alias,ds_nombres,ds_apaterno,fg_genero,fg_activo,fe_alta)";
                                $Query.="VALUES('$email','$email','$email','$email','$email','$email','M','1',CURRENT_TIMESTAMP)  ";
                                $fl_b2c_new_user=EjecutaInsert($Query);

                                #Si efectivamenete se envio el email entonces se guarda la bitacora de envio
                                $Query="INSERT INTO k_envio_email_reg_selfp (fl_usuario,ds_email,ds_first_name,ds_last_name,no_registro,fg_confirmado,fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod,fl_usu_invita,fg_desbloquear_curso)"; 
                                $Query.="values($fl_b2c_new_user,'$email','','','$no_codigo_confirmacion','0','S','$fl_instituto',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,642,'1')";
                                $fl_envio=EjecutaInsert($Query);
                    
                                #se guarda la relacion apara poder debloquear curso seleccionado.
                                $Query="INSERT INTO c_desbloquear_curso_alumno (fl_envio_correo,ds_email,fe_alta,fe_ultmod,fl_invitado_por_usuario,fl_programa_sp) ";
                                $Query.="VALUES($fl_envio,'$email',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usuario,$fl_programa_sp)";
                                $fl_envi=EjecutaInsert($Query);
                    
                                
                                #Dialogo indica que envio incitacion.
                                echo"<script>
                                            $.smallBox({
                                              title : '<h4 >".ObtenEtiqueta(2082).":</h4>',
                                              content : ' <p class=\"text-align-right\">$email</p>',
                                              color : '#659265',
                                              icon : 'fa fa-envelope',
                                              timeout : 2000
                                            });
                                     </script>";
                                
                               
                     }
                

 
  
 
 
  }

  
  if($fg_reenviar==1){
  
  
      $ds_email=RecibeParametroHTML('ds_email');  
      $ds_email=strtolower($ds_email);
      $fl_envio_correo=RecibeParametroNumerico('fl_envio_correo');
      $fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');     

      
      #Validamos formato de eemails
      if((!ValidaEmail($ds_email)) ) {
          $error=1;
          echo"<script>$('#error_email_$fl_envio_correo').removeClass('hidden');</script> ";
          exit;
      }else{
          echo"<script>$('#error_email_$fl_envio_correo').addClass('hidden');</script> ";
          
      }

      
      #Verificamos si la cuenta de correo ya esta registrada en FAME, entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
      $Query="SELECT COUNT(1) FROM c_usuario WHERE ds_email='$ds_email' ";
      $row=RecuperaValor($Query);
      $ds_email_registrado=$row[0];
      if($ds_email_registrado){
          #Dialogo indica que ya esta registrado en fame.
          echo "<script>
                            $.smallBox({
                                title : '<h4 >".ObtenEtiqueta(2083).":</h4>',
                                content : ' <p class=\"text-align-right\">$ds_email</p>',
                                color : '#C46A69',
                                icon : 'fa fa-envelope',
                                timeout : 4000
                            });
                        $('#otro_email_$fl_envio_correo').removeClass('hidden');</script>
                </script> ";
          exit;
          
      }else
          echo"<script>$('#otro_email_$fl_envio_correo').addClass('hidden');</script>";  
      
      
      
      
      
      #Verifica que el email no sea repetido a uno que ya enviamos y lo tenemos en nuestra lista.
      $Query="SELECT A.fl_envio_correo,A.ds_email,fl_invitado_por_usuario,B.fg_confirmado 
              FROM c_desbloquear_curso_alumno A 
              LEFT JOIN k_envio_email_reg_selfp B ON B.fl_envio_correo=A.fl_envio_correo 
              WHERE  A.fl_invitado_por_usuario=$fl_usuario AND A.fl_programa_sp=$fl_programa_sp AND  A.ds_email='$ds_email'   AND B.fg_confirmado='0' OR B.fg_confirmado IS NULL   ";           
      $rs1 = EjecutaQuery($Query);
      
      for($tot=1;$row2=RecuperaRegistro($rs1);$tot++) {
          $email_enviado=str_texto($row2[1]);
          $fl_envi_correo=$row2[0];
          
          
          #Verificamos que sea un registro diferente de la cuenta que teriormente ya enviamos           
          if($email_enviado==$ds_email){
              
              
              
              if($fl_envio_correo==$fl_envi_correo){
                  #No pas nada
              }else{
                  
                  echo"<script>$('#duplicate_email_$fl_envio_correo').removeClass('hidden');</script>";
                  exit;
              }
              
              
              
          }else
              echo"<script>$('#duplicate_email_$fl_envio_correo').addClass('hidden');</script>";   
      }
      
      
      
      #Verificamos si la ecuenta existe para este curso y lo borramos
      $Query="SELECT A.fl_envio_correo,A.ds_email,fl_invitado_por_usuario,B.fg_confirmado 
              FROM c_desbloquear_curso_alumno A 
              LEFT JOIN k_envio_email_reg_selfp B ON B.fl_envio_correo=A.fl_envio_correo 
              WHERE  A.fl_invitado_por_usuario=$fl_usuario AND A.fl_programa_sp=$fl_programa_sp AND  A.ds_email='$ds_email' AND B.fg_confirmado='0' OR B.fg_confirmado IS NULL   ";           
      $row5= RecuperaValor($Query);
      $fl_envio_eliminar=$row5[0];
      
      if($fl_envio_eliminar){
          #Eliminamos el correo enviado anterior, que se encuentra actualmente y volvemos a renviar una nueva llave de acceso. 
          EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_eliminar  ");
          EjecutaQuery("DELETE FROM c_desbloquear_curso_alumno WHERE fl_envio_correo=$fl_envio_eliminar AND fl_programa_sp=$fl_programa_sp   ");
      }else{
      
          #Eliminamos el correo enviado anterior, que se encuentra actualmente y volvemos a renviar una nueva llave de acceso. 
          EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_correo  ");
          EjecutaQuery("DELETE FROM c_desbloquear_curso_alumno WHERE fl_envio_correo=$fl_envio_correo AND fl_programa_sp=$fl_programa_sp   ");
          
      }
      
      
      
      
      
      
      
      
      
   
      
      
      #Eliminamos el correo enviado anterior, que se encuentra actualmente y volvemos a renviar una nueva llave de acceso. 
      EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_correo  ");
      EjecutaQuery("DELETE FROM c_desbloquear_curso_alumno WHERE fl_envio_correo=$fl_envio_correo AND fl_programa_sp=$fl_programa_sp   ");
      EjecutaQuery("DELETE FROM c_usuario WHERE ds_nombres='$ds_email' AND cl_sesion='$ds_email' ");//se colocal el nombre y cesion para identificar que se trata de un email que fue enviado atraves de este canal.

      #Recuperamos el ultimo id del correo para saber y llevar su bitacora.
      $Query="SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
      $row=RecuperaValor($Query);
      $no_envio=$row[0];
      $no_envio=$no_envio + 1 ;
      
      # Genera una nueva clave para la liga de acceso al contrato
      $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
      $ds_cve="";
      for($i = 0; $i < 40; $i++)
          $ds_cve .= substr($str, rand(0,62), 1);
      $ds_cve .= date("Ymd").$no_envio;

      #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
      $no_codigo_confirmacion = substr("$ds_cve", -30, 30);

      #se genera el cuerpo del documento de email
      $ds_encabezado = genera_documentoSP($clave, 1, True,'','',100,$ds_cve,$ds_email,'');
      $ds_cuerpo = genera_documentoSP($clave, 2, True,'','',100,$ds_cve,$ds_email,'');
      $ds_pie = genera_documentoSP($clave, 3, True,'','',100,$ds_cve,$ds_email,'');
      $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;
      $ds_contenido = str_replace("#fame_fname_invited#", $ds_fname_usuario_actual, $ds_contenido); # first name a quein se le envia el correo
      $ds_contenido = str_replace("#fame_lname_invited#", $ds_lame_usuario_actual, $ds_contenido);  #bont link redireccion 
      
      
      $nombre_quien_escribe=$ds_fname_usuario_actual." ".$ds_lname_usuario_actual;
      
      $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);  
      $ds_email_destinatario=$ds_email;
      $nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje
      
      $message  = $ds_contenido;
      $message = utf8_decode(str_ascii(str_uso_normal($message)));
      $ds_titulo=ObtenEtiqueta(950);#etiqueta de asunto del mensjae para el anunciante
      $bcc = ObtenConfiguracion(107);
      $send_mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
      
      //$send_mail=1;
      if($send_mail=1){
          
              $Query ="INSERT INTO c_usuario(ds_login,ds_password,cl_sesion,ds_alias,ds_nombres,ds_apaterno,fg_genero,fg_activo,fe_alta)";
              $Query.="VALUES('$ds_email','$ds_email','$ds_email','$ds_email','$ds_email','$ds_email','M','1',CURRENT_TIMESTAMP)  ";
              $fl_b2c_new_user=EjecutaInsert($Query);

              #Si efectivamenete se envio el email entonces se guarda la bitacora de envio
              $Query="INSERT INTO k_envio_email_reg_selfp (fl_usuario,ds_first_name,ds_last_name,ds_email,no_registro,fg_confirmado,fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod,fl_usu_invita,fg_desbloquear_curso)"; 
              $Query.="values($fl_b2c_new_user,'','','$ds_email','$no_codigo_confirmacion','0','S','$fl_instituto',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,642,'1')";
              $fl_envio=EjecutaInsert($Query);
          
              #se guarda la relacion apara poder debloquear curso seleccionado.
              $Query="INSERT INTO c_desbloquear_curso_alumno (fl_envio_correo,ds_email,fe_alta,fe_ultmod,fl_invitado_por_usuario,fl_programa_sp) ";
              $Query.="VALUES($fl_envio,'$ds_email',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usuario,$fl_programa_sp)";
              $fl_envi=EjecutaInsert($Query);
          
          
              #Dialogo indica que envio incitacion.
              echo"<script>
                    $.smallBox({
                        title : '<h4 >".ObtenEtiqueta(2082).":</h4>',
                        content : ' <p class=\"text-align-right\">$ds_email</p>',
                        color : '#659265',
                        icon : 'fa fa-envelope',
                        timeout : 4000
                    });
              </script>";
         
              $Query="SELECT count(*) 
                      FROM c_desbloquear_curso_alumno A 
                      LEFT JOIN k_envio_email_reg_selfp B ON B.fl_envio_correo=A.fl_envio_correo 
                      WHERE A.fl_invitado_por_usuario=$fl_usuario 
		              AND A.fl_programa_sp=$fl_programa_sp ";
              $row=RecuperaValor($Query);
              $no_enviados=$row[0];
                              
              #NOTIFICACION DE QUE YA CUMPLIO CON EL REQUISITO y tiene que esperar a que se activen  todas.
              $no_invitaciones_enviadas=CuentaEmailEnviadosDesbloquearCurso($fl_usuario,$fl_programa_sp);
              
              if($no_enviados==$no_invitaciones_enviadas){
              
                  #Dialogo indica que envio incitacion.
                  echo"<script>
                        $.smallBox({
                            title : '<h4 >".ObtenEtiqueta(2085).":</h4>',
                            content : ' <p class=\"text-align-right\">&nbsp;</p>',
                            color : '#3276B1',
                            icon : 'fa fa-envelope',
                            timeout : 4000
                        });
                     </script>";
              }
              
              
              
      
      }
      
  
  
  
  }
  
 ?>