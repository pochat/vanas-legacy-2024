<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion();
  # Variable initialization to avoid errors
  $programas_bd=NULL;
  $programas_pre=NULL;
  $programa_sig=NULL;
  $courses_code_bd=NULL;
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CLIB_SP, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  # Variable initiaization(Eventos para validacion de campos) to avoid errors
  $val_camp_obl_1 = 'onblur="ValidaCamposObligatorios(\'nb_programa\', this.value);"';
  $val_camp_obl_2 = 'onblur="ValidaCamposObligatorios(\'no_creditos\', this.value);"';
  $val_camp_obl_3 = 'onblur="ValidaCamposObligatorios(\'no_orden\', this.value);"';
  $val_camp_obl_4 = 'onblur="ValidaCamposObligatorios(\'no_horas\', this.value);"';
  $val_camp_obl_5 = 'onblur="ValidaCamposObligatorios(\'no_semanas\', this.value);"';
  $val_camp_obl_6 = 'onblur="ValidaCamposObligatorios(\'ds_credential\', this.value);"';
  $val_camp_obl_7 = 'onchange="ValidaCamposObligatorios(\'cl_delivery\', this.value);"';
  $val_camp_obl_8 = 'onchange="ValidaCamposObligatorios(\'cl_type\', this.value);"';
  
  # Inicializa variables
  if(empty($fg_error)) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos

        $Query  = "SELECT nb_programa, nb_programa_esp, nb_programa_fra, ds_duracion, ds_tipo, no_orden, no_grados, fl_template, fg_fulltime, no_creditos, nb_thumb, fg_taxes, fg_nuevo_programa, ds_programa, ds_programa_esp, ds_programa_fra, ds_learning, ds_learning_esp, ds_learning_fra, ds_metodo, ds_metodo_esp, ds_metodo_fra, ds_requerimiento, ds_requerimiento_esp, ds_requerimiento_fra, ds_course_code, fg_level, fg_obligatorio,fg_publico,no_email_desbloquear,mn_precio,ds_contenido,no_dias_trial,no_dias_pago ";
        $Query .= "FROM c_programa_sp ";
        $Query .= "WHERE fl_programa_sp = $clave";
        $row = RecuperaValor($Query);

        $nb_programa = str_texto($row['nb_programa']);
        $nb_programa_esp = str_texto($row['nb_programa_esp']);
        $nb_programa_fra = str_texto($row['nb_programa_fra']);
        $ds_duracion = str_texto($row['ds_duracion']);
        $ds_tipo = str_texto($row['ds_tipo']);
        $no_orden = $row['no_orden'];
        $no_grados = $row['no_grados'];
        $fl_template = $row['fl_template'];
        $fg_fulltime = $row['fg_fulltime'];
        $no_creditos = $row['no_creditos'];
        $nb_thumb = str_texto($row['nb_thumb']);
        $fg_taxes = $row['fg_taxes'];
        $fg_nuevo_programa = $row['fg_nuevo_programa'];
        $ds_programa = str_texto($row['ds_programa']);
        $ds_programa_esp = str_texto($row['ds_programa_esp']);
        $ds_programa_fra = str_texto($row['ds_programa_fra']);
        $ds_learning = str_texto($row['ds_learning']);
        $ds_learning_esp = str_texto($row['ds_learning_esp']);
        $ds_learning_fra = str_texto($row['ds_learning_fra']);
        $ds_metodo = str_texto($row['ds_metodo']);
        $ds_metodo_esp = str_texto($row['ds_metodo_esp']);
        $ds_metodo_fra = str_texto($row['ds_metodo_fra']);
        $ds_requerimiento = str_texto($row['ds_requerimiento']);
        $ds_requerimiento_esp = str_texto($row['ds_requerimiento_esp']);
        $ds_requerimiento_fra = str_texto($row['ds_requerimiento_fra']);
        $ds_course_code = str_texto($row['ds_course_code']);
        $nb_lvl = str_texto($row['fg_level']);
        $fg_obligatorio = $row['fg_obligatorio'];
        $fg_publicar=$row['fg_publico'];
        $no_email=$row['no_email_desbloquear'];
        $mn_precio=$row['mn_precio'];
        $ds_contenido_curso=str_texto($row['ds_contenido']);
        $no_dias_trial=$row['no_dias_trial'];
        $no_dias_pago=$row['no_dias_pago'];
      
        if(empty($no_email))
            $no_email=null;
        if(empty($mn_precio))
            $mn_precio=null;
        if(empty($no_dias_trial))
            $no_dias_trial=null;
        if(empty($no_dias_pago))
            $no_dias_pago=null;
      
        $Query  = "SELECT no_horas, no_horas_week, no_semanas, ds_credential, cl_delivery, ds_language, cl_type, no_workload, fg_board ";
        $Query .= "FROM k_programa_detalle_sp ";
        $Query .= "WHERE fl_programa_sp = $clave ";
        $row = RecuperaValor($Query);

        # Datos pagos y para contrato
        $no_horas = $row['no_horas'];
        $no_horas_week = $row['no_horas_week'];
        $no_semanas = $row['no_semanas'];
        $ds_credential = $row['ds_credential'];
        $cl_delivery = $row['cl_delivery'];
        $ds_language = $row['ds_language'];
        $cl_type = $row['cl_type'];
        $workload = $row['no_workload'];
        $fg_board = $row['fg_board'];

      # Obtenemos los programas de la clase global
      $rs_g = EjecutaQuery("SELECT fl_grado FROM k_grade_programa_sp WHERE fl_programa_sp = $clave");

      for($i_g=0;$i_g<$row_g=RecuperaRegistro($rs_g);$i_g++){
        $programas_bd .= $row_g[0].",";
      }
      $explosion_bd = explode(",", $programas_bd);
      $programas_bd = $explosion_bd;  
      
      # Programas prerequisito
      $rs = EjecutaQuery("SELECT fl_programa_sp_rel FROM k_relacion_programa_sp WHERE fl_programa_sp_act = $clave AND fg_puesto = 'ANT'");
      
      for($i=0;$i<$row=RecuperaRegistro($rs);$i++){
        $programas_pre .= $row[0].",";
      }
      $programas_pre = explode(",", $programas_pre);  
      
      # Programas siguientes
      $rs_s = EjecutaQuery("SELECT fl_programa_sp_rel FROM k_relacion_programa_sp WHERE fl_programa_sp_act = $clave AND fg_puesto = 'SIG'");
      
      for($i_s=0;$i_s<$row_s=RecuperaRegistro($rs_s);$i_s++){
        $programa_sig .= $row_s[0].",";
      }
      $programa_sig = explode(",", $programa_sig); 

      # Obtenemos sus categorias principales
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'CAT'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_tags[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_tags[$i] = $registro[1];
        $i++ ;
      }  
      if($i != 0){
        $cadena = "";
        foreach($nb_tags as $id=>$nb_tags){
          $cadena .= $nb_tags;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_tags = $cadena;
      }

      # Obtenemos sus categorias tipo hardware
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'HAR'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_har[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_har[$i] = $registro[1];
        $i++ ;
      }    
      if($i != 0){
        $cadena = "";
        foreach($nb_har as $id=>$nb_har){
          $cadena .= $nb_har;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_har = $cadena;
      }

      # Obtenemos sus categorias tipo software
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'SOF'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_sof[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_sof[$i] = $registro[1];
        $i++ ;
      }     
      if($i != 0){
        $cadena = "";
        foreach($nb_sof as $id=>$nb_sof){
          $cadena .= $nb_sof;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_sof = $cadena;
      }

      # Obtenemos sus categorias tipo course code
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'CCE'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_cce[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_cce[$i] = $registro[1];
        $i++ ;
      }    
      if($i != 0){
        $cadena = "";
        foreach($nb_cce as $id=>$nb_cce){
          $cadena .= $nb_cce;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_cce = $cadena;
      }

      # Obtenemos sus categorias tipo course series
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'CSS'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_css[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_css[$i] = $registro[1];
        $i++ ;
      }   
      if($i != 0){
        $cadena = "";
        foreach($nb_css as $id=>$nb_css){
          $cadena .= $nb_css;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_css = $cadena;
      }

      #Couses Code
      $rs_cou = EjecutaQuery("SELECT  fl_course_code FROM k_course_code_prog_fame C 
                                      WHERE fl_programa_sp=$clave  ");
      $courses_code_bd=NULL;
      for($i_c=0;$i_c<$row_co=RecuperaRegistro($rs_cou);$i_c++){
          $courses_code_bd .= $row_co[0].",";
      }
      $courses_code_bd = explode(",", $courses_code_bd);

      # Obtenemos sus categorias tipo software
      $query = "SELECT k.fl_cat_prog_sp, c.nb_categoria FROM k_categoria_programa_sp k, c_categoria_programa_sp c WHERE k.fl_programa_sp = $clave AND k.fl_cat_prog_sp=c.fl_cat_prog_sp AND c.fg_categoria = 'FOS'";
      $result = EjecutaQuery($query);
      $i = 0;
      // while($registro = mysql_fetch_array($result)){
      //   $nb_fos[$i] = $registro[1];
      //   $i++ ;
      // }
      foreach ($result as $registro) {
        $nb_fos[$i] = $registro[1];
        $i++ ;
      }    
      if($i != 0){
        $cadena = "";
        foreach($nb_fos as $id=>$nb_fos){
          $cadena .= $nb_fos;
          if($id!=($i-1))
            $cadena .= ",";
        }
        $nb_fos = $cadena;
      }

    } else { // Alta, inicializa campos
       
      $nb_programa = "";
      $ds_duracion = "";
      $ds_tipo = "";
      $no_creditos = "0";
      $no_orden = "0";
      $no_grados = "4";
      $fl_template = 0;
      $fg_fulltime = 1;
      $mn_class='';
      $mn_extra_class='';
      $nb_thumb='';
      $fg_taxes='0';
      $nb_tags='';
      $nb_sof='';
      $nb_fos='';
      $nb_har='';
      $nb_lvl='';
      $nb_cce='';
      $nb_css='';
      $ds_programa = '';
      $fg_nuevo_programa = '0';
      $ds_learning = '';
      $ds_metodo = '';
      $ds_requerimiento = '';
      $ds_course_code = '';
      $no_horas = '';
      $no_semanas = '';
      $ds_credential = '';
      $cl_delivery = 'O';
      $ds_language = '';
      $cl_type = 2;
      $no_horas_week = '';
      $workload = '';
      $fg_board = 1;
      $fg_obligatorio = 0;
      $explosion_bd = explode(",", $programas_bd);
      $programas_bd = $explosion_bd;
      $explosion_pre = explode(",", $programas_pre);
      $programas_pre = $explosion_pre;
      $explosion_sig = explode(",", $programa_sig);
      $programa_sig = $explosion_sig;
      $explosion_code_bd=explode(",", $courses_code_bd);
      $courses_code_bd=$explosion_code_bd;
      
      $nb_pagina = "";
      $ds_pagina = "";
      $ds_titulo = "";
      $tr_titulo = "";
      $ds_contenido = "";
      $tr_contenido = "";
      $cl_pagina = 0;
      
      # Eventos para validacion de campos
      $val_camp_obl_1 = 'onblur="ValidaCamposObligatorios(\'nb_programa\', this.value);"';
      $val_camp_obl_2 = 'onblur="ValidaCamposObligatorios(\'no_creditos\', this.value);"';
      $val_camp_obl_3 = 'onblur="ValidaCamposObligatorios(\'no_orden\', this.value);"';
      $val_camp_obl_4 = 'onblur="ValidaCamposObligatorios(\'no_horas\', this.value);"';
      $val_camp_obl_5 = 'onblur="ValidaCamposObligatorios(\'no_semanas\', this.value);"';
      $val_camp_obl_6 = 'onblur="ValidaCamposObligatorios(\'ds_credential\', this.value);"';
      // $val_camp_obl_7 = 'onchange="ValidaCamposObligatorios(\'cl_delivery\', this.value);"';
      // $val_camp_obl_8 = 'onchange="ValidaCamposObligatorios(\'cl_type\', this.value);"';
      
    }

    $nb_programa_err = "";
    $ds_duracion_err = "";
    $ds_tipo_err = "";
    $no_orden_err = "";
    $no_grados_err = "";
    $fl_template_err = "";
    $no_horas_err = "";
    $no_semanas_err = "";
    $ds_credential_err = "";
    $no_creditos_err = "";
    $ds_programa_err = "";
    $ds_learning_err = "";
    $ds_metodo_err = "";
    $ds_requerimiento_err = "";

  } else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_programa = RecibeParametroHTML('nb_programa');
    $nb_programa_esp = RecibeParametroHTML('nb_programa_esp');
    $nb_programa_fra = RecibeParametroHTML('nb_programa_fra');
    $nb_programa_err = RecibeParametroNumerico('nb_programa_err');
    $ds_duracion = RecibeParametroHTML('ds_duracion');
    $ds_duracion_err = RecibeParametroNumerico('ds_duracion_err');
    $ds_tipo = RecibeParametroHTML('ds_tipo');
    $ds_tipo_err = RecibeParametroNumerico('ds_tipo_err');
    $no_orden = RecibeParametroNumerico('no_orden');
    $no_orden_err = RecibeParametroNumerico('no_orden_err');
    $no_grados = RecibeParametroNumerico('no_grados');
    $no_grados_err = RecibeParametroNumerico('no_grados_err');
    $no_creditos = RecibeParametroFlotante('no_creditos');
    $no_creditos_err = RecibeParametroNumerico('no_creditos_err');
    $fg_fulltime = RecibeParametroBinario('fg_fulltime');
    $fg_taxes = RecibeParametroBinario('fg_taxes');
    $nb_thumb_err = RecibeParametroNumerico('nb_thumb_err');
    $nb_thumb = RecibeParametroHTML('nb_thumb');
    $cont_array = RecibeParametroNumerico('cont_array');
    $nb_tags = RecibeParametroHTML('nb_tags2');
    $nb_sof = RecibeParametroHTML('nb_sof2');
    $nb_fos = RecibeParametroHTML('nb_fos2');
    $nb_har = RecibeParametroHTML('nb_har2');
    $nb_lvl = RecibeParametroHTML('nb_lvl');
    $nb_cce = RecibeParametroHTML('nb_cce2');
    $nb_css = RecibeParametroHTML('nb_css2');
    $no_horas = RecibeParametroFlotante('no_horas');
    $no_horas_err = RecibeParametroNumerico('no_horas_err');
    $no_horas_week = RecibeParametroNumerico('no_horas_week');
    $no_horas_week_err = RecibeParametroNumerico('no_horas_week_err');
    $no_semanas = RecibeParametroNumerico('no_semanas');
    $no_semanas_err = RecibeParametroNumerico('no_semanas_err');
    $ds_credential = RecibeParametroHTML('ds_credential');
    $ds_credential_err = RecibeParametroNumerico('ds_credential_err');
    $cl_delivery = RecibeParametroHTML('cl_delivery');
    $ds_language = RecibeParametroHTML('ds_language');
    $cl_type = RecibeParametroNumerico('cl_type');
    $fl_template = RecibeParametroNumerico('fl_template');
    $fl_template_err = RecibeParametroNumerico('fl_template_err');
    $workload = RecibeParametroHTML('workload');
    $ds_programa = RecibeParametroHTML('ds_programa');
    $ds_programa_esp = RecibeParametroHTML('ds_programa_esp');
    $ds_programa_fra = RecibeParametroHTML('ds_programa_fra');
    $fl_programa = RecibeParametroNumerico('fl_programa');  
    $fg_nuevo_programa = RecibeParametroNumerico('fg_nuevo_programa');  
    $ds_learning = RecibeParametroHTML('ds_learning');
    $ds_learning_esp = RecibeParametroHTML('ds_learning_esp');
    $ds_learning_fra = RecibeParametroHTML('ds_learning_fra');
    $ds_metodo = RecibeParametroHTML('ds_metodo');
    $ds_metodo_esp = RecibeParametroHTML('ds_metodo_esp');
    $ds_metodo_fra = RecibeParametroHTML('ds_metodo_fra');
    $ds_requerimiento = RecibeParametroHTML('ds_requerimiento');
    $ds_requerimiento_esp = RecibeParametroHTML('ds_requerimiento_esp');
    $ds_requerimiento_fra = RecibeParametroHTML('ds_requerimiento_fra');  
    $ds_course_code = RecibeParametroHTML('ds_course_code');  
    $ds_course_code_err = RecibeParametroNumerico('ds_course_code_err');
    $fg_obligatorio = RecibeParametroBinario('fg_obligatorio');
    
    $nb_pagina = RecibeParametroHTML('nb_pagina');
    $ds_pagina = RecibeParametroHTML('ds_pagina');
    $ds_titulo = RecibeParametroHTML('ds_tipo');
    $tr_titulo = RecibeParametroHTML('tr_titulo');
    $ds_contenido = RecibeParametroHTML('ds_contenido');
    $tr_contenido = RecibeParametroHTML('tr_contenido');
    
    $reg_programa = RecibeParametroHTML('reg_programa');
    $programas_bd = explode(",", $reg_programa);  
    
    $courses_code_bd=explode(",", !empty($reg_course_code)?$reg_course_code:NULL);
    
    $reg_programa_pre = RecibeParametroHTML('reg_programa_pre');
    $programas_pre = explode(",", $reg_programa_pre);  
    
    $reg_programa_sig = RecibeParametroHTML('reg_programa_sig');
    $programa_sig = explode(",", $reg_programa_sig);  
    
    $tab_prog_err = RecibeParametroNumerico('tab_prog_err');
    
    if(!empty($tab_prog_err))
      $style_tab_prog = "style='color:#b94a48;'";
    else
      $style_tab_prog = "style='color:#333;'";
    
    $ds_programa_err = RecibeParametroNumerico('ds_programa_err');
    $ds_learning_err = RecibeParametroNumerico('ds_learning_err');
    $ds_metodo_err = RecibeParametroNumerico('ds_metodo_err');
    $ds_requerimiento_err = RecibeParametroNumerico('ds_requerimiento_err');
    $tab_outline_err = RecibeParametroNumerico('tab_outline_err');
    if(!empty($tab_outline_err))
      $style_outliner_prog = "style='color:#b94a48;'";
    else
      $style_outliner_prog = "style='color:#333;'";

    $tab_cats_err = RecibeParametroNumerico('tab_cats_err');
    if(!empty($tab_cats_err))
      $style_cats = "style='color:#b94a48;'";
    else
      $style_cats = "style='color:#333;'";    
    
    $fg_publicar=RecibeParametroBinario('fg_publicar');
    $no_email=RecibeParametroNumerico('no_email');
	$mn_precio=RecibeParametroFlotante('mn_precio');
    
    $ds_contenido_curso=RecibeParametroHTML('ds_contenido_curso');
	$no_dias_trial=RecibeParametroNumerico('no_dias_trial');
    $no_dias_pago=RecibeParametroNumerico('no_dias_pago');
  }
  
  # fixed page
  $Queryf  = "SELECT nb_pagina, ds_pagina, ds_titulo, tr_titulo, ds_contenido, tr_contenido, cl_pagina_sp ";
  $Queryf .= "FROM c_pagina_sp WHERE fl_programa_sp=$clave ";
  $rowf = RecuperaValor($Queryf);
  $nb_pagina = !empty($rowf[0])?$rowf[0]:NULL;
  $ds_pagina = !empty($rowf[1])?$rowf[1]:NULL;
  $ds_titulo = !empty($rowf[2])?$rowf[2]:NULL;
  $tr_titulo = !empty($rowf[3])?$rowf[3]:NULL;
  $ds_contenido = !empty($rowf[4])?$rowf[4]:NULL;
  $tr_contenido = !empty($rowf[5])?$rowf[5]:NULL;
  $cl_pagina = !empty($rowf[6])?$rowf[6]:NULL;
  if(empty($cl_pagina))
    $cl_pagina = 0;
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CLIB_SP);
    
  echo "<script type='text/javascript' src='".PATH_JS."/frmCourses.js.php'></script>";
  
  # Inicia forma de captura
  Forma_Inicia($clave, True);
  if(!empty($fg_error))
    Forma_PresentaError( );
  
 ?>
  <script>
    // Funcion para validar campos obligatorios
    function ValidaCamposObligatorios(campo_actual, valor){
      if(valor){
        $('#div_' + campo_actual).removeClass('row form-group has-error').addClass('row form-group ');            
        document.getElementById(campo_actual).style.backgroundColor = '#FFF';
      }
    }  
    
    // Funcion para validar campos obligatorios categorias
    function ValidaCamposObligatoriosCats(ul, input){
      var valor = document.getElementById(input).value;
      if(valor){
        document.getElementById(ul).style.backgroundColor = '#FFF';
        document.getElementById(ul).style.borderColor = '#b94a48';
        document.getElementById(ul).style.color = '#b94a48';
      }else{
        document.getElementById(ul).style.backgroundColor = '#FFF0F0';
        document.getElementById(ul).style.borderColor = '#b94a48';
        document.getElementById(ul).style.color = '#b94a48';
      }
    }    
    //Vaida solo numeros
    function soloNumeros(e){
        var key = window.Event ? e.which : e.keyCode
        // backspace
        if (key == 8) return true
        return (key >= 48 && key <= 57)
    }
    //Valida numero y dos decimales
    function NumeroDecimal(e, field) {
        key = e.keyCode ? e.keyCode : e.which
        // backspace
        if (key == 8) return true
        // 0-9
        if (key > 47 && key < 58) {
            if (field.value == "") return true
            regexp = /.[0-9]{2}$/
            return !(regexp.test(field.value))
        }
        // .
        if (key == 46) {
            if (field.value == "") return false
            regexp = /^[0-9]+$/
            return regexp.test(field.value)
        }
        // other key
        return false
 
    }
  </script>
 
<!-- widget content -->
  <div class="widget-body">
      <ul id="myTab1" class="nav nav-tabs bordered">
          <li class="active">
            <a href="#programs" data-toggle="tab">
              <span <?php echo !empty($style_tab_prog)?$style_tab_prog:NULL; ?> id="tab_prog"><i class="fa fa-fw fa-lg fa-info-circle"></i><?php echo ObtenEtiqueta(406) ?></span>
            </a>
          </li>
          <li>
            <a href="#description" data-toggle="tab">
              <span <?php echo !empty($style_cats)?$style_cats:NULL; ?> id=""><i class="fa fa-fw fa-lg fa-tags "></i><?php echo " ".ObtenEtiqueta(1294) ?></span>
            </a>
          </li>
          <li>
            <a href="#outline" data-toggle="tab">
              <span <?php echo !empty($style_outliner_prog)?$style_outliner_prog:NULL; ?> id=""><i class="fa fa-fw fa-lg fa-file-text-o "></i><?php echo " ".ObtenEtiqueta(1295) ?></span>
            </a>
          </li>
          <li>
            <a href="#student_library" data-toggle="tab"><i class="fa fa-fw fa-lg fa-folder-open "></i><?php echo " ".ObtenEtiqueta(1982) ?></a>
          </li>
					<li> 
					  <a href="#Config" data-toggle="tab"><i class="fa fa-magic" aria-hidden="true"></i><?php echo " ".ObtenEtiqueta(2094) ?> </a>
					</li>
        </ul>

                <div id="myTabContent1" class="tab-content padding-10 no-border">
                  <div class="tab-pane fade in active" id="programs">
                    <?php include "clibrary_frm_program_information.php"; ?>
                  </div>

                  <div class="tab-pane fade" id="description">
                    <br>
                    <div class="row">                      
                      <div class="col-lg-4">
                        <?php
                          Forma_CampoCheckbox('', 'fg_nuevo_programa', $fg_nuevo_programa, "<strong>".ObtenEtiqueta(1251)."</strong>",'', true, '', 'right', 'col-sm-4', 'col-sm-8');
                        ?>
                      </div>
                      <div class="col-lg-4">
                        <?php
                          Forma_CampoCheckbox('', 'fg_board', !empty($fg_board)?$fg_board:NULL, "<strong>".ObtenEtiqueta(1938)."</strong>",'', true, '', 'right', 'col-sm-4', 'col-sm-8');
                        ?>
                      </div>
                      <div class="col-lg-4">
                        <?php
                          Forma_CampoCheckbox('', 'fg_obligatorio', $fg_obligatorio, "<strong>".ObtenEtiqueta(2001)."</strong>",'', true, '', 'right', 'col-sm-4', 'col-sm-8');
                        ?>
                      </div>
                    </div>
                    
                    <br>

                    <!-- Field of Study -->
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <?php
                      $query = "SELECT fl_cat_prog_sp, nb_categoria FROM c_categoria_programa_sp WHERE fg_categoria = 'FOS' ";
                      $result = EjecutaQuery($query);
                      $i = 0;
                      // while($registro = mysql_fetch_array($result)){
                      //       $tit_col[$i] = $registro[1];
                      //       $i++ ;
                      // }
                      foreach ($result as $registro) {
                        $tit_col[$i] = $registro[1];
                        $i++;
                      }
                      ?>
                      <script type="text/javascript">
                        var arrayJSFos=<?php echo json_encode($tit_col);?>;
                        $(function(){
                          var sampleTagsFos = arrayJSFos;
                          $('#nb_fos_ul').tagit({
                            availableTags: sampleTagsFos,
                            singleField: true,
                            singleFieldNode: $('#nb_fos')
                          });
                        });
                      </script>

                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <label class="col-sm-2 control-label"><strong><div id="lbl_fos">* <?php echo ObtenEtiqueta(1306); ?></div></strong></label>
                          <div class="col-sm-8">
                            <input type="hidden" name="nb_fos" id="nb_fos" value="<?php echo $nb_fos; ?>">
                            <ul id="nb_fos_ul" onkeyup="ValidaCamposObligatoriosCats('nb_fos_ul', 'nb_fos');"></ul>
                            <div id="err_fos"></div>
                            <div class="note">
                              <strong></strong><i><?php echo ObtenEtiqueta(1324); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                    
                    <!-- Categorias -->
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <?php
                      $query = "SELECT fl_cat_prog_sp, nb_categoria FROM c_categoria_programa_sp WHERE fg_categoria = 'CAT' ";
                      $result = EjecutaQuery($query);
                      $i = 0;
                      // while($registro = mysql_fetch_array($result)){
                      //       $tit_col[$i] = $registro[1];
                      //       $i++ ;
                      // }
                      foreach ($result as $registro) {
                        $tit_col[$i] = $registro[1];
                        $i++;
                      }
                      ?>
                      <script type="text/javascript">
                        var arrayJSCat=<?php echo json_encode($tit_col);?>;
                        $(function(){
                          var sampleTagsCat = arrayJSCat;
                          $('#singleFieldTags').tagit({
                            availableTags: sampleTagsCat,
                            singleField: true,
                            singleFieldNode: $('#nb_tags')
                          });
                        });
                      </script>

                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <label class="col-sm-2 control-label"><strong><div id="lbl_tags">* <?php echo ObtenEtiqueta(1307); ?></div></strong></label>
                          <div class="col-sm-8">
                            <input type="hidden" name="nb_tags" id="nb_tags" value="<?php echo $nb_tags; ?>">
                            <ul id="singleFieldTags" onkeyup="ValidaCamposObligatoriosCats('singleFieldTags', 'nb_tags');"></ul>
                            <div id="err_tags"></div>
                            <div class="note">
                              <strong></strong><i><?php echo ObtenEtiqueta(1315); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                  
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col col-xs-12 col-sm-12">
                        <div class='form-group'>
                          <label class='col-sm-2 control-label'><strong>* <?php echo ObtenEtiqueta(1308); ?></strong></label>
                          <div class='col-sm-8 smart-form'>
                            <label class='select'>
                            <?php
                            # Mostramos los programas que tengan alumnos activos
                            $Query  = "SELECT nb_grado, fl_grado FROM k_grado_fame ";                                  
                            CampoSelectBD('fl_programa_nuevo[]', $Query, !empty($p_actual)?$p_actual:NULL, '', False, 'multiple', $programas_bd, 'grados'); 
                            echo "<i></i>";
                            ?>
                            </label>
                            <div id="err_gdo"></div>
                            <div class="note">
                              <i><strong></strong><?php echo ObtenEtiqueta(1319); ?><br></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Hardware -->
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <?php
                      $query = "SELECT fl_cat_prog_sp, nb_categoria FROM c_categoria_programa_sp WHERE fg_categoria = 'HAR' ";
                      $result = EjecutaQuery($query);
                      $i = 0;
                      // while($registro = mysql_fetch_array($result)){
                      //       $tit_col[$i] = $registro[1];
                      //       $i++ ;
                      // }
                      foreach ($result as $registro) {
                        $tit_col[$i] = $registro[1];
                        $i++;
                      }
                      ?>
                      <script type="text/javascript">
                        var arrayJSHAR=<?php echo json_encode($tit_col);?>;
                        $(function(){
                          var sampleTagsHar = arrayJSHAR;
                          $('#hardware_ul').tagit({
                            availableTags: sampleTagsHar,
                            singleField: true,
                            singleFieldNode: $('#nb_har')
                          });
                        });
                      </script>

                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <label class="col-sm-2 control-label"><strong><div id="lbl_har">* <?php echo ObtenEtiqueta(1309); ?></div></strong></label>
                          <div class="col-sm-8">
                            <input type="hidden" name="nb_har" id="nb_har" value="<?php echo $nb_har; ?>">
                            <ul id="hardware_ul" onkeyup="ValidaCamposObligatoriosCats('hardware_ul', 'nb_har');"></ul>
                            <div id="err_har"></div>
                            <div class="note">
                              <i><strong></strong><?php echo ObtenEtiqueta(1320); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                    
                    <!-- Software -->
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <?php
                      $query = "SELECT fl_cat_prog_sp, nb_categoria FROM c_categoria_programa_sp WHERE fg_categoria = 'SOF' ";
                      $result = EjecutaQuery($query);
                      $i = 0;
                      // while($registro = mysql_fetch_array($result)){
                      //       $tit_col[$i] = $registro[1];
                      //       $i++ ;
                      // }
                      foreach ($result as $registro) {
                        $tit_col[$i] = $registro[1];
                        $i++;
                      }
                      ?>
                      <script type="text/javascript">
                        var arrayJSSof=<?php echo json_encode($tit_col);?>;
                        $(function(){
                          var sampleTagsSof = arrayJSSof;
                          $('#software_ul').tagit({
                            availableTags: sampleTagsSof,
                            singleField: true,
                            singleFieldNode: $('#nb_sof')
                          });
                        });
                      </script>

                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <label class="col-sm-2 control-label"><strong><div id="lbl_sof">* <?php echo ObtenEtiqueta(1310); ?></div></strong></label>
                          <div class="col-sm-8">
                            <input type="hidden" name="nb_sof" id="nb_sof" value="<?php echo $nb_sof; ?>">
                            <ul id="software_ul" onkeyup="ValidaCamposObligatoriosCats('software_ul', 'nb_sof');"></ul>
                            <div id="err_sof"></div>
                            <div class="note">
                              <i><strong></strong><?php echo ObtenEtiqueta(1316); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> 
                    
                    <!-- Level -->
                    
                    <div class="row">
                      <div class="col-lg-1">
                      </div>
                      <div class="col-sm-12 col-lg-12">
                        <?php
                          $p_opc = array(ObtenEtiqueta(1317), ObtenEtiqueta(1321), ObtenEtiqueta(1322));
                          $p_val = array('LVB', 'LVI', 'LVA');
                          Forma_CampoSelect(ObtenEtiqueta(1311), False, 'nb_lvl', $p_opc, $p_val, $nb_lvl, '', False, '', 'right', 'col col-sm-2', 'col col-sm-8');
                        ?>
                        <div class="col-sm-2"></div>
                        <div class="col-sm-8" style="padding-left:5px;">
                          <div class="note">
                              <strong></strong><i><?php echo ObtenEtiqueta(1327); ?></i>
                          </div>
                        </div>
                      </div>
                    </div>
                    <br> 

                    <!-- Course Code -->
                    <div class="col-xs-12 col-sm-12">
                      <div id="div_no_semana2" class="row form-group ">
                        <label class="col-sm-2 control-label text-align-right">
                          <div id="lbl_course_code"><strong>* <?php echo ObtenEtiqueta(1312); ?></strong></div>
                        </label>
                        <div class="col-sm-8" style="padding:0px 8px 0px 6px;"> 
                          <div class="smart-form <?php if(!empty($ds_course_code_err)) echo "has-error"; ?>">	
                            <label class="input">
                              <input class="form-control" id="ds_course_code" name="ds_course_code" value="<?php echo $ds_course_code; ?>" maxlength="25" size="12" type="text">
                              <?php
                              if(!empty($ds_course_code_err))
                                echo "<span class='help-block'><i class='fa fa-warning'></i>".ObtenMensaje($ds_course_code_err)."</span>";
                              ?>
                            </label>
                            <div class="note">
                              <i><strong></strong><?php echo ObtenEtiqueta(1318); ?></i>
                            </div>
                          </div>
                        </div>  
                      </div>
                    </div> 
                    
                    <!-- Course Prerequisites -->
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col col-xs-12 col-sm-12">
                        <div class='form-group'>
                          <label class='col-sm-2 control-label'><strong><?php echo ObtenEtiqueta(1313); ?></strong></label>
                          <div class='col-sm-8 smart-form'>
                            <label class='select'>
                            <?php
                            # Mostramos los programas que tengan alumnos activos
                            $Query  = "SELECT CONCAT(nb_programa, ' (code: ', ds_course_code ,')') as programa, fl_programa_sp FROM c_programa_sp ORDER BY nb_programa ";                                  
                            CampoSelectBD('fl_programa_pre[]', $Query, !empty($p_actuals)?$p_actuals:NULL, '', False, 'multiple', $programas_pre, 'programas_pre');
                            echo "<i></i>";
                            ?>
                            </label>
                            <div class="note">
                              <strong></strong><i><?php echo ObtenEtiqueta(1325); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Course Seriados -->
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col col-xs-12 col-sm-12">
                        <div class='form-group'>
                          <label class='col-sm-2 control-label'><strong><?php echo ObtenEtiqueta(1314); ?></strong></label>
                          <div class='col-sm-8 smart-form'>
                            <label class='select'>
                            <?php
                            # Mostramos los programas que tengan alumnos activos
                            $Query_sig  = "SELECT CONCAT(nb_programa, ' (code: ', ds_course_code ,')') as programa, fl_programa_sp FROM c_programa_sp ORDER BY nb_programa "; 
                            CampoSelectBD('fl_programa_sig[]', $Query_sig, !empty($p_actuals2)?$p_actuals2:NULL, '', False, 'multiple', $programa_sig, 'programa_sig');
                            echo "<i></i>";
                            ?>
                            </label>
                            <div class="note">
                              <strong></strong><i><?php echo ObtenEtiqueta(1326); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Maping -->
                    <div class="row">
                      <div class="col-lg-1"></div>
                      <div class="col col-xs-12 col-sm-12">
                        <div class='form-group'>
                          <label class='col-sm-2 control-label'><strong><?php echo ObtenEtiqueta(2056); ?></strong></label>
                          <div class='col-sm-8 smart-form'>
                            <label class='select'>
                            <?php
                            # Mostramos los programas que tengan alumnos activos
                            $Query_course  = "SELECT CONCAT (C.nb_course_code,' (code: ',C.cl_course_code,') - ',P.ds_pais,', ',CASE WHEN E.ds_provincia IS NULL THEN '' ELSE E.ds_provincia END) AS nb_course,C.fl_course_code  
											FROM c_course_code C
											JOIN c_pais P ON P.fl_pais=C.fl_pais
											LEFT JOIN k_provincias E ON E.fl_provincia=C.fl_estado ORDER BY nb_course asc"; 
                            CampoSelectBD('fl_course_code[]', $Query_course, !empty($p_actual3)?$p_actual3:NULL, '', False, 'multiple', $courses_code_bd, 'course_codes');
                            echo "<i></i>";
                            ?>
                            </label>
							<div id="err_courses_code"></div>
                            <div class="note">
                              <strong></strong><i><?php echo ObtenEtiqueta(2057); ?></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
					<!-- Maping -->
				
          <!---Descripcion Curriculum--->
					<div class="row">
			               <div class="col-md-1"> &nbsp;  </div>
			               <div class="col-md-10">
							         <?php  Forma_CampoTinyMCE("", False, 'ds_contenido_curso', (!empty($ds_contenido_curso)?$ds_contenido_curso:NULL), 50, 20, !empty($ds_contenido_curso_err)?$ds_contenido_curso_err:NULL);?>
			               </div>
			               <div class="col-md-1"> &nbsp; </div>
		      </div>
				   <!---end descripcion curriculum--->
              </div>

<div class="tab-pane fade" id="outline">
  <div class="widget-body">
    <!-- Outline Tags for language starts here -->
    <ul id="myTab4" class="nav nav-tabs bordered">
      <li class="active">
        <a href="#s1outlineLang" data-toggle="tab" aria-expanded="true">English</a>
      </li>
      <li class="">
        <a href="#s2outlineLang" data-toggle="tab" aria-expanded="true">Spanish</a>
      </li>
      <li class="">
        <a href="#s3outlineLang" data-toggle="tab" aria-expanded="true">French</a>
      </li>
    </ul>
    <div id="myTabContentLang1" class="tab-content padding-10">
      <!-- Start row for English -->
      <div class="tab-pane fade in active" id="s1outlineLang">
        <div class="row">
          <div class="col-xs-3 col-sm-3">
            <br>
            <div class="bs-example">
              <dl>
                <dt>* <?php echo ObtenEtiqueta(1298); ?></dt>
                <dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1299); ?></span></dd>
              </dl>
            </div>
          </div>
          <div class="col-xs-9 col-sm-9">
            <?php Forma_CampoTinyMCE("", False, 'ds_programa', $ds_programa, 50, 20, $ds_programa_err);?>
          </div>
        </div>
      
        <div class="row">
          <div class="col-xs-3 col-sm-3">
            <br>
            <div class="bs-example">
              <dl>
                <dt>* <?php echo ObtenEtiqueta(1300); ?></dt>
                <dd><span style="color:#9aa7af; font-style: italic;"><?php echo ObtenEtiqueta(1301); ?></span></dd>
              </dl>
            </div>
          </div>
          <div class="col-xs-9 col-sm-9">
            <?php Forma_CampoTinyMCE("", False, 'ds_learning', $ds_learning, 50, 20, $ds_learning_err);?>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-3 col-sm-3">
            <br>
            <div class="bs-example">
              <dl>
                <dt>* <?php echo ObtenEtiqueta(1302); ?></dt>
                <dd><span style="color:#9aa7af; font-style: italic;">
                <?php echo ObtenEtiqueta(1303); ?>
                </span></dd>
              </dl>
            </div>
          </div>
          <div class="col-xs-9 col-sm-9">
            <?php Forma_CampoTinyMCE("", False, 'ds_metodo', $ds_metodo, 50, 20, $ds_metodo_err);?>
          </div>
        </div>
      
        <div class="row">
          <div class="col-xs-3 col-sm-3">
            <br>
            <div class="bs-example">
              <dl>
                <dt>* <?php echo ObtenEtiqueta(1304); ?></dt>
                <dd><span style="color:#9aa7af; font-style: italic;">
                <?php echo ObtenEtiqueta(1305); ?>
                </span></dd>
              </dl>
            </div>
          </div>
          <div class="col-xs-9 col-sm-9">
            <?php Forma_CampoTinyMCE("", False, 'ds_requerimiento', $ds_requerimiento, 50, 20, $ds_requerimiento_err);?>
          </div>
        </div>
      </div>
      <!-- END Row for English -->
      <!-- Start row for Spanish -->
      <div class="tab-pane fade in " id="s2outlineLang">
         <?php include "clibrary_frm_course_esp.php" ?>
      </div>
      <!-- END Row for Spanish -->
      <!-- Start row for French -->
      <div class="tab-pane fade in " id="s3outlineLang">
          <?php include "clibrary_frm_course_fra.php" ?>
      </div>
      <!-- END Row for French -->
    </div>
  </div>
</div>
<!-- Outline Tags for language ends here -->

                  <div class="tab-pane fade" id="student_library">
                    <div class="row">
                        <div class="widget-body">
                          <ul id="myTab2" class="nav nav-tabs bordered">
                            <li class="active">
                              <a href="#s1fame" data-toggle="tab" aria-expanded="true"><?php echo ObtenEtiqueta(2035); ?></a>
                            </li>
                            <li class="">
                              <a href="#s2fame" data-toggle="tab" aria-expanded="true"><?php echo ObtenEtiqueta(2036); ?></a>
                            </li>
                          </ul>
                          <div id="myTabContent2" class="tab-content padding-10">
                            <div class="tab-pane fade in active" id="s1fame">
                              <div class="row">
                                <div class="col-lg-1">
                                </div>
                                <div class="col-sm-6 col-lg-5">
                                  <?php
                                    Forma_CampoTexto(ObtenEtiqueta(1983), false, 'nb_pagina', $nb_pagina, 50, 50, !empty($nb_pagina_err)?$nb_pagina_err:NULL);
                                  ?>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-lg-1">
                                </div>
                                <div class="col-sm-6 col-lg-5">
                                  <?php
                                    Forma_CampoTexto(ObtenEtiqueta(1984), false, 'ds_pagina', $ds_pagina, 50, 50, !empty($ds_pagina_err)?$ds_pagina_err:NULL);
                                  ?>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-lg-1">
                                </div>
                                <div class="col-sm-6 col-lg-5">
                                  <?php
                                    Forma_CampoTexto(ObtenEtiqueta(1985), False, 'ds_titulo', $ds_titulo, 50, 50, !empty($ds_titulo_err)?$ds_titulo_err:NULL);
                                  ?>
                                </div>
                              </div>
                              <!-- widget content -->
                              <div class="widget-body padding-5">
                    
                                <p class="text-align-center">
                                  <strong><?php echo ObtenEtiqueta(1986); ?></strong>
                                </p>
                                <hr class="simple" />
                                <ul id="myTab12" class="nav nav-tabs bordered">
                                  <li class="active">
                                    <a href="#s1fame1" data-toggle="tab" aria-expanded="true"><i class="fa fa-fw fa-lg fa-language"></i><?php echo ObtenEtiqueta(1989); ?> </a>
                                  </li>
                                  <li class="">
                                    <a href="#s2fame2" data-toggle="tab" aria-expanded="false"><i class="fa fa-fw fa-lg fa-language"></i><?php echo ObtenEtiqueta(1990); ?></a>
                                  </li>                            
                                </ul>
                    
                                <div id="myTabContent10" class="tab-content padding-10">
                                  <div id="s1fame1">
                                    <?php
                                      Forma_CampoTinyMCE(ObtenEtiqueta(271), False, 'ds_contenido', $ds_contenido, 50, 20);
                                    ?>
                                  </div>
                                  <div class="tab-pane fade" id="s2fame2">
                                    <?php
                                      Forma_CampoTinyMCE(ObtenEtiqueta(245), False, 'tr_contenido', $tr_contenido, 50, 20);
                                    ?>
                                  </div>
                                </div>
                    
                              </div>
                              <!-- end widget content -->
                            </div>
                            <div class="tab-pane fade" id="s2fame">
                              <div class="row padding-5">
                                <div class="widget-body">
                                  <ul id="myTabvideos2" class="nav nav-tabs bordered">
                                    <li class="active">
                                      <a href="#s1videos" data-toggle="tab" aria-expanded="true"><?php echo ObtenEtiqueta(2037); ?></a>
                                    </li>
                                    <li class="" onclick="videos(<?php echo $cl_pagina.",".$clave.",".$fg_error.",1"; ?>);">
                                      <a href="#s2videos" data-toggle="tab" aria-expanded="true"><?php echo ObtenEtiqueta(2038); ?></a>
                                    </li>
                                  </ul>
                                  <div id="myTabContent22" class="tab-content padding-10">
                                    <div class="tab-pane fade in active" id="s1videos">
                                      <?php
                                      # Si es nuevo y hay error y existe videos en proceso
                                      $row9 = RecuperaValor("SELECT fl_vid_contet_temp FROM k_vid_content_temp WHERE fl_usuario=$fl_usuario AND fg_fame='1'");
                                      if(!empty($row9[0])){
                                        echo "
                                          <div class='row'>
                                            <div class='col-sm-3'>&nbsp</div>
                                            <div class='col-xs-12 col-sm-5'>
                                              <span class='help-block'><p><code><i class='fa fa-warning'></i> ".ObtenEtiqueta(2038)."</code></p></span>
                                            </div>
                                            <div class='col-sm-3'>&nbsp</div>
                                          </div>";
                                      }
                                      # Paramatros ha enviar
                                      $par_name = array('clave', 'fl_programa', 'usuario', 'fg_fame');
                                      $par_valor = array($cl_pagina, $clave, $fl_usuario, 1);
                                      Forma_DropzoneVideos(0, "", "", "lib_scho_fame", PATH_MODULOS."/content/vid_library.php", ".mov, .mp4, .flv", $par_name, $par_valor, false, "", "", "");
                                      # Fin de DROPZONE
                                      ?>
                                    </div>
                                    <div class="tab-pane fade" id="s2videos">
                                      <div class="row" id="videos_libraryfame"></div>
                                    </div>
                                  </div>
                                  <script>
                                  function videos(cla,pro,error, fame){
                                    $.ajax({
                                      type: "POST",
                                      url : "<?php echo PATH_MODULOS; ?>/content/videos_library.php",
                                      data: "clave="+cla+"&pro="+pro+"&fg_error="+error+"&accion=1&fg_fame="+fame,
                                      success: function(html){
                                        $('#videos_libraryfame').empty().append(html);
                                      }
                                    });
                                  }
                                  </script>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
                
                  
				 
				  <div class="tab-pane fade" id="Config">
					  <div class="row">
                         <div class="col-sm-6 col-lg-6">
                          <?php Forma_CampoTexto(ObtenEtiqueta(2095), False, 'no_email', (!empty($no_email)?$no_email:NULL), 10, 15, !empty($no_email_err)?$no_email_err:NULL,'','','', 'onkeypress=\'return soloNumeros(event)\'' ); ?>
                         </div>
				         <div class="col-sm-6 col-lg-6">
                          <?php Forma_CampoTexto(ObtenEtiqueta(2096), False, 'mn_precio', (!empty($mn_precio)?$mn_precio:NULL), 10, 15, !empty($mn_precio_err)?$mn_precio_err:NULL,'','','','onkeypress=\'return NumeroDecimal(event)\''); ?>
                         </div>
                      </div>
                      <div class="row">
                         <div class="col-sm-6 col-lg-6">
                          <?php Forma_CampoTexto(ObtenEtiqueta(2104), False, 'no_dias_trial', (!empty($no_dias_trial)?$no_dias_trial:NULL), 10, 15, !empty($no_dias_trial_err)?$no_dias_trial_err:NULL,'','','','onkeypress=\'return soloNumeros(event)\''); ?>
                         </div>

                         <div class="col-sm-6 col-lg-6">
                          <?php Forma_CampoTexto(ObtenEtiqueta(2106), False, 'no_dias_pago', (!empty($no_dias_pago)?$no_dias_pago:NULL), 10, 15, !empty($no_dias_pago_err)?$no_dias_pago_err:NULL,'','','','onkeypress=\'return soloNumeros(event)\''); ?>
                         </div>

                      </div>


				 </div>

                    <?php
                        #Agreamos plccheholder con ja scrit ya que el formula actual no se puede.
				        $no_global_email=ObtenConfiguracion(122);
                        $placeholder=ObtenEtiqueta(2098)." (".$no_global_email.")";
                        echo"<script>
                                      $('#no_email').attr('placeholder','".$placeholder."');
                             </script>";
                    
                   
                   
				       $no_global_precio=ObtenConfiguracion(123);
                       $placeholder=ObtenEtiqueta(2098)." (".$no_global_precio.")";
                       echo"<script>
                                      $('#mn_precio').attr('placeholder','".$placeholder."');
                            </script>";
                  
				  
				       $no_global_dias_trial=ObtenConfiguracion(127);
					   $placeholder=ObtenEtiqueta(2098)." (".$no_global_dias_trial.")";
					   echo"<script>
					                 $('#no_dias_trial').attr('placeholder','".$placeholder."');
					        </script>";
				   
                       $no_global_dias_payment=ObtenConfiguracion(128);
					   $placeholder=ObtenEtiqueta(2098)." (".$no_global_dias_payment.")";
					   echo"<script>
					                 $('#no_dias_pago').attr('placeholder','".$placeholder."');
					        </script>";
                    
                    ?>

				 
				 
            </div>
            
            
  <?php 
  if((empty($clave)) AND (empty($fg_error))){ 
  ?>
  <script>
    $(document).ready(function(){
      
      // Course Name
      $('#div_nb_programa').removeClass('row form-group').addClass('row form-group input has-error');
      document.getElementById('nb_programa').style.backgroundColor = '#FFF0F0';
      // Credits
      $('#div_no_creditos').removeClass('row form-group').addClass('row form-group has-error');
      document.getElementById('no_creditos').style.backgroundColor = '#FFF0F0';
      // Display Order
      $('#div_no_orden').removeClass('row form-group').addClass('row form-group has-error');
      document.getElementById('no_orden').style.backgroundColor = '#FFF0F0';
      // Duration in hours
      $('#div_no_horas').removeClass('row form-group').addClass('row form-group has-error');
      document.getElementById('no_horas').style.backgroundColor = '#FFF0F0';
      // Duration in sessions
      $('#div_no_semanas').removeClass('row form-group').addClass('row form-group has-error');
      document.getElementById('no_semanas').style.backgroundColor = '#FFF0F0';
      // Credential issued
      $('#div_ds_credential').removeClass('row form-group').addClass('row form-group has-error');
      document.getElementById('ds_credential').style.backgroundColor = '#FFF0F0';
      
      
      // ### Estilos para categorias
      
      // Field of Study nb_fos_ul
      document.getElementById('nb_fos_ul').style.backgroundColor = '#FFF0F0';
      document.getElementById('nb_fos_ul').style.borderColor = '#b94a48';
      document.getElementById('lbl_fos').style.color = '#b94a48';
      
      // Categories singleFieldTags
      document.getElementById('singleFieldTags').style.backgroundColor = '#FFF0F0';
      document.getElementById('singleFieldTags').style.borderColor = '#b94a48';
      document.getElementById('lbl_tags').style.color = '#b94a48';
      
      // Hardware hardware_ul
      document.getElementById('hardware_ul').style.backgroundColor = '#FFF0F0';
      document.getElementById('hardware_ul').style.borderColor = '#b94a48';
      document.getElementById('lbl_har').style.color = '#b94a48';
      
      // Software software_ul
      document.getElementById('software_ul').style.backgroundColor = '#FFF0F0';
      document.getElementById('software_ul').style.borderColor = '#b94a48';
      document.getElementById('lbl_sof').style.color = '#b94a48';
      
      // Course Code course_code
      document.getElementById('ds_course_code').style.backgroundColor = '#FFF0F0';
      document.getElementById('ds_course_code').style.borderColor = '#b94a48';
      document.getElementById('lbl_course_code').style.color = '#b94a48';
      
    
      
      
      
      // document.getElementById('tab_prog').style.color = '#b94a48';
      
      // Delivery method
      // $('#div_cl_delivery').removeClass('row form-group smart-form').addClass('row form-group smart-form has-error');
      // document.getElementById('cl_delivery').style.backgroundColor = '#FFF0F0'; 
      // Type of course
      // $('#div_cl_type').removeClass('row form-group smart-form').addClass('row form-group smart-form has-error');
      // document.getElementById('cl_type').style.backgroundColor = '#FFF0F0'; 
     
    });
  </script>
  <?php 
  } 
  
  # Valida si hay error en los campos de la pestaña categorias
  if(!empty($fg_error)){
    
    $nb_tags2_err = RecibeParametroNumerico('nb_tags2_err');
    if($nb_tags2_err){
      echo "<script>
        document.getElementById('singleFieldTags').style.borderColor = '#b94a48';
        document.getElementById('lbl_tags').style.color = '#b94a48';
        document.getElementById('err_tags').innerHTML = '<span class=\"help-block\" style=\"color:#b94a48;\"><i class=\"fa fa-warning\"></i>".ObtenMensaje($nb_tags2_err)."</span>';
        
      </script>";
    }
    $nb_sof2_err = RecibeParametroNumerico('nb_sof2_err');
    if(!empty($nb_sof2_err)){
      echo "<script>
        document.getElementById('software_ul').style.borderColor = '#b94a48';
        document.getElementById('lbl_sof').style.color = '#b94a48';
        document.getElementById('err_sof').innerHTML = '<span class=\"help-block\" style=\"color:#b94a48;\"><i class=\"fa fa-warning\"></i>".ObtenMensaje($nb_sof2_err)."</span>';
      </script>";
    }
    $nb_har2_err = RecibeParametroNumerico('nb_har2_err');
    if(!empty($nb_har2_err)){
      echo "<script>
        document.getElementById('hardware_ul').style.borderColor = '#b94a48';
        document.getElementById('lbl_har').style.color = '#b94a48';
        document.getElementById('err_har').innerHTML = '<span class=\"help-block\" style=\"color:#b94a48;\"><i class=\"fa fa-warning\"></i>".ObtenMensaje($nb_har2_err)."</span>';
      </script>";
    }
    $nb_fos2_err = RecibeParametroNumerico('nb_fos2_err');
    if(!empty($nb_fos2_err)){
      echo "<script>
        document.getElementById('nb_fos_ul').style.borderColor = '#b94a48';
        document.getElementById('lbl_fos').style.color = '#b94a48';
        document.getElementById('err_fos').innerHTML = '<span class=\"help-block\" style=\"color:#b94a48;\"><i class=\"fa fa-warning\"></i>".ObtenMensaje($nb_fos2_err)."</span>';
      </script>";
    }
    $reg_programa_err = RecibeParametroNumerico('reg_programa_err');
    if(!empty($reg_programa_err)){
      echo "<script>
        // document.getElementById('nb_fos_ul').style.borderColor = '#b94a48';
        // document.getElementById('lbl_fos').style.color = '#b94a48';
        document.getElementById('err_gdo').innerHTML = '<span class=\"help-block\" style=\"color:#b94a48;\"><i class=\"fa fa-warning\"></i>".ObtenMensaje($reg_programa_err)."</span>';
      </script>";
    }
    $ds_course_code_err = RecibeParametroNumerico('ds_course_code_err');
    if(!empty($ds_course_code_err)){
      echo "<script>
        document.getElementById('lbl_course_code').style.color = '#b94a48';
      </script>";
    }
	
	$reg_course_code_err=RecibeParametroNumerico('reg_course_code_err');
	if(!empty($reg_course_code_err)){
	  echo "<script>
	            document.getElementById('err_courses_code').innerHTML = '<span class=\"help-block\" style=\"color:#b94a48;\"><i class=\"fa fa-warning\"></i>".ObtenMensaje($reg_course_code_err)."</span>';
            </script>";
	
	}
	
	
	
	
    
 
 }
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CLIB_SP, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( ); 
echo "
  <script src='".PATH_LIB."/fame/dropzone.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/moment.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/jquery.mockjax.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/x-editable.min.js'></script>";  
?>