<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PERIODOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!empty($clave)) { // Actualizacion, recupera de la base de datos
    $Query  = "SELECT a.fl_programa, nb_programa, a.fl_periodo, nb_periodo, no_grado, a.fl_term_ini, ".ConsultaFechaBD('fe_inicio', FMT_CAPTURA)." fe_inicio, ds_duracion ";
    $Query .= "FROM k_term a, c_programa b, c_periodo c ";
    $Query .= "WHERE a.fl_programa=b.fl_programa ";
    $Query .= "AND a.fl_periodo=c.fl_periodo ";
    $Query .= "AND fl_term=$clave";
    $row = RecuperaValor($Query);
    $fl_programa = $row[0];
    $nb_programa = str_texto($row[1]);
    $fl_periodo = $row[2];
    $nb_periodo = str_texto($row[3]);
    $no_grado = $row[4];
    $fl_term_ini = $row[5];
    $fe_inicio = $row[6];
    $ds_duracion = $row[7];
    
    # Recupera las fechas de cada semana
    $Query  = "SELECT a.fl_leccion, no_semana, ds_titulo, fl_semana, ".ConsultaFechaBD('fe_publicacion', FMT_CAPTURA)." fe_publicacion, ";
    $Query .= ConsultaFechaBD('fe_entrega', FMT_CAPTURA)." fe_entrega, ".ConsultaFechaBD('fe_calificacion', FMT_CAPTURA)." fe_calificacion, ";
    $Query .= "fg_animacion, no_sketch,  ";
    $Query .= ConsultaFechaBD('fe_publicacion', FMT_HORAMIN)." hr_publicacion,".ConsultaFechaBD('fe_entrega', FMT_HORAMIN)." hr_entrega, ".ConsultaFechaBD('fe_calificacion', FMT_HORAMIN)." hr_calificacion ";
    $Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
    $Query .= "ON (a.fl_leccion=b.fl_leccion AND fl_term=$clave) ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $Query .= "AND no_grado=$no_grado ";
    $Query .= "ORDER BY no_semana";
    $rs = EjecutaQuery($Query);
    for($tot_semanas = 0; $row = RecuperaRegistro($rs); $tot_semanas++) {
      $fl_leccion[$tot_semanas] = $row[0];
      $no_semana[$tot_semanas] = $row[1];
      $ds_titulo[$tot_semanas] = str_texto($row[2]);
      $fl_semana[$tot_semanas] = $row[3];
      $fe_publicacion[$tot_semanas] = $row[4];
      $fe_entrega[$tot_semanas] = $row[5];
      $fe_calificacion[$tot_semanas] = $row[6];
      $fg_animacion[$tot_semanas] = $row[7];
      $no_sketch[$tot_semanas] = $row[8];
      $hr_publicacion[$tot_semanas] = $row[9];
      $hr_entrega[$tot_semanas] = $row[10];
      $hr_calificacion[$tot_semanas] = $row[11];
    }
    
    # Recupera opciones de pago del curso
    $Query  = "SELECT no_a_payments, no_b_payments, no_c_payments, no_d_payments ";
    $Query .= "FROM k_programa_costos ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $row = RecuperaValor($Query);
    $no_payments[1] = $row[0];
    $no_payments[2] = $row[1];
    $no_payments[3] = $row[2];
    $no_payments[4] = $row[3];
    
    # Recupera las fechas de pago
    $Query  = "SELECT no_opcion, no_pago, fl_term_pago, ".ConsultaFechaBD('fe_pago', FMT_CAPTURA)." fe_pago ";
    $Query .= "FROM k_term_pago ";
    if($no_grado > '1')
      $Query .= "WHERE fl_term=$fl_term_ini "; 
    else 
      $Query .= "WHERE fl_term=$clave "; 
    $Query .= "ORDER BY no_opcion, no_pago";
    $rs = EjecutaQuery($Query);
    while($row = RecuperaRegistro($rs)) {
      $no_opcion = $row[0];
      $no_pago = $row[1];
      $fl_term_pago[$no_opcion][$no_pago] = $row[2];
      $fe_pago[$no_opcion][$no_pago] = $row[3];
    }
  }


  
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_PERIODOS);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  
  # Campos de captura
  if(!empty($clave)) {
    Forma_CampoInfo(ObtenEtiqueta(380), $nb_programa."&nbsp;(".$ds_duracion.")");
    Forma_CampoOculto('fl_programa', $fl_programa);
    Forma_CampoOculto('nb_programa', $nb_programa);
    Forma_CampoInfo(ObtenEtiqueta(381), $nb_periodo);
    Forma_CampoOculto('fl_periodo', $fl_periodo);
    Forma_CampoOculto('nb_periodo', $nb_periodo);
    Forma_Espacio( );
    Forma_CampoInfo(ObtenEtiqueta(375), $no_grado);
    Forma_CampoOculto('no_grado', $no_grado);
    Forma_CampoOculto('fe_inicio', $fe_inicio);

    # AGRV 18/03/14 
    # Si el term es diferente de 1 permite elegir la fecha del term inicial
    if ($no_grado!='1') {
      $Query  = "SELECT nb_periodo, fl_term FROM k_term a, c_periodo b ";
      $Query .= "WHERE a.fl_periodo=b.fl_periodo AND fl_programa=$fl_programa AND no_grado=1 AND fe_inicio < STR_TO_DATE('$fe_inicio','%d-%m-%Y') ORDER BY fe_inicio";
      Forma_CampoSelectBD(ObtenEtiqueta(379), False, 'fl_term_ini', $Query, $fl_term_ini, $fl_term_ini_err, True);
    }
  }
 
  Forma_Espacio( );
 
  # Fechas iniciales para cada semana
  if(!empty($clave)) {
    $tit = array(ObtenEtiqueta(390).'|center', ObtenEtiqueta(385), '* '.ObtenEtiqueta(382), '* '.ObtenEtiqueta(383), '* '.ObtenEtiqueta(384),
                 ObtenEtiqueta(393).'|center', ObtenEtiqueta(394).'|center');
    $ancho_col = array('5%', '', '18%', '18%', '18%', '5%', '5%');
    Forma_Tabla_Ini('100%', $tit, $ancho_col);
    for($i = 0; $i < $tot_semanas; $i++) {  
      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      echo "
      <tr class='$clase'>
        <td align='center'>$no_semana[$i]</td>
        <td align='left'>$ds_titulo[$i]</td>
        <td align='left'>";
      if($fe_publicacion_err[$i])
        $ds_clase = 'css_input_error';
      else
        $ds_clase = 'css_input';
      CampoTexto('fe_publicacion_'.$i, $fe_publicacion[$i], 10, 10, $ds_clase);
      Forma_Calendario('fe_publicacion_'.$i);
      if($hr_publicacion_err[$i])
        $ds_clasehr = 'css_input_error';
      else
        $ds_clasehr = 'css_input';
      CampoTexto('hr_publicacion_'.$i, $hr_publicacion[$i], 5, 3, $ds_clasehr, False);
      echo "</td>
        <td align='left'>";
      if($fe_entrega_err[$i])
        $ds_clase = 'css_input_error';
      else
        $ds_clase = 'css_input';
      CampoTexto('fe_entrega_'.$i, $fe_entrega[$i], 10, 10, $ds_clase);
      Forma_Calendario('fe_entrega_'.$i);
      if($hr_entrega_err[$i])
        $ds_clasehr = 'css_input_error';
      else
        $ds_clasehr = 'css_input';
      CampoTexto('hr_entrega_'.$i, $hr_entrega[$i], 5, 3, $ds_clasehr, False);
      echo "</td>
        <td align='left'>";
      if($fe_calificacion_err[$i])
        $ds_clasehr = 'css_input_error';
      else
        $ds_clasehr = 'css_input';
      CampoTexto('fe_calificacion_'.$i, $fe_calificacion[$i], 10, 10, $ds_clase);
      Forma_Calendario('fe_calificacion_'.$i);
      if($hr_calificacion_err[$i])
        $ds_clasehr = 'css_input_error';
      else
        $ds_clasehr = 'css_input';
      CampoTexto('hr_calificacion_'.$i, $hr_calificacion[$i], 5, 3, $ds_clasehr, False);
      echo "</td>
        <td align='center'>";
      if($fg_animacion[$i] == '1')
        echo ETQ_SI;
      else
        echo ETQ_NO;
      echo "</td>
        <td align='center'>$no_sketch[$i]</td>
      </tr>\n";
      Forma_CampoOculto('fl_leccion_'.$i, $fl_leccion[$i]);
      Forma_CampoOculto('no_semana_'.$i, $no_semana[$i]);
      Forma_CampoOculto('ds_titulo_'.$i, $ds_titulo[$i]);
      Forma_CampoOculto('fl_semana_'.$i, $fl_semana[$i]);
      Forma_CampoOculto('fg_animacion_'.$i, $fg_animacion[$i]);
      Forma_CampoOculto('no_sketch_'.$i, $no_sketch[$i]);
    }
    Forma_Tabla_Fin( );
    $fg_error = False;
    $fg_error_fecha = False;
    for($i = 0; $i < $tot_semanas; $i++) {
      $fg_error = $fg_error || ($fe_publicacion_err[$i] == ERR_REQUERIDO) || ($fe_entrega_err[$i] == ERR_REQUERIDO) || ($fe_calificacion_err[$i] == ERR_REQUERIDO);
      $fg_error_fecha = $fg_error_fecha || ($fe_publicacion_err[$i] == ERR_FORMATO_FECHA) || ($fe_entrega_err[$i] == ERR_FORMATO_FECHA) || ($fe_calificacion_err[$i] == ERR_FORMATO_FECHA);
    }
    if($fg_error OR $fg_error_fecha) {
      Forma_Tabla_Ini('100%');
      if($fg_error)
        Forma_Tabla_Error('1', ERR_REQUERIDO);
      if($fg_error_fecha)
        Forma_Tabla_Error('1', ERR_FORMATO_FECHA);
      Forma_Tabla_Fin( );
    }
    Forma_Espacio( );
    
  }
  Forma_CampoOculto('tot_semanas', $tot_semanas);

  # Obtenemos la Fecha cuando el grado es diferente de uno
  if ($no_grado != '1' ) {
    $Query  = "SELECT fe_inicio, fl_term FROM k_term a, c_periodo b ";
    $Query .= "WHERE a.fl_periodo=b.fl_periodo AND fl_programa=$fl_programa AND fl_term= $fl_term_ini AND no_grado=1 AND fe_inicio < STR_TO_DATE('$fe_inicio','%d-%m-%Y') ";
    $Query .= "ORDER BY fe_inicio";
    $row = RecuperaValor($Query);
    $fe_ini_programa = $row[0];
  }else
    $fe_ini_programa = $fe_inicio;


  # Obtenemos las 2 semanas antes de la fecha inicio
  $fe_ini_programa = strtotime('-2 week',strtotime ( $fe_ini_programa ) );
  $fe_inicioD= date ( 'd' , $fe_ini_programa);
  $fe_inicioM= date ( 'm' , $fe_ini_programa);
  $fe_inicioA= date ( 'Y' , $fe_ini_programa);


  # Fechas para cada pago
  if(!empty($fl_term_ini) || (!empty($clave) AND $no_grado=='1')) {
    # Muestra la tabla de los breaks 
    Forma_Seccion('Breaks');
    Forma_Espacio( );
    $titulos = array(ObtenEtiqueta(650), ObtenEtiqueta(371), ObtenEtiqueta(513), ObtenEtiqueta(700));
    $ancho_col = array('20%', '15%','15%', '10%');
    Forma_Tabla_Ini('60%', $titulos, $ancho_col);
        
    # Consulta breaks
    $Query  = "SELECT fl_break, ds_break '".ObtenEtiqueta(650)."', ";
    $Query .= ConsultaFechaBD('fe_ini', FMT_FECHA)." '".ObtenEtiqueta(371)."', ";
    $Query .= ConsultaFechaBD('fe_fin', FMT_FECHA)." '".ObtenEtiqueta(513)."',  DATEDIFF(fe_fin, fe_ini) + 1 as '".ObtenEtiqueta(700)."|right' ";
    $Query .= "FROM c_break ORDER BY fe_ini, fe_fin ";
    $rs = EjecutaQuery($Query);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      $break = str_texto($row[1]);
      $fe_ini = $row[2];
      $fe_fin = $row[3];
      $dias = $row[4];
      if($i % 2 == 0)
          $clase = "css_tabla_detalle";
        else
          $clase = "css_tabla_detalle_bg";
      echo "
        <tr class='$clase'>
          <td>$break</td>
          <td>$fe_ini</td>
          <td>$fe_fin</td>
          <td>$dias</td>
        </tr>";
    }
    Forma_Tabla_Fin( );
    
    # Opciones para las fechas de los pagos
    $opciones = array(1=>'A', 'B', 'C', 'D');
    for($i = 1; $i <= 4; $i++) {
      if(!empty($no_payments[$i])) {
        Forma_CampoOculto('no_payments_'.$i, $no_payments[$i]);
        for($j = 1; $j <= $no_payments[$i]; $j++) {
          if($j == 1){
            Forma_Seccion('Payment dates Option '.$opciones[$i]);
          }
          Forma_CampoTexto("Payment $j ".ETQ_FMT_FECHA, False, 'fe_pago_'.$i.'_'.$j, $fe_pago[$i][$j], 10, 10, $fe_pago_err[$i][$j], '', '', False);
          Forma_Calendario('fe_pago_'.$i.'_'.$j);
          Forma_CampoOculto('fl_term_pago_'.$i.'_'.$j, $fl_term_pago[$i][$j]);
        }
      }
    }
  }

  Forma_Termina(False);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>