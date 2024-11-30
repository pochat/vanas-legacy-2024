<?php
# Libreria de funciones 
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False, 0, True);

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermisoSelf(FUNC_SELF)) {
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}

# Obtenemo el instituto
$fl_instituto = ObtenInstituto($fl_usuario);

# Retrieve the privacy information from the institute administrator
$institutePermits = RecuperaValor("SELECT fg_gender, fg_grade, fg_educational, fg_international, fg_blocking, fg_ferpa, fg_addStudents, fg_addTeachers, fg_deletions FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto");
$fg_addStudents=$institutePermits['fg_addStudents'];
$fg_addTeachers=$institutePermits['fg_addTeachers'];
$fg_deletions=$institutePermits['fg_deletions'];

# Retrieve the rector institute
$rector_institute=RecuperaValor("SELECT fl_instituto_rector FROM c_instituto WHERE fl_instituto=$fl_instituto");

# Check if the rector exists
$re_exist= !empty($rector_institute[0])?true:false;

if ($re_exist==true) {
    # Get permits from Rector
    $rectorPermits = RecuperaValor("SELECT fg_gender, fg_grade, fg_educational, fg_international, fg_blocking, fg_ferpa, fg_addStudents, fg_addTeachers, fg_deletions FROM k_instituto_filtro WHERE fl_instituto=".$rector_institute[0]);
    $re_addStudents=$rectorPermits['fg_addStudents'];
    $re_addTeachers=$rectorPermits['fg_addTeachers'];
    $re_deletions=$rectorPermits['fg_deletions'];
  } else {
    $re_addStudents=0;
    $re_addTeachers=0;
    $re_deletions=0;
  }

# Obtenemos el perfil
$fl_perfil = ObtenPerfilUsuario($fl_usuario);

# Check if a user is selected to see its detail information
$selected = (isset($_GET['selected'])?$_GET['selected']:0);
if ($selected != 0) {
  $allTime = 'selected';
  $ajaxUrl = "Querys/students2.php?selected=" . $selected;
  $Query = "SELECT fl_usuario, ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario = ".decriptClave($selected)." LIMIT 1";
  $studentData = RecuperaValor($Query);
  $studentName = "<a href='index.php#site/students.php'>".ObtenEtiqueta(1921)." > </a> ".$studentData['ds_nombres']." ".$studentData['ds_apaterno'];
  $visible = true;
} else {
  $allTime = '';
  $ajaxUrl = "Querys/students2.php";
  $studentName = "<a href='index.php#site/students.php'>".ObtenEtiqueta(1921)."</a>";
  $visible = false;
}

?>

<style>
  div.dataTables_filter label {
    float: left !important;
  }
</style>

