<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  
  # Revisa el perfil del usuario
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  
  # Recupera datos del usuario
  if($fl_perfil == PFL_MAESTRO) {
    $Query  = "SELECT a.fl_maestro, a.ds_ruta_avatar, ";
    $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
    $Query .= ConcatenaBD($concat)." 'ds_nombre', a.ds_empresa, ds_website, ds_pais ";
    $Query .= "FROM c_maestro a, c_usuario b, c_pais c ";
    $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
    $Query .= "AND a.fl_pais=c.fl_pais ";
    $Query .= "AND b.fg_activo='1' ";
    $Query .= "AND a.fl_maestro=$fl_usuario";
    $row = RecuperaValor($Query);
    if(!empty($row[0])) {
      $ds_nombre = str_uso_normal($row[2]);
      if(!empty($row[3]))
        $ds_empresa = str_uso_normal($row[3]);
      else
        $ds_empresa = "Not defined";
      $ds_website = str_uso_normal($row[4]);
      $ds_pais = str_uso_normal($row[5]);
    }
    echo "
      <b>$ds_nombre</b><br>
      <table border='".D_BORDES."' width='100%' cellpadding='1' cellspacing='0'>
        <tr><td width='25%'></td><td></td></tr>
        <tr><td align='left'>Company:</td><td align='left'>$ds_empresa</td></tr>
        <tr><td align='left'>Website:</td><td align='left'>$ds_website</td></tr>
        <tr><td align='left'>Country:</td><td align='left'>$ds_pais</td></tr>
      </table>";
  }
  else {
    $Query  = "SELECT a.fl_alumno, a.ds_ruta_avatar, ";
    $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
    $Query .= ConcatenaBD($concat)." 'ds_nombre', e.nb_programa, d.ds_pais, a.fl_alumno ";
    $Query .= "FROM c_alumno a, c_usuario b, k_ses_app_frm_1 c, c_pais d, c_programa e ";
    $Query .= "WHERE a.fl_alumno=b.fl_usuario ";
    $Query .= "AND b.cl_sesion=c.cl_sesion ";
    $Query .= "AND c.ds_add_country=d.fl_pais ";
    $Query .= "AND c.fl_programa=e.fl_programa ";
    $Query .= "AND b.fg_activo='1' ";
    $Query .= "AND a.fl_alumno=$fl_usuario";
    $row = RecuperaValor($Query);
    if(!empty($row[0])) {
      $fl_alumno = $row[0];
      $ds_nombre = str_uso_normal($row[2]);
      $nb_programa = str_uso_normal($row[3]);
      $no_nivel = ObtenGradoAlumno($row[5]);
      if(empty($no_nivel))
        $no_nivel = "(No group)";
      $nb_grupo = ObtenNombreGrupoAlumno($fl_alumno);
      if(empty($nb_grupo))
        $nb_grupo = "(No group)";
      $nb_maestro = ObtenNombreMaestroAlumno($fl_alumno);
      if(empty($nb_maestro))
        $nb_maestro = "(No group)";
      $ds_pais = str_uso_normal($row[4]);
    }
    echo "
      <b>$ds_nombre</b><br>
      <table border='".D_BORDES."' width='100%' cellpadding='1' cellspacing='0'>
        <tr><td width='25%'></td><td></td></tr>
        <tr><td align='left'>Course:</td><td align='left'>$nb_programa</td></tr>
        <tr><td align='left'>Term:</td><td align='left'>$no_nivel</td></tr>
        <tr><td align='left'>Group:</td><td align='left'>$nb_grupo</td></tr>
        <tr><td align='left'>Teacher:</td><td align='left'>$nb_maestro</td></tr>
        <tr><td align='left'>Country:</td><td align='left'>$ds_pais</td></tr>
      </table>";
  }
  
?>