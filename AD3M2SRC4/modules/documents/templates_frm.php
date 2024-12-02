<?php

# Libreria de funciones
require '../../lib/general.inc.php';

# Verifica que exista una sesion valida en el cookie y la resetea
ValidaSesion();

# Recibe parametro
$clave = RecibeParametroNumerico('clave');
$fg_error = RecibeParametroNumerico('fg_error');

# Determina si es alta o modificacion
if (!empty($clave))
  $permiso = PERMISO_DETALLE;
else
  $permiso = PERMISO_ALTA;

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermiso(FUNC_DOC_TEMPLATES, $permiso)) {
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}

# Inicializa variables
if (!$fg_error) { // Sin error, viene del listado
  if (!empty($clave)) { // Actualizacion, recupera de la base de datos
    $row = RecuperaValor("SELECT nb_template, fl_categoria, fg_activo, ds_encabezado, ds_encabezado_esp, ds_encabezado_fra, ds_cuerpo, ds_cuerpo_esp, ds_cuerpo_fra, ds_pie,ds_encabezado_de,ds_encabezado_it,ds_cuerpo_de,ds_cuerpo_it FROM k_template_doc WHERE fl_template=$clave");
    $nb_template = str_texto($row["nb_template"]);
    $fl_categoria = $row["fl_categoria"];
    $fg_activo = $row["fg_activo"];
    $ds_encabezado = str_texto($row["ds_encabezado"]);
    $ds_encabezado_esp = str_texto($row["ds_encabezado_esp"]);
    $ds_encabezado_fra = str_texto($row["ds_encabezado_fra"]);
    $ds_cuerpo = str_texto($row["ds_cuerpo"]);
    $ds_cuerpo_esp = str_texto($row["ds_cuerpo_esp"]);
    $ds_cuerpo_fra = str_texto($row["ds_cuerpo_fra"]);
    $ds_cuerpo_de = str_texto($row["ds_cuerpo_de"]);
    $ds_cuerpo_it = str_texto($row["ds_cuerpo_it"]);
    $ds_encabezado_de = str_texto($row["ds_encabezado_de"]);
    $ds_encabezado_it = str_texto($row["ds_encabezado_it"]);
    $ds_pie = str_texto($row["ds_pie"]);


  } else {
    $nb_template = "";
    $fl_categoria = 0;
    $fg_activo = 1;
    $ds_encabezado = "";
    $ds_encabezado_esp = "";
    $ds_encabezado_fra = "";
    $ds_encabezado_de = "";
    $ds_encabezado_it = "";
    $ds_cuerpo = "";
    $ds_cuerpo_de = "";
    $ds_cuerpo_it = "";
    $ds_pie = "";
  }
  $nb_template_err = "";
  $fl_categoria_err = "";
} else { // Con error, recibe parametros (viene de la pagina de actualizacion)
  $nb_template = RecibeParametroHTML('nb_template');
  $nb_template_err = RecibeParametroNumerico('nb_template_err');
  $fl_categoria = RecibeParametroNumerico('fl_categoria');
  $fl_categoria_err = RecibeParametroNumerico('fl_categoria_err');
  $fg_activo = RecibeParametroHTML('fg_activo');
  $ds_encabezado = RecibeParametroHTML('ds_encabezado');
  $ds_encabezado_esp = RecibeParametroHTML('ds_encabezado_esp');
  $ds_encabezado_fra = RecibeParametroHTML('ds_encabezado_fra');
  $ds_encabezado_de = RecibeParametroHTML('ds_encabezado_de');
  $ds_encabezado_it = RecibeParametroHTML('ds_encabezado_it');
  $ds_cuerpo_de = RecibeParametroHTML('ds_cuerpo_de');
  $ds_cuerpo_it = RecibeParametroHTML('ds_cuerpo_it');
  $ds_cuerpo = RecibeParametroHTML('ds_cuerpo');
  $ds_pie = RecibeParametroHTML('ds_pie');
}

