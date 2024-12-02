<?php
  # Librerias
  require '../../lib/general.inc.php';
  $Query  = "SELECT fl_blog, ".ConsultaFechaBD('fe_blog', FMT_FECHA)." '".ObtenEtiqueta(450)."', ds_titulo '".ETQ_TITULO."', ";
  $Query .= "CASE WHEN fg_maestros='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(451)."|center', ";
  $Query .= "CASE WHEN fg_alumnos='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(452)."|center', ";
  $Query .= "CASE WHEN fg_notificacion='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(453)."|center', ds_login '".ObtenEtiqueta(454)."', ";
  $Query .= "no_hits '".ObtenEtiqueta(455)."|right', a.ds_ruta_video,a.ds_progress_video ";
  $Query .= "FROM c_blog a, c_usuario b ";
  $Query .= "WHERE a.fl_usuario=b.fl_usuario ";
  $Query .= "ORDER BY fe_blog DESC";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++){
      $fl_blog = $row[0];
      $fe_blog = $row[1];
      $ds_titulo = $row[2];
      $fg_maestros = $row[3];
      if($fg_maestros==ObtenEtiqueta(NO_ETQ_SI))
        $lbl_maes = "success";
      if($fg_maestros==ObtenEtiqueta(NO_ETQ_NO))
        $lbl_maes = "danger";
      $fg_alumnos = $row[4];
      if($fg_alumnos==ObtenEtiqueta(NO_ETQ_SI))
        $lbl_alu = "success";
      if($fg_alumnos==ObtenEtiqueta(NO_ETQ_NO))
        $lbl_alu = "danger";
      $fg_notificacion = $row[5];
      if($fg_notificacion==ObtenEtiqueta(NO_ETQ_SI))
        $lbl_noti = "success";
      if($fg_notificacion==ObtenEtiqueta(NO_ETQ_NO))
        $lbl_noti = "danger";
      $ds_login = $row[6];
      $no_hits = $row[7];
	  
	  
	  $ds_ruta_video=str_texto($row['ds_ruta_video']);
	  $vid_progres = $row['ds_progress_video'];
      
	
	  if($vid_progres){

		$progreso_video = "<div class='progress progress-xs' data-progressbar-value='".$vid_progres."'><div class='progress-bar'></div></div>";
	  }else{
		  
		$progreso_video="";  
		  
	  }
     
      echo '
        {            
            "datepublisher": "<a href=\'javascript:Envia(\"schoolnews_frm.php\",'.$fl_blog.');\'>'.$fe_blog.'</a>",
            "title": "<td><a href=\'javascript:Envia(\"schoolnews_frm.php\",'.$fl_blog.');\'>'.str_texto($ds_titulo).'</a></td>",
			"duration": "<td> <small class=\'text-muted\'><i>'.$ds_ruta_video.'</i></small> '.$progreso_video.'</td>",
            "teachers": "<td><span class=\'label label-'.$lbl_maes.'\'>'.$fg_maestros.'</span></td>",
            "students": "<td><span class=\'label label-'.$lbl_alu.'\'>'.$fg_alumnos.'</span></td>",            
            "send": "<td><span class=\'label label-'.$lbl_noti.'\'>'.$fg_notificacion.'</span></td>",
            "publisher": "<span class=\'sparkline text-align-center\' data-sparkline-type=\'line\' data-sparkline-width=\'100%\' data-sparkline-height=\'25px\'>'.$ds_login.'</span>",
            "view": " '.$no_hits.' ",
            "delete": "<a class=\'btn btn-xs btn-default\' title=\'Delete\' href=\'javascript:Borra(\"schoolnews_del.php\",'.$fl_blog.');\'><i class=\'fa fa-trash-o\'></i></a>"
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
    ]
}