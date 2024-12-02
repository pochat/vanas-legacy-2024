<?php
	
require '../../lib/general.inc.php';

#Recibe Parametros.
$fg_tipo_clase=RecibeParametroNumerico('fg_tipo_clase');
$fg_repeat=$_POST['fg_repeat'];
$fg_tipo_estudiante=$_POST['fg_tipo_estudiante'];
$allusers=$_POST['allusers'];
$fl_clase_calendar=$_POST['fl_clase_calendar'];
$fl_terms=explode(',',$_POST['fl_terms']);
$fl_term=$_POST['fl_term'];
$fl_periodo=$_POST['fl_periodo'];
$fl_maestro=$_POST['fl_maestro'];
$nb_grupo=$_POST['nb_grupo'];
$no_semana=$_POST['no_semana'];
$ds_titulo=$_POST['nb_clase'];
$fl_programa=$_POST['fl_programa'];
$fl_leccion=$_POST['fl_leccion'];
$fg_edit=$_POST['fg_edit'];
$no_grado=$_POST['no_grado'];
$fe_inicio=$_POST['fe_inicio'];
$fe_final=$_POST['fe_final'];
$fe_inicio=str_replace("T"," ",$fe_inicio);
$fe_inicio=str_replace("Z","",$fe_inicio);
$fe_final=str_replace("T"," ",$fe_final);
$fe_final=str_replace("Z","",$fe_final);
$fe_fin=$_POST['fe_fin'];
$fe_fin=strtotime('1 days',strtotime($fe_fin));
$fe_fin= date('Y-m-d',$fe_fin);	
$fe_calendar=$fe_inicio;
#Obtenemos el horario final clase esto para los repeats.
$hra=$_POST['fe_final']."";
$valor = explode("T", $hra);
$fecha=$valor[0];
$hra_fin=str_replace("Z","",$valor[1]);

$tot_semanas=$_POST['no_semanas'];
//$tot_semanas=12;


        
#Verifica que no exista y si no la crea.
#$Query="SELECT COUNT(*) FROM k_clase_calendar WHERE fl_periodo=$fl_periodo AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion  AND fg_tipo_clase='$fg_tipo_clase' ";
#$row=RecuperaValor($Query);


