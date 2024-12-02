<?php
	
$result["event"] = array();

#Obtenemos fecha mes anterior.
$fe_mes_anterior=  date('Y-m-d',strtotime("-1 month"));

#Obtenemos fecha actual :
$Query = "Select CURDATE() ";
$row = RecuperaValor($Query);
$fe_actual = str_texto($row[0]);
$fe_pasado=strtotime('-30 day',strtotime($fe_pasado));
$fe_pasado= date('Y-m-d',$fe_pasado);
       
 
#Recuperamos los eventos que ya existen en tabla temporal.y que no an sido publicados.
$Querys="SELECT a.fl_clase_calendar,pe.nb_periodo,a.fl_term,a.fl_programa,a.fl_leccion,a.fl_periodo,a.fl_maestro,p.nb_programa,a.nb_grupo,a.no_grado,a.no_semana,l.ds_titulo,a.fe_inicio,a.fe_final,c.ds_nombres,c.ds_apaterno,a.fg_estatus   
FROM k_clase_calendar a
LEFT JOIN c_programa p ON p.fl_programa=a.fl_programa
LEFT JOIN c_leccion l ON l.fl_leccion=a.fl_leccion
LEFT JOIN c_periodo pe ON a.fl_periodo=pe.fl_periodo
LEFT JOIN c_maestro m ON m.fl_maestro=a.fl_maestro 
LEFT JOIN c_usuario c ON c.fl_usuario=m.fl_maestro ";
$rss = EjecutaQuery($Querys);
for($a=0; $row=RecuperaRegistro($rss); $a++){
    $fl_clase_calendar=$row['fl_clase_calendar'];
    $nb_periodo=$row['nb_periodo'];
    $fe_clase=$row['fe_inicio'];
    $fe_fin_clase=$row['fe_final'];
    $fl_term=$row['fl_term'];
    $fl_programa=$row['fl_programa'];
    $nb_programa=$row['nb_programa'];
    $ds_titulo=$row['ds_titulo'];
    $fl_leccion=$row['fl_leccion'];
    $fl_periodo=$row['fl_periodo'];
    $no_grado=$row['no_grado'];
    $no_semana=$row['no_semana'];
    $fl_maestro=$row['fl_maestro'];
    $nb_teacher=$row['ds_nombres']." ".$row['ds_apaterno'];
    $fg_estatus=$row['fg_estatus'];
    $nb_grupo=$row['nb_grupo'];

    if(empty($fl_maestro)){
        $background="#a90329";
    }else{

        if($fg_estatus=='C'){
            $background="#12690c"; 
        }else{
            $background="#0521ca";
        }
    }

    if((empty($fl_programa))&&(empty($fl_leccion))){
        $title="Global Group ".$ds_titulo;
        $description="";
        $id=$fl_clase_calendar.",".$fl_periodo;
        $fg_tipo_clase="multiple_term";
    }else{
        $title=$nb_programa.", Term:".$no_grado."  ".$nb_periodo.", ".$nb_teacher;
        $description="Term ".$no_grado."<br/>Cycle".$nb_periodo."Week:".$no_semana."<br/>Lesson:".$ds_titulo."<br/>".$hr_clase." hrs.";
        $id=$fl_clase_calendar."#".$fl_term."#".$fl_programa."#".$fl_leccion."#".$no_grado."#".$fl_periodo."#".$fl_maestro;
        $fg_tipo_clase="single_term";
    }



    $event1=array(
            "id" => $id,
            "title" => $title,
            "start" => $fe_clase,
            "end"=> $fe_fin_clase,
            "description" =>$description,
            "backgroundColor" => $background,
            "icon"=>"$fl_programa#$no_grado#$all_users#$fl_periodo#$fg_tipo_clase"
            ); 
    
    array_push($result["event"], $event1);
    

    #Recuperamos los eventos que hay en  




    
}        
 

/**
 * Se pintan las echas por los otros modulos, gropus scheduler, global class. etc
 */ 

#Recuperamos todas la clases existentes en groups shceduler  y global calendar.
# Initialize variables
$fl_usuario = ObtenUsuario(False);
$fl_usuario="1";

