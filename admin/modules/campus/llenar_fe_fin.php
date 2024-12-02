<?php
  
  # AGRV 19/03/14
  # Script que sirve para llenar automaticamente los campos fe_fin y fe_completado de los estudiantes 
   
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Selecciona todos los alumnos que tengan el campo fe_fin y fe_completado vacios
  $Query  = "SELECT a.fl_alumno FROM k_pctia a, c_alumno b WHERE a.fl_alumno = b.fl_alumno AND fe_fin IS NULL AND fe_completado IS NULL";
  $rs = EjecutaQuery($Query);
  for($i=0; $row = RecuperaRegistro($rs); $i++) {
    $fl_alumno=$row[0];
    
    # Recupera la sesion de los alumnos
    $Query  = "SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_alumno";
    $row = RecuperaValor($Query);
    $cl_sesion = $row[0];
    
    # Recupera el program start date 
    $Query  = "SELECT fe_inicio, fl_programa ";
    $Query .= "FROM  k_term b, c_periodo c, k_alumno_term d ";
    $Query .= "WHERE b.fl_periodo=c.fl_periodo ";
    $Query .= "AND b.fl_term=d.fl_term AND d.fl_alumno='$fl_alumno' ";
    $Query .= "AND no_grado=1 ";
    $row2 = RecuperaValor($Query);
    $fe_inicio = $row2[0]; 
    $fl_programa = $row2[1];
    
    # Recupera el numero de semanas
    $Query  = "SELECT no_semanas, cl_type  ";
    $Query .= "FROM k_programa_costos ";
    $Query .= "WHERE fl_programa = $fl_programa ";
    $row = RecuperaValor($Query);
    $no_semanas = $row[0]; 
    $cl_type = $row[1]; 
    
    # Recupera contrato
    $Query  = "SELECT no_contrato ";
    $Query .= "FROM k_app_contrato ";
    $Query .= "WHERE cl_sesion='$cl_sesion' ";
    $Query .= "ORDER BY no_contrato";
    $row = RecuperaValor($Query);
    $no_contrato = $row[0]; 
    
    if($cl_type==4) {
      switch($no_contrato) {
        case 1: 
          $no_semanas_i = 0;  
          $no_semanas_f = 52; 
        break;
        case 2: 
          $no_semanas_i = 52; 
          $no_semanas_f = 104; 
        break;
        case 3: 
          $no_semanas_i = 104;
          $no_semanas_f = $no_semanas; 
        break;
      }
    }
    else {
      $no_semanas_i = 0;  
      $no_semanas_f = $no_semanas; 
    }
    
    # Calcula el end date de acuerdo a las semanas de curso y las coloca por default en el campo
    $fe_fin = date("d-m-Y",strtotime("$fe_inicio + $no_semanas_f weeks")); 
    $fe_fin = "'".ValidaFecha($fe_fin)."'";
    
    $Query  = "UPDATE k_pctia SET fe_fin=$fe_fin, fe_completado=$fe_fin WHERE fl_alumno=$fl_alumno";
    EjecutaQuery($Query);
  }
  echo "Proceso terminado $i registros afectados";
  
?>