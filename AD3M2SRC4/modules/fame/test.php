<?php
  # Librerias
  require '../../lib/general.inc.php';
  

  #muestra los institutos que ya se encuentran registrados
  $Query="
  SELECT P.fl_usu_pro,A.ds_ruta_avatar,U.ds_nombres,U.ds_apaterno,
  I.ds_instituto,R.nb_programa,U.fg_activo,U.fe_ultacc,P.ds_progreso,
  P.no_promedio_t, U.fe_sesion,S.ds_pais,P.no_promedio_t,
  U.fl_instituto, U.fl_usuario,R.fl_programa_sp,P.fl_usu_pro,
  I.fg_tiene_plan, DUP.fg_quizes, DUP.fg_grade_tea, U.fl_perfil_sp
  FROM c_alumno_sp A
  LEFT JOIN c_usuario U ON U.fl_usuario=A.fl_alumno_sp
  LEFT JOIN c_instituto I ON I.fl_instituto=U.fl_instituto
  LEFT JOIN k_usuario_programa P ON P.fl_usuario_sp=U.fl_usuario
  LEFT JOIN k_details_usu_pro DUP ON DUP.fl_usu_pro=P.fl_usu_pro
  LEFT JOIN c_programa_sp R ON R.fl_programa_sp=P.fl_programa_sp 
  LEFT JOIN c_pais S ON S.fl_pais=I.fl_pais
  WHERE 1=1 AND U.fl_perfil_sp IN (".PFL_ESTUDIANTE_SELF.", ".PFL_MAESTRO_SELF.")";
  $rs = EjecutaQuery($Query); 
  $totales = CuentaRegistros($rs);
  for($i=1;$row=RecuperaRegistro($rs);$i++) {
    $fl_usu_pro = $row['fl_usu_pro'];
    $fl_alumno = $row['fl_usuario'];    
    $fl_programa = $row['fl_programa_sp'];
    $fg_quizes = $row['fg_quiz'];
    $fg_grade_tea = $row['fg_grade_tea'];
    $fl_perfil_sp = $row['fl_perfil_sp'];
    if(!empty($fg_grade_tea))
      $gabo = "Si";
    else
      $gabo = "No";
    
    echo "<p style='color:red;'>Alumno: ".$fl_alumno." === ".$fl_programa." ".$gabo."</p><h1>PERFIL ".$fl_perfil_sp."</h1><br>";

    $contador_califi_finales=0;      
    #Recuperamos todas las lecciones del programa
    $Query2="SELECT fl_leccion_sp,no_semana, ds_titulo,nb_quiz,no_valor_quiz  FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa ";
    $rs2 = EjecutaQuery($Query2);
    $contador2=0;
    $suma_quizes_calific = 0;
    for($tot2=0;$row2=RecuperaRegistro($rs2);$tot2++) {
      $fl_leccion_sp = $row2[0];      
      # Verifica si tiene quiz la leccion y ha realizado intentos
      if(ExisteEnTabla('k_quiz_pregunta', 'fl_leccion_sp', $fl_leccion_sp) AND ExisteEnTabla('k_quiz_calif_final', 'fl_leccion_sp', $fl_leccion_sp, 'fl_usuario', $fl_alumno, true)){
        #Recuperamos los quizes por cada leccion del programa.
        $Query3 = "
        SELECT no_calificacion,no_intento  
        FROM k_quiz_calif_final 
        WHERE fl_leccion_sp=$fl_leccion_sp 
        AND fl_usuario=$fl_alumno ORDER BY no_intento DESC LIMIT 1 ";        
        $row3 = RecuperaValor($Query3);
        $no_intento=$row3['no_intento'];
        $no_total_calif=$row3['no_calificacion'];
        if(!empty($no_total_calif)){           
          $suma_quizes_calific += $no_total_calif;
          $contador_califi_finales ++;  
        } 
        echo $Query3."<br>";
      }
    }
       
      
    #Calculo de los promedios        
    $promedio_quizes=$suma_quizes_calific/$contador_califi_finales;
    if(empty($promedio_quizes))
      $promedio_quizes = 0;
    $tot_cal_quiz=$promedio_quizes; 
    $tot_pro= $tot_cal_quiz;
    # Actualizamos el prmedio geeral de los quizes
    EjecutaQuery("UPDATE k_details_usu_pro SET no_prom_quiz=$promedio_quizes WHERE fl_usu_pro=$fl_usu_pro");    
        
    # SI el maestro lo califica obtiene datos
     $tot_tea = 0;
    if(!empty($fg_grade_tea)){
        #Realizamos 
        $row0 = RecuperaValor("SELECT SUM( no_calificacion), COUNT(*) FROM k_calificacion_teacher WHERE fl_programa_sp=$fl_programa AND fl_alumno=$fl_alumno");
        $sum_weeks = $row0[0] / $row0[1];
        $tot_tea = $sum_weeks ;
        $tot_pro = ($tot_cal_quiz + $tot_tea)/2;
        EjecutaQuery("UPDATE k_details_usu_pro SET no_prom_teacher=$tot_tea WHERE fl_usu_pro=$fl_usu_pro");        
    }
    
    echo "<p style='color:blue;'>".$promedio_quizes."/".$tot_tea." ".$gabo." ===== ".$tot_pro."</p><br>";
  }
?>
