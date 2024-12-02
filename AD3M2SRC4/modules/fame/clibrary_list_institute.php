<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametro
  $fl_instituto=$_POST['extra_filters']['fl_instituto'];
  # Consulta para el listado
  $Query  = "SELECT a.fl_programa_sp, a.nb_programa ";
  $Query .= "FROM c_programa_sp a  ";
  $Query .= "WHERE a.fg_publico='1' AND fl_instituto is null
             ORDER BY a.nb_programa ASC  ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
        $fl_programa=$row['fl_programa_sp'];
        $nb_programa=$row['nb_programa'];
              
        $Query2="SELECT count(*) FROM k_orden_desbloqueo_curso_alumno WHERE fl_instituto=$fl_instituto  AND fl_programa_sp=$fl_programa ";
        $row2=RecuperaValor($Query2);
        $existe=$row2[0];

        if(!empty($existe)){
            $cheked="checked";   
        }else{
            $cheked="";
        }



      echo '
        {
          "checkbox": "<div class=\'checkbox \'><label><input class=\'checkbox\' onclick=\'AddCursoInstituto('.$fl_programa.');\' id=\'ch_'.$fl_programa.'\' value=\''.$row['fl_programa'].'\' type=\'checkbox\' '.$cheked.' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>",
          
          "name": "'.str_texto($nb_programa).'<br>", 
          "action": "&nbsp;"
 
        }';
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]
}
