<?php
	# Libreria de funciones
	require("../lib/self_general.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  # Obtenemos el perfil del usuario
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  $row_cont_cur = RecuperaValor("SELECT fl_programa_sp FROM c_programa_sp ORDER BY fl_programa_sp DESC LIMIT 1");
  
  #Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
  if($fl_perfil_sp==PFL_ESTUDIANTE_SELF)
  $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);
  
  # Query Principal
  $Query  = " SELECT fl_leccion_sp, nb_programa, no_semana, ds_titulo, ds_leccion, ";
  $Query .= " CASE WHEN ds_vl_ruta IS NULL THEN '".ObtenEtiqueta(17)."' WHEN ds_vl_ruta='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'Video Brief', ";
  $Query .= " CASE WHEN fg_ref_animacion IS NULL THEN '".ObtenEtiqueta(17)."' WHEN fg_ref_animacion='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'req_anim', ";
  $Query .= " CASE WHEN (SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = 1) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'quiz', ";
  $Query .= " c.no_semanas, a.ds_vl_duracion, a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa, ";
  $Query .= " b.no_creditos, b.ds_duracion, c.no_horas, c.no_workload, c.cl_delivery, c.ds_credential, c.cl_type, c.ds_language, ";
  $Query .= " b.ds_programa, b.ds_learning, b.ds_metodo, b.ds_requerimiento,b.no_email_desbloquear ";
  $Query .= " FROM c_leccion_sp a LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) LEFT JOIN k_programa_detalle_sp c ON(c.fl_programa_sp=b.fl_programa_sp) ";
  $Query .= "WHERE b.fg_publico='1' GROUP BY b.fl_programa_sp ";                
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  $y = 0;
  $band = 0;
  for($i=0;$row=RecuperaRegistro($rs);$i++) {
    $fl_leccion_sp = $row[0];
    $nb_programa = str_texto($row[1]); 
    $no_semana = $row[8];
    $fl_programa_sp = $row[10];
    $nb_thumb = str_texto($row[11]); 
    $fg_nuevo_programa = $row[12];
    $no_creditos = $row[13];
    $ds_duracion = $row[14];
    $no_horas = $row[15];
    $no_workload = $row[16];
    $cl_delivery = str_texto($row[17]);
    $ds_credential = str_texto($row[18]);
    $cl_type = str_texto($row[19]);
    $ds_language = str_texto($row[20]);
    $ds_programa = str_uso_normal($row[21]);
    $ds_learning = str_uso_normal($row[22]);
    $ds_metodo = str_uso_normal($row[23]);
    $ds_requerimiento = str_uso_normal($row[24]);
    $no_email_desbloquear=$row['no_email_desbloquear'];
    
    
    switch ($cl_delivery) {
      case "O": $cl_delivery = "Online";    break;
      case "S": $cl_delivery = "On-Site";   break;
      case "C": $cl_delivery = "Combined";  break;
      case "OB": $cl_delivery = "Online / Blended";  break;
    }
    switch ($cl_type) {
      case 1: $cl_type = "Long Term Duration";    break;
      case 2: $cl_type = "Short Term Duration";   break;
      case 3: $cl_type = "Corporate";  break;
      case 4: $cl_type = "Long Term Duration(3 contracts, 1 per year)";  break;
    }

    $row = RecuperaValor("SELECT COUNT(*) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa_sp");
    $no_lecciones = ($row[0]);

    $img = PATH_HOME."/modules/fame/uploads/".$nb_thumb;
    # Style 
    if($i <= 1){
      $style_cuad = "col-sm-4 col-md-4 col-lg-4";
      $style_img  = "";
    }else{
      $style_cuad = "col-sm-4 col-md-4 col-lg-4";
      $style_img  = "";
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
    
    # Obtenemos la cantidad de students en este programa
    $Query  = "SELECT COUNT(*) FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
    $Query .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp." ";
    $row = RecuperaValor($Query);
    $no_studets = $row[0];
    # Obtenemos los grupos que existen de este programa en este instituto
    $Queryg  = "SELECT nb_grupo FROM c_usuario a LEFT JOIN k_usuario_programa b ON(a.fl_usuario=b.fl_usuario_sp) ";
    $Queryg .= "LEFT JOIN c_alumno_sp c ON(c.fl_alumno_sp=a.fl_usuario) ";
    $Queryg .= "WHERE  a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." AND fl_instituto=".$fl_instituto." AND b.fl_programa_sp=".$fl_programa_sp." AND nb_grupo<>'' GROUP BY c.nb_grupo ";
    $rsg = EjecutaQuery($Queryg);
    $no_groups = CuentaRegistros($rsg);                  
    # Obtenemos si el usuario se puede asignar solo al curso
    $rowg = RecuperaValor("SELECT fg_assign_myself_course FROM c_usuario WHERE fl_usuario=".$fl_usuario."");
    $fg_assign_myself_course = $rowg[0];
    # Obtenemos la informacion del programa
    $row00 = RecuperaValor("SELECT fl_usu_pro, ds_progreso, fg_terminado, fg_status_pro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa_sp");
    
    
     
    
   
    if($fl_perfil_sp==PFL_ESTUDIANTE_SELF){
        if($fg_plan_pago)
            $row00[0]=1;
    }
    
    # Esta asignado al curso
    if(!empty($row00[0])) {
        
          if($fg_puede_liberar_curso==1){
              
                  $fg_puede_tomar_curso=VerificaCumplientoRequisitoParaAccederCurso($fl_usuario,$fl_programa_sp,$no_email_desbloquear,$fg_plan_pago,$fl_pagado,$fg_assign_myself_course);
              
                  $fg_desbloqueado_por_envio_email=DesbloqueadoPorPagoCurso($fl_usuario,$fl_programa_sp);
              
                  if($fg_desbloqueado_por_envio_email){
                      $no_dias_faltan_terminar_plan=MuestraTiempoRestanteTrialCurso($fl_usuario,$fl_programa_sp);  
                  }else{
                      $no_dias_faltan_terminar_plan=" ";
                  }
              
                  if($fg_puede_tomar_curso){
                      

                          #Si ya tiene un progreso
                          if(  (!empty($row00['ds_progreso'])) && ($row00['ds_progreso'] <100) ) {
                              $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a> ".$no_dias_faltan_terminar_plan." ";/*Continue*/
                          }else if($row00['fg_terminado']==1){
                                $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>"; /*Review*/
                          }else{    

                              $btn  = " 
                                              <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                                <span class='h4'>                            
                                                    <a class='btn btn-success no-margin' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye fa-1x'></i>&nbsp;".ObtenEtiqueta(1149)."&nbsp;</a>
                                                </span>".$no_dias_faltan_terminar_plan." 
                                              </div>

                                          ";
                          }
                  
                  }else{
                      

                          #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
                          $btn="<div class='row' style='margin:0px;'>
                                    <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                                        <span class='h4' id='btn_desbloquear_curso'>                            
                                                            <a class='btn btn-danger no-margin' onclick='$(\"#ModalPrivacity\").modal(\"toggle\");DesabilitarPagarCurso(".$fl_programa_sp.",".$no_email_desbloquear.");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(2076)."&nbsp;</a>
                                                        </span> ".$no_dias_faltan_terminar_plan."  
                                                  
                                    </div>
                                </div> <br/>   
                                <div class='row' style='margin:0px;'>
                                     <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                               <span class='h4'> <a class='btn btn-primary btn-xs mikelangel'  href='index.php#site/node.php?node=158'    ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2108)." </a></span>
                                     </div>
                                </div>     
                                "; 
                        
                         // $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>"; /*Review*/
                          
                  }   
              
              
              
              
              
          }else{
        
              # Si ya termino el curso solo podra ver sus calificaciones
              # El boton  es color azul
              if($row00[2]==1){
                if($row00[3]==1)
                  $btn = "<a href='javascript:user_pause(".$row00[3].",".$fl_programa_sp.",".$fl_usuario.", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> ".ObtenEtiqueta(1999)."</a>";/*Paused*/
                else
                  $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>";                      
              }
              # Continua el curso
              else{
                # Si esta pausado no podra acceder al desktop
                # Tendra que enviar un correo al teacher o espera a que se lo activen
                if($row00[3]==1)
                  $btn = "<a href='javascript:user_pause(".$row00[3].",".$fl_programa_sp.",".$fl_usuario.", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> ".ObtenEtiqueta(1999)."</a>";
                else{
                  if(empty($row00[1]))
                    $btn = "<a  class='btn btn-success' onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
                  else
                    $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a>";/*Continue*/
                }
              }
              
          }#end else puede liberar curso.    
              
    }
    # No esta asignado al curso
    else{
      # Para los estudiantes
      if($fl_perfil_sp== PFL_ESTUDIANTE_SELF){
          
            if($fg_puede_liberar_curso==1){
                
                
                $fg_puede_tomar_curso=VerificaCumplientoRequisitoParaAccederCurso($fl_usuario,$fl_programa_sp,$no_email_desbloquear,'',$fl_pagado,$fg_assign_myself_course);
                
                if($fg_puede_tomar_curso){
                    
                     $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                <span class='h4'>                            
                                    <a class='btn btn-success no-margin' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye fa-1x'></i>&nbsp;".ObtenEtiqueta(1149)."&nbsp;</a>
                                </span>                          
                              </div>";
                    
                }else{
                    
                    #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
                    $btn="<div class='row' style='margin:0px;'>
                                <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                            <span class='h4' id='btn_desbloquear_curso'>                            
                                                <a class='btn btn-danger no-margin' onclick='$(\"#ModalPrivacity\").modal(\"toggle\");DesabilitarPagarCurso(".$fl_programa_sp.",".$no_email_desbloquear.");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(2076)."&nbsp;</a>
                                            </span> 
                                           
                                </div>
                          </div> <br/>     
                          <div class='row' style='margin:0px;'>
                               <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                               <span class='h4'> <a class='btn btn-primary btn-xs mikelangel'style='magin-left:0px;   href='index.php#site/node.php?node=158'  ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2108)." </a></span>
                               </div>
                         </div>      
                          
                          ";

                }                
                
                
                
                
            }else{    
                    # Verificamos si el estudiante puede asignarse solo al curso
                    if($fg_assign_myself_course==1)                        
                      $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
                    else{
                      $btn  = "
                              <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                <span class='h4'>                            
                                  <a class='btn btn-danger no-margin' href='javascript: myself_layout($fl_programa_sp, $fl_usuario, 122, \"courses_library.php\");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(1888)."&nbsp;<i class='fa fa-envelope-o fa-1x'></i></a>
                                </span>                          
                              </div>";
                    }
        
            }#end else 
        
        
        
      }
      else                      
        $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
    }
  
    # Depende del perfil muestra grupos
    if($fl_perfil_sp == PFL_MAESTRO_SELF or $fl_perfil_sp == PFL_ADMINISTRADOR){
       $groups  = "<a data-toggle='collapse' href='#Collapse_".$i."_1' aria-expanded='false' aria-controls='collapseExample' ";
       $groups .= "onclick='funcion1(\"Collapse_".$i."_1\"); groups_lecc(".$fl_programa_sp.", \"G\", ".$i.");'><center>Groups (".$no_groups.")</center></a>";      
    }
    else{
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
        }
        else {
          element.style.display='none';
          element_tit.style.display='block';
        }
      }
      // Muestra la lista de playlist en cursos
      function DespliegaLista__".$i."(actual, muestra){
        $.ajax({
          type: 'POST',
          url : 'site/listado_ind_palylist.php',
          async: false,
          data: 'fl_programa_sp=".$fl_programa_sp."'+
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
        ActListaGenetal();"; 

      $script_play .= "NewPlaylistCourse_2(".$fl_programa_sp."); ";

		
         /* for($x=1; $x<=$row_cont_cur[0]; $x++){
           $script_play .= "
            if(document.getElementById('zzzz_".$x."')){
              NewPlaylistCourse_2(".$x.");
            }";        
          }*/
        //  $script_play .= "
         // }   
	$script_play .= "
			}	
     </script>";
     # style categorias
     if($y == 2)
        $style_esp = "left: -332px;";
      else
        $style_esp = "";
     # info curse general
     $dl =
     "<dl class='dl-horizontal'>
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
    }
    else{
        
        if( ($fl_perfil_sp==PFL_ESTUDIANTE_SELF)&&($fl_instituto==4)){
             
               if($fg_puede_liberar_curso==1){
                   if(empty($fg_puede_tomar_curso)){
                     
                       $btn_pre = "<a class='btn btn-primary btn-xs' href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1' ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2008)." </a>";
                       
                   }else{
                       $btn_pre = "";  
                   }
                   
               }else{
                       $btn_pre = "";  
               
               }
            
            
            
            
        }else{
            $btn_pre = "";  
        }
        
        
          
    }
    # Progreso del curso
    $progreso = RecuperaValor("SELECT ds_progreso FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa_sp ");
    $progreso = $progreso[0];
    if(empty($progreso))
      $progreso = 0;
    
    $fl_programa_sp_i = $fl_programa_sp;
    $y++;
    
    if($y == 3){
      $band = 1;
      $limit = $fl_programa_sp_i;
    }
    else{
      if($i == ($registros - 1)){
        $band = 1;
        $limit = $fl_programa_sp_i;
      }
      else
        $band = 0 ;
    }
    
    # Divs ocultos para las lecciones y los grupos
    if($band == 1){
      $div = "</div>";
      $div = "<div class='row'>";
        $Query2  = " SELECT fl_leccion_sp, nb_programa, no_semana, ds_titulo, ds_leccion, ";
        $Query2 .= " CASE WHEN ds_vl_ruta IS NULL THEN '".ObtenEtiqueta(17)."' WHEN ds_vl_ruta='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'Video Brief', ";
        $Query2 .= " CASE WHEN fg_ref_animacion IS NULL THEN '".ObtenEtiqueta(17)."' WHEN fg_ref_animacion='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'req_anim', ";
        $Query2 .= " CASE WHEN (SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = 1) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'quiz', ";
        $Query2 .= " c.no_semanas, a.ds_vl_duracion, a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa ";
        $Query2 .= " FROM c_leccion_sp a LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) LEFT JOIN k_programa_detalle_sp c ON(c.fl_programa_sp=b.fl_programa_sp) ";
        $Query2 .= " WHERE a.fl_programa_sp <=$limit ";
        $Query2 .= " GROUP BY b.fl_programa_sp ";                        
        $rs2 = EjecutaQuery($Query2);
        $registros2 = CuentaRegistros($rs2);
        $h = 0;
        for($i2=0;$row2=RecuperaRegistro($rs2);$i2++) {   
          $fl_programa_sp_i = $row2[10];
          $x_i = $i2;
          if($h == 0)
            $ste = "style='width: 31%;'";
          if($h == 1)
            $ste = "style='width: 110%;'";
          if($h == 2)
            $ste = "style='width: 190%;'";
          $top = 25 + $i;
          $div .= 
          "
          <div class='superbox col-xs-12 col-sm-12 col-md-12 col-lg-12'> 
            <!-- ICH: Inicia Div Muestra Grupos -->
            <div class='collapse' id='Collapse_".$x_i."_1' style='position:relative; top:-".$top."px;'>
              <div class='card card-block'>
                <div class='superbox-list active' ".$ste."></div>
                <div class='superbox-show' style='display: block; padding: 1px 1px 1px 1px; background-color: #ccc;'>
                  <div class='widget-body'>
                    <div class='panel-group smart-accordion-default' id='accordion_groups_".$x_i."_1'>
                      <i class='fa fa-cog fa-spin txt-color-blueDark hidden' id='fa-grp-".$x_i."_1'></i>
                    </div>
                  </div>
                </div>        
              </div>
            </div>
            <!-- ICH: Termina Div Muestra Grupos -->
            <!-- ICH: Inicia Div Muestra Lecciones -->
            <div class='collapse' id='Collapse_".$x_i."_2' style='position:relative; top:-23px;'>
              <div class='card card-block'>
                <div class='superbox-list active' ".$ste."></div>
                <div class='superbox-show' style='display: block; padding: 1px 1px 1px 1px; background-color: #ccc;'>
                  <div class='widget-body'>
                    <div class='panel-group smart-accordion-default'>
                      <div class='panel panel-default'>
                        <div class='panel-collapse collapse in' aria-expanded='false' style='height: auto;'>
                          <div class='panel-body no-padding' id='accordion_lecciones_".$x_i."_2'>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>        
              </div>
            </div>
            <!-- ICH: Termina Div Muestra Lecciones -->
          </div>";          
          $h++;
          if($h == 3)
            $h = 0;                            
        }
      
      $y = 0; 
    }
  
    
    
    # Info items
    $result["item".$i] = array(
      "style" => $style_cuad,
      "file" => $img ,
      "programa" => $nb_programa,
      "style_img" => $style_img,
      "fg_nuevo_programa" => $fg_nuevo_programa,
      "label_new_pro" => ObtenEtiqueta(1243),
      "tot_students" => $no_studets,
      "groups" => $groups,
      "fl_programa" => $fl_programa_sp,
      "script_play" => $script_play,
      "no_lecc" => $no_lecciones." ".ObtenEtiqueta(1242),
      "style_esp" => $style_esp,
      "dl" => $dl,
      "btn" => $btn,
      "btn_pre" => $btn_pre,
      "progreso"=> $progreso,
      "band" => $band,
      "band_div" => $div
    );
  }
  $result["result"] = array(
    "tot_reg" => $registros
    );
	echo json_encode((Object) $result);
?>