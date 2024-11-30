 <?php
  
  # Libreria de funciones	
  require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  $Query  = "SELECT c.nb_criterio, c.fl_criterio FROM c_criterio c ";
  $Query .= "WHERE NOT EXISTS (SELECT * FROM k_criterio_programa_fame k WHERE k.fl_programa_sp = $clave AND k.fl_criterio = c.fl_criterio) ";
  $Query .=" AND c.fl_instituto=$fl_instituto ";
  $Query .= "ORDER BY c.nb_criterio ASC ";
  //Forma_CampoSelectBD(ObtenEtiqueta(1330), False, 'fl_criterio', $Query, 0, '', True, "onchange='CambiaEstiloBtn(0, 0);'", 'left', 'col col-md-12', 'col col-md-12', '', 'cop_ru');
  FAMECampoSelectBD(ObtenEtiqueta(1330),'fl_criterio', $Query, $fl_criterio, 'select2', True, "onchange='CambiaEstiloBtn(0, 0);'", $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
  
  




 ?>
<script>
    $('#fl_criterio').select2({
        width: "100%"
    });
</script>