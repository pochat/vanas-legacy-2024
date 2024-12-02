<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else {
    $permiso = PERMISO_ALTA;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CONTENIDOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT a.fl_provincia, a.ds_provincia , a.fl_pais, a.ds_type, a.mn_PST , ";
      $Query .= "a.mn_GST,a.mn_HST,a.mn_tax, ds_notas, b.ds_pais ";
      $Query .= "FROM k_provincias a, c_pais b WHERE a.fl_pais=b.fl_pais AND fl_provincia=$clave";
      $row = RecuperaValor($Query);
      $fl_provincia = $row[0];
      $ds_provincia = $row[1];
      $fl_pais = $row[2];
      $ds_type = $row[3];
      $mn_PST = $row[4];
      $mn_GST = $row[5];
      $mn_HST = $row[6];
      $mn_tax = $row[7];
      $ds_notas = str_texto($row[8]);
      $ds_pais = str_texto($row[9]);
    }
    else { // Alta, inicializa campos
      $ds_provincia = "";
      $fl_pais = "";
      $ds_type = "";
      $mn_PST = "";
      $mn_GST = "";
      $mn_HST = "";
      $mn_tax = "";
      $ds_notas = "";
    }
    $ds_provincia_err = "";
    $fl_pais_err = "";
    $ds_type_err = "";
    $mn_PST_err = "";
    $mn_GST_err = "";
    $mn_HST_err = "";
    $mn_tax_err = "";
    $ds_notas_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_provincia = RecibeParametroHTML('ds_provincia');
    $ds_provincia_err = RecibeParametroHTML('ds_provincia_err');
    $fl_pais = RecibeParametroNumerico('fl_pais');
    $fl_pais_err = RecibeParametroNumerico('fl_pais_err');    
    $ds_pais = RecibeParametroHTML('ds_pais');    
    $ds_type = RecibeParametroHTML('ds_type');
    $ds_type_err = RecibeParametroHTML('ds_type_err');
    $mn_PST = RecibeParametroHTML('mn_PST');
    $mn_PST_err = RecibeParametroHTML('mn_PST_err');
    $mn_GST = RecibeParametroHTML('mn_GST');
    $mn_GST_err = RecibeParametroHTML('mn_GST_err');
    $mn_HST = RecibeParametroHTML('mn_HST');
    $mn_HST_err = RecibeParametroHTML('mn_HST_err');
    $mn_tax = RecibeParametroHTML('mn_tax');
    $mn_tax_err = RecibeParametroHTML('mn_tax_err');
    $ds_notas = RecibeParametroHTML('ds_notas');
    $ds_notas_err = RecibeParametroHTML('ds_notas_err');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  define('FUNC_APROVINCIAS',127);
  PresentaEncabezado(FUNC_APROVINCIAS);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  if(!empty($clave)){
    Forma_CampoInfo(ObtenEtiqueta(287),$ds_pais);
    Forma_CampoOculto('ds_pais',$ds_pais);
  }
  Forma_Espacio();
  Forma_CampoTexto(ObtenEtiqueta(812), True, 'ds_provincia', $ds_provincia, 100, 50, $ds_provincia_err);
  Forma_Espacio();
  if(empty($clave)){
    $Query = "SELECT nb_pais, fl_pais FROM c_pais ORDER BY ds_pais";
    Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'fl_pais', $Query, $fl_pais, $fl_pais_err, True);
  }
  Forma_CampoTexto(ObtenEtiqueta(817), True, 'ds_type', $ds_type, 30, 20, $ds_type_err);
  Forma_CampoTexto(ObtenEtiqueta(813), False, 'mn_PST', $mn_PST, 30, 20, $mn_PST_err);
  Forma_CampoTexto(ObtenEtiqueta(814), False, 'mn_GST', $mn_GST, 30, 20, $mn_GST_err);
  Forma_CampoTexto(ObtenEtiqueta(815), False, 'mn_HST', $mn_HST, 30, 20, $mn_HST_err);
  Forma_CampoTexto(ObtenEtiqueta(816), True, 'mn_tax', $mn_tax, 30, 20, $mn_tax_err);
  Forma_Espacio();
  Forma_CampoTextArea(ObtenEtiqueta(818), False, 'ds_notas', $ds_notas, 50, 8, $ds_notas_err);
  Forma_Espacio();
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CONTENIDOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Automaticamente se sumara los campos de PST GST HST para mostrarlo en tax
  echo "
  <script type='text/javascript'>
  $(document).ready(function(){
    $('#mn_PST').change(function(){
      if($(this).val()=='') pst0 = 0; else pst0 = $(this).val();
      if($('#mn_GST').val()=='') gst0 = 0; else gst0 = $('#mn_GST').val();
      if($('#mn_HST').val()=='') hst0 = 0; else hst0 = $('#mn_HST').val();
      var tax0 = parseFloat(pst0)+parseFloat(gst0)+parseFloat(hst0);
      $('#mn_tax').val(tax0);
    });
    $('#mn_GST').change(function(){
      if($('#mn_PST').val()=='') pst1 = 0; else pst1 = $('#mn_PST').val();
      if($(this).val()=='') gst1 = 0; else gst1 = $(this).val();
      if($('#mn_HST').val()=='') hst1 = 0; else hst1 = $('#mn_HST').val();
      var tax1 = parseFloat(pst1)+parseFloat(gst1)+parseFloat(hst1);
      $('#mn_tax').val(tax1);
    });
    $('#mn_HST').change(function(){
      if($('#mn_PST').val()=='') pst2 = 0; else pst2 = $('#mn_PST').val();
      if($('#mn_GST').val()=='') gst2 = 0; else gst2 = $('#mn_GST').val();
      if($(this).val()=='') hst2 = 0; else hst2 = $(this).val();
      var tax2 = parseFloat(pst2)+parseFloat(gst2)+parseFloat(hst2);
      $('#mn_tax').val(tax2);
    });
  });
  </script>";
  
  # Pie de Pagina
  PresentaFooter( );
?>