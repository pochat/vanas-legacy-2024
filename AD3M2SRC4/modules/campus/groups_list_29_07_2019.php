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
          $fe_dos=strtotime('1 years',strtotime($row[0]));
          $fe_dos= date('d-m-Y',$fe_dos);
  
  }
  
  
  if($fe_ini){
  #Damos formato de fecha alos parametros recibidos.
  $fe_ini =strtotime('90 days',strtotime($fe_ini)); 
  $fecha1= date('Y-m-d',$fe_ini);
  }
  if($fe_dos){
  $fe_dos=strtotime('90 days',strtotime($fe_dos)); 
  $fecha2= date('Y-m-d',$fe_dos);
  }

  #Muestra resultados de la busqueda.
  $Query ="(";
  $Query .="SELECT fl_grupo,nb_programa, nb_periodo,no_grado, nb_grupo, ";
  $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
  $Query .= ConcatenaBD($concat)." '".ObtenEtiqueta(421)."', ";
  $Query .= "(SELECT COUNT(1) FROM k_alumno_grupo f WHERE a.fl_grupo=f.fl_grupo) AS count,fg_grupo_global ";
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

  $Query.=") UNION (";
  $Query.="
      SELECT a.fl_grupo,''nb_programa,''nb_periodo,''no_grado,a.nb_grupo,''Teacher,
       (SELECT COUNT(1) 
      FROM k_alumno_grupo z WHERE a.fl_grupo=z.fl_grupo) AS count,fg_grupo_global  
      FROM c_grupo a 
      JOIN k_grupo_term b ON a.fl_grupo=b.fl_grupo
      JOIN k_term c ON c.fl_term=b.fl_term 
      JOIN c_programa d on d.fl_programa=c.fl_programa
      JOIN c_periodo e ON e.fl_periodo=c.fl_periodo
      WHERE a.fg_grupo_global='1'
  ";
  if($fl_param=='Start Date'){
      if($fecha1)
          $Query.="AND e.fe_inicio >= '$fecha1'  ";
      if($fecha2)
          $Query.="AND e.fe_inicio <= '$fecha2' ";  
      
      
  }
  if(!empty($fl_programa)){
      $Query.="AND c.fl_programa=$fl_programa ";
      
  }


  $Query.=")";


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
      $fg_grupo_global=$row['fg_grupo_global'];      

      $Query2="SELECT a.fl_alumno,b.ds_ruta_avatar, CONCAT(c.ds_nombres,' ',c.ds_apaterno)AS nombre FROM k_alumno_grupo a 
              JOIN c_alumno b ON a.fl_alumno=b.fl_alumno
              JOIN c_usuario c on c.fl_usuario=b.fl_alumno
              WHERE a.fl_grupo=$fl_grupo ";
      $rs2=EjecutaQuery($Query2);
      $avatars="";
      for($m=1;$row2=RecuperaRegistro($rs2);$m++) {
          $ruta_avatar=$row2['ds_ruta_avatar'];
          $nombres=str_texto($row2[2]);
          if(empty($ruta_avatar))
		  $ruta_avatar="Avatar1.jpg";
		

          $avatars.="<a href='javascript:void(0)' rel='tooltip' data-placement='top' data-html='true' data-original-title='Term ".$no_grado."</small>: ".$nombres."'> <img src='".PATH_ALU_IMAGES."/avatars/" . $ruta_avatar . "' class='online' style='height:20px;'></a>  ";


      }
	  
	  #Recuperamos el periodo y el terms.
	  if($fg_grupo_global==1){
		  
		  $Query3="select a.fl_grupo, d.nb_programa,e.nb_periodo,c.no_grado from c_grupo a 
				JOIN k_grupo_term b ON a.fl_grupo=b.fl_grupo
				JOIN k_term c ON c.fl_term=b.fl_term 
				join c_programa d on d.fl_programa=c.fl_programa
				join c_periodo e ON e.fl_periodo=c.fl_periodo
				where a.fl_grupo=$fl_grupo ";
		  $rs3=EjecutaQuery($Query3);
		  
		  $nb_periodo="";
		  $nb_programa="";
		  $no_grado="";
		  for($z=1;$row3=RecuperaRegistro($rs3);$z++) {
			  
			  $nb_periodo.=$row3['nb_periodo']."<br>";
			  $nb_programa.=$row3['nb_programa']."<br>";
			  $no_grado.=$row3['no_grado']."<br>";
			  
			  
		  }
		  
		  
	  }
	  

        
   //  if($fl_grupo<>282){    
  
    echo '
    {
        
        
        "name": "<a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$nb_programa.'</a>",
        "nb_periodo": "<td><a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$nb_periodo.'</a></td>",           
        "grado": "<td><a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$no_grado.'</a></td>",          
        "grupo": "<a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$nb_grupo.'</a> ",
		"teacher":"<a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'>'.$nb_teacher.'</a>  ",
		"student":"<a href=\'javascript:Envia(\"groups_frm.php\",\"'.$fl_grupo.'\");\'> '.$no_students.'  </a><br>'.$avatars.' ",
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
