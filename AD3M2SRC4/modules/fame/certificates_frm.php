<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../fame/lib/layout_front_back.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CERTIFICADO_FAME, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        $Query  = "SELECT  P.fl_programa_sp,P.fl_usuario_sp,C.nb_programa,U.ds_nombres,U.ds_apaterno,S.ds_state,S.ds_city,S.ds_number,S.ds_zip,S.ds_phone_number, ";
        $Query .= "U.ds_email,S.ds_street,S.fl_pais,U.fl_instituto, P.fg_status,P.fl_maestro ";
        $Query .= "FROM k_usuario_programa P ";
        $Query .= "LEFT JOIN c_programa_sp C ON C.fl_programa_sp=P.fl_programa_sp ";
        $Query .= "LEFT JOIN c_usuario U ON U.fl_usuario=P.fl_usuario_sp ";
        $Query .= "LEFT JOIN k_usu_direccion_sp S ON S.fl_usuario_sp=P.fl_usuario_sp WHERE P.fl_usu_pro=$clave ";
      $row = RecuperaValor($Query);
      $fl_programa = str_texto($row[0]);
      $fl_usuario = str_texto($row[1]);
      $nb_programa = str_texto($row[2]);
      $fname = $row[3];
      $lname = $row[4];
      $ds_estado = $row[5];
      $ds_ciudad = $row[6];
      $ds_numero_casa = $row[7];
      $ds_zip = $row[8];
      $ds_numero_telefono = $row[9];
      $ds_email= $row[10];
      $ds_calle= $row[11];
      $fl_pais=$row['fl_pais'];
      $fl_instituto=$row['fl_instituto'];
      $fg_status = $row[14];
      $fl_maestro=$row['fl_maestro'];
      
      #Recuperamos datos de alumno.
      $Query="SELECT ds_ruta_foto,ds_ruta_avatar,ds_oficial FROM c_alumno_sp WHERE fl_alumno_sp=$fl_usuario ";
      $row=RecuperaValor($Query);
      $ds_ruta_foto=$row['ds_ruta_foto'];
      $ds_ruta_avatar=$row['ds_ruta_avatar'];
      $ds_foto_oficial=$row['ds_oficial'];
      
      
      
      
      // $src_imagen=$ds_ruta_foto."/".$ds_foto_oficial;
      $src_imagen = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_usuario."/".$ds_ruta_avatar;
      #Recuperamos el nombre del maestro:
	  $Query="SELECT ds_apaterno,ds_nombres FROM c_usuario WHERE fl_usuario=$fl_maestro ";
	  $row=RecuperaValor($Query);
	  $nb_teacher=$row[1]." ".$row[0];
      
      
    }
    else { // Alta, inicializa campos
      $nb_programa = "";
      $ds_duracion = "";
      $ds_tipo = "";

      
      
      
    }

  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_programa = RecibeParametroHTML('nb_programa');
    $nb_programa_err = RecibeParametroNumerico('nb_programa_err');
  

  }
  
  $total_tuition = number_format((!empty($tuition)?$tuition:NULL) + (!empty($no_costos_ad)?$no_costos_ad:NULL), 2, '.', '');
  $total = number_format((!empty($app_fee)?$app_fee:NULL) + $total_tuition, 2, '.', '');
      
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CERTIFICADO_FAME);
  
  echo "<script type='text/javascript' src='".PATH_JS."/frmCourses.js.php'></script>";
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  $src_dowloand_student="../reports/transcript_fame_quiz_rpt.php?c=$clave&u=$fl_usuario&i=$fl_instituto";
  $src_dowloand_teacher="../reports/transcript_fame_quiz_teacher_rpt.php?c=$clave&u=$fl_usuario&i=$fl_instituto";

  $src_download_student_archives = "../reports/fame_student_archives.php?c=$clave&u=$fl_usuario&i=$fl_instituto&p=$fl_programa&fg_tipo=2";

  
  #Verificamos permisos de asignamet manual.
  
  # Buscamos si el teacher lo califica, si tiene permisos.
  $row00 = RecuperaValor("SELECT fg_quizes, fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$clave");
  $fg_quizes = $row00[0];
  $fg_grade_tea = $row00[1];
  
 ?>
       <!-- widget content -->
            <div class="widget-body">

                <div class="pull-right" style="border-top-width: 0!important; margin-top: 5px!important; font-weight: 700; padding-right:10px;">
                    
                    <?php if($fg_grade_tea){#MUESTRA EL REPORTE SOLO SI ESTA ASIGNADO MANUALMENTE 
                              
                   
                    
                    
                    ?>
                    
						<a class="btn btn-success btn-xs" href="<?php echo $src_dowloand_teacher;  ?>"   id="add_tab"><i class="fa fa-address-book"></i><?php echo ObtenEtiqueta(1603); ?></a> 
                     
                    <?php }else{#Muestra mensaje e errror  ?>
                    
                        <a class="btn btn-danger btn-xs "   rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(1694); ?>" id="add_tab"    href="javascript:void(0);"><i class="fa fa-address-book"></i><?php echo ObtenEtiqueta(1603); ?></a> 
                            

                    <?php } ?>

               
                    <a class="btn btn-success btn-xs" href="<?php echo $src_dowloand_student;  ?>"   id="a1"><i class="fa fa-address-book"></i><?php echo ObtenEtiqueta(1604); ?></a> 
                    <?php
                    # Obtenemos la informacion
                    $Query  = "SELECT fg_oficial, fg_crop, fg_info_user, fg_card, fg_pagado, fl_usuario_doc ";
                    $Query .= "FROM k_usuario_doc WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa AND fg_tipo_doc='2'";
                    $row = RecuperaValor($Query);
                    $fg_oficial = $row[0];
                    $fg_crop = $row[1];
                    $fg_info_user = $row[2];
                    $fg_card = $row[3];
                    $fg_pagado = $row[4];
                    $fl_usuario_doc = $row[0];
                    $pasos = 0;
                    if(!empty($fg_oficial))
                      $pasos++;
                    if(!empty($fg_crop))
                      $pasos++;
                    if(!empty($fg_info_user))
                      $pasos++;
                    if(!empty($fg_card))
                      $pasos++;
                    if(!empty($fg_pagado))
                      $pasos++;
                    $progreso_total = (100/5)*$pasos;
                    switch($pasos){
                      case 1: $steps_color = "red";  $txt_step = ObtenEtiqueta(1163); break;
                      case 2: $steps_color = "yellow";  $txt_step =  ObtenEtiqueta(1164); break;
                      case 3: $steps_color = "blueLight";  $txt_step =  ObtenEtiqueta(1165); break;
                      case 4: $steps_color = "blue";  $txt_step =  ObtenEtiqueta(1166); break;
                      case 5: $steps_color = "greenLight"; $txt_step =  ObtenEtiqueta(1167); break;
                    }
                    // if($fg_status=="RD")
                      // echo "<a class='btn btn-danger btn-xs' href='javascript:certificado();' id='tabs2'><i class='fa fa-address-card-o'></i> ".ObtenEtiqueta(1098)."</a>&nbsp;&nbsp;";
                    // else
                      // echo "<a class='btn btn-success btn-xs' href='javascript:certificado($fl_usuario_doc);' id='tabs2'><i class='fa fa-address-card-o'></i> ".ObtenEtiqueta(1098)."</a>&nbsp;&nbsp;";
                    ?>
                </div>




                                     <!-------------Modal PREVIEW DEL CONTRATO--------------->
                              
                                            <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="abrir">
                                              boton que se ejecua automaticamente
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                              <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                  <div class="modal-header text-center">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>

                                                    <h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;<?php echo $titulo_documento ?></h4>
      
                                                  </div>
                                                  <div class="modal-body" style="padding: 0px;">
                                                      

                                                      <div id="chat-body" class="chat-body custom-scroll" style="background:#fff;">
                                                      
                                                          <div class="row">
                                                              <div class="co-md-6">
                                                                  2
                                                              </div>
                                                              <div class="co-md-6">
                                                                  5
                                                              </div>


                                                          </div>


                                                          <!--Presenta Transcript-->
                                                          <h1 class="text-center"><a href="<?php echo $src_dowloand;  ?>"><i class="fa fa-download" aria-hidden="true"></i></a><?php echo ObtenEtiqueta(1697);?> </h1>
                                                          <h1 class="text-center"><a href="<?php echo $src_dowloand;  ?>"><i class="fa fa-download" aria-hidden="true"></i></a><?php echo ObtenEtiqueta(1698);?> </h1>
                                                           


                                                      </div>
                                                      
          

                                                  </div>
                                                  <div class="modal-footer text-center">
         
                                                     <!--<button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar_modal">Close</button>-->
                                                     <button type="button" class="btn btn-primary" data-dismiss="modal" id="cerrar_modal" >&nbsp;&nbsp;&nbsp;&nbsp; Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
         

                                                  </div>
                                                </div>
                                              </div>
                                            </div>


                                        <!-----------end modal----------------->








                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#programs" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i>Student Information</a>
                    </li>

                </ul>

                
                  



                <div id="myTabContent1" class="tab-content padding-10 no-border">
                
                  <div class="row padding-10">
                    <div class="well col-xs-12 col-sm-5">
                    <?php
                      Profile_pic_FAME($fl_usuario, $fl_programa, 0, $fl_maestro, false);
                    ?>
                    </div>
                    <div class="col-xs-12 col-sm-7">
                      <ul id="sparks" class="">
                        <li>
                          <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                            <a href="<?php echo $src_download_student_archives;  ?>">
                              <i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="Download">&nbsp;</i>
                            </a>
                          </div>
                          <a href="<?php echo $src_download_student_archives;  ?>" rel="tooltip" data-placement="top" data-original-title="White certificate">
                            <i class="fa fa-search">&nbsp;</i>School Archives   
                          </a>
                        </li>
                        <li>
                          <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                            <a href="javascript:certificado(1);">
                              <i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="Download">&nbsp;</i>
                            </a>
                          </div>
                          <a href="javascript:certificado(1);" rel="tooltip" data-placement="top" data-original-title="White certificate">
                            <i class="fa fa-search">&nbsp;</i><?php echo ObtenEtiqueta(1699);?>   
                          </a>
                        </li>
                        <li>
                          <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                            <a href="javascript:certificado(2);">
                              <i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="Download">&nbsp;</i>
                            </a>
                          </div>
                          <a href="javascript:certificado(2);" rel="tooltip" data-placement="top" data-original-title="Color certificate">
                            <i class="fa fa-search">&nbsp;</i><?php echo ObtenEtiqueta(1700);?>   
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  
                  <div class="tab-pane fade in active" id="programs">
                      





                      <div class="row">

                            <div class="col-xs-12 col-sm-6" id="nombre_programa">


                                <?php
                                Forma_Espacio( );
                                Forma_CampoOculto('nb_programa',$nb_programa);
                                 // Forma_CampoTexto(ObtenEtiqueta(1217), True, 'nb_programa', $nb_programa, 100, 30, $nb_programa_err);
                                  
                                  //Forma_CampoTexto(ObtenEtiqueta(284), False, 'nb_programa', $nb_programa, 50, 0,'',False,'',True,'','',  'smart-form form-group','left','col col-sm-6', 'col col-sm-6');
                                  
                                 Forma_CampoOculto('fl_usuario_sp',$fl_usuario);
                                ?>
                            </div>

                          

                      </div>
                      <div class="row">
                           <div class="col-xs-12 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(909), True, 'fname', $fname, 50, 30, !empty($fname_err)?$fname_err:NULL);
                               
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-12 col-sm-6">
                                <?php
                                // Forma_CampoTexto(ObtenEtiqueta(362), True, 'ds_tipo', $ds_tipo, 50, 20, $ds_tipo_err);
                                Forma_CampoTexto(ObtenEtiqueta(910), True, 'lname', $lname, 50, 30, !empty($lname_err)?$lname_err:NULL);
                                ?>
                           </div>
                     </div>

                      <div class="row">
                           <div class="col-xs-12 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(1574), True, 'ds_numero_casa', $ds_numero_casa, 50, 30, !empty($ds_numero_casa_err)?$ds_numero_casa_err:NULL);
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-12 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(1577), True, 'ds_calle', $ds_calle, 50, 30, !empty($ds_calle_err)?$ds_calle_err:NULL);
                                ?>
                           </div>
                     </div>


                      <div class="row">
                           <div class="col-xs-12 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(1575), True, 'ds_ciudad', $ds_ciudad, 50, 30, !empty($ds_ciudad_err)?$ds_ciudad_err:NULL);
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-12 col-sm-6">
                                <?php
                                Forma_CampoTexto(ObtenEtiqueta(1578), True, 'ds_estado', $ds_estado, 50, 30, !empty($ds_estado_err)?$ds_estado_err:NULL);
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
                                Forma_CampoTexto(ObtenEtiqueta(766), False, 'ds_email', $ds_email, 50, 30);
                                
                                 
                                ?>
                           </div>

                          <div class="col-xs-12 col-sm-6">
                              <style>
                                  .input-group .form-control {
                                    width: 119% !important;
                                  }

                              </style>
                                 <div id="div_ds_estado" class="row form-group ">
                                      <label class="col-sm-4 control-label text-align-right">
                                        <strong>* Phone Number:
                                        </strong>
                                      </label>
                                      <div class="col-sm-5">
                                            <label class="input">
            
                                                   <div class="input-group">
                                                      <span class="input-group-addon" id="muestra_codigo" style="background-color: #fff;"> </span>
                                                      <input type="text" class="form-control"  aria-describedby="basic-addon1" name="ds_numero_telefono" id="ds_numero_telefono" value="<?php echo $ds_numero_telefono; ?>">
                                                    </div>
                                            </label>

         
                                      </div>      
                                </div> 


                         </div>
                              


                             
                                




                           </div>
                      
                      
                      <!-- Proceso del certificado --->
                      <div class="row">
                        <div class="col-xs-12 col-sm-6">
                          <div id="div_ds_email" class="row form-group ">
                            <label class="col-sm-4 control-label text-align-right">
                              <strong><?php echo ObtenEtiqueta(1168); ?>:</strong>
                            </label>
                            <div class="col-sm-6">
                              <div class="bar-holder padding-5"><strong></strong>
                                <div class="progress">
                                  <?php                                  
                                  echo "<div class='progress-bar bg-color-".$steps_color."' aria-valuetransitiongoal='".$progreso_total."' 
                                  style='width: ".$progreso_total."%;' aria-valuenow='".$progreso_total."'>".$progreso_total."% (".$txt_step.")</div>";                              
                                  ?>
                                </div>
                              </div>                            
                            </div>      
                          </div>                          
                        </div>
                        <div class="col-xs-12 col-sm-6">
                        <?php
                        $opc = array(ObtenConfiguracion(98), ObtenConfiguracion(99), ObtenConfiguracion(100));
                        $val = array("RD", "RT", "SD");
                        Forma_CampoSelect(ObtenEtiqueta(1169), false, 'fg_status', $opc, $val, $fg_status, '', False, '', 'right', 'col col-sm-4', 'col col-sm-4');
                        ?>
                        </div>
                      </div>
                  </div>




                    

                      



                  

                  </div>

                    
                    <div class="tab-pane fade" id="payments">
					    <div class="row" style="margin-top:-50px;">
                            <div class="col-xs-12 col-sm-12 no-padding padding-bottom-10">
                              <!-------->
                              <!--historuail quiz--->
                            </div>
                        </div>

                          


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

        document.getElementById("nb_programa").disabled = true;


        ValidaInfo();
        MuestraCodigoArea();

        $('#fl_pais').change(function () {

            MuestraCodigoArea();
            ValidaInfo();
        });

        $('#ds_numero_telefono').change(function () {
            ValidaInfo();
        });



        $('#nb_programa').change(function () {
            ValidaInfo();
        });

        $('#fname').change(function () {
            ValidaInfo();
        });

        $('#lname').change(function () {
            ValidaInfo();
        });
        $('#ds_numero_casa').change(function () {
            ValidaInfo();
        });

        $('#ds_calle').change(function () {
            ValidaInfo();
        });

        $('#ds_ciudad').change(function () {
            ValidaInfo();
        });

        $('#ds_estado').change(function () {
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

        var nb_programa = document.getElementById("nb_programa").value;
        var lname = document.getElementById("lname").value;
        var fname = document.getElementById("fname").value;
        var ds_numero_casa = document.getElementById("ds_numero_casa").value;
        var ds_calle = document.getElementById("ds_calle").value;
        var ds_ciudad = document.getElementById("ds_ciudad").value;
        var ds_estado = document.getElementById("ds_estado").value;
        var ds_zip = document.getElementById("ds_zip").value;
        var fl_pais = document.getElementById("fl_pais").value;
        var ds_numero_telefono = document.getElementById("ds_numero_telefono").value;
        var ds_email = document.getElementById("ds_email").value;



            if (nb_programa == '') {
            
                document.getElementById("nb_programa").style.borderColor = "red";
                document.getElementById("nb_programa").style.background = "#fff0f0";

            } else{
                document.getElementById("nb_programa").style.borderColor = "#739e73";
                document.getElementById("nb_programa").style.background = "#f0fff0";
            }


            if (lname == '') {
                document.getElementById("lname").style.borderColor = "red";
                document.getElementById("lname").style.background = "#fff0f0";
            } else {
                document.getElementById("lname").style.borderColor = "#739e73";
                document.getElementById("lname").style.background = "#f0fff0";
            }

            if (fname == '') {
                document.getElementById("fname").style.borderColor = "red";
                document.getElementById("fname").style.background = "#fff0f0";
            } else {
                document.getElementById("fname").style.borderColor = "#739e73";
                document.getElementById("fname").style.background = "#f0fff0";
            }

            if (ds_numero_casa == '') {
                document.getElementById("ds_numero_casa").style.borderColor = "red";
                document.getElementById("ds_numero_casa").style.background = "#fff0f0";
            } else {
                document.getElementById("ds_numero_casa").style.borderColor = "#739e73";
                document.getElementById("ds_numero_casa").style.background = "#f0fff0";
            }

            if (ds_calle == '') {
                document.getElementById("ds_calle").style.borderColor = "red";
                document.getElementById("ds_calle").style.background = "#fff0f0";
            } else {
                document.getElementById("ds_calle").style.borderColor = "#739e73";
                document.getElementById("ds_calle").style.background = "#f0fff0";
            }

            if (ds_ciudad == '') {
                document.getElementById("ds_ciudad").style.borderColor = "red";
                document.getElementById("ds_ciudad").style.background = "#fff0f0";
            } else {
                document.getElementById("ds_ciudad").style.borderColor = "#739e73";
                document.getElementById("ds_ciudad").style.background = "#f0fff0";
            }
            if (ds_estado == '') {
                document.getElementById("ds_estado").style.borderColor = "red";
                document.getElementById("ds_estado").style.background = "#fff0f0";
            } else {
                document.getElementById("ds_estado").style.borderColor = "#739e73";
                document.getElementById("ds_estado").style.background = "#f0fff0";
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



            if (ds_numero_telefono == '') {
                document.getElementById("ds_numero_telefono").style.borderColor = "red";
                document.getElementById("ds_numero_telefono").style.background = "#fff0f0";
            } else {
                document.getElementById("ds_numero_telefono").style.borderColor = "#739e73";
                document.getElementById("ds_numero_telefono").style.background = "#f0fff0";
            }

               


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



            if ((nb_programa.length > 0) && (lname.length > 0) && (fname.length > 0) && (ds_numero_casa.length > 0) && (ds_calle.length > 0) && (ds_ciudad.length > 0) && (ds_estado.length > 0) && (ds_zip.length > 0) && (correcto == 2)) {


                $("#aceptar").removeClass('disabled');



            } else {
                $("#aceptar").addClass('disabled');

            }
    





    }


</script>



  
  <?php

  echo"
   <script>
                                        function archive(){
                                          var answer = confirm('".str_ascii(ObtenMensaje(20))."');
                                          if(answer) {
                                            $.ajax({
                                              type: 'POST',
                                              url : 'archive.php',
                                              async: false,
                                              data: 'clave='+$clave+
                                                    '&fg_archive='+1,
                                              success: function(data) {
                                                 if(data==1)
                                                  window.location = 'courses.php';
                                              }
                                            });
                                          }
                                        }
                                      </script>
  
  ";
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CERTIFICADO_FAME, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_TerminaM($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  # Gabriel
  ?>
  <script>
    function certificado(fg_tipo){
      var user = '<?php echo $fl_usuario; ?>';
      var program = '<?php echo $fl_programa; ?>';
      var url = '<?php echo SP_HOME_W; ?>/fame/site/certificado_pdf.php';
      // Envia datos por forma
      document.certificado.u.value = user;
      document.certificado.p.value = program;
      document.certificado.fg_tipo.value = fg_tipo;
      document.certificado.action = url;
      document.certificado.submit();
    }
  </script>
  <!-- Envia el programa usuario y un flag para identificar que es del back -->
  <form name=certificado method=post>
    <input type=hidden name=u>    
    <input type=hidden name=p>
    <input type=hidden name=fg_tipo>
  </form>
  <?php
  
  
  function Forma_CampoSelectBDM($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4') {
      
      $ds_clase = 'form-control';
      if(!empty($p_error)) {
          $ds_error = ObtenMensaje($p_error);
          $ds_clase_err = 'has-error';
      }
      else {
          $ds_error = NULL;
          $ds_error_err = NULL;
          $ds_clase_err = NULL;
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
  
  
?>