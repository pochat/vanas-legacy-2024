<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
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
  if(!ValidaPermiso(FUNC_PERIODOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$fl_programa = RecibeParametroNumerico('fl_programa');
  $nb_programa = RecibeParametroHTML('nb_programa');
  $fl_periodo = RecibeParametroNumerico('fl_periodo');
  $nb_periodo = RecibeParametroHTML('nb_periodo');
  $no_grado = RecibeParametroNumerico('no_grado');
  $fg_frecuencia = RecibeParametroNumerico('fg_frecuencia');
  $fl_term_ini = RecibeParametroNumerico('fl_term_ini');
  $fe_inicio = RecibeParametroFecha('fe_inicio');

  # Recupera las fechas de cada semana
  $tot_semanas = RecibeParametroNumerico('tot_semanas');
  for($i = 0; $i < $tot_semanas; $i++) {
    $fl_leccion[$i] = RecibeParametroNumerico('fl_leccion_'.$i);
    $no_semana[$i] = RecibeParametroNumerico('no_semana_'.$i);
    $ds_titulo[$i] = RecibeParametroHTML('ds_titulo_'.$i);
    $fl_semana[$i] = RecibeParametroNumerico('fl_semana_'.$i);
    $fe_publicacion[$i] = RecibeParametroFecha('fe_publicacion_'.$i);
    $fe_entrega[$i] = RecibeParametroFecha('fe_entrega_'.$i);
    $fe_calificacion[$i] = RecibeParametroFecha('fe_calificacion_'.$i);
    $fg_animacion[$i] = RecibeParametroBinario('fg_animacion_'.$i);
    $no_sketch[$i] = RecibeParametroNumerico('no_sketch_'.$i);
    $hr_publicacion[$i] = RecibeParametroHoraMin('hr_publicacion_'.$i);
    $hr_entrega[$i] = RecibeParametroHoraMin('hr_entrega_'.$i);
    $hr_calificacion[$i] = RecibeParametroHoraMin('hr_calificacion_'.$i);
  }
  
  # Pagos de cada opcion del programa
  for($i = 1; $i <= 4; $i++) {
    $no_payments[$i] = RecibeParametroNumerico('no_payments_'.$i);
    for($j = 1; $j <= $no_payments[$i]; $j++) {
      $fl_term_pago[$i][$j] = RecibeParametroNumerico('fl_term_pago_'.$i.'_'.$j);
      $fe_pago[$i][$j] = RecibeParametroFecha('fe_pago_'.$i.'_'.$j);
    }
  }
  
  # Valida campos obligatorios
  if(empty($fl_programa))
    $fl_programa_err = ERR_REQUERIDO;
  if(empty($fl_periodo))
    $fl_periodo_err = ERR_REQUERIDO;
  if(empty($no_grado))
    $no_grado_err = ERR_REQUERIDO;
  for($i = 0; $i < $tot_semanas; $i++) {
    if(empty($fe_publicacion[$i]))
      $fe_publicacion_err[$i] = ERR_REQUERIDO;
    if(empty($fe_entrega[$i]))
      $fe_entrega_err[$i] = ERR_REQUERIDO;
    if(empty($fe_calificacion[$i]))
      $fe_calificacion_err[$i] = ERR_REQUERIDO;
    if(empty($hr_entrega[$i]))
      $hr_entrega_err[$i] = ERR_REQUERIDO;
    if(empty($hr_calificacion[$i]))
      $hr_calificacion_err[$i] = ERR_REQUERIDO;
  }
  
  # Valida enteros
  if(empty($no_grado_err) AND !ValidaEntero($no_grado))
    $no_grado_err = ERR_ENTERO;
  if(empty($no_grado_err) AND $no_grado > MAX_TINYINT)
    $no_grado_err = ERR_TINYINT;
  
  # Verifica que no exista el grado
  if(empty($fl_programa_err) AND empty($fl_periodo_err) AND empty($no_grado_err)) {
    $Query  = "SELECT count(1) ";
    $Query .= "FROM k_term ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $Query .= "AND fl_periodo=$fl_periodo ";
    $Query .= "AND no_grado=$no_grado ";
    if(!empty($clave))
      $Query .= "AND fl_term<>$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      $no_grado_err = 108; # Existing level found for this program.
  }
  
  # Verifica que el formato de la fecha sea valido
  for($i = 0; $i < $tot_semanas; $i++) {
    if(!empty($fe_publicacion[$i]) AND !ValidaFecha($fe_publicacion[$i]))
      $fe_publicacion_err[$i] = ERR_FORMATO_FECHA;
    if(!empty($fe_entrega[$i]) AND !ValidaFecha($fe_entrega[$i]))
      $fe_entrega_err[$i] = ERR_FORMATO_FECHA;
    if(!empty($fe_calificacion[$i]) AND !ValidaFecha($fe_calificacion[$i]))
      $fe_calificacion_err[$i] = ERR_FORMATO_FECHA;
    if(!empty($hr_publicacion[$i]) AND !ValidaHoraMin($hr_publicacion[$i]))
      $hr_publicacion_err[$i] = ERR_FORMATO_HORAMIN;
    if(!empty($hr_entrega[$i]) AND !ValidaHoraMin($hr_entrega[$i]))
      $hr_entrega_err[$i] = ERR_FORMATO_HORAMIN;
    if(!empty($hr_calificacion[$i]) AND !ValidaHoraMin($hr_calificacion[$i]))
      $hr_calificacion_err[$i] = ERR_FORMATO_HORAMIN;
  }
  
  # Si el grado es diferente de 1 si encuentra un term inicial no inserta 
  # En caso contrario podra cero
  if($no_grado != '1' AND empty($fl_term_ini)){
    if($no_grado==2)
      $term = "fl_term";
    else
      $term = "fl_term_ini";
    $Query  = "SELECT MAX($term) FROM k_term a, c_periodo b ";
    $Query .= "WHERE a.fl_periodo=b.fl_periodo AND  fl_programa=$fl_programa AND no_grado=$no_grado-1 ";
    $Query .= "AND fe_inicio < (SELECT fe_inicio FROM c_periodo WHERE fl_periodo=$fl_periodo) ";
    $Query .= "ORDER BY fe_inicio DESC";
    $row = RecuperaValor($Query);
    # Buscamos el ultimo term que se inserto con el mismo programa y antes de la fecha del term que se esta insertado 
    # Tambien debe ser un grado meos 1 al que esta recibiendo o intsertando
    if(!empty($row[0])){
      $fl_term_ini = $row[0];
      # Verificamos que el term inicia que se elijio no tenga un term con el mismo grado 
      $row_existe = RecuperaValor("SELECT COUNT(*)FROM k_term WHERE fl_term_ini=$fl_term_ini AND no_grado=$no_grado");      
      if(!empty($row_existe[0])){
        $fl_term_ini = 0;
      }   
    }
    else{// En caso de que no existan term anteriores pondra term inicial cero
      $fl_term_ini = 0;
    }
  }

  # AGRV 18/03/14
  # Verifica que el grado inicial no se haya asignado a otro
  if(!empty($fl_term_ini)) {
    $Query  = "SELECT COUNT(1) ";
    $Query .= "FROM k_term ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $Query .= "AND no_grado=$no_grado ";
    $Query .= "AND fl_term_ini=$fl_term_ini ";
    $Query .= "AND fl_term<>$clave"; 
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      $fl_term_ini_err = 111; # This level was already selected
  }

  # Valida Pagos de cada opcion del programa
  for($i = 1; $i <= 4; $i++) {
    for($j = 1; $j <= $no_payments[$i]; $j++) {
      if(empty($fe_pago[$i][$j]))
        $fe_pago_err[$i][$j] = ERR_REQUERIDO;
      if(!empty($fe_pago[$i][$j]) AND !ValidaFecha($fe_pago[$i][$j]))
        $fe_pago_err[$i][$j] = ERR_FORMATO_FECHA;
    }
  }
  
  # Regresa a la forma con error
  $fg_error = $fl_programa_err || $fl_periodo_err || $no_grado_err || $fl_term_ini_err;
  for($i = 0; $i < $tot_semanas; $i++)
    $fg_error = $fg_error || $fe_publicacion_err[$i] || $fe_entrega_err[$i] || $fe_calificacion_err[$i] || $hr_calificacion_err[$i] || $hr_entrega_err[$i];
  for($i = 1; $i <= 4; $i++) {
    for($j = 1; $j <= $no_payments[$i]; $j++) {
      $fg_error = $fg_error || $fe_pago_err[$i][$j];
    }
  }
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('fl_programa', $fl_programa);
    Forma_CampoOculto('fl_programa_err', $fl_programa_err);
    Forma_CampoOculto('nb_programa', $nb_programa);
    Forma_CampoOculto('fl_periodo', $fl_periodo);
    Forma_CampoOculto('fl_periodo_err', $fl_periodo_err);
    Forma_CampoOculto('nb_periodo', $nb_periodo);
    Forma_CampoOculto('no_grado', $no_grado);
    Forma_CampoOculto('no_grado_err', $no_grado_err);
    Forma_CampoOculto('fg_frecuencia', $fg_frecuencia);
    Forma_CampoOculto('tot_semanas', $tot_semanas);
    Forma_CampoOculto('fl_term_ini', $fl_term_ini);
    Forma_CampoOculto('fl_term_ini_err', $fl_term_ini_err);
    Forma_CampoOculto('fe_inicio', $fe_inicio);
    for($i = 0; $i < $tot_semanas; $i++) {
      Forma_CampoOculto('fl_leccion_'.$i, $fl_leccion[$i]);
      Forma_CampoOculto('no_semana_'.$i, $no_semana[$i]);
      Forma_CampoOculto('ds_titulo_'.$i, $ds_titulo[$i]);
      Forma_CampoOculto('fl_semana_'.$i, $fl_semana[$i]);
      Forma_CampoOculto('fe_publicacion_'.$i, $fe_publicacion[$i]);
      Forma_CampoOculto('fe_publicacion_err_'.$i, $fe_publicacion_err[$i]);
      Forma_CampoOculto('hr_publicacion_'.$i, $hr_publicacion[$i]);
      Forma_CampoOculto('fe_entrega_'.$i, $fe_entrega[$i]);
      Forma_CampoOculto('fe_entrega_err_'.$i, $fe_entrega_err[$i]);
      Forma_CampoOculto('hr_entrega_'.$i, $hr_entrega[$i]);
      Forma_CampoOculto('hr_entrega_err_'.$i, $hr_entrega_err[$i]);
      Forma_CampoOculto('fe_calificacion_'.$i, $fe_calificacion[$i]);
      Forma_CampoOculto('fe_calificacion_err_'.$i, $fe_calificacion_err[$i]);
      Forma_CampoOculto('hr_calificacion_'.$i, $hr_calificacion[$i]);
      Forma_CampoOculto('hr_calificacion_err_'.$i, $hr_calificacion_err[$i]);
      Forma_CampoOculto('fg_animacion_'.$i, $fg_animacion[$i]);
      Forma_CampoOculto('no_sketch_'.$i, $no_sketch[$i]);
    }
    for($i = 1; $i <= 4; $i++) {
      Forma_CampoOculto('no_payments_'.$i, $no_payments[$i]);
      for($j = 1; $j <= $no_payments[$i]; $j++) {
        Forma_CampoOculto('fl_term_pago_'.$i.'_'.$j, $fl_term_pago[$i][$j]);
        Forma_CampoOculto('fe_pago_'.$i.'_'.$j, $fe_pago[$i][$j]);
        Forma_CampoOculto('fe_pago_err_'.$i.'_'.$j, $fe_pago_err[$i][$j]);
      }
    }
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }

  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO k_term (fl_programa, fl_periodo, no_grado, fl_term_ini) ";
    $Query .= "VALUES($fl_programa, $fl_periodo, $no_grado, $fl_term_ini)";
    $fl_term = EjecutaInsert($Query);
  }
  else {
    $Query  = "UPDATE k_term SET fl_programa=$fl_programa, fl_periodo=$fl_periodo, no_grado=$no_grado, fl_term_ini=$fl_term_ini ";
    $Query .= "WHERE fl_term=$clave"; 
    EjecutaQuery($Query);
  }
  
  # Si el grado es uno automaticamente guardara sus fechas de pagos
  if($no_grado=='1' && !empty($fl_term) AND empty($clave)){
    $Query  = "SELECT no_a_payments, no_b_payments, no_c_payments, no_d_payments, no_semanas ";
    $Query .= "FROM k_programa_costos ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $row = RecuperaValor($Query);
    $no_payments[1] = $row[0];
    $no_payments[2] = $row[1];
    $no_payments[3] = $row[2];
    $no_payments[4] = $row[3];
    $no_semanas = $row[4];
    # Obtenemos loa fecha inicial del periodo que eligio y los meses que dura 
    $row = RecuperaValor("SELECT ".  ConsultaFechaBD('fe_inicio', FMT_FECHA)." FROM c_periodo WHERE fl_periodo=$fl_periodo");    
    $fe_inicio = str_texto($row[0]);
    $fe_pago_firt = date('d-m-Y',strtotime('-2 week',strtotime ($fe_inicio )));
    $duracion_meses = $no_semanas/4;
    for($i=1;$i<=4;$i++){
      for($j=1;$j<=$no_payments[$i];$j++){
        $meses = $duracion_meses/$no_payments[$i];
          # Verificamos si encontro un break utiliza la fecha guardada
          if($encontro)
            $fe_pago =  $fe_guardarda;

          # Aumenta los meses que dura los pagos
          if($j==1)
            $fe_pago = $fe_pago_firt;
          else
            $fe_pago = date('Y-m-d',strtotime('+'.$meses.' month '.$fe_pago.''));

          # Busca que la fecha no se encuentre en un break
          $Query = "SELECT  fe_ini FROM c_break WHERE '$fe_pago' BETWEEN fe_ini AND fe_fin ";
          $row =  RecuperaValor($Query);
          $fe_ini = $row[0];

          # Si existe un registro reducira 4 dias antes la fecha inicial del break
          if(!empty($fe_ini)) {
            $fe_guardarda = $fe_pago;
            $fe_pago = date('Y-m-d',strtotime ( '-4 day' , strtotime ( $fe_ini ) ) );
            $encontro = True;
          }
          else{
            $fe_pago =$fe_pago ;
            $encontro = False;
          }
          
          # Formato de fecha de pagos
          $fe_pago = date('Y-m-d', strtotime($fe_pago));
          #Insertamos las fechas de pagos
          $Query_pagos  = "INSERT INTO k_term_pago (fl_term,no_opcion,no_pago,fe_pago) ";
          $Query_pagos .= "VALUES ($fl_term,$i,$j,'$fe_pago') ";
          EjecutaQuery($Query_pagos);
      }
    }
    fe_ini_fe_fin($fl_term);
  }

  # Inserta o actualiza los pagos
  if($no_grado=='1')
    $fl_term_ini=$clave;
    
  # Inserta o actualiza las fechas de pagos de los terms
  if(!empty($fl_term_ini)) {
    for($i = 1; $i <= 4; $i++) {
      for($j = 1; $j <= $no_payments[$i]; $j++) {
        $row = RecuperaValor("SELECT COUNT(1) FROM k_term_pago WHERE fl_term=$fl_term_ini AND no_opcion=$i AND no_pago=$j");
        if($row[0] == '0') {
          if(!empty($fe_pago[$i][$j]))
            $Query = "INSERT INTO k_term_pago (fl_term, no_opcion, no_pago, fe_pago) VALUES ($fl_term_ini, $i, $j, '".ValidaFecha($fe_pago[$i][$j])."')";          
        }
        else {
          if(!empty($fe_pago[$i][$j])) {
            $Query  = "UPDATE k_term_pago SET fe_pago='".ValidaFecha($fe_pago[$i][$j])."' ";
            $Query .= "WHERE fl_term=$fl_term_ini AND no_opcion=$i AND no_pago=$j";
          }
          else 
            $Query  = "DELETE FROM k_term_pago WHERE fl_term=$fl_term_ini AND no_opcion=$i AND no_pago=$j";
        }
        EjecutaQuery($Query);
      }
    }
    fe_ini_fe_fin($fl_term_ini);
  }
  
  # Actualizamos las fechas inicio y fin de cada pago
  
  function fe_ini_fe_fin($clave){
    $Query  = "SELECT  no_pago, d.fe_inicio fe_inicio_programa, CASE  a.no_opcion ";
    $Query .= "WHEN '1'  THEN (SELECT no_a_payments FROM k_programa_costos i WHERE i.fl_programa=b.fl_programa) ";
    $Query .= "WHEN '2'  THEN (SELECT no_b_payments FROM k_programa_costos i WHERE i.fl_programa=b.fl_programa) ";
    $Query .= "WHEN '3'  THEN (SELECT no_c_payments FROM k_programa_costos i WHERE i.fl_programa=b.fl_programa) ";
    $Query .= "WHEN '4'  THEN (SELECT no_d_payments FROM k_programa_costos i WHERE i.fl_programa=b.fl_programa) END no_pagos, c.no_semanas,a.fl_term_pago ";
    $Query .= "FROM k_term_pago a, k_term b, k_programa_costos c, c_periodo d ";
    $Query .= "WHERE a.fl_term=b.fl_term AND b.fl_programa = c.fl_programa AND b.fl_periodo = d.fl_periodo AND b.fl_term=$clave";
    $rs = EjecutaQuery($Query);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      $no_pago = $row[0];
      $fe_inicio_programa = $row[1];
      $no_pagos= $row[2];
      $no_semanas = $row[3];
      $fl_term_pago = $row[4];
      
      //meses que dura el curso
      $meses_duracion=$no_semanas/4;
      //meses por pago
      $meses_pago = $meses_duracion/$no_pagos;
      //anios que se le aumenta a la fecha inicial del programa
      $desfase = ($no_pago-1)*$meses_pago;
      
      //fecha inicial del pago
      $fe_ini_pago = date('Y-m-d',strtotime ( "+ ".$desfase." month", strtotime($fe_inicio_programa)));
      //fecha final del pago
      $fe_fin_pago = date('Y-m-d',strtotime ( "+ ".$meses_pago." month", strtotime($fe_ini_pago)));
      
      //Actualiza los datos
      $Update = "UPDATE k_term_pago SET fe_ini_pago = '$fe_ini_pago', fe_fin_pago = '$fe_fin_pago' WHERE fl_term_pago=$fl_term_pago ";
      EjecutaQuery($Update);
    }
  
  }

  # Inserta o actualiza las semanas
  if(empty($clave)) {
    $row = RecuperaValor("SELECT fe_inicio FROM c_periodo WHERE fl_periodo=$fl_periodo");
    $anio_ini = substr($row[0], 0, 4);
    $mes_ini = substr($row[0], 5, 2);
    $dia_ini = substr($row[0], 8, 2);
    $fe_inicio = date_create( );
    if($fg_frecuencia == 1)
    {
      $limite_entrega = ObtenConfiguracion(23);
      $limite_calificacion = ObtenConfiguracion(24);
    }
    else
    {
      $limite_entrega = ObtenConfiguracion(49);
      $limite_calificacion = ObtenConfiguracion(50);
    }
    $Query  = "SELECT fl_leccion, no_semana ";
    $Query .= "FROM c_leccion ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $Query .= "AND no_grado=$no_grado ";
    $Query .= "ORDER BY no_semana";
    $rs = EjecutaQuery($Query);
    for($i = 0; $row = RecuperaRegistro($rs); $i++) {
      $fl_leccion[$i] = $row[0];
      $no_semana[$i] = $row[1] - 1;
      if($fg_frecuencia == 2)
        $no_semana[$i] *= 2;
      # Obtenemos la hora variable de configuracion y la agragamos las fechas
      $hr = substr(ObtenConfiguracion(68),0,2);
      $min= substr(ObtenConfiguracion(68),3,2);
      date_time_set($fe_inicio, $hr, $min);
      date_date_set($fe_inicio, $anio_ini, $mes_ini, $dia_ini);
      date_modify($fe_inicio, "+".$no_semana[$i]." week");
      $fe_publicacion = date_format($fe_inicio, 'Y-m-d H:i');
      date_modify($fe_inicio, "+$limite_entrega day");
      $fe_entrega = date_format($fe_inicio, 'Y-m-d H:i');
      date_date_set($fe_inicio, $anio_ini, $mes_ini, $dia_ini);
      date_modify($fe_inicio, "+".$no_semana[$i]." week");
      date_modify($fe_inicio, "+$limite_calificacion day");
      $fe_calificacion = date_format($fe_inicio, 'Y-m-d H:i');
      $Query  = "INSERT INTO k_semana(fl_term, fl_leccion, fe_publicacion, fe_entrega, fe_calificacion) ";
      $Query .= "VALUES($fl_term, $fl_leccion[$i], '$fe_publicacion', '$fe_entrega', '$fe_calificacion') ";
      EjecutaQuery($Query);
    }
  }
  else {
    for($i = 0; $i < $tot_semanas; $i++) {
      $fe_publicacion[$i] = "'".ValidaFecha($fe_publicacion[$i])."  ".$hr_publicacion[$i]."'";
      $fe_entrega[$i] = "'".ValidaFecha($fe_entrega[$i])."  ".$hr_entrega[$i]."'";
      $fe_calificacion[$i] = "'".ValidaFecha($fe_calificacion[$i])."  ".$hr_calificacion[$i]."'";
      
      if(empty($fl_semana[$i])) {
        $Query  = "INSERT INTO k_semana(fl_term, fl_leccion, fe_publicacion, fe_entrega, fe_calificacion) ";
        $Query .= "VALUES($clave, $fl_leccion[$i], $fe_publicacion[$i], $fe_entrega[$i], $fe_calificacion[$i])";
        EjecutaQuery($Query);
      }
      else {
        $Query  = "UPDATE k_semana SET fe_publicacion=$fe_publicacion[$i], fe_entrega=$fe_entrega[$i], fe_calificacion=$fe_calificacion[$i] ";
        $Query .= "WHERE fl_semana=$fl_semana[$i]";
        EjecutaQuery($Query);
      }
    }
  }

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>