<?php 
	# Libreria de funciones
  require("../lib/self_general.php");

  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe Parametros
  $fg_filtro = RecibeParametroHTML('fg_filtro');
  $fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');
 
 
?>
<div class="row">
	<div class="col-md-12">
        
		<div id="community-container_items" class="well well-lg" > </div>
	</div>

</div>



<script>
 $(document).ready(function(){
     var container = $("#community-container_items");

        var index, selectedPost, selectedFilter, classmate, myPosts;
        index = 0;
        selectedPost = 0;
        selectedFilter = 0;
        classmate = 0;
        myPosts = 0;
        console.log("inicia pagina comunidad");
        requestItemsboard(container);
        $(window).on('scroll.infinite', function (){
            if($(window).scrollTop() == $(document).height() - $(window).height()) {
                //boardController.requestItemsboard(container);
                requestItemsboard(container);
            }
        });
		
        function requestItemsboard(container) {
            var fg_filtro = "<?php echo $fg_filtro?>";
            var fl_programa_sp = "<?php echo $fl_programa_sp;?>";
            $.ajax({
                type: 'GET',
                url: '/fame/site/community_items.php',
                data: 'fg_filtro=' + fg_filtro +
                      '&fl_programa_sp='+fl_programa_sp+
                // '&classmate='+classmate+
                //'&my_posts=' + myPosts +
                '&index=' + index
            }).done(function (result) {
                var elems = JSON.parse(result);
                // Check for end of board
                if (elems.index.end > 0) {
                    // update index
                    console.log("pasa aki");
                    index = elems.index.end;
                    //alert(index);
                    displayItems(container, elems);
                    // $selector.packery('reloadItems');
                }
                else {
                    container.append("<h2 class='row-seperator-header txt-color-red'><i class='fa  fa-warning'></i> " + elems.index.message + " </h2>");
                }
            });
        }
		
		
		
});

 var displayItems = function(container, elems){
	 
	 var item;
	 var items = "<div class='row' >";
	 var contador_items = 0;
	 var no_items_cerrar = 4;

	 for(var i=0; i<elems.size.total; i++){
		 
		 item = elems["item"+i];
		 
		 contador_items++;

		 var fl_usuario=item.fl_usuario;
		 var ds_name = item.ds_nombres;
		 var fl_usuario_actual = item.fl_usuario_logueado;
		 var ds_avatar = item.ruta_avatar;
		 var ds_profile = item.ds_perfil;
		 var ds_country = item.ds_pais;
		 var ds_institucion = item.nb_instituto;
		 var boton_para_enviar_sms = item.boton_para_enviar_sms;
		 var boton_para_enviar = item.boton_para_enviar;
		 var btn_enviado = item.btn_enviado;
		 
		 items += "<div class='col-md-3'  data-aos='fade-up' >" +
                    "<div class='well student-profile text-center' style='width:255px;'>" +
                      "<a href='#site/myprofile.php?profile_id=" + fl_usuario + "&c=1&uo=" + fl_usuario_actual + "'>" + ds_avatar + "</a>" +
		              "<div style='height:190px;font-size: 14px;'>"+
                        "<div class='text-center'>" +
								"<br><span class='no-margin h6'><strong><a href='#site/myprofile.php?profile_id=" + fl_usuario + "&c=1&uo=" + fl_usuario_actual + "' style='color: #404040;'> " + ds_name + "</a></strong></span>" +
					    "</div>" +
                        "<div class='text-center'>" +
							"<span class='text-muted no-margin h2'><small><i class='fa fa-user-o' aria-hidden='true'></i> " + ds_profile + "</small> </span>" +
					    "</div>" +
                        "<div class='text-center'>" +
					 		"<span class='text-muted no-margin h2'><small><i class='fa fa-institution' aria-hidden='true'></i></a>  " + ds_institucion + "</small> </span>" +
					    "</div>" +
				        "<div class='text-center'>" +
							    "<span class='text-muted no-margin h2'><small><i class='fa fa-globe' aria-hidden='true'></i></a>  " + ds_country + "</small> </span>" +
					    "</div>" +
                      "</div>" +

                      "<div class='text-center'>" +
						//"<br><a  style='background: #fff;margin:5px;' href='#site/profile_view.php?profile_id="+fl_admin+"' class='btn btn-default btn-sm' > View Profile</a>"+
						  "<a class='btn btn-default " + boton_para_enviar_sms + "' style='background: #fff; margin:5px;color:#137103;border-color:#137103;' href='javascript:SendMessageDialog(\"" + fl_usuario + "\",\"" + ds_name + "\");' > Send Message</a>" +
					      "<a class='btn btn-default " + boton_para_enviar + "' style='background: #fff; margin:5px;color:#0092CD;border-color:#0092CD;' id='btn_" + fl_usuario + "' href='javascript:OpenModalInvitacion(" + fl_usuario_actual + "," + fl_usuario + ");' ><i class='fa fa-link' aria-hidden='true'></i> Connect</a>" +
                          "<a class='btn btn-default " + btn_enviado + "' id='btn_peding_" + fl_usuario + "'  style='background: #fff; margin:5px;color:#a4781b;border-color:#a4781b;'><i class='fa fa-check-square-o' aria-hidden='true'></i> Pending</a>" +

					 "</div>" +


		           "</div></div>";
		 
		 
         //cada 4 se cerrara y se incluira otro
	     //se inciializa con <div row> y cada 3 items se cierrra ese div row.. por eso se coloca este contador para cerrarlos y agruparlos  
		 if (contador_items == no_items_cerrar) {

		     items += "</div><div class='row' style='padding-top:12px'>";
		     no_items_cerrar = contador_items + 4;

		 }


		 
	 }
	 
	 
	 
	 
	 
	container.append($(items));
	//container.imagesLoaded(function(){
      //      container.packery({ columnWidth: 320, itemSelector: ".item", gutter: 10 });
    //});
 };

 //**********Inicia script para envio de invitacion para coemzar a chaterar.***********/
 function OpenModalInvitacion(fl_usuario_origen, fl_usuario_destino) {
   

     $('#invitacion_friends').modal('show');

     $.ajax({
         type: 'POST',
         url: 'site/muestra_modal_invitacion.php',
         data: 'fl_usuario_origen=' + fl_usuario_origen +
               '&fl_usuario_destino=' + fl_usuario_destino,

         success: function (html) {
             $('#muestra_ifo_friends').html(html);
         }

     });



 }


 function MostrarRedaccion() {

     $('#add_note').addClass('hidden');
     $('#pant1').addClass('hidden');
     $('#pant2').removeClass('hidden');
     $('#close_modal').removeClass('hidden');


 }
 function Pinta() {

     var text = document.getElementById('comentario_friends').value;

     if (text.length == '') {

     } else {
         $('#text_coment').addClass('has-success');
     }

 }


 function SendConect(fl_usuario_origen, fl_usuario_destino) {

     var ds_mensaje = document.getElementById('comentario_friends').value;


     $.ajax({
         type: 'POST',
         url: 'site/conectar_usuarios.php',
         data: 'fl_usuario_origen=' + fl_usuario_origen +
               '&ds_mensaje=' + ds_mensaje +
               '&fl_usuario_destino=' + fl_usuario_destino,

     }).done(function (result) {

         var result = JSON.parse(result);
         var error = result.error;
         var nb_usuario_origen = result.nb_usuario_origen;

         if (error == 0) {

             $('#btn_' + fl_usuario_destino).addClass('hidden');
             $('#btn_peding_' + fl_usuario_destino).removeClass('hidden');
             $('#invitacion_friends').modal('toggle');

             //Enviamos token de notificacion  node.js bien chido. 
             socket.emit('solicitud-amistad', fl_usuario_origen, fl_usuario_destino, nb_usuario_origen);


         }



     });

 }
 //**************Finaliza fucniones apara envio comunity envio de solicitudes amigos.************/
 

</script>



