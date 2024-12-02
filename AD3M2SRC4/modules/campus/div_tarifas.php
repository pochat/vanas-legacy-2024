<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recupera el usuario actual
  ValidaSesion();

  # Variable initialization
  $total_aut = 0;
  $total_aut_cg = 0;
  $total_aut_nor = 0;
  $tot_aut = 0;
  $tot_aut_cg = 0;
  $tot_aut_nor = 0;
  $disabled = "";

  # Recibe Parametros Numericos
  $fl_maestro = RecibeParametroNumerico('fl_maestro');
  $fe_periodo = RecibeParametroHTML('fe_periodo');
  $fg_error = RecibeParametroNumerico('fg_error');
  $accion = RecibeParametroHTML('accion');

  # Obtenemos el fl_maestro_pago si existen
  $row = RecuperaValor("SELECT fl_maestro_pago, mn_total, fg_publicar, fg_pagado, fe_pagado FROM k_maestro_pago WHERE fl_maestro=$fl_maestro AND DATE_FORMAT(fe_periodo,'%m-%Y')='".$fe_periodo."'");
  $fl_maestro_pago = !empty($row['fl_maestro_pago'])?$row['fl_maestro_pago']:NULL;
  $mn_total = "<b>$</b>".(!empty($row['mn_total'])?$row['mn_total']:NULL);
  $fg_publicar = !empty($row['fg_publicar'])?$row['fg_publicar']:NULL;
  $fg_pagado = !empty($row['fg_pagado'])?$row['fg_pagado']:NULL;
  $fe_pagado = !empty($row['fe_pagado'])?$row['fe_pagado']:NULL;
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
      $fe_periodf="01-".RecibeParametroFecha('fe_periodo');
      $fe_periodf=ValidaFecha($fe_periodf);
      //Algunos teacher no tienen registros entonces se generan.
      $aux = date('Y-m-d', strtotime("{$fe_periodf} + 1 month"));
      $fe_mes = date('Y-m', strtotime("{$aux} - 1 day"));
      $fe_fin_mes = date('Y-m-d', strtotime("{$aux} - 1 day"));
      $fl_clase=$_POST['ds_concepto_sub'];
      
      ############ For Debuging #############
      //echo "row:".$row_subtract." grupo:".$fl_grupo_sub." descripcion:".$ds_concepto_sub." tarifa:".$mn_tarifa_hr_sub." check:".$vcheckbox." pago_det:".$fl_maestro_pago_det." typo:".$type_clase;
      if(!empty($vcheckbox))
        $subtotal = $mn_tarifa_hr_sub;
      else
        $subtotal = 0;
      if(empty($fl_maestro_pago)){
          //Inserta.
        if($vcheckbox==1){
            //Verifica si esixte.
            $Query="SELECT fl_maestro_pago,DATE_FORMAT(fe_periodo,'%Y-%m')AS mes from k_maestro_pago where fl_maestro=$fl_maestro AND DATE_FORMAT(fe_periodo,'%Y-%m')='$last_day' ";
            $rop=RecuperaValor($Query);
            if(empty($rop[0])){
                $Query="INSERT INTO k_maestro_pago(fl_maestro,fe_periodo,mn_total,fg_publicar,fg_pagado,fe_pagado,fg_email)VALUES($fl_maestro,'$fe_fin_mes',$subtotal,'0','0',null,null) ";
                $fl_maestro_pago=EjecutaInsert($Query);
            }
        }

        $row_sub = RecuperaValor("SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det WHERE fg_tipo='".$type_clase."' AND fl_grupo=".$fl_grupo_sub." AND ds_concepto='".$ds_concepto_sub."' ");
        if(!empty($row_sub[2])){
          $Query  = "UPDATE k_maestro_pago_det SET mn_subtotal='".$subtotal."', fg_subtract_class='".$vcheckbox."' WHERE fl_maestro_pago_det=".$row_sub[2]."";
        }else{
          $Query  = "INSERT INTO k_maestro_pago_det(fl_maestro_pago, fg_tipo,fl_grupo,ds_concepto,mn_tarifa_hr, no_horas,mn_subtotal,fg_subtract_class) ";
          $Query .= "VALUES($fl_maestro,'".$type_clase."',$fl_grupo_sub,'$ds_concepto_sub', '$mn_tarifa_hr_sub',1,'".$subtotal."','".$vcheckbox."') ";
        }
      }
      else{
        $Query =  "UPDATE k_maestro_pago_det SET mn_subtotal=".$subtotal.", fg_subtract_class='".$vcheckbox."' WHERE fl_maestro_pago_det=".$fl_maestro_pago_det."";
        
        if(($type_clase=='A')&&(empty($vcheckbox))){
            $Query1="UPDATE k_clase SET mn_rate=0 WHERE fl_clase=$fl_clase ";
            EjecutaQuery($Query1);
        }
     
      }
      ############ For Debuging #############
      //echo "<br>".$Query;
      EjecutaQuery($Query);
      break;
  }
  # Si ya se realizo el pago ya no podra modificar nada
  if($fg_pagado){
    $readonly = "readonly";
    $disabled = "disabled";
  }

  # Encabezado de la tabla datos automaticos
  $titulos = array(ObtenEtiqueta(718),ObtenEtiqueta(716),ObtenEtiqueta(717),ObtenEtiqueta(719),ObtenEtiqueta(720)
                  ,ObtenEtiqueta(721),ObtenEtiqueta(722),ObtenEtiqueta(735),"Attendance",ObtenEtiqueta(723),ObtenEtiqueta(724),"");
  Forma_Tabla_Ini('100%', $titulos, array("8%","5%","10%","8%","15%","15%","8%","7%","7%","7%",""));

  ########## BEGIN OF LIVE SESSION (LECTURE) ############

  # Obtenemos los grupos que imparte el maestro en el periodo que se selecciona
  $Query  = "SELECT no_semana, ds_titulo,".ConsultaFechaBD('d.fe_clase', FMT_FECHA)." fe_clase, CASE d.fg_adicional WHEN '0' THEN '".ObtenEtiqueta(714)."' ELSE '".ObtenEtiqueta(715)."' END ds_descripion, ";
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
    $no_semana = $row['no_semana'];
    $ds_titulo = $row['ds_titulo'];
    $fe_clase = $row['fe_clase'];
    $ds_descripion = $row['ds_descripion'];
    $nb_grupo = $row['nb_grupo'];
    $nb_programa = $row['nb_programa'];
    $nb_periodo = $row['nb_periodo'];
    $hourly_rate = $row['hourly_rate'];
    $amount = $hourly_rate*1; 
    $fl_grupo = $row['fl_grupo']; // $row[8]
    $fl_programa = $row['fl_programa'];
    $no_alumnos = $row['no_alumnos'];
    $fl_clase = $row['fl_clase'];
    $disabled = "";

    if($fg_pagado){
      $readonly = "readonly";
      $disabled = "disabled";
    }

    #Recuperamos la tarifa de la clase:
    $Query="SELECT mn_rate FROM k_clase WHERE fl_clase=$fl_clase ";
    $rowt=RecuperaValor($Query);
    $hourly_rate=$rowt['mn_rate'];
    $amount=$rowt['mn_rate'];

    #esta tarifa defibe el default de los precios que vienen desde detalle de los teacher  mjd*/
    $Queryt="SELECT mn_hour_rate FROM c_maestro WHERE fl_maestro=$fl_maestro ";
    $rot=RecuperaValor($Queryt);
    $mn_tarifa_default=$rot['mn_hour_rate'];
	
    if(empty($hourly_rate)){
        if(!empty($mn_tarifa_default)){
            $amount=$mn_tarifa_default;
            $hourly_rate=$mn_tarifa_default;
            EjecutaQuery("UPDATE k_maestro_tarifa SET mn_lecture_fee=$amount WHERE fl_grupo=$fl_grupo AND fl_maestro=$fl_maestro  ");

            $Query="UPDATE k_clase SET mn_rate=$mn_tarifa_default WHERE fl_clase=$fl_clase ";
            EjecutaQuery($Query);

           # $Query="UPDATE k_maestro_pago_det SET mn_tarifa_hr=$amount, mn_subtotal=$amount  WHERE fg_tipo='A' AND fl_grupo=".$fl_grupo." AND ds_concepto='".$fl_clase."'  ";
           # EjecutaQuery($Query);
        }
    }
	
	

    # Si el periodo de ese grupo ya no esta activado muestra los registros  aunque no tengan alumnos
    # $Query0  = "SELECT fg_activo  FROM c_grupo gr JOIN k_term ter ON(ter.fl_term=gr.fl_term) ";
    # $Query0 .= "JOIN c_periodo per ON(per.fl_periodo=ter.fl_periodo) WHERE gr.fl_grupo=".$fl_grupo." ";
    # $row0 = RecuperaValor($Query0);
    # $periodo_activo = $row0[0];
    # if((!empty($no_alumnos) AND !empty($periodo_activo) OR (empty($no_alumnos) AND empty($periodo_activo)))){
    # if(!empty($no_alumnos)){
      # Si alguna clase ya esta registrada con el grupo del maestro entonces 
      # el monto de esa clase sera el de la BD

    
      $Query2  = "SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det ";
      $Query2 .= "WHERE fg_tipo='A' AND fl_grupo=".$fl_grupo." AND ds_concepto='".$fl_clase."'";
      $row_sub2 = RecuperaValor($Query2);

      if(!empty($row_sub2['fl_maestro_pago_det'])){
        # This variable commented because generates an error
        # $hourly_rate = $row_sub2['mn_subtotal'];
        #$amount = $row_sub2['mn_subtotal'];
        $fl_maestro_pago_det = $row_sub2['fl_maestro_pago_det'];      
        
        if(!empty($row_sub2['fg_subtract_class'])){ #se contabiliza
            $checked = "checked";
            
           
        }else{ #noo contabiliza
            $checked = "";
            $Query="UPDATE k_maestro_pago_det SET  mn_subtotal=0  WHERE fg_tipo='A' AND fl_grupo=".$fl_grupo." AND ds_concepto='".$fl_clase."'  ";
            EjecutaQuery($Query);

            #default 0.
            $amount=0;
            $hourly_rate=0;

        }
      }
	  
	if(($fl_maestro==584)&&($fl_grupo==1126)){
	
	    $amount=80;
        $hourly_rate=80;

	
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

      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";

      # Variable initialization, this is to show the tag "future" in future lessons
      $future_tag = (strtotime($fe_clase)<=strtotime(date('d-m-Y'))?NULL:"<small class='text-muted'><i>Future</i></small");
      
      # if the date is in the future the checked is disabled and the values are 0
      if($future_tag!=NULL){
        $checked = "";
        $disabled = "disabled";
        $amount = 0;
      }

      $Qclase = RecuperaValor("SELECT fl_live_session FROM k_live_session WHERE fl_clase=$fl_clase");
      $fl_live_session = $Qclase[0];
      if(empty($checked)){
          EjecutaQuery("UPDATE k_live_session_asistencia SET cl_estatus_asistencia='1' WHERE fl_live_session=$fl_live_session AND fl_usuario=$fl_maestro ");
      }else{
          EjecutaQuery("UPDATE k_live_session_asistencia SET cl_estatus_asistencia='2' WHERE fl_live_session=$fl_live_session AND fl_usuario=$fl_maestro ");
      }

      $Qassist = RecuperaValor("SELECT cl_estatus_asistencia, fe_asistencia FROM k_live_session_asistencia WHERE fl_live_session=$fl_live_session AND fl_usuario=$fl_maestro");

      $attendanceStatus = !empty($Qassist[0])?$Qassist[0]:NULL;
      $attendanceTimeStamp = !empty($Qassist[1])?substr($Qassist[1], -8):NULL;


      switch ($attendanceStatus) {
        case '1':
          $attendance="<small id='lecture_".$fl_live_session."' class='text-danger'>
                      <i>
                      <select  id='".$fl_live_session."' name='subtract_class".$i."' onchange='change_attendance_lecture(this.id, $fl_maestro, this.value, this.name,$fl_clase);' $disabled>
                      <option value='1' selected>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>
                      <small><i>$attendanceTimeStamp</i></small>";
          $amount = 0;
          //$checked = '';
          break;
        case '2':
          $attendance="<small id='lecture_".$fl_live_session."' class='text-success'>
                      <i>
                      <select id='".$fl_live_session."' name='subtract_class".$i."' onchange='change_attendance_lecture(this.id, $fl_maestro, this.value, this.name,$fl_clase);' $disabled>
                      <option value='1'>Absent</option>
                      <option value='2' selected>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>
                      <small><i>Joined at: </i><i>$attendanceTimeStamp</i></small>";
          //$checked = "";
          break;
        case '3':
          $attendance="<small id='lecture_".$fl_live_session."' class='text-warning'>
                      <i>
                      <select id='".$fl_live_session."' name='subtract_class".$i."' onchange='change_attendance_lecture(this.id, $fl_maestro, this.value, this.name,$fl_clase);' $disabled>
                      <option value='1'>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3' selected>Late</option>
                      </select>
                      </i>
                      </small>
                      <small><i>Joined at: </i><i>$attendanceTimeStamp</i></small>";
          $checked = "checked";
          break;
        default:
          $attendance="<small id='lecture_".$fl_live_session."' class='text-danger'>
                      <i>
                      <select id='".$fl_live_session."' name='subtract_class".$i."' onchange='change_attendance_lecture(this.id, $fl_maestro, this.value, this.name,$fl_clase);' $disabled>
                      <option value='1' selected>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>
                      <small><i>$attendanceTimeStamp</i></small>";
          //$amount = 0;
          //$checked = '';
          break;
      }
      
    $total_aut_nor += $amount;

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
        <td align='center'>".(strtotime($fe_clase)<=strtotime(date('d-m-Y'))?$attendance:$future_tag)."</td>
        <td align='center'>$ ".number_format($hourly_rate,2,'.',',')."</td>
        <td align='center'>$ ".number_format($amount,2,'.',',')."</td>
        <td align='center'>
          <div class='checkbox'><label><input type='checkbox' class='checkbox' name='subtract_class".$i."' id='subtract_class".$i."' $checked $disabled onclick=\"subtract_class('".$i."','".$fl_grupo."','".$fl_clase."','".$hourly_rate."','$fl_maestro_pago_det');\"><span></span></label></div>
        </td>
      </tr>";
      Forma_CampoOculto('fl_grupo_aut'.$i, $fl_grupo);
      Forma_CampoOculto('mn_tarifa_hr_aut'.$i, $hourly_rate);
      Forma_CampoOculto('mn_subtotal_aut'.$i, $amount);  
      Forma_CampoOculto('fl_clase'.$i, $fl_clase);
    // }
  }

  ########## END OF LIVE SESSION (LECTURE) ############

  ########## BEGIN OF GLOBAL CLASS (REVIEW) ############

  # Muestra las sessiones de las clases globales
  # monto por default
  $mn_cglobal_fee = ObtenConfiguracion(96);

  #se define la tarifa default por hora.
  $Queryt="SELECT mn_hour_rate_global_class FROM c_maestro WHERE fl_maestro=$fl_maestro ";
  $rot=RecuperaValor($Queryt);
  $mn_cglobal_fee=$rot['mn_hour_rate_global_class'];


  $Querycg  = "SELECT kcg.no_orden, kcg.ds_titulo, ".ConsultaFechaBD('kcg.fe_clase', FMT_FECHA).", 'Global Class' ds_descripion, cg.ds_clase ds_clase_global, ";
  $Querycg .= "IFNULL((SELECT kmt.mn_cglobal_fee FROM k_maestro_tarifa_cg kmt WHERE kmt.fl_clase_global=cg.fl_clase_global AND kmt.fl_maestro=kcg.fl_maestro), ";
  $Querycg .= "'".$mn_cglobal_fee."') mn_cglobal_fee, cg.fl_clase_global, cg.no_alumnos,  kcg.fl_clase_cg ";
  $Querycg .= "FROM c_clase_global cg ";
  $Querycg .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_maestro=$fl_maestro) ";
  $Querycg .= "WHERE DATE_FORMAT(kcg.fe_clase,'%m-%Y')='".$fe_periodo."'";

  $rcg = EjecutaQuery($Querycg);
  
  $tot_aut_cg = CuentaRegistros($rcg);

  $tot_aut = $tot_aut_nor + $tot_aut_cg;
  
  for($j=$i+1;$row=RecuperaRegistro($rcg);$j++){
    $no_orden = $row[0];
    $ds_titulo = $row[1];
    $fe_clase = $row[2];
    $ds_descripion = $row[3];
    $ds_clase_global = $row[4];
    $nb_programa_sp = "";
    $nb_periodo_sp = "";
    $mn_cglobal_fee = $row[5];    
    $amount_cg = $mn_cglobal_fee*1;    
    $fl_clase_global = $row[6];
    $no_alumnos = $row[7];
    $fl_clase_cg = $row[8];
    $disabled = "";

    if($fg_pagado){
      $readonly = "readonly";
      $disabled = "disabled";
    }
    
    if( ($fl_maestro==46) && ( ($fl_clase_cg >= 1497) && ($fl_clase_cg<=1500)) ){
         $mn_cglobal_fee =65;
         $amount_cg =65;

    }


    #Recuperamos la tarifa de la clase:
    $Query="SELECT mn_rate FROM k_clase_cg WHERE fl_clase_cg=$fl_clase_cg ";
    $rowt=RecuperaValor($Query);
    $mn_cglobal_fee=$rowt['mn_rate'];
    $amount_cg=$rowt['mn_rate'];

    # Si alguna clase ya esta registrada con la clase global
    # el monto de esa clase sera el de la BD 
    $Query2  = "SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det ";
    $Query2 .= "WHERE fg_tipo='ACG' AND fl_grupo=".$fl_clase_global." AND ds_concepto='".$fl_clase_cg."'";
    
    $row_sub2 = RecuperaValor($Query2);
    
    if(!empty($row_sub2[2])){
       
      if(empty($mn_cglobal_fee))
      $mn_cglobal_fee = $row_sub2[0];
      if(empty($amount_cg))
      $amount_cg = $row_sub2[0];
      
      $fl_maestro_pago_det = $row_sub2[2];
      if(!empty($row_sub2[1]))
        $checked = "checked";
      else
        $checked = "";
    }

    if($i % 2 == 0)
      $clase = "css_tabla_detalle";
    else
      $clase = "css_tabla_detalle_bg";

    # Variable initialization, this is to show the tag "future" in future lessons
    $future_tag = (strtotime($fe_clase)<=strtotime(date('d-m-Y'))?NULL:"<small class='text-muted'><i>Future</i></small");

    # if the date is in the future the checked is disabled and the values are 0
    if($future_tag!=NULL){
      $checked = "";
      $disabled = "disabled";
      $amount_cg = 0;
    }

    $Qclase_cg = RecuperaValor("SELECT fl_live_session_cg FROM k_live_sesion_cg WHERE fl_clase_cg=$fl_clase_cg ");

    $fl_live_session_cg = $Qclase_cg['fl_live_session_cg'];

    $Qassist_cg = RecuperaValor("SELECT cl_estatus_asistencia_cg, fe_asistencia_cg FROM k_live_session_asistencia_cg WHERE fl_live_session_cg=$fl_live_session_cg AND fl_usuario=$fl_maestro");

    $attendanceStatus_cg = !empty($Qassist_cg[0])?$Qassist_cg[0]:NULL;
    $attendanceTimeStamp_cg = !empty($Qassist_cg[1])?substr($Qassist_cg[1], -8):NULL;

      switch ($attendanceStatus_cg) {
      case '1':
        $attendance_cg="<small id='review_".$fl_live_session_cg."' class='text-danger'>
                    <i>
                    <select id='rev".$fl_live_session_cg."' name='subtract_cg".$j."' onchange='change_attendance_cg($fl_live_session_cg, $fl_maestro, this.value, this.name,$fl_clase_cg);' $disabled>
                    <option value='1' selected>Absent</option>
                    <option value='2'>Present</option>
                    <option value='3'>Late</option>
                    </select>
                    </i>
                    </small>
                    <small><i>$attendanceTimeStamp_cg</i></small>";
        $amount_cg = 0;
        $checked = '';
        break;
      case '2':
        $attendance_cg="<small id='review_".$fl_live_session_cg."' class='text-success'>
                    <i>
                    <select id='rev".$fl_live_session_cg."' name='subtract_cg".$j."' onchange='change_attendance_cg($fl_live_session_cg, $fl_maestro, this.value, this.name,$fl_clase_cg);' $disabled>
                    <option value='1'>Absent</option>
                    <option value='2' selected>Present</option>
                    <option value='3'>Late</option>
                    </select>
                    </i>
                    </small>
                    <small><i>Joined at: </i><i>$attendanceTimeStamp_cg</i></small>";
        $checked = "checked";
        break;
      case '3':
        $attendance_cg="<small id='review_".$fl_live_session_cg."' class='text-warning'>
                    <i>
                    <select id='rev".$fl_live_session_cg."' name='subtract_cg".$j."' onchange='change_attendance_cg($fl_live_session_cg, $fl_maestro, this.value, this.name,$fl_clase_cg);' $disabled>
                    <option value='1'>Absent</option>
                    <option value='2'>Present</option>
                    <option value='3' selected>Late</option>
                    </select>
                    </i>
                    </small>
                    <small><i>Joined at: </i><i>$attendanceTimeStamp_cg</i></small>";
        $checked = "checked";
        break;
      default:
        $attendance_cg="<small id='review_".$fl_live_session_cg."' class='text-danger'>
                    <i>
                    <select id='rev".$fl_live_session_cg."' name='subtract_cg".$j."' onchange='change_attendance_cg($fl_live_session_cg, $fl_maestro, this.value, this.name,$fl_clase_cg);' $disabled>
                    <option value='1' selected>Absent</option>
                    <option value='2'>Present</option>
                    <option value='3'>Late</option>
                    </select>
                    </i>
                    </small>
                    <small><i>$attendanceTimeStamp_cg</i></small>";
        $amount_cg = 0;
        $checked = '';
        break;
    }

    $total_aut_cg += $amount_cg;

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
      <td align='center'>".(strtotime($fe_clase)<=strtotime(date('d-m-Y'))?$attendance_cg:$future_tag)."</td>
      <td align='center'>$ ".number_format($mn_cglobal_fee,2,'.',',')."</td>
      <td align='center'>$ ".number_format($amount_cg,2,'.',',')."</td>
      <td align='center'>
        <div class='checkbox'><label><input type='checkbox' class='checkbox' name='subtract_cg".$j."' id='subtract_cg".$j."' $checked $disabled 
        onclick=\"subtract_class('".$j."','".$fl_clase_global."','".$fl_clase_cg."','".$mn_cglobal_fee."','$fl_maestro_pago_det', 'ACG');\"><span></span></label>
        </div>
      </td>
    </tr>";
    Forma_CampoOculto('fl_clase_global'.$j, $fl_clase_global);
    Forma_CampoOculto('mn_cglobal_fee'.$j, $mn_cglobal_fee); 
    Forma_CampoOculto('fl_clase_cg'.$j, $fl_clase_cg);
  }

  $total_aut = $total_aut_nor + $total_aut_cg;
  
  // echo "
  //   <tr>
  //   <td></td><td><td>
  //     <td colspan='10' class='css_prompt' align='right'><strong>".ObtenEtiqueta(726).":&nbsp;&nbsp;$ ".number_format($total_aut,2,'.',',')."</strong></td>
  //   </tr>";
  // Forma_Tabla_Fin( );
  // Forma_CampoOculto('tot_aut_nor', $tot_aut_nor);
  // Forma_CampoOculto('tot_aut_cg', $tot_aut_cg);
  
  // #Presntamos la asistencia de los claeses a grupos globales.
  // Forma_Espacio();

  // $titulos = array(ObtenEtiqueta(718),ObtenEtiqueta(716),ObtenEtiqueta(717),ObtenEtiqueta(719),ObtenEtiqueta(720)
  //                 ,ObtenEtiqueta(721),ObtenEtiqueta(722),ObtenEtiqueta(735),"Attendance",ObtenEtiqueta(723),ObtenEtiqueta(724),"");
  // Forma_Tabla_Ini('100%', $titulos, array("7%","5%","11%","8%","14%","17%","8%","7%","7%","7%",""));

  ########## END OF GLOBAL CLASS (REVIEW) ############

  ######### BEGIN OF GLOBAL GROUP (Global) ##############

  $Querygg  = "SELECT  b.no_semana,a.nb_clase ds_titulo,".ConsultaFechaBD('a.fe_clase', FMT_FECHA)." fe_clase, 'Group_Class', (SELECT min( mn_cgrupo) FROM k_maestro_tarifa_gg WHERE fl_clase_grupo=a.fl_clase_grupo) mn_clase,a.fl_clase_grupo,(SELECT COUNT(*) FROM k_alumno_grupo d WHERE d.fl_grupo=c.fl_grupo )no_alumnos,c.fl_grupo,c.nb_grupo FROM k_clase_grupo a JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo JOIN c_grupo c ON c.fl_grupo=a.fl_grupo WHERE a.fl_maestro=$fl_maestro AND DATE_FORMAT(a.fe_clase,'%m-%Y')='".$fe_periodo."' ";

  $rgg = EjecutaQuery($Querygg);

  $tot_aut_gg = CuentaRegistros($rgg);

  $total_gg=0;

  for($k=$j+1;$row=RecuperaRegistro($rgg);$k++){
      $no_orden = $row['no_semana'];
      $ds_titulo = $row['ds_titulo'];
      $fe_clase = $row['fe_clase'];
      $ds_descripion = $row['Group_Class'];
      $mn_cglobal_fee_gg = !empty($row['mn_clase'])?$row['mn_clase']:ObtenConfiguracion(96);    
      $amount_gg = $mn_cglobal_fee_gg*1;;    
      $fl_clase_grupal = $row['fl_clase_grupo'];
      $no_alumnos = $row['no_alumnos'];
      $fl_grupo_gg = !empty($row['fl_grupo'])?$row['fl_grupo']:$row['fl_clase_grupo'];
      $fl_clase_gg = $row['nb_grupo'];
      $ds_clase_grupal =$row['nb_grupo'];
      $disabled = "";

      if($fg_pagado){
        $readonly = "readonly";
        $disabled = "disabled";
      }
      
      #Recupermaos todos los periodos que incluye el programa.
      $concat = array('nb_programa', "' ('", 'ds_duracion', "')'", "' - '", 'nb_periodo', "' - ".ObtenEtiqueta(375)." '", 'no_grado');
      $Querysv="SELECT a.fl_term,a.fl_grupo,b.fl_programa,b.fl_periodo,".ConcatenaBD($concat)." 'nb_term',nb_programa,no_grado     
                FROM k_grupo_term a
                JOIN k_term b ON a.fl_term=b.fl_term 
                JOIN c_programa c ON c.fl_programa=b.fl_programa
                JOIN c_periodo d ON d.fl_periodo=b.fl_periodo
                WHERE a.fl_grupo=$fl_grupo_gg  ";

      $rsm=EjecutaQuery($Querysv);
      
      $total_terms=CuentaRegistros($rsm);
      
      $periodos= NULL;
      $lessons= NULL;
      $no_grados= NULL;
      
      for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
          //$fl_terms_i = $rowm[0];        
          $periodos.=substr($rowm['nb_term'], -6, 6)."<br>";
          if ($lessons != $rowm['nb_programa']."<br>") {
            $lessons.=$rowm['nb_programa']."<br>";
          }
          $no_grados.=$rowm['no_grado']."<br>";
      }


      
      #Recuperamos la tarifa de la clase:
      $Query="SELECT mn_rate FROM k_clase_grupo WHERE fl_clase_grupo=$fl_clase_grupal ";
      $rowt=RecuperaValor($Query);
      $mn_cglobal_fee_gg=$rowt['mn_rate'];
      $amount_gg=$rowt['mn_rate'];


      $Query2  = "SELECT mn_subtotal, fg_subtract_class, fl_maestro_pago_det FROM k_maestro_pago_det ";
      $Query2 .= "WHERE fg_tipo='AG' AND fl_grupo=".$fl_grupo_gg." AND ds_concepto='".$fl_clase_grupal."'";
      
      $row_sub2 = RecuperaValor($Query2);
      
      if(!empty($row_sub2[2])){
          if(empty($mn_cglobal_fee_gg))
              $mn_cglobal_fee_gg = $row_sub2[0];
          if(empty($amount_gg))
            $amount_gg = $row_sub2[0];
            
        
        if(!empty($row_sub2[1]))
          $checked = "checked";
        else
          $checked = "";
          $fl_maestro_pago_det = $row_sub2[2]; 
      }
      ############################################################################

      if($i % 2 == 0)
          $clase = "css_tabla_detalle";
      else
          $clase = "css_tabla_detalle_bg";

      # Variable initialization, this is to show the tag "future" in future lessons
      $future_tag = (strtotime($fe_clase)<=strtotime(date('d-m-Y'))?NULL:"<small class='text-muted'><i>Future</i></small");

      # if the date is in the future the checked is disabled and the values are 0
      if($future_tag!=NULL){
        $checked = "";
        $disabled = "disabled";
        $amount_gg = 0;
      }

      $Qclase_grupal = RecuperaValor("SELECT fl_live_session_grupal FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupal ");

      $fl_live_session_grupal = $Qclase_grupal['fl_live_session_grupal'];

      $Qassist_grupal = RecuperaValor("SELECT cl_estatus_asistencia_gg, fe_asistencia_gg FROM k_live_session_asistencia_gg WHERE fl_live_session_gg=$fl_live_session_grupal AND fl_usuario=$fl_maestro");

      $attendanceStatus_gg = !empty($Qassist_grupal[0])?$Qassist_grupal[0]:NULL;
      $attendanceTimeStamp_gg = !empty($Qassist_grupal[1])?substr($Qassist_grupal[1], -8):NULL;

      switch ($attendanceStatus_gg) {
        case '1':
          $attendance_gg="<small id='global_".$fl_live_session_grupal."' class='text-danger'>
                      <i>
                      <select id='glob".$fl_live_session_grupal."' name='subtract_gg".$k."' onchange='change_attendance_gg($fl_live_session_grupal, $fl_maestro, this.value, this.name,$fl_clase_grupal);' $disabled>
                      <option value='1' selected>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>
                      <small><i>$attendanceTimeStamp_gg</i></small>";
          $amount_gg = 0;
          $checked = "";
          break;
        case '2':
          $attendance_gg="<small id='global_".$fl_live_session_grupal."' class='text-success'>
                      <i>
                      <select id='glob".$fl_live_session_grupal."' name='subtract_gg".$k."' onchange='change_attendance_gg($fl_live_session_grupal, $fl_maestro, this.value, this.name,$fl_clase_grupal);' $disabled>
                      <option value='1'>Absent</option>
                      <option value='2' selected>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>
                      <small><i>Joined at: </i><i>$attendanceTimeStamp_gg</i></small>";
          $checked = "checked";
          break;
        case '3':
          $attendance_gg="<small id='global_".$fl_live_session_grupal."' class='text-warning'>
                      <i>
                      <select id='glob".$fl_live_session_grupal."' name='subtract_gg".$k."' onchange='change_attendance_gg($fl_live_session_grupal, $fl_maestro, this.value, this.name,$fl_clase_grupal);' $disabled>
                      <option value='1'>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3' selected>Late</option>
                      </select>
                      </i>
                      </small>
                      <small><i>Joined at: </i><i>$attendanceTimeStamp_gg</i></small>";
          $checked = "checked";
          break;
        default:
          $attendance_gg="<small id='global_".$fl_live_session_grupal."' class='text-danger'>
                      <i>
                      <select id='glob".$fl_live_session_grupal."' name='subtract_gg".$k."' onchange='change_attendance_gg($fl_live_session_grupal, $fl_maestro, this.value, this.name,$fl_clase_grupal);' $disabled>
                      <option value='1' selected>Absent</option>
                      <option value='2'>Present</option>
                      <option value='3'>Late</option>
                      </select>
                      </i>
                      </small>
                      <small><i>$attendanceTimeStamp_gg</i></small>";
          $amount_gg = 0;
          $checked = "";
          break;
      }
      
      $total_gg+=$amount_gg;
      
      $total_aut = $total_aut_nor+$total_aut_cg+$total_gg;

      echo "
      <tr class='$clase'>
      <td>".$fe_clase."</td>
      <td align='center'>".$no_orden."</td>
      <td align='center'>".$lessons."</td>
     
      <td>".$ds_descripion."</td>
      <td>".$ds_clase_grupal."</td>
      <td>".$ds_titulo."</td>
     <td>".$periodos."</td>
      <td align='center'>".$no_alumnos."</td>
      <td align='center'>".(strtotime($fe_clase)<=strtotime(date('d-m-Y'))?$attendance_gg:$future_tag)."</td> 
      <td align='center'>$ ".number_format($mn_cglobal_fee_gg,2)."</td>
      <td align='center'>$ ".number_format($amount_gg,2)." </td>
      
      <td align='center'>
       <div class='checkbox'><label><input type='checkbox' class='checkbox' name='subtract_gg".$k."' id='subtract_gg".$k."' $checked $disabled 
        onclick=\"subtract_class('".$k."','".$fl_grupo_gg."','".$fl_clase_grupal."','".$mn_cglobal_fee_gg."','$fl_maestro_pago_det', 'AG');\"><span></span></label>
        </div>
      </td>
    </tr>";
       Forma_CampoOculto('$fl_grupo_gg'.$k, $fl_grupo_gg);
      Forma_CampoOculto('mn_cglobal_fee_gg'.$k, $mn_cglobal_fee_gg); 
      Forma_CampoOculto('fl_clase_gg'.$k, $fl_clase_gg);
  }

  //$total_gg = $total_gg;
  // echo "
  //   <tr>
  //   <td></td><td></td><td><td>
  //    <td colspan='10' class='css_prompt' align='right'><strong>".ObtenEtiqueta(726).":&nbsp;&nbsp;$ ".number_format($total_gg,2,'.',',')."</strong></td>
  // </tr>";
  // Forma_Tabla_Fin( );
  // //Forma_CampoOculto('tot_aut_nor', $tot_aut_nor);
  // //Forma_CampoOculto('tot_aut_cg', $tot_aut_cg);
  // Forma_Espacio();

  // # Fin de la tabla datos automaticos
  // # Inicio Tabla manual
  // Forma_Espacio();
  // $titulos2 = array(ObtenEtiqueta(719),ObtenEtiqueta(723),ObtenEtiqueta(727),ObtenEtiqueta(724),"");
  // Forma_Tabla_Ini('100%', $titulos2, array("","","","",""), 'tbl_manual', False);

    ############### END OF TABLE SUM OF ALL AMOUNTS ############
    echo "
      <tr>
      <td></td><td><td>
        <td colspan='10' class='css_prompt' align='right'><strong>".ObtenEtiqueta(726).":&nbsp;&nbsp;$ ".number_format($total_aut,2,'.',',')."</strong></td>
      </tr>";
    Forma_Tabla_Fin( );
    Forma_CampoOculto('tot_aut_nor', $tot_aut_nor);
    Forma_CampoOculto('tot_aut_cg', $tot_aut_cg);
    Forma_CampoOculto('tot_aut_gg', $tot_aut_gg);
    
    #Presntamos la asistencia de los claeses a grupos globales.
    Forma_Espacio();

    $titulos = array(ObtenEtiqueta(718),ObtenEtiqueta(716),ObtenEtiqueta(717),ObtenEtiqueta(719),ObtenEtiqueta(720)
                    ,ObtenEtiqueta(721),ObtenEtiqueta(722),ObtenEtiqueta(735),"Attendance",ObtenEtiqueta(723),ObtenEtiqueta(724),"");
    Forma_Tabla_Ini('100%', $titulos, array("7%","5%","11%","8%","14%","17%","8%","7%","7%","7%",""));
    // registros de la BD
    if(!empty($fl_maestro_pago))
      $fl_maestro=$fl_maestro_pago;
    $Query  = "SELECT ds_concepto, mn_tarifa_hr, no_horas, fl_grupo, mn_subtotal, fl_maestro_pago_det FROM k_maestro_pago_det ";
    $Query .= "WHERE fl_maestro_pago=$fl_maestro AND fg_tipo='M' ORDER BY fl_maestro_pago_det  ";
    $rs = EjecutaQuery($Query);
    for($t=0;$row=RecuperaRegistro($rs);$t++){
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
        <td>";CampoTexto('ds_concepto'.$t, $ds_concepto, 255, 50, $clase_con, False, "$readonly onchange='update_row($t, $fl_maestro_pago_det)'");echo "</td>
        <td>";CampoTexto('mn_tarifa_hr'.$t, $mn_tarifa_hr, 10, 20, $clase_tar, False, "$readonly onchange='update_row($t, $fl_maestro_pago_det)'");echo "</td>
        <td>";CampoTexto('no_horas'.$t, $no_horas, 10, 20, $clase_hrs, False, "$readonly onchange='update_row($t, $fl_maestro_pago_det)'");echo "</td>
        <td style='text-align:center;'>$ ".number_format($mn_tarifa_hr*$no_horas,2,'.',',')."</td>";
        if(empty($fl_maestro_pago) OR empty($fg_pagado))
          echo "<td><a href='javascript:delete_row($fl_maestro_pago_det);'><img src='".PATH_IMAGES."/icon_delete.gif' title='Delete' record=''></a></td>";
        else
          echo "<td>&nbsp;</td>";      
      echo "        
      </tr>";
      Forma_CampoOculto('fl_maestro_pago_det'.$t, $row[5]);
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
  
  $mn_total = $total_aut+$sub_total_man;

  # update amount cuando recibe una accion
  if(!empty($fl_maestro_pago)) /*AND !empty($accion))*//*comentado siempre actuañizara los montos este venga o no la accion, la accion se activa cuando seleccionan unncheckbox en el frm */
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
