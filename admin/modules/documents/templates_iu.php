<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  $fg_reset = RecibeParametroHTML('fg_reset');
  $fg_factory = RecibeParametroHTML('fg_factory');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_DOC_TEMPLATES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  
  if($fg_reset==1) 
  { 
    $Query  = "SELECT ds_encabezado_r, ds_cuerpo_r, ds_pie_r ";
    $Query .= "FROM k_template_doc ";
    $Query .= "WHERE fl_template = $clave";
    $row = RecuperaValor($Query);
    $Query  = "UPDATE k_template_doc SET ds_encabezado = '$row[0]', ds_cuerpo = '$row[1]', ds_pie = '$row[2]' WHERE fl_template = $clave";
    EjecutaQuery($Query);
  }
  
  else
  {
    # Recibe parametros
    $fg_error = 0;
    $nb_template = RecibeParametroHTML('nb_template');
    $fl_categoria = RecibeParametroNumerico('fl_categoria');
    $fg_activo = RecibeParametroHTML('fg_activo');
    if(!empty($fg_activo))
      $fg_activo = "1";
    $ds_encabezado = RecibeParametroHTML('ds_encabezado');
    $ds_cuerpo = RecibeParametroHTML('ds_cuerpo');
    $ds_pie = RecibeParametroHTML('ds_pie');
    
    # Valida campos obligatorios
    if(empty($nb_template))
      $nb_template_err = ERR_REQUERIDO;
    if($fl_categoria==0)
      $fl_categoria_err = ERR_REQUERIDO;
    
    # Regresa a la forma con error
     $fg_error = $nb_template_err || $fl_categoria_err;
    if($fg_error) { 
      echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
      Forma_CampoOculto('fg_error' , $fg_error);
      Forma_CampoOculto('nb_template' , $nb_template);
      Forma_CampoOculto('nb_template_err' , $nb_template_err);
      Forma_CampoOculto('fl_categoria' , $fl_categoria);
      Forma_CampoOculto('fl_categoria_err' , $fl_categoria_err);
      Forma_CampoOculto('fg_activo' , $fg_activo);
      Forma_CampoOculto('ds_encabezado' , $ds_encabezado);
      Forma_CampoOculto('ds_cuerpo' , $ds_cuerpo);
      Forma_CampoOculto('ds_pie' , $ds_pie);
      echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
      exit;
    }
    
    # Actualiza o inserta el registro
    if(!empty($clave)) {
      $Query  = "UPDATE k_template_doc ";
      $Query .= "SET nb_template='$nb_template', fl_categoria=$fl_categoria, fg_activo='$fg_activo', ";
      $Query .= "ds_encabezado='$ds_encabezado', ds_cuerpo='$ds_cuerpo', ds_pie='$ds_pie', fe_modificacion=NOW() ";
      if($fg_factory==1)
        $Query .= ",ds_encabezado_r='$ds_encabezado', ds_cuerpo_r='$ds_cuerpo', ds_pie_r='$ds_pie' ";
      $Query .= "WHERE fl_template=$clave";
    }
    else {
      $Query  = "INSERT INTO k_template_doc (nb_template, fl_categoria, fg_activo, ds_encabezado, ds_cuerpo, ds_pie, ";
      $Query .= "ds_encabezado_r, ds_cuerpo_r, ds_pie_r, fe_creacion, fe_modificacion) ";
      $Query .= "VALUES('$nb_template', $fl_categoria, '$fg_activo', '$ds_encabezado', '$ds_cuerpo', '$ds_pie', ";
      $Query .= "'$ds_encabezado', '$ds_cuerpo', '$ds_pie', NOW(), NOW())";
    }
    EjecutaQuery($Query);
  }
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>