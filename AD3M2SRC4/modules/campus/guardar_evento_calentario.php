<?php
	
require '../../lib/general.inc.php';

require '../../lib/AdobeConnectClient.class.php';
require '../../lib/adobeconnect/LicenciaAdobe.class.php';
require '../../lib/adobeconnect/LicenciaAdobeService.class.php';
require '../../lib/campusclases/ClasesService.class.php';


#Recibimos parametors
$id_recibido=RecibeParametroHTML('id');
$fe_event=RecibeParametroHTML('fe_inicio');

#Identificamos valores ya que mandamos una caden separados por comas. 
$ids=explode(',',$id_recibido);
$fl_grupo=$ids[0];
$fl_term=$ids[1];
$fl_programa=$ids[2];
$fl_clase=$ids[3];
$fl_semana=$ids[4];
$fg_extra_clase=$ids[5];

if($fl_term=='CG')
$fl_clase=$fl_grupo;



#Damos formato ala fecha para guardarla
$fe_evento=str_replace('T',' ',$fe_event);
$fe_clase=$fe_evento;
#El formato viene de '2017-07-23 01:30:00' le quitamos los ultimos dos ceros para validadr con adobe
$new_fe_evento = substr($fe_evento, 0, -3);


#Para laconsulta damos formato y hora
$fe=explode(' ',$new_fe_evento);
$fe_consulta=$fe[0];
$hr_consulta=$fe[1];

