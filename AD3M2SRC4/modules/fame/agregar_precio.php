<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();
  
  $fl_instituto=RecibeParametroNumerico('fl_instituto');
  
  # Inserta D$mn_mensual=1;
  $mn_anual=1;
  $mn_mensual=1;
  $no_ini=1;
  $no_fin=1;
  
  $Query="SELECT MAX(fl_princing) AS fl_princing FROM c_princing ";
  $row=RecuperaValor($Query);
  $fl_princing_temp=$row[0] ;
  $fl_princing=$fl_princing_temp +1 ;
  
  $Query="SELECT no_fin FROM c_princing_temporal WHERE fl_instituto=$fl_instituto ORDER BY fl_princing DESC ";
  $row=RecuperaValor($Query);
  $no_ini=$row['no_fin']+1;
  $no_fin= $no_ini + 1;
  $no_ini=$no_fin+1;

  $Query  = "INSERT INTO c_princing_temporal (fl_princing,mn_mensual, mn_anual, no_ini, no_fin,fl_instituto) ";
  $Query .= "VALUES ($fl_princing,$mn_mensual, $mn_anual,$no_ini, $no_fin,$fl_instituto)";
  EjecutaQuery($Query);
  
#MJD
/*
 *Verifica si este instituto es el rector y si si lo es entonces aplicara lo mismo a sus dependencias.
 *
 */

 $Query="SELECT fl_instituto FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
 $rs=EjecutaQuery($Query);
 for($i=0; $row=RecuperaRegistro($rs); $i++){
     $fl_instituto=$row['fl_instituto'];
     #Genera los registros.   
     $Query="SELECT MAX(fl_princing) AS fl_princing FROM c_princing ";
     $row=RecuperaValor($Query);
     $fl_princing_temp=$row[0] ;
     $fl_princing=$fl_princing_temp +1 ;
          
     $Query="SELECT no_fin FROM c_princing_temporal WHERE fl_instituto=$fl_instituto ORDER BY fl_princing DESC ";
     $row=RecuperaValor($Query);
     $no_ini=$row['no_fin'];    
     $no_fin= $no_ini + 1;
	 $no_ini=$no_ini+1;
          $Query  = "INSERT INTO c_princing_temporal (fl_princing,mn_mensual, mn_anual, no_ini, no_fin,fl_instituto) ";
     $Query .= "VALUES ($fl_princing,$mn_mensual, $mn_anual,$no_ini, $no_fin,$fl_instituto)";
     EjecutaQuery($Query);

 }
?>