<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../fame/lib/layout_front_back.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fl_maestro = RecibeParametroNumerico('fl_maestro');  
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(176, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(empty($fg_error)) { // Sin error, viene del listado
    if(isset($clave)) { // Actualizacion, recupera de la base de datos    
      $Query  = "SELECT us.ds_nombres, us.ds_apaterno, us.fg_genero, us.fe_nacimiento,  ";
      $Query .= "d.ds_number, d.ds_street, d.ds_city, d.ds_state, d.ds_zip, d.fl_pais, d.ds_phone_number, ";
      $Query .= "us.ds_email, pro.nb_programa, pro.nb_thumb, us.fl_usuario,us.fl_instituto,us.fe_alta, us.ds_alias, us.fl_usuario ";
      $Query .= "FROM c_maestro_sp m ";
      $Query .= "LEFT JOIN c_usuario us ON(us.fl_usuario=m.fl_maestro_sp) ";
      $Query .= "LEFT JOIN k_usuario_programa pr ON(pr.fl_usuario_sp=m.fl_maestro_sp) ";
      $Query .= "LEFT JOIN c_programa_sp pro ON(pro.fl_programa_sp=pr.fl_programa_sp) ";
      $Query .= "LEFT JOIN c_instituto i ON(i.fl_instituto=us.fl_instituto) ";
      $Query .= "LEFT JOIN k_usu_direccion_sp d ON(d.fl_usuario_sp=m.fl_maestro_sp) ";
      $Query .= "LEFT JOIN c_pais p ON(p.fl_pais = d.fl_pais ) ";
      if(!empty($fl_usu_pro))
        $Query .= "WHERE pr.fl_usu_pro=".$clave;
      else
        $Query .= "WHERE us.fl_usuario=".$fl_maestro;
      $row = RecuperaValor($Query);
      $ds_nombres = str_texto($row[0]);
      $ds_apaterno = str_texto($row[1]);
      $fg_genero = $row[2];
      $fe_nacimiento = date('d-m-Y',strtotime($row[3]));
      $ds_number = $row[4];
      $ds_street = str_texto($row[5]);
      $ds_city = $row[6];
      $ds_state = $row[7];
      $ds_zip = $row[8];
      $fl_pais = $row[9];
      $ds_phone_number = $row[10];
      $ds_email = $row[11];
      $nb_programa = $row[12];
      $nb_thumb = $row[13];
      $fl_usuario = $row[14];
      $fl_instituto=$row[15];      
      $fe_registro=GeneraFormatoFecha($row[16]);
      $ds_alias = trim($row[17], ' ');
      $fl_usuario_sp = $row[18];
    }
    else { // Alta, inicializa campos
      $ds_nombres = "";
      $ds_apaterno = "";
      $fg_genero = "";
      $fe_nacimiento = "";
      $ds_number = "";
      $ds_street = "";
      $ds_city = "";
      $ds_state = "";
      $ds_zip = "";
      $fl_pais = "";
      $ds_phone_number = "";
      $ds_email = "";
      $nb_programa = "";
      $nb_thumb = "";
    }
  }
  // else { // Con error, recibe parametros (viene de la pagina de actualizacion)

  // }
		
  # Presenta forma de captura
  PresentaHeader();
    
  PresentaEncabezado(FUNC_TEACHERS_FAME);

  # Inicia forma de captura
  Forma_Inicia($clave);
  if(!empty($fg_error))
    Forma_PresentaError();
 ?>
  <!-- Modal -->
  <div class="modal fade" id="modal-empty-student" tabindex="-1" role="dialog" aria-labelledby="item-title" aria-hidden="true">
  </div>
  <!-- widget content -->
  <div class="widget-body">
    <ul id="myTab1" class="nav nav-tabs bordered">
      <li class="active">
          <a href="#teachers" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i><?php echo ObtenEtiqueta(1874); ?></a>
      </li>
      <!------ Change Password ----->
      <div role="menu" class="widget-toolbar">
        <div class="btn-group">
          <button aria-expanded="true" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
            <?php echo ObtenEtiqueta(1875); ?> <i class="fa fa-caret-down"></i>
          </button>
          <ul class="dropdown-menu pull-right">
            <li>
            <a href="javascript:change_pwd(<?php echo $fl_usuario; ?>);">
            <i class="fa fa-key">&nbsp;</i><?php echo ObtenEtiqueta(1876); ?></a>
            </li>
          </ul>                    
        </div>
      </div>
    </ul>
    <div id="myTabContent1" class="tab-content padding-10 no-border">
      <div class="tab-pane fade in active" id="teachers">
          <div class="row padding-10">

          </div>
          
          <div class="row padding-10">            
            <div class="well col-xs-12 col-sm-6">
            <?php
              Profile_pic_FAME($fl_usuario, (!empty($fl_programa)?$fl_programa:NULL), 0, $fl_maestro, false);
            ?>
            </div>
            <div class="col-xs-12 col-sm-6">
               <div class="row">
									<div class="col-xs-12 col-sm-12">
                    <?php $src_dowloand="../reports/contract_fame_rpt.php?c=$fl_instituto&u=$fl_usuario"; ?>
                  <style>
                     #sparks li {
                         overflow:initial !important;
                     }

                  </style>
			            <ul id="sparks" class="" >
				            <li style="">
				              <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
					            <a href="<?php echo $src_dowloand ?>">
					              <i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="Download">&nbsp;</i>
					            </a>
				              </div>
				              <a href="javascript:void(0);" data-toggle="modal" data-target="#myContrat">
					            <i class="fa fa-search">&nbsp;</i>Contract Signed <br />
					                                              Date Signed: <?php echo $fe_registro; ?>
				              </a>
				            </li>
			              </ul>
										<!-------------Modal PREVIEW DEL CONTRATO--------------->
										<button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myContrat" id="asignar">
										  boton que se ejecua automaticamente
										</button>

										<!-- Modal -->
										<div class="modal fade" id="myContrat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
										  <div class="modal-dialog modal-lg" role="document">
											<div class="modal-content">
											  <div class="modal-header text-center">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(913); ?></h4>
											  </div>
											  <div class="modal-body" style="padding: 0px;">
												  <div id="chat-body" class="chat-body custom-scroll" style="background:#fff;">
												  <?php 

													  #se genera el cuerpo del documento del contrato
													  $ds_encabezado_contrato = genera_ContratoFame($fl_instituto, 1,102,$fl_usuario);
													  $ds_cuerpo_contrato = genera_ContratoFame($fl_instituto, 2,102,$fl_usuario);
													  $ds_pie_contrato = genera_ContratoFame($fl_instituto, 3,102,$fl_usuario);
													  
													  echo $ds_encabezado_contrato."<br/> ".$ds_cuerpo_contrato."<br/> ".$ds_pie_contrato;
													  
													  
													  
													  ?>
                            </div>
                          </div>
													  <div class="modal-footer text-center">
														 <!--<button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar_modal">Close</button>-->
														 <button type="button" class="btn btn-primary" data-dismiss="modal" id="cerrar_modal" > Close</button>
													  </div>
													</div>
												</div>
											</div>
											<!-----------end modal----------------->
                    </div><!---end row6--->
                  </div><!--end row--->
                </div>
                <div class="row">
        				  <div class="col-md-12">&nbsp;</div>				  
        		      </div>
                  <div class="row">
                    <div class="col-xs-12 col-sm-6">
                      <?php
                        Forma_CampoTexto(ObtenEtiqueta(909), True, 'ds_nombres', $ds_nombres, 50, 30, !empty($ds_nombres_err)?$ds_nombres_err:NULL);                 
                      ?>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                      <?php
                      Forma_CampoOculto('fl_usuario_sp', $fl_usuario_sp);
                      // Forma_CampoTexto(ObtenEtiqueta(362), True, 'ds_tipo', $ds_tipo, 50, 20, $ds_tipo_err);
                      Forma_CampoTexto(ObtenEtiqueta(910), True, 'ds_apaterno', $ds_apaterno, 50, 30, !empty($ds_apaterno_err)?$ds_apaterno_err:NULL);
                      ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-12 col-sm-6">
                      <?php
                      $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116), ObtenEtiqueta(128)); // Masculino, Femenino
                      $val = array('M', 'F', 'N');
                      Forma_CampoSelect(ObtenEtiqueta(114), False, 'fg_genero', $opc, $val, $fg_genero); 
                      ?>
                    </div>
                    <div class="col-xs-12 col-sm-6 no-margin padding-bottom-10">
                      <?php
                        Forma_CampoTextoCA(ObtenEtiqueta(120), True, 'fe_nacimiento', $fe_nacimiento, 50, 30, !empty($fe_nacimiento_err)?$fe_nacimiento_err:NULL, false, '', true,'', '', 'form-group', 'right', 'col-sm-4', 'col-sm-4 no-padding' );
                        Forma_CalendarioS('fe_nacimiento');

                      ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-12 col-sm-6">
                      <?php
                      Forma_CampoTexto(ObtenEtiqueta(1574), True, 'ds_number', $ds_number, 50, 30, !empty($ds_number_err)?$ds_number_err:NULL);
                    ?>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <?php
                    Forma_CampoTexto(ObtenEtiqueta(1577), True, 'ds_street', $ds_street, 50, 30, !empty($ds_street_err)?$ds_street_err:NULL);
                    ?>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 col-sm-6">
                    <?php
                    Forma_CampoTexto(ObtenEtiqueta(1575), True, 'ds_city', $ds_city, 50, 30, !empty($ds_city_err)?$ds_city_err:NULL);
                    ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                  <?php
                    Forma_CampoTexto(ObtenEtiqueta(1578), True, 'ds_state', $ds_state, 50, 30, !empty($ds_state_err)?$ds_state_err:NULL);
                  ?>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-sm-6">
                  <?php
                    Forma_CampoTexto(ObtenEtiqueta(1576), True, 'ds_zip', $ds_zip, 50, 30, !empty($ds_zip_err)?$ds_zip_err:NULL);
                  ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                  <?php
                    $Query = "SELECT CONCAT(ds_pais,' - ',cl_iso2), fl_pais FROM c_pais WHERE 1=1 and fg_activo='1' ";
                    Forma_CampoSelectBDM(ObtenEtiqueta(287), True, 'fl_pais', $Query, $fl_pais, !empty($fl_pais_err)?$fl_pais_err:NULL, True,'', 'right', 'col col-sm-4', 'col col-sm-6');
                 ?>
               </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-sm-6">
                  <?php
                    Forma_CampoTexto(ObtenEtiqueta(766), True, 'ds_email', $ds_email, 50, 30);   
                  ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                  <style>
                      .input-group .form-control {
                        width: 71% !important;
                      }
                  </style>
                    <div id="div_ds_estado" class="row form-group ">
                      <label class="col-sm-4 control-label text-align-right">
                        <strong> Phone Number:
                        </strong>
                      </label>
                      <div class="col-sm-8">
                        <label class="input">
                          <div class="input-group">
                            <span class="input-group-addon" id="muestra_codigo" style="background-color: #fff;"> </span>
                            <input type="text" class="form-control"  aria-describedby="basic-addon1" name="ds_phone_number" id="ds_phone_number" value="<?php echo $ds_phone_number; ?>">
                          </div>
                        </label>
                      </div>      
                    </div> 
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?php
                    Forma_CampoTexto(ObtenEtiqueta(1129), True, 'ds_alias', $ds_alias, 50, 0, !empty($no_telefono_err)?$no_telefono_err:NULL, false,'',true,"onkeypress='return validarnspace(event);' onkeyup='ChangeAlias(".$fl_usuario_sp.");'");
                    Forma_CampoOculto('ds_alias_bd', $ds_alias);
                    ?>
               </div>
          </div>
          <div class="row">&nbsp;</div><br/>
          <div class="row">&nbsp;</div><br/>
      </div>
    </div>
  </div>