#Recupermaos las clases traslapadas, si esque existen
$Query1="(
SELECT fl_clase_cg,fe_formato_clase,hr_formato_clase AS hr_clase 
FROM clases_globales 
WHERE fe_formato_clase = '$fe_consulta' AND hr_formato_clase='$hr_consulta' 
)
UNION(
SELECT fl_clase,fe_clase,hr_clase AS fl_clase 
FROM groups_schedules 
WHERE fe_clase='$fe_consulta' AND hr_clase='$hr_consulta' 
) ";
$rs1=EjecutaQuery($Query1);
$tot_registros = CuentaRegistros($rs1);
   $fl_licencias_live_sesion= array();
    $contador=0;
    $cl_adobe=0;
    for($m=1;$row=RecuperaRegistro($rs1);$m++){
        
        $contador++;
        
        $fl_clase_=$row[0];

       // array_push($clavesLicenciasUsadas, $row[0]);
        
       
        $Querylive="SELECT cl_licencia FROM k_live_session WHERE fl_clase=$fl_clase_ ";
        $rowlive=RecuperaValor($Querylive);
        $cl_clave_existente=$rowlive['cl_licencia'];
        
        if($cl_clave_existente>$cl_adobe){
        
            $cl_adobe_siguiente=$cl_clave_existente;
            $cl_adobe=$cl_clave_existente;
        
        }
        
        
        
    }     
    

        
           if($tot_registros< 4){
    
    
                        delLiveSession($fl_clase);

 
                        #formato $fechaHora='2017-07-23 19:00';
                        logger("======== INICIO PROCESO CLASE =================");

                        logger("Registro: Global Calendar");

                        logger("Folio clase: " . $fl_clase . "<br>");
                        logger("Fecha clase: " . $fe_clase . "<br>");

                        $fgActualizarRegistroClase = false; 

                        # Verifica si tiene o no creada una live session, si le falta, se la crea.
                        # Borra la clase anterior y agrega la nueva sobre la licencia que le corresponde por la nueva fecha y hora
                        $QueryDel  = "select fl_live_session ";
                        $QueryDel .= "from k_live_session where fl_clase = $fl_clase";   
                        $rowDel = RecuperaValor($QueryDel);
                        $fl_live_session_actual = $rowDel[0];

                        logger("Para verificar si tiene o no live session: $QueryDel");

                        if ( empty($fl_live_session_actual) ) { 
                            $fgActualizarRegistroClase = true;
                        }
                        logger("fgActualizarRegistroClase: $fgActualizarRegistroClase");
                        if ($fgActualizarRegistroClase) 
                            logger("Es necesario actualizar la clase");
                        else
                            logger("NO es necesario actualizar la clase");

                        #En caso de cambio de fecha/hora, eliminamos el registro para liberar la licencia actual y generar un nuevo registro    
                        if ($fgActualizarRegistroClase) {
                        #   Borra la clase anterior y agrega la nueva sobre la licencia que le corresponde por la nueva fecha y hora
                            $QueryDel  = "select fl_live_session, cl_licencia, cl_meeting_id ";
                            $QueryDel .= "from k_live_session where fl_clase = $fl_clase";   
                            $rowDel = RecuperaValor($QueryDel);

                            logger("Se debe borrar la live session?: $QueryDel");
    
                            $fl_live_session_actual = $rowDel[0];
                            $cl_licencia_actual = $rowDel[1];
                            $cl_meeting_id_actual = $rowDel[2];

                           // $LicenciaActual = $licenciaService->getLicenciaByClave($cl_licencia_actual);
                            if (!empty($cl_meeting_id_actual)) {
                                $Query = "DELETE FROM k_live_session WHERE fl_live_session = $fl_live_session_actual";
                                EjecutaQuery($Query);	  
        
                                logger("Se debe borrar la live session [DELETE]: $Query");

                                delLiveSession($cl_meeting_id_actual, $LicenciaActual);      
                            }
                        }

                        #Verificamos si no existe una clase dentro del horario inicial.
                        // MDB
                        // Verifica el numero de clases que estan programadas a la misma hora, si hay disponibles licencias activas de adobe connect, 
                        // entonces puede agregar la clase, de lo contrario regresa el error a la forma.     

                        $licenciaService = new LicenciaAdobeService();
                        $clasesService = new ClasesService();
                        $fechaHora="'".$new_fe_evento."'";
                        $fl_clase_actual = $fl_clase;

                        $clasesTraslapadas = $clasesService->getNumClasesTraslapadas($fechaHora, $fl_clase_actual);
                        $clavesLicenciasTraslapadas = $clasesService->getClavesLicenciasTraslapadas($fechaHora, $fl_clase_actual);      
                        $licenciasAdobe = $licenciaService->getLicenciasDisponibles($clavesLicenciasTraslapadas);      



                        //////////////////////////////////////////////////////////////
                        // IMPORTANTE:
                        // Las licencias a contar son las que ya estan siendo usadas por clases traslapadas mas las que se necesitan para las clases nuevas
                        //
                        // Las licencias que se deben usar para las clases nuevas son las que se obtuvieron en $licenciasAdobe, esta excluye las licencias en uso 
                        // de ese mismo grupo de clases traslapadas.
                        //////////////////////////////////////////////////////////////
                        $numLicenciasAdobe = sizeof($licenciasAdobe) + sizeof($clavesLicenciasTraslapadas);

                        logger("Clases traslapadas: $clasesTraslapadas");
                        logger("Licencias traslapadas: ");
                        logger($clavesLicenciasTraslapadas, true);

                        logger("Licencias disponibles: ");
                        logger($licenciasAdobe, true);      

                        logger("Licencias disponibles: " . $numLicenciasAdobe);
                        logger("Licencias requeridas: " . $clasesTraslapadas);
                        logger("Licencias requeridas DEBE SER MAYOR O IGUAL QUE licencias disponibles");

                        if ( $licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe) )
                            logger("Licencias disponibles " . $licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe));
                        else
                            logger("Sin licencias disponibles " . $licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe));

                         $Query = "";
                              if ($licenciaService->licenciasSuficientes($clasesTraslapadas, $numLicenciasAdobe)) {
          
                                       #Actualiza solo si existen licencia disponibles.
                                       if ($clasesTraslapadas < sizeof($licenciasAdobe)) {
                  
                                           $Query  = "UPDATE k_clase SET fe_clase='$fe_clase' ";
                                           $Query .= "WHERE fl_clase=$fl_clase";			
                                           EjecutaQuery($Query);
                   
                                           logger("ACTUALIZANDO LA CLASE: $Query");
                   
                   
                                           logger("Query para insert/update de la clase: $Query <br>");
                   
                   
                                           #Recuperamos,la licencias ya ocupadas y asignamos la disponible.
                                          
                                           
                                           
                                           
                    
                   
                   
                                           // Creacion sesion campus y AdobeConnect
                                           if (sizeof($licenciasAdobe)> 0) {
                                               $licenciaAC = $licenciasAdobe[$cl_adobe]; // Usa una nueva licencia, toma la primera del arreglo
                                               iuLiveSession($fl_clase, $licenciaAC);  
                                           }
                   
                                           if (!empty($licenciaAC))
                                               logger("La licencia a usar: " . $licenciaAC->getClLicencia());
                   
                   
            
                   
                   
                                           #Actualizamos  clases globales
                                           if($fl_term=='CG'){
                                               $fl_clase_cg=$fl_grupo;

                                               $Query="UPDATE k_clase_cg SET fe_clase='$fe_evento' WHERE fl_clase_cg=$fl_clase_cg ";
                                               EjecutaQuery($Query);
                       
                                               $Query="UPDATE k_clase_cg_temporal SET fe_clase='$fe_evento' WHERE fl_clase_cg=$fl_clase_cg ";
                                               EjecutaQuery($Query);
                       
                                           }else{
                                               #Actualizamos Shedules
                                               $Query="UPDATE k_clase SET fe_clase='$fe_evento' WHERE fl_clase=$fl_clase_recibido AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana AND fg_adicional='$fg_extra_clase' ";
                                               EjecutaQuery($Query);
                                           }

                   
                                           #Actulaiza las licencias de k_live_sesion

                                           echo json_encode((Object) true);
                   
                   
                   
                                         
                   
                   
               
                   
                                       }else{
               
                  
                                       }
          
        
          
         
   
                              }else{
          
            
                              }
                              logger("======== FIN PROCESO CLASE =================");
      


           }








