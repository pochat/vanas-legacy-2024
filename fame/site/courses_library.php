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

  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
  $fl_instituto = ObtenInstituto($fl_usuario);
  MuestraModal("pause_course", true);

?>

  <style>
    .product-info{
        padding:11px 19px 0px 3px !important
    }
  </style>

  <script type="text/javascript">
      // *** Comentado el 25/04/2017
      // Eliminar programas fto_cat_courses_library_pq2.php
      // Eliminar programas fto_cat_courses_library_pq.php
      // Limpia valores del segundo filtro
      function LimpiaFto2(){     
        $("#datos2").select2().select2("val", null);
        $("#datos").select2().select2("val", null);
      }         
    
    //************** Funciones para playlist **************//
    
    function ActualizaPlaylist(consulta){
      if(consulta == "guarda")
        var new_playlist = document.getElementById('new_playlist').value; 
      else
        var new_playlist = "";

      $.ajax({
        type: 'POST',
        url : 'site/recupera_playlist.php',
        async: false,
        data: 'consulta='+consulta+
              '&new_playlist='+new_playlist,
        success: function(data) {
          $("#muestra_playlist").html(data);
       
        }
      });
    }
    
    function muestra_playlist_fto(fl_playlist){      
      $.ajax({
        type: 'POST',
        url : 'site/recupera_playlist.php',
        async: false,
        data: 'fl_playlist='+fl_playlist+
              '&consulta=busca',
        success: function(data) {
          $("#muestra_busqueda_playlist").html(data);
       
        }
      });
    }  
      
    // 1.- Busca un playlist existente
    function busca_playlist(valor, extra){ 
      if(valor == undefined)
        valor = document.getElementById('new_playlist').value;
      if(extra == undefined)
        extra = 0;
      $.ajax({
        type: 'POST',
        url : 'site/recupera_playlist.php',
        async: false,
        data: 'valor='+valor+
              '&accion=busca'+
              '&extra='+extra,
        success: function(data) {
          $('#muestra_prueba').html(data);
          document.getElementById('busca_playlist').value = valor;
          document.getElementById('new_playlist').value = "";
        }
      });
    }
    
    // 2.- Creamos playlist
    function GuardaPlaylist(valor_p){
        var valor = document.getElementById("new_playlist").value;
        
      $.ajax({
        type: 'POST',
        url : 'site/recupera_playlist.php',
        async: false,
        data: 'valor='+valor+
              '&extra='+valor_p+
              '&accion=guarda',
        success: function(data) {
          $('#muestra_busqueda_playlist1').html(data);
       
        }
      });
    }  
    
    // 3.- Busca de acuerdo al playlist seleccionado
    function FiltroPlaylist(valor){ 
      document.getElementById("seleccionados").style.display = "none";
      // document.getElementById('test_prueba').style.display = 'block';
      document.getElementById("sugerencias").style.display = "none";
      document.getElementById("res_ftos").style.display = "inline";
      document.getElementById("muestra_busqueda_playlist").style.display = "none";
      document.getElementById("muestra_loading").style.display = "block";
      LimpiaFto2();
      
      if(valor == 0){
        document.getElementById("busca_playlist").value = "";
        $(".nb_playlist_doc").show();
        $(".nb_playlist").show()
      }
      
      $.ajax({
        type: 'POST',
        url : 'site/recupera_playlist.php',
        async: false,
        data: 'valor='+valor+
              '&accion=filtra_playlist',
        success: function(data) {
          $('#muestra_busqueda_playlist').html(data);
          document.getElementById("muestra_busqueda_playlist").style.display = "block";
       
        }
      });
      
      document.getElementById("muestra_loading").style.display = "none";
      document.getElementById("muestra_div_ftos2").style.display = "none";
      document.getElementById("muestra_busqueda_playlist").style.display = "block";
      
    $('.sortable').sortable({
       opacity: 0.7,
       update: function(event, ui) {
            
        var list_sortable = $(this).sortable('toArray').toString();

        // change order in the database using Ajax
        $.ajax({
            url: 'site/act_ord_playlist.php?fl_playlist=' + valor,
            type: 'POST',
            data: { list_order: list_sortable },
            success: function (data) {
                $('#muestra_orden_playlist').html(data);
            }
        });
        
      }
 
    });

    $('[data-toggle="tooltip"]').tooltip();
    
    }
    
    function ActNom(nom){
      document.getElementById('titulo').innerHTML = nom + "&nbsp;&nbsp;&nbsp;<span class='caret'></span>";
      document.getElementById('test_prueba').style.display = 'block';
    }  
    
    // Div para mostrar input de agregar playlist
    function AddPlaylist(val) {
      element = document.getElementById("aa");
      element_tit = document.getElementById("mtit");
      if (val == true) {
        element.style.display='block';
        element_tit.style.display='none';
      } else {
        element.style.display='none';
        element_tit.style.display='block';
      }
    }
    
    function BtnGuardar(){
      var new_playlist = document.getElementById("new_playlist").value;
        $('#Ccl').removeClass('btn btn-primary btn-xs disabled');
        $('#Ccl').addClass('btn btn-primary btn-xs');
        
      if(new_playlist == ''){
        $('#Ccl').removeClass('btn btn-primary btn-xs');
        $('#Ccl').addClass('btn btn-primary btn-xs disabled');
      }
    }
    
    // Crear una relacion curso-playlist y la inserta en tabla
    function RelCursoPlaylist(fl_programa, fl_playlist){
      $.ajax({
        type: 'POST',
        url : 'site/recupera_playlist.php',
        async: false,
        data: 'accion=guarda_rel_cp'+
              '&valor='+fl_programa+
              '&extra='+fl_playlist,
        success: function(data) {
          $('#div_p').html(data);
       
        }
      });
    }

    //************** UMP  Funciones para generar lista de requerimientos **************//
    // Funcion para ofrecer la opcion de agregar todos los cursos relacionados
    function checkreq(fl_programa, fl_playlist){
      $.ajax({
        type: 'POST',
        url : 'site/recupera_required.php',
        async: false,
        data: 'accion=checkallrequired'+
              '&fl_programa='+fl_programa+
              '&fl_playlist='+fl_playlist,
        success: function(data) {
          $('#insertReq').html(data);
        }
      });
      nb_programa(fl_programa);
      img_prog(fl_programa);
      $('#allthis').attr('onClick', 'javascript:addAll2Playlist('+fl_programa+','+fl_playlist+', "addallrequired", "");');
      $('#onlythis').attr('onClick', 'javascript:RelCursoPlaylist('+fl_programa+','+fl_playlist+'); tt(); NewPlaylistCourse__'+fl_programa+'();');
      $('#required').modal('show');
    }

    function checkreq2(fl_programa, id){
      var nb_playlist = $('#new_playlist__'+id).val();
      $.ajax({
        type: 'POST',
        url : 'site/recupera_required.php',
        async: false,
        data: 'accion=checkallrequired'+
              '&fl_programa='+fl_programa,
        success: function(data) {
          $('#insertReq').html(data);
        }
      });
      nb_programa(fl_programa);
      img_prog(fl_programa);
      $('#allthis').attr('onClick', 'javascript:addAll2Playlist('+fl_programa+', "", "addallrequired", "'+nb_playlist+'");');
      $('#onlythis').attr('onClick', 'javascript:GuardaPlaylist__'+id+'('+fl_programa+'); AddPlaylistInd__'+id+'(false); tt(); NewPlaylistCourse__'+fl_programa+'();');
      $('#required').modal('show');
    }

    // This adds all the related courses to the playlist
    function addAll2Playlist(fl_programa, fl_playlist, action, nb_playlist){
      $.ajax({
        type: 'POST',
        url : 'site/recupera_required.php',
        async: false,
        data: 'accion='+action+
              '&fl_programa='+fl_programa+
              '&fl_playlist='+fl_playlist+
              '&nb_playlist='+nb_playlist,
        success: function(data) {
          tt();
          // $('#insertReq').html(data);
        }
      });
    }

    function nb_programa(fl_programa) {
      $.ajax({
        type: 'POST',
        url: 'site/recupera_nb_programa.php',
        async: false,
        data: 'valor='+fl_programa,
        success: function(data) {
            $('#onlythis').html(data);
        }
      });
    }

    function img_prog(fl_programa) {
      $.ajax({
        type: 'POST',
        url: 'site/recupera_img_programa.php',
        async: false,
        data: 'valor='+fl_programa,
        success: function(data) {
            $('#mod_title').html(data);
        }
      });
    }

    //************** Funciones para nuevo metodo implementado **************//
    
    // Envia a base de datos categoria principal
    function FtaCat(cat, principal){
      $.ajax({
        type: 'POST',
        url : 'site/fto_cat_1.php',
        async: false,
        data: 'categoria='+cat+
              '&principal='+principal
      });
    }
    
    // Div para mostrar sugerencias de filtros
    function FtaCatSugerencias(){
          // document.getElementById("muestra_div_ftos").style.display = "none";
          // document.getElementById("muestra_div_ftos2").style.display = "none";
       $.ajax({
           type: 'POST',
           url : 'site/fto_cat_sugerencias.php',
           async: false,
           data: '',
           success: function(data) {
               $('#sugerencias').html(data);
           }
       });
    }
    
    // Div para mostrar los filtros seleccionados
    function MtraFtoCatSel(){
          $.ajax({
              type: 'POST',
              url : 'site/fto_cat_seleccionados.php',
              async: false,
              data: '',
              success: function(data) {
                  $('#seleccionados').html(data);
              }
          });
          document.getElementById("muestra_div_ftos2").style.display = "none";
    }
    
    // Muestra los cursos resultado de los filtros realizados
    function MtraResFtos(){
          document.getElementById("res_ftos").style.display = "none";
          document.getElementById("muestra_busqueda_playlist").style.display = "none";
          document.getElementById("muestra_loading").style.display = "block";
          $.ajax({
              type: 'POST',
              url : 'site/fto_cat_courses_library.php',
              async: false,
              data: '',
              success: function(data) {
                  $('#res_ftos').fadeIn(1000).html(data);
                  document.getElementById("muestra_busqueda_playlist").style.display = "block";
              }
          });
      document.getElementById("muestra_loading").style.display = "none";
    }
    
    // Borramos un filtro y actualizamos lista
    function DelFto(fl_fto_cat_sp){
      $.ajax({
        type: 'POST',
        url : 'site/fto_cat_elimina.php',
        async: false,
        data: 'fl_fto_cat_sp='+fl_fto_cat_sp,
        success: function(data) {
          $('#del_ftos').html(data);
        }
      });
    }
    
    function SelectTwo(){
      // document.getElementById("muestra_busqueda_playlist").style.display = "none";
      document.getElementById("res_ftos").style.display = "block";
      document.getElementById("test_prueba").style.display = "none";
      document.getElementById("seleccionados").style.display = "block";
      document.getElementById("sugerencias").style.display = "block";
      document.getElementById("titulo").innerHTML = <?php echo "'".ObtenEtiqueta(1263)."'"; ?>  + "&nbsp;&nbsp;&nbsp;<span class='caret'></span>";
    }
  </script>

    <div id="export_moodle"></div>
    <div id="muestra_grupos"></div>
    <div class='modal fade' id='send_email' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='overflow-y:scroll;overflow:auto'data-backdrop='static'>
        <span id="send_email2" class="ui-widget ui-chatbox txt-color-white" style="right: 0px; display: block; padding:0px 600px 350px 0px;">
            <i class="fa fa-cog fa-4x  fa-spin txt-color-white" style="position: relative;left: 25px;"></i><h2><strong> Loadding....</strong></h2>
        </span>
    </div>
  <div id="content">
    <?php
    switch ($perfil_usuario) {
      case PFL_MAESTRO_SELF:
        $titulo = ObtenEtiqueta(1248);
        break;
      default:
        $titulo = ObtenEtiqueta(1247);
    }
    ?>
    <!-- TITULO DE LA PAGINA DEPENDIENDO EL PERFIL -->
      <div class="row">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-5">
          <h1 class="page-title txt-color-blueDark"><?php echo $titulo; ?></h1>
        </div>
      </div>           
  <script>
      function closedthis() {
          $.smallBox({
              title : "<?php echo ObtenEtiqueta(1352) ?>",
              content : "<?php echo ObtenEtiqueta(1353) ?>",
              color : "#739E73",
              iconSmall : "fa fa-save",
              timeout : 4000
          });
      }
      
      function tt(){
          closedthis();
      }

      function exists(){
          $.smallBox({
              title : '<?php echo "YA EXISTE"; ?>',
              content : '<?php echo "Este curso ya existe en el playlist"; ?>',
              color : '#C46A69',
              iconSmall : 'fa fa-info-circle',
              timeout : 4000
          });
      }      
  </script>
      <!-- Div que muestra opciones de filtrado por categoria y playlist, solo para Profesores y administrador -->
      <div class="row">
        <!-- ICH: DIV Filtro Categoria -->
        <div class='col-lg-10'>
            <!-- SELECT PRINCIPAL -->
            <div id="muestra_div_ftos2">
              <div class="form-group">
                <label><?php echo ObtenEtiqueta(1256); ?></label><!-- FiltraCategorias(this.value, 1, 1); ActualizaFtoCat(); MuestraFiltro();  -->
                <select multiple style="width:100%" class="select2" onchange="FtaCat(this.value, 1); FtaCatSugerencias(); MtraFtoCatSel(); MtraResFtos();" id="datos" placeholder="<?php echo ObtenEtiqueta(1258); ?>">
                  <!-- Field of Study -->
                  <optgroup label="<?php echo ObtenEtiqueta(1306); ?>">
                  <?php
                    $Query  = "SELECT a.nb_categoria".$sufix.", a.fl_cat_prog_sp FROM c_categoria_programa_sp a
                    JOIN k_categoria_programa_sp b ON(b.fl_cat_prog_sp=a.fl_cat_prog_sp)
                    /*JOIN c_leccion_sp c ON(c.fl_programa_sp=b.fl_programa_sp) oculto por eficiencia */
                    WHERE fg_categoria = 'FOS' GROUP BY a.fl_cat_prog_sp ORDER BY fg_categoria, a.nb_categoria".$sufix;
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- Skills -->
                  <optgroup label="<?php echo ObtenEtiqueta(1307); ?>">
                  <?php
                     $Query  = "SELECT a.nb_categoria".$sufix.", a.fl_cat_prog_sp FROM c_categoria_programa_sp a
                    LEFT JOIN k_categoria_programa_sp b ON(b.fl_cat_prog_sp=a.fl_cat_prog_sp)
                    /*JOIN c_leccion_sp c ON(c.fl_programa_sp=b.fl_programa_sp) oculto por eficiencia */
                    WHERE fg_categoria = 'CAT' GROUP BY a.fl_cat_prog_sp ORDER BY fg_categoria, a.nb_categoria".$sufix;
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }
                  ?>
                  </optgroup>
                  <!-- School Level -->
                  <optgroup label="<?php echo ObtenEtiqueta(1308); ?>">
                  <?php
                    $Query  = "SELECT nb_grado, fl_grado FROM k_grado_fame ";
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='G-{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>                           
                  <!-- Hardware -->
                  <optgroup label="<?php echo ObtenEtiqueta(1309); ?>">
                  <?php
                    $Query  = "SELECT a.nb_categoria".$sufix.", a.fl_cat_prog_sp FROM c_categoria_programa_sp a
                    LEFT JOIN k_categoria_programa_sp b ON(b.fl_cat_prog_sp=a.fl_cat_prog_sp)
                    /*JOIN c_leccion_sp c ON(c.fl_programa_sp=b.fl_programa_sp) oculto por eficiencia*/ 
