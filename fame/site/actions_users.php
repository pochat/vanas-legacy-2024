<?php
# Libreria de funciones
require("../lib/self_general.php");

# Obtenemos el usuario y el instituto
$fl_usuario = ValidaSesion(False, 0, True);
$fl_instituto = ObtenInstituto($fl_usuario);

$fl_action = RecibeParametroNumerico('fl_action');
$ds_titulo = RecibeParametroHTML('ds_titulo');
$seleccionados = RecibeParametroNumerico('seleccionados');
$usuario = RecibeParametroNumerico('usuario');
$confirmado = RecibeParametroNumerico('confirmado');
$tot_reg = RecibeParametroNumerico('tot_reg'); // registros selecionados
$tot_users = RecibeParametroNumerico('tot_users'); // Usuarios seleccionados porque algunos se repiten por los diferentes cursos que toman
$fl_programa_sp_selec = RecibeParametroNumerico('fl_programa_sp');
$fl_usu_pro = RecibeParametroNumerico('fl_usu_pro');

#2020 -sep  verificamos que el instituto no sea b2c.
$Query="SELECT fg_b2c FROM c_instituto WHERE fl_instituto=$fl_instituto ";
$row=RecuperaValor($Query);
$fg_b2c=$row[0];

# color icono
$fa_style = "font-size:95px; color:#e3e3e3;";

# Verificamos si tiene licencias disponibles en caso contrario no lo hara
$fg_plan = ObtenPlanActualInstituto($fl_instituto);
# Si no hay plan 
if (empty($fg_plan)) {

 #si es user b2c.
 #Verifica que el instituto sea b2c.
 $Query="SELECT fg_b2c,no_tot_licencias_b2c FROM c_instituto WHERE fl_instituto=$fl_instituto ";
 $row=RecuperaValor($Query);
 $fg_b2c=$row['fg_b2c'];
 if($fg_b2c==1){
     $tot_licencias=$row['no_tot_licencias_b2c'];
 }else{
     $tot_licencias = ObtenConfiguracion(102);
 }
  # Licencias activadas sin contar al administrador
  $lic_disponibles = $tot_licencias - ObtenNumeroUserInst($fl_instituto);
} else {
  $tot_licencias =  ObtenNumLicencias($fl_instituto);
  # Obtenemos el numero de licencias
  $lic_disponibles = ObtenNumLicenciasDisponibles($fl_instituto);
}

?>

<!------permite que se visualize bien la j , la g en el input----->
<style>
  .smart-form .input input,
  .smart-form .select select,
  .smart-form .textarea textarea {

    padding: 6px 10px;
  }
</style>
<!---====definimos el tamaño del modal-----===--->
<!--<script>
     $('.modal-dialog').css('width', '40%');
     $('.modal-dialog').css('margin', '10% 10% 15% 30%');  
</script>-->