<script>

function MuestraCodigoArea( ) {


    $.ajax({
        type: 'POST',
        url: 'buscar_codigo_pais.php',
        data: 'fl_pais='+$('#fl_pais').val(),
        async: true,
        success: function (html) {

            $('#muestra_codigo').html(html);

        }

    });


}

$(document).ready(function () {
    // Verifica el alias
    ChangeAlias(<?php echo $fl_usuario_sp; ?>);
    // Valida formulario
    ValidaInfo();
    MuestraCodigoArea();

    $('#fl_pais').change(function () {

        MuestraCodigoArea();
        ValidaInfo();
    });


    $('#ds_nombres').change(function () {
        ValidaInfo();
    });

    $('#ds_apaterno').change(function () {
        ValidaInfo();
    });

    $('#fg_genero').change(function () {
        ValidaInfo();
    });

    $('#fe_nacimiento').change(function () {
        ValidaInfo();            
    });

    $('#ds_number').change(function () {
        ValidaInfo();
    });

    $('#ds_street').change(function () {
        ValidaInfo();
    });

    $('#ds_city').change(function () {
        ValidaInfo();
    });

    $('#ds_state').change(function () {
        ValidaInfo();
    });

    $('#ds_zip').change(function () {
        ValidaInfo();
    });

    $('#fl_pais').change(function () {
        ValidaInfo();
    });

    $('#ds_email').change(function () {
        ValidaInfo();
    });
    

});




