<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  $nuevo = RecibeParametroNumerico('nuevo');
  $cancelar=RecibeParametroNumerico('cancel');
  
  if($cancelar)
    $inicializa=1; //echo "entra";
  
  if ($actual==6)   # Si se realiza busqueda avanzada
    $Query = Query_Completo($nuevo);
  else
    $Query = Query_Principal($criterio, $actual);

  # Muestra pagina de listado
  $campos = array(ETQ_USUARIO, ETQ_NOMBRE, ObtenEtiqueta(360), ObtenEtiqueta(111), ObtenEtiqueta(112));
	
  $html_arriba = "";

  # Data graphs
  require 'students_graphs.php';
  
  
  # Advance search
  $html_arriba .= "
  <div style='position: relative; float: left; width: 100%;'>
    <br>
    <div id='div_principal' style='display: none; padding: 10px; background-color: #E6E1DE; width: 700px; margin: 0 auto;'></div>
    <div style='position: absolute; top: 10px; right: 0;'>
      <a href='javascript:muestraBusquedaAvanzada($inicializa);'>Advanced Search <img src='".PATH_IMAGES."/advanced_search.png' border='none'/></a>
    </div>
    <br>	
  </div>
	<script type='text/javascript'>
		
		function muestraBusquedaAvanzada(inicializa) {
      
      $('#div_principal').css('display', 'block');
      $.ajax({
        type: 'POST',
        url : 'div_dialogo_busqueda_std.php',
        async: false,
        data: 'inicializa='+inicializa,
        success: function(html) {
            $('#div_principal').html(html);
        }
      });
		}
    
  </script>";
  
  if($actual==6) {
    $html_abajo = "
    <script type='text/javascript'>
      muestraBusquedaAvanzada(1);
    </script>";
  }
	
  PresentaPaginaListado(FUNC_ALUMNOS, $Query, TB_LN_NUD, True, True, $campos, '../reports/students_rpt.php', $html_arriba, $html_abajo, 'payments_frm.php','','',True, True);

  function Query_Principal($p_criterio, $p_actual) {
    # Consulta para el listado
    $Query  = "SELECT fl_usuario, ds_login '".ETQ_USUARIO."', ";
    //$concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
    //jgfl 18/11/14 cambio del ds_amaterno primero que el ds_apaterno
    $concat = array('ds_nombres', "' '", NulosBD('ds_amaterno'), "' '",'ds_apaterno');
    $Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."', ";
    $Query .= "CONCAT(nb_programa,' (',ds_duracion,')') '".ObtenEtiqueta(360)."', ";
    $Query .= ConsultaFechaBD('fe_alta', FMT_FECHA)." '".ObtenEtiqueta(111)."', ";
    $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
    $Query .= "(".ConcatenaBD($concat).") '".ObtenEtiqueta(112)."', ";
    $Query .= "CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(113)."|center', ";
    $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(341)."|center' ";
    $Query .= "FROM c_usuario a, c_perfil b, c_sesion c, k_ses_app_frm_1 d, c_programa e ";
    $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
    $Query .= "AND a.cl_sesion=c.cl_sesion ";
    $Query .= "AND c.cl_sesion=d.cl_sesion ";
    $Query .= "AND d.fl_programa=e.fl_programa ";
    $Query .= "AND a.fl_perfil=".PFL_ESTUDIANTE." ";
    if(!empty($p_criterio)) {
      switch($p_actual) {
        case 1: $Query .= "AND ds_login LIKE '%$p_criterio%' "; break;
        case 2: $Query .= "AND (ds_nombres LIKE '%$p_criterio%' OR ds_apaterno LIKE '%$p_criterio%' OR ds_amaterno LIKE '%$p_criterio%') "; break;
        case 3: $Query .= "AND nb_programa LIKE '%$p_criterio%' "; break;
        case 4: $Query .= "AND ".ConsultaFechaBD('fe_alta', FMT_FECHA)." LIKE '%$p_criterio%' "; break;
        case 5: 
          $Query .= "AND (".ConsultaFechaBD('fe_ultacc', FMT_FECHA)." LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_HORA)." LIKE '%$p_criterio%') ";
          break;
        default:
          $Query .= "AND ( ";
          $Query .= "ds_login LIKE '%$p_criterio%' ";
          $Query .= "OR ds_nombres LIKE '%$p_criterio%' OR ds_apaterno LIKE '%$p_criterio%' OR ds_amaterno LIKE '%$p_criterio%' ";
          $Query .= "OR nb_programa LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_alta', FMT_FECHA)." LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_FECHA)." LIKE '%$p_criterio%' ";
          $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_HORA)." LIKE '%$p_criterio%') ";
      }
    }
    $Query .= "ORDER BY fe_alta DESC";
    return $Query;
  }
  
  function Query_Completo($p_criterio) {
    $Query  = "SELECT fl_usuario, ds_login '".ETQ_USUARIO."', ";
    //$concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
    //jgfl 18/11/14 cambio del ds_amaterno primero que el ds_apaterno
    $concat = array('ds_nombres', "' '", NulosBD('ds_amaterno'), "' '",'ds_apaterno');
    $Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."', ";
    $Query .= "nb_programa '".ObtenEtiqueta(360)."', ";
    $Query .= "fe_alta '".ObtenEtiqueta(111)."', ";
    $Query .= "fe_ultacc '".ObtenEtiqueta(112)."', ";
    $Query .= "CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(113)."|center', ";
    $Query .= "CASE WHEN fg_pago='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
    $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(341)."|center' ";
    $Query .= "FROM (";
    $Query .= "
    SELECT a.fl_usuario fl_usuario, a.cl_sesion, a.ds_login ds_login, a.ds_nombres ds_nombres, a.ds_apaterno ds_apaterno, 
    a.ds_amaterno ds_amaterno, a.fg_genero fg_genero, 
    fe_nacimiento,
    a.ds_email, 
    fg_activo, 
    fe_alta, 
    fe_ultacc, 
    (SELECT fe_ultmod FROM c_sesion se WHERE se.cl_sesion=a.cl_sesion) fe_ultmod,
    (SELECT CONCAT(nb_zona_horaria, ' ', 'GMT', ' (', no_gmt, ')') FROM c_zona_horaria zo WHERE zo.fl_zona_horaria=c.fl_zona_horaria) nb_zona_horaria,
    (SELECT fg_international FROM k_app_contrato app WHERE app.cl_sesion=a.cl_sesion ORDER BY no_contrato LIMIT 1) fg_international,
    (SELECT nb_periodo FROM c_periodo w WHERE w.fl_periodo=f.fl_periodo) nb_periodo,
    (SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 LIMIT 1) fe_start_date,
    (SELECT nb_programa FROM c_programa e WHERE e.fl_programa=f.fl_programa) nb_programa,
    (SELECT CONCAT(h.ds_nombres, ' ', h.ds_apaterno) FROM k_alumno_grupo k, c_grupo gpo, c_usuario h WHERE k.fl_grupo=gpo.fl_grupo AND gpo.fl_maestro=h.fl_usuario AND k.fl_alumno=a.fl_usuario) ds_profesor,
    (SELECT nb_grupo FROM c_grupo n, k_alumno_grupo o WHERE n.fl_grupo=o.fl_grupo AND o.fl_alumno=a.fl_usuario) nb_grupo,
    (SELECT no_grado FROM k_term b, k_alumno_grupo d, c_grupo m WHERE b.fl_term=m.fl_term AND d.fl_grupo=m.fl_grupo AND d.fl_alumno=a.fl_usuario) no_grado, 
    fe_carta, fe_contrato, fe_fin, 
	  fe_completado, fe_emision, 
		fg_certificado, 
		fg_honores, 
		fe_graduacion, 
		fg_desercion,
    fg_dismissed,
    fg_job,
    fg_graduacion, 
    ds_add_city, ds_add_state, 
    (SELECT fg_pago FROM c_sesion ses WHERE a.cl_sesion=ses.cl_sesion) fg_pago, 
    (SELECT p.ds_pais FROM c_pais p WHERE p.fl_pais=f.ds_add_country) ds_pais,
    YEAR(fe_nacimiento) ye_fe_nacimiento, YEAR(fe_alta) ye_fe_alta, YEAR(fe_ultacc) ye_fe_ultacc, YEAR(fe_ultmod) ye_fe_ultmod, 
    YEAR(fe_carta) ye_fe_carta, YEAR(fe_contrato) ye_fe_contrato, YEAR(fe_fin) ye_fe_fin, YEAR(fe_completado) ye_fe_completado, 
    YEAR(fe_emision) ye_fe_emision, YEAR(fe_graduacion) ye_fe_graduacion, 
    YEAR((SELECT fe_inicio FROM k_term te, c_periodo i, k_alumno_term al WHERE te.fl_periodo=i.fl_periodo AND te.fl_term=al.fl_term AND al.fl_alumno=a.fl_usuario AND no_grado=1 LIMIT 1)) ye_fe_start_date
    FROM (c_usuario a, c_alumno c, k_ses_app_frm_1 f) LEFT JOIN k_pctia j ON (a.fl_usuario=j.fl_alumno)
    WHERE a.fl_usuario=c.fl_alumno AND a.cl_sesion=f.cl_sesion AND fl_perfil=".PFL_ESTUDIANTE." "; 
    $Query .= ") AS principal ";
    $Query .= "WHERE fl_usuario NOT LIKE '0' ";
    $nuevo=$p_criterio;
    require 'filtros.inc.php';
    $Query .= "ORDER BY fe_alta DESC";
    return $Query;
  }
?>