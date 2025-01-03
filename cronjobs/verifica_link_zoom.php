<?php
if (PHP_OS == 'Linux') {
    #Este cron verifica los links generados de zoom para acceder una clase, si no es valido regenera el link.
    require '/var/www/html/vanas/lib/com_func.inc.php';
    require '/var/www/html/vanas/lib/sp_config.inc.php';

    require '/var/www/html/vanas/AD3M2SRC4/lib/zoom_config.php';
    $file_name_txt = "/var/www/html/vanas/cronjobs/log_verifica_zoom.txt";

} else {

    require '../lib/com_func.inc.php';
    require '../lib/sp_config.inc.php';

    require '../AD3M2SRC4/lib/zoom_config.php';
    $file_name_txt = "log_verifica_zoom.txt";

}



#Obtenemos fecha mes anterior.
$fe_mes_anterior=  date('Y-m-d',strtotime("-0 month"));
$result["event"] = array();

$fe_actual=date('Y-m-d',strtotime("-0 days"));

#Generamos el log.
GeneraLog($file_name_txt,"====================================Inicia proceso ".date("F j, Y, g:i a")."=================================================");

#Recuperamos fechas de chedules and groups.
$Query ="( ";
$Query .="SELECT fl_grupo,fl_term,fl_programa,fl_clase,fl_semana,fname_teacher,lname_teacher,nb_programa,no_semana,ds_titulo,fe_clase,hr_clase,fg_adicional,no_grado,time_format(ADDTIME  (hr_clase ,'01:00:00'), '%H:%i') AS hr_final,fl_maestro   ";
$Query.="FROM groups_schedules ";
$Query.="WHERE fe_clase >= '$fe_mes_anterior' ";
$Query.=") UNION( ";
$Query.="
        SELECT DISTINCT a.fl_grupo,a.fl_term,''fl_programa,c.fl_clase_grupo AS fl_clase,''fl_semana,i.ds_nombres fname_teacher,i.ds_apaterno lname_teacher,  c.nb_clase nb_programa ,h.no_semana,a.nb_grupo ds_titulo, DATE_FORMAT(c.fe_clase,'%Y-%m-%d') as fe_clase,DATE_FORMAT(c.fe_clase,'%H:%i') as hr_clase, ''fg_adicional,''no_grado,''hr_final,i.fl_usuario fl_maestro
         FROM c_grupo a
         JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo
         JOIN k_alumno_grupo g ON g.fl_grupo=a.fl_grupo
         JOIN k_semana_grupo h ON c.fl_semana_grupo=h.fl_semana_grupo
	     join c_usuario i ON i.fl_usuario=c.fl_maestro
	     WHERE fe_clase >= '$fe_mes_anterior'
";
$Query.=") ";

$rs = EjecutaQuery($Query);
$regenerate_link=0;
$regenerate_list="";
$total_links=CuentaRegistros($rs);
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

	if(!empty($fl_programa)){

		#Recuperamos link y liecneic de zoom con eso verificamos el color.
		#Revisa si hay una clase activa en este momento, real
		$Queryl  = "SELECT fl_live_session, zoom_id,zoom_meeting_id ";
		$Queryl .= "FROM k_live_session ";
		$Queryl .= "WHERE fl_clase=".$fl_clase ;
		$rowl = RecuperaValor($Queryl);
		$fl_live_session=$rowl['fl_live_session'];
		$cl_licenica=$rowl['zoom_id'];
		$zoom_meeting_id=$rowl['zoom_meeting_id'];

	}else{

        #clase grupo global
        #Clase grupal mutiples terms
        $Query  = "SELECT fl_live_session_grupal, zoom_id,zoom_meeting_id ";
        $Query .= "FROM k_live_session_grupal ";
        $Query .= "WHERE fl_clase_grupo=".$fl_clase ;
        $rowl = RecuperaValor($Query);
        $fl_live_session=$rowl['fl_live_session_grupal'];
        $cl_licenica=$rowl['zoom_id'];
        $zoom_meeting_id=$rowl['zoom_meeting_id'];



    }

    #Generamos el log.
    GeneraLog($file_name_txt,"======Fecha_clase_$fe_clase====$hr_clase======MettingID==$zoom_meeting_id=================k_live_session===fl_live_session===$fl_live_session==============");



    if($fe_clase>=$fe_actual){
        if((!empty($cl_licenica))&&(!empty($zoom_meeting_id))){
            $verifica_metting=VerifyMeetingZoom($cl_licenica,$zoom_meeting_id);

            #si es link invalido la regenera y actualiza la BD
            if(empty($verifica_metting)){



                #Damos formato a la clase para isertarla en zoom
                $fe_clase_zoom=strtotime('+0 day',strtotime($fe_clase));
                $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                $fe_clase_zoom=$fe_clase_zoom."T".$hr_clase.":00";
                $pass_clase_zoom=rand(99999,5)."i".$fl_live_session_cg;

                #Generamos el log.
                GeneraLog($file_name_txt,"===Regenera link===Fecha_clase_$fe_clase====$hr_clase======Titulo==$ds_titulo=======licencia zoom====$cl_licenica======k_live_session===fl_live_session===$fl_live_session==============");

                if(!empty($fl_programa)){
                    create_meetingZoom($fl_live_session,'60',$ds_titulo,$fe_clase_zoom,$pass_clase_zoom,'k_live_session',$cl_licenica);
                }else{

                    create_meetingZoom($fl_live_session,'60',$ds_titulo,$fe_clase_zoom,$pass_clase_zoom,'k_live_session_grupal',$cl_licenica);

                }

				$regenerate_link ++;

                $regenerate_list.="<b>Topic:</b> $ds_titulo   Group: $nb_grupo --- $fe_clase_zoom  $hr_clase  <br>";



            }

        }
    }






}

#Generamos el log.
GeneraLog($file_name_txt,"====================================Finaliza proceso ".date("F j, Y, g:i a")."=================================================");


#Enviamos emial con la informacion

$from_add = ObtenConfiguracion(4);
$email = ObtenConfiguracion(83);


$Query="SELECT * FROM k_template_doc where fl_template=199 ";
$row=RecuperaValor($Query);
$ds_encabezado = str_uso_normal($row['ds_encabezado']);
$ds_cuerpo =str_uso_normal($row['ds_cuerpo']);
$ds_pie = str_uso_normal($row['ds_pie']);
$subject=$row['nb_template'];

$message=$ds_encabezado.$ds_cuerpo.$ds_pie."<br><br><br>";
$no_regenerate=$total_links-$regenerate_link;

#Realizamos el reemplazo.
$message=str_replace("#total#", $total_links, $message);
$message=str_replace("#regenerate#", $regenerate_link, $message);
$message=str_replace("#not_regenerate#", $no_regenerate, $message);
$message=str_replace("#regenerate_list#", $regenerate_list, $message);

$mail = EnviaMailHTML('', $from_add, $email, $subject, $message);







function GeneraLog($file_name_txt,$contenido_log=''){

    $fch= fopen($file_name_txt, "a+"); // Abres el archivo para escribir en él
    fwrite($fch, "\n".$contenido_log); // Grabas
    fclose($fch); // Cierras el archivo.
}
?>