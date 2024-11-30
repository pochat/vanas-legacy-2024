    <!--================================================== -->

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<!--<script data-pace-options='{ "restartOnRequestAfter": true }' src="<?php echo PATH_SELF_JS; ?>/plugin/pace/pace.min.js"></script>-->
    
    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="<?php echo PATH_SELF_JS; ?>/jquery2_1.min.js"></script>
		<!-- IMPORTANT: APP CONFIG -->
		<script src="<?php echo PATH_SELF_JS; ?>/app.config.js"></script>

		<!-- JQuery 2.0.2 and JQuery-UI 1.10.3 -->
		<script src="<?php echo PATH_SELF_JS; ?>/libs/jquery-2.0.2.min.js"></script>
		<script src="<?php echo PATH_SELF_JS; ?>/libs/jquery-ui-1.10.3.min.js"></script>

		<!-- BOOTSTRAP JS -->
		<!--<script src="<?php echo PATH_SELF_JS; ?>/bootstrap/bootstrap.min.js"></script>-->
		<script src="<?php echo PATH_SELF_JS; ?>/bootstrap/bootstrap.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src="<?php echo PATH_SELF_JS; ?>/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="<?php echo PATH_SELF_JS; ?>/smartwidgets/jarvis.widget.min.js"></script>

		<!-- browser msie issue fix -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/msie-fix/jquery.mb.browser.min.js"></script>

		<!-- FastClick: For mobile devices -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/fastclick/fastclick.js"></script>

		<!-- EASY PIE CHARTS -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

		<!-- ImageLoaded -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/imagesloaded/imagesloaded.pkgd.min.js"></script>
		
		<!-- Packery -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/packery/packery.pkgd.min.js"></script>

		<!-- Countdown -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/jquery.countdown/jquery.plugin.min.js"></script>
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/jquery.countdown/jquery.countdown.min.js"></script>

		<!-- Clock -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/jquery-clock/jqClock.min.js"></script>

		<!-- HTML entities @url: https://github.com/mathiasbynens/he -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/he/he.js"></script>

    <!-- Flowplayer library -->
    <script src="<?php echo PATH_SELF_JS; ?>/flowplayer/flowplayer.min.js"></script>
    <!-- Flowplayer hlsjs engine -->
    <script src="<?php echo PATH_SELF_JS; ?>/flowplayer/flowplayer.hlsjs.min.js"></script>
    <!-- Flowplayer quality selector plugin -->
    <script src="<?php echo PATH_SELF_JS; ?>/flowplayer/flowplayer.vod-quality-selector.js"></script>
	<script src='<?php echo PATH_SELF_JS; ?>/flowplayer/flowplayer.thumbnails.min.js'></script>

    <!-- Node -->
    <?php
    // if ($_SERVER["SERVER_PORT"] != 443)
      echo "<script src='https://campus.vanas.ca:3000/socket.io/socket.io.js'></script>";
    // else
      // echo "<script src='http://campus.vanas.ca:3000/socket.io/socket.io.js'></script>";
    ?>
		<script src="<?php echo PATH_SELF_JS; ?>/node.inc.js"></script>

		<!-- app.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/app.js"></script>

		<!-- JQUERY SELECT2 INPUT -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/select2/select2.min.js"></script>
    
		<!-- vanas.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/vanas.js"></script>

		<!-- board.inc.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/board.inc.js"></script>
		
		   <!-- galeria--->
        <script src="<?php echo PATH_SELF_JS; ?>/galeria.inc.js"></script>
		
		<script src="<?php echo PATH_SELF_JS; ?>/courses.inc.js"></script>
		
		<!-- messages.inc.js -->
		<!--<script src="<?php echo PATH_SELF_JS; ?>/messages.inc.js"></script>-->
		<script src="<?php echo PATH_LIB; ?>/js_node/messages.inc.js<?php echo '?date='.date('Ymdhis'); ?>"></script>

		<!-- desktop.inc.js -->
		<script src="<?php echo PATH_SELF_JS; ?>/desktop.inc.js<?php echo '?date='.date('Ymdhis'); ?>"></script>
    
    <!-- PAGE RELATED PLUGIN(S) -->
		<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/dropzone/dropzone.min.js"></script>
    
    <!-- JQUERY VALIDATE -->
		<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>
    
    <!-- PAGE RELATED PLUGIN(S) -->
		<script src="<?php echo PATH_SELF_JS; ?>/plugin/jquery-form/jquery-form.min.js"></script>
    <!--- Stripe -->
    <script src="https://js.stripe.com/v3/"></script>
    
	<script src="js/plugin/x-editable/moment.min.js"></script>
	<script src="js/plugin/x-editable/jquery.mockjax.min.js"></script>
	<script src="js/plugin/x-editable/x-editable.min.js"></script>

    

	<!-- MJD FEB19: Librerias para tags FAME - Course Library -->
    <link  href="<?php echo PATH_SELF_JS; ?>/tags/master_tag.css" rel="stylesheet" type="text/css">
    <script src="<?php echo PATH_SELF_JS; ?>/tags/tag-it.js" type="text/javascript" charset="utf-8"></script>
    <link href="<?php echo PATH_SELF_JS; ?>/tags/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    $('#loading_fame').hide();
    console.log('termino');
