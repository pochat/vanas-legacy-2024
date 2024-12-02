<?php
# Librerias
require '../../lib/general.inc.php';
require '../../../fame/lib/layout_front_back.php';

# Recibe Parametros
$criterio = RecibeParametroHTML('criterio');



# Consulta para el listado
//$Query  = "SELECT fl_programa, nb_programa '".ObtenEtiqueta(360)."', ds_duracion '".ObtenEtiqueta(361)."', ";
//$Query .= "ds_tipo '".ObtenEtiqueta(362)."', no_grados '".ObtenEtiqueta(365)."|right', ";
//$Query .= "no_orden '".ETQ_ORDEN."|right',a.fg_total_programa,a.fg_taxes    ,a.fg_tax_rate,
//           case when a.fg_fulltime=1 then 'Full Time'
//                else 'Part Time' end sschedule ";
//$Query .= "FROM c_programa a WHERE 1=1 and  fg_archive='0' ";
//$Query .= "ORDER BY no_orden";
//$rs = EjecutaQuery($Query);
//$registros = CuentaRegistros($rs);

$Query  = "SELECT K.fl_usu_pro,CONCAT (U.ds_nombres,' ',U.ds_apaterno) AS nombre ,I.ds_instituto,PA.ds_pais,P.nb_programa, ";
$Query .= "K.ds_progreso,K.no_promedio_t, K.fg_terminado,K.fg_certificado,K.fg_status,DATE_FORMAT(K.fe_enviado, '%b %d, %Y') fe_enviado, ";
$Query .= "K.fe_entregado ,K.fg_pagado, DATE_FORMAT(K.fe_enviado, '%r') hr_enviado, TIMESTAMPDIFF(YEAR,U.fe_nacimiento, CURDATE()) edad, U.fl_usuario, ";
$Query .= "U.fl_perfil_sp, U.fl_instituto, ";
$Query .= "U.ds_login, P.fl_programa_sp ";
$Query .= "FROM k_usuario_programa K ";
$Query .= "JOIN c_usuario U ON U.fl_usuario=K.fl_usuario_sp ";
$Query .= "LEFT JOIN c_programa_sp P ON P.fl_programa_sp=K.fl_programa_sp ";
$Query .= "LEFT JOIN c_instituto I ON I.fl_instituto=U.fl_instituto ";
$Query .= "LEFT JOIN c_pais PA ON PA.fl_pais=I.fl_pais ";
$Query .= "WHERE 1=1 AND K.fg_certificado='1'  ";
$rs = EjecutaQuery($Query);
$registros = CuentaRegistros($rs);




