<?php
# Libreria de funciones
require("../lib/sp_general.inc.php");
require("../lib/sp_session.inc.php");
// require("../lib/sp_forms.inc.php");
require("lib/app_forms.inc.php");



#realiza la lectura del archivo.
 #Proceso de la carga.
$ruta_completa_archivo="Students.csv";
if ($file = fopen($ruta_completa_archivo, "r")){


    while ($data = fgetcsv ($file, 0, ",")){
        $ds_fname = $data[1];
        $ds_mname="";
        $ds_lname= $data[2];
        $ds_email= $data[4];
        $ds_a_email= $data[5];
        $ds_fname_r= $data[6];
        $ds_lname_r= $data[7];
        $ds_add_street=$data[8];

        $ds_firma=$ds_fname." ".$ds_lname;

        $clave = SP_GeneraSesion();
        //$fl_programa=6; //intro 3d character
        $fl_programa=7;  // Concept Art
        $fl_periodo=84;
        $mn_pago=100;

        $QueryS  = "INSERT INTO c_sesion ";
        $QueryS .= "(cl_sesion, fg_app_1, fg_app_2,fg_app_3,fg_app_4, fe_ultmod) ";
        echo$QueryS .= "VALUES ('".$clave."', '1','1','1','1',CURRENT_TIMESTAMP)";
        $fl_sesion = EjecutaInsert($QueryS);



        echo"<br><br>";

        $Queryf1  = 'INSERT INTO k_ses_app_frm_1 ';
        $Queryf1 .= '(cl_sesion, fl_programa, fl_periodo, ds_fname, ds_mname, ds_lname, ds_email, ds_number, fe_ultmod, cl_recruiter, fg_email) ';
        $Queryf1 .= 'VALUES ("'.$clave.'", '.$fl_programa.', '.$fl_periodo.', "'.$ds_fname.'", "'.$ds_mname.'", "'.$ds_lname.'", "'.$ds_email.'", "'.$ds_number.'", CURRENT_TIMESTAMP, 0, "1")';
        EjecutaQuery($Queryf1);

        if($fl_programa==6){
            $Query ="UPDATE k_ses_app_frm_1 SET ds_add_number='116',ds_add_state='Pennsylvania', ds_add_city='Sarver',ds_add_zip='16055',ds_eme_fname='Jeremy',ds_eme_lname='Gugino',ds_eme_number='724-816-5560',ds_eme_relation='Other..',ds_number='7249612060', ds_alt_number='7249612060',fg_ori_ref='0',fg_email_end='1', cl_recruiter=470,fl_immigrations_status=5, ds_link_to_portfolio='CCA Summer Camp Intro 3D Animation',fg_gender='F',fg_ori_via='A', ds_ruta_foto='Kayla_Gugino_ID_3148.jpg',fg_responsable='1'  WHERE cl_sesion='$clave'  ";
            EjecutaQuery($Query);
        }
        if($fl_programa==7){
            $Query ="UPDATE k_ses_app_frm_1 SET ds_add_number='116',ds_add_state='PA', ds_add_city='Sarver',ds_add_zip='16055',ds_eme_fname='Jeremy',ds_eme_lname='Gugino',ds_eme_number='724-816-5560',ds_eme_relation='Other..',ds_number='7249612060', ds_alt_number='7249612060',fg_ori_ref='0',fg_email_end='1', cl_recruiter=470,fl_immigrations_status=5, ds_link_to_portfolio='CCA Summer Camp Intro Concept Art',fg_gender='F',fg_ori_via='C', ds_ruta_foto='Kayla_Gugino_ID_3148.jpg',fg_responsable='1'  WHERE cl_sesion='$clave'  ";
            EjecutaQuery($Query);
        }

        $Query  = "SELECT mn_app_fee_internacional, mn_tuition_internacional, mn_costs_internacional, ds_costs_internacional, ";
        $Query .=" mn_a_due_internacional, mn_a_paid_internacional, mn_b_due_internacional, mn_b_paid_internacional, mn_c_due_internacional, mn_c_paid_internacional, mn_d_due_internacional, mn_d_paid_internacional, cl_type, no_semanas ";
        $Query .= "FROM k_programa_costos ";
        $Query .= "WHERE fl_programa=$fl_programa";
        $row = RecuperaValor($Query);
        $mn_app_fee = $row[0];
        if(empty($mn_app_fee))
            $mn_app_fee = 0.0;
        $mn_tuition = $row[1];
        if(empty($mn_tuition))
            $mn_tuition = 0.0;
        $mn_costs = $row[2];
        if(empty($mn_costs))
            $mn_costs = 0.0;
        $ds_costs = $row[3];
        $mn_a_due = $row[4];
        if(empty($mn_a_due))
            $mn_a_due = 0.0;
        $mn_a_paid = $row[5];
        if(empty($mn_a_paid))
            $mn_a_paid = 0.0;
        $mn_b_due = $row[6];
        if(empty($mn_b_due))
            $mn_b_due = 0.0;
        $mn_b_paid = $row[7];
        if(empty($mn_b_paid))
            $mn_b_paid = 0.0;
        $mn_c_due = $row[8];
        if(empty($mn_c_due))
            $mn_c_due = 0.0;
        $mn_c_paid = $row[9];
        if(empty($mn_c_paid))
            $mn_c_paid = 0.0;
        $mn_d_due = $row[10];
        if(empty($mn_d_due))
            $mn_d_due = 0.0;
        $mn_d_paid = $row[11];
        if(empty($mn_d_paid))
            $mn_d_paid = 0.0;
        $cl_type = $row[12];
        $no_semanas = $row[13];
        $mn_tot_tuition = $mn_tuition + $mn_costs;
        $mn_tot_program = $mn_tot_tuition + $mn_app_fee;

        # Obtenemos el numero de contratos por programa
        $meses_maximo = ObtenConfiguracion(92); // Agregar en configuracion
        $meses_x_contrato = 48; // Agregar en configuracion
        
        # Obtenemos los numeros de contratos que deben tener
        $no_contratos_ceil = ceil($no_semanas/$meses_x_contrato);
        # Obtenemos los contratos que son de 12 meses
        $no_contratos_floor = floor($no_semanas/$meses_x_contrato);

        for($i = 1; $i <= $no_contratos_ceil; $i++) {
            # Obtenemos el numero de meses que cubre el contrato
            # Si el curso dura menos o igual a 18 los meses son lo que semanas entre 4
            # Si el curso dura mas de 18 es mas de un contrato
            # Son 12 meses por contrato a excepcion del ultimo
            # Ejemplo curso dura 30 meses son 2 meses de 12 y un ocntrato de 6 meses
            if($no_semanas <= $meses_maximo){
                $weeks_contrato = round($no_semanas);
            }
            else{
                if($i<=$no_contratos_floor)
                    $weeks_contrato = $meses_x_contrato;
                else{
                    $row3 = RecuperaValor("SELECT SUM( no_weeks ) FROM k_app_contrato WHERE cl_sesion = '$clave' AND no_contrato <$i"); 
                    $weeks_contrato = $no_semanas-$row3[0];
                }
            }
            $Query  = "INSERT INTO k_app_contrato ";
            $Query .= "(cl_sesion, no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_tot_tuition, mn_tot_program, mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
            $Query .= "ds_p_name, ds_education_number, ds_usual_name, no_weeks,fg_payment,mn_discount,ds_discount ) ";
            $Query .= "VALUES('$clave', 1, $mn_app_fee, $mn_tuition, $mn_costs, '$ds_costs', $mn_tot_tuition, $mn_tot_program, $mn_a_due, $mn_a_paid, $mn_b_due, $mn_b_paid, $mn_c_due, $mn_c_paid, $mn_d_due, $mn_d_paid, ";
            $Query .= "'$ds_p_name', '$ds_education_number', ";
            if($fl_programa==7){//concep art 
            $Query .= " '$ds_usual_name', $weeks_contrato,'O',399,'High school agreement' ) ";
            }
            if($fl_programa==6){//intro 3d
                $Query .= " '$ds_usual_name', $weeks_contrato,'O',399,'High school agreement' ) ";
            }
            EjecutaQuery($Query);
            $Query_contrato=$Query;
        }
        

        $Query="UPDATE k_app_contrato SET ";
        $Query.="mn_app_fee=$mn_app_fee,mn_tuition=$mn_tuition,mn_costs=$mn_costs,ds_costs='$ds_costs',mn_tot_tuition=$mn_tot_tuition,  ";
        $Query.="mn_tot_program=$mn_tot_program,mn_a_due=$mn_a_due, mn_a_paid=$mn_a_paid,mn_b_due=$mn_b_due ,mn_b_paid=$mn_b_paid,mn_c_due=$mn_c_due, mn_c_paid=$mn_c_paid,mn_d_due=$mn_d_due,mn_d_paid=$mn_d_paid , fg_aplicar_international='1',fg_international='1' ";
        $Query.="WHERE cl_sesion='".$clave."' ";
        EjecutaQuery($Query);

        if($fl_programa==7){ //concep art
            $Query="UPDATE k_app_contrato SET  mn_tot_tuition=100,mn_a_due=100,mn_a_paid=100, mn_tot_program=100 ";
            $Query.="WHERE cl_sesion='".$clave."' ";
            EjecutaQuery($Query);
        }

        if($fl_programa==6){ //3d
            $Query="UPDATE k_app_contrato SET mn_tot_tuition=100,mn_a_due=100,mn_a_paid=100, mn_tot_program=100 ";
            $Query.="WHERE cl_sesion='".$clave."' ";
            EjecutaQuery($Query);
        }

        $Queryf1 = 'UPDATE k_ses_app_frm_1 SET ds_eme_relation_other="Spouse", fg_disability="0",ds_add_country="226",ds_eme_country=226,   ds_add_street="'.$ds_add_street.'" WHERE cl_sesion="'.$clave.'" ';
        EjecutaQuery($Queryf1);

        $Query_respon  = 'INSERT INTO k_presponsable (fg_email,cl_sesion,ds_fname_r,ds_lname_r,ds_email_r,ds_aemail_r,ds_pnumber_r, ds_relation_r, ds_relation_r_other) ';
        $Query_respon .= 'VALUES ("1","'.$clave.'", "'.$ds_fname_r.'", "'.$ds_lname_r.'", "'.$ds_a_email.'", "'.$ds_aemail_r.'", "7249612060", "Other..", "Supervisor") ';
        EjecutaQuery($Query_respon);

        #Actualizmos el fl_class_time
        $Query="UPDATE k_app_contrato SET fl_class_time=386 WHERE cl_sesion='$clave' ";
        EjecutaQuery($Query);


        if($fl_programa==6){
            $ds_resp_1="When I was looking into finding animation schools for my students, many other schools weren&#039;t permitting students in the 6th-12th grade to participate, however, VANAS did.";
            $ds_resp_2="Virtual Art is inspiring to our students because it helps them to express their creativity in a setting that is both comfortable and accommodating.";
            $ds_resp_3="5";
            $ds_resp_4="no";
            $ds_resp_5="To learn something new";
            $ds_resp_6="from a very young age";
            $ds_resp_7="All of it";
        }
        if($fl_programa==7){
            $ds_resp_1="I researched many other animation schools for my students and no one would accommodate a group younger than college virtually.";
            $ds_resp_2="N/A";
            $ds_resp_3="5";
            $ds_resp_4="no";
            $ds_resp_5="N/A";
            $ds_resp_6="N/A";
            $ds_resp_7="N/A";
        }

        $Queryf2  = 'INSERT INTO k_ses_app_frm_2 ';
        $Queryf2 .= '(cl_sesion, ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, fe_ultmod) ';
        $Queryf2 .= 'VALUES ("'.$clave.'", ';
        $Queryf2 .= ' "'.$ds_resp_1.'", "'.$ds_resp_2.'", "'.$ds_resp_3.'", "'.$ds_resp_4.'", "'.$ds_resp_5.'", "'.$ds_resp_6.'", "'.$ds_resp_7.'", CURRENT_TIMESTAMP)';
        EjecutaQuery($Queryf2);
        

        $fg_resp_1_1="1";
        $fg_resp_1_2="1";
        $fg_resp_1_3="1";
        $fg_resp_1_4="1";
        $fg_resp_1_5="1";
        $fg_resp_1_6="1";
        $fg_resp_2_1="1";
        $fg_resp_2_2="1";
        $fg_resp_2_3="1";
        $fg_resp_2_4="1";
        $fg_resp_2_5="1";
        $fg_resp_2_6="1";
        $fg_resp_2_7="1";
        $fg_resp_3_1="1";
        $fg_resp_3_2="1";

        $Queryf4  = 'INSERT INTO k_ses_app_frm_4 ';
        $Queryf4 .= '(cl_sesion, fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6,fg_resp_2_1,fg_resp_2_2,fg_resp_2_3,fg_resp_2_4,fg_resp_2_5,fg_resp_2_6,fg_resp_2_7,fg_resp_3_1,fg_resp_3_2, fe_ultmod) ';
        $Queryf4 .= 'VALUES ("'.$clave.'", ';
        $Queryf4 .= ' "'.$fg_resp_1_1.'", "'.$fg_resp_1_2.'", "'.$fg_resp_1_3.'", "'.$fg_resp_1_4.'", "'.$fg_resp_1_5.'", "'.$fg_resp_1_6.'","'.$fg_resp_2_1.'","'.$fg_resp_2_2.'","'.$fg_resp_2_3.'","'.$fg_resp_2_4.'","'.$fg_resp_2_5.'","'.$fg_resp_2_6.'","'.$fg_resp_2_7.'","'.$fg_resp_3_1.'","'.$fg_resp_3_2.'", CURRENT_TIMESTAMP) ';
        EjecutaQuery($Queryf4);

        if($fl_programa==6){
            $ds_resp_1_f3="I expect that our students have a wonderful experience and walk away with a head start in their future endeavors.";
            $ds_resp_2_1_f3="Learn";
            $ds_resp_2_2_f3="Expand";
            $ds_resp_2_3_f3="Conquer";
            $ds_resp_3_f3="3D Animation";
            $ds_resp_4_f4="N/A";
            $ds_resp_5_f5="N/A";
            $ds_resp_6_f6="C";
            $ds_resp_7_f7="C";
            $ds_resp_8_f8="N/A";
        }
        if($fl_programa==7){
            $ds_resp_1_f3="N/A";
            $ds_resp_2_1_f3="N/A";
            $ds_resp_2_2_f3="N/A";
            $ds_resp_2_3_f3="N/A";
            $ds_resp_3_f3="Concept Art";
            $ds_resp_4_f4="N/A";
            $ds_resp_5_f5="N/A";
            $ds_resp_6_f6="C";
            $ds_resp_7_f7="C";
            $ds_resp_8_f8="N/A";
        }
        $Queryf3  = 'INSERT INTO k_ses_app_frm_3 ';
        $Queryf3 .= '(cl_sesion, ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3,ds_resp_4,ds_resp_5,ds_resp_6,ds_resp_7,ds_resp_8, fe_ultmod) ';
        $Queryf3 .= 'VALUES ("'.$clave.'", ';
        $Queryf3 .= ' "'.$ds_resp_1_f3.'", "'.$ds_resp_2_1_f3.'", "'.$ds_resp_2_2_f3.'", "'.$ds_resp_2_3_f3.'", "'.$ds_resp_3_f3.'", "'.$ds_resp_4_f4.'","'.$ds_resp_5_f5.'","'.$ds_resp_6_f6.'","'.$ds_resp_7_f7.'","'.$ds_resp_8_f8.'", CURRENT_TIMESTAMP)';
        EjecutaQuery($Queryf3);

        $Query  = "UPDATE c_sesion SET fg_paypal='1', fg_confirmado='1', fg_pago='1',mn_pagado=100, fe_ultmod=CURRENT_TIMESTAMP ";
        $Query .= "WHERE cl_sesion='$clave' ";
        EjecutaQuery($Query);

        $Query  = "UPDATE c_sesion SET cl_metodo_pago='1', fe_pago=CURRENT_TIMESTAMP ";
        $Query .= "WHERE cl_sesion='$clave' ";
        EjecutaQuery($Query);

        #Se genera su contrato.

        # Recupera datos de la sesion
        $Query  = "SELECT cl_sesion, fg_app_1 ";
        $Query .= "FROM c_sesion ";
        $Query .= "WHERE fl_sesion=$fl_sesion";
        $row = RecuperaValor($Query);
        $cl_sesion = $row[0];


        #Recuperamos Nombre del programa y fecha de inicio
        $Query="SELECT nb_programa FROM c_programa where fl_programa=$fl_programa ";
        $row=RecuperaValor($Query);
        $nb_programa=$row['nb_programa'];

        #Recuperamos el periodo.
        $Query="SELECT nb_periodo FROM c_periodo WHERE fl_periodo=$fl_periodo  ";
        $row=RecuperaValor($Query);
        $nb_periodo=$row['nb_periodo'];

        $opc_pago=1;
        # Obtenemos la frecuencia que haya seleccionado
        switch($opc_pago){
            CASE 1: $frecuencia = 'ds_a_freq'; break;
            CASE 2: $frecuencia = 'ds_b_freq'; break;
            CASE 3: $frecuencia = 'ds_c_freq'; break;
            CASE 4: $frecuencia = 'ds_d_freq'; break;
        }
        $row = RecuperaValor("SELECT $frecuencia FROM k_programa_costos WHERE fl_programa=$fl_programa ");
        $ds_frecuencia =$row[0];


        # Actualiza la forma de pago para todos los contratos relacionados al estudiante
        $Query  = "UPDATE k_app_contrato ";
        $Query .= "SET fg_opcion_pago=$opc_pago, ds_frecuencia='$ds_frecuencia' ";
        # Guardamos el metodo de pago  
        $Query .= "WHERE cl_sesion='$cl_sesion'";
        EjecutaQuery($Query);

        $no_contrato = substr($clave, 8, 1);

        //if(empty($no_contrato))
        $no_contrato=1;
        # Guarda la fecha, firmas del estudiante y representante legal y contenido del contrato
        $Query  = "UPDATE k_app_contrato ";
        $Query .= "SET fe_firma=CURRENT_TIMESTAMP, ds_firma_alumno='$ds_firma' ";  
        $Query .= "WHERE cl_sesion='$cl_sesion' ";
        $Query .= "AND no_contrato=$no_contrato ";
        EjecutaQuery($Query);
        
        //echo"fl_sesion=$fl_sesion ";
        //echo"<br><br>";
        $ds_encabezado = htmlentities(genera_documento($fl_sesion, 1, False, False, $no_contrato));
        //echo"<br><br>";
        $ds_cuerpo = htmlentities(genera_documento($fl_sesion, 2, False, False, $no_contrato));
        //echo"<br><br>";
        $ds_pie = htmlentities(genera_documento($fl_sesion, 3, False, False, $no_contrato));
        //echo"<br><br>";
        
        //if($fl_programa==6){
         //   $ds_cuerpo=str_replace("399","100",$ds_cuerpo);
        //}
        //if($fl_programa==7){
         //   $ds_cuerpo=str_replace("399","100",$ds_cuerpo);
        //}


        # Guarda el contenido del contrato
        $Query  = "UPDATE k_app_contrato ";
        $Query .= "SET ds_header='$ds_encabezado', ds_contrato='$ds_cuerpo', ds_footer='$ds_pie' ";
        $Query .= "WHERE cl_sesion='$cl_sesion' ";
        $Query .= "AND no_contrato=$no_contrato ";
        EjecutaQuery($Query);
        
        $dominio_campus = ObtenConfiguracion(60);

        # Genera una nueva clave para la liga de acceso al contrato
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $ds_cve = date("Ymd").$no_contrato;
        for($i = 0; $i < 10; $i++)
            $ds_cve .= substr($str, rand(0,62), 1);
        $ds_cve .= $clave;

        # Actualiza datos de costos para el contrato
        $Query  = "UPDATE k_app_contrato ";
        $Query .= "SET ds_cadena='$ds_cve' ,link_contract='https://".$dominio_campus."/contract_frm.php?c=$ds_cve' ";
        echo$Query .= "WHERE cl_sesion='$cl_sesion' ";
        //$Query .= "AND no_contrato=$no_contrato ";
        EjecutaQuery($Query);

        
        $Query="SELECT fl_term FROM k_term WHERE fl_periodo=$fl_periodo AND fl_programa=$fl_programa ";
        $row=RecuperaValor($Query);
        $fl_term=$row['fl_term'];

        $Query="SELECT fl_term_pago FROM k_term_pago WHERE fl_term=$fl_term AND no_opcion=1 ";
        $row=RecuperaValor($Query);
        $fl_term_pago=$row['fl_term_pago'];


        echo$Query="INSERT INTO k_ses_pago(cl_sesion,fl_term_pago,cl_metodo_pago,fe_pago,mn_pagado)
                VALUES('$cl_sesion',$fl_term_pago,'1',CURRENT_TIMESTAMP,100)";
        EjecutaQuery($Query);


    }




}



echo $Query_contrato;



?>