###############################function de adobe connect################################


function delLiveSession($fl_clase) {
    
    $licenciaServiceAux = new LicenciaAdobeService();      
    
    // Borra la clase en adobe connect
    $QuerySel  = "select fl_live_session, cl_licencia, cl_meeting_id ";
    $QuerySel .= "from k_live_session where fl_clase = $fl_clase";   
    $rowDel = RecuperaValor($QuerySel);
    
    logger("Query select: $QuerySel");

    $fl_live_session_actual = $rowDel[0];
    $cl_licencia_actual = $rowDel[1];
    $cl_meeting_id_actual = $rowDel[2];

    logger("Borrando de adobe connect");
    $LicenciaActual = $licenciaServiceAux->getLicenciaByClave($cl_licencia_actual);
    if (!empty($cl_meeting_id_actual)) {
        //delLiveSession($cl_meeting_id_actual, $LicenciaActual);              
        //$clLicencia = $licenciaAC->getClLicencia();
        $clienteAdobe = new AdobeConnectClient($LicenciaActual);
        $clienteAdobe->deleteMeeting($cl_meeting_id_actual);
    }
    logger("Borrado FIN de adobe connect");
    
    
    
    $Query = "DELETE FROM k_live_session WHERE fl_clase = $fl_clase";
    EjecutaQuery($Query);
    
    logger("Query para borrar live session: $Query");

} 

