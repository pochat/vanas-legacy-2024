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

<ul class="notification-body"></ul>

<script type="text/javascript">
	var users, user, list;
	users = <?php GetChatUsers($fl_usuario); ?>;

	list = "";
	for(var i=0; i<users.size.total; i++){
		user = users['user'+i];
		list += 
			"<li>" +
				"<span>" + 
					"<a href='#site/messages.php?usr="+user.id+"' class='msg'>" +
						"<img src='"+user.avatar+"' class='air air-top-left margin-top-5' width='40' height='40'>" +
						"<span class='from'>"+user.name+"</span>" +
						"<time>"+user.time+"</time>" +
						"<span class='subject'><span class='text-danger'>"+user.unread+"&nbsp;</span></span>" +		
						"<span class='msg-body'></span>" +
					"</a>" +
				"</span>" +
			"</li>";
	}
	$(".notification-body").append(list);
</script>