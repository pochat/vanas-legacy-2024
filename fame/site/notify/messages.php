<?php
	# Librerias
	require("../../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

?>
<!--href='#site/messages.php?usr="+user.id+"'----------    ----->
<ul class="notification-body"></ul>

<script type="text/javascript">
	var users, user, list,mensaje,total;
	users = <?php GetChatUsers($fl_usuario); ?>;
	mensaje="<?php echo ObtenEtiqueta(2397);?>";
    total=users.size.total;

	list = "";
	
	if(total==0){
    
        list +=  "<li id='comments_fame'><span id='' class''>"+
            "<a href='javascript:void(0);' style='padding-top:10px !important' ><div class='alert alert-info text-center' role='alert'>"+mensaje+"</div>"+		               
            "</a>"+
               "</li>";


    }
	
	for(var i=0; i<users.size.total; i++){
		user = users['user'+i];
		list += 
			
        "<li id='comments_fame'><span id='' class''>"+
            "<a href='index.php#site/messages.php?usr="+user.id+"' style='padding-left:50px !important' Onclick='Mark(0,0,"+user.fl_mensaje_directo+",0,0,1)'>"+		
                 "<img src='"+user.avatar+"' alt='' class='air air-top-left margin-top-5' height='40' width='40' />"+
                 "<span class='from'>"+user.name+"</span>"+
                    "<time>"+user.time+"</time>"+
                     "<div class='col-sm-9 no-padding'>"+
                        "<span class='msg-body' style='max-height:110px;white-space: normal;'><span class='text-danger' Onclick='Mark(0,0,"+user.fl_mensaje_directo+",0,0,1,"+user.id+")' >"+user.unread+"&nbsp;</span></span></div>"+
                      "<div class='col-sm-1 text-align-center'>"+
                         
                     "</div>"+
          
               "</span>"+
            "</a>"+
               "</li>";









	}
	$(".notification-body").append(list);
</script>