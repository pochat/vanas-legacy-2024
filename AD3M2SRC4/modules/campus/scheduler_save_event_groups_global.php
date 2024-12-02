<?php
	
require '../../lib/general.inc.php';

#Recibe Parametros.
$fg_tipo_clase=RecibeParametroNumerico('fg_tipo_clase');
$fg_repeat=$_POST['fg_repeat'];
$tot_semanas=$_POST['no_semanas'];
$fl_clase_calendar=$_POST['fl_clase_calendar'];
$fl_terms=explode(',',$_POST['fl_terms']);
$fl_maestro=$_POST['fl_maestro'];
$nb_grupo=$_POST['nb_grupo'];
$no_semana=$_POST['no_semana'];
$ds_titulo=$_POST['nb_clase'];
$hr_inicio=$_POST['hr_inicio'];
$fg_edit=$_POST['fg_edit'];
#Fecha final para repetir el evento.
$fe_fin_repeat=$_POST['fe_fin'];
$fl_periodo=ObtenConfiguracion(143);

$fe_final_repeat=strtotime('0 days',strtotime($fe_fin_repeat));
$fe_final_repeat= date('Y-m-d',$fe_final_repeat);	


$fe_inicio=$_POST['fe_inicio'];
$fe_fin=strtotime('0 days',strtotime($fe_inicio));
$fe_fin= date('Y-m-d',$fe_fin);	

$fe_inicio=strtotime('0 days',strtotime($fe_inicio));
$fe_inicio= date('Y-m-d',$fe_inicio);	
$fe_calendar=$fe_inicio;

#formateamos la hora para saber la exacta a guardra en BD.
#formateamos hora.
$valores=explode(':',$hr_inicio);
$hora=$valores[0];
$minutos=$valores[1];
$segundos=$valores[2];

#verificamos am pm 
$valores2=explode(' ',$minutos);
$minutos=$valores2[0];
$tiempo=$valores2[1];

if($tiempo=='PM'){
    
    switch($hora){
        case '1':
            $no_hora="13";
            break;
        case '2':
            $no_hora="14";
            break;
        case '3':
            $no_hora="15";
            break;
        case '4':
            $no_hora="16";
            break;
        case '5':
            $no_hora="17";
            break;
        case '6':
            $no_hora="18";
            break;
        case '7':
            $no_hora="19";
            break;
        case '8':
            $no_hora="20";
            break;
        case '9':
            $no_hora="21";
            break;
        case '10':
            $no_hora="22";
            break;
        case '11':
            $no_hora="23";
            break;
        case '12':
            $no_hora="12";
            break;
    }

}else{
    
    switch($hora){
        case '1':
            $no_hora="01";
            break;
        case '2':
            $no_hora="02";
            break;
        case '3':
            $no_hora="03";
            break;
        case '4':
            $no_hora="04";
            break;
        case '5':
            $no_hora="05";
            break;
        case '6':
            $no_hora="06";
            break;
        case '7':
            $no_hora="07";
            break;
        case '8':
            $no_hora="08";
            break;
        case '9':
            $no_hora="09";
            break;
        case '10':
            $no_hora="10";
            break;
        case '11':
            $no_hora="11";
            break;
        case '12':
            $no_hora="12";
            break;
    }
}
$hora_inicio=$no_hora.":".$minutos.":00";
$fe_inicio=$fe_inicio." ".$hora_inicio;
$fe_final_repeat=$fe_final_repeat." ".$hora_inicio;

$fe_final = strtotime ('+1 hour', strtotime($fe_inicio)); 
$fe_final = date ( 'Y-m-d H:i:s' , $fe_final); 