$ds_variables = "
    <table border='" . D_BORDES . "' class='css_default'>
      <thead>
      <tr><td width='70%'>&nbsp;</td><td width='40%'>&nbsp;</td></tr>
      </thead>
      <tbody>
      <tr><td>Campus Address:</td><td>#st_address_campus#</td></tr>
      <tr><td>Student first name: </td><td>#st_fname#</td></tr>
      <tr><td>Student middle name: </td><td>#st_mname#</td></tr>
      <tr><td>Student last name:  </td><td>#st_lname#</td></tr>
      <tr><td>Student login: </td><td>#st_login#</td></tr>
      <tr><td>Student ID:  </td><td>#st_id#</td></tr>
      <tr><td>Student previous name:  </td><td>#st_pname#</td></tr>
      <tr><td>Student personal education number:  </td><td>#st_ednum#</td></tr>
      <tr><td>Student Street Number:  </td><td>#st_street_no#</td></tr>
      <tr><td>Student Street Name:  </td><td>#st_street_name#</td></tr>
      <tr><td>Student City:  </td><td>#st_city#</td></tr>
      <tr><td>Student State/Province:  </td><td>#st_state#</td></tr>
      <tr><td>Student country:  </td><td>#st_country#</td></tr>
      <tr><td>Student Postal Code:  </td><td>#st_code_zip#</td></tr>
      <tr><td>Student local mailing address: </td><td>#st_lmadd#</td></tr>
      <tr><td>Student local mailing address postal code: </td><td>#st_lmaddpc#</td></tr>
      <tr><td>Student permanent mailing address: </td><td>#st_pmadd#</td></tr>
      <tr><td>Student permanent mailing address postal code: </td><td>#st_pmaddpc#</td></tr>
      <tr><td>Student telephone number: </td><td>#st_pnone#</td></tr>
      <tr><td>Student alternative telephone number: </td><td>#st_aphone#</td></tr>
      <tr><td>Student email address: </td><td>#st_email#</td></tr>
      <tr><td>Student alternative email address: </td><td>#st_aemail#</td></tr>
      <tr><td>International student: </td><td>#st_ist#</td></tr>
      <tr><td>Student year of birth: </td><td>#st_byear#</td></tr>
      <tr><td>Student month of birth: </td><td>#st_bmonth#</td></tr>
      <tr><td>Student day of birth: </td><td>#st_bday#</td></tr>
      <tr><td>Student gender: </td><td>#st_gender#</td></tr>
      <tr><td>Program name: </td><td>#pg_name#</td></tr>
      <tr><td>Academic Status (Full/Part Time): </td><td>#academic_status#</td></tr>
      <tr><td>Hours of instruction per week: </td><td>#hours_week#</td></tr>
      <tr><td>Program duration in hours: </td><td>#pg_durationh#</td></tr>
      <tr><td>Program duration in weeks: </td><td>#pg_durationw#</td></tr>
      <tr><td>Program duration in months: </td><td>#pg_durationm#</td></tr>
      <tr><td>Program start date: </td><td>#pg_stdate#</td></tr>
      <tr><td>Program end date: </td><td>#pg_edate#</td></tr>
      <tr><td>Program credential: </td><td>#pg_credential#</td></tr>
      <tr><td>Program delivery: </td><td>#pg_delivery#</td></tr>
      <tr><td>Program language: </td><td>#pg_language#</td></tr>
      <tr><td>Program application fee: </td><td>#pg_appfee#</td></tr>
      <tr><td>Program tuition: </td><td>#pg_tuition#</td></tr>
      <tr><td>Program other cost description: </td><td>#pg_ds_other_cost#</td></tr>
      <tr><td>Program other cost: </td><td>#pg_other_cost#</td></tr>
      <tr><td>Program discount description: </td><td>#pg_ds_cost_discount#</td></tr>
      <tr><td>Program discount: </td><td>#pg_cost_discount#</td></tr>
      <tr><td>Program total tuition cost: </td><td>#pg_total_tuition#</td></tr>
      <tr><td>Application fee GST/HST: </td><td>#app_fee_tax#</td></tr>
      <tr><td>Tuition fee GST/HST: </td><td>#tuition_fee_tax#</td></tr>
      <tr><td>Total costs: </td><td>#total_costs#</td></tr>
      <tr><td>Program total cost: </td><td>#pg_total_cost#</td></tr>
      <tr><td>Payment option A: </td><td>#py_optionA#</td></tr>
      <tr><td>Payment option B: </td><td>#py_optionB#</td></tr>
      <tr><td>Payment option C: </td><td>#py_optionC#</td></tr>
      <tr><td>Payment option D: </td><td>#py_optionD#</td></tr>
      <tr><td>Of payments option A: </td><td>#py_paymentsA#</td></tr>
      <tr><td>Of payments option B: </td><td>#py_paymentsB#</td></tr>
      <tr><td>Of payments option C: </td><td>#py_paymentsC#</td></tr>
      <tr><td>Of payments option D: </td><td>#py_paymentsD#</td></tr>
      <tr><td>Payment method: </td><td>#paymethod_contract#</td></tr>
      <tr><td>Start date Contract: </td><td>#start_date_contract#</td></tr>
      <tr><td>End date Contract: </td><td>#end_date_contract#</td></tr>
      <tr><td>Payment due contract: </td><td>#payment_due_contract#</td></tr>
      <tr><td>Tuition paid to date: </td><td>#tut_paid_contract#</td></tr>
      <tr><td>Total balance to date: </td><td>#tot_balance_contract#</td></tr>
      <tr><td>Payment frequency option A: </td><td>#py_freqA#</td></tr>
      <tr><td>Payment frequency option B: </td><td>#py_freqB#</td></tr>
      <tr><td>Payment frequency option C: </td><td>#py_freqC#</td></tr>
      <tr><td>Payment frequency option D: </td><td>#py_freqD#</td></tr>
      <tr><td>Payment Amount Due option A: </td><td>#py_dueoptionA#</td></tr>
      <tr><td>Payment Amount Due option B: </td><td>#py_dueoptionB#</td></tr>
      <tr><td>Payment Amount Due option C: </td><td>#py_dueoptionC#</td></tr>
      <tr><td>Payment Amount Due option D: </td><td>#py_dueoptionD#</td></tr>
      <tr><td>Payment Amount Paid option A: </td><td>#py_paidoptionA#</td></tr>
      <tr><td>Payment Amount Paid option B: </td><td>#py_paidoptionB#</td></tr>
      <tr><td>Payment Amount Paid option C: </td><td>#py_paidoptionC#</td></tr>
      <tr><td>Payment Amount Paid option D: </td><td>#py_paidoptionD#</td></tr>
      <tr><td>Electronic student signature: </td><td>#st_signature#</td></tr>
      <tr><td>Electronic student signature date: </td><td>#st_signaturedt#</td></tr>
      <tr><td>Electronic legal guardian signature date: </td><td>#st_lg_signaturedt#</td></tr>
      <tr><td>Electronic legal guardian signature: </td><td>#st_lg_signature#</td></tr>
      <tr><td>Contract template modification date: </td><td>#con_mod_date#</td></tr>
      <tr><td>Letter mail sent date: </td><td>#sent_date#</td></tr>
      <tr><td>" . ObtenEtiqueta(617) . "</td><td>#no_grado#</td></tr>
      <tr><td>" . ObtenEtiqueta(739) . "</td><td>#current_term_gpa#</td></tr>
      <tr><td>" . ObtenEtiqueta(740) . "</td><td>#program_gpa#</td></tr>
      <tr><td>Take Action</td><td>#sts_take_action#</td></tr>
      <tr><td>Expiration letter</td><td>#fe_expiration#</td></tr>
      <tr><td>Self Pace Name of person to invite</td><td>#sp_invitation_name#</td></tr>
       <tr><td>Self Pace Button Link</td><td>#sp_invitation_link#</td></tr>
       <tr><td>International student Citizenship</td><td>#st_citizenship#</td></tr>
       <tr><td>Student permit</td><td>#st_study_permit#</td></tr>
       <tr><td>Student other permit</td><td>#st_study_other_permit#</td></tr>
       <tr><td>Identify yourself as an aboriginal person </td><td>#fg_st_aboriginal#</td></tr>
       <tr><td>Aboriginal person</td><td>#ds_st_aboriginal#</td></tr>
       <tr><td>Have a long-term physical or mental health condition </td><td>#fg_st_disabilities#</td></tr>
       <tr><td>Description health condition</td><td>#ds_st_disabilities#</td></tr>
	   <tr><td>Description Class Time</td><td>#hr_class_time#</td></tr>
        <tr>td>Opt Non-Binary #opt_nonbinary#</td></tr>
        <tr><td>Careers</td><td>#ds_careers#</td></tr>
        <tr><td>Objectives</td><td>#ds_objectives#</td></tr>
        <tr><td>Teaching</td><td>#ds_teaching#</td></tr>
        <tr><td>Evaluation</td><td>#ds_evaluation#</td></tr>
        <tr><td>Requirements</td><td>#ds_requirements#</td></tr>
        <tr><td>Program organization</td><td>#ds_program_org#</td></tr>
        <tr><td>Combined (If Applicable)</td><td>#ds_combined#</td></tr>
        <tr><td>Combined Class Time</td><td>#ds_combined_time#</td></tr>

