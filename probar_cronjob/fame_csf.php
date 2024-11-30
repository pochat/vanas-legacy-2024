<?php

#librerias propias de FAME.
require '/var/www/html/vanas/fame/lib/self_general.php';

#date_default_timezone_set('America/Vancouver');
#librerias propias de FAME.
//require '../fame/lib/self_general.php';

$from = 'noreply@myfame.org';

#inicializamos variables faltantes.
$ds_department=NULL;
$teacher_id_revisado=NULL;
$ds_gender=NULL;
$nuevo_plan=NULL;
$fl_institutions=NULL;
$fl_instituto=NULL;
$students_id_revisado=NULL;

#( • - •)
#/つ🍪 
#######################################
#Para la carga masiva SCF.

#Recuperamos todos los institutos rectores.
$Query="SELECT a.fl_instituto,a.ds_instituto,b.fl_usuario,b.ds_email,b.ds_nombres,b.ds_apaterno,ruta_sftp 
		FROM c_instituto a
        JOIN c_usuario b ON b.fl_usuario=a.fl_usuario_sp 
		";
$Query.="WHERE a.fg_scf='1' AND fl_instituto_rector is not null ";
$rsi=EjecutaQuery($Query);
while($ro=RecuperaRegistro($rsi)){
    $fl_instituto=$ro['fl_instituto'];
    $ds_instituto=$ro['ds_instituto'];
    $fl_usuario=$ro['fl_usuario'];
    $fl_usu_invita=$fl_usuario;
    $fl_instituto_rector=$ro['fl_instituto'];
    $fl_usuario_rector=$fl_usuario;
    $ds_email_instituto=$ro['ds_email'];
    $first_name_instituto=$ro['ds_nombres'];
    $last_name_instituto=$ro['ds_apaterno'];
    $path_origen=$ro['ruta_sftp'];

    #Se definen rutas 
    //$path_origen="/home/csf093/files/";
    $path_destino="/var/www/html/vanas/CSF_log/";

    #PATH test
    
    //$path_destino="../CSF_log/";

    #PARA LOCAL test.
    # $path_origen="../CSF/";
    # $path_destino="../CSF_log/";

    $path_dest_local = "/var/www/html/vanas/CSF_log";
    //$path_dest_local="../CSF_log/";
    

    #Lista de valores permitidos.
    $valores_permitidos = array("Student","student","STUDENT","Teacher","teacher","TEACHER","School","school","SCHOOL");
    $valor_primera_carga=array("NoThresh","nothresh","NOTHRESH");
    $aplica_threshold=0;
    $scan = scandir($path_origen);
    $new_file="";

    #Obtenemos datos de su plan de pago si lo tiene.
    $fg_plan=ObtenPlanActualInstituto($fl_instituto_rector);
    if(!empty($fg_plan)){
        
        $Queryi="SELECT fl_current_plan,fl_princing,fg_plan,fe_periodo_inicial,fe_periodo_final,no_total_licencias,no_licencias_usadas,no_licencias_disponibles,fg_estatus,mn_total_plan,fg_pago_manual FROM k_current_plan WHERE fl_instituto=$fl_instituto_rector ";
        $row=RecuperaValor($Queryi);
        $fl_current_plan=$row['fl_current_plan'];
        $fl_princing=$row['fl_princing'];
        $fg_plan=$row['fg_plan'];
        $fe_periodo_inicial=$row['fe_periodo_inicial'];
        $fe_periodo_final=$row['fe_periodo_final'];
        $no_total_licencias=$row['no_total_licencias'];
        $no_licencias_usadas=$row['no_licencias_usadas'];
        $no_licencias_disponibles=$row['no_licencias_disponibles'];
        $fg_estatus=$row['fg_estatus'];
        $mn_total_plan=$row['mn_total_plan'];
        $fg_pago_manual=$row['fg_pago_manual'];
        
        $Query="SELECT fl_pago, id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total,fe_creacion FROM k_pago_stripe WHERE fl_instituto=$fl_instituto_rector ";
        $row=RecuperaValor($Query);
        $fl_pago_stripe=$row['fl_pago'];
        $id_cliente_stripe=$row['id_cliente_stripe'];
        $id_pago_stripe=$row['id_pago_stripe'];
        $id_plan_stripe=$row['id_plan_stripe'];
        $id_suscripcion_stripe=$row['id_suscripcion_stripe'];
        $fg_motivo_pago=$row['fg_motivo_pago'];
        $ds_email_stripe=$row['ds_email'];
        $ds_descripcion_pago=$row['ds_descripcion_pago'];
        $mn_monto_=$row['mn_monto'];
        $mn_tax_stripe=$row['mn_tax'];
        $mn_total=$row['mn_total'];
        $fe_creacion=$row['fe_creacion'];
        
        $Query="SELECT fe_periodo_inicial,fe_periodo_final,mn_total,fg_publicar,fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,cl_metodo_pago,ds_cheque,ds_comentario FROM  k_admin_pagos WHERE fl_pago_stripe=$fl_pago_stripe ";
        $roe=RecuperaValor($Query);
        $fe_periodo_inicial_ap=$roe['fe_periodo_inicial'];
        $fe_periodo_final_ap=$roe['fe_periodo_final'];
        $mn_total_ap=$roe['mn_total'];
        $fg_publicar_ap=$roe['fg_publicar'];
        $fg_pago_ap=$roe['fg_pagado'];
        $fe_pago_ap=$roe['fe_pago'];
        $ds_descripcion_ap=$roe['ds_descripcion'];
        $fg_motivo_pago_ap=$roe['fg_motivo_pago'];
        $mn_costo_por_licencia_ap=$roe['mn_costo_por_licencia'];
        $id_invoice_ap=$roe['id_invoice_stripe'];
        $mn_descuento_ap=$roe['mn_descuento'];
        $cl_metodo_pago_ap=$roe['cl_metodo_pago'];
        $ds_cheque_ap=$roe['ds_cheque'];
        $ds_comentario_ap=$roe['ds_comentario'];
    }

    foreach($scan as $new_file){

        #Nombre sel archivo.(Falta identificar como llegara leerlo)
        $new_file=$new_file;
        
        $fecha_=date("ymd_His");
        
        #1.Se identifica la extension del archivo.
        $ext = strtolower(ObtenExtensionArchivo($new_file));
        
        #Se obtiene solamente el nombre del archvio.
        $nombre_archivo=ObtenNombreArchivo($new_file);
        
        #Se define la ruta completa del archivo si no se lee.
        $ruta_completa_archivo=$path_destino.$new_file;
        #Para local.
        //$ruta_completa_archivo="../CSF_log/".$new_file;
        $file_name_txt=$path_destino."_log_uploads.txt";
        
        #Generamos el log.
        GeneraLog($file_name_txt,"====================================Inicia proceso ".date("F j, Y, g:i a")."=================================================");
        
        GeneraLog($file_name_txt,"Obtiene nombre del archivo".$nombre_archivo);

        #Valida que sea un archivo valido.
        if($ext=="csv"){
            
            GeneraLog($file_name_txt,date("F j, Y, g:i a")."-El tipo de archivo es".$ext);

            #Identifica_el_archivo y verifica que sea student/teachers/school.
            //$archivo_valido = strpos($new_file, $valores_permitidos); temporal no se necesitasn
            
            #Identifica que lleve la palabra threshold. temporal no se necesitasn
            //$archivo_valido_primera_carga = strpos($new_file, $valor_primera_carga);

            foreach ($valor_primera_carga as $data1) {

                $archivo_valido1 = strpos($new_file,"".$data1."");
                if(($archivo_valido1 !== false)){
                    $archivo_valido1=$archivo_valido1;
                    $upload_primera_carga=$data1;
                    $aplica_threshold=1;//indica que si procedra los datos en insert/update/delete en la BD FAME..
                    break;

                }

            }

            foreach ($valores_permitidos as $data) {
                
                $archivo_valido = strpos($new_file,$data);
                if($archivo_valido !== false){
                    $archivo_valido=$archivo_valido;
                    $upload_type=$data;
                    break;
                }		

            }

            #Si es un archivo valido entonce pasa a realizar la carga.
            if(!empty($archivo_valido)){
                
                GeneraLog($file_name_txt,date("F j, Y, g:i a")."-".$new_file."Es un archivo valido");

                #Se inserta la bitacora del archivo subido Y en primera instancia queda como type='New'
                $Queryu ='INSERT INTO stage_uploads(user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status,fl_instituto)';
                $Queryu.='VALUES('.$fl_usuario.', "'.$path_dest_local.'","'.$new_file.'","'.$upload_type.'", CURRENT_TIMESTAMP,"NEW",CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,"",'.$fl_instituto.')';
                $fl_upload=EjecutaInsert($Queryu);
                
                GeneraLog($file_name_txt,date("F j, Y, g:i a")."Se inserta bitacora".$Queryu);
                
                #Se renombra el archivo segun sea el caso con la siguiene momenclatura  id_stage_uploads | nombre_archivo | fecha | extension del archivo
                $new_archive_procesed=$fl_upload."_".$nombre_archivo."_".$fecha_.".".$ext;
                
                GeneraLog($file_name_txt,date("F j, Y, g:i a")."Se genera nuevo nombre del archivo".$new_archive_procesed);

                #Se copia el archivo para procesarlo.
                if(rename($path_origen.$new_file, $path_destino.$new_archive_procesed)){
                    //echo "File has been copied successfully";
                    
                    $ruta_completa_archivo=$path_destino.$new_archive_procesed;
                    #para local
                    //$ruta_completa_archivo="../CSF_log/".$new_archive_procesed;
                    
                    GeneraLog($file_name_txt,date("F j, Y, g:i a")."Se copia archivo hacia ruta".$path_origen.$new_file."---->".$path_destino.$new_archive_procesed);
                    
                } else {

                    //echo "Failed to copy file to destination given";
                    GeneraLog($file_name_txt,date("F j, Y, g:i a")."Failed to copy file to destination given".$path_origen.$new_file."--->".$path_destino.$new_archive_procesed);

                }
                
                #Carga de las escuelas.
                if(($upload_type=='school')||($upload_type=='School')||($upload_type=='SCHOOL')){
                    
                    #Generamos el log.
                    GeneraLog($file_name_txt,date("F j, Y, g:i a")."Upload type:".$upload_type);
                    
                    $Query="SELECT COUNT(*) FROM st_school where 1=1 ";
                    $row=RecuperaValor($Query);
                    $conta_reg_ini=$row[0];
                    
                    #Generamos el log.
                    GeneraLog($file_name_txt,date("F j, Y, g:i a")."Query-->".$Query);
                    
                    if($conta_reg_ini==0){
                        $primeracarga=1;
                        
                        #Generamos el log.
                        GeneraLog($file_name_txt,date("F j, Y, g:i a")."Es primeraCarga");
                    }else{
                        
                        $primeracarga=0;
                    }

                    #Limpiamos la revisio de la carga esto para hacer match y sabar cuantos registros elimnados hay.
                    EjecutaQuery("UPDATE c_instituto SET fg_scf_revisado=0 WHERE  fl_instituto=$fl_instituto ");
                    
                    GeneraLog($file_name_txt,date("F j, Y, g:i a")."UPDATE c_instituto SET fg_scf_revisado=0 WHERE  fl_instituto=$fl_instituto ");
                    
                    #Proceso de la carga.
                    if ($file = fopen($ruta_completa_archivo, "r")){
                        
                        GeneraLog($file_name_txt,date("F j, Y, g:i a")."Empieza a leer el archivo");
                        
                        # Lee los nombres de los campos
                        // $name_camps = fgetcsv($file, 0, ",", "\"", "\"");
                        // $num_camps = count($name_camps);
                        // $names_camps[$num_camps -1];
                        $tot_reg1 = 0;
                        $contador_insert_school=0;
                        $unchanged_count_school=0;
                        $deleted_count_school=0;
                        $modified_count_school=0;
                        $upload_count_school=0;
                        $insert_school_array=array();
                        $upload_school_array=array();
                        $unchanged_school_array=array();
                        $deleted_school_array=array();
                        $modified_school_array=array();
                        
                        while ($data = fgetcsv ($file, 0, ",")){
                            $school_id = $data[0];
                            $nb_school = $data[1];
                            //$nb_school = htmlentities($data[1],ENT_QUOTES,"UTF-8");

                            #Inicia proceso
                            $Query='UPDATE stage_uploads SET  status_cd="STARTED",upload_file_name_log="'.$new_archive_procesed.'" WHERE id='.$fl_upload.' ';
                            EjecutaQuery($Query);
                            
                            GeneraLog($file_name_txt,date("F j, Y, g:i a").$Query);
                            
                            
                            #Siempre se genera la bitacora de school.
                            $Queryui ='INSERT INTO st_school_bitacora ( upload_id,school_id,operation_code,nb_school,fe_creacion)';
                            $Queryui.='VALUES('.$fl_upload.',"'.$school_id.'","LOADED","'.$nb_school.'",CURRENT_TIMESTAMP)';
                            $st_id_bitacora=EjecutaInsert($Queryui);
                            
                            GeneraLog($file_name_txt,date("F j, Y, g:i a").$Queryui);
                            

                            #Se identifica que es primera carga. y si el el nombre del archivo tiene la palabra threshold_ tambien se toma en cuenta como primera carga.
                            if($primeracarga==1){
                                
                                GeneraLog($file_name_txt,date("F j, Y, g:i a")."Es primeraCarga");
                                
                                #Se inserta en primera instancia la bitacora de estudiantes.
                                $Queryui ='INSERT INTO st_school (upload_id,school_id,operation_code,nb_school,fe_creacion)';
                                $Queryui.='VALUES('.$fl_upload.',"'.$school_id.'","ADD","'.$nb_school.'",CURRENT_TIMESTAMP)';
                                $st_id=EjecutaInsert($Queryui);

                                GeneraLog($file_name_txt,date("F j, Y, g:i a").$Queryui);

                                if($st_id){
                                    $contador_insert_school++;
                                }else{   
                                    
                                }
                                
                                //solo si aplica treshold inserta en FAME.
                                if($aplica_threshold==1){	
                                    
                                    #Se gebera el registro en FAME. IMPORTANTE FALTA SABER QUE USUARIO SERA EL ADMIN.
                                    $Query ='INSERT INTO c_instituto(fl_usuario_sp,cl_tipo_instituto,cl_plan_fame,ds_instituto,school_id, fl_pais,no_usuarios,ds_codigo_pais,ds_codigo_area,no_telefono,fe_creacion,fe_trial_expiracion,fg_tiene_plan,fg_activo,ds_foto,ds_rfc,fg_princing_default,fg_export_moodle,fg_parent_authorization,fl_instituto_rector,fg_scf,fg_scf_revisado) ';
                                    $Query .='VALUES('.$fl_usuario.',1,0,"'.$nb_school.'","'.$school_id.'",73,0,"+33", NULL, NULL, CURRENT_TIMESTAMP,"2019-12-31", "0", "1", NULL, NULL, "0", "0", "1",'.$fl_instituto_rector.',"1",1 )';
                                    $fl_institutonew=EjecutaInsert($Query);
                                    
                                    GeneraLog($file_name_txt,date("F j, Y, g:i a").$Query);
                                    
                                    #Se genera el plan del Instituto. 
                                    $Query="INSERT INTO k_current_plan(fl_instituto,fl_princing,  mn_total_plan,fg_plan,no_total_licencias,no_licencias_disponibles,no_licencias_usadas,no_total_storage,fg_estatus,fe_periodo_inicial,fe_periodo_final,fe_mes_expiracion_tarjeta,fe_anio_expiracion_tarjeta,fg_pago_manual)  ";
                                    $Query.="VALUES ($fl_institutonew,$fl_princing,$mn_total_plan,'$fg_plan',$no_total_licencias,$no_licencias_disponibles,$no_licencias_disponibles,$no_licencias_usadas,'$fg_estatus','$fe_periodo_inicial','$fe_periodo_final','','','1')";
                                    $fl_current_plan=EjecutaInsert($Query);
                                    
                                    #Se genera su invoice.
                                    #se inserta el registro y costo por mes en su bitacora de pagos      
                                    $Query="INSERT INTO k_admin_pagos (fl_current_plan,mn_total,fg_publicar,fe_periodo_inicial,fe_periodo_final, fg_pagado,fe_pago,ds_descripcion,fg_motivo_pago,mn_costo_por_licencia,id_invoice_stripe,mn_descuento,ds_comentario,ds_cheque,cl_metodo_pago)";
                                    $Query.="VALUES($fl_current_plan,$mn_total_plan,'1','$fe_periodo_inicial_ap','$fe_periodo_final_ap','1','','$ds_descripcion_ap','1',$mn_costo_por_licencia_ap,'$id_invoice_ap',$mn_descuento_ap,'$ds_comentario_ap','$ds_cheque_ap',$cl_metodo_pago_ap) ";
                                    $fl_adm_pagos=EjecutaInsert($Query);
                                    
                                    #Guardaso el registro de pago(bitacora de pagos).
                                    $Query="INSERT INTO k_pago_stripe(id_cliente_stripe,id_pago_stripe,id_plan_stripe,id_suscripcion_stripe,fg_motivo_pago,ds_email,ds_descripcion_pago,mn_monto,mn_tax,mn_total, fe_creacion,fl_instituto)";
                                    $Query.="VALUES('$id_cliente_stripe','$id_pago_stripe','$id_plan_stripe','$id_suscripcion_stripe','1','$ds_email_stripe','$ds_descripcion_pago',$mn_monto_,$mn_tax_stripe,$mn_total,CURRENT_TIMESTAMP, $fl_institutonew)";
                                    $fl_pago=EjecutaInsert($Query);
                                    
                                }  
                                
                            }else{

                                #Verifica si existe ese registro en FAME.
                                $Query='SELECT ds_instituto,school_id FROM c_instituto WHERE ds_instituto="'.$nb_school.'" AND school_id="'.$school_id.'"  ';
                                $row=RecuperaValor($Query);
                                $ds_instituto_db=$row['ds_instituto'];
                                $school_id_db=$row['school_id'];
                                
                                GeneraLog($file_name_txt,date("F j, Y, g:i a").$Query);
                                
                                #Se encontro registro igualito.
                                if(!empty($row[0])){
                                    
                                    #Se inserta en primera instancia la bitacora de estudiantes.
                                    $Queryui ='INSERT INTO st_school (upload_id,school_id,operation_code,nb_school,fe_creacion)';
                                    $Queryui.='VALUES('.$fl_upload.',"'.$school_id.'","NO_CHANGE","'.$nb_school.'",CURRENT_TIMESTAMP)';
                                    $st_id=EjecutaInsert($Queryui);
                                    
                                    GeneraLog($file_name_txt,date("F j, Y, g:i a")."Se encuentra registro igualito y se realiza insert".$Queryui);

                                    if($st_id){
                                        $unchanged_count_school++;
                                    }

                                    #Actualizamos para decirle que ese proceso ya fue revisado.
                                    EjecutaQuery('UPDATE c_instituto SET fg_scf_revisado=1 WHERE school_id="'.$school_id.'" AND  ds_instituto="'.$nb_school.'" ');					
                                    
                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE c_instituto SET fg_scf_revisado=1 WHERE school_id="'.$school_id.'" AND  ds_instituto="'.$nb_school.'"');
                                    
                                    #Se genera el array de los datos insertados.
                                    $unchanged_school_array['unchanged_school'.$unchanged_count_school]=array(
                                            'school_id'=>$school_id,
                                            'nb_school'=>$nb_school
                                        
                                    );
                                    
                                }else{
                                    
                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').'No se encontro registro y procede a verificar si existe ese school_id='.$school_id.' ');
                                    
                                    #Verifica si existe ese registro en FAME.()
                                    $Query='SELECT ds_instituto,school_id FROM c_instituto WHERE school_id="'.$school_id.'"  ';
                                    $row=RecuperaValor($Query);
                                    $ds_instituto_db=$row['ds_instituto'];
                                    $school_id_db=$row['school_id'];
                                    
                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                    
                                    #Si se encontro el registro y posiblemente solo cambio su nombre.
                                    if($school_id_db==$school_id){
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se encontro el school_id, ".$school_id_db.", posiblemente  solo cambio algunos datos");
                                        
                                        #UPDATE C_INSTITUTO
                                        $Query='UPDATE c_instituto SET fg_scf_revisado=1 WHERE school_id="'.$school_id.'" ';
                                        EjecutaQuery($Query);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                        
                                        #Se inserta en primera instancia la bitacora de estudiantes.
                                        $Queryui ='INSERT INTO st_school (upload_id,school_id,operation_code,nb_school,fe_creacion)';
                                        $Queryui.='VALUES('.$fl_upload.',"'.$school_id.'","MODIFY","'.$nb_school.'",CURRENT_TIMESTAMP)';
                                        $st_id=EjecutaInsert($Queryui);
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                        if($st_id){
                                            $modified_count_school++;
                                        }

                                        #Se genera el array de los datos.
                                        $modified_school_array['modified_school'.$modified_count_school]=array(
                                                'school_id'=>$school_id,
                                                'nb_school'=>$nb_school
                                            
                                        );
                                        
                                    }else{
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."No se encontro el registro en FAME,se genrea nuevo registro");
                                        
                                        #Se genera nuevo registro.
                                        #Se inserta en primera instancia la bitacora de estudiantes.
                                        $Queryui ='INSERT INTO st_school (upload_id,school_id,operation_code,nb_school,fe_creacion)';
                                        $Queryui.='VALUES("'.$fl_upload.'","'.$school_id.'","ADD","'.$nb_school.'",CURRENT_TIMESTAMP)';
                                        $st_id=EjecutaInsert($Queryui);

                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                        if($st_id){
                                            $contador_insert_school++;
                                        }
                                        
                                        #Se gebera el registro en FAME. IMPORTANTE FALTA SABER QUE USUARIO SERA EL ADMIN.
                                        #$Query ='INSERT INTO c_instituto(fl_usuario_sp,cl_tipo_instituto,cl_plan_fame,ds_instituto,school_id, fl_pais,no_usuarios,ds_codigo_pais,ds_codigo_area,no_telefono,fe_creacion,fe_trial_expiracion,fg_tiene_plan,fg_activo,ds_foto,ds_rfc,fg_princing_default,fg_export_moodle,fg_parent_authorization,fl_instituto_rector,fg_scf,fg_scf_revisado) ';
                                        #$Query .='VALUES('.$fl_usuario.',2,0,"'.$nb_school.'","'.$school_id.'",73,0,"+33", NULL, NULL, CURRENT_TIMESTAMP,"2019-12-31", "0", "1", NULL, NULL, "0", "0", "1",107,"1",1 )';
                                        #$fl_institutonew=EjecutaInsert($Query);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                        
                                        #Se genera el array de los datos.
                                        $insert_school_array['insert_school'.$contador_insert_school]=array(
                                                'school_id'=>$school_id,
                                                'nb_school'=>$nb_school
                                            
                                        );
                                        
                                    }
                                    
                                }
                                
                            }
                            
                        }#end wile
                        
                    }#end file open.

                    #Revisamos cuanto no fueron porcesados de los existentes en FAME Y determinamos que fuweron eliminados.
                    $Query="SELECT COUNT(*)FROM c_instituto WHERE fg_scf_revisado=0  AND fl_instituto_rector=$fl_instituto_rector ";
                    $ro=RecuperaValor($Query);
                    $deleted_count_school=$ro[0];

                    #Desactivamos estos institutos.
                    EjecutaQuery("UPDATE c_instituto SET fg_activo='0' WHERE fg_scf_revisado=0 AND fl_instituto_rector=$fl_instituto_rector ");

                    #Actualizamos la bitacora de carga.
                    $Query="UPDATE stage_uploads SET end_time=CURRENT_TIMESTAMP,
										 added_count=$contador_insert_school,
										 unchanged_count=$unchanged_count_school,
										 deleted_count=$deleted_count_school,
										 modified_count=$modified_count_school,
                                         status_cd='COMPLETED'
										 WHERE id=$fl_upload ";
                    EjecutaQuery($Query);
                    
                    #Recuperamos el proceso de carga del archivo.
                    $Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
									,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
									TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
									TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds
									FROM stage_uploads WHERE id=$fl_upload ";

                    $row=RecuperaValor($Query);

                    $user_id=$row['user_id'];
                    $upload_file_path=$row['upload_file_path'];
                    $upload_file_name=$row['upload_file_name'];
                    $upload_type=$row['upload_type'];
                    $upload_date=$row['upload_date'];
                    $status_cd=$row['status_cd'];
                    $start_time=GeneraFormatoFecha($row['start_time']);
                    $start_time_=$row['start_time'];
                    $end_time=$row['end_time'];
                    $proc_status=$row['proc_status'];
                    $upload_time_hrs=$row['hrs'];
                    $upload_time_minutes=$row['minutes'];
                    $upload_time_seconds=$row['seconds'];
                    
                    $runtime=$upload_time_hrs."h ". $upload_time_minutes."m ".$upload_time_seconds."s";
                    
                    if($end_time){
                        $finish="<i class='fa fa-check-circle' style='color:#226108;'></i>";
                        
                    }
                    
                    #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
                    $date = date_create($start_time_);
                    $start_time_=date_format($date,'F j, Y, g:i:s a');
                    
                    $date = date_create($end_time);
                    $end_time=date_format($date,'F j, Y, g:i:s a');

                    #cALCULAMOS TOTALES POR RENGLON.
                    $Total_school=$contador_insert_school+$deleted_count_school+$unchanged_count_school+$modified_count_school;

                    #Calculamos el treshold.
                    $threshold_modified_school= ($modified_count_school/$Total_school)*100;
                    $threshold_insert_school= ($contador_insert_school/$Total_school)*100;
                    $threshold_deleted_school= ($deleted_count_school/$Total_school)*100;
                    $threshold_unchanged_school= ($unchanged_count_school/$Total_school)*100;

                    #Default para ejecutar en FAME.
                    $permiso_ejecutar_DB_FAME=1;
                    $error_threshold=0;
                    #El limite es 5%, si rebasa esa cantidad envia emial con los datos de los isntitutos procesados.
                    if($threshold_modified_school>5){					
                        $permiso_ejecutar_DB_FAME=0;
                        $error_threshold=1;
                    }
                    if($threshold_insert_school>5){					
                        $permiso_ejecutar_DB_FAME=0;
                        $error_threshold=1;
                    }
                    if($threshold_deleted_school>5){	
                        $permiso_ejecutar_DB_FAME=0;
                        $error_threshold=1;
                    }
                    if($threshold_unchanged_school>5){
                        #$permiso_ejecutar_DB_FAME=0;
                    }
                    #Es la primera carga.
                    if($primeracarga==1){
                        if($aplica_threshold==1){
                            $permiso_ejecutar_DB_FAME=1;
                            $error_threshold=0;
                        }
                    }
                    
                    if($aplica_threshold==1){
                        $permiso_ejecutar_DB_FAME=1;
                        $error_threshold=0;
                        
                    }else{
                        
                        EjecutaQuery("UPDATE stage_uploads SET nothresh_modified='$threshold_modified_school',nothresh_deleted='$threshold_deleted_school',nothresh_added='$threshold_insert_school' WHERE id=$fl_upload "); 
                        
                    }
                    
                    #Actualizamos la bitacora de lo que fue importado.
                    $Query="UPDATE stage_uploads SET proc_status='$error_threshold', modified_count=$modified_count_school, deleted_count=$deleted_count_school,added_count=$contador_insert_school,unchanged_count=$unchanged_count_school WHERE id=$fl_upload ";
                    EjecutaQuery($Query);
                    
                    $datas=array();                   
                    $datas['datos']= array(
                       'name_reference'=>'School',   
                       'contador_insert'=>$contador_insert_school,
                       'deleted_count'=>$deleted_count_school,
                       'unchanged_count'=>$unchanged_count_school,
                       'upload_count'=>$Total_school,
                       'modified_count'=>$modified_count_school,
                       'threshold_modified'=>$threshold_modified_school,
                       'threshold_insert'=>$threshold_insert_school,
                       'threshold_deleted'=>$threshold_deleted_school,
                       'threshold_unchanged'=>$threshold_unchanged_school,
                       'error_threshold'=>$error_threshold
                    );

                    #fINALIZA EL PROCESO
                    $Query='UPDATE stage_uploads SET status_cd="SEALED",upload_count='.$Total_school.' WHERE id='.$fl_upload.' ';
                    EjecutaQuery($Query);
                    
                    /****************/
                    if($permiso_ejecutar_DB_FAME==1){

                        #Proceso de la carga.
                        if ($file = fopen($ruta_completa_archivo, "r")){
                            
                            GeneraLog($file_name_txt,date("F j, Y, g:i a")."Empieza a leer el archivo");
                            
                            # Lee los nombres de los campos
                            // $name_camps = fgetcsv($file, 0, ",", "\"", "\"");
                            // $num_camps = count($name_camps);
                            // $names_camps[$num_camps -1];
                            $tot_reg1 = 0;
                            $contador_insert_school=0;
                            $unchanged_count_school=0;
                            $deleted_count_school=0;
                            $modified_count_school=0;
                            $upload_count_school=0;
                            $insert_school_array=array();
                            $upload_school_array=array();
                            $unchanged_school_array=array();
                            $deleted_school_array=array();
                            $modified_school_array=array();
                            
                            while ($data = fgetcsv ($file, 0, ",")){
                                $school_id = $data[0];
                                $nb_school = $data[1];
                                //$nb_school = htmlentities($data[1],ENT_QUOTES,"UTF-8");
                                
                                if($primeracarga==1){							
                                    
                                    $contador_insert_school++;                                      
                                    
                                }else{									

                                    #Verifica si existe ese registro en FAME.
                                    $Query='SELECT ds_instituto,school_id FROM c_instituto WHERE ds_instituto="'.$nb_school.'" AND school_id="'.$school_id.'"  ';
                                    $row=RecuperaValor($Query);
                                    $ds_instituto_db=$row['ds_instituto'];
                                    $school_id_db=$row['school_id'];
                                    
                                    GeneraLog($file_name_txt,date("F j, Y, g:i a").$Query);
                                    
                                    #Se encontro registro igualito.
                                    if(!empty($row[0])){

                                        $unchanged_count_school++;
                                        
                                        #aCTUALIZAMO Y DECIMSO QUE YA FUE REVISADO 
                                        EjecutaQuery('UPDATE c_instituto SET fg_scf_revisado=1 WHERE school_id="'.$school_id.'" ');					
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE c_instituto SET fg_scf_revisado=1 WHERE school_id="'.$school_id.'" AND  ds_instituto="'.$nb_school.'"');
                                        
                                        #Se genera el array de los datos insertados.
                                        $unchanged_school_array['unchanged_school'.$unchanged_count_school]=array(
                                                'school_id'=>$school_id,
                                                'nb_school'=>$nb_school
                                    
                                        );
                                        
                                        
                                    }else{
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').'No se encontro registro y procede a verificar si existe ese school_id='.$school_id.' ');
                                        
                                        #Verifica si existe ese registro en FAME.()
                                        $Query='SELECT ds_instituto,school_id FROM c_instituto WHERE school_id="'.$school_id.'"  ';
                                        $row=RecuperaValor($Query);
                                        $ds_instituto_db=$row['ds_instituto'];
                                        $school_id_db=$row['school_id'];
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                        
                                        #Si se encontro el registro y posiblemente solo cambio su nombre.
                                        if($school_id_db==$school_id){

                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se encontro el school_id, ".$school_id_db.", posiblemente  solo cambio algunos datos");
                                            
                                            #UPDATE C_INSTITUTO
                                            $Query='UPDATE c_instituto SET ds_instituto="'.$nb_school.'",fg_scf_revisado=1 WHERE school_id="'.$school_id.'" ';
                                            EjecutaQuery($Query);
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                            
                                            #Se inserta en primera instancia la bitacora de estudiantes.
                                            ##$Queryui ='INSERT INTO st_school (upload_id,school_id,operation_code,nb_school,fe_creacion)';
                                            ##$Queryui.='VALUES('.$fl_upload.',"'.$school_id.'","MODIFY","'.$nb_school.'",CURRENT_TIMESTAMP)';
                                            ##$st_id=EjecutaInsert($Queryui);
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                            ##if($st_id){
                                            $modified_count_school++;
                                            ##}

                                            #Se genera el array de los datos.
                                            $modified_school_array['modified_school'.$modified_count_school]=array(
                                                    'school_id'=>$school_id,
                                                    'nb_school'=>$nb_school
                                        
                                            );
                                            
                                        }else{
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."No se encontro el registro en FAME,se genrea nuevo registro");
                                            
                                            #Se genera nuevo registro.
                                            #Se inserta en primera instancia la bitacora de estudiantes.
                                            #$Queryui ='INSERT INTO st_school (upload_id,school_id,operation_code,nb_school,fe_creacion)';
                                            #$Queryui.='VALUES("'.$fl_upload.'","'.$school_id.'","ADD","'.$nb_school.'",CURRENT_TIMESTAMP)';
                                            #$st_id=EjecutaInsert($Queryui);

                                            #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                            #if($st_id){
                                            $contador_insert_school++;
                                            #}
                                            
                                            #Se gebera el registro en FAME. IMPORTANTE FALTA SABER QUE USUARIO SERA EL ADMIN.
                                            $Query ='INSERT INTO c_instituto(fl_usuario_sp,cl_tipo_instituto,cl_plan_fame,ds_instituto,school_id, fl_pais,no_usuarios,ds_codigo_pais,ds_codigo_area,no_telefono,fe_creacion,fe_trial_expiracion,fg_tiene_plan,fg_activo,ds_foto,ds_rfc,fg_princing_default,fg_export_moodle,fg_parent_authorization,fl_instituto_rector,fg_scf,fg_scf_revisado) ';
                                            $Query .='VALUES('.$fl_usuario.',1,0,"'.$nb_school.'","'.$school_id.'",73,0,"+33", NULL, NULL, CURRENT_TIMESTAMP,"2019-12-31", "0", "1", NULL, NULL, "0", "0", "1",'.$fl_instituto_rector.',"1",1 )';
                                            $fl_institutonew=EjecutaInsert($Query);
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                            #Se genera el array de los datos.
                                            $insert_school_array['insert_school'.$contador_insert_school]=array(
                                                    'school_id'=>$school_id,
                                                    'nb_school'=>$nb_school
                                        
                                            );

                                        }
                                        
                                    }                                
                                    
                                    #end_treshold.
                                    
                                }

                            }#end while.
                            
                        }#end file open.
                        
                        #Revisamos cuanto no fueron porcesados de los existentes en FAME Y determinamos que fuweron eliminados.
                        $Query="SELECT COUNT(*)FROM c_instituto WHERE fg_scf_revisado=0 AND fg_scf='1' ";
                        $ro=RecuperaValor($Query);
                        $deleted_count_school=$ro[0];

                        #Desactivamos estos institutos.
                        EjecutaQuery("UPDATE c_instituto SET fg_activo='0' WHERE fg_scf_revisado=0 AND fg_scf='1'  ");

                        /*#Actualizamos la bitacora de carga.
                        $Query="UPDATE stage_uploads SET end_time=CURRENT_TIMESTAMP,
                        added_count=$contador_insert_school,
                        unchanged_count=$unchanged_count_school,
                        deleted_count=$deleted_count_school,
                        modified_count=$modified_count_school,
                        status_cd='COMPLETED'
                        WHERE id=$fl_upload ";
                        EjecutaQuery($Query);
                         */
                        
                        
                        /* #Recuperamos el proceso de carga del archivo.
                        $Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
                        ,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
                        TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
                        TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds
                        FROM stage_uploads WHERE id=$fl_upload ";
                        $row=RecuperaValor($Query);
                        $user_id=$row['user_id'];
                        $upload_file_path=$row['upload_file_path'];
                        $upload_file_name=$row['upload_file_name'];
                        $upload_type=$row['upload_type'];
                        $upload_date=$row['upload_date'];
                        $status_cd=$row['status_cd'];
                        $start_time=GeneraFormatoFecha($row['start_time']);
                        $start_time_=$row['start_time'];
                        $end_time=$row['end_time'];
                        $proc_status=$row['proc_status'];
                        $upload_time_hrs=$row['hrs'];
                        $upload_time_minutes=$row['minutes'];
                        $upload_time_seconds=$row['seconds'];
                        
                        $runtime=$upload_time_hrs."h ". $upload_time_minutes."m ".$upload_time_seconds."s";
                        
                        if($end_time){
                        $finish="<i class='fa fa-check-circle' style='color:#226108;'></i>";
                        
                        }
                        
                        #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
                        $date = date_create($start_time_);
                        $start_time_=date_format($date,'F j, Y, g:i:s a');
                        
                        $date = date_create($end_time);
                        $end_time=date_format($date,'F j, Y, g:i:s a');

                        #cALCULAMOS TOTALES POR RENGLON.
                        $Total_school=$contador_insert_school+$deleted_count_school+$unchanged_count_school+$modified_count_school;
                         */

                        

                    }else{
                        
                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Error Treshold Porcentaje error");	
                        
                    }
                    
                    /***************/
                    #Envia notificacion via email con los resultados.
                    #Se les envia email de notificacion al usuario. ya incluye threshold.
                    //EnvioEmailCSF($ds_email_instituto,'',$first_name_instituto,$last_name_instituto,$fl_instituto,178,'',$datas);
                    



                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Rnvia email y Termina lectura del archivo".date('F j, Y, g:i a'));
                    GeneraLog($file_name_txt,"====================================Finaliza proceso ".date('F j, Y, g:i a')."=================================================");
                    

                }#finaliza school.	  
                



                /*******************************STUDENTS**************************************************************************************************************************************************************/


                if(($upload_type=='student')||($upload_type=='Student')||($upload_type=='STUDENT')){

                    #Generamos el log.
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Upload type:".$upload_type);

                    $Query="SELECT COUNT(*) FROM st_students WHERE fl_instituto=$fl_instituto ";
                    $row=RecuperaValor($Query);
                    $conta_reg_ini=$row[0];

                    #Generamos el log.
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Query-->".$Query);

                    if($conta_reg_ini==0){
                        $primeracarga=1;
                        #Generamos el log.
                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Es primera Carga");
                    }else{
                        $primeracarga=0;
                        #Generamos el log.
                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."No es la primera Carga");
                    }

                    #Proceso de la carga.
                    if ($file = fopen($ruta_completa_archivo, "r")){
                        
                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Empieza a leer el archivo");
                        
                        # Lee los nombres de los campos
                        // $name_camps = fgetcsv($file, 0, ",", "\"", "\"");
                        // $num_camps = count($name_camps);
                        // $names_camps[$num_camps -1];
                        $tot_reg1 = 0;
                        $contador_insert_std=0;
                        $unchanged_count_student=0;
                        $deleted_count_student=0;
                        $modified_count_student=0;
                        
                        $insert_student_array=array();
                        $upload_student_array=array();
                        $unchanged_student_array=array();
                        $deleted_student_array=array();
                        $modified_student_array=array();

                        while ($data = fgetcsv ($file, 0, ",")){

                            if ($data[0]=='First Name' || $data[1]=='Last Name' || $data[2]=='School ID' || $data[3]=='Email' || $data[4]=='Course ID' || $data[5]=='Status' || $data[11]=='School Level' || $data[12]=='Teacher ID' || $data[13]=='Student ID' || $data[14]=='Username') {

                                // Do Noting
                                
                            } else {

                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."Empieza a leer el archivo");
                                
                                $first_name = $data[0];
                                $last_name_pat = $data[1];
                                $school_id = $data[2];
                                $email = $data[3];
                                $course_id = $data[4];
                                $status = $data[5];
                                $last_login = $data[6];//fe_ultacc
                                $progress = $data[7];//ds_progreso
                                $assesment = $data[8];//ds_assesment
                                $gpa = $data[9];
                                $grupo = $data[10];//nb_grupo
                                $school_level = $data[11];//fl_grado
                                $teacher_id = $data[12];
                                $student_id=$data[13];//student_id
                                $username=$data[14];//ds_alias
                                $master_time_table_id=$data[15];
                                
                                if(($status=='N/A')||(empty($status))){
                                    $status=0;
                                }else{
                                    $status=1;
                                }
                                if(($gpa=='N/A')||(empty($gpa))){
                                    $gpa=0;
                                }
                                if(($grupo=='N/A')||(empty($grupo))){
                                    $grupo="";
                                }

                                #Recuperamos su teacher si existe.
                                $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'"  ';

                                $rop=RecuperaValor($Query);
                                
                                $fl_maestro=$rop['fl_maestro_sp'];
                                if(empty($fl_maestro))
                                    $fl_maestro=0;  
                                
                                if((!empty($first_name))&&(!empty($teacher_id))){

                                    #Identificamos si pertenece algun instituto poreviamente cargado.
                                    $Query="SELECT fl_instituto,fl_usuario_sp FROM c_instituto WHERE school_id='$school_id'  ";
                                    $rok=RecuperaValor($Query);
                                    $fl_instituto_db=$rok['fl_instituto'];
                                    $fl_usuario_sp_instituto=$rok['fl_usuario_sp'];
                                    if(empty($fl_instituto_db))
                                        $fl_instituto_db=0;
                                    
                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                    
                                    #Inicia proceso
                                    $Query="UPDATE stage_uploads SET status_cd='STARTED',upload_file_name_log='$new_archive_procesed' WHERE id=$fl_upload ";
                                    EjecutaQuery($Query);
                                    
                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."UPDATE stage_uploads SET status_cd='STARTED',upload_file_name_log='$new_archive_procesed' WHERE id=$fl_upload  ");

                                    $fl_perfil_sp=PFL_ESTUDIANTE_SELF;
                                    
                                    #Siempre se genera la bitacora de estudiantes.
                                    $Queryui ='INSERT INTO st_students_bitacora ( upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username)';
                                    $Queryui.='VALUES('.$fl_upload.',"'.$student_id.'","LOADED","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'")';
                                    $st_id_bitacora=EjecutaInsert($Queryui);
                                    
                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                    /*NOTA, EN LA TABLA st_student el student_id de donde viene??????? archivo excel no esta identifiacdo para hacer la comparacion
                                    2. EN EL ARCHIVO DE excel muestra el campo pupil_number, la cual en BD no existe.*/
                                    
                                    if($primeracarga==1){
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Es primeraCarga");
                                        
                                        #Se inserta en primera instancia la bitacora de estudiantes.
                                        $Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username,fl_instituto)';
                                        $Queryui.='VALUES('.$fl_upload.',"'.$student_id.'","LOADED","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'",'.$fl_instituto_db.')';
                                        $st_id=EjecutaInsert($Queryui);

                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);
                                        
                                        #Verificamos que ese usuario no exista y se inserta.
                                        $Query='SELECT student_id FROM st_students WHERE student_id ="'.$student_id.'" and upload_id='.$fl_upload.'  ';
                                        $rop=RecuperaValor($Query);
                                        $fl_alumno_esxistente=$rop['student_id'];

                                        #Veriffica que no exista el usuario ni alias/username
                                        $Query='SELECT COUNT(*) FROM c_usuario WHERE ds_alias="'.$username.'"  ';
                                        $regu=RecuperaValor($Query);
                                        $existe_usario=$regu[0];

                                        if(empty($existe_usario)){
                                            $contador_insert_std++;
                                        }
                                        
                                        #Se generan registros en FAME.
                                        
                                        #Generamos su pasword temporal.
                                        #$ds_pass=substr( md5(microtime()), 1, 8);
                                        
                                        # Genera un identificador de sesion
                                        #$cl_sesion_nueva = sha256($username.$first_name.$last_name_pat.$ds_pass);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera su sesion nueva:".$cl_sesion_nueva);

                                        #$Query ='INSERT INTO c_usuario (ds_login,cl_sesion,ds_password,ds_alias,ds_nombres,ds_apaterno,ds_email,fg_activo,fe_alta,fl_perfil_sp,fl_instituto,fg_scf,fg_scf_revisado)';
                                        #$Query.='VALUES("'.$username.'","'.$cl_sesion_nueva.'","'.sha256($ds_pass).'","'.$username.'","'.$first_name.'","'.$last_name_pat.'","'.$email.'","1",CURRENT_TIMESTAMP,15,'.$fl_instituto_db.',1,1)';
                                        #$fl_user=EjecutaInsert($Query);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Realiza Insert a la tabla de c_usuario-->".$Query);
                                        
                                        #Asigmaos al teacher.
                                        #$Query="UPDATE c_usuario SET fl_usu_invita=".$fl_maestro." WHERE fl_usuario=$fl_user ";
                                        #EjecutaQuery($Query);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Genera Rekacion student-teacher-->".$Query);
                                        
                                        #Se genera el dato en c_alumno_sp
                                        #$Query="INSERT INTO c_alumno_sp (fl_alumno_sp)";
                                        #$Query.="VALUES($fl_user)";
                                        #EjecutaQuery($Query);

                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."inserta al alumno".$Query);
                                        
                                        #Verificamos el isntitutio
                                        #$Query='SELECT fl_instituto FROM c_instituto WHERE school_id="'.$school_id.'" AND fg_scf="1" ';
                                        #$rop=RecuperaValor($Query);
                                        #$fl_institutions=$rop['fl_instituto'];

                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Recuperamos al instituto ".$Query);

                                        #EjecutaQuery("UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user  ");
                                        #GeneraLog($file_name_txt,"UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user  ");

                                        

                                        #Recuperamos el no. de usuarios que tiene el instituto y le sumaos el nuevo registro
                                        #$Query="SELECT no_usuarios FROM c_instituto WHERE fl_instituto=$fl_institutions ";
                                        #$row=RecuperaValor($Query);
                                        #$no_usuarios_actual = $row[0] +1 ;
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                        
                                        #Actualizamos el registro de numero de usuarios que tiee el isntituto.
                                        #$Query="UPDATE c_instituto SET no_usuarios=$no_usuarios_actual WHERE fl_instituto=$fl_institutions ";
                                        #EjecutaQuery($Query);

                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);


                                        #Recuperamos el fl_usuario_mae4stro atarvaes de su techaer Id que previaente ya fue insertado.
                                        #$Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'" ';
                                        #$row=RecuperaValor($Query);
                                        #$fl_maestro_sp_db=$row['fl_maestro_sp'];

                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                        
                                        #if(empty($fl_maestro_sp_db))
                                        #    $fl_maestro_sp_db=0;
                                        #if(empty($fl_programa_sp))
                                        #    $fl_programa_sp=0;
                                        
                                        
                                        /***NOTA**/
                                        #Se inserta en k_usuario_programa  aqui falta la relacion de programa del arcghivo csf con el fl_prorama de FAME. ellos deben asignar teacher.
                                        
                                        #$Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa,course_id) ";
                                        #$Query.="VALUES($fl_user,$fl_programa_sp,0,'0','0','RD','0',0,$fl_maestro_sp_db,'0',CURRENT_TIMESTAMP,'$course_id')";
                                        #$fl_usu_pro=EjecutaInsert($Query);
                                        
                                        
                                        #Se les envia email de notificacion al usuario.
                                        #EnvioEmailCSF($email,$username,$first_name,$last_name_pat,$fl_instituto,178,$ds_pass,'');
                                        
                                        
                                        
                                        
                                    }else{

                                        #Ya es la segunda carga.                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Pasa ser comparado ya existen datos en Fame.");

                                        #Verifica si existe ese registro en FAME.
                                        $Query='SELECT ds_login,ds_alias,ds_nombres,ds_apaterno,ds_email,b.fg_scf,b.fl_usuario,c.course_id  
                                                        FROM c_alumno_sp a 
                                                        JOIN c_usuario b ON b.fl_usuario=a.fl_alumno_sp 
                                                        JOIN k_usuario_programa c ON c.fl_usuario_sp=b.fl_usuario AND c.course_id="'.$course_id.'"
                                                        JOIN c_instituto d ON d.fl_instituto= b.fl_instituto 
                                                        WHERE d.school_id="'.$school_id.'" AND  b.ds_login="'.$username.'" AND ds_nombres="'.$first_name.'" AND ds_apaterno="'.$last_name_pat.'" AND ds_email="'.$email.'"   AND fl_perfil_sp=15
                                                        ';

                                        $row=RecuperaValor($Query);
                                        $ds_login_db=$row['ds_login'];
                                        $username_db=$row['ds_login'];
                                        $course_id_db=$row['course_id'];
                                        $fl_usuario_student=$row['fl_usuario'];
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Pasa verificar si encuentra el registro.");
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                        
                                        //falta obtener instituto. instituto(aqui falta)

                                        #Se encontro registro igualito.
                                        if(!empty($row[0])){
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se encontro registro y solo pasamos generar bitacora de carga");
                                            #Se inserta en primera instancia la bitacora de estudiantes.
                                            $Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username,fl_instituto)';
                                            $Queryui.='VALUES('.$fl_upload.',"'.$student_id.'","NO_CHANGE","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'",'.$fl_instituto.')';
                                            $st_id=EjecutaInsert($Queryui);

                                            #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);
                                            if($student_id_revisado<>$student_id){                                       
                                                $unchanged_count_student++;                                                                         
                                            }
                                            EjecutaQuery('UPDATE c_usuario SET fg_scf_revisado=1,fg_activo="1" WHERE fl_usuario='.$fl_usuario_student.' AND fl_perfil_sp='.PFL_ESTUDIANTE_SELF.'  ');
                                            
                                            #Se genera el array.
                                            $unchanged_student_array['unchanged_student'.$unchanged_count_student]=array(
                                                    'fl_usuario'=>$fl_usuario_student,
                                                    'email'=>$email,
                                                    'first_name'=>$first_name,
                                                    'last_name_pat'=>$last_name_pat
                                                
                                            );
                                            
                                        }else{
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."No encontro registro y pasa a verificar si existe student_id=".$username);
                                            
                                            #Verifica si existe ese registro en FAME.
                                            $Query='SELECT ds_login,ds_alias,ds_nombres,ds_apaterno,ds_email,b.fg_scf,b.fl_usuario,c.course_id  
                                                        FROM c_alumno_sp a 
                                                        JOIN c_usuario b ON b.fl_usuario=a.fl_alumno_sp  
                                                        JOIN k_usuario_programa c ON c.fl_usuario_sp=b.fl_usuario AND c.course_id="'.$course_id.'"
                                                        WHERE  b.ds_login="'.$username.'"  AND fl_perfil_sp="'.PFL_ESTUDIANTE_SELF.'"  ';
                                            $row=RecuperaValor($Query);
                                            $ds_login_db=$row['ds_login'];
                                            $username_db=$row['ds_login'];
                                            $course_id_db=$row['course_id'];
                                            $fl_usuario_db=$row['fl_usuario'];

                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                            #Si se encontro el username y el curso.
                                            if(($username_db==$username)&&($course_id_db==$course_id)){


                                                
                                                #Se inserta en primera instancia la bitacora de estudiantes.
                                                $Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username,fl_instituto)';
                                                $Queryui.='VALUES('.$fl_upload.',"","MODIFY","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'",'.$fl_instituto_db.')';
                                                $st_id=EjecutaInsert($Queryui);

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera carga -->".$Queryui);
                                                if($student_id_revisado<>$student_id){
                                                    //if($st_id){
                                                    $modified_count_student++;
                                                    //}
                                                }
                                                
                                                EjecutaQuery('UPDATE c_usuario SET  fg_scf_revisado=1 WHERE fl_usuario='.$fl_usuario_db.' ');

                                                #Actualizamos datos generales de ese registro.
                                                #$Query='UPDATE c_usuario SET ds_nombres="'.$first_name.'" ,ds_apaterno="'.$last_name.'",ds_email="'.$email.'",fl_instituto='.$fl_instituto_db.',fg_scf_revisado=1,fg_activo="1"  WHERE fl_usuario='.$fl_usuario_db.' ';
                                                #EjecutaQuery($Query);
                                                
                                                #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Si se encontro y actualiamios datos adicionales-->".$Query);
                                                

                                                #if(empty($fl_maestro))
                                                #    $fl_user_invitador=$fl_usuario_sp_instituto;

                                                #Asigmaos al usuario invitador
                                                #$Query="UPDATE c_usuario SET fl_usu_invita=".$fl_user_invitador." WHERE fl_usuario=$fl_usuario_db ";
                                                #EjecutaQuery($Query);

                                                #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se actualiza relacion teacher student-->".$Query);
                                                
                                                #Se genera el array.
                                                $modified_student_array['modified_student'.$modified_count_student]=array(
                                                        'fl_usuario'=>$fl_usuario_db,
                                                        'email'=>$email,
                                                        'first_name'=>$first_name,
                                                        'last_name_pat'=>$last_name_pat,
                                                        'fl_instituto'=>$fl_instituto_db
                                                    
                                                );
                                                
                                                
                                                
                                                
                                                
                                                

                                            }else{
                                                
                                                #Solo cambio de curso, y si se encontro el username.
                                                
                                                if($username_db==$username){
                                                    
                                                    #Se inserta en primera instancia la bitacora de estudiantes.
                                                    $Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username,fl_instituto)';
                                                    $Queryui.='VALUES('.$fl_upload.',"'.$student_id.'","MODIFY","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'",'.$fl_instituto_db.')';
                                                    $st_id=EjecutaInsert($Queryui);

                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera carga -->".$Queryui);
                                                    if(($st_id)&&($student_id_revisado<>$student_id)){
                                                        $modified_count_student++;
                                                    }
                                                    
                                                    EjecutaQuery('UPDATE c_usuario SET  fg_scf_revisado=1 WHERE fl_usuario='.$fl_usuario_db.' ');
                                                    
                                                    #Actualizamos datos generales de ese registro.
                                                    //$Query='UPDATE c_usuario SET ds_nombres="'.$first_name.'",fg_activo="1" ,ds_apaterno="'.$last_name.'",ds_email="'.$email.'",fl_instituto='.$fl_instituto_db.'  WHERE fl_usuario='.$fl_usuario_db.' ';
                                                    //EjecutaQuery($Query);
                                                    
                                                    //GeneraLog($file_name_txt,"Actualiza datos generales--> ".$Query);
                                                    
                                                    #Asigmaos al teacher.
                                                    //$Query="UPDATE c_usuario SET fl_usu_invita=".$fl_maestro." WHERE fl_usuario=$fl_usuario_db ";
                                                    //EjecutaQuery($Query);

                                                    //GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se actualiza relacion teacher student-->".$Query);
                                                    
                                                    #Se genera el array.
                                                    $modified_student_array['modified_student'.$modified_count_student]=array(
                                                            'fl_usuario'=>$fl_usuario_db,
                                                            'email'=>$email,
                                                            'first_name'=>$first_name,
                                                            'last_name_pat'=>$last_name_pat,
                                                            'fl_instituto'=>$fl_instituto_db
                                                        
                                                    );
                                                    
                                                    
                                                }else{
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    #Se genera nuevo registro.
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera nuevo registro -->");
                                                    #El registro no fue localizado pasa ser nuevo dato.
                                                    
                                                    #Se inserta en primera instancia la bitacora de estudiantes.
                                                    $Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username,fl_instituto)';
                                                    $Queryui.='VALUES('.$fl_upload.',"'.$student_id.'","ADD","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'",'.$fl_instituto_db.')';
                                                    $st_id=EjecutaInsert($Queryui);

                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera carga -->".$Queryui);

                                                    //if($st_id){
                                                    //  $contador_insert_std++;
                                                    //}
                                                    
                                                    #Verificamos que ese usuario no exista y se inserta.
                                                    $Query='SELECT st_id FROM st_students WHERE student_id ="'.$student_id.'" AND upload_id='.$fl_upload.'   ';
                                                    $rop=RecuperaValor($Query);
                                                    $fl_alumno_esxistente=$rop['st_id'];

                                                    #Veriffica que no exusta el usuario por su alias/username
                                                    $Query='SELECT COUNT(*) FROM c_usuario WHERE ds_alias="'.$username.'"  ';
                                                    $regu=RecuperaValor($Query);
                                                    $existe_usario=$regu[0];



                                                    if((empty($fl_alumno_esxistente))&&(empty($existe_usario))){                                   
                                                        $contador_insert_std++;
                                                    }
                                                    
                                                    

                                                    #Se generan registros en FAME.
                                                    #Generamos su pasword temporal.
                                                    #$ds_pass=substr( md5(microtime()), 1, 8);
                                                    
                                                    # Genera un identificador de sesion
                                                    #$cl_sesion_nueva = sha256($username.$first_name.$last_name_pat.$ds_pass);
                                                    
                                                    #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera sesion>".$cl_sesion_nueva);
                                                    
                                                    //$Query ='INSERT INTO c_usuario (ds_login,cl_sesion,ds_password, ds_alias,ds_nombres,ds_apaterno,ds_email,fg_activo,fe_alta,fl_perfil_sp,fl_instituto,fg_scf)';
                                                    //$Query.='VALUES("'.$username.'","'.$cl_sesion_nueva.'","'.sha256($ds_pass).'","'.$username.'","'.$first_name.'","'.$last_name_pat.'","'.$email.'","1",CURRENT_TIMESTAMP,15,'.$fl_instituto_db.',1)';
                                                    //$fl_user=EjecutaInsert($Query);
                                                    
                                                    #Asigmaos al teacher.
                                                    //$Query="UPDATE c_usuario SET fl_usu_invita=".$fl_maestro." WHERE fl_usuario=$fl_user ";
                                                    //EjecutaQuery($Query);

                                                    //GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se actualiza relacion teacher-student-->".$Query);
                                                    
                                                    
                                                    #Se genera el dato en c_alumno_sp
                                                    //$Query="INSERT INTO c_alumno_sp (fl_alumno_sp)";
                                                    //$Query.="VALUES($fl_user)";
                                                    //EjecutaQuery($Query);

                                                    //GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Se genera el dato en c_alumno_sp".$Query);
                                                    
                                                    #Verificamos el isntitutio
                                                    #$Query='SELECT fl_instituto FROM c_instituto WHERE school_id="'.$school_id.'" AND fg_scf="1" ';
                                                    #$rop=RecuperaValor($Query);
                                                    #$fl_institutions=$rop['fl_instituto'];

                                                    #GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Verificamos el isntitutio".$Query);
                                                    
                                                    //EjecutaQuery("UPDATE c_usuario SET fl_instituto=$fl_institutions,fg_activo='1' WHERE  fl_usuario=$fl_user  ");
                                                    //GeneraLog($file_name_txt,"UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user ");

                                                    #Falta asociarlo al programa
                                                    #Recuperamos el fl_usuario_mae4stro atarvaes de su techaer Id que previaente ya fue insertado.
                                                    #$Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'" ';
                                                    #$row=RecuperaValor($Query);
                                                    #$fl_maestro_sp_db=$row['teacher_id'];

                                                    #GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Recuperamos el fl_usuario_mae4stro atarvaes de su techaer Id que previaente ya fue insertado.".$Query);

                                                    #if(empty($fl_maestro_sp_db)){
                                                    #    $fl_maestro_sp_db=0;
                                                    #}  
                                                    #if(empty($fl_programa_sp)){
                                                    #    $fl_programa_sp=0;
                                                    #}

                                                    #Se les envia email de notificacion al usuario.
                                                    #EnvioEmailCSF($email,$username,$first_name,$last_name_pat,$fl_instituto,171,$ds_pass,'');

                                                    #Se inserta en k_usuario_programa  aqui falta la relacion de programa del arcghivo csf con el fl_prorama de FAME. ellos deben hacerr la relacion si existe.
                                                    
                                                    //$Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa,course_id) ";
                                                    //$Query.="VALUES($fl_user,$fl_programa_sp,0,'0','0','RD','0',0,$fl_maestro_sp_db,'0',CURRENT_TIMESTAMP,'$course_id')";
                                                    //$fl_usu_pro=EjecutaInsert($Query);
                                                    
                                                    
                                                    #Se genera el array.
                                                    $insert_student_array['insert_student'.$contador_insert_std]=array(
                                                            'fl_usuario'=>$fl_usuario_db,
                                                            'email'=>$email,
                                                            'first_name'=>$first_name,
                                                            'last_name_pat'=>$last_name_pat,
                                                            'fl_instituto'=>$fl_instituto_db
                                                        
                                                    );
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    

                                                }




                                            }#end else

                                            
                                        }
                                        

                                    }##end segunda carga

                                    
                                }##end fisrt_name
                                
                                #Nos sirve para no repetir el mismo registro y saber si en BD FAME se actualizo el mismo usuario.
                                $student_id_revisado=$student_id;
                                
                                
                            }#end while estudents

                        }
                        
                    }#end open file.
                    
                    #Revisamos cuanto no fueron porcesados de los existentes en FAME Y determinamos que fuweron eliminados.
                    $Query="SELECT COUNT(*)FROM c_usuario WHERE fg_scf_revisado=0  AND fg_activo='1' AND fl_instituto=$fl_instituto AND fg_csf='1' AND fl_perfil_sp=".PFL_ESTUDIANTE_SELF."  ";
                    $ro=RecuperaValor($Query);
                    $deleted_count=$ro[0];

                    #Desactivamos estos usuarios.
                    //EjecutaQuery("UPDATE c_usuario SET fg_activo='0' WHERE fg_scf_revisado=0 AND fg_scf='1' AND fl_perfil_sp=".PFL_ESTUDIANTE_SELF."  ");

                    #Actualizamos la bitacora de carga.
                    $Query="UPDATE stage_uploads SET end_time=CURRENT_TIMESTAMP,
										 added_count=$contador_insert_std,
										 unchanged_count=$unchanged_count_student,
										 deleted_count=$deleted_count_student ,
										 modified_count=$modified_count_student
										 WHERE id=$fl_upload ";
                    EjecutaQuery($Query);

                    #Recuperamos el proceso de carga del archivo.
                    $Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
									,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
								    TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
								    TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds
									FROM stage_uploads WHERE id=$fl_upload ";
                    $row=RecuperaValor($Query);
                    $user_id=$row['user_id'];
                    $upload_file_path=$row['upload_file_path'];
                    $upload_file_name=$row['upload_file_name'];
                    $upload_type=$row['upload_type'];
                    $upload_date=$row['upload_date'];
                    $status_cd=$row['status_cd'];
                    $start_time=GeneraFormatoFecha($row['start_time']);
                    $start_time_=$row['start_time'];
                    $end_time=$row['end_time'];
                    $proc_status=$row['proc_status'];
                    
                    $upload_time_hrs=$row['hrs'];
                    $upload_time_minutes=$row['minutes'];
                    $upload_time_seconds=$row['seconds'];	

                    $runtime=$upload_time_hrs."h ". $upload_time_minutes."m ".$upload_time_seconds."s ";
                    
                    
                    if($end_time){
                        $finish="<i class='fa fa-check-circle' style='color:#226108;'></i>";
                        
                    }
                    
                    #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
                    $date = date_create($start_time_);
                    $start_time_=date_format($date,'F j, Y, g:i:s a');
                    
                    $date = date_create($end_time);
                    $end_time=date_format($date,'F j, Y, g:i:s a');

                    #cALCULAMOS TOTALES POR RENGLON.
                    $Total_student=$contador_insert_std+$deleted_count_student+$unchanged_count_student+$modified_count_student;

                    
                    #Calculamos el treshold.
                    $threshold_modified_student= ($modified_count_student/$Total_student)*100;
                    $threshold_insert_student= ($contador_insert_std/$Total_student)*100;
                    $threshold_deleted_student= ($deleted_count_student/$Total_student)*100;
                    $threshold_unchanged_student= ($unchanged_count_student/$Total_student)*100;

                    #Default para ejecutar en FAME.
                    $permiso_ejecutar_DB_FAME=1;
                    $error_threshold=0;

                    #El limite es 5%, si rebasa esa cantidad envia emial con los datos de los isntitutos procesados.
                    if($threshold_modified_student>5){
                        $permiso_ejecutar_DB_FAME=0;
                        $error_threshold=1;
                    }
                    if($threshold_insert_student>5){
                        $permiso_ejecutar_DB_FAME=0;
                        $error_threshold=1;
                    }
                    if($threshold_deleted_student>5){
                        $permiso_ejecutar_DB_FAME=0;
                        $error_threshold=1;
                    }
                    if($primeracarga==1){
                        if($aplica_threshold==1){
                            $permiso_ejecutar_DB_FAME=1;
                            $error_threshold=0;
                        }
                    }
                    if($aplica_threshold==1){
                        $permiso_ejecutar_DB_FAME=1;
                        $error_threshold=0;
                        
                    }else{
                        EjecutaQuery("UPDATE stage_uploads SET nothresh_modified='$threshold_modified_student',nothresh_deleted='$threshold_deleted_student',nothresh_added='$threshold_insert_student' WHERE id=$fl_upload ");	
                        
                        
                    }

                    #Actualizamos la bitacora de lo que fue importado.
                    $Query="UPDATE stage_uploads SET proc_status='$error_threshold', modified_count=$modified_count_student, deleted_count=$deleted_count_student,added_count=$contador_insert_std,unchanged_count=$unchanged_count_student WHERE id=$fl_upload ";
                    EjecutaQuery($Query);
                    
                    #Se les envia email de notificacion al usuario.
                    #EnvioEmailCSF($ds_email_instituto,'',$first_name_instituto,$last_name_instituto,$fl_instituto,178,'',$data);                   
                    
                    $datas=array();
                    $datas['datos']= array(
                       'name_reference'=>'Students',
                       
                       'contador_insert'=>$contador_insert_std,
                       'deleted_count'=>$deleted_count_student,
                       'unchanged_count'=>$unchanged_count_student,
                       'upload_count'=>$Total_student,
                       'modified_count'=>$modified_count_student,
                       'threshold_modified'=>$threshold_modified_student,
                       'threshold_insert'=>$threshold_insert_student,
                       'threshold_deleted'=>$threshold_deleted_student,
                       'error_threshold'=>$error_threshold
                    
                    );
                    
                    
                    
                    #fINALIZA EL PROCESO
                    $Query="UPDATE stage_uploads SET status_cd='SEALED',upload_count=$Total_student WHERE id=$fl_upload ";
                    EjecutaQuery($Query);

                    if($permiso_ejecutar_DB_FAME==1){
                        
                        #Desactivamos estos usuarios.
                        EjecutaQuery("UPDATE c_usuario SET fg_scf_revisado=0  WHERE  fl_instituto=$fl_instituto AND fl_perfil_sp=".PFL_ESTUDIANTE_SELF."  ");

                        
                        
                        #Proceso de la carga.
                        if ($file = fopen($ruta_completa_archivo, "r")){
                            
                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Empieza a leer el archivo");
                            
                            # Lee los nombres de los campos
                            // $name_camps = fgetcsv($file, 0, ",", "\"", "\"");
                            // $num_camps = count($name_camps);
                            // $names_camps[$num_camps -1];
                            $tot_reg1 = 0;
                            $contador_insert_std=0;
                            $unchanged_count_student=0;
                            $deleted_count_student=0;
                            $modified_count_student=0;
                            $student_id_revisado="";
                            
                            $insert_student_array=array();
                            $upload_student_array=array();
                            $unchanged_student_array=array();
                            $deleted_student_array=array();
                            $modified_student_array=array();
                            
                            while ($data = fgetcsv ($file, 0, ",")){

                                if ($data[0]=='First Name' || $data[1]=='Last Name' || $data[2]=='School ID' || $data[3]=='Email' || $data[4]=='Course ID' || $data[5]=='Status' || $data[11]=='School Level' || $data[12]=='Teacher ID' || $data[13]=='Student ID' || $data[14]=='Username') {

                                    // Do Noting
                                    
                                } else {

                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Empieza a leer el archivo");
                                    
                                    $first_name = $data[0];
                                    $last_name_pat = $data[1];
                                    $school_id = $data[2];
                                    $email = $data[3];
                                    $course_id = $data[4];
                                    $status = $data[5];
                                    $last_login = $data[6];//fe_ultacc
                                    $progress = $data[7];//ds_progreso
                                    $assesment = $data[8];//ds_assesment
                                    $gpa = $data[9];
                                    $grupo = $data[10];//nb_grupo
                                    $school_level = $data[11];//fl_grado
                                    $teacher_id = $data[12];
                                    $student_id=$data[13];
                                    $username=$data[14];//ds_alias
                                    $master_time_table_id=$data[15];
                                    
                                    
                                    if(($status=='N/A')||(empty($status))){
                                        $status=0;
                                    }else{
                                        $status=1;
                                    }
                                    if(($gpa=='N/A')||(empty($gpa))){
                                        $gpa=0;
                                    }
                                    if(($grupo=='N/A')||(empty($grupo))){
                                        $grupo="";
                                    }

                                    #Recuperamos su teacher si existe.
                                    $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'"  ';
                                    $rop=RecuperaValor($Query);
                                    $fl_maestro=$rop['fl_maestro_sp'];
                                    if(empty($fl_maestro)){
                                        $fl_maestro=0;
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."No se encontro al teacher en FAME".$Query."");
                                        $se_encontro_teacher_fame=0;                                        
                                    }else{
                                        $se_encontro_teacher_fame=1;
                                    }
                                    
                                    if((!empty($first_name))&&(!empty($teacher_id))){

                                        #Identificamos si pertenece algun instituto poreviamente cargado.
                                        $Query="SELECT fl_instituto,fl_usuario_sp FROM c_instituto WHERE school_id='$school_id'  ";
                                        $rok=RecuperaValor($Query);
                                        $fl_instituto_db=$rok['fl_instituto'];
                                        $fl_usuario_sp_instituto=$rok['fl_usuario_sp'];
                                        if(empty($fl_instituto_db))
                                            $fl_instituto_db=0;
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                        
                                        
                                        #Inicia proceso
                                        #$Query="UPDATE stage_uploads SET status_cd='STARTED',upload_file_name_log='$new_archive_procesed' WHERE id=$fl_upload ";
                                        #EjecutaQuery($Query);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."UPDATE stage_uploads SET status_cd='STARTED',upload_file_name_log='$new_archive_procesed' WHERE id=$fl_upload  ");

                                        $fl_perfil_sp=PFL_ESTUDIANTE_SELF;
                                        
                                        #Siempre se genera la bitacora de estudiantes.
                                        #$Queryui ='INSERT INTO st_students_bitacora ( upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username)';
                                        #$Queryui.='VALUES('.$fl_upload.',"","LOADED","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'")';
                                        #$st_id_bitacora=EjecutaInsert($Queryui);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                        /*NOTA, EN LA TABLA st_student el student_id de donde viene??????? archivo excel no esta identifiacdo para hacer la comparacion
                                        2. EN EL ARCHIVO DE excel muestra el campo pupil_number, la cual en BD no existe.*/
                                        
                                        if($primeracarga==1){
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."Es primeraCarga");
                                            
                                            #Se inserta en primera instancia la bitacora de estudiantes.
                                            #$Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username)';
                                            #$Queryui.='VALUES('.$fl_upload.',"","LOADED","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'")';
                                            #$st_id=EjecutaInsert($Queryui);

                                            #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                            ##if($st_id){
                                            ##$contador_insert_std++;
                                            ##}
                                            
                                            
                                            #Se generan registros en FAME.
                                            if($aplica_threshold==1){
                                                
                                                
                                                
                                                #Generamos su pasword temporal.
                                                $ds_pass=substr( md5(microtime()), 1, 8);
                                                
                                                # Genera un identificador de sesion
                                                $cl_sesion_nueva = sha256($username.$first_name.$last_name_pat.$ds_pass);
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera su sesion nueva:".$cl_sesion_nueva);

                                                #Verificamos que ese usuario no exista y se inserta.
                                                $Query='SELECT fl_alumno_sp FROM c_alumno_sp WHERE student_id ="'.$student_id.'"   ';
                                                $rop=RecuperaValor($Query);
                                                $fl_alumno_esxistente=$rop['fl_alumno_sp'];
                                                
                                                #Veriffica que no exusta el usuario por su alias/username
                                                $Query='SELECT COUNT(*) FROM c_usuario WHERE ds_alias="'.$username.'"  ';
                                                $regu=RecuperaValor($Query);
                                                $existe_usario=$regu[0];


                                                if( (empty($fl_alumno_esxistente))&&(empty($existe_usario))) {
                                                    $Query ='INSERT INTO c_usuario (ds_login,cl_sesion,ds_password,ds_alias,ds_nombres,ds_apaterno,ds_email,fg_activo,fe_alta,fl_perfil_sp,fl_instituto,fg_scf,fg_scf_revisado, csf_student_id, csf_teacher_id)';
                                                    $Query.='VALUES("'.$username.'","'.$cl_sesion_nueva.'","'.sha256($ds_pass).'","'.$username.'","'.$first_name.'","'.$last_name_pat.'","'.$email.'","1",CURRENT_TIMESTAMP,15,'.$fl_instituto_db.',1,1, '.$student_id.', '.$teacher_id.')';
                                                    $fl_user=EjecutaInsert($Query);
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Realiza Insert a la tabla de c_usuario-->".$Query);
                                                    
                                                    #Asigmaos al teacher.
                                                    $Query="UPDATE c_usuario SET fl_usu_invita=".$fl_maestro." WHERE fl_usuario=$fl_user ";
                                                    EjecutaQuery($Query);
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Genera Rekacion student-teacher-->".$Query);
                                                    
                                                    #Se genera el dato en c_alumno_sp
                                                    $Query='INSERT INTO c_alumno_sp (fl_alumno_sp,student_id,fl_grado)';
                                                    $Query.='VALUES('.$fl_user.',"'.$student_id.'",'.$school_level.')';
                                                    EjecutaQuery($Query);
                                                    
                                                    $contador_insert_std++;
                                                }
                                                
                                                #Recuperamos el user existente en BD.
                                                $Query='SELECT fl_alumno_sp FROM c_alumno_sp WHERE student_id="'.$student_id.'" ';
                                                $rowu=RecuperaValor($Query);
                                                $fl_user=$rowu['fl_alumno_sp'];

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."inserta al alumno".$Query);
                                                
                                                #Verificamos el istituto
                                                $Query='SELECT fl_instituto FROM c_instituto WHERE school_id="'.$school_id.'"  ';
                                                $rop=RecuperaValor($Query);
                                                $fl_institutions=$rop['fl_instituto'];

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."Recuperamos al instituto ".$Query);

                                                EjecutaQuery("UPDATE c_usuario SET fg_activo='1', fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user  ");
                                                GeneraLog($file_name_txt,"UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user  ");

                                                

                                                #Recuperamos el no. de usuarios que tiene el instituto y le sumaos el nuevo registro
                                                $Query="SELECT no_usuarios FROM c_instituto WHERE fl_instituto=$fl_institutions ";
                                                $row=RecuperaValor($Query);
                                                $no_usuarios_actual = $row[0] +1 ;
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                                
                                                #Actualizamos el registro de numero de usuarios que tiee el isntituto.
                                                $Query="UPDATE c_instituto SET no_usuarios=$no_usuarios_actual WHERE fl_instituto=$fl_institutions ";
                                                EjecutaQuery($Query);

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);


                                                #Recuperamos el fl_usuario_mae4stro atarvaes de su techaer Id que previaente ya fue insertado.
                                                $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'" ';
                                                $row=RecuperaValor($Query);
                                                $fl_maestro_sp_db=$row['fl_maestro_sp'];

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                                
                                                if(empty($fl_maestro_sp_db))
                                                    $fl_maestro_sp_db=0;
                                                if(empty($fl_programa_sp))
                                                    $fl_programa_sp=0;
                                                
                                                
                                                /***NOTA**/
                                                #Se inserta en k_usuario_programa  aqui falta la relacion de programa del arcghivo csf con el fl_prorama de FAME. ellos deben asignar teacher(este query no va.).
                                                #if(($course_id<>$course_id_revisado)&&($student_id_revisado<>$student_id)){
                                                $Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa,course_id,teacher_id) ";
                                                $Query.="VALUES($fl_user,$fl_programa_sp,0,'0','0','RD','0',0,$fl_maestro_sp_db,'0',CURRENT_TIMESTAMP,'$course_id','$teacher_id')";
                                                $fl_usu_pro=EjecutaInsert($Query);




                                                #}
                                                
                                                $Query="SELECT fl_usu_pro FROM k_usuario_programa WHERE course_id='$course_id' AND fl_usuario_sp=".$fl_user." ";
                                                $rupro=RecuperaValor($Query);
                                                $fl_usu_pro=$rupro['fl_usu_pro'];

                                                if(!empty($grupo)){
                                                    #Verifica que no exista ese alumno con ese grupo y si no lo  inserta.
                                                    $Query='SELECT COUNT(*) FROM c_grupo_fame WHERE fl_alumno_sp ='.$fl_user.' AND nb_grupo="'.$grupo.'" AND fl_usu_pro='.$fl_usu_pro.' ';
                                                    $rop=RecuperaValor($Query);
                                                    if(empty($rop[0])){

                                                        $Query='INSERT INTO c_grupo_fame(fl_alumno_sp,nb_grupo,fl_usuario_creacion,fl_instituto,fe_creacion,fe_ulmod,fl_usu_pro) 
                                                                    VALUES('.$fl_user.',"'.$grupo.'",'.$fl_usuario_rector.','.$fl_instituto_db.',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'.$fl_usu_pro.') ';
                                                        $fl_grup=EjecutaInsert($Query);
                                                    }

                                                }


                                                
                                                #Se les envia email de notificacion al usuario.  MJD 10-DIC-2019  ALOS ALUMNOS NO SE LES ENVIA EMAIL(en dasboard del teacher enviara invitacion manualmente.)
                                                //EnvioEmailCSF($email,$username,$first_name,$last_name_pat,$fl_instituto,171,$ds_pass,'');
                                            }
                                            
                                            
                                            
                                        }else{
                                            #Segunda carga                      
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."Pasa ser comparado ya existen datos en Fame.");

                                            #Verifica si existe ese registro en FAME.
                                            $Query='SELECT ds_login,ds_alias,ds_nombres,ds_apaterno,ds_email,b.fg_scf,b.fl_usuario,c.course_id  
                                                                FROM c_alumno_sp a 
                                                                JOIN c_usuario b ON b.fl_usuario=a.fl_alumno_sp 
                                                                JOIN k_usuario_programa c ON c.fl_usuario_sp=b.fl_usuario AND c.course_id="'.$course_id.'"
                                                                JOIN c_instituto d ON d.fl_instituto= b.fl_instituto 
                                                                WHERE d.school_id="'.$school_id.'" AND  b.ds_login="'.$username.'" AND ds_nombres="'.$first_name.'" AND ds_apaterno="'.$last_name_pat.'" AND ds_email="'.$email.'"   AND fl_perfil_sp=15
                                                                ';
                                            $row=RecuperaValor($Query);
                                            $ds_login_db=$row['ds_login'];
                                            $username_db=$row['ds_login'];
                                            $course_id_db=$row['course_id'];
                                            $fl_usuario_student=$row['fl_usuario'];
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."Pasa verificar si encuentra el registro.");
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                            
                                            //falta obtener instituto. instituto(aqui falta)

                                            #Se encontro registro igualito.
                                            if(!empty($row[0])){
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se encontro registro y solo pasamos generar bitacora de carga");
                                                #Se inserta en primera instancia la bitacora de estudiantes.
                                                #$Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username)';
                                                #$Queryui.='VALUES('.$fl_upload.',"","NO_CHANGE","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'")';
                                                #$st_id=EjecutaInsert($Queryui);

                                                #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                                if($student_id_revisado<>$student_id){    
                                                    $unchanged_count_student++;
                                                }
                                                $fl_user=$fl_usuario_student;
                                                EjecutaQuery('UPDATE c_usuario SET fg_scf_revisado=1,fg_activo="1" WHERE fl_usuario='.$fl_usuario_student.' AND fl_perfil_sp='.PFL_ESTUDIANTE_SELF.'  ');

                                                
                                            }else{
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."No encontro registro y pasa a verificar si existe student_id=".$username);
                                                
                                                #Verifica si existe ese registro en FAME.
                                                $Query='SELECT ds_login,ds_alias,ds_nombres,ds_apaterno,ds_email,b.fg_scf,b.fl_usuario,c.course_id  
                                                                FROM c_alumno_sp a 
                                                                JOIN c_usuario b ON b.fl_usuario=a.fl_alumno_sp  
                                                                JOIN k_usuario_programa c ON c.fl_usuario_sp=b.fl_usuario AND c.course_id="'.$course_id.'"
                                                                WHERE  b.ds_login="'.$username.'"  AND fl_perfil_sp="'.PFL_ESTUDIANTE_SELF.'"  ';
                                                $row=RecuperaValor($Query);
                                                $ds_login_db=$row['ds_login'];
                                                $username_db=$row['ds_login'];
                                                $course_id_db=$row['course_id'];
                                                $fl_usuario_db=$row['fl_usuario'];

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                                #Si se encontro el username y el curso.
                                                if(($username_db==$username)&&($course_id_db==$course_id)){


                                                    
                                                    #Se inserta en primera instancia la bitacora de estudiantes.
                                                    /*$Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username)';
                                                    $Queryui.='VALUES('.$fl_upload.',"","MODIFY","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'")';
                                                    $st_id=EjecutaInsert($Queryui);
                                                     */
                                                    #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera carga -->".$Queryui);
                                                    #if($st_id){
                                                    $modified_count_student++;
                                                    #}
                                                    
                                                    EjecutaQuery('UPDATE c_usuario SET  fg_scf_revisado=1 WHERE fl_usuario='.$fl_usuario_db.' ');
                                                    
                                                    $Query="SELECT fl_usu_pro FROM k_usuario_programa WHERE course_id='$course_id' AND fl_usuario_sp=".$fl_user." ";
                                                    $rupro=RecuperaValor($Query);
                                                    $fl_usu_pro=$rupro['fl_usu_pro'];

                                                    if(!empty($grupo)){
                                                        #Verifica que no exista ese alumno con ese grupo y si no lo  inserta.
                                                        $Query='SELECT COUNT(*) FROM c_grupo_fame WHERE fl_alumno_sp ='.$fl_usuario_db.' AND nb_grupo="'.$grupo.'" AND fl_usu_pro='.$fl_usu_pro.'  ';
                                                        $rop=RecuperaValor($Query);
                                                        if(empty($rop[0])){

                                                            $Query='INSERT INTO c_grupo_fame(fl_alumno_sp,nb_grupo,fl_usuario_creacion,fl_instituto,fe_creacion,de_ulmod,fl_usu_pro) 
                                                                    VALUES('.$fl_user.',"'.$grupo.'",'.$fl_usuario_rector.','.$fl_instituto_db.',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'.$fl_usu_pro.') ';
                                                            $fl_grup=EjecutaInsert($Query);
                                                        }
                                                    }
                                                    
                                                    EjecutaQuery('UPDATE c_alumno_sp SET nb_grupo="'.$grupo.'" WHERE fl_alumno_sp='.$fl_usuario_db.' ');
                                                    
                                                    #Actualizamos datos generales de ese registro.
                                                    $Query='UPDATE c_usuario SET ds_nombres="'.$first_name.'" ,ds_apaterno="'.$last_name.'",ds_email="'.$email.'",fl_instituto='.$fl_instituto_db.',fg_scf_revisado=1,fg_activo="1"  WHERE fl_usuario='.$fl_usuario_db.' ';
                                                    EjecutaQuery($Query);
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Si se encontro y actualiamios datos adicionales-->".$Query);
                                                    

                                                    if(empty($fl_maestro))
                                                        $fl_user_invitador=$fl_usuario_sp_instituto;

                                                    #Asigmaos al usuario invitador
                                                    $Query="UPDATE c_usuario SET fg_activo='1', fl_usu_invita=".$fl_user_invitador." WHERE fl_usuario=$fl_usuario_db ";
                                                    EjecutaQuery($Query);

                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se actualiza relacion teacher student-->".$Query);
                                                    
                                                }else{
                                                    
                                                    #Solo cambio de curso, y si se encontro el username.
                                                    
                                                    if($username_db==$username){
                                                        
                                                        #Se inserta en primera instancia la bitacora de estudiantes.
                                                        #$Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username)';
                                                        #$Queryui.='VALUES('.$fl_upload.',"","MODIFY","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'")';
                                                        #$st_id=EjecutaInsert($Queryui);

                                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera carga -->".$Queryui);
                                                        #if($st_id){
                                                        $modified_count_student++;
                                                        #}
                                                        
                                                        EjecutaQuery('UPDATE c_usuario SET fg_activo="1",  fg_scf_revisado=1 WHERE fl_usuario='.$fl_usuario_db.' ');
                                                        
                                                        EjecutaQuery('UPDATE c_alumno_sp SET nb_grupo="'.$grupo.'" WHERE fl_alumno_sp='.$fl_usuario_db.' ');
                                                        
                                                        #Actualizamos datos generales de ese registro.
                                                        $Query='UPDATE c_usuario SET ds_nombres="'.$first_name.'",fg_activo="1" ,ds_apaterno="'.$last_name.'",ds_email="'.$email.'",fl_instituto='.$fl_instituto_db.'  WHERE fl_usuario='.$fl_usuario_db.' ';
                                                        EjecutaQuery($Query);
                                                        
                                                        GeneraLog($file_name_txt,"Actualiza datos generales--> ".$Query);
                                                        
                                                        #Asigmaos al teacher.
                                                        $Query="UPDATE c_usuario SET fl_usu_invita=".$fl_maestro." WHERE fl_usuario=$fl_usuario_db ";
                                                        EjecutaQuery($Query);

                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se actualiza relacion teacher student-->".$Query);
                                                        
                                                        
                                                        if($course_id<>$course_id_db){
                                                            
                                                            #Recuperamos el fl_usuario_mae4stro atarvaes de su techaer Id que previaente ya fue insertado.
                                                            $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'" ';
                                                            $row=RecuperaValor($Query);
                                                            $fl_maestro_sp_db=$row['fl_maestro_sp'];
                                                            
                                                            
                                                            #se cambio de curso. se genera otro curso.
                                                            $Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa,course_id,teacher_id) ";
                                                            $Query.="VALUES($fl_usuario_db,0,0,'0','0','RD','0',0,$fl_maestro_sp_db,'0',CURRENT_TIMESTAMP,'$course_id','$teacher_id')";
                                                            $fl_usu_pro=EjecutaInsert($Query);  
                                                            
                                                            
                                                            
                                                        }
                                                        

                                                        $Query="SELECT fl_usu_pro FROM k_usuario_programa WHERE course_id='$course_id' AND fl_usuario_sp=".$fl_user." ";
                                                        $rupro=RecuperaValor($Query);
                                                        $fl_usu_pro=$rupro['fl_usu_pro'];


                                                        if(!empty($grupo)){
                                                            #Verifica que no exista ese alumno con ese grupo y si no lo  inserta.
                                                            $Query='SELECT COUNT(*) FROM c_grupo_fame WHERE fl_alumno_sp ='.$fl_usuario_db.' AND nb_grupo="'.$grupo.'"  ';
                                                            $rop=RecuperaValor($Query);
                                                            if(empty($rop[0])){

                                                                $Query='INSERT INTO c_grupo_fame(fl_alumno_sp,nb_grupo,fl_usuario_creacion,fl_instituto,fe_creacion,de_ulmod) 
                                                                        VALUES('.$fl_user.',"'.$grupo.'",'.$fl_usuario_rector.','.$fl_instituto_db.',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ';
                                                                $fl_grup=EjecutaInsert($Query);
                                                            }
                                                        }
                                                        #Se genera el array.
                                                        $modified_student_array['modified_student'.$modified_count_student]=array(
                                                                'fl_usuario'=>$fl_usuario_db,
                                                                'email'=>$email,
                                                                'first_name'=>$first_name,
                                                                'last_name_pat'=>$last_name_pat,
                                                                'fl_instituto'=>$fl_instituto_db
                                                            
                                                        );
                                                        
                                                        
                                                        
                                                        
                                                        
                                                    }else{
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        #Se genera nuevo registro.
                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera nuevo registro -->");
                                                        #El registro no fue localizado pasa ser nuevo dato.
                                                        
                                                        #Se inserta en primera instancia la bitacora de estudiantes.
                                                        #$Queryui ='INSERT INTO st_students (upload_id,student_id,operation_code,email,first_name,last_name_pat,last_name_mat,teacher_id,school_id,course_id,status,gpa,groups,school_level,fl_perfil_sp,username)';
                                                        #$Queryui.='VALUES('.$fl_upload.',"","ADD","'.$email.'","'.$first_name.'","'.$last_name_pat.'","","'.$teacher_id.'","'.$school_id.'","'.$course_id.'","'.$status.'",'.$gpa.',"'.$grupo.'",'.$school_level.',"15","'.$username.'")';
                                                        #$st_id=EjecutaInsert($Queryui);

                                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera carga -->".$Queryui);

                                                        #if($st_id){
                                                        $contador_insert_std++;
                                                        #}

                                                        #Se generan registros en FAME.
                                                        #Generamos su pasword temporal.
                                                        $ds_pass=substr( md5(microtime()), 1, 8);
                                                        
                                                        # Genera un identificador de sesion
                                                        $cl_sesion_nueva = sha256($username.$first_name.$last_name_pat.$ds_pass);
                                                        
                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera sesion>".$cl_sesion_nueva);
                                                        
                                                        #Verificamos que ese usuario no exista y se inserta.
                                                        $Query='SELECT fl_alumno_sp FROM c_alumno_sp WHERE student_id ="'.$student_id.'"   ';
                                                        $rop=RecuperaValor($Query);
                                                        $fl_alumno_esxistente=$rop['fl_alumno_sp'];

                                                        #Veriffica que no exusta el usuario por su alias/username
                                                        $Query='SELECT COUNT(*) FROM c_usuario WHERE ds_alias="'.$username.'"  ';
                                                        $regu=RecuperaValor($Query);
                                                        $existe_usario=$regu[0];

                                                        
                                                        if((empty($fl_alumno_esxistente))&&(empty($existe_usario))){
                                                            
                                                            $Query ='INSERT INTO c_usuario (ds_login,cl_sesion,ds_password,ds_alias,ds_nombres,ds_apaterno,ds_email,fg_activo,fe_alta,fl_perfil_sp,fl_instituto,fg_scf,fg_scf_revisado, csf_student_id, csf_teacher_id)';
                                                            $Query.='VALUES("'.$username.'","'.$cl_sesion_nueva.'","'.sha256($ds_pass).'","'.$username.'","'.$first_name.'","'.$last_name_pat.'","'.$email.'","1",CURRENT_TIMESTAMP,15,'.$fl_instituto_db.',1,1, '.$student_id.', '.$teacher_id.')';
                                                            $fl_user=EjecutaInsert($Query);
                                                            
                                                            #Asigmaos al teacher.
                                                            $Query="UPDATE c_usuario SET fl_usu_invita=".$fl_maestro." WHERE fl_usuario=$fl_user ";
                                                            EjecutaQuery($Query);
                                                            
                                                            //EjecutaQuery('UPDATE c_alumno_sp SET nb_grupo="'.$grupo.'" WHERE fl_alumno_sp='.$fl_user.' ');
                                                            
                                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se actualiza relacion teacher-student-->".$Query);
                                                            
                                                            #Se genera el dato en c_alumno_sp
                                                            $Query='INSERT INTO c_alumno_sp (fl_alumno_sp,student_id,fl_grado)';
                                                            $Query.='VALUES('.$fl_user.',"'.$student_id.'",'.$school_level.')';
                                                            EjecutaQuery($Query);

                                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Se genera el dato en c_alumno_sp".$Query);
                                                            
                                                            $contador_insert_std++;
                                                            
                                                        }

                                                        #Recuperamos el user existente en BD.
                                                        $Query='SELECT fl_alumno_sp FROM c_alumno_sp WHERE student_id="'.$student_id.'" ';
                                                        $rowu=RecuperaValor($Query);
                                                        $fl_user=$rowu['fl_alumno_sp'];
                                                        
                                                        
                                                        #Verificamos el isntitutio
                                                        $Query='SELECT fl_instituto FROM c_instituto WHERE school_id="'.$school_id.'"  ';
                                                        $rop=RecuperaValor($Query);
                                                        $fl_institutions=$rop['fl_instituto'];

                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Verificamos el isntitutio".$Query);
                                                        
                                                        EjecutaQuery("UPDATE c_usuario SET fl_instituto=$fl_institutions,fg_activo='1' WHERE  fl_usuario=$fl_user  ");
                                                        GeneraLog($file_name_txt,"UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user ");

                                                        #Falta asociarlo al programa
                                                        #Recuperamos el fl_usuario_mae4stro atarvaes de su techaer Id que previaente ya fue insertado.
                                                        $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'" ';
                                                        $row=RecuperaValor($Query);
                                                        $fl_maestro_sp_db=$row['fl_maestro_sp'];

                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Recuperamos el fl_usuario_mae4stro atarvaes de su techaer Id que previaente ya fue insertado.".$Query);

                                                        if(empty($fl_maestro_sp_db)){
                                                            $fl_maestro_sp_db=0;
                                                        }   
                                                        if(empty($fl_programa_sp)){
                                                            $fl_programa_sp=0;
                                                        }

                                                        #Se les envia email de notificacion al usuario.  MJD 10-12-2019  (el alumno se le enviara invitacion manualmente desde el panel del teacher).
                                                        //EnvioEmailCSF($email,$username,$first_name,$last_name_pat,$fl_instituto,171,$ds_pass,'');

                                                        #Se inserta en k_usuario_programa  aqui falta la relacion de programa del arcghivo csf con el fl_prorama de FAME. ellos deben hacerr la relacion si existe.
                                                        #if($course_id<>$course_id_revisado){
                                                        $Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa,course_id) ";
                                                        $Query.="VALUES($fl_user,$fl_programa_sp,0,'0','0','RD','0',0,$fl_maestro_sp_db,'0',CURRENT_TIMESTAMP,'$course_id')";
                                                        $fl_usu_pro=EjecutaInsert($Query);
                                                        #}

                                                        $Query="SELECT fl_usu_pro FROM k_usuario_programa WHERE course_id='$course_id' AND fl_usuario_sp=".$fl_user." ";
                                                        $rupro=RecuperaValor($Query);
                                                        $fl_usu_pro=$rupro['fl_usu_pro'];



                                                        if(!empty($grupo)){
                                                            #Verifica que no exista ese alumno con ese grupo y si no lo  inserta.
                                                            $Query='SELECT COUNT(*) FROM c_grupo_fame WHERE fl_alumno_sp ='.$fl_user.' AND nb_grupo="'.$grupo.'" AND fl_usu_pro='.$fl_usu_pro.'  ';
                                                            $rop=RecuperaValor($Query);
                                                            if(empty($rop[0])){

                                                                $Query='INSERT INTO c_grupo_fame(fl_alumno_sp,nb_grupo,fl_usuario_creacion,fl_instituto,fe_creacion,de_ulmod,fl_usu_pro) 
                                                                        VALUES('.$fl_user.',"'.$grupo.'",'.$fl_usuario_rector.','.$fl_instituto_db.',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'.$fl_usu_pro.') ';
                                                                $fl_grup=EjecutaInsert($Query);
                                                            }
                                                        }



                                                        
                                                    }




                                                }#end else

                                                
                                            }
                                            

                                        }##end segunda carga

                                        $student_id_revisado=$student_id;   
                                        $teacher_id_revisado=$teacher_id;
                                        $course_id_revisado=$course_id;

                                        
                                        #Verifica que no exista en la tabla de institutos_alumnos(por si existe un alumno en mas de dos institutciones.)
                                        $Query="SELECT fl_usuario_sp FROM k_instituto_alumno WHERE fl_usuario_sp=$fl_user AND fl_instituto=$fl_instituto_db ";
                                        $row=RecuperaValor($Query);
                                        if(empty($row['fl_usuario_sp'])){
                                            
                                            $Query ="INSERT INTO k_instituto_alumno (fl_usuario_sp,fl_instituto,fg_aceptado,fe_creacion,fe_ultmod,fl_usuario_invitando) ";
                                            $Query.="VALUES($fl_user,$fl_instituto_db,'1',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usuario_sp_instituto) ";
                                            EjecutaInsert($Query);
                                            
                                        }

                                        #si existen mas de dos institutos del mismo teacher quiere decir que si existen un teacher en varios institutos y se identifica al user con un flag->fg_select_instituto 
                                        $Query="SELECT COUNT(*) FROM k_instituto_alumno WHERE fl_alumno=".$fl_user." ";
                                        $row=RecuperaValor($Query);
                                        $result=!empty($row[0])?$row[0]:NULL;
                                        if($result>=2){
                                            EjecutaQuery("UPDATE c_usuario SET fg_select_instituto='1' WHERE fl_usuario=$fl_user  ");

                                        }

                                    }##end fisrt_name

                                }
                                
                            }#end while students
                            
                        }#end open file.
                        
                        #Revisamos cuanto no fueron porcesados de los existentes en FAME Y determinamos que fuweron eliminados.
                        $Query="SELECT COUNT(*) FROM c_usuario WHERE fg_scf_revisado=0 AND  fl_instituto=$fl_instituto_db  AND fg_activo='1' AND fl_perfil_sp=".PFL_ESTUDIANTE_SELF."  ";
                        $ro=RecuperaValor($Query);
                        $deleted_count=$ro[0];

                        #Desactivamos estos institutos.
                        //EjecutaQuery("UPDATE c_usuario SET fg_activo='0' WHERE fg_scf_revisado=0 AND fg_scf='1' AND fl_perfil_sp=".PFL_ESTUDIANTE_SELF."  ");

                        #Actualizamos la bitacora de carga.
                        $Query="UPDATE stage_uploads SET end_time=CURRENT_TIMESTAMP,
												 added_count=$contador_insert_std,
												 unchanged_count=$unchanged_count_student,
												 deleted_count=$deleted_count_student ,
												 modified_count=$modified_count_student
												 WHERE id=$fl_upload ";
                        EjecutaQuery($Query);
                        
                        #Recuperamos el proceso de carga del archivo.
                        $Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
											,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
											TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
											TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds
											FROM stage_uploads WHERE id=$fl_upload ";
                        $row=RecuperaValor($Query);
                        $user_id=$row['user_id'];
                        $upload_file_path=$row['upload_file_path'];
                        $upload_file_name=$row['upload_file_name'];
                        $upload_type=$row['upload_type'];
                        $upload_date=$row['upload_date'];
                        $status_cd=$row['status_cd'];
                        $start_time=GeneraFormatoFecha($row['start_time']);
                        $start_time_=$row['start_time'];
                        $end_time=$row['end_time'];
                        $proc_status=$row['proc_status'];
                        
                        $upload_time_hrs=$row['hrs'];
                        $upload_time_minutes=$row['minutes'];
                        $upload_time_seconds=$row['seconds'];	

                        $runtime=$upload_time_hrs."h ". $upload_time_minutes."m ".$upload_time_seconds."s ";
                        
                        if($end_time){
                            $finish="<i class='fa fa-check-circle' style='color:#226108;'></i>";
                            
                        }
                        
                        #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
                        $date = date_create($start_time_);
                        $start_time_=date_format($date,'F j, Y, g:i:s a');
                        
                        $date = date_create($end_time);
                        $end_time=date_format($date,'F j, Y, g:i:s a');

                        #cALCULAMOS TOTALES POR RENGLON.
                        $Total_student=$contador_insert_std+$deleted_count_student+$unchanged_count_student+$modified_count_student;

                        
                    }#END proceso la carga.
                    
                    
                    
                    
                    
                    #Se les envia email de notificacion al usuario. ya incluye threshold.
                    EnvioEmailCSF($ds_email_instituto,'',$first_name_instituto,$last_name_instituto,$fl_instituto,178,'',$datas);
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Rnvia email y Termina lectura del archivo".date('F j, Y, g:i a'));
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."====================================Finaliza proceso ".date('F j, Y, g:i a')."=================================================");
                    
                    

                }#end upload students.
                
                
                

                /***********************************Teachers*******************************************************************************************************************************************************************************************/

                #------> se genera el registro de teachers.

                if(($upload_type=='teacher')||($upload_type=='Teacher')||($upload_type=='TEACHER')){

                    #Generamos el log.
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Upload type:".$upload_type);
                    
                    $Query="SELECT COUNT(*) FROM st_teachers where 1=1 ";
                    $row=RecuperaValor($Query);
                    $conta_reg_ini=$row[0];

                    #Generamos el log.
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Query-->".$Query);


                    if($conta_reg_ini==0){
                        $primeracarga=1;
                        #Generamos el log.
                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Es primera Carga");
                    }else{
                        #Generamos el log.
                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."No es la primera Carga");
                        $primeracarga=0;
                    }
                    
                    
                    #Limpiamos la revisio de la carga esto para hacer match y sabar cuantos registros elimnados hay.
                    EjecutaQuery("UPDATE c_usuario SET fg_scf_revisado=0 WHERE fl_instituto=$fl_instituto AND fl_perfil_sp=".PFL_MAESTRO_SELF." ");
                    
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Limpiamos la revisio de la carga esto para hacer match y sabar cuantos registros elimnados hay..UPDATE c_usuario SET fg_scf_revisado=0 WHERE  fl_perfil_sp=".PFL_MAESTRO_SELF."  ");
                    

                    $Query="UPDATE stage_uploads SET status_cd='NEW' WHERE id=$fl_upload ";
                    EjecutaQuery($Query);
                    GeneraLog($file_name_txt,$Query);
                    
                    #Proceso de la carga.
                    if ($file = fopen($ruta_completa_archivo, "r")){
                        
                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Empieza a leer el archivo");
                        
                        # Lee los nombres de los campos
                        // $name_camps = fgetcsv($file, 0, ",", "\"", "\"");
                        // $num_camps = count($name_camps);
                        // $names_camps[$num_camps -1];
                        $tot_reg1 = 0;
                        $contador_insert_teacher=0;
                        $unchanged_count_teacher=0;
                        $deleted_count_teacher=0;
                        $modified_count_teacher=0;
                        
                        $insert_teacher_array=array();
                        $upload_teacher_array=array();
                        $unchanged_teacher_array=array();
                        $deleted_teacher_array=array();
                        $modified_teacher_array=array();
                        
                        while ($data = fgetcsv ($file, 0, ",")){

                            if ($data[0]=='Teacher ID' || $data[1]=='Username' || $data[3]=='First Name' || $data[4]=='Last Name' || $data[5]=='School' || $data[6]=='School ID' || $data[6]=='Email') {

                                // Do Noting
                                
                            } else {

                                $teacher_id = $data[0];
                                $username = $data[1];
                                $departament = $data[2];
                                $first_name = $data[3];
                                $last_name = $data[4];
                                $school = $data[5];
                                $school_number = $data[6];//school id
                                $email = $data[7];
                                $course = $data[8];
                                $status = $data[9];
                                $las_login = $data[10];
                                $progress = $data[11];
                                
                                if(($departament=='N/A')||(empty($departament))){
                                    $departament=0;
                                }else{
                                    $departament=1;
                                }
                                if(($school=='N/A')||(empty($school))){
                                    $school=0;
                                }
                                if(($school_number=='N/A')||(empty($school_number))){
                                    $school_number=0;
                                }
                                if(($course=='N/A')||(empty($course))){
                                    $course=0;
                                }
                                if(($status=='N/A')||(empty($status))){
                                    $status=0;
                                }
                                if(($las_login=='N/A')||(empty($las_login))){
                                    $las_login=0;
                                }
                                if(($progress=='N/A')||(empty($progress))){
                                    $progress=0;
                                }
                                
                                if(!empty($teacher_id)){

                                    #genera status started.
                                    $Query="UPDATE stage_uploads SET status_cd='STARTED',upload_file_name_log='$new_archive_procesed' WHERE id=$fl_upload ";
                                    EjecutaQuery($Query);
                                    
                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                    $fl_perfil_sp=PFL_MAESTRO_SELF;
                                    
                                    #Siempre se genera la bitacora de teachers.
                                    $Queryui ='INSERT INTO st_teachers_bitacora ( upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp)';
                                    $Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","LOADED","","15")';
                                    $st_id_bitacora=EjecutaInsert($Queryui);
                                    
                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                    #Verificamos el isntitutio
                                    $Query='SELECT fl_instituto,fl_usuario_sp FROM c_instituto WHERE school_id="'.$school_number.'"  ';
                                    $rop=RecuperaValor($Query);
                                    $fl_institutions=!empty($rop['fl_instituto'])?$rop['fl_instituto']: null;
                                    $fl_instituto=!empty($rop['fl_instituto'])?$rop['fl_instituto']: null;$fl_usu_invita=!empty($rop['fl_usuario_sp'])?$rop['fl_usuario_sp']:null;
                                    /*
                                     *Nota: Si el teacher no trae id del instituto y no se encuentra en fame este pasa a disponibilidad del rector.
                                     */
                                    if(empty($fl_instituto)){##se obteine del usuario generado del rector admin
                                        $fl_instituto=$fl_instituto_rector;$fl_usu_invita=2395;
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."No se encontro el instituto en FAME con clave:$school_number , y pasa al rector. con fl_instituto".$fl_instituto);
                                    }

                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                    
                                    if($primeracarga==1){
                                        
                                        #Se inserta en primera instancia la bitacora de teachers.
                                        $Queryui ='INSERT INTO st_teachers (upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp,fl_instituto)';
                                        $Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","LOADED","","14",'.$fl_instituto.')';
                                        $st_id=EjecutaInsert($Queryui);

                                        GeneraLog($file_name_txt,$Queryui);
                                        
                                        $Query='SELECT teacher_id from st_teachers WHERE teacher_id="'.$teacher_id.'" and upload_id='.$fl_upload.'   ';
                                        $row=RecuperaValor($Query);
                                        $existe_teacher_id=$row['teacher_id'];

                                        #Veriffica que no exusta el usuario por su alias/username
                                        $Query='SELECT COUNT(*) FROM c_usuario WHERE ds_alias="'.$username.'"  ';
                                        $regu=RecuperaValor($Query);
                                        $existe_usario=$regu[0];


                                        if(empty($existe_usario)){
                                            $contador_insert_teacher++;
                                        }

                                        

                                        #Se generan registros en FAME.
                                        #Generamos su pasword temporal.
                                        #$ds_pass=substr( md5(microtime()), 1, 8);
                                        
                                        # Genera un identificador de sesion
                                        #$cl_sesion_nueva = sha256($username.$first_name.$last_name.$ds_pass);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera su sesion nueva:".$cl_sesion_nueva);
                                        
                                        #$Query ='INSERT INTO c_usuario (ds_login,cl_sesion,ds_password ,ds_alias,ds_nombres,ds_apaterno,ds_email,fg_activo,fe_alta,fl_perfil_sp,fl_instituto,fg_scf,fg_scf_revisado)';
                                        #$Query.='VALUES("'.$username.'","'.$cl_sesion_nueva.'","'.sha256($ds_pass).'","'.$username.'","'.$first_name.'","'.$last_name.'","'.$email.'","1",CURRENT_TIMESTAMP,14,'.$fl_instituto.',1,1)';
                                        #$fl_user_teacher=EjecutaInsert($Query);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Realiza Insert a la tabla de c_usuario-->".$Query);
                                        #$Query="UPDATE c_usuario SET fl_usu_invita=$fl_usu_invita WHERE fl_usuario=$fl_user_teacher ";EjecutaQuery($Query);
                                        #Se genera el registro en la tabla de fame_teachers.
                                        #$Query='INSERT INTO c_maestro_sp (fl_maestro_sp,teacher_id)';
                                        #$Query.='VALUES('.$fl_user_teacher.','.$teacher_id.')';
                                        #EjecutaQuery($Query);
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Realiza Insert a la tabla de c_maestro_sp-->".$Query);

                                        #Recuperamos el no. de usuarios que tiene el instituto y le sumaos el nuevo registro
                                        #$Query="SELECT no_usuarios FROM c_instituto WHERE fl_instituto=$fl_instituto ";
                                        #$row=RecuperaValor($Query);
                                        #$no_usuarios_actual = $row[0] +1 ;
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                        
                                        #Actualizamos el registro de numero de usuarios que tiee el isntituto.
                                        #$Query="UPDATE c_instituto SET no_usuarios=$no_usuarios_actual WHERE fl_instituto=$fl_instituto ";
                                        #EjecutaQuery($Query);

                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);


                                        
                                        
                                        #EjecutaQuery("UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user_teacher  ");
                                        #GeneraLog($file_name_txt,"UPDATE c_usuario SET fl_instituto=$fl_instituto WHERE  fl_usuario=$fl_user_teacher ");
                                        
                                        #Se les envia email de notificacion al usuario.
                                        #EnvioEmailCSF($email,$username,$first_name,$last_name,$fl_instituto,171,$ds_pass,'');
                                        
                                        #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se envia email ".$email);
                                        #}  

                                    }else{
                                        
                                        #Ya es la segunda carga.

                                        #Verifica si eexiste ese registro  en FAME con el username y el id_teacher. | ds_login para FAME
                                        $Query ='SELECT ds_login,ds_alias,ds_nombres,ds_apaterno,ds_email,a.teacher_id,fg_scf,b.fl_usuario  
                                                     FROM c_maestro_sp a 
                                                     JOIN c_usuario b ON b.fl_usuario=a.fl_maestro_sp  
                                                     WHERE a.teacher_id="'.$teacher_id.'" AND
                                                     /*AND b.fl_instituto='.$fl_instituto.' */
                                                     EXISTS(
                                                        SELECT 1 FROM k_instituto_teacher i WHERE i.fl_maestro_sp=b.fl_usuario)   
                                                     AND b.ds_login="'.$username.'" AND ds_nombres="'.$first_name.'" AND ds_apaterno="'.$last_name.'" AND ds_email="'.$email.'"  ';
                                        $row=RecuperaValor($Query);

                                        $ds_login_=$row['ds_login'];
                                        $username_db=$row['ds_login'];
                                        $teacher_id_db=$row['teacher_id'];
                                        $fl_usuario_maestro=$row['fl_usuario'];

                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                        #Se encontro registro igualito.
                                        if(!empty($row[0])){

                                            #El dato no sufre ningun cambio pasa a operation_code 'NO_CHANGE' y se inserta la bitacora.
                                            $Queryui ='INSERT INTO st_teachers (upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp,fl_instituto)';
                                            $Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","NO_CHANGE","","14",'.$fl_instituto.')';
                                            $st_id=EjecutaInsert($Queryui);
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);
                                            if($teacher_id_revisado<>$teacher_id){
                                                $unchanged_count_teacher++; 
                                            }

                                            $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'" ';
                                            $row=RecuperaValor($Query);
                                            $fl_maestro_sp=$row['fl_maestro_sp'];
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query); 

                                            EjecutaQuery('UPDATE c_usuario SET fg_scf_revisado=1 WHERE fl_usuario='.$fl_maestro_sp.' AND fl_perfil_sp='.PFL_MAESTRO_SELF.' ');
                                            
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE c_usuario SET fg_scf_revisado=1 WHERE fl_usuario='.$fl_maestro_sp.' AND fl_perfil_sp='.PFL_MAESTRO_SELF.'  ');

                                            #Se genera el array.
                                            $unchanged_teacher_array['unchanged_teacher'.$unchanged_count_teacher]=array(
                                                    'fl_usuario'=>$fl_maestro_sp,
                                                    'email'=>$email,
                                                    'first_name'=>$first_name,
                                                    'last_name_pat'=>$last_name,
                                                    'teacher_id'=>$teacher_id,
                                                    'username'=>$username
                                                
                                            );
                                            



                                        }else{
                                            
                                            #Si se encontro el username|ds login y el teacher id,eso quiere decir que solo sufrio modificaciones en algun otro dato, entonces actualizamos el registro.
                                            $Query ='SELECT ds_login,ds_alias,ds_nombres,ds_apaterno,ds_email,a.teacher_id,fg_scf,b.fl_usuario,b.fl_instituto  
                                                     FROM c_maestro_sp a 
                                                     JOIN c_usuario b ON b.fl_usuario=a.fl_maestro_sp  
                                                     WHERE a.teacher_id="'.$teacher_id.'" 
                                                     AND b.ds_login="'.$username.'"   ';
                                            $row=RecuperaValor($Query);      
                                            $username_db=$row['ds_login'];
                                            $teacher_id_db=$row['teacher_id'];
                                            $fl_usuario_db=$row['fl_usuario'];
                                            $fl_instituto_db=$row['fl_instituto'];

                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                            if(($username_db==$username)&&($teacher_id_db==$teacher_id)){
                                                
                                                $Queryui ='INSERT INTO st_teachers (upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp,fl_instituto)';
                                                $Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","MODIFY","","14",'.$fl_instituto.')';
                                                $st_id=EjecutaInsert($Queryui);
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);
                                                
                                                if($st_id){
                                                    
                                                    $modified_count_teacher++;
                                                }
                                                
                                                
                                                #Actualizamos datos generales de ese registro.
                                                #$Query='UPDATE c_usuario SET fg_scf_revisado=1,fl_instituto='.$fl_instituto.', ds_nombres="'.$first_name.'" ,ds_apaterno="'.$last_name.'",ds_email="'.$email.'" WHERE fl_usuario='.$fl_usuario_db.' ';
                                                $Query='UPDATE c_usuario SET fg_scf_revisado=1 WHERE fl_usuario='.$fl_usuario_db.' ';                          
                                                EjecutaQuery($Query);
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                                
                                                
                                                #Se genera el array.
                                                $modified_teacher_array['modified_teacher'.$modified_count_teacher]=array(
                                                        'fl_usuario'=>$fl_usuario_db,
                                                        'email'=>$email,
                                                        'first_name'=>$first_name,
                                                        'last_name_pat'=>$last_name,
                                                        'teacher_id'=>$teacher_id,
                                                        'username'=>$username,
                                                        'fl_instituto'=>$fl_instituto
                                                    
                                                );
                                                
                                                
                                                
                                                
                                            }else{
                                                
                                                #Se genera nuevo registro.
                                                
                                                #El registro no fue localizado pasa ser nuevo dato.

                                                $Queryui ='INSERT INTO st_teachers (upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp,fl_instituto)';
                                                $Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","ADD","","14",'.$fl_instituto.')';
                                                $st_id=EjecutaInsert($Queryui);
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."El registro no fue localizado pasa ser nuevo dato.".$Query);

                                                #Veriffica que no exusta el usuario por su alias/username
                                                $Query='SELECT COUNT(*) FROM c_usuario WHERE ds_alias="'.$username.'"  ';
                                                $regu=RecuperaValor($Query);
                                                $existe_usario=$regu[0];


                                                if(($teacher_id_revisado<>$teacher_id)&&(empty($existe_usario))){
                                                    $contador_insert_teacher++;
                                                }
                                                
                                                
                                                
                                                #Se generan registros en FAME.
                                                #Generamos su pasword temporal.
                                                #$ds_pass=substr( md5(microtime()), 1, 8);
                                                
                                                # Genera un identificador de sesion
                                                #$cl_sesion_nueva = sha256($username.$first_name.$last_name.$ds_pass);
                                                
                                                #GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera su sesion nueva:".$cl_sesion_nueva);

                                                /*
                                                $Query ='INSERT INTO c_usuario (ds_login,cl_sesion,ds_password,ds_alias,ds_nombres,ds_apaterno,ds_email,fg_activo,fe_alta,fl_perfil_sp,fl_instituto,fg_scf,fg_scf_revisado)';
                                                $Query.='VALUES("'.$username.'","'.$cl_sesion_nueva.'","'.sha256($ds_pass).'","'.$username.'","'.$first_name.'","'.$last_name.'","'.$email.'","1",CURRENT_TIMESTAMP,14,'.$fl_instituto.',1,1)';
                                                $fl_user_teacher=EjecutaInsert($Query);

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera c_usuario".$Query);

                                                #Se genera el registro en la tabla de fame_teachers.
                                                $Query='INSERT INTO c_maestro_sp (fl_maestro_sp,teacher_id)';
                                                $Query.='VALUES('.$fl_user_teacher.',"'.$teacher_id.'")';
                                                EjecutaQuery($Query);

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera c_maestro_sp".$Query);

                                                #Verificamos el isntitutio
                                                $Query='SELECT fl_instituto FROM c_instituto WHERE school_id="'.$school_number.'" AND fg_scf="1" ';
                                                $rop=RecuperaValor($Query);
                                                $fl_institutions=$rop['fl_instituto'];
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Verificamos el isntitutio-->".$Query);
                                                 */

                                                #EjecutaQuery("UPDATE c_usuario SET fg_scf_revisado=1, fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user_teacher  ");

                                                #GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user_teacher ');

                                                
                                                #Se les envia email de notificacion al usuario.
                                                #EnvioEmailCSF($email,$username,$first_name,$last_name,$fl_instituto,171,'','');
                                                
                                                #GeneraLog($file_name_txt,date('F j, Y, g:i a').'Envia email '.$email);
                                                
                                                
                                                
                                                #Se genera el array.
                                                $insert_teacher_array['insert_teacher'.$contador_insert_teacher]=array(
                                                        'fl_usuario'=>$fl_user_teacher,
                                                        'email'=>$email,
                                                        'first_name'=>$first_name,
                                                        'last_name_pat'=>$last_name,
                                                        'teacher_id'=>$teacher_id,
                                                        'username'=>$username,
                                                        'fl_instituto'=>$fl_instituto
                                                    
                                                );
                                                
                                            }

                                        } #end else registro no igual ala existente en DB.

                                    } #Eend de la segunda cargar teachers.
                                }#end_ teacher_id

                                $teacher_id_revisado=$teacher_id;

                            }
                            

                        } #end while lectura archivo.
                        
                        #Revisamos cuanto no fueron porcesados de los existentes en FAME Y determinamos que fuweron eliminados.
                        $Query="SELECT COUNT(*)FROM c_usuario WHERE fg_scf_revisado=0 AND fg_activo='1' AND fl_perfil_sp=".PFL_MAESTRO_SELF."  ";
                        $ro=RecuperaValor($Query);
                        $deleted_count_teacher=$ro[0];

                        #Desactivamos estos institutos.
                        EjecutaQuery("UPDATE c_usuario SET fg_activo='0' WHERE fg_scf_revisado=0 AND fl_instituto=$fl_instituto AND fl_perfil_sp=".PFL_MAESTRO_SELF."  ");

                        
                        
                        
                        #Actualizamos la bitacora de carga.
                        $Query="UPDATE stage_uploads SET end_time=CURRENT_TIMESTAMP,
											 added_count=$contador_insert_teacher,
											 unchanged_count=$unchanged_count_teacher,
											 deleted_count=$deleted_count_teacher ,
											 modified_count=$modified_count_teacher
											 WHERE id=$fl_upload ";
                        EjecutaQuery($Query);

                        #Indicamos que la carga ya esta completada.
                        $Query="UPDATE stage_uploads SET status_cd='COMPLETED' WHERE id=$fl_upload ";
                        EjecutaQuery($Query);

                        #Indicamos que la carga ya esta completada.
                        $Query="UPDATE stage_uploads SET status_cd='SEALED' WHERE id=$fl_upload ";
                        EjecutaQuery($Query);



                        #Recuperamos el proceso de carga del archivo.
                        $Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
									    ,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
									    TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
									    TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds
										FROM stage_uploads WHERE id=$fl_upload ";
                        $row=RecuperaValor($Query);
                        $user_id=$row['user_id'];
                        $upload_file_path=$row['upload_file_path'];
                        $upload_file_name=$row['upload_file_name'];
                        $upload_type=$row['upload_type'];
                        $upload_date=$row['upload_date'];
                        $status_cd=$row['status_cd'];
                        $start_time=GeneraFormatoFecha($row['start_time']);
                        $start_time_=$row['start_time'];
                        $end_time=$row['end_time'];
                        $proc_status=$row['proc_status'];
                        
                        $upload_time_hrs=$row['hrs'];
                        $upload_time_minutes=$row['minutes'];
                        $upload_time_seconds=$row['seconds'];
                        
                        $runtime=$upload_time_hrs."h ". $upload_time_minutes."m ".$upload_time_seconds."s ";

                        if($end_time){
                            $finish="<i class='fa fa-check-circle' style='color:#226108;'></i>";
                            
                        }
                        
                        
                        #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
                        $date = date_create($start_time_);
                        $start_time_=date_format($date,'F j, Y, g:i:s a');
                        
                        $date = date_create($end_time);
                        $end_time=date_format($date,'F j, Y, g:i:s a');
                        
                        #cALCULAMOS TOTALES POR RENGLON.
                        $Total_teacher=$contador_insert_teacher+$modified_count_teacher+$deleted_count_teacher+$unchanged_count_teacher;
                        
                        #Calculamos el treshold.
                        $threshold_modified_teacher= ($modified_count_teacher/$Total_teacher)*100;
                        $threshold_insert_teacher= ($contador_insert_teacher/$Total_teacher)*100;
                        $threshold_deleted_teacher= ($deleted_count_teacher/$Total_teacher)*100;
                        $threshold_unchanged_teacher= ($unchanged_count_teacher/$Total_teacher)*100;

                        #Default para ejecutar en FAME.
                        $permiso_ejecutar_DB_FAME=1;
                        $error_threshold=0;
                        #El limite es 5%, si rebasa esa cantidad envia emial con los datos de los isntitutos procesados.
                        if($threshold_modified_teacher>5){
                            $permiso_ejecutar_DB_FAME=0;
                            $error_threshold=1;
                        }
                        if($threshold_insert_teacher>5){
                            $permiso_ejecutar_DB_FAME=0;
                            $error_threshold=1;
                        }
                        if($threshold_deleted_teacher>5){
                            $permiso_ejecutar_DB_FAME=0;
                            $error_threshold=1;
                        }
                        if($primeracarga==1){
                            if($aplica_threshold==1){
                                $permiso_ejecutar_DB_FAME=1;
                                $error_threshold=0;
                            }
                        }
                        if($aplica_threshold==1){
                            $permiso_ejecutar_DB_FAME=1;
                            $error_threshold=0;
                        }else{
                            EjecutaQuery("UPDATE stage_uploads SET nothresh_modified='$threshold_modified_teacher',nothresh_deleted='$threshold_deleted_teacher',nothresh_added='$threshold_insert_teacher' WHERE id=$fl_upload ");		
                        }
                        $datas=array();
                        
                        $datas['datos']= array(
                           'name_reference'=>'Teachers',
                           
                           'contador_insert'=>$contador_insert_teacher,
                           'deleted_count'=>$deleted_count_teacher,
                           'unchanged_count'=>$unchanged_count_teacher,
                           'upload_count'=>$Total_teacher,
                           'modified_count'=>$modified_count_teacher,
                           'threshold_modified'=>$threshold_modified_teacher,
                           'threshold_insert'=>$threshold_insert_teacher,
                           'threshold_deleted'=>$threshold_deleted_teacher,
                           'error_threshold'=>$error_threshold
                        
                        );
                        
                        #fINALIZA EL PROCESO
                        $Query="UPDATE stage_uploads SET proc_status='$error_threshold', status_cd='SEALED',upload_count=$Total_teacher WHERE id=$fl_upload ";
                        EjecutaQuery($Query);
                        
                        
                        


                    } #end open archivo.
                    
                    
                    
                    
                    /*******************/
                    if($permiso_ejecutar_DB_FAME==1){
                        
                        
                        #Proceso de la carga.
                        if ($file = fopen($ruta_completa_archivo, "r")){
                            
                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."Empieza a leer el archivo");
                            
                            # Lee los nombres de los campos
                            // $name_camps = fgetcsv($file, 0, ",", "\"", "\"");
                            // $num_camps = count($name_camps);
                            // $names_camps[$num_camps -1];
                            $tot_reg1 = 0;
                            $contador_insert_teacher=0;
                            $unchanged_count_teacher=0;
                            $deleted_count_teacher=0;
                            $modified_count_teacher=0;
                            
                            $insert_teacher_array=array();
                            $upload_teacher_array=array();
                            $unchanged_teacher_array=array();
                            $deleted_teacher_array=array();
                            $modified_teacher_array=array();
                            
                            while ($data = fgetcsv ($file, 0, ",")){
                                if ($data[0]=='Teacher ID' || $data[1]=='Username' || $data[3]=='First Name' || $data[4]=='Last Name' || $data[5]=='School' || $data[6]=='School ID' || $data[6]=='Email') {

                                    // Do Noting
                                    
                                } else {

                                    $teacher_id = $data[0];
                                    $username = $data[1];
                                    $departament = $data[2];
                                    $first_name = $data[3];
                                    $last_name = $data[4];
                                    $school = $data[5];
                                    $school_number = $data[6];//school id
                                    $email = $data[7];
                                    $course = $data[8];
                                    $status = $data[9];
                                    $las_login = $data[10];
                                    $progress = $data[11];
                                    
                                    if(($departament=='N/A')||(empty($departament))){
                                        $departament=0;
                                    }else{
                                        $departament=1;
                                    }
                                    if(($school=='N/A')||(empty($school))){
                                        $school=0;
                                    }
                                    if(($school_number=='N/A')||(empty($school_number))){
                                        $school_number=0;
                                    }
                                    if(($course=='N/A')||(empty($course))){
                                        $course=0;
                                    }
                                    if(($status=='N/A')||(empty($status))){
                                        $status=0;
                                    }
                                    if(($las_login=='N/A')||(empty($las_login))){
                                        $las_login=0;
                                    }
                                    if(($progress=='N/A')||(empty($progress))){
                                        $progress=0;
                                    }
                                    
                                    if(!empty($teacher_id)){

                                        #genera status started.
                                        $Query="UPDATE stage_uploads SET status_cd='STARTED',upload_file_name_log='$new_archive_procesed' WHERE id=$fl_upload ";
                                        EjecutaQuery($Query);
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                        $fl_perfil_sp=PFL_MAESTRO_SELF;
                                        
                                        #Siempre se genera la bitacora de teachers.
                                        #$Queryui ='INSERT INTO st_teachers_bitacora ( upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp)';
                                        #$Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","LOADED","","15")';
                                        #$st_id_bitacora=EjecutaInsert($Queryui);
                                        
                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);

                                        #Verificamos el isntitutio
                                        $Query='SELECT fl_instituto,fl_usuario_sp FROM c_instituto WHERE school_id="'.$school_number.'"  ';
                                        $rop=RecuperaValor($Query);
                                        $fl_institutions=!empty($rop['fl_instituto'])?$rop['fl_instituto']: null;
                                        $fl_instituto=!empty($rop['fl_instituto'])?$rop['fl_instituto']: null;
                                        $fl_usu_invita=!empty($rop['fl_usuario_sp'])?$rop['fl_usuario_sp']:null;
                                        /*
                                         *Nota: Si el teacher no trae id del instituto y no se encuentra en fame este pasa a disponibilidad del rector.
                                         */
                                        if(empty($fl_instituto)){##se obteine del usuario generado del rector admin
                                            $fl_instituto=$fl_instituto_rector;$fl_usu_invita=2395;
                                            GeneraLog($file_name_txt,date('F j, Y, g:i a')."No se encontro el instituto en FAME con clave:$school_number , y pasa al rector. con fl_instituto".$fl_instituto);
                                        }

                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                        
                                        if($primeracarga==1){
                                            
                                            #Se inserta en primera instancia la bitacora de teachers.
                                            #$Queryui ='INSERT INTO st_teachers (upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp)';
                                            #$Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","LOADED","","14")';
                                            #$st_id=EjecutaInsert($Queryui);

                                            #GeneraLog($file_name_txt,$Queryui);
                                            
                                            #if($st_id){
                                            //$contador_insert_teacher++;
                                            #}

                                            if($aplica_threshold==1){

                                                #Se generan registros en FAME.
                                                #Generamos su pasword temporal.
                                                $ds_pass=substr( md5(microtime()), 1, 8);
                                                
                                                # Genera un identificador de sesion
                                                $cl_sesion_nueva = sha256($username.$first_name.$last_name.$ds_pass);
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera su sesion nueva:".$cl_sesion_nueva);


                                                #Verificamos que no exista ese usuario en FAME
                                                $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'"  ';

                                                $rol=RecuperaValor($Query);

                                                $fl_maestro_existente=$rol['fl_maestro_sp'];
                                                
                                                ##$Query="SELECT fl_usuario FROM c_usuario WHERE fl_usuario=$fl_maestro_existente AND fl_instituto=$fl_instituto  ";
                                                ##$rec=RecuperaValor($Query);
                                                ##$fl_usuario_maestro=$rec['fl_usuario'];
                                                
                                                #Veriffica que no exusta el usuario por su alias/username
                                                $Query='SELECT COUNT(*) FROM c_usuario WHERE ds_alias="'.$username.'"  ';
                                                $regu=RecuperaValor($Query);
                                                $existe_usario=$regu[0];
                                                
                                                if(empty($existe_usario)){
                                                    
                                                    $Query ='INSERT INTO c_usuario (ds_login,cl_sesion,ds_password ,ds_alias,ds_nombres,ds_apaterno,ds_email,fg_activo,fe_alta,fl_perfil_sp,fl_instituto,fg_scf,fg_scf_revisado)';
                                                    $Query.='VALUES("'.$username.'","'.$cl_sesion_nueva.'","'.sha256($ds_pass).'","'.$username.'","'.$first_name.'","'.$last_name.'","'.$email.'","1",CURRENT_TIMESTAMP,14,'.$fl_instituto.',1,1)';
                                                    $fl_user_teacher=EjecutaInsert($Query);
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Realiza Insert a la tabla de c_usuario-->".$Query);
                                                    $Query="UPDATE c_usuario SET fl_usu_invita=$fl_usu_invita WHERE fl_usuario=$fl_user_teacher ";
                                                    EjecutaQuery($Query);
                                                    
                                                    #Se genera el registro en la tabla de fame_teachers.
                                                    $Query='INSERT INTO c_maestro_sp (fl_maestro_sp,teacher_id)';
                                                    $Query.='VALUES('.$fl_user_teacher.',"'.$teacher_id.'")';
                                                    EjecutaQuery($Query);
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Realiza Insert a la tabla de c_maestro_sp-->".$Query);

                                                    #Recuperamos el no. de usuarios que tiene el instituto y le sumaos el nuevo registro
                                                    $Query="SELECT no_usuarios FROM c_instituto WHERE fl_instituto=$fl_instituto ";
                                                    $row=RecuperaValor($Query);
                                                    $no_usuarios_actual = $row[0] +1 ;
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                                    
                                                    #Actualizamos el registro de numero de usuarios que tiee el isntituto.
                                                    $Query="UPDATE c_instituto SET no_usuarios=$no_usuarios_actual WHERE fl_instituto=$fl_instituto ";
                                                    EjecutaQuery($Query);

                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                                    #Actualizamos al estudiante si aun esta no esta asigando el maestro atraves de su teacher_id.
                                                    EjecutaQuery('UPDATE k_usuario_programa SET fl_maestro='.$fl_user_teacher.' WHERE teacher_id="'.$teacher_id.'" ');
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE k_usuario_programa SET fl_maestro='.$fl_user_teacher.' WHERE teacher_id="'.$teacher_id.'"');
                                                    
                                                    EjecutaQuery("UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user_teacher  ");
                                                    GeneraLog($file_name_txt,"UPDATE c_usuario SET fl_instituto=$fl_instituto WHERE  fl_usuario=$fl_user_teacher ");
                                                    
                                                    #Se les envia email de notificacion al usuario.
                                                    EnvioEmailCSF($email,$username,$first_name,$last_name,$fl_instituto,171,$ds_pass,'');
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se envia email ".$email);
                                                    
                                                    $contador_insert_teacher++;
                                                    
                                                }else{

                                                    $fl_user_teacher=$fl_maestro_existente;

                                                }
                                                
                                                #Verifica que no exista en la tabla de institutos_teachers (por si existe un teacher en mas de dos institutciones.)
                                                $Query="SELECT fl_maestro_sp FROM k_instituto_teacher WHERE fl_maestro_sp=$fl_user_teacher AND fl_instituto=$fl_institutions ";

                                                $row=RecuperaValor($Query);

                                                if(empty($row['fl_maestro_sp'])){
                                                    
                                                    $Query ="INSERT INTO k_instituto_teacher (fl_maestro_sp,fl_instituto,fg_aceptado,fe_creacion,fe_ultmod,fl_usuario_invitando) ";
                                                    $Query.="VALUES($fl_user_teacher,$fl_institutions,'1',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usu_invita) ";
                                                    EjecutaInsert($Query);
                                                    
                                                }

                                                #si existen mas de dos institutos del mismo teacher quiere decir que si existen un teacher en varios institutos y se identifica al user con un flag->fg_select_instituto 
                                                $Query="SELECT COUNT(*) FROM k_instituto_teacher WHERE fl_maestro_sp=".$fl_user_teacher." ";
                                                $row=RecuperaValor($Query);

                                                if($row[0]>=2){

                                                    EjecutaQuery("UPDATE c_usuario SET fg_select_instituto='1' WHERE fl_usuario=$fl_user_teacher  ");
                                                }

                                            }
                                            
                                        }else{
                                            
                                            #Ya es la segunda carga.

                                            #Verifica si eexiste ese registro  en FAME con el username y el id_teacher. | ds_login para FAME
                                            $Query ='SELECT ds_login,ds_alias,ds_nombres,ds_apaterno,ds_email,a.teacher_id,fg_scf,b.fl_usuario  
                                                         FROM c_maestro_sp a 
                                                         JOIN c_usuario b ON b.fl_usuario=a.fl_maestro_sp  
                                                         WHERE a.teacher_id="'.$teacher_id.'" AND
                                                         /*AND b.fl_instituto='.$fl_instituto.' */
                                                         EXISTS(
                                                         SELECT 1 FROM k_instituto_teacher i WHERE i.fl_maestro_sp=b.fl_usuario)  
                                                         AND b.ds_login="'.$username.'" AND ds_nombres="'.$first_name.'" AND ds_apaterno="'.$last_name.'" AND ds_email="'.$email.'"  ';
                                            $row=RecuperaValor($Query);      
                                            $ds_login_=!empty($row['ds_login'])?$row['ds_login']:NULL;
                                            # PREGUNTA PARA MIKE, Porque estas usando ds_login dos veces como indice?
                                            $username_db=!empty($row['ds_login'])?$row['ds_login']:NULL;
                                            $teacher_id_db=!empty($row['teacher_id'])?$row['teacher_id']:NULL;
                                            $fl_usuario_maestro=!empty($row['fl_usuario'])?$row['fl_usuario']:NULL;

                                            GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                            #Se encontro registro igualito.
                                            if(!empty($row[0])){

                                                #El dato no sufre ningun cambio pasa a operation_code 'NO_CHANGE' y se inserta la bitacora.
                                                #$Queryui ='INSERT INTO st_teachers (upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp)';
                                                #$Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","NO_CHANGE","","14")';
                                                #$st_id=EjecutaInsert($Queryui);
                                                
                                                #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);
                                                if($teacher_id_revisado<>$teacher_id){
                                                    $unchanged_count_teacher++; 
                                                }

                                                $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'" ';
                                                $row=RecuperaValor($Query);
                                                $fl_maestro_sp=$row['fl_maestro_sp'];
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query); 

                                                EjecutaQuery('UPDATE c_usuario SET fg_activo="1", fg_scf_revisado=1 WHERE fl_usuario='.$fl_maestro_sp.' AND fl_perfil_sp='.PFL_MAESTRO_SELF.'  ');
                                                
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE c_usuario SET fg_scf_revisado=1 WHERE fl_usuario='.$fl_maestro_sp.' AND fl_perfil_sp='.PFL_MAESTRO_SELF.'  ');

                                                #Actualizamos al estudiante si aun esta no esta asigando el maestro atraves de su teacher_id.
                                                EjecutaQuery('UPDATE k_usuario_programa SET fl_maestro='.$fl_maestro_sp.' WHERE teacher_id="'.$teacher_id.'" ');
                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE k_usuario_programa SET fl_maestro='.$fl_maestro_sp.' WHERE teacher_id="'.$teacher_id.'"');
                                                


                                                #Se genera el array.
                                                $unchanged_teacher_array['unchanged_teacher'.$unchanged_count_teacher]=array(
                                                        'fl_usuario'=>$fl_maestro_sp,
                                                        'email'=>$email,
                                                        'first_name'=>$first_name,
                                                        'last_name_pat'=>$last_name,
                                                        'teacher_id'=>$teacher_id,
                                                        'username'=>$username
                                                    
                                                );
                                                
                                                #Verifica que no exista en la tabla de institutos_teachers(por si existe un teacher en mas de dos institutciones.)
                                                $Query="SELECT fl_maestro_sp FROM k_instituto_teacher WHERE fl_maestro_sp=$fl_maestro_sp AND fl_instituto=$fl_institutions ";

                                                $row=RecuperaValor($Query);

                                                if(empty($row['fl_maestro_sp'])){
                                                    
                                                    $Query ="INSERT INTO k_instituto_teacher (fl_maestro_sp,fl_instituto,fg_aceptado,fe_creacion,fe_ultmod,fl_usuario_invitando) ";
                                                    $Query.="VALUES($fl_maestro_sp,$fl_institutions,'1',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usu_invita) ";

                                                    EjecutaInsert($Query);
                                                    
                                                }

                                                #si existen mas de dos institutos del mismo teacher quiere decir que si existen un teacher en varios institutos y se identifica al user con un flag->fg_select_instituto 
                                                $Query="SELECT COUNT(*) FROM k_instituto_teacher WHERE fl_maestro_sp=".$fl_maestro_sp." ";
                                                $row=RecuperaValor($Query);
                                                if($row[0]>=2){
                                                    EjecutaQuery("UPDATE c_usuario SET fg_select_instituto='1' WHERE fl_usuario=$fl_maestro_sp  ");

                                                }

                                            }else{
                                                
                                                #Si se encontro el username|ds login y el teacher id,eso quiere decir que solo sufrio modificaciones en algun otro dato, entonces actualizamos el registro.
                                                $Query ='SELECT ds_login,ds_alias,ds_nombres,ds_apaterno,ds_email,a.teacher_id,fg_scf,b.fl_usuario,b.fl_instituto  
                                                         FROM c_maestro_sp a 
                                                         JOIN c_usuario b ON b.fl_usuario=a.fl_maestro_sp  
                                                         WHERE a.teacher_id="'.$teacher_id.'" 
                                                         AND b.ds_login="'.$username.'"   ';
                                                $row=RecuperaValor($Query);
                                                
                                                $username_db=!empty($row['ds_login'])?$row['ds_login']:NULL;
                                                $teacher_id_db=!empty($row['teacher_id'])?$row['teacher_id']:NULL;
                                                $fl_usuario_db=!empty($row['fl_usuario'])?$row['fl_usuario']:NULL;
                                                $fl_instituto_db=!empty($row['fl_instituto'])?$row['fl_instituto']:NULL;

                                                GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);

                                                if(($username_db==$username)&&($teacher_id_db==$teacher_id)){
                                                    
                                                    #$Queryui ='INSERT INTO st_teachers (upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp)';
                                                    #$Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","MODIFY","","14")';
                                                    #$st_id=EjecutaInsert($Queryui);
                                                    
                                                    #GeneraLog($file_name_txt,date('F j, Y, g:i a').$Queryui);
                                                    
                                                    if($teacher_id_revisado<>$teacher_id){
                                                        
                                                        $modified_count_teacher++;
                                                    }
                                                    
                                                    
                                                    #Actualizamos datos generales de ese registro.
                                                    $Query='UPDATE c_usuario SET fg_activo="1",fg_scf_revisado=1,fl_instituto='.$fl_instituto.', ds_nombres="'.$first_name.'" ,ds_apaterno="'.$last_name.'",ds_email="'.$email.'" WHERE fl_usuario='.$fl_usuario_db.' ';
                                                    EjecutaQuery($Query);
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').$Query);
                                                    
                                                    #Actualizamos al estudiante si aun esta no esta asigando el maestro atraves de su teacher_id.
                                                    EjecutaQuery('UPDATE k_usuario_programa SET fl_maestro='.$fl_usuario_db.' WHERE teacher_id="'.$teacher_id.'" ');
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE k_usuario_programa SET fl_maestro='.$fl_usuario_db.' WHERE teacher_id="'.$teacher_id.'"');
                                                    
                                                    
                                                    
                                                    #Se genera el array.
                                                    $modified_teacher_array['modified_teacher'.$modified_count_teacher]=array(
                                                            'fl_usuario'=>$fl_usuario_db,
                                                            'email'=>$email,
                                                            'first_name'=>$first_name,
                                                            'last_name_pat'=>$last_name,
                                                            'teacher_id'=>$teacher_id,
                                                            'username'=>$username,
                                                            'fl_instituto'=>$fl_instituto
                                                        
                                                    );
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    #Verifica que no exista en la tabla de institutos_teachers(por si existe un teacher en mas de dos institutciones.)
                                                    $Query="SELECT fl_maestro_sp FROM k_instituto_teacher WHERE fl_maestro_sp=$fl_usuario_db AND fl_instituto=$fl_instituto ";

                                                    $row=RecuperaValor($Query);

                                                    if(empty($row['fl_maestro_sp'])){
                                                        
                                                        $Query ="INSERT INTO k_instituto_teacher (fl_maestro_sp,fl_instituto,fg_aceptado,fe_creacion,fe_ultmod,fl_usuario_invitando) ";
                                                        $Query.="VALUES($fl_usuario_db,$fl_instituto,'1',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usu_invita) ";
                                                        EjecutaInsert($Query);
                                                        
                                                    }

                                                    #si existen mas de dos institutos del mismo teacher quiere decir que si existen un teacher en varios institutos y se identifica al user con un flag->fg_select_instituto 
                                                    $Query="SELECT COUNT(*) FROM k_instituto_teacher WHERE fl_maestro_sp=".$fl_maestro_sp." ";
                                                    $row=RecuperaValor($Query);
                                                    if($row[0]>=2){
                                                        EjecutaQuery("UPDATE c_usuario SET fg_select_instituto='1' WHERE fl_usuario=$fl_maestro_sp  ");

                                                    }
                                                    
                                                }else{
                                                    
                                                    #Se genera nuevo registro.
                                                    
                                                    #El registro no fue localizado pasa ser nuevo dato.

                                                    #$Queryui ='INSERT INTO st_teachers (upload_id,teacher_id,username,ds_department,names,last_name_pat,last_name_mat,school_id,email,course,operation_code,groups,fl_perfil_sp)';
                                                    #$Queryui.='VALUES('.$fl_upload.',"'.$teacher_id.'","'.$username.'","'.$ds_department.'","'.$first_name.'","'.$last_name.'","","'.$school_number.'","'.$email.'","","ADD","","14")';
                                                    #$st_id=EjecutaInsert($Queryui);
                                                    
                                                    #GeneraLog($file_name_txt,date('F j, Y, g:i a')."El registro no fue localizado pasa ser nuevo dato.".$Query);

                                                    #if($st_id){
                                                    //$contador_insert_teacher++;
                                                    #}
                                                    
                                                    
                                                    
                                                    #Se generan registros en FAME.
                                                    #Generamos su pasword temporal.
                                                    $ds_pass=substr( md5(microtime()), 1, 8);
                                                    
                                                    # Genera un identificador de sesion
                                                    $cl_sesion_nueva = sha256($username.$first_name.$last_name.$ds_pass);
                                                    
                                                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera su sesion nueva:".$cl_sesion_nueva);

                                                    
                                                    #Verificamos que no exista ese usuario en FAME
                                                    $Query='SELECT fl_maestro_sp FROM c_maestro_sp WHERE teacher_id="'.$teacher_id.'"  ';
                                                    $rol=RecuperaValor($Query);
                                                    $fl_maestro_existente=$rol['fl_maestro_sp'];
                                                    
                                                    $Query="SELECT fl_usuario FROM c_usuario WHERE fl_usuario=$fl_maestro_existente AND fl_instituto=$fl_instituto  ";
                                                    $rec=RecuperaValor($Query);
                                                    $fl_usuario_maestro=$rec['fl_usuario'];
                                                    
                                                    #Veriffica que no exusta el usuario por su alias/username
                                                    $Query='SELECT COUNT(*) FROM c_usuario WHERE ds_alias="'.$username.'"  ';
                                                    $regu=RecuperaValor($Query);
                                                    $existe_usario=$regu[0];

                                                    if((empty($fl_usuario_maestro))&&(empty($existe_usario))){



                                                        $Query ='INSERT INTO c_usuario (ds_login,cl_sesion,ds_password,ds_alias,ds_nombres,ds_apaterno,ds_email,fg_activo,fe_alta,fl_perfil_sp,fl_instituto,fg_scf,fg_scf_revisado)';
                                                        $Query.='VALUES("'.$username.'","'.$cl_sesion_nueva.'","'.sha256($ds_pass).'","'.$username.'","'.$first_name.'","'.$last_name.'","'.$email.'","1",CURRENT_TIMESTAMP,14,'.$fl_instituto.',1,1)';
                                                        $fl_user_teacher=EjecutaInsert($Query);

                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera c_usuario".$Query);

                                                        #Se genera el registro en la tabla de fame_teachers.
                                                        $Query='INSERT INTO c_maestro_sp (fl_maestro_sp,teacher_id)';
                                                        $Query.='VALUES('.$fl_user_teacher.',"'.$teacher_id.'")';
                                                        EjecutaQuery($Query);

                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."Se genera c_maestro_sp".$Query);

                                                        #Verificamos el isntitutio
                                                        $Query='SELECT fl_instituto FROM c_instituto WHERE school_id="'.$school_number.'"  ';
                                                        $rop=RecuperaValor($Query);
                                                        $fl_institutions=$rop['fl_instituto'];
                                                        
                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a')."#Verificamos el isntitutio-->".$Query);


                                                        EjecutaQuery("UPDATE c_usuario SET fg_scf_revisado=1, fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user_teacher  ");

                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE c_usuario SET fl_instituto=$fl_institutions WHERE  fl_usuario=$fl_user_teacher ');

                                                        
                                                        #Se les envia email de notificacion al usuario.
                                                        EnvioEmailCSF($email,$username,$first_name,$last_name,$fl_instituto,171,$ds_pass,'');
                                                        
                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').'Envia email '.$email);
                                                        
                                                        #Actualizamos al estudiante si aun esta no esta asigando el maestro atraves de su teacher_id.
                                                        EjecutaQuery('UPDATE k_usuario_programa SET fl_maestro='.$fl_user_teacher.' WHERE teacher_id="'.$teacher_id.'" ');
                                                        GeneraLog($file_name_txt,date('F j, Y, g:i a').'UPDATE k_usuario_programa SET fl_maestro='.$fl_user_teacher.' WHERE teacher_id="'.$teacher_id.'"');
                                                        
                                                        $contador_insert_teacher++;
                                                        
                                                        #Verifica que no exista en la tabla de institutos_teachers(por si existe un teacher en mas de dos institutciones.)
                                                        $Query="SELECT fl_maestro_sp FROM k_instituto_teacher WHERE fl_maestro_sp=$fl_user_teacher AND fl_instituto=$fl_institutions ";

                                                        $row=RecuperaValor($Query);

                                                        if(empty($row['fl_maestro_sp'])){
                                                            
                                                            $Query ="INSERT INTO k_instituto_teacher (fl_maestro_sp,fl_instituto,fg_aceptado,fe_creacion,fe_ultmod,fl_usuario_invitando) ";
                                                            $Query.="VALUES($fl_user_teacher,$fl_institutions,'1',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usu_invita) ";
                                                            EjecutaInsert($Query);
                                                            
                                                        }

                                                        #si existen mas de dos institutos del mismo teacher quiere decir que si existen un teacher en varios institutos y se identifica al user con un flag->fg_select_instituto 
                                                        $Query="SELECT COUNT(*) FROM k_instituto_teacher WHERE fl_maestro_sp=".$fl_user_teacher." ";

                                                        $row=RecuperaValor($Query);

                                                        if($row[0]>=2){
                                                            EjecutaQuery("UPDATE c_usuario SET fg_select_instituto='1' WHERE fl_usuario=$fl_user_teacher  ");

                                                        }
                                                        
                                                    }
                                                    
                                                    #Verifica que no exista en la tabla de institutos_teachers.
                                                    $Query="SELECT fl_maestro_sp FROM k_instituto_teacher WHERE fl_maestro_sp=$fl_user_teacher AND fl_instituto=$fl_institutions ";

                                                    $row=RecuperaValor($Query);

                                                    if(empty($row['fl_maestro_sp'])){
                                                        
                                                        $Query ="INSERT INTO k_instituto_teacher (fl_maestro_sp,fl_instituto,fg_aceptado,fe_creacion,fe_ultmod,fl_usuario_invitando) ";
                                                        $Query.="VALUES($fl_user_teacher,$fl_institutions,'1',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usu_invita) ";
                                                        EjecutaInsert($Query);
                                                        
                                                    }
                                                    
                                                    #Se genera el array.
                                                    $insert_teacher_array['insert_teacher'.$contador_insert_teacher]=array(
                                                            'fl_usuario'=>$fl_user_teacher,
                                                            'email'=>$email,
                                                            'first_name'=>$first_name,
                                                            'last_name_pat'=>$last_name,
                                                            'teacher_id'=>$teacher_id,
                                                            'username'=>$username,
                                                            'fl_instituto'=>$fl_instituto
                                                        
                                                    );
                                                    
                                                }

                                            } #end else registro no igual ala existente en DB.

                                        } #Eend de la segunda cargar teachers.
                                        
                                        $teacher_id_revisado=$teacher_id;
                                        
                                    }#end_ teacher_id
                                }
                                
                            } #end while lectura archivo.
                            
                            #Revisamos cuanto no fueron porcesados de los existentes en FAME Y determinamos que fuweron eliminados.
                            $Query="SELECT COUNT(*)FROM c_usuario WHERE fg_scf_revisado=0  AND fl_instituto=$fl_instituto AND fg_activo='1' AND fl_perfil_sp=".PFL_MAESTRO_SELF."  ";
                            $ro=RecuperaValor($Query);
                            $deleted_count_teacher=$ro[0];

                            #Desactivamos estos institutos.
                            EjecutaQuery("UPDATE c_usuario SET fg_activo='0' WHERE fg_scf_revisado=0  AND fl_instituto=$fl_instituto AND fl_perfil_sp=".PFL_MAESTRO_SELF."  ");

                            #Actualizamos la bitacora de carga.
                            $Query="UPDATE stage_uploads SET end_time=CURRENT_TIMESTAMP,
													 added_count=$contador_insert_teacher,
													 unchanged_count=$unchanged_count_teacher,
													 deleted_count=$deleted_count_teacher ,
													 modified_count=$modified_count_teacher
													 WHERE id=$fl_upload ";
                            EjecutaQuery($Query);

                            #Indicamos que la carga ya esta completada.
                            $Query="UPDATE stage_uploads SET status_cd='COMPLETED' WHERE id=$fl_upload ";
                            EjecutaQuery($Query);

                            #Indicamos que la carga ya esta completada.
                            $Query="UPDATE stage_uploads SET status_cd='SEALED' WHERE id=$fl_upload ";
                            EjecutaQuery($Query);

                            #Recuperamos el proceso de carga del archivo.
                            $Query="SELECT user_id,upload_file_path,upload_file_name,upload_type,upload_date,status_cd,start_time,end_time,proc_status 
												,TIMESTAMPDIFF(HOUR, start_time, end_time) AS hrs,
												TIMESTAMPDIFF(MINUTE, start_time, end_time) AS minutes,
												TIMESTAMPDIFF(SECOND, start_time, end_time) AS seconds
												FROM stage_uploads WHERE id=$fl_upload ";
                            $row=RecuperaValor($Query);
                            $user_id=$row['user_id'];
                            $upload_file_path=$row['upload_file_path'];
                            $upload_file_name=$row['upload_file_name'];
                            $upload_type=$row['upload_type'];
                            $upload_date=$row['upload_date'];
                            $status_cd=$row['status_cd'];
                            $start_time=GeneraFormatoFecha($row['start_time']);
                            $start_time_=$row['start_time'];
                            $end_time=$row['end_time'];
                            $proc_status=$row['proc_status'];
                            $upload_time_hrs=$row['hrs'];
                            $upload_time_minutes=$row['minutes'];
                            $upload_time_seconds=$row['seconds'];
                            $runtime=$upload_time_hrs."h ". $upload_time_minutes."m ".$upload_time_seconds."s ";

                            if($end_time){
                                $finish="<i class='fa fa-check-circle' style='color:#226108;'></i>";
                                
                            }
                            
                            #Damos formato de fecha ala fecha inicio y fin del proceso (sep 29, 2016 54:00)
                            $date = date_create($start_time_);
                            $start_time_=date_format($date,'F j, Y, g:i:s a');
                            
                            $date = date_create($end_time);
                            $end_time=date_format($date,'F j, Y, g:i:s a');
                            
                            #cALCULAMOS TOTALES POR RENGLON.
                            $Total_teacher=$contador_insert_teacher+$modified_count_teacher+$deleted_count_teacher+$unchanged_count_teacher;
                            
                        } #end open archivo.
                    }
                    
                    /********************/
                    
                    #Se les envia email de notificacion al usuario.
                    EnvioEmailCSF($ds_email_instituto,'',$first_name_instituto,$last_name_instituto,$fl_instituto,178,'',$datas);
                    
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."eNvia email ".$ds_email_instituto." y Termina lectura del archivo");
                    GeneraLog($file_name_txt,date('F j, Y, g:i a')."====================================Finaliza proceso =================================================");

                }#end upload _teacher
            }else{ #end archivo valido.
                GeneraLog($file_name_txt,date('F j, Y, g:i a')."No se encontro un archivo valido, no contiene ninguna palabra clave |school| student| teacher|, ni la palabra nothreshold_");

            }
        }else{ #end archvio cvs.
            
            GeneraLog($file_name_txt,date('F j, Y, g:i a')."Tipo de extension no valido".$ext);
        }
    }#end scandir.
}#end while institutos 










