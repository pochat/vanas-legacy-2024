<?php
  
  # Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
  
  # Recibe Parametros
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  
  # Funcion para verificar si tomo los cursos obligatorios
  # Verificamos el programa obligatorio que no tiene prerequisito
  $row = RecuperaValor("SELECT fl_programa_sp FROM c_programa_sp a WHERE a.fg_obligatorio='1' ");
  $fl_programa_obl = $row[0];
  # Iniciado y terminado el curso
  $row9 = RecuperaValor("SELECT fl_usu_pro, fg_terminado FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fg_terminado='1' AND fl_programa_sp=$fl_programa_obl");
  if(empty($fl_programa_obl) || (!empty($row9[0]) && $row9[1]=='1'))
    $fl_programa_obl = $fl_programa_sp;
    
  
  # Verifica que los programas obligatorios ya los haya cursado
  $programa_obligario = Mandatory_programas($fl_usuario, $fl_programa_obl, $fl_programa_sp);  
  if($programa_obligario['inicia']==true){   
    $flprograma = $programa_obligario['prerequisito'];
    $programa = ObtenNombreCourse($flprograma);    
  }
  else{
    $flprograma = $programa_obligario['prerequisito'];
    $programa = ObtenNombreCourse($flprograma);
  }
  
  # Datos del Curso
  # Obtenemos imagen del programa
  $row = RecuperaValor("SELECT nb_thumb FROM c_programa_sp WHERE fl_programa_sp=$flprograma");
  $nb_thumb = $row[0];
  $img = PATH_ADM."/modules/fame/uploads/".$nb_thumb;
      
  # Obtenemos la cantidad de students en este programa
  $Query  = "SELECT COUNT(*) FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
  $Query .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$flprograma." ";
  $row = RecuperaValor($Query);
  $no_students = $row[0];
  # Obtenemos los grupos que existen de este programa en este instituto
  $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
  $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
  $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$flprograma." GROUP BY c.nb_grupo ";
  $rsg = EjecutaQuery($Queryg);
  $no_groups = CuentaRegistros($rsg);
      
  # Numero de lecciones
  $row = RecuperaValor("SELECT COUNT(*) FROM c_leccion_sp WHERE fl_programa_sp = $flprograma");
  $tot_lecciones = ($row[0]);
      
  # Obtenemos si el usuario se puede asignar solo al curso
  $rowg = RecuperaValor("SELECT fg_assign_myself_course FROM c_usuario WHERE fl_usuario=".$fl_usuario."");
  $fg_assign_myself_course = $rowg[0];
  # Obtenemos la informacion del programa
  $row00 = RecuperaValor("SELECT fl_usu_pro, ds_progreso, fg_terminado, fg_status_pro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$flprograma");
  $fl_usu_pro = $row00[0];
  $ds_progreso= $row00[1];
  $new = "";
  $style_pro= "padding-left:13px;";
  # Esta asignado al curso
  if(!empty($row00[0])){
    # Si ya termino el curso solo podra ver sus calificaciones
    # El boton  es color azul
    if($row00[2]==1){
      if($row00[3]==1)
        $btn = "<a href='javascript:user_pause(".$row00[3].",".$flprograma.",".$fl_usuario.", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> ".ObtenEtiqueta(1999)."</a>";
      else
        $btn = "<a href='#site/desktop.php?fl_programa=".$flprograma."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>";                      
    }
    # Continua el curso
    else{
      # Si esta pausado no podra acceder al desktop
      # Tendra que enviar un correo al teacher o espera a que se lo activen
      if($row00[3]==1)
        $btn = "<a href='javascript:user_pause(".$row00[3].",".$flprograma.",".$fl_usuario.", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> ".ObtenEtiqueta(1999)."</a>";        
      else{
        if(empty($row00[1]))
          $btn = "<a  class='btn btn-success' href='javascript: $(\"#ModalPrivacity\").modal(\"toggle\"); redireccionar(\"#site/desktop.php?fl_programa=$flprograma\");'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";          
        else          
          $btn = "<a href='javascript: $(\"#ModalPrivacity\").modal(\"toggle\"); redireccionar(\"#site/desktop.php?fl_programa=$flprograma\");' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a>";
      }
    }
  }
  # No esta asignado al curso
  else{    
    # Para los estudiantes
    if($perfil_usuario== PFL_ESTUDIANTE_SELF){
      # Verificamos si el estudiante puede asignarse solo al curso
      if($fg_assign_myself_course==1){
        $btn = "<a onclick='requeridos(".$flprograma.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
      }
      else{
        $style_pro= "padding-left:25px;";
        $btn  = "
        <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 ' style='padding-left:10px;' >
          <span class='h4'>                            
            <a class='btn btn-danger no-margin' href='javascript: $(\"#ModalPrivacity\").modal(\"toggle\"); myself_layout($fl_programa_sp, $fl_usuario, 122, \"courses_library.php\");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(1888)."&nbsp;<i class='fa fa-envelope-o fa-1x'></i></a>
          </span>                          
        </div>";
      }
    }
    else
      $btn = "<a id='btn_start' href='javascript:redireccionar(\"#site/desktop.php?fl_programa=$flprograma&new=1\");' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
  }
  echo"
  <style>  
 @media (min-width: 768px){
.modal-dialog {
    width: 600px !important;
    margin: 30px auto !important;
}
.mike_jd{
	width:40%;
	margin:10% 10% 15% 30%;
	
}
 }
  </style>
  
  ";
  
 # echo "
 # <script>
 # $('.modal-dialog').css('width', '40%');
 #$('.modal-dialog').css('margin', '10% 10% 15% 30%');
 # </script>";
 
  // $programaa = "<div><strong style='text-decoration:underline;'>".$programa."</strong></div>";
  # Verifica si es necesario un curso 
  # Si ya paso el curso pueden iniciar
  if($flprograma<>$fl_programa_sp){
    if(!ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario, 'fl_programa_sp', $flprograma, true)){
      $title_modal = ObtenEtiqueta(2006)." ".$programaa;
      if(!empty($row00[0])){
        $title_modal = ObtenEtiqueta(2005)." ".$programaa;
        $btn = "<a id='btn_start' href='javascript:requeridos($flprograma);' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
      }
    }
    else{
      $title_modal = ObtenEtiqueta(2006)." ".$programaa;
    }
  }
  else{
    if($flprograma==$fl_programa_sp){
      if(empty($fl_usu_pro))
        $new = "&new=1";
      echo "<script> $(document).ready(function(){
        $('#ModalPrivacity').modal('toggle');
        redireccionar(\"#site/desktop.php?fl_programa=$flprograma".$new."\");
      });</script>";exit;
      
    }
    else{
     $title_modal = ObtenEtiqueta(2005)." ".$programaa;
      $btn = "<a id='btn_start' href='javascript:$(\"#ModalPrivacity\").modal(\"toggle\");  redireccionar(\"#site/desktop.php?fl_programa=$flprograma&new=1\");' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>"; 
    }   
  }  
