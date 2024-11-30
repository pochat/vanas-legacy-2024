<?php
 
	# Libreria de funciones
	require("../lib/self_general.php");

  # Obtenemos usuario
  $fl_usuario_sp = ValidaSesion(False,0, True);
  
  # Recibimos posicion a eliminar
  $fl_fto_cat_sp = RecibeParametroNumerico('fl_fto_cat_sp');
  
  // echo "1.- Elimina registro unico <br>";
  EjecutaQuery("DELETE FROM k_filtro_categoria_fame  WHERE fl_fto_cat_sp = $fl_fto_cat_sp");
  EjecutaQuery("DELETE FROM k_filtro_sugerencia_fame WHERE fl_fto_cat_sp = $fl_fto_cat_sp");
  
  // echo "2.- Recupera registros sobrantes y migramos a nueva tabla <br>";
  $Query = "SELECT fl_cat_prog_sp, fl_fto_cat_sp FROM k_filtro_categoria_fame WHERE fl_usuario_sp = $fl_usuario_sp";
  $rs = EjecutaQuery($Query);
  $cuantos = CuentaRegistros($rs);
  for($i=0;$row = RecuperaRegistro($rs);$i++){
    // echo "3.- Insertamos categorias restantes fl_cat_prog_sp = ' $row[0]'<br>INSERT INTO k_filtro_elimina_fame (fl_cat_prog_sp, fl_usuario_sp) VALUES ('$row[0]', $fl_usuario_sp)<br><br>";
    EjecutaQuery("INSERT INTO k_filtro_elimina_fame (fl_cat_prog_sp, fl_usuario_sp) VALUES ('$row[0]', $fl_usuario_sp)");
    
    // echo "4.- Voy borrando conforme voy insertando<br>DELETE FROM k_filtro_categoria_fame WHERE fl_fto_cat_sp = $row[1]<br><br>";
    EjecutaQuery("DELETE FROM k_filtro_categoria_fame WHERE fl_fto_cat_sp = $row[1]");
    
    // echo "5.- Elimino resultados<br>DELETE FROM k_filtro_sugerencia_fame WHERE fl_fto_cat_sp = $row[1]<br><br>";
    EjecutaQuery("DELETE FROM k_filtro_sugerencia_fame WHERE fl_fto_cat_sp = $row[1]");
  }
  
  // echo "6.- Envio a tabla real a traves de la funcion correspondiente FtaCat(cat, principal) <br>";
  $Query = "SELECT fl_cat_prog_sp FROM k_filtro_elimina_fame WHERE fl_usuario_sp = $fl_usuario_sp";
  $rs = EjecutaQuery($Query);
  for($i=0;$row = RecuperaRegistro($rs);$i++){
    if($i == 0)
      $principal = 1;
    else
      $principal = 0;
    
    if(is_numeric($row[0])){
      echo "<script>
        FtaCat($row[0], $principal);
        // MtraResFtos();
      </script>";
    }else{
      echo '
      <script>
        FtaCat(\''.$row[0].'\', '.$principal.');
        // MtraResFtos();
      </script>
    ';
    }
    
    // echo "Una vez movidos, los elimino de tabla temporal<br>";
    EjecutaQuery("DELETE FROM k_filtro_elimina_fame WHERE fl_cat_prog_sp = '$row[0]'");

  }
  
  if(empty($cuantos)){
    echo "<script>
      document.getElementById('seleccionados').style.display = 'none';
      document.getElementById('test_prueba').style.display = 'block';
      document.getElementById('sugerencias').style.display = 'none';
    </script>";
  }
  
?>