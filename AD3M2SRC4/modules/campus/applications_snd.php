<?php
  
  # Libreria general de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('c', True);
  $no_contrato = RecibeParametroNumerico('con', True);
  
  # Recupera datos de la sesion
  $Query  = "SELECT cl_sesion ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE fl_sesion=$clave";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  
  # Recupera correo del aplicante
  $Query  = "SELECT ds_email, fe_inicio fe_start,ADDDATE(fe_inicio, INTERVAL no_semanas week) fe_end ";
  $Query .= "FROM k_ses_app_frm_1 a, c_periodo b, k_programa_costos c ";
  $Query .= "WHERE a.fl_periodo=b.fl_periodo AND a.fl_programa=c.fl_programa AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_email = str_texto($row[0]);#.",".ObtenConfiguracion(20);
  $fe_start = $row[1];
  $fe_end = $row[2];
  $existe=array();
  # Buscamos en los anios que cubre el curso del alumno 
  # y verificamos que exista un break en cada uno de ellos
  for($i=0;$i<=substr($fe_end,0,4)-substr($fe_start,0,4);$i++){
   $anio = substr($fe_start,0,4)+$i;
   $Query = "SELECT CASE WHEN COUNT(*)>0 THEN 1 ELSE 0 END existe FROM c_break WHERE YEAR(fe_ini)='".$anio."'";
   $row = RecuperaValor($Query);
   $existe [] = $row[0];
   $tot_anios ++;
  }

  # si existe al menos un anio que no tenga break manda un error
  if(!in_array(0, $existe))
    $falta = 0;
  else
    $falta = 1;

  $from_add = ObtenConfiguracion(4);

  $ds_encabezado = genera_documento($clave, 1, True);
  $ds_cuerpo = genera_documento($clave, 2, True);
  $ds_pie = genera_documento($clave, 3, True);
  
  # Genera una nueva clave para la liga de acceso al contrato
  $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
  $ds_cve = date("Ymd").$no_contrato;
  for($i = 0; $i < 10; $i++)
    $ds_cve .= rand(0,10);
  $ds_cve .= $clave;

  $dominio_campus = ObtenConfiguracion(60);
  
  # Envia el correo
  $subject = ObtenEtiqueta(598);
  $message  = $ds_encabezado.$ds_cuerpo;
  $message .= "https://".$dominio_campus."/contract_frm.php?c=$ds_cve&s=$clave<br><br><br>";
  $message .= $ds_pie;
  
  if(!empty(ObtenConfiguracion(59))){
    $bcc = ObtenConfiguracion(20);    
  }
  else{
    $bcc = '';
  }
  
    
  
 
  $falta="";
  if(empty($falta)){  
    #Se envia a configuracion y al alumno
    //$mail_apply = EnviaMailHTML('', $from_add, $ds_email, $subject, $message, $bcc);
      $mail_apply= Mailer($ds_email,$subject,$message,'','','',$bcc);



	if($mail_apply){
	//envia mike
    $bcc2="mike@vanas.ca";
    $mail_apply2 = EnviaMailHTML('', $from_add, $bcc2, $subject, $message);

	}
    #Recuperamos email responsable alumno
    $Query="SELECT  ds_email_r  FROM k_presponsable WHERE cl_sesion='$cl_sesion' ";
    $row=RecuperaValor($Query);
    $ds_email_responsable=$row['ds_email_r'];
       
    #Recuperamos email alternativo
    $Query="SELECT ds_a_email FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
    $row=RecuperaValor($Query);
    $ds_email_alternative=$row['ds_a_email'];
    
    if(!empty($ds_email_responsable)){
        //EnviaMailHTML('', $from_add, $ds_email_responsable, $subject, $message);
        Mailer($ds_email_responsable,$subject,$message);
    }

    if(!empty($ds_email_alternative)){
        //EnviaMailHTML('', $from_add, $ds_email_alternative, $subject, $message);
        Mailer($ds_email_alternative,$subject,$message);
    }


    #Se manda emial al responsabñe o al email alternativo.
    /*  if( (!empty($ds_email_responsable)) ||(!empty($ds_email_alternative)) ){

     

      if(!empty($ds_email_alternative))
            $bcc=$ds_email_alternative;
        else
            $bcc="";
        #Se manda email si exuste un responsable.
        if(!empty($ds_email_responsable)){
            $mail_apply_responsable = EnviaMailHTML('', $from_add, $ds_email_responsable, $subject, $message, $bcc);
        }

        #Se envia email si existe el alternativo
        if( (empty($ds_email_responsable))&&(!empty($ds_email_alternative))){
            $mail_apply_responsable = EnviaMailHTML('', $from_add, $ds_email_alternative, $subject, $message);
          }
   }
     */
  }
  
  $confirmacion = "";
  $error = "";
  if(!empty($mail_apply))
  {
    $confirmacion = 1;
    # Actualiza datos de costos para el contrato
    $Query  = "UPDATE k_app_contrato ";
    $Query .= "SET ds_cadena='$ds_cve' ,link_contract='https://".$dominio_campus."/contract_frm.php?c=$ds_cve&s=$clave' ";
    $Query .= "WHERE cl_sesion='$cl_sesion' ";
    $Query .= "AND no_contrato=$no_contrato ";
    EjecutaQuery($Query);
  }
  else{
    $error = 1;
    # si al anio le falta greagar los break envia error
    if(!empty($falta))
      $falta = $falta;
  }
    
    
  # Regresa al detalle
  echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('confirmacion', $confirmacion);
	Forma_CampoOculto('break',$falta);
    Forma_CampoOculto('error', $error);
    # Enviamos los anios que hacen falta agreagar un braek
    if(!empty($falta)){      
      for($j=0;$j<=$tot_anios-1;$j++){
        $anios = substr($fe_start,0,4)+$j;
         Forma_CampoOculto('existe_'.$j, $existe[$j]);
         Forma_CampoOculto('anios_'.$j,$anios);
      }
      Forma_CampoOculto('tot_anios', $tot_anios-1);
    }
      

    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";

?>