<?php 
# Libreria de funciones
require ('../AD3M2SRC4/lib/general.inc.php');





# Presenta pagina de Login
$page_title = "FAME Higher Education";

$fl_usuario_registro=RecibeParametroNumerico('i',true);

if(!empty($fl_usuario_registro)){
	$Query="SELECT ds_first_name,ds_last_name,ds_email FROM k_friends_invitation where fl_friends_invitation=$fl_usuario_registro";
	$row=RecuperaValor($Query);
	$ds_fname=str_texto($row['ds_first_name']);
	$ds_lname=str_texto($row['ds_last_name']);
	$ds_email=str_texto($row['ds_email']);
}


?>

<!DOCTYPE html>
<html lang="en-us" >
	<head>
	

       <meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title><?php echo $page_title ?> </title>
		<!--<meta name="description" content="">
		<meta name="author" content="">
        -->
		
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Please note (smartadmin-production.css) was created using LESS variables -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-skins.css">

		<!-- SmartAdmin RTL Support is under construction
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.css"> -->

		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/demo.css">

		<!-- FAVICONS -->

         <link rel="shortcut icon" href="https://campus.vanas.ca/fame/img/fame.ico" type="image/x-icon">
		<link rel="icon" href="https://campus.vanas.ca/fame/img/fame.ico" type="image/x-icon">


		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

         <script src="js/bootbox.min.js"></script>

   <?php
   
   ?>





</head>
<style>

   /** html { overflow-y:hidden; }se elimina el scroll horizontal de la pag*/

</style>



	<body class="animated fadeInDown fixed-ribbon" style="background:#f3f3f3;">

  		
		<!-- MAIN PANEL -->
		<div id="main" role="main" class="no-margin">



<style>
/*estilo para la imgane backgronud*/
.bgimage {
background-image: url(img/login.jpg);
background-size: 100%;
 background-repeat: no-repeat;
}
.bgimage-inside {
padding-top: 10.36%; /* this is actually (426/1140)*100 */
}

.fonts{

    color: #34373E;
    font-weight: normal;
    line-height: 1.2em;
    -webkit-text-stroke: 1px transparent;
	
	}
	</style>
	

						 <div class="row">
							  <div class="col-md-12 bgimage">
										<div class="bgimage-inside"></div>
							  </div>
						 </div>

 <style>
  .fixed-ribbon #content {
    padding-top: 10px !important;
}

     .smart-form .input input, .smart-form .select select, .smart-form .textarea textarea {
         height: 34px !important;
     }

  </style>


                                    <div class="row">
										<div class="col-md-12 text-center">
											<h1 style="font-size:35px;"><?php echo ObtenEtiqueta(906) ?></h1>
									
										</div>

									</div>	
			<!-- MAIN CONTENT -->
			<div id="content" class="container">



   
    





