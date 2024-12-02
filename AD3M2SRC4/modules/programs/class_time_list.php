<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros  
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += $advanced_search;
  $fl_param = $_POST['fl_param'];
  $fe_ini = $_POST['fe_uno'];
  $fe_dos = $_POST['fe_dos'];

  if($fe_ini){
  #Damos formato de fecha alos parametros recibidos.
  $fe_ini =strtotime('0 days',strtotime($fe_ini)); 
  $fecha1= date('Y-m-d',$fe_ini);
  }
  if($fe_dos){
  $fe_dos=strtotime('0 days',strtotime($fe_dos)); 
  $fecha2= date('Y-m-d',$fe_dos);
  }

  #Muestra resultados de la busqueda.
  $Query="SELECT fl_periodo, nb_periodo ,fe_inicio, ";
  $Query.="(SELECT COUNT(1) FROM k_term b JOIN c_programa p ON p.fl_programa=b.fl_programa WHERE b.fl_periodo=a.fl_periodo AND p.fg_archive='0' )AS no_cursos ,  ";
  $Query.="CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_activo ";
  $Query.="FROM c_periodo a  WHERE 1=1 ";
  if($fl_param=='Start Date'){
     if($fecha1)
       $Query.="AND fe_inicio >= '$fecha1'  ";
     if($fecha2)
       $Query.="AND fe_inicio <= '$fecha2' ";  
     
  
  }
  $Query.="ORDER BY fe_inicio ASC ";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  
?>
{

    "data": [
    <?php
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $fl_periodo=$row['fl_periodo'];
      $nb_periodo=$row['nb_periodo'];
      $fe_inicio=$row['fe_inicio'];
      $no_cursos=$row['no_cursos'];
      $fg_activo=$row['fg_activo'];
      $fe_inici=$row['fe_inicio'];

      #DAMOS FORMATO DIA,MES, AÑO. para fecha de iniico
      $date = date_create($fe_inicio);
      $fe_inicio=date_format($date,'F j, Y');

      #recuperamos datos de los calsstimes
  $Querype="SELECT case
            when cl_dia=1 then 'Monday'
            when cl_dia=2 then 'Tuesday'
            when cl_dia=3 then 'Wednesday'
            when cl_dia=4 then 'Thursday'
            when cl_dia=5 then 'Friday'
            when cl_dia=6 then 'Saturday'
            when cl_dia=7 then 'Sunday' end cl_dia, no_hora1,no_tiempo1,no_hora2,no_tiempo2 from c_periodo where fl_periodo=$fl_periodo ";
  $rowpe=RecuperaValor($Querype);
  if($rowpe[0])
  {
      $classtime_combined=$rowpe[0]." ".$rowpe[1]." ".$rowpe[2]." to ".$rowpe[3]." ".$rowpe[4];
  }else{
      $classtime_combined="";
  }


	  #Por cada perdiodo recupermos sus class_times.y pintamos las que tengan
	   # Programas
	  $Query  = "SELECT a.fl_programa, nb_programa ";
	  $Query .= "FROM c_programa a, k_term b ";
	  $Query .= "WHERE a.fl_programa=b.fl_programa ";
	  $Query .= "AND b.fl_periodo=$fl_periodo AND a.fg_archive='0' ";
	  $Query .= "ORDER BY no_orden, no_grado ";
	  $rs1 = EjecutaQuery($Query);
	  $no_programs=CuentaRegistros($rs1);
	  $nb_programa="";
	  $nb_program="";
	  $cont_pro=0;
	  for($ii=1;$roww=RecuperaRegistro($rs1);$ii++) {

           $fl_programa=$roww[0];
           $nb_programa=str_texto($roww[1]);

           $que="SELECT fl_class_time,fl_programa FROM k_class_time WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo ";
           $rs2=EjecutaQuery($que);

           for($iii=1;$rowww=RecuperaRegistro($rs2);$iii++) {
               $fl_class_time=$rowww['fl_class_time'];
			   $fl_programa_class=$rowww[1];



				$Wqe="SELECT CASE WHEN cl_dia='1' THEN '".ObtenEtiqueta(2390)."'
								  WHEN cl_dia='2' THEN '".ObtenEtiqueta(2391)."'
								  WHEN cl_dia='3' THEN '".ObtenEtiqueta(2392)."'
								  WHEN cl_dia='4' THEN '".ObtenEtiqueta(2393)."'
								  WHEN cl_dia='5' THEN '".ObtenEtiqueta(2394)."'
								  WHEN cl_dia='6' THEN '".ObtenEtiqueta(2395)."'
                                  WHEN cl_dia='7' THEN 'Sunday' 
								  ELSE '".ObtenEtiqueta(2396)."' END dia ,no_hora,ds_tiempo
					  FROM k_class_time_programa WHERE fl_class_time=$fl_class_time
					";
			     $rs3 = EjecutaQuery($Wqe);
				 $totclass=CuentaRegistros($rs3);
				 $horarios="";
                 $tiene_pro=0;
				 for($mi=1;$romi=RecuperaRegistro($rs3);$mi++) {

					 $nb_di=$romi[0];
                     $nd_hora=$romi[1];
                     $ampm=$romi[2];

					 $horarios .= $nb_di." ".$nd_hora." ".$ampm;
					   if($mi<=($totclass-1))
						$horarios.= ", ";
					  else
						$horarios.="";

					 $tiene_pro=1;
				}
           }
           if($tiene_pro)
            $cont_pro++;
          if($horarios)
          $nb_program .= " <b>".$nb_programa."</b><br> ".$horarios."<br>";



      }


     if(($cont_pro==$no_cursos)&&($no_cursos>0))
	    $labels="success";
     else
        $labels="danger";






    echo '
    {
        "checkbox": " ",
        "class_time_combined":"<b>Combined Class Schedule</b> <br>'.$classtime_combined.'",
        "class_time":" '.$nb_program.'",
        "name": "<a href=\'javascript:Envia(\"class_time_frm.php\",\"'.$fl_periodo.'\");\'>'.$nb_periodo.'</a>",
        "fe_inicio": "<td><a href=\'javascript:Envia(\"class_time_frm.php\",\"'.$fl_periodo.'\");\'>'.$fe_inicio.'</a></td>",
        "course": "<td><a href=\'javascript:Envia(\"class_time_frm.php\",\"'.$fl_periodo.'\");\'>'.$no_cursos.'</a></td>",
        "estatus": "<a href=\'javascript:Envia(\"class_time_frm.php\",\"'.$fl_periodo.'\");\'>'.$fg_activo.'</a> ",
        "delete": "<span class=\'hidden\'>'.$fe_inici.'</span>  <span class=\'label label-'.$labels.'\'>'.$no_cursos.'</span> "



    }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
?>
   ]

}
