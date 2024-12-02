<?php

require '../../lib/general.inc.php';
require '../../lib/AdobeConnectClient.class.php';

require '../../lib/adobeconnect/LicenciaAdobe.class.php';
require '../../lib/adobeconnect/LicenciaAdobeService.class.php';
require '../../lib/campusclases/ClasesService.class.php';
require '../../lib/zoom_config.php';



$zoom_id=RecibeParametroHTML('value');
$data=$_POST['name'];
$fl_live_session=$_POST['pk'];
$values=explode("#",$data);
$tabla=$values[0];
$fecha=$values[1];
$hora=$values[2];
$ds_titulo=$values[3];
$zoom_id_actual=$values[4];
$fl_clase=$values[5];

$ds_titulo=str_replace('<br/>',' ',$ds_titulo);
$ds_titulo=str_replace('<br>',' ',$ds_titulo);
$ds_titulo=str_replace('<small>',' ',$ds_titulo);
$ds_titulo=str_replace('<small/>',' ',$ds_titulo);
$ds_titulo=str_replace('<b>',' ',$ds_titulo);
$ds_titulo=str_replace('<b/>',' ',$ds_titulo);
#Damos formato a la clase para zoom.
//$fe_clase_zoom=strtotime('+0 day',strtotime($fe_clase_d));
//$fe_clase_zoom= date('Y-m-d',$fe_clase_zoom);
//$fe_clase_actual=$fe_clase_zoom;
$fe_clase_zoom=$fecha."T".$hora.":00";
$pass_clase_zoom=rand(99999,5)."i".$fl_live_session;


#se busac la tabla.
switch ($tabla){
    case "k_live_session_grupal":    
        $columna="fl_live_session_grupal";
        $columna2="fl_clase_grupo";
        break;
    case "k_live_session":   
        $columna="fl_live_session";
        $columna2="fl_clase";
        break;
    case "k_live_sesion_cg": 
        $columna="fl_live_session_cg";$columna2="fl_clase_cg";
        break;
}

$Queryl  = "SELECT $columna, zoom_id,zoom_meeting_id ";
$Queryl .= "FROM $tabla ";
$Queryl .= "WHERE $columna2=".$fl_clase ;
$rowl = RecuperaValor($Queryl);
$cl_licenica=$rowl['zoom_id'];
$zoom_meeting_id=$rowl['zoom_meeting_id'];




$verifica_metting=VerifyMeetingZoom($zoom_id_actual,$zoom_meeting_id);

if(!empty($verifica_metting)){
    #Eliminamos la clase de zoom
    DeletedMeetingZoom($fl_live_session,$tabla,$zoom_id_actual);
}

#Creamos la clase en zoom
create_meetingZoom($fl_live_session,'60',$ds_titulo,$fe_clase_zoom,$pass_clase_zoom,''.$tabla.'',$zoom_id);





?>
