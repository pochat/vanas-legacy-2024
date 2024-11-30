<?php 

# Libreria de funciones
require ('../AD3M2SRC4/lib/general.inc.php');

# clases.
require(SP_HOME.'/fame/class/EnumTipoRegistro.php');

# Variable initialization to avoid errors
$ya_no_tiene_licencias=NULL;
$si_tiene_acceso=NULL;

# Presenta pagina de Login
$page_title = "FAME Higher Education";
$ds_cve=$_GET['r']??NULL;
$confirma_autorizacion=$_GET['a']??NULL;

#Recuperamos la llave que permitira el acceso al sistema y es la que se guardo ala BD.
$no_codigo_confirmacion = substr("$ds_cve", -30, 30);

$Query="SELECT no_registro,fg_tipo_registro,fl_envio_correo,fl_invitado_por_instituto,fg_desbloquear_curso,fg_feed,fe_expiracion FROM k_envio_email_reg_selfp WHERE no_registro='$no_codigo_confirmacion' ";
$row=RecuperaValor($Query);
$no_codigo_valido_db=$row[0]??NULL;
$fg_tipo_registro=$row[1]??NULL;
$fl_envio_correo=$row[2]??NULL;
$fl_instituto=$row[3]??NULL;
$fg_desbloquear_curso=$row[4]??NULL;
$fg_feed=$row['fg_feed']??NULL;
$fe_expiracion_liga=$row['fe_expiracion']??NULL;

if(empty($fg_desbloquear_curso))
$fg_desbloquear_curso=0;

#vERIFICAMOS LICENCIA DISPONIBLES.
$Que="SELECT fg_tiene_plan,fg_parent_authorization FROM c_instituto WHERE fl_instituto=$fl_instituto ";
$r=RecuperaValor($Que);
$fg_tiene_plan=$r['fg_tiene_plan']??NULL;
$fg_parent_authorization=$r['fg_parent_authorization']??NULL;

#Obtenemos las numero de licencias
if($fg_tiene_plan==1){
	$Querys="SELECT no_licencias_disponibles FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
	$ri=RecuperaValor($Querys);
	$no_licencias_disponibles=$ri['no_licencias_disponibles'];

}

#Se recupera nombre del teaher/alumno:
$Query="SELECT ds_first_name,ds_last_name,ds_email,fg_confirmado,fg_tipo_registro FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_correo ";
$row=RecuperaValor($Query);
$ds_first_name= $row[0]??NULL;
$ds_last_name= $row[1]??NULL;
$ds_email=$row[2]??NULL;
$fg_ya_esta_confirmado=$row[3]??NULL;
$fg_tipo_registro=$row[4]??NULL;

#Para confirmar la autorizacion del tutor.
if($confirma_autorizacion){

  function genera_documento_sp($fl_usuario,$opc,$fl_template){
	
	  # Recupera datos del template del documento
	  switch($opc){
		case 1: $campo = "ds_encabezado"; break;
		case 2: $campo = "ds_cuerpo"; break;
		case 3: $campo = "ds_pie"; break;
		case 4: $campo = "nb_template"; break;
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
		  
	  # Recupera datos usuario
	  $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno,ds_login, fg_genero, ds_email, ".ConsultaFechaBD('fe_nacimiento', FMT_FECHA)." fe_nacimiento, fl_usu_invita,ds_alias ";
	  $Query .= "FROM c_usuario WHERE fl_usuario=$fl_usuario ";
	  $row = RecuperaValor($Query);
	  $ds_fname = str_texto($row[0]);
	  $ds_lname = str_texto($row[1]);

      $url=ObtenConfiguracion(116);

	  $cadena = str_replace("#fame_fname#", $ds_fname, $cadena);                        # Student first name 
      $cadena = str_replace("#fame_mname#", $ds_mname, $cadena);                        # Student middle name 
      $cadena = str_replace("#fame_lname#", $ds_lname, $cadena);                        # Student last name
      $cadena = str_replace("#fame_link_login#", $ds_lname, $cadena);

	  return ($cadena);

	}

    #Recupermaos de quien pertenece esa llave de autorizacion
    $Query="SELECT ds_fname,ds_lname,ds_email_alumno,fl_responsable_alumno,fl_usuario,no_codigo_autorizacion,nb_parentesco 
            FROM k_responsable_alumno A JOIN c_parentesco B ON B.cl_parentesco=A.cl_parentesco  
            WHERE no_codigo_autorizacion='$no_codigo_confirmacion' "; 
    $row=RecuperaValor($Query);
    $fname_tutor=str_texto($row['ds_fname']);
    $lname_tutor=str_texto($row['ds_lname']);
    $ds_email_alumno_confirmar=str_texto($row['ds_email_alumno']);
    $fl_responsable_alumno=$row['fl_responsable_alumno'];
	$fl_usuario_confirmar=$row['fl_usuario'];
    $no_codigo_autorizacion_actual=str_texto($row['no_codigo_autorizacion']);
    $nb_parentesco=str_texto($row['nb_parentesco']);

    #Le mandamos un email estudiante diciendo que su ya puede accesar a FAMME.
    $ds_encabezado = genera_documento_sp($fl_usuario_confirmar, 1,144);
    $ds_cuerpo = genera_documento_sp($fl_usuario_confirmar, 2,144);
    $ds_pie = genera_documento_sp($fl_usuario_confirmar,3,144);
    $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;

    #Remplaxzamos texto para saber quien autorizo.
    $ds_contenido = str_replace("#ds_fname_r#", $fname_tutor, $ds_contenido); # first name a quein se le envia el correo
    $ds_contenido = str_replace("#ds_lname_r#", $lname_tutor, $ds_contenido);  #bont link redireccion 
    $ds_contenido = str_replace("#fame_parent_relationship#", $nb_parentesco, $ds_contenido);  #bont link redireccion

    $row=RecuperaValor("SELECT nb_template FROM k_template_doc WHERE fl_template=144 ");
    $nb_template=str_texto($row[0]);
    
    $message  = $ds_contenido;
    $message = utf8_decode(str_ascii(str_uso_normal($message)));
    $bcc=ObtenConfiguracion(107);
    $nb_quien_envia_email=ObtenEtiqueta(949);#Vamcouver School nombre de quien envia el mensaje
    $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);
    $ds_titulo=$nb_template;#etiqueta de asunto del mensjae para el envio
    $ds_email_destinatario=$ds_email_alumno_confirmar;
    $mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);

    if($mail){

        EjecutaQuery("UPDATE c_usuario SET fg_activo='1' WHERE fl_usuario=$fl_usuario_confirmar ");
        EjecutaQuery("UPDATE k_responsable_alumno SET fg_autorizado='1' WHERE fl_responsable_alumno=$fl_responsable_alumno ");
        
        $si_tiene_acceso=1;
        $presenta_autorizacion=1;
    }

}

if(!empty($no_codigo_valido_db)){
            #solo si coincide la info de la bd con la del link. del correo
            if($no_codigo_confirmacion == $no_codigo_valido_db){
                
                if($fg_ya_esta_confirmado==0){

						#Entonces si tiene plan verificamos sus licencias.
						if($fg_tiene_plan==1){
							
                                if(($fg_tipo_registro=='T')||($fg_tipo_registro=='A')){
                                    $si_tiene_acceso=1;
                                }else{

								        if($no_licencias_disponibles > 0){
									        $si_tiene_acceso=1;
								        }else{
								            $ya_no_tiene_licencias=1;
								        }
                                }
							 
						}else{
							$si_tiene_acceso=1;
						}
                }
            }
}

#Ultima condicion verificamos si tiene fecha de expiracion.
if($fe_expiracion_liga){
	
	#Recuperamos la fecha actual
	#Obtenemos fecha actual :
    $Query = "Select CURDATE() ";
    $row = RecuperaValor($Query);
    $fe_actual = str_texto($row[0]);
    $fe_actual=strtotime('+0 day',strtotime($fe_actual));
    $fe_actual= date('Y-m-d',$fe_actual);
	
	if($fe_expiracion_liga>=$fe_actual){
		$si_tiene_acceso=1;
		
	}else{
		$si_tiene_acceso=0;
		}
}

?>

<!DOCTYPE html>
<html lang="en-us">
	<head>
		<meta charset="utf-8">
		<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->

		<title><?php echo $page_title ?> </title>
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Use the correct meta names below for your web application
			 Ref: http://davidbcalhoun.com/2010/viewport-metatag 
			 
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">-->
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<!-- Basic Styles -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="font-awesome-4.6.3/font-awesome-4.6.3/css/font-awesome.min.css">

		<!-- SmartAdmin Styles : Please note (smartadmin-production.css) was created using LESS variables -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production.css">
		<link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-skins.css">

		<!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
		<link rel="stylesheet" type="text/css" media="screen" href="css/demo.css">

		<!-- FAVICONS -->
		<link rel="shortcut icon" href="https://campus.vanas.ca/fame/img/fame.ico" type="image/x-icon">
		<link rel="icon" href="https://campus.vanas.ca/fame/img/fame.ico" type="image/x-icon">

		<!-- GOOGLE FONT -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
       
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>

        <!-- estilos para e calendario-->
        <script src="js/datepicker/js/jquery.min.js"></script>
        <link rel="stylesheet" href="js/datepicker/css/bootstrap-datepicker.min.css" />
        <script src="js/datepicker/js/bootstrap-datepicker.min.js"></script>

        <!--end estilos para el calendario -->
	</head>
	<body class="" style="background:#f3f3f3; height:120%;">
		<!-- possible classes: minified, fixed-ribbon, fixed-header, fixed-width-->

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

				   .btn-primary {

                         background-color: #0092cd !important;
				    }

                                  .bootstrapWizard li.active .step, .bootstrapWizard li.active.complete .step {
    background:#069FDC !important;

				    }

				</style>
				 <div class="row">
					  <div class="col-md-12 bgimage">
							<div class="bgimage-inside"></div>
					  </div>
				 </div>

	<input type="hidden" id="aliass"  name="aliass" value="" />
	
<?php