function ValidaInfo( ) {
    
    var fname = document.getElementById("ds_nombres").value;
    var lname = document.getElementById("ds_apaterno").value;
    var fg_genero = document.getElementById("fg_genero").value;
    var fe_nacimiento = document.getElementById("fe_nacimiento").value;
    var ds_numero_casa = document.getElementById("ds_number").value;
    var ds_calle = document.getElementById("ds_street").value;
    var ds_ciudad = document.getElementById("ds_city").value;
    var ds_estado = document.getElementById("ds_state").value;
    var ds_zip = document.getElementById("ds_zip").value;
    var fl_pais = document.getElementById("fl_pais").value;
    var ds_numero_telefono = document.getElementById("ds_phone_number").value;
    var ds_email = document.getElementById("ds_email").value;
    
    


        if (fname == '') {
            document.getElementById("ds_nombres").style.borderColor = "red";
            document.getElementById("ds_nombres").style.background = "#fff0f0";
        } else {
            document.getElementById("ds_nombres").style.borderColor = "#739e73";
            document.getElementById("ds_nombres").style.background = "#f0fff0";
        }
        
        if (lname == '') {
            document.getElementById("ds_apaterno").style.borderColor = "red";
            document.getElementById("ds_apaterno").style.background = "#fff0f0";
        } else {
            document.getElementById("ds_apaterno").style.borderColor = "#739e73";
            document.getElementById("ds_apaterno").style.background = "#f0fff0";
        }

        
        if (fg_genero == '') {
            document.getElementById("fg_genero").style.borderColor = "red";
            document.getElementById("fg_genero").style.background = "#fff0f0";
        } else {
            document.getElementById("fg_genero").style.borderColor = "#739e73";
            document.getElementById("fg_genero").style.background = "#f0fff0";
        }

        if (fe_nacimiento == '') {
            document.getElementById("fe_nacimiento").style.borderColor = "red";
            document.getElementById("fe_nacimiento").style.background = "#fff0f0";
        } else {
            document.getElementById("fe_nacimiento").style.borderColor = "#739e73";
            document.getElementById("fe_nacimiento").style.background = "#f0fff0";
        }

        

        if (ds_numero_casa == '') {
            document.getElementById("ds_number").style.borderColor = "red";
            document.getElementById("ds_number").style.background = "#fff0f0";
        } else {
            document.getElementById("ds_number").style.borderColor = "#739e73";
            document.getElementById("ds_number").style.background = "#f0fff0";
        }

        if (ds_calle == '') {
            document.getElementById("ds_street").style.borderColor = "red";
            document.getElementById("ds_street").style.background = "#fff0f0";
        } else {
            document.getElementById("ds_street").style.borderColor = "#739e73";
            document.getElementById("ds_street").style.background = "#f0fff0";
        }

        if (ds_ciudad == '') {
            document.getElementById("ds_city").style.borderColor = "red";
            document.getElementById("ds_city").style.background = "#fff0f0";
        } else {
            document.getElementById("ds_city").style.borderColor = "#739e73";
            document.getElementById("ds_city").style.background = "#f0fff0";
        }
        if (ds_estado == '') {
            document.getElementById("ds_state").style.borderColor = "red";
            document.getElementById("ds_state").style.background = "#fff0f0";
        } else {
            document.getElementById("ds_state").style.borderColor = "#739e73";
            document.getElementById("ds_state").style.background = "#f0fff0";
        }
        if (ds_zip == '') {
            document.getElementById("ds_zip").style.borderColor = "red";
            document.getElementById("ds_zip").style.background = "#fff0f0";
        } else {
            document.getElementById("ds_zip").style.borderColor = "#739e73";
            document.getElementById("ds_zip").style.background = "#f0fff0";
        }

        if ((fl_pais == '')||(fl_pais==0)) {
            document.getElementById("fl_pais").style.borderColor = "red";
            document.getElementById("fl_pais").style.background = "#fff0f0";
        } else {
            document.getElementById("fl_pais").style.borderColor = "#739e73";
            document.getElementById("fl_pais").style.background = "#f0fff0";
        }



        // if (ds_numero_telefono == '') {
            // document.getElementById("ds_phone_number").style.borderColor = "red";
            // document.getElementById("ds_phone_number").style.background = "#fff0f0";
        // } else {
            // document.getElementById("ds_phone_number").style.borderColor = "#739e73";
            // document.getElementById("ds_phone_number").style.background = "#f0fff0";
        // }

           


        if (ds_email == '') {
            document.getElementById("ds_email").style.borderColor = "red";
            document.getElementById("ds_email").style.background = "#fff0f0";
        } else {

            

                expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                if (!expr.test(ds_email)) {

                    document.getElementById("ds_email").style.borderColor = "red";
                    document.getElementById("ds_email").style.background = "#fff0f0";
                    var correcto=1;
                } else {
                    document.getElementById("ds_email").style.borderColor = "#739e73";
                    document.getElementById("ds_email").style.background = "#f0fff0";
                    var correcto=2;
                }
        }

        if ( (lname.length > 0) && (fname.length > 0) && (ds_numero_casa.length > 0) && (ds_calle.length > 0) && (ds_ciudad.length > 0) && (ds_estado.length > 0) && (ds_zip.length > 0) && (correcto == 2) && (fe_nacimiento.length>0)) {

            $("#aceptar").removeClass('disabled');

        } else {
            $("#aceptar").addClass('disabled');

        }

    }

    // changer password 
    function change_pwd(clave){
      $("#modal-empty-student").modal();
       $.ajax({
            type: 'POST',
            url: 'pwd_frm.php',
            data: 'clave='+clave,
            async: true,
            success: function (html) {

                $('#modal-empty-student').html(html);

            }

        });
    }
    
