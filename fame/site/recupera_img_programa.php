<?php

# Libreria de funciones
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False, 0, True);
$fl_instituto = ObtenInstituto($fl_usuario);
$perfil_usuario = ObtenPerfilUsuario($fl_usuario);

#Verficamos si es alumno de vanas para colocar boton de pagar curso o invitar a otro compadre.
if ($perfil_usuario == PFL_ESTUDIANTE_SELF)
    $fg_puede_liberar_curso = PuedeLiberarCurso($fl_instituto, $fl_usuario);

$fl_programa = $_REQUEST['valor'];

$Query = "SELECT nb_programa".$sufix.", nb_thumb FROM c_programa_sp WHERE fl_programa_sp = ".$fl_programa;
$rs = RecuperaValor($Query);
$nb_programa = $rs[0];
$nb_thumb = $rs[1];
echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
      </button>
      <div style="display: inline-block; margin-left: 10px;"><img class="padding-10" src="/AD3M2SRC4/modules/fame/uploads/'.$nb_thumb.'" style="height: 120px; margin: 4px;"></div>
      <div class="padding-10" style="display: inline-block; position: relative; top: 20px;">
      <h6 class="modal-title" style=""><strong>
       '.$nb_programa.' </strong></h6><br><h4>'.ObtenEtiqueta(2581).'</h4>
      </div>';
?>
