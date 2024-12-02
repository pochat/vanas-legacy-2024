<?php
  
  # Parametros de conexion de base de datos
  define("DATABASE_SERVER", "localhost");
  define("DATABASE_USER",   "vanas_usvanas");
  define("DATABASE_PWD",    "pwdvanas100373k"); 
  define("DATABASE_NAME",   "vanas_vanas");
  define("DATABASE_TYPE",   "mysql");

  # Clase para conexion a Base de Datos 
  // abrir conexion, realizar las consultas y cierre de conexion
  class BD {
    var $SERVER_NAME = DATABASE_SERVER;
    var $USER_NAME = DATABASE_USER;
    var $PWD = DATABASE_PWD;
    var $DATABASE_NAME = DATABASE_NAME;
    var $CONEXION;
    
    function Conecta_BD( ) 
    {
      $this->CONEXION = mysql_connect( $this->SERVER_NAME, $this->USER_NAME, $this->PWD, $this->DATABASE_NAME );
      mysql_select_db( $this->DATABASE_NAME ) or die( mysql_error( ) );
    }
    
    function sql_exec($query) 
    {
      $query = mysql_query($query) or die(mysql_error());
      return $query;
    }
    
    function fetch_array($query) 
    {
      $query = mysql_fetch_array($query);
      return $query;
    }
    
    function fetch_row($query) 
    {
      $query = mysql_fetch_row($query);
      return $query;
    }
    
    function close() 
    {
      mysql_close($this->CONEXION);
    }
  }

  # Funcion para ejecutar consultas a Base de Datos 
  function EjecutaQuery($Query) 
  {
    $BD = new BD();
    $BD->Conecta_BD();
    $res = $BD->sql_exec($Query);
    $BD->close();
    return $res;
  }


  $rs = EjecutaQuery("SELECT ds_valor FROM c_configuracion WHERE cl_configuracion = 4");
  $from_mail = mysql_fetch_array($rs);
  
  $rs = EjecutaQuery("SELECT ds_valor FROM c_configuracion WHERE cl_configuracion = 1");
  $servidor = mysql_fetch_array($rs);
  
  $rs = EjecutaQuery("SELECT ds_valor FROM c_configuracion WHERE cl_configuracion = 5");
  $puerto = mysql_fetch_array($rs);
  
  # Consulta para el listado
  $Query = "SELECT CONCAT(ds_nombres, ' ', ds_apaterno) 'Teacher', ds_email, 
            nb_grupo, nb_programa, d.no_grado, no_semana,  
            (SELECT CONCAT(ds_nombres, ' ', ds_apaterno) FROM c_usuario WHERE a.fl_alumno = c_usuario.fl_usuario) 'Student', 
            fe_calificacion, c.fl_maestro
            FROM k_entrega_semanal a, c_usuario b, c_grupo c, k_term d, c_programa e, k_semana f, c_leccion g 
            WHERE c.fl_maestro = b.fl_usuario 
            AND a.fl_grupo = c.fl_grupo 
            AND c.fl_term = d.fl_term 
            AND d.fl_programa = e.fl_programa 
            AND a.fl_semana = f.fl_semana 
            AND f.fl_leccion = g.fl_leccion 
            AND fe_calificacion < DATE(NOW())
            AND fl_promedio_semana IS NULL
            ORDER BY c.fl_maestro, nb_programa, nb_grupo, a.fl_alumno ";

  
  $rs = EjecutaQuery($Query);
  $i = 0;
  while($row = mysql_fetch_array($rs)) 
  {
    if($row[8] != $datos[$i][$j[$i]][8])
    { $i++;
      $j[$i] = 0;
    }
    $j[$i]++;
    for($k=0; $k<9; $k++)
      $datos[$i][$j[$i]][$k] = $row[$k];
  }
  
  # Prepara variables de ambiente para envio de correo
    $app_frm_email = $from_mail[0];
    ini_set("SMTP", $servidor[0]);
    ini_set("smtp_port", $puerto[0]);
    ini_set("sendmail_from", $app_frm_email);
    
    
  for($l=1; $l<=$i; $l++)
  {
    $app_to_email = $datos[$l][1][1];
    
    # Envia correo de aviso en retraso de calificaciones
    $subject  = "Grading deadline overdue";
    $message  = "Dear ".$datos[$l][1][0]."\n";
    $message .= "\n";
    $message .= "The grading deadline is overdue for the following assignments: \n\n";
    
    #echo "<br><br>".$app_frm_email."<br>";
    #echo $app_to_email."<br>";
    #echo $subject."<br>";
    
    for($m=1; $m<=$j[$l]; $m++)
    {
      $message .= "Course Name: ".$datos[$l][$m][3]."\n";
      $message .= "Group: ".$datos[$l][$m][2]."\n";
      $message .= "Term: ".$datos[$l][$m][4]."\n";
      $message .= "Week: ".$datos[$l][$m][5]."\n";
      $message .= "Student: ".$datos[$l][$m][6]."\n";
      $message .= "Grading deadline: ".$datos[$l][$m][7]."\n\n";
    }
    
    $message .= "\nRegards\n";
    $message .= "Vancouver Animation School\n\n";
    $message .= "This is an automatic email. Please do not reply.\n";
    
    
    #echo $message;
  
  $headers = "From: $app_frm_email\r\nReply-To: $app_frm_email\r\n";
  $mail_sent = mail($app_to_email, $subject, $message, $headers);
  }
?>