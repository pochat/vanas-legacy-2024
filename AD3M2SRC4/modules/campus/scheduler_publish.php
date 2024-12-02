<?php
	
require '../../lib/general.inc.php';

//para generar las urls actualmente sigue ocupando las fucniones previamentes creadas para saber las clases translapadas.
require '../../../modules/liveclass/bbb_api.php';
require '../../lib/AdobeConnectClient.class.php';
require '../../lib/adobeconnect/LicenciaAdobe.class.php';
require '../../lib/adobeconnect/LicenciaAdobeService.class.php';
require '../../lib/campusclases/ClasesService.class.php'; 
require '../../lib/zoom_config.php';
require_once('../../lib/tcpdf/config/lang/eng.php');
require_once('../../lib/tcpdf/tcpdf.php');

$email_destino = 'ask@vanas.ca';
$email_destino = 'mike@vanas.ca';


$file_name_txt="log_publish.txt";
#Generamos el log.
GeneraLog($file_name_txt,"====================================Inicia proceso ".date("F j, Y, g:i a")."=================================================");


$fl_periodo=RecibeParametroNumerico('fl_periodo');
$no_semanas=explode(',',$_POST['no_semanas']);

#falta un algoritmo que identifique el periodo proximo.
$fl_periodo=ObtenConfiguracion(143);

#Recuperamos datos del periodo.
$Query="SELECT * FROM c_periodo WHERE fl_periodo=$fl_periodo ";
$row=RecuperaValor($Query);
$fe_inicio_periodo=$row['fe_inicio'];


$sumary="<table  width='100%' >";
$sumary .="<tr>
               <td style='background:#ddd;'>Week</td>
               <td style='background:#ddd;'>Tot. classes</td>
               <td style='background:#ddd;'>Tot. students</td>
               <td style='background:#ddd;'>Published</td>
               <td style='background:#ddd;'>No Published</td>
          </tr>";

