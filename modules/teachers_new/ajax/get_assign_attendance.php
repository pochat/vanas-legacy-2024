<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  
  # Recibe parametros
  $fl_entrega_semanal = RecibeParametroNumerico('fl_entrega_semanal');
  
  # Recupera datos de la entrega
  $fe_actual = ObtenFechaActual( );
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
  $Query  = "SELECT a.fl_alumno, a.fl_grupo, a.fg_entregado, a.fl_promedio_semana, a.fl_semana, d.no_semana, ";
  $Query .= ConcatenaBD($concat)." 'ds_nombre', ";
  $Query .= "DATE_FORMAT(c.fe_entrega, '%c') fe_entrega_mes, DATE_FORMAT(c.fe_entrega, '%e, %Y') fe_entrega_r, ";
  $Query .= "DATE_FORMAT(a.fe_entregado, '%c') fe_entregado_mes, DATE_FORMAT(a.fe_entregado, '%e, %Y %H:%i') fe_entregado_r, ";
  $Query .= "DATEDIFF(c.fe_entrega, a.fe_entregado)+1 dif_entrega, DATEDIFF(c.fe_entrega, '$fe_actual')+1 dif_hoy, d.ds_titulo ";
  $Query .= "FROM k_entrega_semanal a, c_usuario b, k_semana c, c_leccion d ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario ";
  $Query .= "AND a.fl_semana=c.fl_semana ";
  $Query .= "AND c.fl_leccion=d.fl_leccion ";
  $Query .= "AND a.fl_entrega_semanal=$fl_entrega_semanal ";
  $row = RecuperaValor($Query);
  $fl_alumno = $row[0];
  $fl_grupo = $row[1];
  $fg_entregado = $row[2];
  $fl_promedio_semana = $row[3];
  $fl_semana = $row[4];
  $no_semana = $row[5];
  $ds_nombre = str_uso_normal($row[6]);
  $fe_entrega = ObtenNombreMes($row[7])." ".$row[8];
  $fe_entregado = ObtenNombreMes($row[9])." ".$row[10];
  if(empty($row[9]))
    $fe_entregado = "<b>Pending</b>";
  if((!empty($row[11]) AND $row[11] >= 0) OR $row[12] >= 0) {
    $ds_en_tiempo = "Yes";
    $fg_en_tiempo = True;
  }
  else {
    $ds_en_tiempo = "No";
    $fg_en_tiempo = False;
  }
  $ds_titulo = $row[13];
  
  # Recupera la fecha programada para la clase en linea
  $Query  = "SELECT a.fe_clase, a.fl_clase ";
  $Query .= "FROM k_clase a LEFT JOIN k_live_session b ";
  $Query .= "ON (a.fl_clase=b.fl_clase) ";
  $Query .= "WHERE a.fl_semana=$fl_semana ";
  $Query .= "AND a.fl_grupo=$fl_grupo ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fe_clase[$i] = $row[0];
    $fl_clase[$i] = $row[1];
  }
  
  #Forma del dialogo
  echo "
  <form name='datos_attendance' id='datos_attendance'   method='POST' action='ajax/assign_attendance.php'>
    <p>Attendance for <b>$ds_nombre</b> on class <b>$ds_titulo</b> the week <b>$no_semana</b> </p>
    <select name='cl_estatus_assistencia' id='cl_estatus_assistencia' >
      <option value=0>Pending</option>";
    $Query = "SELECT cl_estatus_asistencia,nb_estatus FROM c_estatus_asistencia ";
    $rs = EjecutaQuery($Query);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      echo "
      <option value=$row[0]";
      if($row[0] == $fl_promedio_semana)
        echo " selected";
      echo ">$row[1] ".str_uso_normal($row[1])."
      </option>";
    }
  echo "
    <select>";
    for($i=0; $i<$registros; $i++)
      echo "<input type='hidden' name='fl_clase_".$i."' value='".$fl_clase[$i]."'>";
      
  echo "
    <input type='hidden' name='registros' value='$registros'>
    <input type='hidden' name='fl_entrega_semanal' value='$fl_entrega_semanal'>
    <input type='hidden' name='fl_alumno' value='$fl_alumno'>
  </form>";

  
  
?>