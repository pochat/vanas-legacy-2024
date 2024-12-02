<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recupera el usuario actual
  ValidaSesion( );
  
  # Recibe Parametros Numericos
  $fl_maestro = RecibeParametroNumerico('fl_maestro');
  $fe_periodo = RecibeParametroHTML('fe_periodo');
  $fg_error = RecibeParametroNumerico('fg_error');
  $accion = RecibeParametroHTML('accion');
  
  # Obtenemos el fl_maestro_pago si existen
  $row = RecuperaValor("SELECT fl_maestro_pago, mn_total, fg_publicar, fg_pagado, fe_pagado FROM k_maestro_pago WHERE fl_maestro=$fl_maestro AND DATE_FORMAT(fe_periodo,'%m-%Y')='".$fe_periodo."'");
  $fl_maestro_pago = $row[0];
  $mn_total = "<b>$</b>".$row[1];
  $fg_publicar = $row[2];
  $fg_pagado = $row[3];
  $fe_pagado = $row[4];
  if(empty($fl_maestro_pago))
    $fl_maestro_pago_ac = $fl_maestro;
  else
    $fl_maestro_pago_ac = $fl_maestro_pago;
  # Dependiendo de la accion recibida
  switch($accion){
    case 'insert':      
      EjecutaQuery("INSERT INTO k_maestro_pago_det(fl_maestro_pago, fg_tipo,fl_grupo,ds_concepto,mn_tarifa_hr, no_horas) VALUES($fl_maestro_pago_ac,'M',0,'Extra', 0.00,0.00)");
    break;
    case 'update':
      $ds_concepto = RecibeParametroHTML('ds_concepto');
      $mn_tarifa_hr = RecibeParametroFlotante('mn_tarifa_hr');
      $no_horas = RecibeParametroFlotante('no_horas');
      $fl_maestro_pago_det = RecibeParametroNumerico('fl_maestro_pago_det');
      $mn_subtotal = $mn_tarifa_hr * $no_horas;
      $Query  = "UPDATE k_maestro_pago_det SET fl_maestro_pago=$fl_maestro_pago_ac, ds_concepto='$ds_concepto', mn_tarifa_hr=$mn_tarifa_hr, ";
      $Query .= "no_horas=$no_horas, mn_subtotal=$mn_subtotal WHERE fl_maestro_pago=$fl_maestro_pago_ac AND fl_maestro_pago_det=$fl_maestro_pago_det";      
      EjecutaQuery($Query);
    break;
    case 'delete':
      $fl_maestro_pago_det = RecibeParametroNumerico('fl_maestro_pago_det');   
      EjecutaQuery("DELETE FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago_ac AND fl_maestro_pago_det=$fl_maestro_pago_det AND fg_tipo='M'");
    break;// Insertamos la clase para saber si la hizo o no
    case 'subtract':
      $row_subtract = RecibeParametroNumerico('row_subtract');
      $fl_grupo_sub = RecibeParametroNumerico('fl_grupo_sub');
      $ds_concepto_sub = RecibeParametroNumerico('ds_concepto_sub');
      $mn_tarifa_hr_sub = RecibeParametroHTML('mn_tarifa_hr_sub');
      $vcheckbox = RecibeParametroNumerico('vcheckbox');
      $fl_maestro_pago_det = RecibeParametroNumerico('maestro_pago');
      $type_clase = RecibeParametroHTML('type_clase');
      if(!empty($vcheckbox))
        $subtotal = $mn_tarifa_hr_sub;
      else
        $subtotal = 0;
      if(empty($fl_maestro_pago)){
        $row_sub = RecuperaValor("SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det WHERE fg_tipo='".$type_clase."' AND fl_grupo=".$fl_grupo_sub." AND ds_concepto='".$ds_concepto_sub."' ");
        if(!empty($row_sub[2])){
          $Query  = "UPDATE k_maestro_pago_det SET mn_subtotal='".$subtotal."', fg_subtract_class='".$vcheckbox."' WHERE fl_maestro_pago_det=".$row_sub[2]."";
        }else{
          $Query  = "INSERT INTO k_maestro_pago_det(fl_maestro_pago, fg_tipo,fl_grupo,ds_concepto,mn_tarifa_hr, no_horas,mn_subtotal,fg_subtract_class) ";
          $Query .= "VALUES($fl_maestro,'".$type_clase."',$fl_grupo_sub,'$ds_concepto_sub', '$mn_tarifa_hr_sub',1,'".$subtotal."','".$vcheckbox."') ";
        }
      }
      else{
        $Query =  "UPDATE k_maestro_pago_det SET mn_subtotal='".$subtotal."', fg_subtract_class='".$vcheckbox."' WHERE fg_tipo='".$type_clase."' AND fl_maestro_pago_det=".$fl_maestro_pago_det."";
      }
      // echo $Query;
      EjecutaQuery($Query);
      
  }
  # Si ya se realizo el pago ya no podra modificar nada
  if($fg_pagado){
    $readonly = "readonly";
    $disabled = "disabled";
  }
 # Encabezado de la tabla datos automaticos
  $titulos = array(ObtenEtiqueta(718),ObtenEtiqueta(716),ObtenEtiqueta(717),ObtenEtiqueta(719),ObtenEtiqueta(720)
                  ,ObtenEtiqueta(721),ObtenEtiqueta(722),ObtenEtiqueta(735),ObtenEtiqueta(723),ObtenEtiqueta(724),"");
  Forma_Tabla_Ini('90%', $titulos, array("","","","","","","","","","",""));

  # Obtenemos los grupos que imparte el maestro en el periodo que se selecciona
  $Query  = "SELECT no_semana, ds_titulo,".ConsultaFechaBD('d.fe_clase', FMT_FECHA).", CASE d.fg_adicional WHEN '0' THEN '".ObtenEtiqueta(714)."' ELSE '".ObtenEtiqueta(715)."' END ds_descripion, ";
  $Query .= "a.nb_grupo, e.nb_programa,(SELECT nb_periodo FROM c_periodo j WHERE j.fl_periodo=f.fl_periodo) nb_periodo, ";
  $Query .= "CASE d.fg_adicional WHEN '0' THEN IFNULL((SELECT t.mn_lecture_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_maestro),e.mn_lecture_fee) ";
  $Query .= "ELSE IFNULL((SELECT t.mn_extra_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_maestro),e.mn_extra_fee) END hourly_rate ";
  $Query .= ",a.fl_grupo,e.fl_programa, CASE a.no_alumnos WHEN 0 
    THEN (SELECT COUNT(1) FROM k_alumno_historia f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1')
    ELSE a.no_alumnos END no_alumnos, ";
  // $Query .= "(SELECT COUNT(1) FROM k_alumno_grupo f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1'), ";
  // $Query .= "(SELECT COUNT(1) FROM k_alumno_historia f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1'), ";
  $Query .= "d.fl_clase  ";
  $Query .= "FROM c_grupo a, k_clase d, c_programa e, k_term f ,k_semana b LEFT JOIN c_leccion c ON(c.fl_leccion=b.fl_leccion) ";
  $Query .= "WHERE a.fl_term = b.fl_term AND a.fl_grupo=d.fl_grupo AND b.fl_semana=d.fl_semana AND c.fl_programa = e.fl_programa ";
  $Query .= "AND c.fl_programa=e.fl_programa AND a.fl_term = f.fl_term AND b.fl_term = f.fl_term AND DATE_FORMAT(d.fe_clase,'%m-%Y')='".$fe_periodo."' ";
  $Query .= "AND a.fl_maestro=$fl_maestro ";
  # El grupo debe tener estudiantes 
  //$Query .= "AND (SELECT COUNT(1) FROM k_alumno_grupo f, c_usuario e WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1')>0 ";
  $Query .= "ORDER BY d.fe_clase ";
  $rs = EjecutaQuery($Query);
  $tot_aut_nor = CuentaRegistros($rs);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $no_semana = $row[0];
    $ds_titulo = $row[1];
    $fe_clase = $row[2];
    $ds_descripion = $row[3];
    $nb_grupo = $row[4];
    $nb_programa = $row[5];
    $nb_periodo = $row[6];
    $hourly_rate = $row[7];
    $amount = $hourly_rate*1;    
    $no_alumnos = $row[10];
    $fl_clase = $row[11];
    $checked = "checked";
    # Si el periodo de ese grupo ya no esta activado muestra los registros  aunque no tengan alumnos
    // $Query0  = "SELECT fg_activo  FROM c_grupo gr JOIN k_term ter ON(ter.fl_term=gr.fl_term) ";
    // $Query0 .= "JOIN c_periodo per ON(per.fl_periodo=ter.fl_periodo) WHERE gr.fl_grupo=".$row[8]." ";
    // $row0 = RecuperaValor($Query0);
    // $periodo_activo = $row0[0];
    // if((!empty($no_alumnos) AND !empty($periodo_activo) OR (empty($no_alumnos) AND empty($periodo_activo)))){
    // if(!empty($no_alumnos)){
      # Si alguna clase ya esta registrada con el grupo del maestro entonces 
      # el monto de esa clase sera el de la BD 
      $Query2  = "SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det ";
      $Query2 .= "WHERE fg_tipo='A' AND fl_grupo=".$row[8]." AND ds_concepto='".$fl_clase."'";
      $row_sub2 = RecuperaValor($Query2);
      if(!empty($row_sub2[2])){
        $hourly_rate = $row_sub2[0];
        $amount = $row_sub2[0];      
        if(!empty($row_sub2[1]))
          $checked = "checked";
        else
          $checked = " ";
        $fl_maestro_pago_det = $row_sub2[2];
      }        
      /*Si existe algun error podemos comentar esta parte*/
      /*if(!empty($fl_maestro_pago)){  //comentado el 28_ago_2018 no se podia deschekerar un registro., no pasa nada si volvemos a colocar
        $row1 = RecuperaValor("SELECT p.mn_tarifa_hr, p.mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det p WHERE p.fg_tipo='A' AND p.fl_maestro_pago=$fl_maestro_pago AND p.ds_concepto=$fl_clase");
        $hourly_rate = number_format($row1[0],2,'.',',');
        $amount = $row1[1];
        $fg_subtract_class = $row1[2];
        $fl_maestro_pago_det = $row1[3];
        if(!empty($fg_subtract_class) OR $fg_subtract_class==1){
          $checked = "checked";
        }
        else{
          $checked = " ";
        }
      }
      */
      $total_aut_nor += $amount;
      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";

      echo "
      <tr class='$clase'>
        <td>".$fe_clase."</td>
        <td align='center'>".$no_semana."</td>
        <td>".$ds_titulo."</td>   
        <td>".$ds_descripion."</td>
        <td>".$nb_grupo."</td>
        <td>".$nb_programa."</td>
        <td>".$nb_periodo."</td>
        <td align='center'>".$no_alumnos."</td>
        <td align='center'>$ ".number_format($hourly_rate,2,'.',',')."</td>
        <td align='center'>$ ".number_format($amount,2,'.',',')."</td>
        <td align='center'>
          <div class='checkbox'><label><input type='checkbox' class='checkbox' name='subtract_class".$i."' id='subtract_class".$i."' $checked $disabled onclick=\"subtract_class('".$i."','".$row[8]."','".$fl_clase."','".$row[7]."','$fl_maestro_pago_det');\"><span></span></label></div>
        </td>
      </tr>";
      Forma_CampoOculto('fl_grupo_aut'.$i, $row[8]);
      Forma_CampoOculto('mn_tarifa_hr_aut'.$i, $hourly_rate);
      Forma_CampoOculto('mn_subtotal_aut'.$i, $amount);  
      Forma_CampoOculto('fl_clase'.$i, $fl_clase);
    // }
  }
  # Muestra las sessiones de las clases globales
  # monto por default
  $mn_cglobal_fee = ObtenConfiguracion(96);
  $Querycg  = "SELECT kcg.no_orden, kcg.ds_titulo, ".ConsultaFechaBD('kcg.fe_clase', FMT_FECHA).", 'Global Class' ds_descripion, cg.ds_clase ds_clase_global, ";
  $Querycg .= "IFNULL((SELECT kmt.mn_cglobal_fee FROM k_maestro_tarifa_cg kmt WHERE kmt.fl_clase_global=cg.fl_clase_global AND kmt.fl_maestro=kcg.fl_maestro), ";
  $Querycg .= "'".$mn_cglobal_fee."') mn_cglobal_fee, cg.fl_clase_global, cg.no_alumnos,  kcg.fl_clase_cg ";
  $Querycg .= "FROM c_clase_global cg ";
  $Querycg .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_maestro=$fl_maestro) ";
  $Querycg .= "WHERE DATE_FORMAT(kcg.fe_clase,'%m-%Y')='".$fe_periodo."'";
  $rcg = EjecutaQuery($Querycg);
  $tot_aut_cg = CuentaRegistros($rcg);
  $tot_aut = $tot_aut_nor + $tot_aut_cg;
  for($j=0;$row=RecuperaRegistro($rcg);$j++){
    $no_orden = $row[0];
    $ds_titulo = $row[1];
    $fe_clase = $row[2];
    $ds_descripion = $row[3];
    $ds_clase_global = $row[4];
    $nb_programa_sp = "";
    $nb_periodo_sp = "";
    $mn_cglobal_fee = $row[5];    
    $amount_sp = $mn_cglobal_fee*1;    
    $fl_clase_global = $row[6];
    $no_alumnos = $row[7];
    $fl_clase_cg = $row[8];
    
    # Si alguna clase ya esta registrada con la clase global
    # el monto de esa clase sera el de la BD 
    $Query2  = "SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det ";
    $Query2 .= "WHERE fg_tipo='ACG' AND fl_grupo=".$fl_clase_global." AND ds_concepto='".$fl_clase_cg."'";
    $row_sub2 = RecuperaValor($Query2);
    if(!empty($row_sub2[2])){
      $mn_cglobal_fee = $row_sub2[0];
      $amount_sp = $row_sub2[0];      
      if(!empty($row_sub2[1]))
        $checked = "checked";
      else
        $checked = " ";
      $fl_maestro_pago_det = $row_sub2[2];
    }
    
    $total_aut_sp += $amount_sp;
    if($i % 2 == 0)
      $clase = "css_tabla_detalle";
    else
      $clase = "css_tabla_detalle_bg";

    echo "
    <tr class='$clase'>
      <td>".$fe_clase."</td>
      <td align='center'>".$no_orden."</td>
      <td>".$ds_titulo."</td>   
      <td>".$ds_descripion."</td>
      <td>".$ds_clase_global."</td>
      <td>".$nb_programa_sp."</td>
      <td>".$nb_periodo_sp."</td>
      <td align='center'>".$no_alumnos."</td>
      <td align='center'>$ ".number_format($mn_cglobal_fee,2,'.',',')."</td>
      <td align='center'>$ ".number_format($amount_sp,2,'.',',')."</td>
      <td align='center'>
        <div class='checkbox'><label><input type='checkbox' class='checkbox' name='subtract_cg".$j."' id='subtract_cg".$j."' $checked $disabled 
        onclick=\"subtract_class('".$j."','".$fl_clase_global."','".$fl_clase_cg."','".$row[5]."','$fl_maestro_pago_det', 'ACG');\"><span></span></label></div>
      </td>
    </tr>";
    Forma_CampoOculto('fl_clase_global'.$j, $fl_clase_global);
    Forma_CampoOculto('mn_cglobal_fee'.$j, $mn_cglobal_fee); 
    Forma_CampoOculto('fl_clase_cg'.$j, $fl_clase_cg);
  }
  $total_aut = $total_aut_nor + $total_aut_sp;
  echo "
    <tr>
      <td colspan='10' class='css_prompt' align='right'><strong>".ObtenEtiqueta(726).":&nbsp;&nbsp;$ ".number_format($total_aut,2,'.',',')."</strong></td>
    </tr>";
  Forma_Tabla_Fin( );
  Forma_CampoOculto('tot_aut_nor', $tot_aut_nor);
  Forma_CampoOculto('tot_aut_cg', $tot_aut_cg);
  # Fin de la tabla datos automaticos
  # Inicio Tabla manual
  Forma_Espacio();
  $titulos2 = array(ObtenEtiqueta(719),ObtenEtiqueta(723),ObtenEtiqueta(727),ObtenEtiqueta(724),"");
  Forma_Tabla_Ini('100%', $titulos2, array("","","","",""), 'tbl_manual', False);
    // registros de la BD
    if(!empty($fl_maestro_pago))
      $fl_maestro=$fl_maestro_pago;
    $Query  = "SELECT ds_concepto, mn_tarifa_hr, no_horas, fl_grupo, mn_subtotal, fl_maestro_pago_det FROM k_maestro_pago_det ";
    $Query .= "WHERE fl_maestro_pago=$fl_maestro AND fg_tipo='M' ORDER BY fl_maestro_pago_det  ";
    $rs = EjecutaQuery($Query);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      $ds_concepto = str_texto($row[0]);
      $mn_tarifa_hr = $row[1];
      $no_horas = $row[2];
      $sub_total_man += $row[4];      
        
      $fl_maestro_pago_det = $row[5];
        
      # cuando hay error indica en el campo
      if($fg_error AND empty($ds_concepto))
        $clase_con = 'css_input_error';
      else
        $clase_con = 'form-control';
      if($fg_error AND $mn_tarifa_hr<=0)
        $clase_tar = 'css_input_error';
      else
        $clase_tar = 'form-control';
      if($fg_error AND $no_horas<=0)
        $clase_hrs = 'css_input_error';
      else
        $clase_hrs = 'form-control';
        
      echo "
      <tr>
        <td>";CampoTexto('ds_concepto'.$i, $ds_concepto, 255, 50, $clase_con, False, "$readonly onchange='update_row($i, $fl_maestro_pago_det)'");echo "</td>
        <td>";CampoTexto('mn_tarifa_hr'.$i, $mn_tarifa_hr, 10, 20, $clase_tar, False, "$readonly onchange='update_row($i, $fl_maestro_pago_det)'");echo "</td>
        <td>";CampoTexto('no_horas'.$i, $no_horas, 10, 20, $clase_hrs, False, "$readonly onchange='update_row($i, $fl_maestro_pago_det)'");echo "</td>
        <td style='text-align:center;'>$ ".number_format($mn_tarifa_hr*$no_horas,2,'.',',')."</td>";
        if(empty($fl_maestro_pago) OR empty($fg_pagado))
          echo "<td><a href='javascript:delete_row($fl_maestro_pago_det);'><img src='".PATH_IMAGES."/icon_delete.gif' title='Delete' record=''></a></td>";
        else
          echo "<td>&nbsp;</td>";      
      echo "        
      </tr>";
      Forma_CampoOculto('fl_maestro_pago_det'.$i, $row[5]);
      $tot_manual ++;
    }

  if(empty($fl_maestro_pago) OR empty($fg_pagado) OR !empty($fg_pagado)){
    echo "
      <tr><td colspan='4'>&nbsp;</td><td><a href='javascript:add_row();'><img src='".PATH_IMAGES."/icon_add.png' title='Add' record=''></a></td></tr>
      <tr>
        <td colspan='4' class='css_prompt' align='right'><strong>".ObtenEtiqueta(734).": &nbsp;&nbsp;$ ".number_format($sub_total_man,2,'.',',')."</strong></td>
        <td></td>
      </tr>";
    Forma_Tab_Fin(False);
  }

  Forma_CampoOculto('tot_manual', $tot_manual);
  Forma_CampoOculto('fl_maestro_pago', $fl_maestro_pago);
  Forma_Espacio();
  
  $mn_total = $total_aut + $sub_total_man;
  # update amount cuando recibe una accion
  if(!empty($fl_maestro_pago) AND !empty($accion))
    EjecutaQuery("UPDATE  k_maestro_pago SET mn_total=$mn_total WHERE fl_maestro_pago=$fl_maestro_pago ");
  
  echo "
  <table align='center'>
    <tr>
      <td class='css_prompt'  align='right'>&nbsp;</td>
      <td><div class='checkbox'><label>";CampoCheckbox('fg_publicar', $fg_publicar);echo "<span>".ObtenEtiqueta(728)."</span></label></div></td>
    </tr>
    <tr>
      <td class='css_prompt' align='right' >&nbsp;</td>
      <td><div class=checkbox''><label>";CampoCheckbox('fg_pagado', $fg_pagado);echo "<span>".ObtenEtiqueta(729)."</span></label></div></td>
    </tr>
    <tr>
      <td class='css_prompt' align='right'>".ObtenEtiqueta(730).":</td>
      <td>";if(!empty($fg_pagado))echo $fe_pagado; echo "</td>
    </tr>
    <tr>
      <td class='css_prompt' align='right'>".ObtenEtiqueta(731).":</td>
      <td> $ ".number_format($mn_total,2,'.',',')."</td>
    </tr>
  </table>";  
  if(!empty($fl_maestro_pago))
    EjecutaQuery("UPDATE k_maestro_pago SET mn_subtotal='".number_format($mn_total,2,'.',',')."' WHERE fl_maestro_pago=".$fl_maestro_pago."");
  Forma_CampoOculto('mn_total', $mn_total);
  
?>