?>
{

"data": [
<?php
for ($i = 1; $row = RecuperaRegistro($rs); $i++) {

  // start random string
  $randString = rand(1000000, 9000000);
  $randString = base64_encode($randString);

  /**
   * falta pregubtarle gabriel en que monento cambian su estatus. y donde guarda el regisro donde se especific si fue pagado o no.
   */


  $progresso = $row['ds_progreso'];
  $fe_solicitud_certificado = $row['fe_enviado'];
  $estatus_certificado = $row['fg_status'];
  $no_promedio = $row['no_promedio_t'];
  $fg_pagado = $row['fg_pagado'];
  $hr_solicitud_certificado = $row['hr_enviado'];
  $age = $row['edad'];
  $fl_usuario = $row['fl_usuario'];
  $fl_perfil = $row['fl_perfil_sp'];
  $fl_instituto = $row['fl_instituto'];
  $ds_login = $row['ds_login'];
  $fl_programa_sp = $row['fl_programa_sp'];



  #damos formato si el certifiacdo se encuantra pagado.
  if ($fg_pagado) {

    $color_label_pagado = "success";
    $etq_pagado = "Yes";
  } else {

    $color_label_pagado = "danger";
    $etq_pagado = "No";
  }


  #calculamos el promedio y asignamos un estatus.
  $Query2 = "SELECT cl_calificacion, no_min, no_max FROM c_calificacion ";
  $rs2 = EjecutaQuery($Query2);
  $color_label_promedio = '';
  $etq_label_promedio = '';
  for ($j = 1; $row2 = RecuperaRegistro($rs2); $j++) {

    $no_min = $row2['no_min'];
    $no_max = $row2['no_max'];

    if (($no_promedio >= $no_min) && ($no_promedio <= $no_max)) {

      $color_label_promedio = "success";
      $etq_label_promedio = $row2['cl_calificacion'] . " ( " . $no_promedio . " %)"; #A A+ A- B+ 


    }
  }
  #formamos la etiqueta 




  #damos formato ala fecha
  // $fe_solicitud_certificado=strtotime('+0 day',strtotime($fe_solicitud_certificado));
  // $fe_solicitud_certificado= date('d-m-Y',$fe_solicitud_certificado);

  #DEPNDIENFO EL ESTATUS DEL CERTIFICADO , PINTA LOS BOTONES AMARILLO:WARNING, ROJO:DANGER  VERDER:SEND 


  switch ($estatus_certificado) {
    case "RD":
      $color_label = "danger";
      $etq = ObtenConfiguracion(98); # pide el certificado
      break;
    case "RT":
      $color_label = "warning";
      $etq =  ObtenConfiguracion(99); #(ya pidio el certificado , proceso interno de vanas para enviarlo)
      break;
    case "SD":
      $color_label = "success";
      $etq = ObtenConfiguracion(100); # EL CERTIFICADO YA FUE ENVIADO
      break;
  }

  # Si existe la foto oficial 
  # Recupera el perfil del usuario

  # Verifica si el usuario tiene un avatar
  if ($fl_perfil == PFL_MAESTRO_SELF)
    $row1 = RecuperaValor("SELECT ds_oficial FROM c_maestro_sp WHERE fl_maestro_sp=$fl_usuario");
  else {
    if ($fl_perfil == PFL_ESTUDIANTE_SELF)
      $row1 = RecuperaValor("SELECT ds_oficial FROM c_alumno_sp WHERE fl_alumno_sp=$fl_usuario");
    else
      $row1 = RecuperaValor("SELECT ds_oficial FROM c_administrador_sp WHERE fl_adm_sp=$fl_usuario");
  }
  # ID
  if (empty($row1[0]))
    $ds_ruta_id = ObtenAvatarUsuario($fl_usuario, false);
  else
    $ds_ruta_id = PATH_SELF_UPLOADS . "/" . $fl_instituto . "/USER_" . $fl_usuario . "/" . $row1[0];

  echo '
        {
           "checkbox": "<!--<div class=\'checkbox \'><label><input class=\'checkbox\' id=\'ch_' . $i . '\' value=\'' . $row[0] . '\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\'' . $registros . '\' id=\'tot_registros\' /> </label></div>-->",
            "name": "<a href=\'javascript:Envia(\"certificates_frm.php\",' . $row[0] . ');\'>' . str_texto($row[1]) . '<br><small class=\'text-muted\'><i></i></small></a>",
            "name_school": "<td><a href=\'javascript:Envia(\"certificates_frm.php\",' . $row[0] . ');\'>' . str_texto($row[2]) . '<br><small class=\'text-muted\'><i>' . str_texto($row[3]) . '</i></small></a></td>",           
            "course": "<td><a href=\'javascript:Envia(\"certificates_frm.php\",' . $row[0] . ');\'>' . str_texto($row[4]) . '</a></td>", 
            "age": "<a href=\'javascript:Envia(\"certificates_frm.php\",' . $row[0] . ');\'><td>' . $age . '</a></td>",
            "ide": "<div class=\'project-members\'><a href=\'javascript:void(0)\'><img src=\'' . $ds_ruta_id . '\' class=\'\'></a></div>",
            "progress": "<td><div class=\'progress progress-xs\' data-progressbar-value=\'' . $progresso . '\'><div class=\'progress-bar\'></div></div><span class=\'hidden\'>' . $progresso . '</span></td>", 
            "gpa": "<span class=\'label label-' . $color_label_promedio . '\'>' . $etq_label_promedio . '</span> ",  
            "estatus": "<td><a href=\'javascript:Envia(\"certificates_frm.php\",' . $row[0] . ');\'><span class=\'label label-' . $color_label . '\'>' . $etq . '</span>   </a></td>",            
            "date": "<td><a href=\'javascript:Envia(\"certificates_frm.php\",' . $row[0] . ');\'> ' . $fe_solicitud_certificado . '<br><small class=\'text-muted\'><i>' . $hr_solicitud_certificado . '</i></small>  </a></td>",            
            "link": "<a href=\'https://go.myfame.org/fame/fame_transcript_frm.php?c='.$row[0].'&u='.$fl_usuario.'&i='.$fl_instituto.'&p='.$fl_programa_sp.''. $randString . '\' target=\'_blank\'>https://go.myfame.org/fame/fame_transcript_frm.php?c='.$row[0].'&u='.$fl_usuario.'&i='.$fl_instituto.'&p='.$fl_programa_sp.''. $randString . '</a>",            
            "link_password": "' . $ds_login . '",            
            "paid": "<span class=\'label label-' . $color_label_pagado . '\'>' . $etq_pagado . '</span> "
 
        }';
  if ($i <= ($registros - 1))
    echo ",";
  else
    echo "";
}
?>
]

}