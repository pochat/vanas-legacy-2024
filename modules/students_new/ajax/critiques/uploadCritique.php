<?php

include('../../../lib/adodb/adodb.inc.php');

echo "BEGIN UPLOAD...\n";

// Variable para usar valores de desarrollo (windows) o de produccion (linux)
$enProduccion = True;

if ($enProduccion)
  echo "uploadCritique.php En producción";
else
  echo "uploadCritique.php En desarrollo";

$fl_entrega_semanal = $_GET["folio"];
$startDateTime = $_GET["startDateTime"];
$finishDateTime = $_GET["finishDateTime"];

// Log
$escribeArchivoLog = False;
$log = new LogFile();
$log->setIdArchivo($fl_entrega_semanal);
$log->setEscribeEnArchivo($escribeArchivoLog);
$log->inicializaLog();


$archivosABorrar = Array();

$uniqueId = rand(10000,99999);

$fileNameCritique = "rc_" . $fl_entrega_semanal . "_" . $uniqueId . ".flv";
// Nombre del archivo orginal de la grabacion de la webcam del teacher, no lleva uniqueId
$fileNameWebcam = "rc_" . $fl_entrega_semanal . "_cam.flv";

// El archivo final en formato ogg, debe tener el mismo nombre que el original, es el nombre que se escribe en la base de datos
// Lo mismo para la webcam
list($nombreCritiqueSinExtension, $extension) = explode(".",$fileNameCritique);

$log->msgLog("Nombre del archivo sin extensión: " . $nombreCritiqueSinExtension);

// Los archivos originales se deben borrat
// TODO Borrar la webcam
array_push($archivosABorrar, $fileNameCritique);


$extensionFinal = ".ogg";
$fileNameCritiqueOgg = $nombreCritiqueSinExtension . $extensionFinal;
$fileNameWebcamOgg   = $nombreCritiqueSinExtension . "_cam" . $extensionFinal;

/* MDB 17/SEP/2011
 * Ya no se sube el archivo, se copia de la ruta especificada
 */
