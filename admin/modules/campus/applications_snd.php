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

  $from_add = ObtenConfiguracion(20);

  $ds_encabezado = genera_documento($clave, 1, True);
  $ds_cuerpo = genera_documento($clave, 2, True);
  $ds_pie = genera_documento($clave, 3, True);
  
  # Genera una nueva clave para la liga de acceso al contrato
  $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
  $ds_cve = date("Ymd").$no_contrato;
  for($i = 0; $i < 10; $i++)
    $ds_cve .= substr($str, rand(0,62), 1);
  $ds_cve .= $clave;

  $dominio_campus = ObtenConfiguracion(60);
  
  # Envia el correo
  $subject = ObtenEtiqueta(598);
  $message  = $ds_encabezado.$ds_cuerpo;
  $message .= "http://".$dominio_campus."/contract_frm.php?c=$ds_cve<br><br><br>";
  $message .= $ds_pie;
  
  if(ObtenConfiguracion(59)){
    $cc =  ObtenConfiguracion(83);
    $bcc = ObtenConfiguracion(20);    
  }
  else{
    $cc = '';
    $bcc = '';
  }
  if(empty($falta)){  
    $mail_apply = EnviaMailHTML('', $from_add, $ds_email, $subject, $message, $bcc);
    # Envia a admin
    $mail_admin = EnviaMailHTML('', $from_add, $cc, $subject, $message);
  }
  
  $confirmacion = "";
  $error = "";
  if($mail_apply AND $mail_admin)
  {
    $confirmacion = 1;
    # Actualiza datos de costos para el contrato
    $Query  = "UPDATE k_app_contrato ";
    $Query .= "SET ds_cadena='$ds_cve' ";
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