<?php
	
require '../../lib/general.inc.php';

$fg_data=RecibeParametroNumerico('fg_data');


?>

<form id="wizard-1_<?php echo $fg_data;?>" novalidate="novalidate">
	<div id="bootstrap-wizard-1_<?php echo $fg_data;?>" class="col-sm-12">
		<div class="form-bootstrapWizard">
			<ul class="bootstrapWizard form-wizard hidden">
				<li class="active" data-target="#step1">
					<a href="#tab1" data-toggle="tab"> <span class="step">1</span> <span class="title">Student information</span> </a>
				</li>
				<!---<li data-target="#step2">
					<a href="#tab2" data-toggle="tab"> <span class="step">2</span> <span class="title">Terms start dates</span> </a>
				</li>
				<li data-target="#step3">
					<a href="#tab3" data-toggle="tab"> <span class="step">3</span> <span class="title">Groups and schedules</span> </a>
				</li>
				<li data-target="#step4">
					<a href="#tab4" data-toggle="tab"> <span class="step">4</span> <span class="title">Save Form</span> </a>
				</li>-->
			</ul>
			<div class="clearfix"></div>
		</div>
		<div class="tab-content">
			<div class="tab-pane active" id="tab1">
				<br>
				<h3><strong>Step 1 </strong> - Student Infomation</h3><br />		
				<div class="row">				
					<div class="col-sm-12">
																	
                         <?php 
                         if($fg_data==1){                            
                             $Query  = "SELECT DISTINCT p.nb_programa, p.fl_programa 
                                        FROM k_ses_app_frm_1 a 
                                        JOIN c_sesion s ON a.cl_sesion=s.cl_sesion 
                                        JOIN c_programa p ON p.fl_programa=a.fl_programa
                                        JOIN k_app_contrato c ON a.cl_sesion=c.cl_sesion
                                        JOIN c_periodo pe ON pe.fl_periodo=a.fl_periodo  
                                        WHERE s.fg_app_1='1' AND s.fg_app_2='1' AND s.fg_app_3='1' AND s.fg_app_4='1' 
                                        AND s.fg_confirmado='1' AND s.fg_inscrito='0' AND s.fg_archive='0'
                                        AND (no_contrato IS NULL OR no_contrato=1) ORDER BY fe_inicio ASC";
                             Forma_CampoSelectBD('Course name', False, 'fl_programa_application', $Query,0,'',true,'onclick=\'MuestraUsers(1);\'');
                         } 
                         if($fg_data==2){
                             
                             $Querys  = "SELECT DISTINCT  p.nb_programa, Term.fl_programa
                                        FROM c_usuario u
                                        JOIN k_ses_app_frm_1 a ON a.cl_sesion=u.cl_sesion
                                        JOIN c_programa p ON p.fl_programa= a.fl_programa
                                        left JOIN k_alumno_grupo AlumnoGrupo ON(AlumnoGrupo.fl_alumno = u.fl_usuario) AND AlumnoGrupo.fg_grupo_global<>'1'
                                        LEFT JOIN c_grupo Grupo ON (Grupo.fl_grupo = AlumnoGrupo.fl_grupo)
                                        LEFT JOIN k_term Term ON(Term.fl_term = Grupo.fl_term)
                                        LEFT JOIN c_periodo pe ON pe.fl_periodo=a.fl_periodo AND pe.fl_periodo=Term.fl_periodo 
                                        JOIN c_alumno alu ON alu.fl_alumno=u.fl_usuario
                                        WHERE u.fg_activo='1' AND u.fl_perfil=3 ";
                            Forma_CampoSelectBD('Course name', False, 'fl_programa_student', $Querys, 0,'',true,'onclick=\'MuestraUsers(2);\'');

                         }
                         ?>
                         
                         <div id="table_users"></div>
                        
                        
                        			
					</div>				
				</div>
			</div>
			<div class="tab-pane" id="tab2">
				<br>
				<h3><strong>Step 2</strong> - Terms start dates</h3><br />				
				<div class="row">
					<div class="col-md-12" id="terms_start_dates"></div>
					
				</div>			
			</div>
			<div class="tab-pane" id="tab3">
				<br>
				<h3><strong>Step 3</strong> - Groups and schedules</h3>
				<br>
				<div class="row">
                    <div class="col-md-12" id="groups_schedules">

                    </div>
				</div>
			</div>
			<div class="tab-pane" id="tab4">
				<br>
				<h3><strong>Step 4</strong> - Groups and schedules</h3>
				<br>
				<div class="row">
                    <div class="col-md-12">

                    </div>
				</div>
			</div>
				
			<div class="form-actions"style="margin-top: 0px;">
				<div class="row hidden">
					<div class="col-sm-12">
						<ul class="pager wizard no-margin">
							<!--<li class="previous first disabled">
							<a href="javascript:void(0);" class="btn btn-lg btn-default"> First </a>
							</li>-->
							<li class="previous disabled">
								<a href="javascript:void(0);" class="btn btn-lg btn-default"> Previous </a>
							</li>
																		
							<li class="next" id="step1" >
								<a href="javascript:void(0);" class="btn btn-lg txt-color-darken" onclick="Step(1);"> Next paso 1</a>
							</li>
                            <li class="next hidden" id="step2" onclick="Step(2);">
								<a href="javascript:void(0);" class="btn btn-lg txt-color-darken"> Next paso 2 </a>
							</li>
                             <li class="next hidden" id="step3" onclick="Step(3);">
								<a href="javascript:void(0);" class="btn btn-lg txt-color-darken"> Next paso 3 </a>
							</li>
                            
						</ul>
					</div>
				</div>
			</div>
				
		</div>
	</div>