<!-- widget grid -->
<section id="Section1" class="">
	<!-- START ROW -->

	<div class="row">

	        <div class="col-md-3">
                                                            <div  id="presenta_modal" name="presenta_modal">
																	
															  </div>
	        </div>

            <div class="col-md-6">

     <!-- NEW COL START -->
		<article class="col-sm-12 col-md-12 col-lg-12">

			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget" id="wid-id-8" data-widget-editbutton="false" data-widget-custombutton="false">
			
						
						<!--<form action="send_registration.php" method="post" name="contact-form" id ="contact-form" class="smart-form">-->
						<div  name="contact-form" id ="contact-form" class="smart-form">
                            <style>
                                .smart-form header {
                                    border-bottom: 0px dashed rgba(0,0,0,.2) !important;
                                }
                                .jarviswidget > div {
                                    /*form sin borders*/

                                    border-right-color: #FFF !important;
                                    border-bottom-color: #FFF !important;
                                    border-left-color: #FFF !important;
                                }
                                .smart-form fieldset {
                                   
                                    padding: 0px 14px 5px !important;
                                }

                            </style>

                            <?php
                            
                            $no_dias_permitidos_para_modo_trial=ObtenConfiguracion(101);
                            $no_usuarios_maximos_trial=ObtenConfiguracion(102);
                            
                            $title=ObtenEtiqueta(2075);
							$cadena=ObtenEtiqueta(908);
                            $cadena= ""; # first name a quein se le envia el correo
                            $title2="";
							
							
                            ?>


							<fieldset>					
								 <p class="text-center" style="font-size:22px; color:#0092dc;"><?php echo $title; ?> <p>
														
                                <section class="note">
																	<p class="text-center" style="font-size: 14px;">  <span><i><?php echo $title2 ?></i></span></p>
																	</section>
                                
								<div class="row" id="formulario">
                                    <div class="col-xs-12 col-sm-2 col-lg-2">
                                                            
                                    </div>
                                    <div class="col-xs-12 col-sm-8 col-lg-8" >
                                       <section>
                                            
                                            <label class="label "style=" font-size: 14px;"><?php echo ObtenEtiqueta(909) ?>:</label>
                                            <label class="input" id="fname"> <i class="icon-append fa fa-user"></i>
                                            <input id="ds_firts_name" name="ds_firts_name" type="text" value="<?php echo $ds_fname;?>" >
                                            <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i><?php echo ObtenEtiqueta(959) ?></b></label>
                                      </section>

                                        <section>
                                            <label class="label"style=" font-size: 14px;"> <?php echo ObtenEtiqueta(910) ?>:</label>
                                            <label class="input" id="lname"> <i class="icon-append fa fa-user"></i>
                                            <input id="ds_last_name" name="ds_last_name" type="text" value="<?php echo $ds_lname;?>" >
                                            <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> <?php echo ObtenEtiqueta(960) ?></b></label>
                                      </section>


									<section>
									        <div class="form-group">
													<label class="label"style=" font-size: 14px;"> <?php echo ObtenEtiqueta(287) ?>:</label>
													<select style="width:100%" id="fl_pais" name="fl_pais" class="select2" >
                                                        <option value="0"><?php echo ObtenEtiqueta(70); ?></option>
													<?php 
                                                    $Query = "SELECT ds_pais, fl_pais FROM c_pais WHERE 1=1 and fg_activo='1' ";
                                                    $rs = EjecutaQuery($Query);
                                                    
                                                     while($row = RecuperaRegistro($rs)) {
                                                         $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
                                                    ?>	
															<option value="<?php echo $row[1];?>"><?php echo $etq_campo; ?></option>
															
                                                    <?php 
                                                     }
                                                    ?>

													</select>
												</div>
									  
									</section>  
									  
									  
									  
                                       <section>
                                            <label class="label"style=" font-size: 14px;"><?php echo ObtenEtiqueta(911) ?>:</label>
                                            <label class="input" id="correo"> <i class="icon-append fa fa-envelope-o"></i>
                                            <input id="email" name="email" type="text" value="<?php echo $ds_email;?>" >
                                            <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> <?php echo ObtenEtiqueta(961) ?></b></label>
                                      </section>

									  
									  <section>
                                            <label class="label"style=" font-size: 14px;"><?php echo ObtenEtiqueta(2307) ?>:</label>
                                            <label class="input" id="conf_correo"> <i class="icon-append fa fa-envelope-o"></i>
                                            <input id="confirmacion_email" name="confirmacion_email" type="text" value="<?php echo $ds_email;?>" >
                                            <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> <?php echo ObtenEtiqueta(2308) ?></b></label>
											<label class="text-danger hidden" id="err_email"> <?php echo ObtenEtiqueta(2308) ?></label>
                                      </section>
									  
									  
									  
									  
                                    </div>
                                    <div class="col-xs-12 col-sm-2 col-lg-2">

                                    </div>

								</div>

                                     <div class="row" >
                                         <div class="col-xs-12 col-sm-2 col-lg-2">

                                         </div>
                                         <div class="col-xs-12 col-sm-8 col-lg-8">
                                            <section>
									        <label class="checkbox" style="padding-left: 24px;"><input name="fg_rm" id="fg_rm" type="checkbox"  onclick="HabilitaBoton(this.form);"><i></i><p style="font-size:13px; color:#999;"><?php echo ObtenEtiqueta(912) ?><font color="#0092dc"><a href="javascript:void(0);"  data-toggle="modal" data-target="#myModal2" >&nbsp;<?php echo ObtenEtiqueta(913); ?></a></font></p></label>
								           </section>
                                         </div>
                                         <div class="col-xs-12 col-sm-2 col-lg-2">

                                         </div>

                                     </div>


                                     
							</fieldset>
							            <style>
							            .smart-form footer {
                                            background: #fff !important;
							            }


							                .alert {
							                    color: #FFF;
							                    border-width: 0;
							                    border-left-width: 0px;
							                }
							                smart-form footer .btn {
							                    
							                    height: 38px !important;
							                }
							                .btn-primary {
							                   
							                     background-color: #0092cd !important;
							                }
							              



							        </style>
                                        <footer>
                                            <div class="col-xs-2 col-sm-2 col-lg-2">

                                            </div>
                                            <div class="col-xs-8 col-sm-8 col-lg-8">

                                                <table width="100%">
                                                    <thead>
                                                        <th width="30%">

                                                        </th>
                                                        <th width="40%">
                                                          <p><button type="submit" class="btn btn-primary alert" name="bot" id="bot" style="border-radius: 10px;" onclick='HabilitaBoton(this.form);' disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(914); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></p>    
                                                        </th>
                                                        <th width="30%">

                                                        </th>
                                                    </thead>


                                            </table>

                                            </div>
                                            <div class="col-xs-2 col-sm-2 col-lg-2">

                                            </div>


                                            
                                          

                                        </footer>


							                       
							               
							


						</div>						
						
					
					
		
				
										


		</article>
		<!-- END COL -->	



            </div>

            <div class="col-md-3">


                
                                  <!-- Modal  que presenta el contrato. -->
                                    <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal2">
                                      MJD boton para mstrar el contrato se queda oculta como referencia nada mas
                                    </button>


                                    <div class="modal fade " id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                      <div class="modal-dialog modal-lg" role="document" style="margin: 150px auto !important;
                                                         margin-top: 150px;">
                                        <div class="modal-content">
                                          <div class="modal-header text-center">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(913); ?></h4>
      
      
                                          </div>
                                          <div class="modal-body" style="padding: 0px;">

                                              

                                              <div id="chat-body" class="chat-body custom-scroll" style="background:#fff;">
                                                 <!---Presentamos el contrato --->
												 
												 
                                                  <?php  
                                                  
                                                  #se genera el cuerpo del documento del contrato
                                                  $ds_encabezado_contrato = genera_ContratoFame($fl_instituto, 1,102,$fl_usuario);
                                                  $ds_cuerpo_contrato = genera_ContratoFame($fl_instituto, 2, 102,$fl_usuario);
                                                  $ds_pie_contrato = genera_ContratoFame($fl_instituto, 3,102,$fl_usuario);
                                                  
                                                  echo $ds_encabezado_contrato."<br/> ".$ds_cuerpo_contrato."<br/> ".$ds_pie_contrato;
                                                  
                                                  ?>
                                              </div>
          
          

                                          </div>
                                          <div class="modal-footer text-center">
          
       

                                              <a  class="btn btn-primary" style="font-size:14px;border-radius: 10px; border-color: #2c699d;" data-dismiss="modal" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Close &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>


        

                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                 <!----------------->

                    








            </div>
		

	</div>

	<!-- END ROW -->

