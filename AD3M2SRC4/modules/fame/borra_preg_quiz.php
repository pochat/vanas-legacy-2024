<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # ICH: Borra cada pregunta y sus respuestas 
  $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp');
  $tab = RecibeParametroHTML('tab');
  $quiz_delete = RecibeParametroBinario('quiz_delete');
  $ruta = "uploads";
  
  # Si hay usuario en el programa que no han terminado no podra eliminar la quiz
  $Queryj  = "SELECT COUNT(*) FROM k_entrega_semanal_sp a ";
  $Queryj .= "LEFT JOIN c_leccion_sp b ON(a.fl_leccion_sp=b.fl_leccion_sp) ";
  $Queryj .= "LEFT JOIN k_usuario_programa c ON(c.fl_usuario_sp=a.fl_alumno AND c.fl_programa_sp=b.fl_programa_sp) ";
  $Queryj .= "WHERE a.fl_leccion_sp=$fl_leccion_sp AND c.fg_terminado='0' ";
  $rowj = RecuperaValor($Queryj);
  $users_on_leccion = $rowj[0];  
  // $users_on_leccion = 0;  
  # Si no hay usuarios elimina
  if($users_on_leccion == 0){
    if($quiz_delete==false || $quiz_delete == ""){
      $c = strlen($tab);
      $no_tab = substr($tab, 5, $c); 
      # Revisamos si la tab ya esta guardada en caso de que no apenas se esta creado puede borrarla
      if(ExisteEnTabla('k_quiz_pregunta', 'fl_leccion_sp', $fl_leccion_sp, 'no_orden', $no_tab, true)){
        $row = RecuperaValor("SELECT fl_quiz_pregunta, fg_tipo FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp AND no_orden = $no_tab");
        $fl_quiz_pregunta = $row["fl_quiz_pregunta"];
        if(empty($fl_quiz_pregunta))
          $fl_quiz_pregunta = 0;
        $fg_tipo = $row["fg_tipo"];
        
        # Buscamos  los archivos si la pregunta es de tio imagen
        if($fg_tipo=="I" || $fg_tipo==""){
          if(!empty($fl_quiz_pregunta))
            $Query2 = "SELECT ds_respuesta, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta=".$fl_quiz_pregunta;
          else
            $Query2 = "SELECT ds_respuesta, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta=0 AND no_tab=$no_tab";
          $rs2 = EjecutaQuery($Query2);
          for($r=0;$row2=RecuperaRegistro($rs2);$r++){
            $ds_respuesta = $row2["ds_respuesta"];
            $ds_respuesta_esp = $row2["ds_respuesta_esp"];
            $ds_respuesta_fra = $row2["ds_respuesta_fra"];            
            # Eliminamos el archivo
            unlink($ruta."/".$ds_respuesta);
          }
        }
        
        EjecutaQuery("DELETE FROM k_quiz_pregunta WHERE fl_leccion_sp = $fl_leccion_sp AND no_orden = $no_tab");
        EjecutaQuery("DELETE FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta");
        $error = false;
        $tab_new = "";
      }
      else{
       $error = "new";
       $tab_new = "new1";
      }
    }
    else{
     
      # Obtenemos todos las preguntas de la leccion
      $rs = EjecutaQuery("SELECT fl_quiz_pregunta, no_orden, fg_tipo FROM k_quiz_pregunta WHERE fl_leccion_sp=$fl_leccion_sp");
      $tot_preguntas = CuentaRegistros($rs);
      for($i=1;$row=RecuperaRegistro($rs);$i++){
        $fl_quiz_pregunta = $row["fl_quiz_pregunta"];
        $no_orden = $row["no_orden"];
        $fg_tipo = $row["fg_tipo"];
        # Buscamos las imagenes respuestas de las preguntas
        if($fg_tipo=="I"){
          $Query2 = "SELECT ds_respuesta, ds_respuesta_esp, ds_respuesta_fra FROM k_quiz_respuesta WHERE fl_quiz_pregunta=".$fl_quiz_pregunta;
          $rs2 = EjecutaQuery($Query2);
          for($r=0;$row2=RecuperaRegistro($rs2);$r++){
            $ds_respuesta = $row2["ds_respuesta"];
            $ds_respuesta_esp = $row2["ds_respuesta_esp"];
            $ds_respuesta_fra = $row2["ds_respuesta_fra"];            
            # Eliminamos el archivo
            unlink($ruta."/".$ds_respuesta);
          }
        }
        # Eliminamos las respuestas de las preguntas
        EjecutaQuery("DELETE FROM k_quiz_respuesta WHERE fl_quiz_pregunta = $fl_quiz_pregunta");
        # Eliminamos la pregunta
        EjecutaQuery("DELETE FROM k_quiz_pregunta WHERE fl_quiz_pregunta = $fl_quiz_pregunta");
      }

      # Actualiza los campos de la leccion
      if(!empty($fl_leccion_sp)){
        EjecutaQuery("UPDATE c_leccion_sp SET nb_quiz='', no_valor_quiz=0 WHERE fl_leccion_sp=$fl_leccion_sp");
      }
      
      $error = false;
      $tab_new = "";
    }
    
  }
  else{
    $error = true;
    $tab_new = "";
    if(!ExisteEnTabla('k_quiz_pregunta', 'fl_leccion_sp', $fl_leccion_sp, 'no_orden', $no_tab, true)){
      $tab_new = "new1";
    }
  }
  $result['valores'] = 
    array(
    "error" => $error,
    "tab_new" => $tab_new,
    "users_on_leccion" => $users_on_leccion,
    "tot_preguntas" => $tot_preguntas,
    "Queryj" => "UPDATE c_leccion_sp SET nb_quiz='', no_valor_quiz=0 WHERE fl_leccion_sp=$fl_leccion_sp",
    "tab" => $tab
    );
  
  echo json_encode((Object) $result);
?>