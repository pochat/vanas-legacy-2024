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
  require '../../lib/zoom_config.php';

  # Variable initialization to avoid errors
  $fl_term_err=NULL;
  $fl_maestro_err=NULL;
  $nb_grupo_err=NULL;
  $fe_clase_err=NULL;
  $moderatorPW=NULL;
  $attendeePW=NULL;
  $welcome=NULL;


  function delLiveSession($clMeetingId, LicenciaAdobe $licenciaAC) {

    $clLicencia = $licenciaAC->getClLicencia();

    $clienteAdobe = new AdobeConnectClient($licenciaAC);

    $clienteAdobe->deleteMeeting($clMeetingId);

  }

  function iuLiveSession($p_fl_clase, LicenciaAdobe $licenciaAC) {

    # Variable initialiation to avoid errors
    $moderatorPW=NULL;
    $attendeePW=NULL;
    $welcome=NULL;

    $clLicencia = $licenciaAC->getClLicencia();

    logger("clLicencia: $clLicencia <br>");

	  // MDB ADOBECONNECT Datos para los parametros Integracion Adobe Connect
	  // Se crean las sesiones de Adobe Connect desde este programa
	  // Para BBB no es necesario crearlas, se hace al momento en que un teacher
	  // o un student entra a la sesion desde el campus.
    $Query  = "SELECT ds_titulo, DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') fe_ini, ";
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

    $fl_live_session = !empty($row[0])?$row[0]:NULL;
    $cl_meeting_id = !empty($row[1])?$row[1]:NULL;

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
        $clienteAdobe->addHostMeeting($meeting_id, 'public-access');

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
  $fg_grupo_global=RecibeParametroNumerico('fg_grupo_global');
  $fg_zoom=RecibeParametroBinario('optionsRadio2');
  $fg_zoom=1;//sep-2020  all zoom.
  # Obtenemos los programas que se seleccionaron
  $terms = isset($_REQUEST['fl_term2'])?$_REQUEST['fl_term2']:NULL;

  if( (!empty($clave))&&  (!empty($terms)) )
  $fg_grupo_global=1;


  # Recupera los alumnos del grupo.
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
  if(empty($fg_grupo_global)){

      if(empty($fl_term))
          $fl_term_err = ERR_REQUERIDO;

		if(empty($fl_maestro))
		$fl_maestro_err = ERR_REQUERIDO;

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

  }
  if(empty($nb_grupo))
    $nb_grupo_err = ERR_REQUERIDO;





      // MDB
      // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect,
      // entonces puede agregar la clase, de lo contrario regresa el error a la forma.

      $licenciaService = new LicenciaAdobeService();
      $clasesService = new ClasesService();

      $fechaHora = "'" . ValidaFecha(!empty($fe_clase[$i])?$fe_clase[$i]:NULL).' '.ValidaHoraMin(!empty($hr_clase[$i])?$hr_clase[$i]:NULL) . "'";

      $fl_clase_actual = !empty($fe_clase[$i])?$fe_clase[$i]:NULL;

      $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, $fl_clase_actual);
      $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, $fl_clase_actual);
      $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);

      if (!$licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {
        $fe_clase_err[$i] = ObtenMensaje(230);
      }
      if($fg_zoom==1){#Solo si viene checked de zoom.
          #Zoom
          $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, $fl_clase_actual);
          $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, $fl_clase_actual);
          $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);

          if (!$licenciaService->licenciasSuficientesZoom($clasesTraslapadasZoom, sizeof($licenciasZoom))) {
              $fe_clase_err[$i] = ObtenMensaje(230);
          }

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


	        $Query  = "INSERT INTO c_grupo (fl_term, fl_maestro, nb_grupo,fg_grupo_global,fg_zoom) ";
			$Query .= "VALUES($fl_term, $fl_maestro, '$nb_grupo','$fg_grupo_global','$fg_zoom')";
			$fl_grupo = EjecutaInsert($Query);


			#Cuando se agrega un fgrupo global.
			  if($fg_grupo_global==1){


					foreach ($terms as $fl_term){

							$fl_term = $fl_term;

									$Query  = "INSERT INTO k_grupo_term(fl_term, fl_grupo) ";
									$Query .= "VALUES($fl_term, $fl_grupo)";
									$fl_grupo_terms = EjecutaInsert($Query);
					}

					EjecutaQuery("UPDATE c_grupo SET fl_term=0,fl_maestro=0 WHERE fl_grupo=$fl_grupo ");

			  }

  }
  else {


	        //en laravel quitar el fg_zoom, ya que todo sera por zoom.
			$Query  = "UPDATE c_grupo SET fl_maestro=$fl_maestro,fg_zoom='$fg_zoom', nb_grupo='$nb_grupo',fg_grupo_global='$fg_grupo_global' ";
			$Query .= "WHERE fl_grupo=$clave";
			EjecutaQuery($Query);


			#Para actualizar el grupo.
			if($fg_grupo_global==1){

					EjecutaQuery("DELETE FROM k_grupo_term WHERE fl_grupo=$clave  ");

					foreach ($terms as $fl_term){

							$fl_term = $fl_term;

							$Query  = "INSERT INTO k_grupo_term(fl_term, fl_grupo) ";
							$Query .= "VALUES($fl_term, $clave)";
							$fl_grupo_terms = EjecutaInsert($Query);
					}



			}





  }




  # Reinicializa los alumnos del grupo
  if(!empty($clave)) {

	        if($fg_grupo_global==1){

                    #Insertamos los alumnos al grupo.
				    EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_grupo=$clave AND fg_grupo_global='1' ");
                    EjecutaQuery("DELETE FROM k_alumno_grupo_global WHERE fl_grupo=$clave");
                    for($i = 0; $i < $tot_alumnos; $i++) {
                        if(!empty($fl_alumno[$i])) {

                            # Asigna al alumno al nuevo grupo
                            EjecutaQuery("INSERT INTO k_alumno_grupo(fl_grupo, fl_alumno,fg_grupo_global) VALUES($clave, ".$fl_alumno[$i].",'1')");
                            # Asigna al alumno al nuevo grupo
                            EjecutaQuery("INSERT INTO k_alumno_grupo_global(fl_grupo, fl_alumno) VALUES($clave, ".$fl_alumno[$i].")");

                        }

                    }




			}else{

		            #Elimina y vuelve a insertar.
					EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_grupo=$clave AND fg_grupo_global<>'1' ");
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
						EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_alumno=".$fl_alumno[$i]." AND fg_grupo_global<>'1' ");

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


  }

  # Inserta o actualiza las fechas de clases virtuales
  if(empty($clave)) {


		if($fg_grupo_global==1){





		}else{





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

			  $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, $clave);
			  $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, $clave);
			  $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);

			  #Para Zoom
              $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, $clave);
			  $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, $clave);
			  $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);

              #2020 se comenta esto ahora se valida con zoom
              #if ($licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {
			  if($licenciaService->licenciasSuficientesZoom($clasesTraslapadasZoom, sizeof($licenciasZoom))) {

				$Query  = "INSERT INTO k_clase (fl_grupo, fl_semana, fe_clase) ";
				$Query .= "VALUES($fl_grupo, $fl_semana, '$fe_clase_d $hr_clase_d')";
				$fl_clase_insertada = EjecutaInsert($Query);

				// Creacion sesion campus y AdobeConnect
				$licenciaAC = $licenciasAdobe[0];
				iuLiveSession($fl_clase_insertada, $licenciaAC);

                #Generamos la clase de Zoom.
                # Recuperamos la clase ya que el ui inserta las live sesions
                $Query  = "SELECT fl_live_session,zoom_url ";
                $Query .= "FROM k_live_session ";
                $Query .= "WHERE fl_clase=".$fl_clase_insertada;

                $row = RecuperaValor($Query);
                $fl_live_session = $row[0];
                $zoom_url=$row[1];

                #Verifica la fecha actual y crea las futuras clases en zoom(al recuperar registros.)
                if(empty($zoom_url)){

                    #Verifica las fechas futuras a el dia actual y las crea.
                    #Obtenemos fecha actual :
                    $Query = "Select CURDATE() ";
                    $row = RecuperaValor($Query);
                    $fe_actual = str_texto($row[0]);
                    $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                    $fe_actual= date('Y-m-d',$fe_actual);

                    #Damos formato a la clase para zoom.
                    $fe_clase_zoom=strtotime('+0 day',strtotime($fe_clase_d));
                    $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                    $fe_clase_actual=$fe_clase_zoom;
                    $fe_clase_zoom=$fe_clase_zoom."T".$hr_clase_d.":00";
                    $pass_clase_zoom=rand(99999,5)."i".$fl_live_session;


                    $Query="SELECT CONCAT(nb_programa,' (',c.ds_duracion,') -Level Term ',no_grado) FROM k_term a
                            JOIN c_periodo b ON a.fl_periodo=b.fl_periodo
                            JOIN c_programa c ON c.fl_programa=a.fl_programa
                            WHERE fl_term=$fl_term ";
                    $row=RecuperaValor($Query);
                    $ds_titulo=$row[0]." ".$nb_grupo;

                    $Query="SELECT ds_titulo,b.no_semana FROM k_semana a
                            JOIN c_leccion b ON a.fl_leccion=b.fl_leccion
                            WHERE a.fl_semana=$fl_semana ";
                    $row=RecuperaValor($Query);
                    $ds_lecc=$row[0];
                    $no_semana=$row[1];
                    $ds_titulo="Week $no_semana: ".$ds_titulo." - ".$ds_lecc;

                    //if($fe_clase_actual>=$fe_actual){

                        $licenciaAZ = $licenciasZoom[0];
                        if((!empty($fl_live_session))&&(!empty($licenciaAZ))){
                            #Creamos la clase en zoom
                            create_meetingZoom($fl_live_session,'60',$ds_titulo,$fe_clase_zoom,$pass_clase_zoom,'k_live_session',$licenciaAZ);
                        }

                   // }


                }




			  }
			  else {
				// TODO Mensaje de error
			  }





			}


		}


  }
  else {


		if($fg_grupo_global==1){


			#Aqui cuando realiza el update vefirica que tengan los libks de zoom y si no lo egenrea siempre y cuando este selcecionado el check zoom.
            #Recupera las clases.
            $Query="SELECT no_semana,b.nb_clase,b.fl_maestro, ".ConsultaFechaBD('b.fe_clase', FMT_CAPTURA)." fe_clase,".ConsultaFechaBD('b.fe_clase', FMT_HORAMIN)." hr_clase,b.fg_obligatorio,a.fl_semana_grupo,b.fl_clase_grupo,b.ds_dia_clase
												  FROM k_semana_grupo a
												  JOIN k_clase_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo
                                WHERE a.fl_grupo=$clave
                                ORDER BY no_semana ASC

												  ";
            $rs3 = EjecutaQuery($Query);
            for($im=0;$row3=RecuperaRegistro($rs3);$im++){
                $contador_reg++;
                $fl_grupo=$clave;
                $fl_semana_grupo=$row3['fl_semana_grupo'];
                $fl_clase_grupo=$row3['fl_clase_grupo'];
                $no_semana=$row3['no_semana'];
                $nb_clase=$row3['nb_clase'];
                $fl_maestro=$row3['fl_maestro'];
                $fe_clase=$row3['fe_clase'];
                $hr_clase=$row3['hr_clase'];
                $fg_mandatory=$row3['fg_obligatorio'];
                $fg_dia_sesion=$row3['ds_dia_clase'];

                # Revisa si hay una clase global activa en este momento
                $Query  = "SELECT fl_live_session_grupal, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
                $Query .= "FROM k_live_session_grupal ";
                $Query .= "WHERE fl_clase_grupo=".$fl_clase_grupo;
                $row = RecuperaValor($Query);
                $fl_live_session = $row[0];
                $cl_estatus = $row[1];
                $ds_meeting_id = $row[2];
                $ds_password_asistente = $row[3];
                $zoom_url=$row[4];
                $zoom_id=$row[5];

                #Recuperamos el host del email.
                #Recuperamos la cuenta
                $Query="SELECT host_email_zoom FROM zoom WHERE id=$zoom_id ";
                $row=RecuperaValor($Query);
                $ds_host_zoom=$row[0];
                $fe_clase_err="";

                $licenciaService = new LicenciaAdobeService();
                $clasesService = new ClasesService();

                $fechaHora = "'" . ValidaFecha($fe_clase) . ' ' . ValidaHoraMin($hr_clase) . "'";

                $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, 0);
                $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, 0);
                $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);

                if (!$licenciaService->licenciasSuficientes($clasesTraslapadas, sizeof($licenciasAdobe))) {
                    $fe_clase_err = ObtenMensaje(230)."<br/>";

                }

                #Para Zoom.
                $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, 0);
                $clavesLicenciasTraslapadasZoom=$clasesService->getClavesLicenciasTraslapadasZoom($fechaHora,0);
                $licenciasZoom=$licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);
                $licenciasZoomDisponibles=$licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);

                if ($clasesTraslapadasZoom > sizeof($licenciasZoom)) {
                    $rsClasesTraslapadas = $clasesService->getClasesTraslapadasZoom($fechaHora,0);

                    $arrClavesTraslapadas = array();
                    for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
                        $arrClavesTraslapadas[$ix] = $rowx[0];
                    }

                }
                $existe=null;
                if(!empty($arrClavesTraslapadas)){
                    $existe = array_search($fl_clase_grupo,$arrClavesTraslapadas,false);
                }
                if(empty($existe)){


                    if(empty($fl_live_session)){
                        $Query ="INSERT INTO k_live_session_grupal (fl_clase_grupo,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente,cl_licencia)";
                        $Query.="VALUES($fl_clase_grupo,1,'','','','1')";
                        $fl_live_session=EjecutaInsert($Query);
                    }


                    //if($fl_clase_grupo==756){
                     //   $licenciasZoomDisponibles = array();
                     //   $clLicencia = 2;
                     //   array_push($licenciasZoomDisponibles, $clLicencia);
                   // }



                    if((!empty($fl_live_session))&&(empty($zoom_url))){

                        # Creacion sesion zoom.
                        if (sizeof($licenciasZoomDisponibles)> 0) {

                            #Damos formato a la clase para isertarla en zoom
                            $fe_clase_zoom=strtotime('+0 day',strtotime($fe_clase));
                            $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                            $fe_clase_zoom=$fe_clase_zoom."T".$hr_clase.":00";
                            $pass_clase_zoom=rand(99999,5)."i".$fl_live_session;

                            $licenciaAZ = $licenciasZoomDisponibles[0]; // Usa una nueva licencia, toma la primera del arreglo
                            if($fg_zoom==1){
                                create_meetingZoom($fl_live_session,'50',$nb_clase,$fe_clase_zoom,$pass_clase_zoom,'k_live_session_grupal',$licenciaAZ);
                            }

                         }


                    }

                }





            }










		}else{





				logger("Total de semanas: $tot_semanas");

				for($i = 0; $i < $tot_semanas; $i++) {
                  $fe_clase_v=$fe_clase[$i];
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
					$QueryDel  = "select fl_live_session, cl_licencia, cl_meeting_id,zoom_id ";
					$QueryDel .= "from k_live_session where fl_clase = $clave_clase";
					$rowDel = RecuperaValor($QueryDel);

					logger("Se debe borrar la live session?: $QueryDel");

					$fl_live_session_actual = $rowDel[0];
					$cl_licencia_actual = $rowDel[1];
					$cl_meeting_id_actual = $rowDel[2];
                    $zoom_id=$rowDel[3];

					$LicenciaActual = $licenciaService->getLicenciaByClave($cl_licencia_actual);


                    #Elimina la clase de zoom
                    if( (!empty($fl_live_session_actual))&& (!empty($zoom_id)) ){
                        DeletedMeetingZoom($fl_live_session_actual,'k_live_session',$zoom_id);
                    }
					if(!empty($cl_meeting_id_actual)) {



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

                  #Zoom
                  $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, $clave_clase);
                  $clavesLicenciasTraslapadasZoom=getClavesLicenciasTraslapadasZoom($fechaHora, $clave_clase);
                  $licenciasZoom=$licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);



				  //////////////////////////////////////////////////////////////
				  // IMPORTANTE:
				  // Las licencias a contar son las que ya estan siendo usadas por clases traslapadas mas las que se necesitan para las clases nuevas
				  //
				  // Las licencias que se deben usar para las clases nuevas son las que se obtuvieron en $licenciasAdobe, esta excluye las licencias en uso
				  // de ese mismo grupo de clases traslapadas.
				  //////////////////////////////////////////////////////////////
				  $numLicenciasAdobe = sizeof($licenciasAdobe) + sizeof($clavesLicenciasTraslapadas);
				  $numLicenciasZoom = sizeof($licenciasZoom) + sizeof($clavesLicenciasTraslapadasZoom);
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

                  if($fg_zoom==1){

                  }else{
                      $clasesTraslapadasZoom=$clasesTraslapadas;
                      $licenciasZoom=$licenciasAdobe;
                  }
                  #Se cambia por la de zoom
                  //if($licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe)) {
                  if($licenciaService->licenciasSuficientesZoom($clasesTraslapadasZoom, $licenciasZoom)) {

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

                    #Creacion de clase zoom.
                    if(sizeof($licenciasZoom)> 0){

                        # Revisa si hay una clase activa en este momento
                        $Query  = "SELECT fl_live_session, cl_estatus,zoom_url ";
                        $Query .= "FROM k_live_session ";
                        $Query .= "WHERE fl_clase=".$clave_clase;
                        $row = RecuperaValor($Query);
                        $fl_live_session = $row[0];
                        $cl_estatus = $row[1];
                        $zoom_url=$row[2];

                        if(empty($fl_live_session)){
                            $Query ="INSERT INTO k_live_session(fl_clase,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente,cl_meeting_id,cl_licencia) ";
                            $Query.="VALUES($clave_clase,1,'','','','',0)";
                            $fl_live_session=EjecutaInsert($Query);
                        }

                        if(empty($zoom_url)){

                            #vERIFICAMOS SI EXISTE EL ZOOM URL y si no lo crea. para live_sesion.
                            #Obtenemos fecha actual :
                            $Query = "Select CURDATE() ";
                            $row = RecuperaValor($Query);
                            $fe_actual = str_texto($row[0]);
                            $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                            $fe_actual= date('Y-m-d',$fe_actual);

                            #Damos formato a la clase para zoom.
                            $fe_clase_zoom=strtotime('+0 day',strtotime($fe_clase_v));
                            $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                            $fe_clase_actual=$fe_clase_zoom;
                            $fe_clase_zoom=$fe_clase_zoom."T".$hr_clase[$i].":00";
                            $pass_clase_zoom=rand(99999,5)."i".$fl_live_session;

                            $Query="SELECT CONCAT(nb_programa,' (',c.ds_duracion,'-',b.nb_periodo, ') -Level Term ',no_grado) FROM k_term a
                            JOIN c_periodo b ON a.fl_periodo=b.fl_periodo
                            JOIN c_programa c ON c.fl_programa=a.fl_programa
                            WHERE fl_term=$fl_term ";
                            $row=RecuperaValor($Query);
                            $ds_titulo=$row[0]." ".$nb_grupo;

                            $Query="SELECT ds_titulo FROM k_semana a
                            JOIN c_leccion b ON a.fl_leccion=b.fl_leccion
                            WHERE a.fl_semana=".$fl_semana[$i]." ";
                            $row=RecuperaValor($Query);
                            $ds_lecc=$row[0];
                            $ds_titulo=$ds_titulo." - ".$ds_lecc;

                            //if($fe_clase_actual>=$fe_actual){
                                #Creamos la clase en zoom

                                $licenciaAZ = $licenciasZoom[0]; // Usa una nueva licencia, toma la primera del arreglo
                                if($fg_zoom==1){ #solo si viene seleccionado que se creara la de zoom.
                                    create_meetingZoom($fl_live_session,'60',$ds_titulo,$fe_clase_zoom,$pass_clase_zoom,'k_live_session',$licenciaAZ);
                                }
                                logger("La licencia a usar para zoom: ");
                            //}


                        }







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



  }

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
?>