</script>



  
  <?php  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_TEACHERS_FAME, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_TerminaM($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
  function CampoSelectBDGRupoFAME($p_nombre, $p_query, $p_actual, $p_clase='css_input', $p_seleccionar=False, $p_script='',$p_etiqueta='',$p_requerido=false,$p_query2) {
    echo "<div class='form-group smart-form'>";
    if($p_etiqueta){
        echo"<label class='input col col-sm-4 control-label text-align-right'><strong>"; 
        
        if($p_requerido)
            echo"* ";
        
        echo" $p_etiqueta :</strong></label>";
    }
    echo "<div class='col col-sm-5' style='padding-right: 0px;'><select id='$p_nombre' name='$p_nombre' class='select2'";
    if(!empty($p_script)) echo " $p_script";
    echo ">\n";
    if($p_seleccionar){        
      echo"<optgroup label=''>";
      echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
      echo"</optgroup>"; 
    }
    $rs = EjecutaQuery($p_query);#ejecua el primer query para saber la clasificacion padre.
    while($row = RecuperaRegistro($rs)) {        
        $nombre=str_texto($row[1]);
        echo"<optgroup label='$nombre'>";
        $contador++;
        $p_query_tem=$p_query2;        
        #reemplazamos el identificador por primer resultado, del primer query,(es como buscar su papa jajaja)
        $p_query_tem = str_replace("#id_valor#", $row[0], $p_query_tem);
        $rs2=EjecutaQuery($p_query_tem);
        while($row2 = RecuperaRegistro($rs2)) {
            
            $nb_nombre2=$row2[1];
            $contador++;
            
            echo "<option value=\"$row2[0]\"";
            if($p_actual == $row2[0])
                echo " selected";
                   $etq_campo = DecodificaEscogeIdiomaBD($row2[1]);	
            echo ">$etq_campo</option>\n";
            
        }        
        echo"</optgroup>";
    }
    echo "</select>
    </div>
    </div>";
}

  
  function Forma_CampoSelectBDM($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
      
      $ds_clase = 'form-control';
      if(!empty($p_error)) {
          $ds_error = ObtenMensaje($p_error);
          $ds_clase_err = 'has-error';
      }
      else {
          $ds_error = "";
          $ds_error_err = "";
          $ds_clase_err="";
      }
     
      
     
      
      echo "
  <div class='form-group smart-form $ds_clase_err'>
    <label class='$col_sm_etq control-label text-align-$etq_align'>
      <strong>";
      if($p_requerido)  echo "* ";
      if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
      echo "
      </strong>
    </label>
    <div class='$col_sm_cam' style='padding-right: 0px;' ><label class='select'>";
      CampoSelectBD($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
      echo "<i></i>";
      if(!empty($p_error))
          echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
      echo "
    </label></div>     
  </div>";
  }
  
  
  
  
  
  
  function Forma_TerminaM($p_guardar=False, $p_url_cancelar='', $p_etq_aceptar=ETQ_SALVAR, $p_etq_cancelar=ETQ_CANCELAR, $p_click_cancelar='') {
      
     
      # Destino para el boton Cancelar
      if(empty($p_click_cancelar)) {
          if(empty($p_url_cancelar)) {
              $nb_programa = ObtenProgramaBase( );
              $click_cancelar = "parent.location='$nb_programa'";
          }
          else
              $click_cancelar = "parent.location='$p_url_cancelar'";
      }
      else
          $click_cancelar = $p_click_cancelar;
      
      echo "
        <footer>";

      echo "
          <div style='width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;' outline='0' class='ui-widget ui-chatbox'>";
      if($p_guardar)
          echo "<a class='btn btn-primary btn-circle btn-xl disabled' title='".$p_etq_aceptar."' name='aceptar' id='aceptar' onClick='javascript:document.datos.submit();'><i class='fa fa-save'></i></a>&nbsp;";
      echo "  <a class='btn btn-default btn-circle btn-xl' title='".$p_etq_cancelar."' name='aceptar' id='cancelar' onClick=\"$click_cancelar\"><i class='fa fa-times'></i></a>
          </div>          
        </footer>
      </form>
    </div>
  </div>";
      
  }
  
  
  
  
  
  
  
  function Forma_CampoTextoC($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='', $class_div = "form-group", $prompt_aling='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4') {
      
      if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
          if(!empty($p_error)) {
              $ds_error = ObtenMensaje($p_error);
              $ds_clase_err = 'has-error';
              $ds_clase = 'form-control';      
          }
          else {
              $ds_clase = 'form-control';
              $ds_error = "";
              $ds_clase_err = '';
          }
          if(!empty($p_id)) {
              if($fg_visible)
                  $ds_visible = "inline";
              else
                  $ds_visible = "none";
          }
       
          echo "
    <div id='div_".$p_nombre."' class='row ".$class_div." ".$ds_clase_err."'>
      <label class='$col_sm_promt control-label text-align-$prompt_aling'>
        <strong>";
          if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
          if($p_requerido) echo "* ";
          if(!empty($p_prompt)) echo "$p_prompt:"; else echo "&nbsp;";
          if(!empty($p_id)) echo "</div>";
          echo "
        </strong>
      </label>
      <div class='$col_sm_cam'>
        <label class='input'>";
          if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
          CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
          if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
          if(!empty($p_id)) echo "</div>";
          if(!empty($p_error)){          
              echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
          }
          echo "
        </label>
      </div>      
    </div>";
          
      }
      else
          Forma_CampoOculto($p_nombre, $p_valor);
  }
  
  
  
  function Forma_CalendarioS($p_nombre) {
      
      echo "
    <script type='text/javascript'>
    $(function(){
      $('#$p_nombre').datepicker({
        //showOn: 'button',
        //buttonImage: '".PATH_IMAGES."/".IMG_CALENDARIO."',
        //buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: '".EscogeIdioma('dd-mm-yy','mm-dd-yy')."',
        showAnim: 'slideDown',
        showOtherMonths: true,
        selectOtherMonths: true,
        showMonthAfterYear: false,
        yearRange: 'c-50:c+2',
        autoSize: false,
        //dayNames: [".ETQ_DIAS_SEMANA."],
        //dayNamesMin: [".ETQ_DIAS_CORTO."],
        //monthNames: [".ETQ_MESES."],
        //monthNamesShort: [".ETQ_MESES_CORTO."],
        prevText : '<',
				nextText : '>'
      });   
		});
   $('#$p_nombre').addClass('hasDatepicker');
    $('<i class=\'icon-append fa fa-calendar\'></i>').insertBefore('#$p_nombre');
    /*Al elemento se le cambia de clase   */ 
    $('#div_".$p_nombre."').removeClass('form-control');
    if($('#err_".$p_nombre."').val()=='1')
      $('#div_".$p_nombre."').attr('class','row smart-form has-error');
    else
      $('#div_".$p_nombre."').attr('class','row form-group smart-form');
    $('#$p_nombre').removeClass('form-control');
    $('#$p_nombre').attr('class','datepicker');
    //$('#div_".$p_nombre."').css('margin-left','-30px');
		</script>";  
  }
  
  
  
  
  
  
  function Forma_CampoTextoCA($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='', $class_div = "form-group", $prompt_aling='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4') {
      
      if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
          if(!empty($p_error)) {
              $ds_error = ObtenMensaje($p_error);
              $ds_clase_err = 'has-error';
              $ds_clase = 'form-control';      
          }
          else {
              $ds_clase = 'form-control';
              $ds_error = "";
              $ds_clase_err = '';
          }
          if(!empty($p_id)) {
              if($fg_visible)
                  $ds_visible = "inline";
              else
                  $ds_visible = "none";
          }
      
          
          echo "
    <div id='div_".$p_nombre."' class='row ".$class_div." ".$ds_clase_err."'>
      <label class='$col_sm_promt control-label text-align-$prompt_aling'>
        <strong>";
          if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
          if($p_requerido) echo "* ";
          if(!empty($p_prompt)) echo "$p_prompt:&nbsp;&nbsp;"; else echo "&nbsp;";
          if(!empty($p_id)) echo "</div>";
          echo "
        </strong>
      </label>
      <div class='$col_sm_cam'>
        <label class='input'>";
          if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
          CampoTextoCA($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
          if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
          if(!empty($p_id)) echo "</div>";
          if(!empty($p_error)){          
              echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
          }
          echo "
        </label>
      </div>      
    </div>";
          
      }
      else
          Forma_CampoOculto($p_nombre, $p_valor);
  }
  
  function CampoTextoCA($p_nombre, $p_valor, $p_maxlength, $p_size, $p_clase='css_input', $p_password=False, $p_script='') {
      
      if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
          if(!$p_password)
              $ds_tipo = 'text';
          else
              $ds_tipo = 'password';
          echo "<input type='$ds_tipo' style='margin-left:15px;' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" maxlength='$p_maxlength' size='$p_size'";
          if($p_password)
              echo " autocomplete='off'";
          if(!empty($p_script)) echo " $p_script";
          echo ">";
      }
      else
          Forma_CampoOculto($p_nombre, $p_valor);
  }
?>