</script>
    <?php 
    $fl_usuario=ValidaSesion(False,0, True);
    ?>

    <script type="text/javascript">
	
	function PermitirCourse(accion,fl_usuario,fl_programa,fg_ejecucion){
	
    var mensaje = $("#ds_mesaje_declined").val();
	if(mensaje){
		var mensaje= mensaje;
	}else{
		var mensaje='';
	}
		
	if(fg_ejecucion){
		var fg_ejecucion=1;
	}else{
		var fg_ejecucion=0;
	}
	
	
	 $.ajax({
		 type:'POST',
		 url:'site/access_course.php',
		 data:'accion='+accion+
		      '&fl_usuario=' + fl_usuario +
              '&mensaje='+mensaje+
			  '&fg_ejecucion='+fg_ejecucion+
			  '&fl_programa='+fl_programa,
		 async:true,
		 success:function(html){			 
		     $('#accion_addwsz').html(html);
		 
		 }
		 
	 });
	
	
}
	
    pageSetUp();    
    
    $(document).ready(function(){
      $("#refresh").click();
    });

</script>

<!--

<script type="text/javascript">
    setTimeout(function(){ 
       
        //  console.log('llego a entra');
        var container = $("#offline-list");
        var fl_usuario=<?php echo $fl_usuario;?>;
        $.ajax({
            type: 'POST',
            url: 'lib/presenta_listado_chat.php',
            data: 'fl_usuario=' + fl_usuario,

            async: true,
        }).done(function (result) {
            var elems = JSON.parse(result);			
            displayChat(container, elems);
			
        }); 

        var displayChat = function(container, elems){
            var item;
            var items="";
            var fl_usuario=<?php echo $fl_usuario;?>;
			  var ds_nombre="<?php echo ObtenNombreUsuario($fl_usuario);?>";
			  
            for(var i=1; i<elems.size.total; i++){
                            
                            
                            
                item = elems["item"+i];
                var fl_usuario=elems["item"+i].fl_usuario;
                var ds_ruta_avatar=elems["item"+i].ds_ruta_avatar;
                var ds_nombre=elems["item"+i].ds_nombre;
                var nb_perfil=elems["item"+i].nb_perfil;
                var nb_instituto=elems["item"+i].nb_instituto;
                var ds_pais=elems["item"+i].ds_pais;
                items+=
                "<li id=\"user-"+fl_usuario+"\">" +
                "	<div class='media'>"+
                "    <a href=\"#site/messages.php?usr="+fl_usuario+"\" class='pull-left media-thumb'><img src=\""+ds_ruta_avatar+"\" class=\"media-object\"></a>"+
                "     <div class=\"media-body\"> "+
                "         <a href=\"#site/messages.php?usr="+fl_usuario+"\" class=\"hidden\"><strong>"+ds_nombre+"</strong> ("+nb_perfil+")</a>"+
                "         <a href=\"#\" id=\"div_"+fl_usuario+"\" class=\"usr\" "+
                "            data-chat-id=\""+fl_usuario+"\""+
                "			 data-chat-fname=\""+ds_nombre+"\""+
                "		     data-chat-lname=\"\""+
                "            data-chat-status=\"online\""+
                "            data-chat-alertmsg=\"\""+
                "            data-chat-alertshow=\"false\""+
                "            data-rel=\"popover-hover\""+
                "            data-placement=\"right\""+
                "            data-html=\"true\""+
                "            data-content=\""+
				"							<div class='usr-card'>"+
				"						    	<div class='usr-card-content'>"+
				"									<h3>Jessica Dolof</h3>"+
				"									<p>Sales Administrator</p>"+
				"								</div>"+
				"							</div>"+
				"						\">"+
                "            <i></i>"+ds_nombre+""+
                "         </a>"+
                "         <small><i class=\"fa fa-institution\"></i>"+nb_instituto+"</small> "+
                "         <small><i class=\"fa fa-globe\"></i>"+ds_pais+"</small>"+
                            
                "     <div>"+
                "   </div>"+
                "</li>";			
                             
                              
            }
                          
            container.append($(items));
                          
                         
            socket.emit('add-user', {"fl_user": fl_usuario, "ds_name": ds_nombre},elems);
                          
        }

    }, 1000);

    </script>
-->

<!--
<script>

    $.ajax({
        type: 'POST',
        url: 'lib/user_chat.php',
        data: 'fl_usuario=<?php echo $fl_usuario;?>',
        async: false,
        success: function (html) {
            $('#offline-list').html(html);



        }
    });

</script>
-->

 