#Obtenemos fecha mes anterior.
$fe_mes_anterior=  date('Y-m-d',strtotime("-1 month"));
#Recuperamos fechas de chedules and groups.
$Query="SELECT fl_grupo,fl_term,fl_programa,fl_clase,fl_semana,fname_teacher,lname_teacher,nb_programa,no_semana,ds_titulo,fe_clase,hr_clase,fg_adicional,no_grado,time_format(ADDTIME  (hr_clase ,'01:00:00'), '%H:%i') AS hr_final,fl_maestro   ";
$Query.="FROM groups_schedules ";
$Query.="WHERE fe_clase > '$fe_mes_anterior' ";
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
    $hr_final_clase=$row['hr_final'];
    $fl_maestro=$row['fl_maestro'];
    #Recupermos el grupo 
    $Query="SELECT nb_grupo,fg_zoom FROM c_grupo WHERE fl_grupo=$fl_grupo ";
    $row=RecuperaValor($Query);
    $nb_grupo=str_texto($row['nb_grupo']);
    $fg_zoom=$row['fg_zoom'];
    
    if($fg_zoom==1){
        $InZoom="(Zoom) ";
        $icon="fa-video-camera";
    }else{
        $InZoom=" ";
        $icon="";
    }
    
    #Recuperamos link y liecneic de zoom con eso verificamos el color.
    #Revisa si hay una clase activa en este momento, real
    $Queryl  = "SELECT fl_live_session, zoom_id ";
    $Queryl .= "FROM k_live_session ";
    $Queryl .= "WHERE fl_clase=".$fl_clase ;
    $rowl = RecuperaValor($Queryl);
    $cl_licenica=$rowl['zoom_id'];
    $background="#2270b3";//cafe

   


    
    if($fg_extra_clase){
        //   $background="#119595";
        $estra_clase="Extraclass <br/>";
    }else{
        //    $background="#50A6C2";
        $estra_clase="";
    }
    
    
    $hrs=$hr_clase.":00";

    #Verificmos el teacher activo.
    $Query="SELECT fg_activo FROM c_usuario WHERE fl_usuario=$fl_maestro ";
    $ro=RecuperaValor($Query);
    if($ro['fg_activo']==1){

        



        #Verificamos que tenga alumnos activos y si no, tampoco lo muestra.
        $Query="SELECT COUNT(*) FROM k_alumno_grupo a 
                                JOIN c_usuario b ON a.fl_alumno=b.fl_usuario AND b.fg_activo='1' AND a.fl_grupo=$fl_grupo ";
        $ros=RecuperaValor($Query);

        if($ros[0]>0){



            $event1=array(
                  "id" => $fl_grupo.",".$fl_term.",".$fl_programa.",".$fl_clase.",".$fl_semana.",".$fg_extra_clase,
                  "title" => $InZoom.$nb_teacher,
                  "start" => $fe_clase."T".$hrs,
                  "end"=>$fe_clase."T".$hr_final_clase.":00",
                  "description" =>$nb_grupo." <br/>".$nb_programa."<br/>Term ".$no_term."<br/>".$estra_clase."Week:".$no_semana."<br/>Lesson:".$ds_titulo."<br/>".$hr_clase." hrs.",
                  "backgroundColor" => $background,
                  "icon" => $icon
                  ); 
            
            array_push($result["event"], $event1);
        }
    }    
}


