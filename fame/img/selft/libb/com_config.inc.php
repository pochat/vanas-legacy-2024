<?php
  #date_default_timezone_set("America/Mexico_City");
  # Compatibilidad de ambientes
  define("FG_PRODUCCION", False);
  if(FG_PRODUCCION) {
    define("DATABASE_USER",  "dev");
    define("DATABASE_PWD",   "D#v3L0p3rr");
    define("INICIO_W",       "/");
    define("CMD_FFMPEG",     "/usr/bin/ffmpeg");
    define("PATH_LINKS",     "/usr/local/red5/webapps/oflaDemo/streams");
    define("PATH_STREAMING", "/var/www/html/vanas/vanas_videos");
    # Variables para critique
    define("RUTA_ARCHIVO_CRITICA", "/var/www/html/vanas/dev/java/");
    define("DIRECTORIO_CRITIQUES", "/modules/students/critiques/");
    define("DIRECTORIO_STREAM_WEBCAM", "/usr/share/red5/webapps/saveStreaming/streams/webcamTeacher/");
    define("FFMPEG_CMD", "/usr/bin/ffmpeg -i ");
    define("SOX_CMD", "/usr/local/bin/sox ");
  }
  else {
    define("DATABASE_USER", "root");
    define("DATABASE_PWD",  "root1234");
    define("INICIO_W",      "/vanas");
    define("CMD_FFMPEG",    "ffmpeg");
    define("PATH_LINKS",    ""); // En desarrollo no se crean ligas
    define("PATH_STREAMING", $_SERVER[DOCUMENT_ROOT]."/vanas/vanas_videos");
    # Variables para critique
    define("RUTA_ARCHIVO_CRITICA", "E:\\Desarrollo\\Java\\EjemplosMDB\\Sockets\\Upload\\Server\\");
    define("DIRECTORIO_CRITIQUES", "/modules/students/critiques/");
    define("DIRECTORIO_STREAM_WEBCAM", "C:/Archivos de programa/Red5/webapps/saveStreaming/streams/webcamTeacher/");
    define("FFMPEG_CMD", "C:/ffmpeg-git-81ef892-win32-static/bin/ffmpeg.exe -i ");
    define("SOX_CMD", "C:\sox-14-3-2\sox.exe ");
  }
  
  # Base de datos
  define("DATABASE_MYSQL",     "mysql");
  define("DATABASE_SLQSERVER", "odbc_mssql");
  define("DATABASE_TYPE",      DATABASE_MYSQL);
  define("DATABASE_SERVER",    "localhost");
  define("DATABASE_NAME",      "vanas_vanas");
  define("DATABASE_FG_DSN",    False);
  define("DATABASE_DSN",       "Driver={SQL Server};Server=".DATABASE_SERVER.";Database=".DATABASE_NAME.";");
  
  # Variables para manejo de sesion
  define("SESION_ADMIN",       "vanas_admin");
  define("SESION_CAMPUS",      "vanas_campus");
  define("SESION_VIGENCIA",    60*60*24);
  define("SESION_RM",          "remember_me");
  define("SESION_VIGENCIA_RM", 3600*24*365*5);
  define("SESION_CHECK_RM",    "check_remember_me");
  
  # Variables para manejo de idioma
  define("ESPANOL",         1);
  define("INGLES",          2);
  define("IDIOMA_NOMBRE",   "vanas_lang");
  define("IDIOMA_VIGENCIA", 60*60*24*365);
  define("IDIOMA_DEFAULT",  INGLES);
  define("IDIOMA_ALTERNO",  ESPANOL);
  
  # Tipos de permiso para funciones
  define("PERMISO_EJECUCION",    1);
  define("PERMISO_DETALLE",      2);
  define("PERMISO_MODIFICACION", 3);
  define("PERMISO_ALTA",         4);
  define("PERMISO_BAJA",         5);
  
  # Manejo de fechas
  define("FMT_CAPTURA",  1);
  define("FMT_FECHA",    2);
  define("FMT_HORA",     3);
  define("FMT_HORAMIN",  4);
  define("FMT_DATETIME", 5);
  
  # Variables de ambiente
  define("ADMINISTRADOR",    1);
  define("PFL_MAESTRO",      2);
  define("PFL_ESTUDIANTE",   3);
  define("CALIFICACION_NA", 11);
  # Agregamos el nuevo perfil para administradores de self pace
  define("PFL_ADMINISTRADOR", 11);
  
  # Tipos de contenido (c_tipo_contenido.cl_tipo_contenido)
  define("TC_NODO",     1);
  define("TC_NOTICIA",  2);
  define("TC_EVENTO",   3);
  define("TC_LIGA",     4);
  define("TC_PROGRAMA", 5);
  
  # Codigos genericos de error (c_mensaje.cl_mensaje)
  define("ERR_DEFAULT",          0);
  define("ERR_SIN_PERMISO",      1);
  define("ERR_REFERENCIADO",     2);
  define("ERR_REQUERIDO",        3);
  define("ERR_EXPORTAR",         4);
  define("ERR_FORMATO_FECHA",    5);
  define("ERR_FORMATO_EMAIL",    6);
  define("ERR_DUPVAL",           7);
  define("ERR_ENTERO",           8);
  define("ERR_FG_FIJO",          9);
  define("ERR_TINYINT",         10);
  define("ERR_SMALLINT",        11);
  define("ERR_FORMATO_HORAMIN", 12);
  define("MSG_ELIMINAR",        13);
  define("ERR_DUPVAL2",         14);
  define("ERR_ARCHIVO_JPEG",    15);
  define("ERR_FECHA_MAYOR",     16);
  
  # Longitud maxima por tipo de campos
  define("MAX_SMALLINT", 65534);
  define("MAX_TINYINT",    255);
  
  # Variables para critiques
  define("PATH_ARCHIVOS_CRITICAS", $_SERVER[DOCUMENT_ROOT].INICIO_W.DIRECTORIO_CRITIQUES);
  define("PATH_VIDEO_ORIGEN", $_SERVER[DOCUMENT_ROOT].INICIO_W."modules/students/videos/");
  
      /**
 * EGMC 
 * Rutas de carpetas de clases
 * 20160517
 */
