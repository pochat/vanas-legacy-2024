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

  $muestra = RecibeParametroNumerico('muestra');
  
  if($muestra == 1){

?>
  <div id="mi_prueba" class="popover fade bottom in"  style="top: 56px; left: -140px; display: block; width:300px;"><div class="arrow" style="left: 70%;"></div><h3 class="popover-title" style="display: none;"></h3>
    <div class="text-right" style="padding:0 5px 0 25px;">
      <a href="javascript:DespliegaListaPri(0);"><i class="fa fa-times" style="color:#9aa7af; font-size:10px;" aria-hidden="true"></i></a>
    </div>
    <div class="popover-content" style="padding-top:0px"><div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; padding-bottom:7px;">
        <div class="icon-addon addon-sm">
          <input placeholder="<?php echo ObtenEtiqueta(1259); ?>" class="form-control" id="busca_playlist" type="text"> <!-- onkeypress="busca_playlist(this.value, 0);" -->
          <label for="<?php echo ObtenEtiqueta(1259); ?>" class="glyphicon glyphicon-search" rel="tooltip" title="" data-original-title="<?php echo ObtenEtiqueta(1259); ?>"></label>
        </div>
        <div class="bs-example">
          <div id="muestra_prueba" style="padding-top:3px;">
            <div class="bs-example" style="height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;">
              <div id="div_prueba_test">                   
              </div> 
            </div> 
          </div>
          <hr style="margin-top: 3px; margin-bottom: 3px;">
          <center>
            <div id="mtit" style="display: block;  text-align:left; padding-left:7px;">
              <a href="javascript:AddPlaylist(true)"><span style="color:#9aa7af; font-style: italic;"><i class="fa fa-plus-square-o"></i> <?php echo ObtenEtiqueta(1260); ?></span></a>
            </div>
            <div id="aa" style="display: none;">
              <div class="form-group">
                <input class="form-control" name="new_playlist" id="new_playlist" placeholder="Playlist name" onkeyup="BtnGuardar();" type="text">
                <br>
                <div style="float: right;">
                  <a class="btn btn-danger btn-xs" onclick="AddPlaylist(false)"><?php echo ObtenEtiqueta(1261); ?></a>
                  <a class="btn btn-primary btn-xs disabled" href="javascript:GuardaPlaylist(0); AddPlaylist(false); DespliegaListaPri(1);" id="Ccl"><?php echo ObtenEtiqueta(1262); ?></a> <!-- busca_playlist(); -->
                </div>
              </div>
            </div>
          </center>		
        </div>
      </div></div>
  </div>
  <script>
  $(document).ready(function(){
    $.ajax({
      type: 'POST',
      url : 'site/recupera_playlist.php',
      async: false,
      data: 'valor=actualiza_lista',
      success: function(data) {
        $('#div_prueba_test').html(data);
      }
    });  
    
    function hide_divs(search) {
      var search = search.toLowerCase();
      $(".nb_playlist").hide(); // hide all divs
      $('.nb_playlist > div[class*="'+search+'"]').parent().show(); // show the ones that match
      $(".nb_playlist_doc").hide();
      $('.nb_playlist_doc > div[class*="'+search+'"]').parent().show();
		}
    
    function show_all() {
      $(".nb_playlist_doc").show();
      $(".nb_playlist").show() // ant = demo-icon-font - new = nb_playlist Id a buscar en los div de nombres playlist
    }
    
    $("#busca_playlist").keyup(function() {
      var search = $.trim(this.value);
      if (search === "") {
        show_all();
      }
      else {
        hide_divs(search);
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