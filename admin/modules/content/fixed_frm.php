<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametro
  $fg_error = RecibeParametroNumerico('fg_error');
  $clave = RecibeParametroHTML('clave');
  $clave = explode("_",$clave);

  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_FIXED, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave[0])) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_pagina, ds_pagina, ds_titulo, tr_titulo, ds_contenido, tr_contenido, fl_programa, no_grado, fg_fijo ";
      $Query .= "FROM c_pagina WHERE cl_pagina=$clave[0] AND fl_programa=$clave[1] AND no_grado=$clave[2]";
      $row = RecuperaValor($Query);
      $nb_pagina = str_texto($row[0]);
      $ds_pagina = str_texto($row[1]);
      $ds_titulo = str_texto($row[2]);
      $tr_titulo = str_texto($row[3]);
      $ds_contenido = str_texto($row[4]);
      $tr_contenido = str_texto($row[5]);
      $fl_programa = $row[6];
      $no_grado = $row[7];
      $fg_fijo = $row[8];
      $Query  = "SELECT nb_programa, ds_duracion FROM c_programa WHERE fl_programa = $fl_programa";
      $row = RecuperaValor($Query);
      $nb_programa = $row[0];
      $ds_duracion = $row[1];
    }
    else { // Alta, inicializa campos
      $nb_pagina = "";
      $ds_pagina = "";
      $ds_titulo = "";
      $tr_titulo = "";
      $ds_contenido = "";
      $tr_contenido = "";
      $fl_programa = 0;
      $no_grado = 0;
      $fg_fijo = 0;
    }
    $cl_pagina_nueva = "";
    $cl_pagina_err = "";
    $nb_pagina_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $cl_pagina_nueva = RecibeParametroNumerico('cl_pagina_nueva');
    $cl_pagina_err = RecibeParametroNumerico('cl_pagina_err');
    $nb_pagina = RecibeParametroHTML('nb_pagina');
    $nb_pagina_err = RecibeParametroNumerico('nb_pagina_err');
    $ds_pagina = RecibeParametroHTML('ds_pagina');
    $ds_titulo = RecibeParametroHTML('ds_titulo');
    $tr_titulo = RecibeParametroHTML('tr_titulo');
    $ds_contenido = RecibeParametroHTML('ds_contenido');
    $tr_contenido = RecibeParametroHTML('tr_contenido');
    $fg_fijo = RecibeParametroNumerico('fg_fijo');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_FIXED);
  
  # Forma para captura de datos
  Forma_Inicia($clave[0]);
  if($fg_error)
    Forma_PresentaError( );
    
  Forma_CampoOculto('fg_fijo' , $fg_fijo);
  
  # Si se esta editando
  if(!empty($clave[0])) {
    Forma_CampoInfo(ETQ_CLAVE, $clave[0]);
    Forma_CampoOculto('cl_pagina_nueva', $cl_pagina_nueva);
    if($fg_fijo == 0)
    {
      Forma_CampoInfo(ObtenEtiqueta(380), $nb_programa."-".$ds_duracion);
      Forma_CampoOculto('fl_programa', $fl_programa);
      if($no_grado == 0)
        $grado = "";
      else
        $grado = $no_grado;
      Forma_CampoInfo(ObtenEtiqueta(375), $grado);
      Forma_CampoOculto('no_grado', $no_grado);
    }
  }
  else
  {
    Forma_CampoTexto(ETQ_CLAVE, True, 'cl_pagina_nueva', $cl_pagina_nueva, 5, 10, $cl_pagina_err);
    if($fg_fijo == 0)
    {
      $concat = array('nb_programa', "' - '", 'ds_duracion');
      $Query  = "SELECT ".ConcatenaBD($concat).", fl_programa FROM c_programa ORDER BY no_orden";
      Forma_CampoSelectBD(ObtenEtiqueta(380), False, 'fl_programa', $Query, $fl_programa, '', True);
      #script para los combox dependientes del programa a elejir
      echo "
      <script language='javascript'>
        $(document).ready(function(){
           $('#fl_programa').change(function () {
             $('#fl_programa option:selected').each(function () {
                fl_programa=$(this).val();
                $.post('grados.php', { fl_programa: fl_programa }, function(data){
                $('#no_grado').html(data);
                });            
            });
           })
        });
        </script>";
      echo "
      <tr>
        <td class='css_prompt' align='right'>".ObtenEtiqueta(375).":</td>
        <td class='css_default'><select name='no_grado' id='no_grado' class='css_input'></select></td>
      </tr>";
      
    }
  }
  Forma_Espacio( );
  
  # Campos de captura
  Forma_CampoTexto(ObtenEtiqueta(270), True, 'nb_pagina', $nb_pagina, 50, 30, $nb_pagina_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_pagina', $ds_pagina, 255, 50);
  Forma_Espacio( );
  Forma_CampoTexto(ETQ_TITULO, False, 'ds_titulo', $ds_titulo, 255, 50);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_titulo', $tr_titulo, 255, 50);
  Forma_Espacio( );
  Forma_CampoTinyMCE(ObtenEtiqueta(271), False, 'ds_contenido', $ds_contenido, 50, 20);
  Forma_CampoTinyMCE(ObtenEtiqueta(245), False, 'tr_contenido', $tr_contenido, 50, 20);
  Forma_Espacio( );
  
  $rs = EjecutaQuery("SELECT ds_ruta_video, fl_video_contenido FROM k_video_contenido 
                          WHERE cl_pagina=$clave[0] AND fl_programa=$clave[1] AND no_grado=$clave[2] 
                          ORDER BY fl_video_contenido");
  $no_videos =  CuentaRegistros($rs);
  Forma_CampoOculto('no_videos', $no_videos);
  
  if($no_videos > 0) {
    for($i = 1; $row = RecuperaRegistro($rs); $i++)
    {
      $ds_ruta_video[$i] = $row[0];
      $fl_video_contenido[$i] = $row[1];
      $ext = strtoupper(ObtenExtensionArchivo($ds_ruta_video[$i]));
      switch($ext) {
        case "SWF": $ruta = SP_FLASH_W; break;
        case "FLV": $ruta = PATH_STREAMING; break;
        default:    $ruta = SP_VIDEOS_W; break;
      }
      
      Forma_CampoPreview(ObtenEtiqueta(457), 'ds_ruta_video'.$i, $ds_ruta_video[$i], $ruta, True, False);
      Forma_CampoOculto('fl_video_contenido'.$i, $fl_video_contenido[$i]);
    }
    Forma_FileUploader(ObtenEtiqueta(216), False, 'archivo', "'flv', 'mov'", '500 * 1024 * 1024', '', False);
  }
  else
    Forma_FileUploader(ObtenEtiqueta(457), False, 'archivo', "'flv', 'mov'", '500 * 1024 * 1024', '', False);
    Forma_CampoInfo('NOTE', ObtenEtiqueta(187)); // NOTE: (Explicacion del codigo a usar para incrustar un video en el TinyMCE)
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_FIXED, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>