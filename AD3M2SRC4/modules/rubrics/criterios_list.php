<?php
# Librerias
require '../../lib/general.inc.php';

# Recibe Parametros
$criterio = RecibeParametroHTML('criterio');

#muestra los institutos que ya se encuentran registrados Y TIENEN ASIGNADO UN CURSO/PROGRAMA.
$Query = "SELECT fl_criterio, nb_criterio, fl_instituto FROM c_criterio WHERE fl_instituto IS NULL ORDER BY fl_criterio DESC ";
$rs = EjecutaQuery($Query);
$registros = CuentaRegistros($rs);

?>
{
"data": [
<?php
for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
  $fl_criterio = $row['fl_criterio'];
  $nb_criterio = str_texto($row['nb_criterio']);

  #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
  $Query1 = "SELECT C.fl_calificacion_criterio, C.ds_calificacion, ds_descripcion FROM k_criterio_fame K
         JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
         WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=5 ";
  $row = RecuperaValor($Query1);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_calificacion1 = str_texto(!empty($row[1]) ? $row[1] : NULL);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_descripcion1 = str_texto(!empty($row[2]) ? $row[2] : NULL);

  #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
  $Query2 = "SELECT C.fl_calificacion_criterio, C.ds_calificacion, ds_descripcion FROM k_criterio_fame K
         JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
         WHERE  fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=4 ";
  $row = RecuperaValor($Query2);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_calificacion2 = str_texto(!empty($row[1]) ? $row[1] : NULL);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_descripcion2 = str_texto(!empty($row[2]) ? $row[2] : NULL);

  //$ds_descripcion2=str_replace("&#47;"," ",$ds_descripcion2);
  //$ds_descripcion2=str_replace("&#039;"," ",$ds_descripcion2);

  #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
  $Query3 = "SELECT C.fl_calificacion_criterio, C.ds_calificacion, ds_descripcion FROM k_criterio_fame K
         JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
         WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=3 ";
  $row = RecuperaValor($Query3);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_calificacion3 = str_texto(!empty($row[1]) ? $row[1] : NULL);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_descripcion3 = str_texto(!empty($row[2]) ? $row[2] : NULL);

  #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
  $Query4 = "SELECT C.fl_calificacion_criterio, C.ds_calificacion, ds_descripcion FROM k_criterio_fame K
         JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
         WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=2 ";
  $row = RecuperaValor($Query4);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_calificacion4 = str_texto(!empty($row[1]) ? $row[1] : NULL);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_descripcion4 = str_texto(!empty($row[2]) ? $row[2] : NULL);

  #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
  $Query5 = "SELECT C.fl_calificacion_criterio, C.ds_calificacion, ds_descripcion FROM k_criterio_fame K
         JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio=C.fl_calificacion_criterio
         WHERE fl_criterio=$fl_criterio AND C.fl_calificacion_criterio=1 ";
  $row = RecuperaValor($Query5);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_calificacion5 = str_texto(!empty($row[1]) ? $row[1] : NULL);
  // Fetches the request parameter and sets a default value if it doesn’t exist
  $ds_descripcion5 = str_texto(!empty($row[2]) ? $row[2] : NULL);

  $Query6 = "SELECT DISTINCT C.ds_titulo, C.no_semana,P.nb_programa,  C.fl_leccion_sp				                            
                FROM  k_criterio_programa_fame K
                JOIN c_leccion_sp C ON C.fl_leccion_sp =K.fl_programa_sp
                LEFT join c_programa_sp P ON P.fl_programa_sp=C.fl_programa_sp
                JOIN c_criterio T ON T.fl_criterio=K.fl_criterio 
                WHERET.fl_criterio=$fl_criterio ORDER BY C.ds_titulo ASC ";
  $rs6 = EjecutaQuery($Query6);
  $tot_registros = CuentaRegistros($rs6);
  $lecciones = " ";
  for ($m = 1; $row2 = RecuperaRegistro($rs6); $m++) {

    $ds_leccion = str_texto($row2['ds_titulo']);
    $no_semana = $row2['no_semana'];
    $nb_programa = str_texto($row2['nb_programa']);

    $lecciones .= "<tr>";
    $lecciones .= "<td>$nb_programa <br/><i>$ds_leccion  &nbsp;&nbsp;&nbsp;Session:$no_semana</i> </td>";
    $lecciones .= "</tr>";
  }

  $Query7 = "SELECT DISTINCT C.ds_titulo,C.no_semana,P.nb_programa,  C.fl_leccion,C.no_grado				                            
                FROM  k_criterio_programa K
                 JOIN c_leccion C ON C.fl_leccion =K.fl_programa
                 LEFT join c_programa P ON P.fl_programa=C.fl_programa
                JOIN c_criterio T ON T.fl_criterio=K.fl_criterio 
                WHERE 
                
                T.fl_criterio=$fl_criterio ORDER BY C.ds_titulo ASC ";
  $rs7 = EjecutaQuery($Query7);
  $tot_registros2 = CuentaRegistros($rs7);
  $lecciones2 = " ";
  for ($z = 1; $row3 = RecuperaRegistro($rs7); $z++) {

    $ds_leccion = str_texto($row3['ds_titulo']);
    $no_semana = $row3['no_semana'];
    $nb_programa = str_texto($row3['nb_programa']);
    $no_grado = str_texto($row3['no_grado']);

    $lecciones2 .= "<tr>";
    $lecciones2 .= "<td>$nb_programa <br/><i>$ds_leccion  &nbsp;&nbsp;&nbsp;Week:$no_semana</i> &nbsp;&nbsp;&nbsp;Term:$no_grado</td>";
    $lecciones2 .= "</tr>";
  }

  //$ds_calificacion1="";
  //$ds_descripcion1="";  

  // $ds_calificacion1="";
  // $ds_descripcion1=""; 
  // $ds_calificacion2="";
  // $ds_descripcion2="";
  //$ds_calificacion3="";
  //$ds_descripcion3="";
  // $ds_calificacion4="";
  // $ds_descripcion4="";
  // $ds_calificacion5="";
  // $ds_descripcion5="";

  echo '
    {
        "checkbox": " ",
        "name_criterio": "<a href=\'javascript:Envia(\"criterios_frm.php\",' . $fl_criterio . ');\'>' . $nb_criterio . '</a> ",
        "name": "<a href=\'javascript:Envia(\"criterios_frm.php\",' . $fl_criterio . ');\'>' . $ds_calificacion1 . '</a><br/><small class=\'text-muted\'><i>' . $ds_descripcion1 . ' </i></small>",
        "desc_uno": "<td><a href=\'javascript:Envia(\"criterios_frm.php\",' . $fl_criterio . ');\'>' . $ds_calificacion2 . '</a><br/><small class=\'text-muted\'><i>' . $ds_descripcion2 . ' </i></small></td>",           
        "desc_dos": "<td><a href=\'javascript:Envia(\"criterios_frm.php\",' . $fl_criterio . ');\'>' . $ds_calificacion3 . '</a><br/><small class=\'text-muted\'><i>' . $ds_descripcion3 . ' </i></small></td>", 
        "desc_tres": "<td class=\'text-center\'><a href=\'javascript:Envia(\"criterios_frm.php\",' . $fl_criterio . ');\' class=\'text-center\'>' . $ds_calificacion4 . ' </a><br/><small class=\'text-muted\'><i>' . $ds_descripcion4 . ' </i></small></td>",
        "ide": "<a href=\'javascript:Envia(\"criterios_frm.php\",' . $fl_criterio . ');\'><td>' . $ds_calificacion5 . '</a><br/><small class=\'text-muted\'><i>' . $ds_descripcion5 . ' </i></small></td>",
        "lecciones":"' . $lecciones . ' <hr/> ' . $lecciones2 . '  ",
		"del": "<a href=\'javascript:Borra(\"criterios_del.php\",' . $fl_criterio . ');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>"
    }';

  if ($i <= ($registros - 1))
    echo ",";
  else
    echo "";
}
?>
]
}