?>

<!-- Modal del programa que requiere ----->
<div class="modal-dialog mike_jd" role="document" id="modal_actions">
  <div class="modal-content">
  <!--- Header -->
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-smile-o"></i> <?php echo $title_modal; ?></h4>
    </div>
    <!-- Body --->
    <div class="modal-body">
      <div class="product-content product-wrap clearfix">
        <div class="row">
          <!-- Imagen Inf Students Groups-->
          <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="product-image"> 
              <img src="<?php echo $img; ?>" alt="<?php echo $programa; ?>" class="img-responsive" style="margin:auto;"> 
              <div class="product-info smart-form">
                <div class="row">
                  <div class="col-md-3"> 
                  </div>
                  <div class="col-md-12 col-sm-12 col-xs-12"> 
                    <h5>
                      <small>
                        <center><?php echo "Students ($no_students)"; ?></center>
                        <center><?php echo "Groups ($no_groups)"; ?></center>
                      </small>
                    </h5>												
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--- Boton para iniciar o continurar detalles de curso --->
          <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div style="float: right;">                 
                  <a class="btn btn-default disabled"><span class="caret"></span></a> 
                </div> 
              </div> 
            </div>
            <div class="product-deatil" style="padding-top: 0px; padding-bottom:2px;">
              <h5 class="name">
                <a>
                <?php
                echo $programa;
                ?>
                </a>
              </h5>
                <a data-toggle="collapse" aria-expanded="false" aria-controls="collapseExample2" class="disabled">                  
                    <span style="font-size: 24px;color: #21c2f8;font-family: Lato,sans-serif;"><?php echo $tot_lecciones; ?> Lessons</span>                  
                </a>  
                <p></p>
                <div class="row">
                  
                <div class="col-xs-12 col-sm-12 col-md-12"> 
                  <!--- ICH: Div para mostrar informacion general del curso --->
                  <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                    <li class="dropdown">
                      <a href="javascript:void(0);" data-toggle="dropdown" style="padding:0px 0px 0px 0px; background:transparent;" class="disabled"><i class="fa fa-fw fa-sm fa-info-circle " style="color:#9aa7af;" aria-hidden="true"></i></a>
                    </li>
                  </ul>
                  <!--- ICH: Div para mostrar distintas categorias del curso --->
                  <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                    <li class="dropdown">
                      <a href="javascript:void(0);" data-toggle="dropdown" style="padding:0px 0px 0px 0px; background:transparent;"  class="disabled"><i class="fa fa-fw fa-sm fa-tags " style="color:#9aa7af;" aria-hidden="true"></i></a>
                    </li>
                  </ul>
                  <!--- ICH: Div para mostrar informacion del curso --->
                  <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                    <a href="javascript:void(0);"><i class="fa fa-fw fa-sm fa-file-text-o" style="color:#9aa7af;" data-toggle="modal" aria-hidden="true"></i></a>
                  </ul>
                </div>
               </div>
            </div>
            <div class="description"></div>
            <div class="product-info smart-form">
              <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12" >                   
                  <?php echo $btn; ?>
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row">                      
                      <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10" style="<?php echo $style_pro; ?>"> 
                        <div class="progress progress-xs" data-progressbar-value="<?php echo $ds_progreso; ?>"><div class="progress-bar"></div></div>
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
  </div>
</div>