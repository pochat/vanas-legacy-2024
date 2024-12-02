<?php

# ARV 12/02/14
# Programa que sirve para mostrar los filtros dinamicos de una busqueda avanzada mediante la seleccion 
# del tipo de campo puede ser de tipo fecha, de descripcion incluyendo numeros y letras o de tipo flag con opcion si o no
# La libreria de funciones
require '../../lib/general.inc.php';

$opcion1 = RecibeParametroHTML('opcion');
$filtro = RecibeParametroHTML('filtro');
$crit = RecibeParametroHTML('crit');
$fecha1 = RecibeParametroHTML('fecha1');
$fecha2 = RecibeParametroHTML('fecha2');
$opc = RecibeParametroNumerico('div_filtro');
// echo "opcion1:$opcion1 <br/> opc: $opc <br /> filtro: $filtro <br> crit: $crit <br> fecha1: $fecha1 <br> fecha2:$fecha2";
if (!empty($opcion1)) {

    # Arreglo de opciones para campos tipo flag YES NO
   // $opciones = array(ObtenEtiqueta(16), ObtenEtiqueta(17));
   // $opciones2 = array(ObtenEtiqueta(115), ObtenEtiqueta(116));

    # Arreglo de opciones de filtrado para los campos tipo descripcion y numericos
    $filtros = array(ObtenEtiqueta(681), ObtenEtiqueta(637), ObtenEtiqueta(638), ObtenEtiqueta(639), ObtenEtiqueta(640), ObtenEtiqueta(641));

    switch ($opcion1) {
        case (ETQ_USUARIO):
            $opcion = 'ds_login';
            break;
        case (ETQ_NOMBRE):
            $opcion = 'ds_nombres';
            break;
        case (ObtenEtiqueta(118)):
            $opcion = 'ds_apaterno';
            break;
        case (ObtenEtiqueta(119)):
            $opcion = 'ds_amaterno';
            break;
        case (ObtenEtiqueta(114)):
            $opcion = 'fg_genero';
            break;
        case (ObtenEtiqueta(120)):
            $opcion = 'fe_nacimiento';
            break;
        case (ObtenEtiqueta(121)):
            $opcion = 'ds_email';
            break;
        case (ObtenEtiqueta(113)):
            $opcion = 'fg_activo';
            break;
        case (ObtenEtiqueta(111)):
            $opcion = 'fe_alta';
            break;
        case (ObtenEtiqueta(112)):
            $opcion = 'fe_ultacc';
            break;
        case (ObtenEtiqueta(340)):
            $opcion = 'fe_ultmod';
            break;
        case (ObtenEtiqueta(360)):
            $opcion = 'nb_programa';
            break;
        case (ObtenEtiqueta(297)):
            $opcion = 'ds_profesor';
            break;
        case (ObtenEtiqueta(426)):
            $opcion = 'nb_grupo';
            break;
        case (ObtenEtiqueta(375)):
            $opcion = 'no_grado';
            break;
        case (ObtenEtiqueta(284)):
            $opcion = 'ds_add_city';
            break;
        case (ObtenEtiqueta(285)):
            $opcion = 'ds_add_state';
            break;
        case (ObtenEtiqueta(287)):
            $opcion = 'ds_pais';
            break;
        case (ObtenEtiqueta(342)):
            $opcion = 'nb_periodo';
            break;
        case (ObtenEtiqueta(619)):
            $opcion = 'fg_international';
            break;
        case (ObtenEtiqueta(411)):
            $opcion = 'nb_zona_horaria';
            break;
        case (ObtenEtiqueta(60)):
            $opcion = 'fe_start_date';
            break;
        case (ObtenEtiqueta(540)):
            $opcion = 'fe_carta';
            break;
        case (ObtenEtiqueta(541)):
            $opcion = 'fe_contrato';
            break;
        case (ObtenEtiqueta(544)):
            $opcion = 'fe_fin';
            break;
        case (ObtenEtiqueta(545)):
            $opcion = 'fe_completado';
            break;
        case (ObtenEtiqueta(546)):
            $opcion = 'fe_emision';
            break;
        case (ObtenEtiqueta(547)):
            $opcion = 'fg_certificado';
            break;
        case (ObtenEtiqueta(548)):
            $opcion = 'fg_honores';
            break;
        case (ObtenEtiqueta(556)):
            $opcion = 'fe_graduacion';
            break;
        case (ObtenEtiqueta(558)):
            $opcion = 'fg_desercion';
            break;
        case (ObtenEtiqueta(559)):
            $opcion = 'fg_dismissed';
            break;
        case (ObtenEtiqueta(644)):
            $opcion = 'fg_job';
            break;
        case (ObtenEtiqueta(645)):
            $opcion = 'fg_graduacion';
            break;
        case (ObtenEtiqueta(670)):
            $opcion = 'ye_fe_nacimiento';
            break;
        case (ObtenEtiqueta(671)):
            $opcion = 'ye_fe_alta';
            break;
        case (ObtenEtiqueta(672)):
            $opcion = 'ye_fe_ultacc';
            break;
        case (ObtenEtiqueta(673)):
            $opcion = 'ye_fe_ultmod';
            break;
        case (ObtenEtiqueta(674)):
            $opcion = 'ye_fe_start_date';
            break;
        case (ObtenEtiqueta(675)):
            $opcion = 'ye_fe_carta';
            break;
        case (ObtenEtiqueta(676)):
            $opcion = 'ye_fe_contrato';
            break;
        case (ObtenEtiqueta(677)):
            $opcion = 'ye_fe_fin';
            break;
        case (ObtenEtiqueta(678)):
            $opcion = 'ye_fe_completado';
            break;
        case (ObtenEtiqueta(679)):
            $opcion = 'ye_fe_emision';
            break;
        case (ObtenEtiqueta(680)):
            $opcion = 'ye_fe_graduacion';
            break;
    }

    $tipo = substr($opcion, 0, 2);

    echo "
      <input type='hidden' name='actual' id='actual' value='6'>
      <input type='hidden' name='nuevo' id='nuevo' value='1'>";

    switch ($tipo) {

        case 'fg':
            echo "
			<input type='hidden' name='opcion_$opc' id='opcion_$opc' value='$opcion'>      
			<input type='hidden' name='tipo_$opc' id='tipo_$opc' value='1'>
        <div class='col col-sm-6'><label class='select'>
        <select name='filtro_$opc' id='filtro_$opc' class='form-control input-sm' >";
            $tot_opc_arreglo = count($opciones);
            for ($i = 0; $i < $tot_opc_arreglo; $i++) {
                if ($opcion == 'fg_genero') {
                    $seleccionado = (($i + 1) == $filtro) ? " selected" : "";
                    echo "
          <option value=" . ($i + 1) . "$seleccionado>$opciones2[$i]</option> ";
                } else {
                    $seleccionado = (($i + 1) == $filtro) ? " selected" : "";
                    echo "
					<option value=" . ($i + 1) . "$seleccionado>$opciones[$i]</option> ";
                }
            }
            echo "
				</select><i></i>
        </label>
        </div>";

            break;
        case 'fe':
            echo "
			<input type='hidden' name='opcion_$opc' id='opcion_$opc' value='$opcion'>
			<input type='hidden' name='tipo_$opc' id='tipo_$opc' value='2'>
			<div class='col col-sm-6'><label class='input'><input type='text' name='fe_uno_$opc' id='fe_uno_$opc' maxlength='10' class='datepicker hasDatepicker' value='$fecha1' placeholder='" . ObtenEtiqueta(643) . "'>" . Forma_Calendario("fe_uno_$opc") . "</label></div>
      <div class='col col-sm-6'><label class='input'><input type='text' name='fe_dos_$opc' id='fe_dos_$opc' maxlength='10' class='datepicker hasDatepicker' value='$fecha2' placeholder='" . ObtenEtiqueta(642) . "'>" . Forma_Calendario("fe_dos_$opc")."</label></div>";
            break;
        default:
            echo "
			<input type='hidden' name='opcion_$opc' id='opcion_$opc' value='$opcion'>
			<input type='hidden' name='tipo_$opc' id='tipo_$opc' value='3'>
          <div class='col col-sm-6'><label class='select'>
						<select name='filtro_$opc' id='filtro_$opc' class='form-control input-sm'>";
            $tot_fil_arreglo = count($filtros);
            for ($i = 0; $i < $tot_fil_arreglo; $i++) {
                $seleccionado = (($i + 1) == $filtro) ? " selected" : "";
                echo "
								<option value=" . ($i + 1) . "$seleccionado>$filtros[$i]</option> ";
            }
            echo "
						</select><i></i>
          </label>
          </div>	
					<div class='col col-sm-6'><label class='input'><input type='text' maxlength='250' size='30' class='form-control input-sm' name='criterio_$opc' id='criterio_$opc' value='$crit'></label></div>";
            break;
    }
}
else
  echo "
    <input type='hidden' name='all' id='all' value='1'>";
//Dbg::printQuerys();
?>