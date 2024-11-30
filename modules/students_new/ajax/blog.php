<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Receive parameters
  $fl_blog = RecibeParametroNumerico('blog', True);

	function NewsQuery($fl_usuario){
		$fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
		$Query  = "SELECT fl_blog, ds_titulo, ds_resumen, ds_ruta_imagen, DATE_FORMAT(fe_blog, '%M %e, %Y') ";
		$Query .= "FROM c_blog ";
		$Query .= "WHERE fg_alumnos='1' ";
		$Query .= "AND fe_blog <= $fe_actual ";
		$Query .= "AND DATE_ADD(fe_blog, INTERVAL ".ObtenConfiguracion(18)." DAY) >= $fe_actual ";
		$Query .= "ORDER BY fe_blog DESC";
		$rs = EjecutaQuery($Query);

		for($i=0; $row=RecuperaRegistro($rs); $i++){
			$fl_blog = $row[0];
			$ds_titulo = str_uso_normal($row[1]);
			$ds_resumen = html_entity_decode($row[2]);
			$ds_resumen =str_replace("nbsp;"," ",$ds_resumen);
            $ds_resumen =str_replace("&"," ",$ds_resumen);
            $ds_resumen =str_replace(' #39;',"'",$ds_resumen);
            
			$ds_ruta_imagen = str_ascii($row[3]);
			if(!empty($ds_ruta_imagen)){
				$ds_ruta_imagen = "<img src='".SP_THUMBS."/news/$ds_ruta_imagen'>";
			} else {
				$ds_ruta_imagen = "<img src='".SP_IMAGES."/".S_NEWS_THUMB_DEF."'>";
			}
			$fe_blog = $row[4];

			$row2 = RecuperaValor("SELECT COUNT(1) FROM k_not_blog WHERE fl_blog=$fl_blog AND fl_usuario=$fl_usuario");
			if($row2[0] > 0){
				$ds_notificar = "(Unread)";
			}	else {
				$ds_notificar = "";
			}

			$result["blog".$i] = array(
				"fl_blog" => $fl_blog,
				"title" => $ds_titulo,
				"abstract" => $ds_resumen,
				"image" => $ds_ruta_imagen,
				"time" => $fe_blog,
				"unread" => $ds_notificar
			);

		}
		$result["size"] = array("total" => $i);
		echo json_encode((Object)$result);
		
	}
?>

<div class="row">
	<div class="col-xs-12">
		<div class="well well-light no-margin padding-10">
			<div id="news-container" class="well well-light no-margin"></div>
		</div>
	</div>
</div>

<!-- Setup an empty modal for any news post to use -->
<div class="modal fade" id="news-modal-container" tabindex="-1" role="dialog" aria-labelledby="news-title" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content"></div>
	</div>
</div>

<script type="text/javascript">
	var newsContainer, modalContainer, news, blogPost, blogs;
	newsContainer = $("#news-container");
	modalContainer = $("#news-modal-container");
	news = <?php NewsQuery($fl_usuario); ?>;
	blogPost = <?php echo json_encode((int) $fl_blog); ?>;

	// If there is a given blog post, open the modal
	if(blogPost){
		modalContainer.on("show.bs.modal", function(event){
			$(this).find(".modal-content").load("ajax/blog_detail.php", "blog="+blogPost);
		});
		modalContainer.modal("show");
		modalContainer.off("show.bs.modal");
	}

	// Setup page, list of news posts
	blogs = "";
	if(news.size.total > 0){
		for(var i=0; i<news.size.total; i++){
			var blog = news["blog"+i];
			blogs +=
				"<li class='media' style='list-style:none;'>"+
					"<a class='pull-left' data-toggle='modal' href='ajax/blog_detail.php?blog="+blog.fl_blog+"' data-target='#news-modal-container'>"+blog.image+"</a>"+
					"<div class='media-body'>"+
						"<span id='unread-"+blog.fl_blog+"' class='text-danger fg-notice'>"+blog.unread+" </span>"+
						"<span class='lead'>"+
							"<strong><a data-toggle='modal' href='ajax/blog_detail.php?blog="+blog.fl_blog+"' data-target='#news-modal-container'>"+blog.title+"</a></strong>"+
						"</span>"+
						" [<a data-toggle='modal' href='ajax/blog_detail.php?blog="+blog.fl_blog+"' data-target='#news-modal-container' class='font-xs'>More Details</a>]"+
						blog.abstract+
						"<span class='text-muted'>"+blog.time+"</span>"+
					"</div>"+
				"</li>";
		}
		newsContainer.append(blogs);	
	} else {
		blogs += "<div class='jumbotron text-center' style='background-color:#ffffff'>"+"<h1>There are no School News</h1>"+"</div>";
		newsContainer.append(blogs);
	}
	
	// Empty out the modal content everytime the modal is closed
	$('body').on('hidden.bs.modal', '.modal', function () {
  	$(this).removeData('bs.modal').find('.modal-content').empty();
	});

	// Updates the (Unread) flag whenever a school news is clicked
	$("a[data-toggle]").on("click", function() {
		// Find out which blog number's unread message to delete
		var blogNumber = $(this).attr("href").split("=")[1];

		$("#unread-"+blogNumber).text("");
	});
</script>