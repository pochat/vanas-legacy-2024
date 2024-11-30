<?php 
# Libreria de funciones
require ('../AD3M2SRC4/lib/general.inc.php');

# Presenta pagina de Login
$page_title = "VanAS Online Campus - Login";
?>

<!DOCTYPE html>
<html lang="en-us" >
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title> <?php echo $page_title != "" ? $page_title : ""; ?></title>
		<meta name="description" content="">
		<meta name="author" content="">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

	
		   <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
            

        
        <!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Caution! DO NOT change the order -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/smartadmin-production-plugins.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/smartadmin-production.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/smartadmin-skins.min.css">

		<!-- SmartAdmin RTL Support is under construction-->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_HOME; ?>/bootstrap/css/smartadmin-rtl.min.css">

        <script src="js/bootbox.min.js"></script>



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
		<link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">
		<link rel="icon" href="img/favicon/favicon.ico" type="image/x-icon">

		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">



   <?php
    echo "
<script type='text/javascript'>

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-20837828-1']);
      _gaq.push(['_setDomainName', '.vanas.ca']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>

    <script type='text/javascript'>

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-27662999-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>

    <script type='text/javascript'>

     

      var BrowserDetect = {
        init: function () {
          this.browser = this.searchString(this.dataBrowser) || \"An unknown browser\";
          this.version = this.searchVersion(navigator.userAgent)
            || this.searchVersion(navigator.appVersion)
            || \"an unknown version\";
          this.OS = this.searchString(this.dataOS) || \"an unknown OS\";
        },
        searchString: function (data) {
          for (var i=0;i<data.length;i++) {
            var dataString = data[i].string;
            var dataProp = data[i].prop;
            this.versionSearchString = data[i].versionSearch || data[i].identity;
            if (dataString) {
              if (dataString.indexOf(data[i].subString) != -1)
                return data[i].identity;
            }
            else if (dataProp)
              return data[i].identity;
          }
        },
        searchVersion: function (dataString) {
          var index = dataString.indexOf(this.versionSearchString);
          if (index == -1) return;
          return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
        },
        dataBrowser: [
          {
            string: navigator.userAgent,
            subString: \"Chrome\",
            identity: \"Chrome\"
          },
          {   string: navigator.userAgent,
            subString: \"OmniWeb\",
            versionSearch: \"OmniWeb/\",
            identity: \"OmniWeb\"
          },
          {
            string: navigator.vendor,
            subString: \"Apple\",
            identity: \"Safari\",
            versionSearch: \"Version\"
          },
          {
            prop: window.opera,
            identity: \"Opera\"
          },
          {
            string: navigator.vendor,
            subString: \"iCab\",
            identity: \"iCab\"
          },
          {
            string: navigator.vendor,
            subString: \"KDE\",
            identity: \"Konqueror\"
          },
          {
            string: navigator.userAgent,
            subString: \"Firefox\",
            identity: \"Firefox\"
          },
          {
            string: navigator.vendor,
            subString: \"Camino\",
            identity: \"Camino\"
          },
          {   // for newer Netscapes (6+)
            string: navigator.userAgent,
            subString: \"Netscape\",
            identity: \"Netscape\"
          },
          {
            string: navigator.userAgent,
            subString: \"MSIE\",
            identity: \"Explorer\",
            versionSearch: \"MSIE\"
          },
          {
            string: navigator.userAgent,
            subString: \"Gecko\",
            identity: \"Mozilla\",
            versionSearch: \"rv\"
          },
          {     // for older Netscapes (4-)
            string: navigator.userAgent,
            subString: \"Mozilla\",
            identity: \"Netscape\",
            versionSearch: \"Mozilla\"
          }
        ],
        dataOS : [
          {
            string: navigator.platform,
            subString: \"Win\",
            identity: \"Windows\"
          },
          {
            string: navigator.platform,
            subString: \"Mac\",
            identity: \"Mac\"
          },
          {
               string: navigator.userAgent,
               subString: \"iPhone\",
               identity: \"iPhone/iPod\"
            },
          {
            string: navigator.platform,
            subString: \"Linux\",
            identity: \"Linux\"
          }
        ]

      };
      BrowserDetect.init();

      function validaForma(forma)
      {
        if(BrowserDetect.browser==\"Firefox\" || BrowserDetect.browser==\"Chrome\" || BrowserDetect.browser==\"Safari\")
        {
          document.datos.submit();
        }
        else 
        {
          alert('Access is allowed only from Chrome, Firefox, or Safari');
          return;
        }   
      }

    </script>";
    ?>





</head>
	<body class="animated fadeInDown fixed-ribbon" style="background:#f3f3f3;">

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
  </style>
  
  
							<!-- MAIN CONTENT -->
							<div id="content" class="container">
							
							
									<div class="row">
										<div class="col-md-12 text-center">
											<h1 style="font-size:35px;"><?php echo ObtenEtiqueta(906) ?></h1>
										<br>
										</div>

									</div>	
	
	
	
	
	
	
<style>
	
	.smart-form header {
	border-bottom: 0px dashed rgba(0,0,0,.2)!important;
	}
	
	.smart-form footer {
	background: rgba(255, 255, 255, 0)!important;
	}
	
	.smart-form .note a {
	color:#0092dc;
	
	}

    /*es para reducir el espacio de margen que xiste en el headddr del login**/
    .client-form header {
        padding: 5px 13px !important;
    }
	.smart-form fieldset {
    display: block;
    padding: 2px 14px 5px !important;
	}
</style>
	
	
															

	
	
									  <div class="row">
												  <div class="col-md-4">
												  	<!--Pantalla de envio de mensajes de error-->						
															  <div  id="presenta_modal" name="presenta_modal">
																	
															  </div>
												  </div>

												  <div class="col-md-4">

                                                     


														<form name="order-form" id="order-form" method="post" action="send_registration.php" class="smart-form client-form"  novalidate="novalidate">
														
															<header style="background:#fff;">
																	<p class="text-center" style="font-size:26px; color:#0092dc;"><?php echo ObtenEtiqueta(907) ?> <p>
															
																	<section class="note">
																	<p class="text-center">  <span><i><?php echo ObtenEtiqueta(908) ?></i></span></p>
																	</section>
																	
																	
																	
																	<div id="envio_contacto"></div>
															</header>
															
															<fieldset>
																
																<div class="row">
																		<div class="col-md-2">
																		</div>
																		
																		<div class="col-md-8">
																			<section>
																				<label class="label">* <?php echo ObtenEtiqueta(909) ?></label>
																				<label class="input"> 
																				
																				
																				<input type="text" id="ds_firts_name" name="ds_firts_name"/>
																				<!--<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter username</b></label>-->
																
																			</section>
																			
																			<section>
																				<label class="label">* <?php echo ObtenEtiqueta(910) ?></label>
																				<label class="input"> 
																				
																				
																				<input type="text" id="ds_last_name" name="ds_last_name" />
																				<!--<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter username</b></label>-->
																			</section>
																			
																			
																			<section>
																					<label class="label">* <?php echo ObtenEtiqueta(911) ?></label>
																					<label class="input"> <!--<i class="icon-append fa fa-lock"></i>-->
																					<input type='text' name='ds_email' id='ds_email' />
																					
																									
																						
																			</section>
																			
																		</div>
																		
																		<div class="col-md-2">
																		</div>
																</div>
																
																
																
																
																						<section class="note">
																						  <label class="checkbox">
																									 <?php
																									if($fg_check_rm)
																										echo "<input type='checkbox' name='fg_rm' id='fg_rm' value='1' checked='True' onclick='validaInformacion(this.form);' >";
																									else
																										echo "<input type='checkbox' name='fg_rm' id='fg_rm' value='1' onclick='validaInformacion(this.form);' >";
																									?>
																										
																							<i></i><p style="font-size:11px; color:#999;"><?php echo ObtenEtiqueta(912) ?><font color="#0092dc"><a href=""><?php echo ObtenEtiqueta(913); ?></a></font></p><?php ?>                   
																									  
																						  
																						</section>
																
																
																
																
																
																
																
															</fieldset>
															
															
														
														<style>
														.alert {
														
														color:#fff !important;

                                                       padding: 4px;
                                                        padding-top: 4px  !important;
                                                        padding-right: 4px  !important;
                                                        padding-bottom: 4px  !important;
                                                        padding-left: 4px  !important;
														}


														</style>		
				<p>&nbsp;</p>
              
													<!--	<p class="text-center"><a href="javascript:void(0);" class="btn btn-primary alert" disabled> <?php echo ObtenEtiqueta(914); ?>    </a></p>
													-->
                                                         <p class="text-center">   <input type='button'  value='&nbsp;&nbsp;&nbsp;&nbsp; <?php echo ObtenEtiqueta(914); ?>&nbsp;&nbsp;&nbsp;&nbsp;' name="bot" id="bot" class="btn btn-primary btn-xs alert" onclick='validaInformacion(this.form);' disabled/>	</p>
                                                           
                                                            <!--<button type="submit" id="btn_aceptar" name="aceptar" onClick="javascript:document.datos.submit();" class="btn btn-primary alert" onClick="validar();">
															Validate Form
														</button>-->
														
														</form>
						
											
												  </div>


												  <div class="col-md-4">

						
												  </div>



									  </div>
							</div><!--- end content-->
  </div>
  <!-- END MAIN PANEL -->
  <!-- ==========================CONTENT ENDS HERE ========================== -->



    <!-- <p>Content here. <a class="alert" href=#>Alert!</a></p>-->

    <!-- JS dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="bootstrap.min.js"></script>

    



<!-- PAGE RELATED PLUGIN(S) -->
		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>

		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script>
		    if (!window.jQuery) {
		        document.write('<script src="js/libs/jquery-2.0.2.min.js"><\/script>');
		    }
		</script>

		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
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
		<script src="js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
		<script src="js/plugin/fuelux/wizard/wizard.js"></script>

<script type="text/javascript">






    // DO NOT REMOVE : GLOBAL FUNCTIONS!

    $(document).ready(function () {
		
        pageSetUp();

    });

		</script>




<!-- bootbox code -->
    <script src="bootbox.min.js"></script>


        <script>
            $(document).ready(function () {

			
			$('#ds_firts_name').change(function () {
					validaInformacion();
			});
			
			 $('#ds_last_name').change(function () {
					validaInformacion();
			});
			 $('#ds_email').change(function () {
					validaInformacion();
			});
			
			
			
			
			
            });

        </script>











        <script>


            
            function validaInformacion(forma) 
            {

                var ds_firts_name = document.getElementById('ds_firts_name').value;
                var ds_last_name = document.getElementById('ds_last_name').value;
                var ds_email = document.getElementById('ds_email').value;
				
				if($('#fg_rm').is(':checked')) {  
														var fg_aceptar=1;  
													} else {  
														var fg_aceptar=0;  
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
                } else if (ds_email.length == '') {
                    document.getElementById('fg_rm').checked = false;
					$('#bot').attr('disabled', true);//se desabilita
                    document.getElementById('ds_email').focus();
                    return;
                } else if (ds_email.length > 0) {



                                        expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                                        if (!expr.test(ds_email)) {
                                            document.getElementById('fg_rm').checked = false;
                                            //alert('Ingrese un email valido');
                                            var valor = 1;

                                        } else {
                                            var valor = 2;
                                        }

										//hasta que todos los campos sean llenados podremos enviar el form
                                        if ((fg_aceptar==1) &&(ds_firts_name.length > 0) && (ds_last_name.length > 0) && (valor != 1)) {

                                            $('#bot').attr('disabled', false);//se desabilita
                                        } else {

                                            $('#bot').attr('disabled', true);//se habilita

                                        }

                }


                //desacivamos el submit del boton del formulario
                $('input[type="submit"]').prop('disabled', false);

               // document.getElementById('asignar').click();



                //forma.submit();
               
            }

        </script>







    <script>
        $(document).on("click", ".alert", function (e) {

		
					var ds_firts_name = document.getElementById('ds_firts_name').value;
					var ds_last_name = document.getElementById('ds_last_name').value;
					var ds_email = document.getElementById('ds_email').value;
					var fg_aceptar = document.getElementById('fg_rm').checked;
					
					
					
				
					
					
					
					
					
					
						if($('#fg_rm').is(':checked')) {  
														var fg_aceptar=1;  
													} else {  
														var fg_aceptar=0;  
						} 
						
					
						$.ajax({
							type: 'POST',
							url: 'send_resgistration.php',
							data: 'ds_firts_name=' + ds_firts_name +
								  '&ds_last_name=' + ds_last_name +
								  '&fg_aceptar=' + fg_aceptar +
								  '&ds_email=' + ds_email,


							async: false,
							success: function (html) {
								$('#envio_contacto').html(html);
							
								
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
		
		
		
		
	
		
						if ((fg_aceptar == 1) && (ds_email.length > 0) && (ds_firts_name.length > 0) && (ds_last_name.length > 0)) {
					//aparee el modal ara redireccionar al sitio de email
					//	document.getElementById('asignar').click();
					}
		




		
		
        });

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


?>




</body>
</html>