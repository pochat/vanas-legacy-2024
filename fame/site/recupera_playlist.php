<?php

  # Libreria de funciones
  require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);
  
  #Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
  if($perfil_usuario==PFL_ESTUDIANTE_SELF)
  $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);

  # Variable initializtion
  $fg_puede_liberar_curso=NULL;

  #Identifiacmos si el logueado es b2c
  if($fg_puede_liberar_curso)
	  $fg_b2c=1;
  else
	  $fg_b2c=0;
  #Los trials no pueden ver boton export
  $fg_plan_instituto_=ObtenPlanActualInstituto($fl_instituto);
  if(empty($fg_plan_instituto_))
    $fg_b2c=1;
  
  # Accion principal
  $accion = !empty($_REQUEST['accion'])?$_REQUEST['accion']:NULL;
  # Valor (cadena)a buscar
  $valor = !empty($_REQUEST['valor'])?$_REQUEST['valor']:NULL;
  # Valor (cadena)a buscar
  $extra = !empty($_REQUEST['extra'])?$_REQUEST['extra']:NULL;
  
  $no=$extra-1;
  
  $dat="Cerrar_".$no;
  
   $Querym="SELECT fl_dialog FROM c_dialog_play_list WHERE 1=1 ";
   $rsm = EjecutaQuery($Querym);

   for($m=0;$rowm = RecuperaRegistro($rsm);$m++){
       
          $no_id=str_texto($rowm[0]);
		  
		if($no_id>0){
          echo"<script>
                 document.getElementById('$no_id').click();
              </script>
          ";
		}
   }

  EjecutaQuery("DELETE FROM c_dialog_play_list WHERE 1=1 ");
  
  $Query="INSERT INTO c_dialog_play_list (fl_dialog)VALUES('$dat') ";
  EjecutaInsert($Query);

  #Se agrega validacion para saber si el Instituto puede ver boton de export Moodle
  if(VerBotonExportMoodle($fl_instituto)){
	$fg_export_moodle=1;
  }else{
    $fg_export_moodle=0;
  }  
  echo"<style>
