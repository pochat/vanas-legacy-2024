<?php
  # Media_ComboBox_Update.php (media_cb_upt.php)

  # Libreria de funciones
  require '../../lib/general.inc.php';

  $fl_programa = RecibeParametroNumerico('fl_programa');
  $no_grado = RecibeParametroNumerico('no_grado');
  $nb_select = RecibeParametroHTML('nb_select');

  # Get maximum term of a program(course)
  $Query  = "SELECT no_grados FROM c_programa WHERE fl_programa=$fl_programa";
  $row = RecuperaValor($Query);
  $no_grados = $row[0];

  if($nb_select == "course")
  {
    # Term/Level combo box
    for($i=0; $i< $no_grados; $i++){
      $opc[$i] = $i+1;
      $val[$i] = $i+1;
    }
    CampoSelect('no_grado', $opc, $val, 1, 'css_input', False, 'onchange = TermChange()');
  }

  if($nb_select == "term")
  {
    # Check for valid terms/levels of a program
    if($no_grado > $no_grados)
      $no_grado = 1;
    # Find the initial value of fl_class 

    # Class combo box 
    $Query  = "SELECT nb_class, fl_class FROM c_class WHERE fl_programa=$fl_programa AND no_grado=$no_grado ORDER BY no_orden";
    CampoSelectBD('fl_class', $Query, '');
  }  

?>