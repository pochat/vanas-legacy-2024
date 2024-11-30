<?php
	
	
 # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
 
	$result["event"] = array();

    #Recuperamos fechas de chedules and groups
    $Query="SELECT fl_grupo,fl_term,fl_programa,fl_clase,fl_semana,fname_teacher,lname_teacher,nb_programa,no_semana,ds_titulo,fe_clase,hr_clase,fg_adicional,no_grado   ";
    $Query.="FROM groups_schedules order by fe_clase DESC ";
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
                          "end"=> $fe_clase." ".$hrs,
                          "allDay"=> false,
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
        $fl_maestro=!empty($row2['fl_mastro'])?$row2['fl_mastro']:NULL;
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
                             "end"=> $fe_clase,
                             "allDay"=> false,
                             "description" =>$nb_class_topic.$nb_teacher."<br/>Week:".$no_semana."<br/>".$hr_clase." hrs.",
                             "backgroundColor" => $background
                             ); 
        
        
        
                 array_push($result["event"], $event2);
    
    }
    
 
 
    #Query para traer las claes grupales.

    $Queryg="
        SELECT DISTINCT a.nb_grupo,c.nb_clase,DATE_FORMAT(c.fe_clase, '%Y-%m-%d')
		  ,DATE_FORMAT(c.fe_clase, '%H:%i'),time_format(ADDTIME  (c.fe_clase ,'01:00:00'), '%H:%i') AS hr_final
        ,c.fl_clase_grupo 
        FROM c_grupo a
        JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo 
        JOIN k_alumno_grupo g ON g.fl_grupo=a.fl_grupo
    ";
    $rsg = EjecutaQuery($Queryg);
	while($rowg=RecuperaRegistro($rsg)){
        $fl_clase_grupo=$rowg['fl_clase_grupo'];
        $nb_grupo = $rowg[0];
        $nb_clase=$rowg[1];
		$fe_clase = $rowg[2];
		$hr_clase = $rowg[3];
        $hr_final_clase=$rowg['hr_final'];
        $ds_clase_global =$nb_grupo." - ".$nb_clase;

        $programas=ObtenEtiqueta(2521);
        $fg_clase_global="GG";

        $event = array(
        "id" => $fl_clase_grupo.",".$fg_clase_global,
    	"title" => $ds_clase_global,
  		"start" => $fe_clase."T".$hr_clase.":00",
        "end"=>$fe_clase."T".$hr_final_clase.":00",
        "allDay"=> false,
  		"description" => $programas ." ".$hr_clase,
  		"backgroundColor" => "#0B6138"
  	    );

        array_push($result["event"], $event);  
    }

    
    





  
  echo json_encode((Object) $result);
?>