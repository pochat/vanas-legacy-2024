<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../modules/liveclass/bbb_api.php';
  require '../../lib/zoom_config.php';
  include ('Sessionadobe.php');
  
  $fl_usuario = ObtenUsuario();
  
  # Recibe parametros
  $accion = RecibeParametroHTML('accion');
  $clave = RecibeParametroNumerico('clave');
  $ds_clase_d = RecibeParametroHTML('ds_clase_d');
  $fl_maestro_d = RecibeParametroNumerico('fl_maestro_d');
  $fg_dia_sesion_d = RecibeParametroNumerico('fg_dia_sesion_d');
  $fe_start_date_d = RecibeParametroHTML('fe_start_date_d');
  $hr_sesion_d = RecibeParametroHoraMin('hr_sesion_d');
  $fg_mandatory_d = RecibeParametroNumerico('fg_mandatory_d');
  $fg_error = RecibeParametroNumerico('variable');
  // echo "clase".$ds_clase_d."maestro".$fl_maestro_d."dia".$fg_dia_sesion_d."fecha".$fe_start_date_d."hora".$hr_sesion_d."manda".$fg_mandatory_d;
  $view_select = False;
  # Dependiendo a la accion
  switch($accion){
    case 'inserta':
      $row1 = RecuperaValor("SELECT MAX(fl_clase_cg) FROM k_clase_cg_temporal");
      $row0 = RecuperaValor("SELECT DATE_FORMAT(fe_clase,'%Y-%m-%d') FROM k_clase_cg_temporal WHERE fl_clase_cg=".$row1[0]."");
      $fe_clase_anterior = $row0[0];     
      if(!empty($fe_clase_anterior)){
        $anio_pub = substr($fe_clase_anterior, 0, 4);
        $mes_pub = substr($fe_clase_anterior, 5, 2);
        $dia_pub = substr($fe_clase_anterior, 8, 2);        
      }
      else{
        $anio_pub = substr($fe_start_date_d, -4);
        $mes_pub = substr($fe_start_date_d, 3, 2);
        $dia_pub = substr($fe_start_date_d, 0, 2);
      }
      $fe_publicacion = $dia_pub."-".$mes_pub."-".$anio_pub;
      $fe_publicacion = date_create( );
      date_date_set($fe_publicacion, $anio_pub, $mes_pub, $dia_pub);
      $dia_semana = date('N', date_format($fe_publicacion, 'U')); 
      if($fg_dia_sesion_d > $dia_semana)
        $dif_dias = $fg_dia_sesion_d - $dia_semana;
      else
        $dif_dias = 7 - $dia_semana + $fg_dia_sesion_d;
      date_modify($fe_publicacion, "+ $dif_dias day");
      $fe_clase_d = date_format($fe_publicacion, 'Y-m-d'); // Se toma como valor por omision la fecha de publicacion + n dias     
      $fe_clase_d = $fe_clase_d." ".$hr_sesion_d;
      
      //2021 se toma la fecha original del calendario del form
      //$fe_clase_d=strtotime('+0 day',strtotime($fe_start_date_d));
      //$fe_clase_d= date('Y-m-d',$fe_clase_d);
      //$fe_clase_d=$fe_clase_d." ".$hr_sesion_d;
      #Insertamos las fechas
      if(!empty($ds_clase_d) && !empty($fl_maestro_d) && $fg_dia_sesion_d>0 && !empty($fe_start_date_d) && !empty($hr_sesion_d)){
        $row = RecuperaValor("SELECT MAX(no_orden) FROM k_clase_cg_temporal");
        $no_orden = $row[0]+1;
        $Query  = "INSERT INTO k_clase_cg_temporal(fl_clase_global, no_orden, ds_titulo, fe_clase, fg_obligatorio, fl_maestro, fl_usuario) ";
        $Query .= "VALUES($clave, $no_orden, '$ds_clase_d', '$fe_clase_d', '$fg_mandatory_d', $fl_maestro_d, $fl_usuario)";
        EjecutaQuery($Query);
         echo "
        <script>
          var msg_error = $('#msg_error');
            $('#div_ds_clase').removeClass('has-error');
            $('#div_fl_maestrog').removeClass('has-error');
            $('#div_fg_dia_sesion').removeClass('has-error');
            $('#div_fe_start_date').removeClass('has-error');
            $('#div_hr_sesion').removeClass('has-error');
        </script>";        
      }else{
        echo "
        <div class='text-danger' id='msg_error'></div>
        <script>
        var msg_error = $('#msg_error');";
        if(empty($ds_clase_d))
          echo "$('#div_ds_clase').addClass('has-error'); msg_error.html('<label><strong>".ObtenMensaje(ERR_REQUERIDO)."</strong></label>');";
        if(empty($fl_maestro_d))
          echo "$('#div_fl_maestrog').addClass('has-error'); msg_error.html('<label><strong>".ObtenMensaje(ERR_REQUERIDO)."</strong></label>');";
        if($fg_dia_sesion_d==0 || $fg_dia_sesion_d == '')
          echo "$('#div_fg_dia_sesion').addClass('has-error'); msg_error.html('<label><strong>".ObtenMensaje(ERR_REQUERIDO)."</strong></label>');";
        if(empty($fe_start_date_d) || !ValidaHoraMin($fe_start_date_d))
          echo "$('#div_fe_start_date').addClass('has-error'); msg_error.html('<label><strong>".ObtenMensaje(ERR_REQUERIDO)."</strong></label>');";
        if(empty($hr_sesion_d))
          echo "$('#div_hr_sesion').addClass('has-error'); msg_error.html('<label><strong>".ObtenMensaje(ERR_REQUERIDO)."</strong></label>');";
        echo "</script>";
      }
    break;
    case 'borra':
      $fl_clase_cg = RecibeParametroNumerico('fl_clase_cg');
      
      #Recuperamos la clase para recuperar id de zoom
      $QueryDel  = "SELECT fl_live_session_cg,zoom_id,zoom_url ";
      $QueryDel .= "FROM k_live_sesion_cg WHERE fl_clase_cg = $fl_clase_cg";   
      $rowDel = RecuperaValor($QueryDel);
      $fl_live_session_cg = $rowDel[0];
      $zoom_id=$rowDel[1];
      $zoom_url=$rowDel[2];

      if((!empty($fl_live_session_cg))&&(!empty($zoom_id))){
          EjecutaQuery("UPDATE k_live_sesion_cg SET zoom_id=NULL,zoom_url=NULL WHERE fl_clase_cg = $fl_clase_cg ");

          #Eliminamos la clase de zoom
          DeletedMeetingZoom($fl_live_session_cg,'k_live_sesion_cg',$zoom_id);
      }


      # Eliminamos el registro de la clase en adobe
      delLiveSession($fl_clase_cg,True);
        
      // Borramos la clase de la BD
      $Query = "DELETE FROM k_clase_cg_temporal WHERE fl_clase_cg = $fl_clase_cg";
      EjecutaQuery($Query);    
      $Query = "DELETE FROM k_live_sesion_cg WHERE fl_clase_cg = $fl_clase_cg";
      EjecutaQuery($Query);
    break;
    case 'actualiza':
      $renglon = RecibeParametroNumerico('renglon');
      $fl_clase_cg = RecibeParametroNumerico('fl_clase_cg');
      $ds_titulo = RecibeParametroHTML('ds_titulo');
      $fl_maestro_clase = RecibeParametroNumerico('fl_maestro_clase');
      $fe_clase = RecibeParametroFecha('fe_clase');
      $hr_clase = RecibeParametroHoraMin('hr_clase');
      $fg_obliga = RecibeParametroHTML('fg_obliga');
      $fe_class = "'".ValidaFecha($fe_clase)." ".$hr_clase."'";      
      // echo "renglon:".$renglon."title:".$ds_titulo."fecha y hora".$fe_class."obliga:".$fg_obliga;
      if(!empty($ds_titulo) && !empty($fe_clase) && !empty($hr_clase)){
        

          #Recuperamos la clase para recuperar id de zoom
          $QueryDel  = "SELECT fl_live_session_cg,zoom_id,zoom_url ";
          $QueryDel .= "FROM k_live_sesion_cg WHERE fl_clase_cg = $fl_clase_cg";   
          $rowDel = RecuperaValor($QueryDel);
          $fl_live_session_cg = $rowDel[0];
          $zoom_id=$rowDel[1];
          $zoom_url=$rowDel[2];

          if((!empty($fl_live_session_cg))&&(!empty($zoom_id))){
          
              EjecutaQuery("UPDATE k_live_sesion_cg SET zoom_url=NULL, zoom_id=null WHERE fl_clase_cg = $fl_clase_cg  ");
              #Eliminamos la clase de zoom
              DeletedMeetingZoom($fl_live_session_cg,'k_live_sesion_cg',$zoom_id);
          }



        # Eliminamos el registro de la clase en adobe.
        //delLiveSession($fl_clase_cg,true);//2021 ya no se eleimna solo se actualiza los datos en bd como nulos.
        EjecutaQuery("UPDATE k_live_session SET zoom_password=NULL, zoom_url=NULL, zoom_meeting_id=NULL, zoom_host_id=NULL, zoom_id=NULL WHERE fl_clase=$fl_clase_cg ");
        # Actualizamos la informacion
        $Query  = "UPDATE k_clase_cg_temporal SET ds_titulo='$ds_titulo', fe_clase=$fe_class,  fg_obligatorio='$fg_obliga' ";  
        if(!empty($fl_maestro_clase))
          $Query .= ", fl_maestro=$fl_maestro_clase, fl_usuario=$fl_usuario ";
        $Query .= "WHERE fl_clase_cg = $fl_clase_cg ";
        EjecutaQuery($Query);
      }
      else{
        echo "
        <script>
          var msg_error = $('#msg_error');";
          if(empty($ds_titulo))
            echo "$('#div_ds_titulo_".$renglon."').addClass('has-error');";
          if(empty($fe_clase) && !ValidaFecha($fe_clase))
            echo "$('#div_fe_clase_".$renglon."').addClass('has-error');";
          if(empty($hr_clase))
            echo "$('#div_hr_clase_".$renglon."').addClass('has-error');";
        echo "
        </script>";
      }
    break;
    case 'change_teacher':
      $view_select = true;
      $renglon = RecibeParametroNumerico("reglon");
    break;
  }
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)){ // Actualizacion, recupera de la base de datos
      # Copiamos los registros a la clase temporal
      //$Queryc  = "INSERT INTO k_clase_cg_temporal (fl_clase_cg, fl_clase_global, no_orden,fe_clase,fg_obligatorio, fl_maestro, ds_titulo, fl_usuario) ";
      $Queryc = "SELECT fl_clase_cg, fl_clase_global, no_orden, fe_clase, fg_obligatorio, fl_maestro, ds_titulo, $fl_usuario FROM k_clase_cg WHERE fl_clase_global=$clave";
      $rstem = EjecutaQuery($Queryc);
      $count_class = 0;
      for($t = 0; $rowt = RecuperaRegistro($rstem); $t++){

          $count_class++;

          $fl_clase_cg = $rowt[0];
          $fl_clase_global=$rowt[1];
          $no_orden=$rowt[2];
          $fe_clase=$rowt[3];
          $fg_obligatorio=$rowt[4];
          $fl_maestro=$rowt[5];
          $ds_titulo=$rowt[6];
           
          $Queryci  = " INSERT INTO k_clase_cg_temporal (fl_clase_cg, fl_clase_global, no_orden,fe_clase,fg_obligatorio, fl_maestro, ds_titulo, fl_usuario) ";
          $Queryci .= " VALUES($fl_clase_cg, $fl_clase_global, $count_class, '$fe_clase', '$fg_obligatorio', $fl_maestro, '$ds_titulo', $fl_usuario ) ";
          EjecutaInsert($Queryci);
      


      }
      
      //EjecutaQuery($Queryc);




    }
    # Recupera las fechas de cada clase de la tabla temporal
    $QueryS  = "SELECT fl_clase_cg, no_orden, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
    $QueryS .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, ds_ruta_avatar, a.fl_maestro, ds_titulo ";
    $QueryS .= "FROM k_clase_cg_temporal a LEFT JOIN c_maestro b ON(b.fl_maestro=a.fl_maestro) WHERE /*fl_usuario=$fl_usuario AND*/ fl_clase_global=$clave ";      
    $QueryS .= "ORDER BY no_orden";
    $rsS = EjecutaQuery($QueryS);
    for($tot_semanas = 0; $row = RecuperaRegistro($rsS); $tot_semanas++) {
      $fl_clase_cg = $row[0];
      $no_orden = $row[1];
      $fe_clase = $row[2];
      $hr_clase = $row[3];
      $fg_obligatorio = $row[4];
      $ds_ruta_avatar = $row[5];
      $fl_maestro = $row[6];
      $ds_titulo = str_texto($row[7]);
    }
  }
  else{ //Recibe Parametros con error
    # Recupera las fechas de cada clase de la tabla temporal.
    $QueryS  = "SELECT fl_clase_cg, no_orden, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
    $QueryS .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, ds_ruta_avatar, a.fl_maestro, ds_titulo ";
    $QueryS .= "FROM k_clase_cg_temporal a LEFT JOIN c_maestro b ON(b.fl_maestro=a.fl_maestro) WHERE fl_usuario=$fl_usuario ";      
    $QueryS .= "ORDER BY no_orden";
    $rsS = EjecutaQuery($QueryS);
    for($tot_semanas = 0; $row = RecuperaRegistro($rsS); $tot_semanas++) {
      $fl_clase_cg = $row[0];
      $no_orden = $row[1];
      $fe_clase = $row[2];
      $hr_clase = $row[3];
      $fg_obligatorio = $row[4];
      $ds_ruta_avatar = $row[5];
      $fl_maestro = $row[6];
      $ds_titulo = str_texto($row[7]);
    }
  }
  
  
  $fg_error = False;
  $name = ObtenNombre($fl_usuario); 
  $SALT = ObtenConfiguracion(33);
  $URL = ObtenConfiguracion(32);
 ?>
  <div class="col col-xs-12 col-sm-12 col-lg-12 no-padding">
  <table class="display projects-table table table-striped table-hover dataTable no-footer">
    <thead>
      <tr>
        <th style="width: 5%;"><?php echo ObtenEtiqueta(1013); ?></th>
        <th style="width: 15%;"><?php echo ObtenEtiqueta(1009); ?></th>
        <th style="width: 15%;"><?php echo ObtenEtiqueta(1002); ?></th>
        <th style="width: 12%;"><?php echo ObtenEtiqueta(1010); ?></th>
        <th style="width: 15%;"><?php echo ObtenEtiqueta(1003); ?></th>
        <th style="width: 10%;"><?php echo ObtenEtiqueta(1004); ?></th>
        <th style="width: 10%;"><?php echo "Attendance"; ?></th>
        <th style="width: 8%;"><?php echo ObtenEtiqueta(1006); ?></th>
        <th style="width: 8%;">&nbsp;</th>
      </tr>
    </thead>
 <?php
  # Lista de las sesiones
 $rs = EjecutaQuery($QueryS);$licencia_ocupada=array();
  for($i = 0; $row = RecuperaRegistro($rs); $i++){
    $fl_clase_cg = $row[0];
    $no_orden = $row[1];
    $fe_clase = $row[2];
    $hr_clase = $row[3];
    $fg_obligatorio = $row[4];  
    $ds_ruta_avatar = $row[5];
    $fl_maestro = $row[6];
    $row2 = RecuperaValor("SELECT CONCAT(ds_nombres,' ', ds_apaterno) FROM c_usuario WHERE fl_usuario=$fl_maestro");
    $ds_nombres_maestro = str_texto($row2[0]);
    $ds_titulo = str_texto($row[7]);

    $Qclase_cg = RecuperaValor("SELECT `fl_live_session_cg` FROM `k_live_sesion_cg` WHERE `fl_clase_cg`=$fl_clase_cg ");

    $fl_live_session_cg = $Qclase_cg['fl_live_session_cg'];

    $Qassist_cg = RecuperaValor("SELECT cl_estatus_asistencia_cg FROM k_live_session_asistencia_cg WHERE fl_live_session_cg=$fl_live_session_cg AND fl_usuario=$fl_maestro");

    $statusAsistencia_cg = !empty($Qassist_cg[0])?$Qassist_cg[0]:NULL;

    $future = "<small class='text-warning'><i>Future</i></small";

      switch ($statusAsistencia_cg) {
      case '1':
        $attendance_cg="<small class='text-danger'>
                    <i>
                    <select id='".$fl_live_session_cg."' name='".($i+1)."' onchange='change_attendance_cg(this.id, $fl_maestro, this.value, this.name);'>
                    <option value='1' selected>Absent</option>
                    <option value='2'>Present</option>
                    <option value='3'>Late</option>
                    </select>
                    </i>
                    </small>";
        break;
      case '2':
        $attendance_cg="<small class='text-success'>
                    <i>
                    <select id='".$fl_live_session_cg."' name='".($i+1)."' onchange='change_attendance_cg(this.id, $fl_maestro, this.value, this.name);'>
                    <option value='1'>Absent</option>
                    <option value='2' selected>Present</option>
                    <option value='3'>Late</option>
                    </select>
                    </i>
                    </small>";
        break;
      case '3':
        $attendance_cg="<small class='text-warning'>
                    <i>
                    <select id='".$fl_live_session_cg."' name='".($i+1)."' onchange='change_attendance_cg(this.id, $fl_maestro, this.value, this.name);'>
                    <option value='1'>Absent</option>
                    <option value='2'>Present</option>
                    <option value='3' selected>Late</option>
                    </select>
                    </i>
                    </small>";
        break;
      default:
        $attendance_cg="<small class='text-danger'>
                    <i>
                    <select id='".$fl_live_session_cg."' name='".($i+1)."' onchange='change_attendance_cg(this.id, $fl_maestro, this.value, this.name);'>
                    <option value='1' selected>Absent</option>
                    <option value='2'>Present</option>
                    <option value='3'>Late</option>
                    </select>
                    </i>
                    </small>";
        break;
    }

    # Estilo de la fila
    if($i % 2 == 0)
      $clase = "css_tabla_detalle";
    else
      $clase = "css_tabla_detalle_bg";
    
    # Revisa si hay una clase global activa en este momento
    $Query  = "SELECT fl_live_session_cg, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
    $Query .= "FROM k_live_sesion_cg ";
    $Query .= "WHERE fl_clase_cg=".$fl_clase_cg;
    $row = RecuperaValor($Query);
    $fl_live_session = $row[0];
    $cl_estatus = $row[1];
    $ds_meeting_id = $row[2];
    $ds_password_asistente = $row[3];
    $zoom_url=$row['zoom_url']; 
    $zoom_id=$row['zoom_id'];

    #Recuperamos la cuenta
    $Query="SELECT host_email_zoom FROM zoom WHERE id=$zoom_id ";
    $row=RecuperaValor($Query);
    $ds_host_zoom=$row[0];

    if(!empty($fl_live_session) AND $cl_estatus == '1') {      
      // MDB ADOBECONNECT 
      $urlAdobeConnect = ObtenConfiguracion(53);
      $joinURL = $urlAdobeConnect . $ds_meeting_id . "/?guestName=Admin";
      $ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'><i class='fa fa-external-link'></i></a>";
    }
    else
      $ds_liga = "<i class='fa fa-external-link'></i>";
    
    # Licencias de Adobe
    $licenciaService = new LicenciaAdobeService();
    $clasesService = new ClasesService();
    $fechaHora = "'" . ValidaFecha($fe_clase) . ' ' . ValidaHoraMin($hr_clase) . "'";    
    $clave_clase = $fl_clase_cg;

    #Siemre se omiten las actuales ya que siempre se hacen refrencias a las tabla temporal.
    //if($accion=='new'){
    $fg_omitir_actuales=1;
    //}


    # Clases Traslapadas
    $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, $clave_clase);    
    $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, $clave_clase);    
    $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas, True);

    #Para zoom

    $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, $clave_clase,$fg_omitir_actuales,1);    
    $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, $clave_clase);    
    $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom, True);
    $licenciasZoomTotal = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);

    // var_dump($licenciasAdobe);
    $fe_clase_err = "";
    # Verificar hacer prubas y falta agregar las clases extras de los grupos
    // echo "clasesTraslapadas: ".$clasesTraslapadas."<br>licenciasAdobe:".sizeof($licenciasAdobe);
    if ($clasesTraslapadas > sizeof($licenciasAdobe)) {
      $rsClasesTraslapadas = $clasesService->getClasesTraslapadas($fechaHora);
      $arrClavesTraslapadas = array();
      $arrClavesTraslapadasGlobales = array();
      for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
        if(!empty($rowx[1]))
          $arrClavesTraslapadas[$ix] = $rowx[0];
        else
          $arrClavesTraslapadasGlobales[$ix] = $rowx[0];
      }
      // var_dump($arrClavesTraslapadasGlobales); 
      // $infoTraslapadas  = getInfoTraslapadas($arrClavesTraslapadas); // Clases Normales
      // $infoTraslapadas  .= getInfoTraslapadas($arrClavesTraslapadas); // Clases Normales
      // $infoTraslapadas .= getInfoTraslapadas($arrClavesTraslapadasGlobales, false); // Clases Globales
      //$tit_grupo = ObtenEtiqueta(420);
      //$tit_semana = ObtenEtiqueta(716);
      //$tit_titulo = ObtenEtiqueta(385);
      //$tit_fecha = ObtenEtiqueta(425);
      //$tit_tipo = ObtenEtiqueta(44);    
      //$info = "<table class=\"tabla_traslapadas\">";
      //$info .= "<tr><th>$tit_grupo</th><th>$tit_semana</th><th>$tit_titulo</th><th>$tit_fecha</th><th>$tit_tipo</th></tr>";
      $info .= getInfoTraslapadasNormales($arrClavesTraslapadas);
      $info .= getInfoTraslapadasGlobales($arrClavesTraslapadasGlobales);        
      $info .= "</table>";
      
      
      //$fe_clase_err = ObtenMensaje(230);// . " Clases traslapadas: $clasesTraslapadas Licencias disponibles: " . sizeof($licenciasAdobe);
      $fe_clase_err = "<a href='javascript:void(0);' class='traslapadas' data-trigger='focus' rel='popover-hover' data-placement='top' data-html='true' ";
      $fe_clase_err .= "title='<a href=\"#\" class=\"close\" data-dismiss=\"alert\">x</a> " . $info . "' data-content=''><span style='color:red;'>" . ObtenMensaje(230) . "</span></a>";
    }
    //falta omitir la temporal actual y eso arregal eso.
    $info="";$fe_clase_err = "";
    #Para zoom.
    if($clasesTraslapadasZoom > sizeof($licenciasZoomTotal)) {
        $rsClasesTraslapadas = $clasesService->getClasesTraslapadasZoom($fechaHora);
        $arrClavesTraslapadas = array();
        $arrClavesTraslapadasGlobales = array();
        $arrClavesTraslapadasGrupales=array();
        for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {

            if($rowx[6]==1){#Grupales Globales
                $arrClavesTraslapadasGrupales[$ix] = $rowx[0];
            }
            if(($rowx[6]==0)&&($rowx[7]==0)){
                #Clases normales
                $arrClavesTraslapadas[$ix] = $rowx[0];
            }    
            #Clases_globales
            if($rowx[7]==1){
                $arrClavesTraslapadasGlobales[$ix] = $rowx[0];
            } 
        }
        // var_dump($arrClavesTraslapadasGlobales);
        // $infoTraslapadas  = getInfoTraslapadas($arrClavesTraslapadas); // Clases Normales
        // $infoTraslapadas  .= getInfoTraslapadas($arrClavesTraslapadas); // Clases Normales
        // $infoTraslapadas .= getInfoTraslapadas($arrClavesTraslapadasGlobales, false); // Clases Globales
        /**$tit_grupo = ObtenEtiqueta(420);
        $tit_semana = ObtenEtiqueta(716);
        $tit_titulo = ObtenEtiqueta(385);
        $tit_fecha = ObtenEtiqueta(425);
        $tit_tipo = ObtenEtiqueta(44);    
        $info = "<table class=\"tabla_traslapadas\">";
        $info .= "<tr><th>$tit_grupo</th><th>$tit_semana</th><th>$tit_titulo</th><th>$tit_fecha</th><th>$tit_tipo</th></tr>";
        **/
        $info .= getInfoTraslapadasGlobalesGrupales($arrClavesTraslapadasGrupales);
        $info .= getInfoTraslapadasNormales($arrClavesTraslapadas);
        $info .= getInfoTraslapadasGlobales($arrClavesTraslapadasGlobales);
        
        $info .= "</table>";
        
        
        //$fe_clase_err = ObtenMensaje(230);// . " Clases traslapadas: $clasesTraslapadas Licencias disponibles: " . sizeof($licenciasAdobe);
        $fe_clase_err = "<a href='javascript:void(0);' class='traslapadas' data-trigger='focus' rel='popover-hover' data-placement='top' data-html='true' ";
        $fe_clase_err .= "title='<a href=\"#\" class=\"close\" data-dismiss=\"alert\">x</a> " . $info . "' data-content=''><span style='color:red;'>" . ObtenMensaje(230) . "</span></a>";
    }
   

    $msg_error = "";
    if($fe_clase_err) {
      $ds_clase = 'css_input_error';
      $ds_error = 'state-error';
      $msg_error = "<br/>" . $fe_clase_err;
    }
    else{
      $ds_clase = 'form-control';
      $ds_error = '';
    }
    echo "
    <tr class='$clase' id='reg_lecciones_$i'>
      <td align='center'>$no_orden</td>
      <td align='left'>
        <div id='div_ds_titulo_".($i+1)."' class='form-group smart-form'>
        <div class='col col-sm-12 col-lg-11 col-xs-11'>
          <label class='input'>
            <span class='icon-append'>".$ds_liga."</span>";
        CampoTexto('ds_titulo_'.($i+1), $ds_titulo, 100, 0,'', False, "onchange='ActualizaCG($clave, $fl_clase_cg, ".($i+1).");'");
    echo "</label>
          </div>
          $msg_error
        </div>
      </td>
      <td class='text-align-center'>";
      // Ruta de la foto del teacher
      $ruta_foto = PATH_MAE_IMAGES."/avatars/".str_texto($ds_ruta_avatar);
    echo "
      <div class='project-members' id='img_maestro_".($i+1)."'>
        <a href='javascript:change_maestro(".($i+1).", ".$clave.");' rel='tooltip' data-placement='top' data-html='true' data-original-title='".$ds_nombres_maestro."'>
          <img src='".$ruta_foto."' class='online' alt='user'>
        </a>
      </div>
      <div id='div_fl_maestro_".($i+1)."' class='form-group smart-form'>
        <div class='col col-sm-12 col-lg-12 col-xs-12'>
          <label class='select'>";
          $Query  = "SELECT CONCAT(usr.ds_nombres,' ',usr.ds_apaterno), ma.fl_maestro, ma.ds_ruta_avatar ";
          $Query .= "FROM c_maestro ma LEFT JOIN c_usuario usr ON(usr.fl_usuario=ma.fl_maestro) ";
          $Query .= "WHERE usr.fg_activo='1' ";      
          CampoSelectBD('fl_maestro_'.($i+1), $Query, $fl_maestro, '', False, 'onchange="ActualizaCG('.$clave.', '.$fl_clase_cg.', '.($i+1).');"');          
    echo"
          </label>
        </div>
      </div>      
      <script>
      $(document).ready(function(){";
        # Solo podra modificar el maestro que le de click
        if(!$view_select){
          echo "
          $('#fl_maestro_".($i+1)."').addClass('hidden');
          $('#img_maestro_".($i+1)."').css('display','block')";
        }
        else{
          if(($i+1)==$renglon){
            echo "
            $('#img_maestro_".$renglon."').css('display','none');
            $('#fl_maestro_".$renglon."').removeClass('hidden');
            $('#fl_maestro_".$renglon."').select2({});";
          }
          else
            echo "$('#fl_maestro_".($i+1)."').addClass('hidden');";
        }
    echo "
      });
      </script>";
    echo "
      </td>
      <td align='left'>".date('l', strtotime($fe_clase))."<br><small class=\'text-muted\'><i>".date('M d, Y', strtotime($fe_clase))."</i></small></td>
      <td align='left'>";
      echo "<div id='div_fe_clase_".($i+1)."' class='row form-group smart-form'><div class='col col-sm-12'><label class='input col col-sm-10 no-padding $ds_error'>";
      CampoTexto('fe_clase_'.($i+1), $fe_clase, 10, 10, $ds_clase, False, "readonly onchange='ActualizaCG($clave, $fl_clase_cg, ".($i+1).");'");
      Forma_Calendario('fe_clase_'.($i+1));
      echo "</label></div></td>
      <td>
       <div id='div_hr_clase_".($i+1)."' class='form-group smart-form'><div class='col col-sm-7 no-padding' id='div_hr_clase_".$i."'><label class='input'>";    
      CampoTexto('hr_clase_'.($i+1), $hr_clase, 10, 5, $ds_clase, False, "onchange='ActualizaCG($clave, $fl_clase_cg, ".($i+1).");'");
      echo "<td align='center'>".(strtotime($fe_clase)<=strtotime(date('d-m-Y'))?$attendance_cg:$future)." ";
      if(!empty($zoom_url)){
          echo"<br><p>&nbsp;</p><a href='$zoom_url' target='_blank'>zoom <i class='fa fa-external-link' aria-hidden='true'></i> </a><br><small class='text-muted'>$ds_host_zoom</small>";
      }
      echo "</td>";
      echo "</label></div>";
      echo"</div>";

     
      echo"
      </td>
      <td align='center'>
        <div class='checkbox'>
          <label>";    
            CampoCheckbox('fg_obligatorio_'.($i+1), $fg_obligatorio, '', '',True, "onchange='ActualizaCG($clave, $fl_clase_cg, ".($i+1).");'");
      echo "</label>
        </div>
      </td>
      <td align='center'>";
      # No podra borrar la promera sesion
      if($i!=0){
        echo "
          <a href=\"javascript:BorrarCG(".$fl_clase_cg.", ".$clave.");\" title='".ETQ_ELIMINAR."'><i class='fa fa-trash-o fa-2x'></i></a>";
      }
    echo "
      </td>
    </tr>\n";
    Forma_CampoOculto('fl_clase_cg'.($i+1), $fl_clase_cg);
    Forma_CampoOculto('no_orden_'.$i, $no_orden);
  }
  Forma_CampoOculto('tot_semanas', $tot_semanas);