foreach ($no_semanas as $no_semana){

    //echo"<script>      
      //       $('#label_no_$no_semana').addClass('hidden');
       //      $('#label_process_$no_semana').removeClass('hidden');
       //  </script>";

   // $result['no_semana']=$no_semana;
   // echo json_encode((Object) $result);

    #Recupermaos el no de students por semana.
    $Query0="SELECT COUNT(*)  FROM k_clase_calendar_alumno WHERE fl_clase_calendar IN (SELECT fl_clase_calendar FROM k_clase_calendar a WHERE a.fl_periodo=$fl_periodo AND a.no_semana=$no_semana AND  (fg_estatus<>'C' AND fg_estatus<>'P' OR fg_estatus IS NULL ) ) ";
    $row0=RecuperaValor($Query0);
    $no_alumnos=$row0[0];


    #Recuperamos la semana seleccionada para procesar datos.
    $Query="SELECT * FROM k_clase_calendar WHERE no_semana=$no_semana AND (fg_estatus<>'C' AND fg_estatus<>'P' OR fg_estatus IS null ) ";
    $rsg=EjecutaQuery($Query);  GeneraLog($file_name_txt,$Query);
    $tot_reg_semana=CuentaRegistros($rsg);
    $count_exitosos=0;
    $count_no_exitosos=0;
    $fg_create_term=0;
    $fg_create_students=0;
    $fg_create_group=0;
    $fg_assign_group=0;
    $fg_mover_term=0;
    $fg_create_semana_clases=0;
    $fg_estatus=0;
    for($ig=1;$rowg=RecuperaRegistro($rsg);$ig++) {
        $fl_clase_calendar=$rowg['fl_clase_calendar'];
        $fg_tipo_clase=$rowg['fg_tipo_clase'];
        $nb_grupo=$rowg['nb_grupo'];
        $fl_term=$rowg['fl_term'];
        $fl_maestro=$rowg['fl_maestro'];
        $fl_programa=$rowg['fl_programa'];
        $fl_leccion=$rowg['fl_leccion'];
        $fe_clase=$rowg['fe_inicio'];
        $ds_titulo=$rowg['ds_titulo'];


        #Elimnamos las fechas si existen.
        $Query="DELETE FROM k_semana WHERE fl_term=$fl_term ";
        EjecutaQuery($Query);


        #Recuperamos el No. de estudiantes de ese grupo o clase.
        $Querya="SELECT COUNT(*) FROM k_clase_calendar_alumno WHERE fl_clase_calendar=$fl_clase_calendar ";
        $rowa=RecuperaValor($Querya);
        $no_students=$rowa[0];

        GeneraLog($file_name_txt,"Tipo clase:".$fg_tipo_clase);

        #Single_term
        if($fg_tipo_clase==2){
            
            #Verifica que no exista el grupo y si no la crea
            $Query2="SELECT fl_grupo FROM c_grupo WHERE nb_grupo='$nb_grupo' AND fl_term=$fl_term ";
            $row2=RecuperaValor($Query2);
            $fl_grupo=$row2[0];
            if(empty($fl_grupo)){

                $Queryg =" INSERT INTO c_grupo(nb_grupo,fl_term,no_alumnos,fg_zoom ";
                if(!empty($fl_maestro))
                    $Queryg .=",fl_maestro ";
                $Queryg .=" ) ";
                $Queryg .=" VALUES('$nb_grupo',$fl_term,$no_students,'1' ";
                if(!empty($fl_maestro))
                    $Queryg .=",$fl_maestro ";
                $Queryg .=" ) ";
                $fl_grupo=EjecutaInsert($Queryg);

            }
            $fg_create_group=1;
            GeneraLog($file_name_txt,"Fl grupo es:".$fl_grupo);

            #Elimina y vuelve a insertar.
            EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_grupo=$fl_grupo AND fg_grupo_global<>'1' ");

            #Se crea la relacion de alumno y grupo.
            $Querya="SELECT fl_alumno,fg_tipo_estudiante FROM k_clase_calendar_alumno WHERE fl_clase_calendar=$fl_clase_calendar ";
            $rsa=EjecutaQuery($Querya);
            for($a=1;$rowa=RecuperaRegistro($rsa);$a++) {
                $fl_alumno=$rowa['fl_alumno'];
                $fg_tipo_estudiante=$rowa['fg_tipo_estudiante'];
                

                if($fg_tipo_estudiante==1){

                    #Recuperamos el fl_sesion
                    $Querysesion="SELECT fl_sesion,cl_sesion FROM c_sesion WHERE cl_sesion='$fl_alumno' ";
                    $rowsesion=RecuperaValor($Querysesion);
                    $fl_sesion=$rowsesion['fl_sesion'];

                    //Aplications se tiene que crear el estudiante.
                    require "scheduler_enroll_student.php";    

                    $Query="SELECT fl_usuario FROM c_usuario WHERE cl_sesion='".$rowsesion[1]."' ";
                    $row=RecuperaValor($Query);
                    $fl_alumno=$row['fl_usuario'];
                    
                }
                if($fg_tipo_estudiante==2){
                    //Actualizamos su term con el siguiente.
                    $Queryu="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_alumno ";
                    $rowu=RecuperaValor($Queryu);
                    $cl_sesion=$rowu['cl_sesion'];

                    $Query="UPDATE k_ses_app_frm_1 SET fl_periodo=$fl_periodo  WHERE cl_sesion='$cl_sesion' ";
                    EjecutaQuery($Query);

                    $fg_mover_term=1;
                }


                #Recupera el grupo actual del alumno
                $Query  = "SELECT fl_periodo, b.fl_term ";
                $Query .= "FROM k_alumno_grupo a, c_grupo b, k_term c ";
                $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
                $Query .= "AND b.fl_term=c.fl_term ";
                $Query .= "AND a.fl_alumno=".$fl_alumno;
                $row = RecuperaValor($Query);
                $fl_periodo_ori = $row[0];
                $fl_term_ori = $row[1];

                #Saca al alumno del grupo actual
                EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_alumno=".$fl_alumno." AND fl_grupo=$fl_grupo AND fg_grupo_global<>'1' ");
                
                # Revisa si el grupo anterior es del mismo periodo
                if(!empty($fl_periodo_ori) AND $fl_periodo == $fl_periodo_ori AND $fl_term <> $fl_term_ori){
                    EjecutaQuery("DELETE FROM k_alumno_term WHERE fl_alumno=".$fl_alumno." AND fl_term=$fl_term_ori");
                }
                
                #Asigna al alumno al nuevo grupo.
                EjecutaQuery("INSERT INTO k_alumno_grupo(fl_grupo, fl_alumno) VALUES($fl_grupo, ".$fl_alumno.")");
                # Inserta un alumno en k_alumno_historia
                $row = RecuperaValor("SELECT 1 FROM k_alumno_historia WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo ");
                if(!ExisteEnTabla('k_alumno_historia','fl_alumno', $fl_alumno, 'fl_grupo', $fl_grupo, True)){
                    $Query  = "INSERT INTO k_alumno_historia(fl_alumno, fl_programa, fl_periodo, no_grado, fl_grupo, fl_maestro, fe_inicio) ";
                    $Query .= "VALUES($fl_alumno, $fl_programa, $fl_periodo, $no_grado, $fl_grupo, $fl_maestro, CURRENT_TIMESTAMP)";
                    EjecutaQuery($Query);
                }
                # Revisa si ya existen los datos del alumno para el term, si no los inicializa
                $row = RecuperaValor("SELECT fl_alumno FROM k_alumno_term WHERE fl_alumno=".$fl_alumno." AND fl_term=$fl_term");
                if(empty($row[0])){
                    EjecutaQuery("INSERT INTO k_alumno_term (fl_alumno, fl_term) VALUES(".$fl_alumno.", $fl_term)");
                }


                
            }

            #Una vez realizado la asignacion del estudiante se procede como ultimo paso la creacion de la semana de clase y la fecha.
            #Verificamos que no exista la clase k_semana
            $Query="
                SELECT fl_semana, no_semana, ds_titulo, fe_publicacion 
                FROM k_semana a, c_leccion b 
                WHERE a.fl_leccion=b.fl_leccion 
                AND fl_term=$fl_term AND a.fl_leccion=$fl_leccion AND no_semana=$no_semana ";
            $seman=RecuperaValor($Query);
            $fl_semana=$seman['fl_semana'];

            if(empty($fl_semana)){

                $limite_entrega = ObtenConfiguracion(23);
                $limite_calificacion = ObtenConfiguracion(24);

                $fe_entrega=strtotime('+ '.$limite_entrega.' days',strtotime($fe_clase));
                $fe_entrega= date('Y-m-d H:i:s',$fe_entrega);	

                $fe_calificacion=strtotime('+ '.$limite_calificacion.' days',strtotime($fe_clase));
                $fe_calificacion= date('Y-m-d H:i:s',$fe_calificacion);	
                
                $Query ="INSERT INTO k_semana(fl_term,fl_leccion,fe_publicacion,fe_entrega,fe_calificacion) ";
                $Query.="VALUES($fl_term, $fl_leccion,'$fe_clase','$fe_entrega','$fe_calificacion') ";
                $fl_semana=EjecutaInsert($Query);

            }
            GeneraLog($file_name_txt,"Fl semana es:".$fl_semana);

            GeneraLog($file_name_txt,"Inicia metting zoom:");
            #Generamos el meeting de zoom.

            $licenciaService = new LicenciaAdobeService();
            $clasesService = new ClasesService();
            
            $fechaHora = "'".$fe_clase."'"; 

            $valor = explode(" ",$fe_clase);
            $fe_clase_d=$valor[0];
            $hr_clase_d=$valor[1];

            GeneraLog($file_name_txt,"fechaHora:".$fechaHora."  fe_clase_d:".$fe_clase_d."  hr_clase_d:".$hr_clase_d);



            #Para Zoom.
            $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, $clave);
            $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, $clave);      
            $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom); 
            
            if($licenciaService->licenciasSuficientesZoom($clasesTraslapadasZoom, sizeof($licenciasZoom))) { 

                GeneraLog($file_name_txt,"Si hubo licencias disponibles:");

                $Query  = "INSERT INTO k_clase (fl_grupo, fl_semana, fe_clase) ";
                $Query .= "VALUES($fl_grupo, $fl_semana, '$fe_clase_d $hr_clase_d')";
                $fl_clase_insertada = EjecutaInsert($Query);
                
                GeneraLog($file_name_txt,"Se crea la clase:".$fl_clase_insertada);

                #Generamos la clase de Zoom.
                # Recuperamos la clase ya que el ui inserta las live sesions
                $Query  = "SELECT fl_live_session,zoom_url ";
                $Query .= "FROM k_live_session ";
                $Query .= "WHERE fl_clase=".$fl_clase_insertada;         
                $row = RecuperaValor($Query);
                $fl_live_session = $row[0];
                $zoom_url=$row[1];

                if(empty($fl_live_session)){
                    $Query="INSERT INTO k_live_session(fl_clase,cl_estatus)VALUES($fl_clase_insertada,1) ";
                    $fl_live_session=EjecutaInsert($Query);

                    GeneraLog($file_name_txt,"Se crea live sesion es :".$fl_live_session);
                }


                #Verifica la fecha actual y crea las futuras clases en zoom(al recuperar registros.)
                if(empty($zoom_url)){

                    GeneraLog($file_name_txt,"Procede a generar la zoom url");

                    #Verifica las fechas futuras a el dia actual y las crea.
                    #Obtenemos fecha actual :
                    $Query = "Select CURDATE() ";
                    $row = RecuperaValor($Query);
                    $fe_actual = str_texto($row[0]);
                    $fe_actual=strtotime('+0 day',strtotime($fe_actual));
                    $fe_actual= date('Y-m-d',$fe_actual);

                    #Damos formato a la clase para zoom.
                    $fe_clase_zoom=strtotime('+0 day',strtotime($fe_clase_d));
                    $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                    $fe_clase_actual=$fe_clase_zoom;
                    $fe_clase_zoom=$fe_clase_zoom."T".$hr_clase_d;
                    $pass_clase_zoom=rand(99999,5)."i".$fl_live_session;
                    

                    $Query="SELECT CONCAT(nb_programa,' (',c.ds_duracion,') -Level Term ',no_grado) FROM k_term a
                            JOIN c_periodo b ON a.fl_periodo=b.fl_periodo 
                            JOIN c_programa c ON c.fl_programa=a.fl_programa
                            WHERE fl_term=$fl_term ";
                    $row=RecuperaValor($Query);
                    $ds_titulo=$row[0]." ".$nb_grupo;

                    $Query="SELECT ds_titulo,b.no_semana FROM k_semana a
                            JOIN c_leccion b ON a.fl_leccion=b.fl_leccion 
                            WHERE a.fl_semana=$fl_semana ";
                    $row=RecuperaValor($Query);
                    $ds_lecc=$row[0];
                    $no_semana_=$row[1];
                    $ds_titulo="Week $no_semana_: ".$ds_titulo." - ".$ds_lecc;

                    GeneraLog($file_name_txt,"Ds titulo de la clase:".$ds_titulo);
                    $licenciaAZ = $licenciasZoom[0];
                    if((!empty($fl_live_session))&&(!empty($licenciaAZ))){
                        GeneraLog($file_name_txt,"Create meeting================");
                        GeneraLog($file_name_txt,"fl_live_session: ".$fl_live_session);
                        GeneraLog($file_name_txt,"ds_titulo: ".$ds_titulo);
                        GeneraLog($file_name_txt,"fe_clase_zoom: ".$fe_clase_zoom);
                        GeneraLog($file_name_txt,"pass_clase_zoom: ".$pass_clase_zoom);
                        GeneraLog($file_name_txt,"licenciaAZ: ".$licenciaAZ);
                        #Creamos la clase en zoom
                        $fg_creado=create_meetingZoom($fl_live_session,'60',$ds_titulo,$fe_clase_zoom,$pass_clase_zoom,'k_live_session',$licenciaAZ);
                    }

                    


                }




            }






        }
        #Multimples terms 
        if($fg_tipo_clase==3){
            

            #Buscamos el term
            $Queryt="SELECT fl_term FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
            $rowt=RecuperaValor($Queryt);
            $fl_term=$rowt['fl_term'];


            #verifica que no exista un grupo normal sencillo.
            $Query1="SELECT fl_grupo FROM c_grupo WHERE nb_grupo='$nb_grupo N' AND fl_term=$fl_term  ";
            $row1=RecuperaValor($Query1);
            $fl_grupo_norm=$row1[0];
            if(empty($fl_grupo_norm)){

                $Query_gru1 ="INSERT INTO c_grupo(nb_grupo,fg_zoom,fg_grupo_global,fl_maestro,fl_term ";
                $Query_gru1.=" ) ";
                $Query_gru1.=" VALUES('$nb_grupo N','0','0',0,$fl_term ";
                $Query_gru1.=" ) ";
                $fl_grupo_norm=EjecutaInsert($Query_gru1);

                $Queryd1="DELETE FROM k_alumno_grupo WHERE fl_grupo=$fl_grupo_norm  ";  
                EjecutaQuery($Queryd1);

              
                

            }



            #Verifica que no exista el grupo y si no la crea
            $Query2="SELECT fl_grupo FROM c_grupo WHERE nb_grupo='$nb_grupo' AND fg_grupo_global='1' ";
            $row2=RecuperaValor($Query2);
            $fl_grupo=$row2[0];
            if(empty($fl_grupo)){

                $Queryg = "INSERT INTO c_grupo(nb_grupo,fg_zoom,fg_grupo_global,fl_maestro,fl_term ";
                $Queryg .=" ) ";
                $Queryg .=" VALUES('$nb_grupo','1','1',0,0 ";
                $Queryg .=" ) ";
                $fl_grupo=EjecutaInsert($Queryg);

            }
            $fg_create_group=1;
            GeneraLog($file_name_txt,"Fl grupo es:".$fl_grupo);
            #Verificamos los terms seleccionados y los insertamos
            $Queryt ="SELECT fl_term FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
            $rst=EjecutaQuery($Queryt);
            $Queryd="DELETE FROM k_grupo_term WHERE fl_grupo=$fl_grupo "; EjecutaQuery($Queryd);
            for($t=1;$rowt=RecuperaRegistro($rst);$t++) {
                $fl_term=$rowt['fl_term'];

                

                $Query  = "INSERT INTO k_grupo_term(fl_term, fl_grupo) ";
                $Query .= "VALUES($fl_term, $fl_grupo)";
                $fl_grupo_terms = EjecutaInsert($Query);
            }

            #Se crea la relacion de alumno y grupo.
            $Queryd1="DELETE FROM k_alumno_grupo WHERE fl_grupo=$fl_grupo AND fg_grupo_global='1' ";  EjecutaQuery($Queryd1);
            $Queryd2="DELETE FROM k_alumno_grupo_global WHERE fl_grupo=$fl_grupo ";  EjecutaQuery($Queryd2);

            #Insertamos alumnos-grupo.
            $Querya="SELECT fl_alumno,fg_tipo_estudiante FROM k_clase_calendar_alumno WHERE fl_clase_calendar=$fl_clase_calendar ";
            $rsa=EjecutaQuery($Querya);
            for($a=1;$rowa=RecuperaRegistro($rsa);$a++) {
                $fl_alumno=$rowa['fl_alumno'];
                $fg_tipo_estudiante=$rowa['fg_tipo_estudiante'];

                if($fg_tipo_estudiante==1){
                    //aplications se tiene que crear el estudiante.
                    #Recuperamos el fl_sesion
                    $Querysesion="SELECT fl_sesion,cl_sesion FROM c_sesion WHERE cl_sesion='$fl_alumno' ";
                    $rowsesion=RecuperaValor($Querysesion);
                    $fl_sesion=$rowsesion['fl_sesion'];

                    require "scheduler_enroll_student.php";
                    
                    $Query="SELECT fl_usuario FROM c_usuario WHERE cl_sesion='".$rowsesion[1]."' ";
                    $row=RecuperaValor($Query);
                    $fl_alumno=$row['fl_usuario'];


                }

                if($fg_tipo_estudiante==2){
                    //Actualizamos su term con el siguiente.
                    $Queryu="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_alumno ";
                    $rowu=RecuperaValor($Queryu);
                    $cl_sesion=$rowu['cl_sesion'];

                    $Query="UPDATE k_ses_app_frm_1 SET fl_periodo=$fl_periodo  WHERE cl_sesion='$cl_sesion' ";
                    EjecutaQuery($Query);
                }
                #Elimina y vuelve a insertar.
                EjecutaQuery("DELETE FROM k_alumno_grupo WHERE fl_alumno=$fl_alumno AND fg_grupo_global<>'1' ");
                # Asigna al alumno al nuevo grupo normal
                $grupo_alumn="INSERT INTO k_alumno_grupo(fl_grupo, fl_alumno,fg_grupo_global) VALUES($fl_grupo_norm, $fl_alumno,'0')  "; 
                $fl_groups_alumno=EjecutaInsert($grupo_alumn);

                # Asigna al alumno al nuevo grupo
                EjecutaQuery("INSERT INTO k_alumno_grupo(fl_grupo, fl_alumno,fg_grupo_global) VALUES($fl_grupo, ".$fl_alumno.",'1') ");
                
               
                # Asigna al alumno al nuevo grupo
                EjecutaQuery("INSERT INTO k_alumno_grupo_global(fl_grupo, fl_alumno) VALUES($fl_grupo, ".$fl_alumno.") ");
                

                
            }


            #Generamos la clase para este grupo.
            #Se inserta la semana. 
            $Query  = "INSERT INTO k_semana_grupo (fl_grupo, no_semana, fe_publicacion, fe_entrega, fe_calificacion) ";
            $Query .= "VALUES($fl_grupo, $no_semana, '$fe_clase', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            $fl_semana=EjecutaInsert($Query);
            #En base ala fecha de clase se obtiene el dia de clase Monday, Friday  $ds_dia_clase="";
            $ds_dia_clase=date("l",strtotime($fe_clase."+ 0 days"));

            #Se inserta la clase. 
            $Query  = "INSERT INTO k_clase_grupo (fl_grupo,fl_maestro, fl_semana_grupo,nb_clase,ds_dia_clase, fe_clase, fg_obligatorio, fg_adicional) ";
            $Query .= "VALUES($fl_grupo,$fl_maestro, $fl_semana,'$ds_titulo','$ds_dia_clase', '$fe_clase', '1', '0')";
            $fl_clase_grupo=EjecutaInsert($Query);

            GeneraLog($file_name_txt,"Fl semana es:".$fl_semana);
            GeneraLog($file_name_txt,"Fl clase_grupo es:".$fl_clase_grupo);

            GeneraLog($file_name_txt,"Inicia metting zoom:");

            #Genrrtamos el metting de zoom.
            $fechaHora = "'".$fe_clase."'"; 

            $valor = explode(" ",$fe_clase);
            $fe_clase_zoom=$valor[0];
            $hr_sesion=$valor[1];

            // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
            // entonces puede agregar la clase, de lo contrario regresa el error a la forma.
            $licenciaService = new LicenciaAdobeService();
            $clasesService = new ClasesService();
            
            //$fechaHora =  $fe_clase;//"'" . ValidaFecha($fe_start_date) . ' ' . ValidaHoraMin($hr_sesion) . "'";
            GeneraLog($file_name_txt,"fechaHora:".$fechaHora."  fe_clase_zoom:".$fe_clase_zoom."  hr_sesion:".$hr_sesion);
            #Zoom
            $clasesTraslapadasZoom = $clasesService->getNumClasesTraslapadasZoom($fechaHora, 0);
            $clavesLicenciasTraslapadasZoom = $clasesService->getClavesLicenciasTraslapadasZoom($fechaHora, 0);      
            $licenciasZoom = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom);
            $licenciasZoomDisponible = $licenciaService->getLicenciasDisponiblesZoom($clavesLicenciasTraslapadasZoom,True);

            if ($clasesTraslapadasZoom > sizeof($licenciasZoom)) {  
                $rsClasesTraslapadas = $clasesService->getClasesTraslapadasZoom($fechaHora,0);
                
                $arrClavesTraslapadas = array();
                for($ix = 0; $rowx = RecuperaRegistro($rsClasesTraslapadas); $ix++) {
                    $arrClavesTraslapadas[$ix] = $rowx[0];
                }
                
            } 
            $existe = array_search($fl_clase_grupo, $arrClavesTraslapadas, false);	
            if(empty($existe)){
                #Creacion de zoom class
                if (sizeof($licenciasZoomDisponible)> 0) {
                    

                    #Recuperamos los datos de la clase que ya fue creada.
                    $Query="SELECT fl_live_session_grupal FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupo ";
                    $row=RecuperaValor($Query);
                    $fl_live_session_grupal=$row[0];

                    if(empty($fl_live_session_grupal)){
                        $Query ="INSERT INTO k_live_session_grupal (fl_clase_grupo,cl_estatus,ds_meeting_id,ds_password_admin,ds_password_asistente,cl_licencia)";
                        $Query.="VALUES($fl_clase_grupo,1,'','','','1')";
                        $fl_live_session_grupal=EjecutaInsert($Query);
                    }
                    GeneraLog($file_name_txt,"Se crea la clase k_live_session_grupal fl_live_session_grupal:".$fl_live_session_grupal);

                    if(!empty($fl_live_session_grupal)){
                        #Damos formato a la clase para isertarla en zoom
                        $fe_clase_zoom=strtotime('+0 day',strtotime($fe_start_date));
                        $fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
                        $fe_clase_zoom=$fe_clase_zoom."T".$hr_sesion;
                        $pass_clase_zoom=rand(99999,5)."i".$fl_live_session_grupal;

                        GeneraLog($file_name_txt,"Create meeting");
                        $licenciaAZ = $licenciasZoomDisponible[0]; // Usa una nueva licencia, toma la primera del arreglo

                        GeneraLog($file_name_txt,"fl_live_session_grupal: ".$fl_live_session_grupal);
                        GeneraLog($file_name_txt,"nb_clase: ".$nb_clase);
                        GeneraLog($file_name_txt,"fe_clase_zoom: ".$fe_clase_zoom);
                        GeneraLog($file_name_txt,"pass_clase_zoom: ".$pass_clase_zoom);
                        GeneraLog($file_name_txt,"licenciaAZ: ".$licenciaAZ);


                        $fg_creado=create_meetingZoom($fl_live_session_grupal,'60',$nb_clase,$fe_clase_zoom,$pass_clase_zoom,'k_live_session_grupal',$licenciaAZ);
                        
                    }


                }

            }			




        }

        if($fl_clase_grupo){
            
            #Marcamos ok a ala semana 1.
            $Querty="UPDATE k_clase_calendar SET fg_estatus='C' WHERE fl_clase_calendar=$fl_clase_calendar ";
            EjecutaQuery($Querty);

            #Realizamos el conteo de los exitosos
            $count_exitosos++;
        }else{
            
            #Realizamos el conteo de los faltantes
            $count_no_exitosos++;

        }

        #Insertamos solo bitacora
        $data ="INSERT INTO k_clase_calendar_semana_status(fl_periodo,no_semana,fe_inicio,fe_final,fg_create_term,fg_create_students,fg_create_group,fg_assign_group,fg_mover_term,fg_create_semana_clases,fg_estatus,fe_creacion,fe_ultmod) ";
        $data.="VALUES($fl_periodo,$no_semana,'$fe_inicio_periodo',NULL,'$fg_create_term','$fg_create_students','$fg_create_group','$fg_assign_group','$fg_mover_term','$fg_create_semana_clases','$fg_estatus',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ";
        $fl_data=EjecutaInsert($data);
        
    }


    //Pintamos cuantos exitosos y cuantos faltaron.
  /*  echo"<script>
            $('#tot_processs_$no_semana').empty();
            $('#tot_no_process_$no_semana').empty();
            $('#tot_processs_$no_semana').append($count_exitosos);
            $('#tot_no_process_$no_semana').append($count_no_exitosos);
         </script>";
    */
   if($no_semana%2 == 0){
        $bckgro="#ddd";
    }else{
        $bckgro="";
    }
    $sumary .="<tr>
               <td style='background:$bckgro;'>$no_semana</td>
               <td style='background:$bckgro;'>$tot_reg_semana</td>
               <td style='background:$bckgro;'>$no_alumnos</td>
               <td style='background:$bckgro;'>$count_exitosos</td>
               <td style='background:$bckgro;'>$count_no_exitosos</td>
               </tr>
    ";

