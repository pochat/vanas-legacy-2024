<?php 

 # Verifica que exista una sesion valida en el cookie y la resetea
 $fl_usuario = ValidaSesion(False,0, True);
 # Intituto del usuario
 $fl_instituto = ObtenInstituto($fl_usuario);
 $fl_perfil = ObtenPerfilUsuario($fl_usuario);
 ?>
<style>      
	#lib_scho_fame .dropzone .dz-default.dz-message{
	background-image: url(<?php echo PATH_HOME;?>/bootstrap/img/dropzone/spritemap_videos.png);
	}
	[data-progressbar-value] {
    margin-top: 5px!important;
	}
</style>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-lg-4 col-md-4"> &nbsp; </div>    
	<div class="col-xs-12 col-sm-12 col-lg-4 col-md-4">     
		<div class="smart-form">
			<?php FAMEInputText(ObtenEtiqueta(2359),'title_vid_lib_scho_fame', (isset($title_video)?$title_video:NULL), false); ?>
		</div> 
	</div>
	<div class="col-xs-12 col-sm-12 col-lg-4 col-md-4"> &nbsp; </div> 
</div>

<div class="row">
	<div class="col-md-12">
		<div class="dropzone" id="lib_scho_fame" style="min-height: 145px; padding:10px 0px 0px 20px">
			<input type="hidden" name="nb_video" id="nb_video" value="">
		</div>
	</div>
</div>



<br><br>
<div class="row" id="muestra_student_library_videos"></div>




<script>
	function Videos(){

		 var cla=document.getElementById("fl_programa_nuevo_creado").value;												       
		 var pro=document.getElementById("fl_programa_nuevo_creado").value;
		 var fame=1;
		 var fl_usuario=<?php echo $fl_usuario;?>;
		 var fg_creado_instituto=1;
		 var titulo_video=document.getElementById("title_vid_lib_scho_fame").value;
		 
		 $.ajax({
			 type: "POST",
			 url : "site/videos_library.php",
			  data: "clave="+cla+"&pro="+pro+"&accion=1&fg_fame="+fame+"&fg_creado_instituto="+fg_creado_instituto+"&fl_user="+fl_usuario,
			  success: function(html){
				  $('#muestra_student_library_videos').empty().append(html);
			  }
			});
	}
	//Videos();
</script>





<script type="text/javascript">
// DO NOT REMOVE : GLOBAL FUNCTIONS!
$(document).ready(function() {
  // pageSetUp();
  Dropzone.autoDiscover = false;
  var progress_lecc1 = $("#progress_leccionlib_scho_fame");
  $("#lib_scho_fame").dropzone({
	url: "site/vid_library.php",
	addRemoveLinks : true,
	maxFilesize: 1024,            
	acceptedFiles: ".mov, .MOV, .mp4, .MP4, .avi, .AVI, .3gp, .3GP, .wmv, .WMV, .flv, .FLV, .mpg, .MPG, .webm, .WEBM, .mkv, .MKV",
	// Solo permite guardar un registro
	maxFiles: 1,           
	accept: function(file, done) {
	  var filen = file.name;
	  var active_title = "";
	  var elem_title = $("#title_vid_lib_scho_fame");
	  var val_title = elem_title.val();
	  // Si esta activado el campo de titulo debe agregar texto              
	  if(active_title==1 && val_title.length==0){
		$(".dz-error-mark").css("opacity","0.8");
		$(file.previewElement).find(".dz-error-message").text("You must add a title of the video").css("opacity", "0.8").css("margin-left", "100px");
		//$("#div_title_v_lib_scho_fame").addClass("state-error");
		//$("#msg_err_lib_scho_fame").removeClass("hidden");
		 this.removeFile(file);
	  }
	  else{
		if (filen.indexOf(" ")>0) {
		  $(".dz-error-mark").css("opacity","0.8");
		  $(file.previewElement).find(".dz-error-message").text("<?php echo ObtenEtiqueta(2635);?>").css("opacity", "0.8").css("margin-left", "100px");
		}
		else {                
		  done(); 
		}
		//$("#div_title_v_lib_scho_fame").removeClass("state-error");
		//$("#msg_err_lib_scho_fame").addClass("hidden");
	  }
	},
	init: function() {
	  this.on("error", function(file, message) {                    
	    this.removeFile(file); 
	  });
	  this.on("beforeSend", function(){   
	      this.on('error', function(file, message) {                                                                     
	          this.removeFile(file); 
	      });
	      this.on('beforeSend', function(){ 
	          $('#upload_videos').modal('toggle');                     
	      });
		//$("#upload_videoslib_scho_fame").modal("toggle");
		//progress_lecc1.empty().width("0%").append("0%");
		//$(".dz-progress").hide();
	  });
	  // Proceso del upload
	  this.on("uploadprogress", function (file, progress, bytesSent){
		var progress2 = Math.round(progress);
		progress_lecc1.empty().width(progress2 + "%").append(progress2 + "%");
		//$(".dz-progress").hide();
	  });
	  // Enviamos la clave
	  this.on("sending", function (file, xhr, formData, e) {               
		var elem_title = $("#title_vid_lib_scho_fame");
		var clave=document.getElementById("fl_programa_nuevo_creado").value;
		var fl_programa=document.getElementById("fl_programa_nuevo_creado").value;
		var val_title = elem_title.val();
		formData.append("title_video", val_title); 
		formData.append("clave", clave);
		formData.append("fl_programa", fl_programa);
		formData.append("usuario", "<?php echo $fl_usuario;?>");
		formData.append("fg_fame", "1");
		formData.append("fg_creado_teacher", "1");
	  });
	  this.on("processing", function(file){
		// $("#upload_videoslib_scho_fame").modal("toggle");
	  });
	  this.on("success", function(file, response) {

		 // alert('entro');
		  var obj = jQuery.parseJSON(response)
		  // agregamos el tipo del archivo
		  $("#fg_tipo_video").val(obj.valores.type);
		  $("#fg_upload_videos").val(1);
		  // Guardamos
		  var save = "";
		  var active_title = "";
		  var elem_title = $("#title_video");
		  if(save==true)
			  document.datos.submit();


		  // Si require de titulo una vez subido limpia
		  if(active_title==1){
			elem_title.val("");
			this.removeFile(file);
		  }
		  //rediriggimos la tab para presenar videos.
		 // document.getElementById('muetra_vid').click();
		  
		  
		  
	  });
	},     
	complete: function(file, result){
		if(file.status == "success"){
			 this.removeFile(file);
			 $("#title_vid_lib_scho_fame").val("");
			  //alert('entro2');
		    document.getElementById("nb_video").value = file.name;
		   
		    //Hace el llamado a los videos para ver el progreso.
		    Videos();




	  }
	  var progress3 = "100%";
	  progress_lecc1.empty().width(progress3).append(progress3);
	  //$("#upload_videoslib_scho_fame").modal("toggle");                               
	},                  
	removedfile: function(file, serverFileName){
	  var name = file.name;                   
	  var element;
	  (element = file.previewElement) != null ? 
	  element.parentNode.removeChild(file.previewElement) : 
	  false;
	}
  });
})
</script>
													