<div class="modal-dialog" role="document" id="modal_actions">
  <div class="modal-content">
    <?php
    # Acciones para agregar estudiantes o teachers
    if ($fl_action == ADD_STD || $fl_action == IMP_STD || $fl_action == ADD_MAE || $fl_action == IMP_MAE) {
      # Si tiene licencias podra agregar mas usuario
      if ((!empty($lic_disponibles) && ($fl_action == ADD_STD || $fl_action == IMP_STD)) || ($fl_action == ADD_MAE || $fl_action == IMP_MAE)) {
    ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridModalLabel"><i class="fa <?php if ($fl_action == ADD_STD || $fl_action == ADD_MAE) echo "fa-user-plus";
                                                                    else echo "fa-users"; ?>"></i> <?php echo $ds_titulo;  ?></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col col-sm-12 col-md-12 col-lg-2 text-align-center" style="padding-top:<?php if ($fl_action == ADD_STD || $fl_action == ADD_MAE) echo "8%";
                                                                                                else echo "2%"; ?>;">
              <i style="<?php echo $fa_style; ?>" class="fa <?php if ($fl_action == ADD_STD || $fl_action == ADD_MAE) echo "fa-user-plus";
                                                            else echo "fa-users"; ?> fa-5x" aria-hidden="true"></i>
            </div>
            <div class="col col-md-12 col-sm-12 col-lg-10">
              <form id="contact-form" class="smart-form" action="mik.php" method="post">
                <fieldset>
                  <!----============contenifo modal---------==========--->
                  <?php
                  # Accion de agregar multiple estudiantes o profesores
                  if ($fl_action == ADD_STD  || $fl_action == ADD_MAE) {
                  ?>
                    <!-- Campo de Email -->
                    <section>
                      <label class="label"><?php echo ObtenEtiqueta(1060); ?></label>
                      <label class="input" id="emails">
                        <i class="icon-append fa fa-envelope-o"></i>
                        <input type="email" name="email" id="email" onkeypress='return validarnn(event);'>
                      </label>
                    </section>
                    <!-- Campo de Fist Name -->
                    <section>
                      <label class="label"><?php echo ObtenEtiqueta(1061); ?></label>
                      <label class="input" id="name">
                        <i class="icon-append fa fa-user"></i>
                        <input type="text" name="ds_fname" id="ds_fname" onkeypress='return validarnn(event);'>
                      </label>
                    </section>
                    <!-- Campo de Last Name -->
                    <section>
                      <label class="label"><?php echo ObtenEtiqueta(1062); ?></label>
                      <label class="input" id="apellido">
                        <i class="icon-append fa fa-user"></i>
                        <input type="text" name="ds_lname" id="ds_lname" onkeyup="HabilitaBotonSend();" onkeypress='return validarnn(event);'>
                      </label>
                    </section>

                  <?php
                  }
                  # Acciones de importar estudiantes o maestros
                  if ($fl_action == IMP_STD  || $fl_action == IMP_MAE) {
                    echo '
            <section class="input-file">
            <label class="input" id="div_file">
              <span class="button"><input require id="fl_archivo" onchange="this.parentNode.nextSibling.value = this.value" 
             accept=".csv"
              type="file"><i class="fa fa-cloud-upload"></i> Browse</span><input readonly="" type="text">
            </label>
            <div class="col-sm-12 padding-top-10">
            <div id="id_example" class="alert alert-info fade in">
              <i class="fa-fw fa fa-info text-align-right"></i>
              <strong>' . ObtenEtiqueta(1063) . ':</strong> ' . ObtenEtiqueta(1064) . ' &nbsp;<a href="' . PATH_SELF . '/div/example.zip">' . ObtenEtiqueta(1065) . ' <i class="fa fa-cloud-download"></i> </a>
            </div>
            </div>
            <div id = "barra" class="col-sm-12 progress hidden">
              <div id = "barra_progreso" aria-valuetransitiongoal="100" aria-valuenow="100" class="progress-bar bg-color-primary"></div>
            </div>
            <div id="inf_invitaciones" class="hidden"></div>
            </section>';
                  }
                  ?>
                  <!-------- finaliza contenido modal -------------->
                </fieldset>
              </form>
              <?php
              if ($fl_action == ADD_STD || $fl_action == IMP_STD)
                echo Info_Lice_Trial($fl_instituto, $fl_usuario);
              ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar_modal"><i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?></button>
          <button type="button" class="btn btn-success disabled txt-color-white" onclick="ProcessInfo()" id="envio_boton"><i class="fa fa-check-circle"></i> <?php echo ObtenEtiqueta(1067); ?></button>
        </div>
      <?php
      }
      # Si no tiene licencias mandara error
      else {
        Licencias_Cero($fa_style);
      }
    }
    # Accion de agregar en un grupo al usuario
    # i es mutiple debe selecionar registros
    if ($fl_action == ASG_GROUP || $fl_action == CAM_GROUP) {
      if (!empty($usuario) || !empty($seleccionados)) {
      ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle"></i> <?php echo ObtenEtiqueta(1923); ?></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-2 text-align-center"><i style="<?php echo $fa_style; ?>" class="fa fa-address-card fa-5x"></i>
            </div>
            <div class="col-md-10">
              <form action="mik.php" method="post" id="contact-form" class="smart-form">
                <input type="hidden" id="fl_progra" name="fl_progra" value="<?php echo $fl_programa_sp_selec; ?>" />
                <input type="hidden" id="fl_usu_progra" name="fl_usu_progra" value="<?php echo $fl_usu_pro; ?>" />
                <fieldset>
                  <select id='fl_gruposp' style="width:100%" class="select2">
                    <option value="0"><?php echo ObtenEtiqueta(1079); ?></option>
                    <option id='opt_add_grupo' value="ADDGRP"><?php echo ObtenEtiqueta(1080); ?></option>
                    <?php
                    // $Query  = "SELECT DISTINCT al.nb_grupo, al.nb_grupo  FROM c_alumno_sp al, c_usuario usr ";
                    // $Query .= "WHERE al.fl_alumno_sp = usr.fl_usuario AND usr.fl_instituto='$fl_instituto' ";
                    $Query  = "SELECT nb_grupo, grupo2 FROM ( ";
                    $Query .= "(SELECT al.nb_grupo, al.nb_grupo grupo2 FROM c_alumno_sp al, c_usuario usr ";
                    $Query .= "WHERE al.fl_alumno_sp = usr.fl_usuario AND usr.fl_instituto='$fl_instituto' AND nb_grupo<>'' GROUP BY nb_grupo) ";
                    $Query .= "UNION ";
                    $Query .= "(SELECT g.nb_grupo,g.nb_grupo grupo2 FROM c_grupo_fame g WHERE fl_instituto=$fl_instituto AND nb_grupo<>''GROUP BY nb_grupo) ";
                    $Query .= "UNION ";
                    $Query .= "(SELECT a.nb_grupo, a.nb_grupo grupo2 ";
                    $Query .= "FROM k_envio_email_reg_selfp a WHERE a.fl_invitado_por_instituto='$fl_instituto' AND a.nb_grupo<>'' AND fg_enviado='1' AND fg_confirmado='0' GROUP BY nb_grupo) ";
                    $Query .= ") as main GROUP BY nb_grupo ";
                    $rs = EjecutaQuery($Query);
                    for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
                      echo "<option value='$row[0]'>$row[0]</option>";
                    }
                    ?>
                  </select>
                </fieldset>
                <!-- Nuevo Grupo -->
                <fieldset id='sec_add_group' class="hidden">
                  <label class="label"><?php echo ObtenEtiqueta(1081); ?></label>
                  <label class="input" id="input_new_group">
                    <i class="icon-append fa fa-pencil-square"></i>
                    <input type="text" name="new_group" id="new_group">
                  </label>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="row">
            <div class="col-md-12">
              <button class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?>
              </button>
              <!-- actions_ADD (Active Desactive Delete) -->
              <a class="btn btn-success disabled txt-color-white" id="action_asg_grp">
                <i class="fa fa-check-circle"></i> <?php echo ObtenEtiqueta(1534); ?>
              </a>
            </div>
          </div>
        </div>
      <?php
      } else {
        SeleccioneUsuarios();
      }
    }
    # Accion de agregar en un curso al usuario
    # i es mutiple debe selecionar registros
    if ($fl_action == ASG_COURSE) {
      if (!empty($usuario) || !empty($seleccionados)) {
      ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle"></i> <?php echo ObtenEtiqueta(1924); ?></h4>
        </div>
        <div class="modal-body">

          <div class="row">
            <div class="col-md-2 text-align-center"><i style="<?php echo $fa_style; ?>" class="fa fa-address-card fa-5x"></i>
            </div>
            <div class="col-md-10">
              <form action="mik.php" method="post" id="contact-form" class="smart-form">
                <fieldset>
                  <?php
                  $Query = "SELECT  a.nb_programa, a.fl_programa_sp FROM c_programa_sp a WHERE 1=1 ";
                  # Si es seleccion multiple mostrara todos los cursos
                  # Mostrara los cursos queno ha tomado el usuario              
                  if (empty($seleccionados)) {
                    # Confirmado revisa el k_usuario_programa
                    if (!empty($confirmado)) {
                      $rs = EjecutaQuery("SELECT fl_programa_sp FROM k_usuario_programa WHERE fl_usuario_sp=$usuario");
                      $no = CuentaRegistros($rs);
                      for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
                        $Query .= " AND a.fl_programa_sp <> " . $row[0];
                      }
                    }
                    # No confirmado revisa k_noconfirmado_pro
                    else {
                      $rs = EjecutaQuery("SELECT fl_programa_sp FROM k_noconfirmado_pro WHERE fl_envio_correo=$usuario");
                      $no = CuentaRegistros($rs);
                      for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
                        $Query .= " AND a.fl_programa_sp <> " . $row[0];
                      }
                    }
                  }
                  $Query .= " AND EXISTS(SELECT 1 FROM c_leccion_sp c WHERE c.fl_programa_sp=a.fl_programa_sp) ORDER BY nb_programa ASC ";
                  # Indicamos que vamos asignar al usuario a todos los cursos
                  # Obtenemos la cantidad de cursos que existen
                  $row0 = RecuperaValor("SELECT COUNT(*) FROM c_programa_sp a WHERE EXISTS(SELECT 1 FROM c_leccion_sp c WHERE c.fl_programa_sp=a.fl_programa_sp) ");
                  $extra = "<option value='tot_courses'>" . ObtenEtiqueta(1994) . " <strong>(" . $row0[0] . ")</strong></option>";
                 
                  #mjd sep 2020  si es un instituto que es b2c solo mostrara los cursos adquiridos.
                  if($fg_b2c==1){
                      $Query ="SELECT a.nb_programa,p.fl_programa_sp FROM k_orden_desbloqueo_curso_alumno p
	                        JOIN c_programa_sp a ON a.fl_programa_sp=p.fl_programa_sp AND EXISTS( SELECT 1 FROM c_leccion_sp c WHERE c.fl_programa_sp=a.fl_programa_sp  )
	                        WHERE p.fl_instituto= $fl_instituto ORDER BY a.nb_programa  ";
                      $row0 = RecuperaValor("SELECT COUNT(*) FROM k_orden_desbloqueo_curso_alumno p
	                                         JOIN c_programa_sp a ON a.fl_programa_sp=p.fl_programa_sp AND EXISTS( SELECT 1 FROM c_leccion_sp c WHERE c.fl_programa_sp=a.fl_programa_sp  )
	                                         WHERE p.fl_instituto= 523 ORDER BY a.nb_programa ");
                      $extra = "<option value='tot_courses'>" . ObtenEtiqueta(1994) . " <strong>(" . $row0[0] . ")</strong></option>";
                  }
                  
                  CampoSelectBD('fl_programasp', $Query, 'ALL', 'select2', True, 'style="width:100%"', '', ObtenEtiqueta(1082), '0', $extra);


                  ?>
                  <!--</select>-->
                  <div id="tot_reg_seleccionados" class="hidden"></div>
                  <a id='inf_no_confirmados' class='text-warning cursor-pointer hidden'>
                    <i class='fa fa-warning text-warning'></i> <?php echo ObtenEtiqueta(1083); ?> <strong id="num_no_confirmados"></strong>
                  </a>
                  <div id="reg_no_confirmados" class="hidden"></div>
                  <br /><a id='inf_confirmados' class='text-danger cursor-pointer hidden'>
                    <i class='fa fa-bell text-danger'></i> <?php echo ObtenEtiqueta(1084); ?> <strong id="num_confirmados"></strong>
                  </a>
                  <div id="reg_confirmados" class="hidden"></div>
                </fieldset>


                <fieldset>
                  <?php
                  /*$Query="SELECT b.nb_playlist, b.fl_playlist  
                        FROM c_usuario a JOIN c_playlist b ON a.fl_usuario = b.fl_usuario  
                        WHERE a.fl_instituto = $fl_instituto 
                        AND a.fl_perfil_sp != ".PFL_ESTUDIANTE_SELF." AND a.fl_usuario =$fl_usuario   ORDER BY b.fl_usuario, b.nb_playlist ASC ";
                */
                  $Query = "SELECT b.nb_playlist, b.fl_playlist  
                        FROM c_usuario a JOIN c_playlist b ON a.fl_usuario = b.fl_usuario  
                        WHERE a.fl_instituto = $fl_instituto 
                        AND a.fl_perfil_sp != " . PFL_ESTUDIANTE_SELF . "    ORDER BY b.fl_usuario, b.nb_playlist ASC ";

                  $row0 = RecuperaValor("SELECT COUNT(*) FROM c_usuario a JOIN c_playlist b ON a.fl_usuario = b.fl_usuario WHERE a.fl_instituto = $fl_instituto AND a.fl_perfil_sp != " . PFL_ESTUDIANTE_SELF . " AND a.fl_usuario = $fl_usuario  ORDER BY b.fl_usuario, b.nb_playlist ASC ");
                  $extra = "<option value='tot_play'>" . ObtenEtiqueta(2133) . " <strong>(" . $row0[0] . ")</strong></option>";
                  CampoSelectBDPlay('fl_play_list', $Query, 'ALL', 'select2', True, 'style="width:100%"', '', ObtenEtiqueta(2132), '0', $extra);

                  ?>
                </fieldset>

              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="row">
            <div class="col-md-12">
              <button class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?>
              </button>
              <a class="btn btn-success disabled txt-color-white" id="action_asg_course">
                <i class="fa fa-play"></i> <?php echo ObtenEtiqueta(1534); ?>
              </a>
            </div>
          </div>
        </div>
      <?php
      } else {
        SeleccioneUsuarios();
      }
    }
    # Activamos Desactivamos o eliminamos estudiantes
    # Desasignamos al estudiante del programa
    if ($fl_action == ACTIVE || $fl_action == DESACTIVE) { # Acciones apara Activar Desactivar o Elimnar usuarios
      if (!empty($seleccionados)) {
        switch ($fl_action) {
          case ACTIVE:
            $msg = ObtenMensaje(232);
            break;
          case DESACTIVE:
            $msg = ObtenMensaje(233);
            break;
          case DELETE:
            $msg = ObtenMensaje(234);
            break;
          case DESASIGNAR_COURSE:
            $msg = ObtenMensaje(235);
            break;
        }
      ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle"></i> <?php echo $ds_titulo . " &nbsp;" . date('M d, Y'); ?></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-2 text-align-center"><i style="<?php echo $fa_style; ?>" class="fa fa-warning fa-5x"></i>
            </div>
            <div class="col-md-10">
              <form action="mik.php" method="post" id="contact-form" class="smart-form">
                <fieldset>
                  <h3><?php echo $msg; ?></h3>
                </fieldset>
                <?php
                # Obtenemos los valores de los usuarios seleccionados
                # Si los usuarios seleccionados son mayor a las licencias disponibles 
                # Muestra mensaje indicando que no hay licencias disponibles para los usuariios seleccionados 
                # Deshabilitamos el boton
                if ($fl_action == ACTIVE && $tot_users > $lic_disponibles) {
                  echo '
                  <div class="alert alert-block alert-warning">
                    <h4 class="alert-heading">Warning!</h4>
                    <p class="text-align-left">
                      You selected <strong>' . $tot_users . '</strong> users but have <strong>' . $lic_disponibles . '</strong> license disponibles
                    </p>
                  </div>
                  <script>$("#actions_ADD").remove();</script>';
                }
                ?>
              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-md-12 pull-right">
            <button class="btn btn-sm btn-default" data-dismiss="modal">
              <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?>
            </button>
            <?php
            if ($fl_action == DESASIGNAR_COURSE) {
              echo "             
              <!-- actions_ADD (Active Desactive Delete) -->
              <a class='btn btn-sm btn-danger' id='actions_ADD'>
                <i class='fa fa-stop'></i> " . ObtenEtiqueta(1903) . "
              </a>
              <a class='btn btn-sm btn-warning' id='action_pause_course'>
                <i class='fa fa-pause'></i> " . ObtenEtiqueta(1904) . "
              </a>";
            } else {
              echo "
              <!-- actions_ADD (Active Desactive Delete) -->
              <a class='btn btn-sm btn-success' id='actions_ADD'>
                <i class='fa fa-check-circle'></i> " . ObtenEtiqueta(1534) . "
              </a>";
            }
            ?>
          </div>
        </div>
      <?php
      } else {
        SeleccioneUsuarios();
      }
    }


    # Mostramos la accion para indicar al teachers si el califica o no
    if ($fl_action == ASSESSMENT) {
      # Aqui recibimos el usuario y el programa
      if (!empty($seleccionados) || !empty($usuario)) {
        if (!empty($usuario)) {
          $row = RecuperaValor("SELECT a.fg_quizes, a.fg_grade_tea, b.fl_usu_pro FROM k_details_usu_pro a, k_usuario_programa b WHERE a.fl_usu_pro = b.fl_usu_pro AND a.fl_usu_pro=$usuario");
          $fg_quizes = $row[0];
          $fg_grade_tea = $row[1];
          $checked = "";
          // $user = "";
          if (!empty($fg_grade_tea))
            $checked = "checked";
          $user = "<input type='hidden' id='fl_usu_pro' name='fl_usu_pro' value='" . $row[2] . "'>";
        }
      ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle"></i> <?php echo ObtenEtiqueta(1908); ?></h4>
        </div>
        <div class="modal-body smart-form">
          <div class="row padding-10">
            <div class="col-sm-12 col-md-12 col-lg-2 text-align-center" style="padding-top:5%;"><i style="<?php echo $fa_style; ?>" class="fa fa-bar-chart fa-5x"></i>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-10">
              <div class="row padding-10">

                <label class="checkbox"><input id="fg_quizez" name="fg_quizez" type="checkbox" checked disabled><i></i><strong><?php echo ObtenEtiqueta(1910); ?> </strong>
                  <div>
                    <p><?php echo ObtenEtiqueta(1911); ?></p>
                  </div>
                </label>

              </div>
              <div class="row  padding-10">
                <label class="checkbox">
                  <input id="fg_grade_tea" name="fg_grade_tea" type="checkbox" <?php echo $checked; ?>><i></i>
                  <a style="color:#000;" rel="tooltip" data-placement="top" data-original-title="Gabriel"><strong><?php echo ObtenEtiqueta(1912); ?></strong></a>
                  <div>
                    <p><?php echo ObtenEtiqueta(1913); ?></p>
                  </div>
                </label>
              </div>
              <?php
              if (!empty($seleccionados)) {
                echo "<div class='alert alert-info fade in'>
              <i class='fa-fw fa fa-info'></i>
              <!--<strong>" . ObtenEtiqueta(1978) . "</strong>-->" . ObtenEtiqueta(1979) . "
            </div>";
              }
              echo $user; ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="row">
            <div class="col-md-12">
              <button class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1914); ?>
              </button>
              <?php
              if (!empty($usuario)) {
                echo '<a class="btn btn-primary" id="btn_asig_grade_tec_0">
              <i class="fa fa-check-circle"></i> ' . ObtenEtiqueta(1915) . '
            </a>';
              } else {
                echo '<a class="btn btn-success txt-color-white" id="btn_asig_grade_tec">
              <i class="fa fa-check-circle"></i> ' . ObtenEtiqueta(1915) . '
            </a>';
              }
              ?>
            </div>
          </div>
        </div>
      <?php
      } else
        SeleccioneUsuarios();
    }

    if ($fl_action == DESASIGNAR_COURSE) {
      if (!empty($seleccionados)) {
      ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle"></i> <?php echo $ds_titulo . " &nbsp;" . date('M d, Y'); ?></h4>
        </div>
        <div class="modal-body">
          <div class="row padding-10">
            <div class="col-md-2 text-align-center padding-10"><i style="<?php echo $fa_style; ?>" class="glyphicon glyphicon-warning-sign fa-5x"></i>
            </div>
            <div class="col-md-10 padding-10">
              <form action="mik.php" method="post" id="contact-form" class="smart-form">
                <div class="row padding-bottom-10 h4">
                  <?php echo ObtenEtiqueta(1996); ?>
                </div>
                <label class="checkbox">
                  <input id="return_course" name="return_course" type="checkbox">
                  <i></i><?php echo ObtenEtiqueta(1997); ?>
                </label>
                <label class="checkbox">
                  <input id="pause_course" name="pause_course" type="checkbox">
                  <i></i><?php echo ObtenEtiqueta(1998); ?>
                </label>
              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-md-12 pull-right">
            <button class="btn btn-sm btn-default" data-dismiss="modal">
              <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?>
            </button>
            <!-- actions_ADD (Active Desactive Delete) -->
            <a class='btn btn-sm btn-danger disabled txt-color-white' id='actions_ADD'>
              <i class='fa fa-stop'></i> <?php echo ObtenEtiqueta(1903); ?>
            </a>
            <a class='btn btn-sm btn-warning disabled txt-color-white' id='action_pause_course'>
              <i class='fa fa-pause'></i> <?php echo ObtenEtiqueta(1904); ?>
            </a>
          </div>
        </div>
        <script>
          $(document).ready(function() {
            // activa el boton de return
            $("#return_course").click(function() {
              var val = $(this).prop('checked');
              if (val)
                $("#actions_ADD").removeClass("disabled");
              else
                $("#actions_ADD").addClass("disabled");
            });
            // Activa el boton de pause
            $("#pause_course").click(function() {
              var val = $(this).prop('checked');
              if (val)
                $("#action_pause_course").removeClass("disabled");
              else
                $("#action_pause_course").addClass("disabled");
            });
          })
        </script>
      <?php
      } else {
        SeleccioneUsuarios();
      }
    }

    if ($fl_action == DELETE) {
      if (!empty($seleccionados)) {
      ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-user-times  "></i> <?php echo $ds_titulo . " &nbsp;" . date('M d, Y'); ?></h4>
        </div>
        <div class="modal-body">
          <div class="row padding-10">
            <div class="col-md-2 text-align-center"><i style="<?php echo $fa_style; ?>" class="fa fa-user-times"></i>
            </div>
            <div class="col-md-10 ">
              <form action="mik.php" method="post" id="contact-form" class="smart-form">
                <div class="row" style="font-weight: 100; font-size:medium;">
                  <?php echo ObtenEtiqueta(1810); ?>
                </div>
                <br />
                <div class="row">
                  <label class="checkbox">
                    <input id="delete_1" name="delete_1" type="checkbox">
                    <i></i> <?php echo ObtenEtiqueta(1811); ?>
                  </label>
                  <label class="checkbox">
                    <input id="delete_2" name="delete_2" type="checkbox">
                    <i></i> <?php echo ObtenEtiqueta(1812); ?>
                  </label>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-md-12 pull-right">
            <button class="btn btn-sm btn-default" data-dismiss="modal">
              <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?>
            </button>
            <!-- actions_ADD (Active Desactive Delete) -->
            <a class='btn btn-sm btn-success disabled txt-color-white' id='actions_ADD'>
              <i class='fa fa-check-circle'></i> <?php echo ObtenEtiqueta(1534); ?>
            </a>
          </div>
        </div>
        <script>
          $(document).ready(function() {
            // activa el boton para realizar la accion de elimar usuarios del sistema
            $("#delete_1").click(function() {
              var val = $(this).prop('checked');
              var val_2 = $("#delete_2").prop('checked');
              if (val && val_2)
                $("#actions_ADD").removeClass("disabled");
              else
                $("#actions_ADD").addClass("disabled");
            });
            $("#delete_2").click(function() {
              var val = $(this).prop('checked');
              var val_1 = $("#delete_1").prop('checked');
              if (val && val_1)
                $("#actions_ADD").removeClass("disabled");
              else
                $("#actions_ADD").addClass("disabled");
            });
          });
        </script>
      <?php
      } else {
        SeleccioneUsuarios();
      }
    }

    # Accion para modificar si el usuario se asigna solo al curso o no
    if ($fl_action == ASSIGN_MYSELF) {
      $fg_assign_myself_course = RecibeParametroBinario('fg_assign_myself_course');
      $myself = "";
      if (!empty($fg_assign_myself_course) && !empty($usuario))
        $myself = "checked";
      if ($seleccionados || $usuario) {
      ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle"></i> <?php echo ObtenEtiqueta(1814); ?></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 cil-md-12 col-lg-2 text-align-center"><i style="<?php echo $fa_style; ?>" class="fa fa-unlock-alt fa-5x"></i></div>
            <div class="col-sm-12 cil-md-12 col-lg-10">
              <form action="mik.php" method="post" id="contact-form" class="smart-form padding-10">
                <div class="row padding-bottom-10 h6">
                  <strong>
                    <?php echo ObtenEtiqueta(1817); ?>
                  </strong>
                </div>
                <label class="checkbox">
                  <input id="check_myself" name="check_myself" type="checkbox" <?php echo $myself; ?>>
                  <i></i> <?php echo ObtenEtiqueta(1818); ?>
                </label>
              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-md-12 pull-right">
            <button class="btn btn-sm btn-default" data-dismiss="modal">
              <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?>
            </button>
            <a class='btn btn-sm btn-success txt-color-white' id='btn_myself'>
              <i class='fa fa-check-circle'></i> <?php echo ObtenEtiqueta(1534); ?>
            </a>
          </div>
        </div>
        <script>
          $(document).ready(function() {
            // Si ya esta activado activael boton
            var check_myself = $("#check_myself");
            var check_myself_val = check_myself.prop('checked');
            // Accion del boton
            $('#btn_myself').click(function() {
              var action = $("#fl_action").val();
              var tot_reg = $("#tot_reg").val(),
                i = 1;
              var check = check_myself.is(':checked'),
                val_check;
              if (check)
                val_check = 1;
              else
                val_check = 0;

              if (<?php echo $usuario ?> === 0) {
                for (i; i <= tot_reg; i++) {
                  var reg = $("#ch_" + i).is(':checked');
                  var valor = $("#ch_" + i).val();
                  if (reg == true) {
                    var confirmado = $("#confirmado_" + i).val();
                    $.ajax({
                      type: "POST",
                      url: "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
                      data: 'fl_action=' + action + '&fl_usuario=' + valor + '&confirmado=' + confirmado + '&fg_assign_myself_course=' + val_check,
                      async: false,
                      success: function(html) {}
                    });
                  }
                }
              } else {
                var ch = check_myself.is(':checked');
                if (ch)
                  ch = 1;
                else
                  ch = 0;
                $.ajax({
                  type: "POST",
                  url: "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
                  data: 'fl_action=' + action + '&fl_usuario=' + <?php echo $usuario; ?> + '&confirmado=1&fg_assign_myself_course=' + ch,
                  async: false,
                  success: function(html) {}
                });
              }
              $('#Actions').modal('toggle');
              $('#tbl_users').DataTable().ajax.reload();
            });
          });
        </script>
      <?php
      } else {
        SeleccioneUsuarios();
      }
    }


    # Accion para modificar si el usuario se asigna solo al curso o no
    if (($fl_action == RESEND_EMAIL_INVITATION) || ($fl_action == CSF_RESEND_EMAIL_INVITACION)) {


      if (!empty($seleccionados)) {
      ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="H1"><i class="fa fa-envelope-o "></i> <?php echo ObtenEtiqueta(2311) . " &nbsp;"; ?></h4>
        </div>
        <div class="modal-body">
          <div class="row padding-10">
            <div class="col-md-4 text-align-center"><i style="<?php echo $fa_style; ?>" class="fa fa-envelope-o "></i>
            </div>
            <div class="col-md-4 text-center">
              <form action="mik.php" method="post" id="Form1" class="smart-form">
                <div class="row" style="font-weight: 100; font-size:medium;">
                  <?php echo ObtenEtiqueta(2312); ?>
                </div>
                <br />
                <div id="muestra_emails"> </div>

                <script>
                  var tot_reg = $("#tot_reg").val(),
                    i = 1;
                  var total = 0;
                  var valores = [];
                  for (i; i <= tot_reg; i++) {
                    var reg = $("#ch_" + i).is(':checked');
                    var valor = $("#ch_" + i).val();

                    if (reg == true) {
                      total++;
                      valores[i] = valor;

                    }

                  }


                  $.ajax({
                    type: 'POST',
                    url: 'site/muestra_email_resend.php',
                    data: 'valores=' + valores +
                      '&total=' + total,
                    async: true,
                    success: function(html) {
                      $('#muestra_emails').html(html);
                    }
                  });
                </script>





              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-md-12 pull-right">
            <button class="btn btn-sm btn-default" data-dismiss="modal">
              <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?>
            </button>
            <!-- actions_ADD (Active Desactive Delete) -->
            <a class='btn btn-sm btn-success  txt-color-white' id='btn_myself'>
              <i class='fa fa-check-circle'></i> <?php echo ObtenEtiqueta(1534); ?>
            </a>
          </div>
        </div>
        <script>
          //funcion que te permite dar pausas de tiempo entre scripts(acciones).
          function sleep(milliseconds) {
            var start = new Date().getTime();
            for (var i = 0; i < 1e7; i++) {
              if ((new Date().getTime() - start) > milliseconds) {
                break;
              }
            }
          }


          $(document).ready(function() {

            // Accion del boton
            $('#btn_myself').click(function() {
              var action = $("#fl_action").val();
              var tot_reg = $("#tot_reg").val(),
                i = 1;



              for (i; i <= tot_reg; i++) {
                var reg = $("#ch_" + i).is(':checked');
                var valor = $("#ch_" + i).val();
                if (reg == true) {
                  var confirmado = $("#confirmado_" + i).val();
                  $.ajax({
                    type: "POST",
                    url: "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
                    data: 'fl_action=' + action + '&fl_usuario=' + valor,
                    async: false,

                  }).done(function(result) {
                    var result = JSON.parse(result);
                    var status = result.status;
                    var ds_mensaje = result.ds_mensaje;
                    var ds_email = result.ds_email;

                    if (status == 1) {

                      $.smallBox({
                        title: ds_mensaje,
                        content: ds_email,
                        color: "#739e73",
                        timeout: 500,
                        icon: "fa fa-envelope-o"
                      });



                    }


                  });
                }

                // sleep(2000);

              }

              $('#Actions').modal('toggle');
              $('#tbl_users').DataTable().ajax.reload();



            });
          });
        </script>


      <?php } else {

        SeleccioneUsuarios();
      } ?>



    <?php
    }

    #Asignar/Actualizar Teacher.
    if ($fl_action == ASG_TEACHER) {

    ?>


      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-exclamation-triangle"></i> <?php echo ObtenEtiqueta(2327); ?></h4>
      </div>
      <div class="modal-body">

        <?php
        $Query = " SELECT CONCAT(ds_nombres,' ',ds_apaterno)AS nb_nombres, fl_usuario FROM c_usuario WHERE fl_perfil_sp=" . PFL_MAESTRO_SELF . " AND fl_instituto=$fl_instituto ";
        CampoSelectBD('fl_teacher', $Query, 'ALL', 'select2', True, 'style="width:100%"', '', 'Select Teacher', '0');

        ?>


      </div>


      <div class="modal-footer">
        <div class="col-md-12 pull-right">
          <button class="btn btn-sm btn-default" data-dismiss="modal">
            <i class="fa fa-times-circle"></i> <?php echo ObtenEtiqueta(1066); ?>
          </button>
          <!-- actions_ADD (Active Desactive Delete) -->
          <a class='btn btn-sm btn-success  txt-color-white' id='btn_asgteacher'>
            <i class='fa fa-check-circle'></i> <?php echo ObtenEtiqueta(1534); ?>
          </a>
        </div>
      </div>



      <script>
        $(document).ready(function() {

          // Accion del boton
          $('#btn_asgteacher').click(function() {
            var action = $("#fl_action").val();
            var tot_reg = $("#tot_reg").val(),
              i = 1;
            var fl_teacher = $("#fl_teacher").val();

            //alert(fl_teacher);


            for (i; i <= tot_reg; i++) {
              var reg = $("#ch_" + i).is(':checked');
              var valor = $("#ch_" + i).val();
              var fl_programa_std = $("#fl_programa_std_" + i).val();


              if (reg == true) {
                var confirmado = $("#confirmado_" + i).val();
                $.ajax({
                  type: "POST",
                  url: "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
                  data: 'fl_action=' + action + '&fl_usuario=' + valor + '&fl_teacher=' + fl_teacher + '&fl_programa_std=' + fl_programa_std,
                  async: false,

                }).done(function(result) {


                  var result = JSON.parse(result);
                  var status = result.status;
                  var ds_title = result.ds_title;
                  //var ds_email=result.ds_email;

                  if (status == 1) {

                    $.smallBox({
                      title: ds_title,
                      content: "&nbsp;",
                      color: "#739e73",
                      timeout: 500,
                      icon: "fa fa-graduation-cap"
                    });



                  }


                });
              }

              // sleep(2000);

            }

            $('#Actions').modal('toggle');
            $('#tbl_users').DataTable().ajax.reload();



          });
        });
      </script>




    <?php


    }







    # Perfiles y accion que esta realizando
    if ($fl_action == ADD_STD || $fl_action == IMP_STD)
      CampoOculto('fl_perfil_sp', PFL_ESTUDIANTE_SELF);
    if ($fl_action == ADD_MAE || $fl_action == IMP_MAE)
      CampoOculto('fl_perfil_sp', PFL_MAESTRO_SELF);
    CampoOculto('fl_action', $fl_action);
    # Si ya confirmo el usuario
    CampoOculto('confirmado', $confirmado);

    # Funcion para advertencia de seleccionar usuarios
    function SeleccioneUsuarios()
    {
      echo '
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-warning"></i> ' . ObtenEtiqueta(1890) . '</h4>
    </div>
    <div class="modal-body">
      <div class="row">
      <div class="col-md-2 text-align-center"><i style="font-size:95px; color:#e3e3e3;";  class="fa fa-warning fa-5x"></i>
        </div>
        <div class="col-md-8">
          <form action="mik.php" method="post" id="contact-form" class="smart-form">
            <fieldset class="text-align-center"><h1>' . str_uso_normal(ObtenEtiqueta(1068)) . '</h1></fieldset>
          </form>
        </div>            
      </div>
    </div>
    <div class="modal-footer">
      <div class="col-md-12 pull-right">
      <button class="btn btn-sm btn-default" data-dismiss="modal">
        <i class="fa fa-times-circle"></i> ' . ObtenEtiqueta(1066) . '
      </button>
      </div>
    </div>';
    }

    # Funcion para Informar de la cantidad de licencias disponibles en trials
    function Info_Lice_Trial($p_instituto, $p_usuario)
    {
      // $no_max_licencias_trial = ObtenConfiguracion(102);
      $fg_modo = ObtenPlanActualInstituto($p_instituto);
      # Obtenemos el numero de licencias
      $no_max_licencias_trial = ObtenNumLicenciasDisponibles($p_usuario);
      # Si esta en trail mostrara el limite de licencias que tiene por usar en el trial
      if (!$fg_modo) {
         #2020 -sep  verificamos que el instituto no sea b2c.
          $Query="SELECT fg_b2c,no_tot_licencias_b2c FROM c_instituto WHERE fl_instituto=$p_instituto ";
         $row=RecuperaValor($Query);
         $fg_b2c=$row[0];
         
         if($fg_b2c==1){
             $no_max_licencias_trial = $row['no_tot_licencias_b2c'];
         }else{
             $no_max_licencias_trial = ObtenConfiguracion(102); 
         }


        $no_licencias_disponibles = Licencias_disponibles_Trial($p_instituto);
        $users_x_inst = ObtenNumeroUserInst($p_instituto);
      } else {
        $no_max_licencias_trial =  ObtenNumLicencias($p_instituto);
        $no_licencias_disponibles =  ObtenNumLicenciasDisponibles($p_instituto);
        $users_x_inst = ObtenNumLicenciasUsadas($p_instituto);
      }

      if ($no_licencias_disponibles == 0) {
        $fa_alert = "alert-danger";
        $fa_icono = "fa-times";
        $msj_alert = "you haven´t available licenses";
        $msj_alert = "<strong></strong> You have <strong>" . $licencias . "</strong> available licenses of <strong>" . $no_max_licencias_trial . "</strong>";
      } else {
        $licencias = round($no_max_licencias_trial / 3);
        // if($no_licencias_disponibles<=$licencias){
        if ($no_licencias_disponibles >= $licencias) {
          $fa_alert = "alert-success";
          $fa_icono = "fa-check";
          $msj_alert = "<strong></strong> You have <strong>" . $users_x_inst . "</strong>  used licenses of <strong>" . $no_max_licencias_trial . "</strong>";
        } else {
          $fa_alert = "alert-warning";
          $fa_icono = "fa-warning";
          $msj_alert = "<strong></strong> You have <strong>" . $no_licencias_disponibles . "</strong> available licenses of <strong>" . $no_max_licencias_trial . "</strong>";
        }
      }
      echo "
    
      <div class='col col-md-12 col-sm-12 col-lg-12'>
        <div class='alert " . $fa_alert . " fade in '>
          <i class='fa-fw fa " . $fa_icono . "'></i>
          " . $msj_alert . " " . $btn_disable . "
        </div>
      </div>";
    }

    function Licencias_Cero($style)
    {
      echo
        '<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridModalLabel"><i class="fa fa-thumbs-o-down"></i>&nbsp;' . ObtenEtiqueta(2025) . '</h4>
      </div>
      <div class="modal-body">
        <div class="row">     
        <div class="col col-sm-12 col-md-12 col-lg-2 text-align-center" style="padding-top:5%">
          <i style="' . $style . '" class="fa fa-warning fa-5x" aria-hidden="true"></i>
        </div>
        <div class="col col-md-12 col-sm-12 col-lg-10 text-align-center">        
          <h1 class="error-text tada animated" style="font-size:70px;">' . ObtenEtiqueta(2025) . '</h1>        
        </div>    
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar_modal"><i class="fa fa-times-circle"></i>&nbsp;' . ObtenEtiqueta(1066) . '</button>
        
      </div>';
    }
    ?>
  </div>
