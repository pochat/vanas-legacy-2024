<?php
    # Libreria de funciones
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
    $fl_usuario = ValidaSesion(False,0, True);
    $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
    $fl_instituto = ObtenInstituto($fl_usuario);

    # Verifica que el usuario tenga permiso de usar esta funcion
    if(!ValidaPermisoSelf(FUNC_SELF)) {
        MuestraPaginaError(ERR_SIN_PERMISO);
        exit();
    }
  
    # Accion principal
    $accion = $_REQUEST['accion']??NULL;
    # Valor (cadena)a buscar
    $valor = $_REQUEST['valor']??NULL;
  
    #Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
    if($perfil_usuario==PFL_ESTUDIANTE_SELF)
        $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);

    #Identifiacmos si el logueado es b2c
    if(!empty($fg_puede_liberar_curso))
	    $fg_b2c=1;
    else
	    $fg_b2c=0;
    #Los trials no pueden ver boton export
    $fg_plan_instituto_=ObtenPlanActualInstituto($fl_instituto);
    if(empty($fg_plan_instituto_))
    $fg_b2c=1;

    #2020 -sep  verificamos que el instituto no sea b2c.
    $Query="SELECT fg_b2c FROM c_instituto WHERE fl_instituto=$fl_instituto ";
    $row=RecuperaValor($Query);
    $fg_b2c=$row[0];

    #Se agrega validacion para saber si el Instituto puede ver boton de export Moodle
    if(VerBotonExportMoodle($fl_instituto)){
	    $fg_export_moodle=1;
    }else{
        $fg_export_moodle=0;
    }