?>
  </table> 
  <style>
    .popover{
       border:none;
       border-radius:unset;
       min-width:750px;
       width:100%;
       max-width:750px;
       overflow-wrap:break-word;
    }     

    .popover-title .close{
        position: relative;
        bottom: 3px;
    }
    
    .tabla_traslapadas {
      color: #333;
      font-size: 11px;
      width: 100%;
      border-collapse:
      collapse; border-spacing: 0;
    }

    .tabla_traslapadas td, .tabla_traslapadas th {
      border: 1px solid transparent; /* No more visible border */
      height: 30px;
      transition: all 0.3s; /* Simple transition for hover effect */
    }

    .tabla_traslapadas th {
      background: #0092cd; /* Darken header a bit */
      font-weight: bold;
      color: #FFFFFF
    }

    .tabla_traslapadas td {
      background: #FAFAFA;
    }


  </style>


  <script>
  $( document ).ready(function() {
      $('.traslapadas').popover({
          trigger: 'focus',
          html: true,
          animation: false,
          placement: 'top'
       });
   });

  function change_attendance_cg(live_session_cg, fl_maestro_cg, option_cg, id){
    //alert(live_session_cg+' - '+fl_maestro_cg+ ' - '+option_cg+' - '+id);
    $.ajax({
      type: 'post',
      url: 'change_attendance_review.php',
      data: {
        live_session_cg:live_session_cg,
        fl_maestro_cg:fl_maestro_cg,
        option_cg:option_cg
      },
      success: function (response) {
        if(response!=3){
          location.reload();
          //document.getElementById(id).click(); // Click on the checkbox
        } 
      }
    });

  }
  </script>
