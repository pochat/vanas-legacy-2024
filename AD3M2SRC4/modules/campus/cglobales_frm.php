<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../modules/liveclass/bbb_api.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $destino = RecibeParametroNumerico('destino', True, False);
  if(!empty($destino))
    $clave = RecibeParametroNumerico('clave', True, False);
  else
    $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(132, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  

  #Obtenemos fecha actual :
  $Query = "Select CURDATE() ";
  $row = RecuperaValor($Query);
  $fe_actual = str_texto($row[0]);
  $fe_actual=strtotime('+0 day',strtotime($fe_actual));
  $fe_actual= date('Y-m-d',$fe_actual);

  $QueryS="SELECT id FROM zoom WHERE fg_activo='1' ";
  $rsS = EjecutaQuery($QueryS);
  for($tot_zo = 0; $row = RecuperaRegistro($rsS); $tot_zo++) {

      $host_id_email_zoom=$row[0];

      #vERIFICAMOS SI ES LA PRIMER CONEXION DEL DIA.
      $QUERY="SELECT COUNT(*) FROM zoom WHERE  DATE_FORMAT(fe_ultmod,'%Y-%m-%d')='$fe_actual' and id=$host_id_email_zoom ";
      $row=RecuperaValor($QUERY);
      $no_conexiones_al_dia=$row[0];
      #Aqui reseteamos elo contador a 0 del dia actual./*zoom solo permite 100 request por cuenta.*/
      if(empty($no_conexiones_al_dia)){
          EjecutaQuery("UPDATE zoom SET no_request=0 WHERE id=$host_id_email_zoom ");
      }

  }


  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      # Datos de la clase global
      # Obtenemos los programas de la clase global
      $programas_bd=NULL;
      $rs = EjecutaQuery("SELECT fl_programa FROM k_curso_cg WHERE fl_clase_global=$clave");
      for($i=0;$i<$row=RecuperaRegistro($rs);$i++){
        $programas_bd .= $row[0].",";
      }
      $programas_bd = explode(",", $programas_bd);    
      $Query  = "SELECT  ds_clase, no_alumnos, fg_dia_sesion, hr_sesion, fg_mandatory,fg_zoom ";
      $Query .= "FROM c_clase_global cg WHERE cg.fl_clase_global=$clave";
      $row = RecuperaValor($Query);
      // $fl_maestrog = $row[0];
      // $ds_login = $row[1];
      $ds_titulo = str_texto($row[0]);
      $no_alumnos = $row[1];
      $fg_dia_sesion = $row[2];
      $hr_sesion = $row[3];
      $fg_mandatory = $row[4];
      $fg_zoom=$row['fg_zoom'];

    }
    else { // Alta, inicializa campos
      $programas_bd = explode(",", $programas_bd);
      $fl_maestrog = 0;
      $ds_login = "";
      $ds_clase = "";
      $no_alumnos = 0;
      $fg_dia_sesion = 0;
      # Consultamos la tabla tempral si no hay registros
      $row = RecuperaValor("SELECT COUNT(*) FROM k_clase_cg_temporal");
      $sesiones = $row[0];  
	  #Elimnamos registros bsura que se quedan ahi.
	  EjecutaQuery("DELETE FROM k_clase_cg_temporal WHERE 1=1 ");

      if(empty($clave)){

          # Insertamos la nueva clase global
          $Query  = "INSERT INTO c_clase_global (fl_maestro, ds_clase, fg_dia_sesion, hr_sesion, fg_mandatory,fg_zoom) ";
          $Query .= "VALUES(0, '', 1,   '11:00:00', '1','1')";
          $clave = EjecutaInsert($Query);

      }



	  
    }    
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    # Obtebemos los programas que selecciono
    $programas_seleccionados_err = RecibeParametroNumerico('programas_seleccionados_err');    
    $ds_titulo = RecibeParametroHTML('ds_titulo');
    $ds_titulo_err = RecibeParametroHTML('ds_titulo_err');
    $fl_maestrog = RecibeParametroNumerico('fl_maestrog');
    $ds_clase = RecibeParametroHTML('ds_clase');
    $fg_dia_sesion = RecibeParametroNumerico('fg_dia_sesion');
    $fe_start_date = RecibeParametroFecha('fe_start_date');
    $hr_sesion = RecibeParametroHTML('hr_sesion');
    $fg_mandatory = RecibeParametroBinario('fg_mandatory');
    $fg_zoom=RecibeParametroBinario('fg_zoom');
  }
  
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(132);
  
  # Funciones para manejo de sesiones en vivo para lecciones
  echo "<script src='".PATH_JS."/clasesGlobales.js.php'></script>";
  
  # Inicia forma de captura
  Forma_Inicia($clave, True);
  ?>
  <div id='widget-grid' >
  <?php
  if($fg_error)
    Forma_PresentaError( );  
  ?>
  <div id='widget-grid' >
  <div role="widget" style="" class="jarviswidget" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-custombutton="false" data-widget-sortable="false">
    <header role="heading">
        <div role="menu" class="jarviswidget-ctrls">   
            <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
        </div>
        <span class="widget-icon"> <i class="fa fa-user"></i> </span>
        <h2>
          <strong>
          <?php echo ObtenEtiqueta(928); ?>
          </strong>
        </h2>
    </header>
    <!-- widget div-->
    <!--<div role="content" class="no-padding">-->
    <div class="no-padding">            
        <!-- widget content -->
        <div class="widget-body">          
          <!-- Campos para el maestroy la descripcion--->
          <div class="col col-xs-12 col-sm-10 col-lg-12" style="padding-left:40px;">
            <!--Lista de programas-->
            
              <div class="row">
                  <div class="col col-xs-12 col-sm-4">
                  </div>
                  <div class="col col-xs-12 col-sm-4">
                        <style>
                        .smart-form .radio input + i:after {
                            content: '';
                            top: 3px;
                            left: 3px;
                        }
                        </style>
                       <?php 
                       if($fg_zoom==1){
                           $checked_zoom_2="checked='checked'";
                           $checked_zoom_1="";
                       }else{
                           $checked_zoom_1="checked='checked'";
                           $checked_zoom_2="";
       
                       }
                       ?>

                      <div class="smart-form">
                        <label class="radio">
			            <input type="radio" name="optionsRadio1" id="optionsRadio1" <?php echo $checked_zoom_1;?> >
			            <i style="margin-top: 7px;"></i>Class in Adobe Connect</label>
		                <label class="radio">
			            <input type="radio" name="optionsRadio2" id="optionsRadio2" <?php echo $checked_zoom_2;?>>
			            <i style="margin-top: 7px;"></i>Class in Zoom</label>
                    </div>

                       <script>
                           $(document).ready(function () {
                               $('#optionsRadio1').change(function () {
                                   $('#optionsRadio2').prop('checked', false);
                                   $('#optionsRadio1').prop('checked', true);
                               });
                               $('#optionsRadio2').change(function () {

                                   $('#optionsRadio1').prop('checked', false);
                                   $('#optionsRadio2').prop('checked', true);
                               });
                           });
                       </script>


                  </div>
              </div>
              

              
              <div class="row">
              <div class="col col-xs-12 col-sm-6">
                <div class='form-group smart-form <?php if(!empty($programas_seleccionados_err)) echo "has-error"; ?>'>
                  <label class='col col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label text-align-left'><strong>*<?php echo ObtenEtiqueta(1007); ?></strong></label>
                  <div class='col col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                    <label class='select'>
                    <?php
                    # Mostramos los programas que tengan alumnos activos
                    $Query  = "SELECT CONCAT(pro.nb_programa,' (', pro.ds_duracion ,')'), pro.fl_programa FROM c_usuario usr ";
                    $Query .= "LEFT JOIN k_ses_app_frm_1 frm1 ON (frm1.cl_sesion=usr.cl_sesion) ";
                    $Query .= "JOIN c_programa pro ON (pro.fl_programa=frm1.fl_programa)";
                    $Query .= "WHERE usr.fg_activo='1' GROUP BY fl_programa ORDER BY pro.nb_programa ASC ";                                        
                    CampoSelectBD('fl_programa[]', $Query, !empty($p_actual)?$p_actual:NULL, '', False, 'multiple', $programas_bd, 'fl_programa');
                    echo "<i></i>";
                    if(!empty($programas_seleccionados_err))
                      echo "<span class='help-block'><i class='fa fa-warning'></i>".ObtenEtiqueta($programas_seleccionados_err)."</span>";
                    ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col col-xs-12 col-sm-4">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1008), True, 'ds_titulo', $ds_titulo, 100, 0, !empty($ds_titulo_err)?$ds_titulo_err:NULL, False, '', True, '', '', "form-group", 'left', 'col col-xs-12 col-sm-12 col-lg-12', 'col col-xs-12 col-sm-12col-lg-12');
              ?>

            
              </div>
            </div>


              <?php

              #Obtenemos fecha actual :
              $Query = "Select CURDATE() ";
              $row = RecuperaValor($Query);
              $fe_actual = str_texto($row[0]);
              $fe_actual=strtotime('+0 day',strtotime($fe_actual));
              $fe_prox=strtotime('+1 day',strtotime($row[0]));
              $fe_actual= date('Y-m-d',$fe_actual);
              $date = date_create($fe_actual);
              $fe_actual = date_format($date, 'F j, Y');

              $fe_prox= date('Y-m-d',$fe_prox);
              $date_prox = date_create($fe_prox);
              $fe_prox = date_format($date_prox, 'F j, Y');

              ?>

