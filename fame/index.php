<?php

# Libreria de funciones
require_once("lib/self_general.php");

#$close_maintenance=ObtenConfiguracion(170);
#if($close_maintenance==1)
#{
    
#    header("Location: ../maintenance.php");
#}



/**MJD***Solo se utiliza para aquellos institutos que ya vencieron su plan Trial y posteriormemte quieren adquirir un plan de pago**************************************************/

$no_clave=RecibeParametroHTML('c','',true);  #llave que recibe para confirmar que es un isntituo que qiiere comprar plan.

if(!empty($no_clave)){

    #se verifica si esxiste esa llave en LA BD.
    $Query ="SELECT ds_clave_acceso FROM k_envio_email_fame WHERE ds_clave_acceso='$no_clave' ";
    $Query.=" ";
    $row=RecuperaValor($Query);
    $no_clave_existente_bd=$row[0];

    if($no_clave==$no_clave_existente_bd){

        $Query="SELECT fl_instituto,fl_usuario FROM k_envio_email_fame WHERE ds_clave_acceso='$no_clave' ";
        $row=RecuperaValor($Query);
        $fl_instituto=$row[0];
        $fl_usuario=$row[1];  

        $Query="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_usuario ";
        $row=RecuperaValor($Query);
        $cl_sesion=$row[0]; 

        # Crea cookie con identificador de sesion
        setcookie(SESION_RM, $cl_sesion, time( )+SESION_VIGENCIA_RM, "/");
        setcookie(SESION_CHECK_RM, 'True', time( )+SESION_VIGENCIA_RM, "/");
        //EjecutaQuery("UPDATE c_usuario SET fg_remember_me='1' WHERE cl_sesion='$cl_sesion'");
        ActualizaSesion($cl_sesion, false);

        #redirige al home del billing para realizarpago
        echo"<button type='button' class='hidden' id='redirige' onClick=\"window.location.href='index.php#site/billing.php'\" style='display:none;'></button> ";
        echo"
						<script>
						document.getElementById('redirige').click();//clic automatico que se ejuta y sale modal

					
						</script>
					";   
    }
}

/**************************************/

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermisoSelf(FUNC_SELF)) {  
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}

# Obtenemo el instituto
$fl_instituto = ObtenInstituto($fl_usuario);

//you can add your custom css in $page_css array.
//Note: all css files are inside css/ folder
//$page_css[] = "";
// include("lib/header.php");

//require UI configuration (nav, ribbon, etc.)

//include left panel (navigation)
PresentaHeaderNav($fl_usuario);

// <!-- CONTENT STARTS HERE -->

PresentaMainIni();
PresentaRibbon();
include("lib/contacts.php"); 
PresentaContentIni();  
PresentaContentFin();
PresentaMainFin();

PresentaFooter();

?>
<script type="text/javascript">
    // $(document).ready($('#loading_fame').hide());
    // This maintain the loading until the feed is loaded //
   // $( window ).on( "load", function() {
    //    $('#loading_fame').hide();
        //realizamos el ajax para pintar el chat.
        //$.ajax({
         //   type: 'POST',
          //  url: 'user_chat.php',
          //  data: 'fl_usuario=<?php echo $fl_usuario;?>',
        //async: false,
        //success: function (html) {
        //    $('#offline-list').html(html);
       // }
   // });
  //  });
</script>

<?php 