//    if(empty($count_no_exitosos)){  
        //publich yes. cuando todos sean exito.
/*        echo"<script> 
             $('#label_process_$no_semana').addClass('hidden');
             $('#label_no_$no_semana').addClass('hidden');
             $('#label_yes_$no_semana').removeClass('hidden');
             document.getElementById('ch_semana_$no_semana').checked = false;           
             </script>";
             */

//    }else{
/*
        echo"<script> 
             $('#label_process_$no_semana').addClass('hidden');
             $('#label_no_$no_semana').removeClass('hidden');
             $('#label_yes_$no_semana').addClass('hidden');
             document.getElementById('ch_semana_$no_semana').checked = false;           
             </script>";
             */

     //   break;

//    }





}

$sumary.="</table>";

//Enviamos el email con el resumen.
$ds_encabezado =  GeneraTemplate(1, 191);
$ds_cuerpo =  GeneraTemplate(2, 191);
$ds_pie = GeneraTemplate(3, 191);
$ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;                      

$Query="SELECT nb_template FROM k_template_doc WHERE fl_template=191 ";
$row=RecuperaValor($Query);
$subject=$row['nb_template'];

$ds_contenido = str_replace("#admin#", "ask@vanas.ca", $ds_contenido);  #no_dias_cuso
$ds_contenido = str_replace("#summary#", $sumary, $ds_contenido);  #no_dias_cuso


