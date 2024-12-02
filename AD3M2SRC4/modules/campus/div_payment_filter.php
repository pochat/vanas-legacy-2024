<?php

  # La libreria de funciones
  require '../../lib/general.inc.php';


  # Recupera el usuario actual
  $fl_usuario = ValidaSesion( );

  # Recibe parametros
  $inicializa = RecibeParametroNumerico('inicializa');

  #fechas
  $mes_anio_ac = date('F Y');
  $mes_pasado = date('F Y', strtotime('-1 month', strtotime(date("Y-m-01"))));
  $mes_pasado2 =  date('F Y', strtotime('-2 month', strtotime(date("Y-m-01"))));
  $mes_sig = date('F Y', strtotime('1 month', strtotime(date("Y-m-01"))));
  $mes2_sig = date('F Y', strtotime('2 months', strtotime(date("Y-m-01"))));

	# Arreglo de los titulos de las opciones de busqueda avanzada
  $opc_dates = array(1=> 'The Last 2 Weeks', 'The last 30 days',$mes_anio_ac,$mes_pasado,$mes_pasado2, 'To be Paid');
  $opc_dates_det = array(1=> $mes_anio_ac,$mes_pasado,$mes_pasado2, 'To be Paid');
  $fe_limt = array(1=>$mes_pasado2, $mes_pasado, $mes_anio_ac, $mes_sig, $mes2_sig);

  if(!empty($inicializa)) {
    $parametro=43;
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $dates = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos1 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos2 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos3 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos4 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos5 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fpagos6 = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fec_limit = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fg_students = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fg_nstudents= $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fl_pais = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fg_earned_un = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fg_activo_in = $row[0];
    //estas ConsultaFechaBD para el formato de fecha la se inserte y muestre
    $row = RecuperaValor("SELECT ".ConsultaFechaBD('ds_valor', FMT_FECHA)." FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $startdue = $row[0];
    $row = RecuperaValor("SELECT ".ConsultaFechaBD('ds_valor', FMT_FECHA)." FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $enddue = $row[0];
    $row = RecuperaValor("SELECT ".ConsultaFechaBD('ds_valor', FMT_FECHA)." FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $startdate = $row[0];
    $row = RecuperaValor("SELECT ".ConsultaFechaBD('ds_valor', FMT_FECHA)." FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $enddate = $row[0];
    $row = RecuperaValor("SELECT ds_valor FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $fg_detalle = $row[0];
    $row = RecuperaValor("SELECT ".ConsultaFechaBD('ds_valor', FMT_FECHA)." FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $startdetalle = $row[0];
    $row = RecuperaValor("SELECT ".ConsultaFechaBD('ds_valor', FMT_FECHA)." FROM k_usu_parametro WHERE fl_parametro_funcion=".$parametro++." AND fl_usuario=$fl_usuario");
    $enddetalle = $row[0];
  }
  else{
    $fg_students = 0;
    $fg_nstudents = 0;
    $fg_earned_un = 0;
    $fg_activo_in = 1;
    $dates = 3;
  }

  # Funcion para obtener los filtros dinamicos
	echo "
	<script type='text/javascript'>
		function actualiza_listado() {
			$('#frm_avanzada').submit();
        $.ajax({
          type: 'POST',
          url : 'criterio.php',
          data: 'criterio='+document.forms['Search'].elements[0].value +
          '&fg_export_cvs='+$('#fg_export_cvs').val()+
          '&fg_students='+$('#fg_students').val()+
          '&fg_nstudents='+$('#fg_nstudents').val()+
          '&fg_earned_un='+$('#fg_earned_un').val()+
          '&fg_activo_in='+$('#fg_activo_in').val()+
          '&fpagos1='+$('#fpagos1').val()+
          '&fpagos2='+$('#fpagos2').val()+
          '&fpagos3='+$('#fpagos3').val()+
          '&fpagos4='+$('#fpagos4').val()+
          '&fpagos5='+$('#fpagos5').val()+
          '&fpagos6='+$('#fpagos6').val()+
          '&opcion1='+$('#opcion1').val()+
          '&opcion2='+$('#opcion2').val()+
          '&opcion3='+$('#opcion3').val()+
          '&startdue='+$('#startdue').val()+
          '&enddue='+$('#enddue').val()+
          '&startdate='+$('#startdate').val()+
          '&enddate='+$('#enddate').val(),
          async: false,
          success: function(html) {
            $('#div_principal').html(html);
          }
        });

		}
    function cierraBusquedaAvanzada() {
      $('#div_principal').html('');
      $('#div_principal').css('display', 'none');
    }

    function exportcvs(){
        $('#frm_avanzada').submit();
        $.ajax({
              type: 'POST',
              url : 'criterio.php',
              data: 'criterio='+document.forms['Search'].elements[0].value +
                  '&fg_students='+$('#fg_students').val()+
                  '&fg_export_cvs=1'+
                  '&fg_nstudents='+$('#fg_nstudents').val()+
                  '&fg_earned_un='+$('#fg_earned_un').val()+
                  '&fg_activo_in='+$('#fg_activo_in').val()+
                  '&fpagos1='+$('#fpagos1').val()+
                  '&fpagos2='+$('#fpagos2').val()+
                  '&fpagos3='+$('#fpagos3').val()+
                  '&fpagos4='+$('#fpagos4').val()+
                  '&fpagos5='+$('#fpagos5').val()+
                  '&fpagos6='+$('#fpagos6').val()+
                  '&opcion1='+$('#opcion1').val()+
                  '&opcion2='+$('#opcion2').val()+
                  '&opcion3='+$('#opcion3').val()+
                  '&startdue='+$('#startdue').val()+
                  '&enddue='+$('#enddue').val()+
                  '&startdate='+$('#startdate').val()+
                  '&enddate='+$('#enddate').val(),
                      async: false,
                      success: function(html) {
                        $('#div_principal').html(html);
                      }
                });

    }

    </script>";

  echo "
		<form action='payments.php' method='POST' name='frm_avanzada' id='frm_avanzada'>
      <div style='text-align: center; background-color: #0681C7; color: #FFF;font-weight: bold; font-size:13px;'>Advanced Search filters</div>
      <input type='hidden' name='actual' id='actual' value='12'>
      <input type='hidden' name='nuevo' id='nuevo' value='1'>
			<table style='font-size:12px; width:100%;' border='0' align='center' cellPadding='2' cellSpacing='0' id='table_filter' >";

	# checkbox y filtro para la busqueda avanzada

	echo "
				<tr style='display: block;'>
          <td width='30%'>
            <div style='padding-bottom:10px; font-weight:bold; text-align:center;'>".ObtenEtiqueta(745)."</div>
            <table style='font-size:12px;'>
              <tr>
                <td width='125px'>
                  <input type='radio' id='fg_students' name='fg_students' value='0'";if($fg_students==0) echo "checked"; echo ">".ObtenEtiqueta(746)."<br />
                  <input type='radio' id='fg_students' name='fg_students' value='1'";if($fg_students==1) echo "checked"; echo ">".ObtenEtiqueta(747)." <br />
                  <input type='radio' id='fg_students' name='fg_students' value='2'";if($fg_students==2) echo "checked"; echo ">".ObtenEtiqueta(748)."
                </td>
                <td width='125px'>
                  <input type='radio' id='fg_nstudents' name='fg_nstudents' value='0'";if($fg_nstudents==0) echo "checked"; echo ">".ObtenEtiqueta(746)." <br />
                  <input type='radio' id='fg_nstudents' name='fg_nstudents' value='1'";if($fg_nstudents==1) echo "checked"; echo ">".ObtenEtiqueta(749)." <br />
                  <input type='radio' id='fg_nstudents' name='fg_nstudents' value='2'";if($fg_nstudents==2) echo "checked"; echo ">".ObtenEtiqueta(750)."
                </td>
              </tr>
              <tr>
                <td colspan='2'>
                  <br /><div style='padding-bottom:10px; font-weight:bold; text-align:center;'>".ObtenEtiqueta(751)."/".ObtenEtiqueta(752)."</div>
                  <input type='radio' id='fg_earned_un' name='fg_earned_un' value='0'";if($fg_earned_un==0) echo "checked"; echo "> ".ObtenEtiqueta(746)."
                  <input type='radio' id='fg_earned_un' name='fg_earned_un' value='1'";if($fg_earned_un==1) echo "checked"; echo "> ".ObtenEtiqueta(751)."
                  <input type='radio' id='fg_earned_un' name='fg_earned_un' value='2'";if($fg_earned_un==2) echo "checked"; echo "> ".ObtenEtiqueta(752)."
                </td>
              </tr>
              <tr>
              <td colspan='2'>
                  <br /><div style='padding-bottom:10px; font-weight:bold; text-align:center;'>".ObtenEtiqueta(753)."/".ObtenEtiqueta(754)."</div>
                  <input type='radio' id='fg_activo_in' name='fg_activo_in' value='0'";if($fg_activo_in==0) echo "checked"; echo "> ".ObtenEtiqueta(746)."
                  <input type='radio' id='fg_activo_in' name='fg_activo_in' value='1'";if($fg_activo_in==1) echo "checked"; echo "> ".ObtenEtiqueta(753)."  &nbsp;
                  <input type='radio' id='fg_activo_in' name='fg_activo_in' value='2'";if($fg_activo_in==2) echo "checked"; echo "> ".ObtenEtiqueta(754)."
                </td>
              </tr>
            </table>
          </td>
					<td width='25%' >
            <div style='padding-bottom:10px; font-weight:bold;'>".ObtenEtiqueta(755)."</div>
            <input type='checkbox'  name='fpagos1' id='fpagos1' value='1'";if(!empty($fpagos1)) echo "checked";echo">".ObtenEtiqueta(488)."<br />
            <input type='checkbox'  name='fpagos2' id='fpagos2' value='2'";if(!empty($fpagos2)) echo "checked";echo">Pay Pal Manual<br />
            <input type='checkbox'  name='fpagos3' id='fpagos3' value='3'";if(!empty($fpagos3)) echo "checked";echo">Cheque<br />
            <input type='checkbox'  name='fpagos4' id='fpagos4' value='4'";if(!empty($fpagos4)) echo "checked";echo">Credit Card<br />
            <input type='checkbox'  name='fpagos5' id='fpagos5' value='5'";if(!empty($fpagos5)) echo "checked";echo">Wire Transfer/Deposit<br />
            <input type='checkbox'  name='fpagos6' id='fpagos6' value='6'";if(!empty($fpagos6)) echo "checked";echo">Cash<br /><br /><br />
            <div style='padding-bottom:10px; font-weight:bold;'>".ObtenEtiqueta(756)."...</div>
            <select name='opcion3' id='opcion3' style='font-size:10px;' class='css_default' >
              <option value=0>Select one...</option>";
              $Query = "SELECT DISTINCT(ds_pais), fl_pais FROM k_ses_app_frm_1 a, c_pais b, c_usuario c WHERE  a.cl_sesion = c.cl_sesion AND a.ds_add_country = b.fl_pais ";
              $rs = EjecutaQuery($Query);
              for($i=1;$row= RecuperaRegistro($rs);$i++){
                $ds_pais = $row[0];
                $fl_pais1 = $row[1];
                echo "<option value='$fl_pais1'"; if($fl_pais==$fl_pais1) echo "selected"; echo ">$ds_pais </option>";
              }
      echo "</select>
          </td>
          <td width='22%'>
            <div style='padding-bottom:10px; font-weight:bold;'>".ObtenEtiqueta(757)."</div>
            <select name='opcion1' id='opcion1' style='font-size:10px;' class='css_default' >
              <option value=0>Select one...</option>";
              for ($i=1; $i <=5; $i++){
                echo "<option value='".$i."'";if($fec_limit==$i) echo "selected"; echo ">$fe_limt[$i]</option>";
              }
      echo "
            </select>
            <br /><br /><br /><br />
            <div style='padding-bottom:10px; font-weight:bold;'>".ObtenEtiqueta(758)."</div>
            <select name='opcion2' id='opcion2' style='font-size:10px;' class='css_default' >
              <option value=0>Select one...</option>";
              for ($i=1; $i <=6; $i++){
                echo "<option value='".$i."'";if($dates==$i) echo "selected"; echo ">$opc_dates[$i]</option>";
              }
      echo "
            </select><br /><br /><br /><br />
            <div style='padding-bottom:10px; font-weight:bold;'>".ObtenEtiqueta(744)."</div>
            <select name='fg_detalle' id='fg_detalle' style='font-size:10px;' class='css_default'>
              <option value=0>Select one...</option>";
              for ($i=1; $i <=4; $i++){
                echo "<option value='".$i."'";if($fg_detalle==$i) echo "selected"; echo ">$opc_dates_det[$i]</option>";
              }
      echo "</select>
          </td>
          <td width=160px>
          <div style='padding-bottom:10px; font-weight:bold;'>".ObtenEtiqueta(757)."</div>
          ".ObtenEtiqueta(60).": <input type='text' name='startdue' id='startdue' size='10' maxlength=' 10' class='css_default' value='".$startdue."'>&nbsp;".ObtenEtiqueta(513).": <input type='text' name='enddue' id='enddue' size='10' maxlength=' 10' class='css_default' value='".$enddue."'>";
          Forma_Calendario('startdue');
          Forma_Calendario('enddue');
      echo "<div style='padding-bottom:10px; font-weight:bold;'><br />".ObtenEtiqueta(758)."</div>
          ".ObtenEtiqueta(60).": <input type='text' name='startdate' id='startdate' size='10' maxlength=' 10' class='css_default' value='".$startdate."'>&nbsp;".ObtenEtiqueta(513).": <input type='text' name='enddate' id='enddate' size='10' maxlength=' 10' class='css_default' value='".$enddate."'>";
          Forma_Calendario('startdate');
          Forma_Calendario('enddate');
      echo "<div style='padding-bottom:10px; font-weight:bold;'><br />".ObtenEtiqueta(744)."</div>
          ".ObtenEtiqueta(60).":
          <input type='text' name='startdetalle' id='startdetalle' size='10' maxlength=' 10' class='css_default' value='".$startdetalle."'>
          &nbsp;".ObtenEtiqueta(513).": <input type='text' name='enddetalle' id='enddetalle' size='10' maxlength=' 10' class='css_default' value='".$enddetalle."'>";
          Forma_Calendario('startdetalle');
          Forma_Calendario('enddetalle');

          $opc = array('No','Yes','Quickbooks online (QBO)'); // Masculino, Femenino
          $val = array('0', '1','2');
          Forma_CampoSelect('Export CVS', False, 'fg_export_cvs', $opc, $val, 0,'','','','left','col col-sm-12','col col-sm-12');



      echo "
          </td>
        </tr>";
  echo "
			</table>
		</form>
    <div style='text-align: center;' id='botones'>

        <a id_'link' href='javascript:actualiza_listado();' id='guarda'>Go!</a>
            &nbsp;&nbsp;&nbsp;
        <a href='payments.php'>Reset</a>
    </div>";

?>