if(empty($fl_clase_calendar)){
    
    //clase normal
    if($fg_tipo_clase==2){

        switch ($fg_repeat){
            case '1':
                    $Query ="INSERT INTO k_clase_calendar(fl_term,fl_programa,fl_leccion,fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_grado,no_semana,fe_creacion,fe_ultmod,fg_repeat";
                    if($fl_maestro)
                        $Query .=",fl_maestro ";
                    $Query .=") ";
                    $Query.="VALUES($fl_term,$fl_programa,$fl_leccion,$fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','2',$no_grado,$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat'";
                    if($fl_maestro)
                        $Query.=",$fl_maestro ";
                    $Query.=")";
                    $fl_clase_calendar=EjecutaInsert($Query);

                    #Guardamos relacion de estudiantes para mostrar en el modal.
                    if($fg_tipo_estudiante==2){
                        $QueryP="SELECT fl_usuario FROM c_usuario WHERE fl_usuario IN($allusers) ";
                    }else{
                        $QueryP="SELECT cl_sesion FROM k_ses_app_frm_1 WHERE cl_sesion IN($allusers) ";
                    }
                    $rs=EjecutaQuery($QueryP);
                    for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                        $cl_sesion=$rowc[0];

                        EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                        $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante, fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante', CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                        $fl_insert=EjecutaInsert($Query);
                    }

                break;
                
            case '2':
                //Formula para repeat de los eventos diarios.
                $startDateTime = $fe_inicio;//; '2021-01-04 10:30:00';
                $endDateTime = $fe_final;//'2021-01-04 11:30:00';
                $repeatEndDate = $fe_fin;
                $step  = 1;
                $unit  = 'D';
                $repeatStart = new DateTime($startDateTime);
                $repeatEnd   = new DateTime($repeatEndDate); 
                $interval = new DateInterval("P{$step}{$unit}");
                $period   = new DatePeriod($repeatStart, $interval, $repeatEnd);
                //Se realiza la interaccion para calcular fecha diarimente hasta finalizar el periodo.
                foreach ($period as $key => $date ) {
                    $data=($date->format('Y-m-d H:i:s')) . PHP_EOL;
                    
                    $fe_inicio=$data;
                    $fe_final=($date->format('Y-m-d')) . PHP_EOL;
                    $fe_final=trim($fe_final)." ".$hra_fin;

                    $Query ="INSERT INTO k_clase_calendar(fl_term,fl_programa,fl_leccion,fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_grado,no_semana,fe_creacion,fe_ultmod,fg_repeat ";
                    if($fl_maestro)
                        $Query .=",fl_maestro ";
                    $Query .=") ";
                    $Query.="VALUES($fl_term,$fl_programa,$fl_leccion,$fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','2',$no_grado,$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat' ";
                    if($fl_maestro)
                        $Query.=",$fl_maestro ";
                    $Query.=")";
                    $fl_clase_calendar=EjecutaInsert($Query);

                    #Guardamos relacion de estudiantes para mostrar en el modal.
                    if($fg_tipo_estudiante==2){
                        $QueryP="SELECT fl_usuario FROM c_usuario WHERE fl_usuario IN($allusers) ";
                    }else{
                        $QueryP="SELECT cl_sesion FROM k_ses_app_frm_1 WHERE cl_sesion IN($allusers) ";
                    }
                    $rs=EjecutaQuery($QueryP);
                    for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                        $cl_sesion=$rowc[0];

                        EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                        $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                        $fl_insert=EjecutaInsert($Query);

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
                        #Recuperamos el nombre de la leccion de ese term.
                        $Queryleccion="SELECT ds_titulo FROM c_leccion WHERE fl_programa=$fl_programa AND no_grado=$no_grado AND no_semana=$no_semana ";
                        $row=RecuperaValor($Queryleccion);
                        $ds_titulo=$row['ds_titulo'];

                        $Query ="INSERT INTO k_clase_calendar(fl_term,fl_programa,fl_leccion,fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_grado,no_semana,fe_creacion,fe_ultmod,fg_repeat ";
                        if($fl_maestro)
                            $Query .=",fl_maestro ";
                        $Query .=") ";
                        $Query.="VALUES($fl_term,$fl_programa,$fl_leccion,$fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','2',$no_grado,$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat' ";
                        if($fl_maestro)
                            $Query.=",$fl_maestro ";
                        $Query.=")";
                        $fl_clase_calendar=EjecutaInsert($Query);


                        #Guardamos relacion de estudiantes para mostrar en el modal.
                        if($fg_tipo_estudiante==2){
                            $QueryP="SELECT fl_usuario FROM c_usuario WHERE fl_usuario IN($allusers) ";
                        }else{
                            $QueryP="SELECT cl_sesion FROM k_ses_app_frm_1 WHERE cl_sesion IN($allusers) ";
                        }
                        $rs=EjecutaQuery($QueryP);
                        for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                            $cl_sesion=$rowc[0];

                            EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                            $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                            $fl_insert=EjecutaInsert($Query);

                        }

                    }
                break;

        }


    }


    //Multiples terms.
    if($fg_tipo_clase==3){

        switch ($fg_repeat) {
            case '1':
                $Query ="INSERT INTO k_clase_calendar(fl_programa,fl_leccion,fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_grado,no_semana,fe_creacion,fe_ultmod,fg_repeat ";
                if($fl_maestro)
                    $Query .=",fl_maestro ";
                $Query .=") ";
                $Query.="VALUES($fl_programa,$fl_leccion,$fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','3',$no_grado,$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat' ";
                if($fl_maestro)
                    $Query.=",$fl_maestro ";
                $Query.=")";
                $fl_clase_calendar=EjecutaInsert($Query);

                foreach ($fl_terms as $fl_term){

                    $Query="DELETE FROM k_clase_calendar_terms WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND fl_periodo=$fl_periodo ";
                    EjecutaQuery($Query);

                    $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                    $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                    $dt=EjecutaInsert($Query);
                }

                #Guardamos relacion de estudiantes para mostrar en el modal.
                if($fg_tipo_estudiante==2){
                    $QueryP="SELECT fl_usuario FROM c_usuario WHERE fl_usuario IN($allusers) ";
                }else{
                    $QueryP="SELECT cl_sesion FROM k_ses_app_frm_1 WHERE cl_sesion IN($allusers) ";
                }
                $rs=EjecutaQuery($QueryP);
                for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                    $cl_sesion=$rowc[0];

                    EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                    $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                    $fl_insert=EjecutaInsert($Query);

                }


                break;
            case '2':
                //Formula para repeat de los eventos diarios.
                $startDateTime = $fe_inicio;//; '2021-01-04 10:30:00';
                $endDateTime = $fe_final;//'2021-01-04 11:30:00';
                $repeatEndDate = $fe_fin;
                $step  = 1;
                $unit  = 'D';
                $repeatStart = new DateTime($startDateTime);
                $repeatEnd   = new DateTime($repeatEndDate); 
                $interval = new DateInterval("P{$step}{$unit}");
                $period   = new DatePeriod($repeatStart, $interval, $repeatEnd);
                //Se realiza la interaccion para calcular fecha diarimente hasta finalizar el periodo.
                foreach ($period as $key => $date ) {
                    $data=($date->format('Y-m-d H:i:s')) . PHP_EOL;
                    
                    $fe_inicio=$data;
                    $fe_final=($date->format('Y-m-d')) . PHP_EOL;
                    $fe_final=trim($fe_final)." ".$hra_fin;

                    $Query ="INSERT INTO k_clase_calendar(fl_programa,fl_leccion,fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_grado,no_semana,fe_creacion,fe_ultmod,fg_repeat ";
                    if($fl_maestro)
                        $Query .=",fl_maestro ";
                    $Query .=") ";
                    $Query.="VALUES($fl_programa,$fl_leccion,$fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','3',$no_grado,$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat' ";
                    if($fl_maestro)
                        $Query.=",$fl_maestro ";
                    $Query.=")";
                    $fl_clase_calendar=EjecutaInsert($Query);


                    foreach ($fl_terms as $fl_term){

                        $Query="DELETE FROM k_clase_calendar_terms WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND fl_periodo=$fl_periodo ";
                        EjecutaQuery($Query);

                        $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                        $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                        $dt=EjecutaInsert($Query);
                    }


                }
                #Guardamos relacion de estudiantes para mostrar en el modal.
                if($fg_tipo_estudiante==2){
                    $QueryP="SELECT fl_usuario FROM c_usuario WHERE fl_usuario IN($allusers) ";
                }else{
                    $QueryP="SELECT cl_sesion FROM k_ses_app_frm_1 WHERE cl_sesion IN($allusers) ";
                }
                $rs=EjecutaQuery($QueryP);
                for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                    $cl_sesion=$rowc[0];

                    EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                    $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                    $fl_insert=EjecutaInsert($Query);

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

                    $Query ="INSERT INTO k_clase_calendar(fl_programa,fl_leccion,fl_periodo,nb_grupo,ds_titulo,fe_inicio,fe_final,fg_tipo_clase,no_grado,no_semana,fe_creacion,fe_ultmod,fg_repeat ";
                    if($fl_maestro)
                        $Query .=",fl_maestro ";
                    $Query .=") ";
                    $Query.="VALUES($fl_programa,$fl_leccion,$fl_periodo,'$nb_grupo','$ds_titulo','$fe_inicio','$fe_final','3',$no_grado,$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fg_repeat' ";
                    if($fl_maestro)
                        $Query.=",$fl_maestro ";
                    $Query.=")";
                    $fl_clase_calendar=EjecutaInsert($Query);

                    $Query="DELETE FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
                    EjecutaQuery($Query);

                    foreach ($fl_terms as $fl_term){

                        $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                        $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                        $dt=EjecutaInsert($Query);
                    }

                    #Guardamos relacion de estudiantes para mostrar en el modal.
                    if($fg_tipo_estudiante==2){
                        $QueryP="SELECT fl_usuario FROM c_usuario WHERE fl_usuario IN($allusers) ";
                    }else{
                        $QueryP="SELECT cl_sesion FROM k_ses_app_frm_1 WHERE cl_sesion IN($allusers) ";
                    }
                    $rs=EjecutaQuery($QueryP);
                    for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
                        $cl_sesion=$rowc[0];

                        EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$cl_sesion' and fl_clase_calendar=$fl_clase_calendar ");

                        $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$cl_sesion','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                        $fl_insert=EjecutaInsert($Query);

                    }



                }

                break;
        }




    }

