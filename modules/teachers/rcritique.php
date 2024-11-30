<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_alumno = RecibeParametroNumerico('student', True);
  $no_semana = RecibeParametroNumerico('week', True);
  
  # Revisa que se haya recibido un alumno
  if(empty($fl_alumno)) {
    header("Location: blog.php");
    exit;
  }
  
  # Revisa que se haya recibido un alumno
  if(empty($no_semana))
    $no_semana = ObtenSemanaActualAlumno($fl_alumno);
  
  # Inicializa variables
  $nombre = ObtenNombreUsuario($fl_alumno);
  $titulo = "$nombre's Critique";
  
  # Recupera los datos de la entrega de la semana
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);
  $Query  = "SELECT fl_entrega_semanal ";
  $Query .= "FROM k_entrega_semanal ";
  $Query .= "WHERE fl_alumno=$fl_alumno ";
  $Query .= "AND fl_grupo=$fl_grupo ";
  $Query .= "AND fl_semana=$fl_semana";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal = $row[0];
  
  # Inicia pagina
  PresentaHeader($titulo, $fl_alumno);
  
  # Muestra pizarron al grabar la critica
  PresentaPizarron( );
  
  # Inicia area de trabajo
  echo "
              <tr>
                <td colspan='2' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>";
  
  # Dialogo para webcam
  echo "
  <script type='text/javascript' src='".PATH_LIB."/js_webcam/flash_resize.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_webcam/swfobject.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/frmRCritique.js.php'></script>
  <input type='hidden' id='fl_entrega_semanal' value='$fl_entrega_semanal' />
  <input type='hidden' id='nb_archivo' value='' />
  <input type='hidden' id='fg_video' value='0' />
  <div id='dlg_camara'>";
  PresentaWebcam($fl_entrega_semanal);
  echo "</div>
  <div id='div_critique'></div>
  <script type='text/javascript'>
    CambiaTab($fl_alumno,$no_semana,'assignment', 1);
  </script>";
  
  # Dialogo para asignar calificacion
  echo "
  <script type='text/javascript' src='".PATH_COM_JS."/frmAssignGrade.js.php'></script>
  <div id='dlg_grade'><div id='dlg_grade_content'></div></div>";
  
  # Boton para regresar y para abrir dialogo de asignar calificacion
  echo "
                    <tr>
                      <td colspan='3' align='center'>
                        <button type='button' id='buttons' OnClick=\"javascript:AssignGrade($fl_entrega_semanal);\"'>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Assign grade&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </button>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type='button' id='buttons' OnClick='javascript:history.go(-1);'>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </button>
                      </td>
                    </tr>
                    <tr><td colspan='3' height='20'></td></tr>";
  
  // MDB 29/JUL/2011
  // Creacion del archivo para llamar al applet de grabacion de critica
  // TODO Usar un directorio temporal, borrar los archivos, revisar que no se generen archivos nuevos cuando se cambia de semana, etc.
  $nombreArchivoJNLP = ObtenNombreArchivoJNLP($fl_entrega_semanal);
  $pathArchivosJNLP = $_SERVER[DOCUMENT_ROOT] . SP_HOME . DIRECTORIO_JAR;
  $fileJNLP = fopen($pathArchivosJNLP . "/" . $nombreArchivoJNLP, 'w') or die("can't open file");
  
  // Contenido del archivo, configuracion para abrir el applet
  $contenido = "";
  $contenido .= "<?xml version='1.0' encoding='utf-8'?>\n";
  $contenido .= "<jnlp spec='1.0+' codebase='http://" . REMOTE_SERVER_NAME . SP_HOME . DIRECTORIO_JAR . "' href='" . $nombreArchivoJNLP . "'>\n";
  $contenido .= "	<information> \n";
  $contenido .= "		<title>Vanas Record Critique</title>\n";
  $contenido .= "		<vendor>Vanas</vendor>\n";
  $contenido .= "		<homepage>http://www.vanas.ca/</homepage>\n";
  $contenido .= "		<description>Vanas Record Critique</description>\n";
  $contenido .= "		<description kind='short'>Vanas Record Critique</description>\n";
  $contenido .= "		<offline-allowed/>\n";
  $contenido .= "	</information>\n";
  $contenido .= "	<security>\n";
  $contenido .= "	    <all-permissions/>\n";
  $contenido .= "	</security>\n";
  $contenido .= "	<resources>\n";
  $contenido .= "	<j2se version='1.6+'/>\n";
  $contenido .= "        <jar href='screenshare.jar'/>\n";
  $contenido .= "    </resources>\n";
  $contenido .= "    <application-desc main-class='org.redfire.screen.ScreenShare'>\n";
  $contenido .= "       <argument>" . $fl_entrega_semanal . "</argument>";
  $contenido .= "    </application-desc>\n";
  $contenido .= "</jnlp>\n";
  fwrite($fileJNLP, $contenido);
  fclose($fileJNLP);
  
  # Cierra pagina
  PresentaFooter( );
  
?>