</form>
	                                              

<!-- PAGE RELATED PLUGIN(S) -->
<script src="../../bootstrap/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="../../bootstrap/js/plugin/fuelux/wizard/wizard.min.js"></script>

<script>

    // DO NOT REMOVE : GLOBAL FUNCTIONS!

    $(document).ready(function () {

        pageSetUp();

        //Bootstrap Wizard Validations
        var $validator = $("#wizard-1_<?php echo $fg_data;?>").validate({

            rules: {

            },

            messages: {

            },

            highlight: function (element) {
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('#bootstrap-wizard-1_<?php echo $fg_data;?>').bootstrapWizard({
            'tabClass': 'form-wizard',
            'onNext': function (tab, navigation, index) {
                
                var $valid = $("#wizard-1_<?php echo $fg_data;?>").valid();
                if (!$valid) {
                    $validator.focusInvalid();
                    return false;
                } else {
                    $('#bootstrap-wizard-1_<?php echo $fg_data;?>').find('.form-wizard').children('li').eq(index - 1).addClass(
			          'complete');
                    $('#bootstrap-wizard-1_<?php echo $fg_data;?>').find('.form-wizard').children('li').eq(index - 1).find('.step')
			        .html('<i class="fa fa-check"></i>');
                }
            }
        });

        // fuelux wizard
        var wizard = $('.wizard').wizard();

        wizard.on('finished', function (e, data) {
            //$("#fuelux-wizard").submit();
            //console.log("submitted!");
            $.smallBox({
                title: "Congratulations! Your form was submitted",
                content: "<i class='fa fa-clock-o'></i> <i>1 seconds ago...</i>",
                color: "#5F895F",
                iconSmall: "fa fa-check bounce animated",
                timeout: 4000
            });

        });

    });

    function MuestraUsers(fg_data) {
        $("#table_users").empty();


        //Obtenemos combo select 
        if (fg_data == 1) {
            var fl_programa = document.getElementById('fl_programa_application').value;
        } else {
            var fl_programa = document.getElementById('fl_programa_student').value;
        }

        /*Muestra tabla de usuarios.*/
        $.ajax({
            type: 'POST',
            url: 'scheduler_tbl_users.php',
            data: 'fg_data=' + fg_data+
                  '&fl_programa=' + fl_programa,
            async: false,
            success: function (html) {
             
            $('#table_users').html(html);
                     

            }
        });


    }
    MuestraUsers(<?php echo $fg_data;?>);
    function Step(step) {

        if (step == 1) {
            $('#step1').addClass('hidden');
            $('#step2').removeClass('hidden');
            $('#step3').addClass('hidden');

            //envia informacion y muestra relacion entre alumnos y terms.
            var tot_reg = $("#tot_reg").val(), i = 1, j = 1, seleccionados = 0;
            var fg_tipo_studiante = "<?php echo $fg_data;?>";
            var allusers = "";
            var users = [];
            for (i; i <= tot_reg; i++) {
                var reg = $("#ch_" + i).is(':checked');
                var val = $("#ch_" + i).val();
                var use_lic = $("#use_lic_" + i).val();
                if (reg == true) {
                    seleccionados++;
                    // Solo contara a los estudiantes
                    if ((users.indexOf(val) < 0) && (use_lic == 1)) {
                        allusers +="'"+val+"',";
                        users.push(val);
                    }
                }
            }


            $.ajax({
                type: 'POST',
                url: 'scheduler_asignar_student.php',
                data: 'users=' + users +
                      '&step='+step+
                      '&allusers='+allusers+
                      '&seleccionados=' + seleccionados +
                      '&fg_tipo_studiante=' + fg_tipo_studiante,
                async: false,
                success: function (html) {
                    $('#terms_start_dates').html(html);
                }
            });

        }
        if(step == 2) {
            $('#step1').addClass('hidden');
            $('#step2').addClass('hidden');
            $('#step3').removeClass('hidden');

            $.ajax({
                type: 'POST',
                url: 'scheduler_asignar_student.php',
                data: 'users=' + users +
                      '&allusers=' + allusers +
                      '&step=' + step +
                      '&seleccionados=' + seleccionados +
                      '&fg_tipo_studiante=' + fg_tipo_studiante,
                async: false,
                success: function (html) {
                    $('#terms_start_dates').html(html);
                }
            });




        }
        


    }


		</script>





























