<?php
  
  # 30 5 * * 0 php /mnt/data/home/vanas/cronjob/we_miss_you.php
  # Correra los domingos a las 5 y media de la manana

  # Libreria de funciones
  require '/var/www/html/vanas/fame/lib/self_general.php';
  
  # Si el usuario paso de estos días se le envia un correo
  $days = ObtenConfiguracion(118);
  
  # Fecha actual
  $fe_actual = ObtenerFechaActual();
  
  # Obtenemos el template
  $row1 = RecuperaValor("SELECT nb_template FROM k_template_doc WHERE fl_template=143");
  $nb_template = $row1[0];
  
  # Copia a admin
  $bcc_fame = ObtenConfiguracion(107);
  
  # noreplay
  $noreplay = ObtenConfiguracion(67);
  
  # Variables para activar o desactivar este cron
  $act_inact = ObtenConfiguracion(119);

  if(!empty($act_inact)){
    # Obtenemos los usuarios que tienen dias que no sea han conectado
    $Query = "SELECT fl_usuario, ds_email ";
    $Query .= "FROM c_usuario ";
    $Query .= "WHERE fg_activo='1' AND  fl_perfil_sp IN(".PFL_ADMINISTRADOR.",".PFL_MAESTRO_SELF.",".PFL_ESTUDIANTE_SELF.") ";
    $Query .= "AND fe_ultacc IS NOT NULL  AND DATEDIFF('".$fe_actual."', fe_ultacc )>".$days." LIMIT 1";
    $rs = EjecutaQuery($Query);
    $tot_users = CuentaRegistros($rs);
    
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      $fl_usuario = $row[0];
      $ds_email = $row[1];
      
      # Mensaje
      $body = genera_documento_sp($fl_usuario, 2, 143);
      $footer = genera_documento_sp($fl_usuario, 3, 143);    
      $ds_message = str_uso_normal($body.$footer);
      
      # Variable para enviar el correo
      $envia_notify = false;
      
      # Buscar si ya envio el correo
      if(!ExisteEnTabla('k_usu_inactivity', 'fl_usuario', $fl_usuario)){      
        $envia_notify = true;      
      }
      else{
        # Verifica el ultimo envio del correo
        # Si la ultima vez que se le envio el correo 
        $Query= "SELECT fl_usu_ina, fl_usuario, fe_notify FROM k_usu_inactivity WHERE fl_usuario=".$fl_usuario." AND DATEDIFF(CURDATE(), fe_notify )>".$days." ORDER BY fl_usu_ina DESC limit 1 ";
        $row = RecuperaValor($Query);
        $new_email = $row[0];

        if(!empty($new_email)){
          $envia_notify = true;        
        }      
      }
      
      # Envia la notificacion si es necario
      if($envia_notify==true){
        $fg_email = EnviaMailHTML($nb_template, $noreplay, $ds_email, $nb_template, $ds_message, $bcc_fame);

        if($fg_email==true){
          # Inserta registro
          $Query1 = "INSERT INTO k_usu_inactivity (fl_usuario,fe_notify) VALUES (".$fl_usuario.", NOW())";
          EjecutaQuery($Query1);
        }
      }    
    }
  }

?>