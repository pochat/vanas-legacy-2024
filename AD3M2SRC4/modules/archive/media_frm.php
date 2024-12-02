<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MEDIA, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!empty($clave)) { // Actualizacion, recupera de la base de datos
    $Query  = "SELECT fl_programa, no_grado, no_semana, ds_titulo, ds_leccion, ds_vl_ruta, ds_vl_duracion, ";
    $concat = array(ConsultaFechaBD('fe_vl_alta', FMT_FECHA), "' '", ConsultaFechaBD('fe_vl_alta', FMT_HORAMIN));
    $Query .= "(".ConcatenaBD($concat).") 'fe_vl_alta', ";
    $Query .= "fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch, ";
    $Query .= "ds_as_ruta, ds_as_duracion, ";
    $concat = array(ConsultaFechaBD('fe_as_alta', FMT_FECHA), "' '", ConsultaFechaBD('fe_as_alta', FMT_HORAMIN));
    $Query .= "(".ConcatenaBD($concat).") 'fe_as_alta' ";
    $Query .= "FROM c_leccion ";
    $Query .= "WHERE fl_leccion=$clave";
    $row = RecuperaValor($Query);
    $fl_programa = $row[0];
    $no_grado = $row[1];
    $no_semana = $row[2];
    $ds_titulo = str_texto($row[3]);
    $ds_leccion = str_texto($row[4]);
    $ds_vl_ruta = str_texto($row[5]);
    $ds_vl_duracion = str_texto($row[6]);
    $fe_vl_alta = str_texto($row[7]);
    $fg_animacion = $row[8];
    $fg_ref_animacion = $row[9];
    $no_sketch = $row[10];
    $fg_ref_sketch = $row[11];
    $ds_as_ruta = str_texto($row[12]);
    $ds_as_duracion = str_texto($row[13]);
    $fe_as_alta = str_texto($row[14]);
  }
   

  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_MEDIA);
  
  # Ventana para preview
  require '../programs/preview.inc.php';
  
  # Funciones javascript
  echo "
    <script type='text/javascript'>
      function fuente_archivo(arch)
      {
        if(document.datos[arch+'_a'].value == 0)
        {
          document.datos[arch].disabled = false;
        }
        else
        {
          document.datos[arch].disabled = true;
          document.datos[arch].value = '';
        }
      }
    </script>";
  
  # Inicia forma de captura
  Forma_Inicia($clave, True);
  if(isset($fg_error))
    Forma_PresentaError( );
  
  # Campos de captura
  $Query  = "SELECT CONCAT(nb_programa,' (',ds_duracion,')'), fl_programa FROM c_programa WHERE fg_archive='1' ORDER BY no_orden";
  Forma_CampoSelectBD(ObtenEtiqueta(380), False, 'fl_programa', $Query, $fl_programa);
  Forma_Espacio( );
  
  Forma_CampoTexto(ObtenEtiqueta(375), True, 'no_grado', $no_grado, 3, 5, !empty($no_grado_err)?$no_grado_err:NULL);
  Forma_CampoTexto(ObtenEtiqueta(390), True, 'no_semana', $no_semana, 3, 5, !empty($no_semana_err)?$no_semana_err:NULL);
  Forma_Espacio( );
  
  Forma_CampoTexto(ObtenEtiqueta(385), True, 'ds_titulo', $ds_titulo, 100, 50, !empty($ds_titulo_err)?$ds_titulo_err:NULL);
  Forma_CampoTinyMCE(ObtenEtiqueta(391), True, 'ds_leccion', $ds_leccion, 50, 20, !empty($ds_leccion_err)?$ds_leccion_err:NULL);
  Forma_Espacio( );
  
  if(!empty($ds_as_ruta)) {
    $ext = strtoupper(ObtenExtensionArchivo($ds_as_ruta));
    switch($ext) {
      case "SWF": $ruta = SP_FLASH_W; break;
      case "FLV": $ruta = SP_VIDEOS_W; break;
      default:    $ruta = SP_IMAGES_W; break;
    }
    Forma_CampoPreview('Video Brief', 'ds_as_ruta', $ds_as_ruta, $ruta, True);
    Forma_CampoArchivo(ObtenEtiqueta(216), False, 'archivo1', 60);
  }
  else
    Forma_CampoArchivo('Video Brief', False, 'archivo1', 60);
  $Query  = "SELECT ds_as_ruta, MIN(fl_leccion) FROM c_leccion WHERE ds_as_ruta <> '' GROUP BY ds_as_ruta ORDER BY ds_as_ruta";
  Forma_CampoSelectBD(ObtenEtiqueta(389), False, 'archivo1_a', $Query, 0, '', True, 'onchange = fuente_archivo("archivo1")');
  Forma_CampoTexto(ObtenEtiqueta(396), False, 'ds_as_duracion', $ds_as_duracion, 10, 5);
  Forma_CampoInfo(ObtenEtiqueta(397), $fe_as_alta);
  Forma_CampoOculto('fe_as_alta', $fe_as_alta);
  Forma_Espacio( );
  
  if(!empty($ds_vl_ruta)) {
    $ext = strtoupper(ObtenExtensionArchivo($ds_vl_ruta));
    switch($ext) {
      case "SWF": $ruta = SP_FLASH_W; break;
      case "FLV": $ruta = SP_VIDEOS_W; break;
      default:    $ruta = SP_VIDEOS_W; break;
    }
    Forma_CampoPreview(ObtenEtiqueta(392), 'ds_vl_ruta', $ds_vl_ruta, $ruta, True);
    Forma_FileUploader(ObtenEtiqueta(216), False, 'archivo', "'flv', 'mov', 'mp4'", '1024 * 1024 * 1024', '', True);
  }
  else
    Forma_FileUploader(ObtenEtiqueta(392), False, 'archivo', "'flv', 'mov', 'mp4'", '1024 * 1024 * 1024', '', True);
  $Query  = "SELECT ds_vl_ruta, fl_leccion FROM c_leccion WHERE ds_vl_ruta <> '' GROUP BY ds_vl_ruta ORDER BY ds_vl_ruta";
  Forma_CampoSelectBD(ObtenEtiqueta(389), False, 'archivo_a', $Query, 0, '', True);
  Forma_CampoTexto(ObtenEtiqueta(396), False, 'ds_vl_duracion', $ds_vl_duracion, 10, 5);
  Forma_CampoInfo(ObtenEtiqueta(397), $fe_vl_alta);
  Forma_CampoOculto('fe_vl_alta', $fe_vl_alta);
  Forma_Espacio( );
  
  Forma_CampoCheckbox(ObtenEtiqueta(393), 'fg_animacion', $fg_animacion);
  Forma_CampoCheckbox(ObtenEtiqueta(398), 'fg_ref_animacion', $fg_ref_animacion);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(394), True, 'no_sketch', $no_sketch, 3, 5, !empty($no_sketch_err)?$no_sketch_err:NULL);
  Forma_CampoCheckbox(ObtenEtiqueta(399), 'fg_ref_sketch', $fg_ref_sketch);
  Forma_Espacio( );
  

  Forma_Termina(False);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>