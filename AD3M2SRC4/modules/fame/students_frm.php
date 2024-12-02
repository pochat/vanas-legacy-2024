<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../fame/lib/layout_front_back.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();
  
  # Recibe parametros
  $clave = RecibeParametroHTML('clave');  
  $fg_error = RecibeParametroNumerico('fg_error');

  # Variable initialization to avoid errors
  $tuition=NULL;
  $no_costos_ad=NULL;
  $app_fee=NULL;
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_STUDENTS_FAME, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Inicializa variables
  if(empty($fg_error)) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        $Query  = "SELECT  P.fl_programa_sp,U.fl_usuario,C.nb_programa,U.ds_nombres,U.ds_apaterno,S.ds_state,S.ds_city,S.ds_number,S.ds_zip, ";
        $Query .= "S.ds_phone_number,U.ds_email,S.ds_street,S.fl_pais,U.fg_genero, ";
        $Query .= ConsultaFechaBD('U.fe_nacimiento', FMT_CAPTURA) . " fe_nacimiento, fl_grado,U.fl_instituto,P.fl_maestro,U.fe_alta, U.ds_alias  ";
        $Query .= "FROM c_alumno_sp A
                    LEFT JOIN c_usuario U ON U.fl_usuario=  A.fl_alumno_sp
                    LEFT JOIN k_usuario_programa P ON  P.fl_usuario_sp = A.fl_alumno_sp
                    LEFT JOIN c_programa_sp C ON C.fl_programa_sp=P.fl_programa_sp 
                    LEFT JOIN k_usu_direccion_sp S ON  S.fl_usuario_sp= U.fl_usuario                   
         ";
        if(strpos($clave, "a")==true)
          $Query .= "WHERE U.fl_usuario=".substr($clave, 0, -1);
        else
          $Query .= " WHERE P.fl_usu_pro=$clave ";      
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
      $fe_nacimiento=$row['fe_nacimiento'];
      $fg_genero=!empty($row['fe_genero'])?$row['fe_genero']:NULL;
      $fl_grado = $row['fl_grado'];
	  $fl_instituto=$row['fl_instituto'];
	  $fl_maestro=$row['fl_maestro'];
     $fe_registro=GeneraFormatoFecha($row['fe_alta']);
     $ds_alias = $row['ds_alias'];
      
      #Recuperamos datos de alumno.
      $Query="SELECT ds_ruta_foto,ds_ruta_avatar,ds_oficial FROM c_alumno_sp WHERE fl_alumno_sp=$fl_usuario ";
      $row=RecuperaValor($Query);
      $ds_ruta_foto=$row['ds_ruta_foto'];
      $ds_ruta_avatar=$row['ds_ruta_avatar'];
      $ds_foto_oficial=$row['ds_oficial'];
      
      $src_imagen = PATH_SELF_UPLOADS."/".$fl_instituto."/USER_".$fl_usuario."/".$ds_ruta_avatar;
	  #Recuperamos el nombre del maestro:
	  $Query="SELECT ds_apaterno,ds_nombres FROM c_usuario WHERE fl_usuario=$fl_maestro ";
	  $row=RecuperaValor($Query);
	  $nb_teacher=$row[1]." ".$row[0];
	  
      if(empty($fl_pais)){
	  
		$Query="SELECT fl_pais FROM k_envio_email_reg_selfp   WHERE ds_email='$ds_email' ";
		$row=RecuperaValor($Query);
	    $fl_pais=$row['fl_pais'];
		
		$Query="SELECT count(*) FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_usuario ";
		$row=RecuperaValor($Query);

		if(!empty($row[0])){
			EjecutaQuery("UPDATE k_usu_direccion_sp SET fl_pais=$fl_pais WHERE fl_usuario_sp=$fl_usuario ");
		}else{
			$Query="INSERT INTO k_usu_direccion_sp ( fl_usuario_sp,fl_pais)VALUES($fl_usuario,$fl_pais) ";
			EjecutaQuery($Query);
		}
	  }
    } else { // Alta, inicializa campos
      $nb_programa = "";
      $ds_duracion = "";
      $ds_tipo = "";
    }
  } else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_programa = RecibeParametroHTML('nb_programa');
    $nb_programa_err = RecibeParametroNumerico('nb_programa_err');
  }
  if(strpos($clave, "a")==true)
    $clave = substr($clave, 0, -1);

  $total_tuition = number_format($tuition + $no_costos_ad, 2, '.', '');
  $total = number_format($app_fee + $total_tuition, 2, '.', '');

  #Verificamos si tiene manual asessemt grade
  $Query="SELECT fg_grade_tea, fg_quizes FROM k_details_usu_pro WHERE fl_usu_pro=$clave ";
  $row = RecuperaValor($Query);
  $fg_permite_ver_historial=$row[0]??NULL;
  $fg_permite_ver_historial_quiz=$row[1]??NULL;
	
  # Presenta forma de captura
  PresentaHeader();
  PresentaEncabezado(FUNC_STUDENTS_FAME);
  
  echo"<script src=\"https://use.fontawesome.com/840229d803.js\"></script>";
  echo "<script type='text/javascript' src='".PATH_JS."/frmCourses.js.php'></script>";
  echo"<style>
        .input-group .form-control {
            z-index: 1 !important;    
        }
       </style>";

  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError();
 ?>