</tbody>
    </table>";

$ds_email_var =
  "<table border='" . D_BORDES . "' class='css_default'>
      <tr><td style='text-decoration: underline;'>General Variables</td></tr>
	  <tr><td>Student ID:  </td><td>#st_id#</td></tr>
      <tr><td>User first name:  </td><td>#us_fname#</td></tr>
      <tr><td>User last name:  </td><td>#us_lname#</td></tr>
      <tr><td>User first name (sent from this user):  </td><td>#us_fname_from#</td></tr>
      <tr><td>User last name (sent from this user):  </td><td>#us_lname_from#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td>Avatar:  </td><td>#ds_avatar#</td></tr>
      <tr><td>Avatar (sent from this user):  </td><td>#ds_avatar_from#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td>Comment:  </td><td>#ds_comment#</td></tr>
      <tr><td>Message:  </td><td>#ds_message#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td>Abstract:  </td><td>#ds_abstract#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td>Week number:  </td><td>#no_week#</td></tr>
      <tr><td>Day (Mon ~ Sun):  </td><td>#fe_day#</td></tr>
      <tr><td>Date:  </td><td>#fe_date#</td></tr>
      <tr><td>Time:  </td><td>#fe_time#</td></tr>
      <tr><td>Title:  </td><td>#ds_title#</td></tr>
      <tr><td>Current Week Grade:  </td><td>#current_week_grade#</td></tr>
      <tr><td>Name Global Class:  </td><td>#Global_class_name#</td></tr>
      <tr><td>Topic Global Class:  </td><td>#global_class_topic#</td></tr>
      <tr><td>Mandatory Global Class:  </td><td>#global_mandatory#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td style='text-decoration: underline;'>Student Variables</td></tr>
      <tr><td>Student first name: </td><td>#st_fname#</td></tr>
      <tr><td>Student last name:  </td><td>#st_lname#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td style='text-decoration: underline;'>Teacher Variables</td></tr>
      <tr><td>Teacher first name: </td><td>#te_fname#</td></tr>
      <tr><td>Teacher last name:  </td><td>#te_lname#</td></tr>
      <tr><td>" . ObtenEtiqueta(732) . ":  </td><td>#current_month#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td style='text-decoration: underline;'>Tuition Payment Variables</td></tr>
      <tr><td>Payment date:  </td><td>#py_date#</td></tr>
      <tr><td>Payment amount:  </td><td>#py_amount#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td style='text-decoration: underline;'>Assignment Reminder Variables</td></tr>
      <tr><td>Tab names:  </td><td>#nb_tabs#</td></tr>
      <tr><td>Minimum GPA:  </td><td>#minimum_gpa#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td style='text-decoration: underline;'>Missed Class Variables</td></tr>
      <tr><td>Attendance Warning/Attendance Probation:  </td><td>#number_of_absences#</td></tr>
      <tr><td>Missed Class History:  </td><td>#missed_class_term_history#</td></tr>
      <tr><td>&nbsp</td></tr>
      <tr><td style='text-decoration: underline;'>" . ObtenEtiqueta(1126) . "</td><td>#minimum_gpa#</td></tr>
      <tr><td>First name:  </td><td>#ds_fname_r#</td></tr>
      <tr><td>Last name:  </td><td>#ds_lname_r#</td></tr>
      <tr><td>Email:  </td><td>#ds_email_r#</td></tr>
      <tr><td>Email Alternative:  </td><td>#ds_aemail_r#</td></tr>
      <tr><td>Number:  </td><td>#ds_pnumber_r#</td></tr>
      <tr><td>relation:  </td><td>#ds_relation_r#</td></tr>
    </table>";

