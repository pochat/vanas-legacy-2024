<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
    
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_TAKE, $permiso) OR $permiso == PERMISO_ALTA) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  $Query  = "SELECT ds_login, fg_activo, ".ConsultaFechaBD('fe_alta', FMT_FECHA)." fe_alta, ";
  $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
  $Query .= "(".ConcatenaBD($concat).") 'fe_ultacc', ";
  $Query .= "no_accesos, ds_nombres, ds_apaterno, ds_amaterno, ds_email, a.fl_perfil, b.nb_perfil, fg_genero, ";
  $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, cl_sesion, ds_notas ";
  $Query .= "FROM c_usuario a, c_perfil b, c_alumno c ";
  $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
  $Query .= "AND a.fl_usuario=c.fl_alumno ";
  $Query .= "AND fl_usuario=$clave";
  $row = RecuperaValor($Query);
  $ds_login = str_texto($row[0]);
  $fg_activo = $row[1];
  $cl_sesion = $row[13];
  $ds_notas =  str_texto($row[14]);

  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT a.fl_programa, nb_programa, nb_periodo, a.fl_periodo "; 
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND cl_sesion = '$cl_sesion' ";
  $row = RecuperaValor($Query);
  $fl_programa = $row[0];
  $nb_programa = $row[1];	
  $nb_periodo = $row[2];
  $fl_periodo = $row[3];
 

  # Recupera datos de Official Transcript
  $Query = "SELECT ";
  $Query .= ConsultaFechaBD('fe_carta', FMT_FECHA)." fe_carta, ";
  $Query .= ConsultaFechaBD('fe_contrato', FMT_FECHA)." fe_contrato, ";
  $Query .= ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin, ";
  $Query .= ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado, ";
  $Query .= ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision, ";
  $Query .= "fg_certificado, fg_honores, ";
  $Query .= ConsultaFechaBD('fe_graduacion', FMT_FECHA)." fe_graduacion, ";
	$Query .= "fg_desercion, fg_dismissed, fg_job, fg_graduacion ";
  $Query .= "FROM k_pctia ";
  $Query .= "WHERE fl_alumno = $clave ";
  $Query .= "AND fl_programa = $fl_programa ";
  $row = RecuperaValor($Query);
  $fe_completado = $row[3];
  $fg_certificado = $row[5];
  $fg_honores = $row[6];
  $fe_graduacion = $row[7];
	$fg_desercion = $row[8];
  $fg_dismissed = $row[9];
  $fg_job = $row[10];
  $fg_graduacion = $row[11];
  

  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_TAKE);
  
  #Liga para generar reporte oficial para PCTIA
  if(!empty($fe_completado))
    $ds_diploma = "&nbsp;&nbsp;&nbsp;<a href='../reports/diploma_rpt.php?clave=$clave' target='blank'>".ObtenEtiqueta(535)."</a>";
  else
    $ds_diploma = "&nbsp;";

  # Forma para captura de datos
  Forma_Inicia($clave);

  # Informacion de programa periodo
  Forma_CampoInfo(ETQ_USUARIO, $ds_login.$ds_diploma);
  Forma_CampoInfo(ObtenEtiqueta(360),$nb_programa);
  Forma_CampoInfo(ObtenEtiqueta(342), $nb_periodo);
  Forma_Espacio();
  
  # Student status
  Forma_Seccion('Student Status');
  echo "
  <tr>
    <td colspan='2' class='css_etq_texto' aling='center' style='text-align:center;'>".
    str_uso_normal(ObtenMensaje(227))
    ."</td>
  </tr>";
  Forma_CampoCheckbox(ObtenEtiqueta(113), 'fg_activo', $fg_activo);
  Forma_CampoCheckbox(ObtenEtiqueta(558), 'fg_desercion', $fg_desercion);
  Forma_CampoCheckbox(ObtenEtiqueta(559), 'fg_dismissed', $fg_dismissed);
  Forma_CampoCheckbox(ObtenEtiqueta(644), 'fg_job', $fg_job);
  Forma_CampoCheckbox(ObtenEtiqueta(645), 'fg_graduacion', $fg_graduacion);
  Forma_CampoCheckbox(ObtenEtiqueta(547), 'fg_certificado', $fg_certificado);
  Forma_CampoCheckbox(ObtenEtiqueta(548), 'fg_honores', $fg_honores);
  Forma_CampoTextArea(ObtenEtiqueta(196), False, 'ds_notas', $ds_notas, 80, 3);
  Forma_Espacio( );

  # Datos del programa
  Forma_CampoOculto('fl_programa' , $fl_programa);
  Forma_CampoOculto('fl_periodo' , $fl_periodo);
  Forma_Espacio( );

  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_TAKE, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>