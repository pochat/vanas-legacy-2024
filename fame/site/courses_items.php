<?php

  # Libreria de funciones
  require("../lib/self_general.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Obtenemos el perfil del usuario
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

  $index=$_GET['index'];
  $index = intval($index);
  $index_end = 9;

  # Variable initialization
  $div="";

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  #Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
  if($fl_perfil_sp==PFL_ESTUDIANTE_SELF)
    $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);

  $ds_nombres_user_actual=ObtenNombreUsuario($fl_usuario,$fl_usuario);

  #Identifiacmos si el logueado es b2c
  if(!empty($fg_puede_liberar_curso))
	  $fg_b2c=1;
  else
	  $fg_b2c=0;

  #Los trials no pueden ver boton export
  $fg_plan_instituto_=ObtenPlanActualInstituto($fl_instituto);
  if(empty($fg_plan_instituto_))
      $fg_b2c=1;

  #2020 -sep  verificamos que el instituto no sea b2c.
  $Query="SELECT fg_b2c,fg_export_moodle FROM c_instituto WHERE fl_instituto=$fl_instituto ";
  $row=RecuperaValor($Query);
  $fg_b2c=$row[0];
  $fg_export_moodle=$row[1];

  # Query Principal usuarios generales
  
  /* 14-ene-2021 oculto por eficiencia*/
  /*
  $Query="SELECT b.fl_programa_sp, nb_programa".$sufix." AS nb_programa, no_semana, b.nb_thumb, b.fg_nuevo_programa, b.no_creditos, c.no_horas, c.no_workload, c.ds_credential, ";
  $Query.="c.ds_language, b.no_email_desbloquear, b.fl_instituto,  u.ds_progreso, ";
  $Query.="u.fg_terminado, fg_assign_myself_course, fg_asignado_playlist, fl_usu_pro, fg_status_pro, ";
  $Query.="CONCAT(COUNT(fl_leccion_sp), ' ".ObtenEtiqueta(1242)."') AS no_lecciones, ";
  $Query.="(SELECT COUNT(*) FROM c_usuario h LEFT JOIN k_usuario_programa i ON(fl_usuario=i.fl_usuario_sp) WHERE fl_perfil_sp=$fl_perfil_sp AND fl_instituto=$fl_instituto AND i.fl_programa_sp=b.fl_programa_sp) no_students, ";
  $Query.="CASE WHEN u.fl_maestro IS NULL THEN n.fl_usu_invita ELSE u.fl_maestro END fl_maestro, ";
  $Query.="(CASE 
                WHEN c.cl_delivery = 'O' 
                THEN 'Online' WHEN c.cl_delivery = 'S' 
                THEN 'On-Site' WHEN c.cl_delivery = 'C' 
                THEN 'Combined'  WHEN c.cl_delivery = 'OB' 
                THEN 'Online / Blended' 
           END) AS cl_delivery, ";
  $Query.="(CASE
                WHEN c.cl_type = '1' THEN 'Long Term Duration'
                WHEN c.cl_type = '2' THEN 'Short Term Duration'
                WHEN c.cl_type = '3' THEN 'Corporate'
                WHEN c.cl_type = '4' THEN 'Long Term Duration(3 contracts, 1 per year)'
           END) AS cl_type ";
  $Query .="FROM c_programa_sp b 
                LEFT JOIN c_leccion_sp a ON(b.fl_programa_sp=a.fl_programa_sp)
                LEFT JOIN k_programa_detalle_sp c ON( c.fl_programa_sp=b.fl_programa_sp ) 
                LEFT JOIN k_orden_desbloqueo_curso_alumno d ON (d.fl_programa_sp=b.fl_programa_sp AND d.fl_alumno = 781)
                LEFT JOIN k_usuario_programa u ON(u.fl_programa_sp=b.fl_programa_sp AND u.fl_usuario_sp=$fl_usuario) 
                LEFT JOIN c_usuario n ON(n.fl_usuario=$fl_usuario)
                LEFT JOIN c_instituto o ON(o.fl_instituto=b.fl_instituto)
            WHERE  b.fg_publico = '1'
            GROUP BY b.fl_programa_sp ORDER BY fl_programa_sp DESC ";
       */     