# Presenta forma de captura
PresentaHeader();
PresentaEncabezado(FUNC_DOC_TEMPLATES);
Forma_Inicia($clave);
Forma_Doble_Ini('right');
echo "
      <button class='btn btn-primary' type='button' name='reset' onClick='javascript:resetea();'>" . ObtenEtiqueta(579) . "</button>
      <button class='btn btn-primary' type='button' name='factory' onClick='javascript:guarda_fact();'>" . ObtenEtiqueta(569) . "</button>";
Forma_Doble_Fin();
if ($fg_error)
  Forma_PresentaError();
Forma_Espacio();
Forma_CampoInfo('', ObtenEtiqueta(577));
Forma_Espacio();
# modificamos el campo del nombre del archivo para que no se pueda modificar cuando reciba la clave
if (empty($clave))
  $edita = "disabled";
else
  $edita = "readonly ";

Forma_CampoTexto(ObtenEtiqueta(19), True, 'nb_template', $nb_template, 255, 60 . "' $edita", $nb_template_err);
$Query  = "SELECT nb_categoria, fl_categoria FROM c_categoria_doc ORDER BY nb_categoria";
Forma_CampoSelectBD(ObtenEtiqueta(570), True, 'fl_categoria', $Query, $fl_categoria, $fl_categoria_err, True);
Forma_CampoCheckbox(ObtenEtiqueta(113), 'fg_activo', $fg_activo);
Forma_Espacio();
Forma_Plegable_Ini('Variables available for the document', 1);
echo $ds_variables;
Forma_Plegable_Fin();
Forma_Plegable_Ini('Variables available for email notification', 2);
echo $ds_email_var;
Forma_Plegable_Fin();
Forma_Espacio();
?>
<div>
  <div class="widget-body">
    <ul id="myTab_template" class="nav nav-tabs bordered">
      <li class="active">
        <a id="mytabTemp1" href="#template_eng" data-toggle="tab">
          English
        </a>
      </li>
      <li class="">
        <a id="mytabTemp2" href="#template_esp" data-toggle="tab">
          Spanish
        </a>
      </li>
      <li class="">
        <a id="mytabTemp3" href="#template_fra" data-toggle="tab">
          French
        </a>
      </li>
        <li class="">
            <a id="mytabTemp4" href="#template_de" data-toggle="tab">
                Germany
            </a>
        </li>
        <li class="">
            <a id="mytabTemp5" href="#template_it" data-toggle="tab">
                Italy
            </a>
        </li>
    </ul>
    <div id="myTabDescCont" class="tab-content padding-10 no-border">

      <div class="tab-pane fade in active" id="template_eng">
        <!--  UMP START of English Content-->
        <?php
        Forma_CampoTinyMCE(ObtenEtiqueta(571), False, 'ds_encabezado', $ds_encabezado, 50, 20, '', True);
        Forma_CampoTinyMCE(ObtenEtiqueta(572), False, 'ds_cuerpo', $ds_cuerpo, 50, 20, '', True);
        Forma_Espacio();
        Forma_CampoInfo('', ObtenEtiqueta(578));
        Forma_CampoTinyMCE(ObtenEtiqueta(573), False, 'ds_pie', $ds_pie, 50, 20, '', True);
        Forma_Espacio();
        ?>
        <!--  UMP END of English Content-->
      </div>
      <div class="tab-pane fade in " id="template_esp">
        <!--  UMP START of Spanish Content-->
        <?php
        Forma_CampoTinyMCE(ObtenEtiqueta(571), False, 'ds_encabezado_esp', $ds_encabezado_esp, 50, 20, '', True);
        Forma_CampoTinyMCE(ObtenEtiqueta(572), False, 'ds_cuerpo_esp', $ds_cuerpo_esp, 50, 20, '', True);
        Forma_Espacio();
        //Forma_CampoInfo('', ObtenEtiquetaLang(578, 1));
        //Forma_CampoTinyMCE(ObtenEtiqueta(573), False, 'ds_pie_esp', $ds_pie, 50, 20, '', True);
        ?>
        <!--  UMP END of Spanish Content-->
      </div>
      <div class="tab-pane fade in " id="template_fra">
        <!--  UMP START of French Content-->
        <?php
        Forma_CampoTinyMCE(ObtenEtiqueta(571), False, 'ds_encabezado_fra', $ds_encabezado_fra, 50, 20, '', True);
        Forma_CampoTinyMCE(ObtenEtiqueta(572), False, 'ds_cuerpo_fra', $ds_cuerpo_fra, 50, 20, '', True);
        Forma_Espacio();
        //Forma_CampoInfo('', ObtenEtiquetaLang(578, 3));
        //Forma_CampoTinyMCE(ObtenEtiqueta(573), False, 'ds_pie_fra', $ds_pie, 50, 20, '', True);
        ?>
        <!--  UMP END of French Content-->
      </div>
        <div class="tab-pane fade in " id="template_de">
            <!--  UMP START of French Content-->
            <?php
        Forma_CampoTinyMCE(ObtenEtiqueta(571), False, 'ds_encabezado_de', $ds_encabezado_de, 50, 20, '', True);
        Forma_CampoTinyMCE(ObtenEtiqueta(572), False, 'ds_cuerpo_de', $ds_cuerpo_de, 50, 20, '', True);
        Forma_Espacio();
        //Forma_CampoInfo('', ObtenEtiquetaLang(578, 3));
        //Forma_CampoTinyMCE(ObtenEtiqueta(573), False, 'ds_pie_fra', $ds_pie, 50, 20, '', True);
            ?>
            <!--  UMP END of French Content-->
        </div>
        <div class="tab-pane fade in " id="template_it">
            <!--  UMP START of French Content-->
            <?php
        Forma_CampoTinyMCE(ObtenEtiqueta(571), False, 'ds_encabezado_it', $ds_encabezado_it, 50, 20, '', True);
        Forma_CampoTinyMCE(ObtenEtiqueta(572), False, 'ds_cuerpo_it', $ds_cuerpo_it, 50, 20, '', True);
        Forma_Espacio();
        //Forma_CampoInfo('', ObtenEtiquetaLang(578, 3));
        //Forma_CampoTinyMCE(ObtenEtiqueta(573), False, 'ds_pie_fra', $ds_pie, 50, 20, '', True);
            ?>
            <!--  UMP END of French Content-->
        </div>
    </div>
  </div>
</div>
<?php
Forma_CampoOculto('fg_reset', 0);
Forma_CampoOculto('fg_factory', 0);
# Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
if ($permiso == PERMISO_DETALLE)
  $fg_guardar = ValidaPermiso(FUNC_DOC_TEMPLATES, PERMISO_MODIFICACION);
else
  $fg_guardar = True;
Forma_Termina($fg_guardar);
echo "
<script>
    function resetea() 
    {
        document.getElementById('fg_reset').value = 1;
        document.datos.submit();
    }
    function guarda_fact() 
    {
        document.getElementById('fg_factory').value = 1;
        document.datos.submit();
    }
</script>";

# Pie de Pagina
PresentaFooter();
