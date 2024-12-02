<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroHTML('clave');
  $tmp = explode('a',$clave);
  $clave = $tmp[0];
  $fg_error = RecibeParametroNumerico('fg_error');
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_TEACHER_RATE, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  $year_current_ant = date('Y');
   $mes_actual=date('m');
   
  # Aprincipio de anio
  if(($tmp[1]=='12')&&($mes_actual==1)){
      $year_current_ant=$year_current_ant-1;
  }else{
      $year_current_ant = $year_current_ant;
  }
  
  if(!$fg_error){
    if(!empty($clave))
      $fe_periodo = $tmp[1]."-".$tmp[2];
  } else {
   $fe_periodo = RecibeParametroHTML('fe_periodo');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_TEACHER_RATE);
  
  # Inicia forma de captura
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError( );
  
  # obtenemos el nombre del teacher
  $row = RecuperaValor("SELECT CONCAT(a.ds_nombres, ' ', a.ds_apaterno, ' ', IFNULL(a.ds_amaterno, '')) FROM c_usuario a WHERE fl_usuario=$clave");
  $ds_maestro = str_texto($row[0]);
  Forma_CampoInfo(ObtenEtiqueta(297), $ds_maestro);
  Forma_Espacio( );

  # Periodos guardados y actual
  echo "
  <div class='row'>
    <label class='col col-sm-4 text-align-right'><strong>".ObtenEtiqueta(713).":</strong></label>
    <div class='col col-sm-4'>
      <select id='fe_periodo' name='fe_periodo' class='select2' >
        <option value='0'>".ObtenEtiqueta(70)."</option>";
        # Si ya existen registros mostrar estos periodos pero seleccionara el actual
        # Si no hay registros seleccionara el mes y anio actual
        $Query = "SELECT DISTINCT DATE_FORMAT(fe_periodo,'%m-%Y'), DATE_FORMAT(fe_periodo,'%M, %Y') FROM k_maestro_pago WHERE fl_maestro=$clave ORDER BY fe_periodo DESC";
        $rs = EjecutaQuery($Query);
        $registros = CuentaRegistros($rs);
        for($i=0;$row=RecuperaRegistro($rs);$i++){
          echo "<option value='".$row[0]."'"; if($row[0]==$fe_periodo) echo "selected"; echo ">".$row[1]."</option>";
        }
        # Si no existe registros tanto del mes actual como el anterior mostrar el que esa enviando 
        # Si hay registros pero el que esta recibiendo no existe mostrar el que existe y el que esta recibiendo
        # si ya hay registros del mes anterior y actual esta parte no funcionara
        $row1 = RecuperaValor("SELECT count(*) FROM k_maestro_pago WHERE fl_maestro=$clave AND DATE_FORMAT(fe_periodo,'%m-%Y')='".$fe_periodo."'");
        if(( (empty($row1[0])) AND  (!empty($registros)) ) OR ((empty($row1[0]))AND (empty($registros)))){
          $fe_periodoS = strftime("%B, %Y", strtotime(date('d')."-".$fe_periodo));// mostara el nombre mes y el ano actual
          echo "<option value='".$fe_periodo."'";
            if(!$fg_error AND !empty($clave)) echo "selected";
            if($fg_error) echo "selected";
          echo ">".$fe_periodoS."</option>";
        }
  echo "
      </select>
    </div>
  </div>";
  Forma_Espacio( );
  # Muestra las tablas automatica y manual
  Forma_Doble_Ini();
    echo "<div id='div_tarifas'></div>";
  Forma_Doble_Fin();

  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_TEACHER_RATE, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
  ?>
  <!-- Script para obtener las tablas automatica y manual -->
  <script type="text/javascript">
    function div_tarifas(selected,fg_error){
      $.ajax({
        type: 'POST',
        url : 'div_tarifas.php',
        data: 'fe_periodo='+selected+'&fl_maestro='+<?php echo $clave; ?>+'&fg_error='+fg_error,
        async: false,
        success: function(html) {
          $('#div_tarifas').html(html);
        }
      });
    }
    
    // al momento de cambiar el periodo muestra datos
    $('#fe_periodo').change(function(){
      var selected = $('#fe_periodo option:selected').val();
      div_tarifas(selected,  <?php echo $fg_error; ?>);
    });
    
    // si actualiza y tiene selecionado un periodo muestra la informacion de ese
    var selected = $('#fe_periodo option:selected').val();
    if(selected != 0)
      div_tarifas(selected, <?php echo $fg_error; ?>);
    
    function add_row(){
      $.ajax({
        type: 'POST',
        url : 'div_tarifas.php',
        data: 'accion=insert'+'&fe_periodo='+$('#fe_periodo option:selected').val()+'&fl_maestro='+<?php echo $clave; ?>,
        async: false,
        success: function(html) {
          $('#div_tarifas').html(html);
        }
      });
    }
    
    function update_row(row, fl_maestro_pago_det){
      // Obtiene la informacion de los campos que esta actualizando
      var ds_concepto = $('#ds_concepto'+row).val();
      var mn_tarifa_hr = $('#mn_tarifa_hr'+row).val();
      var no_horas = $('#no_horas'+row).val();
      
      //Envia datos para actualizarlos
      $.ajax({
        type: 'POST',
        url : 'div_tarifas.php',
        data: 'accion=update'+'&ds_concepto='+ds_concepto+'&mn_tarifa_hr='+mn_tarifa_hr+
              '&no_horas='+no_horas+'&fl_maestro_pago_det='+fl_maestro_pago_det+'&fe_periodo='+$('#fe_periodo option:selected').val()+'&fl_maestro='+<?php echo $clave; ?>,
        async: false,
        success: function(html) {
          $('#div_tarifas').html(html);
        }
      });
    }
    
    function delete_row (row){
      //Envia datos para eliminarlos
      $.ajax({
        type: 'POST',
        url : 'div_tarifas.php',
        data: 'accion=delete'+'&fl_maestro_pago_det='+row+'&fe_periodo='+$('#fe_periodo option:selected').val()+'&fl_maestro='+<?php echo $clave; ?>,
        async: false,
        success: function(html) {
          $('#div_tarifas').html(html);
        }
      });
    }
    
    // Funcion para restar o sumar clases que los maestro no imparten
    function subtract_class(row1,fl_grupo1,ds_concepto1,mn_tarifa_hr1, maestro_pago1, type_clase='A'){
      var vcheckbox;
      if(($('#subtract_class'+row1).is(':checked') && type_clase=='A') || ($('#subtract_cg'+row1).is(':checked') && type_clase=='ACG') || ($('#subtract_gg'+row1).is(':checked') && type_clase=='AG'))
        vcheckbox = 1;
      else
        vcheckbox = 0;
      $.ajax({
        type: 'POST',
        url : 'div_tarifas.php',
        data: 'accion=subtract'+'&fe_periodo='+$('#fe_periodo option:selected').val()+'&fl_maestro='+<?php echo $clave; ?>+'&row_subtract='+row1
              +'&fl_grupo_sub='+fl_grupo1+'&ds_concepto_sub='+ds_concepto1+'&mn_tarifa_hr_sub='+mn_tarifa_hr1+'&vcheckbox='+vcheckbox+'&maestro_pago='+maestro_pago1
              +'&type_clase='+type_clase,
        async: false,
        success: function(html) {
          $('#div_tarifas').html(html);
        }
      });
    }

  function change_attendance_lecture(live_session, fl_maestro, option, id,fl_clase){
    //alert(live_session+' - '+fl_maestro+' - '+option+' - '+id);
    $.ajax({
      type: 'post',
      url: 'change_attendance_lecture.php',
      data: {
          live_session:live_session,
          fl_clase:fl_clase,
        fl_maestro:fl_maestro,
        option:option
      },
      async: false,
      success: function (response) {
        var checked = document.getElementById(id).checked;
        if(option==1 && checked==true){
          $('#lecture_'+live_session).removeClass();
          $('#lecture_'+live_session).addClass('text-danger');
          $('#'+id).click(); // Click on the checkbox
        } else if (option==1 && checked==false) {
          $('#lecture_'+live_session).removeClass();
          $('#lecture_'+live_session).addClass('text-danger');
          //location.reload();
        } else if (option==2 && checked==true) {
          $('#lecture_'+live_session).removeClass();
          $('#lecture_'+live_session).addClass('text-success');
          location.reload();
        } else if (option==2 && checked==false) {
          $('#lecture_'+live_session).removeClass();
          $('#lecture_'+live_session).addClass('text-success');
          $('#'+id).click(); // Click on the checkbox
        } else if (option==3 && checked==false) {
          $('#lecture_'+live_session).removeClass();
          $('#lecture_'+live_session).addClass('text-warning');
          $('#'+id).click(); // Click on the checkbox
        } else {
          $('#lecture_'+live_session).removeClass();
          $('#lecture_'+live_session).addClass('text-warning');
          //location.reload();
        }
      }
    });

  }

  function change_attendance_cg(live_session_cg, fl_maestro_cg, option_cg, id,fl_clase){
    //alert(live_session_cg+' - '+fl_maestro_cg+ ' - '+option_cg+' - '+id);
    $.ajax({
      type: 'post',
      url: 'change_attendance_review.php',
      data: {
          live_session_cg:live_session_cg,
          fl_clase:fl_clase,
        fl_maestro_cg:fl_maestro_cg,
        option_cg:option_cg
      },
      async: false,
      success: function (response) {
        var checked = document.getElementById(id).checked;
        if(option_cg==1 && checked==true){
          $('#review_'+live_session_cg).removeClass();
          $('#review_'+live_session_cg).addClass('text-danger');
          $('#'+id).click(); // Click on the checkbox
        } else if (option_cg==1 && checked==false) {
          $('#review_'+live_session_cg).removeClass();
          $('#review_'+live_session_cg).addClass('text-danger');
          location.reload();
        } else if (option_cg==2 && checked==true) {
          $('#review_'+live_session_cg).removeClass();
          $('#review_'+live_session_cg).addClass('text-success');
          location.reload();
        } else if (option_cg==2 && checked==false) {
          $('#review_'+live_session_cg).removeClass();
          $('#review_'+live_session_cg).addClass('text-success');
          $('#'+id).click(); // Click on the checkbox
        } else if (option_cg==3 && checked==false) {
          $('#review_'+live_session_cg).removeClass();
          $('#review_'+live_session_cg).addClass('text-warning');
          $('#'+id).click(); // Click on the checkbox
        } else {
          $('#review_'+live_session_cg).removeClass();
          $('#review_'+live_session_cg).addClass('text-warning');
          location.reload();
        }
      }
    });

  }

  function change_attendance_gg(live_session_gg, fl_maestro_gg, option_gg, id,fl_clase){
    //alert(live_session_gg + ' - ' + fl_maestro_gg + ' - ' + option_gg + ' - ' + id);
    $.ajax({
      type: 'post',
      url: 'change_attendance_global.php',
      data: {
        live_session_gg:live_session_gg,
        fl_maestro_gg:fl_maestro_gg,
        fl_clase:fl_clase,
        option_gg:option_gg
      },
      async: false,
      success: function (response) {
        var checked = document.getElementById(id).checked;
        if(option_gg==1 && checked==true){
          $('#global_'+live_session_gg).removeClass();
          $('#global_'+live_session_gg).addClass('text-danger');
          $('#'+id).click(); // Click on the checkbox
        } else if (option_gg==1 && checked==false) {
          $('#global_'+live_session_gg).removeClass();
          $('#global_'+live_session_gg).addClass('text-danger');
          location.reload();
        } else if (option_gg==2 && checked==true) {
          $('#global_'+live_session_gg).removeClass();
          $('#global_'+live_session_gg).addClass('text-success');
          location.reload();
        } else if (option_gg==2 && checked==false) {
          $('#global_'+live_session_gg).removeClass();
          $('#global_'+live_session_gg).addClass('text-success');
          $('#'+id).click(); // Click on the checkbox
        } else if (option_gg==3 && checked==false) {
          $('#global_'+live_session_gg).removeClass();
          $('#global_'+live_session_gg).addClass('text-warning');
          $('#'+id).click(); // Click on the checkbox
        } else {
          $('#global_'+live_session_gg).removeClass();
          $('#global_'+live_session_gg).addClass('text-warning');
          location.reload();
        }
      }
    });

  }
  </script>
  