WHERE fg_categoria = 'HAR' GROUP BY a.fl_cat_prog_sp ORDER BY fg_categoria, a.nb_categoria".$sufix;  
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- Software -->
                  <optgroup label="<?php echo ObtenEtiqueta(1310); ?>">
                  <?php
                     $Query  = "SELECT a.nb_categoria".$sufix.", a.fl_cat_prog_sp FROM c_categoria_programa_sp a
                    LEFT JOIN k_categoria_programa_sp b ON(b.fl_cat_prog_sp=a.fl_cat_prog_sp)
                    /*JOIN c_leccion_sp c ON(c.fl_programa_sp=b.fl_programa_sp) oculto por eficiencia */ 
WHERE fg_categoria = 'SOF' GROUP BY a.fl_cat_prog_sp ORDER BY fg_categoria, a.nb_categoria".$sufix;  
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- Level -->
                  <optgroup label="<?php echo ObtenEtiqueta(1311); ?>">
                    <option value='<?php echo "LVB"; ?>'><?php echo ObtenEtiqueta(1317); ?></option>
                    <option value='<?php echo "LVI"; ?>'><?php echo ObtenEtiqueta(1321); ?></option>
                    <option value='<?php echo "LVA"; ?>'><?php echo ObtenEtiqueta(1322); ?></option>
                  </optgroup>
                  <!-- Course Code -->
                  <optgroup label="<?php echo ObtenEtiqueta(1312); ?>">
                  <?php
                  $Query  = "SELECT CONCAT(p.nb_programa".$sufix.", ' (code: ', p.ds_course_code ,')') as programa, p.fl_programa_sp FROM c_programa_sp p JOIN c_leccion_sp b ON(b.fl_programa_sp=p.fl_programa_sp) 
                    WHERE p.fg_publico='1' GROUP BY p.fl_programa_sp ORDER BY p.nb_programa".$sufix;                                  
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_programa_sp_fto = ($row[1]);
                        echo "<option value='P-{$fl_programa_sp_fto}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- Mapping -->
                  <optgroup label="<?php echo ObtenEtiqueta(2056); ?>">
                  <?php
                     $Query  = "SELECT  CASE WHEN C.fl_instituto IS NULL THEN  CONCAT (C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,', ',E.ds_provincia) ELSE
                            CONCAT (C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,' - ".ObtenEtiqueta(2626)." ',F.ds_instituto) END AS nb_course 
                            ,C.fl_course_code
                            FROM c_course_code C
                            JOIN c_pais P ON P.fl_pais=C.fl_pais
                            LEFT JOIN k_provincias E ON E.fl_provincia=C.fl_estado 
                            LEFT JOIN c_instituto F ON F.fl_instituto=C.fl_instituto 
                            WHERE  EXISTS ( SELECT 1 FROM k_course_code_prog_fame D JOIN c_programa_sp M ON M.fl_programa_sp=D.fl_programa_sp  WHERE D.fl_course_code=C.fl_course_code AND fg_publico='1' ) 
                            ORDER BY C.nb_course_code ASC ";
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_course_code = str_texto($row[0]);
                      $fl_course_code = ($row[1]);
                        echo "<option value='K-{$fl_course_code}'>{$nb_course_code}</option>";
                    }   
                  ?>
                  </optgroup>
                <!--End Mapping--->
                </select>
              </div>
            </div>
            
            <!-- ICH: Nuevo div para mostrar categorias seleccionadas -->
            <div id="seleccionados"></div>
            
            <!-- SELECT SECUNDARIO -->
            <div id='test_prueba' style="display:none;">
              <div class="form-group">
                
                <label><?php echo ObtenEtiqueta(1256); ?></label> <!-- FiltraCategorias(this.value, 1, 1, 1); ActualizaFtoCat(); MuestraFiltro(); LimpiaFto2(); --> 
                <select multiple style="width:100%" class="select2" onchange="FtaCat(this.value, 1); FtaCatSugerencias(); MtraFtoCatSel(); MtraResFtos(); SelectTwo(); LimpiaFto2();" id="datos2" placeholder="<?php echo ObtenEtiqueta(1258); ?>">
                
                  <!-- Field of Study -->
                  <optgroup label="<?php echo ObtenEtiqueta(1306); ?>">
                  <?php
                    $Query  = "SELECT a.nb_categoria".$sufix.", a.fl_cat_prog_sp FROM c_categoria_programa_sp a
                    LEFT JOIN k_categoria_programa_sp b ON(b.fl_cat_prog_sp=a.fl_cat_prog_sp)
                    /*JOIN c_leccion_sp c ON(c.fl_programa_sp=b.fl_programa_sp)*/ WHERE fg_categoria = 'FOS' GROUP BY a.fl_cat_prog_sp ORDER BY fg_categoria, a.nb_categoria".$sufix;                    
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- Skills -->
                  <optgroup label="<?php echo ObtenEtiqueta(1307); ?>">
                  <?php
                     $Query  = "SELECT a.nb_categoria".$sufix.", a.fl_cat_prog_sp FROM c_categoria_programa_sp a
                    LEFT JOIN k_categoria_programa_sp b ON(b.fl_cat_prog_sp=a.fl_cat_prog_sp)
                    /*JOIN c_leccion_sp c ON(c.fl_programa_sp=b.fl_programa_sp)*/ WHERE fg_categoria = 'CAT' GROUP BY a.fl_cat_prog_sp ORDER BY fg_categoria, a.nb_categoria".$sufix;  
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- School Level -->
                  <optgroup label="<?php echo ObtenEtiqueta(1308); ?>">
                  <?php
                    $Query  = "SELECT nb_grado, fl_grado FROM k_grado_fame ";
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='G-{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>                           
                  <!-- Hardware -->
                  <optgroup label="<?php echo ObtenEtiqueta(1309); ?>">
                  <?php
                     $Query  = "SELECT a.nb_categoria".$sufix.", a.fl_cat_prog_sp FROM c_categoria_programa_sp a
                    LEFT JOIN k_categoria_programa_sp b ON(b.fl_cat_prog_sp=a.fl_cat_prog_sp)
                    /*JOIN c_leccion_sp c ON(c.fl_programa_sp=b.fl_programa_sp)*/ WHERE fg_categoria = 'HAR' GROUP BY a.fl_cat_prog_sp ORDER BY fg_categoria, a.nb_categoria".$sufix;  
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- Software -->
                  <optgroup label="<?php echo ObtenEtiqueta(1310); ?>">
                  <?php
                     $Query  = "SELECT a.nb_categoria".$sufix.", a.fl_cat_prog_sp FROM c_categoria_programa_sp a
                    LEFT JOIN k_categoria_programa_sp b ON(b.fl_cat_prog_sp=a.fl_cat_prog_sp)
                    /*JOIN c_leccion_sp c ON(c.fl_programa_sp=b.fl_programa_sp)*/ WHERE fg_categoria = 'SOF' GROUP BY a.fl_cat_prog_sp ORDER BY fg_categoria, a.nb_categoria".$sufix;  
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_cat_prog_sp = ($row[1]);
                        echo "<option value='{$fl_cat_prog_sp}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- Level -->
                  <optgroup label="<?php echo ObtenEtiqueta(1311); ?>">
                    <option value='<?php echo "LVB"; ?>'><?php echo ObtenEtiqueta(1317); ?></option>
                    <option value='<?php echo "LVI"; ?>'><?php echo ObtenEtiqueta(1321); ?></option>
                    <option value='<?php echo "LVA"; ?>'><?php echo ObtenEtiqueta(1322); ?></option>
                  </optgroup>
                  <!-- Course Code -->
                  <optgroup label="<?php echo ObtenEtiqueta(1312); ?>">
                  <?php
                    $Query="SELECT CONCAT(p.nb_programa".$sufix.", ' (code: ', p.ds_course_code ,')') as programa, p.fl_programa_sp FROM c_programa_sp p JOIN c_leccion_sp b ON(b.fl_programa_sp=p.fl_programa_sp) 
                            WHERE p.fg_publico='1' GROUP BY p.fl_programa_sp ORDER BY p.nb_programa".$sufix;
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_categoria = str_texto($row[0]);
                      $fl_programa_sp_fto = ($row[1]);
                        echo "<option value='P-{$fl_programa_sp_fto}'>{$nb_categoria}</option>";
                    }   
                  ?>
                  </optgroup>
                  <!-- Mapping -->
                  <optgroup label="<?php echo ObtenEtiqueta(2056); ?>">
                  <?php
                    $Query="SELECT CONCAT (C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,', ',E.ds_provincia) as course_code ,C.fl_course_code 
                            FROM c_course_code C
                            JOIN c_pais P ON P.fl_pais=C.fl_pais
                            JOIN k_provincias E ON E.fl_provincia=C.fl_estado
                            WHERE  EXISTS ( SELECT 1 FROM k_course_code_prog_fame D JOIN c_programa_sp M ON M.fl_programa_sp=D.fl_programa_sp WHERE D.fl_course_code=C.fl_course_code  AND fg_publico='1' ) 
                            ORDER BY C.nb_course_code ASC ";
                    $rs = EjecutaQuery($Query);
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_course_code = str_texto($row[0]);
                      $fl_course_code = ($row[1]);
                        echo "<option value='K-{$fl_course_code}'>{$nb_course_code}</option>";
                    }   
                  ?>
                  </optgroup>
                <!--End Mapping--->
                </select>
            </div> 
          </div>
        </div>
        
        <!-- ICH: DIV Filtro Playlist -->

        <script>
          function DespliegaListaPri(muestra){
            $.ajax({
              type: 'POST',
              url : 'site/listado_pri_palylist.php',
              async: false,
              data: 'muestra='+muestra,
              success: function(data) {
                $('#muestra_listado_pri_playlist').html(data);
              }
            });
          }
        </script>

        <div class='col-lg-2'>
          <label>&nbsp;</label><br>
          <a href="javascript:DespliegaListaPri(1);" class="btn btn-default" ><div id="titulo"><?php echo ObtenEtiqueta(1263); ?>&nbsp;&nbsp;&nbsp;<span class="caret"></span></div></a>
          <div id="muestra_listado_pri_playlist"></div>
        </div>        
      </div>
            
      <!---Div para mostrar resultados por filtros --->
      <div id="muestra_div_ftos"></div>
      <div style="padding-top:5px;"></div>
      <!---Div para mostrar sugerencias de filtros --->
      <div class="col-lg-10" style="padding:0 6px 0 0"><div id="sugerencias"></div></div>
      <!---Div para eliminar filtros (no regresa nada)--->
      <div id="del_ftos"></div>
      <!---Div para mostrar resultados de filtros --->
      <div id="res_ftos"></div>
      <!-- Div para mensaje de cargando -->      
      <div id="muestra_loading" style="display: none;">
        <br><br><br><br><br>
        <center>
          <span id="gabriel" class="ui-widget  txt-color-black">
            <i class="fa fa-cog fa-4x  fa-spin txt-color-black"></i><h2><strong></strong></h2>
          </span>
        </center>
      </div>
      
