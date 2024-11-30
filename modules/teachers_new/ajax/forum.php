<?php  
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");
	require("../../common/lib/cam_forum.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  $fl_tema = RecibeParametroNumerico('theme', True);

	function GetForumTitle($fl_tema){
		$row = RecuperaValor("SELECT nb_tema FROM c_f_tema WHERE fl_tema=$fl_tema");
		$titulo = str_uso_normal($row[0]);
		echo json_encode($titulo);
	}
?>

<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
		<h1 id="forum-title" class="page-title txt-color-blueDark">
			<i class="fa-fw fa fa-home"></i> 
		</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="well well-sm">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="well well-light well-sm no-margin">
						<div class="row">
							

							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div id="stream-posts" class="stream-body"></div>
							</div>

							<!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
								
							</div> -->

						</div>
					</div>
				</div>
			</div>
		</div>

		
	</div>
</div>

<!-- <div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="well well-sm">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="well well-light well-sm no-margin no-padding">
						<div class="row">
							

							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div id="post-content" class="chat-body no-padding profile-message"></div>
							</div>

							<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
								
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

		<form id="form-main-post" method="post" class="well no-margin" onsubmit="return false;">
			<textarea id="ds_post" rows="2" class="form-control" placeholder="What are you thinking?"></textarea>
			<div class="form-group padding-bottom-10 padding-top-10">
				<button type="submit" class="btn btn-sm btn-primary pull-right" onclick='InsertPost();'>Post</button>
			</div>
		</form>
	</div>
</div> -->

<!-- <div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<form id="form-main-post" method="post" class="well no-margin" onsubmit="return false;">
			<textarea id="ds_post" rows="2" class="form-control" placeholder="What are you thinking?"></textarea>
			<div class="form-group padding-bottom-10 padding-top-10">
				<button type="submit" class="btn btn-sm btn-primary pull-right" onclick='InsertPost();'>Post</button>
			</div>
		</form>
		<div id="post-content" class="chat-body no-padding profile-message">	
		</div>
	</div>
	<span id="post-index"></span>
</div> -->

<script type="text/javascript">
	// set up page title
	var title = <?php GetForumTitle($fl_tema); ?>;
	$("#forum-title").append(title);

	// initializes the page with 5 posts
	RequestPosts(0, 5);

	$(window).scroll(function() {
   	if($(window).scrollTop() + $(window).height() == $(document).height()) {
      var no_from = $("#post-index").attr("value");
      if(typeof no_from != "undefined"){
      	RequestPosts(no_from, 5);	
      }
   	}
	});
	
	function DisplayMainPost(result){
		var content = JSON.parse(result);
		var total_posts = content.size.total;
		$("#post-index").attr("value", content.size.no_from);	// update post index value

		var post = "";
		var posts = "";
		for (var i = 0; i<total_posts; i++){
			var fl_post = content.data["fl_post"+i];
			RequestComments(fl_post);

			post += AddTimeBar(content.data["fe_post"+i]);
			posts += AddLineSeparator();

			// check if there are any embedded videos within a post
			var regVideo = /(<iframe.*?><\/iframe>)/g;
			if( regVideo.test(content.data["post"+i]) ){

				// filter out all the videos embedded within the post
				var filter_post = content.data["post"+i].match(regVideo);

				// extract url from the iframes
				var urlExpr = URLExpr();
				for (var k in filter_post){

					var url = filter_post[k].match(urlExpr);
					url = String(url).replace(/'|"/, "");					// remove trailing " and '

					var idYT = CheckYoutubeURL(url);
					if ( idYT != false ){
						var youtube_panel = 
							"<div id='youtube-well"+fl_post+"-"+idYT+"' class='well'>" +
								"<a href='javascript:void(0);' onclick='AsyncVideo(\"youtube\", \""+url+"\", \""+fl_post+"\");'>" +
									"<img src='<?php echo PATH_CAMPUS; ?>/students/img/vanas/vanasplayicon.png' class='play-button'>" +
									"<img id='youtube"+fl_post+"-"+idYT+"'>" +
								"</a>" +
							"</div>";
						
					  ObtainYouTubeThumbnail(idYT, fl_post);
						content.data["post"+i] = content.data["post"+i].replace(filter_post[k], youtube_panel);
					} 
					else {
						var idVIM = CheckVimeoURL(url);
						var vimeo_panel = 
							"<div id='vimeo-well"+fl_post+"-"+idVIM+"' class='well'>" +
								"<a href='javascript:void(0);' onclick='AsyncVideo(\"vimeo\", \""+url+"\", \""+fl_post+"\");'>" +
									"<img src='<?php echo PATH_CAMPUS; ?>/students/img/vanas/vanasplayicon.png' class='play-button'>" +
									"<img id='vimeo"+fl_post+"-"+idVIM+"'>" +
								"</a>" +
							"</div>";

						ObtainVimeoThumbnail(url, fl_post);
						content.data["post"+i] = content.data["post"+i].replace(filter_post[k], vimeo_panel);
					}
				}	
			}
			posts +=
				//"<div class='row'>" +
				//	"<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>" +
						"<div class='row'>" +
							"<div class='col-xs-12'>" +
								
								"<img class='img-avatar' src='"+content.data["avatar"+i]+"'>" +
								"<a href='#ajax/profile_view.php?profile_id="+content.data["fl_usuario_post"+i]+"'>" + 
									"<h5 class='username'>"+content.data["name"+i]+"</h5>" +
								"</a>" +
								"<a href='#ajax/profile_view.php?profile_id="+content.data["fl_usuario_post"+i]+"'>" + 
									"<small class='text-muted ultra-light'>"+content.data["fe_post"+i]+"</small>" +
								"</a>" +
								
							"</div>" +
						"</div>" +
						"<div class='row'>" +
							"<div class='col-xs-12'>" +	
								content.data["post"+i] +
								content.data["archive"+i] +
							"</div>" +
						"</div>";
				//	"</div>" +
				//"</div>";
			post += 
				"<ul>" +
					"<li id='post-"+fl_post+"' class='message'>" +
						"<img class='img-responsive' src='"+content.data["avatar"+i]+"'>" +
						"<span class='message-text'>" +
							"<a href='#ajax/profile_view.php?profile_id="+content.data["fl_usuario_post"+i]+"' class='username'>" + content.data["name"+i] +
								"<small class='text-muted pull-right ultra-light'> "+/*calculate time difference*/" </small>" +
							"</a>" + 
							content.data["post"+i] +
							content.data["archive"+i] +
						"</span>" +
						"<ul class='list-inline font-xs'>" +
							"<li><a href='' class='text-info'><i class='fa fa-thumbs-up'></i> Like</a></li>"+
						"</ul>" +
					"</li>" +
					"<li id='comment-"+fl_post+"'></li>" +
				"</ul>";
		}

		var new_post = AddTimeBar(content.data.fe_post0);
		new_post += 
			"<div class='row'>" +
				"<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>" +
					"<div class='row'>" +
						"<div class='col-xs-1'>" +			// avatar
							"<img src='"+content.data.avatar0+"'>" +
						"</div>" +
						"<div class='col-xs-11'>" +			// post content
							content.data.post0 +
							content.data.archive0 +
						"</div>" +
					"</div>" +
				"</div>" +
			"</div>";

		//$("#stream-posts").append(new_post);
		$("#stream-posts").append(posts);

		$("#post-content").append(post);
		//$("#post-content").append("<a id='show-older-posts' href='javascript:void(0);' onclick='RequestPosts(\""+no_from+"\", \""+no_to+"\")'>Older Posts</a>")
	}

	function DisplayCommentPost(result){
		var content = JSON.parse(result);
		var total_comments = content.size.total_comments;
		var total_bar = content.size.total_bar;
		var fl_post = content.fl_post;
		var fl_comentario = content.fl_comentario;

		var comments = "";

		// add the avatar bar
		if(total_bar != 0){
			comments += "<li class='message message-reply bar'><p>"+total_comments+" comments &nbsp</p>";
			for(var i = 0; i<total_bar; i++)
				comments += "<img src='"+content.bar_data["avatar"+i]+"'>";
			comments += "</li>";
		}
				
		// display comment posts
		for(var i = 0; i<total_comments; i++){
			// check if there are any embedded videos within a post
			var regVideo = /(<iframe.*?><\/iframe>)/g;
			if( regVideo.test(content.data["comment"+i]) ){

				// filter out all the videos embedded within the post
				var filter_comment = content.data["comment"+i].match(regVideo);

				// extract url from the iframes
				var urlExpr = URLExpr();
				for (var k in filter_comment){
					var url = filter_comment[k].match(urlExpr);
					url = String(url).replace(/'|"/, "");					// remove trailing " and '

					var idYT = CheckYoutubeURL(url);
					if ( idYT != false ){
						var youtube_panel = 
							"<div id='youtube-well"+fl_comentario+"-"+idYT+"' class='well'>" +
								"<a href='javascript:void(0);' onclick='AsyncVideo(\"youtube\", \""+url+"\", \""+fl_comentario+"\");'>" +
									"<img src='<?php echo PATH_CAMPUS; ?>/students/img/vanas/vanasplayicon.png' class='play-button'>" +
									"<img id='youtube"+fl_comentario+"-"+idYT+"'>" +
								"</a>" +
							"</div>";
						
					  ObtainYouTubeThumbnail(idYT, fl_comentario);
						content.data["comment"+i] = content.data["comment"+i].replace(filter_comment[k], youtube_panel);
					} 
					else {
						var idVIM = CheckVimeoURL(url);
						var vimeo_panel = 
							"<div id='vimeo-well"+fl_comentario+"-"+idVIM+"' class='well'>" +
								"<a href='javascript:void(0);' onclick='AsyncVideo(\"vimeo\", \""+url+"\", \""+fl_comentario+"\");'>" +
									"<img src='<?php echo PATH_CAMPUS; ?>/students/img/vanas/vanasplayicon.png' class='play-button'>" +
									"<img id='vimeo"+fl_comentario+"-"+idVIM+"'>" +
								"</a>" +
							"</div>";

						ObtainVimeoThumbnail(url, fl_comentario);
						content.data["comment"+i] = content.data["comment"+i].replace(filter_comment[k], vimeo_panel);
					}
				}
			}
			comments += 	
				"<li class='message message-reply'>" +
					"<img src='"+content.data["avatar"+i]+"'>" +
					"<span class='message-text'>" +
						"<a href='#ajax/profile_view.php?profile_id="+content.data["fl_usuario_com"+i]+"' class='username'>"+content.data["name"+i]+"</a>" +
						content.data["comment"+i] +	
						content.data["archive"+i] +
					"</span>" +
					"<ul class='list-inline font-xs'>" +
						"<li><span class='text-muted'>"+content.data["fe_comentario"+i]+"</span></li>" +
						"<li><a href='javascript:void(0);' class='text-info'><i class='fa fa-thumbs-up'></i> Like</a>	</li>" +
					"</ul>" +
				"</li>";
		}
		// reply text field
		comments += AddReplyBar(fl_post);

		$("#comment-"+fl_post).replaceWith(comments);
	}

	//Removes the thumbnail and replace it with a embedded video
	function AsyncVideo(type, url, post){
		
		if(type == "youtube"){
			var video = "<iframe width='480' height='360' src='"+url+"' frameborder='0' allowfullscreen></iframe>";
			var id = CheckYoutubeURL(url);

			$("#youtube-well"+post+"-"+id).empty();
			$("#youtube-well"+post+"-"+id).css("width", "520");
			$("#youtube-well"+post+"-"+id).append(video);
		} 
		else if(type == "vimeo") {
			var video = "<iframe src='"+url+"' width='480' height='270' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>";
			var id = CheckVimeoURL(url);

			$("#vimeo-well"+post+"-"+id).empty();
			$("#vimeo-well"+post+"-"+id).css("width", "520");
			$("#vimeo-well"+post+"-"+id).append(video);
		}
	}
	function ObtainYouTubeThumbnail(src, post){
		$.getJSON("https://www.googleapis.com/youtube/v3/videos?part=snippet&id="+src+"&key=AIzaSyC4iKw4yYoVY4g5SpqLdJkFJi6mBKLNaek").done(function(result){
			var video_id = result.items[0].id;

			$("#youtube"+post+"-"+video_id).before("<h4>"+result.items[0].snippet.title+"</h4>");
			$("#youtube"+post+"-"+video_id).attr("src", result.items[0].snippet.thumbnails.high.url);
		});
	}
	function ObtainVimeoThumbnail(src, post){
		$.getJSON("http://vimeo.com/api/oembed.json?url="+src).done(function(result){
			var video_id = result.video_id;
			
			$("#vimeo"+post+"-"+video_id).before("<h4>"+result.title+"</h4>");
			$("#vimeo"+post+"-"+video_id).attr("src", result.thumbnail_url);
		});
	}
	/** 
	 * URL-RegEx 1.2 
	 * @author: Some Web Guy
	 * @url: http://someweblog.com/url-regular-expression-javascript-link-shortener/
   */
	function URLExpr(){
   	return /\(?\b(?:(http|https|ftp):\/\/)?((?:www.)?[a-zA-Z0-9\-\.]+[\.][a-zA-Z]{2,4}|localhost(?=\/)|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?::(\d*))?([\/]?[^\s\?]*[\/]{1})*(?:\/?([^\s\n\?\[\]\{\}\#]*(?:(?=\.)){1}|[^\s\n\?\[\]\{\}\.\#]*)?([\.]{1}[^\s\?\#]*)?)?(?:\?{1}([^\s\n\#\[\]\(\)]*))?([\#][^\s\n]*)?\)?/gi;
	}
	/**
	 * JavaScript function to match (and return) the video Id 
	 * of any valid Youtube Url, given as input string.
	 * @author: Stephan Schmitz <eyecatchup@gmail.com>
	 * @url: http://stackoverflow.com/a/10315969/624466
	 */
	function CheckYoutubeURL(url) {
		var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
	 	return (url.match(p)) ? RegExp.$1 : false;
	}

	function CheckVimeoURL(url) {
		var p = /player\.vimeo\.com\/video\/([0-9]*)/;
		return url.match(p) ? RegExp.$1 : false; 
	}

	function AddTimeBar(fe_post){
		var bar = "<div class='timeline-seperator'><span>"+fe_post+"</span></div>";
	
		return bar;
	}

	function AddLineSeparator(){
		return "<div class='line-seperator'></div>";
	}
	function AddReplyBar(fl_post){
		var bar =
			"<div class='row'>" +
				"<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>" +
					"<div class='well reply-field'>" +
						"<div class='row'>" +
							"<div class='col-xs-1 col-sm-1 col-md-1 col-lg-1' style='padding-right: 0;'>" +
								"<img src='"+<?php GetUserAvatar($fl_usuario); ?>+"'>" +
							"</div>" +
							"<div class='col-xs-10 col-sm-10 col-md-10 col-lg-10 no-padding'>" +
								"<input id='ds_comentario-"+fl_post+"' class='form-control' placeholder='Reply something...' type='text'>" +
							"</div>" + 
							"<div class='col-xs-1 col-sm-1 col-md-1 col-lg-1' >" +
								"<button onclick='InsertComment(\""+fl_post+"\");' class='btn btn-default'>Reply</button>" +
							"</div>" +
						"</div>" +
					"</div>" +
				"</div>" +
			"</div>";
				
		return bar;
	}

	// Actualiza area de Posts
	//function MuestraPosts( ) {
	function RequestPosts(no_from, no_to) {
		var fl_tema = <?php echo $fl_tema; ?>;
		$("#show-older-posts").remove();

		$.ajax({
			type: 	'POST',
			url : 	'ajax/forum_div.php',
			data: 	'fl_tema='+fl_tema +
							'&no_from='+no_from +
							'&no_to='+no_to
		}).done(function(result){
			DisplayMainPost(result);
		});
	}

	function RequestComments(fl_post) {
		$.ajax({
			type: 	'POST',
			url : 	'ajax/forum_comment_div.php',
			data: 	'fl_post='+ fl_post,
		}).done(function(result){
			DisplayCommentPost(result);
		});
	}

	// Inserta el post nuevo y actualiza area de Posts
	function InsertPost() {  
		var fl_tema = <?php echo $fl_tema; ?>;
		var fl_usuario = <?php echo $fl_usuario; ?>;
  	
	  var ds_post = $('#ds_post');

	  bValid = checkLength(ds_post);
	  if(bValid) {
	  	$.ajax({
	  		type: 'POST',
	  		url : 'ajax/forum_div.php',
	  		data: 'fl_usuario='+fl_usuario+
	  					'&fl_tema='+fl_tema+
	  					'&ds_post='+encodeURIComponent(ds_post.val())+
	  					'&archivo='+''
	  	}).done(function(result){
	  		$("#post-content").empty();
	  		ds_post.val("");
	  		DisplayMainPost(result);
	  	});
  	}
  	else
  		alert('Please enter text for your post.');
	}

	// Inserta un comentario nuevo y actualiza area de comentarios del Posts
	function InsertComment(fl_post) {
		var fl_usuario = <?php echo $fl_usuario; ?>;

	  var ds_comentario = $('#ds_comentario-'+fl_post);

	  bValid = checkLength(ds_comentario);
	  if(bValid) {
	    $.ajax({
	      type: 'POST',
	      url : 'ajax/forum_comment_div.php',
	      data: 'fl_usuario='+fl_usuario +
	            '&fl_post='+fl_post+
	            '&ds_comentario='+encodeURIComponent(ds_comentario.val())+
	            '&archivo='+''
	    }).done(function(result){
	    	$("#post-"+fl_post).nextAll().remove();
	    	$("#post-"+fl_post).after("<li id='comment-"+fl_post+"'></li>");
	    	DisplayCommentPost(result);
	    });
	  }
	  else
	    alert('Please enter your comment.');
	}

	// Validaciones
	function checkLength(o) {
	  if(o.val().length > 0)
	    return true;
	  else
	    return false;
	}

</script>