function EnvioEmailCSF($ds_email_destinatario,$username='',$first_name='',$last_name_pat='',$fl_instituto,$fl_template_email,$password='',$data){
	
    #Recuperamos datos generales el instituto.
    $Query="SELECT fl_usuario_sp,ds_instituto,ds_nombres,ds_apaterno FROM c_instituto a JOIN c_usuario b ON b.fl_usuario=a.fl_usuario_sp WHERE a.fl_instituto=$fl_instituto ";
    $rom=RecuperaValor($Query);
    $fl_usuario_ins=$rom['fl_usuario_sp'];
    $ds_instituto=$rom['ds_instituto'];
    $user_fname_invitador=$rom['ds_nombres'];
    $user_lname_invitador=$rom['ds_apaterno'];
	
    #Recuperamos el ultimo id del correo para saber y llevar su bitacora.
    $Query="SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
    $row=RecuperaValor($Query);
    $no_envio=$row[0];
    $no_envio=$no_envio + 1 ;
	
    # Genera una nueva clave para la liga de acceso al contrato
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    $ds_cve="";
    for($i = 0; $i < 40; $i++)
        $ds_cve .= substr($str, rand(0,62), 1);
    $ds_cve .= date("Ymd").$no_envio;
    
    #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
    $no_codigo_confirmacion = substr("$ds_cve", -30, 30);
	
    # Obtenmos el template
    $ds_header = genera_documento_sp('', 1, $fl_template_email);
    $ds_body = genera_documento_sp('', 2, $fl_template_email);
    $ds_footer = genera_documento_sp('', 3, $fl_template_email);
    $ds_mensaje=$ds_header.$ds_body.$ds_footer;
    $dominio_campus = ObtenConfiguracion(116);
    $ds_mensaje = str_replace("#fame_fname#", $first_name, $ds_mensaje);
    $ds_mensaje = str_replace("#fame_lname#", $last_name_pat, $ds_mensaje);
    $ds_mensaje = str_replace("#fame_fname_friends#", $ds_instituto, $ds_mensaje);
    $ds_mensaje = str_replace("#fame_lname_friends#", " ", $ds_mensaje);
    $ds_mensaje = str_replace("#fame_username#", $username, $ds_mensaje);
    $ds_mensaje = str_replace("#fame_password#", $password, $ds_mensaje);	  
    $ds_mensaje = str_replace("#fame_link#", $dominio_campus, $ds_mensaje);
    $ds_mensaje = str_replace("#fame_reference#",$data['datos']['name_reference'], $ds_mensaje);
    $ds_mensaje = str_replace("#fame_created#",$data['datos']['contador_insert'], $ds_mensaje);
    $ds_mensaje = str_replace("#fame_uploaded#",$data['datos']['upload_count'], $ds_mensaje);
    $ds_mensaje = str_replace("#fame_deleted#",$data['datos']['deleted_count'], $ds_mensaje);
    $ds_mensaje = str_replace("#fame_unchanged#",$data['datos']['unchanged_count'], $ds_mensaje);
    $ds_mensaje = str_replace("#fame_modified#",$data['datos']['modified_count'], $ds_mensaje);
    $ds_mensaje = str_replace("#fame_total#",$data['datos']['upload_count'], $ds_mensaje);
    
    if($data['datos']['error_threshold']==1){
        #Sustituimos datos del treshold.
        $ds_mensaje = str_replace("#fame_created_percentage_threshold#",$data['datos']['threshold_insert']."%", $ds_mensaje);
        $ds_mensaje = str_replace("#fame_deleted_percentage_threshold#",$data['datos']['threshold_deleted']."%", $ds_mensaje);
        $ds_mensaje = str_replace("#fame_modified_percentage_threshold#",$data['datos']['threshold_modified']."%", $ds_mensaje);
        $ds_mensaje = str_replace("#fame_unchanged_percentage_threshold#","", $ds_mensaje);
        $ds_mensaje = str_replace("#fame_total_percentage_threshold#","", $ds_mensaje);
    }else{
        #En el emial mno presentamos esos datos.
        $ds_mensaje = str_replace("#fame_created_percentage_threshold#","", $ds_mensaje);
        $ds_mensaje = str_replace("#fame_deleted_percentage_threshold#","", $ds_mensaje);
        $ds_mensaje = str_replace("#fame_modified_percentage_threshold#","", $ds_mensaje);
        $ds_mensaje = str_replace("#fame_unchanged_percentage_threshold#","", $ds_mensaje);
        $ds_mensaje = str_replace("#fame_total_percentage_threshold#","", $ds_mensaje);
        $ds_mensaje = str_replace("Threshold","", $ds_mensaje);
    }

    # Nombre del template
    $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=$fl_template_email AND fg_activo='1'";
    $template = RecuperaValor($Query0);
    $subject = str_uso_normal($template[0]);
    
    # Este email es necesario
    $from = ObtenConfiguracion(107);#de donde sale el email.
    $copy_mail='noreply@myfame.org';
    # Enviamos el correo al usuario dependiendo de la accion.
    $send_email=EnviaMailHTML($from, $from, $ds_email_destinatario, $subject, $ds_mensaje,$copy_mail);
    $send_email=EnviaMailHTML($from, $from, $copy_mail, $subject, $ds_mensaje);

    EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE ds_email='$ds_email_destinatario' ");

    #Si efectivamenete se envio el email entonces se guarda la bitacora de envio
    $Query="INSERT INTO k_envio_email_reg_selfp (fg_confirmado,ds_first_name,ds_last_name,ds_email,no_registro,fg_confirmado,fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod,fl_usu_invita,fg_desbloquear_curso,fl_friends_invitation,fg_feed,fe_expiracion,ds_cupon)"; 
    $Query.="values('1','$first_name','$last_name_pat','$ds_email_destinatario','$no_codigo_confirmacion','0','S',$fl_instituto,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usuario_ins,'0',0,'','','')";
    $fl_envio_=EjecutaInsert($Query);
}

function GeneraLog($file_name_txt,$contenido_log=''){
    
    $fch= fopen($file_name_txt, "a+"); // Abres el archivo para escribir en él
    fwrite($fch, "\n".$contenido_log); // Grabas
    fclose($fch); // Cierras el archivo.
}

?>