<!-- ====Nota: MJD Por X razon , solo asi me funciono el mostrar y ocultar las tabs de teacher grade , solo en este archivo.===---->
 <script>
 function MuestraTeacherGrade(){
     $("#programs").removeClass('active');
     $("#programs").removeClass('in');
     $("#teacher_grade").addClass('active');
     $("#teacher_grade").addClass('in');
     $("#teacher_grade2").addClass('active');
     $("#teacher_grade2").addClass('in');
     $("#quiz").removeClass('active');
     $("#quiz").removeClass('in');
     $("#quiz2").removeClass('active');
     $("#quiz2").removeClass('in');
}
function MuestraQuizHistory(){
     $("#programs").removeClass('active');
     $("#programs").removeClass('in');
     $("#teacher_grade").removeClass('active');
     $("#teacher_grade").removeClass('in');
     $("#teacher_grade2").removeClass('active');
     $("#teacher_grade2").removeClass('in');
     $("#quiz").addClass('active');
     $("#quiz").addClass('in');
     $("#quiz2").addClass('active');
     $("#quiz2").addClass('in');
}
 </script>

<!-- Modal -->
<div class="modal fade" id="modal-empty-student" tabindex="-1" role="dialog" aria-labelledby="item-title" aria-hidden="true">
</div>
<!-- widget content -->
    <div class="widget-body">
        <!-- <div class="pull-right" style="border-top-width: 0!important; margin-top: 5px!important; font-weight: 700;">
        <a class="btn btn-success btn-xs" href="javascript:void(0);" id="add_tab"><i class="fa fa-address-book"></i>Transcript</a>
        <a class="btn btn-danger btn-xs" href="javascript:void(0);" id="tabs2"><i class="fa fa-address-card-o"></i> Certificate</a>&nbsp;&nbsp;
        </div>-->
        <ul id="myTab1" class="nav nav-tabs bordered">
            <li class="active">
                <a href="#programs" data-toggle="tab"><i class="fa fa-fw fa-lg fa-info"></i>Student Information</a>
            </li>
            <li>
                <a href="#quiz" data-toggle="tab" id="historial_quiz" onclick="MuestraQuizHistory();" ><i class="fa fa-fw fa-lg fa-cubes"></i><?php echo ObtenEtiqueta(1919); ?></a>
            </li>
            <li>
                <a href="#teacher_grade" data-toggle="tab" id="teacher_grade" onclick="MuestraTeacherGrade();"><i class="fa fa-fw fa-lg fa-edit"></i><?php echo ObtenEtiqueta(1691); ?></a>
            </li>
            <!------ Change Password ----->
            <div role="menu" class="widget-toolbar">
                <div class="btn-group">
                    <button aria-expanded="true" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
                        Actions <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="javascript:change_pwd(<?php echo $fl_usuario; ?>);"><i class="fa fa-key">&nbsp</i>Change password</a>
                        </li>
                    </ul>
                </div>
            </div>
        </ul>

        <div id="myTabContent1" class="tab-content padding-10 no-border">
            <div class="tab-pane fade in active" id="programs">
                <div class="row padding-10">
                    <div class="well col-xs-12 col-sm-6">
                        <?php
                            Profile_pic_FAME($fl_usuario, $fl_programa, 0, $fl_maestro, false);
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
                            <div class="col-xs-12 col-sm6">
                                <ul id="sparks" class="">
                                    <li>
                                        <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                                            <a href="javascript:certificado(1);">
                                                <i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="Download">&nbsp;</i>
                                            </a>
                                        </div>
                                        <a href="javascript:certificado(1);" rel="tooltip" data-placement="top" data-original-title="white certificate">
                                            <i class="fa fa-search">&nbsp;</i>white certificate
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        &nbsp;
                    </div>
					  

                    <div class="row">
                        <div class="col-xs-12 col-sm-6" id="nombre_programa">

                            <?php
                                Forma_Espacio( );
                                // Forma_CampoTexto(ObtenEtiqueta(1217), True, 'nb_programa', $nb_programa, 100, 30, $nb_programa_err);
                                Forma_CampoOculto('nb_programa', $nb_programa);
                                //Forma_CampoTexto(ObtenEtiqueta(284), False, 'nb_programa', $nb_programa, 50, 0,'',False,'',True,'','',  'smart-form form-group','left','col col-sm-6', 'col col-sm-6');
                                Forma_CampoOculto('fl_usuario_sp',$fl_usuario);
                            ?>
                        </div>
                        <div class="col-xs-12 col-sm-6">&nbsp;
                            <!----------aqui falta defifnir la foto del estudiante------------->
                            <img src="<?php $src_imagen ?>" alt="" class="img-rounded">
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
                                $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116), ObtenEtiqueta(128)); // Masculino, Femenino, Neutral
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
                                    width: 71% !important;
                                }
                            </style>
                            <div id="div_ds_estado" class="row form-group ">
                                <label class="col-sm-4 control-label text-align-right">
                                    <strong>* Phone Number:
                                    </strong>
                                </label>
                                <div class="col-sm-8">
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
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <?php
                                    $Query = "SELECT cl_clasificacion_grado,nb_clasificacion_grado  FROM  c_clasificacion_grado WHERE 1=1 ";
                                    $Query2 = "SELECT fl_grado,nb_grado,cl_clasificacion_grado FROM k_grado_fame WHERE cl_clasificacion_grado=#id_valor# ORDER BY  cl_clasificacion_grado asc ";
                                    CampoSelectBDGRupoFAME('fl_grado', $Query, $fl_grado, 'select2', True, $p_script='',ObtenEtiqueta(1831),false, $Query2);
                                ?>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <?php
                                    Forma_CampoTexto(ObtenEtiqueta(1129), True, 'ds_alias', $ds_alias, 50, 0, !empty($no_telefono_err)?$no_telefono_err:NULL, false,'',true,"onkeypress='return validarnspace(event);' onkeyup='ChangeAlias(".$fl_usuario.");'");
                                    Forma_CampoOculto('ds_alias_bd', $ds_alias);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <?php
							        Forma_CampoOculto('fl_programa_sp',$fl_programa);
                                    $Query = "SELECT CONCAT(ds_nombres,' - ',ds_apaterno), fl_usuario FROM c_usuario WHERE fl_perfil_sp=".PFL_MAESTRO_SELF." AND fl_instituto=$fl_instituto and fg_activo='1' ";
                                    Forma_CampoSelectBDM(ObtenEtiqueta(1917), True, 'fl_maestro', $Query, $fl_maestro, !empty($fl_maestro_err)?$fl_maestro_err:NULL, True,'', 'right', 'col col-sm-4', 'col col-sm-6');
                                ?>
							</div>
					    </div><br>
                        <div class="row">&nbsp;</div><br/>
                    </div>
				</div>

                    <!----=====================--historuail quiz-------==========----->
                    <div class="tab-pane fade" id="quiz2">
					    <div class="row" >
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>
                                            <small><?php echo ObtenEtiqueta(1217); ?></small>
                                            <div class="padding-left-10"><strong><?php echo $nb_programa; ?></strong></div>
                                        </h4>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                                            <a href="<?php echo"../reports/transcript_fame_quiz_rpt.php?c=$clave&u=$fl_usuario&i=$fl_instituto"; ?> ">
                                                <i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="Download">&nbsp;</i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
							</div>			  
                        </div>
						
						<?php if($fg_permite_ver_historial_quiz==1 || PFL_ADMINISTRADOR){ ?><br>
                        <div class="row no-margin">
                            <table  class="table table-striped table-bordered table-hover" width="100%">
                                <thead>
                                <tr>
                                    <th class="text-center"><?php echo ObtenEtiqueta(1605); ?> </th>
                                    <th class="text-center"><?php echo ObtenEtiqueta(1606); ?> </th>
                                    <th class="text-center"><?php echo ObtenEtiqueta(1607); ?> </th>
                                    <th class="text-center"><?php echo ObtenEtiqueta(1608); ?> </th>
                                    <th class="text-center"><?php echo ObtenEtiqueta(1609); ?> </th>
                                    <th class="text-center"><?php echo ObtenEtiqueta(1610); ?> </th>
                                    <th class="text-center"><?php echo ObtenEtiqueta(1611); ?> </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $total_registros_a_dividir=0;
                                                
                                    #Recuperamos todas las lecciones de ese programa
                                    $Query2="SELECT fl_leccion_sp,no_semana,ds_titulo,nb_quiz,no_valor_quiz  FROM 
													c_leccion_sp WHERE fl_programa_sp=$fl_programa AND nb_quiz IS NOT NULL ";
                                    $rs2 = EjecutaQuery($Query2);
                                    $contador2=0;
                                    $total_porcentaje=0;
                                    for($tot2=0;$row2=RecuperaRegistro($rs2);$tot2++) {
                                        $fl_leccion_sp=$row2[0];
                                        $no_session=$row2['no_semana'];
                                        $nb_quiz=$row2['nb_quiz'];

                                        #Recuperamos los no_intentos de  quizes por cada leccion del programa y que tien el alumno..
                                        $Query3="SELECT no_calificacion,no_intento,cl_calificacion,fe_final FROM k_quiz_calif_final 
												WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario=$fl_usuario ORDER BY no_intento ASC ";
                                        //$row3 = RecuperaValor($Query3);
                                        $rs3=EjecutaQuery($Query3);
                                        $contador3=0;
                                        $tot_reg3=CuentaRegistros($rs3);

                                        for($tot3=0;$row3=RecuperaRegistro($rs3);$tot3++){

                                            $fe_termino_quiz=GeneraFormatoFecha($row3['fe_final']);
                                            $attemp=$row3['no_intento'];
                                            $grade=$row3['cl_calificacion'];
                                            $contador3++;

                                            #solo presenta los ultimos intentos validos.
                                            if($contador3==$tot_reg3){
                                                $porcentaje=$row3['no_calificacion']."%";
                                                $no_weight=$row2['no_valor_quiz']."%";
                                                $total_porcentaje+=$row3['no_calificacion'];
                                                $total_registros_a_dividir++;
                                            }else{
                                                $porcentaje=null;
                                                $no_weight=null;
                                            }

                                            echo "<tr>
												<td> ".$fe_termino_quiz." </td>
												<td class='text-center'> ".$no_session." </td>
												<td> ".$nb_quiz."  </td>
												<td class='text-center'> ".$attemp." </td>
												<td class='text-center'> ".$no_weight." </td>
												<td class='text-center'> ".$grade."  </td>
												<td class='text-center'> ".$porcentaje." </td>
											    </tr>";
                                        }
                                    }
                                    #Se realiza calculo de final promedio. y su equivalencia
                                    if($total_registros_a_dividir>0)
                                        $porcentaje_final=$total_porcentaje/$total_registros_a_dividir;
                                    else
                                        $porcentaje_final=$total_porcentaje/1;

                                    #Obtenemos el equivalente.
                                    $Query="SELECT cl_calificacion,no_min,no_max,no_equivalencia FROM c_calificacion_sp WHERE 1=1 ";
                                    $rs4 = EjecutaQuery($Query);
                                    $tot_registros = CuentaRegistros($rs4);
                                    for($i=1;$row4=RecuperaRegistro($rs4);$i++){
                                        $no_min=$row4['no_min'];
                                        $no_max=$row4['no_max'];
                                        if(( $porcentaje_final >=$no_min)&&($porcentaje_final<=$no_max) )
                                            $grade_final=$row4['cl_calificacion'];
                                    }
                                ?>
                                <tr><td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class='text-right'><?php echo ObtenEtiqueta(524).":"; ?></td>
                                    <td class='text-center'><?php echo $grade_final; ?></td>
                                    <td class='text-center'><?php echo $porcentaje_final."%"; ?> </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
					    <?php } else { #presenta mensaje de que no tiene permisos para ver quiz ?>
							<div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="alert alert-danger" >
                                        <strong><i class="fa fa-user-times fa-5" aria-hidden="true"></i></strong>&nbsp;<?php echo "You can't ".ObtenEtiqueta(1268); ?>
                                    </div>
                                </div>
                            </div>
					    <?php } ?>
					</div>
					<!-----====================end quiz======================-->

					<!------=========Inicia Teacher grade=========------>
                      <div class="tab-pane fade" id="teacher_grade2">
                          <?php if($fg_permite_ver_historial==1){ ?>
						            <div class="row">
						                <div class="col-md-12">
							              <div class="row">
								            <div class="col-md-6">
									            <h4>                            
									            <small><?php echo ObtenEtiqueta(1217); ?></small>
									            <div class="padding-left-10"><strong><?php echo $nb_programa; ?></strong></div>
									            </h4>
								            </div>
								            <div class="col-md-6">
                                                <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
									                <a href="<?php echo"../reports/transcript_fame_grade_teacher_cvs.php?c=$clave&u=$fl_usuario&i=$fl_instituto"; ?> ">
									                    <i class="fa fa-file-excel-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="Download">&nbsp;</i>
									                </a>
                                                </div>
									            <div class="txt-color-blue" style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
									            <a href="<?php echo"../reports/transcript_fame_quiz_teacher_rpt.php?c=$clave&u=$fl_usuario&i=$fl_instituto"; ?> ">
									              <i class="fa fa-file-pdf-o txt-color-blue" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="Download">&nbsp;</i>
									            </a>
								              </div>
								            </div>
							  
							  
							              </div>
							
							            <br/>	
							
							  
							
							             <!--- DATOS DE LOS QUIZES ---->
                                        <div class="row no-margin"> 

							
								            <table  class="table table-striped table-bordered table-hover" width="100%">
								              <thead>
									            <tr>
									              <th><?php echo ObtenEtiqueta(1605); ?> </th>
									              <th class='text-center'><?php echo ObtenEtiqueta(1606); ?> </th>
									              <th class='text-center'><?php echo ObtenEtiqueta(1692); ?> </th>
									  
									  
									              <th class='text-center'> <?php echo ObtenEtiqueta(1610); ?> </th>
									              <th class='text-center'><?php echo ObtenEtiqueta(1611); ?> </th>
									            </tr>
								              </thead>
								              <tbody>
								  
								  
								  
								               <?php
                                    #1.verificamos cuantas lecciones existen en esete programa(CUANDO EXISTE FL_PROMEDIO QUIERE DECIR QUE YA ESTA CALIFICADA)
                                    $Query3="SELECT A.fl_alumno,A.fl_leccion_sp,A.fl_promedio_semana,C.nb_programa,B.ds_titulo,B.no_valor_rubric,B.no_semana,D.cl_calificacion,D.no_equivalencia 
											            FROM k_entrega_semanal_sp A
											            JOIN c_leccion_sp B  ON B.fl_leccion_sp=A.fl_leccion_sp 
											            JOIN c_programa_sp C ON C.fl_programa_sp=B.fl_programa_sp
											            JOIN c_calificacion_sp D ON D.fl_calificacion=A.fl_promedio_semana
											            WHERE A.fl_alumno=$fl_usuario AND C.fl_programa_sp=$fl_programa AND fl_promedio_semana IS NOT NULL ORDER BY B.no_semana ASC ";
									
                                    
                                    $rs3 = EjecutaQuery($Query3);
                                    $contador3=0;
                                    $total_reg=CuentaRegistros($rs3);
                                    $sum_porcentaje=0;

                                    for($tot3=0;$row3=RecuperaRegistro($rs3);$tot3++) {
                                        $contador3 ++; 
                                        $fl_leccion_sp=$row3[1];
                                        $fl_promedio_semana=$row3[2];
                                        $nb_leccion=$row3[4];
                                        $no_valor_rubric=$row3[5];
                                        $no_session=$row3[6];
                                        $grade=$row3[7];
                                        $porcentaje=$row3[8];
                                        
                                        # NOTA: EN esta seccion no aplica, se coloca el numero que saco el estudiante.
                                        #Recuperamos la calificacion asignada por el teacher (sin calculos ni equivalencias.)
                                        $Query2="SELECT no_calificacion FROM k_calificacion_teacher WHERE fl_alumno=$fl_usuario and fl_leccion_sp=$fl_leccion_sp AND fl_programa_sp=$fl_programa ";
                                        $row2=RecuperaValor($Query2);
                                        $no_calificacion= $row2['no_calificacion'];
                                        
                                        
                                        #Recupermaos la fecha de utima modificacion/creacion
                                        $Query3 ="SELECT fe_modificacion 
														            FROM c_com_criterio_teacher 
														            WHERE fl_leccion_sp=$fl_leccion_sp AND fl_alumno=$fl_usuario AND fl_programa_sp=$fl_programa  AND fg_com_final='1' ";
                                        
                                        $row3=RecuperaValor($Query3);
                                        $fe_modificacion=GeneraFormatoFecha($row3[0]);
                                        
                                        
                                        

                                        #2. Por cada leccion es uyn registro.
                                        echo "
												              <tr>
													            <td > ".$fe_modificacion." </td>
													            <td class='text-center'> ".$no_session." </td>
													            <td> ".$nb_leccion."  </td>
												   
													            <td class='text-center'> ".$grade."  </td>
													            <td class='text-center'> ".number_format($no_calificacion)."% </td>
												              </tr>";

                                        $sum_porcentaje += $no_calificacion;
                                    }

                                    $total=$contador3==0?0:$sum_porcentaje/$contador3;

                                    #Buscamos en que rangose encuentra y se recuepra el grado final.
                                    $Query="SELECT cl_calificacion,no_min,no_max,no_equivalencia FROM c_calificacion_sp WHERE 1=1 ";
                                    $rs4 = EjecutaQuery($Query);
                                    $tot_registros = CuentaRegistros($rs4);
                                    for($i=1;$row4=RecuperaRegistro($rs4);$i++){
                                        $no_min=$row4['no_min'];
                                        $no_max=$row4['no_max'];
                                        
                                        
                                        if(( $total >=$no_min)&&($total<=$no_max) ){
                                            
                                            $grade_final=$row4['cl_calificacion'];
                                            
                                        }
                                        
                                        
                                    }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right"><?php echo ObtenEtiqueta(524); ?>:</td>
                                        <td class="text-center"><?php echo $grade_final; ?></td>
                                        <td class="text-center"><?php echo number_format($total)."%"; ?></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                        </div>
                                        </div>
						            </div>	

							<?php }else{ #presenta mensaje de que no tiene permitido ver historial  ?>

                            <div class="row">
                                <div class="col-md-12 text-center">

                                    <div class="alert alert-danger" >
                                        <strong><i class="fa fa-user-times fa-5" aria-hidden="true"></i></strong>&nbsp;<?php echo ObtenEtiqueta(1694); ?>
                                    </div>
                                </div>

                            </div>


                            <?php } ?>
							
							
						
						
						
                      </div>
                    <!----=====End teacher grade===---->




                  
               
                </div>
            </div>
  
  <div id="muestra_save"> </div>
