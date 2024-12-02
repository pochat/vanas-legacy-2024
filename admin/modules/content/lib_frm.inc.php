<?php
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  $fl_perfil = ObtenPerfil($fl_usuario);
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso($func, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Revisa si se pueden dar de alta nodos
  if(empty($clave)) {
    $Query  = "SELECT COUNT(1) ";
    $Query .= "FROM c_funcion a, k_flujo_nivel b ";
    $Query .= "WHERE a.fl_flujo=b.fl_flujo ";
    $Query .= "AND b.no_nivel=1 ";
    $Query .= "AND a.cl_tipo_contenido=$cl_tipo_contenido ";
    if($fl_usuario != ADMINISTRADOR) {
      $Query .= "AND (";
      $Query .= "EXISTS(SELECT 1 FROM k_nivel_usuario WHERE fl_flujo=b.fl_flujo AND no_nivel=b.no_nivel AND fl_usuario=$fl_usuario) ";
      $Query .= "OR EXISTS(SELECT 1 FROM k_nivel_perfil WHERE fl_flujo=b.fl_flujo AND no_nivel=b.no_nivel AND fl_perfil=$fl_perfil)";
      $Query .= ")";
    }
    $row = RecuperaValor($Query);
    if($row[0] == 0) {
      MuestraPaginaError(105); // No se enconto ninguna seccion disponible para crear un nodo para su usuario.
      exit;
    }
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT a.fl_funcion, ".EscogeIdioma('b.nb_funcion', 'b.tr_funcion').", a.cl_template, c.nb_template, a.nb_titulo, ";
      $Query .= "a.tr_titulo, a.ds_resumen, a.tr_resumen, ";
      $Query .= ConsultaFechaBD('a.fe_ini', FMT_CAPTURA)." fe_ini, ".ConsultaFechaBD('a.fe_fin', FMT_CAPTURA)." fe_fin, ";
      $Query .= "a.no_orden, a.fg_fijo, a.fg_activo, b.fl_flujo, a.fl_usuario_alta, a.fl_usuario_mod, ";
      $concat = array(ConsultaFechaBD('a.fe_alta', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_alta', FMT_HORA));
      $Query .= "(".ConcatenaBD($concat).") fe_alta, ";
      $concat = array(ConsultaFechaBD('a.fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_ultmod', FMT_HORA));
      $Query .= "(".ConcatenaBD($concat).") fe_ultmod, no_nivel, ";
      $Query .= ConsultaFechaBD('a.fe_evento', FMT_CAPTURA)." fe_evento, a.fg_menu, b.nb_flash_default ";
      $Query .= "FROM c_contenido a, c_funcion b, c_template c ";
      $Query .= "WHERE a.fl_funcion=b.fl_funcion ";
      $Query .= "AND a.cl_template=c.cl_template ";
      $Query .= "AND a.fl_contenido=$clave";
      $row = RecuperaValor($Query);
      $fl_funcion = $row[0];
      $nb_funcion = $row[1];
      $cl_template = $row[2];
      $nb_template = $row[3];
      $nb_titulo = str_texto($row[4]);
      $tr_titulo = str_texto($row[5]);
      $ds_resumen = str_texto($row[6]);
      $tr_resumen = str_texto($row[7]);
      $fe_ini = $row[8];
      $fe_fin = $row[9];
      $no_orden = $row[10];
      $fg_fijo = $row[11];
      $fg_activo = $row[12];
      $fl_flujo = $row[13];
      $fl_usuario_alta = $row[14];
      $fl_usuario_mod = $row[15];
      $fe_alta = $row[16];
      $fe_ultmod = $row[17];
      $no_nivel = $row[18];
      $fe_evento = $row[19];
      $fg_menu = $row[20];
      $nb_flash_default = $row[21];
      $concat = array("ds_nombres", "' '", "ds_apaterno");
      $row = RecuperaValor("SELECT (".ConcatenaBD($concat).") FROM c_usuario WHERE fl_usuario=$fl_usuario_alta");
      $ds_usuario_alta = str_texto($row[0]);
      $row = RecuperaValor("SELECT (".ConcatenaBD($concat).") FROM c_usuario WHERE fl_usuario=$fl_usuario_mod");
      $ds_usuario_mod = str_texto($row[0]);
      
      # Recupera caracteristicas del template
      $Query  = "SELECT fg_titulo, fg_resumen, fg_fecha_evento, no_texto, no_imagen_dinamica, no_flash, no_tabla, fg_anexo ";
      $Query .= "FROM c_template WHERE cl_template=$cl_template";
      $row = RecuperaValor($Query);
      $fg_titulo = $row[0];
      $fg_resumen = $row[1];
      $fg_fecha_evento = $row[2];
      $no_texto = $row[3];
      $no_imagen_dinamica = $row[4];
      $no_flash = $row[5];
      $no_tabla = $row[6];
      $fg_anexo = $row[7];
      
      # Recupera textos asociados al contenido
      if($no_texto > 0) {
        $rs = EjecutaQuery("SELECT no_orden, fl_texto, ds_contenido, tr_contenido FROM k_texto WHERE fl_contenido=$clave");
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
          $no_reg = $row[0];
          $fl_texto[$no_reg] = $row[1];
          $ds_contenido[$no_reg] = str_texto($row[2]);
          $tr_contenido[$no_reg] = str_texto($row[3]);
        }
      }
      
      # Recupera imagenes dinamicas asociadas al contenido
      if($no_imagen_dinamica > 0) {
        $Query  = "SELECT no_orden, fl_imagen_dinamica, ds_caption, tr_caption, nb_archivo, tr_archivo, ds_alt, tr_alt, ds_liga ";
        $Query .= "FROM k_imagen_dinamica WHERE fl_contenido=$clave";
        $rs = EjecutaQuery($Query);
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
          $no_reg = $row[0];
          $fl_imagen_dinamica[$no_reg] = $row[1];
          $ds_caption_i[$no_reg] = str_texto($row[2]);
          $tr_caption_i[$no_reg] = str_texto($row[3]);
          $nb_archivo_i[$no_reg] = str_texto($row[4]);
          $tr_archivo_i[$no_reg] = str_texto($row[5]);
          $ds_alt_i[$no_reg] = str_texto($row[6]);
          $tr_alt_i[$no_reg] = str_texto($row[7]);
          $ds_liga_i[$no_reg] = str_texto($row[8]);
        }
      }
      
      # Recupera archivos de flash asociados al contenido por template
      if($no_flash > 0) {
        $rs = EjecutaQuery("SELECT no_orden, fl_flash, nb_archivo, tr_archivo, no_width, no_height FROM k_flash WHERE fl_contenido=$clave");
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
          $no_reg = $row[0];
          $fl_flash[$no_reg] = $row[1];
          $nb_archivo_f[$no_reg] = str_texto($row[2]);
          $tr_archivo_f[$no_reg] = str_texto($row[3]);
          $no_width_f[$no_reg] = $row[4];
          $no_height_f[$no_reg] = $row[5];
        }
      }
      
      # Recupera las tablas asociadas al contenido
      if($no_tabla > 0) {
        $rs = EjecutaQuery("SELECT no_orden, fl_tabla FROM k_tabla WHERE fl_contenido=$clave");
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
          $no_reg = $row[0];
          $fl_tabla[$no_reg] = $row[1];
        }
      }
      
      # Recupera los archivos anexos asociados al contenido
      if($fg_anexo == 1) {
        $Query  = "SELECT fl_anexo, no_orden, ds_caption, tr_caption, nb_archivo, tr_archivo, ds_texto, tr_texto, nb_imagen ";
        $Query .= "FROM k_anexo ";
        $Query .= "WHERE fl_contenido=$clave ";
        $Query .= "ORDER BY no_orden";
        $rs = EjecutaQuery($Query);
        for($i = 0; $row = RecuperaRegistro($rs); $i++) {
          $fl_anexo[$i] = $row[0];
          $no_orden_a[$i] = $row[1];
          $ds_caption_a[$i] = str_texto($row[2]);
          $tr_caption_a[$i] = str_texto($row[3]);
          $nb_archivo_a[$i] = str_texto($row[4]);
          $tr_archivo_a[$i] = str_texto($row[5]);
          $ds_texto_a[$i] = str_texto($row[6]);
          $tr_texto_a[$i] = str_texto($row[7]);
          $nb_imagen_a[$i] = str_texto($row[8]);
        }
        $regs_ini_anexos = $i;
        $tot_regs_anexos = $i;
        $regs_borrar_anexos = '';
      }
      
      # Ligas
      if($cl_tipo_contenido == TC_LIGA) {
        $row = RecuperaValor("SELECT fl_seccion, cl_pagina, ds_ruta, tr_ruta, fg_ventana FROM k_liga WHERE fl_contenido=$clave");
        $fl_seccion = $row[0];
        $cl_pagina = $row[1];
        $ds_ruta = str_texto($row[2]);
        $tr_ruta = str_texto($row[3]);
        $fg_ventana = $row[4];
        if($fl_seccion <> "") {
          $row = RecuperaValor("SELECT nb_funcion, tr_funcion FROM c_funcion WHERE fl_funcion=$fl_seccion");
          $nb_seccion = str_texto(EscogeIdioma($row[0], $row[1]));
        }
        if($cl_pagina <> "") {
          $row = RecuperaValor("SELECT nb_pagina FROM c_pagina WHERE cl_pagina=$cl_pagina");
          $nb_pagina = str_texto($row[0]);
        }
      }
    }
    else { // Alta, inicializa campos
      $fl_funcion = "";
      $nb_funcion = "";
      $cl_template = "";
      $nb_template = "";
      $fe_ini = "";
      $fe_fin = "";
      $no_orden = "1";
      $fg_fijo = "0";
      $fg_activo = "0";
      $fe_alta = "";
      $fe_ultmod = "";
      $no_nivel = "1";
      $nb_titulo = "";
      $tr_titulo = "";
      $ds_resumen = "";
      $tr_resumen = "";
      $ds_contenido = "";
      $tr_contenido = "";
      $fe_evento = "";
      if($cl_tipo_contenido <> TC_NOTICIA)
        $fg_menu = "1";
      else
        $fg_menu = "0";
      $nb_flash_default = "";
      $concat = array("ds_nombres", "' '", "ds_apaterno");
      $row = RecuperaValor("SELECT (".ConcatenaBD($concat).") FROM c_usuario WHERE fl_usuario=$fl_usuario");
      $ds_usuario_alta = $row[0];
      $ds_usuario_mod = "";
    }
    $nb_funcion_err = "";
    $nb_template_err = "";
    $no_orden_err = "";
    $fe_ini_err = "";
    $fe_fin_err = "";
    $fe_evento_err = "";
    $ds_nota = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $fl_funcion = RecibeParametroNumerico('fl_funcion');
    $nb_funcion = RecibeParametroHTML('nb_funcion');
    $nb_funcion_err = RecibeParametroNumerico('nb_funcion_err');
    $cl_template = RecibeParametroNumerico('cl_template');
    $nb_template = RecibeParametroHTML('nb_template');
    $nb_template_err = RecibeParametroNumerico('nb_template_err');
    $nb_titulo = RecibeParametroHTML('nb_titulo');
    $tr_titulo = RecibeParametroHTML('tr_titulo');
    $ds_resumen = RecibeParametroHTML('ds_resumen');
    $tr_resumen = RecibeParametroHTML('tr_resumen');
    $ds_contenido = RecibeParametroHTML('ds_contenido');
    $tr_contenido = RecibeParametroHTML('tr_contenido');
    $fe_ini = RecibeParametroFecha('fe_ini');
    $fe_ini_err = RecibeParametroNumerico('fe_ini_err');
    $fe_fin = RecibeParametroFecha('fe_fin');
    $fe_fin_err = RecibeParametroNumerico('fe_fin_err');
    $no_orden = RecibeParametroNumerico('no_orden');
    $no_orden_err = RecibeParametroNumerico('no_orden_err');
    $fg_fijo = RecibeParametroNumerico('fg_fijo');
    $fg_activo = RecibeParametroNumerico('fg_activo');
    $ds_nota = RecibeParametroHTML('ds_nota');
    $ds_usuario_alta = RecibeParametroHTML('ds_usuario_alta');
    $ds_usuario_mod = RecibeParametroHTML('ds_usuario_mod');
    $fe_alta = RecibeParametroFecha('fe_alta');
    $fe_ultmod = RecibeParametroFecha('fe_ultmod');
    $no_nivel = RecibeParametroNumerico('no_nivel');
    $fe_evento = RecibeParametroFecha('fe_evento');
    $fe_evento_err = RecibeParametroNumerico('fe_evento_err');
    $fg_menu = RecibeParametroNumerico('fg_menu');
    $fg_titulo = RecibeParametroNumerico('fg_titulo');
    $fg_resumen = RecibeParametroNumerico('fg_resumen');
    $fg_fecha_evento = RecibeParametroNumerico('fg_fecha_evento');
    $no_texto = RecibeParametroNumerico('no_texto');
    $no_imagen_dinamica = RecibeParametroNumerico('no_imagen_dinamica');
    $no_flash = RecibeParametroNumerico('no_flash');
    $no_tabla = RecibeParametroNumerico('no_tabla');
    $fg_anexo = RecibeParametroNumerico('fg_anexo');
    
    # Recupera el flujo de autorizacion
    $row = RecuperaValor("SELECT fl_flujo FROM c_funcion WHERE fl_funcion=$fl_funcion");
    $fl_flujo = $row[0];
    
    # Recupera textos asociados al contenido
    for($i = 1; $i <= $no_texto; $i++) {
      $fl_texto[$i] = RecibeParametroNumerico('fl_texto_'.$i);
      $ds_contenido[$i] = RecibeParametroHTML('ds_contenido_'.$i);
      $tr_contenido[$i] = RecibeParametroHTML('tr_contenido_'.$i);
    }
    
    # Recupera imagenes dinamicas asociadas al contenido
    for($i = 1; $i <= $no_imagen_dinamica; $i++) {
      $fl_imagen_dinamica[$i] = RecibeParametroNumerico('fl_imagen_dinamica_'.$i);
      $ds_caption_i[$i] = RecibeParametroHTML('ds_caption_i_'.$i);
      $tr_caption_i[$i] = RecibeParametroHTML('tr_caption_i_'.$i);
      $nb_archivo_i[$i] = RecibeParametroHTML('nb_archivo_i_'.$i);
      $tr_archivo_i[$i] = RecibeParametroHTML('tr_archivo_i_'.$i);
      $ds_alt_i[$i] = RecibeParametroHTML('ds_alt_i_'.$i);
      $tr_alt_i[$i] = RecibeParametroHTML('tr_alt_i_'.$i);
      $ds_liga_i[$i] = RecibeParametroHTML('ds_liga_i_'.$i);
    }
    
    # Recupera archivos de flash asociados al contenido
    for($i = 1; $i <= $no_flash; $i++) {
      $fl_flash[$i] = RecibeParametroNumerico('fl_flash_'.$i);
      $nb_archivo_f[$i] = RecibeParametroHTML('nb_archivo_f_'.$i);
      $tr_archivo_f[$i] = RecibeParametroHTML('tr_archivo_f_'.$i);
      $no_width_f[$i] = RecibeParametroNumerico('no_width_f_'.$i);
      $no_width_f_err[$i] = RecibeParametroNumerico('no_width_f_err_'.$i);
      $no_height_f[$i] = RecibeParametroNumerico('no_height_f_'.$i);
      $no_height_f_err[$i] = RecibeParametroNumerico('no_height_f_err_'.$i);
    }
    
    # Recupera las tablas asociadas al contenido
    for($i = 1; $i <= $no_tabla; $i++)
      $fl_tabla[$i] = RecibeParametroNumerico('fl_tabla_'.$i);
    
    # Recupera anexos asociados al contenido
    if($fg_anexo == 1) {
      $regs_ini_anexos = RecibeParametroNumerico('regs_ini_anexos');
      $tot_regs_anexos = RecibeParametroNumerico('tot_regs_anexos');
      $regs_borrar_anexos = RecibeParametroHTML('regs_borrar_anexos');
      for($i = 0; $i < $tot_regs_anexos; $i++) {
        $fl_anexo[$i] = RecibeParametroNumerico('fl_anexo_'.$i);
        $no_orden_a[$i] = RecibeParametroNumerico('no_orden_a_'.$i);
        $ds_caption_a[$i] = RecibeParametroHTML('ds_caption_a_'.$i);
        $ds_caption_a_err[$i] = RecibeParametroNumerico('ds_caption_a_err_'.$i);
        $tr_caption_a[$i] = RecibeParametroHTML('tr_caption_a_'.$i);
        $nb_archivo_a[$i] = RecibeParametroHTML('nb_archivo_a_'.$i);
        $tr_archivo_a[$i] = RecibeParametroHTML('tr_archivo_a_'.$i);
        $ds_texto_a[$i] = RecibeParametroHTML('ds_texto_a_'.$i);
        $tr_texto_a[$i] = RecibeParametroHTML('tr_texto_a_'.$i);
        $nb_imagen_a[$i] = RecibeParametroHTML('nb_imagen_a_'.$i);
      }
    }
    
    # Recupera Ligas
    if($cl_tipo_contenido == TC_LIGA) {
      $fl_seccion = RecibeParametroNumerico('fl_seccion');
      $cl_pagina = RecibeParametroNumerico('cl_pagina');
      $ds_ruta = RecibeParametroHTML('ds_ruta');
      $tr_ruta = RecibeParametroHTML('tr_ruta');
      $fg_ventana = RecibeParametroNumerico('fg_ventana');
      $nb_seccion = RecibeParametroHTML('nb_seccion');
      $nb_pagina = RecibeParametroHTML('nb_pagina');
    }
  }
  
  # Recupera archivos de video streaming asociados al contenido. Aplica solo a Pages y News
    if($cl_tipo_contenido == TC_NODO || $cl_tipo_contenido == TC_NOTICIA) {
      $rs = EjecutaQuery("SELECT fl_flash, nb_archivo FROM k_flash WHERE fl_contenido=$clave");
      $no_videos =  CuentaRegistros($rs);
      for($i = 1; $row = RecuperaRegistro($rs); $i++) {
        $fl_flash_stream[$i] = $row[0];
        $nb_archivo_stream[$i] = str_texto($row[1]);
      }
    }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado($func);
  
  # Funciones para manejo de anexos
  if($fg_anexo == 1)
    echo "<script src='".PATH_JS."/frmAnexos.js.php'></script>";
  
  # Ventana para preview de imagenes, archivos de flash y anexos
  if(!empty($no_imagen_dinamica) OR !empty($no_flash) OR $fg_anexo == 1)
    require 'preview.inc.php';
  
  # Inicia forma para captura de datos
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError( );
  
  # Datos generales
  if(!empty($clave)) {
    Forma_CampoInfo(ObtenEtiqueta(154), $nb_funcion);
    Forma_CampoOculto('fl_funcion', $fl_funcion);
    Forma_CampoOculto('nb_funcion', $nb_funcion);
    Forma_CampoInfo(ObtenEtiqueta(153), $nb_template);
    Forma_CampoOculto('cl_template', $cl_template);
    Forma_CampoOculto('nb_template', $nb_template);
    Forma_CampoOculto('fg_titulo', $fg_titulo);
    Forma_CampoOculto('fg_resumen', $fg_resumen);
    Forma_CampoOculto('fg_fecha_evento', $fg_fecha_evento);
    Forma_CampoOculto('no_texto', $no_texto);
    Forma_CampoOculto('no_imagen_dinamica', $no_imagen_dinamica);
    Forma_CampoOculto('no_flash', $no_flash);
    Forma_CampoOculto('no_tabla', $no_tabla);
    Forma_CampoOculto('fg_anexo', $fg_anexo);
  }
  else {
    Forma_CampoOculto('tipo_contenido', $cl_tipo_contenido);
    Forma_CampoLOV(ObtenEtiqueta(154), True, 'fl_funcion', $fl_funcion, 'nb_funcion', $nb_funcion, 35, 
      LOV_SECCIONES, LOV_TIPO_RADIO, LOV_ENORME, 'tipo_contenido', $nb_funcion_err);
    Forma_CampoLOV(ObtenEtiqueta(153), True, 'cl_template', $cl_template, 'nb_template', $nb_template, 35, 
      LOV_TEMPLATES, LOV_TIPO_RADIO, LOV_MEDIANO, 'fl_funcion', $nb_template_err);
  }
  Forma_CampoTexto(ObtenEtiqueta(190).' '.ETQ_FMT_FECHA, False, 'fe_ini', $fe_ini, 10, 10, $fe_ini_err);
  Forma_Calendario('fe_ini');
  Forma_CampoTexto(ObtenEtiqueta(192).' '.ETQ_FMT_FECHA, False, 'fe_fin', $fe_fin, 10, 10, $fe_fin_err);
  Forma_Calendario('fe_fin');
  if($cl_tipo_contenido <> TC_EVENTO) {
    Forma_CampoCheckbox(ObtenEtiqueta(165), 'fg_menu', $fg_menu, ObtenEtiqueta(220));
    Forma_CampoTexto(ETQ_ORDEN, False, 'no_orden', $no_orden, 5, 5, $no_orden_err);
  }
  else {
    Forma_CampoOculto('fg_menu', '');
    Forma_CampoOculto('no_orden', '0');
  }
  if($fg_fijo == 1) 
    $texto = ETQ_SI;
  else
    $texto = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(161), $texto);
  Forma_CampoOculto('fg_fijo', $fg_fijo);
  Forma_Espacio( );
  
  # Contenido
  if(!empty($clave)) {
    if($cl_tipo_contenido == TC_LIGA) {
      Forma_Seccion(ObtenEtiqueta(203)); // Liga
      Forma_Espacio( );
      Forma_CampoTexto(ObtenEtiqueta(225), False, 'ds_ruta', $ds_ruta, 255, 90);
      Forma_CampoTexto(ObtenEtiqueta(245), False, 'tr_ruta', $tr_ruta, 255, 90);
      Forma_CampoLOV(ObtenEtiqueta(188), False, 'cl_pagina', $cl_pagina, 'nb_pagina', $nb_pagina, 35, 
        LOV_PAGINAS, LOV_TIPO_RADIO, LOV_GRANDE, '', '', True);
      Forma_CampoLOV(ObtenEtiqueta(224), False, 'fl_seccion', $fl_seccion, 'nb_seccion', $nb_seccion, 35, 
        LOV_SECCIONES, LOV_TIPO_RADIO, LOV_ENORME, '', '', True);
      Forma_CampoCheckbox(ObtenEtiqueta(223), 'fg_ventana', $fg_ventana);
    }
    else {
      Forma_Seccion(ObtenEtiqueta(194)); // Contenido
      Forma_Espacio( );
    }
    if($fg_fecha_evento) {
      Forma_CampoTexto(ObtenEtiqueta(193).' '.ETQ_FMT_FECHA, True, 'fe_evento', $fe_evento, 10, 10, $fe_evento_err);
      Forma_Calendario('fe_evento');
      Forma_Espacio( );
    }
    
    # Tabs con detalle del contenido
    if($fg_titulo OR $fg_resumen OR $no_flash > 0 OR $no_imagen_dinamica > 0 OR $no_texto > 0) {
      
      if(FG_TRADUCCION) {
        Forma_Doble_Ini( );
        if(IDIOMA_DEFAULT == ESPANOL)
          $tit_tab_1 = ETQ_ESPANOL;
        else
          $tit_tab_1 = ETQ_INGLES;
        $tit = array($tit_tab_1, ETQ_TRADUCCION);
        PresentaTabs($tit);
        
        # Tab Espanol
        TabIni(1);
        Forma_Tab_Ini( );
      }
      
      if($fg_titulo) {
        Forma_CampoTexto(ETQ_TITULO, False, 'nb_titulo', $nb_titulo, 100, 60);
        Forma_Espacio( );
      }
      if($fg_resumen) {
        if($cl_tipo_contenido <> TC_NODO)
          Forma_CampoTinyMCE(ObtenEtiqueta(191), False, 'ds_resumen', $ds_resumen, 50, 10);
        else {
          Forma_CampoTexto(ObtenEtiqueta(228), False, 'ds_resumen', $ds_resumen, 100, 60);
          Forma_CampoInfo('NOTE', ObtenEtiqueta(229));
        }
        Forma_Espacio( );
      }
      for($i = 1; $i <= $no_flash; $i++) {
        $Query  = "SELECT ds_prompt, tr_prompt ";
        $Query .= "FROM k_prompt_template ";
        $Query .= "WHERE cl_template=$cl_template ";
        $Query .= "AND fg_tipo='F' ";
        $Query .= "AND no_orden=$i";
        $row = RecuperaValor($Query);
        $prompt = EscogeIdioma($row[0], $row[1]);
        if(empty($prompt))
          $prompt = ObtenEtiqueta(210)." ".$i;
        if(empty($nb_archivo_f[$i]))
          $nb_archivo_f[$i] = $nb_flash_default;
        if(!empty($nb_archivo_f[$i])) {
          Forma_CampoPreview($prompt, 'nb_archivo_f_'.$i, $nb_archivo_f[$i], SP_FLASH_W);
          Forma_CampoArchivo(ObtenEtiqueta(216), False, 'archivo_f_'.$i, 60);
        }
        else
          Forma_CampoArchivo($prompt, False, 'archivo_f_'.$i, 60);
        //Forma_CampoTexto(ObtenEtiqueta(218), True, 'no_width_f_'.$i, $no_width_f[$i], 5, 3, $no_width_f_err[$i]);
        //Forma_CampoTexto(ObtenEtiqueta(219), True, 'no_height_f_'.$i, $no_height_f[$i], 5, 3, $no_height_f_err[$i]);
        $no_width_f[$i] = ObtenConfiguracion(8);
        $no_height_f[$i] = ObtenConfiguracion(9);
        Forma_CampoOculto('no_width_f_'.$i, $no_width_f[$i]);
        Forma_CampoOculto('no_height_f_'.$i, $no_height_f[$i]);
        Forma_CampoOculto('fl_flash_'.$i, $fl_flash[$i]);
        Forma_Espacio( );
      }
      for($i = 1; $i <= $no_imagen_dinamica; $i++) {
        $Query  = "SELECT ds_prompt, tr_prompt ";
        $Query .= "FROM k_prompt_template ";
        $Query .= "WHERE cl_template=$cl_template ";
        $Query .= "AND fg_tipo='I' ";
        $Query .= "AND no_orden=$i";
        $row = RecuperaValor($Query);
        $prompt = EscogeIdioma($row[0], $row[1]);
        if(empty($prompt))
          $prompt = ObtenEtiqueta(208)." ".$i;
        if(!empty($nb_archivo_i[$i])) {
          Forma_CampoPreview($prompt, 'nb_archivo_i_'.$i, $nb_archivo_i[$i], SP_IMAGES_W);
          Forma_CampoArchivo(ObtenEtiqueta(216), False, 'archivo_i_'.$i, 60);
        }
        else
          Forma_CampoArchivo($prompt, False, 'archivo_i_'.$i, 60);
        //Forma_CampoTexto(ObtenEtiqueta(207), False, 'ds_caption_i_'.$i, $ds_caption_i[$i], 50, 30);
        //Forma_CampoTexto(ObtenEtiqueta(209), False, 'ds_alt_i_'.$i, $ds_alt_i[$i], 50, 30);
        //Forma_CampoTexto(ObtenEtiqueta(203), False, 'ds_liga_i_'.$i, $ds_liga_i[$i], 255, 60);
        Forma_CampoOculto('ds_caption_i_'.$i, $ds_caption_i[$i]);
        Forma_CampoOculto('ds_alt_i_'.$i, $ds_alt_i[$i]);
        Forma_CampoOculto('ds_liga_i_'.$i, $ds_liga_i[$i]);
        Forma_CampoOculto('fl_imagen_dinamica_'.$i, $fl_imagen_dinamica[$i]);
        Forma_Espacio( );
      }
      for($i = 1; $i <= $no_texto; $i++) {
        $Query  = "SELECT ds_prompt, tr_prompt ";
        $Query .= "FROM k_prompt_template ";
        $Query .= "WHERE cl_template=$cl_template ";
        $Query .= "AND fg_tipo='X' ";
        $Query .= "AND no_orden=$i";
        $row = RecuperaValor($Query);
        $prompt = EscogeIdioma($row[0], $row[1]);
        if(empty($prompt))
          $prompt = ObtenEtiqueta(206)." ".$i;
        Forma_CampoTinyMCE($prompt, False, 'ds_contenido_'.$i, $ds_contenido[$i], 50, 20);
        Forma_CampoOculto('fl_texto_'.$i, $fl_texto[$i]);
        Forma_Espacio( );
      }
      if($no_texto > 0) {
        Forma_CampoInfo('NOTE', ObtenEtiqueta(189)); // NOTE: To create links use the url http://vanas.ca/content.php?contenido= and add the page number at the end. 
        Forma_Espacio( );
      }
      
      if(FG_TRADUCCION) {
        Forma_Tab_Fin( );
        TabFin( );
        
        # Tab Ingles
        TabIni(2);
        Forma_Tab_Ini( );
        Forma_Seccion(ObtenEtiqueta(227), False); // NOTA: Los campos que no se especifiquen tomaran su valor en espanol.
        Forma_Espacio( );
        if($fg_titulo) {
          Forma_CampoTexto(ETQ_TITULO, False, 'tr_titulo', $tr_titulo, 100, 60);
          Forma_Espacio( );
        }
        if($fg_resumen) {
          if($cl_tipo_contenido <> TC_NODO)
            Forma_CampoTextArea(ObtenEtiqueta(191), False, 'tr_resumen', $tr_resumen, 100, 5);
          else
            Forma_CampoTexto(ObtenEtiqueta(228), False, 'tr_resumen', $tr_resumen, 100, 60);
        Forma_Espacio( );
        }
        for($i = 1; $i <= $no_flash; $i++) {
          $Query  = "SELECT ds_prompt, tr_prompt ";
          $Query .= "FROM k_prompt_template ";
          $Query .= "WHERE cl_template=$cl_template ";
          $Query .= "AND fg_tipo='F' ";
          $Query .= "AND no_orden=$i";
          $row = RecuperaValor($Query);
          $prompt = EscogeIdioma($row[0], $row[1]);
          if(empty($prompt))
            $prompt = ObtenEtiqueta(210)." ".$i;
          if(!empty($tr_archivo_f[$i])) {
            Forma_CampoPreview($prompt, 'tr_archivo_f_'.$i, $tr_archivo_f[$i], SP_FLASH_W);
            Forma_CampoArchivo(ObtenEtiqueta(216), False, 'tr_archivo_ft_'.$i, 60);
          }
          else
            Forma_CampoArchivo($prompt, False, 'tr_archivo_ft_'.$i, 60);
          Forma_Espacio( );
        }
        for($i = 1; $i <= $no_imagen_dinamica; $i++) {
          $Query  = "SELECT ds_prompt, tr_prompt ";
          $Query .= "FROM k_prompt_template ";
          $Query .= "WHERE cl_template=$cl_template ";
          $Query .= "AND fg_tipo='I' ";
          $Query .= "AND no_orden=$i";
          $row = RecuperaValor($Query);
          $prompt = EscogeIdioma($row[0], $row[1]);
          if(empty($prompt))
            $prompt = ObtenEtiqueta(208)." ".$i;
          if(!empty($tr_archivo_i[$i])) {
            Forma_CampoPreview($prompt, 'tr_archivo_i_'.$i, $tr_archivo_i[$i], SP_IMAGES_W);
            Forma_CampoArchivo(ObtenEtiqueta(216), False, 'tr_archivo_it_'.$i, 60);
          }
          else
            Forma_CampoArchivo($prompt, False, 'tr_archivo_it_'.$i, 60);
          //Forma_CampoTexto(ObtenEtiqueta(207), False, 'tr_caption_i_'.$i, $tr_caption_i[$i], 50, 30);
          //Forma_CampoTexto(ObtenEtiqueta(209), False, 'tr_alt_i_'.$i, $tr_alt_i[$i], 50, 30);
          Forma_CampoOculto('tr_caption_i_'.$i, $tr_caption_i[$i]);
          Forma_CampoOculto('tr_alt_i_'.$i, $tr_alt_i[$i]);
          Forma_Espacio( );
        }
        for($i = 1; $i <= $no_texto; $i++) {
          $Query  = "SELECT ds_prompt, tr_prompt ";
          $Query .= "FROM k_prompt_template ";
          $Query .= "WHERE cl_template=$cl_template ";
          $Query .= "AND fg_tipo='X' ";
          $Query .= "AND no_orden=$i";
          $row = RecuperaValor($Query);
          $prompt = EscogeIdioma($row[0], $row[1]);
          if(empty($prompt))
            $prompt = ObtenEtiqueta(206)." ".$i;
          Forma_CampoTinyMCE($prompt, False, 'tr_contenido_'.$i, $tr_contenido[$i], 100, 20);
          Forma_Espacio( );
        }
        Forma_Tab_Fin( );
        TabFin( );
        
        CierraTabs( );
        Forma_Doble_Fin( );
        Forma_Espacio( );
      }
      else {
        if($fg_titulo)
          Forma_CampoOculto('tr_titulo', $tr_titulo);
        if($fg_resumen)
          Forma_CampoOculto('tr_resumen', $tr_resumen);
        for($i = 1; $i <= $no_flash; $i++)
          Forma_CampoOculto('tr_archivo_f_'.$i, $tr_archivo_f[$i]);
        for($i = 1; $i <= $no_imagen_dinamica; $i++) {
          Forma_CampoOculto('tr_archivo_i_'.$i, $tr_archivo_i[$i]);
          Forma_CampoOculto('tr_caption_i_'.$i, $tr_caption_i[$i]);
          Forma_CampoOculto('tr_alt_i_'.$i, $tr_alt_i[$i]);
        }
        for($i = 1; $i <= $no_texto; $i++)
          Forma_CampoOculto('tr_contenido_'.$i, $tr_contenido[$i]);
      }
    }
    
    # Tablas
    $Query  = "SELECT ' ' ".EscogeIdioma('nb_tabla', 'tr_tabla').", 0 ";
    $Query .= "UNION ";
    $Query .= "SELECT ".EscogeIdioma('nb_tabla', 'tr_tabla').", fl_tabla ";
    $Query .= "FROM c_tabla ";
    $Query .= "ORDER BY ".EscogeIdioma('nb_tabla', 'tr_tabla');
    for($i = 1; $i <= $no_tabla; $i++) {
      Forma_CampoSelectBD(ObtenEtiqueta(221)." ".$i, False, 'fl_tabla_'.$i, $Query, $fl_tabla[$i]);
      Forma_Espacio( );
    }
    
    # Anexos
    if($fg_anexo == 1) {
      $tit = array(ETQ_ORDEN, '* '.ETQ_NOMBRE."<br>".ObtenEtiqueta(211), ETQ_TRADUCCION."<br>".ETQ_TRADUCCION, ObtenEtiqueta(212)."<br>".ObtenEtiqueta(208), ObtenEtiqueta(245)."<br>&nbsp;", '&nbsp;');
      $ancho_col = array('5%', '20%', '20%', '25%', '25%', '5%');
      $tot_span = count($tit);
      Forma_Tabla_Ini('100%', $tit, $ancho_col, 'anexos');
      $impar = True;
      for($i = 0; $i < $tot_regs_anexos; $i++) {
        if(!empty($fl_anexo[$i]) OR (empty($fl_anexo[$i]) AND !empty($no_orden_a[$i]))) {
          if($impar) {
            $clase = "css_tabla_detalle";
            $clase_ico = "css_tabla_detalle_ico";
          }
          else {
            $clase = "css_tabla_detalle_bg";
            $clase_ico = "css_tabla_detalle_ico_bg";
          }
          $impar = !$impar;
          echo "
        <tr class='$clase' id='reg_anexos_$i'>\n";
          Forma_CampoOculto('fl_anexo_'.$i, $fl_anexo[$i]);
          echo "
          <td align='center' valign='top'>";
          CampoTexto('no_orden_a_'.$i, $no_orden_a[$i], 5, 1, 'css_input');
          echo "</td>
          <td valign='top'>";
          if($ds_caption_a_err[$i])
            $ds_clase = 'css_input_error';
          else
            $ds_clase = 'css_input';
          CampoTexto("ds_caption_a_$i", $ds_caption_a[$i], 255, 20, $ds_clase);
          echo "</td>
          <td valign='top'>";
          CampoTexto("tr_caption_a_$i", $tr_caption_a[$i], 255, 20, 'css_input');
          echo "</td>
          <td>";
          CampoArchivo('archivo_a_'.$i, 20, 'css_input');
          echo "<br>";
          if(!empty($nb_archivo_a[$i]))
            echo "<a href=\"".SP_ANEXOS_W."/".$nb_archivo_a[$i]."\" target='_blank'>$nb_archivo_a[$i]</a>";
          else
            echo ObtenEtiqueta(215);
          Forma_CampoOculto("nb_archivo_a_$i", $nb_archivo_a[$i]);
          echo "</td>
          <td>";
          CampoArchivo('tr_archivo_at_'.$i, 20, 'css_input');
          echo "<br>";
          if(!empty($tr_archivo_a[$i]))
            echo "<a href=\"".SP_ANEXOS_EN_W."/".$tr_archivo_a[$i]."\" target='_blank'>$tr_archivo_a[$i]</a>";
          else
            echo ObtenEtiqueta(215);
          Forma_CampoOculto("tr_archivo_a_$i", $tr_archivo_a[$i]);
          echo "</td>
          <td class='$clase_ico' align='center' valign='top''><a href=\"javascript:BorraEnTabla('anexos', '$i');\">
          <img src='".PATH_IMAGES."/".IMG_BORRAR."' width=17 height=16 border=0 title='".ETQ_ELIMINAR."'></a></td>
        </tr>
        <tr class='$clase' id='reg_anexos2_$i'>
          <td align='center'>&nbsp;</td>
          <td valign='top'>";
          CampoTexto("ds_texto_a_$i", $ds_texto_a[$i], 255, 20, 'css_input');
          echo "</td>
          <td valign='top'>";
          CampoTexto("tr_texto_a_$i", $tr_texto_a[$i], 255, 20, 'css_input');
          echo "</td>
          <td>";
          CampoArchivo('imagen_a_'.$i, 20, 'css_input');
          echo "<br>";
          if(!empty($nb_imagen_a[$i]))
            echo "<a href=\"javascript:Preview('".SP_IMAGES_W."/".$nb_imagen_a[$i]."');\">$nb_imagen_a[$i]</a>";
          else
            echo ObtenEtiqueta(215);
          Forma_CampoOculto("nb_imagen_a_$i", $nb_imagen_a[$i]);
          echo "</td>
          <td>&nbsp;</td>
          <td class='$clase_ico' align='center'>&nbsp;</td>
        </tr>\n";
        }
      }
      Forma_Tabla_Fin( );
      $fg_error = False;
      for($i = 0; $i < $tot_regs_anexos; $i++)
        $fg_error = $fg_error || $ds_caption_a_err[$i];
      Forma_Doble_Ini( );
      echo "
    <TABLE border='".D_BORDES."' cellPadding='3' cellSpacing='0' width='100%'>";
      if($fg_error) {
        $ds_error = ObtenMensaje(ERR_REQUERIDO);
        echo "
        <tr>
          <td class='css_msg_error'>$ds_error</td>
        </tr>";
      }
      echo "
      <tr>
        <td class='css_default'><a href=\"javascript:InsertaEnTabla('anexos');\"><img src='".PATH_IMAGES."/".IMG_NUEVO."' align=top valign=top width=17 height=16 border=0 title='".ETQ_INSERTAR."'> ".ETQ_INSERTAR."</a></td>
      </tr>
    </table>\n";
      Forma_Doble_Fin( );
      Forma_Seccion(ObtenEtiqueta(226), False); // NOTA: La imagen se generara automaticamente si no se especifica
      Forma_CampoOculto('regs_ini_anexos', $regs_ini_anexos);
      Forma_CampoOculto('tot_regs_anexos', $tot_regs_anexos);
      Forma_CampoOculto('regs_borrar_anexos', $regs_borrar_anexos);
      Forma_Espacio( );
    }
  }
  if($cl_tipo_contenido == TC_NODO || $cl_tipo_contenido == TC_NOTICIA)
  {
    Forma_CampoOculto('no_videos', $no_videos);
    
    if($no_videos > 0) {
      $ruta = PATH_STREAMING;
      for($i = 1; $i <= $no_videos; $i++)
      {
        Forma_CampoPreview(ObtenEtiqueta(457), 'ds_ruta_video'.$i, $nb_archivo_stream[$i], $ruta, True, False);
        Forma_CampoOculto('fl_video_contenido'.$i, $fl_flash_stream[$i]);
      }
      Forma_FileUploader(ObtenEtiqueta(216), False, 'archivo', "'flv', 'mov'", '500 * 1024 * 1024', '', False);
    }
    else
      Forma_FileUploader(ObtenEtiqueta(457), False, 'archivo', "'flv', 'mov'", '500 * 1024 * 1024', '', False);
    Forma_CampoInfo('NOTE', ObtenEtiqueta(187)); // NOTE: (Explicacion del codigo a usar para incrustar un video en el TinyMCE)
  }
  
  # Autorizacion
  Forma_Espacio( );
  Forma_Seccion(ObtenEtiqueta(201));
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(195), $ds_usuario_alta);
  Forma_CampoOculto('ds_usuario_alta', $ds_usuario_alta);
  Forma_CampoOculto('fg_activo', $fg_activo);
  if(!empty($clave)) {
    Forma_CampoInfo(ObtenEtiqueta(111), $fe_alta);
    Forma_CampoOculto('fe_alta', $fe_alta);
    if($fe_alta <> $fe_ultmod)
      Forma_CampoInfo(ObtenEtiqueta(197), $fe_ultmod.' '.ObtenEtiqueta(198).' '.$ds_usuario_mod);
    Forma_CampoOculto('ds_usuario_mod', $ds_usuario_mod);
    Forma_CampoOculto('fe_ultmod', $fe_ultmod);
    if($fg_activo == 1) 
      $texto = ETQ_SI;
    else
      $texto = ETQ_NO;
    Forma_CampoInfo(ObtenEtiqueta(199), $texto);
    
    # Determina el nivel del usuario en el flujo de trabajo
    $nivel_usuario = 0;
    $Query = "SELECT MAX(no_nivel) FROM k_nivel_usuario WHERE fl_flujo=$fl_flujo AND fl_usuario=$fl_usuario";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      $nivel_usuario = $row[0];
    $row = RecuperaValor("SELECT MAX(no_nivel) FROM k_nivel_perfil WHERE fl_flujo=$fl_flujo AND fl_perfil=$fl_perfil");
    if(!empty($row[0]) AND $row[0] > $nivel_usuario)
      $nivel_usuario = $row[0];
    
    # Cambio de estado
    Forma_Prompt(ObtenEtiqueta(205));
    $Query  = "SELECT a.no_nivel, ".EscogeIdioma('a.ds_nivel', 'a.tr_nivel').", ";
    if($fl_usuario != ADMINISTRADOR) {
      $Query .= "(SELECT COUNT(1) FROM k_nivel_usuario WHERE fl_flujo=a.fl_flujo AND no_nivel=a.no_nivel AND fl_usuario=$fl_usuario)+";
      $Query .= "(SELECT COUNT(1) FROM k_nivel_perfil WHERE fl_flujo=a.fl_flujo AND no_nivel=a.no_nivel AND fl_perfil=$fl_perfil) editar ";
    }
    else
      $Query .= "1 editar ";
    $Query .= "FROM k_flujo_nivel a ";
    $Query .= "WHERE a.fl_flujo=$fl_flujo ";
    $Query .= "ORDER BY a.no_nivel ";
    $rs = EjecutaQuery($Query);
    while($row = RecuperaRegistro($rs)) {
      if($row[2] > 0 OR $nivel_usuario > $row[0])
        $fg_editar = True;
      else
        $fg_editar = False;
      Forma_CampoRadio(False, 'no_nivel', $row[0], $no_nivel, $row[1], $fg_editar);
    }
    Forma_Espacio( );
    
    # Tabla con historia del flujo de trabajo
    $tit = array(ObtenEtiqueta(200), ETQ_USUARIO, ObtenEtiqueta(202), ObtenEtiqueta(196));
    $ancho_col = array('10%', '20%', '20%', '50%');
    Forma_Tabla_Ini('100%', $tit, $ancho_col);
    $concat = array("b.ds_nombres", "' '", "b.ds_apaterno");
    $Query  = "SELECT ".EscogeIdioma('a.ds_nivel', 'a.tr_nivel').", (".ConcatenaBD($concat)."), ";
    $concat = array(ConsultaFechaBD('a.fe_alta', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_alta', FMT_HORA));
    $Query .= "(".ConcatenaBD($concat).") fe_alta, a.ds_nota ";
    $Query .= "FROM k_estado_hist a, c_usuario b ";
    $Query .= "WHERE a.fl_usuario=b.fl_usuario ";
    $Query .= "AND a.fl_contenido=$clave ";
    $Query .= "ORDER BY a.fe_alta";
    $rs = EjecutaQuery($Query);
    for($i = 0; $row = RecuperaRegistro($rs); $i++) {
      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      echo "
      <tr class='$clase'>
        <td>".str_texto($row[0])."</td>
        <td>".str_texto($row[1])."</td>
        <td>$row[2]</td>
        <td>".str_texto($row[3])."</td>
      </tr>\n";
    }
    Forma_Tabla_Fin( );
    Forma_Espacio( );
  }
  else
    Forma_CampoOculto('no_nivel', $no_nivel);
  
  # Notas del que esta editando o revisando
  Forma_CampoTextArea(ObtenEtiqueta(196), False, "ds_nota", $ds_nota, 60, 3);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso($func, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
 ?>