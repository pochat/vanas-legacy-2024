 <?php
  
  # Funcion Menu
  function PresentaNav($p_usuario){
    
    # Perfil
   $fl_perfil = ObtenPerfilUsuario($p_usuario);
   if($fl_perfil==1)
     $fl_perfil=11;
    # Tipo de Modulo depende del perfil
    switch($fl_perfil){
      case PFL_ADMINISTRADOR: $menu = MENU_ADMIN_SELF;  break;
      case PFL_MAESTRO: $menu = MENU_MAESTRO_SELF;  break;
      case PFL_ESTUDIANTE: $menu = MENU_ALUMNO_SELF;  break;
    }
    
    echo "
    <aside id='left-panel'>
    <div class='login-info'>
      <span> 
        <a href='#ajax/profile.php'>
          <img src='".ObtenAvatarUsuario($p_usuario)."'> 
          <span>".ObtenNombreUsuario($p_usuario)."</span>
        </a> 
      </span>
    </div>
    <nav> 
      <ul style='display: block;'>";
    # Menu dependiendo de los perfiles
    $Query  = "SELECT fl_modulo, nb_modulo, tr_modulo ";
    $Query .= "FROM c_modulo ";
    $Query .= "WHERE fl_modulo_padre=$menu ";
    $Query .= "AND fg_menu='1' ";
    $Query .= "ORDER BY no_orden";
    $rs = EjecutaQuery($Query);
    for($i=0; $row = RecuperaRegistro($rs); $i++){
      $fl_modulo = $row[0];
      $nb_modulo = str_texto(EscogeIdioma($row[1], $row[2]));
      echo "
      <li class='open'>
        <a href='#' onclick='return false;' title='".$nb_modulo."'>
        <i class='fa fa-lg fa-fw'></i><span class='menu-item-parent'>".$nb_modulo."</span><b class='collapse-sign'><em class='fa fa-expand-o'></em></b></a>
        <ul style='display:block;'>";
          # Funciones
          $Query1  = "SELECT fl_funcion, nb_funcion, tr_funcion, nb_flash_default, tr_flash_default ";
          $Query1 .= "FROM c_funcion ";
          $Query1 .= "WHERE fl_modulo= $fl_modulo ";
          $Query1 .= "AND fg_menu='1' ";
          $Query1 .= "ORDER BY no_orden";
          $rs1 = EjecutaQuery($Query1);
          for($j=0; $row1 = RecuperaRegistro($rs1);$j++){
            $fl_funcion = $row1[0];
            $nb_funcion = str_texto(EscogeIdioma($row1[1], $row1[2]));
            $nb_icono = str_uso_normal(EscogeIdioma($row1[3], $row1[4]));            
            echo "<li><a href='".PATH_SELF_SITE."/node.php?node=".$fl_funcion."'>".$nb_funcion."</a></li>";            
          }
      echo "
        </ul>
      </li>";
    }
    echo "
      </ul>
    </nav>
  </aside>";
  
  }
  
  # Funcion Bibbon
  function PresentaRibbon(){
    echo "
    <!-- RIBBON -->
    <div id='ribbon'>
      <div id='hide-menu' class='btn-header pull-left btn-ribbon-left'>
        <span> <a href='javascript:void(0)' title='Menu'><i class='fa fa-reorder'></i></a></span>
      </div>
      <div id='contacts' class='btn-header pull-right btn-ribbon-right'>
        <span> <a href='javascript:void(0)' title='Contacts'><i class='fa fa-users'></i></a> </span>
      </div>
    </div>";
  }
  
  # Funcion Ini Div principal
  function PresentaMainIni(){
    echo "
    <!--  Inicia Main -->
    <div id='main' role='main'>";
  }
  
  # Funcion Ini Div contenido
  function PresentaContentIni(){
    echo "
    <!--  Inicia Content -->
    <div id='content'>";
  }
  
  # Funcion Fin Div contenido
  function PresentaContentFin(){
    echo "
    </div>
    <!-- Fin Content -->";
  }
  
  # Funcion Ini Fin  principal
  function PresentaMainFin(){
    echo "
    </div>
    <!-- Fin de Main -->";
  } 
  
  # Funcion Header y Nav
  function PresentaHeaderNav($p_usuario){
    include('header.php');
    PresentaNav($p_usuario);
  }
  
  # Funcion Scripts y Footer
  function PresentaFooter(){
      include('scripts.php');
    include('footer.php');
  }
  
  # Muestra la inf Tot licencias users Aviable
  function PresentaContentTop($p_usuario){
    
    # Obtenemos el numero de licencias
    $tot_licencias =ObtenNumLicencias($fl_usuario);
    # Licencias activadas
    $no_usuarios = ObtenNumeroUserInst($fl_instituto);
    # Licencias no viables
    $avaible = $tot_licencias - $no_usuarios;
    
    echo "
    <!-- Encabezado de users administrador -->
    <div class='row'>
      <!-- col -->
      <div class='col-xs-12 col-sm-7 col-md-7 col-lg-2 padding-5'>
      
        <div class='form-group'>";     
      $options = array('All Users', 'Students','Teachers', 'Admin(s)', 'Unassigned');
      $valores = array('AU','S','T','AD','UN');
      CampoSelect('fl_users', $options, $valores, 'AU');
    echo "
      </div>
    </div>
      <div class='col-xs-12 col-sm-7 col-md-7 col-lg-2 padding-5'>";
    # Por defaul ocultamos este filtro
    $opt_status = array('Status', 'Active','Inactive');
    $val_status = array(2,1,0);
    CampoSelect('fl_status', $opt_status, $val_status, 2, 'select2 hidden');
    echo "
      </div>
      <script>
      $(document).ready(function(){
        $('#fl_users').on('change', function(){
          $('#fl_status').removeClass('hidden');
          if($(this).val()=='AU')
            $('#fl_status').addClass('hidden');
        });
      });
      </script>
      <!-- end col -->
      <!-- right side of the page with the sparkline graphs -->
      <!-- col -->
      <div class='col-xs-12 col-sm-5 col-md-5 col-lg-8'>
        <!-- sparks -->
        <ul id='sparks'>
          <li class='sparks-info'>
            <h5> Total de Licencias: ".$tot_licencias."</h5>
          </li>
          <li class='sparks-info'>
            <h5> Users: ".$no_usuarios."</h5>
          </li>
          <li class='sparks-info'>
            <h5> Available: ".$avaible."
            </h5>
          </li>
        </ul>
        <!-- end sparks -->
        <div class='text-align-right'>
        <div>15.45 GB(51%) de 30 GB used</div>
        <div><a href='#'>Manage</a></div>
        </div>
      </div>
      <!-- end col -->
    </div>";

  }
  
  # Funcion Tabla Encabezado
  function MuestraTablaIni($p_idtable="example", $p_class="", $p_width = "100%", $p_titulos = array()){ 
    # Por default esta esta clase para las tablas
    if(empty($p_class))
      $p_class = "display projects-table table table-striped table-bordered table-hover";
    echo "
    <table id='$p_idtable' class='$p_class' cellspacing='0' width='$p_width'>
      <thead>
        <tr>";
      # Muetsra los titulos de la tabla
      for($i=0;$i<=sizeof($p_titulos);$i++){
        echo "<th>".$p_titulos[$i]."</th>";
      }
    echo "
        </tr>
      </thead>
      <tbody>";
  }
  
  # Funcion Tabla Footer
  function MuestraTablaFin($p_datatable = true, $p_idtable = ""){
    echo "
      </tbody>
    </table>";
    if($p_datatable){
      echo "
      <script>
      $(document).ready(function(){
        var table = $('#";
        if(!empty($p_idtable))
          echo $p_idtable;
        else
          echo "example";
      echo "').dataTable();
        
      });
      </script>";
    }
  }
  
  # Section Ini
  function SectionIni(){
    echo "
    <!-- widget grid -->
    <section id='widget-grid' class=''>  
      <!-- row -->
      <div class='row'>";
  }
  
  # Section Fin
  function SectionFin(){
    echo "
      </div>
      <!-- end row -->
    </section>
    <!-- end widget content -->";
  }
  
  # Muestra un article INI  
  function ArticleIni($p_size = "col-xs-12 col-sm-12 col-md-12 col-lg-12", $id_wid = "wid-id-0", $p_icono = "fa-table", $p_titulo = "", $colorbutton = false, $togglebutton = false, $deletebutton = false, 
    $fullscreenbutton = false, $sortable =false, $p_btnname = "", $p_btncolor = "", $p_btnopt = "", $p_btnval = ""){
    echo "
    <!-- NEW WIDGET START -->
    <article class='".$p_size."'>
      <!-- Widget ID (each widget will need unique ID)-->
      <div class='jarviswidget' id='".$id_wid."' data-widget-editbutton='false' ";
      # Si desea cambiar color solo activamos
      if(!$colorbutton)
        echo "data-widget-colorbutton='false'";
      # Si desea minimizar color solo activamos
      if(!$togglebutton)
        echo "data-widget-togglebutton='false'";
      # Si desea eliminar color solo activamos
      if(!$deletebutton)
        echo "data-widget-deletebutton='false'";
      # Si desea fullscreenbutton color solo activamos
      if(!$fullscreenbutton)
        echo "data-widget-fullscreenbutton='false'";
      # Si desea que sea sortable es decir se mueva
      if(!$sortable)
        echo "data-widget-sortable='false'";
    echo "
      >
        <header>
          <span class='widget-icon'><i class='fa ".$p_icono."'></i> </span>
          <h2><strong>".$p_titulo."</strong></h2>";
          # Muestra el boton si se agrego en los parametros
          if(!empty($p_btnname) && !empty($p_btncolor) && !empty($p_btnopt) && !empty($p_btnval)){
          echo "
                <div class='widget-toolbar' role='menu'>
                  <div class='btn-group'>
                    <button class='btn dropdown-toggle btn-xs btn-".$p_btncolor."' data-toggle='dropdown'>
                      ".$p_btnname." <i class='fa fa-caret-down'></i>
                    </button>
                    <ul class='dropdown-menu pull-right'>";
                    $tot = count($p_btnopt);
                    for($i = 0; $i < $tot; $i++) {
                      echo "
                      <li>
                        <a href='javascript:actions(\"".$p_btnval[$i]."\", \"".$p_btnopt[$i]."\");'>".$p_btnopt[$i]."</a>
                      </li>";
                    }
          echo "
                    </ul>
                  </div>
                </div>";
          }
    echo "
        </header>

        <!-- widget div-->
        <div style='padding-top:55px;'>

          <!-- widget edit box -->
          <div class='jarviswidget-editbox'>
            <!-- This area used as dropdown edit box -->

          </div>
          <!-- end widget edit box -->

          <!-- widget content -->
          <div class='widget-body no-padding'>";
  }
  
  # Muestra un articulo FIN
  function ArticleFin(){
    echo "
          </div>
          <!-- end widget content -->          
        </div>
        <!-- end widget div -->        
      </div>
      <!-- end widget -->
    </article>
    <!-- WIDGET END --> ";
  }
  
  # Funcion para mostrar Split Button
  function SplitButton(){
    echo "
    <div class='widget-toolbar' role='menu'>
      <div class='btn-group'>
        <button class='btn dropdown-toggle btn-xs btn-warning' data-toggle='dropdown'>
          Dropdown <i class='fa fa-caret-down'></i>
        </button>
        <ul class='dropdown-menu pull-right'>
          <li>
            <a href='javascript:void(0);'>Option 1</a>
          </li>
          <li>
            <a href='javascript:void(0);'>Option 2</a>
          </li>
          <li>
            <a href='javascript:void(0);'>Option 3</a>
          </li>
        </ul>
      </div>
    </div>";
  }
  
   # Muestra 
  function MuestraModal($Id_modal = "idmodal"){
    echo "<div class='modal fade' id='".$Id_modal."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'></div>";
    /*echo "
    <div class='modal fade' id='".$Id_modal."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='display: none;'>
      <div class='modal-dialog'>
        <div class='modal-content'>";
        # Header
        if($p_header){
          echo "
          <div class='modal-header padding-10'>";
          if($p_title){
            echo "
            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>
              &times;
            </button>";
          }
            echo "
            <h4 class='modal-title' id='myModalLabel'><i class='fa fa-exclamation-triangle'></i> <strong id='title_modal'>".$p_title."</strong></h4>";
          echo "
          </div>";
        }
        # Body
        if($p_body){
          echo "
          <div class='modal-body'>".
          $p_layoutBd
          ." 
          </div>";
        }
        # Footer
        if($p_footer){
        echo "
          <div class='modal-footer padding-10'>".
          $p_layoutFt
          ."</div>";
        }
    echo "
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
    $('.modal-dialog').css('width', '50%');
    $('.modal-dialog').css('margin', '15% 15% 15% 25%');    
    </script>";*/
  }

  # Campo Oculto 
  function CampoOculto($p_nombre, $p_valor='') {
  
    echo "
      <input type='hidden' id='$p_nombre' name='$p_nombre' value=\"$p_valor\">\n";
  }

  # Campo Select
  function CampoSelect($p_nombre, $p_opc, $p_val, $p_actual, $p_clase='select2', $p_seleccionar=False, $p_script='') {
  
    $tot = count($p_opc);
    echo "<select id='$p_nombre' name='$p_nombre' class='$p_clase'";
    if(!empty($p_script)) echo " $p_script";
    echo ">\n";
    if($p_seleccionar)
      echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
    for($i = 0; $i < $tot; $i++) {
      echo "<option value=\"$p_val[$i]\"";
      if($p_actual == $p_val[$i])
        echo " selected";
      echo ">$p_opc[$i]</option>\n";
    }
    echo "</select>";
  }

  # Campo Texto
  function CampoTexto($p_nombre, $p_valor, $p_clase='form-control', $p_password=False, $p_script='', $p_placeholder="", $p_icono = "fa-user",  $p_col = "col-md-12") {
    
    if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
      if(!$p_password)
        $ds_tipo = 'text';
      else
        $ds_tipo = 'password';
      echo "
      <section class='".$p_col."'>
        <label class='input'>
          <i class='icon-prepend fa ".$p_icono."'></i>";
      echo "<input tpe='$ds_tipo' class='$p_clase' id='$p_nombre' name='$p_nombre' placeholder='$p_placeholder' value=\"$p_valor\" ";
      if($p_password)
        echo " autocomplete='off'";
      if(!empty($p_script)) echo " $p_script";
      echo ">";
      echo "
          <i style='display: none;' class='form-control-feedback' data-bv-icon-for='".$p_nombre."'></i>
          </label>
        </section>";
    }
    else
      Forma_CampoOculto($p_nombre, $p_valor);
  }

  # Campo para archivo
  function CampoArchivo($p_nombre, $p_size, $p_clase, $p_accept='', $p_maxlength='1') {
    
    if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
      if(!empty($p_accept))
        $ds_accept = "accept='$p_accept'";
      if(!empty($p_maxlength))
        $ds_maxlength = "maxlength='$p_maxlength'";
      $ds_nombre = $p_nombre;
      $ds_clase = $p_clase;
      if(!empty($p_accept) OR $p_maxlength <> '1') {
        $ds_nombre .= "[]";
        $ds_clase = 'multi';
      }
      echo "<input type='file' class='$ds_clase' id='$p_nombre' name='$ds_nombre' size='$p_size' $ds_accept $ds_maxlength>
      <i style='display: none;' class='form-control-feedback' data-bv-icon-for='".$p_nombre."'></i>";    
    }
    else
      Forma_CampoOculto($p_nombre);
  }
?>