</div>
<!--</div>-->
<div class="bd-example bd-example-padded-bottom">
  <button type="button" class="btn btn-primary btn-lg hidden" id="abrir_modal" data-toggle="modal" data-target="#gridSystemModal">
    Launch demo modal
  </button>
</div>

<div id="send_correo" name="send_correo"></div>

<script>
  $('#actions_ADD').click(function() {
    action_ADD();
  });
  $('#action_asg_grp').click(function() {
    var valor = $("#fl_gruposp").val();
    var fl_programa_sp = $("#fl_progra").val();
    var fl_usu_pro = $("#fl_usu_progra").val();
    var next = true;
    if (valor == "ADDGRP") {
      var new_group = $("#new_group").val();
      if (new_group == "") {
        next = false;
        $("#input_new_group").addClass("input input-file state-error");
      }
    }
    // Asignara el grupo
    if (next)
      Asig_GRP('<?php echo $usuario; ?>', valor, fl_programa_sp, 0, fl_usu_pro);
  });
  $('#action_asg_course').click(function() {
    var valor = $("#fl_programasp").val();
    var fl_playlist = $("#fl_play_list").val();
    Asig_GRP('<?php echo $usuario; ?>', valor, fl_playlist);
  });
  $('#action_pause_course').click(function() {
    Pause_Course();
  });
  // Vamos a verificar que usuarios han cursado el programa selecionado
  // Esta accion solo se mostrara cuando sea multiple
  $('#fl_programasp').change(function() {
    var multiple = '<?php echo $seleccionados; ?>';
    if (multiple > 0)
      Verifica_usr_pro($(this).val());
  });
  // Muestra los usuarios no confirmados
  $("#inf_no_confirmados").click(function() {
    var aux = $('#reg_no_confirmados').is(":visible");
    if (aux == true)
      $('#reg_no_confirmados').addClass("hidden");
    else
      $('#reg_no_confirmados').removeClass("hidden");
  });
  // Muestra los usuarios confirmados que ya tiene el programa selecionado
  $("#inf_confirmados").click(function() {
    var aux = $('#reg_confirmados').is(":visible");
    if (aux == true)
      $('#reg_confirmados').addClass("hidden");
    else
      $('#reg_confirmados').removeClass("hidden");
  });
  document.getElementById('abrir_modal').click(); //clic automatico que se ejuta y sale modal

  // teacher podran asignar calificacion
  $('#btn_asig_grade_tec').click(function() {
    Asig_Grade_Tec();
  });
  $('#btn_asig_grade_tec_0').click(function() {
    Asig_Grade_Tec(false);
  });