<div class="row">
        <div class="col-md-12 text-center">
            <h5>Last reset: <?php echo $fe_actual;?> @ 5pm Vancouver <br />
                Next reset: <?php echo $fe_prox;?> @ 5pm Vancouver</h5>
            <table class="table" style="margin:auto;" width="100%">
                <thead>
                    <td>Zoom Host Id</td>
                    <td>Available </td>
                    <td>Used </td>
                </thead>
                <tbody>
                    <?php
                    $QueryS1="SELECT id,no_request,host_email_zoom FROM zoom WHERE fg_activo='1' ";
                    $rsS1 = EjecutaQuery($QueryS1);
                    for($tot_zo1 = 0; $row1 = RecuperaRegistro($rsS1); $tot_zo1++) {
                        $id_zoom=$row1['host_email_zoom'];
                        $no_request=$row1[1];
                        $total=100;

                        $disponible=100-$no_request;

                        echo"<tr>
                                <td>$id_zoom</td>
                                <td>$disponible </td>
                                <td>$no_request</td>
                             </tr>";
                    }
                    ?>

                </tbody>
            
            </table>
        </div>
    </div>

 <br />

            <div class="row">              
              <div class="col col-xs-12 col-sm-12 col-lg-2">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1009), False, 'ds_clase', !empty($ds_clase)?$ds_clase:NULL, 100, 0, !empty($ds_clase_err)?$ds_clase_err:NULL,False, '', True, 'style=\'padding:6px 2px;\'', '', "form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-2">
              <?php
              $Query  = "SELECT CONCAT(usr.ds_nombres,' ',usr.ds_apaterno), ma.fl_maestro, ma.ds_ruta_avatar ";
              $Query .= "FROM c_maestro ma LEFT JOIN c_usuario usr ON(usr.fl_usuario=ma.fl_maestro) ";
              $Query .= "WHERE usr.fg_activo='1' ";             
              Forma_CampoSelectBD(ObtenEtiqueta(1002), False, 'fl_maestrog', $Query, !empty($fl_maestrog)?$fl_maestrog:NULL, !empty($fl_maestrog_err)?$fl_maestrog_err:NULL, True, '', 'left', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-2">
              <?php
              $opc = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
              $val = array(1, 2, 3, 4, 5, 6, 7);
              $icono = '&nbsp;<a href="javascript:void(0);" rel="popover-hover" data-placement="top" data-original-title="'.ObtenEtiqueta(20).'" data-content="'.ObtenMensaje(231).'"><i class="fa fa-info-circle"></i></a>';
              Forma_CampoSelect(ObtenEtiqueta(1010).$icono, False, 'fg_dia_sesion', $opc, $val, !empty($fg_dia_sesion)?$fg_dia_sesion:NULL, !empty($fg_dia_sesion_err)?$fg_dia_sesion_err:NULL, False, '', 'left', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-2">
              <?php
              Forma_CampoTexto(ObtenEtiqueta(1011),False,'fe_start_date', !empty($fe_start_date)?$fe_start_date:NULL, 10, 10, !empty($fe_start_date)?$fe_start_date:NULL, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
              Forma_Calendario('fe_start_date');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-1">
              <?php
              $hr_sesion = ObtenConfiguracion(93);
              Forma_CampoTexto(ObtenEtiqueta(1004), False, 'hr_sesion', $hr_sesion, 10, 5, '',False, '', True, '', '', "form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-1" style="padding:0px 65px 0px 65px;">
              <?php
              Forma_CampoCheckbox(ObtenEtiqueta(1006), 'fg_mandatory', $fg_mandatory, '', '', True, '', 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-1">
                <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</label>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <a class="btn btn-primary" href="javascript:InsertaCG(<?php echo $clave; ?>);"><i class="fa fa-plus fa-1x"></i> <?php echo ObtenEtiqueta(1012); ?></a>
                </div>
              </div>
            </div>
          </div>
                    
          <!-- Lista de las sesiones -->
          <div class="col col-xs-12 col-sm-12 col-lg-12 padding-10">
            <div class="row">
            <?php
            # Clases Extras
            Forma_Doble_CampoDivAjax('div_cg', $clave, $fg_error, 0, $p_func_ini='');
            Forma_Espacio( );
            ?>
            </div>
          </div>
        </div>
    </div>
  </div>  
  </div>
  <?php
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(132, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>