<script>


     $(document).ready(function () {

	        $('#fl_maestro').change(function () {
			
			 var fl_maestro = document.getElementById('fl_maestro').value;
			 var fl_programa_sp = document.getElementById('fl_programa_sp').value;
			 var fl_usuario=<?php echo $fl_usuario; ?>;
				$.ajax({
						type: 'POST',
						url: 'guardar_maestro.php',
						data: 'fl_maestro='+fl_maestro+
						      '&fl_usuario='+fl_usuario+
						      '&fl_programa_sp='+fl_programa_sp,
						async: true,
						success: function (html) {

							$('#muestra_save').html(html);

						}

					});
	           
	        });
      });



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

        // Verifica el alias
        ChangeAlias(<?php echo $fl_usuario; ?>);
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

        $('#fg_genero').change(function () {
            ValidaInfo();
        });

        $('#fe_nacimiento').change(function () {
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
        var fg_genero = document.getElementById("fg_genero").value;
        var ds_alias = document.getElementById("ds_alias").value;
        var fe_nacimiento = document.getElementById("fe_nacimiento").value;


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
            
            if (ds_alias == '') {
                document.getElementById("ds_alias").style.borderColor = "red";
                document.getElementById("ds_alias").style.background = "#fff0f0";
            } else {
                document.getElementById("ds_alias").style.borderColor = "#739e73";
                document.getElementById("ds_alias").style.background = "#f0fff0";
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



            if ( (lname.length > 0) && (fname.length > 0) && (ds_numero_casa.length > 0) && (ds_calle.length > 0) && (ds_ciudad.length > 0) && (ds_estado.length > 0) && (ds_zip.length > 0) && (correcto == 2) && ds_alias.length>0) {


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
    $fg_guardar = ValidaPermiso(FUNC_STUDENTS_FAME, PERMISO_MODIFICACION);
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
      }
     
      
     
      
      echo "
  <div class='form-group smart-form ".(!empty($ds_clase_err)?$ds_clase_err:NULL)."'>
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