</script>



<script>
  $(document).ready(function() {
    $("#email").change(function() {
      HabilitaBotonSend();



    });

    $('#ds_fname').change(function() {
      HabilitaBotonSend();

      var fname = document.getElementById('ds_fname').value;
      if (fname.length > 0) {
        //se agrega la clase al label
        $("#name").removeClass('state-error');
        $("#name").addClass('state-success');


      } else {
        //se agrega la clase al label
        $("#name").removeClass('state-success');
        $("#name").addClass('state-error');
      }


    });

    $('#ds_lname').change(function() {
      HabilitaBotonSend();
      var lname = document.getElementById('ds_lname').value;
      if (lname.length > 0) {
        $("#apellido").removeClass('state-error');
        //se agrega la clase al label
        $("#apellido").addClass('state-success');
      } else {
        //se agrega la clase al label
        $("#apellido").removeClass('state-success');
        $("#apellido").addClass('state-error');
      }
    });

    // Validamos el campo del archivo
    $("#fl_archivo").change(function() {
      var file = $(this).val();
      var ext = file.split('.').pop().toLowerCase();
      // Si el archivo es correcto podra importar
      if (ext == "csv") {
        // Activamos el boton
        $("#envio_boton").removeClass('disabled');
        // En caso de hay existido un error eliminara la clase del error
        $("#div_file").removeClass("state-error");
        $("#mgs_file_err").removeClass("has-error");
        $(".fa").removeClass("fa-times");
        // Cambiara el alert por un succes
        $(".alert").removeClass("alert-info");
        $(".alert").addClass("alert-success fade in");
        // cambiara el icono del 
        $(".fa").removeClass("fa-info");
        $(".fa").addClass("fa-check");
        // Mostramos barra de proceso
        $('#barra').removeClass('hidden');
      } else {
        $("#div_file").addClass("state-error");
        $("#mgs_file_err").addClass("has-error");
        $("#envio_boton").addClass('disabled');
        $(".alert").removeClass("alert-info fade in");
        $(".alert").removeClass("alert-success fade in");
        $(".alert").addClass("alert-danger fade in");
        // cambiara el icono
        $(".fa").removeClass("fa-check");
        $(".fa").removeClass("fa-info");
        $(".fa").addClass("fa-times");
        // Ocultamos barra de proceso
        $('#barra').addClass('hidden');
        $('#inf_invitaciones').addClass('hidden');
      }
    });

    // Validamos  que seleciono un grupo
    // Debe selecionar un grupo
    loadScript("<?php echo PATH_SELF_JS; ?>/plugin/select2/select2.min.js", function() {
      $("#fl_gruposp").select2({
        dropdownParent: $("#Acciones")
      });
      $("#fl_gruposp").change(function() {
        var valor = $(this).val();
        if (valor == 0) {
          $("#action_asg_grp").addClass('disabled');
          $("#sec_add_group").addClass("hidden");
        } else {
          $("#action_asg_grp").removeClass('disabled');
          if (valor == "ADDGRP")
            $("#sec_add_group").removeClass("hidden");
          else
            $("#sec_add_group").addClass("hidden");
        }
      });
      // recargar plugin 
      $("#fl_programasp").select2({
        dropdownParent: $("#Acciones")
      });

      // recargar plugin 
      $("#fl_play_list").select2({
        dropdownParent: $("#Acciones")
      });

      // recargar plugin 
      $("#fl_teacher").select2({
        dropdownParent: $("#Acciones")
      });


      // Activamos el boton
      $("#fl_programasp").change(function() {

        var valor = $(this).val();

        if (valor == 0) {
          $("#action_asg_course").addClass('disabled');
        } else {
          $("#action_asg_course").removeClass('disabled');
        }

        //reseteamos el combo del play list en cero
        $("#fl_play_list").select2("val", "0");
        //ocultamos el div que muestra los totales asinados.
        $('#tot_reg_seleccionados').removeClass('hidden');
        $('#inf_no_confirmados').removeClass('hidden');
        $('#inf_confirmados').removeClass('hidden');

      });


      $("#fl_play_list").change(function() {

        var valor = $(this).val();
        if (valor == 0) {
          $("#action_asg_course").addClass('disabled');
        } else {
          $("#action_asg_course").removeClass('disabled');
        }

        //reseteamos el combo del fl_programasp  en cero
        $("#fl_programasp").select2("val", "0");
        //ocultamos el div que muestra los totales asinados.
        $('#tot_reg_seleccionados').addClass('hidden');
        $('#inf_no_confirmados').addClass('hidden');
        $('#inf_confirmados').addClass('hidden');

      });

    });
  });
