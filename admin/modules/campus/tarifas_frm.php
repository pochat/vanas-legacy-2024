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
  
  if(!$fg_error){
    if(!empty($clave))
      $fe_periodo = $tmp[1]."-".date('Y');
  }
  else
   $fe_periodo = RecibeParametroHTML('fe_periodo');
  

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
  <tr>
    <td class='css_prompt' align='right' >".ObtenEtiqueta(713).":</td>
    <td>
      <select id='fe_periodo' name='fe_periodo' class='css_input' >
        <option value='0'>".ObtenEtiqueta(70)."</option>";
        # Si ya existen registros mostrar estos periodos pero seleccionara el actual
        # Si no hay registros seleccionara el mes y anio actual
       $Query = "SELECT DATE_FORMAT(fe_periodo,'%m-%Y'), DATE_FORMAT(fe_periodo,'%M, %Y') FROM k_maestro_pago WHERE fl_maestro=$clave ORDER BY fe_periodo";
        $rs = EjecutaQuery($Query);
        $registros = CuentaRegistros($rs);
        for($i=0;$row=RecuperaRegistro($rs);$i++){
          echo "<option value='".$row[0]."'"; if($row[0]==$fe_periodo) echo "selected"; echo ">".$row[1]."</option>";
        }
        # Si no existe registros tanto del mes actual como el anterior mostrar el que esa enviando 
        # Si hay registros pero el que esta recibiendo no existe mostrar el que existe y el que esta recibiendo
        # si ya hay registros del mes anterior y actual esta parte no funcionara
        $row1 = RecuperaValor("SELECT count(*) FROM k_maestro_pago WHERE fl_maestro=$clave AND DATE_FORMAT(fe_periodo,'%m-%Y')='".$fe_periodo."'");
        if((empty($row1[0]) AND !empty($registros)) OR (empty($row1[0])AND empty($registros))){
          $fe_periodoS = strftime("%B, %Y", strtotime(date('d')."-".$fe_periodo));// mostara el nombre mes y el a?o actual
          echo "<option value='".$fe_periodo."'";
            if(!$fg_error AND !empty($clave)) echo "selected";
            if($fg_error) echo "selected";
          echo ">".$fe_periodoS."</option>";
        }
  echo "
      </select>
    </td>
  </tr>";
  Forma_Espacio( );
  # Muestra las tablas automatica y manual
  echo "
  <tr>
    <td colspan='2'><div id='div_tarifas'></div></td>
  </tr>";

  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_TEACHER_RATE, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
  # Script para obtener las tablas automatica y manual
  echo "
  <script>
    function div_tarifas(selected,fg_error){
      $.ajax({
        type: 'POST',
        url : 'div_tarifas.php',
        data: 'fe_periodo='+selected+'&fl_maestro='+$clave+'&fg_error='+fg_error,
        async: false,
        success: function(html) {
          $('#div_tarifas').html(html);
        }
      });
     }
    
    // al momento de cambiar el periodo muestra datos
    $('#fe_periodo').change(function(){
      var selected = $(\"#fe_periodo option:selected\").val();
      div_tarifas(selected, $fg_error);
    });
    
    // si actualiza y tiene selecionado un periodo muestra la informacion de ese
    var selected = $(\"#fe_periodo option:selected\").val();
    if(selected != 0)
      div_tarifas(selected, $fg_error);
    
    function add_row(){
      $.ajax({
        type: 'POST',
        url : 'div_tarifas.php',
        data: 'accion=insert'+'&fe_periodo='+$(\"#fe_periodo option:selected\").val()+'&fl_maestro='+$clave,
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
              '&no_horas='+no_horas+'&fl_maestro_pago_det='+fl_maestro_pago_det+'&fe_periodo='+$(\"#fe_periodo option:selected\").val()+'&fl_maestro='+$clave,
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
        data: 'accion=delete'+'&fl_maestro_pago_det='+row+'&fe_periodo='+$(\"#fe_periodo option:selected\").val()+'&fl_maestro='+$clave,
        async: false,
        success: function(html) {
          $('#div_tarifas').html(html);
        }
      });
    }
    
    // Funcion para restar o sumar clases que los maestro no imparten
    function subtract_class(row1,fl_grupo1,ds_concepto1,mn_tarifa_hr1, maestro_pago1){
      var vcheckbox;
      if($('#subtract_class'+row1).is(':checked'))
        vcheckbox = 1;
      else
        vcheckbox = 0;
      $.ajax({
        type: 'POST',
        url : 'div_tarifas.php',
        data: 'accion=subtract'+'&fe_periodo='+$(\"#fe_periodo option:selected\").val()+'&fl_maestro='+$clave+'&row_subtract='+row1
              +'&fl_grupo_sub='+fl_grupo1+'&ds_concepto_sub='+ds_concepto1+'&mn_tarifa_hr_sub='+mn_tarifa_hr1+'&vcheckbox='+vcheckbox+'&maestro_pago='+maestro_pago1,
        async: false,
        success: function(html) {
          $('#div_tarifas').html(html);
        }
      });
    }
 
  </script>";
  
?>