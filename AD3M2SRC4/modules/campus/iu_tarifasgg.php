<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recupera el usuario actual
  ValidaSesion( );
  
  # Recibe Parametros Numericos
  $fl_maestro = RecibeParametroNumerico('fl_maestro');
  $fl_grupo = RecibeParametroHTML('fl_grupo');
  $mn_monto=RecibeParametroHTML('mn_monto');
  $fl_clase_grupo = RecibeParametroNumerico('fl_clase_grupo');

  $Query="SELECT mn_hour_rate_group_global FROM c_maestro WHERE fl_maestro=$fl_maestro ";
  $ro=RecuperaValor($Query);
  $mn_default=$ro['mn_hour_rate_group_global'];
  if(empty($mn_monto))
      $mn_monto=$mn_default;

  EjecutaQuery("DELETE FROM k_maestro_tarifa_gg WHERE fl_clase_grupo=$fl_clase_grupo AND fl_maestro=$fl_maestro ");
  
  EjecutaInsert("INSERT INTO k_maestro_tarifa_gg (fl_maestro,fl_clase_grupo,mn_cgrupo )VALUES($fl_maestro,$fl_clase_grupo,$mn_monto) ");





?>