</script>

<script>
  function ProcessInfo() {

    var fl_action = $("#fl_action").val();
    if (fl_action == '<?php echo IMP_STD; ?>' || fl_action == '<?php echo IMP_MAE; ?>') {
      var datos = new FormData();
      datos.append('fl_archivo', $('#fl_archivo')[0].files[0]);
      datos.append('fl_action', fl_action);
      $.ajax({
        type: "post",
        url: '<?php echo PATH_SELF; ?>/div/func_envio_correo.php',
        contentType: false, // se envie multipart
        data: datos,
        processData: false, // poque vamos enviar un archivo
      }).done(function(html) {
        $('#send_correo').html(html);
        // $('#Actions').modal('toggle');
        $('#tbl_users').DataTable().ajax.reload();
      });

    }
    if (fl_action == '<?php echo ADD_STD ?>' || fl_action == '<?php echo ADD_MAE; ?>') {
      var email = document.getElementById('email').value;
      var fname = document.getElementById('ds_fname').value;
      var lname = document.getElementById('ds_lname').value;

      $.ajax({
        type: 'POST',
        url: '<?php echo PATH_SELF; ?>/div/func_envio_correo.php',
        data: 'email=' + email +
          '&fname=' + fname +
          '&lname=' + lname +
          '&fl_action=' + fl_action,
        async: true,
        success: function(html) {
          $('#send_correo').html(html);
          // $('#Actions').modal('toggle');
          $('#tbl_users').DataTable().ajax.reload();
        }
      });
    }



  }

  function EnviaInvitacionUserExistenteOtroInstituto(fl_action, fl_usuario) {

    var email = document.getElementById('email').value;
    var fname = document.getElementById('ds_fname').value;
    var lname = document.getElementById('ds_lname').value;
    var fg_user_ya_existente = 1;

    $.ajax({
      type: 'POST',
      url: '<?php echo PATH_SELF; ?>/div/func_envio_correo.php',
      data: 'email=' + email +
        '&fname=' + fname +
        '&lname=' + lname +
        '&fl_usuario_existente=' + fl_usuario +
        '&fg_user_ya_existente=' + fg_user_ya_existente +
        '&fl_action=' + fl_action,
      async: true,
      success: function(html) {
        $('#send_correo').html(html);
        // $('#Actions').modal('toggle');
        $('#tbl_users').DataTable().ajax.reload();
      }
    });

  }

  function HabilitaBotonSend() {

    var email = document.getElementById('email').value;
    var fname = document.getElementById('ds_fname').value;
    var lname = document.getElementById('ds_lname').value;




    if (email.length == "") {
      document.getElementById('email').focus();
      return;
    } else if (email.length > 0) {
      expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

      if (!expr.test(email)) {
        var valor = 1;
        $("#emails").removeClass('state-success');
        $("#emails").addClass('state-error');
      } else {
        var valor = 2;
        $("#emails").removeClass('state-error');
        $("#emails").addClass('state-success');
        // document.getElementById("email").focus();

      }

      if ((fname.length > 0) && (lname.length > 0) && (valor != 1)) {


        $("#envio_boton").removeClass('disabled'); //muestra el  boton

        return;

      } else {



      }


    } else if (fname.length == "") {
      $("#envio_boton").addClass('disabled'); //se esconde el  boton
      document.getElementById("fname").focus();
      return;

    } else if (lname.length == "") {
      $("#envio_boton").addClass('disabled'); //se esconde el primer boton
      document.getElementById("lname").focus();
      return;


    } else {

      $("#envio_boton").removeClass('disabled'); //muestra el segundo boton

    }

  }

  pageSetUp();