<style>
 .placeholder {
    border: 1px solid green;
    background-color: white;
    -webkit-box-shadow: 0px 0px 10px #888;
    -moz-box-shadow: 0px 0px 10px #888;
    box-shadow: 0px 0px 10px #888;
 }
 .tile {
        height: 100px;
 }
 .grid {
     margin-top: 1em;
 }
 .well {
     min-height: 300px ;
     background: transparent;
     border: 0px solid #ddd ;
     -webkit-box-shadow: 0 0px 0px #ececec ;
 }
 /* On screens that are 600px or less, set the background color to olive */
@media screen and (max-width: 600px) {
    .well {
        min-height: 520px ;
    }
}
</style>
      <!-- LISTADO DE PROGRAMAS -->
      <!-- ICH: Inicia  DIV que muestra cursos dependiendo de las categorias seleccionadas -->
      <div id="muestra_busqueda_playlist">
          <!-- ICH: Inicia  DIV principal que muestra cursos -->
          <section id="widget-grid" class="">
            <div class="row " id="items_programs">
            <?php
            $row_cont_cur = RecuperaValor("SELECT fl_programa_sp FROM c_programa_sp   WHERE fg_publico='1'   ORDER BY fl_programa_sp DESC LIMIT 1");
            echo "<input type='hidden' name='row_cont_cur' id='row_cont_cur' value='$row_cont_cur[0]'>";
            ?>
            </div>
          </section>
          <!-- ICH: Termina DIV principal que muestra cursos -->
      </div>
      <!-- ICH: Termina DIV que muestra cursos dependiendo de las categorias seleccionadas -->
  </div>
  <!-- ICH: Termina DIV que muestra cursos dependiendo de las categorias seleccionadas -->
  <div class="modal" id="modalCategorias" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">        
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <!---MJD modal que mostrar bien chido el contenido de el course code--->
  <div class="modal" id="myModal2" data-backdrop="static">
  <div class="modal-dialog">
      <div class="modal-content" id='presenta_contenido_curriculum' >
      </div>
    </div>