</section>
<!-- end widget grid -->



			</div>
			<!-- END MAIN CONTENT -->

		</div>
		<!-- END MAIN PANEL -->



   	<!--================================================== -->

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>

		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script>
		    if (!window.jQuery) {
		        document.write('<script src="js/libs/jquery-2.0.2.min.js"><\/script>');
		    }
		</script>

		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
		    if (!window.jQuery.ui) {
		        document.write('<script src="js/libs/jquery-ui-1.10.3.min.js"><\/script>');
		    }
		</script>

		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events
		<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

		<!-- BOOTSTRAP JS -->
		<script src="js/bootstrap/bootstrap.min.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src="js/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="js/smartwidgets/jarvis.widget.min.js"></script>

		<!-- EASY PIE CHARTS -->
		<script src="js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

		<!-- SPARKLINES -->
		<script src="js/plugin/sparkline/jquery.sparkline.min.js"></script>

		<!-- JQUERY VALIDATE -->
		<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>

		<!-- JQUERY MASKED INPUT -->
		<script src="js/plugin/masked-input/jquery.maskedinput.min.js"></script>

		<!-- JQUERY SELECT2 INPUT -->
		<script src="js/plugin/select2/select2.min.js"></script>

		<!-- JQUERY UI + Bootstrap Slider -->
		<script src="js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

		<!-- browser msie issue fix -->
		<script src="js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

		<!-- FastClick: For mobile devices -->
		<script src="js/plugin/fastclick/fastclick.js"></script>

		<!--[if IE 7]>

		<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

		<![endif]-->

		<!-- Demo purpose only -->
		<script src="js/demo.js"></script>

		<!-- MAIN APP JS FILE -->
		<script src="js/app.js"></script>

		<!-- PAGE RELATED PLUGIN(S) -->
		<script src="js/plugin/jquery-form/jquery-form.min.js"></script>
		

        <script>

           $(document).ready(function () {

			
               $('#ds_firts_name').change(function () {
                   HabilitaBoton();
				   
				   var ds_firts_name = document.getElementById('ds_firts_name').value;
				   
				   if(ds_firts_name.length == ''){
					    $('#fname').removeClass('state-success');
					    $('#fname').addClass('state-error');
					   
				   }else{
					   $('#fname').removeClass('state-error');
					    $('#fname').addClass('state-success');
					   
				   }
				   
               });
			
               $('#ds_last_name').change(function () {
                   HabilitaBoton();
				   
				   var ds_last_name = document.getElementById('ds_last_name').value;
				   
				   if(ds_last_name.length == ''){
					    $('#lname').removeClass('state-success');
					    $('#lname').addClass('state-error');
					   
				   }else{
					   $('#lname').removeClass('state-error');
					   $('#lname').addClass('state-success');
					   
				   }
				   
				   
				   
               });
               $('#email').change(function () {
                   HabilitaBoton();
				   
				   var email = document.getElementById('email').value;
				   
				   //if(email.length == ''){
					 //   $('#correo').removeClass('state-success');
					  //  $('#correo').addClass('state-error');
					   
				//   }else{
					//   $('#correo').removeClass('state-error');
					//   $('#correo').addClass('state-success');
					   
				//   }
				   
				   
               });
             
			  
			
			
			
           });



         function  HabilitaBoton (){
           
             

             var ds_firts_name = document.getElementById('ds_firts_name').value;
             var ds_last_name = document.getElementById('ds_last_name').value;
             var ds_email = document.getElementById('email').value;
             var fl_pais = document.getElementById('fl_pais').value;
			 var confirmacion_email=document.getElementById('confirmacion_email').value;

			
			 
			 
			 
             if ($('#fg_rm').is(':checked')) {
                 var fg_aceptar = 1;
             } else {
                 var fg_aceptar = 0;
             }

            

             if (ds_firts_name.length == '') {
                 //check1.checked = false;
                 document.getElementById('fg_rm').checked = false;
                 $('#bot').attr('disabled', true);//se desabilita
                 document.getElementById('ds_firts_name').focus();
                 return;
             } else if (ds_last_name.length == '') {
                 document.getElementById('fg_rm').checked = false;
                 $('#bot').attr('disabled', true);//se desabilita
                 document.getElementById('ds_last_name').focus();
                 return;
             } else if (fl_pais == 0) {
                 document.getElementById('fg_rm').checked = false;
                 $('#bot').attr('disabled', true);//se desabilita
                 document.getElementById('fl_pais').focus();
                 return;
             } else if (ds_email.length == '') {
                 document.getElementById('fg_rm').checked = false;
                 $('#bot').attr('disabled', true);//se desabilita
                 document.getElementById('email').focus();
                 return;
             } else if (ds_email.length > 0) {



                 expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                 if (!expr.test(ds_email)) {
                     document.getElementById('fg_rm').checked = false;
                     //alert('Ingrese un email valido');
                     var valor = 1;
                     document.getElementById('email').focus();
					 $('#correo').removeClass('state-success');
					 $('#correo').addClass('state-error');
					 
					 
                 } else {
                     var valor = 2;
					  $('#correo').removeClass('state-error');
					  $('#correo').addClass('state-success');
					 
                 }

				 
				 if(confirmacion_email==ds_email){
				 
				 
				 //se coultan letras rojas
				 $('#err_email').addClass('hidden');
				 $('#conf_correo').removeClass('state-error');
				 $('#conf_correo').addClass('state-success');
				 
                 //hasta que todos los campos sean llenados podremos enviar el form
                 if ((fg_aceptar == 1) && (ds_firts_name.length > 0) && (ds_last_name.length > 0) && (valor != 1)&&(fl_pais >0) ) {

                     $('#bot').attr('disabled', false);//se desabilita
                     
                 } else {
                    
                     $('#bot').attr('disabled', true);//se habilita

                 }
				 
				 }else{
					 
					 //salen letras rojas.
					 $('#err_email').removeClass('hidden');
					 $('#conf_correo').removeClass('state-success');
					 $('#conf_correo').addClass('state-error');
					 
					 document.getElementById('fg_rm').checked = false;//se desabilita
					 
				 }
				 
				 
				 

             }

             //desacivamos el submit del boton del formulario
             $('input[type="submit"]').prop('disabled', false);




           }

		   
		   
		   //desabilitamos el copiar el texto.
		$(document).ready(function(){
			  $("#email").on('paste', function(e){
				e.preventDefault();
				//alert('Esta acción está prohibida');
			  })
			  
			  $("#email").on('copy', function(e){
				e.preventDefault();
				//alert('Esta acción está prohibida');
			  })
			  
			  $("#confirmacion_email").on('paste', function(e){
				e.preventDefault();
				//alert('Esta acción está prohibida');
			  })
			  
			  $("#confirmacion_email").on('copy', function(e){
				e.preventDefault();
				//alert('Esta acción está prohibida');
			  })
			  
			  
		});
		   
		   
		   

        </script>


 <script>
     $(document).on("click", ".alert", function (e) {
         var ds_firts_name = document.getElementById('ds_firts_name').value;
         var ds_last_name = document.getElementById('ds_last_name').value;
         var ds_email = document.getElementById('email').value;
         var fg_aceptar = document.getElementById('fg_rm').checked;
         var fl_pais = document.getElementById('fl_pais').value;
		 var confirmacion_email=document.getElementById('confirmacion_email').value;
		 
		 //Proviene del feeed.
		 var fl_envio=<?php echo $fl_usuario_registro;?>;
		 
		 var fg_student=1;

             if($('#fg_rm').is(':checked')) {  
                 var fg_aceptar=1;  
             } else {  
                 var fg_aceptar=0;  
             } 


             $.ajax({
                 type: 'POST',
                 url: 'div/send_resgistration.php',
                 data: 'ds_firts_name='+ds_firts_name+
                       '&ds_last_name='+ds_last_name+
                       '&fg_aceptar='+fg_aceptar+
                       '&ds_email='+ds_email+
					   '&fl_envio='+fl_envio+
                       '&fl_pais='+fl_pais+
					   '&fg_student='+fg_student,


                 async: false,
                 success: function (html) {
                     $('#envio_contacto').html(html);

                    
                     document.getElementById('fg_rm').checked = false;
                 }
             });

             if (fg_aceptar == 1) {

                 //saber dominio
                 $.ajax({
                     type: 'POST',
                     url: 'search_domain_email.php',
                     data: 'ds_firts_name=' + ds_firts_name +
                           '&ds_last_name=' + ds_last_name +
                           '&fg_aceptar=' + fg_aceptar +
                           '&ds_email=' + ds_email,


                     async: false,
                     success: function (html) {
                         $('#presenta_modal').html(html);

                         
                         

                     }
                 });


             }

             


     });


