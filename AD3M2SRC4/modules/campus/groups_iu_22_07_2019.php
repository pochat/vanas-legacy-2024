<?php
  function logger($msg, $esVarDump=true) {
    $showLog = false;
    if(!$showLog)
      return false;
    
    if($esVarDump)
      var_dump($msg);
    else
      echo $msg . "<br>";
  }
?>

<?php  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../lib/AdobeConnectClient.class.php';
  
  require '../../lib/adobeconnect/LicenciaAdobe.class.php';
  require '../../lib/adobeconnect/LicenciaAdobeService.class.php';
  require '../../lib/campusclases/ClasesService.class.php';
    
  function delLiveSession($clMeetingId, LicenciaAdobe $licenciaAC) {
    
    $clLicencia = $licenciaAC->getClLicencia();
    
    $clienteAdobe = new AdobeConnectClient($licenciaAC);
    
    $clienteAdobe->deleteMeeting($clMeetingId);
    
  }
  
  function iuLiveSession($p_fl_clase, LicenciaAdobe $licenciaAC) {
    
    $clLicencia = $licenciaAC->getClLicencia();
    
    logger("clLicencia: $clLicencia <br>");
    
	  // MDB ADOBECONNECT Datos para los parametros Integracion Adobe Connect
	  // Se crean las sesiones de Adobe Connect desde este programa
	  // Para BBB no es necesario crearlas, se hace al momento en que un teacher 
	  // o un student entra a la sesion desde el campus.
    $Query  = "select ds_titulo, DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') fe_ini, ";
    $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL 2 HOUR), '%Y-%m-%dT%H:%i:%s') fe_fin ";
    $Query .= "from k_clase a, k_semana b, c_leccion c ";
    $Query .= "where a.fl_semana = b.fl_semana ";
    $Query .= "and b.fl_leccion = c.fl_leccion ";
    $Query .= "and a.fl_clase = $p_fl_clase";
    $row = RecuperaValor($Query);   
    
    logger("Query en iuLiveSession: $Query <br>");

    $titulo_leccion = $row[0];
    $fecha_inicio = $row[1];
    $fecha_fin = $row[2];
    $id_leccion_url = "VAN_".$clLicencia."_".rand(1000000, 9999999);   

    // Inserta o actualiza la sesion en adobe connect    
    $clienteAdobe = new AdobeConnectClient($licenciaAC);

    // Crea o actualiza la reunion en adobe connect
    $Query  = "select fl_live_session, cl_meeting_id ";
    $Query .= "from k_live_session where fl_clase = $p_fl_clase";   
    $row = RecuperaValor($Query);
    
    logger("Query 2 en iuLiveSession: $Query <br>");

    $fl_live_session = $row[0];
    $cl_meeting_id = $row[1];

    // MDB 25/OCT/2012
    // El nombre de la leccion para la sesion de adobe connect debe ser de maximo 60 caracteres
    // El titulo tiene concatenado el folio de la clase que es un int de 8 y un gion, por lo que para
    // el titulo de la leccion quedan 51 caracteres disponibles, trunco la cadena a esa longitud.
    $titulo_leccion = substr($titulo_leccion, 0, 51);
    
    $titulo_leccion = $titulo_leccion . "_" . $p_fl_clase;
    
    logger("Titulo leccion: $titulo_leccion <br>");
    
    // No existe la sesion y la crea, existe y la actualiza
    if (empty($fl_live_session)) { 
	  // Regresa un arreglo de tipo:
	  // $data["estatus"]
	  // $data["meeting_id"]
	  // $data["url-path"]
	  logger("Insertando el live session <br>");
      
      $data = $clienteAdobe->createMeeting('', utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin, $id_leccion_url);        

	  $meeting_id = $data["meeting_id"];
	  logger ("Meeting id: $meeting_id <br>");
      
	  if ($data["estatus"] == "existe")
		$id_leccion_url = $data["url-path"];
	        
      // Se creo la clase en adobe connect
      if (!empty($meeting_id)) {
        // Permisos para que se puedan conectar a la clase
        $clienteAdobe->permisosMeeting($meeting_id);    
        $clienteAdobe->addHostMeeting($meeting_id);

        $Query  = "INSERT INTO k_live_session ";
        $Query .= "(fl_clase, cl_estatus, ";
        $Query .= "ds_meeting_id, ds_password_admin, ds_password_asistente, ds_mensaje_bienvenida, cl_meeting_id, cl_licencia) ";
        $Query .= "VALUES ($p_fl_clase, 1, '$id_leccion_url', '$moderatorPW', '$attendeePW', '$welcome', '$meeting_id', $clLicencia) ";
        EjecutaQuery($Query);
      }
      else {
        // No se pudo crear, puede ser que ya exista        
      }
    }
    else {
      $clienteAdobe->updateMeeting($cl_meeting_id, utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin);
    }	  
    // Termina codigo Adobe connect 
  }
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
    
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_GRUPOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$fl_term = RecibeParametroNumerico('fl_term');
  $fl_maestro = RecibeParametroNumerico('fl_maestro');
  $ds_login = RecibeParametroHTML('ds_login');
  $nb_grupo = RecibeParametroHTML('nb_grupo');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fl_periodo = RecibeParametroNumerico('fl_periodo');
  $no_grado = RecibeParametroNumerico('no_grado');
  $fg_dia_sesion = RecibeParametroNumerico('fg_dia_sesion');
  
  # Recupera los alumnos del grupo
  $tot_alumnos = RecibeParametroNumerico('tot_alumnos');
  for($i = 0; $i < $tot_alumnos; $i++)
    $fl_alumno[$i] = RecibeParametroNumerico('fl_alumno_'.$i);
  
  # Recupera las fechas de cada clase
  $tot_semanas = RecibeParametroNumerico('tot_semanas');
  for($i = 0; $i < $tot_semanas; $i++) {
    $fl_semana[$i] = RecibeParametroNumerico('fl_semana_'.$i);
    $no_semana[$i] = RecibeParametroNumerico('no_semana_'.$i);
    $ds_titulo[$i] = RecibeParametroHTML('ds_titulo_'.$i);
    $fe_clase[$i] = RecibeParametroFecha('fe_clase_'.$i);
    $hr_clase[$i] = RecibeParametroHoraMin('hr_clase_'.$i);
    $fl_clase[$i] = RecibeParametroNumerico('fl_clase_'.$i);
    //$fe_original[$i] = RecibeParametroFecha('fe_original_'.$i);
    //$hr_original[$i] = RecibeParametroHoraMin('hr_original_'.$i);        
  }

  # Valida campos obligatorios
  if(empty($fl_term))
    $fl_term_err = ERR_REQUERIDO;
  if(empty($fl_maestro))
    $fl_maestro_err = ERR_REQUERIDO;
  if(empty($nb_grupo))
    $nb_grupo_err = ERR_REQUERIDO;
  for($i = 0; $i < $tot_semanas; $i++) {
    if(empty($fe_clase[$i]))
      $fe_clase_err[$i] = ERR_REQUERIDO;
    if(empty($hr_clase[$i]))
      $hr_clase_err[$i] = ERR_REQUERIDO;
  }
  
  # Verifica que el formato de la fecha sea valido
  for($i = 0; $i < $tot_semanas; $i++) {
    if(!empty($fe_clase[$i]) AND !ValidaFecha($fe_clase[$i]))
      $fe_clase_err[$i] = ERR_FORMATO_FECHA;
    if(!empty($hr_clase[$i]) AND !ValidaHoraMin($hr_clase[$i]))
      $hr_clase_err[$i] = ERR_FORMATO_HORAMIN;
    
    
      // MDB
      // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
      // entonces puede agregar la clase, de lo contrario regresa el error a la forma.     
      
      $licenciaService = new LicenciaAdobeService();
      $clasesService = new ClasesService();
            
      $fechaHora = "'" . ValidaFecha($fe_clase[$i]) . ' ' . ValidaHoraMin($hr_clase[$i]) . "'";
      
      $fl_clase_actual = $fl_clase[$i];
      
      $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, $fl_clase_actual);
      $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, $fl_clase_actual);      
      $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);      
            
      if (!$licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {    
        $fe_clase_err[$i] = ObtenMensaje(230);
      }   
        
  }
  
  # Regresa a la forma con error
  $fg_error = $fl_term_err || $fl_maestro_err || $nb_grupo_err;
  for($i = 0; $i < $tot_semanas; $i++) {
    $fg_error = $fg_error || $fe_clase_err[$i];
    $fg_error = $fg_error || $hr_clase_err[$i];
  }
  logger("Tiene error: $fg_error");
  
  logger($fe_clase_err, true);
 
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('fl_term', $fl_term);
    Forma_CampoOculto('fl_term_err', $fl_term_err);
    Forma_CampoOculto('fl_maestro', $fl_maestro);
    Forma_CampoOculto('fl_maestro_err', $fl_maestro_err);
    Forma_CampoOculto('ds_login', $ds_login);
    Forma_CampoOculto('nb_grupo', $nb_grupo);
    Forma_CampoOculto('nb_grupo_err', $nb_grupo_err);
    Forma_CampoOculto('fl_programa', $fl_programa);
    Forma_CampoOculto('fl_periodo', $fl_periodo);
    Forma_CampoOculto('no_grado', $no_grado);
    Forma_CampoOculto('fg_dia_sesion', $fg_dia_sesion);
    Forma_CampoOculto('tot_semanas', $tot_semanas);
    for($i = 0; $i < $tot_semanas; $i++) {
      Forma_CampoOculto('fl_semana_'.$i, $fl_semana[$i]);
      Forma_CampoOculto('no_semana_'.$i, $no_semana[$i]);
      Forma_CampoOculto('ds_titulo_'.$i, $ds_titulo[$i]);
      Forma_CampoOculto('fe_clase_'.$i, $fe_clase[$i]);
      Forma_CampoOculto('fe_clase_err_'.$i, $fe_clase_err[$i]);
      Forma_CampoOculto('hr_clase_'.$i, $hr_clase[$i]);
      Forma_CampoOculto('hr_clase_err_'.$i, $hr_clase_err[$i]);
      Forma_CampoOculto('fl_clase_'.$i, $fl_clase[$i]);
    }
    echo "\n</form>
