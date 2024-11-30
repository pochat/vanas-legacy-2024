 <?php
  include('layout_front_back.php');
  
  # Funcion Menu selecciona la columna de BD dependiendo el idioma selecionad
	function CreateMenuSP($p_usuario) {
    # Aded by Ulises, select language and apply a sufix for the selection of the right lang-table on the DB
    $langselect = isset($_COOKIE[IDIOMA_NOMBRE])?$_COOKIE[IDIOMA_NOMBRE]:NULL;

    switch ($langselect) {
      case '1': $sufix = '_esp';
        break;

      case '2': $sufix = '';
        break;

      case '3': $sufix = '_fra';
        break;
      
      default: $sufix = '';
        break;
    }
		$fl_usuario = $p_usuario;
		$fl_perfil = ObtenPerfilUsuario($fl_usuario);
		$nb_usuario = ObtenNombreUsuario($fl_usuario);
		$ruta_avatar = ObtenAvatarUsuario($fl_usuario);
        $fl_instituto=ObtenInstituto($fl_usuario);

    # Variable initialization
    $ya_expiro_fecha=NULL;
		
		if(($fl_perfil==PFL_ADMINISTRADOR)||($fl_perfil==PFL_ADM_CSF)){
        
            #Verificamos si, se ecnuantra en modo trial o en plan
            $Query="SELECT fg_tiene_plan FROM c_instituto WHERE fl_instituto=$fl_instituto ";
            $row=RecuperaValor($Query);
            $fg_tiene_plan=$row[0];
            
            #Obtenemos fecha actual :
            $Query = "Select CURDATE() ";
            $row = RecuperaValor($Query);
            $fe_actual = str_texto($row[0]);
            $fe_actual=strtotime('+0 day',strtotime($fe_actual));
            $fe_actual= date('Y-m-d',$fe_actual);
            
            #Institutos que ya tuvieron plan
            if($fg_tiene_plan==1){ 
                 $fe_terminacion= ObtenFechaFinalizacionContratoPlan($fl_instituto);
            }else{
            #Institutos que se quedaron en modo de prueba.                      
                 $fe_terminacion=ObtenFechaFinalizacionTrial($fl_instituto); 
            }
            
            if($fe_terminacion < $fe_actual)
                $ya_expiro_fecha=1;
            else
                $ya_expiro_fecha=0;
            #Verifica si esta congelada la cuenta. mostrara solo billing.
            $Query="SELECT fg_estatus FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
            $row=RecuperaValor($Query);
            if($row['fg_estatus']=='F'){
                $ya_expiro_fecha=1;
            }



        }

        #Verifica que usuario no haya congelado la cuenta, en este caso solo mostrara billing para descongelar.
        if($fl_perfil==PFL_ESTUDIANTE_SELF){

            $Query="SELECT fg_b2c FROM c_usuario WHERE fl_usuario=$fl_usuario ";
            $row=RecuperaValor($Query);
            if($row['fg_b2c']=='1'){
             
                $Query="SELECT fg_status FROM k_current_plan_alumno WHERE fl_alumno=$fl_usuario ";
                $row=RecuperaValor($Query);
                if($row['fg_status']=='F'){
                    $ya_expiro_fecha=1;
                }
            }
        }
        
       

		
		# Initializes the left menu list
		$page_nav = array();

	  # Presenta menu en Columna izquierda
		if($fl_perfil == PFL_MAESTRO_SELF) {
			$menu = MENU_MAESTRO_SELF;
      /*$Query  = "SELECT c.no_semana, c.fl_leccion_sp,a.fl_usuario_sp ";
      $Query .= "FROM k_usuario_programa a,k_details_usu_pro b, c_leccion_sp c, c_programa_sp e, c_alumno_sp f ";
      $Query .= "WHERE     a.fl_maestro = $fl_usuario AND b.fl_usu_pro = a.fl_usu_pro AND a.fl_programa_sp = c.fl_programa_sp AND a.fl_programa_sp = e.fl_programa_sp ";
      $Query .= "AND a.fl_usuario_sp = f.fl_alumno_sp AND EXISTS(SELECT 1 FROM k_entrega_semanal_sp d WHERE     d.fl_alumno = a.fl_usuario_sp AND d.fl_promedio_semana IS NULL ";
      $Query .= "AND EXISTS(SELECT 1 FROM k_entregable_sp k WHERE k.fl_entrega_semanal_sp = d.fl_entrega_semanal_sp)) AND (   c.fg_animacion = '1' OR c.fg_ref_animacion = '1' ";
      $Query .= "OR c.no_sketch > 0 OR c.fg_ref_sketch = '1') AND  b.fg_grade_tea='1' ORDER BY a.fl_usuario_sp"; 
      -----
      $Query .= "SELECT c.no_semana, c.fl_leccion_sp,a.fl_usuario_sp, d.fl_entrega_semanal_sp FROM k_usuario_programa a,k_details_usu_pro b, ";
      $Query .= "c_leccion_sp c, c_programa_sp e, c_alumno_sp f ,k_entrega_semanal_sp d , k_entregable_sp k ";
      $Query .= "WHERE a.fl_maestro = ".$fl_usuario." AND b.fl_usu_pro = a.fl_usu_pro AND a.fl_programa_sp = c.fl_programa_sp ";
      $Query .= "AND a.fl_programa_sp = e.fl_programa_sp AND a.fl_usuario_sp = f.fl_alumno_sp AND k.fl_entrega_semanal_sp = d.fl_entrega_semanal_sp ";
      $Query .= "AND d.fl_alumno = a.fl_usuario_sp AND d.fl_promedio_semana IS NULL  ";
      $Query .= "AND (c.fg_animacion = '1' OR c.fg_ref_animacion = '1' OR c.no_sketch > 0 OR c.fg_ref_sketch = '1') AND  b.fg_grade_tea='1' ORDER BY a.fl_usuario_sp ";
      ---
      $Query1 = "SELECT a.fl_alumno  FROM k_entrega_semanal_sp a, k_entregable_sp b, c_usuario c
      WHERE a.fl_entrega_semanal_sp=b.fl_entrega_semanal_sp AND a.fl_promedio_semana IS NULL
      AND c.fl_usuario=a.fl_alumno AND fl_instituto=".ObtenInstituto($fl_usuario);*/
      $Query1  = "SELECT DISTINCT c.no_semana, c.ds_titulo".$sufix.", c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, ";
      $Query1 .= "e.nb_programa".$sufix.", e.ds_tipo ds_tipo_programa, c.fl_leccion_sp, a.fl_usuario_sp, f.nb_grupo, a.fl_programa_sp ";
      $Query1 .= "FROM k_usuario_programa a, k_details_usu_pro b, c_leccion_sp c, c_programa_sp e, c_alumno_sp f, k_entrega_semanal_sp d, k_entregable_sp k ";
      $Query1 .= "WHERE a.fl_maestro=$fl_usuario AND b.fl_usu_pro= a.fl_usu_pro AND a.fl_programa_sp=c.fl_programa_sp AND a.fl_programa_sp=e.fl_programa_sp ";
      $Query1 .= "AND a.fl_usuario_sp=f.fl_alumno_sp ";
      $Query1 .= "AND d.fl_alumno=a.fl_usuario_sp ";
      $Query1 .= "AND (d.fl_promedio_semana IS NULL OR d.fg_increase_grade='1' ) AND d.fl_leccion_sp=c.fl_leccion_sp AND k.fl_entrega_semanal_sp=d.fl_entrega_semanal_sp ";
      $Query1 .= "AND (c.fg_animacion='1' OR c.fg_ref_animacion='1' OR c.no_sketch > 0 OR c.fg_ref_sketch='1') AND b.fg_grade_tea='1' AND (SELECT fl_usuario FROM c_usuario z WHERE z.fl_usuario =a.fl_usuario_sp AND z.fg_activo='1')  ";
      $Query1 .= "ORDER BY a.fl_usuario_sp ";
      $rs = EjecutaQuery($Query1);
      $tot_calificar = CuentaRegistros($rs);
      // for($k=0;$rowk=RecuperaRegistro($rs);$k++){
        // $fl_usu = $rowk[0];
        /*$no_semana = $rowk[0];
        $fl_leccion_sp = $rowk[1];
        $fl_alumno_sp = $rowk[2];
        $fl_entrega_semanal_sp = $rowk[3];
        $Query1  = "SELECT a.fl_entrega_semanal_sp, a.fl_alumno, a.fg_entregado, a.fl_promedio_semana ";
        $Query1 .= "FROM k_entrega_semanal_sp a, c_usuario b, k_entrega_semanal_sp c ";
        $Query1 .= "WHERE a.fl_alumno=b.fl_usuario ";
        $Query1 .= "AND a.fl_leccion_sp=$fl_leccion_sp AND a.fl_alumno=$fl_alumno_sp ";
        $Query1 .= "AND a.fl_promedio_semana IS NULL ";
        $Query1 .= "AND c.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp ";
        $Query1 .= "ORDER BY ds_nombres ";
        $rowk1 = RecuperaValor($Query1);*/
        // $row2 = RecuperaValor("SELECT fl_usuario_sp FROM k_usuario_programa WHERE fl_maestro=".$fl_usuario." AND fl_usuario_sp=".$fl_usu);
        // if(!empty($row2[0])){
          // $tot_calificar ++;
        // }
      // }
      # Actualizamos los asignaciones que tiene el teacher
      EjecutaQuery("UPDATE k_usu_notify SET no_submitted_assi=$tot_calificar WHERE fl_usuario=$fl_usuario");
		} else {
            if($fl_perfil == PFL_ADMINISTRADOR)
            $menu = MENU_ADMIN_SELF;
            if($fl_perfil==PFL_ADM_CSF)
            $menu=MENU_PFL_CSF;
            if($fl_perfil==PFL_ESTUDIANTE_SELF)
            $menu = MENU_ALUMNO_SELF;
		}

	  # Recupera las descripciones de los modulos
	  $Query  = "SELECT fl_modulo, nb_modulo".$sufix.", tr_modulo ";
	  $Query .= "FROM c_modulo ";
	  $Query .= "WHERE fl_modulo_padre=$menu ";
      #Para ocultar un moudlo antes de pasar a prod
      //if(($fl_usuario==642)||($fl_usuario==591)||($fl_usuario==666)||($fl_usuario==630)||($fl_usuario==782)){

      //}else{
      //  $Query .= "AND fl_modulo <>41 ";  
     // }
	  $Query .= " AND fg_menu='1' "; //jgfl
	  $Query .= "ORDER BY no_orden";
	  $rs = EjecutaQuery($Query);
	  for($i = 1; $row = RecuperaRegistro($rs); $i++) {
	    $fl_modulo[$i] = $row['fl_modulo'];
	    // $nb_modulo[$i] = str_texto(EscogeIdioma($row['nb_modulo'], $row['tr_modulo']));
      // Se cambia por la funcion de abajo, EscogeIdioma es obsoleto
      //$nb_modulo[$i] = str_texto($row['nb_modulo']);
      $nb_modulo[$i] = htmlentities($row["nb_modulo".$sufix], ENT_QUOTES, "UTF-8");
      // Cambio de la definicion de Query para uso de manejo de idiomas Nuevo
      // $Query  = "SELECT fl_funcion, nb_funcion, tr_funcion, nb_flash_default, tr_flash_default, ds_icono_bootstrap ";
      // Cambio de la definicion de Query para uso de manejo de idiomas Nuevo
	    $Query  = "SELECT fl_funcion, nb_funcion".$sufix.", tr_funcion, nb_flash_default, tr_flash_default, ds_icono_bootstrap ";
	    $Query .= "FROM c_funcion ";
	    $Query .= "WHERE fl_modulo=$fl_modulo[$i] ";
	    $Query .= "AND fg_menu='1' "; //jgfl
		if(($ya_expiro_fecha == 1)&&(($fl_perfil==PFL_ADMINISTRADOR)||($fl_perfil==PFL_ADM_CSF)) )#MJD si el instituto ya vencio su fecha Trial/con Plan,mostrar billing.
        $Query .="AND fl_funcion=155  ";
		
      if($fl_perfil==PFL_ESTUDIANTE_SELF){
		    $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);	   
        if(empty($fg_puede_liberar_curso)){#MJD Solo mostrar el menu billing cuando el studiante sea de vanas
          $Query.="AND fl_funcion<>181   AND fl_funcion<>182  ";				
        }
        if($ya_expiro_fecha==1){
          $Query .="AND fl_funcion=182  ";
        }
      }
      if($fl_perfil==PFL_ADMINISTRADOR){
          #Verifica si este instituto pertenece a un rector flag de cronjobs y lo muestra
          $Queryr="SELECT fl_instituto_rector FROM c_instituto WHERE fl_instituto=$fl_instituto ";
          $rowr=RecuperaValor($Queryr);
          $fl_instituto_rector=$rowr[0];
          $Queryfm="SELECT fg_menu_csf FROM c_instituto WHERE fl_instituto=$fl_instituto_rector ";
          $rowf=RecuperaValor($Queryfm);
          $fg_csf=$rowf['fg_menu_csf'];
          if($fg_csf==1){
              $Query.=" OR fl_funcion=228 ";
          }
      }
		
      $Query .= "ORDER BY no_orden";
	    $rs2 = EjecutaQuery($Query);
	    for($j = 1; $row2 = RecuperaRegistro($rs2); $j++) {
	      $fl_funcion[$i][$j] = $row2[0];
	      $nb_funcion[$i][$j] = htmlentities($row2[1], ENT_QUOTES, "UTF-8");//str_texto(EscogeIdioma($row2[1], $row2[2]));
	      $nb_icono[$i][$j] = str_uso_normal(EscogeIdioma($row2[3], $row2[4]));
	      $ds_icono_bootstrap[$i][$j] = str_uso_normal($row2[5]);
	    }
	    $tot_submodulos[$i] = $j-1;
	  }
	  $tot_modulos = $i-1;
    
	  # Form the menu list array
		for($i = 1; $i <= $tot_modulos; $i++) {
			# Initialize sub array
			$sub_nav = array();

		  # Populate the sub module array first
			for($j = 1; $j <= $tot_submodulos[$i]; $j++) {				
        if(!empty($nb_icono[$i][$j])){
          $nav_icon = "<img src='".SP_IMAGES."/".$nb_icono[$i][$j]."' width='16' height='16'>";
        } else {
          $nav_icon = "";
        }
        $notification = "";
        if(!empty($tot_calificar) && $fl_funcion[$i][$j]==172)
          $notification = "<span class='badge bg-color-red bounceIn animated' style='position: relative;left: 25px;' id='notify_assigment'><strong id='noti_assi_".$fl_usuario."'>".$tot_calificar."</strong></span>";
        
        $sub_nav += array(
          strtolower($nb_funcion[$i][$j]) => array(
            "title" => $nb_funcion[$i][$j],
            "url" => "site/node.php?node=".$fl_funcion[$i][$j],
            "nav_icon" => $nav_icon,
            "ds_icono_bootstrap" => $ds_icono_bootstrap[$i][$j],
            "notification" => $notification
          )
        );
		  }
		  $page_nav += array(
		  	strtolower($nb_modulo[$i]) => array(
		  		"title" => $nb_modulo[$i],
		  		"icon" => "",
		  		"sub" => $sub_nav
		  	)
		  );
		}
	  return $page_nav;    
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
      require_once("lib/config.ui.php");
    // $menu = CreateMenu($p_usuario);  
    include('nav.php');
  }
  
  # Funcion Scripts y Footer
  function PresentaFooter(){
      include('scripts.php');
    include('footer.php');
  }
  
  # Muestra la inf Tot licencias users Aviable
  function PresentaContentTopAdm($p_usuario, $p_size="col-xs-12 col-sm-5 col-md-5 col-lg-5"){

    # Instituto
    $fl_instituto = ObtenInstituto($p_usuario);

    # Modo trial
    $fg_modo = ObtenPlanActualInstituto($fl_instituto);

    # Obtenemos el perfil del usuario
    $fl_perfil = ObtenPerfilUsuario($p_usuario);

    # Si esta en trail mostrara el limite de licencias que tiene por usar en el trial
    if(empty($fg_modo)){
    
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
      $avaible = ObtenNumeroUserInst($fl_instituto);
      $no_usuarios = $tot_licencias - $avaible;
    }else{
      $tot_licencias =  ObtenNumLicencias($fl_instituto);
      # Obtenemos el numero de licencias
      $no_usuarios =ObtenNumLicenciasDisponibles($fl_instituto);
      # Licencias no viables
      $avaible = $tot_licencias - $no_usuarios;      
    }

    # Obtiene los dias que le restan del trial
    $no_dias_trial = ObtenDiasTrial($fl_instituto);

    # Obtenemos el peso de la carpeta de la institucion
    $GB = 32212254720;
    $percentage = abs(round((($GB-GetDirectorySize(PATH_SELF_UPLOADS_F . "/" . $fl_instituto ))*100)/$GB-100, 2));
    $peso = File_Size(PATH_SELF_UPLOADS_F . "/" . $fl_instituto);
    if (empty($peso))
      $peso = $peso . " GB";

    echo "
    <!-- col -->
    <div class='$p_size' style='top:-15px;' id='lic_inst_".$fl_instituto."'>
      <!-- sparks -->
      <ul id='sparks' class='padding-bottom-5'>
        <li class='sparks-info'>
          <h5> ".ObtenEtiqueta(1050).": ".$tot_licencias."</h5>
        </li>
        <li class='sparks-info'>
          <h5> ".ObtenEtiqueta(1051).": ".$avaible."</h5>
        </li>
        <li class='sparks-info'>
          <h5> ".ObtenEtiqueta(1052).": ".$no_usuarios."
          </h5>
        </li>
      </ul>
      <!-- end sparks -->
      <div class='text-align-right'>
      <div class='padding-bottom-5'>".$peso." (".$percentage."%) of 30 GB used</div>
      <div>";
    if (($fl_perfil == PFL_ADMINISTRADOR) || ($fl_perfil == PFL_ADM_CSF))
      echo "<a href='#site/billing.php' style='color:#00BFFF;'>" . ObtenEtiqueta(1053)."</a> ";
    if (empty($fg_modo)) {
      $trial = ObtenEtiqueta(1093);
      $trial = str_replace("#users_limit", $tot_licencias, $trial);
      $no_dias_trial_restan = ObtenEtiqueta(1115);
      $no_dias_trial_restan = str_replace("#no_dias_trial_restan", $no_dias_trial, $no_dias_trial_restan);
     
      if(empty($fg_b2c)){
          echo "
      <div>
        <code>".$trial."<br>".$no_dias_trial_restan."</code>
      </div>";
      }

      }
     echo "
      </div>
      </div>
    </div>
    <!-- end col -->
    <script>
        // Consulta el archivo convertidor
        $(document).ready(function(){
            UpdateLicences()
        });
    </script>";
    //Esta funcion se cambio por solo al abrir la pagina o refrescar setInterval(function(){ UpdateLicences() },5000);
  }
  
  # Funcion Tabla Encabezado
  function MuestraTablaIni($p_idtable="example", $p_class="", $p_width = "100%", $p_titulos = array(), $p_seleccionar = True){ 
    # Por default esta esta clase para las tablas
    if(empty($p_class))
      $p_class = "display projects-table table table-striped table-bordered table-hover";
    # Total de los registros
    $tot_registros = 0;
    echo "
    <table id='$p_idtable' class='$p_class' cellspacing='0' width='$p_width'>
      <thead>
        <tr>";
    if($p_seleccionar)
        echo "<th><label class='checkbox no-padding no-margin'><input class='checkbox' type='checkbox' id='sel_todo' name='sel_todo'><span></span></label></th>";

      # Muetsra los titulos de la tabla
      // for($i=0;$i<=sizeof($p_titulos);$i++){        cualquirer error con las tablas revisar esta linea
      for($i=0;$i<=sizeof($p_titulos);$i++){        
        // echo "<th style='width:".$p_ancho[$i]."'>".$p_titulos[$i]."</th>";
        if (!empty($p_titulos[$i])) {
          echo "<th>".$p_titulos[$i]."</th>";
        } else {
          echo "<th></th>";
        }
        $tot_registros++;
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
    $fullscreenbutton = false, $sortable =false, $p_btnname = "", $p_btncolor = "", $p_btnopt = "", $p_btnval = "", $p_btndescr="",$boton_reset="",$p_btn_upload=""){
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

		  if($boton_reset){
		  echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			   <a href='javascript:void(0);' Onclick='ResetFilter();'  class='btn btn-default btn-xs' style='margin-left:5px;margin-bottom:5px;'><i class='fa fa-refresh'></i>&nbsp; ".ObtenEtiqueta(2306)."</a> ";
		  }

		  if($p_btn_upload){
          echo" <button class='btn dropdown-toggle btn-sm btn-primary' onclick='UploadStudentLibrary();' style='float:right;margin-top: 5px;margin-right:5px;' data-toggle='modal' data-target='#exampleModalLong'>
			    <i class='fa fa-check-circle' aria-hidden='true'></i> Upload
			</button> ";
			}
		  
		  
		  
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
                        <a href='javascript:actions(\"".$p_btnval[$i]."\", \"".$p_btnopt[$i]."\");' rel='tooltip' data-placement='left' 
                        data-original-title='".$p_btndescr[$i]."'
                        >".$p_btnopt[$i]."</a>
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
  function MuestraModal($Id_modal = "idmodal", $p_static=false){
    echo "<div class='modal fade' id='".$Id_modal."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='overflow-y:scroll;overflow:auto'";
    if($p_static)
      echo " data-backdrop='static' ";
    echo "></div>";
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
  
  # Campo SelectBD
  function CampoSelectBD($p_nombre, $p_query, $p_actual, $p_clase='select2', $p_seleccionar=False, $p_script='', $p_valores='', $p_seleccionar_txt = 'Select',
  $p_seleccionar_val =0, $p_option_extra="") {  
    echo "<select id='$p_nombre' name='$p_nombre' class='".$p_clase."'";
    if(!empty($p_script)) echo " $p_script";
    echo ">\n";
    if($p_seleccionar)
      echo "<option value=".$p_seleccionar_val." data-id='".$p_seleccionar_val."'>".$p_seleccionar_txt."</option>\n";
    $rs = EjecutaQuery($p_query);
    while($row = RecuperaRegistro($rs)) {
      echo "<option value=\"$row[1]\"";
      if($p_actual == $row[1])
        echo " selected";
      
      # Determina si se debe elegir un valor por traduccion
      $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
      echo " data-fulltext='".(!empty($row[2])?$row[2]:NULL)."'>$etq_campo</option>\n";
    }
    if(!empty($p_option_extra))
      echo $p_option_extra;
    echo "</select>";
    # Si el select es multiple recibimos diferentes valores
    if(!empty($p_valores)){
      echo "    
      <script>
      $(document).ready(function(){
        $(\".select2\").val([";
      for($k=0;$k<count($p_valores);$k++){
        echo "\"$p_valores[$k]\",";
      }
      echo "
      ]).select2();
      });
      </script>";
    }
  }
  # Campo Texto
  function CampoTexto($p_nombre, $p_valor, $p_clase='form-control', $p_password=False, $p_script='', $p_placeholder="", $p_icono = "fa-user", $p_col = "col-md-12", $p_append = "prepend") {
    
    if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
      if(!$p_password)
        $ds_tipo = 'text';
      else
        $ds_tipo = 'password';
      echo "
      <section class='".$p_col."'>
        <label id='lb_".$p_nombre."' class='input'>";
          if(!empty($p_icono)){
            echo "<i class='icon-".$p_append." fa ".$p_icono."'></i>";
          }
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
  
  # Calendario
  function Forma_Calendario($p_nombre) {
  
  echo "
    <script type='text/javascript'>
    $(function(){
      $('#".$p_nombre."').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        showAnim: 'slideDown',
        showOtherMonths: true,
        selectOtherMonths: true,
        showMonthAfterYear: false,
        yearRange: 'c-50:c+50',
        autoSize: true,
        prevText : '<',
				nextText : '>'
      }); 
		});   
    $('#".$p_nombre."').addClass('hasDatepicker');
    $('<i class=\'icon-append fa fa-calendar\'></i>').insertBefore('#$p_nombre');
    /*Al elemento se le cambia de clase   */ 
    $('#div_".$p_nombre."').removeClass('form-control');
    if($('#err_".$p_nombre."').val()=='1')
      $('#div_".$p_nombre."').attr('class','row smart-form has-error');
    else
      $('#div_".$p_nombre."').attr('class','row form-group smart-form');
    //$('#div_".$p_nombre."').css('margin-left','-30px');
		</script>";  
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
  
  # function para mostar informacion de los trials
  # Esto solo aparaecer a los administradores o teachers
  function Info_Trial($p_usuario){ 
    # Obtenemos el perfil del usuario
    $fl_perfil = ObtenPerfilUsuario($p_usuario);
    if($fl_perfil == PFL_ADMINISTRADOR  || $fl_perfil == PFL_MAESTRO_SELF){
      # Este mensaje aparecera si la institucion se encunetra en un estado trail
      $fl_instituto = ObtenInstituto($p_usuario);     
      $fg_plan = Obten_Status_Trial($fl_instituto);
      $dias_trial = ObtenDiasTrial($fl_instituto);     
      $user_trial = ObtenConfiguracion(102);
      $user_trial2 = $user_trial/2;
      $licencias_dispobles = Licencias_disponibles_Trial($fl_instituto);
      #mensaje
      $mensaje_trial = "You have ".$dias_trial." days left in your free trial with a maximum of  ".$user_trial." licenses. ";
      $color_msg = "0071BD";
      if($dias_trial<=5 || $licencias_dispobles <= $user_trial2)
        $color_msg = "C79121";
      # Si tiene plan ya no aparecera este mensaje
      if(!$fg_plan){        
        echo "
        <script>
          $(document).ready(function(){         
            $.smallBox({
              title : '<h5 ><i class=\'fa fa-info-circle\'></i> <strong> Trial period information</strong></h5>',
              content : '<h6> &nbsp;<i>".$mensaje_trial."</i></h6>',
              color : '#".$color_msg."',
              iconSmall : 'fa fa-clock-o bounce animated',
              timeout : 4000
            });
          });
          pageSetUp();
        </script>";
      }
      // else{
        // echo "
        // <script>
          // $(document).ready(function(){         
            // $.smallBox({
              // title : '<h5 ><i class=\'fa fa-info\'></i> <strong>Information</strong></h5>',
              // content : '<h6> &nbsp;<i>You have ".$licencias_dispobles." licences</i></h6>',
              // color : '#0071BD',
              // iconSmall : 'fa fa-clock-o bounce animated',
              // timeout : 4000
            // });
          // });
          // pageSetUp();
        // </script>";
      // }
    }
  }

  # Funcion para mostrar alerta 
  function Alert($p_title="", $p_content="", $p_color="5F895F", $p_icono="fa-info", $p_time="",$p_valor_extra=""){

      if(!empty($p_valor_extra)){
          #Recuperamos datos del estudiante encontrado en otro instituto.
          $Query="SELECT b.ds_instituto,a.ds_nombres,a.ds_apaterno,ds_email,fl_usuario  FROM c_usuario a JOIN c_instituto b ON a.fl_instituto=b.fl_instituto WHERE  a.fl_usuario=$p_valor_extra ";
          $ro=RecuperaValor($Query);
          $nb_instituto=$ro['ds_instituto'];
          $ds_usuario=$ro['ds_nombres']." ".$ro['ds_apaterno'];
          $ds_email=$ro['ds_email'];
          $fl_usuario_invitado=$ro['fl_usuario'];

          echo"
                <script>
                        $(document).ready(function() {
                            $.smallBox({
					            title : \"  ".ObtenEtiqueta(2558)."\",
					            content : \"  ".$ds_usuario."<br><small>".$nb_instituto."</small><br><small>".$ds_email."</small> <p class='text-align-right'><a href='javascript:void(0);' onclick='EnviaInvitacionUserExistenteOtroInstituto(".ADD_STD.",".$fl_usuario_invitado.");' class='btn btn-primary btn-sm'>Yes</a> <a href='javascript:void(0);' class='btn btn-default btn-sm'>No</a></p>\",
					            color : \"#296191\",
					            //timeout: 8000,
					            icon : \"fa fa-graduation-cap\"
				            });

                         });
                </script>
             ";


      }else{


          echo '<script> 
                $(document).ready(function() {
                  $.smallBox({
                     title: "'.$p_title.'",
                     content: "'.$p_content.'",
                     color: "#'.$p_color.'",
                     iconSmall: "fa '.$p_icono.' bounce animated",';
                      if(!empty($p_time))
                          echo 'timeout: 4000';
                      echo '
                  });
                })
                document.getElementById(\'cerrar_modal\').click();//clic automatico cierra el  modal
            </script>';
      }
  }
  
  

  # Funcion muestra la forma de pago de stripe
  function FormaStripe($frm_name="frm_stripe", $mn_amount_p, $mn_tax_p, $url_charge, $fl_programa_sp=0, $ds_descripcion_pago='',$fg_tipo_plan='',$fg_motivo_pago='',$no_licencias_compradas=0,$mn_descuento='',$fg_desbloquear_curso='',$fg_plan_curso='',$fg_cupon='',
  $fg_plan_seleccionado=''){
	  
	  
	  
	  
      # Variables Stripe
      $public_key = ObtenConfiguracion(111);    
      $currency = ObtenConfiguracion(113);
      $fl_usuario = ValidaSesion(False,0, True);
      $fl_instituto=ObtenInstituto($fl_usuario);
      
      # Verificamos si paga tax
      if($mn_tax_p>0){
          $mn_tax = $mn_amount_p * $mn_tax_p;
          $mn_amount = $mn_amount_p + $mn_tax;

          #Stripe solo permite dos decimales.
          $mn_amount=number_format((float)$mn_amount,2,'.','');
          $mn_tax=number_format((float)$mn_tax,2,'.','');
      }
      else{
          $mn_amount = $mn_amount_p;
          
          
      }
      
      

      # Forma 
      echo "
                <link rel='stylesheet' type='text/css' media='screen' href='".PATH_SELF_CSS."/stripe.css'>
                <div class='row padding-10 text-align-center'>
                    <img src='".PATH_SELF_IMG."/creditCards_small.jpg' class='superbox-img' style='width:40%;'>
                </div>";
      

      echo "
               <div class='row padding-10'>
        
       ";
      
      if($fg_desbloquear_curso){
          if(empty($mn_tax))
              $mn_tax=0;
      }  
      
      
      
      if($fg_cupon){ 
          
          
          
          
          #Recupermaos la informacion de la tarjeta de credito si es que la tiene
          $info_tarjeta =FAMEVerificaInformacionTarjetaCredito($fl_usuario);
          $data=explode(",",$info_tarjeta);
          $no_tarjeta=$data[0];
          $ds_tipo=$data[1];
          $fe_mes_expiracion=$data[2];
          $fe_anio_expiracion=$data[3];
          
          $no_=strlen($fe_mes_expiracion);
          if($no_==1)
              $fe_mes_expiracion_tarjeta="0".$fe_mes_expiracion;
          
          
          echo"       <div class='panel panel-default' style='font-style:normal;color: #346597;'>
                                <div class='panel-heading text-left'><b>Payment Details:</b></div>
                                <div class='panel-body' style='background:#d6dde7;color:#346597;'>
                                    <div class='row'>
                                            <div class='col-md-6'>
                                                    <table class='table' style='padding: 5px;'>
                                                    <tbody>
                                                    <tr>
                                                    <td width='9%' class='text-right' style='border-top:0px;'>Cost:</td>
                                                    <td style='border-top:0px;'>$<span id='anti_costo'>".$mn_amount_p."</span> ".$currency."</td>
                                                    <td style='border-top:0px;'>&nbsp;</td>
                                                    </tr>
                        
                                                   
                                            
                                                     <tr>
                                                        <td style='border-top:0px;' width='9%' class='text-right'>".ObtenEtiqueta(2166).":</td>
                                                        <td style='border-top:0px;' colspan='2'>
                                                            <div class='smart-form col-md-6' style='padding-left:0px;'>
                                                                 <label class='input' id='labelinput'> <i class='icon-append fa fa-ticket' aria-hidden='true'></i>
											                            <input name='ds_codigo_cupon' onkeyup='PintarVerde();' type='text' id='ds_codigo_cupon' />
                                                                 </label>
                                                                  <span id='codigo_correcto' class='text-center text-success hidden' style='margin-top: 5px;'><i class='fa fa-check-circle-o'></i> ".ObtenEtiqueta(2167)." </span>
                                                                  <span id='codigo_error' class='text-center text-danger hidden'style='margin-top: 5px;'><i class='fa fa-times-circle-o'></i> ".ObtenEtiqueta(2168)." </span>
                                     
                                                            </div>
                                                            <div class='col-md-4'>
                                                             &nbsp;<a href='javascript:void(0);' onclick='VerificaCodigo();' class='btn btn-default' style='border-radius: 10px;'>".ObtenEtiqueta(2169)."</a>
                                      
                                                             </div>
                                                        </td>
                            
                                                    </tr> 

                                                    <tr  id='info_descuento' class='hidden  text-success'>
                                                    <td width='9%' class='text-right' style='border-top:0px;'><b>".ObtenEtiqueta(2170).": (<span id='fg_tipo_descuen'></span>)</b></td>
                                                    <td style='border-top:0px;'><b>- $<span id='cantidad_con_descuento'></span> ".$currency."</b></td>
                                                    <td style='border-top:0px;'>&nbsp;</td>
                                                    </tr> 
                                                    
													<tr id='subtota' class='hidden'>
                                                    <td width='9%' class='text-right' style='border-top:0px;'>Subtotal:</td>
                                                    <td style='border-top:0px;'>$<span id='subtotal_vist'></span> ".$currency."</td>
                                                    <td style='border-top:0px;'>&nbsp;</td>
                                                    </tr>
													
													
													
                                                    <tr>
                                                    <td width='9%' class='text-right' style='border-top:0px;'>".ObtenEtiqueta(1713).":</td>
                                                    <td style='border-top:0px;'>$<span id='ant_tax'>".$mn_tax."</span> ".$currency."</td>
                                                    <td style='border-top:0px;'>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td width='9%' class='text-right'><strong>Total:</strong></td>
                                                        <td><strong>$<span id='mn_cant_pagar'>".$mn_amount."</span> ".$currency."</strong></td>
                                                    </tr>

                                                    </tbody>
                                                    </table>
                                            </div>
                                            <div class='col-md-6'>";
          
          
          if(!empty($no_tarjeta)) {
              #Presentamos infor de su tarjeta de credito            
              echo"
                                                <div class='well' style='border: 0px solid #ddd;'>
                                                      <span class=' text-left' ><b>".ObtenEtiqueta(2146)."</b></span><br /><br />
                                                     <div class='row'>
                                                          
                                                         <div class='col-md-8'>
                                                              <p><span class=''><b>".$ds_tipo." ".ObtenEtiqueta(2147)." </b></span>".$no_tarjeta."</p>
                                                              <p><span class=''><b>". ObtenEtiqueta(2148)."</b></span> ".$fe_mes_expiracion_tarjeta."/".$fe_anio_expiracion." </p>
                                                         </div>
                                                         
                                                         <div class='col-md-4'>
                                                            <i class='fa fa-credit-card-alt' aria-hidden='true' style='font-size: 30px;padding-right:5px;'></i>
                                                         
                                                         </div>
                                                         
                                                         
                                                     </div>
                                                     <div class='row'>

                                                       
                                                        <div class='col-md-12 text-left'>
                                                                <br/>
                                                                <div class='smart-form' style=' '> 
                                                                    <label class='checkbox' id='label_checkbox'>
												                            <input name='otra_tarjeta' id='otra_tarjeta' type='checkbox'>
												                        <i style='top: -2px;'></i>".ObtenEtiqueta(2171)."</label>                     
                                                                </div> 
                                                                        
                                                        </div>
                                                        
                                                        
                                                      </div>
                                                      <div class='row hidden' id='gif_misma_tarjeta'><div class='col-md-12 text-center' id='contenedor_gif'> <h6><img src='img/loading_stripe.gif' style='height:40px;'><strong> ".ObtenEtiqueta(1738)."</strong></h6>   </div></div>
                                                 </div>
                                                ";
          }
          
          
          echo"                                
                                            </div>
                                    
                                    </div>
                                    
                                        ";                 
          
          if( (!empty($no_tarjeta)) && (!empty($fg_cupon)) )
              $hidden_pago="hidden";
          else
              $hidden_pago="";    
          
          
          
          
          
          
          
          
          
          echo"   

            
                                     
                                </div>
                        </div>
                     "; 
          
          if(!empty($no_tarjeta)){  
              
           
              echo"<div class='row'>
                                    <div class='col-md-12 text-center'>  
                                         <p  class='button2' style='border-radius:16px;text-decoration:none;cursor:pointer;' id='btn_misma_tarjeta' onclick='RealizarPagoMismaTarjeta();'><strong style='text-decoration:none !important;'>Process payment </strong></a>                                        
                                     </div>
                                </div>";
          }
          
      }else{    
          echo" 

                <div class='alert alert-block alert-info'>
                ";
          
          if($fg_tipo_plan){
              $mn_porc_tax=$mn_tax_p*100;
              
              if(empty($mn_tax))
                  $mn_tax=0;
              
              
              
              
              echo"
                                <h4> ".ObtenEtiqueta(1723)."</h4>
             
                                <hr style='margin:3px;'>
                                <div class='row'>
                                       <div class='col-md-7'>
                                            $ds_descripcion_pago
                ";                            
              
              $info_tarjeta=FAMEVerificaInformacionTarjetaCreditoBilling($fl_instituto);                                 
              $data=explode(",",$info_tarjeta);
              $no_tarjeta=$data[0];
              $ds_tipo=$data[1];
              $fe_mes_expiracion=$data[2];
              $fe_anio_expiracion=$data[3];
              
              $no_=strlen($fe_mes_expiracion);
              if($no_==1)
                  $fe_mes_expiracion_tarjeta="0".$fe_mes_expiracion;
              
              if(!empty($no_tarjeta)) {
                  
                  
                  #Presentamos infor de su tarjeta de credito            
                  echo"                            <br/><br/>
                                                <div class='well' style='border: 0px solid #fff;'>
                                                      <span class=' text-left' ><b>".ObtenEtiqueta(2146)."</b></span><br /><br />
                                                     <div class='row'>
                                                          
                                                         <div class='col-md-8'>
                                                              <p><span class=''><b>".$ds_tipo." ".ObtenEtiqueta(2147)." </b></span>".$no_tarjeta."</p>
                                                              <p><span class=''><b>". ObtenEtiqueta(2148)."</b></span> ".$fe_mes_expiracion_tarjeta."/".$fe_anio_expiracion." </p>
                                                         </div>
                                                         
                                                         <div class='col-md-4'>
                                                            <i class='fa fa-credit-card-alt' aria-hidden='true' style='font-size: 30px;padding-right:5px;'></i>
                                                         
                                                         </div>
                                                         
                                                         
                                                     </div>
                                                     <div class='row'>

                                                       
                                                        <div class='col-md-12 text-left'>
                                                                <br/>
                                                                <div class='smart-form' style=' '> 
                                                                    <label class='checkbox' id='label_checkbox'>
												                            <input name='otra_tarjeta' id='otra_tarjeta' type='checkbox'>
												                        <i style='top: -2px;'></i>".ObtenEtiqueta(2171)."</label>                     
                                                                </div> 
                                                                        
                                                        </div>
                                                        
                                                        
                                                      </div>
                                                      <div class='row hidden' id='gif_misma_tarjeta'><div class='col-md-12 text-center' id='contenedor_gif'> <h6><img src='img/loading_stripe.gif' style='height:40px;'><strong> ".ObtenEtiqueta(1738)."</strong></h6>   </div></div>
                                                 </div>
                                                ";

                  
                  
                  
                  if(!empty($no_tarjeta) )
                      $hidden_pago="hidden";
                  else
                      $hidden_pago="";    
                  
                  $fg_plan_seleccionado=0; 
                  
              }
              
              
              
              
              
              
              echo"                                
                                       </div>
                                       <div class='col-md-3 text-right'>
			                                   ".ObtenEtiqueta(1750).":<br/>
                                               ".ObtenEtiqueta(1712).":<br/>
                                ";
              
              echo" ".ObtenEtiqueta(1713)." (".$mn_porc_tax."%):<br/>";
              
              
              echo"  ".ObtenEtiqueta(1714).":
                                        </div>
               
                                        <div clas='col-md-2'>
			                                    ".$mn_descuento." % <br/>
                                                $".number_format($mn_amount_p,2)." <br/>";
              
              echo" $".number_format($mn_tax,2)." <br/>";
              
              echo"    $".number_format($mn_amount,2)."  
                                        </div>
               
                                 </div>
                                    ";
          }else{

              
              echo"<h6 class='alert-heading no-margin'>Payment Details:</h6>";
              echo"
                                            <ul class='list-unstyled'>";
              
              if($fg_desbloquear_curso){
                  if(empty($mn_tax))
                      $mn_tax=0;
                  
                  
                  echo"<li class='txt-color-black'><strong><i class='fa fa-check'></i> <i>Cost: $".$mn_amount_p." ".$currency."</i></strong></li>";
              }else{
                  echo" <li class='txt-color-black'><strong><i class='fa fa-check'></i> <i>Certified cost: $".$mn_amount_p." ".$currency."</i></strong></li> ";
              }     
              
              echo"           <li class='txt-color-black'><strong><i class='fa fa-check'></i> <i>".ObtenEtiqueta(1713).": $".$mn_tax." ".$currency."</i></strong></li>
                                                        <li class='txt-color-red'><strong><i class='fa fa-plus'></i> <i>Total cost: $".$mn_amount." ".$currency."</i></strong></li>
                                            </ul>";      
              
              
          }
          
          
          echo"</div><!--end alrt info--->";
      }
      
      echo"</div><!-----end row-10--->";
      
      
      if( (!empty($no_tarjeta)) && (empty($fg_cupon)) ){  
          
          echo"<div class='row hidden' id='gif_misma_tarjeta'><div class='col-md-12 text-center' id='contenedor_gif'><h6><img src='img/loading_stripe.gif' style='height:40px;'><strong> ".ObtenEtiqueta(1738)."</strong></h6>   </div></div>
                                                ";
          echo"<div class='row'>
                                    <div class='col-md-12 text-center'>  
                                         <p  class='button2' style='border-radius:16px;text-decoration:none;cursor:pointer;' id='btn_misma_tarjeta' onclick='RealizarPagoMismaTarjeta_Billing();'><strong style='text-decoration:none !important;'>Process payment </strong></a>                                        
                                     </div>
                                </div>";
      }
      
      
      
      
      
      echo"<form id='".$frm_name."' style='font-style:normal;'>   ";   
      
      if(empty($hidden_pago))
          $hidden_pago="";
	  
      echo "
    
      <div class='group $hidden_pago' id='dtos_nombre' >
        <label style='font-size: 14px;'>
          <span>".ObtenEtiqueta(1707)."</span>
          <input name='cardholder-name' class='field' placeholder='Jane Doe' />
        </label>
        <label style='display:none;'>
          <span>Phone</span>
          <input class='field' placeholder='(123) 456-7890' type='tel' />
        </label>
      </div>
      <div class='group $hidden_pago' id='dtos_card'>
        <label style='font-size: 14px;'>
          <span>".ObtenEtiqueta(1708)." </span>
          <div id='card-element' class='field'></div>
        </label>
      </div>";
      #Solo si tiene plan en el curso.
      if(!empty($fg_plan_curso))
          echo "<input type='hidden' id='fg_plan_curso' name='fg_plan_curso' value='".$fg_plan_curso."'> ";
      
      # Si mandamos el programa lo agregamos a la forma
      if(!empty($fl_programa_sp))
          echo "<input type='hidden' id='fl_programa_sp' name='fl_programa_sp' value='".$fl_programa_sp."'>";   
      # Enviamos el tax
      if($mn_tax_p>0){
          echo "<input type='hidden' id='mn_tax' name='mn_tax' value='".Conv_Dollars_Stripe($mn_tax)."'>";
      }else{
          $mn_tax=0;
          echo "<input type='hidden' id='mn_tax' name='mn_tax' value='".Conv_Dollars_Stripe(0)."'>";
      }
      echo "
      <input type='hidden' id='mn_amount' name='mn_amount' value='".Conv_Dollars_Stripe($mn_amount)."'>
      <input type='hidden' id='currency' name='currency' value='".$currency."'> 
      <input type='hidden' id='ds_descripcion_pago' name='ds_descripcion_pago' value='".$ds_descripcion_pago."'>
      <input type='hidden' id='fg_tipo_plan' name='fg_tipo_plan' value='".$fg_tipo_plan."'>
	  <input type='hidden' id='fg_motivo_pago' name='fg_motivo_pago' value='".$fg_motivo_pago."'>
	  <input type='hidden' id='no_licencias_compradas' name='no_licencias_compradas' value='".$no_licencias_compradas."'> ";
      
      #Inputs para procesar pago coupons b2c.
	  
      if(empty($fg_plan_seleccionado))
          $fg_plan_seleccionado=0;
	  
      echo"
       <input type='hidden' id='mn_monto_cupon' name='mn_monto_cupon' value=''>
       <input type='hidden' id='fg_cupon' name='fg_cupon' value=''>
       <input type='hidden' id='fg_tipo_descuento' name='fg_tipo_descuento' value=''>
       <input type='hidden' id='fl_cupon' name='fl_cupon' value=''>
       <input type='hidden' id='fg_plan_seleccionado' name='fg_plan_seleccionado' value='".$fg_plan_seleccionado."'>
      
       ";  
      
      
      echo"   
      <div class='outcome' style='padding-top:0px; min-height:0px;'>
        <div class='error' role='alert'></div>
        <div class='success'>
          Your Payment Correct!!!
        </div>
      </div>
	  ";
      
      #fomateamos la cantidad.
      if($fg_tipo_plan)
          $mn_amount=number_format($mn_amount,2);
      
      echo"
	  
	  <div class='col-md-12 text-center hidden' id='presenta_gif'><h5><img src='img/loading_stripe.gif' style='height:40px;'><strong> ".ObtenEtiqueta(1738)."</strong></h5> </div>
      
	  
      <button type='submit' class='button2 $hidden_pago' id='btn_pago' onclick='Pagar();' style='border-radius:16px;'><strong>".ObtenEtiqueta(1710)." $<span id='mn_cant_pagar2'>".$mn_amount."</span> ".$currency." </strong></button>      
    </form>
    
    
    
    
    <script>
	function Pagar(){

        $('#presenta_gif').removeClass('hidden');
		$('#btn_pago').addClass('hidden');

    }
	   
    var stripe = Stripe('".$public_key."');
    var elements = stripe.elements();

    var card = elements.create('card', {
      style: {
        base: {
          iconColor: '#666EE8',
          color: '#31325F',
          lineHeight: '40px',
          fontWeight: 300,
          fontFamily: '\"Helvetica Neue\", Helvetica, sans-serif',
          fontSize: '15px',

          '::placeholder': {
            color: '#8898aa',
          },
        },
      }
    });
    card.mount('#card-element');

    function setOutcome(result) {
	  
	
      var successElement = document.querySelector('.success');
      var errorElement = document.querySelector('.error');
      successElement.classList.remove('visible');
      errorElement.classList.remove('visible');

      if (result.token) { 
        //alert('entro');
        $('#presenta_gif').removeClass('hidden');
		$('#btn_pago').addClass('hidden');
	    //alert('fua');
        // Use the token to create a charge or a customer
        // https://stripe.com/docs/charges              
        // Token of Stripe
        var token = result.token.id;
        var forma = $('#".$frm_name."');
        // Agregamos el token a la forma
        forma.append($('<input type=\"hidden\" name=\"stripeToken\" name=\"stripeToken\" />').val(token));
        // datos de la forma
        var frm_stripe = forma.serialize();
		
		  $('#presenta_gif').removeClass('hidden');     
          $('#btn_pago').addClass('hidden');
          
          
        $.ajax({
          type: 'POST',
          url: '".$url_charge."',
          async: false,
          data: frm_stripe,
        })
        .done(function(result2){
          var stripe_result = JSON.parse(result2);
          var payment = stripe_result.paid;
          var error = stripe_result.error;
          
          if(error == 0){
            if(payment==true){
              // successElement.classList.add('visible');
              // Send menssage
              forma.empty().append(stripe_result.message);
              // actualizamos la tabla
              $('#tbl_mycourses').DataTable().ajax.reload();
            }
            else{
              errorElement.textContent = '<strong>Error on your payment,</strong> Llease review data your card!!! ';
              errorElement.classList.add('visible');
			   $('#presenta_gif').addClass('hidden');
               $('#btn_pago').removeClass('hidden');
            }
          }
          else{
            errorElement.textContent = error;
            errorElement.classList.add('visible');
			 $('#presenta_gif').addClass('hidden');
             $('#btn_pago').removeClass('hidden');
          }
          
        });
      } else if (result.error) {
        errorElement.textContent = result.error.message;
        errorElement.classList.add('visible');
      }
    }

    card.on('change', function(event) {
      setOutcome(event);
    });

    document.querySelector('#frm_stripe').addEventListener('submit', function(e) {
      e.preventDefault();
      var form = document.querySelector('#frm_stripe');
      var extraDetails = {
        name: form.querySelector('input[name=cardholder-name]').value,
        amount: form.querySelector('input[name=mn_amount]').value,
        currency: form.querySelector('input[name=currency]').value
      };
      stripe.createToken(card, extraDetails).then(setOutcome);
    });
    
    
    
     $('#ds_codigo_cupon').change(function () {
         var ds_codigo_cupon=document.getElementById('ds_codigo_cupon').value;
            if(ds_codigo_cupon.length > 0){
               $('#labelinput').addClass('state-success');           
           
            }else{
               $('#labelinput').removeClass('state-success');
            }
     });
    
            
        function PintarVerde(){
    
           var ds_codigo_cupon=document.getElementById('ds_codigo_cupon').value;
           
           if(ds_codigo_cupon.length > 0){
               $('#labelinput').addClass('state-success');           
           
           }else{
               $('#labelinput').removeClass('state-success');
           }
           
    
    }
    
    //Pra verficiar el cupon valido.
    function VerificaCodigo(){
    
                var ds_codigo_cupon=document.getElementById('ds_codigo_cupon').value;
                var mn_tax=$mn_tax;
                var mn_subtotal=$mn_amount_p;
                var fg_plan_seleccionado=$fg_plan_seleccionado;
                
                 $.ajax({
                       type: 'POST',
                       url: 'site/verifica_cupon_valido.php',
                       async: false,
                       data: 'ds_codigo_cupon='+ ds_codigo_cupon +
                             '&mn_tax='+ mn_tax +
                             '&fg_plan_seleccionado='+ fg_plan_seleccionado +
                             '&mn_subtotal='+mn_subtotal,
                      
                   }).done(function(result){
                   
                       var resultado = JSON.parse(result);
                       
                       //Codigo no existe en BD
                       if(resultado.fg_error==1){
                         $('#codigo_correcto').addClass('hidden');
                         $('#info_descuento').addClass('hidden');
                         $('#codigo_error').removeClass('hidden');
                         
                         //limpiamos montos
                         $('#cantidad_con_descuento').empty();
                         $('#mn_cant_pagar').empty();
                         $('#mn_cant_pagar').append(resultado.mn_cantidad_con_descuento);
                         $('#mn_cant_pagar2').empty();
                         $('#mn_cant_pagar2').append(resultado.mn_cantidad_con_descuento);
                         
                         
                         //limpiamos input en ceros
                         $('#mn_monto_cupon').val('');
                         $('#fg_cupon').val('');
                         $('#fg_tipo_descuento').val('');
                         $('#fl_cupon').val('');
                         
                          $('#anti_costo').empty();
                          $('#anti_costo').append(resultado.mn_costo_anterior);
                          $('#ant_tax').empty();
                          $('#ant_tax').append(resultado.mn_tax_anterior);
                         
                          $('#mn_amount').empty();
                          $('#mn_amount').val(resultado.mn_subtotal_total_anterior);
                          $('#mn_tax').empty();
                          $('#mn_tax').val(resultado.mn_tax_anterior_stripe);
                         
                          $('#subtota').addClass('hidden');
                       }
                       if(resultado.fg_error==2){
                         $('#codigo_error').addClass('hidden');
                         $('#codigo_correcto').removeClass('hidden');
                       
                         //Colocamos la cantidad con descuento
                          $('#info_descuento').removeClass('hidden');
                          $('#cantidad_con_descuento').empty();
                          $('#cantidad_con_descuento').append(resultado.mn_cantidad_descuento_vista);
                          $('#mn_cant_pagar').empty();
                          $('#mn_cant_pagar').append(resultado.mn_cantidad_con_descuento);
                          
                          $('#mn_cant_pagar2').empty();
                          $('#mn_cant_pagar2').append(resultado.mn_cantidad_con_descuento);
                          $('#mn_monto_cupon').val(resultado.mn_cantidad_descuento);
                          $('#fg_tipo_descuen').empty();
                          $('#fg_tipo_descuen').append(resultado.fg_signo_descuento);
                          $('#fg_cupon').val(1);
                          $('#fg_tipo_descuento').val(resultado.fg_tipo_descuento);
                          $('#fl_cupon').val(resultado.fl_cupon);
                          $('#anti_costo').empty();
                          $('#anti_costo').append(resultado.mn_costo_anterior);
                          $('#ant_tax').empty();
                          $('#ant_tax').append(resultado.mn_nuevo_tax);
                         
						  $('#subtota').removeClass('hidden');
						
						  $('#subtotal_vist').empty();
						  $('#subtotal_vist').append(resultado.mn_subtotal_vista);
						 
                          $('#mn_amount').empty();
                          $('#mn_amount').val(resultado.mn_cantidad_stripe);
                          $('#mn_tax').empty();
                          $('#mn_tax').val(resultado.mn_tax_sripe);
                          
                       }
                       
                       
                   
                   });
                   
    
    
    }
    
    
     $(document).ready(function () {
     
            $('#otra_tarjeta').change(function () {
               
            
                if ($('#otra_tarjeta').is(':checked')){
                
                        $('#btn_misma_tarjeta').addClass('hidden');
                        $('#dtos_nombre').removeClass('hidden');
                        $('#dtos_card').removeClass('hidden');
                        $('#btn_pago').removeClass('hidden');
                    
                }else{
                
                        $('#btn_misma_tarjeta').removeClass('hidden');
                        $('#dtos_nombre').addClass('hidden');
                        $('#dtos_card').addClass('hidden');
                        $('#btn_pago').addClass('hidden');
                }
            
     
            });
            
            
           
            
            
     
     });
    
     function RealizarPagoMismaTarjeta(){
     
       var publickey='".$public_key."';
       var mn_tax = document.getElementById('mn_tax').value;
       var mn_amount=document.getElementById('mn_amount').value;
       var fl_programa_sp=document.getElementById('fl_programa_sp').value;
       var fg_cupon=document.getElementById('fg_cupon').value;
       var currency=document.getElementById('currency').value;
       var fg_misma_tarjeta=1;
       var fg_tipo_descuento=document.getElementById('fg_tipo_descuento').value;
       var fl_cupon=document.getElementById('fl_cupon').value;
       var fg_plan_seleccionado=document.getElementById('fg_plan_seleccionado').value;
       var ds_codigo_cupon=document.getElementById('ds_codigo_cupon').value;
       
       var forma=$('#contenedor_gif');
       
       $('#btn_misma_tarjeta').addClass('hidden');
       $('#gif_misma_tarjeta').removeClass('hidden');
       $('#label_checkbox').addClass('state-disabled');
       
       
       
       //alert(publickey+'mn_tax:'+mn_tax+'mn_amount:'+mn_amount+'fl_programa_sp'+fl_programa_sp+'fg_cupon:'+fg_cupon+'currency:'+currency+'fg_tipodescuento:'+fg_tipo_descuento+'fl_cupon'+fl_cupon+'fg_plan-seleccuionado'+fg_plan_seleccionado);
     
        $.ajax({
                       type: 'POST',
                       url: 'site/charge_misma_tarjeta.php',
                       async: false,
                       data: 'ds_codigo_cupon='+ ds_codigo_cupon +
                             '&publickey='+ publickey +
                             '&mn_tax='+ mn_tax +
                             '&mn_amount='+ mn_amount +
                             '&fl_programa_sp='+ fl_programa_sp +
                             '&fg_cupon='+ fg_cupon +
                             '&currency='+ currency +
                             '&fg_misma_tarjeta='+ fg_misma_tarjeta +
                             '&fg_tipo_descuento='+ fg_tipo_descuento +
                             '&fl_cupon='+ fl_cupon +
                             '&fg_plan_seleccionado='+ fg_plan_seleccionado,
                      
                   }).done(function(result2){
       
                          var stripe_result = JSON.parse(result2);
                          var payment = stripe_result.paid;
                          var error = stripe_result.error;
                          
                         if(error == 0){
                         
                                if(payment==true){
                                  
                                  forma.empty().append(stripe_result.message);
                                }
                                else{
                                   forma.empty().append(stripe_result.message);
			                       $('#gif_misma_tarjeta').addClass('hidden');
                                   $('#btn_misma_tarjeta').removeClass('disabled');
                                   $('#label_checkbox').removeClass('state-disabled');
                                }
                         
                         
                         
                         
                         }else{
                             forma.empty().append(stripe_result.message);
                             $('#btn_misma_tarjeta').removeClass('disabled');
                             $('#gif_misma_tarjeta').addClass('hidden');
                             $('#label_checkbox').removeClass('state-disabled');
                         
                         }
                   
                   
                   });
       
     }
    
     
     
     function RealizarPagoMismaTarjeta_Billing(){
     
     
          var publickey='".$public_key."';    
          var mn_tax = document.getElementById('mn_tax').value;  
          
          
          var mn_amount=document.getElementById('mn_amount').value;
          var currency=document.getElementById('currency').value;
          var ds_descripcion_pago=document.getElementById('ds_descripcion_pago').value;
          
          var fg_tipo_plan=document.getElementById('fg_tipo_plan').value;
          var fg_motivo_pago=document.getElementById('fg_motivo_pago').value;
          var no_licencias_compradas=document.getElementById('no_licencias_compradas').value;

          var forma=$('#contenedor_gif');
  
          
          
          $('#btn_misma_tarjeta').addClass('hidden');
          $('#gif_misma_tarjeta').removeClass('hidden');
          $('#label_checkbox').addClass('state-disabled');
          $('#btn_misma_tarjeta').addClass('hidden');
          
         
          
          
          $.ajax({
                  type:'POST',
                  url:'site/billing_pago_misma_tarjeta.php',
                  async: false,
                  data:'publickey='+ publickey +
                       '&mn_tax='+ mn_tax +
                       '&currency='+ currency +
                       '&mn_amount='+ mn_amount +
                       '&ds_descripcion_pago='+ ds_descripcion_pago +
                       '&fg_tipo_plan='+ fg_tipo_plan +
                       '&fg_motivo_pago='+ fg_motivo_pago +
                       '&no_licencias_compradas='+ no_licencias_compradas,
          
          }).done(function(result){
          
          
               var stripe_result = JSON.parse(result);
               var payment = stripe_result.paid;
               var error = stripe_result.error;
                    
                    
              if(error == 0){
                        if(payment==true){
                            
                            forma.empty().append(stripe_result.message);
                        
                        }else{
                                   $('#gif_misma_tarjeta').addClass('hidden');
                                   $('#btn_misma_tarjeta').removeClass('hidden');
                                   $('#label_checkbox').removeClass('state-disabled');
                                   $('#btn_misma_tarjeta').empty().append(stripe_result.error);
                            
                            
                        }   
                        
               }else{  
               
                     $('#btn_misma_tarjeta').removeClass('hidden');
                     $('#gif_misma_tarjeta').addClass('hidden');
                     $('#label_checkbox').removeClass('state-disabled');
					 $('#btn_misma_tarjeta').empty().append(stripe_result.error);
               }
                     
          
           });
          
          
          
     
     }
     
     
     
     
    
    </script>
        ";
  }


  
  
 /*
  # Funcion muestra la forma de pago de stripe
  function FormaStripe($frm_name="frm_stripe", $mn_amount_p, $mn_tax_p, $url_charge, $fl_programa_sp=0, $ds_descripcion_pago='',$fg_tipo_plan='',$fg_motivo_pago='',$no_licencias_compradas=0,$mn_descuento='',$fg_desbloquear_curso='',$fg_plan_curso=''){
    # Variables Stripe
    $public_key = ObtenConfiguracion(111);    
    $currency = ObtenConfiguracion(113);
    # Verificamos si paga tax
    if($mn_tax_p>0){
      $mn_tax = $mn_amount_p * $mn_tax_p;
      $mn_amount = $mn_amount_p + $mn_tax;

      #Stripe solo permite dos decimales.
      $mn_amount=number_format((float)$mn_amount,2,'.','');
	  $mn_tax=number_format((float)$mn_tax,2,'.','');
    }
    else{
      $mn_amount = $mn_amount_p;
      
    
    }
	  
	  

    # Forma 
    echo "
    <link rel='stylesheet' type='text/css' media='screen' href='".PATH_SELF_CSS."/stripe.css'>
    <div class='row padding-10 text-align-center'>
      <img src='".PATH_SELF_IMG."/creditCards_small.jpg' class='superbox-img' style='width:40%;'>
    </div>";
 //if($mn_tax_p>0){

      echo "
      <div class='row padding-10'>
          <div class='alert alert-block alert-info'>
      ";
      
        if($fg_tipo_plan){
            $mn_porc_tax=$mn_tax_p*100;
            
			if(empty($mn_tax))
            $mn_tax=0;
			
            
            
            
            echo"
             <h4> ".ObtenEtiqueta(1723)."</h4>
             <hr style='margin:3px;'>
             <div class='row'>
               <div class='col-md-7'>
                    $ds_descripcion_pago
               </div>
               <div class='col-md-3 text-right'>
			       ".ObtenEtiqueta(1750).":<br/>
                   ".ObtenEtiqueta(1712).":<br/>
              ";
           
                echo" ".ObtenEtiqueta(1713)." (".$mn_porc_tax."%):<br/>";
            
            
            echo"  ".ObtenEtiqueta(1714).":
               </div>
               
               <div clas='col-md-2'>
			         ".$mn_descuento." % <br/>
                     $".number_format($mn_amount_p,2)." <br/>";
                    
                   echo" $".number_format($mn_tax,2)." <br/>";
                     
            echo"    $".number_format($mn_amount,2)."  
               </div>
               
             </div>
        
            ";
        }else{
             echo"
                    <h6 class='alert-heading no-margin'>Payment Details:</h6>";
             echo"
                    <ul class='list-unstyled'>";
             
            if($fg_desbloquear_curso){
                 if(empty($mn_tax))
                 $mn_tax=0;
                
                
                echo"<li class='txt-color-black'><strong><i class='fa fa-check'></i> <i>Cost: $".$mn_amount_p." ".$currency."</i></strong></li>";
            }else{
                echo" <li class='txt-color-black'><strong><i class='fa fa-check'></i> <i>Certified cost: $".$mn_amount_p." ".$currency."</i></strong></li> ";
            }     
                               
            echo"     <li class='txt-color-black'><strong><i class='fa fa-check'></i> <i>".ObtenEtiqueta(1713).": $".$mn_tax." ".$currency."</i></strong></li>
                      <li class='txt-color-red'><strong><i class='fa fa-plus'></i> <i>Total cost: $".$mn_amount." ".$currency."</i></strong></li>
                    </ul>";      
            
            
        }
       echo"
          </div>
      </div>";
      
       
      
    //}
    echo "
    <form id='".$frm_name."'>
      <div class='group' >
        <label style='font-size: 14px;'>
          <span>".ObtenEtiqueta(1707)."</span>
          <input name='cardholder-name' class='field' placeholder='Jane Doe' />
        </label>
        <label style='display:none;'>
          <span>Phone</span>
          <input class='field' placeholder='(123) 456-7890' type='tel' />
        </label>
      </div>
      <div class='group'>
        <label style='font-size: 14px;'>
          <span>".ObtenEtiqueta(1708)." </span>
          <div id='card-element' class='field'></div>
        </label>
      </div>";
      #Solo si tiene plan en el curso.
      if(!empty($fg_plan_curso))
          echo "<input type='hidden' id='fg_plan_curso' name='fg_plan_curso' value='".$fg_plan_curso."'> ";
    
      # Si mandamos el programa lo agregamos a la forma
      if(!empty($fl_programa_sp))
        echo "<input type='hidden' id='fl_programa_sp' name='fl_programa_sp' value='".$fl_programa_sp."'>";   
      # Enviamos el tax
      if($mn_tax_p>0)
        echo "<input type='hidden' id='mn_tax' name='mn_tax' value='".Conv_Dollars_Stripe($mn_tax)."'>";
    echo "
      <input type='hidden' id='mn_amount' name='mn_amount' value='".Conv_Dollars_Stripe($mn_amount)."'>
      <input type='hidden' id='currency' name='currency' value='".$currency."'> 
      <input type='hidden' id='ds_descripcion_pago' name='ds_descripcion_pago' value='".$ds_descripcion_pago."'>
      <input type='hidden' id='fg_tipo_plan' name='fg_tipo_plan' value='".$fg_tipo_plan."'>
	  <input type='hidden' id='fg_motivo_pago' name='fg_motivo_pago' value='".$fg_motivo_pago."'>
	  <input type='hidden' id='no_licencias_compradas' name='no_licencias_compradas' value='".$no_licencias_compradas."'>
      <div class='outcome' style='padding-top:0px; min-height:0px;'>
        <div class='error' role='alert'></div>
        <div class='success'>
          Your Payment Correct!!!
        </div>
      </div>
	  ";
    
    #fomateamos la cantidad.
    if($fg_tipo_plan)
    $mn_amount=number_format($mn_amount,2);
    
    echo"
	  
	  <div class='col-md-12 text-center hidden' id='presenta_gif'><h5><img src='img/loading_stripe.gif' style='height:40px;'><strong> ".ObtenEtiqueta(1738)."</strong></h5> </div>
      
	  
      <button type='submit' class='button2' id='btn_pago' style='border-radius:16px;'><strong>".ObtenEtiqueta(1710)." $".$mn_amount." ".$currency." </strong></button>      
    </form>
    <script>
	
	   
    var stripe = Stripe('".$public_key."');
    var elements = stripe.elements();

    var card = elements.create('card', {
      style: {
        base: {
          iconColor: '#666EE8',
          color: '#31325F',
          lineHeight: '40px',
          fontWeight: 300,
          fontFamily: '\"Helvetica Neue\", Helvetica, sans-serif',
          fontSize: '15px',

          '::placeholder': {
            color: '#8898aa',
          },
        },
      }
    });
    card.mount('#card-element');

    function setOutcome(result) {
	  
	
      var successElement = document.querySelector('.success');
      var errorElement = document.querySelector('.error');
      successElement.classList.remove('visible');
      errorElement.classList.remove('visible');

      if (result.token) { 

 $('#presenta_gif').removeClass('hidden');
		$('#btn_pago').addClass('hidden');
	  
        // Use the token to create a charge or a customer
        // https://stripe.com/docs/charges              
        // Token of Stripe
        var token = result.token.id;
        var forma = $('#".$frm_name."');
        // Agregamos el token a la forma
        forma.append($('<input type=\"hidden\" name=\"stripeToken\" name=\"stripeToken\" />').val(token));
        // datos de la forma
        var frm_stripe = forma.serialize();
		
		        
       
        $.ajax({
          type: 'POST',
          url: '".$url_charge."',
          async: false,
          data: frm_stripe,
        })
        .done(function(result2){
          var stripe_result = JSON.parse(result2);
          var payment = stripe_result.paid;
          var error = stripe_result.error;
          
          if(error == 0){
            if(payment==true){
              // successElement.classList.add('visible');
              // Send menssage
              forma.empty().append(stripe_result.message);
              // actualizamos la tabla
              $('#tbl_mycourses').DataTable().ajax.reload();
            }
            else{
              errorElement.textContent = '<strong>Error on your payment,</strong> Llease review data your card!!! ';
              errorElement.classList.add('visible');
			   $('#presenta_gif').addClass('hidden');
               $('#btn_pago').removeClass('hidden');
            }
          }
          else{
            errorElement.textContent = error;
            errorElement.classList.add('visible');
			 $('#presenta_gif').addClass('hidden');
             $('#btn_pago').removeClass('hidden');
          }
          
        });
      } else if (result.error) {
        errorElement.textContent = result.error.message;
        errorElement.classList.add('visible');
      }
    }

    card.on('change', function(event) {
      setOutcome(event);
    });

    document.querySelector('#frm_stripe').addEventListener('submit', function(e) {
      e.preventDefault();
      var form = document.querySelector('#frm_stripe');
      var extraDetails = {
        name: form.querySelector('input[name=cardholder-name]').value,
        amount: form.querySelector('input[name=mn_amount]').value,
        currency: form.querySelector('input[name=currency]').value
      };
      stripe.createToken(card, extraDetails).then(setOutcome);
    });
    </script>
        ";
  }
 */
  # Stripe recibe centavos
  # Es necesario convertir el monto a centavos para que stripe respete la cantidad
  function Conv_Dollars_Stripe($monto){
    
    $mn_dollar = $monto * 100;
    
    return $mn_dollar;
    
  }
  
  # Los usuarios canadiense pagan tax
  # Parametro pais puede cambiar en un futuro podria cobrarle a distintos paises
  function Tax_Can_User($fl_usuario, $pais_tax=38){
    # Verificamos si el usuario pagaria tax
    $Query  = "SELECT  b.fl_pais, b.ds_state ";
    $Query .= "FROM c_usuario a ";
    $Query .= "JOIN k_usu_direccion_sp b ON(a.fl_usuario=b.fl_usuario_sp) ";
    $Query .= "WHERE a.fl_usuario=$fl_usuario ";
    $row = RecuperaValor($Query);
    $fl_pais = $row[0];
    $fl_provincia = $row[1];
    # Si el pais de canada paga tax
    if($fl_pais==$pais_tax){
      # Obtenemos la provincia
      $row0 = RecuperaValor("SELECT mn_tax FROM k_provincias WHERE fl_provincia=$fl_provincia");
      $mn_tax = $row0[0]/100;
    }
    else{
      $mn_tax = 0.0;
    }
    
    # Return tax
    return $mn_tax;
  }

  # Funcion para obtener el espacio en disco de un directorio
  function GetDirectorySize($url){
    $bytestotal = 0;
    $path = realpath($url);
    if($path!==false && $path!='' && file_exists($path)){
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
        }
    }
    return $bytestotal;
  }

  # Funcion para obtener el espacio que etsa ocupando un usuario
  function File_Size($url, $decimales = 2, $fl_usuario = 0)
  {
    $bytestotal = GetDirectorySize($url);
    $peso =  $bytestotal;
    //$peso = filesize($url);
    $clase = array(" Bytes", " KB", " MB", " GB", " TB");
    if ($peso != 0){
      $pesoo = round($peso / pow(1024, ($i = floor(log($peso, 1024)))), $decimales) . $clase[$i];
    } else {
       $pesoo = 0;
    }
    
    # Actualiza  los datos del usuario
    if(!empty($fl_usuario))
      EjecutaQuery("UPDATE c_usuario SET no_usage='".$pesoo."' WHERE fl_usuario=$fl_usuario ");
    else
      return $pesoo;
  }
?>
