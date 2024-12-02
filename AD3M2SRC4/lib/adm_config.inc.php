<?php
  
  # Variables para debug
  define("D_DEBUG_ADO", False);
  define("D_BORDES",    0);
  
  # Rutas compartidas del Sitio Publico
  //define("SP_HOME",      $_SERVER[DOCUMENT_ROOT]."/AD3M2SRC4");
  define("SP_HOME",      $_SERVER['DOCUMENT_ROOT']);
  define("SP_IMAGES",    SP_HOME   . "/images");
  define("SP_THUMBS",    SP_IMAGES . "/thumbs");
  define("SP_FLASH",     SP_HOME   . "/swf");
  define("SP_VIDEOS",    SP_HOME   . "/videos");
  define("SP_ANEXOS",    SP_HOME   . "/attachments");
  define("SP_ANEXOS_EN", SP_ANEXOS . "/eng");
  define("SP_TMP",       SP_HOME   . "/tmp");
  
  # Rutas del Sitio Publico para mostrar en web 
  define("SP_HOME_W",      ""); /// en produccion debe estar vacia
  define("SP_IMAGES_W",    SP_HOME_W   . "/images");
  define("SP_THUMBS_W",    SP_IMAGES_W . "/thumbs");
  define("SP_FLASH_W",     SP_HOME_W   . "/swf");
  define("SP_VIDEOS_W",    SP_HOME_W   . "/videos");
  define("SP_ANEXOS_W",    SP_HOME_W   . "/attachments");
  define("SP_ANEXOS_EN_W", SP_ANEXOS_W . "/eng");
  define("SP_TMP_W",       SP_HOME_W   . "/tmp");
  
  # Rutas de los Sitios de Alumnos y Maestros
  define("PATH_CAMPUS",     SP_HOME_W . "/modules");
  define("PATH_ALU",        PATH_CAMPUS . "/students");
  define("PATH_ALU_IMAGES", PATH_ALU . "/images");
  define("PATH_MAE",        PATH_CAMPUS . "/teachers");
  define("PATH_MAE_IMAGES", PATH_MAE . "/images");
  
  # Rutas para directorios
  define("PATH_HOME",    "/AD3M2SRC4");
  //define("PATH_HOME",    "/AD3M2SRC4");
  define("PATH_CSS",     PATH_HOME . "/css");
  define("PATH_EXPORT",  PATH_HOME . "/export");
  define("PATH_IMAGES",  PATH_HOME . "/images");
  define("PATH_JS",      PATH_HOME . "/js");
  define("PATH_LIB",     PATH_HOME . "/lib");
  define("PATH_LOGS",    PATH_HOME . "/logs");
  define("PATH_MODULOS", PATH_HOME . "/modules");
  define("PATH_TMP",     PATH_HOME . "/tmp");
  
  # Rutas para la aministracion viejita
  define("PATH_HOME_V1",    "/admin");
  define("PATH_CSS_V1",     PATH_HOME_V1 . "/css");
  define("PATH_EXPORT_V1",  PATH_HOME_V1 . "/export");
  define("PATH_IMAGES_V1",  PATH_HOME_V1 . "/images");
  define("PATH_JS_V1",      PATH_HOME_V1 . "/js");
  define("PATH_LIB_V1",     PATH_HOME_V1 . "/lib");
  define("PATH_LOGS_V1",    PATH_HOME_V1 . "/logs");
  define("PATH_MODULOS_V1", PATH_HOME_V1 . "/modules");
  define("PATH_TMP_V1",     PATH_HOME_V1 . "/tmp");
  
  # Rutas para el fame
  define("PATH_SELF", SP_HOME_W."/fame");
  define("PAGINA_INICIO_SELF",    PATH_SELF."/index.php");
  define("PATH_SELF_SITE", PATH_SELF."/site");
  define("PATH_SELF_QUERY", PATH_SELF."/Query");
  define("PATH_SELF_LIB", PATH_SELF."/lib");
  define("PATH_SELF_JS", PATH_SELF."/js");
  define("PATH_SELF_CSS", PATH_SELF."/css");
  define("PATH_SELF_IMG", PATH_SELF."/img");
  define("PATH_SELF_UPLOADS", PATH_SELF_SITE."/uploads");  // Archivos de los usuarios
  define("PATH_SELF_VIDEOS", SP_HOME.'/vanas_videos/fame/lessons');
  
  # Variables para manejo de sesion
  define("SESION_INVALIDO",  PATH_HOME."/login.php?err=1");
  define("SESION_EXPIRADA",  PATH_HOME."/login.php?err=2");
  define("SESION_NO_EXISTE", PATH_HOME."/login.php?err=3");
  define("SESION_INACTIVO",  PATH_HOME."/login.php?err=4");
  define("OLVIDO_INVALIDO",  PATH_HOME."/forgot.php?err=1");
  define("OLVIDO_ERR_ENVIO", PATH_HOME."/forgot.php?err=3");
  define("OLVIDO_INACTIVO",  PATH_HOME."/forgot.php?err=4");
  define("OLVIDO_EXITO",     PATH_HOME."/forgot.php?err=5");
  
  # Paginas de uso general
  define("PAGINA_INICIO",   PATH_HOME."/home.php");
  define("PAGINA_ERROR",    PATH_HOME."/error.php");
  define("PAGINA_SALIR",    PATH_HOME."/login.php?r=".rand(1, 32000));
  define("PAGINA_IDIOMA",   PATH_HOME."/lang.php");
  define("PAGINA_OLVIDO",   PATH_HOME."/forgot.php");
  
  # Variables de ambiente
  define("MAIL_SERVER",     ObtenConfiguracion(1));
  define("REGSXPAG",        ObtenConfiguracion(2));
  define("MAX_NIVELES_AUT", ObtenConfiguracion(3));
  define("MAIL_FROM",       ObtenConfiguracion(4));
  define("MAIL_PORT",       ObtenConfiguracion(5));
  define("FG_TRADUCCION",   ObtenConfiguracion(6));
  define("MAXPAGS",         10);
  define("PERFIL_RECRUITER",ObtenConfiguracion(91));
  
  # Identificadores de Menus de sistema (c_modulo.fl_modulo)
  define("MENU_ADMON", 1);
  
  # Variables para despliegue de imagenes
  define("IMG_LOGIN",      ObtenNombreImagen(1));
  define("IMG_ADMON",      ObtenNombreImagen(2));
  define("IMG_ERROR",      ObtenNombreImagen(3));
  define("IMG_INFO",       ObtenNombreImagen(4));
  define("IMG_WARNING",    ObtenNombreImagen(5));
  define("IMG_HELP",       ObtenNombreImagen(6));
  define("IMG_BUSCAR",     ObtenNombreImagen(7));
  define("IMG_NUEVO",      ObtenNombreImagen(8));
  define("IMG_ARCHIVO",    ObtenNombreImagen(9));
  define("IMG_EDITAR",     ObtenNombreImagen(10));
  define("IMG_BORRAR",     ObtenNombreImagen(11));
  define("IMG_CALENDARIO", ObtenNombreImagen(12));
  define("IMG_EXAMINAR",   ObtenNombreImagen(13));
  define("IMG_LIMPIAR",    ObtenNombreImagen(14));
  define("IMG_EXCEL",      ObtenNombreImagen(15));
  define("IMG_PDF",        ObtenNombreImagen(16));
  define("IMG_AGREGAR",    ObtenNombreImagen(17));
  
  # Etiquetas generales
  define("ETQ_TITULO_PAGINA", ObtenEtiqueta(1));
  define("ETQ_FOOTER",        ObtenEtiqueta(2));
  define("ETQ_LINK_LOGO",     ObtenEtiqueta(3));
  define("ETQ_ALT_LOGO",      ObtenEtiqueta(4));
  define("ETQ_TITULO_ADMON",  ObtenEtiqueta(5));
  define("ETQ_SALIR",         ObtenEtiqueta(6));
  define("ETQ_FECHA",         ObtenEtiqueta(7));
  define("ETQ_USUARIO",       ObtenEtiqueta(8));
  define("ETQ_IDIOMA",        ObtenEtiqueta(9));
  define("ETQ_INSERTAR",      ObtenEtiqueta(10));
  define("ETQ_EDITAR",        ObtenEtiqueta(11));
  define("ETQ_ELIMINAR",      ObtenEtiqueta(12));
  define("ETQ_SALVAR",        ObtenEtiqueta(13));
  define("ETQ_CANCELAR",      ObtenEtiqueta(14));
  define("ETQ_REGISTROS",     ObtenEtiqueta(15));
  define("NO_ETQ_SI",         16);
  define("ETQ_SI",            ObtenEtiqueta(NO_ETQ_SI));
  define("NO_ETQ_NO",         17);
  define("ETQ_NO",            ObtenEtiqueta(NO_ETQ_NO));
  define("ETQ_NOMBRE",        ObtenEtiqueta(18));
  define("ETQ_DESCRIPCION",   ObtenEtiqueta(19));
  define("ETQ_TIT_INFO",      ObtenEtiqueta(20));
  define("ETQ_TIT_WARN",      ObtenEtiqueta(21));
  define("ETQ_TIT_ERROR",     ObtenEtiqueta(22));
  define("ETQ_TIT_CONFIRM",   ObtenEtiqueta(23));
  define("ETQ_REGRESAR",      ObtenEtiqueta(24));
  define("ETQ_ERROR",         ObtenEtiqueta(25));
  define("ETQ_EXPORTAR",      ObtenEtiqueta(26));
  define("ETQ_BUSCAR",        ObtenEtiqueta(27));
  define("ETQ_BUSCAR_EN",     ObtenEtiqueta(28));
  define("ETQ_TODOS_CAMPOS",  ObtenEtiqueta(29));
  define("ETQ_EJECUTAR",      ObtenEtiqueta(30));
  define("ETQ_LIMPIAR",       ObtenEtiqueta(31));
  define("ETQ_MOSTRANDO",     ObtenEtiqueta(32));
  define("ETQ_DE",            ObtenEtiqueta(33));
  define("ETQ_REGISTRO",      ObtenEtiqueta(34));
  define("ETQ_FMT_FECHA",     ObtenEtiqueta(35));
  define("ETQ_DIAS_SEMANA",   ObtenEtiqueta(36));
  define("ETQ_DIAS_CORTO",    ObtenEtiqueta(37));
  define("ETQ_MESES",         ObtenEtiqueta(38));
  define("ETQ_MESES_CORTO",   ObtenEtiqueta(39));
  define("ETQ_ANTERIOR",      ObtenEtiqueta(40));
  define("ETQ_SIGUIENTE",     ObtenEtiqueta(41));
  define("ETQ_CLAVE",         ObtenEtiqueta(42));
  define("ETQ_TRADUCCION",    ObtenEtiqueta(43));
  define("ETQ_TIPO",          ObtenEtiqueta(44));
  define("ETQ_TITULO",        ObtenEtiqueta(45));
  define("ETQ_ACEPTAR",       ObtenEtiqueta(46));
  define("ETQ_SELECCIONAR",   ObtenEtiqueta(47));
  define("ETQ_ORDEN",         ObtenEtiqueta(48));
  define("ETQ_DETALLE",       ObtenEtiqueta(49));
  define("ETQ_ESPANOL",       ObtenEtiqueta(50));
  define("ETQ_INGLES",        ObtenEtiqueta(51));
  define("ETQ_IMPRIMIR",      ObtenEtiqueta(701));
  
  # Extensiones para funciones
  define("PGM_FORM",   "_frm");
  define("PGM_INSUPD", "_iu");
  define("PGM_INSERT", "_i");
  define("PGM_UPDATE", "_u");
  define("PGM_DELETE", "_del");
  define("PGM_EXPORT", "_exp");
  define("PGM_REPORT", "_rpt");
  define("PGM_SEND",   "_snd");
  
  # Tipos de tablas LN=Listas Normales, LE=Listas Editables, I=Insert, U=Update, D=Delete, N=No permitido
  define("TB_LN_NNN",  0);
  define("TB_LN_NND",  1);
  define("TB_LN_NUN",  2);
  define("TB_LN_NUD",  3);
  define("TB_LN_INN",  4);
  define("TB_LN_IND",  5);
  define("TB_LN_IUN",  6);
  define("TB_LN_IUD",  7);
  define("TB_LE_NNN", 10);
  define("TB_LE_NND", 11);
  define("TB_LE_NUN", 12);
  define("TB_LE_NUD", 13);
  define("TB_LE_INN", 14);
  define("TB_LE_IND", 15);
  define("TB_LE_IUN", 16);
  define("TB_LE_IUD", 17);
  
  # Constantes para LOVS
  define("LOV_TIPO_RADIO",  1);
  define("LOV_TIPO_CHKBOX", 2);
  define("LOV_CHICO",       1);
  define("LOV_MEDIANO",     2);
  define("LOV_GRANDE",      3);
  define("LOV_ENORME",      4);
  
  # Identificadores de LOVs
  define("LOV_PERFILES",    1);
  define("LOV_USUARIOS",    2);
  define("LOV_SECCIONES",   3);
  define("LOV_TEMPLATES",   4);
  define("LOV_MENUS",       5);
  define("LOV_SUBMENUS",    6);
  define("LOV_MAESTROS",    7);
  define("LOV_PAGINAS",     8);
  
  
  # Identificadores de funciones (fl_funcion de c_funcion)
  define("FUNC_CURSOS",      1);
  define("FUNC_CLASSES",    98);
  define("FUNC_MEDIA",       2);
  define("FUNC_CICLOS",      8);
  define("FUNC_PERIODOS",   70);
  
  define("FUNC_APP_FRM",    71);
  define("FUNC_ALUMNOS",     4);
  define("FUNC_MAESTROS",   72);
  define("FUNC_GRUPOS",      3);
  define("FUNC_CRITICAS",    6);
  define("FUNC_BLOGS",       5);
  define("FUNC_RESPALDOS",  10);
  define("FUNC_PAGOS",      34);
  define("FUNC_FORO",       81);
  define("FUNC_TAKE",       114);
  define("FUNC_TEACHER_RATE",       122);
  define("FUNC_SHARE_NETWORK",       126);
  
  define("FUNC_REP_INGR",   11);
  define("FUNC_REP_DATA",   12);
  define("FUNC_REP_FECHAS", 13);
  define("FUNC_REP_PCTIA",  14);
  
  define("FUNC_NODOS",      15);
  define("FUNC_NOTICIAS",   16);
  define("FUNC_EVENTOS",    17);
  define("FUNC_LIGAS",      18);
  define("FUNC_FIXED",      68);
  define("FUNC_MENUS",      24);
  define("FUNC_SECCIONES",  25);
  define("FUNC_CONTENIDOS", 26);
  define("FUNC_TEMPLATES",  27);
  define("FUNC_FLUJOS",     28);
  
  define("FUNC_CATEGORIAS", 92);
  define("FUNC_DOC_TEMPLATES", 93);
  
  define("FUNC_VARIABLES",  19);
  define("FUNC_IMAGENES",   21);
  define("FUNC_ETIQUETAS",  29);
  define("FUNC_MENSAJES",   30);
  define("FUNC_CORREOS",    20);
  define("FUNC_PAISES",     69);
  define("FUNC_ZONAS",       9);
  define("FUNC_CRITERIOS",  73);
  define("FUNC_ESCALAS",     7);
  define("FUNC_BREAKS",     97);
  
  define("FUNC_PWD_OTROS",  31);
  define("FUNC_PERFILES",   32);
  define("FUNC_USUARIOS",   33);
  define("FUNC_PWD",        35);
  define("FUNC_GLOBALCALENDAR",177);
  define("FUNC_COURSESCODE",180);
  define("FUNC_CUPON",184);
  define("FUNC_CLASS_TIMES",199);
  
  # Funciones FAME rubrics
  define("FUNC_CRITERIO_FAME", 165);
  
  // ICH 11/10/2016
  // Actualizado 23/12/2016
  # Funciones FAME
  define("FUNC_CLIB_SP",   151);
  define("FUNC_LMED_SP",   152);
  define("FUNC_LICENCES",   145);
  define("FUNC_BILLING",   146);
  define("FUNC_FREE_TRIAL",   149);
  define("FUNC_PARTNER_SCHOOL",   148);
  define("FUNC_STUDENTS_FAME",   150);
  define("FUNC_TEACHERS_FAME",   176);
  define("FUNC_CERTIFICADO_FAME",   147);
  define("FUNC_REFERRAL_STATUS",183);
  define("FUNC_COUPON_B2C",185);

  #Fubcones Gamificacion
  define("FUNC_EVENTS",186);
  define("FUNC_ACTIONES",187);
  define("FUNC_LEVELS",188);
  define("FUNC_ACHIEVEMENTS",189);
  
  
  
  # Variable para usar FLASH o HLS  
  define("VIDEOS_CMD_HLS", SP_HOME."/fame/ffmpeg/ffmpeg");
  # Videos CAMPUS
  define("DATA2", "/var/www/html/vanas");
  define("VIDEOS_CAMPUS", DATA2 . "/vanas_videos/campus");  
  define("VID_CAM_LEC", VIDEOS_CAMPUS . "/lessons");  
  define("VID_CAM_BREF", VIDEOS_CAMPUS . "/brief");  
  define("VID_CAM_NEWS", VIDEOS_CAMPUS . "/news");  
  define("VID_CAM_STU_LIB", VIDEOS_CAMPUS . "/student_library");
  # Videos FAME
  define("VIDEOS_FAME", SP_HOME . "/vanas_videos/fame");    
  define("VID_FAME_STU_LIB", VIDEOS_FAME . "/library");  
  
  
?>