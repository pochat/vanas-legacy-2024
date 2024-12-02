<?php

require '../../lib/general.inc.php';

//Dbg::pd($_POST);
//$extraConditions = $_POST['extra_filters'];
parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
$_POST += $advanced_search;
//$whereDates = '';
//if ($extraConditions['inicia_fe_pago'] != '' && $extraConditions['finaliza_fe_pago'] != '') {
//    $whereDates = 'AND Ingreso.fe_pago BETWEEN "' . $extraConditions['inicia_fe_pago'] . '" AND "' . $extraConditions['finaliza_fe_pago'] . '"';
//}
$nuevo = $_POST['nuevo'];
$actual = $_POST['actual'];
//$all = $_POST['all'];
require 'filtros.inc.php';
/*if(!empty($all))
  $Query = "";*/
//die;
$query = '
    SELECT Main.*,
           Main.nb_usuario "name",
           CONCAT_WS(" ", Main.ds_add_city, Main.nb_zona_horaria) "country",
           CONCAT_WS(" ", Main.nb_programa, "Term:", Main.no_grado) "program",
           Main.status_label "status",
           -- Main.progress_std "progress",
           "teachers" "teachers"
           -- ,OCULTO POR EFICIENCIA "grades" "grades"
           -- ,Main.fg_activo "active"
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
                  WHEN PCTIA.fg_job LIKE "1" THEN "'.ObtenEtiqueta(644).'"
                  WHEN PCTIA.fg_graduacion LIKE "1" THEN "'.ObtenEtiqueta(645).'"
                  WHEN PCTIA.fg_dismissed LIKE "1" THEN "'.ObtenEtiqueta(559).'"
	          WHEN PCTIA.fg_desercion LIKE "1" THEN "'.ObtenEtiqueta(558).'"
	          WHEN Usuario.fg_activo LIKE "1" THEN "'.ObtenEtiqueta(113).'"
                  ELSE "Not Set"
                  END status_label,
                  CASE WHEN Grupo.fl_term >0 THEN Grupo.fl_term ELSE 0 END fl_term,
                  Form1.fl_programa,
                  CASE WHEN Term.no_grado >0 THEN Term.no_grado ELSE 0 END no_grado,
            (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= Alumno.no_promedio_t AND no_max >= Alumno.no_promedio_t LIMIT 1) cl_calificacion,
            Alumno.mn_progreso, Programa.ds_duracion,Alumno.fg_absence, Alumno.fg_change_status,Form1.ds_add_state ds_add_state_2
                  /*
                  OCULTO POR EFICIENCIA
                  ,IFNULL(GROUP_CONCAT(TRUNCATE(Grade.no_promedio, 0) ORDER BY Grade.no_grado ),0) grades_by_term
                  */
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
          /*
          OCULTO POR EFICIENCIA
          LEFT JOIN (SELECT kat.fl_alumno, t.no_grado, kat.no_promedio, t.fl_programa
                     FROM k_alumno_term kat
                     JOIN k_term t ON(t.fl_term = kat.fl_term)
                     LEFT JOIN (SELECT kat.fl_alumno, t.fl_term, t.no_grado
                                FROM k_alumno_term kat
                                JOIN k_term t ON(t.fl_term = kat.fl_term)
		                ) t2 ON(t2.fl_alumno = kat.fl_alumno
                                        AND t2.no_grado = t.no_grado
                                        AND  t.fl_term < t2.fl_term )
		     WHERE t2.fl_term IS NULL
                     ORDER BY t.no_grado, t.fl_term) Grade ON(Grade.fl_alumno = Usuario.fl_usuario
                                                              AND Grade.fl_programa = Programa.fl_programa)
          */
          JOIN c_pais Pais ON(Pais.fl_pais = Form1.ds_add_country)
          LEFT JOIN k_pctia PCTIA ON (PCTIA.fl_alumno = Usuario.fl_usuario)
          WHERE Usuario.fl_perfil = 3
          GROUP BY Usuario.fl_usuario) AS Main
    WHERE true = true '.$Query.'
    ORDER BY Main.fe_alta DESC ';
    // echo $query;
// Dbg::pd($query);
$JQueryDataTable = new JQueryDataTable();
//Dbg::data($_POST);
if (false) {
    $JQueryDataTable->queryInfo($_POST + array(
        'query' => $query,
        'aliasTable' => 'List'));
    die;
}
/**
 * EGMC 20160517
 * Data
 */
$data = $JQueryDataTable->queryInfo($_POST + array(
    'query' => $query,
    'aliasTable' => 'List'), false);
//Dbg::printQuerys();
//Dbg::pd($data);
//die;
$dt = $data['data'];

$flUsuarios = array();
foreach ($dt as $key => $lt) {
    $flUsuarios[] = $lt['List']['fl_usuario'];
}

