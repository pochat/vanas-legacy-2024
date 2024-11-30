<?php

# Libreria de funciones
 require("../../common/lib/cam_general.inc.php");
 # Librerias para el pdf
  require_once('../../../AD3M2SRC4/lib/tcpdf/config/lang/eng.php');
  require_once('../../../AD3M2SRC4/lib/tcpdf/tcpdf.php');


  # Send notification email to the student that has a grade assigned
  # Email Library
  #MJD PHP MAILER 6.5
  if (PHP_OS=='Linux') { # when is production
      require('/var/www/html/AWS_SES/PHP/com_email_func.inc.php');
  }else{
      require($_SERVER['DOCUMENT_ROOT'].'/AWS_SES/PHP/com_email_func.inc.php');

  }



  # Load AWS class
if (PHP_OS == 'Linux') { # when is production
    require('/var/www/html/AWS_SES/aws/aws-autoloader.php');
} else {
    require($_SERVER['DOCUMENT_ROOT'] . '/AWS_SES/aws/aws-autoloader.php');
}
  use Aws\Common\Aws;

  # Include html parser
if (PHP_OS == 'Linux') { # when is production
    require('/var/www/html/vanas/modules/common/new_campus/lib/simple_html_dom.php'); // produccion
} else {
    require($_SERVER['DOCUMENT_ROOT'] . '/modules/common/new_campus/lib/simple_html_dom.php');

}


# Initialize Amazon Web
if (PHP_OS == 'Linux') { # when is production
    $aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');
} else {

   // $aws = Aws::factory($_SERVER['DOCUMENT_ROOT'] . '/AWS_SES/PHP/config.inc.php');
}
  # Get the client
#  $client = $aws->get('Ses');
  $client = "ses";
  # Initialize the sender address
  $from = 'noreply@vanas.ca';





