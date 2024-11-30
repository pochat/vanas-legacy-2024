<?php 
	# Libreria de funciones
	require("../../modules/common/lib/cam_general.inc.php");
	require("../lib/layout_self.php");
	require("../lib/self_func.php");
  
  // $fl_insituto = ObtenInstituto($fl_usuario);
  $fl_insituto = 0;
?>
<!-- Encabezado de users administrador -->
<div class="row">
  <button id="prueba" class="btn btn-primary">test</button>
  <!-- col -->
  <div class="col-xs-12 col-sm-7 col-md-7 col-lg-2 padding-5">
    <div class="form-group">
      <?php
      $options = array('All Users', 'Students','Teachers', 'Admin(s)', 'Unassigned');
      $valores = array('AU','S','T','AD','UN');
      CampoSelect('fl_users', $options, $valores, 'AU');
      ?>
    </div>
  </div>
  
  <div class="col-xs-12 col-sm-7 col-md-7 col-lg-2 padding-5">
    <?php
    # Por defaul ocultamos este filtro
    $opt_status = array('Status', 'Active','Inactive');
    $val_status = array(2,1,0);
    CampoSelect('fl_status', $opt_status, $val_status, 2, 'select2 hidden');
    ?>
  </div>
  <!-- end col -->
  
  <!-- right side of the page with the sparkline graphs -->
  <!-- col -->
  <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
    <!-- sparks -->
    <ul id="sparks">
      <li class="sparks-info">
        <h5> Total de Licencias: <?php echo $tot_licencias = ObtenNumLicencias($fl_usuario); ?></h5>
      </li>
      <li class="sparks-info">
        <h5> Users: <?php echo $no_usuarios = ObtenNumeroUserInst($fl_instituto); ?></h5>
      </li>
      <li class="sparks-info">
        <h5> Available: 
        <?php
        echo $avaible = $tot_licencias - $no_usuarios;
        ?>
        </h5>
      </li>
    </ul>
    <!-- end sparks -->
    <div class="text-align-right">
    <div>15.45 GB(51%) de 30 GB used</div>
    <div><a href="#">Manage</a></div>
    </div>
  </div>
  <!-- end col -->

</div>

<!--- Listado de todos los usarios (Maestros y Estudiantes)-->
<div class="row" style="padding:5px;">
  <?php
    SectionIni();
      # Valores para el boton de actions
      $opt_btn = array('Add Student', 'Import Student', 'Add Teacher', 'Import Teacher', 'Activate', 'Desactive', 'Delete');
      $val_btn = array(1,2,3,4,5,6,7);     
      ArticleIni("gabriel", "fa-table", "Hide/Show Columns", true, true, false, false, false, "Actions", "default", $opt_btn, $val_btn);
        # Muestra Inicio de la tabla
        $titulos = array("ID", "Name", "Type", "Status", "Last login", "Usage");
        MuestraTablaIni("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos);
        $Query  = "SELECT  CONCAT( a.ds_nombres,' ', a.ds_apaterno ), c.nb_perfil, ";
        $Query .= "CASE a.fg_activo WHEN 1 THEN 'Active' ELSE 'Inactive' END status, '2 days ago'  'last_login', '0.01 GB' 'usage' ";
        $Query .= "FROM c_usuario a,  c_perfil c WHERE a.fl_perfil = c.fl_perfil ";
        $rs = EjecutaQuery($Query);
        for($i=0;$row=RecuperaRegistro($rs);$i++){
          $ds_ruta_foto = "";
          $ds_nombre = $row[0];
          $nb_perfil = $row[1];
          $status = $row[2];
          if($status == "Active")
            $color = "success";
          else
            $color = "danger";
          $last_login = $row[3];
          $usage = $row[4];
          # Porel momento ponemos la imagen de algunos
          $ruta_foto = PATH_ALU_IMAGES."/avatars/ak9207150322_ava19970.jpg";
          echo "
          <tr>
            <td class='text-align-center'>
              <div class='project-members'>
                <a href='#' rel='tooltip' data-placement='top' data-html='true' data-original-title='".$ds_nombre."'>
                  <img src='".$ruta_foto."' class='online' alt='user' width='30px' height='30px'>
                </a>
              </div>
            </td>
            <td>".$ds_nombre."</td>
            <td>".$nb_perfil."</td>
            <td class='text-align-center'><span class='label label-".$color."'>".$status."</span></td>
            <td class='text-align-center'>".$last_login."</td>
            <td>".$usage."</td>
            <td>&nbsp;</td>
          </tr>";        
        }
        # Muestra Fin de la tabla
        MuestraTablaFin(true, "tbl_users");
      ArticleFin();
    SectionFin();
  ?>
</div>

<!-- PAGE RELATED PLUGIN(S) DATETABLES -->
<script src="<?php echo PATH_N_COM_JS; ?>/plugin/datatables/jquery.dataTables-cust.min.js"></script>
<script src="<?php echo PATH_N_COM_JS; ?>/plugin/datatables/DT_bootstrap.js"></script>

 <!-- PAGE RELATED PLUGIN(S) -->
  <!--<script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/jquery.dataTables.min.js"></script>-->
  <script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/dataTables.colVis.min.js"></script>
  <script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/dataTables.tableTools.min.js"></script>
  <script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatables/dataTables.bootstrap.min.js"></script>
  <script src="<?php echo PATH_HOME; ?>/bootstrap/js/plugin/datatable-responsive/datatables.responsive.min.js"></script>