</div>
<!-- UMP Nuevo Modal PARA requisitos -->
<div id="required" class="modal fadeIn" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 90%;">
    <div class="modal-content">
      <div id="mod_title" class="modal-header" style=" background-color: #337AB7; padding:16px 16px; color:#FFF; border-bottom:2px dashed #337AB7;">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id='insertReq' class="modal-body" style="margin: auto;">
        <!-- Displays here the required courses -->
      </div>
      <div class="modal-footer">
        <button id='allthis' type="button" class="btn btn-primary" data-dismiss="modal" onclick=""><?php echo ObtenEtiqueta(2583); ?></button>
        <button id='onlythis' type="button" class="btn btn-success" data-dismiss="modal" onclick="">Only add this</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    pageSetUp();
    // Main container view programs
    var container_programs = $("#items_programs");
   
    $(document).ready(function(){
      
        //initializa variables.
        var selectedPost, classmate, myPosts;
        index = 0;
        selectedPost = 0;
        classmate = 0;
        myPosts = 0;
        var selectedFilter = 0;
        var myPosts = "";
        var index = 0;
        var no_index=[];

        console.log("inicia pagina programas");
        
        requestItemsprogramsCourses(container_programs);
            $(window).on('scroll.infinite', function () {
                if ($(window).scrollTop() == $(document).height() - $(window).height()) {
                    requestItemsprogramsCourses(container_programs);
                }
            });

            function requestItemsprogramsCourses(container_programs) {

                

                $.ajax({
                    type: 'GET',
                    url: 'site/courses_items.php',
                    data: 'tema=' + selectedFilter +
                                '&my_posts=' + myPosts +
                                '&index=' + index,
                    beforeSend: function () {
                      //  $('#loading_fame').show();
                    }
                }).done(function (result) {
                    var elems = JSON.parse(result);
                    // Check for end of board
                    // if(elems.index.end > 0){
                    // update index
                    index = elems.index.end;
                    console.log('es index:'+index);
                    if (no_index.includes(index)) {

                    }else{
                        displayPrograms(container_programs, elems);

                       // $('#loading_fame').hide();
                    }

                    //var index_pasado = index;
                    no_index.push(index);
                    
                });
            }
       
    });

    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    function Assign_Grp_Crs(p_user, p_curso, p_grp){
        var asignar;
        if($('#ch_'+p_user).is(':checked'))
            asignar = 1;
        else
            asignar = 0;
        $.ajax({
            type: "POST",
            url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
            data: 'fl_action=100&fl_usuario='+p_user+'&fl_programa_std='+p_curso+'&nb_grupo='+p_grp+'&asignar='+asignar,
            async: false,
            success: function(html){
                location.reload();
            }
        });
    }

    // Funcion para borrar un playlist o relacion curso - playlist
    function Borra_Playlist(valor, extra){
        $.ajax({
            type: 'POST',
            url : 'site/recupera_playlist.php',
            async: false,
            data: 'valor='+valor+'&accion=borrar'+'&extra='+extra,
            success: function(data) {
                $('#muestra_prueba').html(data);
            }
        });
        NewPlaylistCourse_<?php echo "_".$row_cont_cur[0]; ?>();
    }
      
    // Funcion para confirmar la eliminacion de un playlist o relacion curso - playlist
    function confirma_borra(valor, extra){
        if(extra == 0){
            title = "<?php echo ObtenEtiqueta(1276); ?>";
            content = "<?php echo ObtenEtiqueta(1277); ?> <p class='text-align-right'><a href='javascript:Borra_Playlist(" + valor + ", " + extra + ");' class='btn btn-success btn-sm'><?php echo ObtenEtiqueta(16); ?></a> <a href='javascript:void(0);' class='btn btn-danger btn-sm'><?php echo ObtenEtiqueta(17); ?></a></p>";
        } else {
            title = "<?php echo ObtenEtiqueta(1278); ?>";
            content = "<?php echo ObtenEtiqueta(1279); ?> <p class='text-align-right'><a href='javascript:Borra_Playlist(" + valor + ", " + extra + ");' class='btn btn-success btn-sm'><?php echo ObtenEtiqueta(16); ?></a> <a href='javascript:void(0);' class='btn btn-danger btn-sm'><?php echo ObtenEtiqueta(17); ?></a></p>";
        }

        $.smallBox({
            title : title,
            content : content,
            color : "#C46A69",
            //timeout: 8000,
            // icon : "fa fa-question swing animated"
        });
    }
</script>    

<div id="muestra_orden_playlist" name="muestra_orden_playlist"> </div>