<script>
 document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_grupo (fl_term, fl_maestro, nb_grupo) ";
    $Query .= "VALUES($fl_term, $fl_maestro, '$nb_grupo')";
    $fl_grupo = EjecutaInsert($Query);
  }
  else {
    $Query  = "UPDATE c_grupo SET fl_maestro=$fl_maestro, nb_grupo='$nb_grupo' ";
    $Query .= "WHERE fl_grupo=$clave";
    EjecutaQuery($Query);
  }
  
  # Reinicializa los alumnos del grupo
  if(!empty($clave)) {
    EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_grupo=$clave");
    for($i = 0; $i < $tot_alumnos; $i++) {
      if(!empty($fl_alumno[$i])) {
        
        # Recupera el grupo actual del alumno
        $Query  = "SELECT fl_periodo, b.fl_term ";
        $Query .= "FROM k_alumno_grupo a, c_grupo b, k_term c ";
        $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
        $Query .= "AND b.fl_term=c.fl_term ";
        $Query .= "AND a.fl_alumno=".$fl_alumno[$i];
        $row = RecuperaValor($Query);
        $fl_periodo_ori = $row[0];
        $fl_term_ori = $row[1];
        
        # Saca al alumno del grupo actual
        EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_alumno=".$fl_alumno[$i]."");
        
        # Revisa si el grupo anterior es del mismo periodo
        if(!empty($fl_periodo_ori) AND $fl_periodo == $fl_periodo_ori AND $fl_term <> $fl_term_ori)
          EjecutaQuery("DELETE FROM k_alumno_term WHERE fl_alumno=".$fl_alumno[$i]." AND fl_term=$fl_term_ori");
        
        # Asigna al alumno al nuevo grupo
        EjecutaQuery("INSERT INTO k_alumno_grupo(fl_grupo, fl_alumno) VALUES($clave, ".$fl_alumno[$i].")");
        
        # Inserta un alumno en k_alumno_historia
        $row = RecuperaValor("SELECT 1 FROM k_alumno_historia WHERE fl_alumno=$fl_alumno[$i] AND fl_grupo=$clave");
        if(!ExisteEnTabla('k_alumno_historia','fl_alumno', $fl_alumno[$i], 'fl_grupo', $clave, True)){
          $Query  = "INSERT INTO k_alumno_historia(fl_alumno, fl_programa, fl_periodo, no_grado, fl_grupo, fl_maestro, fe_inicio) ";
          $Query .= "VALUES($fl_alumno[$i], $fl_programa, $fl_periodo, $no_grado, $clave, $fl_maestro, CURRENT_TIMESTAMP)";
          EjecutaQuery($Query);
        }
        # Revisa si ya existen los datos del alumno para el term, si no los inicializa
        $row = RecuperaValor("SELECT fl_alumno FROM k_alumno_term WHERE fl_alumno=".$fl_alumno[$i]." AND fl_term=$fl_term");
        if(empty($row[0]))
          EjecutaQuery("INSERT INTO k_alumno_term (fl_alumno, fl_term) VALUES(".$fl_alumno[$i].", $fl_term)");
      }
    }
  }  
  
  # Inserta o actualiza las fechas de clases virtuales
  if(empty($clave)) {
    $rs = EjecutaQuery("SELECT fl_semana, fe_publicacion FROM k_semana WHERE fl_term=$fl_term");
    while($row = RecuperaRegistro($rs)) {
      $fl_semana = $row[0];
      $anio_pub = substr($row[1], 0, 4);
      $mes_pub = substr($row[1], 5, 2);
      $dia_pub = substr($row[1], 8, 2);
      $fe_publicacion = date_create( );
      date_date_set($fe_publicacion, $anio_pub, $mes_pub, $dia_pub);
      $dia_semana = date('N', date_format($fe_publicacion, 'U')); 
      if($fg_dia_sesion > $dia_semana)
        $dif_dias = $fg_dia_sesion - $dia_semana;
      else
        $dif_dias = 7 - $dia_semana + $fg_dia_sesion;
      date_modify($fe_publicacion, "+$dif_dias day");
      $fe_clase_d = date_format($fe_publicacion, 'Y-m-d'); // Se toma como valor por omision la fecha de publicacion + n dias
      # Si la fecha de clase cae en un break 
      $hr_clase_d = ObtenConfiguracion(26);
      
      # Verificamos los breaks de la school con la fe_clase_d
           
      // MDB
      // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
      // entonces puede agregar la clase, de lo contrario regresa el error a la forma.     
      
      $licenciaService = new LicenciaAdobeService();
      $clasesService = new ClasesService();
            
      $fechaHora = '{$fe_clase_d} {$hr_clase_d}';
      
      $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora);
      $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora);      
      $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);      

      if ($licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {    
        
        $Query  = "INSERT INTO k_clase (fl_grupo, fl_semana, fe_clase) ";
        $Query .= "VALUES($fl_grupo, $fl_semana, '$fe_clase_d $hr_clase_d')";
        $fl_clase_insertada = EjecutaInsert($Query);
        
        // Creacion sesion campus y AdobeConnect
        $licenciaAC = $licenciasAdobe[0];
        iuLiveSession($fl_clase_insertada, $licenciaAC);
        
      }
      else {
        // TODO Mensaje de error
      }
          
    }
  }
  else {    
    
    logger("Total de semanas: $tot_semanas");
    
    for($i = 0; $i < $tot_semanas; $i++) {
      $fe_clase[$i] = "'".ValidaFecha($fe_clase[$i])." ".$hr_clase[$i]."'";
      //$fe_clase_actual[$i] = "'".ValidaFecha($fe_original[$i])." ".$hr_original[$i]."'";
      
      if(empty($fl_clase[$i]))
        $clave_clase = $clave;
      else
        $clave_clase = $fl_clase[$i];      
      
      logger("======== INICIO PROCESO CLASE =================");
      
      logger("Registro: $i");
      
      logger("Folio clase: " . $fl_clase[$i] . "<br>");
      logger("Fecha clase: " . $fe_clase[$i] . "<br>");
      //logger("Fecha original: " . $fe_clase_actual[$i] . "<br>");
      
      /*$fecha_original = strtotime($fe_clase[$i]);
      $fecha_modificada = strtotime($fe_clase_actual[$i]);*/

      $fgActualizarRegistroClase = false; 
      
      
      // Verifica si tiene o no creada una live session, si le falta, se la crea.
      // Borra la clase anterior y agrega la nueva sobre la licencia que le corresponde por la nueva fecha y hora
      $QueryDel  = "select fl_live_session ";
      $QueryDel .= "from k_live_session where fl_clase = $clave_clase";   
      $rowDel = RecuperaValor($QueryDel);
      $fl_live_session_actual = $rowDel[0];
      
      logger("Para verificar si tiene o no live session: $QueryDel");
      /* ($fecha_original != $fecha_modificada) OR */
      if ( empty($fl_live_session_actual) ) { 
        $fgActualizarRegistroClase = true;
      }
      
      logger("fgActualizarRegistroClase: $fgActualizarRegistroClase");
      if ($fgActualizarRegistroClase) 
        logger("Es necesario actualizar la clase");
      else
        logger("NO es necesario actualizar la clase");
      
      // En caso de cambio de fecha/hora, eliminamos el registro para liberar la licencia actual y generar un nuevo registro    
      if ($fgActualizarRegistroClase) {
        // Borra la clase anterior y agrega la nueva sobre la licencia que le corresponde por la nueva fecha y hora
        $QueryDel  = "select fl_live_session, cl_licencia, cl_meeting_id ";
        $QueryDel .= "from k_live_session where fl_clase = $clave_clase";   
        $rowDel = RecuperaValor($QueryDel);

        logger("Se debe borrar la live session?: $QueryDel");
        
        $fl_live_session_actual = $rowDel[0];
        $cl_licencia_actual = $rowDel[1];
        $cl_meeting_id_actual = $rowDel[2];

        $LicenciaActual = $licenciaService->getLicenciaByClave($cl_licencia_actual);
        if (!empty($cl_meeting_id_actual)) {
          $Query = "DELETE FROM k_live_session WHERE fl_live_session = $fl_live_session_actual";
          EjecutaQuery($Query);	  
          
          logger("Se debe borrar la live session [DELETE]: $Query");
          
          delLiveSession($cl_meeting_id_actual, $LicenciaActual);      
        }
      }
             
      
      // MDB
      // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
      // entonces puede agregar la clase, de lo contrario regresa el error a la forma.     
      
      $licenciaService = new LicenciaAdobeService();
      $clasesService = new ClasesService();      
      
      $fechaHora = $fe_clase[$i];
      
      $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, $clave_clase);
      $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, $clave_clase);      
      $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas, true);
      
      //////////////////////////////////////////////////////////////
      // IMPORTANTE:
      // Las licencias a contar son las que ya estan siendo usadas por clases traslapadas mas las que se necesitan para las clases nuevas
      //
      // Las licencias que se deben usar para las clases nuevas son las que se obtuvieron en $licenciasAdobe, esta excluye las licencias en uso 
      // de ese mismo grupo de clases traslapadas.
      //////////////////////////////////////////////////////////////
      $numLicenciasAdobe = sizeof($licenciasAdobe) + sizeof($clavesLicenciasTraslapadas);
      
      logger("Clases traslapadas: $clasesTraslapadas");
      logger("Licencias traslapadas: ");
      logger($clavesLicenciasTraslapadas, true);
      
      logger("Licencias disponibles: ");
      logger($licenciasAdobe, true);      
         
      logger("Licencias disponibles: " . $numLicenciasAdobe);
      logger("Licencias requeridas: " . $clasesTraslapadas);
      logger("Licencias requeridas DEBE SER MAYOR O IGUAL QUE licencias disponibles");
      
      if ( $licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe) )
        logger("Licencias disponibles " . $licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe));
      else
        logger("Sin licencias disponibles " . $licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe));
      
      $Query = "";
      if ($licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe)) {    

        if(empty($fl_clase[$i])) {
          $Query  = "INSERT INTO k_clase (fl_grupo, fl_semana, fe_clase) ";
          $Query .= "VALUES($clave_clase, $fl_semana[$i], $fe_clase[$i])";                    
          EjecutaQuery($Query);
          logger("INSERTANDO LA CLASE: $Query");
        }
        else {
      
          // Modifica el registro solo si tuvo cambios
          
          if ($fgActualizarRegistroClase) {          
            // Borra la clase anterior y agrega la nueva sobre la licencia que le corresponde por la nueva fecha y hora
           /* $QueryDel  = "select fl_live_session, cl_licencia, cl_meeting_id ";
            $QueryDel .= "from k_live_session where fl_clase = $clave_clase";   
            $rowDel = RecuperaValor($QueryDel);

            $fl_live_session_actual = $rowDel[0];
            $cl_licencia_actual = $rowDel[1];
            $cl_meeting_id_actual = $rowDel[2];*/
            
           // $LicenciaActual = $licenciaService->getLicenciaByClave($cl_licencia_actual);
            /* Ya no se borra la licencia 
            if (!empty($cl_meeting_id_actual))
              delLiveSession($cl_meeting_id_actual, $LicenciaActual);*/ 

            $Query  = "UPDATE k_clase SET fe_clase=$fe_clase[$i] ";
            $Query .= "WHERE fl_clase=$clave_clase";			
            EjecutaQuery($Query);
            
            logger("ACTUALIZANDO LA CLASE: $Query");
       
          }
          
        }
        
        logger("Query para insert/update de la clase: $Query <br>");
        
        // Creacion sesion campus y AdobeConnect
        if (sizeof($licenciasAdobe)> 0) {
          $licenciaAC = $licenciasAdobe[0]; // Usa una nueva licencia, toma la primera del arreglo
          iuLiveSession($clave_clase, $licenciaAC);  
        }
        
        if (!empty($licenciaAC))
          logger("La licencia a usar: " . $licenciaAC->getClLicencia());
        
        
              
      }
      else {
        //$fe_clase_err[$i] = ObtenMensaje(230);
      }
      logger("======== FIN PROCESO CLASE =================");
    }
    
  }

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));  
?>