if((empty($fl_clase_calendar))||($fl_clase_calendar=='undefined')){
    


        switch ($fg_repeat) {
            case '1'://POR UNICA OCASION.
                $Query ="INSERT INTO k_clase_calendar(fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_semana,fe_creacion,fe_ultmod,fg_repeat ";
                if($fl_maestro)
                    $Query .=",fl_maestro ";
                $Query .=") ";
                $Query.="VALUES($fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','3',$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat' ";
                if($fl_maestro)
                    $Query.=",$fl_maestro ";
                $Query.=")";
                $fl_clase_calendar=EjecutaInsert($Query);

                foreach ($fl_terms as $fl_term){


                    #Recuperamos el programa seleccionado por cada term.
                    $Queryt="SELECT fl_programa,fl_periodo,no_grado FROM k_term WHERE fl_term=$fl_term ";
                    $rowt=RecuperaValor($Queryt);
                    $fl_programa=$rowt['fl_programa'];
                    $fl_periodo=$rowt['fl_periodo'];
                    $no_grado=$rowt['no_grado'];

                    $Query="DELETE FROM k_clase_calendar_terms WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND fl_clase_calendar=$fl_clase_calendar ";
                    EjecutaQuery($Query);

                    $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                    $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                    $dt=EjecutaInsert($Query);


                    #Recupermaos los estudiantes de ese programa y ese periodo.
                    #Guardamos relacion de estudiantes para mostrar en el modal.
                    /*if($fg_tipo_estudiante==2){
                        $QueryP="SELECT fl_usuario FROM c_usuario a
                                JOIN k_ses_app_frm_1 b ON a.cl_sesion=b.cl_sesion
                                WHERE b.fl_periodo=$fl_periodo ";
                    }else{
                        $QueryP="
                                SELECT DISTINCT a.cl_sesion FROM k_ses_app_frm_1 a
                                JOIN k_term c ON c.fl_periodo=a.fl_periodo 
                                JOIN c_sesion s ON s.cl_sesion=a.cl_sesion
                                WHERE a.fl_periodo=$fl_periodo AND a.fl_programa=$fl_programa AND c.fl_term=$fl_term
                                AND s.fg_app_1='1' AND s.fg_app_2='1' AND s.fg_app_3='1' AND s.fg_app_4='1' AND s.fg_confirmado='1' 
                                ";
                    }*/
                    $QueryP="SELECT DISTINCT fl_alumno FROM k_clase_fetch_programs a 
                                 JOIN k_clase_fetch_programs_alumno b ON b.fl_clase_fetch_programs=a.fl_clase_fetch_programs
                                 WHERE a.fl_periodo=$fl_periodo AND a.fl_programa=$fl_programa AND a.fg_tipo_clase='multiple_term' ";
                    $rs=EjecutaQuery($QueryP);
                    for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                        $cl_sesion=$rowc[0];

                        if(strlen($cl_sesion)>9){
                            $fg_tipo_estudiante=1;
                        }else{
                            $fg_tipo_estudiante=2;
                        }



                        EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                        $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                        $fl_insert=EjecutaInsert($Query);

                    }


                }

               


                break;
            case '2':
                //Formula para repeat de los eventos diarios.
                $startDateTime = $fe_inicio;//; '2021-01-04 10:30:00';
                $endDateTime = $fe_final_repeat;//'2021-01-04 11:30:00';
                $repeatEndDate = $fe_fin_repeat;
                $step  = 1;
                $unit  = 'D';
                $repeatStart = new DateTime($startDateTime);
                $repeatEnd   = new DateTime($repeatEndDate); 
                $interval = new DateInterval("P{$step}{$unit}");
                $period   = new DatePeriod($repeatStart, $interval, $repeatEnd);
                //Se realiza la interaccion para calcular fecha diarimente hasta finalizar el periodo.
                $contador=0;
                foreach ($period as $key => $date ) {
                    $data=($date->format('Y-m-d H:i:s')) . PHP_EOL;
                    
                    $fe_inicio=$data;
                    $fe_final=($date->format('Y-m-d')) . PHP_EOL;
                    $fe_final=trim($fe_final)." ".$hora_inicio;

                    $contador ++;

                    $Query ="INSERT INTO k_clase_calendar(fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_semana,fe_creacion,fe_ultmod,fg_repeat ";
                    if($fl_maestro)
                        $Query .=",fl_maestro ";
                    $Query .=") ";
                    $Query.="VALUES($fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','3',$contador,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat' ";
                    if($fl_maestro)
                        $Query.=",$fl_maestro ";
                    $Query.=")";
                    $fl_clase_calendar=EjecutaInsert($Query);


                    foreach ($fl_terms as $fl_term){

                        #Recuperamos el programa seleccionado por cada term.
                        $Queryt="SELECT fl_programa,fl_periodo,no_grado FROM k_term WHERE fl_term=$fl_term ";
                        $rowt=RecuperaValor($Queryt);
                        $fl_programa=$rowt['fl_programa'];
                        $fl_periodo=$rowt['fl_periodo'];
                        $no_grado=$rowt['no_grado'];

                        $Query="DELETE FROM k_clase_calendar_terms WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND fl_clase_calendar=$fl_clase_calendar ";
                        EjecutaQuery($Query);


                        $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                        $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                        $dt=EjecutaInsert($Query);

                        #Recupermaos los estudiantes de ese programa y ese periodo.
                        #Guardamos relacion de estudiantes para mostrar en el modal.
                        /*if($fg_tipo_estudiante==2){
                            $QueryP="SELECT fl_usuario FROM c_usuario a
                                JOIN k_ses_app_frm_1 b ON a.cl_sesion=b.cl_sesion
                                WHERE b.fl_periodo=$fl_periodo ";
                        }else{
                            $QueryP="
                                SELECT DISTINCT a.cl_sesion FROM k_ses_app_frm_1 a
                                JOIN k_term c ON c.fl_periodo=a.fl_periodo 
                                JOIN c_sesion s ON s.cl_sesion=a.cl_sesion
                                WHERE a.fl_periodo=$fl_periodo AND a.fl_programa=$fl_programa AND c.fl_term=$fl_term
                                AND s.fg_app_1='1' AND s.fg_app_2='1' AND s.fg_app_3='1' AND s.fg_app_4='1' AND s.fg_confirmado='1' 
                                ";
                        }*/
                        $QueryP="SELECT DISTINCT fl_alumno FROM k_clase_fetch_programs a 
                                 JOIN k_clase_fetch_programs_alumno b ON b.fl_clase_fetch_programs=a.fl_clase_fetch_programs
                                 WHERE a.fl_periodo=$fl_periodo AND a.fl_programa=$fl_programa AND a.fg_tipo_clase='multiple_term' ";
                        $rs=EjecutaQuery($QueryP);
                        for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                            $cl_sesion=$rowc[0];

                            if(strlen($cl_sesion)>9){
                                $fg_tipo_estudiante=1;
                            }else{
                                $fg_tipo_estudiante=2;
                            }



                            EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                            $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                            $fl_insert=EjecutaInsert($Query);

                        }




                    }


                }
                
                break;
            case '3':
                $count=0;
                    #Se calcula la fecha final del periodo que indicara que se reptira ese evento
                for($a = 0; $a < $tot_semanas; $a++){
                    $count++;
                    $no_semana=$count;

                    if($count==1){
                        $fe_inicio=date("Y-m-d H:i:s",strtotime($fe_inicio."+ 0 days"));
                        $fe_final=date("Y-m-d H:i:s",strtotime($fe_final."+ 0 days"));
                    }else{

                        $fe_inicio=date("Y-m-d H:i:s",strtotime($fe_inicio."+ 7 days"));
                        $fe_final=date("Y-m-d H:i:s",strtotime($fe_final."+ 7 days"));
                    }

                    $Query ="INSERT INTO k_clase_calendar(fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_semana,fe_creacion,fe_ultmod,fg_repeat ";
                    if($fl_maestro)
                        $Query .=",fl_maestro ";
                    $Query .=") ";
                    $Query.="VALUES($fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','3',$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat' ";
                    if($fl_maestro)
                        $Query.=",$fl_maestro ";
                    $Query.=")";
                    $fl_clase_calendar=EjecutaInsert($Query);

                    

                    foreach ($fl_terms as $fl_term){

                        #Recuperamos el programa seleccionado por cada term.
                        $Queryt="SELECT fl_programa,fl_periodo,no_grado FROM k_term WHERE fl_term=$fl_term ";
                        $rowt=RecuperaValor($Queryt);
                        $fl_programa=$rowt['fl_programa'];
                        $fl_periodo=$rowt['fl_periodo'];
                        $no_grado=$rowt['no_grado'];

                        $Query="DELETE FROM k_clase_calendar_terms WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND fl_clase_calendar=$fl_clase_calendar ";
                        EjecutaQuery($Query);


                        $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                        $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                        $dt=EjecutaInsert($Query);

                        #Recupermaos los estudiantes de ese programa y ese periodo.
                        #Guardamos relacion de estudiantes para mostrar en el modal.
                        /*if($fg_tipo_estudiante==2){
                            $QueryP="SELECT fl_usuario FROM c_usuario a
                                JOIN k_ses_app_frm_1 b ON a.cl_sesion=b.cl_sesion
                                WHERE b.fl_periodo=$fl_periodo ";
                        }else{
                            $QueryP="
                                SELECT DISTINCT a.cl_sesion FROM k_ses_app_frm_1 a
                                JOIN k_term c ON c.fl_periodo=a.fl_periodo 
                                JOIN c_sesion s ON s.cl_sesion=a.cl_sesion
                                WHERE a.fl_periodo=$fl_periodo AND a.fl_programa=$fl_programa AND c.fl_term=$fl_term
                                AND s.fg_app_1='1' AND s.fg_app_2='1' AND s.fg_app_3='1' AND s.fg_app_4='1' AND s.fg_confirmado='1' 
                                ";
                        }*/
                        $QueryP="SELECT DISTINCT fl_alumno FROM k_clase_fetch_programs a 
                                 JOIN k_clase_fetch_programs_alumno b ON b.fl_clase_fetch_programs=a.fl_clase_fetch_programs
                                 WHERE a.fl_periodo=$fl_periodo AND a.fl_programa=$fl_programa AND a.fg_tipo_clase='multiple_term' ";
                        $rs=EjecutaQuery($QueryP);
                        for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                            $cl_sesion=$rowc[0];

                            if(strlen($cl_sesion)>9){
                                $fg_tipo_estudiante=1;
                            }else{
                                $fg_tipo_estudiante=2;
                            }

                            EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                            $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)
                                    VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                            $fl_insert=EjecutaInsert($Query);

                        }



                    }


                }

                break;
        }



