<?php
  
  # Variables para debug
  define("D_DEBUG_ADO", False);
  define("D_BORDES",    0);
  
  # Rutas compartidas del Sitio Publico
  define("SP_HOME",      "");
  define("SP_IMAGES",    SP_HOME . "/images");
  define("SP_THUMBS",    SP_IMAGES . "/thumbs");
  define("SP_FLASH",     SP_HOME . "/swf");
  define("SP_VIDEOS",    SP_HOME . "/videos");
  define("SP_ANEXOS",    SP_HOME . "/attachments");
  define("SP_ANEXOS_EN", SP_ANEXOS . "/eng");
  
  # Rutas para directorios
  define("PATH_CSS", SP_HOME . "/css");
  define("PATH_JS",  SP_HOME . "/js");
  define("PATH_LIB", SP_HOME . "/lib");
  define("PATH_TMP", SP_HOME . "/tmp");
  define("PATH_XML", SP_HOME . "/xml");
  
  # Rutas de los Sitios de Alumnos y Maestros
  define("PATH_CAMPUS",       SP_HOME . "/modules");
  define("PATH_CAMPUS_F",     $_SERVER[DOCUMENT_ROOT].PATH_CAMPUS);
  define("PATH_COM",          PATH_CAMPUS."/common");
  define("PATH_COM_CSS",      PATH_COM . "/css");
  define("PATH_COM_IMAGES",   PATH_COM . "/images");
  define("PATH_COM_JS",       PATH_COM . "/js");
  define("PATH_COM_LIB",      PATH_COM . "/lib");
  define("PATH_ALU",          PATH_CAMPUS . "/students");
  define("PATH_ALU_IMAGES",   PATH_ALU . "/images");
  define("PATH_ALU_IMAGES_F", PATH_CAMPUS_F . "/students/images");
  define("PATH_MAE",          PATH_CAMPUS . "/teachers");
  define("PATH_MAE_IMAGES",   PATH_MAE . "/images");
  define("PATH_MAE_IMAGES_F", PATH_CAMPUS_F . "/teachers/images");

  # Path for new Student and Teacher Campus
  define("PATH_N_COM",          PATH_CAMPUS."/common/new_campus");
  define("PATH_N_COM_F",        $_SERVER['DOCUMENT_ROOT'].PATH_N_COM);
  define("PATH_N_COM_CSS",      PATH_N_COM . "/css");
  define("PATH_N_COM_IMAGES",   PATH_N_COM . "/images");
  define("PATH_N_COM_JS",       PATH_N_COM . "/js");
  define("PATH_N_COM_LIB",      PATH_N_COM . "/lib");
  define("PATH_N_COM_UPLOAD",   PATH_N_COM . "/upload");

  define("PATH_N_ALU",          PATH_CAMPUS . "/students_new");
  define("PATH_N_ALU_PAGES",    PATH_CAMPUS . "/students_new/ajax");
  define("PATH_N_MAE",          PATH_CAMPUS . "/teachers_new");
  define("PATH_N_MAE_PAGES",    PATH_CAMPUS . "/teachers_new/ajax");
  
  # Rutas del Sitio de Administracion
  define("PATH_ADM",        SP_HOME . "/AD3M2SRC4");
  define("PATH_HOME",       "/AD3M2SRC4");
  define("PATH_ADM_CSS",    PATH_ADM . "/css");
  define("PATH_ADM_IMAGES", PATH_ADM . "/images");
  define("PATH_ADM_JS",     PATH_ADM . "/js");
  
  # Rutas del Self pace
  define("PATH_SELF", SP_HOME."/self_pace");
  define("PATH_SELF_PUB", PATH_SELF."/public");
  define("PATH_SELF_LIB", PATH_SELF."/lib");
  
  # Paginas de uso general
  define("PAGINA_INICIO",    SP_HOME."/index.php");
  define("PAGINA_INI_ALU",   PATH_ALU."/desktop.php");
  define("PAGINA_INI_MAE",   PATH_MAE."/blog.php");
  define("PAGINA_INI_ADM",   PATH_ADM."/home.php");
  define("PAGINA_NOD_ALU",   PATH_ALU."/node.php");
  define("PAGINA_NOD_MAE",   PATH_MAE."/node.php");
  define("PAGINA_CON_ALU",   PATH_ALU."/content.php");
  define("PAGINA_CON_MAE",   PATH_MAE."/content.php");
  define("FRM_CONTACTO",     SP_HOME."/contact.php");
  define("PGM_CONTACTO",     SP_HOME."/contact_send.php");
  define("PAGINA_SECCION",   SP_HOME."/section.php");
  define("PAGINA_NODO",      SP_HOME."/node.php");
  define("PAGINA_CONTENIDO", SP_HOME."/content.php");
  define("PAGINA_ERROR",     SP_HOME."/error.php");
  define("PAGINA_OLVIDO",    SP_HOME."/forgot_campus.php");
  define("PAGINA_SALIR",     SP_HOME."/logout.php");
  
  # Variables para manejo de sesion
  define("SESION_INVALIDO",  SP_HOME."/login.php?err=1");
  define("SESION_EXPIRADA",  SP_HOME."/login.php?err=2");
  define("SESION_NO_EXISTE", SP_HOME."/login.php?err=3");
  define("SESION_INACTIVO",  SP_HOME."/login.php?err=4");
  define("SESION_CONTACTE",  SP_HOME."/login.php?err=5");
  define("SESION_EN_USO",    SP_HOME."/login.php?err=6");
  define("CAMPUS_CERRADO",   SP_HOME."/login.php?err=7");
  define("OLVIDO_INVALIDO",  PAGINA_OLVIDO."?msg=1");
  define("OLVIDO_ERR_ENVIO", PAGINA_OLVIDO."?msg=3");
  define("OLVIDO_INACTIVO",  PAGINA_OLVIDO."?msg=4");
  define("OLVIDO_EXITO",     PAGINA_OLVIDO."?msg=5");
  
  # Variables de ambiente
  define("MAIL_SERVER",      ObtenConfiguracion(1));
  define("MAIL_FROM",        ObtenConfiguracion(4));
  define("MAIL_PORT",        ObtenConfiguracion(5));
  define("MAX_LONG_RESUMEN", 400);
  define("MAX_OPC_MENU",     30);
  define("SOCIAL_NETWORKS",  ObtenConfiguracion(78));  
  define("PERFIL_RECRUITER", ObtenConfiguracion(91));
  
  # Identificadores de Menus de sistema (c_modulo.fl_modulo)
  define("MENU_MAESTROS",  2);
  define("MENU_ALUMNOS",   3);
  define("MENU_PUBLICO",   4);
  define("MENU_FOOTER",   25);
  define("MENU_NOTICIAS", 24);
  
  # Agregamos el nuevo Usuario para el Self Pace
  define("MENU_ADMIN_SELF",11);
  define("MENU_MAESTRO_SELF",12);
  define("MENU_ALUMNO_SELF",13);
  
  # Identificadores de contenidos para paginas fijas
  define("PAG_CONTACTO",     1);
  define("PAG_MENU_AZUL",    2);
  define("PAG_CANCELA_PAGO", 3);
  define("PAG_FACULTY",     10);
  
  # Identificadores de secciones
  define("SEC_HOME",     47);
  define("SEC_APPFORM",  58);
  define("SEC_FACULTY",  53);
  define("SEC_CONTACTO", 64);
  
  # Extensiones para funciones
  define("PGM_FORM",   "_frm");
  define("PGM_INSUPD", "_iu");
  
  # Templates de diseno (c_template.cl_template)
  define("TMPL_LIGA",        1);
  define("TMPL_CARATULA",   "20");
  define("TMPL_NOTICIA_00", 20);
  define("TMPL_NOTICIA_01", 21);
  define("TMPL_NODO_01",    31);
  
  # Templates de diseno (c_template.cl_template)
  define("LIB_TMPL_NOTICIA_00", "lib/tmpl_noticia_00.inc.php");
  define("LIB_TMPL_NOTICIA_01", "lib/tmpl_noticia_01.inc.php");
  define("LIB_TMPL_NODO_01",    "lib/tmpl_nodo_01.inc.php");
  
  # Variables para despliegue de imagenes
  define("IMG_ERROR",        ObtenNombreImagen(3));
  define("IMG_INFO",         ObtenNombreImagen(4));
  define("IMG_WARNING",      ObtenNombreImagen(5));
  define("IMG_HELP",         ObtenNombreImagen(6));
  define("IMG_CALENDARIO",   ObtenNombreImagen(12));
  define("IMG_PDF",          ObtenNombreImagen(108));
  define("IMG_AUDIO",        ObtenNombreImagen(109));
  define("IMG_DEFAULT",      ObtenNombreImagen(110));
  define("NEWS_IMG_DEF",     ObtenNombreImagen(111));
  define("NEWS_THUMB_DEF",   ObtenNombreImagen(112));
  define("S_NEWS_IMG_DEF",   ObtenNombreImagen(113));
  define("S_NEWS_THUMB_DEF", ObtenNombreImagen(114));
  define("IMG_T_AVATAR_DEF", ObtenNombreImagen(202));
  define("IMG_S_AVATAR_DEF", ObtenNombreImagen(203));
  
  # Etiquetas generales
  define("ETQ_USUARIO",     ObtenEtiqueta(8));
  define("ETQ_IDIOMA",      ObtenEtiqueta(9));
  define("ETQ_TIT_INFO",    ObtenEtiqueta(20));
  define("ETQ_TIT_WARN",    ObtenEtiqueta(21));
  define("ETQ_TIT_ERROR",   ObtenEtiqueta(22));
  define("ETQ_TIT_CONFIRM", ObtenEtiqueta(23));
  define("ETQ_REGRESAR",    ObtenEtiqueta(24));
  define("ETQ_FMT_FECHA",   ObtenEtiqueta(35));
  define("ETQ_DIAS_SEMANA", ObtenEtiqueta(36));
  define("ETQ_DIAS_CORTO",  ObtenEtiqueta(37));
  define("ETQ_MESES",       ObtenEtiqueta(38));
  define("ETQ_MESES_CORTO", ObtenEtiqueta(39));
  define("ETQ_ANTERIOR",    ObtenEtiqueta(40));
  define("ETQ_SIGUIENTE",   ObtenEtiqueta(41));
  define("ETQ_TITULO",      ObtenEtiqueta(52));
  define("ETQ_HOME",        ObtenEtiqueta(53));
  define("ETQ_BUSQUEDA",    ObtenEtiqueta(54));
  define("ETQ_CONTACTO",    ObtenEtiqueta(55));
  define("ETQ_TIT_PAG",     ObtenEtiqueta(66));
  
  # Identificadores de sitios
  define("FUNC_ALUMNOS",  1);
  define("FUNC_MAESTROS", 2);
  
  # Variables usadas en la herramienta para RECORD CRITIQUE
  define("DIRECTORIO_JAR", "/recordCritiqueJar");  
  define("REMOTE_SERVER_NAME", "campus.vanas.ca");
  
?>
