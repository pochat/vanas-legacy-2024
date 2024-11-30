<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  require("../lib/sp_forms.inc.php");
  require("app_form.inc.php");
  
  # Revisa si hay una sesion activa para reiniciarla, si no, genera una nueva
  $clave = SP_RecuperaSesion( );
  $row = RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_1 WHERE cl_sesion='$clave'");
  if(empty($clave) OR $row[0] == '0') {
    $clave = SP_GeneraSesion( );
    $fg_nueva = True;
  }
  else {
    SP_ActualizaSesion($clave);
    $fg_nueva = False;
  }
  
  # Recibe parametros
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Inicializa variables
  if(!$fg_error) { // Sin error
    if(!$fg_nueva) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
      $Query .= ConsultaFechaBD('fe_birth', FMT_CAPTURA)." fe_birth, ";
      $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, ";
      $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, ds_eme_country, ";
      $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, fl_programa, fl_periodo, ds_link_to_portfolio, ds_add_foto, ";
      $Query .= "fg_responsable, cl_recruiter ";
      $Query .= "FROM k_ses_app_frm_1 ";
      $Query .= "WHERE cl_sesion='$clave'";
      $row = RecuperaValor($Query);      
      $ds_fname = str_texto($row[0]);
      $ds_mname = str_texto($row[1]);
      $ds_lname = str_texto($row[2]);
      $ds_number = str_texto($row[3]);
      $ds_alt_number = str_texto($row[4]);
      $ds_email = str_texto($row[5]);
      $ds_email_conf = str_texto($row[5]);
      $fg_gender = str_texto($row[6]);
      $fe_birth = $row[7];
      $ds_add_number = str_texto($row[8]);
      $ds_add_street = str_texto($row[9]);
      $ds_add_city = str_texto($row[10]);
      $ds_add_state = str_texto($row[11]);
      $ds_add_zip = str_texto($row[12]);
      $ds_add_country = str_texto($row[13]);
      $ds_eme_fname = str_texto($row[14]);
      $ds_eme_lname = str_texto($row[15]);
      $ds_eme_number = str_texto($row[16]);
      $ds_eme_relation = str_texto($row[17]);
      $ds_eme_country = str_texto($row[18]);
      $fg_ori_via = str_texto($row[19]);
      $ds_ori_other = str_texto($row[20]);
      $fg_ori_ref = str_texto($row[21]);
      $ds_ori_ref_name = str_texto($row[22]);
      $fl_programa = $row[23];
      $fl_periodo = $row[24];
      $ds_link_to_portfolio = str_texto($row[25]);
      $ds_add_foto = str_texto($row[26]);
      $fg_responsable = $row[27];
      $cl_recruiter = $row[28];
      $Query  = "SELECT ds_p_name, ds_education_number, fg_international, cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, 
      ds_m_add_state, ds_m_add_zip, ds_m_add_country, ds_a_email, cl_preference_3 ";
      $Query .= "FROM k_app_contrato ";
      $Query .= "WHERE cl_sesion='$clave'";
      $row = RecuperaValor($Query);
      $ds_p_name = str_texto($row[0]);
      $ds_education_number = str_texto($row[1]);
      $fg_international = $row[2];
      $cl_preference_1 = $row[3];
      $cl_preference_2 = $row[4];
      $ds_m_add_number = str_texto($row[5]);
      $ds_m_add_street = str_texto($row[6]);
      $ds_m_add_city = str_texto($row[7]);
      $ds_m_add_state = str_texto($row[8]);
      $ds_m_add_zip = str_texto($row[9]);
      $ds_m_add_country = str_texto($row[10]);
      $ds_a_email = str_texto($row[11]);      
      $cl_preference_3 = $row[12];
      $fl_provincia = $ds_add_state;
      $Queryr  = "SELECT ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r ";
      $Queryr .= "FROM k_presponsable WHERE cl_sesion='$clave";
      $rowr = RecuperaValor($Queryr);
      $ds_fname_r = str_texto($rowr[0]);
      $ds_lname_r = str_texto($rowr[1]);
      $ds_email_r = str_texto($rowr[2]);
      $ds_aemail_r = str_texto($rowr[3]);
      $ds_pnumber_r = str_texto($rowr[4]);
      $ds_relation_r = str_texto($rowr[5]);
    }
    else { // Alta, inicializa campos      
      $ds_fname = "";
      $ds_mname = "";
      $ds_lname = "";
      $ds_number = "";
      $ds_alt_number = "";
      $ds_email = "";
      $ds_email_conf = "";
      $fg_gender = "";
      $fe_birth = "";
      $ds_add_number = "";
      $ds_add_street = "";
      $ds_add_city = "";
      $ds_add_state = "";
      $ds_add_zip = "";
      $ds_add_country = "";
      $ds_eme_fname = "";
      $ds_eme_lname = "";
      $ds_eme_number = "";
      $ds_eme_relation = "";
      $ds_eme_country = "";
      $fg_ori_via = "";
      $ds_ori_other = "";
      $fg_ori_ref = "";
      $ds_ori_ref_name = "";
      $fl_programa = "";
      $fl_periodo = "";
      $ds_link_to_portfolio = "";
      $ds_p_name = "";
      $ds_education_number = "";      
      $fg_international = "";
      $cl_preference_1 = 0;
      $cl_preference_2 = 0;
      $ds_m_add_number = "";
      $ds_m_add_street = "";
      $ds_m_add_city = "";
      $ds_m_add_state = "";
      $ds_m_add_zip = "";
      $ds_m_add_country = "";
      $ds_a_email = "";
      $fl_provincia = $ds_add_state;
      $ds_add_foto = "";
      $fg_responsable = "";
      $ds_fname_r = "";
      $ds_lname_r = "";
      $ds_email_r = "";
      $ds_aemail_r = "";
      $ds_pnumber_r = "";
      $ds_relation_r = "";
    }
    $fl_programa_err = "";
    $fl_periodo_err = "";
    $ds_fname_err = "";
    $ds_lname_err = "";
    $ds_number_err = "";
    $ds_alt_number_err = "";
    $ds_email_err = "";
    $ds_email_conf_err = "";
    $fg_gender_err = "";
    $fe_birth_err = "";
    $ds_add_number_err = "";
    $ds_add_street_err = "";
    $ds_add_city_err = "";
    $ds_add_state_err = "";
    $ds_add_zip_err = "";
    $ds_add_country_err = "";
    $ds_eme_fname_err = "";
    $ds_eme_lname_err = "";
    $ds_eme_number_err = "";
    $ds_eme_relation_err = "";
    $ds_eme_country_err = "";
    $fg_ori_via_err = "";
    $ds_ori_other_err = "";
    $fg_ori_ref_err = "";
    $ds_ori_ref_name_err = "";
    $fg_international_err = "";
    $cl_preference_1_err = "";
    $cl_preference_2_err = "";
    $ds_add_foto_err = "";
    $fg_responsable_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)    
    $ds_fname = RecibeParametroHTML('ds_fname', True);
    $ds_fname_err = RecibeParametroNumerico('ds_fname_err');
    $ds_mname = RecibeParametroHTML('ds_mname', True);
    $ds_lname = RecibeParametroHTML('ds_lname', True);
    $ds_lname_err = RecibeParametroNumerico('ds_lname_err');
    $ds_number = RecibeParametroHTML('ds_number');
    $ds_number_err = RecibeParametroNumerico('ds_number_err');
    $ds_alt_number = RecibeParametroHTML('ds_alt_number');
    $ds_alt_number_err = RecibeParametroNumerico('ds_alt_number_err');
    $ds_email = RecibeParametroHTML('ds_email');
    $ds_email_err = RecibeParametroNumerico('ds_email_err');
    $ds_email_conf = RecibeParametroHTML('ds_email_conf');
    $ds_email_conf_err = RecibeParametroNumerico('ds_email_conf_err');
    $fg_gender = RecibeParametroHTML('fg_gender');
    $fg_gender_err = RecibeParametroNumerico('fg_gender_err');
    $fe_birth = RecibeParametroFecha('fe_birth');
    $fe_birth_err = RecibeParametroNumerico('fe_birth_err');
    $ds_add_number = RecibeParametroHTML('ds_add_number');
    $ds_add_number_err = RecibeParametroNumerico('ds_add_number_err');
    $ds_add_street = RecibeParametroHTML('ds_add_street');
    $ds_add_street_err = RecibeParametroNumerico('ds_add_street_err');
    $ds_add_city = RecibeParametroHTML('ds_add_city');
    $ds_add_city_err = RecibeParametroNumerico('ds_add_city_err');
    $ds_add_state = RecibeParametroHTML('ds_add_state');
    $ds_add_state_err = RecibeParametroNumerico('ds_add_state_err');
    $ds_add_zip = RecibeParametroHTML('ds_add_zip');
    $ds_add_zip_err = RecibeParametroNumerico('ds_add_zip_err');
    $ds_add_country = RecibeParametroHTML('ds_add_country');
    $ds_add_country_err = RecibeParametroNumerico('ds_add_country_err');
    $ds_eme_fname = RecibeParametroHTML('ds_eme_fname');
    $ds_eme_fname_err = RecibeParametroNumerico('ds_eme_fname_err');
    $ds_eme_lname = RecibeParametroHTML('ds_eme_lname');
    $ds_eme_lname_err = RecibeParametroNumerico('ds_eme_lname_err');
    $ds_eme_number = RecibeParametroHTML('ds_eme_number');
    $ds_eme_number_err = RecibeParametroNumerico('ds_eme_number_err');
    $ds_eme_relation = RecibeParametroHTML('ds_eme_relation');
    $ds_eme_relation_err = RecibeParametroNumerico('ds_eme_relation_err');
    $ds_eme_country = RecibeParametroHTML('ds_eme_country');
    $ds_eme_country_err = RecibeParametroNumerico('ds_eme_country_err');
    $fg_ori_via = RecibeParametroHTML('fg_ori_via');
    $fg_ori_via_err = RecibeParametroNumerico('fg_ori_via_err');
    $ds_ori_other = RecibeParametroHTML('ds_ori_other');
    $ds_ori_other_err = RecibeParametroNumerico('ds_ori_other_err');
    $fg_ori_ref = RecibeParametroHTML('fg_ori_ref');
    $fg_ori_ref_err = RecibeParametroNumerico('fg_ori_ref_err');
    $ds_ori_ref_name = RecibeParametroHTML('ds_ori_ref_name');
    $ds_ori_ref_name_err = RecibeParametroNumerico('ds_ori_ref_name_err');
    $fl_programa = RecibeParametroNumerico('fl_programa');
    $fl_programa_err = RecibeParametroNumerico('fl_programa_err');
    $fl_periodo = RecibeParametroNumerico('fl_periodo');
    $fl_periodo_err = RecibeParametroNumerico('fl_periodo_err');
    $ds_link_to_portfolio = RecibeParametroHTML('ds_link_to_portfolio');    
    $ds_p_name = RecibeParametroHTML('ds_p_name');
    $ds_education_number = RecibeParametroHTML('ds_education_number');
    $fg_international = RecibeParametroHTML('fg_international');
    $fg_international_err = RecibeParametroNumerico('fg_international_err');
    $cl_preference_1 = RecibeParametroNumerico('cl_preference_1');
    $cl_preference_1_err = RecibeParametroNumerico('cl_preference_1_err');
    $cl_preference_2 = RecibeParametroNumerico('cl_preference_2');
    $cl_preference_2_err = RecibeParametroNumerico('cl_preference_2_err');
    $ds_m_add_number = RecibeParametroHTML('ds_m_add_number');
    $ds_m_add_street = RecibeParametroHTML('ds_m_add_street');
    $ds_m_add_city = RecibeParametroHTML('ds_m_add_city');
    $ds_m_add_state = RecibeParametroHTML('ds_m_add_state');
    $ds_m_add_zip = RecibeParametroHTML('ds_m_add_zip');
    $ds_m_add_country = RecibeParametroHTML('ds_m_add_country');
    $ds_a_email = RecibeParametroHTML('ds_a_email');
    $cl_preference_3 = RecibeParametroNumerico('cl_preference_3');
    $cl_preference_3_err = RecibeParametroNumerico('cl_preference_3_err');
    $fg_provincia = RecibeParametroNumerico('fg_provincia');
    $fl_provincia = RecibeParametroNumerico('fl_provincia');
    $ds_ruta_foto = RecibeParametroHTML("ds_ruta_foto");
    $ds_ruta_foto_err = RecibeParametroHTML("ds_ruta_foto_err");
    $fg_responsable = RecibeParametroBinario("fg_responsable");
    $fg_responsable_err = RecibeParametroNumerico("fg_responsable_err");
    $ds_fname_r = RecibeParametroHTML('ds_fname_r');
    $ds_fname_r_err = RecibeParametroNumerico('ds_fname_r_err');
    $ds_lname_r = RecibeParametroHTML('ds_lname_r');
    $ds_lname_r_err = RecibeParametroNumerico('ds_lname_r_err');
    $ds_email_r = RecibeParametroHTML('ds_email_r');
    $ds_email_r_err = RecibeParametroHTML('ds_email_r_err');
    $ds_aemail_r = RecibeParametroHTML('ds_aemail_r');
    $ds_aemail_r_err = RecibeParametroHTML('ds_aemail_r_err');
    $ds_pnumber_r = RecibeParametroHTML('ds_pnumber_r');
    $ds_pnumber_r_err = RecibeParametroHTML('ds_pnumber_r_err');
    $ds_relation_r = RecibeParametroHTML('ds_relation_r');
    $ds_relation_r_err = RecibeParametroHTML('ds_relation_r_err');
    $cl_recruiter = RecibeParametroNumerico('cl_recruiter');
    $cl_recruiter_err = RecibeParametroNumerico('cl_recruiter_err');
  }
  
  # Header
  PresentaHeaderAF( );
  
  # Cuerpo de la pagina
  echo "
    <table border='".D_BORDES."' width='100%' height='584' valign='top' cellspacing='0' cellpadding='0' class='app_form'>
      <tr>
        <td width='20' height='20'>&nbsp;</td>
        <td>&nbsp;</td>
        <td width='20'>&nbsp;</td>
      </tr>
      <tr>
        <td height='30'>&nbsp;</td>
        <td><b>".ObtenEtiqueta(55)."</b></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><br>".ObtenEtiqueta(71)."</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td valign='top'>\n";
  
  # Inicia la forma de captura
  Forma_Inicia($clave,True, 'ABSP4MDSFSDF8V_iu.php');
  if($fg_error)
    Forma_PresentaError( );
  Forma_Espacio( );
  
  # Select de Periodos dependiente del select de Programas
  $rs = EjecutaQuery("SELECT fl_programa FROM c_programa WHERE fg_archive='0' ORDER BY no_orden");
  $claves_periodos = "";
  $textos_periodos = "";
  $programas = "";
  for($i = 1; $row = RecuperaRegistro($rs); $i++) {
    $claves_periodos .= "claves_fl_periodo[$row[0]] = new Array(";
    $textos_periodos .= "textos_fl_periodo[$row[0]] = new Array(";
    $Query  = "SELECT fl_periodo, nb_periodo ";
    $Query .= "FROM c_periodo a ";
    $Query .= "WHERE EXISTS (SELECT 1 FROM k_term b WHERE b.fl_periodo=a.fl_periodo AND fl_programa=$row[0] AND no_grado=1) ";
    $Query .= "AND fg_activo='1' ";
    $Query .= "ORDER BY fe_inicio";
    $rs2 = EjecutaQuery($Query);
    for($j = 1; $row2 = RecuperaRegistro($rs2); $j++) {
      if($j > 1) {
        $claves_periodos .= ",";
        $textos_periodos .= ",";
      }
      else {
        if(!empty($programas))
          $programas .= ",";
        $programas .= "$row[0]";
      }
      $claves_periodos .= "'$row2[0]'";
      $textos_periodos .= "'$row2[1]'";
    }
    $claves_periodos .= ");\n";
    $textos_periodos .= ");\n";
  }
  echo "
