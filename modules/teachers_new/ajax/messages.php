<?php 
	# Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recieve paramters
  $usr_interaccion = RecibeParametroNumerico('usr', True);
?>

<div class="well well-sm">
	<div class="row">
		<div class="col-xs-12">
			<div class="well well-light well-sm no-margin no-padding">
				<div class="row">
					<!-- Chat Container -->
					<div class="col-xs-12 col-sm-7 col-md-9">
						<!-- User Destination Name -->
						<div class="panel-heading" style="border-bottom: 1px solid #E3E3E3; border-right: 1px solid #E3E3E3; background-color: #FCFCFC;">
							<h6 id="name-container" class="panel-title text-center">Click on a user to start a chat</h6>
						</div>
						<!-- Message Container -->
						<div id="message-container" class="panel-body padding-10" style="overflow-y:auto; overflow-x:hidden;"></div>
						<!-- Reply Bar -->
						<form id='comment-form' method='POST' class='panel-footer' onsubmit='return false;' style="border-right:1px solid #E3E3E3;">
							<div class='input-group'>
								<input type='text' id='message' name='message' onkeypress="return Enviar(event)" class='form-control' placeholder='Write a reply...'>
					      <span class='input-group-btn'>
					        <span id='message-submit' class='btn btn-default btn-primary' style='display:inline;'>Send</span>
					      </span>
							</div>
						</form>
					</div>
					<!-- Recent User List -->
					<div class="col-xs-12 col-sm-5 col-md-3">
						<div class="panel-group smart-accordion-default" id="accordion" role="tablist" aria-multiselectable="true" style="border-left: 1px solid #E3E3E3;">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h6 class="panel-title text-center">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> <i class="fa fa-lg fa-angle-down pull-right"></i> <i class="fa fa-lg fa-angle-up pull-right"></i> Recent Chat Users </a>
									</h6>
								</div>
								<!-- List of users -->
								<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
									<div id="recent-user-container" class="panel-body" style="padding: 0 10px 10px; overflow-y:auto; overflow-x:hidden;"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	// Page Variables
	var userOri, userOriAvatar, userDest, recentUsers, recentUserContainer, messageContainer;
	userOri = <?php echo json_encode($fl_usuario); ?>;
	userOriAvatar = <?php echo json_encode(ObtenAvatarUsuario($fl_usuario)); ?>;
	userDest = <?php echo json_encode($usr_interaccion); ?>;
	recentUsers = <?php GetChatUsers($fl_usuario); ?>;
	recentUserContainer = $("#recent-user-container");
	messageContainer = $("#message-container");

	$(document).ready(function(){
		$(window).scrollTop(0);
		messagesController.setupRecentUserList(recentUserContainer, recentUsers);

		// If no default destination user is selected, display chat with the most recent user
		if(userDest == 0){ userDest = recentUsers.user0.id; }
		messagesController.setSelectedUser(userDest);
		messagesController.requestMessages(messageContainer);

		// Initiate a height for the chat window and set the scroll position
		$(window).load(function(){
			messagesController.resizeChatWindow(messageContainer);
			messagesController.resizeChatListWindow(recentUserContainer, messageContainer);
			messagesController.adjustScrollHeight(messageContainer);
		});

		// Selecting a recent chat user
		$(".recent-chat-user").on("click", function(){
			var data = $(this).data();
			messagesController.setSelectedUser(data.user);
			messagesController.requestMessages(messageContainer);
		});

		// Correct the height of the chat box on resize
		$(window).on("resize.pageWindows", function(){
			messagesController.resizeChatWindow(messageContainer);
			messagesController.resizeChatListWindow(recentUserContainer, messageContainer);
		});

		// Stop window resizing -- (covers 99% of the cases), may need rework
		$("a[href*='node.php'], a[href*='messages.php'], a[href*='profile.php'], a[href*='blog.php'], a[href*='tuition_payment.php']").on("click", function(){
			$(window).off("resize.pageWindows");
		});

		// Message reply
		$("#message-submit").on("click", function(){
			messagesController.submitReply($("#message"), userOri, userOriAvatar);
		});
	});
	function Enviar(e) {
	    //See notes about 'which' and 'key'
	    if (e.keyCode == 13) {
	        
	        messagesController.submitReply($("#message"), userOri, userOriAvatar);
	        return false;
	    }
	}
</script>