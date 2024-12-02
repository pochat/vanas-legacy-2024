<?php
  # ARV 12/02/14
	# Este programa crea el colorbox de la busqueda avanzada y hace llamado por ajax de los filtros dinamicos.
	
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recupera el usuario actual
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $inicializa = RecibeParametroNumerico('inicializa');
	
	# Arreglo de los titulos de las opciones de busqueda avanzada
	$titulos = array(
	ETQ_USUARIO, /*user*/ ETQ_NOMBRE, /*name*/ ObtenEtiqueta(118), /*last name*/ ObtenEtiqueta(119), /*middle name*/ ObtenEtiqueta(114), /*gender*/ 
  ObtenEtiqueta(120), /*date of birth*/ ObtenEtiqueta(121), /*email*/ 
  ObtenEtiqueta(113), /*active*/ ObtenEtiqueta(111), /*date created*/ ObtenEtiqueta(112), /*last login*/ ObtenEtiqueta(340), /*app submitted date*/ 
  ObtenEtiqueta(360), /*course name*/ 
  ObtenEtiqueta(297), /*teacher*/ ObtenEtiqueta(426), /*current group*/ ObtenEtiqueta(375), /*level/term*/ 
  ObtenEtiqueta(284), /*city*/ ObtenEtiqueta(285), /*state*/ ObtenEtiqueta(287),	/*country*/ 
  ObtenEtiqueta(342), /*term*/ 
  ObtenEtiqueta(619), /*international student*/
  ObtenEtiqueta(411),	/*time zone*/ 
  ObtenEtiqueta(60), /*program start date*/ 
  ObtenEtiqueta(540), /*acceptance letter date*/ ObtenEtiqueta(541), /*admission contract date*/ ObtenEtiqueta(544), /*program end date*/ 
  ObtenEtiqueta(545), /*program date completed*/ ObtenEtiqueta(546), /*official transcript print date*/ 
  ObtenEtiqueta(547), /*academic credential*/ ObtenEtiqueta(548), /*awards*/ ObtenEtiqueta(556), /*graduation date*/ 
  ObtenEtiqueta(558), /*student withdrawal*/ ObtenEtiqueta(559), /*student dismissed*/
  ObtenEtiqueta(644), /*student placement*/ ObtenEtiqueta(645), /*graduated*/ 
  ObtenEtiqueta(670), /*year of birth*/ ObtenEtiqueta(671), /*year created*/ ObtenEtiqueta(672), /*last login year*/ 
  ObtenEtiqueta(673), /*app submitted year*/ 
  ObtenEtiqueta(674), /*program start year*/ 
  ObtenEtiqueta(675), /*acceptance letter year*/ ObtenEtiqueta(676), /*admission contract year*/
  ObtenEtiqueta(677), /*program end year*/ ObtenEtiqueta(678), /*program year completed*/  
  ObtenEtiqueta(679), /*official transcript print year*/ ObtenEtiqueta(680), /*graduation year*/ 
	); 
  
  # Ordena alfabeticamente los titulos
  sort($titulos); 
  if($inicializa) {
    $parametro = 1;
    for($i=0; $i<7; $i++) {
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
     $campo[$i] = $row[0];
    }
    for($i=0; $i<7; $i++) {
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $tipo[$i] = $row[0];
    }
    for($i=0; $i<7; $i++) {
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $filtro[$i] = $row[0];
    }
    for($i=0; $i<7; $i++) {
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $crit[$i] = $row[0];
    }
    for($i=0; $i<7; $i++) {
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $fecha1[$i] = $row[0];
    }
    for($i=0; $i<7; $i++) {
      $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
      $fecha2[$i] = $row[0];
    }
  }
  
  # Funcion para obtener los filtros dinamicos
	echo "	
	<script type='text/javascript'>
		
		function busqueda_avanzada(opc) {
      
			switch (opc)
			{
				case 1:
					$.ajax({
						type: 'POST',
						url : 'div_busqueda_students.php',
            async: false,
						data: 'opcion='+$('#opcion1').val()+
                  '&filtro=$filtro[0]'+
                  '&crit=$crit[0]'+
                  '&fecha1=$fecha1[0]'+
                  '&fecha2=$fecha2[0]'+
									'&div_filtro='+opc,
						success: function(html) {
								$('#filtro1').html(html);
						}
					});
					break;
				case 2:
					$.ajax({
						type: 'POST',
						url : 'div_busqueda_students.php',
            async: false,
						data: 'opcion='+$('#opcion2').val()+
                  '&filtro=$filtro[1]'+
                  '&crit=$crit[1]'+
                  '&fecha1=$fecha1[1]'+
                  '&fecha2=$fecha2[1]'+
									'&div_filtro='+opc,
						success: function(html) {
								$('#filtro2').html(html);
						}
					});
					break;
				case 3:
					$.ajax({
						type: 'POST',
						url : 'div_busqueda_students.php',
            async: false,
						data: 'opcion='+$('#opcion3').val()+
                  '&filtro=$filtro[2]'+
                  '&crit=$crit[2]'+
                  '&fecha1=$fecha1[2]'+
                  '&fecha2=$fecha2[2]'+
									'&div_filtro='+opc,
						success: function(html) {
								$('#filtro3').html(html);
						}
					});
					break;
				case 4:
					$.ajax({
						type: 'POST',
						url : 'div_busqueda_students.php',
            async: false,
						data: 'opcion='+$('#opcion4').val()+
                  '&filtro=$filtro[3]'+
                  '&crit=$crit[3]'+
                  '&fecha1=$fecha1[3]'+
                  '&fecha2=$fecha2[3]'+
									'&div_filtro='+opc,
						success: function(html) {
								$('#filtro4').html(html);
						}
					});
					break;
          case 5:
					$.ajax({
						type: 'POST',
						url : 'div_busqueda_students.php',
            async: false,
						data: 'opcion='+$('#opcion5').val()+
                  '&filtro=$filtro[4]'+
                  '&crit=$crit[4]'+
                  '&fecha1=$fecha1[4]'+
                  '&fecha2=$fecha2[4]'+
									'&div_filtro='+opc,
						success: function(html) {
								$('#filtro5').html(html);
						}
					});
					break;
          case 6:
					$.ajax({
						type: 'POST',
						url : 'div_busqueda_students.php',
            async: false,
						data: 'opcion='+$('#opcion6').val()+
                  '&filtro=$filtro[5]'+
                  '&crit=$crit[5]'+
                  '&fecha1=$fecha1[5]'+
                  '&fecha2=$fecha2[5]'+
									'&div_filtro='+opc,
						success: function(html) {
								$('#filtro6').html(html);
						}
					});
					break;
          case 7:
					$.ajax({
						type: 'POST',
						url : 'div_busqueda_students.php',
            async: false,
						data: 'opcion='+$('#opcion7').val()+
                  '&filtro=$filtro[6]'+
                  '&crit=$crit[6]'+
                  '&fecha1=$fecha1[6]'+
                  '&fecha2=$fecha2[6]'+
									'&div_filtro='+opc,
						success: function(html) {
								$('#filtro7').html(html);
						}
					});
					break;
			}
		}
		
		function actualiza_listado() {
			$('#frm_avanzada').submit();
		}
    
    function cierraBusquedaAvanzada() {
      $('#div_principal').html('');
      $('#div_principal').css('display', 'none');
    }
	
    function agregar_filtro(opc){
      
      $('#campo'+opc).css('display', 'block');
    }
  
    function quitar_filtro(opc){
     
      $('#opcion'+opc).val('0');
      $('#opcion_'+opc).val('');
      $('#filtro'+opc).html('');
      $('#campo'+opc).css('display', 'none');
    }
		
  </script>";
  
  $ancho_prompt = "250";
  $ancho_filtro = "350";
	echo "
		<form action='students.php' method='POST' name='frm_avanzada' id='frm_avanzada'>
      <div style='text-align: center; background-color: #0681C7; color: #FFF; margin-bottom: 10px; font-weight: bold;'>Advanced Search filters</div>
			<table border='".D_BORDES."' align='center' cellPadding='2' cellSpacing='0'>";	
	
	# Primer filtro
	echo "
				<tr style='display: block;'>
          <td width='20'><a href='javascript:agregar_filtro(2);'><img src='".PATH_IMAGES."/".IMG_AGREGAR."' title='Add filter'></a></td>
          <td width='30'>&nbsp;</td>
					<td width='{$ancho_prompt}'>
						<select name='opcion1' id='opcion1' class='css_default' onChange='javascript:busqueda_avanzada(1);'>
							<option value=0>Select a field to search...</option>";
	$tot_tit_arreglo = count($titulos); 
	for($i = 0; $i < $tot_tit_arreglo; $i++) {
		echo "
							<option value='".$titulos[$i]."'>$titulos[$i]</option> ";
	}
	echo "
						</select>
					</td>	
					<td width='{$ancho_filtro}'><div id='filtro1'></div></td>
				</tr>";
  if(!empty($campo[0])) {
    $valor_filtro=recupera_opcion($campo[0]);
    echo "
    <script type='text/javascript'>
      $('#opcion1').val('$valor_filtro');
      busqueda_avanzada(1);
    </script>";
  }
    
	# Segundo filtro
  $visible = (!empty($campo[1])) ? "block" : "none";
	echo "
				<tr style='display: $visible' id='campo2'>
          <td width='20'><a href='javascript:agregar_filtro(3);'><img src='".PATH_IMAGES."/".IMG_AGREGAR."' title='Add filter'></a></td>
          <td width='30'><a href='javascript:quitar_filtro(2);'><img src='".PATH_IMAGES."/".IMG_BORRAR."' title='Remove filter'></a></td>
					<td width='{$ancho_prompt}'>
						<select name='opcion2' id='opcion2' class='css_default' onChange='javascript:busqueda_avanzada(2);'>
							<option value=0>Select a field to search...</option>";
	$tot_tit_arreglo = count($titulos); 
	for($i = 0; $i < $tot_tit_arreglo; $i++) {
		echo "
							<option value='".$titulos[$i]."'>$titulos[$i]</option> ";
	}
	echo "
						</select>
					</td>	
					<td width='{$ancho_filtro}'><div id='filtro2'></div></td>
				</tr>";
  
   if(!empty($campo[1])) {
    $valor_filtro=recupera_opcion($campo[1]);
    echo "
    <script type='text/javascript'>
      $('#opcion2').val('$valor_filtro');
      busqueda_avanzada(2);
    </script>";
  }
				
	# Tercer filtro
  $visible = (!empty($campo[2])) ? "block" : "none";
	echo "
        <tr style='display: $visible'  id='campo3'>
          <td width='20'><a href='javascript:agregar_filtro(4);'><img src='".PATH_IMAGES."/".IMG_AGREGAR."' title='Add filter'></a></td>
          <td width='30'><a href='javascript:quitar_filtro(3);'><img src='".PATH_IMAGES."/".IMG_BORRAR."' title='Remove filter'></a></td>
					<td width='{$ancho_prompt}'>
						<select name='opcion3' id='opcion3' class='css_default' onChange='javascript:busqueda_avanzada(3);'>
							<option value=0>Select a field to search...</option>";
	$tot_tit_arreglo = count($titulos); 
	for($i = 0; $i < $tot_tit_arreglo; $i++) {
		echo "
							<option value='".$titulos[$i]."'>$titulos[$i]</option> ";
	}
	echo "
						</select>
					</td>	
					<td width='{$ancho_filtro}'><div id='filtro3'></div></td>
				</tr>";
        
  if(!empty($campo[2])) {
    $valor_filtro=recupera_opcion($campo[2]);
    echo "
    <script type='text/javascript'>
      $('#opcion3').val('$valor_filtro');
      busqueda_avanzada(3);
    </script>";
  }
  
	# Cuarto filtro
  $visible = (!empty($campo[3])) ? "block" : "none";
	echo "
				<tr style='display: $visible'  id='campo4'>
          <td width='20'><a href='javascript:agregar_filtro(5);'><img src='".PATH_IMAGES."/".IMG_AGREGAR."' title='Add filter'></a></td>
          <td width='30'><a href='javascript:quitar_filtro(4);'><img src='".PATH_IMAGES."/".IMG_BORRAR."' title='Remove filter'></a></td>
					<td width='{$ancho_prompt}'>
						<select name='opcion4' id='opcion4' class='css_default' onChange='javascript:busqueda_avanzada(4);'>
							<option value=0>Select a field to search...</option>";
	$tot_tit_arreglo = count($titulos); 
	for($i = 0; $i < $tot_tit_arreglo; $i++) {
		echo "
							<option value='".$titulos[$i]."'>$titulos[$i]</option> ";
	}
	echo "
						</select>
					</td>	
					<td width='{$ancho_filtro}'><div id='filtro4'></div></td>
				</tr>";
        
  if(!empty($campo[3])) {
    $valor_filtro=recupera_opcion($campo[3]);
    echo "
    <script type='text/javascript'>
      $('#opcion4').val('$valor_filtro');
      busqueda_avanzada(4);
    </script>";
  }
        
  # Quinto filtro
  $visible = (!empty($campo[4])) ? "block" : "none";
	echo "
				<tr style='display: $visible'  id='campo5'>
          <td width='20'><a href='javascript:agregar_filtro(6);'><img src='".PATH_IMAGES."/".IMG_AGREGAR."' title='Add filter'></a></td>
          <td width='30'><a href='javascript:quitar_filtro(5);'><img src='".PATH_IMAGES."/".IMG_BORRAR."' title='Remove filter'></a></td>
					<td width='{$ancho_prompt}'>
						<select name='opcion5' id='opcion5' class='css_default' onChange='javascript:busqueda_avanzada(5);'>
							<option value=0>Select a field to search...</option>";
	$tot_tit_arreglo = count($titulos); 
	for($i = 0; $i < $tot_tit_arreglo; $i++) {
		echo "
							<option value='".$titulos[$i]."'>$titulos[$i]</option> ";
	}
	echo "
						</select>
					</td>	
					<td width='{$ancho_filtro}'><div id='filtro5'></div></td>
				</tr>";
        
   if(!empty($campo[4])) {
    $valor_filtro=recupera_opcion($campo[4]);
    echo "
    <script type='text/javascript'>
      $('#opcion5').val('$valor_filtro');
      busqueda_avanzada(5);
    </script>";
  }
        
  # Sexto filtro
  $visible = (!empty($campo[5])) ? "block" : "none";
	echo "
				<tr style='display: $visible'  id='campo6'>
          <td width='20'><a href='javascript:agregar_filtro(7);'><img src='".PATH_IMAGES."/".IMG_AGREGAR."' title='Add filter'></a></td>
          <td width='30'><a href='javascript:quitar_filtro(6);'><img src='".PATH_IMAGES."/".IMG_BORRAR."' title='Remove filter'></a></td>
					<td width='{$ancho_prompt}'>
						<select name='opcion6' id='opcion6' class='css_default' onChange='javascript:busqueda_avanzada(6);'>
							<option value=0>Select a field to search...</option>";
	$tot_tit_arreglo = count($titulos); 
	for($i = 0; $i < $tot_tit_arreglo; $i++) {
		echo "
							<option value='".$titulos[$i]."'>$titulos[$i]</option> ";
	}
	echo "
						</select>
					</td>	
					<td width='{$ancho_filtro}'><div id='filtro6'></div></td>
				</tr>";
  
   if(!empty($campo[5])) {
    $valor_filtro=recupera_opcion($campo[5]);
    echo "
    <script type='text/javascript'>
      $('#opcion6').val('$valor_filtro');
      busqueda_avanzada(6);
    </script>";
  }
  
  # Septimo filtro
  $visible = (!empty($campo[6])) ? "block" : "none";
	echo "
				<tr style='display: $visible'  id='campo7'>
          <td width='20'>&nbsp;</td>
          <td width='30'><a href='javascript:quitar_filtro(7);'><img src='".PATH_IMAGES."/".IMG_BORRAR."' title='Remove filter'></a></td>
					<td width='{$ancho_prompt}'>
						<select name='opcion7' id='opcion7' class='css_default' onChange='javascript:busqueda_avanzada(7);'>
							<option value=0>Select a field to search...</option>";
	$tot_tit_arreglo = count($titulos); 
	for($i = 0; $i < $tot_tit_arreglo; $i++) {
		echo "
							<option value='".$titulos[$i]."'>$titulos[$i]</option> ";
	}
	echo "
						</select>
					</td>	
					<td width='{$ancho_filtro}'><div id='filtro7'></div></td>
				</tr>";
  
   if(!empty($campo[6])) {
    $valor_filtro=recupera_opcion($campo[6]);
    echo "
    <script type='text/javascript'>
      $('#opcion7').val('$valor_filtro');
      busqueda_avanzada(7);
    </script>";
  }  
  echo "
			</table>
		</form>
    <div style='text-align: center;'>
      <a id_'link' href='javascript:actualiza_listado();'>Go!</a>

      &nbsp;&nbsp;&nbsp;
      <a href='javascript:cierraBusquedaAvanzada();'>Cancel</a>
    </div>";
  function recupera_opcion($p_campo) {
    switch ($p_campo) {
        case ('ds_login'):
          $opcion=ETQ_USUARIO;
          break;
        case ('ds_nombres'):
          $opcion=ETQ_NOMBRE;
          break;
        case ('ds_apaterno'):
          $opcion=ObtenEtiqueta(118);
          break;
        case ('ds_amaterno'):
          $opcion=ObtenEtiqueta(119);
          break;
        case ('fg_genero'):
          $opcion=ObtenEtiqueta(114);
          break;
        case ('fe_nacimiento'):
          $opcion=ObtenEtiqueta(120);
          break; 
        case ('ds_email'):
          $opcion=ObtenEtiqueta(121);
          break;
        case ('fg_activo'):
          $opcion=ObtenEtiqueta(113);
          break;
        case ('fe_alta'):
          $opcion=ObtenEtiqueta(111);
          break;
        case ('fe_ultacc'):
          $opcion=ObtenEtiqueta(112);
          break;
        case ('fe_ultmod'):
          $opcion=ObtenEtiqueta(340);
          break;
        case ('nb_programa'):
          $opcion=ObtenEtiqueta(360);
          break;  
        case ('ds_profesor'):
          $opcion=ObtenEtiqueta(297);
          break;
        case ('nb_grupo'):
          $opcion=ObtenEtiqueta(426);
          break;
        case ('no_grado'):
          $opcion=ObtenEtiqueta(375);
          break;
        case ('ds_add_city'):
          $opcion=ObtenEtiqueta(284);
          break; 
        case ('ds_add_state'):
          $opcion=ObtenEtiqueta(285);
          break;
        case ('ds_pais'):
          $opcion=ObtenEtiqueta(287);
          break;
        case ('nb_periodo'):
          $opcion=ObtenEtiqueta(342);
          break;
        case ('fg_international'):
          $opcion=ObtenEtiqueta(619);
          break;
        case ('nb_zona_horaria'):
          $opcion=ObtenEtiqueta(411);
          break;
        case ('fe_start_date'):
          $opcion=ObtenEtiqueta(60);
          break;  
        case ('fe_carta'):
          $opcion=ObtenEtiqueta(540);
          break;  
        case ('fe_contrato'):
          $opcion=ObtenEtiqueta(541);
          break;  
        case ('fe_fin'):
          $opcion=ObtenEtiqueta(544);
          break;  
        case ('fe_completado'):
          $opcion=ObtenEtiqueta(545);
          break;  
        case ('fe_emision'):
          $opcion=ObtenEtiqueta(546);
          break;  
        case ('fg_certificado'):
          $opcion=ObtenEtiqueta(547);
          break;  
        case ('fg_honores'):
          $opcion=ObtenEtiqueta(548);
          break;  
        case ('fe_graduacion'):
          $opcion=ObtenEtiqueta(556);
          break;  
        case ('fg_desercion'):
          $opcion=ObtenEtiqueta(558);
          break;  
        case ('fg_dismissed'):
          $opcion=ObtenEtiqueta(559);
          break;  
        case ('fg_job'):
          $opcion=ObtenEtiqueta(644);
          break;
        case ('fg_graduacion'):
          $opcion=ObtenEtiqueta(645);
          break;  
        case ('ye_fe_nacimiento'):
          $opcion=ObtenEtiqueta(670);
          break;
        case ('ye_fe_alta'):
          $opcion=ObtenEtiqueta(671);
          break;
        case ('ye_fe_ultacc'):
          $opcion=ObtenEtiqueta(672);
          break;
        case ('ye_fe_ultmod'):
          $opcion=ObtenEtiqueta(673);
          break;
        case ('ye_fe_start_date'):
          $opcion=ObtenEtiqueta(674);
          break; 
        case ('ye_fe_carta'):
          $opcion=ObtenEtiqueta(675);
          break;
        case ('ye_fe_contrato'):
          $opcion=ObtenEtiqueta(676);
          break;
        case ('ye_fe_fin'):
          $opcion=ObtenEtiqueta(677);
          break;
        case ('ye_fe_completado'):
          $opcion=ObtenEtiqueta(678);
          break;
        case ('ye_fe_emision'):
          $opcion=ObtenEtiqueta(679);
          break; 
        case ('ye_fe_graduacion'):
          $opcion=ObtenEtiqueta(680);
          break; 
      }
      return $opcion;
    }

?>