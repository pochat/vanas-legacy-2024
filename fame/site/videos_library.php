<?php
# Librerias
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);

# Recibe parametros
$clave = RecibeParametroNumerico('clave',True);
$fg_error = RecibeParametroNumerico('fg_error'); 

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermisoSelf(FUNC_SELF)) {
MuestraPaginaError(ERR_SIN_PERMISO);
exit;
}

# Intituto del usuario
$fl_instituto = ObtenInstituto($fl_usuario);
$fl_perfil = ObtenPerfilUsuario($fl_usuario);
  
# Recibe parametros
$clave = RecibeParametroNumerico('clave');//fl_programa_
$fl_programa = RecibeParametroNumerico('pro');
$no_grado = RecibeParametroNumerico('grade');
$accion = RecibeParametroNumerico('accion'); // 1 muestra videos 2 elimina video 3 guarda titulo del video
$fg_fame = RecibeParametroNumerico('fg_fame');
$fg_eliminar=RecibeParametroNumerico('fg_eliminar');


if($fg_eliminar==1){

    $fl_video_contenido_sp=RecibeParametroNumerico('fl_video_contenido_sp');
    $fl_vid_contet_temp=RecibeParametroNumerico('fl_vid_contet_temp');

    $Query="SELECT no_orden FROM  k_vid_content_temp WHERE fl_vid_contet_temp= $fl_vid_contet_temp ";
    $row=RecuperaValor($Query);
    $no_orden=$row['no_orden'];

    EjecutaQuery("DELETE FROM k_video_contenido_sp WHERE fl_video_contenido_sp=$fl_video_contenido_sp ");
    EjecutaQuery("DELETE FROM k_vid_content_temp WHERE fl_vid_contet_temp=$fl_vid_contet_temp ");

    #Elimnamos toda la carpeta.
    $path_eliminar=$_SERVER[DOCUMENT_ROOT]."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido_sp."/video_".$no_orden."";                   
    

    function delete_files($target) {
		if(is_dir($target)){
			$files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

			foreach( $files as $file ){
				delete_files( $file );      
			}

			rmdir( $target );
		} elseif(is_file($target)) {
			unlink( $target );  
		}
	}
    delete_files($path_eliminar);


}




?>

<style>
    [data-progressbar-value]::after{
            content: ''
        }
    [data-progressbar-value]::before{
            content: ''
        }

    .blink_me {
     animation: blinker 1.3s linear infinite;
    }

    @keyframes blinker {
      50% {
        opacity: 0;
      }
    }
</style>

<?php

echo"
<script>
	//Funcion para mostrar los videos en modal(videos_library).
	function MuestraVideos(fl_video_contenido_sp,fl_vid_contet_temp){

		 $.ajax({
             type: 'POST',
             url: 'site/show_video_student_library.php',
             data: 'fl_vid_contet_temp='+fl_vid_contet_temp+
                   '&fl_usuario=$fl_usuario'+
			       '&fl_video_contenido_sp='+fl_video_contenido_sp,
             async: true,
             success: function (html) {
                 $('#modal_videos_library').html(html);
             }
         });	
	}
	function DeleteVideo(fl_video_contenido_sp,fl_vid_contet_temp){
		
		 var answer = confirm('".ObtenEtiqueta(2641)."');
		if(answer) {
		 $.ajax({
             type: 'POST',
             url: 'site/videos_library.php',
             data: 'fl_vid_contet_temp='+fl_vid_contet_temp+
                   '&pro=$fl_programa'+
                   '&clave=$clave'+
			       '&fg_eliminar=1'+
			       '&fl_video_contenido_sp='+fl_video_contenido_sp,
             async: true,
             success: function (html) {
                 $('#muestra_student_library_videos').html(html);
				 
             }
         });	
		
		}
		
	}
