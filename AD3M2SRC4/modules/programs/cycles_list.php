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
  $Query="SELECT fl_periodo, nb_periodo ,".ConsultaFechaBD('fe_inicio', FMT_FECHA)." AS fe_inicio, ";
  $Query.="(SELECT COUNT(1) FROM k_term b WHERE b.fl_periodo=a.fl_periodo)AS no_cursos ,  ";
  $Query.="CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END fg_activo ";
  $Query.="FROM c_periodo a  WHERE 1=1 ";
  if($fl_param=='Start Date'){
     if($fecha1)
       $Query.="AND fe_inicio >= '$fecha1'  ";
     if($fecha2)
       $Query.="AND fe_inicio <= '$fecha2' ";  
     
  
  }
  $Query.="ORDER BY fe_inicio DESC ";

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
      

         
  
    echo '
    {
        "checkbox": " ",
        
        "name": "<a href=\'javascript:Envia(\"cycles_frm.php\",\"'.$fl_periodo.'\");\'>'.$nb_periodo.'</a>",
        "fe_inicio": "<td><a href=\'javascript:Envia(\"cycles_frm.php\",\"'.$fl_periodo.'\");\'>'.$fe_inicio.'</a></td>",           
        "course": "<td><a href=\'javascript:Envia(\"cycles_frm.php\",\"'.$fl_periodo.'\");\'>'.$no_cursos.'</a></td>",          
        "estatus": "<a href=\'javascript:Envia(\"cycles_frm.php\",\"'.$fl_periodo.'\");\'>'.$fg_activo.'</a> ",
        "delete": "<a href=\'javascript:Borra(\"cycles_del.php\", \"'.$fl_periodo.'\");\' class=\'btn btn-xs btn-default\' ><i class=\'fa  fa-trash-o\'></i> </a> "
                        
           
 
    }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
