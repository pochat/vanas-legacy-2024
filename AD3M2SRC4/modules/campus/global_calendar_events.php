<?php
	
	# Initialize variables
  $fl_usuario = ObtenUsuario(False);
  $fl_usuario="1";
  
 
	$result["event"] = array();

    #Obtenemos fecha actual :
    $Query = "Select CURDATE() ";
    $row = RecuperaValor($Query);
    $fe_actual = str_texto($row[0]);
    $fe_pasado=strtotime('-30 day',strtotime($fe_pasado));
    $fe_pasado= date('Y-m-d',$fe_pasado);
    
   
    
    $hace_un_mes="";
    
    
    #Recuperamos fechas de chedules and groups
    $Query="SELECT fl_grupo,fl_term,fl_programa,fl_clase,fl_semana,fname_teacher,lname_teacher,nb_programa,no_semana,ds_titulo,fe_clase,hr_clase,fg_adicional,no_grado   ";
    $Query.="FROM groups_schedules ";
    $rs = EjecutaQuery($Query);
    for($a=0; $row=RecuperaRegistro($rs); $a++){
        $fl_grupo=$row['fl_grupo'];
        $fl_term=$row['fl_term'];
        $fl_programa=$row['fl_programa'];
        $fl_semana=$row['fl_semana'];
        $fl_clase=$row['fl_clase'];
        $fname_teacher=$row['fname_teacher'];
        $lname_teacher=$row['lname_teacher'];
        $nb_teacher=$fname_teacher." ".$lname_teacher;
        $nb_programa=str_uso_normal($row['nb_programa']);
        $no_semana=$row['no_semana'];
        $ds_titulo=str_uso_normal($row['ds_titulo']);
        $fe_clase=$row['fe_clase'];
        $hr_clase=$row['hr_clase'];
        $fg_extra_clase=$row['fg_adicional'];
        $no_term=$row['no_grado'];
        
                   if($fg_extra_clase){
                       $background="#35A9A9";
                        $estra_clase="Extraclass <br/>";
                    }else{
                        $background="#50A6C2";
                        $estra_clase="";
                    }
        
            
                   $hrs=$hr_clase.":00";
                    $event1=array(
                          "id" => $fl_grupo.",".$fl_term.",".$fl_programa.",".$fl_clase.",".$fl_semana.",".$fg_extra_clase,
                          "title" => $nb_teacher,
                          "start" => $fe_clase." ".$hrs,
                          "description" =>$nb_programa."<br/>Term ".$no_term."<br/>".$estra_clase."Week:".$no_semana."<br/>Lesson:".$ds_titulo."<br/>".$hr_clase." hrs.",
                          "backgroundColor" => $background
                          ); 
            
                    array_push($result["event"], $event1);
               
        
        
        
    
    
    }
    
    #Recuperamos las clases globales.
    $Query2="SELECT fl_clase_cg,fl_maestro,ds_clase,no_semana,fe_clase,ds_titulo,fname_teacher,lname_teacher,hr_formato_clase ";
    $Query2.="FROM clases_globales WHERE 1=1 ";
    $rs2 = EjecutaQuery($Query2);
    for($b=0; $row2=RecuperaRegistro($rs2); $b++){
        $fl_clase_cg=$row2['fl_clase_cg'];
        $fl_maestro=$row2['fl_mastro'];
        $ds_clase_titulo=str_uso_normal($row2['ds_clase']);
        $no_semana=$row2['no_semana'];
        $fe_clase=$row2['fe_clase'];
        $nb_class_topic=str_uso_normal($row2['ds_titulo']);
        $fname_teacher=str_uso_normal($row2['fname_teacher']);
        $lname_teacher=str_uso_normal($row2['lname_teacher']);
        $hr_clase=str_uso_normal($row2['hr_formato_clase']);
        
        $fg_clase_global="CG";
        if(!empty($fl_maestro))
        $nb_teacher="<br/>".$fname_teacher." ".$lname_teacher;
        else
        $nb_teacher=" "; 
        
        $background="#296191";
                      $event2=array(
                             "id" => $fl_clase_cg.",".$fg_clase_global,
                             "title" => $ds_clase_titulo,
                             "start" => $fe_clase,
                             "description" =>$nb_class_topic.$nb_teacher."<br/>Week:".$no_semana."<br/>".$hr_clase." hrs.",
                             "backgroundColor" => $background
                             ); 
        
        
        
                 array_push($result["event"], $event2);
    
    }
    
 
 
  
  echo json_encode((Object) $result);
?>