$Query="SELECT b.fl_programa_sp, nb_programa".$sufix." AS nb_programa, no_semana, b.nb_thumb, b.fg_nuevo_programa, b.no_creditos, c.no_horas, c.no_workload, c.ds_credential, ";
  $Query.="c.ds_language, b.no_email_desbloquear, b.fl_instituto ,";
  //$Query.="CONCAT(COUNT(fl_leccion_sp), ' ".ObtenEtiqueta(1242)."') AS no_lecciones, ";
  //$Query.="CASE WHEN u.fl_maestro IS NULL THEN n.fl_usu_invita ELSE u.fl_maestro END fl_maestro, ";
  $Query.="(CASE 
                WHEN c.cl_delivery = 'O' 
                THEN 'Online' WHEN c.cl_delivery = 'S' 
                THEN 'On-Site' WHEN c.cl_delivery = 'C' 
                THEN 'Combined'  WHEN c.cl_delivery = 'OB' 
                THEN 'Online / Blended' 
           END) AS cl_delivery, ";
  $Query.="(CASE
                WHEN c.cl_type = '1' THEN 'Long Term Duration'
                WHEN c.cl_type = '2' THEN 'Short Term Duration'
                WHEN c.cl_type = '3' THEN 'Corporate'
                WHEN c.cl_type = '4' THEN 'Long Term Duration(3 contracts, 1 per year)'
           END) AS cl_type ";
  $Query .="FROM c_programa_sp b 
                LEFT JOIN c_leccion_sp a ON(b.fl_programa_sp=a.fl_programa_sp)
                LEFT JOIN k_programa_detalle_sp c ON( c.fl_programa_sp=b.fl_programa_sp )                 
                LEFT JOIN c_instituto o ON(o.fl_instituto=b.fl_instituto)
            WHERE  b.fg_publico = '1'
            GROUP BY b.fl_programa_sp ORDER BY b.fl_programa_sp DESC ";  
  $Query .=" LIMIT $index_end OFFSET $index ";
  $Queryp =$Query;
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  $y = 0;
  $band = 0;

  for($i=0;$row=RecuperaRegistro($rs);$i++) {

      # PUT ALL THE RECORDS FIELDS TO VARIABLES
      $fl_programa_sp = $row['fl_programa_sp'];
      $nb_programa = $row['nb_programa'];
      $no_semana = $row['no_semana'];
      $nb_thumb = str_texto($row['nb_thumb']);
      $fg_nuevo_programa = $row['fg_nuevo_programa'];
      $no_creditos = $row['no_creditos'];
      $no_horas = $row['no_horas'];
      $no_workload = $row['no_workload'];
      $cl_delivery = str_texto($row['cl_delivery']);
      $ds_credential = str_texto($row['ds_credential']);
      $cl_type = str_texto($row['cl_type']);
      $ds_language = str_texto($row['ds_language']);
      $no_email_desbloquear=$row['no_email_desbloquear'];
      $fl_instituto_programa=$row['fl_instituto'];
      $no_lecciones = $row['no_lecciones']??NULL;
     // $fg_export_moodle=$row['fg_export_moodle']??0;
      $ds_progreso=$row['ds_progreso']??0;
      $fg_terminado=$row['fg_terminado']??NULL;
      $no_students=$row['no_students'];
      $fg_assign_myself_course=$row['fg_assign_myself_course']??0;
      $fl_maestro=$row['fl_maestro'];
      $fg_asignado_play_list=$row['fg_asignado_playlist']??NULL;
      $fl_usu_pro = $row['fl_usu_pro']??NULL;
      $fg_status_pro = $row['fg_status_pro']??NULL;

      # VARIABLE INITIALIZATION
      $img = PATH_HOME."/modules/fame/uploads/".$nb_thumb;
      $style_cuad = "col-sm-4 col-md-4 col-lg-4";
      $style_img  = "";

      $Querystudents="SELECT COUNT(*) FROM k_usuario_programa a JOIN c_usuario b ON b.fl_usuario=a.fl_usuario_sp WHERE a.fl_programa_sp=$fl_programa AND b.fl_instituto=$fl_instituto  ";
      $rowstudent=RecuperaValor($Querystudents);
      $no_students=$rowstudent[0]??0;

      #Recuperamos el progreso y datos del estudiante....
      $Query="SELECT COUNT(*) FROM  c_leccion_sp WHERE fl_programa_sp=$fl_programa_sp ";
      $row=RecuperaValor($Query);
      $no_lecciones = $row[0]." ".ObtenEtiqueta(1242);


      #Recuperamos el progreso del estudiante. si lo tiene.
      $Query="SELECT fl_usu_pro,ds_progreso,fg_terminado,fl_maestro,fg_status_pro FROM k_usuario_programa WHERE fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_usuario ";
      $row=RecuperaValor($Query);
      $ds_progreso=$row['ds_progreso']??0;
      $fl_usu_pro = $row['fl_usu_pro']??NULL;
      $fl_maestro=$row['fl_maestro'];
      $fl_usu_pro = $row['fl_usu_pro']??NULL;
      $fg_status_pro = $row['fg_status_pro']??NULL;

      if(empty($fl_maestro)){
          $Query="SELECT fl_usu_invita FROM c_usuario where fl_usuario=$fl_usuario ";
          $row=RecuperaValor($Query);
          $fl_maestro=$row['fl_usu_invita'];
      }

    if($fl_perfil_sp==PFL_ESTUDIANTE_SELF){

      #Verificamos el pago del curso
      $fl_pagado=VerificaPagoCurso($fl_usuario,$fl_programa_sp);

      $fg_plan_pago=RecuperaPlanActualAlumnoFame($fl_usuario);

      #Vlidamos su periodo de vigencia
      $fg_plan_vigente=VerificaVigenciaPlanAlumno($fl_usuario);

      if(empty($fg_plan_vigente))
        $fg_plan_pago=null;

      #Solo si no tiene un plan de pago verifica email enviados para acceso
      if(empty($fg_plan_pago)){

        #Verificmaos el numero de emeial requeridos del programa/curso y si no lo tiene asignado pues lnaza valor por default.
        $no_email_enviados=CuentaEmailEnviadosDesbloquearCurso($fl_usuario,$fl_programa_sp);
        if(!empty($no_email_enviados)){
          $no_email_desbloquear=$no_email_enviados;
        }else{
          if(empty($no_email_desbloquear)){
            $no_email_desbloquear=ObtenConfiguracion(122);
          }
        }
      }
    }


    


    # Obtenemos los grupos que existen de este programa en este instituto
    $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
    $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
    $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp." AND nb_grupo<>'' GROUP BY c.nb_grupo ";
    $rsg = EjecutaQuery($Queryg);
    $no_groups = CuentaRegistros($rsg);

    if(($fl_perfil_sp==PFL_ESTUDIANTE_SELF)&&(!empty($fg_puede_liberar_curso))){
        if($fg_plan_pago)
            $fl_usu_pro=1;
    }
    
    # Esta asignado al curso
    if(!empty($fl_usu_pro)) {
        
      if(!empty($fg_puede_liberar_curso) && $fg_puede_liberar_curso==1){
          
        $fg_puede_tomar_curso=VerificaCumplientoRequisitoParaAccederCurso($fl_usuario,$fl_programa_sp,$no_email_desbloquear,$fg_plan_pago,$fl_pagado,$fg_assign_myself_course);
    
        $fg_desbloqueado_por_envio_email=DesbloqueadoPorPagoCurso($fl_usuario,$fl_programa_sp);    
        
        if($fg_desbloqueado_por_envio_email){ #if(empty($fg_plan_pago))
            if((empty($fg_plan_pago))&&($fl_programa_sp<>33))
              $no_dias_faltan_terminar_plan=MuestraTiempoRestanteTrialCurso($fl_usuario,$fl_programa_sp,1);
            else
              $no_dias_faltan_terminar_plan="";    
        }else{ #if(empty($fg_plan_pago))
            if((empty($fg_plan_pago))&&($fl_programa_sp<>33))
              $no_dias_faltan_terminar_plan=MuestraTiempoRestanteTrialCurso($fl_usuario,$fl_programa_sp);
            else
              $no_dias_faltan_terminar_plan="";    
        }
          
        if($fg_puede_tomar_curso){
            
          #Si ya tiene un progreso
          if(  (!empty($ds_progreso)) && ($ds_progreso <100) ) {
              $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a> ".$no_dias_faltan_terminar_plan." ";/*Continue*/
          }else if($fg_terminado==1){
                $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>"; /*Review*/
          }else{
              $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
              <span class='h4'>
              <a class='btn btn-success no-margin' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye fa-1x'></i>&nbsp;".ObtenEtiqueta(1149)."&nbsp;</a>
              </span>".$no_dias_faltan_terminar_plan." </div>";
          }
        }else{
          #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
          $btn="<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
          <span class='h4' id='btn_desbloquear_curso'>
          <a class='btn btn-danger no-margin' style='background-color: #D97789 !important; border-color: #D97789 !important;'  onclick='$(\"#ModalPrivacity\").modal(\"toggle\");DesabilitarPagarCurso(".$fl_programa_sp.",".$no_email_desbloquear.");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(2076)."&nbsp;</a>
          </span> ".$no_dias_faltan_terminar_plan."</div>";
          // $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>"; /*Review*/
        }
      }else{
        # Si ya termino el curso solo podra ver sus calificaciones
        # El boton  es color azul
        if($fg_terminado==1){
          if($fg_status_pro==1)
            $btn = "<a href='javascript:user_pause(".$fg_status_pro.",".$fl_programa_sp.",".$fl_usuario.", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> ".ObtenEtiqueta(1999)."</a>";/*Paused*/
          else
            $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>";                      
        }else{ # Continua el curso
          # Si esta pausado no podra acceder al desktop
          # Tendra que enviar un correo al teacher o espera a que se lo activen
          if($fg_status_pro==1) {
              $btn = "<a href='javascript:user_pause(" . $fg_status_pro . "," . $fl_programa_sp . "," . $fl_usuario . ", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> " . ObtenEtiqueta(1999) . "</a>";
          }else{
            if(empty($ds_progreso)){
                  if(!empty($fg_asignado_play_list))
                      $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-success'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1149)."</a>";/*Start*/
                  else    
                      $btn = "<a  class='btn btn-success' onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
            }else{
              $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a>";/*Continue*/
            }
          }
        } 
      }#end else puede liberar curso.         
    } else {# No esta asignado al curso
      # Para los estudiantes
      if($fl_perfil_sp== PFL_ESTUDIANTE_SELF){
          
        if($fg_puede_liberar_curso==1){
          $fg_puede_tomar_curso=VerificaCumplientoRequisitoParaAccederCurso($fl_usuario,$fl_programa_sp,$no_email_desbloquear,'',$fl_pagado,$fg_assign_myself_course);
          if(!empty($fg_puede_tomar_curso)){
               $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
               <span class='h4'>
               <a class='btn btn-success no-margin' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye fa-1x'></i>&nbsp;".ObtenEtiqueta(1149)."&nbsp;</a></span></div>";
          } else {
            #Verificamos is puede seguir mandando emails
            $fg_btn_habilitado=VerificaBotonParaDesbloquearCursoPorMetodoEnvioEmail($fl_usuario,$fl_programa_sp);
            $exp=explode("#",$fg_btn_habilitado);
            $fg_btn_habilitado=$exp[0];
            $etq_disabled=$exp[1];

            if($fg_btn_habilitado){
                $class="success";
                $etqb=ObtenEtiqueta(2121);
		            $style="background-color: #ea75a5 !important; border-color: #ea75a5 !important;";
		            $iconob="users";
            } else {
                $class="danger";
                $etqb=ObtenEtiqueta(2076);
		            $style="background-color: #b077d9 !important; border-color: #b077d9 !important;";
		            $iconob="lock";
            }
            #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
            $btn="<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
            <span class='h4' id='btn_desbloquear_curso'>
            <a class='btn btn-$class no-margin' style='$style' onclick='$(\"#ModalPrivacity\").modal(\"toggle\");DesabilitarPagarCurso(".$fl_programa_sp.",".$no_email_desbloquear.");'><i class='fa fa-$iconob fa-1x'></i>&nbsp;".$etqb."&nbsp;</a></span></div>";
          }
        } else {
          # Verificamos si el estudiante puede asignarse solo al curso
          if($fg_assign_myself_course==1)  {                      
            $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
          } else {
            $etq_mensaje=ObtenEtiqueta(2598);
            $etq_1=ObtenEtiqueta(2605);
            $etq_2=ObtenEtiqueta(2604);
            $etq_student=ObtenEtiqueta(2229);
            $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
            <span class='h4'>
            <a class='btn btn-danger no-margin' href='javascript: myself_layout($fl_programa_sp, $fl_usuario, 122, \"courses_library.php\",$fl_maestro,\"$ds_nombres_user_actual\",\"$nb_programa\",\"$etq_mensaje\",\"$etq_1\",\"$etq_2\",\"$etq_student\");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(1888)."&nbsp;<i class='fa fa-envelope-o fa-1x'></i></a></span></div>";
          }
        }#end else 
      } else {

          #mjd sep-2020 es un instituto b2c y solo puede acceder a cursos que estan permitidos.
          if($fg_b2c==1){
             
              #verifgica si el curso esta desbloqueado/asignado/comprado.  */k_orden_desbloqueo_curso_alumno  ESTA TABLA hay que cabuarla de nombre ligara institutos tambien que ya compraron un curso en especifico.
              $Querypc="SELECT COUNT(*)FROM k_orden_desbloqueo_curso_alumno where fl_instituto=$fl_instituto AND fl_programa_sp=$fl_programa_sp ";
              $rowpc=RecuperaValor($Querypc);
              if(!empty($rowpc[0])){
                  #btn normal de acceso al curso.
                  $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
                  
              }else{
                  $class="danger";
                  $etqb=ObtenEtiqueta(2076);
                  $style="background-color: #b077d9 !important; border-color: #b077d9 !important;";
                  $iconob="lock";
                  $btn = "<a href='javascript:void(0);' style='$style' class='btn btn-$class'> <i class='fa fa-$iconob'></i> ".$etqb."</a>";
              }
          
          
          
          }else{
              $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
          }
      
      }
    }
    # Depende del perfil muestra grupos
    if($fl_perfil_sp == PFL_MAESTRO_SELF or $fl_perfil_sp == PFL_ADMINISTRADOR){
        $groups  = "<a href='javascript:void(0);' ";
        $groups .= "onclick='PresentaModalGroups(".$fl_programa_sp.", \"G\");'><center>Groups (".$no_groups.")</center></a>";
    } else {
        $groups = "<center>Groups (".$no_groups.")</center>";
    }
    # Scripts playlist
    $script_play = 
    "<!--- Inicia Playlist por curso --->
    <script>     
      // Funciones para playlist de cursos    
      // Div para mostrar input de agregar playlist AddPlaylist
      
      function AddPlaylistInd__".$i."(val) {
        element = document.getElementById('aa__".$i."');
        element_tit = document.getElementById('mtit__".$i."');
        document.getElementById('new_playlist__".$i."').value = '';
        if (val == true) {
          element.style.display='block';
          element_tit.style.display='none';
        } else {
          element.style.display='none';
          element_tit.style.display='block';
        }
      }
      
      // Muestra la lista de playlist en cursos
      function DespliegaLista__".$i."(actual, muestra,fl_programa_sp){
        $.ajax({
          type: 'POST',
          url : 'site/listado_ind_palylist.php',
          async: false,
          data: 'fl_programa_sp='+fl_programa_sp+
                '&actual='+actual+
                '&muestra='+muestra,
          success: function(data) {
            $('#muestra_listado_ind_playlist__".$i."').html(data);
          }
        });
      }
      
      // 1.- Busca un playlist existente
      function busca_playlist__".$i."(valor, extra){ 
        if(valor == undefined)
          valor = document.getElementById('new_playlist__".$i."').value;
        if(extra == undefined)
          extra = 0;
        $.ajax({
          type: 'POST',
          url : 'site/recupera_playlist.php',
          async: false,
          data: 'valor='+valor+
                '&accion=busca'+
                '&extra='+extra,
          success: function(data) {
            $('#muestra_prueba__".$i."').html(data);
            document.getElementById('busca_playlist__".$i."').value = valor;
            document.getElementById('new_playlist__".$i."').value = '';
          }
        });
      }
      
      // Guarda los playlist creados en cursos
      function GuardaPlaylist__".$i."(valor_p){
        var valor = document.getElementById('new_playlist__".$i."').value;
        $.ajax({
          type: 'POST',
          url : 'site/recupera_playlist.php',
          async: false,
          data: 'valor='+valor+
                '&extra='+valor_p+
                '&accion=guarda',
          success: function(data) {
            $('#muestra_busqueda_playlist1').html(data);
          }
        });
      }
      
      // Activa boton guardar por curso
      function BtnGuardar__".$i."(){
        var new_playlist = document.getElementById('new_playlist__".$i."').value;
          $('#Ccl__".$i."').removeClass('btn btn-primary btn-xs disabled');
          $('#Ccl__".$i."').addClass('btn btn-primary btn-xs');
          
        if(new_playlist == ''){
          $('#Ccl__".$i."').removeClass('btn btn-primary btn-xs');
          $('#Ccl__".$i."').addClass('btn btn-primary btn-xs disabled');
        }
      }
    
      function ActListaGenetal(){
        $.ajax({
          type: 'POST',
          url : 'site/recupera_playlist.php',
          async: false,
          data: 'valor=actualiza_lista',
          success: function(data) {
            $('#div_prueba_test').html(data);
          }
        });  
      }
      
      function NewPlaylistCourse_2(valor){
         $.ajax({ 
          type: 'POST',
          url : 'site/recupera_playlist.php',
          async: false,
          data: 'valor=add_curso_playlist'+
                '&extra='+valor,
          success: function(data) {
            $('#div_prueba__'+valor).html(data);
          }
        });
      }
      
      function NewPlaylistCourse__".$fl_programa_sp."(){
        ActListaGenetal();
        NewPlaylistCourse_2(".$fl_programa_sp.");
			}	
     </script>";

     # style categorias
     if($y == 2)
        $style_esp = "left: -332px;";
      else
        $style_esp = "";

     # info curse general
     $dl = "<dl class='dl-horizontal'>
              <dt>".ObtenEtiqueta(360).":</dt>
                <dd>".$nb_programa."</dd>
              <dt>".ObtenEtiqueta(1216).":</dt>
                <dd>".$no_creditos."</dd>
              <dt>".ObtenEtiqueta(1220).":</dt>
                <dd>".$no_horas."</dd>
              <dt>".ObtenEtiqueta(1222).":</dt>
                <dd>".$no_semana."</dd>
              <dt>".ObtenEtiqueta(1252).":</dt>
                <dd>".$no_workload."</dd>
              <dt>".ObtenEtiqueta(1224).":</dt>
                <dd>".$cl_delivery."</dd>
              <dt>".ObtenEtiqueta(1223).":</dt>
                <dd>".$ds_credential."</dd>
              <dt>".ObtenEtiqueta(1226).":</dt>
                <dd>".$cl_type."</dd>
              <dt>".ObtenEtiqueta(1296).":</dt>
                <dd>".$ds_language."</dd>
            </dl>";

    # Muestra btn preview para teacher y administradores
    if($fl_perfil_sp == PFL_MAESTRO_SELF || $fl_perfil_sp == PFL_ADMINISTRADOR){
      $btn_pre = "<a class='btn btn-primary btn-xs' href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1' ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2008)." </a>";
    } else {
      if( ($fl_perfil_sp==PFL_ESTUDIANTE_SELF)&&($fl_instituto==4)){
        if($fg_puede_liberar_curso==1){
          if(empty($fg_puede_tomar_curso)){
               $btn_pre = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
               <a class='btn btn-primary btn-xs' style='background-color: #779BD9 !important; border-color: #779BD9 !important;' href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1&uc=1'><i class='fa fa-eye'></i> ".ObtenEtiqueta(2108)." </a></div>";
          } else {
            $btn_pre = "";
          }
        } else {
          $btn_pre = "";
        }
      } else {
        $btn_pre = "";
      }          
    }
    
    $fl_programa_sp_i = $fl_programa_sp;
    $y++;
    
    if($y == 3){
      $band = 1;
      $limit = $fl_programa_sp_i;
    } else {
      if($i == ($registros - 1)){
        $band = 1;
        $limit = $fl_programa_sp_i;
      } else {
        $band = 0 ;
      }
    }
    
    # Divs ocultos para las lecciones y los grupos
    if($band == 1){
    //  $div = "</div>"; //SE COMENTAN 01-MAY-05 Todos los cursos estan en cl-m-4 y se van acomodando solos no es necesarios agruparlos en 4 bloques con el div row
    //  $div .= "<div class='row'>";
      $y = 0; 
    }

    #El ultimo filtro que se tendra es que si el programa se puede compartir a nivel general 'o solo puede ver visto por el instituto logueado.
    $fg_compartir_curso=Share_Course($fl_programa_sp,$fl_instituto_programa,$fl_instituto);
   
    # Info items
    $result["item".$i] = array(
      "fg_b2c" =>$fg_b2c,
      "fg_export_moodle"=>$fg_export_moodle,
      "style" => $style_cuad,
      "file" => $img ,
      "programa" => $nb_programa,
      "style_img" => $style_img,
      "fg_nuevo_programa" => $fg_nuevo_programa,
      "label_new_pro" => ObtenEtiqueta(1243),
      "tot_students" => $no_students,
      "groups" => $groups,
      "fl_programa" => $fl_programa_sp,
      "script_play" => $script_play,
      "no_lecc" => $no_lecciones,
      "style_esp" => $style_esp,
      "dl" => $dl,
      "btn" => $btn,
      "fg_curso_visible"=>$fg_compartir_curso,
      "btn_pre" => $btn_pre,
      "progreso"=> $ds_progreso,
      "band" => $band,
      "band_div" => $div
    );
  }
  $result["result"] = array(
    "tot_reg" => $registros
    );
  if($i == 0){
      $result["index"] = array("end" => 0, "message" => "No records","Queryp"=>$Query);
      echo json_encode((Object)$result);
      exit;
  }
  $result["size"] = array("total" => $i, "querypincipal"=>$Queryp);
  $result["index"] = array("end" => $index+$index_end);


  echo json_encode((Object) $result);
?>