</script>



		<script type="text/javascript">

		    // DO NOT REMOVE : GLOBAL FUNCTIONS!

		    $(document).ready(function () {

		        pageSetUp();



		      

		        var $contactForm = $("#contact-form").validate({
		            // Rules for form validation
		            rules: {
		                ds_firts_name: {
		                    required: true
		                },

		                ds_last_name: {
                         required: true
                        },
		                email: {
		                    required: true,
		                    email: true
		                },
		                message: {
		                    required: true,
		                    minlength: 10
		                }
		            },

		            // Messages for form validation
		            messages: {
		                ds_firts_name: {
		                   required: '',
		                },
		                ds_last_name: {
		                    required: '',
		                },
		                email: {
		                    required: '',
		                    email: ''
		                },
		                message: {
		                    required: ''
		                }
		            },

		            // Ajax form submition
		            submitHandler: function (form) {
		                $(form).ajaxSubmit({
		                    success: function () {
		                        $("#contact-form").addClass('submited');
		                    }
		                });
		            },

		            // Do not change code below
		           // errorPlacement: function (error, element) {
		               // error.insertAfter(element.parent());
		           // }
		        });

		      

		        

		       



		    })

		</script>




























<?php

function Forma_CampoTextoSP($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='', $class_div = "form-group", $prompt_aling='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4') {
    
    if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        if(!empty($p_error)) {
            $ds_error = ObtenMensaje($p_error);
            $ds_clase_err = 'has-error';
            $ds_clase = 'form-control';      
        }
        else {
            $ds_clase = 'form-control';
            $ds_error = "";
            $ds_clase_err = '';
        }
        if(!empty($p_id)) {
            if($fg_visible)
                $ds_visible = "inline";
            else
                $ds_visible = "none";
        }
       
        
        echo "
    <div id='div_".$p_nombre."' class='row ".$class_div." ".$ds_clase_err."'>
      <label  text-align-$prompt_aling'>
        ";
        if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
        if($p_requerido) echo "* ";
        if(!empty($p_prompt)) echo "$p_prompt:"; else echo "&nbsp;";
        if(!empty($p_id)) echo "</div>";
        echo "
       
      </label>
      <div class='$col_sm_cam'>
       ";
        if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
        CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
        if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
        if(!empty($p_id)) echo "</div>";
        if(!empty($p_error)){          
            echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
        }
        echo "
     
      </div>      
    </div>";
        
    }
    else
        Forma_CampoOculto($p_nombre, $p_valor);
}