// Displays the list of users for the contact list
function DisplayUserChat($row, $fl_usuario){
    $fl_user = $row[0];

    $ds_nombre = str_uso_normal($row[2]);
    $ds_apaterno = str_uso_normal($row[13]);

    # profession = teacher's company(ds_empresa) in industry or student's program(nb_programa)		
    $ds_profession = str_uso_normal($row[3]);
    
    # Identificamos si es FAME O VANAS
    $edad = $row[11];
    $fame = $row[12];

    # Institute
    $fl_instituto = ObtenInstituto($fl_user);
    if($fame==1){
        $nb_instituto = ObtenNameInstituto($fl_instituto); 
        $ds_ruta_avatar = ObtenAvatarUsuario($fl_user);
    }
    else{
        $nb_instituto = ObtenEtiqueta(2010);
        $ds_ruta_avatar = ObtenAvatarUsrVanas($fl_user);
    }
    if(empty($ds_ruta_avatar))
        $ds_ruta_avatar =  "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."' class='".$class."' width='70' height='70'>";
    $ds_pais = str_uso_normal($row[4]);
    # Pais de la institution que lo invito
    if(empty($ds_pais) && $fame==1){
        $row1 = RecuperaValor("SELECT b.ds_pais FROM c_instituto a, c_pais b WHERE a.fl_pais = b.fl_pais AND a.fl_instituto=$fl_instituto");
        $ds_pais = $row1[0];
    }
    
    # Obtenemos el perfil del usuario
    $fl_perfil = ObtenPerfilUsuario($fl_user); 
    if(empty($fl_perfil))
        $fl_perfil = ObtenPerfil($fl_user);
    # Perfil
    $row0 = RecuperaValor("SELECT nb_perfil FROM  c_perfil WHERE fl_perfil=$fl_perfil");
    $nb_perfil = $row0[0];   
    
	#Recupermaos el nombre del Usuario Logado
    $QueryU="SELECT CONCAT(ds_nombres,' ',ds_apaterno)as nombre FROM c_usuario WHERE fl_usuario=$fl_usuario ";
    $rowU=RecuperaValor($QueryU);
    $fnameU=$rowU[0];
	
    # blockgin last name
    $ds_nombre = ObtenNombreUsuario($fl_user,$fl_usuario);

    if($fl_usuario <> $fl_user){
        
        //echo" <li id='user-$fl_user'>user $fl_usuario</li>";
        # link to chat message  ya no se utiliza todo lo mandamos por ajax.
        echo 
        "
        <li id='user-$fl_user'>
					<div class='media'>
						<a href='#site/messages.php?usr=$fl_user' class='pull-left media-thumb'><img src='".$ds_ruta_avatar."' class='media-object'></a>
						<div class='media-body'>
							<a href='#site/messages.php?usr=$fl_user' class='hidden'><strong>$ds_nombre</strong> (".$nb_perfil.")</a>
							
							        <a href='#' id='div_$fl_user' class='usr' 
										data-chat-id='$fl_user' 
									  	data-chat-fname='$ds_nombre ' 
									  	data-chat-lname='' 
									  	data-chat-status='online' 
									  	data-chat-alertmsg='' 
									  	data-chat-alertshow='false' 
									  	data-rel='popover-hover' 
									  	data-placement='right' 
									  	data-html='true' 
									  	data-content=\"
											<div class='usr-card'>
												
												<div class='usr-card-content'>
													<h3>Jessica Dolof</h3>
													<p>Sales Administrator</p>
												</div>
											</div>
										\"> 
									  	<i></i>$ds_nombre
									</a>
							
							
							<small><i class='fa fa-institution'></i> $nb_instituto</small>
							<small><i class='fa fa-globe'></i> $ds_pais</small>
						</div>
					</div>
					
					               
									
									
									
									
                    
					
					
					
				</li>";
    } 
    
}


?>


<div class="row hidden">
		<div class="col-md-8">
		<p>&nbsp;</p>
		</div>

        <div class="col-md-4 "  style="position:fixed;z-index:1000;bottom:0px;right:0px;">
		
			<div class="chatbox mike" id="chatbox" >
					<div class="panel panel-primary cat" >
					
					
						<div class="panel-heading text-left"   id="accordion2" data-position="on" data-toggle="collapse" onClick='MuestraNoUser();' data-parent="#accordion2" href="#collapseOneChat" style="padding: 2px;cursor:pointer;height:27px !important;">
						   <small style="padding-left:15px;font-size:13px;"><i class="fa fa-comments" aria-hidden="true"></i> Chat (<span id="user_onlines">0</span>)</small> 
						   
						          
						   
						   
						   
						</div>
						
						<div class="panel-collapse collapse" id="collapseOneChat">
							<div class="panel-body" style="height:400px; margin-right:-14px;">
								<div class="scrollbar" style="height: 400px;" >
								   
									<!-----Inicia User --------->
																			
											<div class="tab-content">
												<div class="tab-pane active">

													<!-- Online List -->

													<h4 class="text-left" style="color:#999;"><?php echo ObtenEtiqueta(1935); ?></h4>
													<ul id="online-list" class="chatuserlist"></ul> 

													<div class="mb30"></div>

													<!-- Offline List -->
													<h4 class="hidden"><?php echo ObtenEtiqueta(1936); ?></h4>
													<ul id="offline-list" class="chatuserlist hidden">
														<?php
                                                                             # have already validated $fl_usuario in header.php
                                                                            //    $rs = GetUserOnline($fl_usuario);
                                                                            //   while($row = RecuperaRegistro($rs)) {
                                                                            //  	  DisplayUserChat($row, $fl_usuario);	
                                                                           //  	 }
                                                        ?>
													</ul>
												</div>
											</div>

					
								
								</div>
								
							</div>
							<div class="panel-footer hidden">
								
								
							
								
								
							</div>
						</div>
					</div>
			</div>		
        </div>
    </div>




<!-- Chat UI : plugin -->
 <script src='<?php echo PATH_SELF_JS; ?>/smart-chat-ui/smart.chat.ui.min.js' charset="utf-8"></script>
 <script src='<?php echo PATH_SELF_JS; ?>/smart-chat-ui/smart.chat.manager.min.js' charset="utf-8"></script>