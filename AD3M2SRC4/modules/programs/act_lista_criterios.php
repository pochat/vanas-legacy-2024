 <?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  $Query  = "SELECT c.nb_criterio, c.fl_criterio FROM c_criterio c ";
  $Query .= "WHERE NOT EXISTS (SELECT * FROM k_criterio_programa k WHERE k.fl_programa = $clave AND k.fl_criterio = c.fl_criterio) ";
  $Query .= "ORDER BY c.nb_criterio ASC ";
  Forma_CampoSelectBD(ObtenEtiqueta(1330), False, 'fl_criterio', $Query, 0, '', True, "onchange='CambiaEstiloBtn(0, 0);'", 'left', 'col col-md-12', 'col col-md-12', '', 'cop_ru');
   echo "
   <script>
   pageSetUp();
   </script>";
?>