/*
if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
  echo "File ". $_FILES['userfile']['name'] ." uploaded successfully.\n";
  move_uploaded_file ($_FILES['userfile'] ['tmp_name'], $_FILES['userfile'] ['name']);
 *
 */

  if ($enProduccion) {
    // Prod
    $rutaArchivoCritica = "/home/vanas/www/java/";
    $comandoExecute = "cp " . $rutaArchivoCritica . "rc_" . $fl_entrega_semanal . ".flv" . " " . "\"" .$pathArchivosCriticas. $fileNameCritique . "\"";
  }
  else {
    // Desa
    $rutaArchivoCritica = "E:\\Desarrollo\\Java\\EjemplosMDB\\Sockets\\Upload\\Server\\";
    $comandoExecute = "copy \"" . $rutaArchivoCritica . "rc_" . $fl_entrega_semanal . ".flv\"" . " " . "\"" .$pathArchivosCriticas. $fileNameCritique . "\"";
  }
  array_push($archivosABorrar, "rc_" . $fl_entrega_semanal . ".flv");
  $log->msgLog("COPIA EL VIDEO DE LA CRITICA AL DIRECTORIO DE TRABAJO", "DEBUG");
  $log->msgLog($comandoExecute, "DEBUG");
  exec($comandoExecute);

  // TODO Usar las constantes de conexion a BD que ya existen
  $user = "vanas_usvanas";
  $pwd  = "pwdvanas100373k";
  $server = "localhost";
  $db     = "vanas_vanas";

  $DB = NewADOConnection("mysql://$user:$pwd@$server/$db?persist");

  // Antes de hacer el update, leer el nombre del archivo y borrarlo para que no se queden en el server
  $rows = $DB->Execute("select ds_critica_animacion from k_entrega_semanal where fl_entrega_semanal = " . $fl_entrega_semanal );
  if ($rows) {
    $nb_archivo_critica = $rows->fields[0];

    list($nombreArchivoActual, $extensionActual) = explode(".",$nb_archivo_critica);

    array_push($archivosABorrar, $nb_archivo_critica);
    array_push($archivosABorrar, $nombreArchivoActual . "_cam." . $extensionActual);
  }

  $ok = $DB->Execute("update k_entrega_semanal set ds_critica_animacion = '" . $fileNameCritiqueOgg . "' where fl_entrega_semanal = " . $fl_entrega_semanal);

  //$ok = $DB->Execute("delete from k_record_critique_session where fl_entrega_semanal = " . $fl_entrega_semanal );
  //$ok = $DB->Execute("insert into k_record_critique_session values(null, " . $fl_entrega_semanal . ",1,'" . $startDateTime . "', '" . $finishDateTime . "')"  );
  $ok = $DB->Execute("update k_record_critique_session set fe_fin = '" . $finishDateTime . "' where fl_entrega_semanal = " . $fl_entrega_semanal);

  $log->msgLog("update k_record_critique_session set fe_fin = '" . $finishDateTime . "' where fl_entrega_semanal = " . $fl_entrega_semanal);

  // TODO Usar variables del archivo de config
  if ($enProduccion) {
    // Prod
    $homeVanas = "/";
    $directorioCritiques = "/modules/students/critiques/";
    $directorioStreamWebcam = "/usr/share/red5/webapps/saveStreaming/streams/webcamTeacher/";
    $ffmpeg_cmd = "/usr/local/bin/ffmpeg -i ";
    $sox_cmd = "/usr/local/bin/sox ";
  }
  else {
    // Desa
    $homeVanas = "/vanas/";
    $directorioCritiques = "/modules/students/critiques/";
    $directorioStreamWebcam = "C:/Archivos de programa/Red5/webapps/saveStreaming/streams/webcamTeacher/";
    $ffmpeg_cmd = "C:/ffmpeg-git-81ef892-win32-static/bin/ffmpeg.exe -i ";
    $sox_cmd = "C:\sox-14-3-2\sox.exe ";
  }



  /********************************************************
  * Variables comunes
  ********************************************************/
  //$parametros_ffmpeg = " -vcodec libtheora -acodec libvorbis -b 2400k ";
  // Flash " -r 25 -an -sameq "
  $pathArchivosCriticas = $_SERVER[DOCUMENT_ROOT] . $homeVanas . $directorioCritiques;
  $pathVideoOrigen = $_SERVER[DOCUMENT_ROOT] . $homeVanas . "modules/students/videos/";  // TODO Poner en una variable o verificar si ya existe una para el directorio de los videos

  /********************************************************
  * Convierte el video de la critica
  ********************************************************/
  $videoCritique = $pathArchivosCriticas . $fileNameCritique;
  //$parametros_ffmpeg = " -vcodec libtheora -an ";
  //$parametros_ffmpeg = " -vcodec libtheora -an -r 23.97 -b 320k ";
  $parametros_ffmpeg = " -an ";
  $comandoExecute = $ffmpeg_cmd . $videoCritique . $parametros_ffmpeg . $videoCritique;
  $log->msgLog("CONVIERTE EL VIDEO DE LA CRITICA Y LO DEJA EN DIRECTORIO DE TRABAJO", "DEBUG");
  $log->msgLog($comandoExecute, "DEBUG");
  exec($comandoExecute);


  /********************************************************
  * Informacion de la sesion de la critica
  ********************************************************/
  $fe_inicio_critica = "";
  $fe_fin_critica = "";

  $rows = $DB->Execute("select fe_inicio, fe_fin from k_record_critique_session where fl_entrega_semanal = ?", array($fl_entrega_semanal) );
  if ($rows) {
    $fe_inicio_critica = $rows->fields[0];
    $fe_fin_critica = $rows->fields[1];
  }

  $log->msgLog("Inicio crítica: " . $fe_inicio_critica);
  $log->msgLog("Fin crítica: " . $fe_fin_critica);

  /********************************************************
  * Arreglo de duraciones y coordenadas de los segmentos
  * de audio generados con los clicks del teacher
  * en el reproductor de video
  ********************************************************/
  $no_inicio_play = "";
  $no_stop_play = "";
  $fe_inicio = "";
  $fe_fin = "";
  $nb_archivo_video = "";

  $arrAudios = Array();

  $rows = $DB->Execute("select no_inicio_play, no_stop_play, fe_inicio, fe_fin, nb_archivo_video from k_record_critique_audio where fl_entrega_semanal = ? order by fl_rc_audio_session", array($fl_entrega_semanal));

  if ($rows) {
   while (!$rows->EOF) {
      $no_inicio_play = $rows->fields[0];
      $no_stop_play = $rows->fields[1];
      $fe_inicio = $rows->fields[2];
      $fe_fin = $rows->fields[3];
      $nb_archivo_video = $rows->fields[4];


      $log->msgLog("DATOS TRAIDOS DESDE LA BASE ===> ");
      $log->msgLog("no_inicio_play " . $no_inicio_play);
      $log->msgLog("no_stop_play " . $no_stop_play);
      $log->msgLog("fe_inicio " . $fe_inicio);
      $log->msgLog("fe_fin " . $fe_fin);
      $log->msgLog("nb_archivo_video " . $nb_archivo_video);

      array_push($arrAudios,(Array("startPlay"  => $no_inicio_play,
                                   "stopPlay"   => $no_stop_play,
                                   "fechaStart" => $fe_inicio,
                                   "fechaStop"  => $fe_fin,
                                   "nombreArchivo" => $nb_archivo_video)
                            )
                );

      $rows->MoveNext();
   }
  }

  // En el arreglo generado en el loop de arriba tenemos la información de los segmentos de audio
  // que se generaron de los click del usuario, falta incluir al arreglo los segmentos de silencio.
  //
  // Con la informacion de audios y silencios se crea un nuevo arreglo que corresponde a la secuencia de
  // silencios y audios para formar el audio final que se pega al video de la critica.

  // El nuevo arreglo debe guardar la siguiente informacion:
  //  1. Duracion del audio o silencio
  //  2. Para el caso de audio, posicion inicial del audio original, para el caso de silencio, Cero
  //  3. Para el caso de audio, posicion final del audio original, para el caso de silencio es la duracion
  //  4. Nombre del archivo de video original, para el caso de silencio, null

  $arrAudioYSilencios = Array();

  for ($i=0; $i<sizeof($arrAudios); $i++) {
    // El primer registro del nuevo arreglo corresponde al tramo inicial, que va del inicio de la critica, al inicio del primer
    // evento de audio del usuario.
    if ($i == 0) {
      array_push($arrAudioYSilencios, Array( "duracion" => getDateDiffMilliseconds( $fe_inicio_critica, $arrAudios[$i]["fechaStart"] ),
                                             "startPlay" => 0,
                                             "stopPlay" => getDateDiffMilliseconds( $fe_inicio_critica, $arrAudios[$i]["fechaStart"] ),
                                             "nombreArchivo" => ""));
      array_push($arrAudioYSilencios, Array( "duracion" => getDateDiffMilliseconds( $arrAudios[$i]["fechaStart"], $arrAudios[$i]["fechaStop"] ),
                                             "startPlay" => $arrAudios[$i]["startPlay"],
                                             "stopPlay" => $arrAudios[$i]["stopPlay"],
                                             "nombreArchivo" => $arrAudios[$i]["nombreArchivo"]));
    }
    else {
      array_push($arrAudioYSilencios, Array( "duracion" => getDateDiffMilliseconds( $arrAudios[$i-1]["fechaStop"], $arrAudios[$i]["fechaStart"] ),
                                             "startPlay" => 0,
                                             "stopPlay" => getDateDiffMilliseconds( $arrAudios[$i-1]["fechaStop"], $arrAudios[$i]["fechaStart"] ),
                                             "nombreArchivo" => ""));
      array_push($arrAudioYSilencios, Array( "duracion" => getDateDiffMilliseconds( $arrAudios[$i]["fechaStart"], $arrAudios[$i]["fechaStop"] ),
                                             "startPlay" => $arrAudios[$i]["startPlay"],
                                             "stopPlay" => $arrAudios[$i]["stopPlay"],
                                             "nombreArchivo" => $arrAudios[$i]["nombreArchivo"]));
    }
  }


  // Crear audios y silencios
  $archivosFinales = Array();
  $log->msgLog("ARCHIVOS FINALES");
  for ($i=0; $i<sizeof($arrAudioYSilencios); $i++) {

    $log->msgLog("NOMBRE DEL ARCHIVO " . $arrAudioYSilencios[$i]["nombreArchivo"]);

    // Ruta del video original que critico el teacher
    if ($arrAudioYSilencios[$i]["nombreArchivo"] != "") {
      // Estos archivos son los originales, NO SE DEBEN BORRAR ¡¡¡¡¡¡¡
      $videoOrigen =  $pathVideoOrigen . $arrAudioYSilencios[$i]["nombreArchivo"];

      $log->msgLog("VIDEO ORIGINAL " . $videoOrigen);

      // El nombre del archivo de la critica viene en el formato nombre_archivo.flv
      $archivoAudioOrigen = $nombreCritiqueSinExtension . "_audio_" . $i . ".ogg";

      // TODO Para no estar generando tantas veces el audio, se debe verificar si existe
      $comando1 = $ffmpeg_cmd . "\"" . $videoOrigen . "\"" . " -vn -acodec libvorbis \"" . $pathArchivosCriticas . $archivoAudioOrigen . "\"";
      array_push($archivosABorrar, $archivoAudioOrigen);
      $log->msgLog("CREANDO EL AUDIO DEL VIDEO ORIGINAL", "DEBUG");
      $log->msgLog($comando1, "DEBUG");
      exec($comando1);

      /*
      // Convertir a un nuevo archivo para que reconozca segundo por segundo
      $comando2 = $sox_cmd . " \"" . $pathArchivosCriticas . $fileNameCritique . "_audio_origen.ogg\" \"" . $pathArchivosCriticas . $fileNameCritique . "_audio.ogg\"";
      $log->msgLog($comando2, "DEBUG");
      exec($comando2);
       */

      // MDB 12/OCT/2011
      // Para evitar que se generen silencios gigantes al no indicar la duracion
      echo "arrAudioYSilencios[i][stopPlay] " . $arrAudioYSilencios[$i]["stopPlay"];
      if ($arrAudioYSilencios[$i]["stopPlay"] == "null" ||
          $arrAudioYSilencios[$i]["stopPlay"] == "" ||
          $arrAudioYSilencios[$i]["stopPlay"] == "undefined" ||
          empty($arrAudioYSilencios[$i]["stopPlay"])) {

        $arrAudioYSilencios[$i]["stopPlay"] = $arrAudioYSilencios[$i]["startPlay"];

        echo "AUDIOS Hay un valor invalido en stopPlay para el valor " . $i . " le asignamos el valor " . $arrAudioYSilencios[$i]["stopPlay"];

      }



      // Extraer los audios
      // Cortar los segmentos necesarios
      $log->msgLog("CREANDO SEGMENTOS DE AUDIO");
      $nombreAudio = $nombreCritiqueSinExtension . "_" . $i . ".ogg";
      array_push($archivosABorrar, $nombreAudio);
      $comando2 = $sox_cmd . " \"" . $pathArchivosCriticas . $archivoAudioOrigen . "\" -r 44100 -c 2 " . $nombreAudio ." trim " . $arrAudioYSilencios[$i]["startPlay"] . " " . $arrAudioYSilencios[$i]["duracion"] / 1000;
      $log->msgLog("-----------------------");
      $log->msgLog("AUDIO " . $i . " Duracion: " . $arrAudioYSilencios[$i]["duracion"] /1000 ." Nombre del archivo: " . $arrAudioYSilencios[$i]["nombreArchivo"] . " De " . $arrAudioYSilencios[$i]["startPlay"] . " A " . $arrAudioYSilencios[$i]["stopPlay"]);
      $log->msgLog($comando2, "DEBUG");
      exec($comando2);
      $log->msgLog("-----------------------");

      array_push($archivosFinales, $nombreAudio);

    }
    else {
      // Crear silencios

      // MDB 12/OCT/2011
      // Para evitar que se generen silencios gigantes al no indicar la duracion
      if ($arrAudioYSilencios[$i]["stopPlay"] == "null" ||
          $arrAudioYSilencios[$i]["stopPlay"] == "" ||
          $arrAudioYSilencios[$i]["stopPlay"] == "undefined" ||
          empty($arrAudioYSilencios[$i]["stopPlay"])) {

        $arrAudioYSilencios[$i]["stopPlay"] = $arrAudioYSilencios[$i]["startPlay"];

        echo "SILENCIOS Hay un valor invalido en stopPlay para el valor " . $i . " le asignamos el valor " . $arrAudioYSilencios[$i]["stopPlay"];

      }



      $nombreSilencio = $nombreCritiqueSinExtension . "_silence_" . $i . ".ogg";
      array_push($archivosABorrar, $nombreSilencio);
      $comando3 = $sox_cmd . " -n -r 44100 -c 2 " . $nombreSilencio . " trim " . $arrAudioYSilencios[$i]["startPlay"] /1000 . " " . $arrAudioYSilencios[$i]["stopPlay"] /1000;
      $log->msgLog("-----------------------");
      $log->msgLog("CREANDO SILENCIO " . $i . " Duracion: " . $arrAudioYSilencios[$i]["duracion"] ." Nombre del archivo: Siempre null De " . $arrAudioYSilencios[$i]["startPlay"] /1000 . " A " . $arrAudioYSilencios[$i]["stopPlay"] /1000);
      $log->msgLog($comando3, "DEBUG");
      exec($comando3);
      $log->msgLog("-----------------------");

      array_push($archivosFinales, $nombreSilencio);
    }
  }


  // Extraer solo el video de la critica
  $nombreSoloVideo = $nombreCritiqueSinExtension . "_solo_video.ogg";
  array_push($archivosABorrar, $nombreSoloVideo);
  $comando2 = $ffmpeg_cmd . "\"" . $videoCritique . "\"" . " -an -r 23.97 " . "\"" . $pathArchivosCriticas . $nombreSoloVideo . "\"";
  $log->msgLog($comando2, "DEBUG");
  // MDB exec($comando2);
  $nombreSoloVideo = $fileNameCritique;

  $comandoFinal = "";
  for ($i=0; $i<sizeof($archivosFinales); $i++) {
    // Crear el nuevo audio con los silencios necesarios
    $comandoFinal .= $archivosFinales[$i] . " ";
  }

  $nombreAudioFinal = $nombreCritiqueSinExtension . "_audio_final.ogg";
  array_push($archivosABorrar, $nombreAudioFinal);
  $comando2 = $sox_cmd . $comandoFinal . " " . $nombreAudioFinal;
  $log->msgLog($comando2, "DEBUG");
  exec($comando2);


  $log->msgLog("---------------------------------------");

  // Pegar el nuevo audio al video
  // Flash
  // $nombreVideoFinal = $nombreCritiqueSinExtension . "_final.flv";
  // ogg
  $nombreVideoFinal = $nombreCritiqueSinExtension . "_final.ogg";
  array_push($archivosABorrar, $nombreVideoFinal);
  // Flash
  // $comandoVideo = $ffmpeg_cmd . "\"" . $pathArchivosCriticas . $nombreAudioFinal . "\"  -i \"" . $pathArchivosCriticas . $nombreSoloVideo . "\"" . " -vcodec flv -ar 44100 -sameq \"" .  $pathArchivosCriticas . $nombreVideoFinal ."\"";
  //$comandoVideo = $ffmpeg_cmd . "\"" . $pathArchivosCriticas . $nombreAudioFinal . "\"  -i \"" . $pathArchivosCriticas . $nombreSoloVideo . "\"" . " -vcodec libtheora  -acodec libvorbis  \"" .  $pathArchivosCriticas . $nombreVideoFinal ."\"";

   // MDB 19/SEP/2011
  // Si no pudo generar el archivo de audio o es una critica sin audio, solo convierte el video
  $archivo_audio = "";
  $comando_con_audio = "";
  if (file_exists($pathArchivosCriticas . $nombreAudioFinal)) {
    $log->msgLog("Existe el archivo de audio final", "DEBUG");
    $archivo_audio = "\"" . $pathArchivosCriticas . $nombreAudioFinal . "\"  -i ";
    $comando_con_audio = " -acodec libvorbis -b 320k ";
  }
  else {
    $log->msgLog("NO Existe el archivo de audio final", "DEBUG");
    $archivo_audio = "";
    $comando_con_audio = " -an ";
  }
  $comandoVideo = $ffmpeg_cmd . $archivo_audio . "\"" . $pathArchivosCriticas . $nombreSoloVideo . "\"" . " -vcodec libtheora  -r 23.97 " . $comando_con_audio . "  \"" .  $pathArchivosCriticas . $nombreVideoFinal ."\"";
  $log->msgLog($comandoVideo, "DEBUG");
  exec($comandoVideo);



  $log->msgLog("---------------------------------------");

  $rows = $DB->Execute("select ds_critica_animacion from k_entrega_semanal where fl_entrega_semanal = " . $fl_entrega_semanal );
  if ($rows) {
    $nb_archivo_critica = $rows->fields[0];
  }

  $log->msgLog("Nombre del archivo final en la base de datos: " . $nb_archivo_critica);

  if ($enProduccion) {
    // Prod
    $comandoCopy = "cp -f " .  $nombreVideoFinal ."  " . $fileNameCritiqueOgg;
  }
  else {
    // Desa
    $comandoCopy = "copy " .  $nombreVideoFinal ."  " . $fileNameCritiqueOgg;
  }

  $log->msgLog($comandoCopy);
  exec($comandoCopy);


  // Este comando me esta dando problemas en la conversion
  // -vcodec libtheora -acodec libvorbis -b 2400k -sameq

  // Convierte el video de la webcam
  $pathArchivosWebcam = $directorioStreamWebcam;
  $videoWebcam = "\"" . $pathArchivosWebcam . $fileNameWebcam . "\"";
  //$comandoExecute = $ffmpeg_cmd . $videoWebcam . " -sameq -ar 44100 " . "\"" .$pathArchivosCriticas. $fileNameWebcam . "\"";
  //$comandoExecute = "copy " . $videoWebcam . " " . "\"" .$pathArchivosCriticas. $fileNameWebcam . "\"";
  // Solo copiando el archivo
  // $comandoExecute = "cp " . $videoWebcam . " " . "\"" .$pathArchivosCriticas. $fileNameWebcam . "\"";
  // OGG
  //$comandoExecute = $ffmpeg_cmd . $videoWebcam . " -vcodec libtheora -sameq  -acodec libvorbis -b 2400k -ab 32k " . "\"" .$pathArchivosCriticas. $fileNameWebcamOgg . "\"";
  $comandoExecute = $ffmpeg_cmd . $videoWebcam . " -vcodec libtheora -acodec libvorbis -b 320k -ab 32k -r 23.97 " . "\"" .$pathArchivosCriticas. $fileNameWebcamOgg . "\"";
  $log->msgLog($comandoExecute, "DEBUG");
  exec($comandoExecute);

  // MDB 19/SEP/2011
  // Si la camara no tiene audio, traer por lo menos el video
  if (!file_exists($pathArchivosCriticas. $fileNameWebcamOgg)) {
    $log->msgLog("No se creo la camara con audio, intentar crearla con solo el video", "DEBUG");
    $comandoExecute = $ffmpeg_cmd . $videoWebcam . " -vcodec libtheora -b 320k -r 23.97 -an " . "\"" .$pathArchivosCriticas. $fileNameWebcamOgg . "\"";
    $log->msgLog($comandoExecute, "DEBUG");
    exec($comandoExecute);
  }


  // Elimina los archivos temporales
  $borrarArchivosTemporales = 1;

  if ($borrarArchivosTemporales == 1) {
    for ($i=0; $i<sizeof($archivosABorrar); $i++) {
      if ( $archivosABorrar[$i] != "" )
        unlink( $archivosABorrar[$i] );

      $log->msgLog("Eliminando el archivo " . $archivosABorrar[$i], "DEBUG");
    }

  $log->finalizaLog();


  //} MDB 17/SEP/2011 Ya no se sube el archivo, se copia de la ruta especificada

}
else {
  echo "Possible file upload attack: ";
  echo "filename '". $_FILES['userfile']['tmp_name'] . "'.";
  print_r($_FILES);
}



  function getDateDiffMilliseconds( $fecha1, $fecha2 ) {

    $diferencia = 0;

    if (!($fecha1 == "null" || $fecha1 == "NULL" || $fecha1 == "undefined" || empty($fecha1) ||
        $fecha2 == "null" ||  $fecha2 == "NULL" || $fecha2 == "undefined" || empty($fecha2))) {

        // $fecha en formato Y-M-D H:M:S.MS
        $formato = "%d-%d-%d %d:%d:%d.%d";

        sscanf($fecha1, $formato, $anio, $mes, $dia, $hora, $minutos, $segundos, $milisegundos1);
        $secs1 = mktime ( $hora, $minutos, $segundos, $mes, $dia, $anio );

        sscanf($fecha2, $formato, $anio, $mes, $dia, $hora, $minutos, $segundos, $milisegundos2);
        $secs2 = mktime ( $hora, $minutos, $segundos, $mes, $dia, $anio );

        $diferencia_segundos = $secs2 - $secs1;

        $diferencia_milis = $milisegundos2 - $milisegundos1;

        $diferencia = ($diferencia_segundos * 1000) + $diferencia_milis;

    }

    return $diferencia;
  }


  /*function $log->msgLog( $strLog, $level = "INFO" ) {

    $logLevel = "ALL";  // INFO | DEGUG | ALL  | NONE
    $escribeEnArchivo = True;

    if ($logLevel == "NONE") return false;

    if ($level == $logLevel  || $logLevel == "ALL" ) {
      if($escribeEnArchivo) {

      }
      else
        echo $strLog ."\n";
    }

  }*/


  class LogFile {

    private $fileHandler;
    private $escribeEnArchivo;
    private $idArchivo;

    public function __construct() {
    }

    public function setEscribeEnArchivo($escribeEnArchivo) {
      $this->escribeEnArchivo = $escribeEnArchivo;
    }

    public function getEscribeEnArchivo() {

      if (empty($this->escribeEnArchivo)) {
        $escribir = False;
      }
      else {
        $escribir = $this->escribeEnArchivo;
      }

      return $escribir;
    }

    public function setIdArchivo($id) {
      $this->idArchivo = $id;
    }

    public function getIdArchivo() {
      return $this->idArchivo;
    }

    public function inicializaLog() {

      if ($this->getEscribeEnArchivo()) {

        $idArchivo = $this->getIdArchivo();
        if (empty($idArchivo))
          $idArchivo = 0;

        $idArchivoLog = rand(10000,99999);
        $pathLogFiles = "logs";
        $archivoLog = $pathLogFiles . "/" . $idArchivoLog . "_" . $idArchivo . ".log";

        $this->fileHandler = fopen($archivoLog, 'w');
      }
    }

    public function finalizaLog() {
      if ($this->getEscribeEnArchivo())
        fclose($this->fileHandler);
    }

    public function escribeLog( $strLog ) {
      if ($this->getEscribeEnArchivo()) {
        fwrite($this->fileHandler, $strLog);
        echo $strLog . PHP_EOL;
      }
      else {
        echo $strLog . PHP_EOL;
      }
    }

    public function msgLog( $strLog, $level = "INFO" ) {

        $logLevel = "ALL";  // INFO | DEGUG | ALL  | NONE

        if ($logLevel == "NONE") return false;

        if ($level == $logLevel  || $logLevel == "ALL" ) {
          $this->escribeLog($strLog);
        }

      }


  }



?>