<!-- LISTADO PARA LOS USUARIOS DEL ADMINISTRADOR ES DECIR TEACHERS Y STUDENTS2 -->
<div class="row" style="padding:5px;">
  <div class="row">
    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-3'>
      <div class='form-group'>
        <select style="width:100%" class="select2 select2-offscreen" tabindex="-1" title="" id="fl_programa_sp" name="fl_programa_sp">
          <optgroup label=''>
            <option value='ALL'><?php echo ObtenEtiqueta(1069); ?></option>
          </optgroup>
          <!-- Porgramas asignados -->
          <optgroup label="<?php echo ObtenEtiqueta(1825); ?>">
            <?php
            /*$Query1  = "SELECT nb_programa, CONCAT('ACR_','',fl_programa_sp), fl_programa_sp FROM c_programa_sp a ";
          $Query1 .= "WHERE EXISTS (SELECT 1 FROM k_usuario_programa b WHERE b.fl_programa_sp=a.fl_programa_sp AND fl_maestro=$fl_usuario)";*/
            $Query1  = "SELECT c.nb_programa".$sufix.", CONCAT('ACR_','',a.fl_programa_sp), a.fl_programa_sp FROM k_usuario_programa a, c_usuario b, c_programa_sp c ";
            $Query1 .= "WHERE a.fl_usuario_sp=b.fl_usuario AND a.fl_programa_sp=c.fl_programa_sp AND a.fl_maestro=$fl_usuario AND fl_instituto='$fl_instituto' ";
            $Query1 .= "GROUP BY a.fl_programa_sp ORDER BY nb_programa".$sufix;
            $rs1 = EjecutaQuery($Query1);
            for ($i = 0; $row1 = RecuperaRegistro($rs1); $i++) {
              //$nb_programa1 = $row1[0];
              $nb_programa1 = htmlentities($row1[0], ENT_QUOTES, "UTF-8");
              $seach1 = $row1[1];
              $fl_programa_sp1 = $row1[2];
              # Buscamos el numero de alumnos en cada programa
              $row11 = RecuperaValor("SELECT COUNT(*)FROM k_usuario_programa a, c_usuario b WHERE a.fl_usuario_sp=b.fl_usuario AND fl_programa_sp=$fl_programa_sp1 AND fl_maestro=$fl_usuario");
              echo "<option value='".$nb_programa1."'>".$nb_programa1." (".$row11[0].")</option>";
            }
            ?>
          </optgroup>
          <!-- programas no asignados -->
          <optgroup label="<?php echo ObtenEtiqueta(1826); ?>">
            <?php
            $Query2  = "SELECT nb_programa".$sufix.", CONCAT('ACR_','',fl_programa_sp) FROM c_programa_sp a ";
            $Query2 .= "WHERE not EXISTS (SELECT 1 FROM k_usuario_programa b WHERE b.fl_programa_sp=a.fl_programa_sp AND fl_maestro=$fl_usuario)";
            $rs2 = EjecutaQuery($Query2);
            for ($j = 0; $row2 = RecuperaRegistro($rs2); $j++) {
              //$nb_programa2 = $row2[0];
              $nb_programa2 = htmlentities($row2[0], ENT_QUOTES, "UTF-8");
              $seach2 = $row2[1];
              echo "<option value='".$seach2."'>".$nb_programa2." (0)</option>";
            }
            ?>
          </optgroup>
          <?php
          # buscamos los usuarios de la escuela que no han sido asigados
          $Queryun  = "SELECT COUNT(*) FROM c_usuario a ";
          $Queryun .= "WHERE a.fl_instituto=$fl_instituto AND a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." ";
          $Queryun .= "AND NOT EXISTS(SELECT 1 FROM k_usuario_programa b WHERE a.fl_usuario = b.fl_usuario_sp) ";
          $rowun = RecuperaValor($Queryun);
          $Querynoc  = "SELECT COUNT(*) FROM k_envio_email_reg_selfp a WHERE a.fl_invitado_por_instituto=$fl_instituto ";
          $Querynoc .= "AND fl_usu_invita=$fl_usuario AND fg_confirmado='0' AND a.fg_tipo_registro='S' ";
          $Querynoc .= "AND NOT EXISTS(SELECT 1 FROM k_noconfirmados_pro b WHERE b.fl_envio_correo = a.fl_envio_correo) ";
          $rowncon = RecuperaValor($Querynoc);
          $no_asignados = $rowun[0] + $rowncon[0];
          ?>
          <optgroup label='<?php echo ObtenEtiqueta(1920); ?>'>
            <option value='<?php echo ObtenEtiqueta(1039); ?>'><?php echo ObtenEtiqueta(1920)." (" . $no_asignados . ")"; ?></option>
          </optgroup>
        </select>
      </div>
      <div class='form-group'>
        <?php
        // Filter by progress
        $opt_prog = array(ObtenEtiqueta(2564), '100% ('.ObtenEtiqueta(2565).')', '0% ('.ObtenEtiqueta(2566).')', ObtenEtiqueta(2567));
        $val_prog = array('', 'hundred', 'zero', 'in_the_middle');
        Camposelect('fl_progress', $opt_prog, $val_prog, 2, 'select2');
        ?>
      </div>
    </div>
    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-3'>
      <div class='form-group'>
        <?php
        # Por defaul ocultamos este filtro        
        $opt_status = array(ObtenEtiqueta(1040), ObtenEtiqueta(1041), ObtenEtiqueta(1042));
        $val_status = array('ALL', 1, 0);
        CampoSelect('fl_status', $opt_status, $val_status, 2, 'select2');
        ?>
      </div>
      <div class='form-group'>
        <?php
        $option_extra = "<option value='".ObtenEtiqueta(1039)."'>".ObtenEtiqueta(1039)."</option>";
        /*$Query  = "SELECT al.nb_grupo, al.nb_grupo FROM c_alumno_sp al, c_usuario usr ";
        $Query .= "WHERE al.fl_alumno_sp = usr.fl_usuario AND usr.fl_instituto='$fl_instituto' AND nb_grupo<>'' GROUP BY nb_grupo ";*/
        $Query  = "SELECT nb_grupo, grupo2 FROM ( ";
        $Query .= "(SELECT al.nb_grupo, al.nb_grupo grupo2 FROM c_grupo_fame al, c_usuario usr ";
        $Query .= "WHERE al.fl_alumno_sp = usr.fl_usuario AND usr.fl_instituto='$fl_instituto' AND nb_grupo<>'' GROUP BY nb_grupo) ";
        $Query .= "UNION ";
        $Query .= "(SELECT a.nb_grupo, a.nb_grupo grupo2 ";
        $Query .= "FROM k_envio_email_reg_selfp a WHERE a.fl_invitado_por_instituto='$fl_instituto' AND a.nb_grupo<>'' AND fg_enviado='1' AND fg_confirmado='0' GROUP BY nb_grupo) ";
        $Query .= ") as main GROUP BY nb_grupo ";
        CampoSelectBD('fl_grupo_sp', $Query, 'ALL', 'select2', True, '', '', ObtenEtiqueta(1070), 'ALL', $option_extra);
        ?>
      </div>
    </div>
    <div class='col-xs-12 col-sm-7 col-md-7 col-lg-2'>
    </div>
    <?php
    echo PresentaContentTopAdm($fl_usuario, "col-xs-12 col-sm-5 col-md-5 col-lg-4");
    ?>
  </div>

  <?php
  #Si el instituto tiene el csf se mostrara menu.
  $Query = "SELECT fg_scf FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $rowf = RecuperaValor($Query);

  if ($rowf['fg_scf'] == 1) {
    $EnvioInvitacionSCF = 44;
    $etq_envio_invitation_csf = ObtenEtiqueta(2587);
    $desc_etq_envio_invitacion_csf = ObtenEtiqueta(2588);
  } else {
    $EnvioInvitacionSCF = "";
    $etq_envio_invitation_csf = "";
    $desc_etq_envio_invitacion_csf = "";
  }
  
  SectionIni();
  # Valores para el boton de actions
  $opt_btn = array();
  $desc_btn = array();
  $val_btn = array();

  # Show or Hide Add/Import Students
  if ($fl_perfil==25 || $fg_addStudents==1) {
    array_push($opt_btn, ObtenEtiqueta(1043), ObtenEtiqueta(1044));
    array_push($desc_btn, ObtenEtiqueta(1116), ObtenEtiqueta(1117));
    array_push($val_btn, ADD_STD, IMP_STD);
  } elseif ($fl_perfil==13 && $re_addStudents==1 && $fg_addStudents==0) {
    array_push($opt_btn, ObtenEtiqueta(1043), ObtenEtiqueta(1044));
    array_push($desc_btn, ObtenEtiqueta(1116), ObtenEtiqueta(1117));
    array_push($val_btn, ADD_STD, IMP_STD);
  } elseif ($fl_perfil==13 && $re_exist==false) {
    array_push($opt_btn, ObtenEtiqueta(1043), ObtenEtiqueta(1044));
    array_push($desc_btn, ObtenEtiqueta(1116), ObtenEtiqueta(1117));
    array_push($val_btn, ADD_STD, IMP_STD);
  }
  # Show or Hide Add/Import Teachers
  if ($fl_perfil == 25 || $fl_perfil == 13 || $fg_addTeachers==1) {
    array_push($opt_btn, ObtenEtiqueta(1045), ObtenEtiqueta(1046));
    array_push($desc_btn, ObtenEtiqueta(1118), ObtenEtiqueta(1119));
    array_push($val_btn, ADD_MAE, IMP_MAE);
  } elseif ($fl_perfil==13 && $re_addStudents==1 && $fg_addTeachers==0) {
    array_push($opt_btn, ObtenEtiqueta(1045), ObtenEtiqueta(1046));
    array_push($desc_btn, ObtenEtiqueta(1118), ObtenEtiqueta(1119));
    array_push($val_btn, ADD_MAE, IMP_MAE);
  } elseif ($fl_perfil==13 && $re_exist==false) {
    array_push($opt_btn, ObtenEtiqueta(1045), ObtenEtiqueta(1046));
    array_push($desc_btn, ObtenEtiqueta(1118), ObtenEtiqueta(1119));
    array_push($val_btn, ADD_MAE, IMP_MAE);
  }
  # This actions does not has profile conditions
  array_push($opt_btn, ObtenEtiqueta(1071), ObtenEtiqueta(1072), ObtenEtiqueta(1073),ObtenEtiqueta(1047), ObtenEtiqueta(1048));
  array_push($desc_btn, ObtenEtiqueta(1905), ObtenEtiqueta(1906), ObtenEtiqueta(1907),
    ObtenEtiqueta(1120), ObtenEtiqueta(1121));
  array_push($val_btn, ASG_GROUP, ASG_COURSE, DESASIGNAR_COURSE, ACTIVE, DESACTIVE);
  # Show or Hide Delete users
  if ($fl_perfil == 25 || $fl_perfil == 13 || $fg_deletions==1) {
    array_push($opt_btn, ObtenEtiqueta(1049));
    array_push($desc_btn, ObtenEtiqueta(1122));
    array_push($val_btn, DELETE);
  } elseif ($fl_perfil==13 && $re_addStudents==1 && $fg_deletions==0) {
    array_push($opt_btn, ObtenEtiqueta(1049));
    array_push($desc_btn, ObtenEtiqueta(1122));
    array_push($val_btn, DELETE);
  } elseif ($fl_perfil==13 && $re_exist==false) {
    array_push($opt_btn, ObtenEtiqueta(1049));
    array_push($desc_btn, ObtenEtiqueta(1122));
    array_push($val_btn, DELETE);
  }
  # This actions does not has profile conditions
  array_push($opt_btn, ObtenEtiqueta(1908), ObtenEtiqueta(1815), ObtenEtiqueta(2309), ObtenEtiqueta(2327), $etq_envio_invitation_csf);
  array_push($desc_btn, ObtenEtiqueta(1909), ObtenEtiqueta(1816), ObtenEtiqueta(2310), ObtenEtiqueta(2328), $desc_etq_envio_invitacion_csf);
  array_push($val_btn, ASSESSMENT, ASSIGN_MYSELF, RESEND_EMAIL_INVITATION, ASG_TEACHER, $EnvioInvitacionSCF);

  #####################################################################
  // Bellow commented for the use of the new privacy settings above
  // $opt_btn = array(ObtenEtiqueta(1043), ObtenEtiqueta(1044), ObtenEtiqueta(1045), ObtenEtiqueta(1046), ObtenEtiqueta(1071), ObtenEtiqueta(1072), ObtenEtiqueta(1073), ObtenEtiqueta(1047), ObtenEtiqueta(1048), ObtenEtiqueta(1049), ObtenEtiqueta(1908), ObtenEtiqueta(1815), ObtenEtiqueta(2309), ObtenEtiqueta(2327), $etq_envio_invitation_csf);
  // $desc_btn = array(ObtenEtiqueta(1116), ObtenEtiqueta(1117), ObtenEtiqueta(1118), ObtenEtiqueta(1119), ObtenEtiqueta(1120), ObtenEtiqueta(1121), ObtenEtiqueta(1122),'', 'Assessment');
  // $desc_btn = array(ObtenEtiqueta(1116), ObtenEtiqueta(1117), ObtenEtiqueta(1118), ObtenEtiqueta(1119), ObtenEtiqueta(1905), ObtenEtiqueta(1906), ObtenEtiqueta(1907),ObtenEtiqueta(1120), ObtenEtiqueta(1121), ObtenEtiqueta(1122), ObtenEtiqueta(1909), ObtenEtiqueta(1816), ObtenEtiqueta(2310), ObtenEtiqueta(2328), $desc_etq_envio_invitacion_csf);
  // $val_btn = array(ADD_STD, IMP_STD, ADD_MAE, IMP_MAE, ASG_GROUP, ASG_COURSE, DESASIGNAR_COURSE, ACTIVE, DESACTIVE, DELETE, ASSESSMENT, ASSIGN_MYSELF, RESEND_EMAIL_INVITATION, ASG_TEACHER, $EnvioInvitacionSCF);
  #####################################################################
  ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "students2", "fa-table", $studentName, true, true, false, false, false, ObtenEtiqueta(1074), "default", $opt_btn, $val_btn, $desc_btn);
  # Muestra Inicio de la tabla
  $titulos = str_uso_normal(array(
    ObtenEtiqueta(1054), ObtenEtiqueta(1055), ObtenEtiqueta(1075), ObtenEtiqueta(1640), ObtenEtiqueta(1076),
    ObtenEtiqueta(1057), ObtenEtiqueta(1106), ObtenEtiqueta(1058), ObtenEtiqueta(1077), ObtenEtiqueta(1908), ObtenEtiqueta(1814), ObtenEtiqueta(1078)
  ));
  MuestraTablaIni("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos);

  # Muestra Fin de la tabla
  MuestraTablaFin(false);

  # Campos para el total de registros
  CampoOculto('tot_reg', !empty($tot_reg)?$tot_reg:NULL);

  # Muestra el modal para las acciones
  MuestraModal("Actions");
  ArticleFin();
  SectionFin();
?>
</div>

<style>
  .lds-ellipsis {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
  }
  .lds-ellipsis div {
    position: absolute;
    top: 33px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: #0171BD;
    animation-timing-function: cubic-bezier(0, 1, 1, 0);
  }
  .lds-ellipsis div:nth-child(1) {
    left: 8px;
    animation: lds-ellipsis1 0.6s infinite;
  }
  .lds-ellipsis div:nth-child(2) {
    left: 8px;
    animation: lds-ellipsis2 0.6s infinite;
  }
  .lds-ellipsis div:nth-child(3) {
    left: 32px;
    animation: lds-ellipsis2 0.6s infinite;
  }
  .lds-ellipsis div:nth-child(4) {
    left: 56px;
    animation: lds-ellipsis3 0.6s infinite;
  }
  @keyframes lds-ellipsis1 {
    0% {
      transform: scale(0);
    }
    100% {
      transform: scale(1);
    }
  }
  @keyframes lds-ellipsis3 {
    0% {
      transform: scale(1);
    }
    100% {
      transform: scale(0);
    }
  }
  @keyframes lds-ellipsis2 {
    0% {
      transform: translate(0, 0);
    }
    100% {
      transform: translate(24px, 0);
    }
  }
</style>

<script type="text/javascript">
  // Loader Object
  var loader='<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>';
  // Reset Button object
  var resetButton = '<a href="javascript:void(0);" id="btn_reset_filter" onclick="ResetFilter();" class="btn btn-default btn-xs" style="margin-left:5px;margin-bottom:5px;margin-top: 5px;"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp; Reset search</a>';
  // Drop Dowun Last # days select button object
  var daysSelect = '<select id="daySelect" class="form-control" onchange="reloadTableDays()" style="margin-left: 6px;" value="-8"><option value="-8">Last 7 days</option><option value="-31">Last 30 days</option><option value="-999999999999" <?php echo $allTime; ?>>All Time</option></select>';
  /* DO NOT REMOVE : GLOBAL FUNCTIONS!
   *
   * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
   *
   * // activate tooltips
   * $("[rel=tooltip]").tooltip();
   *
   * // activate popovers
   * $("[rel=popover]").popover();
   *
   * // activate popovers with hover states
   * $("[rel=popover-hover]").popover({ trigger: "hover" });
   *
   * // activate inline charts
   * runAllCharts();
   *
   * // setup widgets
   * setup_widgets_desktop();
   *
   * // run form elements
   * runAllForms();
   *
   ********************************
   *
   * pageSetUp() is needed whenever you load a page.
   * It initializes and checks for all basic elements of the page
   * and makes rendering easier.
   *
   */
  /* Debemos agregarlo para el fucnionamiento de diversos  plugins*/
  pageSetUp();

  /*
   * ALL PAGE RELATED SCRIPTS CAN GO BELOW HERE
   * eg alert("my home function");
   * 
   * var pagefunction = function() {
   *   ...
   * }
   * loadScript("js/plugin/_PLUGIN_NAME_.js", pagefunction);
   * 
   * TO LOAD A SCRIPT:
   * var pagefunction = function (){ 
   *  loadScript(".../plugin.js", run_after_loaded);  
   * }
   * 
   * OR you can load chain scripts by doing
   * 
   * loadScript(".../plugin.js", function(){
   *   loadScript("../plugin.js", function(){
   *     ...
   *   })
   * });
   */

  // pagefunction
  /** INICIO DE SCRIPT PARA DATATABLE **/
  var pagefunction = function() {
    // alert('ola');
    /* Formatting function for row details - modify as you need */
    function format(d) {
      // `d` is the original data object for the row
      return d;
    }

    // clears the variable if left blank
    // The next .on is not user animore
    // on('processing.dt', function(e, settings, processing) {
    //   $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'none');
    //   $("#vanas_loader").show();
    //   if (processing == false)
    //     $("vanas_loader").hide();
    // }).
    var table = $('#tbl_users').DataTable({
      ajax: {
          url: "<?php echo $ajaxUrl; ?>",
          type: "POST",
          data: {
              days: function() {
                  if (typeof $('#daySelect').val() !== 'undefined') {
                      return $('#daySelect').val()
                  }
                  if ('<?php echo $selected; ?>' != 0) {
                      return -999999999999
                  } else {
                      return -8
                  }
              }
          }
      },
      bDestroy: true,
      iDisplayLength: 10,
      scrollX: true,
      language:
              {
                loadingRecords: loader
              },
      columns: [
          {
              data: "checkbox",
              width: "15px",
              orderable: false
          },
          {
              data: "id",
              width: "15px",
              orderable: true
          },
          {
              data: "name",
          },
          {
              data: "grupo",
          },
          {
              data: "grade",
              className: "text-align-center",
          },
          {
              data: "programa",
              visible: "<?php echo $visible; ?>",
          },
          {
              data: "status",
              className: "text-align-center",
          },
          {
              data: "use_licence",
              className: "text-align-center",
          },
          {
              data: "lastactivity",
              orderable: true,
          },
          {
              data: "progress",
              orderable: true,
              visible: "<?php echo $visible; ?>"
          },
          {
              data: "assessment",
              className: "text-align-center",
              visible: "<?php echo $visible; ?>"
          },
          {
              data: "myself",
              className: "text-align-center"
          },
          {
              data: "gpa",
              className: "text-align-center"
          },
          {
              data: "ACR",
              visible: false
          },
          {
              data: "AGR",
              visible: false
          },
          {
              data: "ACT",
              visible: false
          },
          {
              data: "activity",
              visible: false
          }
      ],
      order:
      [
          [1, 'asc'],
          [16, 'desc']
      ],
      "fnDrawCallback": function(oSettings) {
        // Inserts the buttons for "reset" and "Last # days" on draw
        buttonsInitialization();
        runAllCharts();
        /** Se tuiliza para el nombre de las imagenes **/
        $("[rel=tooltip]").tooltip();
        /** Total de registros **/
        var oSettings = this.fnSettings();
        var iTotalRecords = oSettings.fnRecordsTotal();
        /** Es necesario si vamos a selelecionar muchos registros en la tabla **/
        $("#tot_reg").val(iTotalRecords);
      }
    });

    // Add event listener for opening and closing details
    $('#tbl_users tbody').on('click', 'td.details-control', function() {
      var tr = $(this).closest('tr');
      var row = table.row(tr);

      if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
      } else {
        // Open this row
        row.child(format(row.data())).show();
        tr.addClass('shown');
      }
    });

    /** INICIO DE SELECIONAR TODOS ***/
    $('#sel_todo').on('change', function() {
      var v_sel_todo = $(this).is(':checked'),
        i;
      var iTotalRecords = $('#tot_reg').val();
      for (i = 1; i <= iTotalRecords; i++) {
        $("#ch_" + i).prop('checked', v_sel_todo);
      }
    })
    /** FIN DE SELECIONAR TODOS ***/

    /*** INICIO DE BUSQUEDA AVANZADA ***/
    /** OBTENEMOS EL VALOR DEL  TIPO DE STATUS A BUSCAR **/
    // Programas
    $("#fl_programa_sp").on('change', function() {
      var v = $(this).val();
      // busca en la columna del tupo        
      table.columns(13).search(v).draw();
    });
    // Usuarios activos o inactivos
    $("#fl_status").on('change', function() {
      var v = $(this).val();
      // busca en la columna del tupo        
      table.columns(15).search(v).draw();
    });
    // Progress
    $("#fl_progress").on('change', function() {
      var v = $(this).val();
      // busca en la columna del tupo        
      table.columns(9).search(v).draw();
    });
    // Programas
    $("#fl_grupo_sp").on('change', function() {
      var v = $(this).val();
      // busca en la columna del tupo        
      table.columns(3).search(v).draw();
    });

    /*** FIN DE BUSQUEDA AVANZADA ***/
  };

  /** FIN DE SCRIPT PARA DATATABLE **/
  // end pagefunction

  // load related plugins & run pagefunction
  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/
  /** IMPORTANTES Y NECESARIAS EN DONDE SE UTILIZEN **/
  loadScript("../fame/js/plugin/datatables/jquery.dataTables.min.js", function() {
    loadScript("../fame/js/plugin/datatables/dataTables.colVis.min.js", function() {
      loadScript("../fame/js/plugin/datatables/dataTables.tableTools.min.js", function() {
        loadScript("../fame/js/plugin/datatables/dataTables.bootstrap.min.js", function() {
          loadScript("../fame/js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
        });
      });
    });
  });

  /** FIN CARGA DE SCRIPT PARA LAS  DATATABLES **/
  function ResetFilter() {
    $('#tbl_users').DataTable().search('').draw(); //limpiar el restet
  }

  function DeleteGroup(fl_grupo_fame) {
    var answer = confirm('Delete group?');
    if (answer) {
      $.ajax({
        type: 'POST',
        url: 'site/borrar_grupos.php',
        data: 'fl_grupo_fame=' + fl_grupo_fame,
        async: false
      }).done(function(result2) {

      });
      $('#tbl_users').DataTable().ajax.reload();
    }
  }

//MJDDialogo que muestra la advertenceia de como utilizar el listado de studens
// $.smallBox({
//   title : '<?php #echo ObtenEtiqueta(2586); ?>',
//   content : '<?php #echo ObtenEtiqueta(2585);?>',
//   color : "#5384AF",
//   timeout: 20000,
//   icon : "fa fa-info-circle"
// });

// Inserts the reset button and the drop down menu for last # days
function buttonsInitialization(){
  /* Fitler Reset Button insertion */
  if (!$('#btn_reset_filter').length) {
    $('#tbl_users_filter').append(resetButton);
  }
  /* Drop down menu for last activity days filter insertion */
  if (!$('#daySelect').length) {
    $('#tbl_users_length').append(daysSelect);
  }
}
// Loader insert on table on processing
function loaderDisplay() {
  $('tbody').children().remove();
  $('tbody').html('<tr><td style="background-color: #fff; height: 80px;"><div class="lds-ellipsis" style="position: absolute; left: 44%;"><div></div><div></div><div></div><div></div></div></td></tr>');
}

function reloadTableDays() {
  $('#tbl_users').DataTable().ajax.reload(loaderDisplay());
}

</script>