function Forma_CampoSelectBDSP($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
    
    $ds_clase = 'form-control';
    if(!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase_err = 'has-error';
    }
    else {
        $ds_error = "";
        $ds_error_err = "";
    }
   
    echo "
  <div class='form-group smart-form $ds_clase_err'>
    <label class='$col_sm_etq control-label text-align-$etq_align'>
      ";
    if($p_requerido)  echo "* ";
    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
    echo "
      
    </label>
    <div class='$col_sm_cam'><label class='select'>";
    CampoSelectBD($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    echo "<i></i>";
    if(!empty($p_error))
        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
    echo "
    </label></div>     
  </div>";
}

function CampoSelectBDSP($p_nombre, $p_query, $p_actual, $p_clase='css_input', $p_seleccionar=False, $p_script='') {
    

    
    echo "<select id='$p_nombre' name='$p_nombre' class='select2  required/'  ";
    if(!empty($p_script)) echo " $p_script";
    echo " >\n";
    if($p_seleccionar)
        echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
    $rs = EjecutaQuery($p_query);
    while($row = RecuperaRegistro($rs)) {
        echo "<option value=\"$row[1]\"";
        if($p_actual == $row[1])
            echo " selected";
        
        # Determina si se debe elegir un valor por traduccion
        $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
        echo ">$etq_campo</option>\n";
    }
    echo "</select>";
}


/*
#funcioes propias de registration.php
# Funcion para generar EMAIL DE INVITACION
function genera_ContratoFame($clave, $opc, $correo=False, $firma=False, $no_contrato=1,$fl_template,$ds_cve,$fl_instituto) {

    
    # Recupera datos del template del documento
    switch($opc)
    {
        case 1: $campo = "ds_encabezado"; break;
        case 2: $campo = "ds_cuerpo"; break;
        case 3: $campo = "ds_pie"; break;
        case 4: $campo = "nb_template"; break;
    }
    $Query  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
    $row = RecuperaValor($Query);
    $cadena = str_uso_normal($row[0]);
    
    #Recuperamos datos generales del iNSTITUTO 
    
    $Query="SELECT I.ds_instituto,I.ds_codigo_pais,I.ds_codigo_area,I.no_telefono  ";
    $Query.="FROM c_instituto I ";
    $Query.="JOIN c_pais P ON P.fl_pais=I.fl_pais ";
    $Query.="WHERE I.fl_instituto=$fl_instituto ";
    $row=RecuperaValor($Query);
    $nb_instituto=$row['ds_instituto'];
    $ds_codigo_pais=$row['ds_codigo_pais'];
    $ds_codigo_area=$row['ds_codigo_area'];
    $no_telefono=$row['no_telefono'];
    
    
    
    
    
    # Sustituye variables con datos del alumno
    $cadena = str_replace("#sp_nb_instituto#", "".$nb_instituto,$cadena); # nb_isntituto 
    //$cadena = str_replace("#sp_nb_admin#", "".$src_redireccion, $cadena);  #bont link redireccion 
    

    
    return ($cadena);
}


*/




?>




</body>
</html>