$fl_criterio=RecibeParametroNumerico('fl_criterio');
$ds_comentarios=RecibeParametroHTML('ds_descripcion');
$no_calificacion=RecibeParametroNumerico('no_calificacion');
$no_grado=RecibeParametroNumerico('no_grado');#Term
$fl_alumno=RecibeParametroNumerico('fl_alumno');
$fl_leccion=RecibeParametroNumerico('fl_leccion');
$fl_programa=RecibeParametroNumerico('fl_programa');
$es_comentario_final= RecibeParametroNumerico('comen_final');
$fg_comentario_criterio=RecibeParametroNumerico('fg_comentario_crietrio');
$fg_guardar_todo=RecibeParametroNumerico('fg_guardar_todo');
$ds_comentario_final_teacher=RecibeParametroHTML('ds_comentarios');
$rangeInput=RecibeParametroNumerico('rangeInput');
$increse_grade = RecibeParametroNumerico('increse_grade');
$fg_increase_grade = RecibeParametroBinario('fg_increase_grade');
$fl_entrega_semanal = RecibeParametroNumerico('fl_entrega_semanal');
$fl_semana=RecibeParametroNumerico('fl_semana');
$fl_grupo=RecibeParametroNumerico('fl_grupo');
$fg_calificado=RecibeParametroBinario('fg_calificado');
$fl_maestro = ValidaSesion(False); # Verifica que exista una sesion valida en el cookie y la resetea










    #Ejecura acciones si son comentarios de cada criterio
    if($fg_comentario_criterio==1){







	        #Verificamos si existe
        $Query="SELECT fl_criterio FROM c_com_criterio_teacher_campus WHERE fl_criterio=$fl_criterio AND fl_alumno=$fl_alumno AND fl_leccion=$fl_leccion AND no_grado='$no_grado'  AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo   ";
	        $row=RecuperaValor($Query);
	        $existe=$row[0];

	            if($existe){

		            $Query="UPDATE c_com_criterio_teacher_campus SET ds_comentarios='$ds_comentarios',fe_modificacion=CURRENT_TIMESTAMP ";
                    if(!empty($rangeInput))
                    $Query.=", no_porcentaje_equivalente=$rangeInput ";
                    $Query.="WHERE fl_criterio=$fl_criterio AND fl_alumno=$fl_alumno AND fl_leccion=$fl_leccion AND no_grado='$no_grado' AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo  ";
		            EjecutaQuery($Query);

	                    #Recuperamos la fecha de modificacion para presentarla
                    $Query="SELECT fe_modificacion FROM c_com_criterio_teacher_campus WHERE fl_criterio=$fl_criterio AND fl_alumno=$fl_alumno  AND fl_leccion=$fl_leccion AND no_grado='$no_grado'  AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo  ";
			            $row=RecuperaValor($Query);
			            $fe_modificacion=$row[0];

			            $fe_modificacion=strtotime('+0 day',strtotime($fe_modificacion));
			            $fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
			            #DAMOS FORMATO DIA,MES, AÑO.
			            $date = date_create($fe_modificacion);
			            $fe_modificacion=date_format($date,'F j , Y , g:i a');



	            }else{
			            #Eliminamos el comentario del teacher
                    EjecutaQuery("DELETE FROM c_com_criterio_teacher_campus WHERE fl_criterio=$fl_criterio AND fl_alumno=$fl_alumno  AND fl_leccion=$fl_leccion AND no_grado='$no_grado'  AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo  ");



			            #Recupermos el peso que tiene el criterio.
			            $Query="SELECT no_valor
                                FROM k_criterio_programa a
                                JOIN c_criterio b ON b.fl_criterio=a.fl_criterio
                                WHERE b.fl_criterio=$fl_criterio AND fl_programa=$fl_leccion  ";
			            $row=RecuperaValor($Query);
			            $no_porcentaje=$row[0];

			            $no_porcentaje_criterio= ( $no_porcentaje * $rangeInput ) /100 ;

			            #Inserta comentarios de cada criterio.
			            $Query="INSERT INTO c_com_criterio_teacher_campus (fl_criterio,no_porcentaje_equivalente,ds_comentarios,fl_alumno,fl_programa,fl_leccion,no_grado,fl_semana,fl_grupo,fe_creacion,fe_modificacion)";
			            $Query.="VALUES ($fl_criterio,$rangeInput,'$ds_comentarios',$fl_alumno,$fl_programa,$fl_leccion,'$no_grado',$fl_semana,$fl_grupo, CURRENT_TIMESTAMP,CURRENT_TIMESTAMP )";
			            $fl_comentario=EjecutaInsert($Query);

			            #Recuperamos la fecha de modificacion para presentarla
			            $Query="SELECT fe_modificacion FROM c_com_criterio_teacher_campus WHERE fl_comentario_teacher= $fl_comentario ";
			            $row=RecuperaValor($Query);
			            $fe_modificacion=$row[0];

			            $fe_modificacion=strtotime('+0 day',strtotime($fe_modificacion));
			            $fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
			            #DAMOS FORMATO DIA,MES, AÑO.
			            $date = date_create($fe_modificacion);
			            $fe_modificacion=date_format($date,'F j , Y , g:i a');
                   }






    }

    if($es_comentario_final==1){#Quiere decir que es comentario final de la rubric.


        #Eliminamos el comentario del teacher
        EjecutaQuery("DELETE FROM c_com_criterio_teacher_campus WHERE  fl_alumno=$fl_alumno AND no_grado='$no_grado'  AND fl_leccion=$fl_leccion AND fg_com_final='1' AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo  ");


        #Inserta comentario final del teacher.
        $Query="INSERT INTO c_com_criterio_teacher_campus (ds_comentarios,fl_alumno,fl_programa,no_grado,fl_leccion,fe_creacion,fe_modificacion,fg_com_final,fl_semana,fl_grupo )";
        $Query.="VALUES ('$ds_comentario_final_teacher',$fl_alumno,$fl_programa,'$no_grado',$fl_leccion,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'1',$fl_semana,$fl_grupo )";
        $fl_comentario_teacher=EjecutaInsert($Query);

        #Recuperamos la fecha de modificacion para presentarla
        $Query="SELECT fe_modificacion FROM c_com_criterio_teacher_campus WHERE fl_comentario_teacher= $fl_comentario_teacher ";
        $row=RecuperaValor($Query);
        $fe_modificacion=$row[0];

        $fe_modificacion=strtotime('+0 day',strtotime($fe_modificacion));
        $fe_modificacion= date('Y-m-d H:i:s',$fe_modificacion );
        #DAMOS FORMATO DIA,MES, AÑO.
        $date = date_create($fe_modificacion);
        $fe_modificacion=date_format($date,'F j , Y , g:i a');


    }

    if($fg_guardar_todo==1){#Quiere decir que se guarda ya todo la calificacion asignada del teacher


        #Verificamos si existen comentarios del teacher por cada criterio., entonces recorremos todas las calificaciones asignadas.
        $Queryc="SELECT fl_criterio,no_porcentaje_real FROM c_calculo_criterio_temp_campus WHERE fl_alumno=$fl_alumno AND fl_leccion=$fl_leccion AND no_grado='$no_grado' AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo     ORDER BY fl_criterio ASC  ";
        $rs2 = EjecutaQuery($Queryc);
        for($i=1;$row2=RecuperaRegistro($rs2);$i++){

              $fl_criterio_in=$row2['fl_criterio'];
              $no_porcentaje_in=$row2['no_porcentaje_real'];


                 #Verificamos si existe un comentario asignado del teacher por cada calificacion(rangeInput).
              $Queryb="SELECT fl_comentario_teacher FROM c_com_criterio_teacher_campus WHERE fl_criterio=$fl_criterio_in AND  fl_alumno=$fl_alumno AND fl_leccion=$fl_leccion AND no_grado='$no_grado' AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo  ";
                 $rowb=RecuperaValor($Queryb);
                 $ds_comentario_teacher=$rowb[0];

                 #Si no existe el cometario inserta un comentario vacio
                 if(empty($ds_comentario_teacher)){


                     #Inserta comentarios de cada criterio.
                     $Query="INSERT INTO c_com_criterio_teacher_campus (fl_criterio,no_porcentaje_equivalente,ds_comentarios,fl_alumno,fl_programa,fl_leccion,no_grado,fl_grupo,fl_semana,fe_creacion,fe_modificacion)";
                     $Query.="VALUES ($fl_criterio_in,$no_porcentaje_in,'',$fl_alumno,$fl_programa,$fl_leccion,'$no_grado',$fl_grupo,$fl_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP )";
                     $fl_comentario=EjecutaInsert($Query);

                 }

        }


        #Verificamos si existe un comentario general del  teacher.
        $Queryc="SELECT fl_comentario_teacher FROM c_com_criterio_teacher_campus WHERE fl_alumno=$fl_alumno AND fl_leccion=$fl_leccion AND no_grado='$no_grado' AND fl_programa=$fl_programa    AND fg_com_final='1'  AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo  ";
        $rowc=RecuperaValor($Queryc);
        $ds_comentario_final_teacher=$rowc[0];
        if(empty($ds_comentario_final_teacher)){

            #Inserta comentario final del teacher.
            $Query="INSERT INTO c_com_criterio_teacher_campus (ds_comentarios,fl_alumno,fl_programa,fl_leccion,fl_semana,fl_grupo,fe_creacion,no_grado,fe_modificacion,fg_com_final)";
            $Query.="VALUES ('',$fl_alumno,$fl_programa,$fl_leccion,$fl_semana,$fl_grupo,CURRENT_TIMESTAMP,'$no_grado',CURRENT_TIMESTAMP,'1' )";
            $fl_comentario_final_teacher=EjecutaInsert($Query);

        }



        #Eliminamos el comentario del teacher.
        EjecutaQuery("DELETE FROM k_calificacion_teacher_campus WHERE  fl_alumno=$fl_alumno AND no_grado='$no_grado'  AND fl_leccion=$fl_leccion AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo  ");





        #Sumamos todos los que pernetencen al alumno, programa y leccion.
        $Query ="SELECT SUM(no_porcentaje) FROM c_calculo_criterio_temp_campus  WHERE fl_alumno=$fl_alumno AND no_grado='$no_grado' AND fl_leccion=$fl_leccion AND fl_programa=$fl_programa AND fl_semana=$fl_semana AND fl_grupo=$fl_grupo  ";
        $row=RecuperaValor($Query);
        $no_calificacion=$row[0];






            $Query="INSERT INTO k_calificacion_teacher_campus (fl_alumno,fl_programa,fl_leccion,ds_comentarios,no_calificacion,no_grado,fl_grupo,fl_semana) ";
            $Query.="VALUES($fl_alumno,$fl_programa,$fl_leccion,'$ds_comentario_final_teacher',$no_calificacion,$no_grado,$fl_grupo,$fl_semana)";
            $fl_calificacion_teacher=EjecutaInsert($Query);


            #Verificamos en que rango se encuentra y se le asigna su promedio
            $Query="SELECT fl_calificacion, no_min,no_max,fg_aprobado  FROM c_calificacion WHERE 1=1 ";
            $rs=EjecutaQuery($Query);

            for($i=1;$row=RecuperaRegistro($rs);$i++){
                $no_min=$row[1];
                $no_max=$row[2];

                if( ($no_calificacion>=$no_min) && ($no_calificacion<=$no_max)){

                        $fl_promedio=$row[0];
                        $fg_aprobado=$row['fg_aprobado'];

                }

            }

            #Actualizamos el promedio
            $Query="UPDATE k_entrega_semanal SET fl_promedio_semana=$fl_promedio  WHERE fl_alumno=$fl_alumno AND fl_entrega_semanal=$fl_entrega_semanal ";
            EjecutaQuery($Query);


			#Actualizamos la fecha de odificacion
			$Query="UPDATE c_com_criterio_teacher_campus SET fe_modificacion=CURRENT_TIMESTAMP WHERE fl_alumno=$fl_alumno AND fl_leccion=$fl_leccion AND fl_programa=$fl_programa AND no_grado='$no_grado' AND fg_com_final='1' ";
			EjecutaQuery($Query);


            /**
             *MJD los criterios pasara a ser solo registro especifico por alumno , ya que el ADMIN puede elimnar criterios del programa y volverlos a restructurar, es por estarazon que se guardade como se califico al alumno(se congela calif del alumno).
             *
             */
            /**********Guardamos la calificacion congelada del alumno.****************/


            if($fg_calificado==1){

                        #//
                        // EjecutaQuery("DELETE FROM k_criterio_programa_alumno WHERE fl_alumno=$fl_alumno AND fl_criterio=$fl_criterio_existe AND fl_programa=$fl_programa ");
                        //$Query="SELECT fl_criterio,fl_programa,no_valor,no_orden FROM k_criterio_programa_alumno WHERE fl_programa=$fl_leccion AND fl_alumno=$fl_alumno AND fl_criterio=".$rowM['fl_criterio']."  ";
                        //$rsM=EjecutaQuery($QueryM);
                        //for($m=1;$rowM=RecuperaRegistro($rsM);$m++){
                        //Genera registros por alumno y programa.
                        //    $QueryN="INSERT INTO k_criterio_programa_alumno (fl_criterio,fl_programa,no_valor,no_orden,fl_alumno)";
                        //    $QueryN.="VALUES(".$rowM['fl_criterio'].",".$rowM['fl_programa'].",".$rowM['no_valor'].",".$rowM['no_orden'].",".$fl_alumno.")";
                        //    EjecutaQuery($QueryN);
                       // }




            }else{


                         #Volvemos a generar nuevos registros.
                         $QueryM="SELECT fl_criterio,fl_programa,no_valor,no_orden FROM k_criterio_programa WHERE fl_programa=$fl_leccion ";
                         $rsM=EjecutaQuery($QueryM);
                         for($m=1;$rowM=RecuperaRegistro($rsM);$m++){

                                #Genera registros por alumno y programa.
                                $QueryN="INSERT INTO k_criterio_programa_alumno (fl_criterio,fl_programa,no_valor,no_orden,fl_alumno)";
                                $QueryN.="VALUES(".$rowM['fl_criterio'].",".$rowM['fl_programa'].",".$rowM['no_valor'].",".$rowM['no_orden'].",".$fl_alumno.")";
                                EjecutaQuery($QueryN);
                         }



            }




            /*********End Save****************/




        	 #Redirige a tab1.
			  echo"<script>
			        $(document).ready(function () {

						 $('#cerrar').click ();
						    //le asignamo un retardo para confirmar nuestro pago
						    setTimeout(function(){
						    location.reload();
								RequestPending('p_grade');
						}, 3000);

				    });
				</script>
				";


				  #Se envia notificacion via email al estudiante y teacher.
              /*********************************************************************************************************************************/

              $fl_calificacion = $fl_promedio;

                  # Prepare Email Template
                  $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template=12 AND fg_activo='1'";
                  $grade_template = RecuperaValor($Query);
                  $ds_template = str_uso_normal($grade_template[0].$grade_template[1].$grade_template[2]);

                  # Create a DOM object
                  //$ds_template_html = new simple_html_dom();

                  # Teacher's info
                  $Query = "SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_maestro";
                  $row = RecuperaValor($Query);
                  $te_fname = $row[0];
                  $te_lname = $row[1];


                  # Lesson info
                  $Query  = "SELECT b.ds_nombres, b.ds_apaterno, d.no_semana, d.ds_titulo, b.ds_email ";
                  $Query .= ",e.ds_add_number, e.ds_add_street, e.ds_add_city, e.ds_add_state, e.ds_add_zip, f.ds_pais, a.fl_alumno, g.nb_programa, b.cl_sesion, a.fl_semana ";
                  $Query .= "FROM k_entrega_semanal a ";
                  $Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno ";
                  $Query .= "LEFT JOIN k_semana c ON c.fl_semana=a.fl_semana ";
                  $Query .= "LEFT JOIN c_leccion d ON d.fl_leccion=c.fl_leccion ";
                  $Query .= "LEFT JOIN k_ses_app_frm_1 e ON e.cl_sesion=b.cl_sesion ";
                  $Query .= "LEFT JOIN c_pais f ON e.ds_add_country=f.fl_pais ";
                  $Query .= "LEFT JOIN c_programa g ON g.fl_programa=e.fl_programa ";
                  $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
                  $Query .= "AND b.fg_activo='1' ";
                  $row = RecuperaValor($Query);
                  $st_fname = $row[0];
                  $st_lname = $row[1];
                  $no_week = $row[2];
                  $ds_title = $row[3];
                  $ds_email = $row[4];
                  $ds_add_number = $row[5];
                  $ds_add_street = $row[6];
                  $ds_add_city = $row[7];
                  $ds_add_state = $row[8];
                  $ds_add_zip = $row[9];
                  $ds_add_country = $row[10];
                  $st_lmadd = $ds_add_number." ".$ds_add_street.", ".$ds_add_city." ".$ds_add_state.",".$ds_add_country;
                  $fl_alumno = $row[11];
                  $nb_programa = str_texto($row[12]);
                  $cl_sesion = str_texto($row[13]);
                  # Obtenemos el flsesion del alumno
                  $rowsesion = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='$cl_sesion' ");
                  $fl_sesion = $rowsesion[0];
                  $fl_semana = $row[14];

                  # Vamos actualizar los GPA terms y GPA program_gpa
                  $QueryT  = "SELECT SUM(i.no_equivalencia)/COUNT(a.fl_semana), a.fl_term, no_grado, c.fl_alumno ";
                  $QueryT .= "FROM k_semana a, k_term b, k_entrega_semanal c, c_calificacion i ";
                  $QueryT .= "WHERE a.fl_term=b.fl_term AND a.fl_semana=c.fl_semana AND c.fl_promedio_semana=i.fl_calificacion ";
                  $QueryT .= "AND a.fl_term IN(SELECT fl_term FROM k_alumno_term e WHERE e.fl_alumno=c.fl_alumno AND c.fl_alumno=$fl_alumno) ";
                  $QueryT .= "GROUP BY a.fl_term ";
                  $rsT = EjecutaQuery($QueryT);
                  for($i=0;$rowT=RecuperaRegistro($rsT);$i++){
                    EjecutaQuery("UPDATE k_alumno_term SET no_promedio='".$rowT[0]."' WHERE fl_alumno=$rowT[3] AND fl_term=$rowT[1] ");
                  }

                  # Obtenemos el promedio de los terms que curso el student
                  $Querypgpa  = "SELECT MAX(a.fl_term) FROM k_alumno_term a, k_term b, c_periodo c ";
                  $Querypgpa .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo ";
                  $Querypgpa .= "AND a.fl_alumno=$fl_alumno GROUP BY b.no_grado ORDER BY c.fe_inicio, b.no_grado";
                  $consulta = EjecutaQuery($Querypgpa);
                  for($k=0;$rowpgpa = RecuperaRegistro($consulta);$k++){
                    $fl_termt = $rowpgpa[0];
                    $row0 = RecuperaValor("SELECT no_promedio FROM k_alumno_term WHERE fl_term=$fl_termt AND fl_alumno=$fl_alumno");
                    if($row0[0]>0){
                      $promediopgpa++;
                      $promedio_x_t += $row0[0];
                    }
                  }
				  if($promediopgpa>0){
                  # Actuaizamos el program GPA
                  EjecutaQuery("UPDATE c_alumno SET no_promedio_t='".round($promedio_x_t/$promediopgpa)."' WHERE fl_alumno=$fl_alumno");
				  }else{
					 EjecutaQuery("UPDATE c_alumno SET no_promedio_t='0' WHERE fl_alumno=$fl_alumno");
				  }
                  # Obtenemos la calificacion del term y el grado actual
                  $QueryTerm  = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <=ROUND(no_promedio) AND no_max >=ROUND(no_promedio)), no_promedio ";
                  $QueryTerm .= "FROM k_alumno_term WHERE fl_term=".ObtenTermAlumno($fl_alumno)." AND fl_alumno=$fl_alumno";
                  $rowc = RecuperaValor($QueryTerm);
                  $cl_cal_term = $rowc[0];
                  $no_grado = ObtenGradoAlumno($fl_alumno);
                  $current_term_promedio = round($rowc[1]);
                  $current_term_gpa = $cl_cal_term." ".$current_term_promedio."%";
                  # Obtenemos el promedio general del curso
                  $QueryGPA  = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t)), ";
                  $QueryGPA .= "no_promedio_t FROM c_alumno WHERE fl_alumno=$fl_alumno ";
                  $row2 = RecuperaValor($QueryGPA);
                  $gpa_grl = $row2[0]." ".round($row2[1])."%";
                  $row3 = RecuperaValor("");
                    if(empty($gpa_grl))
                    $gpa_grl = "(No assigment)";
                  $no_promedio_t = $row2[1];

                  $variables1 = array(
                    'st_fname' => $st_fname,
                    'st_lname' => $st_lname,
                    'te_fname' => $te_fname,
                    'te_lname' => $te_lname,
                    'no_week' => $no_week,
                    'ds_title' => $ds_title,
                    'st_lmadd' => $st_lmadd,
                    'st_country' => $ds_add_country,
                    'st_lmaddpc' => $ds_add_zip,
                    'current_term_gpa' => $current_term_gpa,
                    'no_grado' => $no_grado,
                    'pg_name' => $nb_programa,
                    'program_gpa' => $gpa_grl,
                    'fl_sesion' => $fl_sesion
                  );

                  # Generate the email template with the variables
                  //$ds_email_template = GenerateTemplate($ds_template, $variables1);

                  # Load the template into html
                 // $ds_template_html->load($ds_email_template);
                  # Get base url (domain)
                 // $base_url = $ds_template_html->getElementById("login-redirect")->href;
                  # Set url path and query string
                  $component_week = "week=".$no_week;
                  $component_tab = "&tab=critique";
               //   $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/desktop.php?".$component_week.$component_tab;

                //  SendNoticeMail($client, $from, $ds_email, '', 'New Grade Assigned', $ds_template_html);

                  # Si existe algun problema solo con desactivar ya no funcionara este proceso
                  if(ObtenConfiguracion(81)){
                    # Correo para appy
                    // $apply=ObtenConfiguracion(83);
                    $apply=ObtenConfiguracion(83);

                    # Calificacion Minima aprovada
                    $reprovada  = "SELECT cl_calificacion, no_min FROM c_calificacion ";
                    $reprovada .= "WHERE no_equivalencia=(SELECT MIN(no_equivalencia) FROM c_calificacion WHERE fg_aprobado='1') ";
                    $rowr = RecuperaValor($reprovada);
                    $cl_calificacion = $rowr[0];
                    $no_equivalencia = round($rowr[1]);
                    $calificacion_min = $cl_calificacion." ".$no_equivalencia."%";

                    # Inicializa variables de ambiente para envio de correo adjunto
                    ini_set("SMTP", MAIL_SERVER);
                    ini_set("smtp_port", MAIL_PORT);
                    ini_set("sendmail_from", MAIL_FROM);

                    # Emelemtos para el envio de correos warnings
                    $eol = "\n";
                    $separator = md5(time());
                    $headers  = 'From: '.$from.' <'.$from.'>'.$eol;
                    $headers .= 'Bcc:'.$apply.'' . "\r\n";
                    $headers .= 'MIME-Version: 1.0' .$eol;
                    $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

                    // Fecha para nombre del archivo
                    $row = RecuperaValor("SELECT DATE_FORMAT(CURRENT_DATE(),'%d-%m-%Y') ");
                    $Date = $row[0];

                    # Verificacion de la calificacion si no es aprovatoria mandara un correo al alumno
                    $row1 = RecuperaValor("SELECT fg_aprobado,cl_calificacion,no_equivalencia FROM c_calificacion WHERE fl_calificacion=$fl_calificacion");
                    $aprovado = $row1[0];
                    $current_week_grade = $row1[1]." ".round($row1[2])."%";



                    if(($aprovado=='0') && $fl_calificacion>0){

                        # Template para calificaciones no aprovadas
                      $Query1 = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=30 AND fg_activo='1'";
                      $grade_warning = RecuperaValor($Query1);
                      $template_grade_warning = str_uso_normal($grade_warning[0].$grade_warning[1].$grade_warning[2]);
                      $template_warning = str_texto($grade_warning[3]);

                      $variables2 = array(
                        'st_fname' => $st_fname,
                        'st_lname' => $st_lname,
                        'te_fname' => $te_fname,
                        'te_lname' => $te_lname,
                        'no_week' => $no_week,
                        'current_week_grade' => $current_week_grade,
                        'ds_title' => $ds_title,
                        'st_lmadd' => $st_lmadd,
                        'st_country' => $ds_add_country,
                        'st_lmaddpc' => $ds_add_zip,
                        'current_term_gpa' => $current_term_gpa,
                        'no_grado' => $no_grado,
                        'pg_name' => $nb_programa,
                        'program_gpa' => $gpa_grl,
                        'fl_sesion' => $fl_sesion,
                        'fl_template' => 30,
                        'minimum_gpa' => $calificacion_min
                      );

                      # Template
                      $Hwarning = GenerateTemplate($grade_warning[0],$variables2);
                      $Bwarning = GenerateTemplate($grade_warning[1],$variables2);
                      $Fwarning = GenerateTemplate($grade_warning[2],$variables2);
                      $ds_email_warning = $Hwarning.$Bwarning.$Fwarning;

                      # PDF
                      class ConPiesweek extends TCPDF {
                        // Header
                        function Header(){
                          $this->writeHTML($Hwarning, true, 0, true, 0);
                        }
                        // Footer
                        function Footer(){
                          $this->SetY(-20);
                          $this->writeHTML($Fwarning, true, 0, true, 0);
                        }
                      }

                      // Creamos un nuevo objeto usando la clase extendida classpies
                      $pdf = new ConPiesweek();
                      $pdf->SetFont('times','',10);

                      // Add a page
                      $pdf->AddPage("P");

                      // Output the HTML content
                      $pdf->writeHTMLCell(180, 100, 10,30,$Bwarning, 0, 0, false, true,'',true);

                      // Nombre del archivo
                      $fileName = $fl_sesion."30".$Date.'.pdf';

                      // Pasamos el archivo a base64
                      $fileattweek = $pdf->Output($fileName, 'S'); //genera la codificacion para enviar adjuntado el archivo

                      // Mensaje email
                      //$messageW  = "--".$separator.$eol;
                      //$messageW .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
                      //$messageW .= $ds_email_warning.$eol;
                     // $messageW .= "--".$separator.$eol;
                     // $messageW .= $fileattweek;
                     // $messageW .= "--".$separator."--".$eol;

                      #send email:
                      $sendemail= Mailer($ds_email,$template_warning,$ds_email_warning,'',$fileattweek,$fileName,'',true);

                      if($sendemail){

                        # Insertamos los datos del email enviado parq eu se congelen
                        $QueryGrade  = "INSERT INTO k_alumno_template (fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
                        $QueryGrade .= "VALUES ($fl_sesion,30,CURRENT_TIMESTAMP,'$Hwarning','$Bwarning','$Fwarning')";
                        EjecutaQuery($QueryGrade);
                      }


                    }

                    # Verificamos si la Program GPA no es aprobatorio mandara una notification
                    $rowPGPA = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)");
                    $aprovadoPGPA = $rowPGPA[1];

                    if($aprovadoPGPA=='0'){

                      # Variables
                      $variables3 = array(
                        'st_fname' => $st_fname,
                        'st_lname' => $st_lname,
                        'te_fname' => $te_fname,
                        'te_lname' => $te_lname,
                        'no_week' => $no_week,
                        'ds_title' => $ds_title,
                        'st_lmadd' => $st_lmadd,
                        'st_country' => $ds_add_country,
                        'st_lmaddpc' => $ds_add_zip,
                        'current_term_gpa' => $current_term_gpa,
                        'no_grado' => $no_grado,
                        'pg_name' => $nb_programa,
                        'program_gpa' => $gpa_grl,
                        'minimum_gpa' => $calificacion_min
                      );

                      # Template para terms no aprovados
                      $templatePGPA = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=29 AND fg_activo='1'";
                      $rowPGPA1 = RecuperaValor($templatePGPA);
                      $template_PGPA = str_uso_normal($rowPGPA1[0].$rowPGPA1[1].$rowPGPA1[2]);
                      $template_warningPGPA = str_texto($rowPGPA1[3]);

                      # Template
                      $headerPGPA = GenerateTemplate(genera_documento($fl_sesion, 1, 29),$variables3);
                      $bodyPGPA = GenerateTemplate(genera_documento($fl_sesion, 2, 29),$variables3);
                      $footerPGPA = GenerateTemplate(genera_documento($fl_sesion, 3, 29),$variables3);
                      $ds_email_warningPGPA = $headerPGPA.$bodyPGPA.$footerPGPA;

                      # PDF
                      class ConPiesterm extends TCPDF {
                        // Header
                        function Header(){
                          $this->writeHTML($headerPGPA, true, 0, true, 0);
                        }
                        // Footer
                        function Footer(){
                          $this->SetY(-20);
                          $this->writeHTML($footerPGPA, true, 0, true, 0);
                        }
                      }

                      // Creamos un nuevo objeto usando la clase extendida classpies
                      $pdf = new ConPiesterm();
                      $pdf->SetFont('times','',10);

                      // Add a page
                      $pdf->AddPage("P");

                      // Output the HTML content
                      $pdf->writeHTMLCell(180, 100, 10,30,$bodyPGPA, 0, 0, false, true,'',true);

                      // Nombre del archivo
                      $fileNameterm = $fl_sesion."29".$Date.'.pdf';

                      // Pasamos el archivo a base64
                      $fileattterm = $pdf->Output($fileNameterm, 'S'); //genera la codificacion para enviar adjuntado el archivo

                      // Mensaje email
                      /*$messagePGPA  = "--".$separator.$eol;
                      $messagePGPA .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
                      $messagePGPA .= $ds_email_warningPGPA.$eol;
                      $messagePGPA .= "--".$separator.$eol;
                      $messagePGPA .= $fileattterm;
                      $messagePGPA .= "--".$separator."--".$eol;
                      */
                      if($fg_aprobado=='1'){

                      }

                      if($fg_aprobado=='0'){
                          #send email:
                          $sendemail= Mailer($ds_email,$template_warningPGPA,$ds_email_warningPGPA,'',$fileattterm,$fileNameterm,'',true);
                          if($sendemail){
                              # Insertamos los datos del email enviado parq eu se congelen
                              $QueryPGAP  = "INSERT INTO k_alumno_template (fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
                              $QueryPGAP .= "VALUES ($fl_sesion,29,CURRENT_TIMESTAMP,'$headerPGPA','$bodyPGPA','$footerPGPA')";
                              EjecutaQuery($QueryPGAP);
                          }
                      }


                    }

                    # Verificamos si es la penultima semana y su GPA term no aprovado se enviara un mensaje de advertencia
                    # Obtenemos la penultima seman del term que esta cursando
                    $rowPS = RecuperaValor("SELECT MAX(fl_semana)-1 FROM k_semana WHERE fl_term=(SELECT MAX(fl_term) FROM k_alumno_term WHERE fl_alumno=$fl_alumno)");
                    $semana_penultima =$rowPS[0];
                    $semana_actual = $fl_semana;
                    $rowTGPA = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($current_term_promedio) AND no_max >= ROUND($current_term_promedio)");
                    $aprovadoTGPA = $rowTGPA[1];

                    
       
                    if(($semana_actual==$semana_penultima) && ($aprovadoTGPA=='0')){
                      # Template para calificaciones no aprovadas
                      $templateTGPA = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=31 AND fg_activo='1'";
                      $rowTGPA1 = RecuperaValor($templateTGPA);
                      $template_TGPA = str_uso_normal($rowTGPA1[0].$rowTGPA1[1].$rowTGPA1[2]);
                      $template_warningTGPA = str_texto($rowTGPA1[3]);

                      $variables3 = array(
                        'st_fname' => $st_fname,
                        'st_lname' => $st_lname,
                        'te_fname' => $te_fname,
                        'te_lname' => $te_lname,
                        'no_week' => $no_week,
                        'ds_title' => $ds_title,
                        'st_lmadd' => $st_lmadd,
                        'st_country' => $ds_add_country,
                        'st_lmaddpc' => $ds_add_zip,
                        'current_term_gpa' => $current_term_gpa,
                        'no_grado' => $no_grado,
                        'pg_name' => $nb_programa,
                        'program_gpa' => $gpa_grl,
                        'minimum_gpa' => $calificacion_min
                      );

                      # Template
                      $headerTGPA = GenerateTemplate(genera_documento($fl_sesion, 1, 31));
                      $bodyTGPA =  GenerateTemplate(genera_documento($fl_sesion, 2, 31));
                      $footerTGPA =  GenerateTemplate(genera_documento($fl_sesion, 3, 31));
                      $ds_email_warningTGPA = $headerTGPA.$bodyTGPA.$footerTGPA;

                      # PDF
                      class ConPies extends TCPDF {
                        // Header
                        function Header(){
                          $this->writeHTML($headerTGPA, true, 0, true, 0);
                        }
                        // Footer
                        function Footer(){
                          $this->SetY(-20);
                          $this->writeHTML($footerTGPA, true, 0, true, 0);
                        }
                      }

                      // Creamos un nuevo objeto usando la clase extendida classpies
                      $pdfp = new ConPies();
                      $pdfp->SetFont('times','',10);

                      // Add a page
                      $pdfp->AddPage("P");

                      // Output the HTML content
                      $pdfp->writeHTMLCell(180, 100, 10,30,$bodyTGPA, 0, 0, false, true,'',true);

                      // Nombre del archivo
                      $fileNamep = $fl_sesion."31".$Date.'.pdf';

                      // Pasamos el archivo a base64
                      $fileattprogram = $pdfp->Output($fileNamep, 'S'); //genera la codificacion para enviar adjuntado el archivo

                      // Mensaje email
                      #$messagePGPA  = "--".$separator.$eol;
                      #$messagePGPA .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
                      #$messagePGPA .= $ds_email_warningTGPA.$eol;
                      #$messagePGPA .= "--".$separator.$eol;
                      #$messagePGPA .= $fileattprogram;
                      #$messagePGPA .= "--".$separator."--".$eol;
                      $sendemail= Mailer($ds_email,$template_warningTGPA,$ds_email_warningTGPA,'',$fileattprogram,$fileNamep,'',true);
                      if($sendemail){
                        # Insertamos los datos del email enviado parq eu se congelen
                        $QueryTGPA  = "INSERT INTO k_alumno_template (fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
                        $QueryTGPA .= "VALUES ($fl_sesion,31,CURRENT_TIMESTAMP,'$headerTGPA','$bodyTGPA','$footerTGPA')";
                        EjecutaQuery($QueryTGPA);
                      }
                    }
                  }





              /*********************************************************************************************************************************/




    }




if(empty($fg_guardar_todo)){#solo presenta cuando se ejecutan los cometarios del tecaher.
?>
<h2 style="margin: 2px 4px; line-height: 60%;font-size:13px;"><i><small><?php echo ObtenEtiqueta(1680)." :<br/>".$fe_modificacion; ?></small></i></h2>


<?php
}



?>
