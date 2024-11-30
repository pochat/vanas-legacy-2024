<?php

	#librerias propias de FAME.
	require '/var/www/html/vanas/fame/lib/self_general.php';

	#librerias propias de FAME.
//    require '../fame/lib/self_general.php';

    $from = 'noreply@vanas.ca';


    #Recuperaos la fecha actual en formato y-m-d
    $fe_actual=ObtenerFechaActual();
    



	#Manda un email a todos los usuarios que ya se graduaron para contestar un cuestionario pasando 6 meses que se graduaron se enviara cron por 3 veces solamente.
   $Query ='
    SELECT Main.*,
           Main.nb_usuario "name",
           CONCAT_WS(" ", Main.ds_add_city, Main.nb_zona_horaria) "country",
           CONCAT_WS(" ", Main.nb_programa, "Term:", Main.no_grado) "program",
           Main.status_label "status",
           "teachers" "teachers"           
    FROM (SELECT Usuario.fl_usuario fl_usuario,
                 Usuario.cl_sesion,
                 Usuario.ds_login ds_login,
                 CONCAT_WS(" ", IFNULL(Usuario.ds_nombres, ""),
                                IFNULL(Usuario.ds_apaterno, ""),
                                IFNULL(Usuario.ds_amaterno, "")) nb_usuario,
                 Usuario.ds_nombres ds_nombres,
                 Usuario.ds_apaterno ds_apaterno,
                 Usuario.ds_amaterno ds_amaterno,
                 Usuario.fg_genero fg_genero,
                 Usuario.fe_nacimiento,
                 Usuario.ds_email,
                 Usuario.fg_activo,
                 Usuario.fe_alta, 
                 DATE_FORMAT(Usuario.fe_alta, "%d-%m-%Y") AS fe_alta_label,
                 Usuario.fe_ultacc,
                 USesion.fe_ultmod,
                 Alumno.no_promedio_t,
                 Alumno.ds_notas,
                 CONCAT(ZH.nb_zona_horaria, " ", "GMT", " (", ZH.no_gmt, ")") nb_zona_horaria,
                 (SELECT fg_international
                  FROM k_app_contrato app
                  WHERE app.cl_sesion = Usuario.cl_sesion
                  ORDER BY no_contrato LIMIT 1) fg_international,   
                 Periodo.nb_periodo,
                 (SELECT fe_inicio
                  FROM k_term te, c_periodo i, k_alumno_term al
                  WHERE te.fl_periodo = i.fl_periodo 
                  AND te.fl_term = al.fl_term
                  AND al.fl_alumno = Usuario.fl_usuario
                  AND no_grado = 1
                  LIMIT 1) fe_start_date,    
                 Programa.nb_programa,
                 CONCAT(Profesor.ds_nombres, " ", Profesor.ds_apaterno) ds_profesor,
                 Grupo.nb_grupo,                 
                 PCTIA.fe_carta,
                 PCTIA.fe_contrato,
                 PCTIA.fe_fin,
                 PCTIA.fe_completado,
                 PCTIA.fe_emision,
                 PCTIA.fg_certificado,
                 PCTIA.fg_honores,
                 PCTIA.fe_graduacion,
                 (fe_graduacion + INTERVAL 6 month)fe_graduacion_seis_meses,
                 (PCTIA.fe_graduacion + INTERVAL 6 month + INTERVAL 7 DAY)fe_graduacion_seis_meses_one,
                 (PCTIA.fe_graduacion + INTERVAL 6 month + INTERVAL 14 DAY)fe_graduacion_seis_meses_two,
                 PCTIA.fg_desercion,
                 PCTIA.fg_dismissed,
                 PCTIA.fg_job,
                 PCTIA.fg_graduacion,
                 Form1.ds_add_city,
                 Form1.ds_add_state,
                 USesion.fg_pago,
                 Pais.ds_pais,
                 YEAR(Usuario.fe_nacimiento) ye_fe_nacimiento,
                 YEAR(Usuario.fe_alta) ye_fe_alta,
                 YEAR(Usuario.fe_ultacc) ye_fe_ultacc,
                 YEAR(Form1.fe_ultmod) ye_fe_ultmod,
                 YEAR(PCTIA.fe_carta) ye_fe_carta,
                 YEAR(PCTIA.fe_contrato) ye_fe_contrato,
                 YEAR(PCTIA.fe_fin) ye_fe_fin,
                 YEAR(PCTIA.fe_completado) ye_fe_completado,
                 YEAR(PCTIA.fe_emision) ye_fe_emision,
                 YEAR(PCTIA.fe_graduacion) ye_fe_graduacion,
                 (SELECT YEAR(fe_inicio)
                  FROM k_term te, c_periodo i, k_alumno_term al
                  WHERE te.fl_periodo = i.fl_periodo
                  AND te.fl_term = al.fl_term
                  AND al.fl_alumno = Usuario.fl_usuario
                  AND no_grado = 1
                  LIMIT 1) ye_fe_start_date,
                  CASE
                  WHEN PCTIA.fg_job LIKE "1" THEN "Work placement"
                  WHEN PCTIA.fg_graduacion LIKE "1" THEN "Graduated" 
                  WHEN PCTIA.fg_dismissed LIKE "1" THEN "Student dismissed" 
	          WHEN PCTIA.fg_desercion LIKE "1" THEN "Student withdrawal"
	          WHEN Usuario.fg_activo LIKE "1" THEN "Active"
                  ELSE "Not Set"
                  END status_label,
                  CASE WHEN Grupo.fl_term >0 THEN Grupo.fl_term ELSE 0 END fl_term,
                  Form1.fl_programa,
                  CASE WHEN Term.no_grado >0 THEN Term.no_grado ELSE 0 END no_grado,
            (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= Alumno.no_promedio_t AND no_max >= Alumno.no_promedio_t LIMIT 1) cl_calificacion,
            Alumno.mn_progreso, Programa.ds_duracion,Alumno.fg_absence, Alumno.fg_change_status ,ProgCosto.cl_delivery,ProgCosto.ds_credential  
          FROM c_usuario Usuario
          JOIN c_sesion USesion ON(USesion.cl_sesion = Usuario.cl_sesion)
          JOIN c_alumno Alumno ON(Usuario.fl_usuario = Alumno.fl_alumno)
          JOIN c_zona_horaria ZH ON(ZH.fl_zona_horaria = Alumno.fl_zona_horaria)
          LEFT JOIN k_alumno_grupo AlumnoGrupo ON(AlumnoGrupo.fl_alumno = Usuario.fl_usuario) AND AlumnoGrupo.fg_grupo_global<>"1" 
          LEFT JOIN c_grupo Grupo ON (Grupo.fl_grupo = AlumnoGrupo.fl_grupo)
          LEFT JOIN c_usuario Profesor ON(Grupo.fl_maestro = Profesor.fl_usuario)
          LEFT JOIN k_term Term ON(Term.fl_term = Grupo.fl_term)
          JOIN k_ses_app_frm_1 Form1 ON(Usuario.cl_sesion = Form1.cl_sesion)
          JOIN c_programa Programa ON(Programa.fl_programa = Form1.fl_programa)
          JOIN c_periodo Periodo ON (Periodo.fl_periodo = Form1.fl_periodo)
          JOIN k_programa_costos ProgCosto ON ProgCosto.fl_programa=Programa.fl_programa  

          JOIN c_pais Pais ON(Pais.fl_pais = Form1.ds_add_country)
          LEFT JOIN k_pctia PCTIA ON (PCTIA.fl_alumno = Usuario.fl_usuario)
          WHERE Usuario.fl_perfil = 3 AND ( Usuario.ds_graduate_status is null or  Usuario.ds_graduate_status="" )
          GROUP BY Usuario.fl_usuario) AS Main
    WHERE true = true ';
   $Query.='AND fe_graduacion IS NOT NULL 
           AND fe_graduacion>="2020-07-01" ';
   $Query.='AND  (ds_credential="Diploma" OR fl_programa=31 ) ';

	$rs1 = EjecutaQuery($Query);
	for($i=1;$row=RecuperaRegistro($rs1);$i++){
	   
        $fl_usuario=$row['fl_usuario'];
        $ds_login=$row['ds_login'];
        $nb_usuario=$row['nb_usuario'];
        $ds_email=$row['ds_email'];
        $nb_programa=$row['nb_programa'];
        $fe_graduacion=$row['fe_graduacion'];
        $fe_graduacion_seis_meses=$row['fe_graduacion_seis_meses'];
        $fe_graduacion_seis_meses_one=$row['fe_graduacion_seis_meses_one'];
        $fe_graduacion_seis_meses_two=$row['fe_graduacion_seis_meses_two'];

   
		#Se genera un email de confirmacion al alumno que recibe el curso 
		$ds_encabezado =  GeneraTemplate(1, 187);
		$ds_cuerpo =  GeneraTemplate(2, 187);
		$ds_pie = GeneraTemplate(3, 187);
		$ds_contenido=html_entity_decode($ds_encabezado.$ds_cuerpo.$ds_pie);                      
                    
        $ds_contenido = str_replace("#st_fname#", $nb_usuario, $ds_contenido);  #no_dias_cuso
        $ds_contenido = str_replace("#id_student#", $fl_usuario, $ds_contenido);  #no_dias_cuso
        $ds_contenido = str_replace("#pg_name#", $nb_programa, $ds_contenido);  #no_dias_cuso   
    
        #Verificamos que no haya mas de 3 envios de email.   
        $Querym="SELECT COUNT(*) FROM k_envio_cronjob WHERE fl_usuario=$fl_usuario AND fl_template=187 ";
        $rowem=RecuperaValor($Querym);
        $countemail=$rowem[0];

        if(($fe_actual>=$fe_graduacion_seis_meses)||($fe_actual==$fe_graduacion_seis_meses_one)||($fe_actual==$fe_graduacion_seis_meses_two)){

            //envia solo tres intentos.
            if($countemail<3){
                
		        #Recuperamos el titulo del documento email
		        $Query="SELECT nb_template FROM k_template_doc WHERE fl_template=187 ";
		        $row=RecuperaValor($Query);
		        $ds_titulo=str_texto($row[0]);
                
		        $ds_email_de_quien_envia_mensaje=$from;
		        $nb_nombre_envia_email=ObtenEtiqueta(949);#nombre de quien envia el mensaje
		        $bcc="noreply@vanas.ca";
		        $message  = $ds_contenido;
		        $message = utf8_decode(str_ascii(str_uso_normal($message)));
                
		        $mail = EnviaMailHTML($nb_nombre_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email, $ds_titulo, $message, $bcc);
                
                $Queri="INSERT INTO k_envio_cronjob (fl_usuario,ds_mensaje,fe_cron,fg_enviado,fl_template)VALUES($fl_usuario,'$message',CURRENT_TIMESTAMP,'1',187)";
                $fl_conrob=EjecutaInsert($Queri);
                


            }	
			
        }	
                                      
                                            
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