#Update
}else{
    
    //clase normal
    if($fg_tipo_clase==2){

        #Cuando trae el fl_calendar
        if($fl_clase_calendar){

            #solo se mpdifica ese evento
            if($fg_edit==1){

                $Query ="UPDATE k_clase_calendar SET nb_grupo='$nb_grupo',ds_titulo='$ds_titulo' ";
                if($fl_maestro)
                    $Query.=",fl_maestro=$fl_maestro ";
                $Query.="WHERE fl_clase_calendar=$fl_clase_calendar ";
                EjecutaQuery($Query);
                
            }

            #Se modifican todos los eventos programados esa hora. 
            if($fg_edit==2){    
                $Query ="UPDATE k_clase_calendar SET nb_grupo='$nb_grupo',ds_titulo='$ds_titulo' ";
                if($fl_maestro)
                    $Query.=",fl_maestro=$fl_maestro ";
                $Query.="WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion AND fl_periodo=$fl_periodo AND no_grado=$no_grado  ";
                EjecutaQuery($Query);
            }





        }else{
            //cuando sigue en modo editable y programable la tarea.
            $Query ="UPDATE k_clase_calendar SET nb_grupo='$nb_grupo',no_semana=$no_semana,ds_titulo='$ds_titulo' ";
            if($fl_maestro)
                $Query.=",fl_maestro=$fl_maestro ";
            $Query.="WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo  AND fl_leccion=$fl_leccion AND no_semana=$no_semana AND fg_tipo_clase='$fg_tipo_clase' ";
            EjecutaQuery($Query);

            $Query ="SELECT fl_clase_calendar FROM k_clase_calendar ";
            $Query.="WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo  AND fl_leccion=$fl_leccion AND no_semana=$no_semana AND fg_tipo_clase='$fg_tipo_clase' ";
            $row=RecuperaValor($Query);
            $fl_clase_calendar=$row['fl_clase_calendar'];

        }
    }

    #Multiples terms.
    if($fg_tipo_clase==3){
        
        #solo se mpdifica ese evento
        if($fg_edit==1){
        
            $Query ="UPDATE k_clase_calendar SET nb_grupo='$nb_grupo',ds_titulo='$ds_titulo',no_semana=$no_semana ";
            if($fl_maestro)
                $Query.=",fl_maestro=$fl_maestro ";
            $Query.="WHERE fl_clase_calendar=$fl_clase_calendar ";
            EjecutaQuery($Query);

            #eleiminamos los term eñegidos y volvemos a insertar.
            $Query="DELETE FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
            EjecutaQuery($Query);

            foreach ($fl_terms as $fl_term){

                $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                $dt=EjecutaInsert($Query);
            }   
        }

        //eventos repetidos semanalmente.
        if($fg_edit==2){
            
            $qUERY="SELECT * FROM k_clase_calendar WHERE fl_periodo=$fl_periodo AND fl_programa=$fl_programa AND fl_leccion=$fl_leccion AND no_grado=$no_grado    ";
            $rs=EjecutaQuery($QueryP);
            for($a=1;$row=RecuperaRegistro($rs);$a++) {
                $fl_clase_calendar=$row['fl_clase_calendar'];

                $Query ="UPDATE k_clase_calendar SET nb_grupo='$nb_grupo',ds_titulo='$ds_titulo',no_semana=$no_semana ";
                if($fl_maestro)
                    $Query.=",fl_maestro=$fl_maestro ";
                $Query.="WHERE fl_clase_calendar=$fl_clase_calendar  ";
                EjecutaQuery($Query);

                #eleiminamos los term eñegidos y volvemos a insertar.
                $Query="DELETE FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
                EjecutaQuery($Query);
                
                foreach ($fl_terms as $fl_term){

                    $Query ="INSERT INTO k_clase_calendar_terms(fl_clase_calendar,fl_term,fl_programa,fl_periodo,fe_creacion,fe_ultmod)";
                    $Query.="VALUES($fl_clase_calendar,$fl_term, $fl_programa,$fl_periodo,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                    $dt=EjecutaInsert($Query);
                }   


            }




            






        }





    }



}














#Guardamos relacion de estudinates. sTUDENTS
/*if($fg_tipo_estudiante==2){

    $QueryP="SELECT * FROM c_usuario WHERE fl_usuario IN($allusers) ";
    $rs=EjecutaQuery($QueryP);
    for($c=1;$rowc=RecuperaRegistro($rs);$c++) {
        $fl_usuario=$rowc['fl_usuario'];

        EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_alumno='$fl_usuario' and fl_clase_calendar=$fl_clase_calendar ");

        $Query="INSERT INTO k_clase_calendar_alumno(fl_clase_calendar,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod)VALUES($fl_clase_calendar,'$fl_usuario','$fg_tipo_estudiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
        $fl_insert=EjecutaInsert($Query);

    }
}*/


?>

<script>
    $(document).ready(function () {
        $.smallBox({
            title: "Successfully! ",
            content: "<i class='fa fa-clock-o'></i> <i>1 seconds ago...</i>",
            color: "#5F895F",
            iconSmall: "fa fa-check bounce animated",
            timeout: 4000
        });

        var calendar = $('#calendar').fullCalendar({

            defaultDate: '<?php echo $fe_calendar;?>'
        });


});

</script>

