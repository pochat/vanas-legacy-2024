<?php

  # Libreria de funciones
  // require_once("../../lib/sp_general.inc.php");
  require_once("../lib/self_general.php");

  function VerificarrDireccionCorreo($direccion){
       $Sintaxis='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
       if(preg_match($Sintaxis,$direccion))
          return true;
       else
         return false;
    }
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  
  # Recibe  datos
  $fl_action=RecibeParametroNumerico('fl_action');
  $fg_user_ya_existente=RecibeParametroNumerico('fg_user_ya_existente');
  $fl_usuario_esta_invitado=RecibeParametroNumerico('fl_usuario_existente');
  
  # Verificamos si aun hay licencias disponibles
  $Query="SELECT fg_tiene_plan FROM c_instituto where fl_instituto=$fl_instituto ";
  $rowp=RecuperaValor($Query);
  $fg_tiene_plan=$rowp['fg_tiene_plan'];
  
  if(empty($fg_tiene_plan)){
	$licencias_disponibles_trial = ValidaUserTrial($fl_usuario);
  }else{
	$licencias_disponibles_trial=ObtenNumLicenciasDisponibles($fl_instituto);  
  }
  
  # Si la accion es solo 
  if($fl_action == ADD_STD || $fl_action == ADD_MAE){
    $ds_first_name= RecibeParametroHTML('fname');
    $ds_last_name = RecibeParametroHTML('lname');
    $ds_email = RecibeParametroHTML('email');
  
    
    if($licencias_disponibles_trial == 0){
      Alert("Trial licenses", "You haven´t available licenses", "CE2F3A", "fa-times");
    }
    else{

        #Primero verifica si el email pertnece al isntituto 
        $Query="SELECT COUNT(1) FROM c_usuario WHERE  ds_email='$ds_email' AND fl_perfil_sp IN (".PFL_MAESTRO_SELF.",".PFL_ESTUDIANTE_SELF.",".PFL_ADMINISTRADOR.")AND fl_instituto=$fl_instituto ";
		$row=RecuperaValor($Query);
		$ya_existe_email_registrado=$row[0]; 

        $Query2="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto ";
        $rowg=RecuperaValor($Query2);
        $nb_instituto_invi=$rowg['ds_instituto'];

        # Enviamos notificacion si fue o no enviado la invitacion
        if(!empty($ya_existe_email_registrado)){
            Alert(ObtenEtiqueta(1523), "<i class='fa fa-times'></i> <i> </i>", "CE2F3A", "fa-times");
        }else{


            if($fg_user_ya_existente==1){

                # Enviamos invitacion para unirse a este Instituto, aqui este usuario ya pertnece a otro Instituto.
                Send_Invitacion($ds_email, $ds_first_name, $ds_last_name, '', $fl_action, $fl_usuario,$fl_usuario_esta_invitado,$fl_instituto);
                Alert(ObtenEtiqueta(954)." ".$ds_email, "<i class='fa fa-smile-o'></i> <i> </i>", "5F895F", "fa-check", 4000); 
                
                #Enviamos notificacion via node.
                echo"<script>
                        
                        var fl_usuario=".$fl_usuario.";
                        var nb_instituto='".$nb_instituto_invi."';
                        var etq='".ObtenEtiqueta(2561)."';
                        var fl_instituto_invita=".$fl_instituto.";
                        var fl_user_alk_invitando=".$fl_usuario_esta_invitado.";
                        socket.emit('invitation-instituto', fl_usuario,nb_instituto,etq,fl_instituto_invita,fl_user_alk_invitando);
                     </script>";


            }else{
                
                #Primero verifica si el email pertnece a otro isntituto ya registrado.
                $Query="SELECT fl_usuario FROM c_usuario WHERE  ds_email='$ds_email' AND fl_perfil_sp IN (".PFL_MAESTRO_SELF.",".PFL_ESTUDIANTE_SELF.",".PFL_ADMINISTRADOR.")AND fl_instituto<>$fl_instituto ";
                $row=RecuperaValor($Query);
                $ya_existe_email_registrado=$row[0];

                if(!empty($ya_existe_email_registrado)){
                    
                    #Saldra una alerta para indicar que enviara invitacion para indicar que fue agregado en otro instituto.#09_10_2010, ya no se envia ahora se verifica si existe ya en fame y se envia otra invitacion(un alumno ya puede pertenecer varias escuelas.) 
                    //Send_Invitacion($ds_email, $ds_first_name, $ds_last_name, '', $fl_action, $fl_usuario,$row[0]);
                    Alert(ObtenEtiqueta(954)." ".$ds_email, "<i class='fa fa-smile-o'></i> <i> </i>", "296191", "fa-check","",$row[0]);    

                }else{
                    # Enviamos invitacion
                    Send_Invitacion($ds_email, $ds_first_name, $ds_last_name, '', $fl_action, $fl_usuario);
                    Alert(ObtenEtiqueta(954)." ".$ds_email, "<i class='fa fa-smile-o'></i> <i> </i>", "5F895F", "fa-check", 4000);    

                }

            }

			   
        }


		
		
		  
		  
		  
    }
  }
  
  # Import estudiantes o teachers
  if($fl_action == IMP_STD || $fl_action == IMP_MAE){
    $tamano = $_FILES["fl_archivo"]['size'];
    $tipo = $_FILES["fl_archivo"]['type'];
    $archivo = $_FILES["fl_archivo"]['name'];
    $ext = strtolower(ObtenExtensionArchivo($_FILES['fl_archivo']['name']));
    $new_file = "exp_".$fl_instituto."_".rand(1000,2000).".".$ext;    
    $file_new = rename ( $archivo, $new_file);
    $archivotmp = $_FILES["fl_archivo"]['tmp_name'];
    # Mueve el archivo
    if(move_uploaded_file($archivotmp, $new_file)){
      $nb_archivo=1;
    }else{
      $nb_archivo=0;  
    }
    # Si esta el archivo realizara la accion
    if (($file = fopen($new_file, "r")) !== FALSE && $nb_archivo ) {
      # Lee los nombres de los campos
      $name_camps = fgetcsv($file, 0, ",", "\"", "\"");
      $num_camps = count($name_camps);
      $names_camps[$num_camps -1];
      $tot_reg1 = 0;
      while ($data = fgetcsv ($file, 1000, ",")){
        $ds_email = $data[0];
        $ds_fname = $data[1];
        $ds_lname = $data[2];
        $nb_grupo = $data[3];
        if(!empty($ds_email) && !empty($ds_fname) && !empty($ds_lname))
          $tot_reg1 ++;
      }
    }
    
    # Empezamos a enviar correo o las notificaciones 
    if (($fichero = fopen($new_file, "r")) !== FALSE && $nb_archivo ) {
      # Lee los nombres de los campos
      $nombres_campos = fgetcsv($fichero, 0, ",", "\"", "\"");
      $num_campos = count($nombres_campos);
      $nombres_campos[$num_campos -1];
      # Variables para contadores
      $no_enviados = 0; 
      $emails_enviados = "";
      $no_noenviados = 0;
      $emails_noenviados = "";     
      $tot_reg = 1;      
      while ($data = fgetcsv ($fichero, 1000, ",")){
        # Columnas del archivo
        $ds_email = str_html_bd($data[0]);
        $ds_fname = str_html_bd($data[1]);
        $ds_lname = str_html_bd($data[2]);
        $nb_grupo = str_html_bd($data[3]);
        if(!empty($ds_email) && !empty($ds_fname) && !empty($ds_lname)){
        # Eliminamos los registros que se encuentren con el mismo correo
        EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE ds_email='".$ds_email."'");
        
        # Descanso 1 minuto para mostrar de forma pausada el proceso
        sleep(0.10);
        
        # Obtenemos el porcentaje
        $porcentaje = round($tot_reg * 100 / $tot_reg1);
        
        # Verificamos si aun hay licencias disponibles
        $licencias_disponibles_trial = Licencias_disponibles_Trial($fl_instituto);
        
		$fg_plan=  ObtenPlanActualInstituto($fl_instituto);

        if(!empty($fg_plan)){
        
            $licencias_disponibles_trial=ObtenNumLicenciasDisponibles($fl_instituto);
        
        }
        
        
		
		
		
        # Si no hay licencias no envia los correos y debera detener el proceso
        if($licencias_disponibles_trial>=$tot_reg1){
        
          #Verificamos si la cuenta de correo ya esta activo entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
          $Query="SELECT COUNT(1) FROM c_usuario WHERE  ds_email='$ds_email' AND fl_perfil_sp IN (".PFL_MAESTRO_SELF.",".PFL_ESTUDIANTE_SELF.") ";
          $row=RecuperaValor($Query);
          $existe_reg = $row[0];
          
          # Verificamos el email
          // $email_validar = filter_var($ds_email, FILTER_VALIDATE_EMAIL);
          $email_validar = VerificarrDireccionCorreo($ds_email);
          
          # Verifica si se envia la invitacion
          if(!$existe_reg AND $email_validar){
            # Enviamos invitacion si no obtenemos un insert quiere decir que no se envio la invitacion
            $fl_insertado = Send_Invitacion($ds_email, $ds_fname, $ds_lname, $nb_grupo, $fl_action, $fl_usuario);
            if(!empty($fl_insertado)){
              $no_enviados ++;
              $emails_enviados = $emails_enviados."- ".$ds_email."<br>";
              $send = True;
            }
            else{
              $no_noenviados ++;
              $ds_causa = ObtenEtiqueta(1085);
              $emails_noenviados = $emails_noenviados."- ".$ds_email." (".$ds_causa.")<br>";
              $send = False;
            }            
          }
          else{
            # Dependiendo del perfil guadara el registro
            if($fl_action==ADD_STD || $fl_action == IMP_STD){
                $fg_tipo_registro="S";//Studiante,representa que es una invitacion para el admistrador
            }
            if($fl_action==ADD_MAE || $fl_action == IMP_MAE){
                $fg_tipo_registro="T";//teacher
            }
            # Causa del porque no se envio confirmacion
            $ds_causa = ObtenEtiqueta(1086);
            if(!$email_validar)
              $ds_causa = ObtenEtiqueta(1085);            
            
            $no_noenviados ++;
            $emails_noenviados = $emails_noenviados."- ".$ds_email." (".$ds_causa.")<br>";
            $send = False;
          }
        }
        else{
          $ds_causa = ObtenEtiqueta(1087); 
          $no_noenviados ++;
          $emails_noenviados = $emails_noenviados."- ".$ds_email." (".$ds_causa.")<br>";
          $send = False;
        }
        if($send==False){
          #intituto
          $fl_instituto = ObtenInstituto($fl_usuario);
          # Insertamos pero no enviamos el correo porque ya existe en la bd
          $Query = "INSERT INTO k_envio_email_reg_selfp (ds_first_name,ds_last_name,ds_email,no_registro,fg_confirmado, ";
          $Query.= "fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod, fg_enviado, ds_causa, nb_grupo) "; 
          $Query.= "VALUES('$ds_fname','$ds_lname','$ds_email','NULL','0','$fg_tipo_registro','$fl_instituto', ";
          $Query.= "CURRENT_TIMESTAMP,CURRENT_TIMESTAMP, '0', '$ds_causa', '$nb_grupo') ";
          EjecutaQuery($Query);
        }
        
        # Mostramos el avance
        ?>
        <script>
          var porcentaje = '<?php echo $porcentaje; ?>%';
          $('#barra_progreso').css('width', porcentaje);
          $('#barra_progreso').html(porcentaje);
        </script>
        <?php
        # Con esta funcion hago que se muestre el resultado de inmediato y no espere a terminar todo el bucle con los 25 registros para recien mostrar el resultado
        flush();
        ob_flush();
        
        $tot_reg ++;
        }
      }      
      fclose ($fichero );
            
    }
    
    # Enviamos notificacion si fue o no enviado la invitacion
    ?>
    <script>
    $("#inf_invitaciones").empty();
    $("#inf_invitaciones").removeClass("hidden");
    var mensaje = "<div><h5><strong><?php echo ObtenEtiqueta(1088); ?> (<?php echo $tot_reg-1; ?>)</strong><h5></div>";
        mensaje += "<a id='aemail_enviados' class='text-success cursor-pointer'><i class='fa fa-check'></i> <?php echo ObtenEtiqueta(1089); ?> <strong>(<?php echo $no_enviados; ?>)</strong></a>";
        mensaje += "<div id='emails_enviados' class='bs-example hidden'><?php echo $emails_enviados; ?></div>";
        mensaje += "<br/><a id='aemail_noenviados' class='text-danger cursor-pointer'><i class='fa fa-bell text-danger'></i> <?php echo ObtenEtiqueta(1090); ?> <strong>(<?php echo $no_noenviados; ?>)</strong></a>";
        mensaje += "<div id='emails_noenviados' class='hidden'><?php echo $emails_noenviados; ?></div>";
    $("#inf_invitaciones").append(mensaje);
    $("#envio_boton").hide();
    $("#cerrar_modal").empty();
    $("#cerrar_modal").append("<i class='fa fa-times-circle fa-check'></i> Close");
    $("#cerrar_modal").removeClass("btn-default");
    $("#cerrar_modal").addClass("btn-success");
    // Ocultamos el browser
    $("#div_file").empty();
    $("#id_example").hide().empty();    
    
    
    
    // Muestra los correos que se enviaron
    $("#aemail_enviados").click(function(){
      var aux = $('#emails_enviados').is(":visible"); 
      if(aux==true)
        $('#emails_enviados').addClass("hidden");
      else
        $('#emails_enviados').removeClass("hidden");
      $("#emails_noenviados").addClass("hidden");
    });
    
    // Muestra los correo no enviados
    $("#aemail_noenviados").click(function(){
      $("#emails_enviados").addClass("hidden");
       var aux = $('#emails_noenviados').is(":visible"); 
      if(aux==true)
        $('#emails_noenviados').addClass("hidden");
      else
        $('#emails_noenviados').removeClass("hidden");
    });
    $("#envio_boton").addClass('disabled');
    
    </script>
    <?php 
    # Eliminamos el archivo
    unlink($new_file);    
  }