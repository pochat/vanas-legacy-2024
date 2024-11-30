<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  # Muestra los meses 
  echo "
  <div class='row'>
    <!-- Payment-->
    <div class='col-xs-12'>
      <div class='well well-light padding-10'>
        <div class='row'>
          <div class='col-xs-12'> 
            <div class='well well-light no-margin no-padding'>
              <div class='well well-light no-margin'>
                <h6 class='text-center no-margin padding-5'>".ObtenEtiqueta(732)."</h6>
              </div>
              <div class='well well-light no-margin no-padding'>
                <table id='dt_basic' class='table table-striped table-bordered table-hover'>
                  <thead>
                    <tr>
                    <th style='display:none;'></th>
                    <th>".ObtenEtiqueta(713)."</th>
                    <th class='text-center'>".ObtenEtiqueta(733)."</th>
                    <th class='text-center'>".ObtenEtiqueta(729)."</th>
                    </tr>
                  </thead>
                  <tbody>";
                  # Muestra registros de los teachers
                  $Query  = "SELECT fl_maestro_pago, DATE_FORMAT(fe_periodo,'%M, %Y'), mn_total, CASE fg_pagado WHEN '1' THEN 'Paid' ELSE 'To be Paid' END fg_pagado, ";
                  $Query .= "MONTH(fe_pagado),CASE fg_pagado WHEN '0' THEN '".ObtenEtiqueta(654)."' WHEN '1' THEN '".ObtenEtiqueta(655)."'  END fg_pagado ";
                  $Query .= "FROM k_maestro_pago WHERE fl_maestro=$fl_usuario  AND fg_publicar='1' ";
                  $Query .= "ORDER BY fl_maestro_pago DESC ";
                  $rs = EjecutaQuery($Query);
                  $suma=0;
                  for($i=0;$row=RecuperaRegistro($rs);$i++){  
                      
                      $fecha=$row[1];
                      $fl_maestro_pago=$row['fl_maestro_pago'];
                      $mn_monto=$row['mn_total'];

                      $Query="SELECT fg_tipo FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago  ";
                      $ro=RecuperaValor($Query);
                      $fg_tipo=!empty($ro['fg_tipo'])?$ro['fg_tipo']:NULL;

                      


                      #Nota: El query viene varios registros de la misma periodo pertenecientes alas clases grupales, no encontre otra forma de omitir para que solo s
                      #se presente un regiuatro por periodo asiq ue se verfifica que la fecha que proviene del query npo se repita y ademas solo se toma en cuenta el monto mayor ya que ese monto
                      #ya viene incluido la suma de los otrso registros provenientes de la misma fecha y que corresponden al clases grupales., saludos a todos.

                      # bellow if commented because the variable "$fecha_registro" don't exists
                      //if($fecha_registro!=$fecha){

                          $Query="SELECT MAX(mn_total), fl_maestro_pago FROM k_maestro_pago WHERE fl_maestro=".$fl_usuario." AND fg_publicar='1' AND DATE_FORMAT(fe_periodo,'%M, %Y')='$fecha' ";
                          $roe=RecuperaValor($Query);
                          $mn_monto=$roe[0];
                          $fl_maestro_pago=$roe['fl_maestro_pago'];

                          # Enviamos como parametro el fl_maestro_pago
                          echo "
                            <tr>
                              <td style='display:none;'>&nbsp;</td>
                              <td><a href='index.php#ajax/payment_iu.php?fl_maestro_pago=".$fl_maestro_pago."'>".$row[1]."&nbsp;&nbsp;-&nbsp;".ObtenEtiqueta(761)."</a></td>
                              <td class='text-center'><a href='index.php#ajax/payment_iu.php?fl_maestro_pago=".$fl_maestro_pago."'>$ ".number_format($mn_monto,2,'.',',')."</a></td>
                              <td class='text-center'><a href='index.php#ajax/payment_iu.php?fl_maestro_pago=".$fl_maestro_pago."'> ".$row[5]."</a></td>
                            </tr>";
                      # bellow "}" commented because the command if is commented
                      //}
                       
                     $fecha_registro=$fecha;

                  }  
                  echo "
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- script para la talas con busueda -->
  <script src='".PATH_N_COM_JS."/plugin/datatables/jquery.dataTables-cust.js'></script>
  <!--<script src='".PATH_N_COM_JS."/plugin/datatables/DT_bootstrap.js'></script>-->
  <script type='text/javascript'>
  $(document).ready(function() {
    $('#dt_basic').dataTable({     
    });	

  })
  </script>";
  
?>