if($si_tiene_acceso){//verificamos si tiene acceso

    Forma_CampoOculto('fl_envio_correo',$fl_envio_correo);

    #verificamos el tipo de registro  Admin /Teacher
    if($fg_tipo_registro==EnumTipoRegistro::Administrador){

?>

	    <div class="row">
					<div class="col-md-12 text-center">
					<br>
					<h1 id="titulo1" style="font-size:35px;color:#625E5E;margin: 1px 0;"><?php echo ObtenEtiqueta(926) ?></h1>
                    <h1 class="hidden" id="titulo2" style="font-size:35px;color:#625E5E;margin: 1px 0;"><?php echo ObtenEtiqueta(932) ?></h1>
                    <h1 class="hidden" id="titulo3" style="font-size:35px;color:#625E5E;margin: 1px 0;"><?php echo ObtenEtiqueta(957) ?></h1>

<br>
					</div>

		</div>	

		<!-- MAIN PANEL -->
		<div id="main" role="main">

			<!-- MAIN CONTENT -->
			<div id="content">
					<!-- row -->
					<div class="row">
				
						<div class="col-md-1">
							&nbsp;
						</div>
				
						<div class="col-md-10">

                                                        <div class="row">

																<div class="col-md-10">
                                                                    <br />
																<ul class="bootstrapWizard form-wizard" >
																	<li class="active" data-target="#step1" id="tabss1" >
																		<a href="#tab1" data-toggle="tab" class="not-active"> <span class="step">1</span> <span class="title" ><?php echo ObtenEtiqueta(923);  ?></span> </a>
																	</li>
																	<li data-target="#step2" id="tabss2" >
																		<a href="#tab2" data-toggle="tab" class="not-active"> <span class="step">2</span> <span class="title"><?php echo ObtenEtiqueta(924);  ?></span> </a>
																	</li>
																	<li data-target="#step3" class="hidden" id="tabss3">
																		<a href="#tab3" data-toggle="tab" class="not-active"> <span class="step">3</span> <span class="title"><?php echo ObtenEtiqueta(925);  ?></span> </a>
																	</li>
																	<li data-target="#step4" class="hidden" id="tabss4">
																		<a href="#tab4" data-toggle="tab" class="not-active"> <span class="step">4</span> <span class="title hidden">Save Form</span> </a>
																	</li>
                                                                    
																	<li data-target="#step5" id="tabss5" >
																		<a href="#tab5" data-toggle="tab" class="not-active" id="quitarlink"> <span class="step">3</span> <span class="title"><?php echo ObtenEtiqueta(925);  ?></span> </a>
																	</li>

																</ul>
                                                                    <br />
                                                                    <br />
                                                                    <br />
                                                                    <br />
																</div>

															</div>

							<div class="row">
								<!-- NEW WIDGET START -->
								<article class="col-md-10">

									<!-- Widget ID (each widget will need unique ID)-->
									<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false" data-widget-deletebutton="false">
										<!--
										<header>&nbsp;
										</header>-->
						
										<style>
										.jarviswidget > div {
											border-right-color: #FFF !important;
											border-bottom-color: #FFF !important;
											border-left-color: #FFF !important;
										}
										h3 {
                                        margin: 3px 0 !important;
                                            margin-top: 3px !important;
                                            margin-right: 0px !important;
                                            margin-bottom: 3px !important;
                                            margin-left: 0px !important;
                                        }
                                        </style>
						
										<!-- widget div-->
										<div>

											<!-- widget content -->
											<div class="widget-body">
												<div class="row">
													<form id="wizard-1" novalidate="novalidate">
														<div id="bootstrap-wizard-1" class="col-sm-12">
															<div class="form-bootstrapWizard">
															<style>
															.bootstrapWizard li.complete .step {
															background: #4fbc0d !important;
															
															}
															
															 .bootstrapWizard li {

																width: 33% !important;
															}
															    .bootstrapWizard li .title {
															        font-size: 16px !important;
															    }
															
															</style>

                                                                <!---clase que nopermite seleccionar los numeros---->
                                                                <style>
                                                                        .not-active {
                                                                                   pointer-events: none;
                                                                                   cursor: default;
                                                                                }
                                                                    </style>

																<!--<div class="clearfix"style="background:#6e6f71;"></div>-->
															</div>
															<div class="tab-content">
																<div class="tab-pane active" id="tab1">
                                                                    <h3 class="text-center" style="color:#0092dc; font-size:24px;" ><?php echo ObtenEtiqueta(927) ?></h3>
																	<div class="row">
																		<div class="col-sm-3">
																		</div>
																		<div class="col-sm-6">
                                                                            <br /><br />
                                                                                <table width="100%">
                                                                                        <tr>
                                                                                            <th width="15%">

                                                                                            </th>
                                                                                            <th width="85%" >



																			                   <!--<legend>About You:</legend>-->
																				                <div class="form-check">
																					                <label class="form-check-label" style="font-size:16px;color:#6B6969;">
																					                <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios1" value="1"  >
																					                <?php echo ObtenEtiqueta(930) ?>
																					                </label>
																				                </div>
																				                <div class="form-check">
																					                <label class="form-check-label" style="font-size:16px;color:#AEAAAA;">
																					                <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios2" value="2" disabled/>
																					                <?php echo ObtenEtiqueta(931) ?>
																					                </label>
																				                </div>
                                                                                                <br />
                                                                                                <br />
                                                                                                <br />
																				                <!--<legend>END About You:</legend>-->
                                                                                            </th>
                                                                                           
                                                                                        </tr>
                                                                                    </table>

																		</div>
																		<div class="col-sm-3">
																		</div>
																	</div>
																</div>
															   <style>
															   .has-success .control-label, .has-success .radio, .has-success .checkbox, .has-success .radio-inline, .has-success .checkbox-inline {
																color: #747774 !important;
																}
                                                               /*tama–o del los input*/
															       .form-control {
                                                                       font-size: 14px !important;
															       }

															   </style>
                <form data-toggle="validator" role="form">
																<div class="tab-pane" id="tab2">
																	<h3 class="text-center" style="color:#0092dc; font-size:24px;"><?php echo ObtenEtiqueta(928) ?></h3>
						                                            <br />
																	<div class="row">
																	    <div class="col-sm-3">
																		</div>
																		<div class="col-sm-6">
                                                                            <div class="form-group has-feedback">
                                                                                <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(933)  ?>:</label>
                                                                                <div class="input-group">
                                                                                      <span class="input-group-addon"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span>
                                                                                      <input type="text"  maxlength="100" class="form-control" id="ds_name_school" name="ds_name_school"  required> 
                                                                                </div>
                                                                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>

                                                                                    <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                            </div>
                                                                            <style>
                                                                                .select2-container .select2-choice {

                                                                                    border: 0px solid #468847 !important;
                                                                                 
                                                                            }
                                                                            </style>

                                                                        <div class="form-group" >
                                                                        <?php
                                                                                $Query = "SELECT ds_pais, fl_pais FROM c_pais WHERE 1=1 and fg_activo='1' ";
                                                                                Forma_CampoSelectBDSP(ObtenEtiqueta(287),false, 'fl_country', $Query, $fl_country, '',true,'','left','col col-sm-12','col col-sm-12');
                                                                        ?>																		
                                                                        </div>
                                                              
																		<div class="form-group hidden" id="presenta_estado" >
																		<?php
																				$Query = "SELECT ds_provincia,fl_provincia FROM k_provincias WHERE fl_pais=38 ";
																				Forma_CampoSelectBDSP1(ObtenEtiqueta(1578),false, 'fl_estado', $Query, $fl_estado, '',true,'','left','col col-sm-12','col col-sm-12');
																		?>		
																		</div>
<script>

 $(document).ready(function () {

                  //campos 2da tab          
				
                  $('#fl_country').change(function () {
                     var fl_country=document.getElementById('fl_country').value;
					 
						if(fl_country==38){
						 $('#presenta_estado').removeClass('hidden');
						
						}else{
						
						 $('#presenta_estado').addClass('hidden');
						}
              });
});			  

</script>
<?php

?>
																		</div>
																		<div class="col-sm-3">
																		</div>
																	</div>
																</div>
																<div class="tab-pane" id="tab3">
																	<h3 class="text-center" style="color:#0092dc;font-size:22px;"><?php echo ObtenEtiqueta(935) ?></h3>
																	<br />
																	<div class="row">
																	    <!--<div class="col-sm-1">
																		</div>-->
																		<div class="col-sm-12">
																				<div class="row">
																					<div class="col-md-4">
																							<div class="form-group has-feedback "  id="div_alias">
																								<label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;">Username<!--<?php echo ObtenEtiqueta(910);?>-->:</label>
																								<div class="input-group">
																										<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
																										<input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="alias" name="alias"  onkeypress="return NoSpace(event);" onchange="ChangeAlias();HabilitaBotonTabs3();"  required/>
																								</div>
																								<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																								<span for="err_alias" class="help-block hidden" id="err_alias" >Username already exists</span>
																							</div>
																					</div>
																					<div class="col-sm-4">
                                                                                         <div class="form-group has-feedback">
                                                                                             <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(936)  ?>:</label>
                                                                                                <div class="input-group">
                                                                                                      <span class="input-group-addon"><i class="fa fa-unlock-alt" aria-hidden="true"></i></span>
                                                                                                      <input type="password" pattern="" maxlength="100" class="form-control" id="ds_pass1" name="ds_pass1" placeholder="<?php echo ObtenEtiqueta(958); ?>" required>
                                                                                                </div>
                                                                                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                                        </div>



<?php

?>
																					</div>
																					<div class="col-sm-4">
																					            <div class="form-group has-feedback">
                                                                                                     <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(937)  ?>:</label>
                                                                                                        <div class="input-group">
                                                                                                              <span class="input-group-addon"><i class="fa fa-unlock-alt" aria-hidden="true"></i></span>
                                                                                                              <input type="password" pattern="" maxlength="100" class="form-control" id="ds_pass2" name="ds_pass2"  required>
                                                                                                        </div>
                                                                                                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                        <div id="error_pass" class="help-block with-errors hidden"></div>
                                                                                                </div>
                                                                                    
                                                                                    
<?php
        // Forma_CampoTextoSP(ObtenEtiqueta(937), False, 'ds_pass2', $ds_pass2, 20, 0,'',True,'',True,'','',  'smart-form form-group','left','col col-sm-12', 'col col-sm-12'); 
        // Forma_Espacio();
?>
																					</div>
																				</div>
																				
																				<div class="row">
																				    <div class="col-sm-6">
                                                                                         <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(938)  ?>:</label>
                                                                                        <div class="form-group has-feedback" id="formul" >
                                                                                               
                                                                                                <div class="input-group">
                                                                                                       
                                                                                                        <span class="input-group-addon" id="codigo_pais" name="codigo_pais">&nbsp;</span>
                                                                                                     
                                                                                                         <?php
                                                                                                                                                    $Query = "SELECT CONCAT(ds_pais,' - ',cl_iso2), fl_pais FROM c_pais WHERE 1=1 and fg_activo='1' ";
                                                                                                                                                    Forma_CampoSelectBDSP3('', False, 'cl_iso_pais', $Query, $cl_iso_pais, '', true,'','left','col col-sm-12','col col-sm-12');
                                                                                                         ?>
                                                                                                </div>
                                                                                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                        </div>
																					</div>

                                                                                                         <!------=========Valida que no acepte espacios en el Nombre,segundo nombre, apellido ========--->
																										 <script>

																											 function validarn(e) { // 1
																												 tecla = (document.all) ? e.keyCode : e.which; // 2
																												 if (tecla == 8) return true; // 3
																												 if (tecla == 32) return false;
																												 if (tecla == 9) return true; // 3
																												 if (tecla == 11) return true; // 3
																												 patron = /[0-9-zÃ±Ã‘'Ã¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ Ã¨Ã¬Ã²Ã¹Ã€ÃˆÃŒÃ’Ã™Ã¢ÃªÃ®Ã´Ã»Ã‚ÃŠÃŽÃ”Ã›Ã‘Ã±Ã¤Ã«Ã¯Ã¶Ã¼Ã„Ã‹ÃÃ–Ãœ\s\t]/; // 4

																												 te = String.fromCharCode(tecla); // 5
																												 return patron.test(te); // 6
																											 }

																										</script>
																										<!------=============================------->  

               




																					<div class="col-sm-6"><!--Presentara el codigo de pais--->
																							<div class="row">
																								<div class="col-sm-6">



                                                                                                   


                                                                                                    <div id="ocultar"  style="margin-top:4px;">
                                                                                                         
																								    </div>
																										
																										    <div class="form-group has-feedback" >
                                                                                                                    <label for="inputTwitter" class="control-label" style="font-size:16px;">&nbsp;</label>
                                                                                                                    <div class="input-group">
                                                                                                                            <span class="input-group-addon"><i class="fa fa-phone-square" aria-hidden="true"></i>  </span> 
                                                                                                                            <input type="text"  maxlength="8" size="8" class="form-control" id="ds_codigo_telefono"    name="ds_codigo_telefono"   data-inputmask="'mask': '(999)'" placeholder="<?php echo ObtenEtiqueta(952); ?>" style="padding-right: 1px;" onkeypress="return validarn(event)"  required/>
                                                                                                                    </div>
                                                                                                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                
                                                                                                            </div>
																								</div>
																								

                                                                                                


																								<div class="col-sm-6">
																									  
																								
																									 <style>
																									     .has-feedback .form-control {
																									         padding-right: 1px !important;
																									     }
																									 </style>
				

 



																											
                                                                                                                            <div class="form-group has-feedback" style="margin-top: 0px;">
                                                                                                                                <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;">&nbsp;</label>
                                                                                                                                <div class="input-group">
                                                                                                                                        <span class="input-group-addon"><i class="fa fa-volume-control-phone" aria-hidden="true"></i></span>
                                                                                                                                        <input type="text" pattern="&" maxlength="20" size="20" class="form-control" id="ds_numero_telefono" name="ds_numero_telefono"    placeholder ="<?php echo ObtenEtiqueta(953); ?>" onkeypress="return validarn(event)" onkeyup='HabilitaBotonTabs3();'  required/>
                                                                                                                                </div>
                                                                                                                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                                                <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                                                                            </div>

																										<script>
																										function HabilitaBotonTabs3(){
																										
																												var no_telefono=document.getElementById('ds_numero_telefono').value;
																												var ds_alias=document.getElementById('aliass').value;
																												
																												if(ds_alias){
																													
																												}else{
																													
																												   var ds_alias="";
																												}		
																												
																												
																													if(no_telefono.length==''){
																												
																														 //alert('entro qaui numero tele');
																														$('#tabs4').attr('disabled', true);//se desabilita
																															//document.getElementById('ds_numero_telefono').focus();
																														return;

																													}else{
																														
																														
																														
																														if(ds_alias==1){
																															$('#tabs4').attr("disabled", false);
																														}else{
																															$('#tabs4').attr("disabled", true);
																															
																														}
																													}
																										
																										}
																										</script>
																												
																											
																											
																										 
																										
																								
																								</div>
																								
																							</div>
																					
																					
																						
																					</div>
																				
																				
																				</div>
																		
																		</div>
																		
																		<div class="col-sm-1">
																			
																		</div>
																	</div>
																	
																	
																	
																	
																</div>
																<div class="tab-pane" id="tab4">
																	
																	<h3 class="text-center" style="color:#0092dc;font-size:22px;"><?php echo ObtenEtiqueta(939) ?></h3>
																	
																	<br>
																	
																				<div class="row">

                                                                                    <div class="col-md-1">

                                                                                    </div>

                                                                                    <div class="col-md-10">

                                                                                        <div class="row">
																					        <div class="col-sm-6">

                                                                                                                                    <div class="form-group has-feedback" style="margin-top: 4px;" id="names">
                                                                                                                                        <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(909)  ?>:</label>
                                                                                                                                        <div class="input-group">
                                                                                                                                                <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                                                                                                                                <input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="fname" name="fname"  value="<?php echo $ds_first_name;?>"  required/>
                                                                                                                                        </div>
                                                                                                                                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                                                        <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                                                                                    </div>




																				
																					        </div>
																					        <div class="col-sm-6">
                                                                                                                                   <div class="form-group has-feedback" style="margin-top: 4px;" id="apellido">
                                                                                                                                        <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(910)  ?>:</label>
                                                                                                                                        <div class="input-group">
                                                                                                                                                <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                                                                                                                                <input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="lname" name="lname" value="<?php echo $ds_last_name;?>"   required/>
                                                                                                                                        </div>
                                                                                                                                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                                                        <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                                                                                    </div>




																				
																					        </div>
                                                                                          </div>
                                                                                        </div>
                                                                                    <div class="col-md-1">

                                                                                    </div>

																				</div>
																				
																				<div class="row">

                                                                                    

                                                                                        <div class="col-md-1">

                                                                                        </div>

                                                                                       <div class="col-md-10">
                                                                                           <div class="row">
																					            <div class="col-sm-6">
                                                                                        

                                                                                                                            <div class="form-group has-feedback" style="margin-top: 4px;" id="correo">
                                                                                                                                <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(940)  ?>:</label>
                                                                                                                                <div class="input-group">
                                                                                                                                        <span class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                                                                                                                                        <input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="email" name="email" onkeyup='HabilitarBotonEnvio();'  required/>
                                                                                                                                </div>
                                                                                                                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                                                <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                                                                            </div>


																				
																					                </div>
																					                <div class="col-sm-6">
																								                <style>
																								                .alert {
																								                color: #FFFEFC !important;
																								                padding: 8px !important;
																								                }
																								                    .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open .dropdown-toggle.btn-primary {
																								                    border-color: #fff0 !important;
                                                                                                    
                                                                                                                    }


																								                </style>
                                                                                                        <br />	
																					                       <p class="text-center"style="margin-top:13px;">
                                                                                                               <a href="javascript:void(0);"  class="btn btn-primary alert" name="bot" id="bot" style="border-radius: 10px;border-left-width: 0px;" disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(944); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </a>
                                                                                               
                                                                                                             <!--  <a href="javascript:void(0);" class="btn btn-primary alert" name="bot" id="bot" disabled/>Send invite by email </a>--></p>
																					<!-------MODAL---->
																					                                    <div name="send_correo"id="send_correo">

																					                                    </div>
																					
																										              <!------------------------------------------------------------------------>
																					
																					
																					
																					
																					
																					                </div>
                                                                                               </div>
                                                                                           </div>
                                                                                        <div class="col-md-1">

                                                                                        </div>
																				</div>
																				
																				
																				<div class="row" >
																						
																						<div class="col-sm-12 text-center" style="margin-top:-15px;">
																								<section class="col-sm-3">
																								
																								</section>

                                                                                                <style>
                                                                                                    label input.checkbox[type="checkbox"]:checked + span {
                                                                                                        font-weight: 500 !important;
                                                                                                    }
                                                                                                </style>

																								<section class="col-sm-6 note" >
																							
                                                                                                    <label class="checkbox-inline">
															                                          <input class="checkbox style-0" type="checkbox" name="fg_rm" id="fg_rm">
															                                          <span style="font-size:14px;color:#6B6969;"><?php echo ObtenEtiqueta(941) ?></span>
														                                        </label>
                                                                                                    
                                                                                               
																					
																				</div>
																					
																				</div>	
																				
																				
																				
																				
																	
																</div>
																
																
																<div class="tab-pane" id="tab5">
																	<br>
																	<h3 class="text-center" style="color:#0092dc;font-size: 48px;"> <?php echo ObtenEtiqueta(942) ?></h3>
                                                                    <p class="text-center" style="font-size:30px;color:#4fbc0d;" ><i class="fa fa-check fa-lg" ></i></p>
																	<br>
																	
																	<style>
																		.butp {
																								color: #FFFEFC !important;
																								padding: 3px !important;
																								}																
																	
																	</style>
																	
																	<br />

                                                                    <div class="row">
                                                                        <div class="col-md-12 ">

                                                                            <table width="100%">
                                                                                                                                                        
                                                                                        <thead><tr>
                                                                                            <th width="30%"></th>
                                                                                            <th width="40%" align="center">
                                                                                              <p class="text-center" >
                                                                                                
                                                                                                <a href="index.php#site/fame_feed.php"  class="btn btn-primary" style="border-radius: 10px;" name="finish" id="finish">
                                                                                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo ObtenEtiqueta(943);  ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                                                                                                </a>
                                                                                              </p>
                                                                                            </th>
                                                                                            <th width="30%"></th>
                                                                                            </tr>
                                                                                        </thead>

                                                                            </table>

                                                                        </div>

                                                                    </div>


                                                                    

																	          <!--  <p class="text-center"> <input type='button'  value='&nbsp;&nbsp;&nbsp;&nbsp; <?php echo ObtenEtiqueta(943); ?>&nbsp;&nbsp;&nbsp;&nbsp;' name="irdashboard" id="irdashboard" class="btn btn-primary btn-xs alert butp" onClick="window.location.href='../index.php'">	</p>-->
																	<br>
																	<br>
																</div>
																
																
																<style>
																.form-actions {
																
																}
																</style>
						
						
						
																								<style>
																								.butp {
																								color: #FFFEFC !important;
																								padding: 3px !important;
																								}
																								
																								
																								
																								</style>
						
						
																<div class="form-actions" style="background:#f3f3f3; margin-left: -15px;
																									margin-right: -15px;
																									margin-bottom: -15px;">
																

<!----------FINALIZA FORM-------->
                      </form>






<div class="row">
    <div vlass="col-md-12 text-center">

        <table width="100%">
            <thead>
                <tr>
                   <td width="30%"></td>
                    <td width="40%" align="center">

                         <a href="#tab2" data-toggle="tab" class="btn btn-primary " name="tabs2" id="tabs2" onclick="PasarTab2()" style="font-size: 14px;border-radius: 10px;" disabled/>     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(962); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>
                        <a href="#tab3" data-toggle="tab" class="btn btn-primary hidden" name="tabs3" id="tabs3" onclick="PasarTab3()" style="font-size: 14px;border-radius: 10px;" disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo ObtenEtiqueta(962); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>
                        <a href="#tab4" data-toggle="tab" class="btn btn-primary hidden" name="tabs4" id="tabs4" onclick="PasarTab4()" style="font-size: 14px;border-radius: 10px;" disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(962); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>
                        <a href="#tab5" data-toggle="tab" class="btn btn-primary hidden" name="tabs5" id="tabs5" onclick="PasarTab5()" style="font-size: 14px;border-radius: 10px;" disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(962); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a></p>
       

                    </td>
                    <td width="30%"></td>
                </tr>

            </thead>


        </table>

        
    </div>

</div>
                                                                    
																</div>
																
						
															</div>
														</div>
													</form>
												</div>
						
											</div>
											<!-- end widget content -->
																		
										</div>
										<!-- end widget div -->
										
																	
										
										
						
									</div>
									<!-- end widget -->
						
								</article>
								<!-- WIDGET END -->
								
																		
								
							</div>
						</div>
						
						
						<div class="col-md-1">
							&nbsp;
						</div>
				
					</div>
				
					<!-- end row -->
			

			</div>
			<!-- END MAIN CONTENT -->

		</div>
		<!-- END MAIN PANEL -->



<?php
    }
    if(($fg_tipo_registro==EnumTipoRegistro::Teacher)||($fg_tipo_registro==EnumTipoRegistro::Student)){
        
        
        if($fg_tipo_registro==EnumTipoRegistro::Teacher){
            $Titulo=ObtenEtiqueta(965);
			 Forma_CampoOculto('fe_nacimiento',"");
			
			}
        if($fg_tipo_registro==EnumTipoRegistro::Student){
            $Titulo=ObtenEtiqueta(981);
         
            
            echo"
            <style>
                .select2-container .select2-choice {
                   height:30px !important;
                }
            
            
            </style>
            ";
            
        }
        
        Forma_CampoOculto('fg_tipo_registro',$fg_tipo_registro);
        
?>



                    <!--Presenta titulo ---->
                     <div class="row">
					            <div class="col-md-12 text-center">
					            <br>
					            <h1  style="font-size:35px;color:#625E5E;margin: 1px 0;margin-left:51px;"><?php echo $Titulo; ?></h1>
                               
								<br />

					            </div>

		             </div>	

                    <!-- MAIN PANEL -->
		            <div id="main" role="main">

                                 <!-- MAIN CONTENT -->
			                    <div id="content">
					                    <!-- row -->
					                    <div class="row">
				
						                    <div class="col-md-1">
							                    &nbsp;
						                    </div>
				
						                    <div class="col-md-10">
											
											
														<div class="row">
															<div class="col-md-10" >
																

																<ul class="bootstrapWizard form-wizard">
																	<li class="active" data-target="#step6" id="tabss6">
																		<a href="#tab6" data-toggle="tab" class="not-active"> <span class="step">1</span> <span class="title"><?php echo ObtenEtiqueta(966);  ?></span> </a>
																	</li>
																	<li data-target="#step7"  id="tabss7">
																		<a href="#tab7" data-toggle="tab" class="not-active"> <span class="step">2</span> <span class="title"><?php echo ObtenEtiqueta(967);  ?></span> </a>
																	</li>
																	<li data-target="#step8" id="tabss8" >
																		<a href="#tab8 data-toggle="tab" class="not-active" id="tabss9"> <span class="step">3</span> <span class="title"><?php echo ObtenEtiqueta(968);  ?></span> </a>
																	</li>
	
	
																</ul>
																<br />
                                                                    <br />
                                                                    <br />
                                                                    <br />
															</div>
														 </div>
											
											
											
											
											
											
											
											
											
											        <?php 
														if($fg_desbloquear_curso==1){
													    echo"<style>
														     .jarviswidget{
															    margin: -15px 0 30px ;
															 }
															 .jarviswidget>div{
															   padding: 0px 13px 0;
															 }
															 .form-actions{
															   padding: 6px 14px 15px;
															 }
															 </style>";
													
														}
													?>
											
											
											
                                                    <div class="row">
								                        <!-- NEW WIDGET START -->
								                        <article class="col-md-10">
                                                                    <!-- Widget ID (each widget will need unique ID)-->
									                                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false" data-widget-deletebutton="false">
										                                        <!--
										                                        <header>&nbsp;
										                                        </header>-->
                                                                                <style>
										                                        .jarviswidget > div {
											                                        border-right-color: #FFF !important;
											                                        border-bottom-color: #FFF !important;
											                                        border-left-color: #FFF !important;
										                                        }
																				
																				
										                                        </style>

                                                                                    <!-- widget div-->
										                                            <div>
                                                                                                <!-- widget content -->
											                                                    <div class="widget-body">
												                                                    <div class="row">
													                                                    <form id="wizard-1" novalidate="novalidate">
														                                                    <div id="bootstrap-wizard-1" class="col-sm-12">
															                                                    <div class="form-bootstrapWizard">
															
															                                                                    <style>
															                                                                    .bootstrapWizard li.complete .step {
															                                                                    background: #4fbc0d !important;
															
															                                                                    }
															
															                                                                     .bootstrapWizard li {

																                                                                    width: 33% !important;
															                                                                    }
															                                                                        .bootstrapWizard li .title {
															                                                                            font-size: 16px !important;
															                                                                        }
															
															                                                                    </style>

                                                                                                                        <!---clase que nopermite seleccionar los numeros---->
                                                                                                                        <style>
                                                                                                                            .not-active {
                                                                                                                                       pointer-events: none;
                                                                                                                                       cursor: default;
                                                                                                                                    }
																																	
																																	h3 {
																																			margin: 3px 0 !important;
																																				margin-top: 3px !important;
																																				margin-right: 0px !important;
																																				margin-bottom: 3px !important;
																																				margin-left: 0px !important;
																																			}
                                                                                                                        </style>

															                                                            
																														
																														
																														
																														
																                                                    
															                                                    </div>
                                                                                                                        <?php 
        
        $nombre_completo="<b>$ds_first_name  $ds_last_name</b>";
        
        if($fg_tipo_registro==EnumTipoRegistro::Teacher){
            
            $titulo=ObtenEtiqueta(969)." ".$nombre_completo."".ObtenEtiqueta(970);
            
        }
        
        if($fg_tipo_registro==EnumTipoRegistro::Student){
		    if($fg_desbloquear_curso)
		    $nombre_completo=$ds_email;
		
            $titulo=ObtenEtiqueta(969)." ".$nombre_completo."".ObtenEtiqueta(982);
        }
        
        
        
        
                                                                                                                        ?>
                                                                                                                <div class="tab-content" style="padding-top:10px;">
																                                                    <div class="tab-pane active" id="tab6">
                                                                                                                   
                                                                                                                                <h3 class="text-center" style="color:#6e6f71; font-size:22px;" ><?php echo $titulo?></h3>
						                                                                                                <br />
                                                                                                                        <p class="text-center" style="color:#6e6f71; font-size:16px;"><?php echo ObtenEtiqueta(971); ?></p>
                                                                                                                        
																	                                                            <div class="row">
						
																		                                                            <div class="col-sm-2">
																		                                                            </div>
																		                                                            <div class="col-sm-7"><br>
                                                                                                                                        <table width="100%">
                                                                                                                                            <tr>
                                                                                                                                                <th width="15%">

                                                                                                                                                </th>
                                                                                                                                                <th width="85%">
                                                                                                                                                    <!--<legend>About You:</legend>-->
																				                                                            <div class="form-check">
																					                                                            <label class="form-check-label" style="font-size:16px;color:#686666;">
																					                                                            <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios3" value="1"  >
																					                                                            <?php echo " ".str_uso_normal(ObtenEtiqueta(972))." ".str_uso_normal(ObtenEtiqueta(974))." "; ?>
																					                                                            </label>
																				                                                            </div>
																				                                                            <div class="form-check">
																					                                                            <label class="form-check-label" style="font-size:16px;color:#686666;">
																					                                                            <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios4" value="2" disabled/>
																					                                                            <?php echo " ".str_uso_normal(ObtenEtiqueta(973))." ".str_uso_normal(ObtenEtiqueta(975))." "; ?>
																					                                                            </label>
																				                                                            </div>
                                                                                                                                                </th>
                                                                                                                                               

                                                                                                                                            </tr>

                                                                                                                                        </table>


																			                                                               
																				                                                           
																		                                                            </div>
																		                                                            <div class="col-sm-3">
																		                                                            </div>
						
																	                                                            </div>

                                                                                                                     </div><!---end tab6--->

                                                                                                                             <style>
															                                                                   .has-success .control-label, .has-success .radio, .has-success .checkbox, .has-success .radio-inline, .has-success .checkbox-inline {
																                                                                color: #747774 !important;
																                                                                }
                                                                                                                               /*tama–o del los input*/
															                                                                       .form-control {
                                                                                                                                       font-size: 14px !important;
															                                                                       }


															                                                                   </style>



                                                                                                                    <form data-toggle="validator" role="form">




                                                                    												<div class="tab-pane" id="tab7">
                                                                                                                        <?php 
																															$su_email=ObtenEtiqueta(976)." ".$ds_email;
        
                                                                                                                        ?>
                                                                                                                           
																	                                                        <h3  class="text-center" style="color:#6e6f71;font-size:22px;" ><?php echo $titulo ?></h3>
                                                                                                                           
                                                                                                                        <p class="text-center" style="color:#6e6f71; font-size:16px;"><?php echo $su_email ?></p>
                                                                                                                        <br />
																	                                                        <div class="row">
																															
																															
																															
																															
																															
																	                                                           <!--<div class="col-md-1">
																															   </div>-->
																															   <div class="col-md-12">
                                                                                                                                   <?php 
                                                                                                                                    if($fg_tipo_registro==EnumTipoRegistro::Student){                                                                                                                          
                                                                                                                                     ?>
																																	 
																																	 
																																
																																	<?php if($fg_desbloquear_curso==1){ ?>
																																			<div class="row">
																																				<div class="col-md-4">
																																							<div class="form-group has-feedback" style="margin-top: 4px;" id="names">
																																								<label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(909);?>:</label>
																																								<div class="input-group">
																																										<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
																																										<input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="fname" name="fname" value="<?php echo $ds_first_name; ?>"  required/>
																																								</div>
																																								<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																																								
																																							</div>
																																				
																																				</div>
																																				<div class="col-md-4">
																																							<div class="form-group has-feedback" style="margin-top: 4px;" id="names">
																																								<label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(910);?>:</label>
																																								<div class="input-group">
																																										<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
																																										<input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="lname" name="lname" value="<?php echo $ds_last_name; ?>"  required/>
																																								</div>
																																								<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																																								
																																							</div>
																																				
																																				</div>
																																				
																																				<div class="col-md-4">
																																							<div class="form-group has-feedback " style="margin-top: 4px;" id="div_alias">
																																								<label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;">Username<!--<?php echo ObtenEtiqueta(910);?>-->:</label>
																																								<div class="input-group">
																																										<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
																																										<input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="alias" name="alias" onkeypress="return NoSpace(event);"  onchange ="ChangeAlias();"  required/>
																																								</div>
																																								<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																																								<span for="err_alias" class="help-block hidden" id="err_alias" >Username already exists</span>
																																							</div>
																																				
																																				
																																				</div>
																																				
																																				
																																			</div>

																																	<?php }else{
																																	       echo"<input type='hidden' id='fname' name='fname' value=''  />
																																		        <input type='hidden' id='lname' name='lname' value=''  />
																																		       ";
																																	      
																																	} ?>
																																	
																																		 
																																	 
																																	 
																																	 
																																	 
																																	 
																																	<?php if(empty($fg_desbloquear_curso)){ ?> 
																																	<div class="row">
																																		<div class="col-md-4">
																																		
																																					<div class="form-group has-feedback " style="margin-top: 4px;" id="div_alias">
																																						<label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;">Username<!--<?php echo ObtenEtiqueta(910);?>-->:</label>
																																						<div class="input-group">
																																								<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
																																								<input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="alias" name="alias" onkeypress="return NoSpace(event);"  onchange ="ChangeAlias();"  required/>
																																						</div>
																																						<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																																						<span for="err_alias" class="help-block hidden" id="err_alias" >Username already exists</span>
																																					</div>
																																		
																																		</div>
																																	
																																	</div> 
																																	<?php } ?>
                                                                                                                                   <div class="row">
                                                                                                                                       
                                                                                                                                       <div class="col-sm-4">

                                                                                                                                           <div class="form-group" >
                                                                                                                                           <?php
																																					$p_opc = array('--Select One--',ObtenEtiqueta(115), ObtenEtiqueta(116), ObtenEtiqueta(128));
																																					$p_val = array('0','M', 'F', 'N');
																																					 Forma_CampoSelectSP(ObtenEtiqueta(114), False, 'cl_sexo', $p_opc, $p_val, $cl_sexo, $cl_sexo_err);     
                                                                                                                                            ?>																		
                                                                                                                                            </div>





                                                                                                                                       </div>


                                                                                                                                       <div class="col-sm-4">

                                                                                                                                                         <div class="form-group  " id="nacimiento" style="margin-bottom: 3px;">
																																								<label for="inputTwitter" class="control-label" style="font-size:16px;color:#6C6B6B;"><?php echo ObtenEtiqueta(120)?>:</label>
																																									<div class="input-group">
																																									  <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
																																									  <input type="text" pattern="" maxlength="100" class="form-control"  style="z-index:110 !important;"  id="fe_nacimiento" name="fe_nacimiento" required>
																																									</div>
																																									<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																																									<!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
																																						 </div>
                                                                                                                                       </div>
																																	   
																																	   	<div class="col-sm-4">

																																				
                                                                                                                                                <!-----Presenta Modal de contrato tecaher y alumno------>

                                                                                                                                                <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal7" id="muestra_dialogo_menor_edad">
                                                                                                                                                    MJD boton para mostrar los campos,de de requerimeintos si el alumno es menor de edad. 
                                                                                                                                                </button>


                                                                                                                                                <div class="modal fade " id="myModal7" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                                                                                                                    <div class="modal-dialog modal-lg" role="document" style="
                                                                                                                                                                        ">
                                                                                                                                                    <div class="modal-content">
                                                                                                                                                        <div class="modal-header text-center">
																																						
                                                                                                                                                        <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                                                                                                                                                        <h4 class="modal-title text-center" style="font-size:23px;"><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(1648); ?></h4>
      
      
                                                                                                                                                        </div>
                                                                                                                                                        <div class="modal-body" style="padding: 0px;">

                                                                                                                                                            <div class="row">
                                                                                                                                                                <div class="col-md-2">

                                                                                                                                                                </div>

                                                                                                                                                                <div class="col-md-8">
																																								<br/>
                                                                                                                                                                    <p for="inputTwitter" class="control-label text-center" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(1649); ?></p>
                                                                                                                                                                    <p for="inputTwitter" class="control-label text-center" style="font-size:14px;color:#6B6969;"><?php echo ObtenEtiqueta(1650); ?></p>

                                                                                                                                                                    <p>&nbsp;</p>

                                                                                                                                                                     <div class="form-group has-feedback" style="margin-top: 4px;" id="fnameparentesco">
                                                                                                                                                                        <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(909)  ?>:</label>
                                                                                                                                                                        <div class="input-group">
                                                                                                                                                                                <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                                                                                                                                                                <input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="fname_parentesco" name="fname_parentesco"   required/>
                                                                                                                                                                        </div>
                                                                                                                                                                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                                                                                        <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                                                                                                                    </div>

                                                                                                                                                                    
                                                                                                                                                                     <div class="form-group has-feedback" style="margin-top: 4px;" id="lnameparentesco">
                                                                                                                                                                        <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(910)  ?>:</label>
                                                                                                                                                                        <div class="input-group">
                                                                                                                                                                                <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                                                                                                                                                                <input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="lname_parentesco" name="lname_parentesco"   required/>
                                                                                                                                                                        </div>
                                                                                                                                                                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                                                                                        <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                                                                                                                    </div>


                                                                                                                                                                     <div class="form-group" >
                                                                                                                                                                  <?php  
                                                                                                                                                                    $Query = "SELECT nb_parentesco,cl_parentesco FROM c_parentesco WHERE 1=1 ";
                                                                                                                                                                    Forma_CampoSelectBDSP(ObtenEtiqueta(1651),false, 'cl_parentesco', $Query, $cl_parentesco, '',true,'','left','col col-sm-12','col col-sm-12');
                                                                                                                                                                  ?>		
                                                                                                                                        
                                                  
                                                                                                                                                                  
                                                                                                                                                                         </div>
																																										 
																																										 
																																										 
																																										  <div class="form-group has-feedback" style="margin-top: 4px;" id="emailparentesco">
                                                                                                                                                                        <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;"><?php echo ObtenEtiqueta(1133)  ?>:</label>
                                                                                                                                                                        <div class="input-group">
                                                                                                                                                                                <span class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                                                                                                                                                                                <input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="email_parentesco" name="email_parentesco"   required/>
                                                                                                                                                                        </div>
                                                                                                                                                                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                                                                                                                                                        <!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
                                                                                                                                                                    </div>
																																										 
																																										 



                                                                                                                                                                </div>

                                                                                                                                                                <div class="col-md-2">

                                                                                                                                                                </div>

                                                                                                                                                            </div>

                                                                                                                                                                <!---Presentamos formas de campo para llenar, cuando son alumnos menores de edad --->
												 
												 
                                                                                                                                                                
                                                                                                                                                                <!-------------------------------->
                                                                                                                                                     
          
          

                                                                                                                                                        </div>
                                                                                                                                                        <div class="modal-footer text-center">
          
      
                                                                                                                                                            <a  class="btn btn-secondary" style="font-size:14px;border-radius: 10px; border-color: #2c699d;" data-dismiss="modal" id="cerrar_relacion" ><i class="fa fa-times-circle"></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Cancel &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                                                                                                                                            <a  class="btn btn-primary" style="font-size:14px;border-radius: 10px; border-color: #2c699d;" data-dismiss="modal" onclick="GuardaRelacion();" ><i class="fa fa-check-circle"></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Accept &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>


        

                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    </div>
                                                                                                                                                </div>


                                                                                                                                                <!-----END fin modal------------------------------------->









                                                                                                                                             <div class="form-group" >

																																			 

																																			   <?php
                                                                                                                                        
                                                                                                                                        $Query = "SELECT cl_clasificacion_grado,nb_clasificacion_grado  FROM  c_clasificacion_grado WHERE 1=1 ";
                                                                                                                                        Forma_SelectBDGrupo(ObtenEtiqueta(1640), False, 'cl_grado', $Query, $cl_grado, $cl_grado_err);  
                                                                                                                                        
                                                                                                                                        
                                                                                                                                               ?>																		
                                                                                                                                            </div>





                                                                                                                                        </div>
																																	   
																																	   
																																	   
																																	   
                                                                                                                                    </div>
																																	
																																	
																																	
																																	

																																   <div id="guardar_regist"></div>	

                                                                                                                                   <?php
                                                                                                                                    }
																																	
																																	
																																if($fg_tipo_registro==EnumTipoRegistro::Teacher){
	
                                                                                                                                   ?>

																																   <div class="row">
																																   
																																		<div class="col-md-4">
																																	
																																					<div class="form-group has-feedback " style="margin-top: 4px;" id="div_alias">
																																						<label for="inputTwitter" class="control-label" style="font-size:16px;color:#6B6969;">Username<!--<?php echo ObtenEtiqueta(910);?>-->:</label>
																																						<div class="input-group">
																																								<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
																																								<input type="text" pattern="&" maxlength="100" size="100" class="form-control" id="alias" name="alias" onkeypress="return NoSpace(event);" onchange ="ChangeAlias();"  required/>
																																						</div>
																																						<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																																						<span for="err_alias" class="help-block hidden" id="err_alias" >Username already exists</span>
																																					</div>
																																	
																																		</div>
																																   
																																   </div>
																																   
																																   
																																   

																																	<?php 
																																	}
																																	?>

																																	 <div class="row">
																																			<div class="col-sm-6">
																																						 <div class="form-group has-feedback">
																																								<label for="inputTwitter" class="control-label" style="font-size:16px;color:#6C6B6B;"><?php echo ObtenEtiqueta(977)?>:</label>
																																									<div class="input-group">
																																									  <span class="input-group-addon"><i class="fa fa-unlock-alt" aria-hidden="true">&nbsp;</i></span>
																																									  <input type="password" pattern="" maxlength="100" class="form-control" style="font-size:13px !important;" id="ds_pass3" name="ds_pass3" placeholder="<?php echo ObtenEtiqueta(958); ?>" required>
																																									</div>
																																									<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																																									<!--<div class="help-block with-errors">Hey look, this one has feedback icons!</div>-->
																																						 </div>
																																				
																																			</div>

																																						

																																			<div class="col-sm-6">
																						
																																					<div class="form-group has-feedback">
																																						 <label for="inputTwitter" class="control-label" style="font-size:16px;color:#6C6B6B;"><?php echo ObtenEtiqueta(978)?>:</label>
																																							<div class="input-group">
																																								  <span class="input-group-addon"><i class="fa fa-unlock-alt" aria-hidden="true">&nbsp;</i></span>
																																								  <input type="password" pattern="" maxlength="100" class="form-control" style="font-size:13px !important;" id="ds_pass4" name="ds_pass4"  required>
																																							</div>
																																							<span class="glyphicon form-control-feedback" aria-hidden="true"></span>
																																							<div id="error_pass" class="help-block with-errors hidden"></div>
																																					</div>

																																			</div>
																																	</div>
																																</div>


																	                                                       
																																<div class="col-md-1">
																															    </div>
																															</div>

                                                                                                                        	<div class="row" >
																																					<style>
																																						label input.checkbox[type="checkbox"]:checked + span {
																																							font-weight: 500 !important;
																																						}
																																					</style>
																						                                            
																																	    <div class="col-sm-12 text-center" style="margin-top:-30px;">
																																	
																																			<div class="row">
																																			
																																			<style>
																																			  a:hover, a:focus {
																																				/*color: #0092dc !important;*/
																																				text-decoration: none !important;
																																				
																																				}
																																			    .sinhover:hover {
																																			    color: #0092dc !important;
                                                                                                                                                }
																																			</style>
																																			
																																				<div class="col-md-2">
																																				</div>
																																				<div class="col-md-8">
																																				<br/>
																																				<section class="col-sm-12 note text-center" >
																						
                                                                                                                      
                                                                                                                                                <!-----confimation del teacher----->    	
																																				<label class="checkbox-inline">
																																				  <input class="checkbox style-0" type="checkbox" name="fg_terminar" id="fg_terminar">
																																	              <span  style="font-size:14px;color:#6C6B6B;margin-bottom:4px;margin-right:1px;"><?php echo ObtenEtiqueta(912);?></span><span style="font-size:14px;color:#0092dc;"><a class="sinhover" href="" data-toggle="modal" data-target="#myModal5">&nbsp;<?php echo ObtenEtiqueta(913); ?></a></span>
																																			    </label>

                                                                                                                                                                                   

																																			
																																			</section>
																																				</div>

																																				<div class="col-md-2">
																																				</div>


																																			</div>
																																	
																								                                            <section class="col-sm-3">
																																				
<!-----Presenta Modal de contrato tecaher y alumno------>

                                                                                                                                                <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal5">
                                                                                                                                                    MJD boton para mstrar el contrato se queda oculta como referencia nada mas de este boyton
                                                                                                                                                </button>


                                                                                                                                                <div class="modal fade " id="myModal5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
        $ds_encabezado_contrato = genera_ContratoFame($fl_instituto, 1,102,$fg_tipo_registro,$ds_first_name,$ds_last_name);
        $ds_cuerpo_contrato = genera_ContratoFame($fl_instituto, 2, 102,$fg_tipo_registro,$ds_first_name,$ds_last_name);
        $ds_pie_contrato = genera_ContratoFame($fl_instituto, 3,102,$fg_tipo_registro,$ds_first_name,$ds_last_name);
        
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


                                                                                                                                                <!-----END fin modal------------------------------------->




																																				</section>
																																			
																																			
																																			
																					
																						                                            </div>
																					
																				                                            </div>	






                                                                                                                    </div>


																													


                                                                                                                    <div class="tab-pane" id="tab8">
                                                                                                                            <br>
																	                                                       <h3 class="text-center" style="color:#0092dc;font-size: 48px;"> <?php echo ObtenEtiqueta(942) ?></h3><!--succes--->
                                                                                                                            <p class="text-center" style="font-size:30px;color:#4fbc0d;" ><i class="fa fa-check fa-lg" ></i></p>
                                                                                                                            <br />
                                                                                                                            <p class="text-center " id="text_succes" style="font-size:25px;color:#848981;" ><?php echo ObtenEtiqueta(979) ?></p>
                                                                                                                            <p class="text-center hidden" id="text_autorized" style="font-size:16px;color:#848981;" >  </p><!-----texto que parece cuando se registra un menor de edad.indica que tiene que revisar su email para confirmar su autorizacion y acceso.------>
                                                                                                                           <style>
																		                                                        .butp {
																																		color: #FFFEFC !important;
																																		padding: 3px !important;
																								                                      }																
																	
																	                                                        </style>
																	
																	                                                        <br />

											                                                                                <div class="row">
                                                                                                                                    <div class="col-md-12 text-center">

                                                                                                                                        <table width="100%">
                                                                                                                                                        
                                                                                                                                                    <thead><tr>
                                                                                                                                                        <th width="30%"></th>
                                                                                                                                                        <th width="40%" align="center"><p class="text-center" >
                                                                                                                                                        <a style="border-radius: 10px;" class="btn btn-primary" name="finish2" id="finish2"     onClick="window.location.href='index.php#site/fame_feed.php'" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo ObtenEtiqueta(980);  ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </a></p></th>
                                                                                                                                                        <a style="border-radius: 10px;" class="btn btn-primary hidden" name="btn_red_login" name="btn_red_login" id="btn_red_login"     onClick="window.location.href='<?php echo ObtenConfiguracion(116); ?>'" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Accept &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </a></p></th>
                                                                                                                                                        
                                                                                                                                                        
                                                                                                                                                        <th width="30%"></th>
                                                                                                                                                        </tr>
                                                                                                                                                    </thead>

                                                                                                                                        </table>

                                                                                                                                    </div>

                                                                                                                           </div>

                                                                                                                    </div>








                                                                                                                    


                                                                                                                    
																								                                <style>
																								                                .butp {
																								                                color: #FFFEFC !important;
																								                                padding: 3px !important;
																								                                }
																								
																								
																								
																								                                </style>
						
						
																                                                                    <div class="form-actions" style="background:#f3f3f3; margin-left: -15px;
																									                                                                    margin-right: -15px;
																									                                                                    margin-bottom: -15px;
																																										<?php if($fg_desbloquear_curso==1) 
																																										      echo"margin-top:5px; ";
																																										?>
																																									
																																										" >
																                                                                   

																                                                                    <!----------FINALIZA FORM-------->
																					                                                </form>

                                                                                                                    <!--<style>

                                                                                                                        a:hover, a:focus {
                                                                                                                            color: #fff !important;
                                                                                                                        }

                                                                                                                    </style>-->

                                                                                                                      <div class="row">
                                                                                                                            <div vlass="col-md-12 text-center">
                                                                                                                                <table width="100%">
                                                                                                                                    <thead>
                                                                                                                                        <tr>
                                                                                                                                            <td width="30%"></td>
                                                                                                                                            <td width="40%" align="center">

                                                                                                                                                <a href="#tab7" data-toggle="tab" class="btn btn-primary " name="tabs7" id="tabs7" onclick="PasarTab7()" style="font-size: 14px;border-radius: 10px; " disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(962); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </a>
                                                                                                                                                <a href="#tab8" data-toggle="tab" class="btn btn-primary hidden" name="tabs8" id="tabs8" onclick="PasarTab8()" style="font-size: 14px;border-radius: 10px;" disabled/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(962); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </a>  

                                                                                                                                                 <a href="#tab8" data-toggle="tab" class="btn btn-primary hidden" name="tab_autorizacion" id="tab_autorizacion" onclick="PasarTabAutorizacion()" style="font-size: 14px;border-radius: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ObtenEtiqueta(962); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </a>
																																				 <br><br>
                                                                                                                                            </td>
                                                                                                                                            <td width="30%"></td>
                                                                                                                                        </tr>

                                                                                                                                    </thead>


                                                                                                                                </table>
                                                                                                                            </div>

                                                                                                                         </div>








                                                                                                                </div><!---end tab contnt--->
                                                                                                            </div>
                                                                                                        </form>
                                                                                                    </div>


                                                                                                </div>
                                                                                                <!--end  widget div-->


                                                                                      </div>
                                                                                      <!--end widget div-->
                                                                                </div>
                                                                                <!-- Widget ID --->
                                                         </article>
                                                    </div>


                                            </div><!--end col-md-10-->

                                            <div class="col-md-1">
                                                &nbsp;
                                            </div>
                                        </div>
                                       <!--end row -->
                                </div>
                                <!-- MAIN CONTENT -->
                    </div><!-- MAIN PANEL -->









<?php
    }
    
    if($presenta_autorizacion){
    
    
?>

    <!-- MAIN PANEL -->
		<div id="main" role="main">
       <!-- MAIN CONTENT -->
			<div id="content">
			
					<!-- row -->
					<div class="row">
						<div class="col-md-1">&nbsp;</div>
				
						    <div class="col-md-10">


                                <!-- NEW WIDGET START -->
								<article class="col-md-10"><br><br>
											<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false" data-widget-deletebutton="false">
										                                        <!--
										                                        <header>&nbsp;
										                                        </header>-->
                                                                                <style>
										                                        .jarviswidget > div {
											                                        border-right-color: #FFF !important;
											                                        border-bottom-color: #FFF !important;
											                                        border-left-color: #FFF !important;
										                                        }
																				
																				
										                                        </style>	

                                                        <div class="col-md-12 text-center">
															<?php if($no_codigo_autorizacion_actual){?>
                                                            <br><br/>
					                                            <h1  style="font-size:35px;color:#625E5E;margin: 1px 0;"><?php echo ObtenEtiqueta(2072) ?></h1>
																
																
																
																
																
																<h3 class="text-center" style="color:#0092dc;font-size: 48px;"> Success!</h3>
																<p class="text-center" style="font-size:30px;color:#4fbc0d;"><i class="fa fa-check fa-lg"></i></p>
																<br><br>
															    <?php }else{?>
																
																		<br />
																		<br />
																		<h1  style="font-size: 40px; font-weight: bold;line-height:60px; color: #CC2626;"><?php echo ObtenEtiqueta(1519);?></h1>
																		<i class="fa fa-smile-o fa-6" aria-hidden="true" style="font-size:12em; color:#CC2626; " ></i>
																		

																		<h2  style="font-size: 31.5px;font-weight: bold;color:#CC2626;"><?php echo ObtenEtiqueta(1783);?> </h2>
														
																		<br />
																		<span style="font-size:20px;color: #10D3FF;"><a class="btn btn-pri" href="../index.php#site/home.php" style="background:#0FBAE1;color:#fff;text-decoration:none;"><i class="fa fa-share" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(963);?>  </a> </span>
																		<br>
																		<br>
																		<br>
																		<br>


															   <?php } ?>
																
																
																
                                                        </div>
														
											</div>			
                                 </article>
                           </div>
                    </div>
            </div>
        </div>

<?php

    }#end if presenta autorizacion.
    
    
?>














       
<?php

}else{

    # Redirecciona al home
    //header("Location: http://www.vanas.ca");
    
    #Presenta pag de error
    //$imgSeveridad = PATH_ADM_IMAGES."/".IMG_ERROR;
    $imgSeveridad = "/AD3M2SRC4/".IMG_ERROR;

	if($ya_no_tiene_licencias==1){
	
?>
			<!-- presenta que ya no tiene licencias disponibles--->

             <div class="row">
							<div class="col-sm-12 text-center">
                                <br />
                                <br />
                                <h1  style="font-size: 40px; font-weight: bold;line-height:60px; color: #CC2626;"><?php echo ObtenEtiqueta(1791);?></h1>
                                <i class="fa fa-smile-o fa-6" aria-hidden="true" style="font-size:12em; color:#CC2626; " ></i>
								

                                <!--<h2  style="font-size: 31.5px;font-weight: bold;color:#CC2626;"><?php echo ObtenEtiqueta(1783);?> </h2>-->
				
                                <br />
                                <span style="font-size:20px;color: #10D3FF;"><a class="gohome jm-font-size" href="../index.php#site/home.php" style="text-decoration:none;color: #0FBAE1;"> <?php echo ObtenEtiqueta(963);?>  </a> </span>
							</div>
				
			</div>
    
    <?php 
	 }else{
	?>
			<!---presenta que ya espiro su licencia--->
			<div class="row">
							<div class="col-sm-12 text-center">
                                <br />
                                <br />
                                <h1  style="font-size: 40px; font-weight: bold;line-height:60px; color: #CC2626;"><?php echo ObtenEtiqueta(1519);?></h1>
                                <i class="fa fa-smile-o fa-6" aria-hidden="true" style="font-size:12em; color:#CC2626; " ></i>
								

                                <h2  style="font-size: 31.5px;font-weight: bold;color:#CC2626;"><?php echo ObtenEtiqueta(1783);?> </h2>
				
                                <br />
                                <span style="font-size:20px;color: #10D3FF;"><a class="gohome jm-font-size" href="../index.php#site/home.php" style="text-decoration:none;color: #0FBAE1;"> <?php echo ObtenEtiqueta(963);?>  </a> </span>
							</div>
				
			</div>
	
	<?php 
	
	}
	
	?>
	



        <!-- MAIN PANEL -->
		<div id="Div1" role="main">





        </div>




 
 <?php
}
 ?>



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
       
       <!-- Node -->
       <script src='https://campus.vanas.ca:3000/socket.io/socket.io.js'></script>		
	   <script src="js/node.inc.js"></script>
		<!-- Demo purpose only -->
		<script src="js/demo.js"></script>

		<!-- MAIN APP JS FILE -->
		<script src="js/app.js"></script>

		<!-- PAGE RELATED PLUGIN(S) -->
		<script src="js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
		<script src="js/plugin/fuelux/wizard/wizard.js"></script>
  
        
         <script>
            

             $(document).ready(function () {
                
                // $('.ui-datepicker-div').css('z-index', '999999999');

               



                 $('#fe_nacimiento').datepicker({

                     changeMonth: true,
                     changeYear: true,
                     showAnim: 'slideDown',
                     showOtherMonths: true,
                     selectOtherMonths: true,
                     showMonthAfterYear: false,
                     yearRange: 'c-50:c+2',
                     autoSize: true,
                     zIndexOffset: 2040 ,
                     dateFormat: '<?php echo EscogeIdioma('dd-mm-yy','mm-dd-yy') ?> ',
                    nextText: '>',
                    prevText: '<'
                 });

                 
            });

        </script>
        
        
             	
<script>
    $(document).ready(function () {
            

        $('#optionsRadios1').change(function () {
            if ($('#optionsRadios1').is(':checked')) {
                var fg_option = 1;
            } else {
                var fg_option = 2;
            }
            HablitarBoton1(fg_option)//habilta el boton solo si se seleeciona la primer opcion
        });
        $('#optionsRadios2').change(function () {
            if ($('#optionsRadios2').is(':checked')) {
                var fg_option = 2;
            } else {
                var fg_option = 1;
            }
            HablitarBoton1(fg_option)//habilta el boton solo si se seleeciona la primer opcion
        });


    });
    function HablitarBoton1(fg_option) {//habilta el boton solo si se seleeciona la primer opcion
        var fg_option = fg_option;
        //alert(fg_option);
        if (fg_option == 1) {

            $('#tabs2').attr('disabled', false);//se habilita el boton
        }else{
    
            $('#tabs2').attr('disabled', true);//se deshabilita el boton
         }

       

    }

</script>		
 

        <!--activando tabs con js--->
        <script>
            $(document).ready(function () {


                //ejecuta la primer tab
                $('#tabs2').change(function () {
                    PasarTab2( );
                });

                //ejecuta la primer tab
                $('#tabs3').change(function () {
                    PasarTab3();
                    
                });



                //ejecuta la primer tab
                $('#tabs4').change(function () {
                    PasarTab4();
                });
				
				
				

              

	   });
            
		    function ChangeAlias(){
				
				var x =  document.getElementById("alias").value;
				
			    if(x.length>0){	
				
						$.ajax({
						type: "POST",
						dataType: 'json',
						url: "site/valida_alias.php",
						async: false,
						data: "ds_alias="+x+
						      "&fg_registro=1",
							            
						success: function(result){
							  var error = result.resultado.fg_error;
							  
							  if(error==true){
								$("#div_alias").removeClass('form-group');
								$("#div_alias").removeClass('has-success');
								$("#div_alias").addClass('has-error');
								
								$("#err_alias").removeClass('hidden');
								var a="";
								$("#aliass").val(a);
								
							  }
							  else{
								$("#div_alias").addClass('has-success');
								$("#div_alias").removeClass('has-error');
								$("#err_alias").addClass('hidden');
								var a=1;
								$("#aliass").val(a);
								
							  }
							  
						}
						});
					
				}
				
				
			}	


            function PasarTab2() {

               // alert('entro a tab2');
                //se cambia el titulo
                    $("#titulo1").addClass('hidden');//se oculta titulo 1
                    $("#titulo2").removeClass('hidden');//se muestra titulo 2

                    $("#tabss1").removeClass('active');//quita la clase active del tab 1
                    $("#tabss1").addClass('complete');//agrega color verde ala tab 1
                    $("#tabss2").addClass('active');//coloca azul la tab 2.

                    $("#tabs2").hide();    //se esconde el primer boton
                    $("#tabs3").removeClass('hidden');    //muestra el tercer boton
                    $('#tabs3').attr('disabled', true);//se desabilita el boton
              
                    


            }


            function PasarTab3() {

                BuscarCodigoPais();






               // alert('entro a tabs 3');
                $("#titulo1").addClass('hidden');//agrega color verde ala tab 1
                $("#titulo2").addClass('hidden');//agrega color verde ala tab 1
                $("#titulo3").removeClass('hidden');//se muestra titulo 2

                $("#tabss1").removeClass('active');//quita la clase active del tab 1
                $("#tabss1").addClass('complete');//agrega color verde ala tab 1
                $("#tabss2").removeClass('active');//quita la clase active del tab 1
                $("#tabss2").addClass('complete');//coloca azul la tab 2.

                $("#tabss5").addClass('active');//coloca azul la tab 2.

                $("#tabs2").hide();    //se esconde el primer boton
                $("#tabs3").hide();    //se esconde el tercer boton y muestra el 3ro desabilitado
                $("#tabs4").removeClass('hidden');    //muestra el tercer boton
                $('#tabs4').attr('disabled', true);//se desabilita el boton

                
          
            }



            function PasarTab4() {

                //alert('entro a tab 4');
                var ds_name_school = document.getElementById('ds_name_school').value;
                var fl_country = document.getElementById('fl_country').value;
                var ds_pass1 = document.getElementById('ds_pass1').value;
                var ds_pass2 = document.getElementById('ds_pass2').value;
                var cl_iso_pais = document.getElementById('cl_iso_pais').value;
                //var ds_coddigo_pais2= document.getElementById('ds_coddigo_pais2').value;
                var ds_codigo_pais = document.getElementById('ds_codigo_pais').value;
                var ds_codigo_telefono = document.getElementById('ds_codigo_telefono').value;
                var ds_numero_telefono = document.getElementById('ds_numero_telefono').value;
                var fname = document.getElementById('fname').value;
                var lname = document.getElementById('lname').value;
                var email = document.getElementById('email').value;
                var fl_envio_correo = document.getElementById('fl_envio_correo').value;
				var fl_estado = document.getElementById('fl_estado').value;
				var ds_alias=document.getElementById('alias').value;
				
				if(alias){
					
				}else{
					var ds_alias="";
				
					
				}
				
                if ($('#optionsRadios1').is(':checked')) {
                    var fg_option = 1;
                } else {
                    var fg_option = 2;
                }


                //alert(fg_option);

                $.ajax({
                    type: 'POST',
                    url: 'div/func_guardar_confirmacion_registro.php',
                    data: 'ds_name_school=' + ds_name_school +
                          '&fl_country=' + fl_country +
                          '&ds_pass1=' + ds_pass1 +
                          '&ds_pass2=' + ds_pass2 +
                          '&cl_iso_pais=' + cl_iso_pais +
                          //'&ds_coddigo_pais2=' + ds_coddigo_pais2+
                          '&ds_codigo_pais=' + ds_codigo_pais +
                          '&ds_codigo_telefono=' + ds_codigo_telefono +
                          '&ds_numero_telefono=' + ds_numero_telefono +
						  '&ds_alias='+ds_alias+
                          '&fname=' + fname +
                          '&lname=' + lname +
                          '&email=' + email +
						  '&fl_estado='+fl_estado+
                          '&fl_envio_correo='+ fl_envio_correo +
                          '&fg_option=' + fg_option
                      
                                                 ,


                    async: false,
                    success: function (html) {
                        //$('#send_correo').html(html);



                    }
                });



                $("#tabss2").removeClass('active');//quita la clase active del tab 2
                $("#tabss2").addClass('complete');//agrega color verde ala tab 2
                $("#tabss5").addClass('active');//coloca azul la tab con num.3 pero en realidad es la tab5.

                $("#tabs2").hide();    //se esconde el primer boton
                $("#tabs3").hide();    //se esconde el tercer boton
                $("#tabs4").hide();    //se esconde el tercer boton
                $("#tabs5").removeClass('hidden');    //muestra el cuarto boton
                $('#tabs5').attr('disabled', true);//se desabilita el boton 4

               
               





            }


            function PasarTab5() {

                $("#tabss5").removeClass('active');//quita la clase active del tab 5
                $("#tabss5").addClass('complete');//agrega color verde ala tab 5
                $("#tabs5").hide();    //se esconde el quinto boton




















            }


        </script>


        
        <script>
            //conponentes del segunto tab
            $(document).ready(function () {

                    $('#ds_name_school').change(function () {
                        HabilitaBotonTab2();
                    });

                    $('#fl_country').change(function () {
                        HabilitaBotonTab2();
                        var fl_country = document.getElementById('fl_country').value;

                        if (fl_country == 0) {
                            //se coloca en rojo
                            document.getElementById("borderes").style.border = "1px solid #b94a48";//se pone en verde
                        } else {
                           $("#borderes").removeClass('border');
                            document.getElementById("borderes").style.border = "1px solid #468847";//se pone en verde
                           

                            
                        }


                    });
					
					$('#fl_estado').change(function () {
					   var fl_estado = document.getElementById('fl_estado').value;
					    HabilitaBotonTab2();
						
						if(fl_estado==0){
						    //se coloca en rojo
                            document.getElementById("borderes1").style.border = "1px solid #b94a48";//se pone en verde
						}else{
						     $("#borderes1").removeClass('border');
                             document.getElementById("borderes1").style.border = "1px solid #468847";//se pone en verde
						}
					
                    });

          //campos del tercer tab          

                    $('#ds_pass1').change(function () {
                        HabilitaBotonTab3();
                    });

                    $('#ds_pass2').change(function () {
                        HabilitaBotonTab3();
                    });

					
					
					
					
                    $('#cl_iso_pais').change(function () {
                        BuscarCodigoPais();
                        HabilitaBotonTab3();

                        var cl_iso_pais = document.getElementById('cl_iso_pais').value;

                        //alert(cl_iso_pais);

                        if (cl_iso_pais == 0) {
                           // alert('se pone verde');
                            document.getElementById("borderes3").style.border = "1px solid #b94a48";//se pone en verde
                            
                            $("#formul").removeClass('has-success');
                            $("#formul").addClass('has-error');

                        } else {
                           // alert('s pone gris');
                            $("#borderes3").removeClass('border3');
                            $("#formul").addClass('has-success');
                            document.getElementById("borderes3").style.border = "1px solid #468847";//se pone en verde
                            
                        }

						
					   document.getElementById('ds_codigo_telefono').focus();

                    });

                   // $('#ds_codigo_pais').change(function () {
                     //   HabilitaBotonTab3();
                   // });

                    $('#ds_codigo_telefono').change(function () {
                        HabilitaBotonTab3();
                    });

                    $('#ds_numero_telefono').change(function () {
                        HabilitaBotonTab3();
                    });






            });

            function HabilitaBotonTab2(){
                var ds_name_school = document.getElementById('ds_name_school').value;
                var fl_country = document.getElementById('fl_country').value;
                var fl_estado= document.getElementById('fl_estado').value;   
               // alert(fl_country);

                if (ds_name_school == '') {
                    $('#tabs3').attr('disabled', true);//se desabilita
                    document.getElementById('ds_name_school').focus();
                    return;
                } else if (fl_country == 0 ) {
                    $('#tabs3').attr('disabled', true);//se desabilita
                    document.getElementById('fl_country').focus();
                    return;
                } 
				else {
						
					 if( (fl_estado == 0 ) && (fl_country==38) ) {
						//alert('entro');

							$('#tabs3').attr('disabled', true);//se habilita
							 document.getElementById('fl_estado').focus();
                             return;
					 }else{
							$('#tabs3').attr('disabled', false);//se desabilita
					 }
                }



            }


            function HabilitaBotonTab3() {
                


                var ds_pass1 = document.getElementById("ds_pass1").value;
                var ds_pass2 = document.getElementById("ds_pass2").value;
                var cl_iso_pais = document.getElementById("cl_iso_pais").value;
                var ds_codigo_pais =  $("#ds_codigo_pais").val();
                var ds_codigo_telefono = document.getElementById("ds_codigo_telefono").value;
                var ds_numero_telefono = document.getElementById("ds_numero_telefono").value;
				var alias=document.getElementById("aliass").value;
                


                if (ds_pass1 == "") {
                   // alert("entro aquipass");
                    $("#tabs4").attr("disabled", true);//se desabilita
                    document.getElementById("ds_pass1").focus();
                    return;

                } else if (ds_pass2 == "") {
                   // alert("entro aquipass2");
                    $("#tabs4").attr("disabled", true);//se desabilita
                    document.getElementById("ds_pass2").focus();
                    return;
                } else if (ds_pass1 != ds_pass2) {
                   // alert("diferente");
                    $("#tabs4").attr("disabled", true);//se desabilita
                    document.getElementById("ds_pass2").focus();
                    $("#error_pass").removeClass('hidden');  //apaarece el mensaje de de que no coincide los passwors
                    
                  

                    return;
                } else if (ds_pass1 == ds_pass2) {
                    // alert("diferente");
                   // $("#tabs4").attr("disabled", true);//se desabilita
                    document.getElementById("cl_iso_pais").focus();
                    $("#error_pass").addClass('hidden');  //apaarece el mensaje de de que no coincide los passwors





                    if (cl_iso_pais == 0) {
                        //alert("pais");
                        $("#tabs4").attr("disabled", true);//se desabilita
                        document.getElementById("cl_iso_pais").focus();
                        return;
                    } else if (ds_codigo_pais == '') {

                        $('#tabs4').attr('disabled', true);//se desabilita
                        //document.getElementById('ds_codigo_pais').focus();
                        return;
                    } else if (ds_codigo_telefono == '') {

                        $('#tabs4').attr('disabled', true);//se desabilita
                        //document.getElementById('ds_codigo_telefono').focus();
                        return;
                    } else if (ds_numero_telefono == '') {
                        // alert('entro qaui numero tele');
                        $('#tabs4').attr('disabled', true);//se desabilita
                        document.getElementById('ds_numero_telefono').focus();
                        return;
                    }else if(alias==''){
						$('#tabs4').attr('disabled', true);//se desabilita
						document.getElementById('aliass').focus();
						return;
					}			
					else {

                        //alert('todo listo');
                        $("#error_pass").addClass('hidden');  //desapaarece el mensaje de de que no coincide los passwors
                        $('#tabs4').attr('disabled', false);//se desabilita

                    }








                }
                else if (cl_iso_pais == 0) {
                   
                    $("#tabs4").attr("disabled", true);//se desabilita
                    document.getElementById("cl_iso_pais").focus();
                    return;
                } else if (ds_codigo_pais == '') {

                    $('#tabs4').attr('disabled', true);//se desabilita
                    document.getElementById('ds_codigo_pais').focus();
                    return;
                } else if (ds_codigo_telefono == '') {

                    $('#tabs4').attr('disabled', true);//se desabilita
                   
                    return;
                } else if (ds_numero_telefono == '') {
                  
                    $('#tabs4').attr('disabled', true);//se desabilita
                    document.getElementById('ds_numero_telefono').focus();
                    return;
					
                }else if(alias==1){
					$('#tabs4').attr('disabled', true);//se desabilita
				    document.getElementById('aliass').focus();
					return;
			    }else {

                   
					$("#error_pass").addClass('hidden');  //desapaarece el mensaje de de que no coincide los passwors
                    $('#tabs4').attr('disabled', false);//se desabilita

                }
                


            }


        </script>




		
		<script>
		 function BuscarCodigoPais( ){
		     var cl_iso_paiss = document.getElementById('cl_iso_pais').value;
		     var ds_pass1 = document.getElementById("ds_pass1").value;
		     var ds_pass2 = document.getElementById("ds_pass2").value;


		  $.ajax({
                  type: 'POST',
                  url : 'div/func_buscar_codigo_pais.php',
				  data: 'cl_iso_pais='+$('#cl_iso_pais').val(),
                  async: true,
                  success: function(html) {
				  
                      if (cl_iso_paiss == 0) {
                          document.getElementById('ocultar').style.display = 'none';

					  }else{
						document.getElementById('ocultar').style.display = 'none';
					}
				  


					  $('#codigo_pais').html(html);

					  if (ds_pass1 == '') {

					     // document.getElementById('alias').focus();
					  } else {

					   
					  }

					  
                  }

                });
				
		 
		


		 }
		 
		 $(document).ready(function()
			{
				
				
	
			});  
		 
		</script>
		
		
		
		
		<script>
		
		 $(document).ready(function () {
		
		    $('#fg_rm').change(function () {
					BotonVisible();
			});

			$('#fname').change(function () {
					HabilitarBotonEnvio();
				});
				
				$('#lname').change(function () {
					HabilitarBotonEnvio();
				});
				
				//$('#email').change(function () {
					//HabilitarBotonEnvio();
				//});
			
		
		
		 });
		
		
		
		 $(document).on("click", ".alert", function (e) {
            EnviarDatos();
        });

		
		//funcion para enviar correos  e inbvitaciones
        function EnviarDatos() {
		
            //alert('entro');
			 var ds_name_school = document.getElementById('ds_name_school').value;
			 var fl_country = document.getElementById('fl_country').value;
			 var ds_pass1 = document.getElementById('ds_pass1').value;
			 var ds_pass2 = document.getElementById('ds_pass2').value;
			 var cl_iso_pais= document.getElementById('cl_iso_pais').value;
			 //var ds_coddigo_pais2= document.getElementById('ds_coddigo_pais2').value;
			 var ds_codigo_pais= document.getElementById('ds_codigo_pais').value;
			 var ds_codigo_telefono= document.getElementById('ds_codigo_telefono').value;
			 var ds_numero_telefono= document.getElementById('ds_numero_telefono').value;
			 var fname= document.getElementById('fname').value;
			 var lname= document.getElementById('lname').value;
			 var email= document.getElementById('email').value;
			
			 var envio_multiple=1; 
			


						
				 $.ajax({
											type: 'POST',
											url: 'div/send_presenta_modal.php',
											data: 'ds_name_school=' + ds_name_school +
												  '&fl_country=' + fl_country +
												  '&ds_pass1=' + ds_pass1 +
												  '&ds_pass2=' + ds_pass2+
												  '&cl_iso_pais=' + cl_iso_pais+
												  //'&ds_coddigo_pais2=' + ds_coddigo_pais2+
												  '&ds_codigo_pais=' + ds_codigo_pais+
												  '&ds_codigo_telefono=' + ds_codigo_telefono+
												  '&ds_numero_telefono=' + ds_numero_telefono+
												  '&fname=' + fname+
												  '&lname=' + lname+
												  '&email=' + email+
												  '&envio_multiple=' + envio_multiple
												  ,


											async: false,
											success: function (html) {
												$('#send_correo').html(html);
												

												
											}
										});





			    $.ajax({
							type: 'POST',
							url: 'send_email_envio.php',
							data: 'ds_name_school=' + ds_name_school +
								  '&fl_country=' + fl_country +
								  '&ds_pass1=' + ds_pass1 +
								  '&ds_pass2=' + ds_pass2+
								  '&cl_iso_pais=' + cl_iso_pais+
								  //'&ds_coddigo_pais2=' + ds_coddigo_pais2+
								  '&ds_codigo_pais=' + ds_codigo_pais+
								  '&ds_codigo_telefono=' + ds_codigo_telefono+
								  '&ds_numero_telefono=' + ds_numero_telefono+
								  '&fname=' + fname+
								  '&lname=' + lname+
								  '&email=' + email+
								  '&envio_multiple=' + envio_multiple
								  ,


							async: false,
							success: function (html) {
								$('#envio_email').html(html);
							    
								
							}
						});
			

			           
			
			
			
			
			
			
			
			// document.getElementById('asignar').click();//clic automatico que se ejuta y sale modal


			
			
			
			
			
        }
		
		
		
		
		
		
		 function HabilitarBotonEnvio() {
		 
		     var fname= document.getElementById('fname').value;
			 var lname= document.getElementById('lname').value;
			 var email= document.getElementById('email').value;
			 //alert('entro');
			  if (fname.length == '') {
			  
						$('#bot').attr('disabled', true);//se desabilita el boton de envio de mensajes
						document.getElementById('fname').focus();
						document.getElementById('fg_rm').checked = false;
                    return;
			  
			  }else if(lname.length == '') { 
			         $('#bot').attr('disabled', true);//se desabilita el boton de envio de mensajes
			         document.getElementById('lname').focus();
					 document.getElementById('fg_rm').checked = false;
                    return;
			  }else if (email.length == '') {
                    $('#bot').attr('disabled', true);//se desabilita el boton de envio de mensajes
                    document.getElementById('email').focus();
					document.getElementById('fg_rm').checked = false;
                    return;
                }else if (email.length > 0) {
				
				
						//$('#bot').attr('disabled', false);//se habilita el boton de envio de mensajes	
				
										// se valida  fromato de email
										expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                                        if (!expr.test(email)) {
                                            //alert('no es email valido');
                                            var valor = 1;

                                       } else {
                                            var valor = 2;
                                        }

										//hasta que todos los campos sean llenados podremos enviar el form
                                        if ((fname.length > 0) && (lname.length > 0) && (valor != 1)) {
											
                                            $('#bot').attr('disabled', false);//se desabilita
                                       } else {
											
                                            $('#bot').attr('disabled', true);//se habilita

                                        }
				
									document.getElementById('fg_rm').focus();
							
				}else{
	
			 
			 }
			 
		 
		 
		 
			
						
						
						
						
		 
		 }
		 
		 function BotonVisible(){
				var fname= document.getElementById('fname').value;
				var lname= document.getElementById('lname').value;
				var email= document.getElementById('email').value;
							if($('#fg_rm').is(':checked')) {  
														var fg_aceptar=1;  
								//$('#bot').attr('disabled', true);//se desabilita el boton de envio de mensajes							
													} else {  
														var fg_aceptar=0;  
							//	$('#bot').attr('disabled', false);//se habilita el boton de envio de mensajes						
							} 
							
							
							if(fg_aceptar==1){
							    $('#bot').attr('disabled', true);//se desabilita el boton de envio de mensajes

							    $('#tabs5').attr('disabled', false);//se habilta el boton 4  para finalizar el registro
                                //alert('entro a aceptar=1');

							
							} else {
							    //alert('entro a aceptar=0');

							    if((fname=='')&&(lname=='')&&(email=='')){
							      //  alert('disable,ture');
							        $('#bot').attr('disabled', false);//se habilita el boton de envio de mensajes
							        $('#tabs5').attr('disabled', true);//se desabilita el boton 4 e impide la finalizacion del registro 
							    }else{

							       // alert('disable,false');
							        $('#bot').attr('disabled', false);//se habilita el boton de envio de mensajes
							        $('#tabs5').attr('disabled', true);//se desabilita el boton 4 e impide la finalizacion del registro 
							    }



							}
							
		 
		 }
		 
		
		</script>
<!-----=====================================================================================================--------------->		
<!----================-------------------para la seccion de teachers y alumnos create acount-----------=============------------------>		
	<script>



	    



	    $(document).ready(function () {

	     


	        $('#optionsRadios3').change(function () {
	           // alert('entro');
	            if ($('#optionsRadios3').is(':checked')) {
	                var fg_option = 1;
	            } else {
	                var fg_option = 2;
	            }
	            HablitarBoton7(fg_option)//habilta el boton solo si se seleeciona la primer opcion
	        });


	        $('#optionsRadios4').change(function () {
	            if ($('#optionsRadios4').is(':checked')) {
	                var fg_option = 2;
	            } else {
	                var fg_option = 1;
	            }
	            HablitarBoton7(fg_option)//habilta el boton solo si se seleeciona la primer opcion
	        });



	        $('#fg_terminar').change(function () {

	            
                //Obtenemos la fecha que selecciono para determinar su edad.
	            var fe_nacimiento = document.getElementById('fe_nacimiento').value;
	            
	            var fecha_na = fe_nacimiento.split("-");
	            var dia_nacim = fecha_na[0];
	            var mes_nacim = fecha_na[1];
	            var anio_nacim = fecha_na[2];
	            
	           


	            var fecha_hoy = new Date();	           
	            var ahora_anio = fecha_hoy.getFullYear();
	            var ahora_mes = fecha_hoy.getMonth();
	            var ahora_dia = fecha_hoy.getDate();
	            var edad = ahora_anio - anio_nacim;
				var fg_parent_authorization=<?php echo $fg_parent_authorization;?>;

	            if ($('#fg_terminar').is(':checked')) {
	                var fg_selecciono = 1;
	                //$('#bot').attr('disabled', true);//se desabilita el boton de envio de mensajes							
	            } else {
	                var fg_selecciono = 0;
	                //	$('#bot').attr('disabled', false);//se habilita el boton de envio de mensajes						
	            }
            
	            if (edad < 19) { //si es menor de 18 aparece fialogo para 
	               

	                if (fg_selecciono == 1) {

						if(fg_parent_authorization==1){//solo muestra el dialogo si en panel adm esta habilitado la opcion de parent partnerschool/settings
							document.getElementById("muestra_dialogo_menor_edad").click();//clic automatico que se ejuta y sale modal
	                    }else{
							HacerBotonVisible();
						}						
					
					}


	            }else {


	                HacerBotonVisible();

	            }
               


	            
	        });


	        $('#cl_sexo').change(function () {
	            var cl_sexo = document.getElementById('cl_sexo').value;


	            if (cl_sexo == 0) {
	                $("#sexo").removeClass('border2');
	                $("#sexo").addClass('border4');
	            }
	            else {
	                $("#sexo").removeClass('border2');
	                $("#sexo").removeClass('border4');
	                $("#sexo").addClass('border3');

	            }


	            



	        });



	        $('#cl_grado').change(function () {
	            var cl_grado = document.getElementById('cl_grado').value;

	            if (cl_grado == 0) {
	                $("#grado").removeClass('border2');
	                $("#grado").addClass('border4');
	            }
	            else {
	                $("#grado").removeClass('border2');
	                $("#grado").removeClass('border4');
	                $("#grado").addClass('border3');

	            }

	            HacerBotonVisible();
	        });


	        $('#fe_nacimiento').change(function () {
	            var fe_nacimiento = document.getElementById('fe_nacimiento').value;
	            HacerBotonVisible();

	            if (fe_nacimiento == '') {
	                $("#nacimiento").addClass('has-error');
	            } else {
	                $("#nacimiento").addClass('has-success');
	            }

	            document.getElementById("fg_terminar").checked = false;

	        });




	    });



	    function GuardaRelacion() {
	        var cl_parentesco = document.getElementById('cl_parentesco').value;
	        var lname_parentesco = document.getElementById('lname_parentesco').value;
	        var fname_parentesco = document.getElementById('fname_parentesco').value;
	        var fname_alumno = document.getElementById('fname').value;
	        var lname_alumno = document.getElementById('lname').value;
            var ds_email_estudiante='<?php echo $ds_email; ?>';
			var email_parentesco= document.getElementById('email_parentesco').value;
			var fl_envio_correo = '<?php echo $fl_envio_correo; ?>';
			
	        $.ajax({
	            type: 'POST',
	            url: 'func_guardar_registro_parentesco.php',
	            data: 'cl_parentesco=' + cl_parentesco +
                      '&ds_email_estudiante=' + ds_email_estudiante +
					  '&fl_envio_correo=' + fl_envio_correo +
                      '&fname_alumno=' + fname_alumno +
                      '&lname_alumno=' + lname_alumno +
                      '&lname_parentesco=' + lname_parentesco +
					  '&email_parentesco=' + email_parentesco +
                      '&fname_parentesco=' + fname_parentesco,


	            async: false,
	            success: function (html) {
	               // $('#guardar_relacion').html(html);

	                $("#tabs8").addClass('hidden');
	                $("#tab_autorizacion").removeClass('hidden');
	            }
	        });


	        HacerBotonVisible();
	    }


	    function HablitarBoton7(fg_option) {//habilta el boton solo si se seleeciona la primer opcion
	        var fg_option = fg_option;
	        //alert(fg_option);
	        if (fg_option == 1) {

	            $('#tabs7').attr('disabled', false);//se habilita el boton
	        } else {

	            $('#tabs7').attr('disabled', true);//se deshabilita el boton
	        }



	    }

	 

<?php
if($fg_tipo_registro==EnumTipoRegistro::Student) {
    
    
    
?>			
			 function HacerBotonVisible() {
			 
			 
			 
			 
			var ds_pass3 = document.getElementById('ds_pass3').value;
	        var ds_pass4 = document.getElementById('ds_pass4').value;
	        var cl_sexo = document.getElementById('cl_sexo').value;
	        var cl_grado = document.getElementById('cl_grado').value;
	        var fe_nacimiento = document.getElementById('fe_nacimiento').value;
			var fname = document.getElementById('fname').value;
			var lname = document.getElementById('lname').value;
			var fg_desbloquear_curso=<?php echo $fg_desbloquear_curso?>;
			
			var alias=document.getElementById('aliass').value;
			
			
			   if ($('#fg_terminar').is(':checked')) {
	                var fg_aceptar = 1;
	         							
	            } else {
	                var fg_aceptar = 0;
	          					
	            }
			
			   //alert(fg_desbloquear_curso);
			
				if(fg_desbloquear_curso==1){
				
					if ((fg_aceptar == 1) && (ds_pass3.length > 0) && (cl_sexo != 0) && (fe_nacimiento.length > 0) &&(cl_grado !=0) && (fname.length>0)&&(lname.length>0) && (alias==1)  ) {
							$("#tabs8").attr("disabled", false);//se habilita el boton para pasar al asigiuente seccion
						} else {

							$("#tabs8").attr("disabled", true);//se desabilita el boton para pasar al asigiuente seccion
						}
					
			
				}else{

						if ((fg_aceptar == 1) && (ds_pass3.length > 0) && (cl_sexo != 0) && (fe_nacimiento.length > 0) &&(cl_grado !=0) &&(alias==1)  ) {
							$("#tabs8").attr("disabled", false);//se habilita el boton para pasar al asigiuente seccion
						} else {

							$("#tabs8").attr("disabled", true);//se desabilita el boton para pasar al asigiuente seccion
						}
				}		
			}
			
			
<?php
}


if($fg_tipo_registro==EnumTipoRegistro::Teacher) {
    
?>
		
		   function HacerBotonVisible() {

	        var ds_pass3 = document.getElementById('ds_pass3').value;
	        var ds_pass4 = document.getElementById('ds_pass4').value;
	        var alias=document.getElementById('aliass').value;
			//alert('entro');

	            if ($('#fg_terminar').is(':checked')) {
	                var fg_aceptar = 1;
	         							
	            } else {
	                var fg_aceptar = 0;
	          					
	            }
				
				//alert(ds_pass3);
		

				if((fg_aceptar == 1 )&& (ds_pass3.length > 0) &&(alias==1) ){
					//alert('se habilta');
					$("#tabs8").attr("disabled", false);//se habilita el boton para pasar al asigiuente seccion
					
					
				} else {

					$("#tabs8").attr("disabled", true);//se desabilita el boton para pasar al asigiuente seccion
					$("#fg_terminar").attr('checked', false);
				}

			}
        <?php
    
    
}
        ?>

            
	       


	           

	       







</script>	
        
<script>
    $(document).ready(function () {

        //ejecuta la primer tab
        $('#tabs7').change(function () {
            PasarTab7();
        });

        $('#tabs8').change(function () {

		   PasarTab8();
        });





    });

    //se ejecuta en el primer boton del tab de teachers
    function PasarTab7() {

        $("#tabss6").removeClass('active');//quita la clase active del tab 6
        $("#tabss6").addClass('complete');//agrega color verde ala tab 1
        $("#tabss7").addClass('active');//coloca azul la tab 2.
        $("#tabs7").addClass('hidden');     //se esconde el primer boton
        $("#tabs8").removeClass('hidden');    //muestra el segundo boton
        $('#tabs8').attr('disabled', true);//se desabilita el boton



    }

	
	
<?php 

if($fg_tipo_registro==EnumTipoRegistro::Student) {

?>	
	
    function PasarTab8() {
       // alert('entro');

	   $("#tabs8").addClass('hidden'); //esconde el boton que hay en la tab 8
			
	   
        var ds_pass3 = document.getElementById('ds_pass3').value;
        var ds_pass4 = document.getElementById('ds_pass4').value;
        var cl_sexo = document.getElementById('cl_sexo').value;
        var cl_grado = document.getElementById('cl_grado').value;
        var fe_nacimiento = document.getElementById('fe_nacimiento').value;

        var fl_envio_correo = document.getElementById('fl_envio_correo').value;
        var fg_tipo_registro = document.getElementById('fg_tipo_registro').value;
        var fname=document.getElementById('fname').value;
		var lname=document.getElementById('lname').value;
		var fg_desbloquear_curso=<?php echo $fg_desbloquear_curso?>;
		var ds_alias=document.getElementById('alias').value;
		if(ds_alias){
			
		}else{
			
		   var ds_alias="";
		}
		
            if ($('#optionsRadios3').is(':checked')) {
                var fg_option = 1;
            } else {
                var fg_option = 2;
            }

        //fg_tipo_registro:Teacher/Alumno
		//alert('entro');
        $.ajax({
            type: 'POST',
            url: 'div/func_guardar_registro_genera_acceso.php',
            data: 'ds_pass3='+ds_pass3+
                  '&ds_pass4='+ds_pass4+
                  '&cl_sexo='+cl_sexo+
				  '&fname='+fname+
				  '&lname='+lname+
				  '&fg_desbloquear_curso='+fg_desbloquear_curso+
				  '&ds_alias='+ds_alias+
                  '&fe_nacimiento='+fe_nacimiento+
                  '&fg_option='+fg_option+
                  '&fg_tipo_registro='+fg_tipo_registro+
                  '&cl_grado='+cl_grado+
                  '&fl_envio_correo='+fl_envio_correo,
            async: false,
            success: function (html) {
                $('#guardar_regist').html(html);



            }
        });


		
		
		
		
		
		




        $("#tabss7").removeClass('active');//quita la clase active del tab 6
        $("#tabss7").addClass('complete');//agrega color verde ala tab 1
        $("#tabss8").addClass('active');//coloca azul la tab 2.
        $("#tabs8").hide();    //se esconde el primer boton
       

    }


    function PasarTabAutorizacion() {


        $("#tabs8").addClass('hidden'); //esconde el boton que hay en la tab 8

        var fg_falta_autorizacion = 1;
        var ds_pass3 = document.getElementById('ds_pass3').value;
        var ds_pass4 = document.getElementById('ds_pass4').value;
        var cl_sexo = document.getElementById('cl_sexo').value;
        var cl_grado = document.getElementById('cl_grado').value;
        var fe_nacimiento = document.getElementById('fe_nacimiento').value;
        var fl_envio_correo = document.getElementById('fl_envio_correo').value;
        var fg_tipo_registro = document.getElementById('fg_tipo_registro').value;
        var fname=document.getElementById('fname').value;
        var lname=document.getElementById('lname').value;
        var fg_desbloquear_curso=<?php echo $fg_desbloquear_curso?>;
		
		var ds_alias=document.getElementById('alias').value;
		if(ds_alias){
			
		}else{
			
		   var ds_alias="";
		}
		
        if ($('#optionsRadios3').is(':checked')) {
            var fg_option = 1;
        } else {
            var fg_option = 2;
        }

        //fg_tipo_registro:Teacher/Alumno
        //alert('entro');
        $.ajax({
            type: 'POST',
            url: 'div/func_guardar_registro_genera_acceso.php',
            data: 'ds_pass3='+ds_pass3+
                  '&ds_pass4='+ds_pass4+
                  '&fg_falta_autorizacion='+fg_falta_autorizacion+
                  '&cl_sexo='+cl_sexo+
                  '&fname='+fname+
				  '&lname='+lname+
				  '&ds_alias='+ds_alias+
                  '&fe_nacimiento='+fe_nacimiento+
				  '&fg_desbloquear_curso='+fg_desbloquear_curso+
                  '&fg_option='+fg_option+
                  '&fg_tipo_registro='+fg_tipo_registro+
                  '&cl_grado='+cl_grado+
                  '&fl_envio_correo='+fl_envio_correo,


            async: false,
            success: function (html) {
			$('#guardar_regist').html(html);
			PresentaEtiquetaAlumno();
                
               


            }
        });
        
  function PresentaEtiquetaAlumno(){
           var fl_envio_correo = document.getElementById('fl_envio_correo').value;
  
		   $.ajax({
					type: 'POST',
					url: 'muestra_etiqueta.php',
					data: 'fl_envio_correo='+fl_envio_correo,


					async: false,
					success: function (html) {
						$('#text_autorized').html(html);
					   


					}
				});
        
  
         
  
  
  
  }

        

        $("#tabss7").removeClass('active');//quita la clase active del tab 7
        $("#tabss7").addClass('complete');//agrega color verde ala tab 7
        $("#tabss8").addClass('active');//coloca azul la tab 8
        $("#tabs8").hide();    //se esconde el primer boton tab87

        $("#text_succes").addClass('hidden');
        $("#text_autorized").removeClass('hidden');

        $("#finish2").addClass('hidden');
        $("#btn_red_login").removeClass('hidden');
        $("#tab_autorizacion").addClass('hidden');
        

    }





<?php
}
?>

<?php
if($fg_tipo_registro==EnumTipoRegistro::Teacher) {

?>


  function PasarTab8() {
       // alert('entro');

	   $("#tabs8").addClass('hidden'); //esconde el boton que hay en la tab 8
			
	   
        var ds_pass3 = document.getElementById('ds_pass3').value;
        var ds_pass4 = document.getElementById('ds_pass4').value;
     

        var fl_envio_correo = document.getElementById('fl_envio_correo').value;
        var fg_tipo_registro = document.getElementById('fg_tipo_registro').value;
		var ds_alias=document.getElementById('alias').value;
				if(ds_alias){
					
				}else{
					
				   var ds_alias="";
				}
            if ($('#optionsRadios3').is(':checked')) {
                var fg_option = 1;
            } else {
                var fg_option = 2;
            }

        //fg_tipo_registro:Teacher/Alumno
		//alert('entro');
        $.ajax({
            type: 'POST',
            url: 'div/func_guardar_registro_genera_acceso.php',
            data: 'ds_pass3=' + ds_pass3 +
                  '&ds_pass4=' + ds_pass4 +
				  '&ds_alias='+ds_alias+    
                  '&fg_option=' + fg_option +
                  '&fg_tipo_registro=' + fg_tipo_registro +
                  '&fl_envio_correo=' + fl_envio_correo
                                                 ,


            async: false,
            success: function (html) {
                //$('#send_correo').html(html);



            }
        });


		
		
		
		
		
		




        $("#tabss7").removeClass('active');//quita la clase active del tab 6
        $("#tabss7").addClass('complete');//agrega color verde ala tab 1
        $("#tabss8").addClass('active');//coloca azul la tab 2.
        $("#tabs8").hide();    //se esconde el primer boton
       

    }



<?php
}
?>

</script>        
          <script>
              //conponentes del segunto tab
              $(document).ready(function () {

                  //campos 2da tab          

                  $('#ds_pass3').change(function () {
                      HabilitaBotonTab7();
                     // alert('fua');
                  });

                  $('#ds_pass4').change(function () {
                      HabilitaBotonTab7();
                  });

              });
                  


              function HabilitaBotonTab7() {
                  var ds_pass3 = document.getElementById("ds_pass3").value;
                  var ds_pass4 = document.getElementById("ds_pass4").value;
                  
                  if ($('#fg_terminar').is(':checked')) {
                      var fg_aceptar = 1;

                  } else {
                      var fg_aceptar = 0;

                  }



                  if (ds_pass3 == "") {
                      // alert("entro aquipass");
                      $("#tabs8").attr("disabled", true);//se desabilita
                      document.getElementById("ds_pass3").focus();
                      return;

                  } else if (ds_pass4 == "") {
                      // alert("entro aquipass2");
                      $("#tabs8").attr("disabled", true);//se desabilita
                      document.getElementById("ds_pass4").focus();
                      return;
                  } else if (ds_pass3 != ds_pass4) {
                      // alert("diferente");
                      $("#tabs8").attr("disabled", true);//se desabilita
                      document.getElementById("ds_pass4").focus();
                      $("#error_pass").removeClass('hidden');  //apaarece el mensaje de de que no coincide los passwod
                      $("#fg_terminar").attr("disabled", true);//se desabilita
                      return;
                  } else {

                    

                      $("#error_pass").addClass('hidden');  //desapaarece el mensaje de de que no coincide los passwod
                      $("#fg_terminar").attr("disabled", false);//se desabilita
                     // $("#tabs8").attr("disabled", false);//se desabilita




                  }
              }

        </script>
<!------------------------========================================------------------->	
        
        
        
 <?php 
 if($fg_tipo_registro==EnumTipoRegistro::Administrador){
     
 ?>
        
        
            
        	

		<script type="text/javascript">
		
		// DO NOT REMOVE : GLOBAL FUNCTIONS!
		
		$(document).ready(function() {
			

		  

			pageSetUp();
			
			
	
			//Bootstrap Wizard Validations

			  var $validator = $("#wizard-1").validate({
			    

				
			    rules: {
				
				  optionsRadios:{
				   required: true,
				  },
				
				  ds_name_school:{
				  required:true,
				  },
				  fl_country:{
				  required:true,
				  },
				  
				  ds_pass1:{
				  required:true,
				  minlength : 8,
				  },
				  ds_pass2:{
					required:true,
					minlength: 8,
					equalTo: '#ds_pass1'
				  },




				  
				  cl_iso_pais:{
				  required:true,
				  },
				  
				  ds_codigo_pais:{
				  required:true,
				  },

				  ds_codigo_telefono: {
				      required: true
				  },


				  ds_numero_telefono:{
				  required:true,
				  },
				  
				  
			      email: {
			        required: true,
			        email: "Your email address must be in the format of name@domain.com"
			      },
			      fname: {
			        required: true
			      },
			      lname: {
			        required: true
			      },
			      country: {
			        required: true
			      },



			      city: {
			        required: true
			      },
			      postal: {
			        required: true,
			        minlength: 4
			      },
			      wphone: {
			        required: true,
			        minlength: 10
			      },
			      hphone: {
			        required: true,
			        minlength: 10
			      }
			    },
			    
			    messages: {
				    ds_name_school:'<?php echo ObtenEtiqueta(1525); ?>',
			        fl_country: '<?php echo ObtenEtiqueta(1526); ?>',
			        ds_pass1: '<?php echo ObtenEtiqueta(1527); ?>',
			        ds_pass2: '<?php echo ObtenEtiqueta(1528); ?>',
			        cl_iso_pais: '<?php echo ObtenEtiqueta(1529); ?>',
			        ds_numero_telefono: '<?php echo ObtenEtiqueta(1535); ?>',
			        ds_codigo_telefono: '<?php echo ObtenEtiqueta(1540); ?>',
			        fname: '<?php echo ObtenEtiqueta(1530); ?>',
			        lname: '<?php echo ObtenEtiqueta(1531); ?>',
			       email: {
			           required: '<?php echo ObtenEtiqueta(1532); ?>',
			           email: '<?php echo ObtenEtiqueta(1533); ?>'//errro formato de email
			      }
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
			  
			  $('#bootstrap-wizard-1').bootstrapWizard({
			    'tabClass': 'form-wizard',
			    'onNext': function (tab, navigation, index) {
			      var $valid = $("#wizard-1").valid();
			      if (!$valid) {
			        $validator.focusInvalid();
			        return false;
			      } else {
			        $('#bootstrap-wizard-1').find('.form-wizard').children('li').eq(index - 1).addClass(
			          'complete');
			        $('#bootstrap-wizard-1').find('.form-wizard').children('li').eq(index - 1).find('.step')
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

		
		})

		</script>

<?php
 }

 if(($fg_tipo_registro==EnumTipoRegistro::Teacher)|| ($fg_tipo_registro==EnumTipoRegistro::Student)){

?>






		<script type="text/javascript">

		    // DO NOT REMOVE : GLOBAL FUNCTIONS!

		    $(document).ready(function () {

		        pageSetUp();

		        //Bootstrap Wizard Validations
		        var $validator = $("#wizard-1").validate({

		            rules: {
		                ds_pass3: {
		                    required: true,
		                    minlength: 8,
		                },
		                ds_pass4: {
		                    required: true,
		                    minlength: 8,
		                    equalTo: '#ds_pass3'
		                },

		                fe_nacimiento: {
		                    required: true,
		                    
		                },

		                email: {
		                    required: true,
		                    email: "Your email address must be in the format of name@domain.com"
		                }
		               
		            },

		            messages: {
		                ds_pass3:'<?php echo ObtenEtiqueta(1527); ?>',
		                ds_pass4: '<?php echo ObtenEtiqueta(1528); ?>',
                        fe_nacimiento:'',
		                email: {
		                    required: "Please enter email",
		                    email: "Error format email777 "
		                }
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

		        $('#bootstrap-wizard-1').bootstrapWizard({
		            'tabClass': 'form-wizard',
		            'onNext': function (tab, navigation, index) {
		                var $valid = $("#wizard-1").valid();
		                if (!$valid) {
		                    $validator.focusInvalid();
		                    return false;
		                } else {
		                    $('#bootstrap-wizard-1').find('.form-wizard').children('li').eq(index - 1).addClass(
                              'complete');
		                    $('#bootstrap-wizard-1').find('.form-wizard').children('li').eq(index - 1).find('.step')
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


		    })

		</script>












<?php

 }

?>








		<!-- Your GOOGLE ANALYTICS CODE Below -->
		<script type="text/javascript">
		function NoSpace(e){
			
			     tecla = (document.all) ? e.keyCode : e.which; // 2
			     if (tecla == 8) return true; // 3 backspace
			     if (tecla == 32) return false;// space
			     if (tecla == 9) return true; // 3 tab
			     if (tecla == 11) return true; // 3
			     //patron = /[0-9 @._A-Za-zÃƒÂ±Ãƒâ€˜'ÃƒÂ¡ÃƒÂ©ÃƒÂ­ÃƒÂ³ÃƒÂºÃƒÂÃƒâ€°ÃƒÂÃƒâ€œÃƒÅ¡Ãƒ ÃƒÂ¨ÃƒÂ¬ÃƒÂ²ÃƒÂ¹Ãƒâ‚¬ÃƒË†ÃƒÅ’Ãƒâ€™Ãƒâ„¢ÃƒÂ¢ÃƒÂªÃƒÂ®ÃƒÂ´ÃƒÂ»Ãƒâ€šÃƒÅ ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç´´ AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc ÃƒÅ½Ãƒâ€Ãƒâ€ºÃƒâ€˜ÃƒÂ±ÃƒÂ¤ÃƒÂ«ÃƒÂ¯ÃƒÂ¶ÃƒÂ¼Ãƒâ€žÃƒâ€¹ÃƒÂÃƒâ€“ÃƒÅ“\s\t-]/; // 4
			     //te = String.fromCharCode(tecla); // 5
			     //return patron.test(te); // 6
				 if(e.key.match(/[a-z0-9ñÃ©çáéíóúÃƒÂ±Ãƒâ€˜'ÃƒÂ¡ÃƒÂ©ÃƒÂ­ÃƒÂ³ÃƒÂºÃƒÂÃƒâ€°ÃƒÂÃƒâ€œÃƒÅ¡Ãƒ ÃƒÂ¨ÃƒÂ¬ÃƒÂ²ÃƒÂ¹Ãƒâ‚¬ÃƒË†ÃƒÅ’Ãƒâ€™Ãƒâ„¢ÃƒÂ¢ÃƒÂªÃƒÂ®ÃƒÂ´ÃƒÂ»Ãƒâ€šÃƒÅ ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇçÃƒÅ½Ãƒâ€Ãƒâ€ºÃƒâ€˜ÃƒÂ±ÃƒÂ¤ÃƒÂ«ÃƒÂ¯ÃƒÂ¶ÃƒÂ¼Ãƒâ€žÃƒâ€¹ÃƒÂÃƒâ€“ÃƒÅ“´´\s]/i)===null) {
					// Si la tecla pulsada no es la correcta, eliminado la pulsación
					e.preventDefault();
				 }
				 
				 
			
		}
			var _gaq = _gaq || [];
				_gaq.push(['_setAccount', 'UA-XXXXXXXX-X']);
				_gaq.push(['_trackPageview']);
			
			(function() {
				var ga = document.createElement('script');
				ga.type = 'text/javascript';
				ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ga, s);
			})();

		</script>
        
		
		
	</body>

</html>



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
      <label class='$col_sm_promt control-label text-align-$prompt_aling' style='font-size:15px; color:#333;'>
        ";
        if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
        if($p_requerido) echo "* ";
        if(!empty($p_prompt)) echo "$p_prompt:"; else echo "&nbsp;";
        if(!empty($p_id)) echo "</div>";
        echo "
        
      </label>
      <div class='$col_sm_cam'>
        <label class='input'>";
        if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;' style='font-size:15px; color:#333;'>";
        CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
        if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
        if(!empty($p_id)) echo "</div>";
        if(!empty($p_error)){          
            echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
        }
        echo "
        </label>
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

    echo"
  <style>
  .select2-container .select2-choice .select2-arrow {
  border-left: 1px solid #fff !important;
  background: #fff !important;
  }
  </style>
  ";
    echo"
    <style>
      .border{
         border: 1px solid #ccc;
       }   
    </style>
    ";
    echo "
  <div class='form-group smart-form $ds_clase_err' >
    <label  control-label text-align-$etq_align' style='font-size:16px; color:#6B6969;'>
      ";
    if($p_requerido)  echo "* ";
    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
    echo "
      
    </label>
    <div id='borderes' class='border' ><label class='select' required/>";
    CampoSelectBDSP($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    echo "<i></i>";
    if(!empty($p_error))
        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
    echo "
    </label></div>     
  </div>";
}
function Forma_CampoSelectBDSP1($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
    
    $ds_clase = 'form-control';
    if(!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase_err = 'has-error';
    }
    else {
        $ds_error = "";
        $ds_error_err = "";
    }

    echo"
  <style>
  .select2-container .select2-choice .select2-arrow {
  border-left: 1px solid #fff !important;
  background: #fff !important;
  }
  </style>
  ";
    echo"
    <style>
      .border{
         border: 1px solid #ccc;
       }   
    </style>
    ";
    echo "
  <div class='form-group smart-form $ds_clase_err' >
    <label  control-label text-align-$etq_align' style='font-size:16px; color:#6B6969;'>
      ";
    if($p_requerido)  echo "* ";
    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
    echo "
      
    </label>
    <div id='borderes1' class='border' ><label class='select' required/>";
    CampoSelectBDSP($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    echo "<i></i>";
    if(!empty($p_error))
        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
    echo "
    </label></div>     
  </div>";
}
function Forma_CampoSelectBDSP3($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
    
    $ds_clase = 'form-control';
    if(!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase_err = 'has-error';
    }
    else {
        $ds_error = "";
        $ds_error_err = "";
    }

    echo"
  <style>
  .select2-container .select2-choice .select2-arrow {
  border-left: 1px solid #fff !important;
  background: #fff !important;
  }
  </style>
  ";
    echo"
    <style>
      .border3{
         border: 1px solid #ccc;
       }   
    </style>
    ";
    echo "
  <div class='form-group smart-form $ds_clase_err' >
    
      ";
    if($p_requerido)  echo "* ";
    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "";
    echo "
      
    
    <div id='borderes3' class='border3' ><label class='select' required/>";
    CampoSelectBDSP($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    echo "<i></i>";
    if(!empty($p_error))
        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
    echo "</label>
    </div>     
  </div>";
}

function Forma_CampoSelectBDSP2($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
    
    $ds_clase = 'form-control';
    if(!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase_err = 'has-error';
    }
    else {
        $ds_error = "";
        $ds_error_err = "";
    }

    echo"
  <style>
  .select2-container .select2-choice .select2-arrow {
  border-left: 1px solid #fff !important;
  background: #fff !important;
  }
  </style>
  ";
    echo"
    <style>
      .border2{
         border: 1px solid #ccc;
       }   
    </style>
    ";
    echo "
  <div class='form-group smart-form $ds_clase_err' >
    <label  control-label text-align-$etq_align' style='font-size:16px; color:#6B6969;'>
      ";
    if($p_requerido)  echo "* ";
    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
    echo "
      
    </label>
    <div id='borderes2' class='border2' ><label class='select' required/>";
    CampoSelectBDSP($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
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




function Forma_CampoTextoSPPlaceHover($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='', $class_div = "form-group", $prompt_aling='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4',$placehover) {
    
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
      <label class='$col_sm_promt control-label text-align-$prompt_aling' style='font-size:15px; color:#333;'>
        ";
        if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
        if($p_requerido) echo "* ";
        if(!empty($p_prompt)) echo "$p_prompt:"; else echo "&nbsp;";
        if(!empty($p_id)) echo "</div>";
        echo "
        
      </label>
      <div class='$col_sm_cam'>
        <label class='input'>";
        if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
        CampoTextoPlaceHover($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script,$placehover);
        if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
        if(!empty($p_id)) echo "</div>";
        if(!empty($p_error)){          
            echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
        }
        echo "
        </label>
      </div>      
    </div>";
        
    }
    else
        Forma_CampoOculto($p_nombre, $p_valor);
}

function CampoTextoPlaceHover($p_nombre, $p_valor, $p_maxlength, $p_size, $p_clase='css_input', $p_password=False, $p_script='',$placehover) {
    
    if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        if(!$p_password)
            $ds_tipo = 'text';
        else
            $ds_tipo = 'password';
        echo "<input type='$ds_tipo' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" maxlength='$p_maxlength' size='$p_size' placeholder='$placehover' ";
        if($p_password)
            echo " autocomplete='off'";
        if(!empty($p_script)) echo " $p_script";
        echo ">";
    }
    else
        Forma_CampoOculto($p_nombre, $p_valor);
}





function Forma_CampoSelectSP($p_prompt, $p_requerido, $p_nombre, $p_opc, $p_val, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $align_propmt='right', $col_sm_promt='col col-sm-12', $col_sm_cam='col col-sm-12') {
    
    $ds_clase = 'form-control';
    if(!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase_err = 'has-error';
    }
    else {
        $ds_error = "";
        $ds_error_err = "";
    }
    
    
    
    echo"
  <style>
 .select2-container .select2-choice {
 border: 0px solid #fff !important;
 }
 
 
 .select2-container .select2-choice .select2-arrow {
 background: #fff !important;
 border:0px solid #fff !important;
 }

 
  </style>
  ";
    echo"
    <style>
      .border2{
         border: 1px solid #ccc;
       }   
       
      .border3{
         border: 1px solid #468847;
      } 
      .border4{
         border: 1px solid #b94a48;
      }
 
     
    </style>
    ";
    
    
    
    echo "
  <div class='row form-group smart-form $ds_clase_err'>
    <label  control-label text-align-$etq_align' style='font-size:16px; color:#6B6969;margin-bottom:6px;'>
      ";
    if($p_requerido)  echo "* ";
    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
    echo "
      
    </label>
    <div  class='border2' id='sexo'><label class='select'>";
    CampoSelectSP($p_nombre, $p_opc, $p_val, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    echo "<i></i>";
    if(!empty($p_error))
        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
    echo "
    </label></div>     
  </div>";
    
}
function CampoSelectSP($p_nombre, $p_opc, $p_val, $p_actual, $p_clase='css_input', $p_seleccionar=False, $p_script='') {
    
    
    
    
    $tot = count($p_opc);
    echo "<select id='$p_nombre' name='$p_nombre' class='select2'";
    if(!empty($p_script)) echo " $p_script";
    echo ">\n";
    if($p_seleccionar)
        echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
    for($i = 0; $i < $tot; $i++) {
        echo "<option value=\"$p_val[$i]\"";
        if($p_actual == $p_val[$i])
            echo " selected";
        echo ">$p_opc[$i]</option>\n";
    }
    echo "</select>";
}




function Forma_SelectBDGrupo($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $align_propmt='right', $col_sm_promt='col col-sm-12', $col_sm_cam='col col-sm-12') {
    
    $ds_clase = 'form-control';
    if(!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase_err = 'has-error';
    }
    else {
        $ds_error = "";
        $ds_error_err = "";
    }
    
    
    
    echo"
  <style>
 .select2-container .select2-choice {
 border: 0px solid #fff !important;
 }
 
 
 .select2-container .select2-choice .select2-arrow {
 background: #fff !important;
 border:0px solid #fff !important;
 }

 
  </style>
  ";
    echo"
    <style>
      .border2{
         border: 1px solid #ccc;
       }   
       
      .border3{
         border: 1px solid #468847;
      } 
      .border4{
         border: 1px solid #b94a48;
      }
 
     
    </style>
    ";
    
    
    
    echo "
  <div class='row form-group smart-form $ds_clase_err'>
    <label  control-label text-align-$etq_align' style='font-size:16px; color:#6B6969;margin-bottom:6px;'>
      ";
    if($p_requerido)  echo "* ";
    if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
    echo "
      
    </label>
    <div  class='border2' id='grado'><label class='select'>";
    CampoSelectGrupo($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    echo "<i></i>";
    if(!empty($p_error))
        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
    echo "
    </label></div>     
  </div>";
    
}

function CampoSelectGrupo($p_nombre, $p_query, $p_actual, $p_clase='css_input', $p_seleccionar=False, $p_script='') {
    /*    
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
     */

    echo "<select   id='$p_nombre' name='$p_nombre' class='select2'";
    //if($p_seleccionar){
    echo"<optgroup label=''>";
    echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
    echo"</optgroup>"; 
    //}
	$rs = EjecutaQuery($p_query);
    while($row = RecuperaRegistro($rs)) {
        $nombre=str_texto($row[1]);
        echo"<optgroup label='$nombre'>";
		$Query="SELECT fl_grado,nb_grado,cl_clasificacion_grado
                FROM k_grado_fame WHERE cl_clasificacion_grado=$row[0]
                ORDER BY  fl_grado asc  ";
        $rs2=EjecutaQuery($Query);
        //$tot = CuentaRegistros($rs2);
        while($row2 = RecuperaRegistro($rs2)) {
            $nb_nombre2=$row2[1];
            $contador++;
            echo "<option value=\"$row2[0]\"";
			$etq_campo = DecodificaEscogeIdiomaBD($row2[1]);		
            echo ">$etq_campo</option>\n";

            $nb_nombre_actual=$nb_nombre2;
        }

        echo"</optgroup>";
	}	

    echo "</select>";   
    /*  
    $tot = count($p_opc);
    echo "<select id='$p_nombre' name='$p_nombre' class='select2'";
    if(!empty($p_script)) echo " $p_script";
    echo ">\n";
    if($p_seleccionar)
    echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
    for($i = 0; $i < $tot; $i++) {
    echo "<option value=\"$p_val[$i]\"";
    if($p_actual == $p_val[$i])
    echo " selected";
    echo ">$p_opc[$i]</option>\n";
    }
    echo "</select>";
	
     */
}

?>




