<?php

# Libreria de funciones
require '../../lib/general.inc.php';
require '../../lib/zoom_config.php';

#meting eliminado
$cl_licenica=2;
$id_metting="88144219734";

#valido
$cl_licenica=1;
$id_metting="87140183660";

#id no valido
$cl_licenica=2;
$id_metting="83045498420";


$exists=VerifyMeetingZoom($cl_licenica,$id_metting);

echo $exists;


?>