$teachers = CAlumno::getTeachersByFlUsuario($flUsuarios);
$statusColors = array(
    ObtenEtiqueta(644) => "label-danger",
    ObtenEtiqueta(645) => "label-success",
    ObtenEtiqueta(558) => "label-danger",
    ObtenEtiqueta(559) => "label-danger",
    ObtenEtiqueta(113) => "label-success",
    "Not Set" => "label-warning",
);
$tot_registros = 0;
foreach ($dt as $key => $lt) {
    $lt = $lt['List'];
    $tot_registros ++;
    # Obtenemos la clase actual
    $row0 = RecuperaValor("SELECT b.fl_term, c.no_grado FROM k_alumno_grupo a, c_grupo b, k_term c WHERE  a.fl_grupo=b.fl_grupo AND b.fl_term=c.fl_term AND a.fl_alumno =".$lt['fl_usuario']."");
    $current_term = !empty($row0[0])?$row0[0]:NULL;
    $current_grado = !empty($row0[1])?$row0[1]:NULL;
    $Query1  = "SELECT MAX(b.no_semana) ";
    $Query1 .= "FROM k_semana a, c_leccion b ";
    $Query1 .= "WHERE a.fl_leccion=b.fl_leccion ";
    $Query1 .= "AND TO_DAYS(a.fe_publicacion) <= TO_DAYS('".date('Y-m-d')."') ";
    $Query1 .= "AND a.fl_term=$current_term ";
    $Query1 .= "AND b.fl_programa=".$lt['fl_programa']." ";
    $Query1 .= "AND b.no_grado=$current_grado";
    $row1 = RecuperaValor($Query1);
    $week_current = !empty($row1[0])?$row1[0]:NULL;
    $Query00  = "SELECT MAX(a.fl_term), a.no_promedio FROM k_alumno_term a, k_term b ";
    $Query00 .= "WHERE a.fl_term = b.fl_term AND a.fl_alumno=".$lt['fl_usuario']." ORDER BY b.no_grado DESC";
    $row00 = RecuperaValor($Query00);
    $fl_term_max = !empty($row00[0])?$row00[0]:NULL;
    # Obtener el promedio
    $Query01 = "SELECT no_promedio FROM k_alumno_term WHERE fl_alumno=".$lt['fl_usuario']." AND fl_term=".$fl_term_max;
    $row01 = RecuperaValor($Query01);
    $no_promedio = $row01[0];
    if(empty($no_promedio))
      $no_promedio=0;
    $row01 = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio) AND no_max >= ROUND($no_promedio)");
    $cl_calificacion = $row01[0];

    if($lt['fg_change_status']==1)
        $label_fg_change_status="<p><span class='label label-warning'>".ObtenEtiqueta(2059)."</span></p>";
    else
        $label_fg_change_status="";
    if($lt['fg_absence']==1)
        $label_fg_absence="<p><span class='label label-warning'>".ObtenEtiqueta(2058)."</span></p>";
    else
        $label_fg_absence="";

    $Query="SELECT fg_scholarship,a.cl_sesion,a.fl_pais_campus FROM c_sesion a JOIN c_usuario b on b.cl_sesion=a.cl_sesion  WHERE b.fl_usuario=".$lt['fl_usuario']." ";
    $row=RecuperaValor($Query);
    $fg_scholarship=!empty($row['fg_scholarship'])?"Yes":"No";
    $cl_sesion =$row['cl_sesion'];
    $fl_pais_campus=$row['fl_pais_campus'];

    if($fl_pais_campus==226){
        $etq_campus="USA Campus";
    }else{
        $etq_campus="CANADA Campus";
    }


    switch ($fl_pais_campus) {

        case '38':
            $etq_campus = "CANADA Campus";
            break;
        case '226':
            $etq_campus = "USA Campus";
            break;
        case '199':
            $etq_campus = "Spain Campus";

            break;
        case '73':
            $etq_campus = "Fance Campus";

            break;
        case '80':
            $etq_campus = "Germany Campus";

            break;
        case '105':
            $etq_campus = "Italy Campus";

            break;
        case '225':
            $etq_campus = "United Kingdom Campus";

            break;
        case '105':
            $etq_campus = "New Zealand Campus";

            break;
        default:
            $etq_campus = "CANADA Campus";

            break;

    }





    $Query="SELECT fg_payment FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
    $ro_=RecuperaValor($Query);
    $fg_payment=$ro_['fg_payment'];
    $fg_tipo_curso="Online Live Classes";
    if($fg_payment=='C'){
        $fg_tipo_curso="Combined";
    }

    $Queryd="select fg_disability from k_ses_app_frm_1 where cl_sesion='$cl_sesion' ";
    $rowd=RecuperaValor($Queryd);
    $fg_disability=$rowd['fg_disability'];

    if($fg_disability=='1')
    {
        $txt_fg_disability="<small class='text-muted'>Special needs: YES</small>";
    }else{
        $txt_fg_disability="";
    }



    $data['data'][$key]['List'] = array(
        "action" => "<button class='btn btn-xs'>Open case</button> <button class='btn btn-xs btn-danger pull-right' style='margin-left:5px'>Delete Record</button> <button class='btn btn-xs btn-success pull-right'>Save Changes</button> ",
        'name' => '<strong>' . $lt['nb_usuario'] . '</strong>',
        'campus'=> '<br><small class="text-muted">'.$etq_campus.'</small>',
        "country" => $lt['ds_add_city'] . ', ' . $lt['ds_pais'] . '<br><small class="text-muted">' . $lt['nb_zona_horaria'] . '</small>',
        "program" => $lt['nb_programa'] . ' ('.$lt['ds_duracion'].')<br><small class="text-muted">Term: ' . $lt['no_grado'] . ', Week '.$week_current.', GPA: '.$cl_calificacion.' ('.round(number_format($no_promedio,2)).'%) </small><br><small class=\'text-muted\'><i>'.$fg_tipo_curso.'</i></small>',
        "status" => "<p><span class='label " . $statusColors[$lt['status']] . "'>" . $lt['status'] . "</span></p> ".$label_fg_change_status." ".$label_fg_absence." <br><small class='text-muted'>Scholarship: ".$fg_scholarship."</small><br>".$txt_fg_disability." ",
        //"progress" => "<td><div class='progress progress-xs' data-progressbar-value='" . round($lt['no_promedio_t']) . "'><div class='progress-bar'></div></div></td>",
        "progress" => "<td><div class='progress progress-xs' data-progressbar-value='" . round($lt['mn_progreso']) . "'><div class='progress-bar'></div></div><span class='hidden'>" . round($lt['mn_progreso']) . "</span></td>",
        /**
         * EGMC 20160609
         * Oculto por eficiencia
          "grades" => "<span style='margin-top:5px;' class='sparkline display-inline' data-sparkline-type='compositebar' data-sparkline-height='18px' data-sparkline-bar-color='#F80' data-sparkline-line-width='2.5' data-sparkline-barwidth='15' data-sparkline-barspacing='2' data-sparkline-bar-val='[-1," . $lt['grades_by_term'] . "]'></span>",
         */
//        "grades" => "<span style='margin-top:5px;' class='sparkline display-inline' data-sparkline-type='compositebar' data-sparkline-height='18px' data-sparkline-bar-color='#F80' data-sparkline-line-width='2.5' data-sparkline-barwidth='15' data-sparkline-barspacing='2' data-sparkline-line-val='[".$lt['grades_by_term']."]' data-sparkline-bar-val='[".$lt['grades_by_term']."]'></span>",
//        "grades" => "<span style='margin-top:5px;' class='sparkline display-inline' data-sparkline-type='compositebar' data-sparkline-height='18px' data-sparkline-bar-color='#F80' data-sparkline-line-width='2.5' data-sparkline-barwidth='15' data-sparkline-barspacing='2' data-sparkline-line-val='[89,90,89,92,91]' data-sparkline-bar-val='[89,90,89,92,91]'></span>",
        //"active" => "<span class='onoffswitch'><input type='checkbox' name='start_interval' class='onoffswitch-checkbox' id='st1' " . ($lt['fg_activo'] ? "checked='checked'" : '') . "><label class='onoffswitch-label' for='st1'><span class='onoffswitch-inner' data-swchon-text='ON' data-swchoff-text='OFF'></span><span class='onoffswitch-switch'></span></label></span>",
        "comments" => "This is a blank comments area, used to add comments and keep notes",
        "tot_registros" => $tot_registros
            ) + $lt;
    /**
     * EGMC 20160525
     * Se agrega el maestro
     */
    $data['data'][$key]['List']['teachers'] = '';
    if (!empty($teachers[$lt['fl_usuario']])) {
        $data['data'][$key]['List']['teachers'].="<div class='project-members'>";
        foreach ($teachers[$lt['fl_usuario']] as $avtr) {
//            Dbg::pd($avtr);

            if ($avtr['ds_ruta_avatar'] == '') {
                $avtr['ds_ruta_avatar'] = 'male.png';
            }

            $data['data'][$key]['List']['teachers'].= "<a href='javascript:void(0)' rel='tooltip' data-placement='top' data-html='true' data-original-title='Term " . $avtr['no_grado'] . "</small>: " . $avtr['nb_maestro'] . "'><img src='".PATH_MAE_IMAGES."/avatars/" . $avtr['ds_ruta_avatar'] . "' class='online' alt='user'></a>";
        }
        $data['data'][$key]['List']['teachers'].="</div>";
    }
}
//Dbg::pd($data);
die(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP));
