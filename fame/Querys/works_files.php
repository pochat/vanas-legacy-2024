<?php
  	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_log = ValidaSesion(False,0, True);
  $fl_perfil_log = ObtenPerfilUsuario($fl_usuario_log);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  
  # Recibe Parametros 
  $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp', true);
  $fl_alumno = RecibeParametroNumerico('fl_alumno', true);
  $fl_maestro = RecibeParametroNumerico('fl_teacher', true);
  
  # Query
  $Query  = "SELECT a.fl_worksfiles, a.ds_files, a.ds_version, a.ds_descripcion, ";
  $Query .= "DATE_FORMAT(a.fe_file, '%b %d, %Y  at %H:%m' ), a.no_orden, b.fl_instituto, a.fl_usu_upload fl_alu, ";
  $Query .= "CASE fl_perfil_sp WHEN '".PFL_MAESTRO_SELF."' THEN '".ObtenEtiqueta(2228)."' ELSE '".ObtenEtiqueta(2229)."' END usuario ";
  $Query .= "FROM k_worksfiles a, c_usuario b ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario AND a.fl_alumno=".$fl_alumno." AND a.fl_leccion=".$fl_leccion_sp." AND a.fg_campus='0' ";
  $Query .= "ORDER BY fe_file DESC ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
"data": [
<?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
    $fl_worksfiles = $row[0];
    $ds_files = str_texto($row[1]);
    $ds_version = str_texto($row[2]);
    $ds_descripcion = str_texto($row[3]);
    $fe_file = $row[4];
    $no_orden = $row[5];
    $fl_instituto = $row[6];
    $fl_alu = $row[7];
    $usuario = $row[8];    
    $download = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_alumno."/works_files/".$ds_files;
    $btn = "<a href='".$download."' target='_blank' class='btn btn-xs btn-default' data-original-title='Download'><i class='fa fa-download'></i></a>&nbsp;";
    if(($fl_alu==$fl_maestro || $fl_alu==$fl_alumno)  && $fl_alu==$fl_usuario_log)
      $btn .= "<a href='javascript:void(0);' onclick='del_file(".$fl_alumno.", ".$fl_leccion_sp.", ".$fl_worksfiles.");' class='btn btn-xs btn-default' data-original-title='Delete'><i class='fa fa-times'></i></a>";
    
    # Obtenemos el avatar
    $ds_avatar = ObtenAvatarUsuario($fl_alu);
    $ds_nombres = ObtenNombreUsuario($fl_alu);
      echo '
      {
       "id": "<a href=\''.$download.'\' target=\'_blank\'>'.$i.'</a>",
       "name": "<a href=\''.$download.'\' target=\'_blank\'>'.$ds_files.'</a>",
       "version": "<a href=\''.$download.'\' target=\'_blank\'>'.$ds_version.'</a>",
       "descr": "<a href=\''.$download.'\' target=\'_blank\'>'.$ds_descripcion.'</a>",
       "date": "<a href=\''.$download.'\' target=\'_blank\'>'.$fe_file.'</a>",
       "user": "<div class=\'project-members\'><a href=\''.$download.'\' target=\'_blank\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombres.'\'><img src=\''.$ds_avatar.'\' width=\'35\' height=\'35\'/></a></div>",
      "btns": "'.$btn.'"
      }';
       if($registros>1 && $i<=($registros-1))
        echo ",";
      else
        echo "";
  }
?>
 ]
}