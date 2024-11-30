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

$Query = "SELECT nb_programa".$sufix." FROM c_programa_sp WHERE fl_programa_sp = ".$fl_programa;
$rs = RecuperaValor($Query);
$nb_programa = $rs[0];
echo "<span><strong>".ObtenEtiqueta(2584)." </strong> " . $rs[0]."</span>";

?>