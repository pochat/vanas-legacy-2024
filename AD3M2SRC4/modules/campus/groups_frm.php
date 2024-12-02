<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../modules/liveclass/bbb_api.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');

  # Variable initialization to avoid error
  $fg_grupo_global=NULL;
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_GRUPOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT a.fl_term, fl_maestro, ds_login, nb_grupo, fl_programa, fl_periodo, no_grado, a.fg_grupo_global,fg_zoom ";
	  #Al agregar grupos globales esta consulta se modifica con left join para poder traerse los datos del grupo global, no afecta en la consulta orignal.
      //$Query .= "FROM c_grupo a, c_usuario b, k_term c ";
      //$Query .= "WHERE a.fl_maestro=b.fl_usuario ";
      //$Query .= "AND a.fl_term=c.fl_term ";
      //$Query .= "AND fl_grupo=$clave";
      $Query .="FROM c_grupo a ";
	  $Query.="LEFT JOIN c_usuario b on a.fl_maestro=b.fl_usuario
			   LEFT JOIN k_term c on a.fl_term=c.fl_term
	  ";
	  $Query.="WHERE a.fl_grupo=$clave ";

      $row = RecuperaValor($Query);
      $fl_term = $row[0];
      $fl_maestro = $row[1];
      $ds_login = str_texto($row[2]);
      $nb_grupo = str_texto($row[3]);
      $fl_programa = $row[4];
      $fl_periodo = $row[5];
      $no_grado = $row[6];
      $fg_grupo_global=$row['fg_grupo_global'];
      $fg_zoom=$row['fg_zoom'];
    }
    else { // Alta, inicializa campos
      $fl_term = "";
      $fl_maestro = "";
      $ds_login = "";
      $nb_grupo = "";
      $fg_dia_sesion = 1;
      $fg_zoom=1;
    }
    $fl_term_err = "";
    $fl_maestro_err = "";
    $nb_grupo_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $fl_term = RecibeParametroNumerico('fl_term');
    $fl_term_err = RecibeParametroNumerico('fl_term_err');
    $fl_maestro = RecibeParametroNumerico('fl_maestro');
    $fl_maestro_err = RecibeParametroNumerico('fl_maestro_err');
    $ds_login = RecibeParametroHTML('ds_login');
    $nb_grupo = RecibeParametroHTML('nb_grupo');
    $nb_grupo_err = RecibeParametroNumerico('nb_grupo_err');
    $fl_programa = RecibeParametroNumerico('fl_programa');
    $fl_periodo = RecibeParametroNumerico('fl_periodo');
    $no_grado = RecibeParametroNumerico('no_grado');
    $fg_dia_sesion = RecibeParametroNumerico('fg_dia_sesion');
  }
  $fg_zoom=1;
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_GRUPOS);

  # Funciones para manejo de sesiones en vivo para lecciones
  echo "<script src='".PATH_JS."/frmGroups.js.php'></script>";

  # Inicia forma de captura
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError();

