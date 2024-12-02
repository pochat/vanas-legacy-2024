<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  
  
  # Recibe parametros
  $fl_maestro=RecibeParametroHTML('fl_maestro');
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_sp');
  $fl_usuario_sp=RecibeParametroNumerico('fl_usuario');
 
  $Query="UPDATE k_usuario_programa SET fl_maestro=$fl_maestro WHERE fl_usuario_sp=$fl_usuario_sp ";
  $Query.=" AND fl_programa_sp=$fl_programa_sp ";
  EjecutaQuery($Query);
  
 
   echo "<script>
                             $.smallBox({
                                  title : '<h4 >".ObtenEtiqueta(1645).":</h4>',
                                  content : ' <p class=\"text-align-right\"><i class=\"fa fa-save\"></i></p>',
                                  color : '#659265',
                                  icon : 'fa fa-save',
                                  timeout : 4000
                                });
                           $('#otro_email_$i').removeClass('hidden');</script>
                    </script> ";
  
  
  
 
?>