<script language='javascript'>
  var claves_fl_periodo = new Array();
    $claves_periodos
  var textos_fl_periodo = new Array();
    $textos_periodos
  
  function Replace(MasterObj, DetailName) {
    var valorMaster = MasterObj.value;
    
    if(valorMaster == '')
      valorMaster = 0;
    
    itemDetalle = eval('document.datos.' + DetailName);
    clavesNuevasOpciones = eval('claves_' + DetailName + '[' + valorMaster + ']');
    textosNuevasOpciones = eval('textos_' + DetailName + '[' + valorMaster + ']');
    
    // Limpia las opciones del combo
    itemDetalle.length = 0;
    
    // Crea las opciones del combo relacionado, toma los valores de los arreglos de claves y textos
    itemDetalle.options[0] = new Option('".ObtenEtiqueta(70)."', '0');
    for(i = 0, j = 1; i < clavesNuevasOpciones.length; i++, j++) {
      if(clavesNuevasOpciones[i] == '$fl_periodo')
        itemDetalle.options[j] = new Option(textosNuevasOpciones[i], clavesNuevasOpciones[i], false, true);
      else
        itemDetalle.options[j] = new Option(textosNuevasOpciones[i], clavesNuevasOpciones[i]);
    }
  }
  
</script>\n";
  
  # Program
  $concat = array('nb_programa', "' ('", 'ds_duracion', "' - '", 'ds_tipo', "')'");
  $Query  = "SELECT ".ConcatenaBD($concat)." 'ds_texto', fl_programa ";
  $Query .= "FROM c_programa ";
  $Query .= "WHERE fl_programa IN($programas) ";
  $Query .= "ORDER BY nb_programa";
  Forma_CampoSelectBD(ObtenEtiqueta(59), True, 'fl_programa', $Query, $fl_programa, $fl_programa_err, True, 
  "onChange=\"Replace(this, 'fl_periodo');\"");
  Forma_CampoSelectBD(ObtenEtiqueta(60), True, 'fl_periodo', '', $fl_periodo, $fl_periodo_err, True);
  if(!empty($fl_programa))
    echo "<script language='javascript'> Replace(document.datos.fl_programa, 'fl_periodo'); </script>\n";
  
  # Contact information
  Forma_Seccion(ObtenEtiqueta(61));
  Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_fname', $ds_fname, 50, 32, $ds_fname_err);
  Forma_CampoTexto(ObtenEtiqueta(119), False, 'ds_mname', $ds_mname, 50, 32);
  Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_lname', $ds_lname, 50, 32, $ds_lname_err);
  $ruta = PATH_ALU_IMAGES."/id";
  Forma_CampoUpload(ObtenEtiqueta(810), "", 'ds_ruta_foto', $ds_ruta_foto, $ruta, True, 'foto', 60, $ds_ruta_foto_err, 'jpg|jpeg');
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(631), False, 'ds_p_name', $ds_p_name, 250, 32);
  Forma_CampoTexto(ObtenEtiqueta(632), False, 'ds_education_number', $ds_education_number, 50, 32);
  Forma_Sencilla_Ini("*".ObtenEtiqueta(620));
  CampoRadio('fg_international', '1', $fg_international, ObtenEtiqueta(16));
  echo "&nbsp;&nbsp;";
  CampoRadio('fg_international', '0', $fg_international, ObtenEtiqueta(17));
  Forma_Sencilla_Fin( );
  Forma_Error($fg_international_err);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(280), True, 'ds_number', $ds_number, 20, 16, $ds_number_err);
  Forma_CampoTexto(ObtenEtiqueta(281), True, 'ds_alt_number', $ds_alt_number, 20, 16, $ds_alt_number_err);
  Forma_CampoTexto(ObtenEtiqueta(121), True, 'ds_email', $ds_email, 50, 32, $ds_email_err);
  Forma_CampoTexto(ObtenEtiqueta(299), True, 'ds_email_conf', $ds_email_conf, 50, 32, $ds_email_conf_err);
  Forma_CampoTexto(ObtenEtiqueta(127), False, 'ds_a_email', $ds_a_email, 50, 32);
  Forma_CampoTexto(ObtenEtiqueta(339), False, 'ds_link_to_portfolio', $ds_link_to_portfolio, 255, 32);
  Forma_Espacio( );
  $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116)); // Masculino, Femenino
  $val = array('M', 'F');
  Forma_CampoSelect(ObtenEtiqueta(114), True, 'fg_gender', $opc, $val, $fg_gender, $fg_gender_err, True);
  Forma_CampoTexto(ObtenEtiqueta(120).' '.ETQ_FMT_FECHA, True, 'fe_birth', $fe_birth, 10, 10, $fe_birth_err);
  Forma_Calendario('fe_birth');
  Forma_Seccion(ObtenEtiqueta(621));
  $opc = array(ObtenEtiqueta(624), ObtenEtiqueta(625), ObtenEtiqueta(626), ObtenEtiqueta(627), ObtenEtiqueta(628), ObtenEtiqueta(629), ObtenEtiqueta(630));
  $val = array('1', '2', '3', '4', '5', '6', '7');
  Forma_CampoSelect(ObtenEtiqueta(622), True, 'cl_preference_1', $opc, $val, $cl_preference_1, $cl_preference_1_err, True);
  Forma_CampoSelect(ObtenEtiqueta(623), True, 'cl_preference_2', $opc, $val, $cl_preference_2, $cl_preference_2_err, True);
  Forma_CampoSelect(ObtenEtiqueta(616), True, 'cl_preference_3', $opc, $val, $cl_preference_3, $cl_preference_3_err, True);
  
  # Address
  Forma_Seccion(ObtenEtiqueta(62)); 
  $Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
  Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'ds_add_country', $Query, $ds_add_country, $ds_add_country_err, True);
  # Script para manejo de llas provincias
  echo "
  <script type='text/javascript'>
  $(document).ready(
    function(){
      var provincia = '$fg_provincia';
      if(provincia==38){
        $('#fl_provincia').css('display','block');
        $('#ds_add_state').css('display','none');
        $('#fg_provincia').val(38);
      }else{
        $('#fl_provincia').css('display','none');
        $('#ds_add_state').css('display','block');
        $('#fg_provincia').val(0);
      }
      $('#ds_add_country').change(
        function provincia(){
          if($(this).val()==38){
            $('#fl_provincia').css('display','block');
            $('#ds_add_state').css('display','none');
            $('#fg_provincia').val(38);
          }else{
            $('#fl_provincia').css('display','none');
            $('#ds_add_state').css('display','block');
            $('#fg_provincia').val(0);
          } 
        }
      );
    }
  );
  </script>";

  # si hay error
  if(!empty($fg_error) AND ((empty($ds_add_state) AND $fg_provincia!=38) OR ($fg_provincia==38 AND $fl_provincia==0))){
    $ds_error = ObtenMensaje($fg_error);
    $ds_clase = 'css_input_error';
  }
  else {
    $ds_clase = 'css_input';
    $ds_error = "";
  }
  # Muestra el campo  o las provincias de canada
  echo "
  <tr>
    <td class='css_prompt' align='right' valign='middle'>* ".ObtenEtiqueta(285)."</td>
    <td align='left' valign='middle'>
      <input type='hidden' id='fg_provincia' name='fg_provincia'>
      <!--Campo de texto para las provincias-->
      <input class='$ds_clase' id='ds_add_state' name='ds_add_state' value='$ds_add_state' maxlength='50' size='32' type='text'>
      <!--Select para lasprovicnias de canada -->
      <select class='$ds_clase' id='fl_provincia' name='fl_provincia'>
        <option value=0>".ObtenEtiqueta(70)."</option>";
        $Query  = "SELECT ds_provincia, fl_provincia FROM k_provincias WHERE fl_pais=38 ORDER BY ds_provincia";
        $rs = EjecutaQuery($Query);
        for($i=0;$row = RecuperaRegistro($rs);$i++){
          $ds_provincia = $row[0];
          $fl_provincia_o = $row[1];
          if($fl_provincia_o==$fl_provincia)
            $select = "selected";
          else
            $select = "";
          echo "<option ".$select." value=".$fl_provincia_o." >".$ds_provincia."</option>";
        }
  echo "
        </select>
    </td>
  </tr>";

  Forma_CampoTexto(ObtenEtiqueta(284), True, 'ds_add_city', $ds_add_city, 50, 32, $ds_add_city_err);  
  Forma_CampoTexto(ObtenEtiqueta(283), True, 'ds_add_street', $ds_add_street, 50, 32, $ds_add_street_err);
  Forma_CampoTexto(ObtenEtiqueta(282), True, 'ds_add_number', $ds_add_number, 20, 16, $ds_add_number_err);
  Forma_CampoTexto(ObtenEtiqueta(286), True, 'ds_add_zip', $ds_add_zip, 20, 16, $ds_add_zip_err);
  
  # Script para ocultar o mostrar campos persona responsable
  echo "
  <script type='text/javascript'>
    function Add_responsable(){
      var responsable = $(\"input[name='fg_responsable']:checked\").val();
      if(responsable == '1'){
        $('#fname_r_ppt').attr('style', 'display:inline;');
        $('#fname_r').attr('style', 'display:inline;');
        $('#fname_r_err').attr('style', 'display:inline;');
        $('#lname_r_ppt').attr('style', 'display:inline;');
        $('#lname_r').attr('style', 'display:inline;');
        $('#lname_r_err').attr('style', 'display:inline;');
        $('#email_r_ppt').attr('style', 'display:inline;');
        $('#email_r').attr('style', 'display:inline;');
        $('#email_r_err').attr('style', 'display:inline;');
        $('#aemail_r_ppt').attr('style', 'display:inline;');
        $('#aemail_r').attr('style', 'display:inline;');
        $('#aemail_r_err').attr('style', 'display:inline;');
        $('#pnumber_r_ppt').attr('style', 'display:inline;');
        $('#pnumber_r').attr('style', 'display:inline;');
        $('#pnumber_r_err').attr('style', 'display:inline;');
        $('#relation_r_ppt').attr('style', 'display:inline;');
        $('#relation_r').attr('style', 'display:inline;');
        $('#relation_r_err').attr('style', 'display:inline;');
      }
      else{
        $('#fname_r_ppt').attr('style', 'display:none;');
        $('#fname_r').attr('style', 'display:none;');
        $('#fname_r_err').attr('style', 'display:none;');
        $('#lname_r_ppt').attr('style', 'display:none;');
        $('#lname_r').attr('style', 'display:none;');
        $('#lname_r_err').attr('style', 'display:none;');
        $('#email_r_ppt').attr('style', 'display:none;');
        $('#email_r').attr('style', 'display:none;');
        $('#email_r_err').attr('style', 'display:none;');
        $('#aemail_r_ppt').attr('style', 'display:none;');
        $('#aemail_r').attr('style', 'display:none;');
        $('#aemail_r_err').attr('style', 'display:none;');
        $('#pnumber_r_ppt').attr('style', 'display:none;');
        $('#pnumber_r').attr('style', 'display:none;');
        $('#pnumber_r_err').attr('style', 'display:none;');
        $('#relation_r_ppt').attr('style', 'display:none;');
        $('#relation_r').attr('style', 'display:none;');
        $('#relation_r_err').attr('style', 'display:none;');
      }
    }
  </script>";  
  # Persona Responsable de los pagos
  Forma_Seccion(ObtenEtiqueta(865));
  Forma_CampoRadio('', 'fg_responsable', '0', $fg_responsable, ObtenEtiqueta(866),True, " onClick='javascript:Add_responsable();'");
  Forma_CampoRadio('', 'fg_responsable', '1', $fg_responsable, ObtenEtiqueta(867),True, " onClick='javascript:Add_responsable();'");
  if(empty($fg_responsable))
    $fg_respon = False;
  else
    $fg_respon = True;
  Forma_CampoTexto(ObtenEtiqueta(868), True, 'ds_fname_r', $ds_fname_r, 50, 32, $ds_fname_r_err, False, 'fname_r', $fg_respon);
  Forma_CampoTexto(ObtenEtiqueta(869), True, 'ds_lname_r', $ds_lname_r, 50, 32, $ds_lname_r_err, False, 'lname_r', $fg_respon);
  Forma_CampoTexto(ObtenEtiqueta(870), True, 'ds_email_r', $ds_email_r, 50, 32, $ds_email_r_err, False, 'email_r', $fg_respon);
  Forma_CampoTexto(ObtenEtiqueta(871), False, 'ds_aemail_r', $ds_aemail_r, 50, 32, $ds_aemail_r_err, False, 'aemail_r', $fg_respon);
  Forma_CampoTexto(ObtenEtiqueta(872), True, 'ds_pnumber_r', $ds_pnumber_r, 50, 32, $ds_pnumber_r_err, False, 'pnumber_r', $fg_respon);
  Forma_CampoTexto(ObtenEtiqueta(873), True, 'ds_relation_r', $ds_relation_r, 50, 32, $ds_relation_r_err, False, 'relation_r', $fg_respon);
  
  # Mailing Address
  Forma_Seccion(ObtenEtiqueta(633));
  Forma_CampoTexto(ObtenEtiqueta(282), False, 'ds_m_add_number', $ds_m_add_number, 20, 16);
  Forma_CampoTexto(ObtenEtiqueta(283), False, 'ds_m_add_street', $ds_m_add_street, 50, 32);
  Forma_CampoTexto(ObtenEtiqueta(284), False, 'ds_m_add_city', $ds_m_add_city, 50, 32);
  Forma_CampoTexto(ObtenEtiqueta(285), False, 'ds_m_add_state', $ds_m_add_state, 50, 32);
  Forma_CampoTexto(ObtenEtiqueta(286), False, 'ds_m_add_zip', $ds_m_add_zip, 20, 16);
  $Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
  Forma_CampoSelectBD(ObtenEtiqueta(287), False, 'ds_m_add_country', $Query, $ds_m_add_country, '', True);
  
  # Emergency Contact Information
  Forma_Seccion(ObtenEtiqueta(63));
  Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_eme_fname', $ds_eme_fname, 50, 32, $ds_eme_fname_err);
  Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_eme_lname', $ds_eme_lname, 50, 32, $ds_eme_lname_err);
  Forma_CampoTexto(ObtenEtiqueta(280), True, 'ds_eme_number', $ds_eme_number, 20, 16, $ds_eme_number_err);
  Forma_CampoTexto(ObtenEtiqueta(288), True, 'ds_eme_relation', $ds_eme_relation, 50, 32, $ds_eme_relation_err);
  $Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
  Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'ds_eme_country', $Query, $ds_eme_country, $ds_eme_country_err, True);
  
  # Representative
  Forma_Seccion(ObtenEtiqueta(876));  
  $Query  = "SELECT CONCAT( ds_nombres, ' ', ds_apaterno ) , fl_usuario FROM c_usuario usr, c_perfil per ";
  $Query .= "WHERE usr.fl_perfil = per.fl_perfil AND usr.fl_perfil=".PERFIL_RECRUITER." ORDER BY fg_default ASC , ds_nombres ASC ";
  Forma_CampoSelectBD(ObtenEtiqueta(877), True, 'cl_recruiter', $Query, $cl_recruiter, $cl_recruiter_err, False);
  
  # Script para mostrar / ocultar campos
  echo "<script language='javascript'>
    function cambia_other() {
      if($(\"input[name='fg_ori_via']:checked\").val() == '0') {
        $('#other_ppt').attr('style', 'display:inline;');
        $('#other').attr('style', 'display:inline;');
        $('#other_err').attr('style', 'display:inline;');
      }
      else {
        $('#other_ppt').attr('style', 'display:none;');
        $('#other').attr('style', 'display:none;');
        $('#other_err').attr('style', 'display:none;');
      }
    }
    function cambia_ref() {
      if($(\"input[name='fg_ori_ref']:checked\").val() != '0') {
        $('#ref_ppt').attr('style', 'display:inline;');
        $('#ref').attr('style', 'display:inline;');
        $('#ref_err').attr('style', 'display:inline;');
      }
      else {
        $('#ref_ppt').attr('style', 'display:none;');
        $('#ref').attr('style', 'display:none;');
        $('#ref_err').attr('style', 'display:none;');
      }
    }
  </script>\n";
  
  # How did you hear about...
  Forma_Seccion('* '.ObtenEtiqueta(289));
  Forma_Error($fg_ori_via_err);
  Forma_CampoRadio('', 'fg_ori_via', 'A', $fg_ori_via, ObtenEtiqueta(290), True, " onClick='javascript:cambia_other();'");
  Forma_CampoRadio('', 'fg_ori_via', 'B', $fg_ori_via, ObtenEtiqueta(291), True, " onClick='javascript:cambia_other();'");
  Forma_CampoRadio('', 'fg_ori_via', 'C', $fg_ori_via, ObtenEtiqueta(292), True, " onClick='javascript:cambia_other();'");
  Forma_CampoRadio('', 'fg_ori_via', 'D', $fg_ori_via, ObtenEtiqueta(293), True, " onClick='javascript:cambia_other();'");
  Forma_CampoRadio('', 'fg_ori_via', '0', $fg_ori_via, ObtenEtiqueta(294), True, " onClick='javascript:cambia_other();'");
  if($fg_ori_via == '0')
    $fg_other = True;
  else
    $fg_other = False;
  Forma_CampoTexto(ObtenEtiqueta(294), True, 'ds_ori_other', $ds_ori_other, 50, 32, $ds_ori_other_err, False, 'other', $fg_other);
  
  # Were you referred to Vancouver Animation School? 
  Forma_Seccion('* '.ObtenEtiqueta(295));
  Forma_Error($fg_ori_ref_err);
  Forma_CampoRadio('', 'fg_ori_ref', '0', $fg_ori_ref, ObtenEtiqueta(17), True, " onClick='javascript:cambia_ref();'");
  Forma_CampoRadio('', 'fg_ori_ref', 'S', $fg_ori_ref, ObtenEtiqueta(296), True, " onClick='javascript:cambia_ref();'");
  Forma_CampoRadio('', 'fg_ori_ref', 'T', $fg_ori_ref, ObtenEtiqueta(297), True, " onClick='javascript:cambia_ref();'");
  Forma_CampoRadio('', 'fg_ori_ref', 'G', $fg_ori_ref, ObtenEtiqueta(298), True, " onClick='javascript:cambia_ref();'");
  Forma_CampoRadio('', 'fg_ori_ref', 'A', $fg_ori_ref, ObtenEtiqueta(811), True, " onClick='javascript:cambia_ref();'");
  if($fg_ori_ref == 'S' OR $fg_ori_ref == 'T' OR $fg_ori_ref == 'G' OR $fg_ori_ref=='A')
    $fg_ref = True;
  else
    $fg_ref = False;
  Forma_CampoTexto(ObtenEtiqueta(300), True, 'ds_ori_ref_name', $ds_ori_ref_name, 50, 32, $ds_ori_ref_name_err, False, 'ref', $fg_ref);
  Forma_Espacio( );
   
  # Cierra la forma de captura
  Forma_Sencilla_Ini( );
  echo "<button type='button' id='buttons' OnClick='javascript:document.datos.submit();'>".ObtenEtiqueta(41)."</button>";
  Forma_Sencilla_Fin( );
  Forma_Termina( );
  echo "
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
    </table>";
  
  # Footer
  PresentaFooterAF( );
  
?>