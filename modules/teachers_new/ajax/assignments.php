<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

	function GetAssignmentQuery($fl_maestro){
  	
  	# Recupera los programas de los grupos que tiene asignados el profesor
	  $Query  = "SELECT DISTINCT b.fl_programa, c.nb_programa ";
	  $Query .= "FROM c_grupo a, k_term b, c_programa c ";
	  $Query .= "WHERE a.fl_term=b.fl_term ";
	  $Query .= "AND b.fl_programa=c.fl_programa ";
	  $Query .= "AND a.fl_maestro=$fl_maestro ";
	  $Query .= "ORDER BY c.no_orden";
	  $rs = EjecutaQuery($Query);

	  $result = array();
	  
	  for($tot_programas = 0; $row = RecuperaRegistro($rs); $tot_programas++) {
	  	$fl_programa = $row[0];
    	$nb_programa = str_uso_normal($row[1]);
    	
    	# Recupera los grados de cada programa
	    $Query  = "SELECT count(fl_leccion), no_grado ";
	    $Query .= "FROM c_leccion ";
	    $Query .= "WHERE fl_programa=$fl_programa ";
	    $Query .= "GROUP BY no_grado ";
	    $Query .= "ORDER BY no_grado";
	    $rs2 = EjecutaQuery($Query);

	    $terms = array();
	    for($tot_grados = 0; $row2 = RecuperaRegistro($rs2); $tot_grados++) {
	    	$tot_lecciones = $row2[0];
      	$no_grado = $row2[1];

      	# Recupera las lecciones del grado
	      $Query  = "SELECT fl_leccion, no_semana, ds_titulo ";
	      $Query .= "FROM c_leccion ";
	      $Query .= "WHERE fl_programa=$fl_programa ";
	      $Query .= "AND no_grado=$no_grado ";
	      $Query .= "ORDER BY no_semana";
	      $rs3 = EjecutaQuery($Query);

	      $lessons = array();
	      for($k = 0; $row3 = RecuperaRegistro($rs3); $k++) {
	        $fl_leccion = $row3[0];
	        $no_semana = $row3[1];
	        $ds_titulo = str_uso_normal($row3[2]);

	        $lessons += array($no_semana => $ds_titulo);
	      }
	      $terms += array($no_grado => $lessons);
	    }
	    $result += array($nb_programa => $terms);
    }
    echo json_encode((Object)$result);
  }
?>

<div class="row">
	<div class="col-xs-12">
		<div class="well well-light padding-10">
			<div class="row">
				<div class="col-xs-12">	
					<div id="assignment-content" class="well well-light no-margin no-padding"></div>
				</div>	 
			</div>
		</div>
	</div>	 
</div>	

<script type="text/javascript">  
	var programs = <?php GetAssignmentQuery($fl_maestro); ?>;
	var nb_program = Object.keys(programs);

	for(var i=0; i<nb_program.length; i++){

		var table_content = "";
		var program_name = "<div class='well well-light no-margin padding-10'><h3>"+nb_program[i]+"</h3></div>";
		$("#assignment-content").append(program_name);

		for(var no_term in programs[nb_program[i]]){

			var thead = "<thead>"+"<tr>"+"<th  class='col-xs-4'></th>"+"<th class='h4 col-xs-4'>Term "+no_term+"</th>"+"<th class='col-xs-4'></th>"+"</tr>"+"</thead>";

			var tr = "";
			for(var no_lesson in programs[nb_program[i]][no_term] ){
				//console.log(programs[nb_program[i]][no_term][no_lesson]);
				var lesson_name = programs[nb_program[i]][no_term][no_lesson];
				tr += 
					"<tr>"+
						"<td class='col-xs-4'>Week "+no_lesson+"</td>" +												// lesson's week 
						"<td class='col-xs-4'>"+lesson_name+"</td>" +														// lesson's name
						"<td class='col-xs-4'></td>" + 							
					"</tr>";
			}
			var tbody = "<tbody>"+tr+"</tbody>";
			table_content += thead+tbody;
		}

		var table = "<table class='table table-striped table-hover'>"+table_content+"</table>";
		$("#assignment-content").append(table);
	}
	
</script>