.media, .media-body {
    overflow: visible !important;
}
</style>";
  # Listado que permite relacionar un curso con un playlist
  if($valor == 'add_curso_playlist'){
            echo "<div id='div_p'></div>";
            echo " <ul class='list-unstyled'>";
              $row_count = RecuperaValor("SELECT COUNT(1) FROM c_playlist WHERE fl_usuario = $fl_usuario ");
               $row_count[0];
              if(!empty($row_count[0])){
                echo " <div class='bs-example'>
                  <ul class='list-unstyled'>";
          
                    echo "<li><strong>".ObtenEtiqueta(1350)."</strong></li>";
                    $rs = EjecutaQuery("SELECT nb_playlist, fl_playlist FROM c_playlist WHERE fl_usuario = $fl_usuario ORDER BY nb_playlist ASC ");
                    for($i=0;$row = RecuperaRegistro($rs);$i++){
                      $nb_playlist = str_texto($row[0]);
                      $nb_playlist_2 = strtolower($nb_playlist);
                      $fl_playlist = ($row[1]);
              
                      $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $fl_playlist ");
                      $rel_curso_play = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_programa_sp = $extra AND fl_playlist_padre = $fl_playlist ");
                      if(!empty($rel_curso_play[0])){
                        // Opcion para borrar relacion curso - playlist
                        $opc_borra_relacion = "<a href='javascript:confirma_borra({$fl_playlist},{$extra});'><i class='fa fa-times' style='color:#9aa7af; font-size:10px;'></i></a>";
                        // Opcion para crear relacion curso - playlist
                         $opc_crea_relacion = "href='javascript:exists();'";
                      }else{
                        $opc_borra_relacion = "";
                        # Esta variable sirve para desplegar el modal de seleccion multiple
                        $opc_crea_relacion = "href='javascript:checkreq($extra, $fl_playlist);'";
                        # Se comenta por que se agrego el modal de seleccion multiple
                        //$opc_crea_relacion = "href='javascript:RelCursoPlaylist({$extra},{$fl_playlist}); tt(); NewPlaylistCourse__{$extra}(); '";
                      }
              
              #<div style='width:10px; height:0px;' class='{$nb_playlist_2}'> se quita la clse ya que oculta el boton borrar
                      echo "<li>
                          <div class='nb_playlist__$extra'>
                            <div style='width:10px; height:0px;' class='{$nb_playlist_2}'  >";
                              if(!empty($rel_curso_play[0]))
                                echo $opc_borra_relacion;
                              echo "&nbsp;&nbsp;
                            </div>
                            <div style='padding-left:15px;'>
                              <a {$opc_crea_relacion}>{$nb_playlist} ({$count_playlist[0]})</a>
                            </div>
                        </div></li>";
                    }
                    $rest = $row_count[0] - 2;
                  echo "</ul>
                </div>"; 
              }else{
                echo "<div class='note'>
                  <p><center><strong>".ObtenEtiqueta(1257)."</strong></center></p>
                </div>";
              }   
            echo "</ul>";     
  }

  if($accion == 'guarda_rel_cp'){
            $row_count = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_programa_sp = $valor AND fl_playlist_padre = $extra ");

            # Query to know the next no_orden in k_playlist_course
            $no_orden = RecuperaValor("SELECT (SELECT IF((SELECT no_orden FROM k_playlist_course k WHERE fl_playlist_padre = $extra ORDER BY no_orden DESC LIMIT 1) IS NULL, 1, (SELECT no_orden FROM k_playlist_course k WHERE fl_playlist_padre = $extra ORDER BY no_orden DESC LIMIT 1)+1 )) AS no_orden;");
            if(empty($row_count[0]))
            EjecutaQuery("INSERT INTO k_playlist_course (fl_programa_sp, no_orden, fl_playlist_padre) VALUES ($valor, $no_orden[0], $extra)");
  }
  
  # Actualiza lista principal para filtro de playlist
  if($valor=='actualiza_lista'){    
       echo " <ul class='list-unstyled'>";
      
        $msj = ObtenEtiqueta(1263);
        echo " <div class='bs-example'>
          <ul class='list-unstyled'>";
            echo "<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:FiltroPlaylist(0); ActNom(\"$msj\");'><span style='color:#9aa7af; font-style: italic;'>".ObtenEtiqueta(1275)."</span></a></li>";
           
            # Playlist compartidos por Docenetes para Estudiantes
            if($perfil_usuario == PFL_ESTUDIANTE_SELF){
              // Obtenemos usuario admin de la institucion
              $fl_admin_ins = RecuperaValor("SELECT fl_usuario_sp FROM c_instituto WHERE fl_instituto = $fl_instituto");
              // Recuperamos maestros de alumnos
              $arma_maestros2 = "";
              $rs_p = EjecutaQuery("SELECT fl_maestro FROM k_usuario_programa WHERE fl_usuario_sp = $fl_usuario GROUP BY fl_maestro");
              $retered = CuentaRegistros($rs_p);
              for($i=1;$row = RecuperaRegistro($rs_p);$i++){
                  
                $arma_maestros2 = $arma_maestros2." b.fl_usuario = $row[0]  ";
                if($i<=($retered-1))
                    $arma_maestros2 .="OR ";
                    
              }
              // Recuperamos playlist de admin y docentes del alumno
              //$rs = EjecutaQuery("SELECT a.nb_playlist, a.fl_playlist, b.ds_nombres, b.ds_apaterno FROM c_playlist a, c_usuario b WHERE a.fl_usuario = b.fl_usuario AND (b.fl_usuario = $fl_admin_ins[0] OR ($arma_maestros2)) ORDER BY a.fl_usuario, a.nb_playlist ASC ");
              
              #muestra los plylist de los maestros del instituto   
              $rs = EjecutaQuery("SELECT a.nb_playlist, a.fl_playlist, b.ds_nombres, b.ds_apaterno FROM c_playlist a, c_usuario b WHERE fl_instituto=".$fl_instituto." AND a.fl_usuario = b.fl_usuario
                 ORDER BY a.fl_usuario, a.nb_playlist ASC ");

              $cuantos = CuentaRegistros($rs);
              if(!empty($cuantos)){
                echo "<li><strong>".ObtenEtiqueta(1351)."</strong></li>";
                
                for($i=0;$row = RecuperaRegistro($rs);$i++){
                  $nb_playlist = str_texto($row[0]);
                  $nb_playlist_2 = strtolower($nb_playlist);
                  $fl_playlist = ($row[1]);
                  $nb_crea_playlist = str_texto($row[2]);
                  $ds_crea_playlist = str_texto($row[3]);
                  
                  $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $fl_playlist ");
                  
                  echo "<li>
                  <div class='{$nb_playlist_2}'>
                    <div style='padding-left:20px;' class='nb_playlist_doc'>
                      <div class='{$nb_playlist_2}'>
                        <a href='javascript:FiltroPlaylist({$fl_playlist}); ActNom(\"$nb_playlist\");'>{$nb_playlist} ({$count_playlist[0]})</a> <span style='color:#9aa7af; font-style: italic; font-size:12px;'> By {$nb_crea_playlist} {$ds_crea_playlist}</span>
                      </div>
                    </div>
                  </div>
                  </li>";
                }
              }
            }else{
              # Playlist compartidos por docentes y admin de la misma institución
              // Recuperamos playlist de admin y docentes del alumno
              $rs = EjecutaQuery("SELECT b.nb_playlist, b.fl_playlist, a.ds_nombres, a.ds_apaterno FROM c_usuario a, c_playlist b WHERE a.fl_instituto = $fl_instituto AND a.fl_perfil_sp != ".PFL_ESTUDIANTE_SELF." AND a.fl_usuario != $fl_usuario AND a.fl_usuario = b.fl_usuario ORDER BY b.fl_usuario, b.nb_playlist ASC");
              $cuantos = CuentaRegistros($rs);
              if(!empty($cuantos)){
                echo "<li><strong>".ObtenEtiqueta(1351)."</strong></li>";
                
                for($i=0;$row = RecuperaRegistro($rs);$i++){
                  $nb_playlist = str_texto($row[0]);
                  $nb_playlist_2 = strtolower($nb_playlist);
                  $fl_playlist = ($row[1]);
                  $nb_crea_playlist = str_texto($row[2]);
                  $ds_crea_playlist = str_texto($row[3]);
                  
                  $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $fl_playlist ");
                  
                  echo "<li>
                  <div class='{$nb_playlist_2}'>
                    <div style='padding-left:20px;' class='nb_playlist_doc'>
                      <div class='{$nb_playlist_2}'>
                        <a href='javascript:FiltroPlaylist({$fl_playlist}); ActNom(\"$nb_playlist\");'>{$nb_playlist} ({$count_playlist[0]})</a> <span style='color:#9aa7af; font-style: italic; font-size:12px;'> By {$nb_crea_playlist} {$ds_crea_playlist}</span>
                      </div>
                    </div>
                  </div>
                  </li>";
                }
              }
            }
            
            echo "<p></p>";
            # Playlist registrados por el usuario
            echo "<li><strong>".ObtenEtiqueta(1350)."</strong></li>";
            $count_usr = RecuperaValor("SELECT COUNT(1) FROM c_playlist WHERE fl_usuario = $fl_usuario ");
            if(!empty($count_usr[0])){
              $rs = EjecutaQuery("SELECT nb_playlist, fl_playlist FROM c_playlist WHERE fl_usuario = $fl_usuario ORDER BY nb_playlist ASC ");
              for($i=0;$row = RecuperaRegistro($rs);$i++){
                $nb_playlist = str_texto($row[0]);
                $nb_playlist_2 = strtolower($nb_playlist);
                $fl_playlist = ($row[1]);
                
                $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $fl_playlist ");
                
                echo "<li>
                  <div class='nb_playlist'>
				  <div style='width:10px; height:0px; padding-left:20px;'  class='{$nb_playlist_2}'> 
                    <!--<div style='width:10px; height:0px; padding-left:20px;'  >-->
                      <a href='javascript:confirma_borra({$fl_playlist}, 0);'>
                        <i class='fa fa-times' style='color:#9aa7af; font-size:10px;'></i>
                      </a>&nbsp;&nbsp;
                    </div>
                    <div style='padding-left:35px;' >
                      <a href='javascript:FiltroPlaylist({$fl_playlist}); ActNom(\"$nb_playlist\");'>{$nb_playlist} ({$count_playlist[0]})</a>
                    </div>
                  </div>
                </li>";
              }
            }else{
              echo "<div class='note'>
                <p><center><strong>".ObtenEtiqueta(1257)."</strong></center></p>
              </div>";              
            }
            
            $rest = $row_count[0] - 2;
          echo "</ul>
        </div>"; 
      // }else{
        // echo "<div class='note'>
          // <p><center><strong>".ObtenEtiqueta(1257)."</strong></center></p>
        // </div>";
      // }   
    echo "</ul>";    
  }
  
  # Busca un playlist
  if (!empty($valor))
    $qry = "AND nb_playlist LIKE '%$valor%' ";
  if($accion == 'busca'){

    echo " <div class='bs-example'>
      <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
        <ul class='list-unstyled'>";
          $row_count = RecuperaValor("SELECT COUNT(1) FROM c_playlist WHERE fl_usuario = $fl_usuario {$qry} ");        
          if(!empty($row_count[0])){
            $msj = ObtenEtiqueta(1263);
            echo " <div class='bs-example'>
              <ul class='list-unstyled'>";
                // echo "<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:busca_playlist();'><span style='color:#9aa7af; font-style: italic;'>".ObtenEtiqueta(1275)."</span></a></li>";
                // echo "<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:FiltroPlaylist(0); ActNom(\"$msj\");'><span style='color:#9aa7af; font-style: italic;'>".ObtenEtiqueta(1275)."</span></a></li>";
                $rs = EjecutaQuery("SELECT nb_playlist, fl_playlist FROM c_playlist WHERE fl_usuario = $fl_usuario  {$qry} ORDER BY nb_playlist ASC ");
                for($i=0;$row = RecuperaRegistro($rs);$i++){
                  $nb_playlist = str_texto($row[0]);
                  $fl_playlist = ($row[1]);
                    
                      $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $fl_playlist ");
                      
                      $mtra_cont = "(".$count_playlist[0].")";
                    if($extra != 0){
                        $funciones = "FiltroPlaylist($fl_playlist); ActNom(\"$nb_playlist\");";
                    }
                   
                    
                    echo "<li>
                    <div style='width:10px; height:0px;'><a href='javascript:confirma_borra({$fl_playlist},{$extra});'><i class='fa fa-times' style='color:#9aa7af; font-size:10px;'></i></a>&nbsp;&nbsp;</div>
                    <div style='padding-left:15px;'><a href='javascript:{$funciones}'>{$nb_playlist} {$mtra_cont}</a></div>
                    </li>";
                }
                $rest = $row_count[0] - 2;
              echo "</ul>
            </div>"; 
          }else{
            echo "
            <div class='note'>
              <p><center><strong>".ObtenEtiqueta(1257)."</strong></center></p>
            </div>";
          }   
        echo "</ul>
      </div>
    </div>";
  }
  
  # Guarda un nuevo playlist
  if($accion == 'guarda'){
    if(!empty($valor)){
      $fl_playlist_padre=EjecutaInsert("INSERT INTO c_playlist (no_grados, nb_playlist, fl_usuario) VALUES (0, '$valor', $fl_usuario)");
    }
    if(!empty($extra)){
      
      # Query to know the next no_orden in k_playlist_course
      $no_orden = RecuperaValor("SELECT (SELECT IF((SELECT no_orden FROM k_playlist_course k WHERE fl_playlist_padre = $extra ORDER BY no_orden DESC LIMIT 1) IS NULL, 1, (SELECT no_orden FROM k_playlist_course k WHERE fl_playlist_padre = $extra ORDER BY no_orden DESC LIMIT 1)+1 )) AS no_orden;");
      EjecutaQuery("INSERT INTO k_playlist_course (fl_programa_sp, no_orden, fl_playlist_padre) VALUES ($extra, $no_orden[0], $fl_playlist_padre);");
    }
  } 
  
  # Borra un playlist
  if($accion == 'borrar'){
    if(!$extra) {
      EjecutaQuery("DELETE FROM c_playlist WHERE fl_playlist = '$valor'");
      EjecutaQuery("UPDATE k_usuario_programa SET fl_playlist = NULL, fg_asignado_playlist = NULL, fg_revisado_alumno = NULL WHERE fl_playlist = '$valor'");
    } else {
      EjecutaQuery("DELETE FROM k_playlist_course WHERE fl_programa_sp = $extra AND fl_playlist_padre = $valor");
      EjecutaQuery("UPDATE k_usuario_programa SET fl_playlist = NULL, fg_asignado_playlist = NULL, fg_revisado_alumno = NULL WHERE fl_playlist = '$valor'");   
    }
      echo " <div class='bs-example'>
      <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
        ";
    echo " <ul class='list-unstyled'>";
      
        $msj = ObtenEtiqueta(1263);
        echo " <div class='bs-example'>
          <ul class='list-unstyled'>";
            echo "<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:FiltroPlaylist(0); ActNom(\"$msj\");'><span style='color:#9aa7af; font-style: italic;'>".ObtenEtiqueta(1275)."</span></a></li>";
           
            # Playlist compartidos por Docenetes para Estudiantes
            if($perfil_usuario == PFL_ESTUDIANTE_SELF){
              // Obtenemos usuario admin de la institucion
              $fl_admin_ins = RecuperaValor("SELECT fl_usuario_sp FROM c_instituto WHERE fl_instituto = $fl_instituto");
              // Recuperamos maestros de alumnos
              $arma_maestros2 = "";
              $rs_p = EjecutaQuery("SELECT fl_maestro FROM k_usuario_programa WHERE fl_usuario_sp = $fl_usuario GROUP BY fl_maestro");
              for($i=0;$row = RecuperaRegistro($rs_p);$i++){
                $arma_maestros2 = $arma_maestros2." b.fl_usuario = $row[0] ";
              }
              // Recuperamos playlist de admin y docentes del alumno
              $rs = EjecutaQuery("SELECT a.nb_playlist, a.fl_playlist, b.ds_nombres, b.ds_apaterno FROM c_playlist a, c_usuario b WHERE a.fl_usuario = b.fl_usuario AND (b.fl_usuario = $fl_admin_ins[0] OR ($arma_maestros2)) ORDER BY a.fl_usuario, a.nb_playlist ASC ");
              
              $cuantos = CuentaRegistros($rs);
              if(!empty($cuantos)){
                echo "<li><strong>".ObtenEtiqueta(1351)."</strong></li>";
                
                for($i=0;$row = RecuperaRegistro($rs);$i++){
                  $nb_playlist = str_texto($row[0]);
                  $nb_playlist_2 = strtolower($nb_playlist);
                  $fl_playlist = ($row[1]);
                  $nb_crea_playlist = str_texto($row[2]);
                  $ds_crea_playlist = str_texto($row[3]);
                  
                  $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $fl_playlist ");
                  
                  echo "<li>
                  <div class='{$nb_playlist_2}'>
                    <div style='padding-left:20px;' class='nb_playlist_doc'>
                      <div class='{$nb_playlist_2}'>
                        <a href='javascript:FiltroPlaylist({$fl_playlist}); ActNom(\"$nb_playlist\");'>{$nb_playlist} ({$count_playlist[0]})</a> <span style='color:#9aa7af; font-style: italic; font-size:12px;'> By {$nb_crea_playlist} {$ds_crea_playlist}</span>
                      </div>
                    </div>
                  </div>
                  </li>";
                }
              }
            }else{
              # Playlist compartidos por docentes y admin de la misma institución
              // Recuperamos playlist de admin y docentes del alumno
              $rs = EjecutaQuery("SELECT b.nb_playlist, b.fl_playlist, a.ds_nombres, a.ds_apaterno FROM c_usuario a, c_playlist b WHERE a.fl_instituto = $fl_instituto AND a.fl_perfil_sp != ".PFL_ESTUDIANTE_SELF." AND a.fl_usuario != $fl_usuario AND a.fl_usuario = b.fl_usuario ORDER BY b.fl_usuario, b.nb_playlist ASC");
              $cuantos = CuentaRegistros($rs);
              if(!empty($cuantos)){
                echo "<li><strong>".ObtenEtiqueta(1351)."</strong></li>";
                
                for($i=0;$row = RecuperaRegistro($rs);$i++){
                  $nb_playlist = str_texto($row[0]);
                  $nb_playlist_2 = strtolower($nb_playlist);
                  $fl_playlist = ($row[1]);
                  $nb_crea_playlist = str_texto($row[2]);
                  $ds_crea_playlist = str_texto($row[3]);
                  
                  $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $fl_playlist ");
                  
                  echo "<li>
                  <div class='{$nb_playlist_2}'>
                    <div style='padding-left:20px;' class='nb_playlist_doc'>
                      <div class='{$nb_playlist_2}'>
                        <a href='javascript:FiltroPlaylist({$fl_playlist}); ActNom(\"$nb_playlist\");'>{$nb_playlist} ({$count_playlist[0]})</a> <span style='color:#9aa7af; font-style: italic; font-size:12px;'> By {$nb_crea_playlist} {$ds_crea_playlist}</span>
                      </div>
                    </div>
                  </div>
                  </li>";
                }
              }
            }
            
            echo "<p></p>";
            # Playlist registrados por el usuario
            echo "<li><strong>".ObtenEtiqueta(1350)."</strong></li>";
            $count_usr = RecuperaValor("SELECT COUNT(1) FROM c_playlist WHERE fl_usuario = $fl_usuario ");
            if(!empty($count_usr[0])){
              $rs = EjecutaQuery("SELECT nb_playlist, fl_playlist FROM c_playlist WHERE fl_usuario = $fl_usuario ORDER BY nb_playlist ASC ");
              for($i=0;$row = RecuperaRegistro($rs);$i++){
                $nb_playlist = str_texto($row[0]);
                $nb_playlist_2 = strtolower($nb_playlist);
                $fl_playlist = ($row[1]);
                
                $count_playlist = RecuperaValor("SELECT COUNT(1) FROM k_playlist_course WHERE fl_playlist_padre = $fl_playlist ");
                
                echo "<li>
                  <div class='nb_playlist'>
                    <div style='width:10px; height:0px; padding-left:20px;'  class='{$nb_playlist_2}'>
                      <a href='javascript:confirma_borra({$fl_playlist}, 0);'>
                        <i class='fa fa-times' style='color:#9aa7af; font-size:10px;'></i>
                      </a>&nbsp;&nbsp;
                    </div>
                    <div style='padding-left:35px;' >
                      <a href='javascript:FiltroPlaylist({$fl_playlist}); ActNom(\"$nb_playlist\");'>{$nb_playlist} ({$count_playlist[0]})</a>
                    </div>
                  </div>
                </li>";
              }
            }else{
              echo "<div class='note'>
                <p><center><strong>".ObtenEtiqueta(1257)."</strong></center></p>
              </div>";              
            }
            
            $rest = $row_count[0] - 2;
          echo "</ul>
        </div>"; 
      // }else{
        // echo "<div class='note'>
          // <p><center><strong>".ObtenEtiqueta(1257)."</strong></center></p>
        // </div>";
      // }   
    echo "</ul>"; 
        echo "
      </div>
    </div>";    
  }

  # Muestra lista de playlist filtrados
  if($accion == 'filtra_playlist'){
      
      
   #Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
   if($perfil_usuario==PFL_ESTUDIANTE_SELF)
   $fg_puede_liberar_curso=PuedeLiberarCurso($fl_instituto,$fl_usuario);
      
      
   
   #Recupermros quein fue quien creo el playlist.
   $Query="SELECT fl_usuario,fg_editable,no_credito,no_tot_credito FROM c_playlist WHERE fl_playlist=$valor ";
   $rowp=RecuperaValor($Query);
   $fl_usuario_creo_play=$rowp['fl_usuario'];
   $fg_editable_playlist=$rowp['fg_editable'];
   $no_your_creditos=$rowp['no_credito'];
   $no_total_credito=$rowp['no_tot_credito'];
   
   $Qury="SELECT CONCAT(ds_nombres,' ',ds_apaterno),fl_perfil_sp FROM c_usuario WHERE fl_usuario = $fl_usuario_creo_play ";
   $ro=RecuperaValor($Qury);
   $nb_usuario_cretion=str_texto($ro[0]);
   $fl_perfil_crea_play=$ro[1];
   
   if($fl_perfil_crea_play==PFL_ESTUDIANTE_SELF)
	   $nb_usuario_cretion=$nb_usuario_cretion." (Student)";
  if($fl_perfil_crea_play==PFL_MAESTRO_SELF)
	   $nb_usuario_cretion=$nb_usuario_cretion." (Teacher)";
   
   if(empty($no_your_creditos))
       $no_your_creditos=0;
   if(empty($no_total_credito))
       $no_total_credito=0;
   
   if($fg_editable_playlist==1)
       $chequed="checked='checked' ";
   else
       $chequed="";
   
   
   
   if($fl_usuario_creo_play==$fl_usuario){
       
        #va dejar todo nromal.
        $dagrable="sortable grid";
        $cursor="cursor:move";
		$data_togle="data-toggle='tooltip' ";
		$title_toltip="title='Drag to order your playlist' ";
		  
   }else{
   
        #Verificamos el permiso para saber si tiene chance de reordenar el playlist
        if($fg_editable_playlist==1){
            $dagrable="sortable grid";
            $cursor="cursor:move";
			$data_togle="data-toggle='tooltip' ";
			$title_toltip="title='Drag to order your playlist' ";
        }else{
            $dagrable="";
			$cursor="";
			$data_togle="";
			$title_toltip="";
        }
       
        
   
   }
   
   if($valor){
	  
	  echo "<script>

          $('#mi_prueba').hide();
	  </script>";
	  
    }
   
   
   
?>
   <style>
       .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
       right:-9px;
       }
       .onoffswitch-switch {
       right:45px;
       }
   </style>

    <section id="widget-grid" class="">

        <div class="row">
            <div class="col-md-4 text-left">
                <?php  if($fl_usuario_creo_play==$fl_usuario){ #Solo se muestra el boton,si y solo si el usuario que lo creo es el mimso que el que esta loegeuado. ?> 
                <div class="smart-form" style="float:left">

                 
                        <span style="font-size:14px;">Ability to modify Playlist Order?:&nbsp;&nbsp;&nbsp;</span> 
                            <span class="onoffswitch">
								<input name="start_interval" class="onoffswitch-checkbox" <?php echo $chequed;?> id="start_interval" type="checkbox">
								<label class="onoffswitch-label" for="start_interval"> 
								    <span class="onoffswitch-inner" style="font-size:12px;" data-swchon-text="Public" data-swchoff-text="Only me"></span> 
								    <span class="onoffswitch-switch"></span> 

								</label> 

                            </span> 
                    <script>

                        $(document).ready(function () {

                            $('#start_interval').change(function () {

                                if ($('#start_interval').is(':checked')) {

                                    var publico = 1;
                                } else {
                                    var publico = 0;

                                }
                               

                                //pasamos epor ajax el valor para indicar que el playlist sera publico o privado
                                $.ajax({
                                    type: 'POST',
                                    url: 'site/hacer_publico_playlist.php',
                                    data: 'fl_play_list=<?php echo $valor; ?>'+
                                          '&fg_publico='+ publico,
                                          

                                    async: true,
                                    success: function (html) {
                                        //$('#presenta_opc_renovacion').html(html);
                                    }
                                });



                            });


                        });
                         </script>

                      

                   

                </div>
               
                <?php  } ?>
				
				<?php 
				
				$Qu="SELECT nb_playlist FROM c_playlist WHERE fl_playlist=$valor ";
				$qjd=RecuperaValor($Qu);
				$nb_playlist_edit=$qjd[0];
				
				?>
				 <br>
				 
                 <h5 style="font-size:15px;color:#000;">Playlist created by: <?php echo $nb_usuario_cretion; ?></h5>
				 <h6><a href="form-x-editable.html#" id="nb_playlist_edit" data-type="text" data-pk="1" data-original-title="<?php echo ObtenEtiqueta(2578);?>"><?php echo $nb_playlist_edit; ?> </a>
                     </h6>               
				
                <br />

            </div>
            <div class="col-md-8">
                        <ul id='sparks' class='padding-bottom-5' style="margin-right:15px;"><!-- sparks -->
                            <ul id='Ul1' class='padding-bottom-5'>
                             

                                <li class='sparks-info'>
                                <h5>Your School Academic Credits: <span style="font-size:12px;">
                                    <?php if($fl_usuario_creo_play==$fl_usuario){ ?>
                                    <a href="form-x-editable.html#" id="total_your_cred" data-type="text" data-pk="1" data-original-title="How many credits per hour does your institution grant?"><?php echo $no_your_creditos; ?> </a>
                                    
									
									<?php }else{
                                              echo $no_your_creditos;  
                                          }?>

                                   </span><span style="font-size:12px;float: right; margin-top: -21px;" id="total_acad_credit" ><?php echo $no_total_credito;?> </span>

                                   

                                </h5>
                              </li>
                              <li class='sparks-info'>
                                <h5>FAME Academic Credits: <span id='tota_cred' style="font-size:12px;"><?php echo $no_creditos_; ?></span></h5>
                              </li>
                              <li class='sparks-info'>
                                <h5>Assignment Workload: <span id='tota_hrs' style="font-size:12px;"><?php echo $no_horas_; ?></span></h5>
                              </li>
                             
                          
                            </ul>
                        </ul> 
                        <!-- end sparks -->

                <script>
                    //editables
                    $('#total_your_cred').editable({

                        //alert('fua');

                         url: 'site/actualiza_credito.php',
                        type: 'text',
                        pk: '<?php echo $valor;?>',
                        name: '<?php echo $valor; ?>',
                        title: 'Enter username',
						success: function (html) {
                                        $('#total_acad_credit').html(html);
                                    }
                    });
					
					
					 //editables
                    $('#nb_playlist_edit').editable({

                        //alert('fua');

                        url: 'site/actualiza_playlist.php',
                        type: 'text',
                        pk: '<?php echo $valor;?>',
                        name: '<?php echo $valor; ?>',
                        title: 'Enter username',
						success: function (html) {
                            $('#total_acad_credit').html(html);
                        }
                    });
					

                </script>


            </div>

        </div>


      <div class="row <?php echo $dagrable;?>" id="items_programs">
	  
	  
          
                        

	  
	  
	  
	  
        <?php
        
         
        
        
          if(empty($valor)){
            $Query  = " SELECT fl_leccion_sp, nb_programa".$sufix.", ";
            $Query .= " a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa, b.ds_programa, b.ds_learning, b.ds_metodo, b.ds_requerimiento, ";
            $Query .= " b.no_creditos, b.ds_duracion, c.no_horas, c.no_workload, c.cl_delivery, c.ds_credential, c.cl_type, c.ds_language, c.no_semanas,b.no_email_desbloquear,''no_orden,CONVERT(c.no_workload, SIGNED)nohrs  ";
            $Query .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c, k_usuario_programa d, k_playlist_course f ";
            $Query .= " WHERE ";
            $Query .= " a.fl_programa_sp = b.fl_programa_sp AND a.fl_programa_sp = c.fl_programa_sp AND  b.fg_publico='1' ";
            $Query .= " GROUP BY b.fl_programa_sp ";
          }
          else{
            $Query = "SELECT '0', nb_programa".$sufix.", a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa, b.ds_programa, b.ds_learning, b.ds_metodo, b.ds_requerimiento,b.no_creditos, ";
            $Query .= "b.ds_duracion, c.no_horas, c.no_workload, c.cl_delivery, c.ds_credential, c.cl_type, c.ds_language,c.no_semanas,b.no_email_desbloquear,a.no_orden,CONVERT(c.no_workload, SIGNED)nohrs ";
            $Query .= "FROM k_playlist_course a ";
            $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
            $Query .= "LEFT JOIN k_programa_detalle_sp c ON(c.fl_programa_sp=a.fl_programa_sp) ";
            $Query .= "WHERE 1=1 AND  b.fg_publico='1' ";          
            $Query .= "AND a.fl_playlist_padre=$valor ORDER BY no_orden ASC ";
          }          
          $rs = EjecutaQuery($Query);
          
          $registros = CuentaRegistros($rs);
          if(!empty($registros)){
             
              
            $contador_orden=0;  
            for($i=0;$row=RecuperaRegistro($rs);$i++) {
                  $fl_leccion_sp = $row[0];
                  $nb_programa = str_texto($row[1]);
                  $fl_programa_sp = $row[2];
                  $nb_thumb = str_texto($row[3]); 
                  $fg_nuevo_programa = $row[4];
                  $ds_programa = str_uso_normal($row[5]);
                  $ds_learning = str_uso_normal($row[6]);
                  $ds_metodo = str_uso_normal($row[7]);
                  $ds_requerimiento = str_uso_normal($row[8]);  
                  $no_creditos = $row[9];
                  $ds_duracion = $row[10];
                  $no_horas = $row[11];
                  $no_workload = $row[12];
                  $cl_delivery = str_texto($row[13]);
                  $ds_credential = str_texto($row[14]);
                  $cl_type = str_texto($row[15]);
                  $ds_language = str_texto($row[16]);
                  $no_semana = $row[17];
                  $no_email_desbloquear=$row['no_email_desbloquear'];
                  $no_orden=$row['no_orden'];
                  
                  $contador_orden ++;
                  
				  $no_hrs_worload=$row['nohrs'];
				
				  
                  $no_horas_+=$no_hrs_worload;
                  $no_creditos_+=$no_creditos;
                  
                  
				  
				  
				  
                  if($no_orden==0){
                  
                      #Le damos un orden si no lo tiene.
                      $Or="UPDATE k_playlist_course SET no_orden=$contador_orden WHERE fl_programa_sp=$fl_programa_sp AND fl_playlist_padre=$valor ";
                      EjecutaInsert($Or);
                      
                      $Ord="SELECT no_orden FROM k_playlist_course WHERE fl_programa_sp=$fl_programa_sp AND fl_playlist_padre=$valor ";
                      $roor=RecuperaValor($Ord);
                      $no_orden=$roor['no_orden'];
                  
                  
                  }
                  
                  switch ($cl_delivery) {
                    case "O": $cl_delivery = "Online";    break;
                    case "S": $cl_delivery = "On-Site";   break;
                    case "C": $cl_delivery = "Combined";  break;
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

                  if($i <= 1){
                    $style_cuad = "col-sm-6 col-md-6 col-lg-4";
                    $style_img  = "margin:auto;";
                  }
                  else{
                    $style_cuad = "col-sm-6 col-md-6 col-lg-4";
                    $style_img  = "margin:auto;";
                  }
                  
                  
                  if($perfil_usuario==PFL_ESTUDIANTE_SELF){ 
                  
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
                  $row00 = RecuperaValor("SELECT fl_usu_pro, ds_progreso, fg_terminado, fg_status_pro,fg_asignado_playlist FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa_sp");
                  
                  $fg_asignado_play_list=$row00['fg_asignado_playlist'];
                  
                  
                  if(($fl_perfil_sp==PFL_ESTUDIANTE_SELF)&&(!empty($fg_puede_liberar_curso))){
                      if($fg_plan_pago)
                          $row00[0]=1;
                  }
                  
                  # Esta asignado al curso
                  if(!empty($row00[0])){
                      
                            if($fg_puede_liberar_curso==1){
                      
                      
                                $fg_puede_tomar_curso=VerificaCumplientoRequisitoParaAccederCurso($fl_usuario,$fl_programa_sp,$no_email_desbloquear,$fg_plan_pago,$fl_pagado,$fg_assign_myself_course);
                                $fg_desbloqueado_por_envio_email=DesbloqueadoPorPagoCurso($fl_usuario,$fl_programa_sp);

								if($fg_desbloqueado_por_envio_email){
										    if(empty($fg_plan_pago))
											    $no_dias_faltan_terminar_plan=MuestraTiempoRestanteTrialCurso($fl_usuario,$fl_programa_sp,1);
										    else
											    $no_dias_faltan_terminar_plan="";  									 

							    }else{
									  
											if(empty($fg_plan_pago))
												$no_dias_faltan_terminar_plan=MuestraTiempoRestanteTrialCurso($fl_usuario,$fl_programa_sp);
											else
												$no_dias_faltan_terminar_plan=""; 

									  
								}
								
								
                                if($fg_puede_tomar_curso){
                                    #Si ya tiene un progreso
                                    if(  (!empty($row00['ds_progreso'])) && ($row00['ds_progreso'] <100) ) {
                                        $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a>";/*Continue*/
                                    }else if($row00['fg_terminado']==1){
                                        $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-primary'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1096)."</a>"; /*Review*/
                                    }else{    

                                        $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                            <span class='h4'>                            
                                                <a class='btn btn-success no-margin' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye fa-1x'></i>&nbsp;".ObtenEtiqueta(1149)."&nbsp;</a>
                                            </span>".$no_dias_faltan_terminar_plan."                          
                                          </div>";
                                    }
                                    
                                }else{
                                    

                                    #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
                                    $btn="<div class='row' style='margin:0px;'>
											<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
														<span class='h4' id='btn_desbloquear_curso'>                            
															<a class='btn btn-danger no-margin' style='background-color: #D97789 !important; border-color: #D97789 !important;'  onclick='$(\"#ModalPrivacity\").modal(\"toggle\");DesabilitarPagarCurso(".$fl_programa_sp.",".$no_email_desbloquear.");'><i class='fa fa-lock fa-1x'></i>&nbsp;".ObtenEtiqueta(2076)."&nbsp;</a>
														</span>".$no_dias_faltan_terminar_plan."                          
											 </div>
											</div><br/>
											<div class='row' style='margin:0px;'>
												 <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
														   <span class='h4'> <a class='btn btn-primary btn-xs mikelangel' href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1' ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2108)." </a></span>
												 </div>
											</div>
											"; 
                                } 
                                
                                
                      
                            }else{
                                # Si ya termino el curso solo podra ver sus calificaciones
                                # El boton  es color azul
                                if($row00[2]==1){
                                  if($row00[3]==1)
                                    $btn = "<a href='javascript:user_pause(".$row00[3].",".$fl_programa_sp.",".$fl_usuario.", 118,  \"courses_library.php\");' class='btn btn-warning'> <i class='fa fa-pause'></i> ".ObtenEtiqueta(1999)."</a>";
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
                                    if(empty($row00[1])){
                                        if($fg_asignado_play_list)
                                            $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-success'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1149)."</a>";/*Start*/
                                        else 
                                            $btn = "<a  class='btn btn-success' onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
                                    }else{
                                      $btn = "<a href='#site/desktop.php?fl_programa=".$fl_programa_sp."' class='btn btn-warning'> <i class='fa fa-eye'></i> ".ObtenEtiqueta(1095)."</a>";
                                    }
                                  }
                                }
                                
                            }     
                                
                  }
                  # No esta asignado al curso
                  else{
                    # Para los estudiantes
                    if($perfil_usuario== PFL_ESTUDIANTE_SELF){
                        
                         if($fg_puede_liberar_curso==1){
                        
                        
                                 $fg_puede_tomar_curso=VerificaCumplientoRequisitoParaAccederCurso($fl_usuario,$fl_programa_sp,$no_email_desbloquear,'',$fl_pagado,$fg_assign_myself_course);
                             
                                 if($fg_puede_tomar_curso){

                                     $btn  = "<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
                                                <span class='h4'>                            
                                                    <a class='btn btn-success no-margin' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye fa-1x'></i>&nbsp;".ObtenEtiqueta(1149)."&nbsp;</a>
                                                </span>                          
                                              </div>";
                                 
                                 }else{
								 
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
								   }else{
									   $class="danger";
									   $style="background-color: #b077d9 !important; border-color: #b077d9 !important;";
									   $etqb=ObtenEtiqueta(2076);
									   $iconob="lock";
								   }
								 
								 
                                     #Muestra boton con modal para poder pagar el curso o invitar a su compadre.
                                     $btn="<div class='row' style='margin:0px;'>
												<div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
													<span class='h4' id='btn_desbloquear_curso'>                            
														<a class='btn btn-$class no-margin' style='$style' onclick='$(\"#ModalPrivacity\").modal(\"toggle\");DesabilitarPagarCurso(".$fl_programa_sp.",".$no_email_desbloquear.");'><i class='fa fa-$iconob fa-1x'></i>&nbsp;".$etqb."&nbsp;</a>
													</span>                          
												</div>
											</div><br/>
											<div class='row' style='margin:0px;'>
												   <div class='col-xs-10 col-sm-10 col-md-12 col-lg-12 '>
																   <span class='h4'> <a class='btn btn-primary btn-xs mikelangel'style='magin-left:0px; href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1' ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2108)." </a></span>
												   </div>
											 </div> "; 
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
                         }             
                    }
                    else                      
                      $btn = "<a onclick='$(\"#ModalPrivacity\").modal(\"toggle\"); requeridos(".$fl_programa_sp.")' class='btn btn-success'> <i class='fa fa-check'></i> ".ObtenEtiqueta(1149)."</a>";
                  }
                  ?>
                  <script type="text/javascript">
                    function funcion_<?php echo $contador_orden; ?>(nom_func) {
                      //for(x=0; x < <?php echo $registros; ?>; x++){
                       // for(y=1; y<3; y++){
                       //   collapse = 'Collapse_' + x + '_' + y;
                       //   if(nom_func != collapse){
                       //     document.getElementById(collapse).style.display='none';
                       //   }else{
                       //     if(document.getElementById(collapse).style.display == 'block'){
                       //       document.getElementById(collapse).style.display='none';
                       //     }else{
                       //       document.getElementById(collapse).style.display='block';
                       //     }
                       //   }
                       // }
                      //}
                    }
                  </script> 
                  <div class="<?php echo $style_cuad; ?> well span2 tile"  draggable="true" id="divp_<?php echo $fl_programa_sp; ?>" >
                    <div class="product-content product-wrap clearfix">
                      <div class="row">
                        <div class="col-md-5 col-sm-12 col-xs-12">
                          <div class="product-image"> 
                            <img src="<?php echo $img; ?>"style="margin:auto;" alt="<?php echo $nb_programa; ?>" class="img-responsive" <?php echo $style_img; ?>  <?php if(!empty($valor)){ echo"style='$cursor'  ";  } ?>   > 
                            <?php 
                            if(!empty($fg_nuevo_programa)){
                              echo "<span class='tag2 hot'>";
                                echo ObtenEtiqueta(1243); 
                              echo "</span>";  
                            } 
                            ?>
                            <div class="product-info smart-form">
                              <div class="row">
                                <div class="col-md-3"> 
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12"> 
                                  <h5>
                                    <small>
                                      <center><?php echo "Students ($no_studets)"; ?></center>
                                      <?php
                                        if($perfil_usuario == PFL_MAESTRO_SELF or $perfil_usuario == PFL_ADMINISTRADOR){
                                      ?>
                                      <a href="javascript:void(0);" onclick="PresentaModalGroups(<?php echo$fl_programa_sp; ?>,'G')">
                                        <center><?php echo "Groups ($no_groups)"; ?></center>
                                      </a> 
                                      <?php
                                        }else{
                                          ?>
                                          <center><?php echo "Groups ($no_groups)"; ?></center>
                                          <?php
                                        }
                                        ?>
                                      
                                    </small>
                                  </h5>												
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-7 col-sm-12 col-xs-12">
                          <!--------------------------------------------------------------------------------------------------->
                          <!--------------------------------------------------------------------------------------------------->                           
                          <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
							
							 <b class="badge pull-left bg-color-pink" <?php echo $data_togle; ?>   style="margin-left:15px;margin-top:5px;background-color: #997db9!important;" data-placement="top" <?php echo $title_toltip; ?>  id="order_<?php echo $fl_programa_sp; ?>"><?php echo $no_orden;?></b>
						  
							
                              <div style="float: right;">
                                
                                <!--- Inicia Playlist por curso --->
                                <script>
                                  
                                 
                                  
                                  
                                  // Muestra la lista de playlist en cursos
                                  function DespliegaLista_<?php echo "_$i"; ?>(actual, muestra,fl_programa_sp){
                                    $.ajax({
                                      type: 'POST',
                                      url : 'site/listado_ind_palylist.php',
                                      async: false,
                                      data: 'fl_programa_sp='+fl_programa_sp+
                                            '&actual='+actual+
                                            '&muestra='+muestra,
                                      success: function(data) {
                                        $('#muestra_listado_ind_playlist_<?php echo "_$i"; ?>').html(data);
                                      }
                                    });
                                  }
                                  
                                  // 1.- Busca un playlist existente
                                  function busca_playlist_<?php echo "_$i"; ?>(valor, extra){ 
                                    if(valor == undefined)
                                      valor = document.getElementById('new_playlist_<?php echo "_$i"; ?>').value;
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
                                        $('#muestra_prueba_<?php echo "_$i"; ?>').html(data);
                                        document.getElementById('busca_playlist_<?php echo "_$i"; ?>').value = valor;
                                        document.getElementById('new_playlist_<?php echo "_$i"; ?>').value = "";
                                      }
                                    });
                                  }
                                
                                  // Guarda los playlist creados en cursos
                                  function GuardaPlaylist_<?php echo "_$i"; ?>(valor_p){
                                      var valor = document.getElementById("new_playlist_<?php echo "_$i"; ?>").value;
                                      //alert('entro1');
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
                                  function BtnGuardar_<?php echo "_$i"; ?>(){
                                    var new_playlist = document.getElementById("new_playlist_<?php echo "_$i"; ?>").value;
                                      $('#Ccl_<?php echo "_$i"; ?>').removeClass('btn btn-primary btn-xs disabled');
                                      $('#Ccl_<?php echo "_$i"; ?>').addClass('btn btn-primary btn-xs');
                                      
                                    if(new_playlist == ''){
                                      $('#Ccl_<?php echo "_$i"; ?>').removeClass('btn btn-primary btn-xs');
                                      $('#Ccl_<?php echo "_$i"; ?>').addClass('btn btn-primary btn-xs disabled');
                                    }
                                  }
                                
                                  </script>
                                
                                <a onclick="DespliegaLista_<?php echo "_$i"; ?>(<?php echo $i; ?>, 1,<?php echo $fl_programa_sp; ?>);"   class="btn btn-default "><span class="caret"></span></a> 
                                  <div id="muestra_listado_ind_playlist_<?php echo "_$i"; ?>"></div>
                                
                                <!--- Termina Playlist por curso --->

                              </div> 
                            </div> 
                          </div> 
                          <!--------------------------------------------------------------------------------------------------->
                          <!--------------------------------------------------------------------------------------------------->          
                          <div class="product-deatil" style="padding-top: 0px; padding-bottom:2px;">
						 
						
                            <h5 class="name" style="height:50px;">
                              <a>
                                <?php 
                                  echo $nb_programa;
                                ?>
                              </a>
                            </h5>
                              <a  href="javascript:void(0);" onclick="PresentaModalGroups(<?php echo $fl_programa_sp; ?>, 'L');">
                                  <span style="font-size: 24px;color: #21c2f8;font-family: Lato,sans-serif;"><?php echo $no_lecciones." ".ObtenEtiqueta(1242); ?></span>
								  
                              </a> 
                              <p></p>
                              <?php
                                if($y == 2)
                                  $style_esp = "left: -332px;";
                                else
                                  $style_esp = "";
                              ?>
                              
                              <div class="row">
                                
                              <div class="col-xs-12 col-sm-12 col-md-12"> 
                                <!--- ICH: Div para mostrar informacion general del curso --->
                                <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                                  <li class="dropdown">
                                    <a href="javascript:void(0);" data-toggle="dropdown" style="padding:0px 0px 0px 0px; background:transparent;"><i class="fa fa-fw fa-sm fa-info-circle " style="color:#9aa7af;"></i></a>
                                    <ul class="dropdown-menu" role="menu" style="width:500px; <?php echo $style_esp ?>">
                                      <li>
                                        <dl class="dl-horizontal">
                                          <dt><?php echo ObtenEtiqueta(360).":"; ?></dt>
                                            <dd><?php echo $nb_programa; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1216).":"; ?></dt>
                                            <dd><?php echo $no_creditos; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1220).":"; ?></dt>
                                            <dd><?php echo $no_horas; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1222).":"; ?></dt>
                                            <dd><?php echo $no_semana; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1252).":"; ?></dt>
                                            <dd><?php echo $no_workload; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1224).":"; ?></dt>
                                            <dd><?php echo $cl_delivery; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1223).":"; ?></dt>
                                            <dd><?php echo $ds_credential; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1226).":"; ?></dt>
                                            <dd><?php echo $cl_type; ?></dd>
                                          <dt><?php echo ObtenEtiqueta(1296).":"; ?></dt>
                                            <dd><?php echo $ds_language; ?></dd>
                                        </dl>                            
                                      </li>
                                    </ul>
                                  </li>
                                </ul>
                                <!--- ICH: Div para mostrar distintas categorias del curso --->
                                <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                                 <a href="javascript:void(0);" data-toggle="modal" onclick="ModalDetails(<?php echo $fl_programa_sp; ?>, 2);"> <i class="fa fa-fw fa-sm fa-tags " style="color:#9aa7af;"></i></a> 
                                </ul>
                                <!--- ICH: Div para mostrar informacion del curso --->
                                <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
                                 <a href="javascript:void(0);"  onclick="ModalDetails(<?php echo $fl_programa_sp; ?>, 3);"><i class="fa fa-fw fa-sm fa-file-text-o" style="color:#9aa7af;" data-toggle="modal" ></i></a>
                                </ul> 
								<?php if($fg_b2c<>1){
										  if($fg_export_moodle==1){
								?>
										  <ul class="nav navbar-nav navbar-left col-xs-2 col-sm-1 col-md-2">
										     <a href="javascript:void(0);"  onclick="ExportMoodle(<?php echo $fl_programa_sp; ?>, 3);"><i class="fa fa-fw fa-sm fa-external-link" style="color:#9aa7af;" ></i></a>
										
										  </ul>  
                                 <?php 
										  }
									 } 
								 ?>  
                              </div>
                              </div>
                          </div>                          
                          <div class="product-info smart-form">
                            <div class="row">
                              <div class="col-md-12 col-sm-12 col-xs-12"> 
                                <ul class="demo-btns">
                                  <li class="padding-top-10">
                                    <?php echo $btn; ?>
                                  </li>
                                  <li class="padding-top-10">
                                    <?php
                                    if($perfil_usuario == PFL_MAESTRO_SELF || $perfil_usuario == PFL_ADMINISTRADOR){
                                      echo "<a class='btn btn-primary btn-xs' href='index.php#site/desktop.php?fl_programa=".$fl_programa_sp."&preview=1' ><i class='fa fa-eye'></i> ".ObtenEtiqueta(2008)." </a>";
                                    }
                                    ?>
                                  </li>
                                </ul> 
                                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-12 show-stats">
                                  <div class="row">
                                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></div>
                                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10"> 
                                    <?php
                                    $progreso = RecuperaValor("SELECT ds_progreso FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa_sp ");
                                    echo "<div class='progress progress-xs' style='width:93%;' data-progressbar-value='".$progreso[0]."'><div class='progress-bar'></div></div>";
                                    ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>                          
                       </div>
                      </div>
                    </div>
                  </div>	
                  
                  <?php 
                      $fl_programa_sp_i = $fl_programa_sp;
                      $y++; 

                    if($y == 3){
                      $band = 1;
                      $limit = $fl_programa_sp_i;
                    }else{
                      if($i == ($registros - 1)){
                        $band = 1;
                        $limit = $fl_programa_sp_i;
                      }else
                        $band = 0 ;
                    }
                /* 
                    if($band == 1){
                      //echo "</div>";
                     // echo "<div class='row '>";
                        $Query2  = " SELECT fl_leccion_sp, nb_programa, no_semana, ds_titulo, ds_leccion, ";
                        $Query2 .= " CASE WHEN ds_vl_ruta IS NULL THEN '".ObtenEtiqueta(17)."' WHEN ds_vl_ruta='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'Video Brief', ";
                        $Query2 .= " CASE WHEN fg_ref_animacion IS NULL THEN '".ObtenEtiqueta(17)."' WHEN fg_ref_animacion='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'req_anim', ";
                        $Query2 .= " CASE WHEN (SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = 1) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'quiz', ";
                        $Query2 .= " c.no_semanas, a.ds_vl_duracion, a.fl_programa_sp, b.nb_thumb, b.fg_nuevo_programa ";
                        $Query2 .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c ";
                        $Query2 .= " WHERE a.fl_programa_sp = b.fl_programa_sp  AND a.fl_programa_sp = c.fl_programa_sp ";
                        $Query2 .= " AND a.fl_programa_sp <=$limit ";
                        $Query2 .= " GROUP BY b.fl_programa_sp ";
                        // $Query2 .= " ORDER BY fg_nuevo_programa DESC  ";
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
                          ?>   
                          <div class="superbox col-xs-12 col-sm-12 col-md-12 col-lg-12" draggable="true" id="divp_<?php echo $fl_programa_sp; ?>"> 
                            <!-- ICH: Inicia Div Muestra Grupos -->
                            <div class="collapse" id="<?php echo "Collapse_$x_i"; ?>_1">
                              <div class="card card-block">
                                <div class="superbox-list active" <?php echo $ste; ?>></div>
                                <div class="superbox-show" style="display: block; padding: 1px 1px 1px 1px; background-color: #ccc;">
                                  <div class="widget-body">
                                    <div class="panel-group smart-accordion-default" id="accordion_groups_<?php echo $x_i; ?>_1"></div>
                                  </div>
                                </div>        
                              </div>
                            </div>
                            <!-- ICH: Termina Div Muestra Grupos -->
                            
                            <!-- ICH: Inicia Div Muestra Lecciones -->
                            <div class="collapse" id="<?php echo "Collapse_$x_i"; ?>_2"  id="<?php echo "Collapse_$x_i"; ?>_2">
                              <div class="card card-block">
                                <div class="superbox-list active" <?php echo $ste; ?>></div>
                                <div class="superbox-show" style="display: block; padding: 1px 1px 1px 1px; background-color: #ccc;">
                                  <div class="widget-body">
                                    <div class="panel-group smart-accordion-default">
                                      <div class="panel panel-default">
                                        <div class="panel-collapse collapse in" aria-expanded="false" style="height: auto;">
                                          <div class="panel-body no-padding" id="accordion_lecciones_<?php echo $x_i; ?>_2"></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>        
                              </div>
                            </div>
                            <!-- ICH: Termina Div Muestra Lecciones -->
                          </div>								
                          <?php
                          $h++;
                          if($h == 3)
                            $h = 0;                            
                        }
                      
                      $y = 0; 
                    }
                 */   
                    
                    
 
            if(empty($no_horas_))
                $no_horas_=0;
            if(empty($no_creditos_))
                $no_creditos_=0;
            
            
            echo"    
            <script>
                    var total_cred=$no_creditos_;
                    var total_hrs=$no_horas_;

                 
                    $('#tota_hrs').empty();
                    $('#tota_cred').empty();

                    $('#tota_hrs').append(total_hrs);
                    $('#tota_cred').append(total_cred);
            </script>      
            ";
                             



				 
                    
               
                }
          }else{
            ?>
            <br><br>
            <div class="row sortable grid" >
              <div class="col-lg-1"></div>
              <div class="col-lg-10">
                <div class="alert alert-info fade in">
                  <!--<i class="fa-fw fa fa-info"></i>-->
                  <strong>No courses were found related to this playlist.</strong>
                </div> 
              </div>
              <div class="col-lg-1"></div>
            </div>
            <?php
          }
          ?>  
      </div>
      <!-- end row -->
    </section>
    <!-- end widget grid -->
<?php
  }
?>



 

<script>
$( document ).ready(function() {
   
   $('.sortable').sortable();
    $('[data-toggle="tooltip"]').tooltip();
});

</script>
