<?php 
# Libreria de funciones
require("../lib/self_general.php");

  # get the list of items id separated by cama (,)
  $list_order = $_POST['list_order'];
  $fl_playlist_padre = $_REQUEST['fl_playlist'];

  # convert the string list to an array
  $list = explode(',' , $list_order);
  $i = 1 ;
  
  foreach($list as $id) {
      
      $fl_programa_sp=$id;
      $fl_programa_sp=str_replace("divp_","",$fl_programa_sp);
          if(!empty($fl_programa_sp)){

              EjecutaQuery("UPDATE k_playlist_course SET no_orden = $i WHERE fl_programa_sp=$fl_programa_sp AND fl_playlist_padre=$fl_playlist_padre ");

              $i++ ; 
          }

  }
  
  #Recuperamos el Orden de los cursos y volvemos asignar.
  $Query="SELECT no_orden,fl_programa_sp FROM k_playlist_course WHERE fl_playlist_padre=$fl_playlist_padre  ";
  $rs = EjecutaQuery($Query);
  $tot_registros = CuentaRegistros($rs);
  for($i=1;$row=RecuperaRegistro($rs);$i++){
      
      $no_orden=$row[0];
      $fl_programa_sp=$row[1];
     
      
      echo"<script>
      
      
       
             var id_number = $('#order_$fl_programa_sp').text();            
             $('#order_$fl_programa_sp').empty();	            
             var n = parseInt($no_orden);          
             $('#order_$fl_programa_sp').append(n);          
           
      
      </script>";
      
      
      
   //   $result["playlist".$i] = array(
    // "id" => $fl_programa_sp,
    // "no_orden" => $no_orden
     
   //  );
      
      
  }


  
  
  //$result["size"] = array("total" => $i);
  //echo json_encode((Object) $result);
  
 
  
  
?>