<?php 
# Libreria de funciones
require("../lib/self_general.php");

  # get the list of items id separated by cama (,)
  $no_credito = $_POST['value'];
  $fl_playlist = $_REQUEST['name'];

  
    #Verificamos cuantos programas tiene ese playlist.
    $Query = "SELECT SUM(CONVERT(c.no_workload, SIGNED)) ";

    $Query .= "FROM k_playlist_course a ";
    $Query .= "LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) ";
    $Query .= "LEFT JOIN k_programa_detalle_sp c ON(c.fl_programa_sp=a.fl_programa_sp) ";
    $Query .= "WHERE 1=1 AND  b.fg_publico='1' ";          
    $Query .= "AND a.fl_playlist_padre=$fl_playlist  ";

    $ro = RecuperaValor($Query);       
    $no_cursos=$ro[0];
    
    
    #Multiplicmos el no_credito por el numero de cursos para scara el total.
    $no_creditos=  $no_credito * $no_cursos;
    
  
  
  
  $Query="UPDATE c_playlist SET  no_credito=$no_credito,no_tot_credito=$no_creditos WHERE fl_playlist=$fl_playlist ";
  EjecutaQuery($Query);
  
  echo $no_creditos;
  
  
?>