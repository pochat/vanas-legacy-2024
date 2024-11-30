<?php
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe Parametros 
  $fl_leccion = RecibeParametroNumerico('fl_leccion', true);  
  $fl_teacher = ObtenMaestroAlumno($fl_alumno);
  
  # Query
  $Query  = "SELECT a.fl_worksfiles, a.ds_files, a.ds_version, a.ds_descripcion, DATE_FORMAT(a.fe_file, '%b %d, %Y  at %H:%m' ), a.no_orden, a.fl_usu_upload al, ";
  $Query .= "CASE b.fl_perfil  WHEN '".PFL_MAESTRO."' THEN '".ObtenEtiqueta(2224)."' ELSE '".ObtenEtiqueta(2225)."' END usuario ";
  $Query .= "FROM k_worksfiles a , c_usuario b ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario AND a.fl_alumno=".$fl_alumno." AND a.fl_leccion=".$fl_leccion." AND a.fg_campus='1' ";
  $Query .= "ORDER BY a.fe_file DESC ";
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
    $fl_alu = $row[6];
    $delete = "";
    $download = "../students/works_files/works_".$fl_alumno."/".$ds_files;
    if($fl_alu==$fl_alumno)
      $delete = "<a href='javascript:void(0);' onclick='del_file(".$fl_alumno.", ".$fl_leccion.", ".$fl_worksfiles.");' class='btn btn-xs btn-default' data-original-title='Edit Row'><i class='fa fa-times'></i></a>";
    $usuario = $row[7];
    
    # Obtenemos el avatar del usuario que lo subio
    $ds_avatar = ObtenAvatarUsuario($fl_alu);
    $ds_nombres = ObtenNombreUsuario($fl_alu);
    
	if($i==1){
		
		
		//$ds_files="";
		//$ds_version="";
		//$ds_descripcion="";
		//$fe_file="";
		//$ds_avatar="";
		
	}
	
	echo '
      {
       "id": "<a href=\''.$download.'\' target=\'_blank\' download>'.$i.'</a>",
       "name": "<a href=\''.$download.'\' target=\'_blank\' download>'.$ds_files.'</a>",
       "version": "<a href=\''.$download.'\' target=\'_blank\' download>'.$ds_version.'</a>",
       "descr": "<a href=\''.$download.'\' target=\'_blank\' download>'.$ds_descripcion.'</a>",
       "date": "<a href=\''.$download.'\' target=\'_blank\' download>'.$fe_file.'</a>",
       "user": "<div class=\'project-members\'><a href=\''.$download.'\' target=\'_blank\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombres.'\' download><img src=\''.$ds_avatar.'\' width=\'35\' height=\'35\'/></a></div>",
      "btns": "<a href=\''.$download.'\' target=\'_blank\' class=\'btn btn-xs btn-default\' data-original-title=\'Download\' download><i class=\'fa fa-download\'></i></a>&nbsp;'.$delete.'"
      }';
       if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
?>
 ]
}
