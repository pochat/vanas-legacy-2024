<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CURSOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_programa, ds_duracion, ds_tipo, no_orden, no_grados, fl_template, fg_fulltime, fg_taxes, fg_total_programa, fg_archive, ";
      $Query .= "mn_lecture_fee, mn_extra_fee, fg_tax_rate, no_valor_rubrics, fg_total_programa_internacional,fg_tax_rate_internacional ";
      $Query .= "FROM c_programa ";
      $Query .= "WHERE fl_programa=$clave";
      $row = RecuperaValor($Query);
      $nb_programa = str_texto($row[0]);
      $ds_duracion = str_texto($row[1]);
      $ds_tipo = str_texto($row[2]);
      $no_orden = $row[3];
      $no_grados = $row[4];
      $fl_template = $row[5];
      $fg_fulltime = $row[6];
      $fg_taxes = $row[7];
      $fg_total_programa = $row[8];
      $fg_archive = $row[9];
      $mn_lecture_fee= $row[10];
      $mn_extra_fee= $row[11];
      $fg_tax_rate = $row[12];
	  $no_val_rub = $row['no_valor_rubrics'];
	  
      $fg_total_programa_internacional=$row['fg_total_programa_internacional'];
      $fg_tax_rate_internacional=$row['fg_tax_rate_internacional'];
      
      
      
      $style_sin_criterios = "style='display:none;'";
      $style_sin_valor_rubric = "style='display:none;'";
      $style_sin_valor_criterio = "style='display:none;'";
      $style_max_grade = "style='display:none;'";
      $style_max_grade_wrg = "style='display:none;'";
      
      $disabled_no_val_rub = "";
      
      $disabled_det = "";
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
      $Query  = "SELECT no_horas, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, ";
      $Query .= "mn_app_fee, mn_tuition, mn_costs, ds_costs, no_a_payments, ds_a_freq, mn_a_due, mn_a_paid, no_a_interes, ";
      $Query .= "no_b_payments, ds_b_freq, mn_b_due, mn_b_paid, no_b_interes, no_c_payments, ds_c_freq, mn_c_due, mn_c_paid, no_c_interes, ";
      $Query .= "no_d_payments, ds_d_freq, mn_d_due, mn_d_paid, no_d_interes, no_horas_week ";
      $Query .= ", mn_app_fee_internacional, mn_tuition_internacional, mn_costs_internacional,ds_costs_internacional,no_a_payments_internacional,ds_a_freq_internacional,mn_a_due_internacional,mn_a_paid_internacional,    no_a_interes_internacional, ";
	  $Query .= "no_b_payments_internacional,ds_b_freq_internacional,mn_b_due_internacional,mn_b_paid_internacional,no_b_interes_internacional, ";
	  $Query .= "no_c_payments_internacional,ds_c_freq_internacional,mn_c_due_internacional,mn_c_paid_internacional,no_c_interes_internacional, ";
	  $Query .= "no_d_payments_internacional,ds_d_freq_internacional,mn_d_due_internacional,mn_d_paid_internacional,no_d_interes_internacional ";
	 
      $Query .= "FROM k_programa_costos ";
      $Query .= "WHERE fl_programa = $clave ";
      $row = RecuperaValor($Query);
      
      # Datos pagos y para contrato
      $no_horas = $row[0];
      $no_semanas = $row[1];
      $ds_credential = $row[2];
      $cl_delivery = $row[3];
      $ds_language = $row[4];
      $cl_type = $row[5];
      if(!empty($row[6]))
        $app_fee = $row[6];
      else
        $app_fee = 0;
      if(!empty($row[7]))
        $tuition = $row[7];
      else
        $tuition = 0;
      $no_costos_ad = $row[8];
      $ds_costos_ad = $row[9];
      
      
      $app_fee_internacional=$row['mn_app_fee_internacional'];
      if(empty($app_fee_internacional))
          $app_fee_internacional=0;
      $tuition_internacional=$row['mn_tuition_internacional'];
      if(empty($tuition_internacional))
      $tuition_internacional=0;      
      $no_costos_ad_internacional = $row['mn_costs_internacional'];
      $ds_costos_ad_internacional = $row['ds_costs_internacional'];
      
      
      if(!empty($row[10]))
        $no_payments_a = $row[10];
      else
        $no_payments_a = 1;
      if(!empty($row[11]))
        $frequency_a = $row[11];
      else
        $frequency_a = "Full Payment";
      $amount_due_a = $row[12];
      $amount_paid_a = $row[13];
      if(!empty($row[14]))
        $interes_a = $row[14];
      else
        $interes_a = 0;
      $no_payments_b = $row[15];
      $frequency_b = $row[16];
      $amount_due_b = $row[17];
      $amount_paid_b = $row[18];
      $interes_b = $row[19];
      $no_payments_c = $row[20];
      $frequency_c = $row[21];
      $amount_due_c = $row[22];
      $amount_paid_c = $row[23];
      $interes_c = $row[24];
      $no_payments_d = $row[25];
      $frequency_d = $row[26];
      $amount_due_d = $row[27];
      $amount_paid_d = $row[28];
      $interes_d = $row[29];
      $no_horas_week = $row[30];
	  
	  #Internacional
	        
      if(!empty($row['no_a_payments_internacional']))
        $no_payments_a_internacional = $row['no_a_payments_internacional'];
      else
        $no_payments_a_internacional = 1;
      if(!empty($row['ds_a_freq_internacional']))
        $frequency_a_internacional = $row['ds_a_freq_internacional'];
      else
        $frequency_a_internacional = "Full Payment";
      $amount_due_a_internacional = $row['mn_a_due_internacional'];
      $amount_paid_a_internacional = $row['mn_a_paid_internacional'];
      if(!empty($row['no_a_interes_internacional']))
        $interes_a_internacional = $row['no_a_interes_internacional'];
      else
        $interes_a_internacional = 0;
      $no_payments_b_internacional = $row['no_b_payments_internacional'];
      $frequency_b_internacional = $row['ds_b_freq_internacional'];
      $amount_due_b_internacional = $row['mn_b_due_internacional'];
      $amount_paid_b_internacional = $row['mn_b_paid_internacional'];
      $interes_b_internacional = $row['no_b_interes_internacional'];
      $no_payments_c_internacional = $row['no_c_payments_internacional'];
      $frequency_c_internacional = $row['ds_c_freq_internacional'];
      $amount_due_c_internacional = $row['mn_c_due_internacional'];
      $amount_paid_c_internacional = $row['mn_c_paid_internacional'];
      $interes_c_internacional = $row['no_c_interes_internacional'];
      $no_payments_d_internacional = $row['no_d_payments_internacional'];
      $frequency_d_internacional = $row['ds_d_freq_internacional'];
      $amount_due_d_internacional = $row['mn_d_due_internacional'];
      $amount_paid_d_internacional = $row['mn_d_paid_internacional'];
      $interes_d_internacional = $row['no_d_interes_internacional'];
	  
	  
	  
	  
	  
	  
      # Verifica si tiene registro
      $rowt = RecuperaValor("SELECT fl_tema FROM c_f_tema WHERE nb_tema='$nb_programa'");
      if(!empty($rowt[0]))
        $fg_streams = true;
      else
        $fg_streams = false;
      
    }
    else { // Alta, inicializa campos
      $nb_programa = "";
      $ds_duracion = "";
      $ds_tipo = "";
      $no_orden = "0";
      $no_grados = "4";
      $fl_template = 0;
      $fg_fulltime = 1;
      $fg_taxes =0;
      $fg_total_programa =0;
      $fg_archive=0;
      $mn_class='';
      $mn_extra_class='';
      
      $no_horas = '';
      $no_semanas = '';
      $ds_credential = '';
      $cl_delivery = 'O';
      $ds_language = '';
      $cl_type = 1;
      $app_fee = 0;
      $tuition = 0;
      $no_costos_ad = '';
      $ds_costos_ad = '';
      $app_fee_internacional=0;
      $tuition_internacional=0;
      $no_costos_ad_internacional='';
      $ds_costos_ad_internacional='';
      $total_tuition = 0;
      $total = 0;
      $no_payments_a = 1;
      $frequency_a = 'Full Payment';
      $amount_due_a = '';
      $amount_paid_a = '';
      $interes_a = 0;
      $no_payments_b = 5;
      $frequency_b = 'Trimesterly';
      $amount_due_b = '';
      $amount_paid_b = '';
      $interes_b = '';
      $no_payments_c = 15;
      $frequency_c = 'Monthly';
      $amount_due_c = '';
      $amount_paid_c = '';
      $interes_c = '';
      $no_payments_d = 30;
      $frequency_d = 'Monthly';
      $amount_due_d = '';
      $amount_paid_d = '';
      $interes_d = '';
	  
	  
	  #internacional
	  $no_payments_a_internacional = 1;
      $frequency_a_internacional = 'Full Payment';
      $amount_due_a_internacional = '';
      $amount_paid_a_internacional = '';
      $interes_a_internacional = 0;
      $no_payments_b_internacional = 5;
      $frequency_b_internacional = 'Trimesterly';
      $amount_due_b_internacional = '';
      $amount_paid_b_internacional = '';
      $interes_b_internacional = '';
      $no_payments_c_internacional = 15;
      $frequency_c_internacional = 'Monthly';
      $amount_due_c_internacional = '';
      $amount_paid_c_internacional = '';
      $interes_c_internacional = '';
      $no_payments_d_internacional = 30;
      $frequency_d_internacional = 'Monthly';
      $amount_due_d_internacional = '';
      $amount_paid_d_internacional = '';
      $interes_d_internacional = '';
	  
	  
	  
      $no_horas_week = '';
      $fg_streams = true;
	  
	  $no_val_rub = 0;
      $no_ter_co = 100;
	  
	  $style_sin_criterios = "style='display:none;'";
      $style_sin_valor_rubric = "style='display:none;'";
      $style_sin_valor_criterio = "style='display:none;'";
      $style_max_grade = "style='display:none;'";
      $style_max_grade_wrg = "style='display:none;'";
      
      $disabled_no_val_rub = "disabled = 'disabled'";
	  
	  
	  
    }
    $nb_programa_err = "";
    $ds_duracion_err = "";
    $ds_tipo_err = "";
    $no_orden_err = "";
    $no_grados_err = "";
    $fl_template_err = "";
    $no_horas_err = "";
    $no_semanas_err = "";
    $ds_credential_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_programa = RecibeParametroHTML('nb_programa');
    $nb_programa_err = RecibeParametroNumerico('nb_programa_err');
    $ds_duracion = RecibeParametroHTML('ds_duracion');
    $ds_duracion_err = RecibeParametroNumerico('ds_duracion_err');
    $ds_tipo = RecibeParametroHTML('ds_tipo');
    $ds_tipo_err = RecibeParametroNumerico('ds_tipo_err');
    $no_orden = RecibeParametroNumerico('no_orden');
    $no_orden_err = RecibeParametroNumerico('no_orden_err');
    $no_grados = RecibeParametroNumerico('no_grados');
    $no_grados_err = RecibeParametroNumerico('no_grados_err');
    $fg_fulltime = RecibeParametroBinario('fg_fulltime');
    $fg_taxes = RecibeParametroBinario('fg_taxes');
    $fg_total_programa = RecibeParametroBinario('fg_total_programa');
    $fg_archive = RecibeParametroBinario('fg_archive');
    $mn_lecture_fee = RecibeParametroFlotante('mn_lecture_fee');
    $mn_lecture_fee_err = RecibeParametroNumerico('mn_lecture_fee_err');
    $mn_extra_fee = RecibeParametroFlotante('mn_extra_fee');
    $mn_extra_fee_err = RecibeParametroNumerico('mn_extra_fee_err');
    $fg_tax_rate = RecibeParametroBinario('fg_tax_rate');
    
    
    
    
    $no_horas = RecibeParametroNumerico('no_horas');
    $no_horas_err = RecibeParametroNumerico('no_horas_err');
    $no_horas_week = RecibeParametroNumerico('no_horas_week');
    $no_horas_week_err = RecibeParametroNumerico('no_horas_week_err');
    $no_semanas = RecibeParametroNumerico('no_semanas');
    $no_semanas_err = RecibeParametroNumerico('no_semanas_err');
    $ds_credential = RecibeParametroHTML('ds_credential');
    $ds_credential_err = RecibeParametroNumerico('ds_credential_err');
    $cl_delivery = RecibeParametroHTML('cl_delivery');
    $ds_language = RecibeParametroHTML('ds_language');
    $cl_type = RecibeParametroNumerico('cl_type');
    $fl_template = RecibeParametroNumerico('fl_template');
    $fl_template_err = RecibeParametroNumerico('fl_template_err');
    
    $app_fee = RecibeParametroFlotante('app_fee');
    $tuition = RecibeParametroFlotante('tuition');
    $no_costos_ad = RecibeParametroFlotante('no_costos_ad');
    $ds_costos_ad = RecibeParametroHTML('ds_costos_ad');
    
    $app_fee_internacional = RecibeParametroFlotante('app_fee_internacional');
    $tuition_internacional = RecibeParametroFlotante('tuition_internacional');
    $no_costos_ad_internacional = RecibeParametroFlotante('no_costos_ad_internacional');
    $ds_costos_ad_internacional = RecibeParametroHTML('ds_costos_ad_internacional');
    
    $no_payments_a = RecibeParametroNumerico('no_payments_a');
    $frequency_a = RecibeParametroHTML('frequency_a');
    $amount_due_a = RecibeParametroFlotante('amount_due_a');
    $amount_paid_a = RecibeParametroFlotante('amount_paid_a');
    $interes_a = RecibeParametroFlotante('interes_a');
    $no_payments_b =RecibeParametroNumerico('no_payments_b');
    $frequency_b = RecibeParametroHTML('frequency_b');
    $amount_due_b = RecibeParametroFlotante('amount_due_b');
    $amount_paid_b = RecibeParametroFlotante('amount_paid_b');
    $interes_b = RecibeParametroFlotante('interes_b');
    $no_payments_c = RecibeParametroNumerico('no_payments_c');
    $frequency_c = RecibeParametroHTML('frequency_c');
    $amount_due_c = RecibeParametroFlotante('amount_due_c');
    $amount_paid_c = RecibeParametroFlotante('amount_paid_c');
    $interes_c = RecibeParametroFlotante('interes_c');
    $no_payments_d = RecibeParametroNumerico('no_payments_d');
    $frequency_d = RecibeParametroHTML('frequency_d');
    $amount_due_d = RecibeParametroFlotante('amount_due_d');
    $amount_paid_d = RecibeParametroFlotante('amount_paid_d');
    $interes_d = RecibeParametroFlotante('interes_d');
    $fg_streams = RecibeParametroBinario('fg_streams');
	
	#Internacional
    $no_payments_a_internacional = RecibeParametroNumerico('no_payments_a_internacional');
    $frequency_a_internacional = RecibeParametroHTML('frequency_a_internacional');
    $amount_due_a_internacional = RecibeParametroFlotante('amount_due_a_internacional');
    $amount_paid_a_internacional = RecibeParametroFlotante('amount_paid_a_internacional');
    $interes_a_internacional = RecibeParametroFlotante('interes_a_internacional');
    $no_payments_b_internacional =RecibeParametroNumerico('no_payments_b_internacional');
    $frequency_b_internacional = RecibeParametroHTML('frequency_b_internacional');
    $amount_due_b_internacional = RecibeParametroFlotante('amount_due_b_internacional');
    $amount_paid_b_internacional = RecibeParametroFlotante('amount_paid_b_internacional');
    $interes_b_internacional = RecibeParametroFlotante('interes_b_internacional');
    $no_payments_c_internacional = RecibeParametroNumerico('no_payments_c_internacional');
    $frequency_c_internacional = RecibeParametroHTML('frequency_c_internacional');
    $amount_due_c_internacional = RecibeParametroFlotante('amount_due_c_internacional');
    $amount_paid_c_internacional = RecibeParametroFlotante('amount_paid_c_internacional');
    $interes_c_internacional = RecibeParametroFlotante('interes_c_internacional');
    $no_payments_d_internacional = RecibeParametroNumerico('no_payments_d_internacional');
    $frequency_d_internacional = RecibeParametroHTML('frequency_d_internacional');
    $amount_due_d_internacional = RecibeParametroFlotante('amount_due_d_internacional');
    $amount_paid_d_internacional = RecibeParametroFlotante('amount_paid_d_internacional');
    $interes_d_internacional = RecibeParametroFlotante('interes_d_internacional');
	
	
	
	
	# Rubric
    $no_ter_co = RecibeParametroNumerico('no_ter_co');
    $no_val_rub = RecibeParametroNumerico('no_val_rub');
    $no_val_rub_err = RecibeParametroNumerico('no_val_rub_err');
    $tab_description_err = RecibeParametroNumerico('tab_description_err');
    
    $sum_val_grade = RecibeParametroNumerico('sum_val_grade');
    
    
    $tab_rubric_err = RecibeParametroNumerico('tab_rubric_err');
    
    if($tab_description_err) 
        $style_tab_desc = "style='color:#b94a48;'";
    else
        $style_tab_desc = "style='color:#333;'";
    if($tab_rubric_err)
        $style_tab_rub = "style='color:#b94a48;'";
    else
        $style_tab_rub = "style='color:#333;'";
    
    
    if($no_val_rub_err == 0){
        $style_sin_criterios = "style='display:none;'";
        $style_sin_valor_rubric = "style='display:none;'";
    }else{
        if($no_val_rub_err == 1)
            $style_sin_criterios = "style='display:block;'";
        else
            $style_sin_criterios = "style='display:none;'";
        
        if($no_val_rub_err == 2){
            $style_sin_valor_rubric = "style='display:block;'";
            
            if($sum_val_grade != 100 and $sum_val_grade > 0){
            
            
            }else{
            $style_max_grade = "style='display:none;'";
            $style_max_grade_wrg = "style='display:none;'";
            }
        }else{
            $style_sin_valor_rubric = "style='display:none;'";
           
        
        }
        if($no_val_rub_err == 3)
            $style_sin_valor_criterio = "style='display:block;'";
        else
            $style_sin_valor_criterio = "style='display:none;'";      
    }
	
	
	
	
	
  }
  
  $total_tuition = number_format($tuition + $no_costos_ad, 2, '.', '');
  $total = number_format($app_fee + $total_tuition, 2, '.', '');
  
  $total_tuition_internacional = number_format($tuition_internacional + $no_costos_ad_internacional, 2, '.', '');
  $total_internacional = number_format($app_fee_internacional + $total_tuition_internacional, 2, '.', '');
  
  
      
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CURSOS);
  
  echo "<script type='text/javascript' src='".PATH_JS."/frmCourses.js.php'></script>";
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
 ?>
       <!-- widget content -->
            <div class="widget-body">
                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#programs" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i><?php echo ObtenEtiqueta(406) ?></a>
                    </li>
                    <li>
                        <a href="#payments" data-toggle="tab"><i class="fa fa-fw fa-lg fa-money"></i><?php echo ObtenEtiqueta(889) ?></a>
                    </li>

                     <li>
                        <a href="#payments_options" data-toggle="tab"><i class="fa fa-fw fa-lg fa-money"></i><?php echo ObtenEtiqueta(590) ?></a>
                    </li>


                    <li>
                        <a href="#teachers" data-toggle="tab"><i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(185) ?></a>
                    </li>
					
					<li>
						<a id="tab_2" href="#rubric" data-toggle="tab" <?php echo $style_tab_rub; ?>><i class="fa fa-fw fa-lg fa-table"></i><?php echo 'Rubric' ?>
						</a>
					</li>
					

                   
                </ul>

                <div id="myTabContent1" class="tab-content padding-10 no-border">
                  <div class="tab-pane fade in active" id="programs">
                        
                      <div class="row">
                            <div class="col-xs-6 col-sm-6">


                                <?php
                                Forma_Espacio( );
                                  Forma_CampoTexto(ObtenEtiqueta(360), True, 'nb_programa', $nb_programa, 50, 0, $nb_programa_err);
                                  Forma_Espacio( );
                                ?>
                            </div>

                           <div class="col-xs-6 col-sm-6">&nbsp;
                                <?php                                
                                Forma_CampoCheckbox('', 'fg_streams', $fg_streams, '<strong>'.ObtenEtiqueta(2028).'</strong>','', True, '', 'right', 'col-sm-1', 'col-sm-6');
                                Forma_Espacio( );
                                ?>
                           </div>

                      </div>
                      <div class="row">
                           <div class="col-xs-6 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(365), True, 'no_grados', $no_grados, 3, 5, $no_grados_err);
                               
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-6 col-sm-6">
                                <?php
                                // Forma_CampoTexto(ObtenEtiqueta(362), True, 'ds_tipo', $ds_tipo, 50, 20, $ds_tipo_err);
                                Forma_CampoTexto(ETQ_ORDEN, True, 'no_orden', $no_orden, 3, 5, $no_orden_err);
                                ?>
                           </div>
                     </div>

                      <div class="row">
                           <div class="col-xs-6 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(361), True, 'ds_duracion', $ds_duracion, 50, 20, $ds_duracion_err);
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-6 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(368), True, 'no_horas', $no_horas, 50, 20, $no_horas_err);
                                ?>
                           </div>
                     </div>


                      <div class="row">
                           <div class="col-xs-6 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(277), True, 'no_horas_week', $no_horas_week, 50, 20, $no_horas_week_err);
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-6 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(369), True, 'no_semanas', $no_semanas, 50, 20, $no_semanas_err);
                                ?>
                           </div>
                     </div>


                      <div class="row">
                           <div class="col-xs-6 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(610), True, 'ds_credential', $ds_credential, 50, 20, $ds_credential_err);
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-6 col-sm-6">
                                <?php
                                $p_opc = array('Online', 'On-Site', 'Combined');
                                $p_val = array('O', 'S', 'C');
                                Forma_CampoSelect(ObtenEtiqueta(611), True, 'cl_delivery', $p_opc, $p_val, $cl_delivery, $cl_delivery_err);
                                ?>
                           </div>
                     </div>


                      <div class="row">
                           <div class="col-xs-6 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(612), False, 'ds_language', $ds_language, 50, 20);
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-6 col-sm-6">
                                <?php
                                $p_opc1 = array('Long Term Duration', 'Short Term Duration', 'Corporate', 'Long Term Duration(3 contracts, 1 per year)');
                                $p_val1 = array(1, 2, 3, 4);
                                Forma_CampoSelect(ObtenEtiqueta(613), True, 'cl_type', $p_opc1, $p_val1, $cl_type, $cl_type_err);
                                ?>
                           </div>
                     </div>


                      <div class="row">
                           <div class="col-xs-6 col-sm-6">
                                <?php
                                $Query = "SELECT nb_template, fl_template FROM k_template_doc WHERE fl_categoria=1 ORDER BY nb_template";
                                Forma_CampoSelectBD(ObtenEtiqueta(367), True, 'fl_template', $Query, $fl_template, $fl_template_err, True);
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-6 col-sm-6">
                                <?php
                                $Query  = "SELECT DISTINCT CONCAT(nb_programa,' (', ds_duracion,')'), fl_programa FROM c_programa ";
                                $Query .= "WHERE fl_programa <> $clave ";
                                $Query .= "ORDER BY no_orden";
                                $row = RecuperaValor("SELECT COUNT(*) FROM c_leccion WHERE fl_programa = $clave");
                                if($row[0] > 0)
                                    $p_script = "disabled='disabled'";
                                else
                                    $p_script = '';
                                Forma_CampoSelectBD(ObtenEtiqueta(366), False, 'fl_programa', $Query, 0, '', True, $p_script);
                                ?>
                           </div>
                     </div>


                      <div class="row">
                           <div class="col-xs-6 col-sm-6">
                               <?php
                               Forma_CampoCheckBox('Full time','fg_fulltime', $fg_fulltime, '(Uncheck for Part Time)');
                               ?>
                           </div>

                          <div class="col-xs-6 col-sm-6">
                              <?php
                              Forma_CampoCheckBox(ObtenEtiqueta(695),'fg_taxes', $fg_taxes);
                              ?>
                           </div>



                      </div>

                      <div class="row">
                          <div class="col-md-4">
                              &nbsp;
                          </div>
                          <div class="col-md-4 text-center">
                            <?php
                               # Boton para archivar el programa
                                echo "
                                      <div  class='form-group'>
                                        <div class='col col-sm-12 text-align-center'>
                                          <input type='button' value='".ObtenEtiqueta(709)."' onClick='archive();' class='btn btn-primary' />
                                        </div>
                                      </div>
                                     ";
                           ?>
                          </div>


                      </div>



                  

                  </div>
                  <div class="tab-pane fade" id="payments">



                      <ul id="tab_app" class="nav nav-tabs bordered">
                        <li class="active">
                            <a href="#canada" data-toggle="tab"><i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2315) ?></a>
                        </li>
                        <li>
                            <a href="#internacional" data-toggle="tab"><i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?></a>
                        </li>
                     </ul>

                    <div id="tab_app" class="tab-content padding-10 no-border">

                        <div class="tab-pane fade in active" id="canada">
                           

                      


					    <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding padding-bottom-10">
                          
                                <?php
                                # Payments - Program Costs
                                //Forma_Espacio( );
                                //Div_Start_Responsive();
                                ?>

                                <table class="table table-striped" align="center" width="100%">
											<!--<thead>-->
												<tr>
                                                 <th colspan="3" class="text-align-center"><?php echo ObtenEtiqueta(581) ?></th>
                                                </tr>

                                                 <!--<tr>
                                                    <th colspan="2" align="left" style="font-weight:bold"><?php echo ObtenEtiqueta(582) ?> </th>
                                                    <th align="center" style="font-weight:bold"><?php echo ObtenEtiqueta(583) ?></th>
                                                  </tr>-->

											<!--</thead>-->
											<tbody>
												<tr>
													<td colspan="2" width="50%" style="padding-top: 2%;"><?php echo ObtenEtiqueta(584) ?></td>
													
													<td width="30%" align="right">
                                                    
                                                            <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('app_fee', $app_fee, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"'); ?>
                                                                </div>
                                                            </div>
                                                    </td>
													
												 </tr>
												 <tr>
													<td colspan="2" style="padding-top: 2%;"><?php   echo ObtenEtiqueta(585) ?>    </td>
													
													<td>
                                                           <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('tuition', $tuition, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"'); ?>
                                                                </div>
                                                            </div>
                                                    
                                                   </td>
													
												</tr>
												<tr>
													<td><?php echo ObtenEtiqueta(586) ?></td>
													<td class="text-center"><?php echo CampoTexto('ds_costos_ad', $ds_costos_ad, 50, 25, 'form-control');?></td>
													<td>
                                                            <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('no_costos_ad', $no_costos_ad, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"'); ?>
                                                                </div>
                                                            </div>
                                                    </td>
													
												</tr>

												<tr>
													<td colspan="2" style="padding-top: 2%;"><?php   echo ObtenEtiqueta(588) ?>    </td>
													
													<td>
                                                           <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('total_tuition', $total_tuition, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                                                </div>
                                                            </div>
                                                    
                                                   </td>
													
												</tr>
												<tr>
													<td colspan="2" style="padding-top: 2%;"><?php   echo ObtenEtiqueta(589) ?>    </td>
													
													<td>
                                                           <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('total', $total, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                                                </div>
                                                            </div>
                                                    
                                                   </td>
													
												</tr>
												
											</tbody>
										</table>


                                <?php  //Div_close_Resposive(); ?>

                                  

                                <?php
                                         // Div_Start_Responsive();
                                         Forma_Espacio();
                                        echo "
                                          <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>  
                                            <tr>
                                              <td align='right'>
                                                    <div class='form-group'>
                                                                <div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa' name='fg_total_programa' ";
                                        if(!empty($fg_total_programa))
                                            echo "checked";
                                        echo "><span> &nbsp; </span></label></div></div></td>
                                                </tr>
                                                <tr>
                                                   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate' name='fg_tax_rate' ";
                                        if(!empty($fg_tax_rate))
                                            echo "checked";
                                        echo "><span>&nbsp;</span></label></div></div></td>
                                                </tr>
                                              </table>";
                                        // Div_close_Resposive();

                                    ?>


                            </div>
                        </div>

                      
                     </div><!---END TAB-->
                        
                        
                        <div class="tab-pane fade" id="internacional">
                            
                      


					    <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding padding-bottom-10">
                          
                                <?php
                                # Payments - Program Costs
                                //Forma_Espacio( );
                                //Div_Start_Responsive();
                                ?>

                                <table class="table table-striped" align="center" width="100%">
											<!--<thead>-->
												<tr>
                                                 <th colspan="3" class="text-align-center"><?php echo ObtenEtiqueta(581) ?></th>
                                                </tr>

                                                 <!--<tr>
                                                    <th colspan="2" align="left" style="font-weight:bold"><?php echo ObtenEtiqueta(582) ?> </th>
                                                    <th align="center" style="font-weight:bold"><?php echo ObtenEtiqueta(583) ?></th>
                                                  </tr>-->

											<!--</thead>-->
											<tbody>
												<tr>
													<td colspan="2" width="50%" style="padding-top: 2%;"><?php echo ObtenEtiqueta(584) ?></td>
													
													<td width="30%" align="right">
                                                    
                                                            <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('app_fee_internacional', $app_fee_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"'); ?>
                                                                </div>
                                                            </div>
                                                    </td>
													
												 </tr>
												 <tr>
													<td colspan="2" style="padding-top: 2%;"><?php   echo ObtenEtiqueta(585) ?>    </td>
													
													<td>
                                                           <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('tuition_internacional', $tuition_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"'); ?>
                                                                </div>
                                                            </div>
                                                    
                                                   </td>
													
												</tr>
												<tr>
													<td><?php echo ObtenEtiqueta(586) ?></td>
													<td class="text-center"><?php echo CampoTexto('ds_costos_ad_internacional', $ds_costos_ad_internacional, 50, 25, 'form-control');?></td>
													<td>
                                                            <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('no_costos_ad_internacional', $no_costos_ad_internacional, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"'); ?>
                                                                </div>
                                                            </div>
                                                    </td>
													
												</tr>

												<tr>
													<td colspan="2" style="padding-top: 2%;"><?php   echo ObtenEtiqueta(588) ?>    </td>
													
													<td>
                                                           <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('total_tuition_internacional', $total_tuition_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"'); ?>
                                                                </div>
                                                            </div>
                                                    
                                                   </td>
													
												</tr>
												<tr>
													<td colspan="2" style="padding-top: 2%;"><?php   echo ObtenEtiqueta(589) ?>    </td>
													
													<td>
                                                           <div class="row">
                                                                <div class="col-md-6 text-right" style="padding-top: 3%;">
                                                                $
                                                                </div>
                                                                <div class="col-md-6 text-right">
                                                                    <?php echo CampoTexto('total_internacional', $total_internacional, 10, 10, 'form-control', False, "style='text-align:right' readonly='readonly'"); ?>
                                                                </div>
                                                            </div>
                                                    
                                                   </td>
													
												</tr>
												
											</tbody>
										</table>


                                <?php  //Div_close_Resposive(); ?>

                                  

                                <?php
                                         // Div_Start_Responsive();
                                         Forma_Espacio();
                                        echo "
                                          <table  border='".D_BORDES."' width='90%' cellpadding='3' cellspacing='0' class='css_default'>  
                                            <tr>
                                              <td align='right'>
                                                    <div class='form-group'>
                                                                <div class='checkbox'><label>".ObtenEtiqueta(698)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_total_programa_internacional' name='fg_total_programa_internacional' ";
                                        if(!empty($fg_total_programa_internacional))
                                            echo "checked";
                                        echo "><span> &nbsp; </span></label></div></div></td>
                                                </tr>
                                                <tr>
                                                   <td align='right'><div class='form-group'><div class='checkbox'><label>".ObtenEtiqueta(819)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' class='checkbox' id='fg_tax_rate_internacional' name='fg_tax_rate_internacional' ";
                                        if(!empty($fg_tax_rate_internacional))
                                            echo "checked";
                                        echo "><span>&nbsp;</span></label></div></div></td>
                                                </tr>
                                              </table>";
                                        // Div_close_Resposive();

                                    ?>


                            </div>
                        </div>





















                        </div><!---END TAB INTERNACIONAL-->
                        




                    </div>
                    <!----END TABS GENERALES--->
         
                            
                            
                            
                            
                                

                      
                  </div>


                  <div class="tab-pane fade" id="payments_options">


                       <ul id="tab_app2" class="nav nav-tabs bordered">
                        <li class="active">
                            <a href="#canadan" data-toggle="tab"><i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2315) ?></a>
                        </li>
                        <li>
                            <a href="#internacionaln" data-toggle="tab"><i class="fa fa-fw fa-lg fa-user"></i><?php echo ObtenEtiqueta(2316) ?></a>
                        </li>
                     </ul>

                    <div id="tab_app2" class="tab-content padding-10 no-border">

                        <div class="tab-pane fade in active" id="canadan">






                        <div class="row">
                            <div class="col-xs-12 col-sm-12 no-padding">
                                <?php
                                       # Payments - Payment Options
                                // Forma_Doble_Ini( );
                                // Div_Start_Responsive();
                                echo "    
                                      <table class='table table-striped' width='100%'>
                                        <tr class='css_tabla_encabezado'>
                                          <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
                                        </tr>
                                        <tr class='css_tabla_detalle' align='center' style='font-weight:bold;'>
                                          <td width='8%'>".ObtenEtiqueta(591)."</td>
                                          <td width='20%'>".ObtenEtiqueta(592)."</td>
                                          <td width='20%'>".ObtenEtiqueta(593)."</td>
                                          <td width='12%'>".ObtenEtiqueta(594)."</td>
                                          <td width='20%'>".ObtenEtiqueta(595)."</td>
                                          <td width='20%'>".ObtenEtiqueta(596)."</td>
                                        </tr>
                                        <tr class='css_tabla_detalle_bg'>
                                          <td align='center'>A</td>
                                          <td  align='center'>";
                                CampoTexto('no_payments_a', $no_payments_a, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
                                echo "
                                      </td>
                                      <td  align='center'>";
                                CampoTexto('frequency_a', $frequency_a, 15, 10, 'form-control');
                                echo "
                                      </td>
                                      <td  align='right'>";
                                CampoTexto('interes_a', $interes_a, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
                                echo "
                                  </td>
                                  <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                       <td> ";
                                CampoTexto('amount_due_a', $amount_due_a, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                echo"</td>
                                            </tr></table>";
                                echo "
                                      </td>
                                      <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                       <td>";
                               CampoTexto('amount_paid_a', $amount_paid_a, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                    echo"</td>
                            </tr></table>";
                                echo "
                                      </td>
                                    </tr>
                                    <tr class='css_tabla_detalle'>
                                      <td align='center'>B</td>
                                      <td  align='center'>";
                                CampoTexto('no_payments_b', $no_payments_b, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
                                echo "
                                      </td>
                                      <td  align='center'>";
                                CampoTexto('frequency_b', $frequency_b, 15, 10, 'form-control');
                                echo "
                                      </td>
                                      <td  align='right'>";
                                CampoTexto('interes_b', $interes_b, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
                                echo "
                                      </td>
                                      <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                       <td> ";
                                CampoTexto('amount_due_b', $amount_due_b, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                echo"</td>
                                            </tr></table>";
                                
                                echo "
                                      </td>
                                      <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                       <td> ";
                                CampoTexto('amount_paid_b', $amount_paid_b, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                echo"</td>
                                            </tr></table>";
                                echo "
                                      </td>
                                    </tr>
                                    <tr class='css_tabla_detalle_bg'>
                                      <td align='center'>C</td>
                                      <td  align='center'>";
                                CampoTexto('no_payments_c', $no_payments_c, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
                                echo "
                                      </td>
                                      <td  align='center'>";
                                CampoTexto('frequency_c', $frequency_c, 15, 10, 'form-control');
                                echo "
                                      </td>
                                      <td  align='right'>";
                                CampoTexto('interes_c', $interes_c, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
                                echo "
                                      </td>
                                      <td  align='right'>
                                            <table><tr><td>$ &nbsp;</td>
                                                       <td>
                                       ";
                                CampoTexto('amount_due_c', $amount_due_c, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                echo"</td>
                                            </tr></table>";
                                
                                echo "
                                      </td>
                                      <td  align='right'> <table><tr><td>$ &nbsp;</td>
                                                       <td> ";
                                CampoTexto('amount_paid_c', $amount_paid_c, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                echo"</td>
                                            </tr></table>";
                                echo "
                                          </td>
                                        </tr>
                                        <tr class='css_tabla_detalle'>
                                          <td align='center'>D</td>
                                          <td  align='center'>";
                                CampoTexto('no_payments_d', $no_payments_d, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
                                echo "
                                          </td>
                                          <td  align='center'>";
                                CampoTexto('frequency_d', $frequency_d, 15, 10, 'form-control');
                                echo "
                                      </td>
                                      <td  align='right'>";
                                CampoTexto('interes_d', $interes_d, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo()"');
                                echo "
                                      </td>
                                      <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                       <td> ";
                                CampoTexto('amount_due_d', $amount_due_d, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                echo"</td>
                                            </tr></table>";
                                echo "
                                  </td>
                                  <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                       <td> ";
                                CampoTexto('amount_paid_d', $amount_paid_d, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                echo"</td>
                                            </tr></table>";
                                echo "
                                      </td>
                                    </tr>
                                  </table>";
                                // Div_close_Resposive();
                                // Forma_Doble_Fin( );

                            ?>

                            </div>
                        </div>

                       </div><!----end caadadn tab-->



                        <div class="tab-pane fade" id="internacionaln">


                            

                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 no-padding">
                                                <?php
                                                       # Payments - Payment Options
                                                // Forma_Doble_Ini( );
                                                // Div_Start_Responsive();
                                                echo "    
                                                      <table class='table table-striped' width='100%'>
                                                        <tr class='css_tabla_encabezado'>
                                                          <td colspan='6' align='center' style='font-weight:bold;'>".ObtenEtiqueta(590)."</td>
                                                        </tr>
                                                        <tr class='css_tabla_detalle' align='center' style='font-weight:bold;'>
                                                          <td width='8%'>".ObtenEtiqueta(591)."</td>
                                                          <td width='20%'>".ObtenEtiqueta(592)."</td>
                                                          <td width='20%'>".ObtenEtiqueta(593)."</td>
                                                          <td width='12%'>".ObtenEtiqueta(594)."</td>
                                                          <td width='20%'>".ObtenEtiqueta(595)."</td>
                                                          <td width='20%'>".ObtenEtiqueta(596)."</td>
                                                        </tr>
                                                        <tr class='css_tabla_detalle_bg'>
                                                          <td align='center'>A</td>
                                                          <td  align='center'>";
                                                CampoTexto('no_payments_a_internacional', $no_payments_a_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
                                                echo "
                                                      </td>
                                                      <td  align='center'>";
                                                CampoTexto('frequency_a_internacional', $frequency_a_internacional, 15, 10, 'form-control');
                                                echo "
                                                      </td>
                                                      <td  align='right'>";
                                                CampoTexto('interes_a_internacional', $interes_a_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
                                                echo "
                                                  </td>
                                                  <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                                       <td> ";
                                                CampoTexto('amount_due_a_internacional', $amount_due_a_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                                echo"</td>
                                                            </tr></table>";
                                                echo "
                                                      </td>
                                                      <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                                       <td>";
                                               CampoTexto('amount_paid_a_internacional', $amount_paid_a_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                                    echo"</td>
                                            </tr></table>";
                                                echo "
                                                      </td>
                                                    </tr>
                                                    <tr class='css_tabla_detalle'>
                                                      <td align='center'>B</td>
                                                      <td  align='center'>";
                                                CampoTexto('no_payments_b_internacional', $no_payments_b_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
                                                echo "
                                                      </td>
                                                      <td  align='center'>";
                                                CampoTexto('frequency_b_internacional', $frequency_b_internacional, 15, 10, 'form-control');
                                                echo "
                                                      </td>
                                                      <td  align='right'>";
                                                CampoTexto('interes_b_internacional', $interes_b_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
                                                echo "
                                                      </td>
                                                      <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                                       <td> ";
                                                CampoTexto('amount_due_b_internacional', $amount_due_b_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                                echo"</td>
                                                            </tr></table>";
                                
                                                echo "
                                                      </td>
                                                      <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                                       <td> ";
                                                CampoTexto('amount_paid_b_internacional', $amount_paid_b_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                                echo"</td>
                                                            </tr></table>";
                                                echo "
                                                      </td>
                                                    </tr>
                                                    <tr class='css_tabla_detalle_bg'>
                                                      <td align='center'>C</td>
                                                      <td  align='center'>";
                                                CampoTexto('no_payments_c_internacional', $no_payments_c_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
                                                echo "
                                                      </td>
                                                      <td  align='center'>";
                                                CampoTexto('frequency_c_internacional', $frequency_c_internacional, 15, 10, 'form-control');
                                                echo "
                                                      </td>
                                                      <td  align='right'>";
                                                CampoTexto('interes_c_internacional', $interes_c_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
                                                echo "
                                                      </td>
                                                      <td  align='right'>
                                                            <table><tr><td>$ &nbsp;</td>
                                                                       <td>
                                                       ";
                                                CampoTexto('amount_due_c_internacional', $amount_due_c_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                                echo"</td>
                                                            </tr></table>";
                                
                                                echo "
                                                      </td>
                                                      <td  align='right'> <table><tr><td>$ &nbsp;</td>
                                                                       <td> ";
                                                CampoTexto('amount_paid_c_internacional', $amount_paid_c_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                                echo"</td>
                                                            </tr></table>";
                                                echo "
                                                          </td>
                                                        </tr>
                                                        <tr class='css_tabla_detalle'>
                                                          <td align='center'>D</td>
                                                          <td  align='center'>";
                                                CampoTexto('no_payments_d_internacional', $no_payments_d_internacional, 3, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
                                                echo "
                                                          </td>
                                                          <td  align='center'>";
                                                CampoTexto('frequency_d_internacional', $frequency_d_internacional, 15, 10, 'form-control');
                                                echo "
                                                      </td>
                                                      <td  align='right'>";
                                                CampoTexto('interes_d_internacional', $interes_d_internacional, 5, 3, 'form-control', False, 'style="text-align:right" onchange="calcula_costo_internacional()"');
                                                echo "
                                                      </td>
                                                      <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                                       <td> ";
                                                CampoTexto('amount_due_d_internacional', $amount_due_d_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                                echo"</td>
                                                            </tr></table>";
                                                echo "
                                                  </td>
                                                  <td  align='right'><table><tr><td>$ &nbsp;</td>
                                                                       <td> ";
                                                CampoTexto('amount_paid_d_internacional', $amount_paid_d_internacional, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                                
                                                echo"</td>
                                                            </tr></table>";
                                                echo "
                                                      </td>
                                                    </tr>
                                                  </table>";
                                                // Div_close_Resposive();
                                                // Forma_Doble_Fin( );

                                            ?>

                                            </div>
                                        </div>










                        </div><!----end tab internacionaln---->




                
                      </div>  <!-----end tab genral---->
                
                
                </div>





                  <div class="tab-pane fade" id="teachers">
				  
				         <div class="row">
                            <div class="col-xs-12 col-sm-12">
                          
                                    <?php
                                    Forma_Espacio( );
                                    Forma_CampoTexto(ObtenEtiqueta(710), True, 'mn_lecture_fee', $mn_lecture_fee, 50, 20, $mn_lecture_fee_err);
                                    Forma_CampoTexto(ObtenEtiqueta(711), True, 'mn_extra_fee', $mn_extra_fee, 50, 20, $mn_extra_fee_err);
                                    Forma_Espacio();
                                    ?>


                            </div>
                        </div>
				  
                 
                 </div>
               
			   
			<!--===========Inicia Rubric==========-->
			     <div class="tab-pane fade" id="rubric">
				 
						<style>
						#sortable { 
						  list-style: none; 
						  text-align: left; 
						}
						#sortable li { 
						  margin: 0 0 10px 0;
						  height: 225px; 
						}
						</style>
					 
				 
				       
					<script>
					  // Activa el input del valor del rubric
					  function ActivaValorRubric(val){
						if(val == 1)
						  $('#no_val_rub').prop("disabled", false);
					  }
					
					  // Funcion para validar sumatoria de rubrics
					  function ValidaValorRubric(val){     
						  var fl_programa = <?php echo $clave ?>;
						  //var no_semana=document.getElementById('no_semana').value;
						  //var no_grado=document.getElementById('no_grado').value;

						$.ajax({
						  type: 'POST',
						  url : 'valida_leccion_curso_valor.php',
						  async: false,
						  data: 'fl_programa='+fl_programa+
								//'&no_semana='+no_semana+
								//'&no_grado='+no_grado+
								'&rubric=1',
						  success: function(data) {
							if(data=='')
							  data = 0;
							  //alert(data);
							  document.getElementById('no_ter_co2').value = data;
						  }
						});
						
						valor_bd  = document.getElementById('no_ter_co2').value; // Sumatoria de valores base de datos
					   
						if(val == "")
						  val = 0;
						
						no_val_rub_bd = document.getElementById('no_val_rub_bd').value;
					   
						valor_tot = (parseInt(valor_bd) + parseInt(val)) - parseInt(no_val_rub_bd); // Sumatoria base de datos + valor actual
						valor_fin = parseInt(100) - parseInt(valor_tot); // Resta del valor maximo - valor total actual
						document.getElementById('no_ter_co').value = valor_fin;

						// Mostramos mensajes de error
						if(valor_tot > 100){ // Si es Mayor a 100
						  document.getElementById('no_ter_co').style.backgroundColor = '#FFF0F0';
						  document.getElementById('no_ter_co').style.borderColor = '#953b39';
						  document.getElementById('MensajeErrRubric').style.display = 'block';
						  document.getElementById('MensajeWrgRubric').style.display = 'none';  
						}
						if(valor_tot < 100){ // Si es Menor a 100
						  document.getElementById('no_ter_co').style.backgroundColor = '#efe1b3';
						  document.getElementById('no_ter_co').style.borderColor = '#dfb56c';
						  document.getElementById('MensajeErrRubric').style.display = 'none';
						  document.getElementById('MensajeWrgRubric').style.display = 'block';
						}
						if(valor_tot == 100){ // Si es 0 -> Es 100
						  document.getElementById('no_ter_co').style.backgroundColor = '#fff';
						  document.getElementById('no_ter_co').style.borderColor = '#ccc';
						  document.getElementById('MensajeErrRubric').style.display = 'none';  
						  document.getElementById('MensajeWrgRubric').style.display = 'none';         
						}
						if(valor_tot == 0)
						  document.getElementById('style_sin_criterios').style.display = 'none';
					  }
					  
					  // Funcion para cambiar estilos del boton Add Rubric
					  function CambiaEstiloBtn(act, val){
						if(act == 0){
						  $('#btn_add_rubric').removeClass('btn bg-color-blueLight txt-color-white disabled');
						  $('#btn_add_rubric').addClass('btn btn-primary');
						}else{
						  $('#btn_add_rubric').removeClass('btn btn-primary');
						  $('#btn_add_rubric').addClass('btn bg-color-blueLight txt-color-white disabled');
						  var fl_criterio = document.getElementById("fl_criterio").value;
						  var clave = <?php echo $clave; ?>;
						  $.ajax({
							type: 'POST',
							url : 'arma_rubrics_curso.php',
							async: false,
							data: 'fl_criterio='+fl_criterio+
								  '&val='+val+
								  '&clave='+clave,
							success: function(data) {
							  $("#muestra_rubrics").html(data);
							}
						  });              
						}
						if(val != 0)
						  ValidaCriterios();
					  }

					  // Funcion para validar valores de criterios
					  function ValidaCriterios(){   
						cle = <?php echo $clave; ?>;
						$.ajax({
						  type: 'POST',
						  url : 'suma_criterios_curso.php',
						  async: false,
						  data: 'valida=1'+
								'&cle='+cle,
						  success: function(data) {
							
							if(data == "")
							  data = 0;
							
							valor = parseInt(100) - parseInt(data);
							
							document.getElementById('no_ses_wk').value = valor;           
							document.getElementById('sum_val_grade').value = data;
							
							if(data > 100){ // Si es Mayor a 100
							  document.getElementById('no_ses_wk').style.backgroundColor = '#FFF0F0';
							  document.getElementById('no_ses_wk').style.borderColor = '#953b39';
							  document.getElementById('MensajeErrCriterio').style.display = 'block';
							  document.getElementById('MensajeWrgCriterio').style.display = 'none';
							}
							if(data < 100){ // Si es Menor a 100
							  document.getElementById('no_ses_wk').style.backgroundColor = '#efe1b3';
							  document.getElementById('no_ses_wk').style.borderColor = '#dfb56c';
							  document.getElementById('MensajeErrCriterio').style.display = 'none';
							  document.getElementById('MensajeWrgCriterio').style.display = 'block';
							}
							if(data == 100){ // Si es 0 -> Es 100
							  document.getElementById('no_ses_wk').style.backgroundColor = '#EEEEEE';
							  document.getElementById('no_ses_wk').style.borderColor = '#CCCCCC';
							  document.getElementById('MensajeErrCriterio').style.display = 'none';
							  document.getElementById('MensajeWrgCriterio').style.display = 'none';
							}                
						  }
						}); 
					  }
					
					  // Actualiza lista de criterios
					  function ActListaCriterios(){
						
						cle = <?php echo $clave; ?>;
						
						$.ajax({
						  type: 'POST',
						  url : 'act_lista_criterios_curso.php',
						  data: 'clave='+cle,
						  async: false,
						  success: function(html) {
							$('#DivActListaCriterios').html(html);
						  }
						});            
						
						
					  }
					
					</script>
					
					<script>
					  $(document).ready(function() {
						  $('#sortable').sortable({
							  axis: 'y',
							  opacity: 0.7,
							  // handle: 'span',
							  update: function(event, ui) {
								  var list_sortable = $(this).sortable('toArray').toString();
							  // change order in the database using Ajax
								  $.ajax({
									  url: 'act_ord_criterios_curso.php?clave='+<?php echo $clave; ?>,
									  type: 'POST',
									  data: {list_order:list_sortable},
									  success: function(data) {
										  //finished
									  }
								  });
							  }
						  }); // fin sortable
					  });
					  </script>
				 
					 <?php
					  # Obtenemos valor total de la sumatoria de los rubric del mismo programa, misma leccion y mismo term
					  $suma_rubric = RecuperaValor("SELECT SUM(no_valor_rubrics) FROM c_programa WHERE fl_programa = $clave ");
					  Forma_CampoOculto('no_ter_co2', $suma_rubric[0]);
					  
					  if(!empty($clave)){
                         // if($fg_error<>1)                          
						$no_ter_co = ((100) - $suma_rubric[0]);
                      }
					  # Obtenemos valor total de los criterios
					  $suma = RecuperaValor("SELECT SUM(no_valor) FROM k_criterio_curso WHERE fl_programa = $clave");
					  $no_ses_wk = ((100) - $suma[0]);
					  $sum_val_grade = $suma[0];
					?>
								 
										 
						<!-- Select -->
						  <div class="col-sm-4 col-lg-5" style="padding-left:0px; padding-right:0px;">
							<div class="col col-xs-8 col-sm-8" style="padding-left:0px; padding-right:0px;">
							  <div id="DivActListaCriterios">
								<?php
								  # Borramos criterios que no esten relacionados a un curso
								  if((empty($clave)) AND (empty($fg_error)))
									EjecutaQuery("DELETE FROM k_criterio_curso WHERE fl_programa = 0");

								  $Query  = "SELECT c.nb_criterio, c.fl_criterio FROM c_criterio c ";
								  $Query .= "WHERE NOT EXISTS (SELECT * FROM k_criterio_curso k WHERE k.fl_programa = $clave AND k.fl_criterio = c.fl_criterio) ";
								  $Query .= "ORDER BY c.nb_criterio ASC ";
								  Forma_CampoSelectBD(ObtenEtiqueta(1330), False, 'fl_criterio', $Query, 0, '', True, "onchange='CambiaEstiloBtn(0, 0);'", 'left', 'col col-md-12', 'col col-md-12', '', 'cop_ru');
								?>
							  </div>
							</div>
							
							<!-- Boton Add Rubric -->
							<div class="col col-xs-12 col-sm-2">
							  <?php
								echo "<div id='' class='row form-group '>
								  <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
								  <div class='col col-sm-12'><label class='input'><a href='javascript:CambiaEstiloBtn(1, 0); ActListaCriterios(); ActivaValorRubric(1);' class='btn bg-color-blueLight txt-color-white disabled' id='btn_add_rubric' >".ObtenEtiqueta(1332)."</a></label></div>      
								</div>";
							  ?>
							</div>
						  </div>
						  
						  <?php 
							$rub_val  = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1338)."'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
							$var  = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1337)."'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
							$var2 = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1336)."'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
						  ?>
						  
						  <div class="col col-xs-2 col-sm-2" style="padding-left:0px; padding-right:0px; width:135px;">
							<label class="col col-sm-12 control-label text-align-left">
							  <strong><?php echo $rub_val.ObtenEtiqueta(1331).":"; ?></strong>
							</label>
							<div class="col-sm-12"> 
							  <label class="input" id="">
								<input class="form-control" id="no_val_rub" name="no_val_rub" value="<?php echo $no_val_rub; ?>" maxlength="3" size="12" type="text" <?php echo $disabled_no_val_rub; ?> onblur	="ValidaValorRubric(this.value);">
							  </label>
							  <?php 
                              
                              if($fg_error==1)
                                  $no_val_rub=0;
                                  
                              Forma_CampoOculto('no_val_rub_bd', $no_val_rub); ?>
							  <!-- Muestra error si el rubric NO tiene valor pero SI hay criterios -->
							  <div id="style_sin_valor_rubric" <?php echo $style_sin_valor_rubric; ?>>
								<?php 
								  echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
								  data-original-title='".ObtenEtiqueta(1344)."'
								  style='color:#b94a48; font-weight:bold;' id=''>
								  <i class='fa fa-warning'></i> Alert!</a>&nbsp;&nbsp;&nbsp;";
								?>
							  </div>
							</div>  
						  </div>

						  <!-- Inputs peso -->
						  <div class="col col-xs-12 col-sm-5">

							<div class="col col-xs-12 col-sm-4" style = "width:180px;">
							  <label class="col col-sm-12 control-label text-align-left">
								<strong><?php echo $var2.ObtenEtiqueta(1334); ?></strong>
							  </label>
							  <div class="col-sm-12"> 
								  <label class="input" id="">
									<input class="form-control" id="no_ter_co" name="no_ter_co" value="<?php echo $no_ter_co; ?>" maxlength="3" size="12" type="text" disabled="disabled">
								  </label>
								<div id="MensajeErrRubric" style="display:none;">
								  <?php 
									echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
									data-original-title='".ObtenEtiqueta(1340)."'
									style='color:#b94a48; font-weight:bold;' id=''>
									<i class='fa fa-warning'></i> ".ObtenEtiqueta(1348)."</a>&nbsp;&nbsp;&nbsp;";
								  ?>
								</div>
								<div id="MensajeWrgRubric" style="display:none;">
								  <?php 
									echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
									data-original-title='".ObtenEtiqueta(1339)."'
									style='color:#dfb56c; font-weight:bold;' id=''>
									<i class='fa fa-warning'></i> ".ObtenEtiqueta(1349)."</a>&nbsp;&nbsp;&nbsp;";
								  ?>
								</div>
								
							  </div> 
							</div>
						  
							<div class="col col-xs-12 col-sm-5">
							  <label class="col col-sm-12 control-label text-align-left">
								<strong><?php echo $var.ObtenEtiqueta(1333); ?></strong>
							  </label>
							  <div class="col-sm-12"> 	
								  <label class="input" id="">
									<input class="form-control" id="no_ses_wk" name="no_ses_wk" value="<?php echo $no_ses_wk; ?>" maxlength="3" size="12" type="text" disabled="disabled">
									<?php Forma_CampoOculto('sum_val_grade', $sum_val_grade); ?>
								  </label>
								
								 <div id="MensajeErrCriterio" <?php echo $style_max_grade; ?>>
								  <?php 
									echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
									data-original-title='".ObtenEtiqueta(1342)."'
									style='color:#b94a48; font-weight:bold;'>
									<i class='fa fa-warning'></i> ".ObtenEtiqueta(1348)."</a>&nbsp;&nbsp;&nbsp;";
								  ?>
								</div>
								 <div id="MensajeWrgCriterio" <?php echo $style_max_grade_wrg; ?>>
								  <?php 
									echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
									data-original-title='".ObtenEtiqueta(1341)."'
									style='color:#dfb56c; font-weight:bold;'>
									<i class='fa fa-warning'></i> ".ObtenEtiqueta(1349)."</a>&nbsp;&nbsp;&nbsp;";
								  ?>
								</div>
							  </div>  
							</div>
							<?php
							
								echo "<div class='col col-xs-12 col-sm-2'>
							  <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
							  <div class='col col-sm-12'><label class='input'><a href='#AbrePreviewRubric' data-toggle='modal' data-target='#AbrePreviewRubric' class='btn btn-primary'  id='btnAbrePreviewRubric' onClick='MuestraRubric();'>".ObtenEtiqueta(1335)."</a></label></div>      
							</div>";
							
							?>

						  </div>
						  
						  <br>
						  
						  <!-- Muestra error si el rubric tiene valor pero no hay criterios -->
						  <div id="style_sin_criterios" <?php echo $style_sin_criterios; ?>>
							<div class="row">
							  <div class="col-xs-1 col-sm-1"></div>
							  <div class="col-xs-10 col-sm-10">
								<div class="alert alert-danger fade in">
								  <i class="fa-fw fa fa-times"></i>
								  <strong><?php echo ObtenEtiqueta(1343); ?> </strong>
								</div>
							  </div>
							  <div class="col-xs-1 col-sm-1"></div>
							</div>
						  </div>      
						  
						  <?php 
								if(empty($fg_error)) 
									echo "<br><br><br><br>"; 
						  ?>
						  <br>
						   
						  <div class="row">
							<div class="col col-xs-12 col-sm-12">
							  <div id="muestra_rubrics">
								  
								<div class="row">
								  <div class="col col-xs-12 col-sm-12">
									<?php
									
									# Recuperamos registros 
									$Query_p = "SELECT fl_criterio, no_valor FROM k_criterio_curso WHERE fl_programa = $clave ORDER BY no_orden ASC ";
									$rs_p = EjecutaQuery($Query_p);
									$registros_p = CuentaRegistros($rs_p);
									
									echo "<ul id='sortable' style='padding-left:0px;'>";
									
									for($i_p=1;$row_p=RecuperaRegistro($rs_p);$i_p++) {
									  $fl_criterio = $row_p[0];
									  $no_valor = $row_p[1];
									  if($no_valor == NULL)
										$no_valor = "<span style='font-style: italic; color: #D14;'>Empty</span>&nbsp;&nbsp;";
									  if($i_p == $registros_p)
									  $borde = '1px';
									  else
									  $borde = '1px';

									  echo "<li id='$fl_criterio'>";
									?>

									  <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									  <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-0" data-widget-editbutton="false">
									  <div style="border-width: 1px 1px <?php echo $borde; ?>;">
									  <div class="jarviswidget-editbox"></div>
									  <div class="widget-body" style="padding-bottom:0px;">
									  <div class="row" style="padding-bottom:0px; padding-top:0px;">
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
										  <p class="text-align-left" style="margin: -13px 0 1px;"><span class="glyphicon glyphicon-move" style="cursor: move;"></span></p>
										</div>
										<div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
										  <p class="text-align-right" style="margin: -13px 0 1px;"><a href='javascript:CambiaEstiloBtn(1,  <?php echo $fl_criterio; ?>); ActListaCriterios();'><i class="fa fa-times"></i></a></p>
										</div>
									  </div>                      
									  <div class="table-responsive">
										<table class="table table-bordered" style="width:100%;">
										  <thead>
											<tr>
											  <th><center><?php echo ObtenEtiqueta(1656); ?></center></th>
											  <th width="12%"><center><?php echo ObtenEtiqueta(1657); ?></center></th>
											  <th width="12%"><center><?php echo ObtenEtiqueta(1658); ?></center></th>
											  <th width="12%"><center><?php echo ObtenEtiqueta(1659); ?></center></th>
											  <th width="12%"><center><?php echo ObtenEtiqueta(1660); ?></center></th>
											  <th width="12%"><center><?php echo ObtenEtiqueta(1661); ?></center></th>
											  <th width="15%"><center><?php echo "Max Grade"; ?></center></th>
											</tr>
										  </thead>
										  <tbody>
											<?php
											  $name = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
											?>
											<tr>
											  <td><?php echo str_texto($name[0]); ?></td>
											  <?php
												for($x=5; $x>0; $x--){
												  
												  #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
												  $Query1="SELECT C.fl_calificacion_criterio,C.ds_calificacion, ds_descripcion FROM k_criterio_fame K
												  JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio = C.fl_calificacion_criterio
												  WHERE fl_criterio = $fl_criterio AND C.fl_calificacion_criterio = $x ";
												  $row=RecuperaValor($Query1);
												  $ds_calificacion1=$row[1];
												  $ds_descripcion1=$row[2];
												  
												  // echo "<td  width='12%'>$ds_calificacion1<br/><small class='text-muted'><i>$ds_descripcion1</i></small></td>";
												  echo "<td  width='12%'>$ds_calificacion1<br><small class='text-muted'><br>
													<div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
													<small class='text-muted'><i>$ds_descripcion1</i></small>              
													</div>
												  </small></td>";                                  
												}
											  ?>
											  <td  width="15%">
												<div class="widget-body"  style="padding-top:20px; vertical-align: middle; font: bold 40px Arial; text-align: center; ">
												  <div id="user_<?php echo $fl_criterio; ?>"  style="clear: both">
													<a href="#" id="username_<?php echo $fl_criterio; ?>" data-placement="left" data-type="text" data-pk="<?php echo $fl_criterio; ?>" data-original-title="Add value"><?php echo $no_valor; ?></a>%
												  </div>
												</div>
											  </td>
											</tr>
										  </tbody>
										</table>
									  </div>
									  </div>
									  </div>
									  </div>
									  </article> 


									  <script type="text/javascript">

										// DO NOT REMOVE : GLOBAL FUNCTIONS!

										$(document).ready(function() {

										  pageSetUp();

										  /*
										  * X-Ediable
										  */

										  //ajax mocks
										  $.mockjaxSettings.responseTime = 500;

										  $.mockjax({
											url: '/post',
											response: function (settings) {
												log(settings, this);
											}
										  });

										  //TODO: add this div to page
										  function log(settings, response) {
											var s = [],
												str;
											s.push(settings.type.toUpperCase() + ' url = "' + settings.url + '"');
											for (var a in settings.data) {
												if (settings.data[a] && typeof settings.data[a] === 'object') {
													str = [];
													for (var j in settings.data[a]) {
														str.push(j + ': "' + settings.data[a][j] + '"');
													}
													str = '{ ' + str.join(', ') + ' }';
												} else {
													str = '"' + settings.data[a] + '"';
												}
												s.push(a + ' = ' + str);
											}

											if (response.responseText) {
												if ($.isArray(response.responseText)) {
													s.push('[');
													$.each(response.responseText, function (i, v) {
														s.push('{value: ' + v.value + ', text: "' + v.text + '"}');
													});
													s.push(']');
												} else {
													s.push($.trim(response.responseText));
												}
											}
											s.push('--------------------------------------\n');
											$('#console').val(s.join('\n') + $('#console').val());
										  }

										  /*
										  * X-EDITABLES
										  */

										  $('#inline').on('change', function (e) {
											if ($(this).prop('checked')) {
												window.location.href = '?mode=inline#ajax/plugins.html';
											} else {
												window.location.href = '?#ajax/plugins.html';
											}
										  });

										  if (window.location.href.indexOf("?mode=inline") > -1) {
											$('#inline').prop('checked', true);
											$.fn.editable.defaults.mode = 'inline';
										  } else {
											$('#inline').prop('checked', false);
											$.fn.editable.defaults.mode = 'popup';
										  }

										  //defaults
										  $.fn.editable.defaults.url = '/post';
										  //$.fn.editable.defaults.mode = 'inline'; use this to edit inline

										  //enable / disable
										  $('#enable').click(function () {
											$('#user_<?php echo $fl_criterio; ?> .editable').editable('toggleDisabled');
										  });

										  //editables
										  $('#username_<?php echo $fl_criterio; ?>').editable({
											url: 'suma_criterios_curso.php',
											type: 'text',
											pk: <?php echo $fl_criterio; ?>,
											name: '<?php echo $clave; ?>',
											title: 'Enter username',    
											validate: function(value) {
											  var regex = /^[0-9]+$/;
											  if(! regex.test(value)) {
												  return '<?php echo ObtenEtiqueta(1346); ?>';
											  }
											  if(value > 100 ) {
												  return '<?php echo ObtenEtiqueta(1347); ?>';
											  }
											}
										  });


										  $('#user_<?php echo $fl_criterio; ?> .editable').on('hidden', function (e, reason) {
											if (reason === 'save' || reason === 'nochange') {
												var $next = $(this).closest('tr').next().find('.editable');
												if ($('#autoopen').is(':checked')) {
													setTimeout(function () {
														$next.editable('show');
													}, 300);
												} else {
													$next.focus();
													ValidaCriterios();
												}
											}
										  });			

										})

									  </script>
									<?php
									  echo "</li>";
									}
									echo "</ul>";
									?>
								  </div>
								</div>
								
							  
							  </div>
							</div>
						  </div>
						  
						  <div class="row">
							<div class="col col-xs-12 col-sm-12">
							  <!-- Preview Rubric -->
							  <div class="modal fade" id="AbrePreviewRubric" tabindex="-1" role="dialog" aria-labelledby="myModalLabelaa" aria-hidden="true">
								<div class="modal-dialog" style="width:90%;">
								  <div class="modal-content">
									<div class="modal-header">
									  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
										&times;
									  </button>
									  <center><h4 class="modal-title" id="myModalLabelaa"></h4></center>
									</div>
									<div class="modal-body">
									  <div class="row">
										<div class="col-md-12">
										  <!-- Muestra criterios cargados al rubric -->
										  <div id="PreviewRubric"></div>
										</div>
									  </div>
									</div>
									<div class="modal-footer">
									  <center>
									  <?php
										echo "<div style='display:block;' >
										  <button type='button' class='btn btn-primary' data-dismiss='modal' style='font-size: 14px;border-radius: 10px;'><i class='fa fa-check-circle'></i>&nbsp;&nbsp;".ObtenEtiqueta(74)."</button>
										</div>";
									  ?>
									  </center>
									</div>
								  </div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							  </div><!-- /.modal -->

						   
							</div>
						  </div>
						  
					  
								 
								 
								 
								 
				 
				 
				 
				 
				 </div>
			   <!----=====Termina Rubric========-->
			   
			   
                </div>
            </div>
  
  




  
  <?php

  echo"
   <script>
                                        function archive(){
                                          var answer = confirm('".str_ascii(ObtenMensaje(20))."');
                                          if(answer) {
                                            $.ajax({
                                              type: 'POST',
                                              url : 'archive.php',
                                              async: false,
                                              data: 'clave='+$clave+
                                                    '&fg_archive='+1,
                                              success: function(data) {
                                                 if(data==1)
                                                  window.location = 'courses.php';
                                              }
                                            });
                                          }
                                        }
                                      </script>
  
  ";
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CURSOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  
   include(SP_HOME."/AD3M2SRC4/bootstrap/inc/scripts.php");
  echo"  
   <script src='".PATH_SELF_JS."/plugin/x-editable/moment.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/jquery.mockjax.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/x-editable.min.js'></script>
 ";

  
  #scripts para que funcione circulos verdes rubric.
  echo"<script src='../../../modules/common/new_campus/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js'></script>";
  
  echo"
   <!-- Script para preview de rubric -->
  <script>";
  
  echo "
  // Rubric
   function MuestraRubric(){ 
    document.getElementById('myModalLabelaa').innerHTML = \"<i class='fa fa-table' aria-hidden='true'></i> <b>Rubric: </b>\" + document.getElementById('nb_programa').value;
    
    $.ajax({
      type: 'POST',
      url: 'rubric_curso_preview_modal.php',
      data:'clave=".$clave."',
      success: function(data) {
        $('#PreviewRubric').html(data);
      }
    })
    }

  </script>";
  
  
  
  
  
  # Pie de Pagina
 // PresentaFooter( );
  include(SP_HOME."/AD3M2SRC4/bootstrap/inc/footer.php"); 
  
  # Pie de Pagina
  //PresentaFooter( );
  
?>