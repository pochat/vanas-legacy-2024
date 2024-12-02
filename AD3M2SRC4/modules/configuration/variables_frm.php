<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );

  # Recibe parametro
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');

  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_VARIABLES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT ds_configuracion, ds_valor,fg_sistema FROM c_configuracion WHERE cl_configuracion=$clave");
      $ds_configuracion = str_texto($row[0]);
      $ds_valor = str_texto($row[1]);
	  $fg_sistema = str_texto($row[2]);
    }
    else { // Alta, inicializa campos
      MuestraPaginaError(ERR_SIN_PERMISO);
      exit;
    }
    $ds_valor_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_configuracion = RecibeParametroHTML('ds_configuracion');
    $ds_valor = RecibeParametroHTML('ds_valor');
    $ds_valor_err = RecibeParametroNumerico('ds_valor_err');
	$fg_sistema=RecibeParametroBinario('fg_sistema');

  }

  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_VARIABLES);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoInfo(ETQ_CLAVE, $clave);



  Forma_CampoInfo(ObtenEtiqueta(135), $ds_configuracion);
  Forma_CampoOculto('ds_configuracion' , $ds_configuracion);
  $opc = array( ObtenEtiqueta(2135), ObtenEtiqueta(2136)); // Masculino, Femenino, Neutral
  $val = array('0', '1');
  Forma_CampoSelect(ObtenEtiqueta(2137), False, 'fg_sistema', $opc, $val, $fg_sistema);


  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(136), True, 'ds_valor', $ds_valor, 255, 0, $ds_valor_err);

if ($clave == 171) {

    $row = RecuperaValor("SELECT  ". ConsultaFechaBD('fe_counter', FMT_CAPTURA).",". ConsultaFechaBD('round1', FMT_CAPTURA).",". ConsultaFechaBD('round2', FMT_CAPTURA).",". ConsultaFechaBD('round3', FMT_CAPTURA).",fl_periodo,". ConsultaFechaBD('fe_start_date', FMT_CAPTURA)." FROM c_configuracion WHERE cl_configuracion=$clave");
    $fe_counter = str_texto($row[0]);
    $round1 = str_texto($row[1]);
    $round2 = str_texto($row[2]);
    $round3 = str_texto($row[3]);
    $fl_periodo = $row[4];
    $fe_start_date= str_texto($row[5]);


?>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-4">
        <?php
            $Query = "SELECT nb_periodo,fl_periodo FROM c_periodo WHERE fl_periodo>95 ";
            Forma_CampoSelectBD('Period (Class Time)', True, 'fl_periodo', $Query, $fl_periodo, '', False, '', 'left', 'col col-sm-6', 'col col-sm-6');
        ?>
    </div>
    <div class="col-md-6"></div>

</div>
<br />

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-4">
        <?php
              Forma_CampoTexto('Start Date' . ' ' . ETQ_FMT_FECHA, False, 'fe_start_date', $fe_start_date, 10, 0, $fe_start_date_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-6', 'col col-sm-6');
              Forma_Calendario('fe_start_date');
        ?>
    </div>
    <div class="col-md-6"></div>

</div>



   <div class="row">
       <div class="col-md-2">

       </div>
       <div class="col-md-4">
           <?php
              Forma_CampoTexto('Counter' . ' ' . ETQ_FMT_FECHA, False, 'fe_counter', $fe_counter, 10, 0, $fe_counter_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-6', 'col col-sm-6');
              Forma_Calendario('fe_counter');
           ?>
       </div>
       <div class="col-md-6">

       </div>

   </div>
<br />
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-4">
        <?php
              Forma_CampoTexto('Round1' . ' ' . ETQ_FMT_FECHA, False, 'round1', $round1, 10, 0, $round1_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-6', 'col col-sm-6');
              Forma_Calendario('round1');
        ?>
    </div>
    <div class="col-md-6"></div>

</div>
<br />
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-4">
        <?php
              Forma_CampoTexto('Round2' . ' ' . ETQ_FMT_FECHA, False, 'round2', $round2, 10, 0, $round2_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-6', 'col col-sm-6');
              Forma_Calendario('round2');
        ?>
    </div>
    <div class="col-md-6"></div>

</div>
<br />
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-4">
        <?php
              Forma_CampoTexto('Round3' . ' ' . ETQ_FMT_FECHA, False, 'round3', $round3, 10, 0, $round3_err, False, '', True, '', '', 'smart-form form-group', 'left', 'col col-sm-6', 'col col-sm-6');
              Forma_Calendario('round3');
        ?>
    </div>
    <div class="col-md-6"></div>

</div>


<?php
}




  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_VARIABLES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>