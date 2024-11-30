<?php 
	# Libreria de funciones
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Accion principal
  $accion = $_REQUEST['accion']; 
  # Valor (cadena)a buscar
  $valor = $_REQUEST['valor']; 
  # Valor (cadena)a buscar
  $valor = $_REQUEST['valor'];
  
  // $Query_comp = "( ";
  // foreach ($accion as $np => $valor){
    // if($np == 0)
      // $Query_comp .= " e.fl_cat_prog_sp = $valor ";
    // else
      // $Query_comp .= " OR e.fl_cat_prog_sp = $valor ";
  // }
  // $Query_comp .= ") ";
?>
   

          <!-- ICH: Inicia  DIV principal que muestra cursos -->
          <section id="widget-grid" class="">
            <div class="row">
              <?php 

                $rs_cont_fto = RecuperaValor("SELECT COUNT(1) FROM k_cat_prog_rel_usu_sp WHERE fl_usuario_sp = $fl_usuario");
                $cont_fto = $rs_cont_fto[0];
                
                $rs_cont_fto = RecuperaValor("SELECT fl_cat_prog_sp FROM k_cat_prog_rel_usu_sp WHERE fl_usuario_sp = $fl_usuario AND fg_principal = '1'");
                $fl_cat_prog_sp = $rs_cont_fto[0];

                $Query  = " SELECT fl_leccion_sp, nb_programa".$sufix.", no_semana, ds_titulo".$sufix.", ds_leccion, ";
                $Query .= " CASE WHEN ds_vl_ruta IS NULL THEN '".ObtenEtiqueta(17)."' WHEN ds_vl_ruta='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'Video Brief', ";
                $Query .= " CASE WHEN fg_ref_animacion IS NULL THEN '".ObtenEtiqueta(17)."' WHEN fg_ref_animacion='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'req_anim', ";
                $Query .= " CASE WHEN (SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = 1) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'quiz', ";
                $Query .= " c.no_semanas, a.ds_vl_duracion, a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa, ";
                $Query .= " b.no_creditos, b.ds_duracion, c.no_horas, c.no_workload, c.cl_delivery, c.ds_credential, c.cl_type, c.ds_language, ";
                $Query .= " b.ds_programa, b.ds_learning, b.ds_metodo, b.ds_requerimiento ";
                $Query .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c ";
                if(!empty($cont_fto))
                  $Query .= " , k_categoria_programa_sp e, k_cat_prog_rel_usu_sp f ";
                $Query .= " WHERE  ";
                if(!empty($cont_fto))
                  $Query .= " f.fl_usuario_sp = $fl_usuario AND f.fl_cat_prog_sp = $fl_cat_prog_sp AND e.fl_cat_prog_sp = f.fl_cat_prog_sp AND e.fl_programa_sp = b.fl_programa_sp AND ";
                $Query .= " a.fl_programa_sp = b.fl_programa_sp  AND a.fl_programa_sp = c.fl_programa_sp ";
                $Query .= " GROUP BY b.fl_programa_sp "; 
                $rs = EjecutaQuery($Query);
                $registros = CuentaRegistros($rs);
                
                # Si no es categorias entonces es clave de curso y entra aqui
                if(empty($registros)){
                
                $row_padre = RecuperaValor("SELECT fl_cat_prog_sp FROM k_cat_prog_rel_usu_sp WHERE fl_usuario_sp = $fl_usuario AND fg_principal = '1'");  
                
                $Query  = " SELECT fl_leccion_sp, nb_programa".$sufix.", no_semana, ds_titulo".$sufix.", ds_leccion, ";
                $Query .= " CASE WHEN ds_vl_ruta IS NULL THEN '".ObtenEtiqueta(17)."' WHEN ds_vl_ruta='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'Video Brief', ";
                $Query .= " CASE WHEN fg_ref_animacion IS NULL THEN '".ObtenEtiqueta(17)."' WHEN fg_ref_animacion='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'req_anim', ";
                $Query .= " CASE WHEN (SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = 1) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'quiz', ";
                $Query .= " c.no_semanas, a.ds_vl_duracion, a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa, ";
                $Query .= " b.no_creditos, b.ds_duracion, c.no_horas, c.no_workload, c.cl_delivery, c.ds_credential, c.cl_type, c.ds_language, ";
                $Query .= " b.ds_programa, b.ds_learning, b.ds_metodo, b.ds_requerimiento ";
                $Query .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c ";
                if(!empty($cont_fto))
                  $Query .= " , k_categoria_programa_sp e, k_cat_prog_rel_usu_sp f ";
                $Query .= " WHERE  ";
                if(!empty($cont_fto))
                  $Query .= " f.fl_usuario_sp = $fl_usuario AND ";
                $Query .= " a.fl_programa_sp = b.fl_programa_sp  AND b.fl_programa_sp = $row_padre[0] ";
                $Query .= " GROUP BY b.fl_programa_sp "; 
                $rs = EjecutaQuery($Query);
                $registros = CuentaRegistros($rs);
                }
                
                $y = 0;
                $band = 0;
                for($i=0;$row=RecuperaRegistro($rs);$i++) {
                  $fl_leccion_sp = $row[0];
                  $nb_programa = str_texto($row[1]); 
                  $no_semana = $row[8];
                  $fl_programa_sp = $row[10];
                  $nb_thumb = str_texto($row[11]); 
                  $fg_nuevo_programa = $row[12];
                  $no_creditos = $row[13];
                  $ds_duracion = $row[14];
                  $no_horas = $row[15];
                  $no_workload = $row[16];
                  $cl_delivery = str_texto($row[17]);
                  $ds_credential = str_texto($row[18]);
                  $cl_type = str_texto($row[19]);
                  $ds_language = str_texto($row[20]);
                  $ds_programa = str_uso_normal($row[21]);
                  $ds_learning = str_uso_normal($row[22]);
                  $ds_metodo = str_uso_normal($row[23]);
                  $ds_requerimiento = str_uso_normal($row[24]);                  
                  switch ($cl_delivery) {
                    case "O": $cl_delivery = "Online";    break;
                    case "S": $cl_delivery = "On-Site";   break;
                    case "C": $cl_delivery = "Combined";  break;
                  }
                  switch ($cl_type) {
                    case 1: $cl_type = "Long Term Duration";    break;
                    case 2: $cl_type = "Short Term Duration";   break;
                    case 3: $cl_type = "Corporate";  break;
                    case 4: $cl_type = "Long Term Duration(3 contracts, 1 per year)";  break;
                  }
                  
                  $row = RecuperaValor("SELECT COUNT(*) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa_sp");
                  $no_lecciones = ($row[0]);
                  
                  $img = "../AD3M2SRC4/modules/fame/uploads/".$nb_thumb;

                  if($i <= 1){
                    $style_cuad = "col-sm-6 col-md-6 col-lg-4";
                    $style_img  = "";
                  }else{
                    $style_cuad = "col-sm-6 col-md-6 col-lg-4";
                    $style_img  = "";
                  }
                  
                  # Obtenemos la cantidad de students en este programa
                  $Query  = "SELECT COUNT(*) FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
                  $Query .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp." ";
                  $row = RecuperaValor($Query);
                  $no_studets = $row[0];
                  # Obtenemos los grupos que existen de este programa en este instituto
                  $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
                  $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
                  $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp." GROUP BY c.nb_grupo ";
                  $rsg = EjecutaQuery($Queryg);
                  $no_groups = CuentaRegistros($rsg);                  
                  
                  # Buscamos si esta asignado al curso+
                  # Para mostrar los botones
                  # El maestro puede asiganrse al grupo
                  # Pero un estudiante debe tener permiso por el maestr
                  if(ExisteEnTabla('k_usuario_programa', 'fl_programa_sp', $fl_programa_sp, 'fl_usuario_sp', $fl_usuario, True))
                    $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."'  class='btn btn-warning'>".ObtenEtiqueta(1245)."</a>";
                  else{
                    # Obtenemos si el usuario se puede asignar solo al curso
                    $rowg = RecuperaValor("SELECT fg_assign_myself_course FROM c_usuario WHERE fl_usuario=".$fl_usuario."");
                    $btn_disable = "";
                    $btn_msg = "";
                    if(empty($rowg[0]) AND $perfil_usuario==PFL_ESTUDIANTE_SELF){
                      $btn_disable = "disabled txt-color-white";
                      $btn_msg = "";
                    }
                    $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."&new=1'                     
                    class='btn btn-success ".$btn_disable."'>".ObtenEtiqueta(1244)."</a>";
                    if($perfil_usuario== PFL_ESTUDIANTE_SELF){
                      $btn .= "
                      <div class='padding-10'>
                        <span>
                          <i class='fa fa-exclamation-triangle txt-color-red'></i> ".ObtenEtiqueta(1819)."
                        </span>
                        <a href='javascript:send_email_te($fl_programa_sp, $fl_usuario, 122, \"courses_library.php\");' title='Send Email'><i class='fa fa-envelope-o fa-1x'></i></a>
                      </div>";
                    }
                  }                
                  
                  ?>
                  <div class="<?php echo $style_cuad; ?>">
                    <div class="product-content product-wrap clearfix">
                      <div class="row">
                        <div class="col-md-5 col-sm-12 col-xs-12">
                          <div class="product-image"> 
                            <img src="<?php echo $img; ?>" alt="<?php echo $nb_programa; ?>" class="img-responsive" <?php echo $style_img; ?> > 
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
                                      <a data-toggle="collapse" href="#<?php echo "Collapse_$i"; ?>_1" aria-expanded="false" aria-controls="collapseExample" onclick="funcion1('<?php echo "Collapse_$i";?>_1');">
                                        <center><?php echo "Groups ($no_groups)"; ?></center>
                                      </a> 
                                      <?php
                                        }else{
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
                                <a class="btn btn-default" rel="popover" data-placement="bottom" 
                                  data-content="<div class='col-sm-12' style='padding-left: 0px; padding-right: 0px; padding-bottom:7px;'>
                                      <p style='margin: 0 0 1px;'><?php echo ObtenEtiqueta(1274); ?>:</p>
                                    <div class='icon-addon addon-sm'>
                                      <input placeholder='<?php echo ObtenEtiqueta(1259); ?>' class='form-control' type='text' id='busca_playlist' onkeypress='busca_playlist(this.value, <?php echo "$fl_programa_sp"; ?>);'>
                                      <label for='email' class='glyphicon glyphicon-search' rel='tooltip' title='' data-original-title='email'></label>
                                    </div>
                                    <div class='bs-example'>
                                      <div id='muestra_prueba' style='padding-top:3px;'>
                                      <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
                                        <div id='div_prueba_<?php echo "_$i"; ?>'>
                                        </div>
                                      </div> 
                                    </div>
                                    <hr style='margin-top: 3px; margin-bottom: 3px;'>
                                    <center>
                                    <div id='mtit' style='display: block;  text-align:left; padding-left:7px;'>
                                      <a onclick='AddPlaylist(true)'><span style='color:#9aa7af; font-style: italic;'><?php echo ObtenEtiqueta(1260); ?></span></a>
                                    </div>
                                      <div id='aa' style='display: none;'>
                                        <div class='form-group'>
                                          <input class='form-control' type='text' name='new_playlist' id='new_playlist' placeholder='Playlist name' onkeyup='BtnGuardar();'>
                                          <br>
                                          <div style='float: right;'>
                                          <a class='btn btn-danger btn-xs' onclick='AddPlaylist(false)'><?php echo ObtenEtiqueta(1261); ?></a>
                                          <a class='btn btn-primary btn-xs disabled' href='javascript:GuardaPlaylist(<?php echo $fl_programa_sp; ?>); AddPlaylist(false); busca_playlist();' id='Ccl'><?php echo ObtenEtiqueta(1262); ?></a>
                                          </div>
                                        </div>
                                      </div>
                                    </center>		
                                  </div>
                                  " data-html="true" aria-describedby="popover46188" data-toggle="popover"><span class="caret"></span>
                                </a>     
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
                              </div> 
                            </div> 
                          </div> 
                          <!--------------------------------------------------------------------------------------------------->
                          <!--------------------------------------------------------------------------------------------------->          
                          <div class="product-deatil" style="padding-top: 0px; padding-bottom:2px;">
                            <h5 class="name" style="height:50px;">
                              <a>
                                <?php 
                                  echo $nb_programa; 
                                  // echo "<span>";
                                    // echo ObtenEtiqueta(570)." ";
                                    
                                    // echo "<span rel='popover-hover' data-placement='top' 
                                    // data-original-title='Software' data-html='true'
                                    // data-content='- Software 1 <br> - Software 2'>
                                    // ". ObtenEtiqueta(570)."</span>";
                                    
                                    $rs_cat = EjecutaQuery("SELECT c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE fl_programa_sp = $fl_programa_sp AND k.fl_cat_prog_sp = c.fl_cat_prog_sp ");
                                    $reg_cat = CuentaRegistros($rs_cat);
                                    for($i_cat=0;$row_cat=RecuperaRegistro($rs_cat);$i_cat++) {
                                      $nb_categoria = str_texto($row_cat[0]); 
                                      // echo $nb_categoria;
                                      // if($i_cat != ($reg_cat - 1))
                                        // echo ", ";
                                    }
                                  // echo "</span>";
                                ?>
                              </a>
                            </h5>
                              <a data-toggle="collapse" href="#<?php echo "Collapse_$i"; ?>_2" aria-expanded="false" aria-controls="collapseExample2" onclick="funcion1('<?php echo "Collapse_$i";?>_2');">
                                <p class="price-container">
                                  <span><?php echo $no_lecciones." ".ObtenEtiqueta(1242); ?></span>
                                </p>
                                <span class="tag1"></span> 
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
                                <ul class="nav navbar-nav navbar-left">
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
                                <ul class="nav navbar-nav navbar-left" >
                                  <li class="dropdown">
                                    <a href="javascript:void(0);" data-toggle="dropdown" style="padding:0px 0px 0px 0px; background:transparent;"><i class="fa fa-fw fa-sm fa-tags " style="color:#9aa7af;"></i></a>
                                    <ul class="dropdown-menu" role="menu" style="width:auto; <?php echo $style_esp ?>">
                                      <li>
                                        <dl class="dl-horizontal" style="margin-bottom: 0px;">
                                          
                                          <!-- FIEL OF STUDY -->
                                          <dt><?php echo ObtenEtiqueta(1306).":"; ?></dt>
                                            <dd style="padding-bottom:2px;">
                                              <?php 
                                                $tot_fiel = RecuperaValor("SELECT COUNT(*) FROM c_categoria_programa_sp c, k_categoria_programa_sp k WHERE k.fl_programa_sp = $fl_programa_sp AND c.fl_cat_prog_sp = k.fl_cat_prog_sp AND c.fg_categoria = 'FOS'");
                                                if(!empty($tot_fiel[0])){
                                                  $rs_p = EjecutaQuery("SELECT c.nb_categoria FROM c_categoria_programa_sp c, k_categoria_programa_sp k WHERE k.fl_programa_sp = $fl_programa_sp AND c.fl_cat_prog_sp = k.fl_cat_prog_sp AND c.fg_categoria = 'FOS'");
                                                  for($i_p=0;$i_p<$row_p=RecuperaRegistro($rs_p);$i_p++)
                                                    echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#DE8294; border-color:#DE8294;'>".str_texto($row_p[0])."</span>&nbsp;";
                                                }
                                                else
                                                  echo "&nbsp;"; 
                                              ?>
                                            </dd>
                                           
                                          <!-- CATEGORIAS -->
                                          <dt><?php echo ObtenEtiqueta(1307).":"; ?></dt>
                                            <dd style="padding-bottom:2px;">
                                              <?php 
                                                $tot_cat = RecuperaValor("SELECT COUNT(*) FROM c_categoria_programa_sp c, k_categoria_programa_sp k WHERE k.fl_programa_sp = $fl_programa_sp AND c.fl_cat_prog_sp = k.fl_cat_prog_sp AND c.fg_categoria = 'CAT'");
                                                if(!empty($tot_cat[0])){
                                                  $rs_c = EjecutaQuery("SELECT c.nb_categoria FROM c_categoria_programa_sp c, k_categoria_programa_sp k WHERE k.fl_programa_sp = $fl_programa_sp AND c.fl_cat_prog_sp = k.fl_cat_prog_sp AND c.fg_categoria = 'CAT'");
                                                  for($i_c=0;$i_c<$row_c=RecuperaRegistro($rs_c);$i_c++)
                                                    echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#DE82C9; border-color:#DE82C9;'>".str_texto($row_c[0])."</span>&nbsp;";
                                                }
                                                else
                                                  echo "&nbsp;"; 
                                              ?>
                                            </dd>
                                            
                                          <!-- GRADE -->
                                          <dt><?php echo ObtenEtiqueta(1308).":"; ?></dt>
                                            <dd style="padding-bottom:2px;">
                                              <?php  
                                                $tot_gra = RecuperaValor("SELECT g.nb_grado FROM k_grade_programa_sp r, k_grado_fame g WHERE fl_programa_sp = $fl_programa_sp AND r.fl_grado = g.fl_grado");
                                                if(!empty($tot_gra[0])){
                                                  $rs_g = EjecutaQuery("SELECT g.nb_grado FROM k_grade_programa_sp r, k_grado_fame g WHERE fl_programa_sp = $fl_programa_sp AND r.fl_grado = g.fl_grado");
                                                  for($i_g=0;$i_g<$row_g=RecuperaRegistro($rs_g);$i_g++)
                                                    echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#B482DE; border-color:#B482DE;'>".str_texto($row_g[0])."</span>&nbsp;";
                                                }
                                                else
                                                  echo "&nbsp;"; 
                                              ?>
                                            </dd>
                                          
                                          <!-- HARDWARE -->
                                          <dt><?php echo ObtenEtiqueta(1309).":"; ?></dt>
                                            <dd style="padding-bottom:2px;">
                                              <?php 
                                                $tot_har = RecuperaValor("SELECT c.nb_categoria FROM c_categoria_programa_sp c, k_categoria_programa_sp k WHERE k.fl_programa_sp = $fl_programa_sp AND c.fl_cat_prog_sp = k.fl_cat_prog_sp AND c.fg_categoria = 'HAR'");
                                                if(!empty($tot_har[0])){
                                                  $rs_h = EjecutaQuery("SELECT c.nb_categoria FROM c_categoria_programa_sp c, k_categoria_programa_sp k WHERE k.fl_programa_sp = $fl_programa_sp AND c.fl_cat_prog_sp = k.fl_cat_prog_sp AND c.fg_categoria = 'HAR'");
                                                  for($i_h=0;$i_h<$row_h=RecuperaRegistro($rs_h);$i_h++)
                                                    echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#8682DE; border-color:#8682DE;'>".str_texto($row_h[0])."</span>&nbsp;";
                                                }
                                                else
                                                  echo "&nbsp;"; 
                                              ?>
                                            </dd>
                                          
                                          <!-- SOFTWARE -->
                                          <dt><?php echo ObtenEtiqueta(1310).":"; ?></dt>
                                            <dd style="padding-bottom:2px;">
                                              <?php 
                                                $tot_sof = RecuperaValor("SELECT c.nb_categoria FROM c_categoria_programa_sp c, k_categoria_programa_sp k WHERE k.fl_programa_sp = $fl_programa_sp AND c.fl_cat_prog_sp = k.fl_cat_prog_sp AND c.fg_categoria = 'SOF'");
                                                if(!empty($tot_sof[0])){
                                                  $rs_s = EjecutaQuery("SELECT c.nb_categoria FROM c_categoria_programa_sp c, k_categoria_programa_sp k WHERE k.fl_programa_sp = $fl_programa_sp AND c.fl_cat_prog_sp = k.fl_cat_prog_sp AND c.fg_categoria = 'SOF'");
                                                  for($i_s=0;$i_s<$row_s=RecuperaRegistro($rs_s);$i_s++)
                                                    echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#82A5DE; border-color:#82A5DE;'>".str_texto($row_s[0])."</span>&nbsp;";
                                                }
                                                else
                                                  echo "&nbsp;"; 
                                              ?>
                                            </dd>
                                          
                                          <!-- LEVEL -->
                                          <dt><?php echo ObtenEtiqueta(1311).":"; ?></dt>
                                             <?php
                                                $row_lvl = RecuperaValor("SELECT fg_level FROM c_programa_sp WHERE fl_programa_sp = $fl_programa_sp");
                                                  if(empty($row_lvl[0]))
                                                    $ds_level = "";
                                                  else{
                                                    switch ($row_lvl[0]){
                                                      case 'LVB': $ds_level = ObtenEtiqueta(1317); break;
                                                      case 'LVI': $ds_level = ObtenEtiqueta(1321); break;
                                                      case 'LVA': $ds_level = ObtenEtiqueta(1322); break;
                                                    }
                                                  }
                                                echo "<dd style='padding-bottom:2px;'><span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#82D7DE; border-color:#82D7DE;'>".$ds_level."</span>&nbsp;</dd>"
                                              ?>
                                          
                                          <!-- CODIGO DE CURSO -->
                                          <dt><?php echo ObtenEtiqueta(1312).":"; ?></dt>
                                              <?php
                                                $row_cc = RecuperaValor("SELECT ds_course_code FROM c_programa_sp WHERE fl_programa_sp = $fl_programa_sp");
                                                if(!empty($row_cc[0]))
                                                  echo "<dd style='padding-bottom:2px;'><span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#82DEB4; border-color:#82DEB4;'>".str_texto($row_cc[0])."</span>&nbsp;</dd>";
                                                else
                                                  echo "&nbsp;";
                                              ?>
                                          
                                          <!-- CURSO PREREQUISITO -->
                                          <dt><?php echo ObtenEtiqueta(1313).":"; ?></dt>
                                            <dd style="padding-bottom:2px;">
                                              <?php 
                                                $tot_cup = RecuperaValor("SELECT c.nb_programa".$sufix." FROM k_relacion_programa_sp k, c_programa_sp c WHERE k.fl_programa_sp_act = $fl_programa_sp AND k.fl_programa_sp_rel = c.fl_programa_sp AND fg_puesto = 'ANT'");
                                                if(!empty($tot_cup[0])){
                                                  $rs_cp = EjecutaQuery("SELECT c.nb_programa".$sufix." FROM k_relacion_programa_sp k, c_programa_sp c WHERE k.fl_programa_sp_act = $fl_programa_sp AND k.fl_programa_sp_rel = c.fl_programa_sp AND fg_puesto = 'ANT'");
                                                  for($i_cp=0;$i_cp<$row_cp=RecuperaRegistro($rs_cp);$i_cp++)
                                                    echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#82DE82; border-color:#82DE82;'>".str_texto($row_cp[0])."</span>&nbsp;";
                                                }
                                                else
                                                  echo "&nbsp;";
                                              ?>
                                            </dd>
                                            
                                          <!-- CURSO SIGUIENTE -->
                                          <dt><?php echo ObtenEtiqueta(1314).":"; ?></dt>
                                            <dd style="padding-bottom:2px;">
                                              <?php 
                                                $tot_cus = RecuperaValor("SELECT c.nb_programa".$sufix." FROM k_relacion_programa_sp k, c_programa_sp c WHERE k.fl_programa_sp_act = $fl_programa_sp AND k.fl_programa_sp_rel = c.fl_programa_sp AND fg_puesto = 'SIG'");                                                
                                                if(!empty($tot_cus[0])){
                                                  $rs_cs = EjecutaQuery("SELECT c.nb_programa".$sufix." FROM k_relacion_programa_sp k, c_programa_sp c WHERE k.fl_programa_sp_act = $fl_programa_sp AND k.fl_programa_sp_rel = c.fl_programa_sp AND fg_puesto = 'SIG'");
                                                  for($i_cs=0;$i_cs<$row_cs=RecuperaRegistro($rs_cs);$i_cs++)
                                                    echo "<span class='label label-primary' style='font-weight: normal; padding: .1em .6em .0em; font-size: 95%; background-color:#C2DE82; border-color:#C2DE82;'>".str_texto($row_cs[0])."</span>&nbsp;";
                                                }
                                                else
                                                  echo "&nbsp;";
                                              ?>
                                            </dd>
                                        </dl>                            
                                      </li>
                                    </ul>
                                  </li>
                                </ul>
                                <!--- ICH: Div para mostrar informacion del curso --->
                                <a href="javascript:void(0);"><i class="fa fa-fw fa-sm fa-file-text-o" style="color:#9aa7af;" data-toggle="modal" data-target="#myModal<?php echo "_$i"; ?>"></i></a>
                              </div>
                              </div>
                          </div>
                          
                          <div class="description"></div>
                          <div class="product-info smart-form">
                            <div class="row">
                              <div class="col-md-12 col-sm-6 col-xs-6"> 
                                <!--<a href="#site/desktop.php?fl_programa=<?php echo $fl_programa_sp;?>" class="btn btn-success"><?php echo ObtenEtiqueta(1244); ?></a>-->
                                <?php echo $btn; ?>
                              </div>
                            </div>
                          </div>
                          <div class="modal fade" id="myModal<?php echo "_$i"; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    &times;
                                  </button>
                                  <h3 class="modal-title" id="myModalLabel"><b><center><?php echo " ".ObtenEtiqueta(1295) ?></center></b></h3>
                                </div>
                                <div class="modal-body">
                                  <div class="row">
                                    <div class="col-md-12" style="overflow-y: scroll; height:400px; color:#999; padding:0px 50px; 0px 50px;">
                                      <h5><b><?php echo ObtenEtiqueta(1298); ?></b></h5>                  
                                      <p><?php echo $ds_programa; ?></p>
                                      <br>                  
                                      <h5><b><?php echo ObtenEtiqueta(1300); ?></b></h5>                  
                                      <p><?php echo $ds_learning; ?></p>
                                      <br>                  
                                      <h5><b><?php echo ObtenEtiqueta(1302); ?></b></h5>                  
                                      <p><?php echo $ds_metodo; ?></p>
                                      <br>                  
                                      <h5><b><?php echo ObtenEtiqueta(1304); ?></b></h5>                  
                                      <p><?php echo $ds_requerimiento ?></p>
                                      <br>                  
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-primary" data-dismiss="modal">
                                    Close
                                  </button>
                                </div>
                              </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                          </div><!-- /.modal -->
                       </div>
                      </div>
                    </div>
                  </div>	
                  
                  <?php 
                  // if($registros == 1)
                    // $i = 1;

                      $fl_programa_sp_i = $fl_programa_sp;
                      $y++; 

                    if($y == 3){
                      $band = 1;
                      $limit = $fl_programa_sp_i;
                    }else{
                      if($i == ($registros - 1)){
                        $band = 1;
                        $limit = $fl_programa_sp_i;
                      }else
                        $band = 0 ;
                    }
                    
                    if($band == 1){
                      echo "</div>";
                      echo "<div class='row'>";
                        $Query2  = " SELECT fl_leccion_sp, nb_programa".$sufix.", no_semana, ds_titulo".$sufix.", ds_leccion, ";
                        $Query2 .= " CASE WHEN ds_vl_ruta IS NULL THEN '".ObtenEtiqueta(17)."' WHEN ds_vl_ruta='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'Video Brief', ";
                        $Query2 .= " CASE WHEN fg_ref_animacion IS NULL THEN '".ObtenEtiqueta(17)."' WHEN fg_ref_animacion='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'req_anim', ";
                        $Query2 .= " CASE WHEN (SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = 1) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'quiz', ";
                        $Query2 .= " c.no_semanas, a.ds_vl_duracion, a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa ";
                        $Query2 .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c ";
                        $Query2 .= " WHERE a.fl_programa_sp = b.fl_programa_sp  AND a.fl_programa_sp = c.fl_programa_sp ";
                        $Query2 .= " AND a.fl_programa_sp <=$limit ";
                        $Query2 .= " GROUP BY b.fl_programa_sp ";
                        // $Query2 .= " ORDER BY fg_nuevo_programa DESC  ";
                        $rs2 = EjecutaQuery($Query2);
                        $registros2 = CuentaRegistros($rs2);
                        $h = 0;
                        for($i2=0;$row2=RecuperaRegistro($rs2);$i2++) {   
                          $fl_programa_sp_i = $row2[10];
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
                            <div class="collapse" id="<?php echo "Collapse_$x_i"; ?>_1">
                              <div class="card card-block">
                                <div class="superbox-list active" <?php echo $ste; ?>></div>
                                <div class="superbox-show" style="display: block; padding: 1px 1px 1px 1px; background-color: #ccc;">
                                  <div class="widget-body">
                                    <div class="panel-group smart-accordion-default" id="accordion">
                                      <?php
                                      # Obtenemos los grupos
                                      $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
                                      $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
                                      $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp_i." GROUP BY c.nb_grupo ";
                                      $rsg = EjecutaQuery($Queryg);                      
                                      for($m=0;$rowm=RecuperaRegistro($rsg);$m){
                                        $nb_grupo = str_texto($rowm[0]);
                                        # Obtenemos alumnos de este programa en este curso
                                        $Queryj = "SELECT fl_alumno_sp, ds_nombres, nb_grupo, nb_programa".$sufix.", fg_activo, fe_ultacc, ds_progreso, no_promedio_t FROM ( ";
                                        $Queryj .= "(SELECT a.fl_alumno_sp, CONCAT(ds_nombres,' ', ds_apaterno) ds_nombres, a.nb_grupo, d.nb_programa".$sufix.", c.fg_activo, ";
                                        $Queryj .= "DATE_FORMAT(fe_ultacc, '%Y-%m-%d %H:%i:%s') fe_ultacc, b.ds_progreso, b.no_promedio_t ";
                                        $Queryj .= "FROM c_alumno_sp a LEFT JOIN c_usuario c ON(c.fl_usuario=a.fl_alumno_sp) ";
                                        $Queryj .= "LEFT JOIN k_usuario_programa b ON(b.fl_usuario_sp=a.fl_alumno_sp) LEFT JOIN c_programa_sp d ON(d.fl_programa_sp=b.fl_programa_sp) ";
                                        $Queryj .= "WHERE nb_grupo='".$nb_grupo."' AND b.fl_programa_sp=".$fl_programa_sp_i." AND c.fl_instituto=".$fl_instituto." ) UNION ";
                                        $Queryj .= "(SELECT a.fl_alumno_sp, CONCAT(ds_nombres,' ', ds_apaterno) ds_nombres, 'Unassigned' nb_grupo, 'Unassigned' nb_programa".$sufix.", c.fg_activo, ";
                                        $Queryj .= "DATE_FORMAT(fe_ultacc, '%Y-%m-%d %H:%i:%s') fe_ultacc, '0' ds_progreso, '0' no_promedio_t ";
                                        $Queryj .= "FROM c_alumno_sp a LEFT JOIN c_usuario c ON(c.fl_usuario=a.fl_alumno_sp) ";
                                        $Queryj .= "WHERE NOT EXISTS (SELECT 1 FROM k_usuario_programa d WHERE d.fl_usuario_sp= c.fl_usuario) AND a.nb_grupo='' AND c.fl_instituto=".$fl_instituto." ) ";
                                        $Queryj .= ") as students ";                            
                                        $rsj = EjecutaQuery($Queryj);
                                        $rsx = EjecutaQuery($Queryj);
                                        $tot_alumnos_grupo = CuentaRegistros($rsj);
                                        $no_unassigned=0;
                                        $no_assigned=0;
                                        for($x=0;$rowx=RecuperaRegistro($rsx);$x++){
                                          $asi_course = $rowx[2];
                                          $asi_group = $rowx[3];
                                          if($asi_course=="Unassigned" && $asi_group=="Unassigned")
                                            $no_unassigned++;
                                          else
                                            $no_assigned++;
                                        }                      
                                        echo "
                                        <div class='panel panel-default'>
                                          <div class='panel-heading'>
                                            <h4 class='panel-title'>
                                              <a data-toggle='collapse' data-parent='#accordion' href='#".$nb_grupo."_".$fl_programa_sp_i."' aria-expanded='false' class='collapsed'>
                                                <i class='fa fa-lg fa-fw fa-plus-circle txt-color-green pull-left' style='padding-top:5px;'></i>
                                                <i class='fa fa-lg fa-fw fa-minus-circle txt-color-red pull-left'  style='padding-top:5px;'></i>
                                                <strong>".$nb_grupo."</strong> has ".$tot_alumnos_grupo." students ( $no_assigned assigned - $no_unassigned unassigned) 
                                              </a>
                                            </h4>
                                          </div>
                                          <div id='".$nb_grupo."_".$fl_programa_sp_i."' class='panel-collapse collapse' aria-expanded='false'>
                                            <div class='panel-body' style='margin: 2px;'>
                                              <table class='table table-bordered table-condensed padding-10' id='tbl_usergupo' >
                                                <thead>
                                                  <tr>
                                                    <th></th>
                                                    <th>".ObtenEtiqueta(1054)."</th>
                                                    <th>".ObtenEtiqueta(1055)."</th>
                                                    <th>".ObtenEtiqueta(1075)."</th>
                                                    <th>".ObtenEtiqueta(1217)."</th>
                                                    <th>".ObtenEtiqueta(1057)."</th>
                                                    <th>".ObtenEtiqueta(1150)."</th>
                                                    <th>".ObtenEtiqueta(1077)."</th>
                                                    <th>".ObtenEtiqueta(1078)."</th>
                                                  </tr>
                                                </thead>
                                                <tbody>";                            
                                            for($j=0;$rowj=RecuperaRegistro($rsj);$j++){
                                              $tot_alumnos_grupo = $tot_alumnos_grupo;
                                              $fl_alumno_sp = $rowj[0];                                
                                              $ds_nombres = str_texto($rowj[1]);
                                              $nb_grupo_p = str_texto($rowj[2]);
                                              $nb_programa_p = str_texto($rowj[3]);
                                              $fg_status = $rowj[4];
                                              if(!empty($fg_status)){
                                                $status = ObtenEtiqueta(1041);
                                                $color_sts = "success";
                                              }
                                              else{
                                                $status = ObtenEtiqueta(1042);
                                                $color_sts = "danger";
                                              }
                                              $fe_ultacc = $rowj[5];
                                              $fe_ultacc = time_elapsed_string($fe_ultacc, true);
                                              $ds_progreso = $rowj[6];
                                              $no_promedio_t = $rowj[7];
                                              # Obtenemos el GPA
                                              $Queryp = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)";
                                              $prom_t = RecuperaValor($Queryp);
                                              $cl_calificacion = $prom_t[0];
                                              $fg_aprbado_grl = $prom_t[1];
                                              if(!empty($fg_aprbado_grl))
                                                $color_gpa = "success";
                                              else
                                                $color_gpa = "danger";
                                              if(!empty($ds_progreso))
                                                $gpa = "<span class='label label-".$GPA."' style='padding: 3px;'>".$cl_calificacion." (".$no_promedio_t."%)</span>";
                                              else
                                                $gpa = ObtenEtiqueta(1039);
                                              echo "
                                              <tr>
                                                <td>";
                                                if($nb_grupo_p!='Unassigned' && $nb_programa!='Unassigned'){
                                                  echo "
                                                  <label class='checkbox no-padding no-margin'>
                                                    <input class='checkbox' disabled id='ch_".$fl_alumno_sp."' value='".$fl_alumno_sp."' type='checkbox'><span style='left:20px;'></span>
                                                  </label>";
                                                }
                                                else{
                                                   echo "
                                                  <label class='checkbox no-padding no-margin'>
                                                    <input class='checkbox' id='ch_".$fl_alumno_sp."' value='".$fl_alumno_sp."' type='checkbox' 
                                                    onchange=\"Assign_Grp_Crs($fl_alumno_sp, $fl_programa_sp_i, '$nb_grupo');\">
                                                    <span style='left:20px;'></span>
                                                  </label>";
                                                }
                                              echo "
                                                </td>
                                                <td>
                                                  <div class='project-members'>                                      
                                                    <a href='javascript:void(0)' rel='tooltip' data-placement='top' data-html='true' data-original-title='".$ds_nombres."'>
                                                      <img src='".ObtenAvatarUsuario($fl_alumno_sp)."' class='online' alt='".$ds_nombres."' style='width:25px;'>
                                                    </a>
                                                  </div>
                                                </td>
                                                <td>".$ds_nombres."</td>
                                                <td>".$nb_grupo_p."</td>
                                                <td>".$nb_programa_p."</td>
                                                <td  class='text-align-center'><span class='label label-".$color_sts."' style='padding: 3px;'>".$status."</span></td>
                                                <td>".$fe_ultacc."</td>
                                                <td>
                                                  <div class='progress progress-xs' data-progressbar-value='".$ds_progreso."'><div class='progress-bar'></div></div>
                                                </td>
                                                <td>".$gpa."</td>
                                              </tr>";
                                              
                                            }
                                        echo "  </tbody>
                                              </table>
                                            </div>
                                          </div>
                                        </div>";
                                      }
                                      ?>
                                    </div>
                                  </div>
                                </div>        
                              </div>
                            </div>
                            <!-- ICH: Termina Div Muestra Grupos -->
                            
                            <!-- ICH: Inicia Div Muestra Lecciones -->
                            <div class="collapse" id="<?php echo "Collapse_$x_i"; ?>_2"  id="<?php echo "Collapse_$x_i"; ?>_2">
                              <div class="card card-block">
                                <div class="superbox-list active" <?php echo $ste; ?>></div>
                                <div class="superbox-show" style="display: block; padding: 1px 1px 1px 1px; background-color: #ccc;">
                                  <div class="widget-body">
                                    <div class="panel-group smart-accordion-default">
                                      <div class="panel panel-default">
                                        <div class="panel-collapse collapse in" aria-expanded="false" style="height: auto;">
                                          <div class="panel-body no-padding">
                                            <table class="table table-bordered table-condensed">
                                              <thead>
                                                <tr>
                                                  <th><center><?php echo ObtenEtiqueta(1230); ?></center></th>
                                                  <th><?php echo ObtenEtiqueta(1234); ?></th>
                                                  <th><?php echo ObtenEtiqueta(1297); ?></th>
                                                  <th><center><?php echo ObtenEtiqueta(1219); ?></center></th>
                                                  <th><center><?php echo ObtenEtiqueta(1252); ?></center></th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                <?php
                                                  $Query_l  = " SELECT fl_leccion_sp, no_semana, ds_titulo".$sufix.", a.ds_vl_duracion, a.ds_tiempo_tarea,a.ds_learning ";
                                                  $Query_l .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c ";
                                                  $Query_l .= " WHERE a.fl_programa_sp = $fl_programa_sp_i AND a.fl_programa_sp = b.fl_programa_sp  AND a.fl_programa_sp = c.fl_programa_sp ";
                                                  $Query_l .= " ORDER BY no_orden, no_semana ";
                                                  $rs_l = EjecutaQuery($Query_l);
                                                  for($i_l=0;$row_l=RecuperaRegistro($rs_l);$i_l++) {
                                                    $fl_leccion_sp = $row_l[0];
                                                    $no_semana = $row_l[1];
                                                    $ds_titulo = str_texto($row_l[2]); 
                                                    $ds_vl_duracion = $row_l[3];
                                                    $ds_tiempo_tarea = str_texto($row_l[4]); 
                                                    $ds_learning_2 = str_texto($row_l[5]); 
                                                    echo "<tr>
                                                      <td><center>{$no_semana}</center></td>
                                                      <td>{$ds_titulo}</td>
                                                      <td>{$ds_learning_2}</td>
                                                      <td><center>{$ds_vl_duracion} ".ObtenEtiqueta(1240)."</center></td>
                                                      <td><center>{$ds_tiempo_tarea}</center></td>
                                                    </tr>";
                                                  }
                                                ?>
                                              </tbody>
                                            </table>
                                          </div>
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
                ?>		
            </div>
          </section>
          <!-- ICH: Termina DIV principal que muestra cursos -->
           
    <br><br><br><br><br><br><br><br><br><br>
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