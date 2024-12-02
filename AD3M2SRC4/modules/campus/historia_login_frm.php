<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave', True);
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ALUMNOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  //PresentaHeader( );
  //PresentaEncabezado(FUNC_ALUMNOS);
  //Forma_Inicia($clave);
  ?>
  <table id="login" width='100%' class='table table-striped table-hover dataTable no-footer has-columns-hidden'>
  <thead>
    <tr style="background-color:#0092cd;color:#fff;">
      <th><?php echo ObtenEtiqueta(510); ?></th>
      <th><?php echo "Login date";?></th>
      <th><?php echo "Logout date";?></th>
    </tr>
  </thead>
  <tbody>
  <?php
    # Consulta para el listado
    $Query = "SELECT fl_usu_login, CONCAT(ds_nombres, ' ', ds_apaterno) '".ObtenEtiqueta(510)."|left', 
              fe_login 'Login date', fe_logout 'Logout date'
              FROM k_usu_login a, c_usuario b
              WHERE a.fl_usuario = b.fl_usuario
              AND a.fl_usuario = $clave
              ORDER BY fe_login DESC";
    $rs = EjecutaQuery($Query);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    echo "
    <tr>
      <td>".str_texto($row[1])."</td>
      <td>".$row[2]."</td>
      <td>".$row[3]."</td>
    </tr>";
  }
  //Forma_MuestraTabla($Query, TB_LN_NNN, 'login', '', '100%');
  
  //Forma_Termina(False, '', '', ObtenEtiqueta(24), 'javascript:history.back()');
  
  # Pie de Pagina
  //PresentaFooter( );
?>
  </tbody>
  </table>
  <script>
  $("#login").DataTable();
  </script>