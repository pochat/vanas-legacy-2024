<?php
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  // require("../lib/sp_forms.inc.php");
  require("lib/app_forms.inc.php");
  require("app_form.inc.php");
  
  $fg_paso = RecibeParametroNumerico('fg_paso');
  $cl_preference = RecibeParametroNumerico('cl_preference');
  $clave = RecibeParametroHTML('clave');
  $ds_code = RecibeParametroHTML('cd');
  $row = RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_1 WHERE cl_sesion='$clave'");
  if(empty($clave)) {	
    $clave = SP_GeneraSesion( );
    $fg_nueva = True;
	
  }
  else {
    SP_ActualizaSesion($clave);
    $fg_nueva = False;
  }
  
  function botones($paso, $clave){
    if($paso>1)
      $btn_pre = $paso - 1;    
    $btn_nex = $paso + 1;
    if($paso==18)
      $btn_nex = 1;
    $btns = 
    "<div class='form-actions'> 
		<div class='row'>
			<div class='col col-sm-12 col-md-12 col-lg-2'>
				<ul class='pager wizard1 no-margin'>";
				# Solo muestra el bootn anterior despues del primer paso
				if($paso>1){
				  $btns .= "
				  <li class='previous'>
					<a href='javascript:void(0);' class='btn btn-lg btn-default' onclick='app_form(".$btn_pre.", \"".$clave."\", true)'> ".ObtenEtiqueta(2262)." </a>
				  </li>";
				}
	$btns .= "
				</ul>
			</div>
			<div class='col col-sm-12 col-md-12 col-lg-8'>
				<p class='no-margin' style='color:#FF0000;'> <i class='fa fa-warning' style='color:#FF0000;'></i> ".ObtenEtiqueta(2261)."</p>
			</div>
			<div class='col col-sm-12 col-md-12 col-lg-2'>
				<ul class='pager wizard1 no-margin'>
					<li class='next'>";
					if($paso<19){
						$btns .="<a href='javascript:void(0);' class='btn btn-lg txt-color-white' id='btn_".$paso."' style='background-color:#0092cd;'> ".ObtenEtiqueta(2263)." </a>";                
					}
	$btns .= "
					</li>
				</ul>
			</div>
		</div>
      
    </div>";

    
    return $btns;    
  }

  function barra_progress($paso){
    $in = 100/20;
    $width = $paso * $in;
    
    
    if($width>1 && $width<=50)
      $color = "e44a00";
    
    else{
      if($width==100)
        $color = "45cd00";
      else
        $color = "0092cd";
    }
    
    $barra = "
    <br/>
    <div class='row padding-10'>
      <div class='col-sm12 col-md-12 col-lg-4'></div>
      <div class='col-sm12 col-md-12 col-lg-4'>
        <div class='progress progress-micro no-margin' data-progressbar-value='".round($width)."'>
          <div class='progress-bar'></div>
        </div>        
      </div>
      <div class='col-sm12 col-md-12 col-lg-4'></div>
    </div>";
    
    return $barra;
  }
  
  function Personajes($paso_grl){
    if($paso_grl==1)
      $img = SP_IMAGES."/".ObtenNombreImagen(306);
    if($paso_grl==2)
      $img = SP_IMAGES."/".ObtenNombreImagen(307);
    if($paso_grl==3)
      $img = SP_IMAGES."/".ObtenNombreImagen(308);
    if($paso_grl==4)
      $img = SP_IMAGES."/".ObtenNombreImagen(309);
    if($paso_grl==5)
      $img = SP_IMAGES."/".ObtenNombreImagen(310);
    $p = "
    <div class='col col-sm-12 col-md-12 col-lg-2 col-xs-3 padding-top-10' style='left:27px;'    >
      <img src='".$img."' class='superbox-current-img'>
    </div>";
    return $p;
  }
 
  if($fg_paso==1){
    $active1 = "active";
    $padre1 ="active"; 
  } 
  if($fg_paso==2){
    $active2 = "active"; 
    $padre1 ="active";       
  }
  if($fg_paso==3){
    $active3 = "active"; 
    $padre1 = "active";
  }
  if($fg_paso==4){
    $active4 = "active";
    $padre1 = "active";
    $p1 = "complete";
  }
  if($fg_paso==5){
    $active5 = "active";
    $padre2 = "active";
    $p1 = "complete";
  }
  if($fg_paso==6){
    $active6 = "active";
    $padre2 = "active";        
    $p1 = "complete";
  }
  if($fg_paso==7){
    $active7 = "active";
    $padre2 = "active";    
    $p1 = "complete";    
  }
  if($fg_paso==8){
    $active8 = "active";
    $padre2 = "active";    
    $p1 = "complete";
  }
  if($fg_paso==9){
    $active9 = "active";
    $padre3 = "active";    
    $p1 = "complete";
    $p2 = "complete";
  }
  if($fg_paso==10){
    $active10 = "active";
    $padre3 = "active";
    $p1 = "complete";    
    $p2 = "complete";    
  }
  if($fg_paso==11){
    $active11 = "active";
    $padre3 = "active";    
    $p1 = "complete";
    $p2 = "complete";
  }
  if($fg_paso==12){
    $active12 = "active";
    $padre3 = "active";    
    $p1 = "complete";
    $p2 = "complete";
  }
  if($fg_paso==13){
    $active13 = "active";
    $padre4 = "active";    
    $p1 = "complete";
    $p2 = "complete";
    $p3 = "complete";
  }
  if($fg_paso==14){
    $active14 = "active";
    $padre4 = "active";    
    $p1 = "complete";
    $p2 = "complete";
    $p3 = "complete"; 
  }
  if($fg_paso==15){
    $active15 = "active";
    $padre4 = "active";    
    $p1 = "complete";
    $p2 = "complete";
    $p3 = "complete";     
  }
  if($fg_paso==16){
    $active16 = "active";
    $padre4 = "active";
    $p1 = "complete";
    $p2 = "complete";
    $p3 = "complete";     
  }
  if($fg_paso==17){
    $active17 = "active";
    $padre5 = "active";
    $p1 = "complete";
    $p2 = "complete";
    $p3 = "complete"; 
    $p4 = "complete";
  }
  if($fg_paso==18){
    $active18 = "active";
    $padre5 = "active";
    $p1 = "complete";
    $p2 = "complete";
    $p3 = "complete"; 
    $p4 = "complete";
  }
  if($fg_paso==19){
    $active19 = "active";
    $padre5 = "active";
    $p1 = "complete";
    $p2 = "complete";
    $p3 = "complete"; 
    $p4 = "complete";
  }
  if($fg_paso==20){
    $active20 = "active";    
    $p1 = "complete";
    $p2 = "complete";
    $p3 = "complete"; 
    $p4 = "complete";
    $p5 = "complete";
  }
  
  # Consulta de FORM1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_CAPTURA)." fe_birth, ";
  $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, ";
  $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, ds_eme_country, ";
  $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, fl_programa, fl_periodo, ds_link_to_portfolio, ds_ruta_foto, ";
  $Query .= "fg_responsable, cl_recruiter,fg_disability,ds_disability, ds_eme_relation_other,ds_ruta_foto_permiso,".ConsultaFechaBD('fe_start_date',FMT_CAPTURA)." fe_start_date,".ConsultaFechaBD('fe_expirity_date',FMT_CAPTURA)."fe_expirity_date,nb_name_institutcion ";
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
  $fl_provincia = $ds_add_state;
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
  $ds_ruta_foto = str_texto($row[26]);
  $fg_responsable = $row[27];
  $cl_recruiter = $row[28];
  $fg_disabilityie = $row['fg_disability'];
  $ds_disability = str_texto($row['ds_disability']);
  $ds_eme_relation_other = str_texto($row['ds_eme_relation_other']);
  $ds_ruta_foto_permiso=str_texto($row['ds_ruta_foto_permiso']);
  $fe_start_date=str_texto($row['fe_start_date']);
  $fe_expirity_date=str_texto($row['fe_expirity_date']);
  $nb_name_institutcion=str_texto($row['nb_name_institutcion']);
  
  $Query  = "SELECT ds_p_name, ds_education_number, fg_international, cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, 
  ds_m_add_state, ds_m_add_zip, ds_m_add_country, ds_a_email, cl_preference_3, ds_usual_name ";
  $Query .= ", ds_citizenship, fg_study_permit, fg_study_permit_other, fg_aboriginal, ds_aboriginal, fg_health_condition, ds_health_condition, mn_app_fee, mn_tuition ";
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
  $ds_usual_name = str_texto($row[13]);
  $ds_citizenship = str_texto($row[14]);
  $fg_study_permit = $row[15];
  $fg_study_permit_other = $row[16];
  $fg_aboriginal = $row[17];
  $ds_aboriginal = str_texto($row[18]);
  $fg_health_condition = $row[19];
  $ds_health_condition = str_texto($row[20]);
  $mn_app_fee = $row[21];
  $mn_tuition = $row[22];
  
  #Recuperamos tarifas internacionales.
  if($fg_international==1){
    
      $Query="SELECT mn_app_fee_internacional,mn_tuition_internacional FROM k_programa_costos WHERE fl_programa=$fl_programa ";
      $row=RecuperaValor($Query);
      $mn_app_fee = $row[0];
      $mn_tuition = $row[1];
      
  
  }
  
  
  
  $Queryr  = "SELECT ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r, ds_relation_other ";
  $Queryr .= "FROM k_presponsable WHERE cl_sesion='$clave'";
  $rowr = RecuperaValor($Queryr);
  $ds_fname_r = str_texto($rowr[0]);
  $ds_lname_r = str_texto($rowr[1]);
  $ds_email_r = str_texto($rowr[2]);
  $ds_aemail_r = str_texto($rowr[3]);
  $ds_pnumber_r = str_texto($rowr[4]);
  $ds_relation_r = str_texto($rowr[5]);
  $ds_relation_r_other = str_texto($rowr[6]);
  # Obtenemos datos de la sesion
  $rowe = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='".$clave."'");
  $fl_sesion = $rowe[0];

   # Animations
   $data_aos = "fade-up";
   $data_aos_duration1 = "500";
   $data_aos_duration2 = "800";
   $data_aos_delay = "800";
   # Realtions
   $opcr = array(ObtenEtiqueta(2249), ObtenEtiqueta(2250), ObtenEtiqueta(2251), ObtenEtiqueta(2252), ObtenEtiqueta(2253), ObtenEtiqueta(2254));
   $valr = array(ObtenEtiqueta(2249), ObtenEtiqueta(2250), ObtenEtiqueta(2251), ObtenEtiqueta(2252), ObtenEtiqueta(2253), ObtenEtiqueta(2254));
    # html
    $paso = "    
    <div class='row'>
      <div id='wizard-1' novalidate='novalidate'>
        <div id='bootstrap-wizard-1' class='col-sm-12 col-md-12 col-lg-12 col-xs-12'>
          <div class='form-bootstrapWizard padding-10'>
            <ul class='bootstrapWizard form-wizard'>
              <li class='".$padre1." ".$p1."' data-target='#step1'>
              <a href='javascript:void(0);' data-toggle='tab'> <span class='step no-border' style='font-weight:800;font-size:16px;'>1</span> <span class='title'  style='font-size:16px;'>".ObtenEtiqueta(2240)."</span> </a>
              </li>
              <li class='".$padre2." ".$p2."' data-target='#step2'>
              <a href='javascript:void(0);' data-toggle='tab'> <span class='step no-border' style='font-weight:800;font-size:16px;'>2</span> <span class='title'  style='font-size:16px;'>".ObtenEtiqueta(2241)."</span> </a>
              </li>
              <li class='".$padre3." ".$p3."' data-target='#step3'>
              <a href='javascript:void(0);' data-toggle='tab'> <span class='step no-border' style='font-weight:800;font-size:16px;'>3</span> <span class='title'  style='font-size:16px;'>".ObtenEtiqueta(2242)."</span> </a>
              </li>
              <li class='".$padre4." ".$p4."' data-target='#step4'>
              <a href='javascript:void(0);' data-toggle='tab'> <span class='step no-border' style='font-weight:800;font-size:16px;'>4</span> <span class='title'  style='font-size:16px;'>".ObtenEtiqueta(2243)."</span> </a>
              </li>
              <li class='".$padre5." ".$p5."' data-target='#step5'>
              <a href='javascript:void(0);' data-toggle='tab'> <span class='step no-border' style='font-weight:800;font-size:16px;'>5</span> <span class='title'  style='font-size:16px;'>".ObtenEtiqueta(2247)."</span> </a>
              </li>
            </ul>
            <div class='clearfix'></div>
          </div>
          <div class='tab-content padding-top-10' id='tab-content'>";
    if($fg_paso<=18){
    $paso .= "
            <form name='frm_paso".$fg_paso."' id='frm_paso".$fg_paso."' novalidate='novalidate'>
              <input type='hidden' id='clave' name='clave' value='".$clave."'>
              <input type='hidden' id='rela_other' name='rela_other' value='".ObtenEtiqueta(2254)."'>";
    }
          if($fg_paso==1){
            $paso .= "              
              <div class='tab-pane  ".$active1."' id='tab1'>               
                ".barra_progress($fg_paso)."
                <div class='row padding-top-10'>".Personajes(1)."
                  <div id='app1' class='col-sm12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                  # En el primer paso el usuario recibe un email
                  $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(61));
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(117), 'ds_fname', $ds_fname, "12", "text", "fa-user", "", true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(119), 'ds_mname', $ds_mname, "12", "text", "fa-user-times", "");                        
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(118), 'ds_lname', $ds_lname, "12", "text", "fa-user-circle", "", true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(121), 'ds_email', $ds_email, "12", "text", "fa-at", "", true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(299), 'ds_email_conf', $ds_email_conf, "12", "text", "fa-at", "",true);  
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(280), 'ds_number', $ds_number, "12", "text", "fa-whatsapp", "",true);
            $paso .= "
                  <script>
                  $('#ds_fname').on('keyup', function(){
                    var v = $(this).val();
                    var v1 = $('#ds_mname').val();
                    var v2 = $('#ds_lname').val();
                    $('#str_name').empty().append(v+' '+v1+' '+v2);
                  });
                  $('#ds_mname').on('keyup', function(){
                    var v = $('#ds_fname').val();
                    var v1 = $(this).val();
                    var v2 = $('#ds_lname').val();
                    $('#str_name').empty().append(v+' '+v1+' '+v2);
                  });
                  $('#ds_lname').on('keyup', function(){
                    var v = $('#ds_fname').val();
                    var v1 = $('#ds_mname').val();
                    var v2 = $(this).val();
                    $('#str_name').empty().append(v+' '+v1+' '+v2);
                  });
                  $(document).ready(function(){
                    var v = $('#ds_fname').val();
                    var v1 = $('#ds_mname').val();
                    var v2 = $('#ds_lname').val();
                    if(v.length>0 || v1.length || v2.length>0)
                      $('#str_name').empty().append(v+' '+v1+' '+v2);                    
                  });
                  </script>
                  </div>
                </div>
              </div>";
          }
          if($fg_paso==2){
            $paso .= "
            <div class='tab-pane  ".$active2." ' id='tab2'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10'>".Personajes(1)."
              <div class='col-sm12 col-md-12 col-lg-10 col-xs-9'>";
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
                
                $paso .= "
                <script language='javascript'>
                  var claves_fl_periodo = new Array();
                    $claves_periodos
                  var textos_fl_periodo = new Array();
                    $textos_periodos
                  
                  function Replace(MasterObj, DetailName) {
                    var valorMaster = MasterObj.value;

                    if(valorMaster == '')
                      valorMaster = 0;
                    
                    itemDetalle = eval('document.frm_paso2.' + DetailName);
                    clavesNuevasOpciones = eval('claves_' + DetailName + '[' + valorMaster + ']');
                    textosNuevasOpciones = eval('textos_' + DetailName + '[' + valorMaster + ']');
                    
                    // Limpia las opciones del combo
                    itemDetalle.length = 0;
                    
                    // Crea las opciones del combo relacionado, toma los valores de los arreglos de claves y textos
                    itemDetalle.options[0] = new Option('".ObtenEtiqueta(70)."', '');
                    for(i = 0, j = 1; i < clavesNuevasOpciones.length; i++, j++) {
                      if(clavesNuevasOpciones[i] == '$fl_periodo')
                        itemDetalle.options[j] = new Option(textosNuevasOpciones[i], clavesNuevasOpciones[i], false, true);
                      else
                        itemDetalle.options[j] = new Option(textosNuevasOpciones[i], clavesNuevasOpciones[i]);
                    }
                  }                  
                </script>";
                # Programa
                $concat = array('nb_programa', "' ('", 'ds_duracion', "' - '", 'ds_tipo', "')'");
                $Query  = "SELECT ".ConcatenaBD($concat)." 'ds_texto', fl_programa ";
                $Query .= "FROM c_programa ";
                $Query .= "WHERE fl_programa IN($programas) ";
                $Query .= "ORDER BY nb_programa";
                $paso .= 
                "<div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(2244));
                $paso .= Forma_CampoSelectBD_Boostrap(ObtenEtiqueta(59), "fl_programa", $Query, $fl_programa, True, "12", "fa-list-ul", "onChange=\"Replace(this, 'fl_periodo');\"", true);
                if(!empty($fl_programa))
                $paso .= "<script language='javascript'> Replace(document.frm_paso2.fl_programa, 'fl_periodo'); </script>\n";
                $paso .= Forma_CampoSelectBD_Boostrap(ObtenEtiqueta(60), "fl_periodo", "", $fl_periodo, True, "12", "fa-play-circle", "", true);
                $paso .= "</div>
                <div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration2."' data-aos-delay='".$data_aos_delay."'>";
                $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(61));                
                $ruta = "https://".ObtenConfiguracion(60)."/modules/students/images/id/".$ds_ruta_foto;
                $paso .= Forma_CampoUpload(ObtenEtiqueta(810), "ds_ruta_foto", $ds_ruta_foto, $ruta, '.jpg,.png', '1', "12", "fa-address-card", true);
                $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(631), 'ds_p_name', $ds_p_name, "12", "text", "fa-user");
                $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(887), 'ds_usual_name', $ds_usual_name, "12", "text", "fa-user-times");
                $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(632), 'ds_education_number', $ds_education_number, "12", "text", "fa-user-circle");
              $paso .= "
                </div>
                  </div>
                </div>
            </div>";
          }
          if($fg_paso==3){
            $paso .= "
            <div class='tab-pane  ".$active3."' id='tab3'>
                ".barra_progress($fg_paso)."
                <div class='row padding-top-10'>".Personajes(1)."
                  <div class='col-sm12 col-md-12 col-lg-10 col-xs-9'>";
                  # International/permit
                  $paso .= "
                  <div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                  $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(2245));
                  # International
                  $names = array("fg_international", "fg_international");
                  $labels = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
                  $vals = array("1", "0");
                  $scripts = array("onClick='javascript:show_hidden(1);'", "onClick='javascript:show_hidden(1);'"); 
                  $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(620), $names, $labels, $vals, $scripts, $fg_international, $editars, "12 padding-10", "inline-group", true);
                  $citi_hid = "";
                  if(empty($fg_international))
                    $citi_hid = "hidden";
                  $paso .= "
                  <div class='row padding-10 ".$citi_hid."' id='international'>";
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(1024), 'ds_citizenship', $ds_citizenship, "12", "text", "fa-globe", "onkeyup='javascript:show_hidden(1);'", true);
                  $paso .= "</div>";
                  # Student permit
                  $names1 = array("fg_study_permit", "fg_study_permit");
                  $labels1 = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
                  $vals1 = array("1", "0");
                  $scripts1 = array("onClick='javascript:show_hidden(2);'", "onClick='javascript:show_hidden(2);'");
                  $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(1025), $names1, $labels1, $vals1, $scripts1, $fg_study_permit, $editars1, "12 padding-10", "inline-group", true);
                  #  study permit other
                  $names2 = array("fg_study_permit_other", "fg_study_permit_other");
                  $labels2 = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
                  $vals2 = array("1", "0");
                  $scripts2 = array("onClick='javascript:show_hidden(9);'", "onClick='javascript:show_hidden(9);'");
                  
                  if($fg_study_permit==0){
                    $per_hide = "";
				  }
				  if($fg_study_permit==1)
				    $per_hide_permiso_estudio="";
					
                  if($fg_study_permit=="" || $fg_study_permit_other==1)
                    $per_hide = "hidden";
				  if(( $fg_study_permit=="")||($fg_study_permit_other==0))
				    $per_hide_permiso_estudio="hidden";
					
					
				  ####Fechas encaso de elegir yes.
				  $paso .= "
                  <div class='row padding-10 ".$per_hide_permiso_estudio."' id='permiso_estudio'>";
                  $paso .= "<div id='div_fe_start_date'>".Forma_CampoCalendario(ObtenEtiqueta(2180).' '.ETQ_FMT_FECHA, 'fe_start_date', $fe_start_date, "dd-mm-yy", "12 padding-10", "fa-calendar", "", true)."</div>";
				  $paso .= "<div id='div_fe_expirity_date'>".Forma_CampoCalendario(ObtenEtiqueta(2181).' '.ETQ_FMT_FECHA, 'fe_expirity_date', $fe_expirity_date, "dd-mm-yy", "12 padding-10", "fa-calendar", "", true)."</div>";   
				  $paso .= "<div id='div_nb_name_institutcion'>".Forma_CampoTextoBootstrap(ObtenEtiqueta(2182), 'nb_name_institutcion', $nb_name_institutcion, "12", "text", "fa-graduation-cap", "onkeyup='javascript:show_hidden(1);'", true)."</div>";
				  
				 
				  $ruta_permiso = "https://".ObtenConfiguracion(60)."/modules/students/images/id/".$ds_ruta_foto_permiso;
                  $paso .= Forma_CampoUpload(ObtenEtiqueta(2183), "ds_ruta_foto_permiso", $ds_ruta_foto_permiso, $ruta_permiso, '.jpg,.png', '1', "12", "fa-picture-o", true);
				  $paso .= "</div>";
				  #####
					
					
					
                  $paso .= "<div class='row padding-10 ".$per_hide."' id='fgpermit'>";
                  $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(1026), $names2, $labels2, $vals2, $scripts2, $fg_study_permit_other, $editars2, "12", "inline-group", true);
                  $paso .= "</div>";
                  $paso .= "
                  </div>
                  <div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration2."' data-aos-delay='".$data_aos_delay."'>";
                  $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(2246));
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(281), 'ds_alt_number', $ds_alt_number, "12", "text", "fa-phone-square", "",true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(127), 'ds_a_email', $ds_a_email, "12", "text", "fa-envelope-square", "",true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(339), 'ds_link_to_portfolio', $ds_link_to_portfolio, "12", "text", "fa-link", "");
                  $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116)); // Masculino, Femenino
                  $val = array('M', 'F');
                  $paso .= Forma_CampoSelect_Boostrap(ObtenEtiqueta(114), "fg_gender",$opc, $val, $fg_gender, True, "12", "fa-transgender", "", true);
                  if(!ValidaFecha($fe_birth))
                    $fe_birth = "";
                  $paso .=  Forma_CampoCalendario(ObtenEtiqueta(120).' '.ETQ_FMT_FECHA, 'fe_birth', $fe_birth, "dd-mm-yy", "12 padding-10", "fa-birthday-cake", "", true);
             $paso .="
                   </div>
                  </div>
                </div>
             </div>";
          }
          if($fg_paso==4){
            $paso .= "
            <div class='tab-pane ".$active4."' id='tab4'>
                ".barra_progress($fg_paso)."
                <div class='row padding-top-10'>".Personajes(1)."
                  <div class='col col-sm-12 col-md-12 col-lg-10 col-xs-9'>";
                  $paso .= "
                  <div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                  # Address
                  $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(62));
                  $Query  = "SELECT ds_pais, fl_pais FROM c_pais WHERE fl_pais <>96 AND fl_pais<>125 AND fl_pais<>208 ORDER BY ds_pais";
                  $paso .= Forma_CampoSelectBD_Boostrap(ObtenEtiqueta(287), "ds_add_country", $Query, $ds_add_country, True, "12", "fa-globe", "", true);
                   $st1 = "";
                  if($ds_add_country==38)
                    $st1 = "hidden";
                  $paso .= "<div id='ds_state' class='".$st1."'>";
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(285), 'ds_add_state', $ds_add_state,  "12", "text", "fa-building", "", true);
                  $paso .= "</div>";
                  $st = "hidden";
                  if($ds_add_country==38)
                    $st = "";
                  $paso .= "<div id='fl_state' class='".$st."'>";
                  $Query  = "SELECT ds_provincia, fl_provincia FROM k_provincias WHERE fl_pais=38 ORDER BY ds_provincia";
                  $paso .= Forma_CampoSelectBD_Boostrap(ObtenEtiqueta(287), "fl_provincia", $Query, $fl_provincia, True, "12", "fa-building",   "", true);
                  $paso .= "</div>";
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(284), 'ds_add_city', $ds_add_city, "12", "text", "fa-building", "", true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(283), 'ds_add_street', $ds_add_street, "12", "text", "fa-road", "", true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(282), 'ds_add_number', $ds_add_number, "12", "text", "fa-home", "", true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(286), 'ds_add_zip', $ds_add_zip, "12", "text", "fa-qrcode", "", true);
                  $paso .= "
                  </div>
                  </div>
                </div>
            </div>";
          }
          if($fg_paso==5){
            $paso .= "
            <div class='tab-pane ".$active5."' id='tab5'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10'>".Personajes(2)."
                <div class='col col-sm-12 col-md-12 col-lg-10 col-xs-9'>";
                $paso .= "
                <div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                  $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(633));
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(282), 'ds_m_add_number', $ds_m_add_number, "12", "text", "fa-building");
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(283), 'ds_m_add_street', $ds_m_add_street, "12", "text", "fa-road");
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(284), 'ds_m_add_city', $ds_m_add_city, "12", "text", "fa-home");
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(285), 'ds_m_add_state', $ds_m_add_state, "12", "text", "fa-home");
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(286), 'ds_m_add_zip', $ds_m_add_zip, "12", "text", "fa-home");
                  $Query1  = "SELECT ds_pais, fl_pais FROM c_pais WHERE fl_pais <>96 AND fl_pais<>125 AND fl_pais<>208 ORDER BY ds_pais";
                  $paso .= Forma_CampoSelectBD_Boostrap(ObtenEtiqueta(287), "ds_m_add_country", $Query1, $ds_m_add_country, True, "12", "fa-globe");
            $paso .= "
                </div>
              </div>
            </div>";
          }
          if($fg_paso==6){
             $paso .= "
            <div class='tab-pane ".$active6."' id='tab6'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10'>".Personajes(2)."
                <div class='col col-sm-12 col-md-12 col-lg-10 col-xs-9'>";
                $paso .= "
                <div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                  # Emergency Contact Information
                  $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(63));
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(117), 'ds_eme_fname', $ds_eme_fname, "12", "text", "fa-user", "", true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(118), 'ds_eme_lname', $ds_eme_lname, "12", "text", "fa-user-times", "", true);
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(280), 'ds_eme_number', $ds_eme_number, "12", "text", "fa-whatsapp", "", true);
                  
                  $paso .= Forma_CampoSelect_Boostrap(ObtenEtiqueta(288), "ds_eme_relation",$opcr, $valr, $ds_eme_relation, True, "12", "fa-users", 'onchange=show_hidden(14);', true);
                  $hrela = "hidden";
                  if($ds_eme_relation==ObtenEtiqueta(2254))
                    $hrela = "";
                  $paso .= "
                  <div class='".$hrela."' id='other_relation'>";
                  $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(2255), 'ds_eme_relation_other', $ds_eme_relation_other, "12", "text", "fa-users", 'onkeyup=show_hidden(14);', true);
                  $paso .= "
                  </div>";
                  $Query  = "SELECT ds_pais, fl_pais FROM  c_pais WHERE fl_pais <>96 AND fl_pais<>125 AND fl_pais<>208 ORDER BY ds_pais";
                  $paso .= Forma_CampoSelectBD_Boostrap(ObtenEtiqueta(287), 'ds_eme_country', $Query, $ds_eme_country, True, "12", "fa-globe", "", true);
            $paso .= "                
                </div>
              </div>
            </div>";
          }
          
          if($fg_paso==7){
            $paso .= "
            <div class='tab-pane ".$active7."' id='tab7'>
              ".barra_progress($fg_paso)."";
              $paso .= "
              <div class='row padding-top-10'>".Personajes(2)."
                <div class='col col-sm-12 col-md-12 col-lg-10 col-xs-9'>
                  <!-- Primer animacion -->
                  <div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                    $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(1034));
                    # Aboriginal
                    $names3 = array("fg_aboriginal", "fg_aboriginal");
                    $labels3 = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
                    $vals3 = array("1", "0");
                    $scripts3 = array("onClick='javascript:show_hidden(3);'", "onClick='javascript:show_hidden(3);'");         
                    $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(1027), $names3, $labels3, $vals3, $scripts3, $fg_aboriginal, $editars, "12 padding-10", "inline-group", true);
                    # ds_aboriginal
                    $names4 = array("ds_aboriginal", "ds_aboriginal", "ds_aboriginal");
                    $labels4 = array(ObtenEtiqueta(1029), ObtenEtiqueta(1030), ObtenEtiqueta(1031));
                    $vals4 = array("1", "2", "3");
                    $scripts4 = array("onClick='javascript:show_hidden(11);'", "onClick='javascript:show_hidden(11);'");  
                    $abo = "hidden";
                    if($fg_aboriginal==1)
                      $abo = "";
                    $paso .= "
                    <div class='row padding-10 ".$abo."' id='aboriginal'>";
                    $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(1028), $names4, $labels4, $vals4, $scripts4 , $ds_aboriginal, $editars, "12", "inline-group", true);
                    $paso .= "
                    </div>";
                    # Health condition
                    $names5 = array("fg_health_condition", "fg_health_condition");
                    $labels5 = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
                    $vals5 = array("1", "0");
                    $scripts5 = array("onClick='javascript:show_hidden(4);'", "onClick='javascript:show_hidden(4);'");
                    $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(1032), $names5, $labels5, $vals5, $scripts5, $fg_health_condition, $editars, "12 padding-10","inline-group", true);
                    # ds health condition
                    $healt = "hidden";
                    if($fg_health_condition==1)
                      $healt = "";
                    $paso .= "
                    <div class='col col-sm-12 col-md-12 col-lg-12 ".$healt."' id='health'>";
                    $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(1033), 'ds_health_condition', $ds_health_condition, "12", "text", "fa-heartbeat", "onkeyup='javascript:show_hidden(4);'", true);
                    $paso .= "
                    </div>";
                    # Disabilityie
                    $names6 = array("fg_disabilityie", "fg_disabilityie");
                    $labels6 = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
                    $vals6 = array("1", "0");
                    $scripts6 = array("onClick='javascript:show_hidden(5);'", "onClick='javascript:show_hidden(5);'");
                    $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(1778), $names6, $labels6, $vals6, $scripts6, $fg_disabilityie, $editars, "12 padding-10", "inline-group", true);
                    $disi = "hidden";
                    if($fg_disabilityie==1)
                      $disi = "";
                    $paso .= "
                    <div class='col col-sm-12 col-md-12 col-lg-12 ".$disi."' id='disabilityie'>";
                    $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(1779), 'ds_disability', $ds_disability,  $col_size="12", $ds_tipo="text", $fa="fa-blind", "onkeyup='javascript:show_hidden(5);'");
                    $paso .= "
                    </div>";
            $paso .= "
                  </div>";
            $paso .= "
                  </div>
                </div>
              </div>
            </div>";
          }
          if($fg_paso==8){
            
            $paso .= "
            <div class='tab-pane ".$active8."' id='tab8'>
              ".barra_progress($fg_paso)."";
              $paso .= "
              <div class='row padding-top-10'>".Personajes(2)."
                <div class='col col-sm-12 col-md-12 col-lg-10 col-xs-9'>
                    <!-- Primer animacion -->
                    <div data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                    # responsable
                    $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(865));                
                    $names7 = array("fg_responsable", "fg_responsable");
                    $labels7 = array(ObtenEtiqueta(866), ObtenEtiqueta(867));
                    $vals7 = array("1", "0");
                    $scripts7 = array("onClick='javascript:show_hidden(6);'", "onClick='javascript:show_hidden(6);'");
                    $paso .= Forma_CampoRadioBootstrap("", $names7, $labels7, $vals7, $scripts7, $fg_responsable, $editars, "12 padding-10", "inline-group", true);
                    if($fg_responsable==1 || $fg_responsable=="")
                      $respo = "hidden";
                    else
                      $respo = "";
                    $paso .= "
                      <div class='col col-sm-12 col-md-12 col-lg-12  padding-10 ".$respo."' id='Presponsable'>";
                      $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(868), 'ds_fname_r', $ds_fname_r, "12", "text", "fa-user", "", true);
                      $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(869), 'ds_lname_r', $ds_lname_r, "12", "text", "fa-user-times", "", true);
                      $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(870), 'ds_email_r', $ds_email_r, "12", "text", "fa-at", "", true);
                      $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(871), 'ds_aemail_r', $ds_aemail_r, "12", "text", "fa-envelope-square");
                      $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(872), 'ds_pnumber_r', $ds_pnumber_r, "12", "text", "fa-whatsapp", "", true);
                      $paso .= Forma_CampoSelect_Boostrap(ObtenEtiqueta(873), "ds_relation_r",$opcr, $valr, $ds_relation_r, True, "12", "fa-users", 'onchange=show_hidden(15);', true);
                      $rp = "hidden";
                      if($ds_relation_r==ObtenEtiqueta(2254))
                        $rp = "";
                      $paso .= "<div class='".$rp."' id='relation_res'>";
                      $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(2255), 'ds_relation_r_other', $ds_relation_r_other, "12", "text", "fa-user", 'onkeyup=show_hidden(15);', true);
                      $paso .= "</div>";
              $paso .= "
                      </div>
                    </div>
                 </div>
               </div>
              </div>";
          }
          
          if($fg_paso==9){
          $paso .= "
            <div class='tab-pane ".$active9."' id='tab9'>
              ".barra_progress($fg_paso)." ";          
           $paso .= "
              <div class='row padding-top-10'>".Personajes(3)."
                <div class='col col-sm-12 col-md-12 col-lg-7 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                  $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(621));
                  $opc = array(ObtenEtiqueta(624), ObtenEtiqueta(625), ObtenEtiqueta(626), ObtenEtiqueta(627), ObtenEtiqueta(628), ObtenEtiqueta(629), ObtenEtiqueta(630));
                  $val = array('1', '2', '3', '4', '5', '6', '7');
                  $paso .= Forma_CampoSelect_Boostrap(ObtenEtiqueta(622), 'cl_preference_1', $opc, $val, $cl_preference_1, True, "12", "fa-clock-o", "", true);
                  $paso .= Forma_CampoSelect_Boostrap(ObtenEtiqueta(623), 'cl_preference_2', $opc, $val, $cl_preference_2, True, "12", "fa-clock-o", "", true);
                  $paso .= Forma_CampoSelect_Boostrap(ObtenEtiqueta(616), 'cl_preference_3', $opc, $val, $cl_preference_3, True, "12", "fa-clock-o", "", true);
                  $paso .= "  
                  <div class='col col-sm-12 col-md-12 col-lg-12 padding-10'>
                  <div class=''>
                    <!--<i class='fa-fw fa fa-info'></i>-->
                    ".ObtenEtiqueta(2256)." ".ObtenEtiqueta(2248).". <strong class='cursor-pointer' id='refres_preferences'><i class='fa-fw fa fa-refresh'></i> ".ObtenEtiqueta(2257)." </strong>
                  </div>
                  </div>
                  <select id='test' name='test' style='display:none;'>
                    <option value=''>". ObtenEtiqueta(70)."</option>";
                  for($i=0;$i<=count($opc);$i++){
                    $paso .= "<option value='".$val[$i]."'>".$opc[$i]."</option>";
                  }
           $paso .= "
                  </select>
                </div>
              </div>
            </div>";
          }
          if($fg_paso==10){
            $paso .= "
            <div class='tab-pane ".$active10."' id='tab10'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10'>".Personajes(3)."
                <div class='col-sm-12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                $paso .= Forma_CampoOcultoBootstrap('direccion', 'N');
                $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(876));
                $Query  = "SELECT CONCAT( ds_nombres, ' ', ds_apaterno ) , fl_usuario FROM c_usuario usr, c_perfil per ";
                $Query .= "WHERE usr.fl_perfil = per.fl_perfil AND usr.fl_perfil=".PERFIL_RECRUITER." AND usr.fg_activo='1' ORDER BY fg_default ASC , ds_nombres ASC ";
                $paso .= Forma_CampoSelectBD_Boostrap(ObtenEtiqueta(877), 'cl_recruiter', $Query, $cl_recruiter, true, "6", $fa="fa-users", "", true);
                # ori via
                $names8 = array("fg_ori_via", "fg_ori_via", "fg_ori_via", "fg_ori_via", "fg_ori_via","fg_ori_via","fg_ori_via","fg_ori_via");
                $labels8 = array(ObtenEtiqueta(290), ObtenEtiqueta(291), ObtenEtiqueta(292), ObtenEtiqueta(293), ObtenEtiqueta(2338),ObtenEtiqueta(2339),ObtenEtiqueta(2340), ObtenEtiqueta(294));
                $vals8 = array("A", "B", "C", "D","X","Y","Z", "0");
                $scripts8 = array("onClick='javascript:show_hidden(7);'", "onClick='javascript:show_hidden(7);'", "onClick='javascript:show_hidden(7);'", "onClick='javascript:show_hidden(7);'","onClick='javascript:show_hidden(7);'","onClick='javascript:show_hidden(7);'","onClick='javascript:show_hidden(7);'" ,"onClick='javascript:show_hidden(7);'");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(289), $names8, $labels8, $vals8, $scripts8, $fg_ori_via, $editars, "12 padding-10", "inline", true);
				$orivi = "hidden";
                if($fg_ori_via=="0")
                  $orivi = "";
                $paso .= "
                  <div class='col col-sm-12 col-md-12 col-lg-12 ".$orivi."' id='ori_other'>";
                $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(294), 'ds_ori_other', $ds_ori_other, "9", "text", "fa-internet-explorer", "onkeyup='javascript:show_hidden(7);'", true);
                $paso .= "
                  </div>";
                # ori ref
                $names9 = array("fg_ori_ref", "fg_ori_ref", "fg_ori_ref", "fg_ori_ref", "fg_ori_ref");
                $labels9 = array(ObtenEtiqueta(17), ObtenEtiqueta(296), ObtenEtiqueta(297), ObtenEtiqueta(298),  ObtenEtiqueta(811));
                $vals9 = array("0", "S", "T", "G", "A");
                $scripts9 = array("onClick='javascript:show_hidden(8);'", "onClick='javascript:show_hidden(8);'", "onClick='javascript:show_hidden(8);'", "onClick='javascript:show_hidden(8);'", "onClick='javascript:show_hidden(8);'");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(295), $names9, $labels9, $vals9, $scripts9, $fg_ori_ref, $editars, "12 padding-10", "inline", true);
                $oref = "hidden";
                if($fg_ori_ref=="A")
                  $oref = "";
                $paso .= "
                    <div class='col col-sm-12 col-md-12 col-lg-12 ".$oref."' id='ori_oref'>";
                $paso .= Forma_CampoTextoBootstrap(ObtenEtiqueta(300), 'ds_ori_ref_name', $ds_ori_ref_name, "12", "text", "fa-user-secret", "onkeyup='javascript:show_hidden(8);'", true);
              $paso .= "
                  </div>
                </div>
              </div>
            </div>";
          }
          # Consulta del form 2
          $Queryf2  = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
          $Queryf2 .= "FROM k_ses_app_frm_2 ";
          $Queryf2 .= "WHERE cl_sesion='$clave'";
          $row2 = RecuperaValor($Queryf2);
          $ds_resp_1 = str_texto($row2[0]);
          $ds_resp_2 = str_texto($row2[1]);
          $ds_resp_3 = str_texto($row2[2]);
          $ds_resp_4 = str_texto($row2[3]);
          $ds_resp_5 = str_texto($row2[4]);
          $ds_resp_6 = str_texto($row2[5]);
          $ds_resp_7 = str_texto($row2[6]);
          if($fg_paso==11){
            $paso .= "
            <div class='tab-pane ".$active11."' id='tab11'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10' style='padding:20px;'>".Personajes(3)."
                <div class='col-sm-12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                 $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(301),'ds_resp_1', $ds_resp_1, 50, 5, 'custom-scroll', True, "12", "fa-university", true) ;
                 $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(302),'ds_resp_2', $ds_resp_2, 50, 5, 'custom-scroll', True, "12", "fa-paint-brush", true) ;
                 $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(303),'ds_resp_3', $ds_resp_3, 50, 5, 'custom-scroll', True, "12", "fa-magic", true) ;             
              $paso .= "
                </div>
              </div>
            </div> ";            
          }
          if($fg_paso==12){
            $paso .= "
            <div class='tab-pane ".$active12."' id='tab12'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10' style='padding:20px;'>".Personajes(3)."
                <div class='col-sm-12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";             
                 $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(304),'ds_resp_4', $ds_resp_4, 50, 5, 'custom-scroll', True, "12", "fa-gear", true) ;
                 $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(305),'ds_resp_5', $ds_resp_5, 50, 5, 'custom-scroll', True, "12", "fa-line-chart", true) ;
                 $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(306),'ds_resp_6', $ds_resp_6, 50, 5, 'custom-scroll', True, "12", "fa-heart", true) ;
                 $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(307),'ds_resp_7', $ds_resp_7, 50, 5, 'custom-scroll', True, "12", "fa-gamepad", true) ;
              $paso .= "
                </div>
              </div>
            </div> "; 
          }
          
          # Form4
          $Query4  = "SELECT cl_sesion,fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
          $Query4 .= "fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, fg_resp_3_1, fg_resp_3_2 ";
          $Query4 .= "FROM k_ses_app_frm_4 ";
          $Query4 .= "WHERE cl_sesion='$clave'";
          $row = RecuperaValor($Query4);
          if(!empty($row[0])){
            $fg_resp_1_1 = $row[1];
            $fg_resp_1_2 = $row[2];
            $fg_resp_1_3 = $row[3];
            $fg_resp_1_4 = $row[4];
            $fg_resp_1_5 = $row[5];
            $fg_resp_1_6 = $row[6];
            $fg_resp_2_1 = $row[7];
            $fg_resp_2_2 = $row[8];
            $fg_resp_2_3 = $row[9];
            $fg_resp_2_4 = $row[10];
            $fg_resp_2_5 = $row[11];
            $fg_resp_2_6 = $row[12];
            $fg_resp_2_7 = $row[13];
            $fg_resp_3_1 = $row[14];
            $fg_resp_3_2 = $row[15];
          }
          else{
            $fg_resp_1_1 = "";
            $fg_resp_1_2 = "";
            $fg_resp_1_3 = "";
            $fg_resp_1_4 = "";
            $fg_resp_1_5 = "";
            $fg_resp_1_6 = "";
            $fg_resp_2_1 = "";
            $fg_resp_2_2 = "";
            $fg_resp_2_3 = "";
            $fg_resp_2_4 = "";
            $fg_resp_2_5 = "";
            $fg_resp_2_6 = "";
            $fg_resp_2_7 = "";
            $fg_resp_3_1 = "";
            $fg_resp_3_2 = "";
          }
          if($fg_paso==13){
            $paso .= "
            <div class='tab-pane ".$active13."' id='tab13'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10'>".Personajes(4)."
                <div class='col-sm-12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(79));
                # Generals
                $labels1f4 = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
                $vals1f4 = array("1","0");
                $edi1f4 = array(true,true);
                $scr1f4 = array("onClick='javascript:show_hidden(16);'","onClick='javascript:show_hidden(16);'");
                # fg_resp_1_1
                $names1_1 = array("fg_resp_1_1", "fg_resp_1_1"); 
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(82), $names1_1, $labels1f4, $vals1f4, $scr1f4, $fg_resp_1_1, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_1_2
                $names1_2 = array("fg_resp_1_2", "fg_resp_1_2");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(83), $names1_2, $labels1f4, $vals1f4, $scr1f4, $fg_resp_1_2, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_1_3
                $names1_3 = array("fg_resp_1_3", "fg_resp_1_3");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(84), $names1_3, $labels1f4, $vals1f4, $scr1f4, $fg_resp_1_3, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_1_4
                $names1_4 = array("fg_resp_1_4", "fg_resp_1_4");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(85), $names1_4, $labels1f4, $vals1f4, $scr1f4, $fg_resp_1_4, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_1_5
                $names1_5 = array("fg_resp_1_5", "fg_resp_1_5");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(86), $names1_5, $labels1f4, $vals1f4, $scr1f4, $fg_resp_1_5, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_1_6
                $names1_6 = array("fg_resp_1_6", "fg_resp_1_6");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(87), $names1_6, $labels1f4, $vals1f4, $scr1f4, $fg_resp_1_6, $edi1f4, "12 padding-10", "inline-group", true);
            $paso .= "
                </div>
              </div>
            </div>";
          }
          
          if($fg_paso==14){
            $paso .= "
            <div class='tab-pane ".$active14."' id='tab14'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10'>".Personajes(4)."
                <div class='col-sm-12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(80));
                # Generals
                $labels2f4 = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
                $vals2f4 = array("1","0"); 
                $edi1f4 = array(true,true);
                $scr1f4 = array("onClick='javascript:show_hidden(17);'","onClick='javascript:show_hidden(17);'");
                # fg_resp_2_1
                $names2_1 = array("fg_resp_2_1", "fg_resp_2_1");                               
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(88), $names2_1, $labels2f4, $vals2f4, $scr1f4, $fg_resp_2_1, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_2_2
                $names2_2 = array("fg_resp_2_2", "fg_resp_2_2");                
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(89), $names2_2, $labels2f4, $vals2f4, $scr1f4, $fg_resp_2_2, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_2_3
                $names2_3 = array("fg_resp_2_3", "fg_resp_2_3");                
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(90), $names2_3, $labels2f4, $vals2f4, $scr1f4, $fg_resp_2_3, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_2_4
                $names2_4 = array("fg_resp_2_4", "fg_resp_2_4");                
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(91), $names2_4, $labels2f4, $vals2f4, $scr1f4, $fg_resp_2_4, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_2_5
                $names2_5 = array("fg_resp_2_5", "fg_resp_2_5");                
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(92), $names2_5, $labels2f4, $vals2f4, $scr1f4, $fg_resp_2_5, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_2_6
                $names2_6 = array("fg_resp_2_6", "fg_resp_2_6");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(93), $names2_6, $labels2f4, $vals2f4, $scr1f4, $fg_resp_2_6, $edi1f4, "12 padding-10", "inline-group", true);
                # fg_resp_2_6
                $names2_7 = array("fg_resp_2_7", "fg_resp_2_7");                
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(94), $names2_7, $labels2f4, $vals2f4, $scr1f4, $fg_resp_2_7, $edi1f4, "12 padding-10", "inline-group", true);
            $paso .= "
                </div>
              </div>
            </div>";    
          }
          
          if($fg_paso==15){
            $paso .= "
            <div class='tab-pane ".$active15."' id='tab15'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10'>".Personajes(4)."
                <div class='col-sm-12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(81));
                # fg_resp_3_1         
                $edi1f4 = array(true,true,true,true);
                $scr1f4 = array("onClick='javascript:show_hidden(18);'","onClick='javascript:show_hidden(18);'", "onClick='javascript:show_hidden(18);'","onClick='javascript:show_hidden(18);'");
                $names3_1 = array("fg_resp_3_1", "fg_resp_3_1", "fg_resp_3_1", "fg_resp_3_1");                
                $lbl3 = array(ObtenEtiqueta(97),ObtenEtiqueta(98),ObtenEtiqueta(99),ObtenEtiqueta(107));                
                $vl3 = array("0","1","2","3");
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(95), $names3_1, $lbl3, $vl3, $scr1f4, $fg_resp_3_1, $edi1f4, "12 padding-10", "", true);
                # fg_resp_3_2
                $names3_2 = array("fg_resp_3_2", "fg_resp_3_2", "fg_resp_3_2", "fg_resp_3_2");                                
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(96), $names3_2, $lbl3, $vl3, $scr1f4, $fg_resp_3_2, $edi1f4, "12 padding-10", "", true);
            $paso .= "
                </div>
              </div>
            </div>";
          }
          
          
          # consulta form 3
          $Queryf3  = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
          $Queryf3 .= "FROM k_ses_app_frm_3 ";
          $Queryf3 .= "WHERE cl_sesion='$clave'";
          $row3 = RecuperaValor($Queryf3);
          $ds_resp_1_f3 = str_texto($row3[0]);
          $ds_resp_2_1_f3 = str_texto($row3[1]);
          $ds_resp_2_2_f3 = str_texto($row3[2]);
          $ds_resp_2_3_f3 = str_texto($row3[3]);
          $ds_resp_3_f3 = str_texto($row3[4]);
          $ds_resp_4_f3 = str_texto($row3[5]);
          $ds_resp_5_f3 = str_texto($row3[6]);
          $ds_resp_6_f3 = str_texto($row3[7]);
          $ds_resp_7_f3 = str_texto($row3[8]);
          $ds_resp_8_f3 = str_texto($row3[9]);
          if($fg_paso==16){
            $paso .= "
            <div class='tab-pane ".$active16."' id='tab16'>
              ".barra_progress($fg_paso)."

              <div class='row padding-top-10'>".Personajes(4)."
                <div class='col-sm-12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
              $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(308),'ds_resp_1_f3', $ds_resp_1_f3, 50, 5, 'custom-scroll', True, "12", "fa-spinner", true) ;          
            $paso .="
            <div class='col-sm-12 col-md-12 col-lg-12 padding-10'>
              <div class='col-sm-12 col-md-12 col-lg-12 padding-10 smart-form'>
                <label class='label' style='color:#0092cd; font-size:17px;'>".ObtenEtiqueta(309)."</label>
              </div>";
              $paso .= Forma_CampoTextoBootstrap("Goal #1", 'ds_resp_2_1_f3', $ds_resp_2_1_f3, "12", "text", "fa-hand-peace-o","", true);
              $paso .= Forma_CampoTextoBootstrap("Goal #2", 'ds_resp_2_2_f3', $ds_resp_2_2_f3, "12", "text", "fa-hand-peace-o", "", true);
              $paso .= Forma_CampoTextoBootstrap("Goal #3", 'ds_resp_2_3_f3', $ds_resp_2_3_f3, "12", "text", "fa-hand-peace-o", "", true);
            $paso .= "</div>";
              $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(310),'ds_resp_3_f3', $ds_resp_3_f3, 50, 5, 'custom-scroll', True, "12", "fa-graduation-cap", true);          
            $paso .= "
                </div>
              </div>
            </div>";
          }
          if($fg_paso==17){
            $paso .= "
            <div class='tab-pane ".$active17."' id='tab17'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10' style='padding:20px;'>".Personajes(5)."
                <div class='col-sm12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";
                $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(311),'ds_resp_4_f3', $ds_resp_4_f3, 50, 5, 'custom-scroll', True, "12", "fa-list", true);
                $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(312),'ds_resp_5_f3', $ds_resp_5_f3, 50, 5, 'custom-scroll', True, "12", "fa-list", true);
                # ds_resp_6
                $names10 = array("ds_resp_6_f3", "ds_resp_6_f3", "ds_resp_6_f3");
                $labels10 = array(ObtenEtiqueta(314), ObtenEtiqueta(315), ObtenEtiqueta(316));
                $vals10 = array("A", "B", "C");          
                $scripts10 = array("onClick='javascript:show_hidden(12);'", "onClick='javascript:show_hidden(12);'", "onClick='javascript:show_hidden(12);'");  
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(313), $names10, $labels10, $vals10, $scripts10, $ds_resp_6_f3, $editars10, "12 padding-10", "", true);            
            $paso .= "
                </div>
               </div>
            </div>";
          }
          if($fg_paso==18){
            $paso .= "
            <div class='tab-pane ".$active18."' id='tab18'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10' style='padding:20px;'>".Personajes(5)."
                <div class='col-sm12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>";            
                # ds_resp_7
                $names11 = array("ds_resp_7_f3", "ds_resp_7_f3", "ds_resp_7_f3", "ds_resp_7_f3", "ds_resp_7_f3");
                $labels11 = array(ObtenEtiqueta(318), ObtenEtiqueta(319), ObtenEtiqueta(320), ObtenEtiqueta(321), ObtenEtiqueta(322) );
                $vals11 = array("A", "B", "C", "D", "E");          
                $scripts11 = array("onClick='javascript:show_hidden(13);'", "onClick='javascript:show_hidden(13);'", "onClick='javascript:show_hidden(13);'",  "onClick='javascript:show_hidden(13);'",  "onClick='javascript:show_hidden(13);'");  
                $paso .= Forma_CampoRadioBootstrap(ObtenEtiqueta(317), $names11, $labels11, $vals11, $scripts11, $ds_resp_7_f3, $editars11, "12 padding-10", "", true);        
                # ds_resp_8
                $paso .= CampoTextAreaBootstrap(ObtenEtiqueta(323),'ds_resp_8_f3', $ds_resp_8_f3, 50, 5, 'custom-scroll', True, "12", "fa-circle-o-notch", true);
            $paso .= "
                </div>
               </div>
            </div>";
          }
          if($fg_paso==19){
            # Verificamos si el programa solo de paga app fee o es app fee + costo del programa
            # Verificamos si en el pago del curso se agreagara el tax rate           
            $QueryP = "SELECT fg_total_programa, fg_tax_rate, fl_template FROM c_programa WHERE fl_programa=".$fl_programa;
            $RowP = RecuperaValor($QueryP);
            $fg_total_programa = $RowP[0];
            $address1 = $ds_add_number." ".$ds_add_street; // numero y calle            
            $fg_tax_rate = $RowP[1]; // si necesita que se cobre impuesto 
            $fl_template = $RowP[2];
            # Informacion de paypal
            $urlPaypal = ObtenConfiguracion(61);
            $business = ObtenConfiguracion(62);
            $currency_code = ObtenConfiguracion(82);
            $tax_rate_paypal = "";
            
            # Si fg_total_programa es uno entonces pagara app fee mas monto del programa
            if(!empty($fg_total_programa)){
              $mn_due_pagar = $mn_app_fee + $mn_tuition;
              $item_name = ObtenEtiqueta(697);
              # PC| Indetificacion para Pago Completo
              $custom = "PC|$clave";    
            }
            else{ // Solo paga App fee
              $mn_due_pagar = $mn_app_fee;
              $item_name =ObtenEtiqueta(689);
              $custom = $clave;
            } 
			// $mn_due_pagar = 0.01;
            # Si no es de cadana y el programa no requiere tax
            $mn_tax_rate = 0;
            $tax_rate_paypal = "<input type='hidden' name='tax' id='tax' value='$mn_tax_rate'>";
            # Los canadienses van a pagar el tax dependiendo de la provincia
            if($ds_add_country==38){
              # Primera condicion es pago completo
              # Segunda condicion es pago del tax del app fee en programas largos    
              if(!empty($ds_add_state) && (!empty($fg_tax_rate) || empty($fg_tax_rate))){
                $row_tax = RecuperaValor("SELECT ds_abreviada,mn_tax FROM k_provincias WHERE fl_provincia='$ds_add_state'");
                $ds_abreviada = $row_tax[0];
                $mn_tax_rate = $row_tax[1];
                $tax_rate_paypal = "<input type='hidden' name='tax' id='tax' value='".$mn_due_pagar*($mn_tax_rate/100)."'>";
              }
            }
            $url_campus = ObtenConfiguracion(60);
			# Costos del programa
			# Por default mostrar los completos
			# Cuando ingrese un codigo se altera la forma de paypal para enviar el descuento
            if($fg_international==1)
            $Queryd  = "SELECT mn_app_fee_internacional, mn_tuition_internacional ";   
            else    
			$Queryd  = "SELECT mn_app_fee, mn_tuition ";
			$Queryd .= "FROM k_programa_costos where fl_programa=$fl_programa";
			$rowd = RecuperaValor($Queryd);
			$mn_app_fee = $rowd[0];
			$mn_tuition = $rowd[1];					
			$mn_descuento = 0;
			$mn_total = $mn_app_fee + $mn_tuition;
            $paso .= "gabriel".$fg_total_programa."
            <div class='tab-pane ".$active19."' id='tab19'>
              <div name='paso13' id='paso13' novalidate='novalidate'>
              ".barra_progress($fg_paso)."       
              <div class='row padding-top-10' style='padding:20px;'>".Personajes(5)."
                <div class='col-sm12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."' style='font-size:16px;'>";
                  $paso .= Forma_SeccionBootstrap(ObtenEtiqueta(2247));
             $paso .= ObtenEtiqueta(324)."<br/>				
				<div class='padding-top-10'><strong>";
             # Se paga solo e app fee si el fg_total_programa es cero si no es app mas costo programa
             if(!empty($fg_total_programa))
               $paso .= "<br>".ObtenEtiqueta(699);
             $paso .= ObtenEtiqueta(325);
             $paso .= ":</strong></div>	
					<div class='col-xs-12 col-sm-12 col-md-12 padding-top-10'>
						<table id='tbl_costos' class='table'>
							<thead>
								<tr>
									<th colspan='4' style='background-color:#337ab7; color:#FFF;'>
										".ObtenEtiqueta(2292)." ($currency_code)
										<p id='p_info_des'></p>
									</th>									
									<th style='background-color:#337ab7; color:#FFF;'>".ObtenEtiqueta(2293)."</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan='4'>".ObtenEtiqueta(2294)."</td>
									<td>$".$mn_app_fee."</td>
								</tr>
								<tr id='tr_descuento' class='hidden' style='color:#0091d9;'>
									<td colspan='3'>".ObtenEtiqueta(2295)."</td>
									<td>".ObtenEtiqueta(2299)."</td>
									<td id='td_descount'>$".number_format($mn_descuento,2)."</td>
								</tr>
								<tr>
									<td colspan='4'>".ObtenEtiqueta(2296)."</td>
									<td id='td_tuiton'>$".number_format($mn_tuition,2)."</td>
								</tr>
								
								<tr>
									<td colspan='4'>".ObtenEtiqueta(2297)."</td>
									<td id='td_total'>$".number_format($mn_total,2)."</td>
								</tr>								
							</tbody>
						</table>
					</div>
                  <div class='col-xs-12 col-sm-6 col-md-6 padding-top-10' id='div_btn_paypal'>
                    <div class='panel panel-primary pricing-big'>
                        <div class='panel-heading'>
                          <h3 class='panel-title no-padding'>Pay Online</h3>
                        </div>
                        <div class='panel-body no-padding text-align-center' style='height:105px;'>
                          <div class='price-features' style='min-height:0px; font-size:16px;'>
                            ".ObtenEtiqueta(326)."
                          </div>
                        </div>
                        <div class='panel-footer text-align-center' id='inf_paypal' style='height:100px;'>
							<div id='btn_paypal_default' class='no-padding'>
								<div>
									<a class='btn btn-primary padding-5' style='background-color:#ffbe03;border-color:#c9b024; ' href='javascript: document.frmpaypal.submit();' 
									id='paypal_btn' style='width:130px;'>".ObtenEtiqueta(2300)."</a>
								</div>
								<div>
									<img src='assets/img/creditcard_vanas.png' style='height:auto; width:120px;' />
								</div>
							</div>";
							# Verificamos las condiciones 
							# 1 Si tiene cupon
							# 2 Si esta activo
							# 3 Si esta entre la fecha del cupon
							$Queryy  = "SELECT ds_code, ds_descuento FROM c_cupones a, k_cupones_course b ";
							$Queryy .= "WHERE a.fl_cupon=b.fl_cupon AND fl_programa='".$fl_programa."' ";
							$Queryy .= "AND fg_activo='1' AND CURDATE() BETWEEN fe_start AND fe_end ";
							$rowy = RecuperaValor($Queryy);
							$codee = str_texto($rowy[0]);							
							if(!empty($codee)){
								# Calculos
								$ds_descuento = str_texto($rowy[1]);
								# Comprobamos si es porcentaje o money
								if(strpos($ds_descuento, "%")){
									$p = explode("%", $ds_descuento);
									$p1 = $p[0]/100;
									$p2 = $mn_tuition * $p1;
									$p3 = $mn_tuition - $p2;
									$p4 = $p3 + $mn_app_fee;
								}
								else{
									// $p = explode("$", $ds_descuento);
									$p2 = $ds_descuento;
									$p3 = $mn_tuition - $p2;
									$p4 = $p3 + $mn_app_fee;
								}
								$paso .= "
								<div class='no-padding' id='link_cupon'> 
									<a href='javascript:void(0);' onclick='descuento();' id='a_link_cd'>".ObtenEtiqueta(2286)."</a>
								</div>
								<div id='div_cupo' class='hidden no-padding'>
									<div class='col-sm-1'>&nbsp;</div>
									<div class='col-sm-10 no-padding'>
										<div class='smart-form form-group no-padding' id='div_ds_code'>
											<label class='input'> <i class='icon-prepend fa fa-trophy'></i>
												<input placeholder='".ObtenEtiqueta(2288)."' class='effect-7' type='text' name='ds_code' id='ds_code' value=''>
												<span class='focus-border'><i></i></span>
											</label>
										</div>
										<div class='padding-top-10'>
											<a href='javascript:void(0)' id='btn_app_cupon' class='btn btn-primary padding-5' style='width:130px;'>".ObtenEtiqueta(2289)." </a>
										</div>
									</div>
									<div class='col-sm-1'>&nbsp;</div>
								</div>
								<input type='hidden' id='cdbd' name='cdbd' value='8Z".$codee."-cd21983'>
								<input type='hidden' id='cddes' name='cddes' value='".$p2."'>
								<input type='hidden' id='cdtui' name='cdtui' value='".$p3."'>
								<input type='hidden' id='cdtot' name='cdtot' value='".$p4."'>
								";
							}
			  $paso .= "
							<script>
							$('#em-add_descount').remove();
							function descuento(){
								var cod = $('#ds_code');
								var ele = $('#div_cupo');
								var ele2 = $('#btn_paypal_default');
								var div = ele.hasClass('hidden');
								$('#em-ds_code').remove();
								$('#div_ds_code .input').removeClass('state-error state-success');
								if(div==true){
									cod.val('');
									ele.removeClass('hidden');
									ele2.empty();
									$('#inf_paypal').css('height', '150px');
									$('#a_link_cd').empty().append('".ObtenEtiqueta(2287)."');
								}
								else{
									var btn =	
									'<div>'+
										'<a class=\'btn btn-primary padding-5\' href=\'javascript:document.frmpaypal.submit();\' '+
										'id=\'paypal_btn\' style=\'width:130px;\'>".ObtenEtiqueta(2300)."</a>'+
									'</div>'+
									'<div>'+
										'<img src=\'assets/img/creditcard_vanas.png\' style=\'height:auto; width:120px;\' />'+
									'</div>';
									ele.addClass('hidden');
									ele2.empty().append(btn);
									$('#inf_paypal').css('height', '100px');
									$('#a_link_cd').empty().append('".ObtenEtiqueta(2286)."');
								}
							}
							$('#btn_app_cupon').on('click', function(){
								var code = $('#ds_code').val();
								$('#em-ds_code').remove();
								if(code.length>0){	

								
									//Validamos el code
									var str = $('#cdbd').val();
									var p = str.split('-');
									var res = p[0];
									var resp1 = res.substring(2);
									var fl_programa=$fl_programa;
									
									var dspais = '".$ds_add_country."';
									
									if(dspais=='38'){
										var can_tax = ".$mn_tax_rate."/100;
									}else{
										
										var can_tax=0;
									}
									
									
								    var cant_descuento = $('#cddes').val();
									var mn_tuiton = $('#cdtui').val();
									var mn_app_fee=$mn_app_fee;
									
									//Validamos por json que exista ese codigo que aacabmos de ingresar.
									$.ajax({
									  type: 'POST',
									  url: 'valida_cupon.php',
									  async: false,
									  data: 'code='+code+
									        '&mn_tuiton='+mn_tuiton+
											'&mn_app_fee='+mn_app_fee+
									        '&fl_programa='+fl_programa+
											'&can_tax='+can_tax,
									})
									.done(function(result){
									  var code_result = JSON.parse(result);
									  
									   
									  var exito = code_result.success;
									  var mn_total_pagar=code_result.mn_total_pagar;
									  var mn_tax_=code_result.mn_tax;
									  //alert(exito);
									  if(exito==1){
										  
										 	// Guardamos los datos
										var d = $('#cddes').val();
										var e = $('#cdtui').val();
										var c = $('#cdtot').val();
										var data1 = 'fg_cupon=1&clave=".$clave."&mn_descuento='+d+'&ds_code='+code+'&fl_programa=".$fl_programa."';
										$.ajax({
											type: 'POST',
											url: 'app_iu.php',
											cache: false,
											async: false,
											data: data1,
											success: function(){
												$('#div_ds_code .input').removeClass('state-error').addClass('state-success');
												var btn =	
													'<div>'+
														'<a class=\'btn btn-primary padding-5\' href=\'javascript:document.frmpaypal.submit();\' '+
														'id=\'paypal_btn\' style=\'width:130px;\'>".ObtenEtiqueta(2300)."</a>'+
													'</div>'+
													'<div>'+
														'<img src=\'assets/img/creditcard_vanas.png\' style=\'height:auto; width:120px;\' />'+
													'</div>';
												$('#div_cupo').addClass('hidden');
												$('#btn_paypal_default').empty().append(btn);
												$('#inf_paypal').css('height', '100px');
												
												
												
												// Calculos												
												$('#td_descount').empty().append(d);										
												// $('#td_tuiton').empty().append('$'+e);
												$('#td_total').empty().append('$'+mn_total_pagar);
												$('#cdtot').empty().append(mn_total_pagar);
												$('#cdtot').val(mn_total_pagar);
												$('#amount').val(mn_total_pagar);
												//alert(mn_tax_);
												//Agrgamos el tax segun el json devuelto
												$('#tax').val(mn_tax_);
												
												var des1 = 	'<em id=\'em-add_descount\' style=\'color:#D56161;\'><i class=\'fa fa-warning\'></i>".ObtenEtiqueta(2302)."</em>';
												$('#paypal_btn').before(des1);
												$('#link_cupon').remove();
												$('#div_by_button').addClass('hidden');
												$('#div_btn_paypal').addClass('col-sm-offset-3');
												$('#tr_descuento').removeClass('hidden');
												var programtot = '".$fg_total_programa."';
												var canada = '".$ds_add_country."';
												if(programtot=='1'){
													if(canada=='38'){
														var app = ".$mn_app_fee.";
														var tut = ".$mn_tuition." - ".$p2.";
														var tax = ".$mn_tax_rate."/100;
														var tax_app = app * tax;
														var tax_tui = tut * tax;
														var tax_tot = tax_app + tax_tui;
														$('#tax').val(tax_tot);
														$('#frmpaypal').append('<input type=\'hidden\' name=\'discount_amount\' id=\'discount_amount\' value=\'".$p2."\'>');														
													}
													else{
														$('#frmpaypal').append('<input type=\'hidden\' name=\'discount_amount\' id=\'discount_amount\' value=\'".$p2."\'>');
													}
												}
												
												
												
											}
											
											
												
											
										}); 
										  
										  
										  
									  }else{
										  
										  $('#div_ds_code .input').removeClass('state-success').addClass('state-error').after('<em class=\'invalid no-margin\' id=\'em-ds_code\'>".ObtenEtiqueta(2290)."</em>');
								
										  
									  }
									  


									  
          
									});
									
									
									
									
									
									
									
									
									
									//if(resp1===code){
										
									
								//	}
									//else{
										
									//}
								}
								else{
									$('#div_ds_code .input').removeClass('state-success').addClass('state-error').after('<em class=\'invalid no-margin\' id=\'em-ds_code\'>".ObtenEtiqueta(2291)."</em>');
								}
							});
							$('#ds_code').on('keyup', function(){
								var code = $(this).val();
								$('#em-ds_code').remove();
								if(code.length>0){
									$('#div_ds_code .input').removeClass('state-error').addClass('state-success');
								}
								else{
									$('#div_ds_code .input').removeClass('state-success').addClass('state-error').after('<em class=\'invalid no-margin\' id=\'em-ds_code\'>".ObtenEtiqueta(2291)."</em>');
								}
							});
							</script>
                            <form name='frmpaypal' id='frmpaypal' action='$urlPaypal'>
                              <input type='hidden' name='cmd' value='_xclick'>
                              <input type='hidden' name='business' id='business' value='$business'>
                              <input type='hidden' name='currency_code' id='currency_code' value='$currency_code'>
                              <input type='hidden' name='item_name' id='item_name' value='$item_name'>
                              <input type='hidden' name='amount' id='amount' value='$mn_due_pagar'>
                              <input type='hidden' name='custom' id='custom' value='$custom'>
                              ".$tax_rate_paypal."
                              <!--url que regresa una vez que termino el proceso de comunicacion con paypal-->
                              <input type='hidden' name='return' id='return' value='".$url_campus."/app_form/?c=A99".$fl_sesion."f0rm&p=20'>
                              <!--Envia datos a la url espefificada -->
                              <input type='hidden' name='rm' id='rm' value='2'>
                              <!--Si cancela el comprador antes de realizar el pago redirige a la url que se ingresa-->
                              <input type='hidden' name='cancel_return' id='cancel_return' value='".$url_campus."/app_form/?c=A99".$fl_sesion."f0rm&p=19'>
                            </form>							
                          <div>
                          </div>
                        </div>
                    </div>					
                </div>
                
                <div class='col-xs-12 col-sm-6 col-md-6 padding-top-10' id='div_by_button'>
                  <div class='panel panel-primary pricing-big'>
                    <div class='panel-heading'>
                      <h3 class='panel-title no-padding'>".ObtenEtiqueta(2301)."</h3>
                    </div>
                    <div class='panel-body no-padding text-align-center' style='height:105px;'>		                    
                      <div class='price-features' style='min-height:0px; font-size:16px;'>
                        ".ObtenEtiqueta(327)."
                        <input type='hidden' name='origen' id='origen' value='DMMB7SDFVC645BV_frm.php'>
                        <input type='hidden' name='clave' id='clave' value='$clave'>
                      </div>
                    </div>
                    <div class='panel-footer text-align-center' id='info_app' style='padding-top:25px; height:100px; font-size:16px;'>
                     <a href='javascript:void(0)' id='btn_app_frm'> <h3 class='no-padding no-margin'><strong>".ObtenEtiqueta(330)."</strong></h3></a>
                    </div>
                    </div>
                  </div>
					
                 </div>				
                </div>
              </div>
              </div>			  			  
            </div>";
          }
          if($fg_paso==20){
            $paso .= "
            <div class='tab-pane ".$active20."' id='tab20'>
              ".barra_progress($fg_paso)."
              <div class='row padding-top-10'>".Personajes(5)."
                <div class='col-sm-12 col-md-12 col-lg-10 col-xs-9' data-aos='".$data_aos."' data-aos-duration='".$data_aos_duration1."' data-aos-delay='".$data_aos_delay."'>
                  <div class='row'>
                    <div class='col-sm-12 col-md-12 col-lg-2 col-xs-12'></div>
                    <div class='col-sm-12 col-md-12 col-lg-8 col-xs-12'>
                      <h1 class='error-text tada animated' style='font-size:50px; color:#0092DB;'><i class='fa fa-check text-success'></i> ".ObtenEtiqueta(328)."</h1>
                    </div>
                    <div class='col-sm-12 col-md-12 col-lg-2 col-xs-12'></div>
                  </div>
                  <div class='row'>
                  <p>".ObtenEtiqueta(331)."</p>
                  <p>".ObtenEtiqueta(332)."</p>
                  <p>".ObtenEtiqueta(333)."</p>
                  <p>".ObtenEtiqueta(334)."</p>
                  <form method='link' action='index.php'>
                  <ul class='pager wizard1 no-margin'>
                  <li class='next'>
                    <input type='submit' id='buttons'  class='btn btn-lg txt-color-white padding-10' style='background-color:#0092cd;' value='".ObtenEtiqueta(329)."'>
                  </li>
                </ul>
                  </form>
                  </div>
                </div>
              </div>
            </div>";
          }
          if($fg_paso<=19)
            $paso .= botones($fg_paso, $clave);
          
    if($fg_paso<=18){
    $paso .= "
            </form>";
    }
    $paso .= "
          </div>
        </div>
      </div>
    </div>
	<script>
	$(document).ready(function(){
		var fn = '".$ds_fname."';
		var mn = '".$ds_mname."';
		var ln = '".$ds_lname."';
		$('#str_name').empty().val(fn+' '+mn+' '+ln);
	});
	</script>";
    $script = "
    <script src='assets/js/validar.js'></script>
    <script>
    pageSetUp();	
    </script>";

  
  $result["frm"] = $paso;
  $result["script"] = $script;
  $result["clave"] = $clave;
  $result["query"] = $Query."---".$fg_nueva;
  
  echo json_encode((Object) $result);
  
  
?>