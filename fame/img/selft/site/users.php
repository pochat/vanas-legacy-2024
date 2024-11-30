<?php 
	# Libreria de funciones
	// require("../../modules/common/lib/cam_general.inc.php");
	// require("../lib/layout_self.php");
	// require("../lib/self_func.php");
	require("../lib/self_general.php");
  
  // $fl_insituto = ObtenInstituto($fl_usuario);
  // include('/../lib/header.php');
  // include('/../lib/nav.php');
  // PresentaNav();
  $fl_usuario = 1;
  PresentaHeaderNav($fl_usuario);  
  # Obtenemos el perfil
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  if($fl_perfil==1)
    $fl_perfil=11;
?>
<div id="main" role="main">
  <?php
  PresentaRibbon();
  ?>
  <div id="content">
  <?php
  PresentaContentTop($fl_usuario);
  ?>
  <!--- Listado de todos los usarios (Maestros y Estudiantes)-->
  <div class="row" style="padding:5px;">
    <?php
      SectionIni();
        # Valores para el boton de actions
        $opt_btn = array('Add Student', 'Import Student', 'Add Teacher', 'Import Teacher', 'Activate', 'Desactive', 'Delete');
        $val_btn = array(1,2,3,4,5,6,7);
        ArticleIni("col-xs-12 col-sm-12 col-md-12 col-lg-12", "gabriel", "fa-table", "Hide/Show Columns", true, true, false, false, false, "Actions", "default", $opt_btn, $val_btn, $b);
          # Muestra Inicio de la tabla
          $titulos = array("ID", "Name", "Type", "Status", "Last login", "Usage");
          MuestraTablaIni("tbl_users", "display projects-table table table-striped table-bordered table-hover", "100%", $titulos);
          $Query  = "SELECT  CONCAT( a.ds_nombres,' ', a.ds_apaterno ), c.nb_perfil, ";
          $Query .= "CASE a.fg_activo WHEN 1 THEN 'Active' ELSE 'Inactive' END status, '2 days ago'  'last_login', '0.01 GB' 'usage' ";
          $Query .= "FROM c_usuario a,  c_perfil c WHERE a.fl_perfil = c.fl_perfil ";
          # Muestra los usuarios dependiendo del usuario y la escuela
          # Admin mostrara todos sus maestros estudiantes de la escuela
          # Teachers mostarar solo los studendiantes de la escuela
          if($fl_perfil == PFL_MAESTRO)
            $Query .= "AND a.fl_perfil=".PFL_ESTUDIANTE." ";
          
          $Query .= "ORDER BY a.fe_alta DESC ";
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
          MuestraModal("Actions"); 
        ArticleFin();
      SectionFin();
    ?>
  </div>

  </div>
</div>

<?php
// include('/../lib/scripts.php');
// include('/../lib/footer.php');
PresentaFooter();
?>