</script>
";


  #Verificamos que exista
  $exists = RecuperaValor("SELECT fl_vid_contet_temp FROM k_vid_content_temp WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa  AND fg_fame='1'");
  $fame_url = ObtenConfiguracion(116);
  
  $Queryx ="SELECT a.fl_video_contenido_sp,b.fl_vid_contet_temp,a.cl_pagina_sp, a.ds_progreso,nb_archivo,a.fl_programa_sp,a.ds_title_vid,no_orden,a.fe_creacion, ds_duracion,b.fl_usuario  
                FROM k_video_contenido_sp a
                JOIN k_vid_content_temp b ON a.fl_vid_contet_temp =b.fl_vid_contet_temp
                WHERE cl_pagina_sp=$clave AND fl_programa_sp=$fl_programa
                ORDER BY b.fl_vid_contet_temp DESC "; 
   $rsx = EjecutaQuery($Queryx);
   $tot_reg = CuentaRegistros($rsx);
   if(!empty($tot_reg)){
	   
       for($i=0;$rowx=RecuperaRegistro($rsx);$i++){
           $fl_video_contenido_sp=$rowx['fl_video_contenido_sp'];
           $fl_vid_contet_temp=$rowx['fl_vid_contet_temp'];
           $cl_pagina_sp=$rowx['cl_pagina_sp'];
           $ds_progreso=$rowx['ds_progreso'];
           $nb_archivo=$rowx['nb_archivo'];
           $fl_programa_sp=$rowx['fl_programa_sp'];
           $ds_title_vid=$rowx['ds_title_vid'];
           $no_orden=$rowx['no_orden']; 
           $fl_usuario=$rowx['fl_usuario'];
           $fl_instituto = ObtenInstituto($fl_usuario);
           $ruta_img = ObtenConfiguracion(116)."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido_sp."/video_".$no_orden."/img_1.png";  
		   $fe_creacion=$rowx['fe_creacion'];
           $ds_duracion=$rowx['ds_duracion'];
           
           #el codigo originl asi lo manda, para no alterar nada le mandamos el orden./clave por el cl_programa
           $fl_video_contenido=$no_orden;
           $fg_fame=1;
           $clave=$cl_pagina_sp;//fl_programa_sp/cl_pagina_sp
           $fl_programa=$fl_programa_sp;//fl_progama_sp
           $no_grado=2;

           #Damos formato de fecha
           $p_fecha=strtotime('+0 day',strtotime($fe_creacion));
           $p_fecha= date('Y-m-d H:i:s',$p_fecha);
           $date = date_create($p_fecha);
           $fecha=date_format($date,'l F j, Y g:i a');

           if($ds_progreso<100){
               $display_duration="none";
               $status_completed="none";
               $status_encoding="inline";
           }
           if($ds_progreso==100){
               $display_duration="inline";
               $status_completed="inline";
               $status_encoding="none";
           }

?>            
		<div class="col-md-3 padding-top-15" >
			<p class="text-left"><strong><?php echo ObtenEtiqueta(1759);?>:</strong><br><small><?php echo $fecha;?></small><span style="font-size:19px;float:right;cursor: pointer;" onclick="DeleteVideo(<?php echo $fl_video_contenido_sp;?>,<?php echo $fl_vid_contet_temp;?>)"><i class="fa fa fa-trash-o" aria-hidden="true"></i></span></p>			
			<input type='hidden' id='total_convertido<?php echo $fl_video_contenido_sp;?>' name="total_convertido<?php echo $fl_video_contenido_sp;?>" value='<?php echo $ds_progreso;?>' >
            <input type='hidden' id='fl_video_contenido_sp_<?php echo $fl_video_contenido_sp;?>' name="fl_video_contenido_sp_<?php echo $fl_video_contenido_sp;?>" value='<?php echo $fl_video_contenido_sp;?>' >
            <div>
			    <a onclick="MuestraVideos(<?php echo $fl_video_contenido_sp;?>,<?php echo $fl_vid_contet_temp;?>);" data-toggle="modal" data-target="#myModalVideosStudentLibrary">
					    <img src="<?php echo ObtenConfiguracion(116)."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido_sp."/video_".$no_orden."/img_1.png?$fl_video_contenido_sp";?>" name="imgvideo_<?php echo $fl_video_contenido_sp;?>" id="imgvideo_<?php echo $fl_video_contenido_sp;?>" style="height: 141px;" class="superbox-img">
			    </a>
                <span class="label label-info bg-color-darken pull-right" style="position:relative;bottom:30px;right:5px;font-size:12px;display:<?php echo $display_duration;?>;"id="time_duration<?php echo $fl_video_contenido_sp;?>"name="time_duration<?php echo $fl_video_contenido_sp;?>" ><?php echo $ds_duracion;?></span>
               
            </div>
			
                <!--<h6 class="text-left blink_me" id="processing_<?php echo $clave;?>" style='font-size:12px;'><strong><?php echo ObtenEtiqueta(2632);?></strong></h6>-->
                <h6 class="text-left blink_me" id="encoding_video_<?php echo $fl_video_contenido_sp;?>" style='font-size:12px;display:<?php echo $status_encoding;?>;'><strong><?php echo ObtenEtiqueta(2634);?></strong></h6>
				<h6 class="text-left " id="completed_video_<?php echo $fl_video_contenido_sp;?>" style='font-size:12px;display:<?php echo $status_completed;?>;'><strong><?php echo ObtenEtiqueta(2636);?></strong></h6>
				

			<!--<h6 class="text-left " id="completed_video_<?php echo $fl_video_contenido_sp;?>" style='font-size:12px;'><?php echo ObtenEtiqueta(2636);?></h6>-->
				<div class='progress' style="height: 19px;" data-progressbar-value='<?php echo $ds_progreso;?>' id='grl_progress_<?php echo $fl_video_contenido_sp;?>'>
						<div class='progress-bar' id='progress_hls_<?php echo $fl_video_contenido_sp;?>'>
						   <?php echo $ds_progreso;?>%
						</div>
				</div>
			<br>
		    <a href='form-x-editable.html#' id='div_title_vid_<?php echo $fl_video_contenido_sp;?>' data-type='text' data-pk='1' data-original-title='<?php echo ObtenEtiqueta(2201);?>'><?php echo $ds_title_vid;?></a>
	
          
		  
		  
			<script>     
				//editables
				$('#div_title_vid_<?php echo $fl_video_contenido_sp;?>').editable({

					url: 'site/update_name_video_library.php',
					type: 'text',
					pk: '<?php echo $fl_video_contenido;?>',
					name: '<?php echo $fl_video_contenido_sp;?>',
					title: '<?php echo ObtenEtiqueta(2201);?>',
					success: function (html) {
						$('#div_title_vid_<?php echo $fl_video_contenido_sp;?>').html(html);
					}
				});
			 
		    
		       //para saber el proceso por video.
			    function VerificaProgresoVideo_<?php echo $fl_video_contenido_sp;?>() {

			        var progreso_vid = document.getElementById("total_convertido<?php echo $fl_video_contenido_sp;?>").value;
			        var fl_video_contenido_sp = "<?php echo $fl_video_contenido_sp;?>";
			        var no_orden = "<?php echo $no_orden;?>";
			        var fl_programa_sp = "<?php echo $fl_programa_sp;?>";
			        $.ajax({
			            type: 'GET',
			            url: 'site/student_library_progreso_comando.php',
			            data: 'fl_programa_sp='+fl_programa_sp+
                              '&no_orden='+no_orden+
                              '&fl_video_contenido_sp='+fl_video_contenido_sp+
                              '&fg_creado_fame=1'
			        }).done(function (result) {
			                    var content;
			                    content = JSON.parse(result);
                                
                                //si el progreso del video es 100 detenemos la funcion que verifica progreso.
			                    if (content.progress == 100) {
			                        clearInterval(intervalo);

			                        $('#encoding_video_<?php echo $fl_video_contenido_sp;?>').css('display', 'none');
			                        $('#completed_video_<?php echo $fl_video_contenido_sp;?>').css('display', 'inline');
			                    }
                                //pinta barra de progreso.
			                    if (content.progress <= 100) {	                        
			                        $('#grl_progress_<?php echo $fl_video_contenido_sp;?>').attr('data-progressbar-value', content.progress);
			                        $('#progress_hls_<?php echo $fl_video_contenido_sp;?>').empty().append(content.progress + '%');
			                        $('#time_duration<?php echo $fl_video_contenido_sp;?>').css('display','inline');
			                        $('#time_duration<?php echo $fl_video_contenido_sp;?>').empty().append(content.time_duration);
			                        $('#total_convertido<?php echo $fl_video_contenido_sp;?>').empty().val(content.progress);
			                    }
			                   if (content.progress < 100) {
			                       $('#encoding_video_<?php echo $fl_video_contenido_sp;?>').css('display', 'inline');
			                       
	                          
			                           $('#imgvideo_<?php echo $fl_video_contenido_sp;?>').attr('src', '' + content.ruta_thumbnail_video + '');
			                          
			              

			                   }
                               

			        });



			    }
                
                
			    var total_convertido = "<?php echo $ds_progreso;?>";
			    if (total_convertido < 100) {
			        var intervalo = setInterval(function () {

			            VerificaProgresoVideo_<?php echo $fl_video_contenido_sp;?>();

			        },
				    2000);



			    }
			   

		  </script>



		</div>
<?php 
       }
   }
?>
<!-----Modal para mostrar los videos----->				
<div class="modal fade" id="myModalVideosStudentLibrary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"   >
	<div class="modal-dialog">
		<div class="modal-content" id="modal_videos_library">
			<!--		
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title" id="myModalLabel">Article Post</h4>
			</div>
			<div class="modal-body">
			
				<div class="row">
					<div class="col-md-12">
						
					</div>
				</div>

			</div>
			-->
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!---Cierra Modal que muestra videos----->



