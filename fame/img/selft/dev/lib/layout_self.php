<?php

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
        $('#prueba').on('click', function () {
          // alert('ola');
          table.ajax.reload();
          //table.draw();
          return false;
        });
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
  function ArticleIni($id_wid = "wid-id-0", $p_icono = "fa-table", $p_titulo = "", $colorbutton = false, $togglebutton = false, $deletebutton = false, 
    $fullscreenbutton = false, $sortable =false, $p_btnname = "", $p_btncolor = "", $p_btnopt = "", $p_btnval = ""){
    echo "
    <!-- NEW WIDGET START -->
    <article class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
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
                      <li value='".$p_btnval[$i]."'>
                        <a href='javascript:void(0);'>".$p_btnopt[$i]."</a>
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

?>