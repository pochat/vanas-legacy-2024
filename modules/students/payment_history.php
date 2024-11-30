<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  $cl_sesion = $_COOKIE[SESION_CAMPUS];
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Presenta contenido de la pagina
  $titulo = ObtenEtiqueta(690);
  PresentaHeader($titulo);
  
  # Recupera el programa y term que esta cursando el alumno
  $fl_programa = ObtenProgramaAlumno($fl_alumno);
  $fl_term = ObtenTermAlumno($fl_alumno);
  /*if(empty($fl_programa) AND empty($fl_term)){
    $row = RecuperaValor("SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion' ");
    $fl_programa= $row[0];
    $row = RecuperaValor("SELECT fl_term FROM k_alumno_term WHERE fl_alumno=$fl_alumno");
    $fl_term = $row[0];
  }*/
  
  # Recupera el term inicial
  $Query  = "SELECT fl_term_ini ";
  $Query .= "FROM k_term ";
  $Query .= "WHERE fl_programa=$fl_programa ";
  $Query .= "AND fl_term=$fl_term";
  $row = RecuperaValor($Query);
  $fl_term_ini = $row[0];
  
  # Recupera el tipo de pago para el curso
  $Query  = "SELECT fg_opcion_pago ";
  $Query .= "FROM k_app_contrato ";
  $Query .= "WHERE cl_sesion='$cl_sesion'"; 
  $row = RecuperaValor($Query);
  $fg_opcion_pago = $row[0];
  
  if(empty($fl_term_ini))
    $fl_term_ini=$fl_term;
  
  # Se obtiene la descripcion de la frecuencia del pago
  switch($fg_opcion_pago) {
    case 1:
      $mn_due='mn_a_due';
      $ds_frecuencia='ds_a_freq';
      $ds_pagos='no_a_payments';
      break;
    case 2:
      $mn_due='mn_b_due';
      $ds_frecuencia='ds_b_freq';
      $ds_pagos='no_b_payments';
      break;
    case 3:
      $mn_due='mn_c_due';
      $ds_frecuencia='ds_c_freq';
      $ds_pagos='no_c_payments';
      break;
    case 4:
      $mn_due='mn_d_due';
      $ds_frecuencia='ds_d_freq';
      $ds_pagos='no_d_payments';
      break;
  }
  $Query  = "SELECT $ds_frecuencia, $ds_pagos, no_semanas ";
  $Query .= "FROM k_programa_costos ";
  $Query .= "WHERE fl_programa=$fl_programa ";
  $row = RecuperaValor($Query);
  $ds_frecuencia = $row[0]; 
  $no_pagos_opcion = $row[1];
  $no_semanas = $row[2];
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td height='5'></td>
                    </tr>
                    <tr>
                      <td colspan='3' align='center' valign='top' height='80' style='padding: 20px 0 0 0;' class='division_line'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td colspan='2' height='10' class='blank_cells'></td>
                          </tr>";
  #Obtenemos al otro alumno con el mismo nombre y apellidos
  $row2 = RecuperaValor("SELECT ds_nombres, ds_apaterno, fg_genero, ds_login FROM c_usuario WHERE fl_usuario=$fl_alumno");
  $ds_nombres=$row2[0];
  $ds_apaterno=$row2[1];
  $fg_genero=$row2[2];
  $ds_login= $row2[3];

  # Busca si existen alumnos con el mismo nombre y apellidos
  $Query = "SELECT fl_usuario, CONCAT(ds_nombres,' ',ds_apaterno), ds_login FROM c_usuario WHERE ds_nombres='".$ds_nombres."' AND ds_apaterno='".$ds_apaterno."' AND ds_login <> '".$ds_login."' ";
  $rs = EjecutaQuery($Query);
  for($i=0;$row=RecuperaRegistro($rs);$i++) {
    if($i == 0)
      Forma_Seccion(ObtenEtiqueta(694));
    Forma_CampoInfo('', "<a href='".PAGINA_SALIR."'>".$row[1]."&nbsp;".$row[2]."</a>");
  }
  Forma_Espacio();
  
  #Tablas de pagos                     
  $titulos = array("<div style='font-weight:bold;'>".ObtenEtiqueta(481).'|center'."</div>","<div style='font-weight:bold;'>". ObtenEtiqueta(482).'|center'."</div>","<div style='font-weight:bold;'>". ObtenEtiqueta(485).'|center'."</div>",
                  "<div style='font-weight:bold;'>".ObtenEtiqueta(486).'|center'."</div>","<div style='font-weight:bold;'>".ObtenEtiqueta(374).'|center'."</div>","<div style='font-weight:bold;'>". ObtenEtiqueta(596).'|center'."</div>",
                  "<div style='font-weight:bold;'>".ObtenEtiqueta(483).'|center'."</div>","<div style='font-weight:bold;'>". ObtenEtiqueta(487).'|center'."</div>");
  $ancho_col = array('5%', '', '13%', '13%', '13%', '13%', '13%', '');
  Forma_Tabla_Ini('95%', $titulos, $ancho_col);
  
  //para obtener informacion del pago de app_fee
  $Query = "SELECT fl_sesion,  CASE cl_metodo_pago WHEN 1 THEN 'Paypal' WHEN 2 THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' ";
  $Query .= "WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END cl_metodo_pago, (CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, ";
  $Query .="CONCAT('$','',FORMAT(mn_pagado,2)),ds_comentario,fg_pago, ".ConsultaFechaBD('b.fe_ultmod',FMT_FECHA)." FROM c_sesion a, k_ses_app_frm_1 b, k_app_contrato c  WHERE a.cl_sesion='$cl_sesion' AND b.cl_sesion='$cl_sesion'  ";
  $row = RecuperaValor($Query);
  $fl_sesion =$row[0];
  $cl_metodo_app = $row[1];
  $fe_pago_app = $row[2];
  $mn_pagado_app = $row[3];
  $ds_comentario_app = $row[4];
  $fg_pago_app = $row[5];
  $fe_ultmod1 =  str_texto($row[6]);

  
  if(!empty($mn_pagado_app)){
    echo "
        <tr  align='center'>
          <td>Once</td>
          <td>Once</td>
          <td>".$fe_ultmod1."</td>
          <td>".$mn_pagado_app."</td>
          <td>".$fe_pago_app."</td>
          <td>".$mn_pagado_app."</td>
          <td>".$cl_metodo_app."</td>
          <td><a href='".PATH_CAMPUS."/students/invoice.php?fl_sesion=$fl_sesion' target='_blank'><img src='".PATH_ADM_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>";
    echo "
        </tr>";
  }
  #Obtenemos la fecha actual
  $fe_actual  = ObtenFechaActual();
  # Recupera informacion de los pagos
  $Query  = "SELECT fl_term_pago, no_opcion, no_pago, ".ConsultaFechaBD("fe_pago", FMT_FECHA).", DATEDIFF(DATE_FORMAT(fe_pago, '%Y-%m-%d'), '$fe_actual') no_dias ";
  $Query .= "FROM k_term_pago ";
  $Query .= "WHERE fl_term=$fl_term_ini ";
  $Query .= "AND no_opcion=$fg_opcion_pago";
  $rs = EjecutaQuery($Query);
  for($i=0; $row = RecuperaRegistro($rs); $i++) {
    $fl_term_pago = $row[0];
    $no_opcion = $row[1];
    $no_pago = $row[2];
    $fe_limite_pago = $row[3];
    $no_dias = $row[4];// dias con el que sabemos si la fecha limite se paso(no_dias<0 paga late fee, no_dias>0 pago normal de k_app_contrato)
    
    $Query  = "SELECT fl_term_pago, ";
    $Query .= "CASE cl_metodo_pago WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' ";
    $Query .= "WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash'  ";
    $Query .= "END cl_metodo_pago, ";
    $Query .= "(CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, CONCAT('$','',FORMAT(mn_pagado,2)), "; 
    $Query .= "DATEDIFF('".DATE_FORMAT(date_create($fe_limite_pago),'Y-m-d')."',fe_pago) dias ";
    $Query .= "FROM k_alumno_pago ";
    $Query .= "WHERE fl_term_pago=$fl_term_pago ";
    $Query .= "AND fl_alumno=$fl_alumno";
    $row = RecuperaValor($Query);
    $fl_t_pago = $row[0];
    $cl_metodo_pago = $row[1];
    if(empty($cl_metodo_pago)) 
      $cl_metodo_pago = "(To be paid)";
    $fe_pago = $row[2];
    if(empty($fe_pago)) 
      $fe_pago = "(To be paid)";
    $mn_pagado =$row[3];
    if(empty($mn_pagado)) 
      $mn_pagado = "(To be paid)";
    $dias = $row[4]; //si la fe_limite es menor a la fe_pago dias<0 pago latte fee 
    
    $Query  = "SELECT $mn_due ";
    $Query .= "FROM k_app_contrato ";
    $Query .= "WHERE cl_sesion='$cl_sesion'"; 
    $row = RecuperaValor($Query);
    $mn_due = $row[0];
    
    if(empty($fl_t_pago)) {
      if(empty($proximo_pago)){
        $pinta_pdf=false;
        $pay_now=true;
        $proximo_pago=$fl_term_pago;
        $no_opcion_pagar=$no_opcion;
        $no_pago_pagar=$no_pago;
        $fe_limite_pago_pagar=$fe_limite_pago;
        $mn_due_pagar=$mn_due;
      }
      else {
        $pay_now=false;
      }
    }
    else {
      $pinta_pdf=true;
      $pay_now=false;
    }
    
    # Si existe en la tabla pero la fecha pago fue despues de fe limite se le agrega el latte fee,
    # Si no existe en registro en k_alumno_pago y no dias es menor a 0 (fecha limite se paso) agrega el latte fee,
    # Si los dias nos mayores a 0(fecha limite no se ha pasado) obtiene el pago de k_app_contrato
    if(ExisteEnTabla('k_alumno_pago','fl_term_pago',$fl_term_pago) AND $dias<0)
      $mn_due_pagar = '$'.Number_Format($mn_due + ObtenConfiguracion(66),2,'.',',');
    else{
      if($no_dias<0 AND !ExisteEnTabla('k_alumno_pago', 'fl_term_pago', $fl_term_pago))
        $mn_due_pagar = '$'.Number_Format($mn_due + ObtenConfiguracion(66),2,'.',',');
      else
        
        $mn_due_pagar = '$'.$mn_due;
    }      
    if($i % 2 == 0)
      $clase = "css_tabla_detalle";
    else
      $clase = "css_tabla_detalle_bg";

    echo "
                  <tr class='$clase' align='center'>
                    <td>$no_pago</td>
                    <td>$ds_frecuencia</td>
                    <td>$fe_limite_pago</td>
                    <td>".$mn_due_pagar."</td>
                    <td>$fe_pago</td>
                    <td>".$mn_pagado."</td>
                    <td>$cl_metodo_pago</td>";  
    if($pinta_pdf){
      echo "
                    <td><a href='".PATH_CAMPUS."/students/invoice.php?f=$fl_term_pago&pago=$no_pago' target='_blank'><img src='".PATH_ADM_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>";
    } 
    else if($pay_now){
      echo "          <td><a href='tuition_payment.php'>Pay Now!</a></td>";
    }
    else {
      echo "          <td>&nbsp;</td>";
    }
    echo "        </tr>";
  }
  Forma_Tabla_Fin();


  # De la fecha inicial del curso hasta su fecha completado, obtenemos los años  jgfl
  $Query  = "SELECT ".ConsultaFechaBD('c.fe_inicio',FMT_FECHA).", nb_programa, fg_taxes, fg_desercion ";
  $Query .= "FROM k_term b, c_periodo c, k_alumno_term d, k_pctia e, c_programa f ";
  $Query .= "WHERE b.fl_periodo=c.fl_periodo AND b.fl_programa=f.fl_programa ";
  $Query .= "AND b.fl_term=d.fl_term AND d.fl_alumno=e.fl_alumno AND d.fl_alumno='$fl_alumno' ";
  $Query .= "AND no_grado=1 ";
  $row = RecuperaValor($Query);
  $fe_inicio_programa = $row[0];
  $nb_programa = $row[1];
  $fg_taxes = $row[2];
  $fg_desercion = $row[3];
  # Si no obtiene la fe_inicio_programa la traemos del term inicial
  if(empty($fe_inicio_programa) AND empty($nb_programa) AND empty($fg_taxes)){
    $Query  = "SELECT ".ConsultaFechaBD('b.fe_inicio',FMT_FECHA).",nb_programa, fg_taxes,fg_desercion ";
    $Query .= "FROM k_term a, c_periodo b, c_programa c, k_pctia d ";
    $Query .= "WHERE a.fl_periodo = b.fl_periodo AND a.fl_programa=c.fl_programa AND d.fl_alumno=$fl_alumno AND fl_term=$fl_term_ini ";
    $row = RecuperaValor($Query);
    $fe_inicio_programa = $row[0];
    $nb_programa = $row[1];
    $fg_taxes = $row[2];
    $fg_desercion = $row[3];
  }
  
  # Conocemos si el programa tiene permitido generar el taxes
  if(!empty($fg_taxes)){
    # Inicia tabla de los T220a y validamos si con el programa se realizan
    Forma_Espacio();
    Forma_Seccion('<div align="center">'.ObtenEtiqueta(692).'</div>');
    
    # Le sumamos lo numero de meses a la fecha inicial para obtener el fecha final
    # Calculamos la cantidad que se paga por mes
    $fe_inicio1 = DATE_FORMAT(date_create($fe_inicio_programa),'Y-m-d');
    $mes_inicio1 = DATE_FORMAT(date_create($fe_inicio_programa),'m');
    $anio_inicio1 = DATE_FORMAT(date_create($fe_inicio_programa),'Y');
    $meses = ($no_semanas/4);
    $fe_nueva = strtotime ( '+ '.($meses-1).' month' , strtotime ( $fe_inicio1 ) ) ;
    $fe_fin1 = date ( 'Y-m-d' , $fe_nueva );
    $mes_fin1 = date ( 'm' , $fe_nueva );
    $anio_fin1 = date ( 'Y' , $fe_nueva );
    $anios1 = $anio_fin1 - $anio_inicio1;
    $titulos = array('<b>'.ObtenEtiqueta(360).'</b>','<b>Year</b>','<b>Initial month</b>', '<b>Final month</b>','<b>'.ObtenEtiqueta(583).'</b>','');
    $ancho_col = array('20%','20%','20%','20%','5%');
    
    Forma_Tabla_Ini('85%', $titulos,$ancho);
    for($i=0;$i<=$anios1;$i++){
        $anios2=$anio_inicio1+$i;
        if($anios2<date('Y')){
          # Obtiene los meses que conforman el anio para el que se pago 
          if($anio_inicio1==$anio_fin1)
            $num_meses_anio=$mes_fin1-$mes_inicio1+1;
          else{
            $num_meses_anio = 12;
            if($anios2==$anio_fin1)
              $num_meses_anio = $mes_fin1;
            if($anios2==$anio_inicio1)
              $num_meses_anio = 12-$mes_inicio1+1;
          }
          
          # Obtenemos los meses que cubren lo pagos
          # Obtenemos su nombre para mostrarlos en la tabla
          if($anios2==$anio_inicio1){
            if($anio_inicio1==$anio_fin1){
              $mes_ini= $mes_inicio1;
              $mes_fin= $mes_fin1;
            }
            else{
              $mes_ini =$mes_inicio1;
              $mes_fin=12;
            }
          }
          else {
            $mes_ini =1;
            $mes_fin=$mes_fin1;
            if($anios2 != $anio_fin1)
              $mes_fin=12;
          }
          
          # Si el alumno se retiro antes de acabar el curso
          # Obtenemos el ultimo pago y hasta ahi sumamos las cantidades
          if(!empty($fg_desercion) AND ($anios2!=$anio_fin1 AND $anios2!=$anio_inicio1)){
            $Query = "SELECT DATE_FORMAT(fe_pago,'%m') FROM k_alumno_pago WHERE fl_alumno=$fl_alumno AND DATE_FORMAT(fe_pago, '%Y')='$anios2' order by fe_pago DESC ";
            $row = RecuperaValor($Query);
            $num_meses_anio = $row[0];
            $mes_fin = $row[0];
          }
          
          # Monto pagado en el anio
          $monto = ($mn_due / ($meses/$no_pagos_opcion)) * $num_meses_anio;
          $monto = number_format($monto,2,'.',',');
          
          $mes_ini = ObtenNombreMes($mes_ini);
          $mes_fin = ObtenNombreMes($mes_fin);
          # Datos de los taxes
          echo "
          <tr>
            <td>".$nb_programa."</td>
            <td>".$anios2."</td>
            <td>".$mes_ini."</td>
            <td>".$mes_fin."</td>
            <td align='center'>".$monto."</td>
            <td><a href='".PATH_CAMPUS."/students/taxes.php?anio=$anios2&fl_alumno=$fl_alumno&fl_term=$fl_term&num_meses_anio=$num_meses_anio&monto=$monto' target='_blank'><img src='".PATH_ADM_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>
          </tr>";
        }
      }
    Forma_Tabla_Fin();
  }
  
   echo "     
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='3' align='center' valign='top' height='80' class='division_line'>
                      </td>
                    </tr>";
  PresentaFooter( );
  
?>