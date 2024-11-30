<?php 
	# Libreria de funciones
  require("../lib/self_general.php");

  /**
   * Importante el fam/js/comunity.js y el fame/site/comunity_div.php ya no se utliza los dejos por si a futuro llegan a servir o rescatar algo.
   *
   */

  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  

  # Recibe Parametros
  $classmate = RecibeParametroNumerico('classmate', True);

  # List of program's
  function FilterProgram($fl_usuario){

      # Get the sufix for the languaje
      $sufix=langSufix();

      $Query  = "SELECT pr.fl_programa_sp, pr.nb_programa".$sufix;
      $Query .= " FROM k_usuario_programa  usp ";
      $Query .= "LEFT JOIN c_programa_sp pr ON(pr.fl_programa_sp=usp.fl_programa_sp) WHERE ";
      # Obtenemos el perfil del usuario
      # Si es maestro muestra los programas que imparte
      # Si es alumno muesra los que cursa
      $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
      if($fl_perfil_sp == PFL_MAESTRO_SELF)
          $Query .= "fl_maestro=$fl_usuario ";
      else{
          if($fl_perfil_sp == PFL_ESTUDIANTE_SELF)
              $Query .= "fl_usuario_sp=$fl_usuario ";
      }
      $rs = EjecutaQuery($Query);

      $result = array("type" => "program");
      $program = array();

      // program list is stored as value : name (count)
      //while($row = RecuperaRegistro($rs)){
      for($i=0;$row=RecuperaRegistro($rs);$i++) {
          # Buscamos cuantos usuarios existen en cada programa
         // $roww = RecuperaValor("SELECT COUNT(*) FROM k_usuario_programa WHERE fl_programa_sp=".$row[0]."");
        //  $program += array($row[0] => str_uso_normal($row[1])." (".$roww[0].")");
          $program += array($row[0] => str_uso_normal($row[1])." ");
      }
      $result += array("list" => $program);
      echo json_encode((Object) $result);
  }
 
?>
<!-- Cortar texto -->
<style>
.cortar{
  text-overflow:ellipsis;
  white-space:nowrap; 
  overflow:hidden; 
}
.cortar:hover {
  width: auto;
  height: auto;
  white-space: initial;
  overflow:visible;
  cursor: pointer;  
}
.well {
    background: #ffffff !important;
	
}
</style>
<link href='../fame/css/aos.css' rel='stylesheet'>

<div class="row">
	<div class="col-md-12">
		<div class="btn-group">
			<button id="filter-user-title" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
				User <span class="caret"></span>
			</button>
			<ul id="filter-user" class="dropdown-menu">
				<li value="0" class="active"><a href="javascript:void(0);" onClick="PresentaComunity('All');">All</a></li>
				<li value="T"><a href="javascript:void(0);" onClick="PresentaComunity('A');">Administrator</a></li>
				<li value="T"><a href="javascript:void(0);" onClick="PresentaComunity('T');">Teacher</a></li>
				<li value="S"><a href="javascript:void(0);" onClick="PresentaComunity('S');">Student</a></li>
			</ul>
		</div>

        <?php
        # Si el usuario esta cursando programas mostrar el boton para la busqueda
        if(ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario) ||  (ExisteEnTabla('k_usuario_programa', 'fl_maestro', $fl_usuario) && $fl_perfil_sp == PFL_MAESTRO_SELF)){
            echo "
            <div class='btn-group'>
              <button id='filter-program-title' class='btn btn-primary dropdown-toggle' data-toggle='dropdown'>
                Program <span class='caret'></span>
              </button>
              <ul id='filter-program' class='dropdown-menu'>
                <li value ='0' class='active'><a href='javascript:void(0);' onclick='PresentaComunity(\"All\");'>All</a></li>
              </ul>
            </div>";
        }
        ?>
        <div class="btn-group">
		    <button type="button" onclick="PresentaComunity('All');" class="btn btn-primary">Reset</button>
	    </div>

	</div>

</div>
<br />

<script>

    	var result;

    	result = <?php FilterProgram($fl_usuario); ?>;
    	DisplayFilter(result);
  
    	// Populate the filter lists
    	function DisplayFilter(result){
    	    var list, filterType, options;
    	    filterType = result.type;
    	    options = result.list;

    	    list = "";
    	    if(filterType == "program"){
    	        for(var key in options){
    	            list += 
                        "<li value='"+key+"'>"+
                            "<a href='javascript:void(0);' onClick='PresentaComunity(\"P\","+key+");'>"+
                                options[key]+
                            "</a>"+
                        "</li>";
    	        }
    	        $("#filter-program").append(list);
    	    }
    	    if(filterType == "country"){
    	        for(var key in options){
    	            list += 
                        "<li value='"+key+"'>"+
                            "<a href='javascript:void(0);' onClick='Country("+key+");'>"+
                                options[key]+
                            "</a>"+
                        "</li>";
    	        }
    	        $("#filter-country").append(list);
    	    }
	
    	}


</script>





<div id="community-container"> </div>
	


<script>
    function PresentaComunity(fg_filtro,fl_programa_sp) {
        var fg_filtro = "" + fg_filtro + "";
        $('#community-container').empty();

        $("#filter-user li.active").removeClass("active");

        if(fg_filtro=='All'){
            $('#filter-user-title').empty().append('User <span class=\'caret\'></span>');

        }
        if(fg_filtro=='A'){
            $('#filter-user-title').empty().append('Administrator <span class=\'caret\'></span>');
            $("#filter-user li:has(a[onclick='PresentaComunity('"+fg_filtro+"');'])").addClass("active");
        }
        if(fg_filtro=='T'){
            $('#filter-user-title').empty().append('Teacher <span class=\'caret\'></span>');
            $("#filter-user li:has(a[onclick='PresentaComunity('"+fg_filtro+"');'])").addClass("active");
        }
        if(fg_filtro=='S'){
            $('#filter-user-title').empty().append('Student <span class=\'caret\'></span>');
            $("#filter-user li:has(a[onclick='PresentaComunity('"+fg_filtro+"');'])").addClass("active");
        }
        if(fl_programa_sp){
            $("#filter-program li.active").removeClass("active");
            $("#filter-program li:has(a[onclick='PresentaComunity(\""+fg_filtro+"\","+fl_programa_sp+");'])").addClass("active");
        
        }


        



        $.ajax({
            type: 'POST',
            url: '/fame/site/muestra_comunity.php',
            data: 'fg_filtro=' + fg_filtro+
                  '&fl_programa_sp='+fl_programa_sp,
            async: true,
            success: function (html) {
                $('#community-container').html(html);
            }
        });


    }
    
    PresentaComunity('All');




</script>



<!-----Modal para enviar la invitacion------>

<!-- Modal -->
<div class="modal fade" id="invitacion_friends" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-sm" role="document" style="width:50%;">
    <div class="modal-content"  id="muestra_ifo_friends">
      



    </div>
  </div>
</div>

<!----------->
<script type='text/javascript' src='../fame/js/aos.js'></script>
<script>
    AOS.init({
        easing: 'ease-out-back',
        duration: 1000
    });



</script>