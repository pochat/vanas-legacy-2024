<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros  
  $destino = RecibeParametroHTML('destino', False, True);
  if(!empty($destino)){ // cuando se borra o refun en uno de los pagos
    $clave = RecibeParametroHTML('clave', False, True);
  }
  else { //viene del listado o students o applications
    $clave = RecibeParametroHTML('clave');
    if(empty($clave))
      $clave = RecibeParametroHTML('clave', False, True);
    # Se conoce que viene de applications si no existe en c_alumno
    # Entonces al cl_sesion que se recibe le agregamos 'a-' para posteriormente
    # Conocer que es un applications
    if(!strpos($clave,'-') AND !ExisteEnTabla('c_alumno', 'fl_alumno', $clave))
      $clave = "a-".$clave;
    else
      $clave = $clave;// se toma la clave cuando viene del listado o de students
  }
  $fg_error = RecibeParametroNumerico('fg_error');

  if(!empty($fg_error)) { // Error recibe  los parametros enviados
    $fe_fecha = RecibeParametroFecha('fe_fecha');
    $fe_fecha_err  = RecibeParametroNumerico('fe_fecha_err');
    $cl_metodo_pago = RecibeParametroNumerico('cl_metodo_pago');
    $cl_metodo_pago_err= RecibeParametroNumerico('cl_metodo_pago_err');
    $ds_cheque = RecibeParametroHTML('ds_cheque');
    $ds_comentario = RecibeParametroHTML('ds_comentario');
    $fg_app_frm = RecibeParametroNumerico('fg_app_frm');
    $fg_realizar = RecibeParametroBinario('fg_realizar');
    $fg_realizar_err = RecibeParametroNumerico('fg_realizar_err');
  }
  else { //sin error
    $fe_fecha = Date('d-m-Y');
    $mn_late_fee = ObtenConfiguracion(66); 
  
    #revisa si es una forma de aplicacion 
    if(strpos($clave, '-')) { //regresa un numero
      $tmp = explode ('-', $clave);
      $clave=$tmp[1]; // fl_sesion
      $fg_app_frm = 1;
    }
    else{
      $fg_app_frm = 0;
    }
  }

  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PAGOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recupera la sesion
  if(empty($fg_app_frm)) // clave trae un fl_alumno (que es igual a un fl_usuario)
    $Query  = "SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$clave";
  else // trae un fl_sesion
    $Query  = "SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  #Recupera si es inscrito o no 
  $row = RecuperaValor("SELECT fg_inscrito FROM c_sesion WHERE cl_sesion='$cl_sesion'");
  $fg_inscrito = $row[0];
  
  
  # Si no trae sesion busca en c_usuario
  $Query = "SELECT fg_pago FROM c_sesion WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fg_pago = $row[0];
  
  #obtenemos el ultimo pago que se realizo
  if(empty($fg_app_frm)) { // clave trae un fl_alumno
    $Query = "SELECT MAX(fl_alumno_pago) FROM k_alumno_pago WHERE fl_alumno=$clave";
    $row = RecuperaValor($Query);
    $pago_final = $row[0];
  }
  else {
    $Query = "SELECT MAX(fl_ses_pago) FROM k_ses_pago WHERE cl_sesion='$cl_sesion'";
    $row = RecuperaValor($Query);
    $pago_final = $row[0];
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  #scrip para el dialogo
  echo "
  <script type='text/javascript' src='".PATH_JS."/sendtemplate.js.php'></script>";
  PresentaEncabezado(FUNC_PAGOS);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Formato de la fecha se utiliza en latabla del detalle y en el export
  define('FORMAT_DATE','%b %d, %Y');
  
  # Inicializa variables
  if((empty($fg_app_frm) AND !empty($fg_pago)) OR (!empty($fg_app_frm) AND !empty($fg_pago))) { //Es un estudiante y ya realizo el pago de la App fee
    if(empty($fg_app_frm)){
       # Tiene asignado un grupo
      if(ExisteEnTabla('k_alumno_grupo','fl_alumno',$clave)){
        $Query = "SELECT f.fl_programa, CONCAT(f.nb_programa,' (',ds_duracion,')'), e.no_grado, d.nb_grupo, b.ds_login, c.fl_grupo, d.fl_term, ";
        $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno', "' '", NulosBD('b.ds_amaterno', ''));
        $Query .= ConcatenaBD($concat)." ds_nombre, ";
        $Query .= "g.ds_frecuencia, nb_periodo, f.no_grados no_gradoss  ";
        $Query .= "FROM c_usuario b, k_alumno_grupo c, c_grupo d, k_term e, c_programa f, k_app_contrato g, c_periodo h ";
        $Query .= "WHERE b.fl_usuario=c.fl_alumno ";
        $Query .= "AND c.fl_grupo=d.fl_grupo "; 
        $Query .= "AND d.fl_term=e.fl_term ";
        $Query .= "AND e.fl_programa=f.fl_programa ";
        $Query .= "AND b.cl_sesion=g.cl_sesion ";
        $Query .= "AND e.fl_periodo=h.fl_periodo ";
        $Query .= "AND b.fl_usuario=$clave ";
        $Query .= "AND g.no_contrato=1 ";
      }
      # No tiene asigado un grupo
      else{ 
        $Query  = "SELECT a.fl_programa, CONCAT(c.nb_programa,' (',ds_duracion,')'),b.no_grado,''nb_grupo,f.ds_login,'' fl_grupo, b.fl_term, ";
        $Query .= "CONCAT(f.ds_nombres, ' ', f.ds_apaterno, ' ', IFNULL(f.ds_amaterno, '')) ds_nombre, e.ds_frecuencia, nb_periodo, c.no_grados no_gradoss ";
        $Query .= "FROM k_ses_app_frm_1 a, k_term b, c_programa c, c_periodo d, k_app_contrato e, c_usuario f ";
        $Query .= "WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=b.fl_periodo AND a.fl_programa=c.fl_programa AND a.fl_periodo=d.fl_periodo ";
        $Query .= "AND a.cl_sesion=e.cl_sesion AND a.cl_sesion=f.cl_sesion AND no_contrato='1' AND b.fl_term=(SELECT MAX(fl_term) FROM k_alumno_term s WHERE s.fl_alumno=f.fl_usuario) AND f.fl_usuario=$clave ";       
      }      
    }
    else {
      $Query  = "SELECT d.fl_programa, CONCAT(d.nb_programa,' (',ds_duracion,')'),'' no_grado,'' nb_grupo, '' ds_login,'' fl_grupo, e.fl_term, CONCAT(a.ds_fname, ' ', a.ds_lname, ' ', IFNULL(a.ds_mname, '')) ds_nombre, ";
      $Query .= "c.ds_frecuencia,f.nb_periodo, d.no_grados no_gradoss ";
      $Query .= "FROM k_ses_app_frm_1 a, c_sesion b,k_app_contrato c, c_programa d, k_term e, c_periodo f ";
      $Query .= "WHERE a.cl_sesion=b.cl_sesion AND e.fl_programa=d.fl_programa AND b.cl_sesion=c.cl_sesion AND a.fl_programa = d.fl_programa ";
      $Query .= "AND a.fl_periodo=f.fl_periodo AND e.fl_periodo=f.fl_periodo AND b.fl_sesion='$clave' AND c.no_contrato=1  AND e.no_grado=1";
    }

    $row = RecuperaValor($Query);
    $fl_programa = $row[0];
    $nb_programa = str_texto($row[1]);
    $no_grado = $row[2];
    if(empty($no_grado))
      $no_grado = '(no assigment)';
    $nb_grupo = str_texto($row[3]);
    if(empty($nb_grupo))
      $nb_grupo = '(no assigment)';
    $ds_login = str_texto($row[4]);
    if(empty($ds_login))
      $ds_login = '(no assigment)';
    $fl_grupo = $row[5];
    $fl_term = $row[6];
    $ds_nombre = str_texto($row[7]);
    $frecuencia = str_texto($row[8]);
    $nb_periodo = str_texto($row[9]);
    $no_gradoss = $row[10];

  
    # Se visualiza el detalle del alumno 
    Forma_CampoInfo(ObtenEtiqueta(380), $nb_programa);
    Forma_CampoInfo(ObtenEtiqueta(375), $no_grado);
    Forma_CampoInfo(ObtenEtiqueta(380), $nb_periodo);
    Forma_CampoInfo(ObtenEtiqueta(420), $nb_grupo);
    Forma_CampoInfo(ETQ_USUARIO, $ds_login);
    Forma_CampoInfo(ETQ_NOMBRE, $ds_nombre);
    Forma_CampoInfo(ObtenEtiqueta(482), $frecuencia);
    Forma_Espacio( );
        
    # Recupera el tipo de pago para el curso
    $Query  = "SELECT fg_opcion_pago, ds_firma_alumno, fe_firma FROM k_app_contrato WHERE cl_sesion='$cl_sesion'";
    $row = RecuperaValor($Query);
    $fg_opcion_pago = $row[0];
    $ds_firma_alumno = $row[1];
    $fe_firma = $row[2];    
    
    $titulos = array(ObtenEtiqueta(375).'|center',ObtenEtiqueta(481).'|center', ObtenEtiqueta(485).'|center', ObtenEtiqueta(486).'|center',
                   ObtenEtiqueta(374).'|center', ObtenEtiqueta(596).'|center',ObtenEtiqueta(741),ObtenEtiqueta(742),ObtenEtiqueta(743), 
                   ObtenEtiqueta(483).'|center', ObtenEtiqueta(72),'','','');
    $ancho_col = array('20%', '15%', '10%', '15%', '10%','10%','10%','10%', '15%', '15%','3%','4%', '4%');
    Forma_Tabla_Ini('100%', $titulos, $ancho_col);
    
    //para obtener informacion del pago de app_fee
    $Query  = "SELECT CASE cl_metodo_pago WHEN 1 THEN 'Paypal' WHEN 2 THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END cl_metodo_app, ";
    $Query .= "(CONCAT(DATE_FORMAT(fe_pago, '".FORMAT_DATE."'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago_app, ";
    $Query .= "mn_pagado mn_pagado_app,DATE_FORMAT(b.fe_ultmod,'".FORMAT_DATE."') fe_ultmod1, ds_comentario ds_comentario_app , fl_sesion ";
    $Query .= "FROM c_sesion a, k_ses_app_frm_1 b ";
    $Query .= "WHERE a.cl_sesion=b.cl_sesion AND a.cl_sesion='$cl_sesion'";
    $row = RecuperaValor($Query);
    $cl_metodo_app = $row[0];
    $fe_pago_app = $row[1];
    $mn_pagado_app = $row[2];
    $fe_ultmod1 = $row[3];
    $ds_comentario_app = str_texto($row[4]);
    $fl_sesion = $row[5];
    # Podemos modificar la fecha de pago del app fee
    $fe_pago_app = "<a href='javascript:dialogo_refund($clave,$fl_sesion,$fg_inscrito,0,\"FAPP\");' title='Change payment date'>$fe_pago_app</a>";
    # Podemos modificar el metodo de pago
    $cl_metodo_app = "<a href='javascript:dialogo_refund($clave,$fl_sesion,$fg_inscrito,0,\"MAPP\");' title='Change payment method'>$cl_metodo_app</a>";
    # Podemos modificar el comentario
    $ds_comentario_app = "<a href='javascript:dialogo_refund($clave,$fl_sesion,$fg_inscrito,0,\"CAPP\");' title='Change payment method'>$ds_comentario_app</a>";
    if(!empty($fe_pago_app)){
      echo "
          <tr style='font-weight:bold; height:30px;' align='center'>
            <td>app fee</td>
            <td>Once</td>
            <td>$fe_ultmod1</td>
            <td>$mn_pagado_app</td>
            <td>$fe_pago_app</td>
            <td>$mn_pagado_app</td>
            <td>$earned</td>
            <td>$unearned</td>
            <td>$e_u</td>
            <td>$cl_metodo_app</td>
            <td align='left'>$ds_comentario_app</td>
            <td><a href='".PATH_CAMPUS."/students/invoice.php?fl_sesion=$fl_sesion&destino=payments_frm.php'><img src='".PATH_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>";
      echo "
          </tr>";
    }
    # Recupera informacion de los pagos
    switch($fg_opcion_pago) {
      case 1: $mn_due='mn_a_due'; $no_x_payments = 'no_a_payments'; break;
      case 2: $mn_due='mn_b_due'; $no_x_payments = 'no_b_payments'; break;
      case 3: $mn_due='mn_c_due'; $no_x_payments = 'no_c_payments'; break;
      case 4: $mn_due='mn_d_due'; $no_x_payments = 'no_d_payments'; break;
    }

    $fe_actual = Date('Y-m-d');//fecha actual con formato Y-m-d
    $concat = array("DATE_FORMAT(a.fe_pago,'".FORMAT_DATE."')", "' '", ConsultaFechaBD('a.fe_pago', FMT_HORA)); // formato de la fecha en que pago
    # Query para pagos realizados
    if($fg_inscrito==1 AND $fg_app_frm==0){
      $Query_pagado  = "SELECT  a.fl_term_pago, b.no_opcion, b.no_pago, DATE_FORMAT(b.fe_pago,'".FORMAT_DATE."'),(SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'), ";
      $Query_pagado .= "CASE a.cl_metodo_pago ";
      $Query_pagado .= "WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
      $Query_pagado .= "END ds_metodo_pago, ";
      $Query_pagado .= " ".ConcatenaBD($concat).", a.mn_pagado, a.ds_comentario, a.fl_alumno_pago, a.cl_metodo_pago, a.fg_refund,DATEDIFF(a.fe_pago, '$fe_actual') no_dias, ";
      $Query_pagado .= "mn_earned earned, mn_unearned unearned, ds_eu e_u,
      (SELECT nb_periodo FROM c_periodo r, k_term t WHERE r.fl_periodo=t.fl_periodo AND t.fl_term=b.fl_term) terms ";
      $Query_pagado .= "FROM k_alumno_pago a, k_term_pago b ";
      $Query_pagado .= "WHERE a.fl_term_pago = b.fl_term_pago AND a.fl_alumno=$clave ORDER BY b.fe_pago "; 
    }else{
      $Query_pagado  = "SELECT  a.fl_term_pago, b.no_opcion, b.no_pago, DATE_FORMAT(b.fe_pago,'".FORMAT_DATE."'),(SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'), ";
      $Query_pagado .= "CASE a.cl_metodo_pago ";
      $Query_pagado .= "WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
      $Query_pagado .= "END ds_metodo_pago, ";
      $Query_pagado .= " ".ConcatenaBD($concat).", a.mn_pagado, a.ds_comentario, a.fl_ses_pago, a.cl_metodo_pago, a.fg_refund,DATEDIFF(a.fe_pago, '$fe_actual') no_dias, ";
      $Query_pagado .= "'' earned, '' unearned, '' e_u, (SELECT nb_periodo FROM c_periodo r, k_term t WHERE r.fl_periodo=t.fl_periodo AND t.fl_term=b.fl_term) terms ";
      $Query_pagado .= "FROM k_ses_pago a, k_term_pago b ";
      $Query_pagado .= "WHERE a.fl_term_pago = b.fl_term_pago AND  cl_sesion='$cl_sesion' ORDER BY b.fe_pago ";   
    }     
    $rs = EjecutaQuery($Query_pagado);
    $total_pagados = CuentaRegistros($rs);
    for($i=0; $row = RecuperaRegistro($rs); $i++) {
      $fl_term_pago_p = $row[0];
      $no_opcion_p = $row[1];
      $no_pago_p = $row[2];
      $fe_limite_pago_p = $row[3];
      $mn_pago_p = $row[4];
      $ds_metodo_pago_p = $row[5];      
      $fe_pago_p = $row[6];
      $mn_pagado_p = $row[7];
      $ds_comentario_det_p = $row[8];
      $fl_alumno_pago_p = $row[9];
      $cl_metodo_pago_det_p = $row[10];
      $fg_refund_p = $row[11];
      $no_dias = $row[12];
      $earned = Number_format(round($row[13]),2,'.',',');
      $unearned = Number_format(round($row[14]),2,'.',',');
      $e_u = $row[15];
      $terms = $row[16];
      # Validamos si el fg_refund ya se realizo no se podra volver a realizar y estara en colo rojo
      if(empty($fg_refund_p))
        $onclick = "<a href='javascript:realizar_refund($clave,$pago_final,$fg_inscrito,$no_pago_p);' title='Refund payment'>Refund</a>";
       #fg_refund pagos que se regresan
      if($cl_metodo_pago_det_p >0 AND empty($fg_refund_p) AND $pago_final == $fl_alumno_pago_p)
        $refund = "<td>$onclick </td>";
      else{
        $refund = "<td> </td>";
        if(!empty($fg_refund_p))
          $refund = "<td style='color:red; font-weight:bold;'>Refund</td>";
      }
      
      #ultimo pago
      if($pago_final == $fl_alumno_pago_p AND $cl_metodo_pago_det_p > 0)       
        $borrar = "<td><a href=javascript:pago('borrar_payment.php',$clave,$pago_final,$fg_app_frm);><img src=".PATH_HOME."/images/icon_delete.gif title='Delete last payment'></a></td>";
      else
        $borrar = "<td></td>";
      # numero de pago 
      $numero_pago = $i + 1;
      
      # podremos cambiar el motodo de pago
      $ds_metodo_pago_p = "<a href='javascript:dialogo_refund($clave,$fl_alumno_pago_p,$fg_inscrito,$numero_pago,\"M\");' title='Change payment method'>$ds_metodo_pago_p</a>";
      # Podremos Cambiar la fecha de pago
      $fe_pago_p = "<a href='javascript:dialogo_refund($clave,$fl_alumno_pago_p,$fg_inscrito,$numero_pago,\"F\");' title='Change payment date'>$fe_pago_p</a>";
      # Podemos modificar el comentario
      $ds_comentario_det_p = "<a href='javascript:dialogo_refund($clave,$fl_alumno_pago_p,$fg_inscrito,$numero_pago,\"C\");' title='Change payment method'>$ds_comentario_det_p</a>";
      
      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      if($i==0)
        $clase = "css_tabla_detalle_bg";
      
      echo "
        <tr class='$clase' align='center'>
          <td>$terms</td>
          <td>$numero_pago</td>
          <td>$fe_limite_pago_p</td>
          <td>$mn_pago_p</td>
          <td>$fe_pago_p</td>
          <td>$mn_pagado_p</td>
          <td>$earned</td>
          <td>$unearned</td>
          <td>$e_u</td>
          <td>$ds_metodo_pago_p</td>
          <td align='left'>$ds_comentario_det_p</td>
          <td><a href='".PATH_CAMPUS."/students/invoice.php?f=$fl_term_pago_p&pago=$no_pago_p&destino=payments_frm.php&fl_sesion=$fl_sesion&n_pago=$numero_pago'><img src='".PATH_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>";
          echo " 
            ".$borrar."
            ".$refund."
        </tr>";
      $pagos_realizados++;
    }
    # Verificamos si repitio el grado
    $Query3 = "SELECT no_grado FROM k_alumno_term a, k_term b WHERE a.fl_term=b.fl_term and fl_alumno=$clave  ";
    $rs3 = EjecutaQuery($Query3);
    $r = '';
    $repetido = 0 ;
    for($i=0; $row3=RecuperaRegistro($rs3); $i++){
      if($r == $row3[0])
        $repetido++;
      $no_grado_re = $row3[0];
      $r = $no_grado_re;
    }
    
    # Obtenemos el term inicial actual
    $Query  = "SELECT fl_term_ini FROM k_term WHERE fl_programa=$fl_programa AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    $fl_term_ini = $row[0];
    if(empty($fl_term_ini))
      $fl_term_ini=$fl_term;
    # Obtenemos el term inicial cuando se incribio 
    if($fg_inscrito==1 AND $fg_app_frm==0){
      $row = RecuperaValor("SELECT MIN(fl_term) FROM k_alumno_term WHERE fl_alumno=$clave ");
      $term_ini = $row[0];
      # Si el alumno no tiene grupo o un term inscrito el term_ini es el term en que se incribio
      if(empty($term_ini) AND !ExisteEnTabla('k_alumno_term', 'fl_alumno',$clave))
        $term_ini = $fl_term;
      
      # Si el term inicial incrito es igual al term inicial actual entonces el term inicial es el incrito
      if($term_ini==$fl_term_ini){
        $fl_term_ini=$term_ini;
        if(!empty($pagos_realizados))
          $pagos_extras = "AND no_pago>$pagos_realizados ";
      }
      else{ # si el term incrito es diferente del term actual entonces el term inicial es el actual
        $fl_term_ini = $fl_term_ini;
        # Obtenemos el total de pagos y numeros de term para identificar los meses que cubre un term
        $row1 = RecuperaValor("SELECT no_grados, $no_x_payments FROM c_programa a, k_programa_costos k WHERE a.fl_programa=k.fl_programa AND a.fl_programa =$fl_programa");
        $no_grados = $row1[0];
        $no_x_payments = $row1[1];
        if($repetido>0)
          $meses_x_term = ($no_x_payments/$no_grados)*$repetido;
        else
          $meses_x_term = $no_x_payments/$no_grados;
        # Obtenemos el total se pagos realizados y si recursa un term tendran que haber pagos extras de los 
        $pagos_extras = "AND no_pago>$pagos_realizados-$meses_x_term ";
      }
    }

    # Datos de pagos que no se han realizado 
    $Query  = "SELECT fl_term_pago, no_opcion, no_pago, DATE_FORMAT(fe_pago,'".FORMAT_DATE."'), $mn_due, DATEDIFF(a.fe_pago, '$fe_actual') no_dias ";
    $Query .= ",(SELECT nb_periodo FROM c_periodo l, k_term s WHERE l.fl_periodo=s.fl_periodo AND s.fl_term=a.fl_term) terms ";
    $Query .= "FROM k_term_pago a, k_app_contrato b WHERE fl_term=$fl_term_ini ";
    $Query .= "AND no_opcion=$fg_opcion_pago AND no_contrato=1 AND cl_sesion='$cl_sesion' $pagos_extras ";
    $Query .= "ORDER BY no_pago ";
    $rs = EjecutaQuery($Query);
    for($i=0; $row = RecuperaRegistro($rs); $i++) {
      $fl_term_pago = $row[0];
      $no_opcion = $row[1];
      $no_pago = $row[2];
      if($fl_term_ini!=$term_ini){
        if($repetido>0)
          $no_pago_lista = ($pagos_realizados + 1) + $i;
        else
          $no_pago_lista = $pagos_realizados + $i;
      }
      else
        $no_pago_lista = $no_pago;
      $fe_limite_pago = $row[3];
      $mn_pago = $row[4];
      $no_dias = $row[5];
      $terms = $row[6];
    
      //para obtener los pagos
      $concat = array(ConsultaFechaBD('fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('fe_pago', FMT_HORA));
      if(empty($fg_app_frm)){
        $Query  = "SELECT fl_term_pago, ";
        $Query .= "CASE cl_metodo_pago ";
        $Query .= "WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
        $Query .= "END ds_metodo_pago, ";
        $Query .= "(".ConcatenaBD($concat).") fe_pago, mn_pagado, ds_comentario, fl_alumno_pago, cl_metodo_pago,fg_refund, ";
        $Query .= "(SELECT nb_periodo FROM c_periodo b, k_term_pago d, k_term e WHERE b.fl_periodo=e.fl_periodo AND d.fl_term=e.fl_term AND d.fl_term_pago=a.fl_term_pago) nb_periodo ";
        $Query .= "FROM k_alumno_pago a ";
        $Query .= "WHERE fl_term_pago=$fl_term_pago ";
        $Query .= "AND fl_alumno=$clave";
      }
      else {
        $Query  = "SELECT fl_ses_pago, ";
        $Query .= "CASE cl_metodo_pago ";
        $Query .= "WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
        $Query .= "END ds_metodo_pago, ";
        $Query .= "(".ConcatenaBD($concat).") fe_pago, mn_pagado, ds_comentario, fl_ses_pago, cl_metodo_pago,fg_refund "; 
        $Query .= "FROM k_ses_pago ";
        $Query .= "WHERE fl_term_pago=$fl_term_pago ";
        $Query .= "AND cl_sesion='$cl_sesion'";
      }
      
      $row = RecuperaValor($Query);
      $fl_t_pago = $row[0];
      $ds_metodo_pago = $row[1];
      if(empty($ds_metodo_pago)) 
        $ds_metodo_pago = "(To be paid)";
      $fe_pago = $row[2];
      if(empty($fe_pago)) 
        $fe_pago = "(To be paid)";
      $mn_pagado = $row[3];
      if(empty($mn_pagado)) 
        $mn_pagado = "(To be paid)";
      $ds_comentario_det = str_uso_normal($row[4]);
      $fl_alumno_pago = $row[5];
      $cl_metodo_pago_det = $row[6];
      $fg_refund = $row[7];
      
      if(empty($fl_t_pago)) {
        if(empty($proximo_pago)) {
          $pinta_pdf=false;
          $proximo_pago=$fl_term_pago;
          $no_opcion_pagar=$no_opcion;
          $no_pago_pagar=$no_pago;
          $no_pago_pagar_mostrar=$pagos_realizados+1;
          $fe_limite_pago_pagar=$fe_limite_pago;
          # Validamos si los dias son menores 0(paso fecha) paga late fee, si son mayores o igual a  0 pago normal
          if($no_dias < 0)
            $late_fee = ObtenConfiguracion(66);
          $mn_due_pagar=$mn_pago;
        }
      }
      else
        $pinta_pdf = true;

      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg ";  
      if($i == 0)
        $clase = "css_tabla_detalle";
      # Para los alumnos incritos solo obtendra los pagos que hacen faltan por pagar
      # Para los alumnos que no se han inscrito obtendra tanto pagos realizados como lo que hacen falta
      if((empty($fl_t_pago) AND $fg_inscrito==1 AND $fg_app_frm==0) OR (empty($fl_t_pago) AND $fg_inscrito==0 AND $fg_app_frm==1)){
        echo "
            <tr class='$clase' align='center'>
              <td>$terms</td>
              <td>$no_pago_lista</ td>
              <td>$fe_limite_pago</td>
              <td>$mn_pago</td>
              <td>$fe_pago</td>
              <td>$mn_pagado</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>$ds_metodo_pago</td>
              <td align='left'>$ds_comentario_det</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>";
      }
    }
    Forma_Tabla_Fin( );
    Forma_Espacio( );
    # Si pago antes de ser estudiante y no aparece su pago
    # Enotnces mostrar un mensage que hace falta la opcion de pago
    if(empty($fg_opcion_pago) AND empty($ds_firma_alumno) AND empty($fe_firma))
      Forma_CampoInfo('',ObtenMensaje(219));
    # div del dialogo para fg_redund
    echo "
    <div id='dialog'>
      <div id='ds_mensaje'></div>
    </div>"; 
    
    # Pago manual
    if(!empty($proximo_pago)){
      #envia parametros
      Forma_CampoOculto('fl_term_pago', $proximo_pago);
      Forma_CampoOculto('mn_pago',$mn_pago);
      Forma_CampoOculto('no_pago', $no_pago_pagar);
      Forma_CampoOculto('fl_programa', $fl_programa);
      Forma_CampoOculto('fg_opcion_pago', $fg_opcion_pago);
      
      #informacion del pago proximo
      Forma_Seccion('Manual payment');
      Forma_CampoInfo(ObtenEtiqueta(486), $mn_due_pagar);
      # Si contiene Late fee colocamos el campos para el mismo, si no no muestra ni envia nada
      if(!empty($late_fee))
        Forma_CampoTexto(ObtenEtiqueta(703),False,'mn_late_fee',$mn_late_fee, 10,10,'');
      Forma_CampoInfo(ObtenEtiqueta(481), $no_pago_pagar_mostrar);
      Forma_CampoInfo(ObtenEtiqueta(485), $fe_limite_pago_pagar);
      //$fe_fecha = date('d-m-Y');
      Forma_CampoTexto(ObtenEtiqueta(374).' '.ETQ_FMT_FECHA, True, 'fe_fecha', $fe_fecha, 10, 10, $fe_fecha_err);
      Forma_Calendario('fe_fecha');
      $pagos = array('Pay Pal Manual', 'Cheque', 'Credit Card', 'Wire Transfer/Deposit','Cash');
      $num = array('2', '3', '4', '5','6');
      Forma_CampoSelect(ObtenEtiqueta(483), True, 'cl_metodo_pago', $pagos, $num, $cl_metodo_pago, $cl_metodo_pago_err, True);
      Forma_CampoTexto('Cheque', False, 'ds_cheque', $ds_cheque, 255, 30,'');
      Forma_CampoTexto(ObtenEtiqueta(72), False, 'ds_comentario', $ds_comentario,255,60,'');
      Forma_Espacio( );
      Forma_CampoCheckbox('* Confirm manual payment', 'fg_realizar', $fg_realizar, '', '', True);
       # existe un error al no seleccionar el checkbox
      if(!empty($fg_realizar_err)){
        $ds_error = ObtenMensaje($fg_realizar_err);
        echo "<tr> 
                <td align='right' valign='middle' class='css_prompt'></td>
                <td align='left' valign='middle' class='css_input_error' style='color:#F00; font-weight: bold;'>$ds_error</td>
              </tr>";
      }
    }
  }
  else{ //no es un alumno, es una forma de aplicacion o es un alumno que no ha pagado su App fee

      # Recupera la informacion del usuario
      $Query  = "SELECT CONCAT(ds_fname, ' ', ds_mname, ' ', ds_lname) ds_nombre, CONCAT(nb_programa,' (',ds_duracion,')'), mn_app_fee mn_pagado_app1, DATE_FORMAT(d.fe_ultmod,'".FORMAT_DATE."') fe_ultmod1 ";
      $Query .= "FROM k_ses_app_frm_1 a, c_programa b, k_app_contrato c, c_sesion d ";
      $Query .= "WHERE d.cl_sesion='$cl_sesion' AND a.fl_programa=b.fl_programa AND a.cl_sesion=c.cl_sesion AND a.cl_sesion=d.cl_sesion AND no_contrato=1";
      $row = RecuperaValor($Query);
      $ds_nombre = str_texto($row[0]);
      $nb_programa = str_texto($row[1]);
      $mn_pagado_app1 = $row[2];
      $fe_ultmod1 = $row[3];

        
    #Recupera su App fee pagado
    $Query  = "SELECT CASE cl_metodo_pago WHEN 1 THEN 'Paypal' WHEN 2 THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' "; 
    $Query .= "WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END cl_metodo_app, (CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago_app, ";
    $Query .= "mn_pagado mn_pagado_app,". ConsultaFechaBD('b.fe_ultmod',FMT_FECHA) ." fe_ultmod1, ds_comentario ds_comentario_app , fl_sesion ";
    $Query .= "FROM c_sesion a, k_ses_app_frm_1 b ";
    $Query .= "WHERE a.cl_sesion='$cl_sesion' and b.cl_sesion='$cl_sesion' ";
    
    # Se visualiza el detalle del alumno 
    Forma_CampoInfo(ObtenEtiqueta(380), $nb_programa);
    Forma_CampoInfo(ETQ_NOMBRE, $ds_nombre);
    Forma_Espacio( );
    
    $titulos = array(ObtenEtiqueta(481).'|center', ObtenEtiqueta(485).'|center', ObtenEtiqueta(486).'|center',
                   ObtenEtiqueta(374).'|center', ObtenEtiqueta(596).'|center', ObtenEtiqueta(483).'|center', ObtenEtiqueta(72),'',);
    $ancho_col = array('5%', '15%', '15%', '15%', '15%', '15%', '17%','3%');
    Forma_Tabla_Ini('95%', $titulos, $ancho_col);
    echo "
        <tr style='font-weight:bold;' align='center'>
          <td>Once</td>
          <td>$fe_ultmod1</td>
          <td>$mn_pagado_app1</td>
          <td>(To be paid)</td>
          <td>(To be paid)</td>
          <td>(To be paid)</td>
          <td align='left'>&nbsp;</td>
          <td></td>";
    echo "
        </tr>";
    Forma_Tabla_Fin( );
    Forma_Espacio( );
    
    # Forma para realizar el pago
    Forma_Seccion('App Fee Manual Payment');
    Forma_CampoInfo(ObtenEtiqueta(486), $mn_pagado_app1);
    Forma_CampoOculto('mn_pagado_app',$mn_pagado_app1);
    Forma_CampoTexto(ObtenEtiqueta(374).' '.ETQ_FMT_FECHA, True, 'fe_fecha', $fe_fecha, 10, 10, $fe_fecha_err);
    Forma_Calendario('fe_fecha');
    $pagos = array('Pay Pal Manual', 'Cheque', 'Credit Card', 'Wire Transfer/Deposit','Cash');
    $num = array('2', '3', '4', '5', '6');
    Forma_CampoSelect(ObtenEtiqueta(483), True, 'cl_metodo_pago', $pagos, $num, $cl_metodo_pago, '', false);
    Forma_CampoTexto('Cheque', False, 'ds_cheque', $ds_cheque, 255, 30, '');
    Forma_CampoTexto(ObtenEtiqueta(72), False, 'ds_comentario', $ds_comentario, 255, 60, '');
    Forma_Espacio( );
    Forma_CampoCheckbox('* Confirm manual payment', 'fg_realizar', $fg_realizar,'', '', True);
    # existe un error al no seleccionar el checkbox
    if(!empty($fg_realizar_err)){
      $ds_error = ObtenMensaje($fg_realizar_err);
      echo "<tr> 
              <td align='right' valign='middle' class='css_prompt'></td>
              <td align='left' valign='middle' class='css_input_error' style='color:#F00; font-weight: bold;'>$ds_error</td>
            </tr>";
    }
  }

  
  Forma_CampoOculto('fg_app_frm', $fg_app_frm);
  Forma_CampoOculto('fg_pago', $fg_pago);
  Forma_CampoOculto('cl_sesion', $cl_sesion);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE AND !empty($proximo_pago))
    $fg_guardar = ValidaPermiso(FUNC_PAGOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
  #Sscript para el dialgo del refund y la confirmacion
  # Agregamos un type R refund y M cambio de metodo de pago
  echo '<script>
  function realizar_refund(clave,pago_final,fg_inscrito,no_pago){
    dialogo_refund(clave,pago_final,fg_inscrito,no_pago);
  }
  function dialogo_refund(clave,pago_borrar,fg_inscrito,no_pago,type="R"){
      if(type=="C")
        weight=400;
      else
        weight=350;
      $("#dialog").dialog({
        width: weight,
        height: 160,
      });
      div_refund(clave,pago_borrar,fg_inscrito,no_pago,type);
  }
  </script>';
  
  #Script  para la cofirmacion de borrar un pago 
     echo "
     <script>
      function pago(url,clave,borrar,fg_app_frm) {
      var answer = confirm('".str_ascii(ObtenMensaje(MSG_ELIMINAR))."');
      if(answer) {
        document.parametros.clave.value  = clave;
        document.parametros.borrar.value  = borrar;
        document.parametros.fg_app_frm.value  = fg_app_frm;
        document.parametros.action = url;
        document.parametros.submit();
      }
		}
    </script>
    <form name=parametros method=post>
      <input type=hidden name=clave>
      <input type=hidden name=borrar>
      <input type=hidden name=fg_app_frm>
    </form>\n";
?>