#Recuperamos las clases globales.
$Query2="SELECT fl_clase_cg,fl_maestro,ds_clase,no_semana,DATE_FORMAT(fe_clase,'%Y-%m-%d') as fe_clase,ds_titulo,fname_teacher,lname_teacher,hr_formato_clase,time_format(ADDTIME  (hr_formato_clase ,'01:00:00'), '%H:%i') AS hr_final ";
$Query2.="FROM clases_globales ";
$Query2.="WHERE fe_formato_clase > '$fe_mes_anterior' ";
$rs2 = EjecutaQuery($Query2);
for($b=0; $row2=RecuperaRegistro($rs2); $b++){
    $fl_clase_cg=$row2['fl_clase_cg'];
    $fl_maestro=$row2['fl_maestro'];
    $ds_clase_titulo=str_uso_normal($row2['ds_clase']);
    $no_semana=$row2[4];
    $fe_clase=$row2['fe_clase'];
    $nb_class_topic=str_uso_normal($row2['ds_titulo']);
    $fname_teacher=str_uso_normal($row2['fname_teacher']);
    $lname_teacher=str_uso_normal($row2['lname_teacher']);
    $hr_clase=str_uso_normal($row2['hr_formato_clase']);
    $hr_final_clase=$row2['hr_final'];
    
    $fg_clase_global="CG";
    if(!empty($fl_maestro))
        $nb_teacher="<br/>".$fname_teacher." ".$lname_teacher;
    else
        $nb_teacher=" "; 
    
    #Verifica si es por zoom
    $Query="SELECT fl_clase_global FROM clases_globales WHERE fl_clase_cg=$fl_clase_cg ";
    $row=RecuperaValor($Query);
    $fl_clase_globval=$row[0];

    $Query="SELECT fg_zoom FROM c_clase_global WHERE fl_clase_global=$fl_clase_globval ";
    $row=RecuperaValor($Query);
    $fg_zoom=$row['fg_zoom'];
    if($fg_zoom==1){
        $InZoom="(Zoom) ";
        $icon="fa-video-camera";
    }else{
        $InZoom=" ";
        $icon="";
    }

    


    #Verificmos el teacher activo.
    $Query="SELECT fg_activo FROM c_usuario WHERE fl_usuario=$fl_maestro ";
    $ro=RecuperaValor($Query);
    if($ro['fg_activo']==1){

        $Queryl  = "SELECT fl_live_session_cg, zoom_id ";
        $Queryl .= "FROM k_live_sesion_cg ";
        $Queryl .= "WHERE fl_clase_cg=".$fl_clase_cg ;	  
        $rowl = RecuperaValor($Queryl);
        $cl_licenica=$rowl['zoom_id'];

        $background="#2270b3";//cafe

        
        $hrs=$hr_clase.":00";
        $event2=array(
               "id" => $fl_clase_cg.",".$fg_clase_global,
               "title" =>$InZoom.$ds_clase_titulo,
               "start" => $fe_clase."T".$hrs,
               "end"=>$fe_clase."T".$hr_final_clase.":00",
               "description" =>$nb_class_topic.$nb_teacher."<br/>Week:".$no_semana."<br/>".$hr_clase." hrs.",
               "backgroundColor" => $background,
               "icon"=>$icon
               ); 
        
        
        
        array_push($result["event"], $event2);
    }
}

#Recuperamos las clases grupales.
$Queryg="
        SELECT DISTINCT c.fl_clase_grupo, a.nb_grupo,c.nb_clase, DATE_FORMAT(c.fe_clase,'%Y-%m-%d') as fe_clase,DATE_FORMAT(c.fe_clase,'%H:%i') as hr_clase,     time_format(ADDTIME  (fe_clase ,'01:00:00'), '%H:%i') AS hr_final
        ,c.fl_maestro,a.fg_zoom 
        FROM c_grupo a
        JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo 
        JOIN k_alumno_grupo g ON g.fl_grupo=a.fl_grupo
        
    ";
$Queryg.="WHERE c.fe_clase > '$fe_mes_anterior' ";
$rsg = EjecutaQuery($Queryg);
while($rowg=RecuperaRegistro($rsg)){
    $fl_clase=$rowg[0];
    $nb_grupo = $rowg[1];
    $nb_clase=$rowg[2];
    $fe_clase = $rowg[3];
    $hr_clase=str_uso_normal($rowg['hr_clase']);
    $hr_final_clase=$rowg['hr_final'];
    $fl_maestro=$rowg['fl_maestro'];
    $fg_zoom=$rowg['fg_zoom'];

    $hrs=$hr_clase.":00";
    $ds_clase_titulo =$nb_grupo." - ".$nb_clase;

    $fg_clase_global="GG";
    $programas=ObtenEtiqueta(2521);

    if($fg_zoom==1){
        $InZoom="(Zoom) ";
        $icon="fa-video-camera";
    }else{
        $InZoom=" ";
        $icon="";
    }


    $Queryl  = "SELECT fl_live_session_grupal, zoom_id ";
    $Queryl .= "FROM k_live_session_grupal ";
    $Queryl .= "WHERE fl_clase_grupo=".$fl_clase ;
    $rowl = RecuperaValor($Queryl);
    $cl_licenica=$rowl['zoom_id'];

    $background="#2270b3";//cafe


    #Verificmos el teacher activo.
    $Query="SELECT fg_activo FROM c_usuario WHERE fl_usuario=$fl_maestro ";
    $ro=RecuperaValor($Query);
    if($ro['fg_activo']==1){

        $event=array(
        "id" => $fl_clase.",".$fg_clase_global,
        "title" => $InZoom.$ds_clase_titulo,
        "start" => $fe_clase."T".$hrs,
        "end"=>$fe_clase."T".$hr_final_clase.":00",
        "description" =>$programas."<br/>",
        "backgroundColor" => $background,
        "icon" => $icon
        ); 
        
        

        array_push($result["event"], $event);  
    }
}




  
  echo json_encode((Object) $result);
?>