<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros  
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += $advanced_search;
  $fl_param = $_POST['fl_param'];
  $fe_ini = $_POST['fe_uno'];
  $fe_dos = $_POST['fe_dos'];
  $fl_programa=$_POST['fl_programa'];
  
  
  if( ($fl_param=='Start Date')&&(empty($fe_ini))&&(empty($fe_dos) )  ){
      
          #Datos iniciales
          #Obtenemos fecha actual :
          $Query3 = "Select CURDATE() ";
          $row = RecuperaValor($Query3);
          $fe_ini =strtotime('-2 years',strtotime($row[0])); #restamos 1 años.
          $fe_ini= date('d-m-Y',$fe_ini);
          $fe_dos=strtotime('0 days',strtotime($row[0]));
          $fe_dos= date('d-m-Y',$fe_dos);
  
  }
  
  
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
  $Query="SELECT fl_grupo,nb_programa, nb_periodo,no_grado, nb_grupo, ";
  $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
  $Query .= ConcatenaBD($concat)." '".ObtenEtiqueta(421)."', ";
  $Query .= "(SELECT COUNT(1) FROM k_alumno_grupo f WHERE a.fl_grupo=f.fl_grupo) AS count ";
  $Query .= "FROM c_grupo a, k_term b, c_programa c, c_periodo d, c_usuario e ";
  $Query .= "WHERE a.fl_term=b.fl_term ";
  $Query .= "AND b.fl_programa=c.fl_programa ";
  $Query .= "AND b.fl_periodo=d.fl_periodo ";
  $Query .= "AND a.fl_maestro=e.fl_usuario  AND c.fg_archive='0'  ";
  
  
  if($fl_param=='Start Date'){
     if($fecha1)
       $Query.="AND d.fe_inicio >= '$fecha1'  ";
     if($fecha2)
       $Query.="AND d.fe_inicio <= '$fecha2' ";  
     
  
  }
  if(!empty($fl_programa)){
  $Query.="AND c.fl_programa=$fl_programa ";
  
  }
  
  
  $Query .= "ORDER BY no_orden, fe_inicio DESC, no_grado, nb_grupo ";

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

  
?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $fl_grupo=$row['fl_grupo'];
      $nb_programa=$row['nb_programa'];
      $nb_periodo=$row['nb_periodo'];
      $no_grado=$row['no_grado'];
	  $nb_teacher=str_texto($row['5']);
      $nb_grupo=trim(str_texto($row['nb_grupo']));  
      $no_students=$row['count'];
      

   //  if($fl_grupo<>282){    
  
    echo '
    {
        
        
        "name": "<a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$nb_programa.'</a>",
        "nb_periodo": "<td><a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$nb_periodo.'</a></td>",           
        "grado": "<td><a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$no_grado.'</a></td>",          
        "grupo": "<a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$nb_grupo.'</a> ",
		"teacher":"<a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$nb_teacher.'</a>  ",
		"student":"<a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'> '.$no_students.' </a>",
        "delete": "<a href=\'javascript:Borra(\"groups_del.php\", \"'.$fl_grupo.'\");\' class=\'btn btn-xs btn-default\' ><i class=\'fa  fa-trash-o\'></i> </a> "
                        
           
 
    }';
    
    // }
    
    
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