</script>



<?php

# Campo SelectBDEsclusivo para PlaylIst ya que genera otras consultas internas.
function CampoSelectBDPlay(
  $p_nombre,
  $p_query,
  $p_actual,
  $p_clase = 'select2',
  $p_seleccionar = False,
  $p_script = '',
  $p_valores = '',
  $p_seleccionar_txt = 'Select',
  $p_seleccionar_val = 0,
  $p_option_extra = ""
) {
  echo "<select id='$p_nombre' name='$p_nombre' class='" . $p_clase . "'";
  if (!empty($p_script)) echo " $p_script";
  echo ">\n";
  if ($p_seleccionar)
    echo "<option value=" . $p_seleccionar_val . " data-id='" . $p_seleccionar_val . "'>" . $p_seleccionar_txt . "</option>\n";
  $rs = EjecutaQuery($p_query);
  while ($row = RecuperaRegistro($rs)) {
    echo "<option value=\"$row[1]\"";
    if ($p_actual == $row[1])
      echo " selected";

    $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $row[1] ");

    #Recuperamos el usuario:
    $Que = "SELECT U.ds_nombres,U.ds_apaterno FROM c_playlist p
                                                 JOIN c_usuario U on U.fl_usuario=p.fl_usuario 
                                                 WHERE fl_playlist=" . $row[1];
    $ro = RecuperaValor($Que);
    $nb_teacher = str_texto($ro[0]) . " " . str_texto($ro[1]);


    # Determina si se debe elegir un valor por traduccion
    $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
    $etq_teacher = "Created by teacher";
    echo " data-fulltext='" . $row[2] . "'>$etq_campo ({$count_playlist[0]} courses) $etq_teacher {$nb_teacher}</option>\n";
  }
  if (!empty($p_option_extra))
    echo $p_option_extra;
  echo "</select>";
  # Si el select es multiple recibimos diferentes valores
  if (!empty($p_valores)) {
    echo "    
                      <script>
                      $(document).ready(function(){
                        $(\".select2\").val([";
    for ($k = 0; $k < count($p_valores); $k++) {
      echo "\"$p_valores[$k]\",";
    }
    echo "
                      ]).select2();
                      });
                      </script>";
  }
}

?>