?>
  
	<div class="row">      
            <?php
            if(!empty($clave)){
                $p_editar=false;
            }else{
                $p_editar=true;
            }

            Forma_CampoCheckbox('Group Review Class :', 'fg_grupo_global', (isset($fg_grupo_global)?$fg_grupo_global:NULL),'', (isset($fg_grupo_global)?$fg_grupo_global:NULL),$p_editar,'onchange="javascript:MuestraGrupoGlobal();"');
            ?>
			<br><br>
    </div>
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
    <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="smart-form" style="display:none">
                    <label class="radio">
			        <input type="radio" name="optionsRadio1" id="optionsRadio1" <?php echo $checked_zoom_1;?> >
			        <i style="margin-top: 7px;"></i>Class in Adobe Connect</label>
		            <label class="radio">
			        <input type="radio" value="1" name="optionsRadio2" id="optionsRadio2" <?php echo $checked_zoom_2;?>>
			        <i style="margin-top: 7px;"></i>Class in Zoom</label>
                </div>
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
  <?php
  
  
  echo"<div id='fg_grupo_unico' class=''>  ";
  
  # Campos de captura
  $concat = array('nb_programa', "' ('", 'ds_duracion', "')'", "' - '", 'nb_periodo', "' - ".ObtenEtiqueta(375)." '", 'no_grado');
  if(empty($clave)) {    
    $Query  = "SELECT ".ConcatenaBD($concat)." 'nb_term', fl_term ";
    $Query .= "FROM k_term a, c_programa b, c_periodo c ";
    $Query .= "WHERE a.fl_programa=b.fl_programa ";
    $Query .= "AND a.fl_periodo=c.fl_periodo ";
    $Query .= "AND fg_activo='1' AND b.fg_archive='0' ";
    $Query .= "ORDER BY nb_programa, no_grado";
    Forma_CampoSelectBD(ObtenEtiqueta(422), False, 'fl_term', $Query, $fl_term);
  }
  else {
    $Query  = "SELECT ".ConcatenaBD($concat)." 'nb_term' ";
    $Query .= "FROM k_term a, c_programa b, c_periodo c ";
    $Query .= "WHERE a.fl_programa=b.fl_programa ";
    $Query .= "AND a.fl_periodo=c.fl_periodo AND b.fg_archive='0' ";
    $Query .= "AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    Forma_CampoInfo(ObtenEtiqueta(422), !empty($row[0])?$row[0]:NULL);
    Forma_CampoOculto('fl_term', $fl_term);
  }
  
  echo"</div>";
  
  
  
  #Recuperamos los terms seleccionados.
  if($fg_grupo_global==1){
      
      $Querysv="SELECT fl_term, fl_grupo FROM k_grupo_term WHERE fl_grupo=$clave ";
      $rsm=EjecutaQuery($Querysv);
      $total_terms=CuentaRegistros($rsm);

      # Variable initialization to avoid errors
      $fl_programas="";
      $fl_periodos="";
      $fl_terms_i="";

      for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
        $fl_terms_i .=$rowm[0];
        $fl_termsel=$rowm[0];
         
  		  $Query="SELECT fl_programa,fl_periodo from k_term where fl_term=$fl_termsel ";
  		  $ro=RecuperaValor($Query);
  		  $fl_programas.=$ro['fl_programa'];
  		  $fl_periodos.=$ro['fl_periodo'];
		
        if($im<=($total_terms-1)){
          $fl_terms_i.= ",";
          $fl_programas.=",";
          $fl_periodos.=","; 
        } else {
          $fl_terms_i.= "";
          $fl_programas.="";
          $fl_periodos.="";
        }

      }
      $terms_bd = explode(",",$fl_terms_i); 
  }
  /*
    $Querys  = "SELECT a.fl_term ";
    $Querys .= "FROM k_term a, c_programa b, c_periodo c ";
    $Querys .= "WHERE a.fl_programa=b.fl_programa ";
    $Querys .= "AND a.fl_periodo=c.fl_periodo AND b.fg_archive='0' ";
    if($fg_grupo_global=='1'){
        
        $Querysv="SELECT fl_term,fl_grupo FROM c_grupo WHERE nb_grupo='$nb_grupo' ";
        $rsm=EjecutaQuery($Querysv);
        $total_terms=CuentaRegistros($rsm);

        # Variable initialization to avoid errors
        $fl_terms_i="";
        $fl_grupos_i="";

        for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
            $fl_terms_i .= $rowm[0];
            $fl_grupos_i.=$rowm[1];

            
            if($im<=($total_terms-1)){
                $fl_terms_i.= ",";
                $fl_grupos_i.=",";        
            }else{
                $fl_terms_i.= "";
                $fl_grupos_i.="";
            }

        }
        $Querys .= "AND fl_term IN($fl_terms_i)";
    }else{
        
        $Querys.="AND fl_term=$fl_term ";
        $fl_terms_i=$fl_term;
        $fl_grupos_i=$clave;
    }
  
	# Terms seleccionados
    $rs = EjecutaQuery($Querys);
      # Variable initialization to avoid errors
      $programas_pre="";
      for($i=0;$i<$row=RecuperaRegistro($rs);$i++){
        $programas_pre .= $row[0].",";
      }
    $terms_bd = explode(",", $programas_pre);  
  */
  ?>

  <div class="row"  >
        <div id="multiple_select" class="hidden">
 
              <div class="col col-xs-12 col-sm-4 tex-right">
			       <label class='control-label text-align-right' style="float:right;"><strong>*<?php echo ObtenEtiqueta(422); ?></strong></label>
			  </div>
 
              <div class="col col-xs-12 col-sm-4">
                <div class='form-group smart-form'>
                 
                  <div class='col col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                    <label class='select'>
  
						<?php
							$Queryp  = "SELECT ".ConcatenaBD($concat)." 'nb_term', fl_term ";
							$Queryp .= "FROM k_term a, c_programa b, c_periodo c ";
							$Queryp .= "WHERE a.fl_programa=b.fl_programa ";
							$Queryp .= "AND a.fl_periodo=c.fl_periodo ";
							if(empty($clave))
							$Queryp .= "AND fg_activo='1'";
							$Queryp ."AND b.fg_archive='0' ";
							$Queryp .= "ORDER BY nb_programa, no_grado";
							CampoSelectBD('fl_term2[]', $Queryp, !empty($p_actuals)?$p_actuals:NULL, '', False, 'multiple', $terms_bd, 'fl_term2');
							echo"<input type=\"hidden\" name=\"fl_grupos_i\" id=\"fl_grupos_i\" value=\"$fl_grupos_i\">";
						?>		  
					</label>
				  </div>
				</div>
			</div>
		</div>
	</div>			

  <?php

  echo"<style>
   .mike{
   margin-left:-15px;
   }
  
       </style>";
  
  Forma_Espacio( );
  echo"<div id='maestro'>";
  Forma_CampoLOV(ObtenEtiqueta(421), True, 'fl_maestro', $fl_maestro, 'ds_login', $ds_login, 20, 
      LOV_MAESTROS, LOV_TIPO_RADIO, LOV_CHICO, '', $fl_maestro_err, False, '', 'form-group smart-form', 'right', 'col-sm-4', 'col-sm-4 mike'); 
  echo"</div>";	  
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(420), True, 'nb_grupo', $nb_grupo, 50, 20, $nb_grupo_err);
  Forma_Espacio( );
  if(empty($clave))
  {
    $opc = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $val = array(1, 2, 3, 4, 5, 6, 7);
    ?>

    <div id="ds_fecha">
    <?php 
    Forma_CampoSelect(ObtenEtiqueta(427), False, 'fg_dia_sesion', $opc, $val, $fg_dia_sesion);
    Forma_Espacio( );
    ?>
    </div>

    <?php 
  }
  
  if(empty($clave)){
  
        $value_time=ObtenConfiguracion(26);
?>
        <div class="row" id="div_fecha_default">
            <div class="col-md-4 control-label text-right" style="color: #191818;"><strong><?php echo ObtenEtiqueta(2318); ?>:</strong></div>
            <div class="col-md-4">
        
                    <a href="form-x-editable.html#" id="cl_configuracion" data-type="text" data-pk="1" data-original-title="Enter value"><?php echo $value_time;?></a>

            </div>
            <div class="col-md-2"></div>

        </div>


<?php
  }

  
  
  
  # Asignacion de alumnos
  if(!empty($clave)) {
    $tit = array(ETQ_SELECCIONAR.'|center', ObtenEtiqueta(424), ETQ_NOMBRE, ObtenEtiqueta(426),ObtenEtiqueta(422));
    $ancho_col = array('10%', '15%', '30%', '20%');
    Forma_Tabla_Ini('80%', $tit, $ancho_col);
    $Query  = "SELECT fl_usuario, ds_login, ";
    $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
    $Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."' ,b.fl_periodo ,b.fl_programa ";
    $Query .= "FROM c_usuario a, k_ses_app_frm_1 b ";
    $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
    $Query .= "AND a.fl_perfil=".PFL_ESTUDIANTE." AND a.fg_activo='1'  ";
	
	if($fg_grupo_global==1){
	
    $Query .= "AND b.fl_programa IN ($fl_programas) ";
    $Query .= "AND b.fl_periodo IN($fl_periodos) ";
	
	}else{
	
    $Query .= "AND b.fl_programa=$fl_programa ";
    $Query .= "AND b.fl_periodo=$fl_periodo ";
    
	}
	
	$Query .= "ORDER BY ds_login";
    $rs = EjecutaQuery($Query);
    for($tot_alumnos = 0; $row = RecuperaRegistro($rs); $tot_alumnos++) {
      $Query  = "SELECT fl_alumno, a.fl_grupo, nb_grupo,b.fl_term ";
      $Query .= "FROM k_alumno_grupo a, c_grupo b ";
      $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
      if(empty($fg_grupo_global)){
      $Query .= "AND a.fl_grupo=$clave ";
      }
      $Query .= "AND fl_alumno=$row[0]";
      $row2 = RecuperaValor($Query);
      if($tot_alumnos % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";

      if($fg_grupo_global==1){

          $Queru="select fl_alumno from k_alumno_grupo where fg_grupo_global='1' AND fl_alumno=".$row[0]." AND fl_grupo=$clave ";
          $rrt=RecuperaValor($Queru);

			  #Es la nueva ondicio  por que hay multiple select terms. y que tiene varios grupos.
             if(!empty($rrt[0])){
				  $incluido = 1;
			  }else{
				  $incluido = 0;
			  }
		  
	  }else{

			  if($row2[1] == $clave)
				$incluido = 1;
			  else
				$incluido = 0;
	  }

      $Qio="SELECT  CONCAT(nb_programa, ' (', ds_duracion, ')', ' - ', nb_periodo, ' - Level / Term ', no_grado)  
            FROM k_term b 
            JOIN c_programa c ON c.fl_programa=b.fl_programa
            JOIN c_periodo d ON d.fl_periodo=b.fl_periodo  
            WHERE b.fl_term=".$row2['fl_term']."  ";
      $ri=RecuperaValor($Qio);
      $no_grado_=!empty($ri[0])?$ri[0]:NULL;



      echo "
      <tr class='$clase'>
        <td align='center'>
        <div class='col_sm_cam'>
        <div class='checkbox'>
          <label>";
      CampoCheckbox('fl_alumno_'.$tot_alumnos, $incluido, '', $row[0]);
      echo "
          </label>
        </div>
        </div>
        </td>
        <td>$row[1]</td>
        <td>$row[2]</td>
        <td>$row2[2]</td>
        <td>$no_grado_</td>
      </tr>\n";
    }
    Forma_Tabla_Fin( );
    Forma_CampoOculto('tot_alumnos', $tot_alumnos);
    Forma_CampoOculto('fl_programa', $fl_programa);
    Forma_CampoOculto('fl_periodo', $fl_periodo);
    Forma_CampoOculto('no_grado', $no_grado);
    Forma_Espacio( );
  }
  else
    Forma_CampoOculto('tot_alumnos', 0);  


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
<?php
  # Fechas iniciales para cada semana
  if( (!empty($clave))&&($fg_grupo_global<>1)) {
		Forma_Doble_CampoDivAjax('div_lecciones', $clave, $fg_error, 0, $p_func_ini='');
		Forma_Espacio( );
  }else{
	  
	  #Presenta la forma de agregar fechas y horas.
	
      if((!empty($clave))&&($fg_grupo_global==1)){  
          
?>  
    
	   <div class="row">              
              <div class="col col-xs-12 col-sm-12 col-lg-2">
              <?php
                Forma_CampoTexto('Name', False, 'ds_clase', !empty($ds_clase)?$ds_clase:'', 100, 0, !empty($ds_clase_err)?$ds_clase_err:'',False, '', True, 'style=\'padding:6px 2px;\'', '', "form-group", 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-2">
			  
              <?php
          $Query  = "SELECT CONCAT(usr.ds_nombres,' ',usr.ds_apaterno), ma.fl_maestro, ma.ds_ruta_avatar ";
          $Query .= "FROM c_maestro ma LEFT JOIN c_usuario usr ON(usr.fl_usuario=ma.fl_maestro) ";
          $Query .= "WHERE usr.fg_activo='1' ";             
          Forma_CampoSelectBD(ObtenEtiqueta(1002), False, 'fl_maestrog', $Query, !empty($fl_maestrog)?$fl_maestrog:'', !empty($fl_maestrog_err)?$fl_maestrog_err:'', True, '', 'left', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-2">
              <?php
          $opc = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $val = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $icono = '&nbsp;<a href="javascript:void(0);" rel="popover-hover" data-placement="top" data-original-title="'.ObtenEtiqueta(20).'" data-content="'.ObtenMensaje(231).'"><i class="fa fa-info-circle"></i></a>';
          Forma_CampoSelect(ObtenEtiqueta(1010).$icono, False, 'fg_dia_sesion', $opc, $val, !empty($fg_dia_sesion)?$fg_dia_sesion:'', !empty($fg_dia_sesion_err)?$fg_dia_sesion_err:'', False, '', 'left', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-2">
              <?php
          Forma_CampoTexto(ObtenEtiqueta(1011),False,'fe_start_date', !empty($fe_start_date)?$fe_start_date:'',10,10, !empty($fe_start_date)?$fe_start_date:'', False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
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
          Forma_CampoCheckbox(ObtenEtiqueta(1006), 'fg_mandatory', !empty($fg_mandatory)?$fg_mandatory:'', '', '', True, '', 'left', 'col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col-xs-12 col-sm-12 col-md-12 col-lg-12');
              ?>
              </div>
              <div class="col col-xs-12 col-sm-12 col-lg-1">
                <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</label>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <a class="btn btn-primary" href="javascript:InsertaCGG(<?php echo$clave;?>,'inserta');"><i class="fa fa-plus fa-1x"></i><?php echo ObtenEtiqueta(1012);?></a>
                </div>
              </div>
            </div>
	  
		    <div class="row">
				<div class="col-md-12" id="div_clases_grupales">
				
				</div>			
			</div>
	
	  
	<?php 
      }  
	  
	  
	  
	  
  }
  
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_GRUPOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
 
  
  
  # Pie de Pagina
  PresentaFooter( );
  
    ?>

<script>
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
		
    $(document).ready(function () {

        pageSetUp();
        //editables
        $('#cl_configuracion').editable({
            url: 'guardar_configuracion.php',
            type: 'text',
            pk: 1,
            name: 'username',
            title: 'Enter username'
        });

    });
function MuestraGrupoGlobal(){
	
	var check = document.getElementById("fg_grupo_global");
	
	if (check.checked) {
	    $('#multiple_select').removeClass('hidden');
		$('#fg_grupo_unico').addClass('hidden');
		$('#maestro').addClass('hidden');
		$('#ds_fecha').addClass('hidden');
		$('#div_fecha_default').addClass('hidden');
		
	}else{
		$('#multiple_select').addClass('hidden');
		$('#fg_grupo_unico').removeClass('hidden');
		$('#maestro').removeClass('hidden');
		$('#ds_fecha').removeClass('hidden');
		$('#div_fecha_default').removeClass('hidden');
		
		
	}
	
	
	
	
}
MuestraGrupoGlobal();

function InsertaCGG(fl_grupo,accion){
	
	var ds_clase=document.getElementById('ds_clase').value;
	var fl_maestrog=document.getElementById('fl_maestrog').value;
	var fg_dia_sesion=document.getElementById('fg_dia_sesion').value;
	var fe_start_date=document.getElementById('fe_start_date').value;
	var hr_sesion=document.getElementById('hr_sesion').value;
	var fg_mandatory=document.getElementById('fg_mandatory').value;
    
	if(document.datos['fg_mandatory'].checked == true)
    fg_mandatory = '1';
    else
    fg_mandatory = '0';
	
	
	$.ajax({
		type: 'POST',
		url : 'div_lecciones.php',
		data: 'accion='+accion+
		      '&clave='+fl_grupo+
		      '&fg_grupo_global=1'+
			  '&ds_clase='+ds_clase+
			  '&fl_maestrog='+fl_maestrog+
			  '&fg_dia_sesion='+fg_dia_sesion+
			  '&fe_start_date='+fe_start_date+
			  '&hr_sesion='+hr_sesion+
			  '&fg_mandatory='+fg_mandatory,
			  
		async: false,
		success: function(html) {
		  $('#div_clases_grupales').html(html);
		}
	});
	
	
	
}

function ActualizaCG(clave,fl_grupo,fl_clase_grupo){
	
	var accion='actualiza';
	var fl_maestrog=document.getElementById('fl_maestro_'+clave).value;
	var fe_start_date=document.getElementById('fe_clase_'+clave).value;
	var hr_sesion=document.getElementById('hr_clase_'+clave).value;
	var fg_mandatory=document.getElementById('fg_mandatory_'+clave).value;
	var nb_clase=document.getElementById('ds_clase_'+clave).value;
	var fg_dia_sesion=document.getElementById('fg_dia_sesion_'+clave).value;
	if(document.datos['fg_mandatory_'+clave].checked == true)
    fg_mandatory = '1';
    else
    fg_mandatory = '0';
	
	$.ajax({
		type: 'POST',
		url : 'div_lecciones.php',
		data: 'accion='+accion+
			  '&fl_clase_grupo='+fl_clase_grupo+
		      '&clave='+fl_grupo+
			  '&ds_clase='+nb_clase+
		      '&fg_grupo_global=1'+
              '&fg_dia_sesion='+fg_dia_sesion+
			  '&fl_maestrog='+fl_maestrog+
			  '&fg_mandatory='+fg_mandatory+
			  '&fe_start_date='+fe_start_date+
			  '&hr_sesion='+hr_sesion,		  
		async: false,
		success: function(html) {
		  $('#div_clases_grupales').html(html);
		}
	});
	
	
	
	
}

function BorrarCG(clave,fl_grupo,fl_clase_grupo){
	
	var accion='borra';
	
	$.ajax({
		type: 'POST',
		url : 'div_lecciones.php',
		data: 'accion='+accion+
		      '&fg_grupo_global=1'+
			  '&fl_clase_grupo='+fl_clase_grupo+
		      '&clave='+fl_grupo, 
		async: false,
		success: function(html) {
		  $('#div_clases_grupales').html(html);
		}
	});
	
	
}

<?php if(!empty($clave)){?>
InsertaCGG(<?php echo $clave;?>,'muestra');

<?php } ?>
</script>


<?php 
echo"
  
  <script src='".PATH_SELF_JS."/plugin/x-editable/moment.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/jquery.mockjax.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/x-editable.min.js'></script>
  
  ";

?>