function iuLiveSession($p_fl_clase, LicenciaAdobe $licenciaAC) {
    
    $clLicencia = $licenciaAC->getClLicencia();
    
    logger("clLicencia: $clLicencia <br>");
    
    // MDB ADOBECONNECT Datos para los parametros Integracion Adobe Connect
    // Se crean las sesiones de Adobe Connect desde este programa
    // Para BBB no es necesario crearlas, se hace al momento en que un teacher 
    // o un student entra a la sesion desde el campus.
    $Query  = "select ds_titulo, DATE_FORMAT(fe_clase, '%Y-%m-%dT%H:%i:%s') fe_ini, ";
    $Query .= "DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL 2 HOUR), '%Y-%m-%dT%H:%i:%s') fe_fin ";
    $Query .= "from k_clase a, k_semana b, c_leccion c ";
    $Query .= "where a.fl_semana = b.fl_semana ";
    $Query .= "and b.fl_leccion = c.fl_leccion ";
    $Query .= "and a.fl_clase = $p_fl_clase";
    $row = RecuperaValor($Query);   
    
    logger("Query en iuLiveSession: $Query <br>");

    $titulo_leccion = $row[0];
    $fecha_inicio = $row[1];
    $fecha_fin = $row[2];
    $id_leccion_url = "VAN_".$clLicencia."_".rand(1000000, 9999999);   

    // Inserta o actualiza la sesion en adobe connect    
    $clienteAdobe = new AdobeConnectClient($licenciaAC);

    // Crea o actualiza la reunion en adobe connect
    $Query  = "select fl_live_session, cl_meeting_id ";
    $Query .= "from k_live_session where fl_clase = $p_fl_clase";   
    $row = RecuperaValor($Query);
    
    logger("Query 2 en iuLiveSession: $Query <br>");

    $fl_live_session = $row[0];
    $cl_meeting_id = $row[1];

    // MDB 25/OCT/2012
    // El nombre de la leccion para la sesion de adobe connect debe ser de maximo 60 caracteres
    // El titulo tiene concatenado el folio de la clase que es un int de 8 y un gion, por lo que para
    // el titulo de la leccion quedan 51 caracteres disponibles, trunco la cadena a esa longitud.
    $titulo_leccion = substr($titulo_leccion, 0, 51);
    
    $titulo_leccion = $titulo_leccion . "_" . $p_fl_clase;
    
    logger("Titulo leccion: $titulo_leccion <br>");
    
    // No existe la sesion y la crea, existe y la actualiza
    if (empty($fl_live_session)) { 
        // Regresa un arreglo de tipo:
        // $data["estatus"]
        // $data["meeting_id"]
        // $data["url-path"]
        logger("Insertando el live session <br>");
        
        $data = $clienteAdobe->createMeeting('', utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin, $id_leccion_url);        

        $meeting_id = $data["meeting_id"];
        logger ("Meeting id: $meeting_id <br>");
        
        if ($data["estatus"] == "existe")
            $id_leccion_url = $data["url-path"];
        
        // Se creo la clase en adobe connect
        if (!empty($meeting_id)) {
            // Permisos para que se puedan conectar a la clase
            $clienteAdobe->permisosMeeting($meeting_id);    
            $clienteAdobe->addHostMeeting($meeting_id);

            $Query  = "INSERT INTO k_live_session ";
            $Query .= "(fl_clase, cl_estatus, ";
            $Query .= "ds_meeting_id, ds_password_admin, ds_password_asistente, ds_mensaje_bienvenida, cl_meeting_id, cl_licencia) ";
            $Query .= "VALUES ($p_fl_clase, 1, '$id_leccion_url', '$moderatorPW', '$attendeePW', '$welcome', '$meeting_id', $clLicencia) ";
            EjecutaQuery($Query);
        }
        else {
            // No se pudo crear, puede ser que ya exista        
        }
    }
    else {
        $clienteAdobe->updateMeeting($cl_meeting_id, utf8_encode($titulo_leccion), $fecha_inicio, $fecha_fin);
    }	  
    // Termina codigo Adobe connect 
}

function logger($msg, $esVarDump=false) {
    $showLog = false;
    if(!$showLog)
        return false;
    
    if($esVarDump)
        var_dump($msg);
    else
        echo $msg . "<br>";
}
###############################end function ############################################

?>