# Envia el correo
$mail_apply = EnviaMailHTML('', ObtenConfiguracion(4), $email_destino, $subject, $ds_contenido);

$result['fg_termino']=true;
$result['no_semana']=$no_semana;
$result['no_exitosos']=$count_exitosos;
echo json_encode((Object) $result);



function GeneraLog($file_name_txt,$contenido_log=''){
    
    $fch= fopen($file_name_txt, "a+"); // Abres el archivo para escribir en él
    fwrite($fch, "\n".$contenido_log); // Grabas
    fclose($fch); // Cierras el archivo.
}
#Genera Template 
function GeneraTemplate($opc, $fl_template = 0){
    # Recupera datos del template del documento
    switch ($opc) {
        case 1:
            $campo = "ds_encabezado";
            break;
        case 2:
            $campo = "ds_cuerpo";
            break;
        case 3:
            $campo = "ds_pie";
            break;
        case 4:
            $campo = "nb_template";
            break;
    }

    # Obtenemos la informacion del template header body or footer
    $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
    $row = RecuperaValor($Query1);

    $cadena = $row[0];
    # Sustituye caracteres especiales
    $cadena = $row[0];
    $cadena = str_replace("&lt;", "<", $cadena);
    $cadena = str_replace("&gt;", ">", $cadena);
    $cadena = str_replace("&quot;", "\"", $cadena);
    $cadena = str_replace("&#039;", "'", $cadena);
    $cadena = str_replace("&#061;", "=", $cadena);
    $cadena = str_replace("&nbsp;", " ", $cadena);
    $cadena = html_entity_decode($cadena);
    return $cadena;

}

?>