#Update
}else{
    
     
        #solo se mpdifica ese evento
        if($fg_edit==1){
        
            $Query ="UPDATE k_clase_calendar SET fe_inicio='$fe_inicio',fe_final='$fe_final', nb_grupo='$nb_grupo',ds_titulo='$ds_titulo',no_semana=$no_semana ";
            if($fl_maestro)
                $Query.=",fl_maestro=$fl_maestro ";
            $Query.="WHERE fl_clase_calendar=$fl_clase_calendar ";
            EjecutaQuery($Query);

            #eleiminamos los term eñegidos y volvemos a insertar.
            $Query="DELETE FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
            EjecutaQuery($Query);

            foreach ($fl_terms as $fl_term){

                #Recuperamos el programa seleccionado por cada term.
                $Queryt="SELECT fl_programa,fl_periodo,no_grado FROM k_term WHERE fl_term=$fl_term ";
                $rowt=RecuperaValor($Queryt);
                $fl_programa=$rowt['fl_programa'];
                $fl_periodo=$rowt['fl_periodo'];
                $no_grado=$rowt['no_grado'];

                $Query="DELETE FROM k_clase_calendar_terms WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND fl_clase_calendar=$fl_clase_calendar ";
                EjecutaQuery($Query);

                $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                $dt=EjecutaInsert($Query);



                $QueryP="SELECT DISTINCT fl_alumno FROM k_clase_fetch_programs a 
                                 JOIN k_clase_fetch_programs_alumno b ON b.fl_clase_fetch_programs=a.fl_clase_fetch_programs
                                 WHERE a.fl_periodo=$fl_periodo AND a.fl_programa=$fl_programa AND a.fg_tipo_clase='multiple_term' ";
                $rs=EjecutaQuery($QueryP);
                for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                    $cl_sesion=$rowc[0];

                    if(strlen($cl_sesion)>9){
                        $fg_tipo_estudiante=1;
                    }else{
                        $fg_tipo_estudiante=2;
                    }

                    EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                    $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)
                                    VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                    $fl_insert=EjecutaInsert($Query);

                }





            }   
        }

        //eventos repetidos semanalmente.
        if($fg_edit==2){
            
            $qUERY="SELECT * FROM k_clase_calendar WHERE fl_periodo=$fl_periodo AND fg_tipo_clase='3' AND fg_repeat='3' AND nb_grupo='$nb_grupo'    ";
            $rs=EjecutaQuery($qUERY);
            for($a=1;$row=RecuperaRegistro($rs);$a++) {
                $fl_clase_calendar=$row['fl_clase_calendar'];

                #Recupermos la fecha y se lo anexamos al horario para que actualize el horario solamnete.
                $Queryu="SELECT DATE_FORMAT(fe_inicio,'%Y-%m-%d') FROM k_clase_calendar WHERE fl_clase_calendar=$fl_clase_calendar ";
                $rowu=RecuperaValor($Queryu);
                $fe_inicio=$rowu[0]." ".$hora_inicio;

                #se suma 1hra por default.
                $fe_final = strtotime ('+1 hour', strtotime($fe_inicio)); 
                $fe_final = date ( 'Y-m-d H:i:s' , $fe_final); 

                $Query  ="UPDATE k_clase_calendar SET ds_titulo='$ds_titulo' ";
                $Query .=",fe_inicio='$fe_inicio',fe_final='$fe_final' ";
                if($fl_maestro)
                    $Query.=",fl_maestro=$fl_maestro ";
                $Query.="WHERE fl_clase_calendar=$fl_clase_calendar  ";
                EjecutaQuery($Query);

                #eleiminamos los term eñegidos y volvemos a insertar.
                $Query="DELETE FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
                EjecutaQuery($Query);
                
                foreach ($fl_terms as $fl_term){

                    #Recuperamos el programa seleccionado por cada term.
                    $Queryt="SELECT fl_programa,fl_periodo,no_grado FROM k_term WHERE fl_term=$fl_term ";
                    $rowt=RecuperaValor($Queryt);
                    $fl_programa=$rowt['fl_programa'];
                    $fl_periodo=$rowt['fl_periodo'];
                    $no_grado=$rowt['no_grado'];

                    $Query="DELETE FROM k_clase_calendar_terms WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND fl_clase_calendar=$fl_clase_calendar ";
                    EjecutaQuery($Query);

                    $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                    $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                    $dt=EjecutaInsert($Query);







                }   


            }

        }

       
   

}


?>

<script>
    $(document).ready(function () {
        $('#modal_new_event_calendar').modal('hide');
        $('.modal-backdrop').remove();
        $.smallBox({
            title: "Successfully! ",
            content: "<i class='fa fa-clock-o'></i> <i>1 seconds ago...</i>",
            color: "#5F895F",
            iconSmall: "fa fa-check bounce animated",
            timeout: 4000
        });

        date = moment("<?php echo $fe_calendar;?>", "YYYY-MM-DD");
        $("#calendar").fullCalendar('gotoDate', date);
        
        //  defaultDate: '2020-05-05'
       


});

</script>