?>
    <!-- LISTADO DE PROGRAMAS -->
    <!-- ICH: Inicia  DIV que muestra cursos dependiendo de las categorias seleccionadas -->
    <div id="muestra_busqueda_playlist">
        <!-- ICH: Inicia  DIV principal que muestra cursos -->
        <section id="widget-grid" class="">
            <div class="row" id="items_programs">
                <?php
                # Query principal
                $no_fto = RecuperaValor("SELECT fl_fto_cat_sp FROM k_filtro_categoria_fame WHERE fl_usuario_sp=$fl_usuario ORDER BY no_filtro DESC LIMIT 1");
                $no_fto = $no_fto[0]??NULL;

                if(!empty($no_fto)){
                    $Query_2 = "SELECT p.fl_programa_sp, p.nb_programa".$sufix." AS nb_programa, kp.no_semanas, p.nb_thumb, p.fg_nuevo_programa, p.no_creditos, kp.no_horas, kp.no_workload, ";
                    $Query_2 .= "(CASE
                                    WHEN kp.cl_delivery = 'O' THEN 'Online'
                                    WHEN kp.cl_delivery = 'S' THEN 'On-Site'
                                    WHEN kp.cl_delivery = 'C' THEN 'Combined'
                                    WHEN kp.cl_delivery = 'OB' THEN 'Online / Blended'
                                END) AS cl_delivery, ";
                    $Query_2 .= "(CASE
                                    WHEN kp.cl_type = '1' THEN 'Long Term Duration'
                                    WHEN kp.cl_type = '2' THEN 'Short Term Duration'
                                    WHEN kp.cl_type = '3' THEN 'Corporate'
                                    WHEN kp.cl_type = '4' THEN 'Long Term Duration(3 contracts, 1 per year)'
                                END) AS cl_type, ";
                    $Query_2 .= "kp.ds_credential, kp.ds_language, p.no_email_desbloquear, ";
                    $Query_2 .= "p.fl_instituto ";
                    $Query_2 .= "FROM c_programa_sp p LEFT JOIN k_filtro_sugerencia_fame kf ON(kf.fl_programa_sp = p.fl_programa_sp) LEFT JOIN k_programa_detalle_sp kp ON(p.fl_programa_sp = kp.fl_programa_sp) ";
                    $Query_2 .= "WHERE kf.fl_fto_cat_sp = $no_fto AND p.fg_publico='1' ORDER BY fl_programa_sp DESC";
                } else {
                    header("Location: /fame/site/node.php?node=158");
                    exit();
                }

                $rs = EjecutaQuery($Query_2);
                $registros = CuentaRegistros($rs);
                $y = 0;
                $band = 0;
                for($i=0;$row=RecuperaRegistro($rs);$i++) {
                    $fl_programa_sp = $row['fl_programa_sp'];
                    $nb_programa = str_texto($row['nb_programa']);
                    $no_semana = $row['no_semanas'];
                    $nb_thumb = str_texto($row['nb_thumb']);
                    $fg_nuevo_programa = $row['fg_nuevo_programa'];
                    $no_creditos = $row['no_creditos'];
                    $no_horas = $row['no_horas'];
                    $no_workload = $row['no_workload'];
                    $cl_delivery = str_texto($row['cl_delivery']);
                    $ds_credential = str_texto($row['ds_credential']);
                    $cl_type = str_texto($row['cl_type']);
                    $ds_language = str_texto($row['ds_language']);
                    $no_email_desbloquear=$row['no_email_desbloquear'];
				    $fl_instituto_programa=$row['fl_instituto'];

				    # El ultimo filtro que se tendra es el que el programa se puede compartir a nivel general 'o solo puede ver visto por el instituto logueado.
                    $fg_compartir_curso=Share_Course($fl_programa_sp,$fl_instituto_programa,$fl_instituto);

                    if($fg_compartir_curso==1){
                        $row = RecuperaValor("SELECT COUNT(*) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa_sp");
                        $no_lecciones = ($row[0]);

                        $img = PATH_HOME."/modules/fame/uploads/".$nb_thumb;
                        $style_cuad = "col-sm-6 col-md-6 col-lg-4";
                        $style_img  = "margin: auto;";

//                        if($i <= 1){
//                            $style_cuad = "col-sm-6 col-md-6 col-lg-4";
//                            $style_img  = "margin: auto;";
//                        }else{
//                            $style_cuad = "col-sm-6 col-md-6 col-lg-4";
//                            $style_img  = "margin: auto;";
//                        }

                        if($perfil_usuario==PFL_ESTUDIANTE_SELF){
                            #Verificamos el pago del curso
                            $fl_pagado=VerificaPagoCurso($fl_usuario,$fl_programa_sp);

                            $fg_plan_pago=RecuperaPlanActualAlumnoFame($fl_usuario);
                  
                            #Validamos su periodo de vigencia
                            $fg_plan_vigente=VerificaVigenciaPlanAlumno($fl_usuario);
                  
                            if(empty($fg_plan_vigente))
                                $fg_plan_pago=null;

                            #Solo si no tiene un plan de pago verifica email enviados para acceso
                            if(empty($fg_plan_pago)){

                                #Verificmaos el numero de emeial requeridos del programa/curso y si no lo tiene asignado pues lnaza valor por default.
                                $no_email_enviados=CuentaEmailEnviadosDesbloquearCurso($fl_usuario,$fl_programa_sp);
                                if(!empty($no_email_enviados)){
                                    $no_email_desbloquear=$no_email_enviados;
                                }else{
                                    if(empty($no_email_desbloquear)){
                                        $no_email_desbloquear=ObtenConfiguracion(122);
                                    }
                                }
                            }
                        }

                        # Obtenemos la cantidad de students en este programa
                        $Query  = "SELECT COUNT(*) FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
                        $Query .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp." ";
                        $row = RecuperaValor($Query);
                        $no_studets = $row[0];

                        # Obtenemos los grupos que existen de este programa en este instituto
                        $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
                        $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
                        $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp." AND nb_grupo<>'' GROUP BY c.nb_grupo ";
                        $rsg = EjecutaQuery($Queryg);
                        $no_groups = CuentaRegistros($rsg);

                        # Obtenemos si el usuario se puede asignar solo al curso
                        $rowg = RecuperaValor("SELECT fg_assign_myself_course FROM c_usuario WHERE fl_usuario=".$fl_usuario."");
                        $fg_assign_myself_course = $rowg[0];

                        # Obtenemos la informacion del programa
                        $row00 = RecuperaValor("SELECT fl_usu_pro, ds_progreso, fg_terminado, fg_status_pro,fg_asignado_playlist FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa_sp");
                        $fl_usu_pro = $row00['fl_usu_pro']??NULL;
                        $ds_progreso = $row00['ds_progreso']??NULL;

                        $fg_asignado_play_list=$row00['fg_asignado_playlist']??NULL;
                  
                      if(($perfil_usuario==PFL_ESTUDIANTE_SELF)&&(!empty($fg_puede_liberar_curso))){
                          if($fg_plan_pago)
                              $row00[0]=1;
                      }
                      
                      # Esta asignado al curso
                      if(!empty($row00[0])){
                          if($fg_puede_liberar_curso==1){
                              $fg_puede_tomar_curso=VerificaCumplientoRequisitoParaAccederCurso($fl_usuario,$fl_programa_sp,$no_email_desbloquear,$fg_plan_pago,$fl_pagado,$fg_assign_myself_course);
                              $fg_desbloqueado_por_envio_email=DesbloqueadoPorPagoCurso($fl_usuario,$fl_programa_sp);
                              if($fg_desbloqueado_por_envio_email){
                                  if(empty($fg_plan_pago))
                                      $no_dias_faltan_terminar_plan=MuestraTiempoRestanteTrialCurso($fl_usuario,$fl_programa_sp,1);
                                  else
                                      $no_dias_faltan_terminar_plan="";
                              }else{
                                  if(empty($fg_plan_pago))
                                      $no_dias_faltan_terminar_plan=MuestraTiempoRestanteTrialCurso($fl_usuario,$fl_programa_sp);
                                  else
                                      $no_dias_faltan_terminar_plan="";
                              }
                              if($fg_puede_tomar_curso){
                                  #Si ya tiene un progreso
                                  if((!empty($row00['ds_progreso'])) && ($row00['ds_progreso'] <100)) {
                                      $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a>";/*Continue*/
                                  }else if($row00['fg_terminado']==1){
                                      $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>"; /*Review*/
                                  }else{
                                      $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
												<span class='h4'>                            
					    						<a class='btn btn-success no-margin' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye fa-1x'></i>&nbsp;".ObtenEtiqueta(1149)."&nbsp;</a>
												</span>".$no_dias_faltan_terminar_plan."                           
											  </div>";
                                  }
                              }else{
                                  #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
                                  $btn="<div class='row' style='margin:0px;'>
										<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
										<span class='h4' id='btn_desbloquear_curso'>                            
										<a class='btn btn-danger no-margin' style='background-color: #D97789 !important; border-color: #D97789 !important;' onclick='$(\"#ModalPrivacity\").modal(\"toggle\");DesabilitarPagarCurso(".$fl_programa_sp.",".$no_email_desbloquear.");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(2076)."&nbsp;</a>
										</span>".$no_dias_faltan_terminar_plan."                           
										</div>
										</div><br/>
										<div class='row' style='margin:0px;'>
								    	<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
									    <span class='h4'> <a class='btn btn-primary btn-xs mikelangel' href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1' ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2108)." </a></span>
										</div>
										</div> ";
                              }
				  
                          }else{
                              # Si ya termino el curso solo podra ver sus calificaciones
                              # El boton  es color azul
                              if($row00[2]==1){
                                  if($row00[3]==1)
                                      $btn = "<a href='javascript:user_pause(".$row00[3].",".$fl_programa_sp.",".$fl_usuario.", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> ".ObtenEtiqueta(1999)."</a>";
								  else
								      $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>";
                              } else {# Continua el curso
                                  ## Si esta pausado no podra acceder al desktop
                                  # Tendra que enviar un correo al teacher o espera a que se lo activen
                                  if ($row00[3] == 1){
                                      $btn = "<a href='javascript:user_pause(" . $row00[3] . "," . $fl_programa_sp . "," . $fl_usuario . ", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> " . ObtenEtiqueta(1999) . "</a>";
                                  }else{
                                      if(empty($row00[1])){
                                          if($fg_puede_liberar_curso){
                                              #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
                                              $btn="<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
							    					<span class='h4' id='btn_desbloquear_curso'>                            
													<a class='btn btn-danger no-margin'  style='background-color: #D97789 !important; border-color: #D97789 !important;' onclick='$(\"#ModalPrivacity\").modal(\"toggle\");  DesabilitarPagarCurso(".$fl_programa_sp.");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(2076)."&nbsp;</a>
													</span>                          
								    				</div>";
                                          }else{
                                              if($fg_asignado_play_list)
                                                  $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-success'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1149)."</a>";/*Start*/
                                              else
                                                  $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1244)."</a>";
                                          }
                                      }else{
                                          $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a>";
                                      }
                                  }
                              }
                          }
                      } else {# No esta asignado al curso
                          ## Para los estudiantes
                          if($perfil_usuario== PFL_ESTUDIANTE_SELF){
                              if($fg_puede_liberar_curso){
                                  $fg_puede_tomar_curso=VerificaCumplientoRequisitoParaAccederCurso($fl_usuario,$fl_programa_sp,$no_email_desbloquear,'',$fl_pagado,$fg_assign_myself_course);
                                  if($fg_puede_tomar_curso){
                                      $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
											    <span class='h4'>                            
												<a class='btn btn-success no-margin' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye fa-1x'></i>&nbsp;".ObtenEtiqueta(1149)."&nbsp;</a>
												</span>                          
								    			</div>";
                                  }else{
                                      #Verificamos is puede seguir mandando emails
                                      $fg_btn_habilitado=VerificaBotonParaDesbloquearCursoPorMetodoEnvioEmail($fl_usuario,$fl_programa_sp);
                                      $exp=explode("#",$fg_btn_habilitado);
                                      $fg_btn_habilitado=$exp[0];
                                      $etq_disabled=$exp[1];
                                      if($fg_btn_habilitado){
                                          $class="success";
                                          $etqb=ObtenEtiqueta(2121);
                                          $style="background-color: #ea75a5 !important; border-color: #ea75a5 !important;";
                                          $iconob="users";
                                      }else{
                                          $class="danger";
                                          $style="background-color: #b077d9 !important; border-color: #b077d9 !important;";
                                          $etqb=ObtenEtiqueta(2076);
                                          $iconob="lock";
                                      }

                                      #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
                                      $btn="<div class='row' style='margin:0px;'>
											<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
											<span class='h4' id='btn_desbloquear_curso'>                            
											<a class='btn btn-$class no-margin' style='$style' onclick='$(\"#ModalPrivacity\").modal(\"toggle\");DesabilitarPagarCurso(".$fl_programa_sp.",".$no_email_desbloquear.");'><i class='fa fa-$iconob fa-1x'></i>&nbsp;".$etqb."&nbsp;</a>
											</span>                          
											</div>
											</div><br/> 
											<div class='row' style='margin:0px;'>
										    <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
										    <span class='h4'> <a class='btn btn-primary btn-xs mikelangel'  style='magin-left:0px; href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1' ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2108)." </a></span>
										    </div>
											</div> ";
                                  }
                              }else{
                                  # Verificamos si el estudiante puede asignarse solo al curso
                                  if($fg_assign_myself_course==1) {
                                      $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(" . $fl_programa_sp . ")' class='btn btn-success'> <i class='fa fa-check'></i> " . ObtenEtiqueta(1149) . "</a>";
                                  }else{
                                      $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
											    <span class='h4'>                            
												<a class='btn btn-danger no-margin' href='javascript: myself_layout($fl_programa_sp, $fl_usuario, 122, \"courses_library.php\");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(1888)."&nbsp;<i class='fa fa-envelope-o fa-1x'></i></a>
											    </span>                          
											    </div>";
                                  }
                              }
                          } else{

                              #mjd sep-2020 es un instituto b2c y solo puede acceder a cursos que estan permitidos.
                              if($fg_b2c==1){
                                  
                                  #verifgica si el curso esta desbloqueado/asignado/comprado.  */k_orden_desbloqueo_curso_alumno  ESTA TABLA hay que cabuarla de nombre ligara institutos tambien que ya compraron un curso en especifico.
                                  $Querypc="SELECT COUNT(*)FROM k_orden_desbloqueo_curso_alumno where fl_instituto=$fl_instituto AND fl_programa_sp=$fl_programa_sp ";
                                  $rowpc=RecuperaValor($Querypc);
                                  if(!empty($rowpc[0])){
                                      #btn normal de acceso al curso.
                                      $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
                                      
                                  }else{
                                      $class="danger";
                                      $etqb=ObtenEtiqueta(2076);
                                      $style="background-color: #b077d9 !important; border-color: #b077d9 !important;";
                                      $iconob="lock";
                                      $btn = "<a href='javascript:void(0);' style='$style' class='btn btn-$class'> <i class='fa fa-$iconob'></i> ".$etqb."</a>";
                                  }
                                  
                                  
                                  
                              }else{

                                  $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
                              }
                          }
                      }
                  ?>
                  <script type="text/javascript">
                    function funcion1(nom_func) {
                      for(x=0; x < <?php echo $registros; ?>; x++){
                        for(y=1; y<3; y++){
                          collapse = 'Collapse_' + x + '_' + y;
                          if(nom_func != collapse){
                            document.getElementById(collapse).style.display='none';
                          }else{
                            if(document.getElementById(collapse).style.display == 'block'){
                              document.getElementById(collapse).style.display='none';
                            }else{
                              document.getElementById(collapse).style.display='block';
                            }
                          }
                        }
                      }
                    }
                  </script> 
                  <div class="<?php echo $style_cuad; ?>">
                    <div class="product-content product-wrap clearfix">
                      <div class="row">
                        <div class="col-md-5 col-sm-12 col-xs-12">
                          <div class="product-image" style="min-height: 200px; !important"> 
                            <img src="<?php echo $img; ?>" style="margin:auto;"alt="<?php echo $nb_programa; ?>" class="img-responsive" <?php echo $style_img; ?> > 
                            <?php 
                            if(!empty($fg_nuevo_programa)){
                              echo "<span class='tag2 hot'>";
                                echo ObtenEtiqueta(1243); 
                              echo "</span>";  
                            } 
                            ?>
                            <div class="product-info smart-form">
                              <div class="row">
                                <div class="col-md-3"> 
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12"> 
                                  <h5>
                                    <small>
                                      <center><?php echo "Students ($no_studets)"; ?></center>
                                      <?php
                                        if($perfil_usuario == PFL_MAESTRO_SELF or $perfil_usuario == PFL_ADMINISTRADOR){
                                      ?>
                                      <a href="javascript:void(0);" onclick="PresentaModalGroups(<?php echo $fl_programa_sp; ?>, 'G');">
                                        <center><?php echo "Groups ($no_groups)"; ?></center>
                                      </a> 
                                      <?php
                                        }
                                        else{
                                          ?>
                                          <center><?php echo "Groups ($no_groups)"; ?></center>
                                          <?php
                                        }
                                        ?>                                      
                                    </small>
                                  </h5>												
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-7 col-sm-12 col-xs-12">
                          <!--------------------------------------------------------------------------------------------------->
                          <!--------------------------------------------------------------------------------------------------->                           
                          <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                              <div style="float: right;">
                                <script>
                                  // Funciones para actualizar listas de playlist
                                  $(document).ready(function(){
                                    // Actualiza lista principal de playlist (filtra playlist)
                                    $('[data-toggle="popover_principal"]').click(function(){
                                      $.ajax({
                                        type: 'POST',
                                        url : 'site/recupera_playlist.php',
                                        async: false,
                                        data: 'valor=actualiza_lista',
                                        success: function(data) {
                                          $('#div_prueba_a').html(data);
                                        }
                                      });                             
                                    });                        
                                    
                                    // Asigna un curso a un playlist
                                    $('[data-toggle="popover"]').click(function(){
                                      $.ajax({
                                        type: 'POST',
                                        url : 'site/recupera_playlist.php',
                                        async: false,
                                        data: 'valor=add_curso_playlist'+
                                              '&extra=<?php echo "$fl_programa_sp"; ?>',
                                        success: function(data) {
                                          $('#div_prueba_<?php echo "_$i"; ?>').html(data);
                                          // $('#div_prueba_a').html(data);
                                        }
                                      });  
                                    });

                                  });
                                </script>
                                <!-- ### IMPORTANTE ###  Divisiones para playlist -->
                                <a onclick="DespliegaLista_<?php echo "_$i"; ?>(<?php echo $i; ?>, 1,<?php echo $fl_programa_sp; ?>);"  class="btn btn-default"><span class="caret"></span></a>
                                <div id="muestra_listado_ind_playlist_<?php echo "_$i"; ?>"></div>
                              </div> 
                            </div> 
                          </div> 
                          <!--------------------------------------------------------------------------------------------------->
                          <!--------------------------------------------------------------------------------------------------->          
                          <div class="product-deatil" style="padding-top: 0px; padding-bottom:2px;">
                            <h5 class="name">
                              <a>
                                <?php 
                                  echo $nb_programa;
                                ?>
                              </a>
                            </h5>
                              <a  href="javascript:void(0);" onclick="PresentaModalGroups(<?php echo $fl_programa_sp; ?>, 'L');">
                                  <span style="font-size: 24px;color: #21c2f8;font-family: Lato,sans-serif;"><?php echo $no_lecciones." ".ObtenEtiqueta(1242); ?></span>
                              </a>
                              <p></p>
                              <?php
                                if($y == 2)
                                  $style_esp = "left: -332px;";
                                else
                                  $style_esp = "";
                              ?>
                              
                              <div class="row">
                                
                              <div class="col-xs-12 col-sm-12 col-md-12"> 
                                <!--- ICH: Div para mostrar informacion general del curso --->
                                <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                                  <li class="dropdown">
                                    <a href="javascript:void(0);" data-toggle="dropdown" style="padding:0px 0px 0px 0px; background:transparent;"><i class="fa fa-fw fa-sm fa-info-circle " style="color:#9aa7af;"></i></a>
                                    <ul class="dropdown-menu" role="menu" style="width:500px; <?php echo $style_esp ?>">
                                      <li>
                                        <dl class="dl-horizontal">
                                          <dt><?php echo ObtenEtiqueta(360).":"; ?></dt>
                                            <dd><?php echo $nb_programa; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1216).":"; ?></dt>
                                            <dd><?php echo $no_creditos; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1220).":"; ?></dt>
                                            <dd><?php echo $no_horas; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1222).":"; ?></dt>
                                            <dd><?php echo $no_semana; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1252).":"; ?></dt>
                                            <dd><?php echo $no_workload; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1224).":"; ?></dt>
                                            <dd><?php echo $cl_delivery; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1223).":"; ?></dt>
                                            <dd><?php echo $ds_credential; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1226).":"; ?></dt>
                                            <dd><?php echo $cl_type; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1296).":"; ?></dt>
                                            <dd><?php echo $ds_language; ?></dd>
                                        </dl>                            
                                      </li>
                                    </ul>
                                  </li>
                                </ul>                              
                                <!--- ICH: Div para mostrar distintas categorias del curso --->
                                <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                                 <a href="javascript:void(0);" data-toggle="modal" onclick="ModalDetails(<?php echo $fl_programa_sp; ?>, 2);"> <i class="fa fa-fw fa-sm fa-tags " style="color:#9aa7af;"></i></a> 
                                </ul>
                                <!--- ICH: Div para mostrar informacion del curso --->
                                <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                                  <a href="javascript:void(0);"  onclick="ModalDetails(<?php echo $fl_programa_sp; ?>, 3);"><i class="fa fa-fw fa-sm fa-file-text-o" style="color:#9aa7af;" data-toggle="modal" ></i></a>
                                </ul>
								<?php
                                    if($fg_b2c<>1){
                                        if($fg_export_moodle==1){
								?>
									<ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
									  <a href="javascript:void(0);"  onclick="ExportMoodle(<?php echo $fl_programa_sp; ?>, 3);"><i class="fa fa-fw fa-sm fa-external-link" style="color:#9aa7af;" ></i></a>
									
									</ul>
							    <?php
                                        }
                                    }
                                ?>
                              </div>
                              </div>
                          </div>                          
                          <div class="product-info smart-form">
                            <div class="row">
                              <div class="col-md-12 col-sm-12 col-xs-12">                                 
                                <ul class="demo-btns">
                                  <li class="padding-top-10">
                                    <?php echo $btn; ?>
                                  </li>
                                  <li class="padding-top-10">
                                    <?php
                                        if($perfil_usuario == PFL_MAESTRO_SELF || $perfil_usuario == PFL_ADMINISTRADOR){
                                            echo "<a class='btn btn-primary btn-xs' href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1' ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2008)." </a>";
                                        }
                                    ?>
                                  </li>
                                </ul> 
                                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-12 show-stats">
                                  <div class="row">
                                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></div>
                                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10"> 
                                    <?php
                                        $progreso = RecuperaValor("SELECT ds_progreso FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa_sp ");
                                        echo "<div class='progress progress-xs'style='width:93%;'  data-progressbar-value='".($progreso[0]??NULL)."'><div class='progress-bar'></div></div>";
                                    ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>                          
                       </div>
                      </div>
                    </div>
                  </div>
                  <?php
                    $fl_programa_sp_i = $fl_programa_sp;
                    $y++; 

                    if($y == 3){
                        $band = 1;
                        $limit = $fl_programa_sp_i;
                    } else {
                        if($i == ($registros - 1)){
                        $band = 1;
                        $limit = $fl_programa_sp_i;
                        } else {
                            $band = 0;
                        }
                    }
                    # Divs ocultos para las lecciones y los grupos
                    if($band == 1) {
                        echo "</div>";
                        echo "<div class='row'>";
                        $ult = RecuperaValor("SELECT fl_fto_cat_sp FROM k_filtro_categoria_fame WHERE fl_usuario_sp = $fl_usuario ORDER BY no_filtro DESC LIMIT 1 ");
                        if (!empty($ult[0])){
                            $Query2 = " SELECT fl_programa_sp FROM k_filtro_sugerencia_fame WHERE fl_fto_cat_sp = $ult[0] AND fl_programa_sp <= $limit";
                        }else{
                            $Query2 ="SELECT fl_leccion_sp, nb_programa".$sufix." AS nb_programa, no_semana, ds_titulo".$sufix." AS ds_titulo, ds_leccion, ";
                            $Query2.="CASE WHEN ds_vl_ruta IS NULL THEN '".ObtenEtiqueta(17)."' WHEN ds_vl_ruta='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'Video Brief', ";
                            $Query2.="CASE WHEN fg_ref_animacion IS NULL THEN '".ObtenEtiqueta(17)."' WHEN fg_ref_animacion='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'req_anim', ";
                            $Query2.="CASE WHEN (SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = 1) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'quiz', ";
                            $Query2.="c.no_semanas, a.ds_vl_duracion, a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa ";
                            $Query2.="FROM c_leccion_sp a LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) LEFT JOIN k_programa_detalle_sp c ON(c.fl_programa_sp=b.fl_programa_sp) ";
                            $Query2.="WHERE a.fl_programa_sp <=$limit ";
                            $Query2.="GROUP BY b.fl_programa_sp ";
                        }   
                        $rs2 = EjecutaQuery($Query2);
                        $registros2 = CuentaRegistros($rs2);
                        $h = 0;
                        for($i2=0;$row2=RecuperaRegistro($rs2);$i2++) {
                            $fl_programa_sp_i = $row2[0];
                            $x_i = $i2;
                            if($h == 0)
                                $ste = "style='width: 31%;'";
                            if($h == 1)
                                $ste = "style='width: 110%;'";
                            if($h == 2)
                                $ste = "style='width: 190%;'";
                  ?>
                          <div class="superbox col-xs-12 col-sm-12 col-md-12 col-lg-12"> 
                            <!-- ICH: Inicia Div Muestra Grupos -->
                            <div class="collapse" id="<?php echo "Collapse_$x_i"; ?>_1" style="position:relative; top:-23px;">
                              <div class="card card-block">
                                <div class="superbox-list active" <?php echo $ste; ?>></div>
                                <div class="superbox-show" style="display: block; padding: 1px 1px 1px 1px; background-color: #ccc;">
                                  <div class="widget-body">
                                    <div class="panel-group smart-accordion-default" id="accordion_groups_<?php echo $x_i; ?>_1"></div>
                                  </div>
                                </div>        
                              </div>
                            </div>
                            <!-- ICH: Termina Div Muestra Grupos -->
                            
                            <!-- ICH: Inicia Div Muestra Lecciones -->
                            <div class="collapse" id="<?php echo "Collapse_$x_i"; ?>_2" style="position:relative; top:-23px;">
                              <div class="card card-block">
                                <div class="superbox-list active" <?php echo $ste; ?>></div>
                                <div class="superbox-show" style="display: block; padding: 1px 1px 1px 1px; background-color: #ccc;">
                                  <div class="widget-body">
                                    <div class="panel-group smart-accordion-default">
                                      <div class="panel panel-default">
                                        <div class="panel-collapse collapse in" aria-expanded="false" style="height: auto;">
                                          <div class="panel-body no-padding" id="accordion_lecciones_<?php echo $x_i; ?>_2"></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>        
                              </div>
                            </div>
                            <!-- ICH: Termina Div Muestra Lecciones -->
                          </div>								
                          <?php
                          $h++;
                          if($h == 3)
                            $h = 0;
                        }
                      $y = 0; 
                    }
				  }
                }
                ?>		
            </div>
          </section>
          <!-- ICH: Termina DIV principal que muestra cursos -->
    </div>           
<!--    <br><br><br><br><br><br><br><br><br><br>-->
<script type="text/javascript">
pageSetUp();
// DO NOT REMOVE : GLOBAL FUNCTIONS!
function Assign_Grp_Crs(p_user, p_curso, p_grp){
  var asignar;
  if($('#ch_'+p_user).is(':checked'))
    asignar = 1;
  else
    asignar = 0;
  $.ajax({
    type: "POST",
    url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
    data: 'fl_action=100&fl_usuario='+p_user+'&fl_programa_std='+p_curso+'&nb_grupo='+p_grp+'&asignar='+asignar,
    async: false,
    success: function(html){
      location.reload();
    }
  });
}
</script>