/**
 * EGMC 20150629
 * Separador de directorios 
 * Windows => \
 * Linux   => /
 */
define('DS', DIRECTORY_SEPARATOR);
/**
 * EGMC 20150629
 * Ruta completa del directorio raíz de la aplicación
 */
define('ROOT', dirname(dirname(__FILE__)));
define('BASE_PATH', ROOT);
define('ADM_DIRECTORY', 'AD3M2SRC4');
define("ADM_HOME", "/" . ADM_DIRECTORY);
define("PATH_ADM_HOME", ROOT. ADM_HOME);

//define("PATH_ADM_CONFIG", PATH_ADM_HOME . "/config");
define("PATH_ADM_CLASS", PATH_ADM_HOME . "/class");
define("PATH_ADM_MODELS", PATH_ADM_HOME . "/models");
//define("PATH_ADM_CONTROLLERS", PATH_ADM_HOME . "/controllers");
//define("PATH_ADM_VIEWS", PATH_ADM_HOME . "/views");
//define("PATH_ADM_CACHE", PATH_ADM_HOME . "/cache");
//define("PATH_ADM_VENDOR", PATH_ADM_HOME . "/vendor");
//define("PATH_ADM_CSS", PATH_ADM_HOME . "/css");
//define("PATH_ADM_DIV", PATH_ADM_HOME . "/div");
//define("PATH_ADM_EXPORT", PATH_ADM_HOME . "/export");
//define("PATH_ADM_IMAGES", PATH_ADM_HOME . "/images");
//define("PATH_ADM_JS", PATH_ADM_HOME . "/js");
define("PATH_ADM_LIB", PATH_ADM_HOME . "/lib");
//define("PATH_ADM_LOGS", PATH_ADM_HOME . "/logs");
//define("PATH_ADM_QUERY", PATH_ADM_HOME . "/sql");
//define("PATH_ADM_TMP", PATH_ADM_HOME . "/tmp");
//define("PATH_ADM_PEM", PATH_ADM_HOME . "/pem");
//define("PATH_ADM_FACTURAS", PATH_ADM_HOME . "/facturas");
//define("PATH_ADM_TEMPLATES", PATH_ADM_HOME . "/templates");
//define("PATH_ADM_MEDIA", PATH_ADM_HOME . "/media");

  
?>
