<?php 
  # Libreria de funciones	
  require("../lib/self_general.php");
 
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  
  $fl_programa_sp = RecibeParametroNumerico('fl_programa');

  
  
  #Cpmenzamos generndo archivos necesarios.
  #Recuperamos el nombre de la leccion 
  $Query="SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
  $row=RecuperaValor($Query);
  $ds_titulo=$row['nb_programa'];
  $id_curso="ID-".$fl_programa_sp;

  $nb_leccion_sp="FAME_".$ds_titulo;

  #Creamos el nombre del archivo zip
  $nb_leccion_sp =str_replace(":","_",$nb_leccion_sp); 
  $nb_leccion_sp =str_replace(" ","_",$nb_leccion_sp); 
  $nb_leccion_sp =str_replace("-","_",$nb_leccion_sp);
  $nb_leccion_sp =str_replace("(","_",$nb_leccion_sp);
  $nb_leccion_sp =str_replace(")","_",$nb_leccion_sp);



  $url="/var/www/html/vanas/fame/site/".$nb_leccion_sp."_".$fl_instituto.".zip";

  $entro=unlink($url);

?>



