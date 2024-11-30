<?php 
	# Libreria de funciones
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  $i = RecibeParametroNumerico('actual');
  $muestra = RecibeParametroNumerico('muestra');
  $Query = "SELECT fl_playlist FROM c_playlist ORDER BY fl_playlist DESC LIMIT 1";
  $newplalist = RecuperaValor($Query);
  $newpl = $newplalist[0]+1;

  if($muestra == 1){
  
?>
<div id ="MtraDivInd_<?php echo "_$i"; ?>" class="popover fade bottom in" style="top: 33px; left: -50px; display: block; width:300px; z-index: 1;"><div class="arrow" style="left: 90%;"></div><h3 class="popover-title" style="display: none;"></h3>
  <div class="text-right" style="padding:0 5px 0 25px;">
    <a id='Cerrar_<?php echo $i;?>'  href="javascript:DespliegaLista_<?php echo "_$i"; ?>(0, 0,<?php echo $fl_programa_sp;?>);"><i class="fa fa-times" style="color:#9aa7af; font-size:10px;" aria-hidden="true"></i></a>
  </div> <!-- CierraDivInd_<?php #echo "_$i"; ?>(); -->
  <div class="popover-content" style="padding-top:0px"><div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; padding-bottom:7px;">
      <p style="margin: 0 0 1px;">Add course to:</p>
    <div class="icon-addon addon-sm">
      <input placeholder="<?php echo ObtenEtiqueta(1259); ?>" class="form-control" id="busca_playlist_<?php echo "_$i"; ?>" type="text"> <!-- onkeypress="busca_playlist_<?php // echo "_$i"; ?>(this.value, 12);" -->
      <label for="<?php echo ObtenEtiqueta(1259); ?>" class="glyphicon glyphicon-search" rel="tooltip" title="" data-original-title="<?php echo ObtenEtiqueta(1259); ?>"></label>
    </div>
    <div class="bs-example">
      <div id="muestra_prueba_<?php echo "_$i"; ?>" style="padding-top:3px;">
      <div class="bs-example" style="height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;">
        <div id="div_prueba_<?php echo "_$fl_programa_sp"; ?>"> </div>
      </div> 
    </div>
    <hr style="margin-top: 3px; margin-bottom: 3px;">
    <center>
    <div id="mtit_<?php echo "_$i"; ?>" style="display: block;  text-align:left; padding-left:7px;">
      <a href="javascript:AddPlaylistInd_<?php echo "_$i"; ?>(true)"><span style="color:#9aa7af; font-style: italic;"><i class="fa fa-plus-square-o"></i> <?php echo ObtenEtiqueta(1260); ?></span></a>
    </div>
      <div id="aa_<?php echo "_$i"; ?>" style="display: none;">
        <div class="form-group">
          <input class="form-control" name="new_playlist_<?php echo "_$i"; ?>" id="new_playlist_<?php echo "_$i"; ?>" placeholder="Playlist name" onkeyup="BtnGuardar_<?php echo "_$i"; ?>();" type="text">
          <br>
          <div style="float: right;">
          <a class="btn btn-danger btn-xs" onclick="AddPlaylistInd_<?php echo "_$i"; ?>(false)"><?php echo ObtenEtiqueta(1261); ?></a>
          <a class="btn btn-primary btn-xs disabled" href="javascript:checkreq2(<?php echo($fl_programa_sp) ?>, <?php echo($i) ?>);" id="Ccl_<?php echo "_$i"; ?>"><?php echo ObtenEtiqueta(1262); ?></a>
          <!-- <a class="btn btn-primary btn-xs disabled" href="javascript:GuardaPlaylist_<?php //echo "_$i"; ?>(<?php //echo $fl_programa_sp; ?>); AddPlaylistInd_<?php //echo "_$i"; ?>(false); tt(); NewPlaylistCourse_<?php //echo "_$fl_programa_sp"; ?>();" id="Ccl_<?php //echo "_$i"; ?>"><?php //echo ObtenEtiqueta(1262); ?></a> --> <!-- busca_playlist_<?php #echo "_$i"; ?>(); -->
          </div>
        </div>
      </div>
    </center>		
  </div>
  </div></div></div>
  
  <script>
  $(document).ready(function(){
    $.ajax({ 
      type: 'POST',
      url : 'site/recupera_playlist.php',
      async: false,
      data: 'valor=add_curso_playlist'+
            '&extra=<?php echo "$fl_programa_sp"; ?>',
      success: function(data) {
        $('#div_prueba_<?php echo "_$fl_programa_sp"; ?>').html(data);
      }
    });
    
    // ### Busqueda de playlist
    
    function hide_divs_<?php echo "_$i"; ?>(search) {
      var search = search.toLowerCase();
      $(".nb_playlist_<?php echo "_$fl_programa_sp"; ?>").hide(); // hide all divs
      $('.nb_playlist_<?php echo "_$fl_programa_sp"; ?> > div[class*="'+search+'"]').parent().show(); // show the ones that match
		}
    
    function show_all_<?php echo "_$i"; ?>() {
      $(".nb_playlist_<?php echo "_$fl_programa_sp"; ?>").show() // ant = demo-icon-font - new = nb_playlist Id a buscar en los div de nombres playlist
    }
    
    $("#busca_playlist_<?php echo "_$i"; ?>").keyup(function() {
      var search = $.trim(this.value);
      if (search === "") {
        show_all_<?php echo "_$i"; ?>();
      }
      else {
        hide_divs_<?php echo "_$i"; ?>(search);
      }
		});
    
  });
  </script>
  <?php
  }
  else{
    echo "";
  }
  ?>