<?php

#
# MRA: Funciones generales de despliegue para el Sitio Publico
#

# Carga Videos FLV
function PresentaVideo($p_archivo, $p_width, $p_height) {
  
  echo "
                        <script type='text/javascript'>
                          var flashvars = {};
                          // video width
                          var videoWidth = $p_width;
                          // video height
                          var videoHeight = $p_height;
                          // Main Video Path
                          flashvars.videoFilePath = '".SP_VIDEOS."/".$p_archivo."';
                          // Video buffer time (seconds) 
                          flashvars.videoBufferTime = '5'
                          // automatically start video playing when first start video player. (yes/no)   
                          flashvars.autoStartVideoPlay = 'no';
                          // Auto Repeat at end of the video. (yes/no)
                          flashvars.autoRepeat = 'no';
                          // Video starting volume (Max: 100 Min: 0) 
                          flashvars.videoStartVolume= '75';
                          // Show Advertisement video (yes/no)
                          flashvars.showAdvertisementVideo = 'no';
                          // Advertisement Video Path
                          flashvars.advertisementVideoPath = '".SP_VIDEOS."';
                          // Video Title Text 
                          flashvars.titleTxt = \"<font color='#9999FF' size='15'>Vancouver Animation School</font>\";
                          // Video Description Text
                          flashvars.descriptionTxt = '';
                          // Show Logo 
                          flashvars.logoDisplay = 'no';
                          // Logo Image Path 
                          flashvars.logoImagePath = '".SP_IMAGES."/logo.jpg';
                          // Logo Position 
                          flashvars.logoPlacePosition = 'top-left';
                          // Logo Margin Space 
                          flashvars.logoMargin = '20';
                          // Logo Width 
                          flashvars.logoWidth = '86';
                          // Logo Height
                          flashvars.logoHeight = '38';
                          // Logo Transparency Value (100 : Solid  50 : Semi Transparency) 
                          flashvars.logoTransparency = '60';
                          // Define video bar color (blue, green, orange, purple, white, red) 
                          // random : it will select color randomly  
                          flashvars.videoBarColorName = 'white';
                          // Define volume bar color (blue, green, orange, purple, white, red) 
                          // random : it will select color randomly 
                          flashvars.volumeBarColorName = 'white';
                          // Show Cover Image
                          flashvars.coverImageDisplay = 'yes';
                          // cover image path. You can use SWF, PNG, JPG or GIF
                          flashvars.coverImagePath = '".SP_IMAGES."/PosterFrame_White.jpg';
                          // Auto Hide Control Panel and Mouse 
                          flashvars.hideControlPanelAndMouse='yes'
                          // Auto Hide time (second) 
                          flashvars.hideTime='1'
                          var params = {};
                            params.scale = 'exactfit';
                            params.allowfullscreen = 'true';
                            params.salign = 't';
                            params.bgcolor = '000000';
                            params.wmode = 'opaque';
                            
                          var attributes = {};
                          swfobject.embedSWF('".SP_FLASH."/video_player8_flashvars.swf', 'myContent', videoWidth, videoHeight, '9.0.0', false, flashvars, params, attributes);
                        </script>
                        <div id='myContent'>
                          <h1>Alternative content</h1>
                          <p><a href='http://www.adobe.com/go/getflashplayer'><img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a></p>
                        </div>";
}

# Carga videos en flash con streaming
function PresentaVideoJWP($p_file) {
  
  $file = ObtenNombreArchivo($p_file);
  $streamer = "rtmp://".ObtenConfiguracion(60)."/oflaDemo";
  $image = SP_IMAGES."/PosterFrame_White.jpg";
  $width = ObtenConfiguracion(13);
  $height = ObtenConfiguracion(14) + 25;
  $bufferTime = ObtenConfiguracion(56);
  echo "
  <object id='player' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' name='player' width='$width' height='$height'>
    <param name='movie' value='".SP_FLASH."/player.swf' />
    <param name='allowfullscreen' value='true' />
    <param name='allowscriptaccess' value='always' />
    <param name='wmode' value='opaque' />
    <param name='flashvars' value='file=$file&streamer=$streamer&image=$image&bufferlength=$bufferTime&smoothing=false' />
    <embed
      type='application/x-shockwave-flash'
      id='player2'
      name='player2'
      src='".SP_FLASH."/player.swf' 
      width='$width' 
      height='$height'
      allowscriptaccess='always' 
      allowfullscreen='true'
      wmode='opaque'
      flashvars='file=$file&streamer=$streamer&image=$image&bufferlength=$bufferTime&smoothing=false' 
    />
  </object>";
}

# Stream HD videos through flowplayer
function PresentaVideoFP($p_file) {

  $file = ObtenNombreArchivo($p_file);
  $streamer = "rtmp://".ObtenConfiguracion(60)."/oflaDemo";
  $streamer_plugin = SP_FLASH."/flowplayer.rtmp-3.2.13.swf";
  $player = SP_FLASH."/flowplayer.commercial-3.2.18.swf";
  $image = SP_IMAGES."/PosterFrame_PlayIcon.jpg";

  echo "
  <div id='preview-video'><img src='$image'></div>
  <script type='text/javascript'>
    (function(){
      var script = document.createElement('script'); 
      script.type = 'text/javascript';
      script.src = '".PATH_N_COM_JS."/plugin/flowplayer_flash/flowplayer-3.2.13.min.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(script, s);

      var link = document.createElement('link');
      link.href = '".PATH_N_COM_CSS."/flowplayer/playful.css';
      link.type = 'text/css';
      link.rel = 'stylesheet';
      var l = document.getElementsByTagName('link')[0];
      l.parentNode.insertBefore(link, l);
    })();
    
    window.onload = function(){
      // Default 16:9 ratio
      flashembed.conf.width = '720';
      flashembed.conf.height = '405';

      // Create flowplayer
      flowplayer('preview-video', '$player', {
        key: '#$79d60288437ade68168',

        clip: {
          url: 'mp4:$file',
          scaling: 'fit',
          // configure clip to use hddn as our provider, referring to the rtmp plugin
          provider: 'hddn'
        },

        // streaming plugins
        plugins: {
          // controller bar
          controls:  {
            backgroundGradient: 'none',
            backgroundColor: 'transparent',
            timeColor: '#FFFFFF',
            timeSeparator: ' / ',
            durationColor: '#FFFFFF',
            timeBgColor: 'rgb(0,0,0, 0.2)',
            scrubber:true,
            height:45
          },
          // rtmp plugin configuration
          hddn: {
            url: '$streamer_plugin',
            
            // define where the streams are found
            netConnectionUrl: '$streamer'
          },
        },

        canvas: {
          backgroundGradient: 'none'
        },

        // Don't show error messages on player, handle the errors ourselves
        showErrors: false,

        // When error occurs
        onError: function(errorCode, errorMessage){
          // Stream not found
          if(errorCode === 200){
            // Switch back to flv
            this.setClip({
              url: '$file',
              scaling: 'fit',
              provider: 'hddn'
            });
            this.play();
          }
        }
      });
    };
  </script>";
}

# Carga archivos de Flash
function PresentaFlash($p_archivo, $p_width, $p_height, $p_param="") {
  
  $archivo = ObtenNombreArchivo($p_archivo);
  if(!empty($p_param))
    $archivo .= "?".$p_param;
  echo "<script type='text/javascript'>
AC_FL_RunContent('codebase',    'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0',
                 'width',       '$p_width',
                 'height',      '$p_height',
                 'src',         '".SP_FLASH."/$archivo',
                 'quality',     'high',
                 'pluginspage', 'http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash',
                 'movie',       '".SP_FLASH."/$archivo');
  </script>";
}


# Inicio de pagina
function PresentaInicioPagina($p_jQuery=False, $p_Login=True, $p_valida=True) {
  
  # Revisa si el sitio esta disponible
  if($p_valida) {
    $fg_sitio_disponible = ObtenConfiguracion(31);
    if($fg_sitio_disponible <> "1") {
      header("Location: ".SP_HOME."/service.html");
      exit;
    }
  }
  
  # Recibe parametros
  $err = RecibeParametroNumerico('err', True);
  
  echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='cache-control' content='max-age=0' />
<meta http-equiv='cache-control' content='no-cache' />
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<meta name='robots' content='noindex'>
<meta name='googlebot' content='noindex'>
<title>".ETQ_TITULO."</title>\n";
  if($p_jQuery) {
    echo "
<link type='text/css' href='".PATH_ADM_CSS."/theme/jquery-ui-1.8rc3.custom.css' rel='stylesheet'>
<script type='text/javascript' src='".PATH_ADM_JS."/jquery-1.4.2.min.js'></script>
<script type='text/javascript' src='".PATH_ADM_JS."/jquery-ui-1.8rc3.custom.min.js'></script>
<script type='text/javascript' src='".PATH_COM_JS."/frmStreamingVideo.js.php'></script>";
  }
  echo "
<script type='text/javascript' src='".PATH_JS."/CargaImagenes.js'></script>
<script type='text/javascript' src='".PATH_JS."/AC_RunActiveContent.js'></script>
<script type='text/javascript' src='".PATH_JS."/swfobject.js'></script>
<link href='".PATH_CSS."/vanas.css' rel='stylesheet' type='text/css' />
<link rel='shortcut icon' href='".SP_IMAGES."/favicon.ico'>

<script type='text/javascript'>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20837828-1']);
  _gaq.push(['_setDomainName', '.vanas.ca']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<script type='text/javascript'>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-27662999-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0' class='site_background'>
<div id='dlg_video'><div id='dlg_video_content'></div></div>

<script type='text/javascript'>
var sc_project=6551997; 
var sc_invisible=1; 
var sc_security='1194400a'; 
</script>
<script type='text/javascript' src='http://www.statcounter.com/counter/counter.js'></script>
<noscript><div class='statcounter'><a title='vBulletin stat' href='http://statcounter.com/vbulletin/' target='_blank'><img class='statcounter'
src='http://c.statcounter.com/6551997/0/1194400a/1/' alt='vBulletin stat' ></a></div></noscript>
";
  if($p_Login) {
    echo "
<form name='login_frm' method='post' action='login_validate.php'>
<table border='".D_BORDES."' cellpadding='0' cellspacing='0' align='center' width='1024'>
  <tr>
    <td width='31' class='outline_left'></td>
    <td height='30' align='right' class='outline_top'>";
    
    # Presenta mensajes de error
    if(!empty($err)) {
      echo "<span class='login_err'>";
      switch($err) {
        case 1: echo "Invalid username or password..."; break;
        case 2: echo "Session expired, please login again."; break;
        case 3: echo "Session does not exist, please enter user and password."; break;
        case 4: echo "Your user account is inactive."; break;
        case 5: echo "Access denied, please contact Vanas Administrator."; break;
        case 6: echo "You already have an active session! Please wait for session timeout."; break;
        case 7: echo "Online Campus closed for maintenance. Please try again later."; break;
      }
      echo "</span>&nbsp;&nbsp;";
    }
    echo "
      <span class='login'>
        ".ETQ_USUARIO.": <input type='text' name='ds_login' id='ds_login' size='15' maxlength='16'>
        &nbsp;
        ".ObtenEtiqueta(123).": <input type='password' name='ds_password' id='ds_password' size='15' maxlength='16' autocomplete='off' onKeyPress='return submitenter(this,event)'>
        <input type='button' value='".ObtenEtiqueta(76)."' onclick='validaForma(this.form);'>
        &nbsp;
        <a href='".PAGINA_OLVIDO."'>".ObtenEtiqueta(75)."</a>
      </span>
    </td>
    <td width='32' class='outline_right'></td>
  </tr>
</table>
<script type='text/javascript'>
  
  function submitenter(myfield, e) {
    var keycode;
    if(window.event)
      keycode = window.event.keyCode;
    else if(e)
      keycode = e.which;
    else
      return true;
  
  if(keycode == 13) {
    validaForma(myfield.form);
    return false;
  }
  else
    return true;
  }
  
  var BrowserDetect = {
    init: function () {
      this.browser = this.searchString(this.dataBrowser) || \"An unknown browser\";
      this.version = this.searchVersion(navigator.userAgent)
        || this.searchVersion(navigator.appVersion)
        || \"an unknown version\";
      this.OS = this.searchString(this.dataOS) || \"an unknown OS\";
    },
    searchString: function (data) {
      for (var i=0;i<data.length;i++) {
        var dataString = data[i].string;
        var dataProp = data[i].prop;
        this.versionSearchString = data[i].versionSearch || data[i].identity;
        if (dataString) {
          if (dataString.indexOf(data[i].subString) != -1)
            return data[i].identity;
        }
        else if (dataProp)
          return data[i].identity;
      }
    },
    searchVersion: function (dataString) {
      var index = dataString.indexOf(this.versionSearchString);
      if (index == -1) return;
      return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
    },
    dataBrowser: [
      {
        string: navigator.userAgent,
        subString: \"Chrome\",
        identity: \"Chrome\"
      },
      {   string: navigator.userAgent,
        subString: \"OmniWeb\",
        versionSearch: \"OmniWeb/\",
        identity: \"OmniWeb\"
      },
      {
        string: navigator.vendor,
        subString: \"Apple\",
        identity: \"Safari\",
        versionSearch: \"Version\"
      },
      {
        prop: window.opera,
        identity: \"Opera\"
      },
      {
        string: navigator.vendor,
        subString: \"iCab\",
        identity: \"iCab\"
      },
      {
        string: navigator.vendor,
        subString: \"KDE\",
        identity: \"Konqueror\"
      },
      {
        string: navigator.userAgent,
        subString: \"Firefox\",
        identity: \"Firefox\"
      },
      {
        string: navigator.vendor,
        subString: \"Camino\",
        identity: \"Camino\"
      },
      {   // for newer Netscapes (6+)
        string: navigator.userAgent,
        subString: \"Netscape\",
        identity: \"Netscape\"
      },
      {
        string: navigator.userAgent,
        subString: \"MSIE\",
        identity: \"Explorer\",
        versionSearch: \"MSIE\"
      },
      {
        string: navigator.userAgent,
        subString: \"Gecko\",
        identity: \"Mozilla\",
        versionSearch: \"rv\"
      },
      {     // for older Netscapes (4-)
        string: navigator.userAgent,
        subString: \"Mozilla\",
        identity: \"Netscape\",
        versionSearch: \"Mozilla\"
      }
    ],
    dataOS : [
      {
        string: navigator.platform,
        subString: \"Win\",
        identity: \"Windows\"
      },
      {
        string: navigator.platform,
        subString: \"Mac\",
        identity: \"Mac\"
      },
      {
           string: navigator.userAgent,
           subString: \"iPhone\",
           identity: \"iPhone/iPod\"
        },
      {
        string: navigator.platform,
        subString: \"Linux\",
        identity: \"Linux\"
      }
    ]

  };
  BrowserDetect.init();
  
  function validaForma(forma) 
  {
    if(BrowserDetect.browser!=\"Firefox\") 
      {
        alert('Access is allowed only from Firefox');
        return;
      }
    else
      document.login_frm.submit();
  }
  
</script>
</form>";
  }
}


# Primera parte para todas las paginas hasta el inicio del cuerpo
function PresentaHeader($p_nodo=0, $p_jQuery=False) {
  
  PresentaInicioPagina($p_jQuery);
  echo "
<table border='".D_BORDES."' cellpadding='0' cellspacing='0' align='center' width='1024'>
  <tr>
    <td width='31' class='outline_left'></td>
    <td valign='top' class='announcement_bottom'>
      <table border='".D_BORDES."' cellpadding='0' cellspacing='0' >
        <tr>
          <td width='241' height='".(ObtenConfiguracion(14)+38)."' valign='top' class='menu'>
            &nbsp;\n";
  PresentaMenu($p_nodo);
  echo "
          </td>
        </tr>
        <tr>
          <td width='241' height='290' valign='top' class='newsletter_back'>";
  PresentaBoletin( );
  echo "</td>
        </tr>
      </table>
    </td>
    <td valign='top' class='content'>
      <table border='".D_BORDES."' cellpadding='0' cellspacing='0'>
        <tr>
          <td width='360' height='38' class='announcement_top'>&nbsp;</td>
          <td width='360' class='title'>".ETQ_TIT_PAG."</td>
        </tr>
        <tr>
          <td colspan='2' height='734' valign='top' class='content'>\n";
}


# Menu principal del Sitio Publico
function PresentaMenu($p_nodo=0) {
  
  # Recupera el numero de opcion de la seccion del nodo
  $actual = 0;
  if(!empty($p_nodo)) {
    $row = RecuperaValor("SELECT fl_modulo FROM c_funcion WHERE fl_funcion=$p_nodo");
    $fl_modulo = $row[0];
    if(!empty($fl_modulo)) {
      $row = RecuperaValor("SELECT fl_modulo_padre, no_orden FROM c_modulo WHERE fl_modulo=$fl_modulo");
      $fl_modulo_padre = $row[0];
      $no_orden_anterior = $row[1];
      while(!empty($fl_modulo_padre)) {
        $fl_menu = $fl_modulo_padre;
        $no_orden = $no_orden_anterior;
        $row = RecuperaValor("SELECT fl_modulo_padre, no_orden FROM c_modulo WHERE fl_modulo=$fl_modulo_padre");
        $fl_modulo_padre = $row[0];
        $no_orden_anterior = $row[1];
      }
      if($fl_menu == MENU_PUBLICO) {
        if(!empty($no_orden)) {
          $Query  = "SELECT count(1) ";
          $Query .= "FROM c_modulo ";
          $Query .= "WHERE fl_modulo_padre=".MENU_PUBLICO." ";
          $Query .= "AND fg_menu=1 ";
          $Query .= "AND no_orden<=$no_orden";
          $row = RecuperaValor($Query);
          $actual = $row[0];
        }
      }
    }
  }
  
  echo "
    <ul class='links_menu'>";
  
  # Recupera las opciones del menu principal del Sitio Publico
  $Query  = "SELECT fl_modulo, nb_modulo, tr_modulo ";
  $Query .= "FROM c_modulo ";
  $Query .= "WHERE fl_modulo_padre=".MENU_PUBLICO." ";
  $Query .= "AND fg_menu='1' ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  $tot = CuentaRegistros($rs);
  if($tot > MAX_OPC_MENU)
    $tot = MAX_OPC_MENU;
  if($actual > $tot)
    $actual = 0;
  for($i = 1; $i <= $tot; $i++) {
    $row = RecuperaRegistro($rs);
    if($i == $actual)
      $fg_actual = 1;
    else
      $fg_actual = 0;
    $fl_menu = $row[0];
    $titulo = str_ascii(EscogeIdioma($row[1], $row[2]));
    $link = PAGINA_SECCION."?seccion=$fl_menu";
    if($fg_actual == 1) {
      
      # Recupera opciones del submenu
      $Query  = "SELECT fl_funcion, nb_funcion, tr_funcion, fg_multiple, fg_tipo_orden ";
      $Query .= "FROM c_funcion ";
      $Query .= "WHERE fl_modulo=$fl_menu ";
      $Query .= "AND fg_menu='1' ";
      $Query .= "AND fg_tipo_seguridad='X' ";
      $Query .= "ORDER BY no_orden";
      $rs2 = EjecutaQuery($Query);
      $tot2 = CuentaRegistros($rs2);
      if($tot2 > 1) {
        $fg_submenu = False;
        $submenu = "
        <ul class='links_submenu'>";
        while($row2 = RecuperaRegistro($rs2)) {
          $fl_funcion = $row2[0];
          $opcion = EscogeIdioma($row2[1], $row2[2]);
          $fg_multiple = $row2[3];
          $fg_tipo_orden = $row2[4];
          if($fl_funcion == $p_nodo) {
            $submenu .= "
          <li><a href='".PAGINA_NODO."?nodo=$fl_funcion' class='links_submenu_act'>$opcion</a></li>";
            $fg_submenu = True;
          }
          else
            $submenu .= "
          <li><a href='".PAGINA_NODO."?nodo=$fl_funcion' class='links_submenu'>$opcion</a></li>";
        }
        $submenu .= "
        </ul>";
        if($fg_submenu)
          echo "
      <li><a href='$link' class='links_menu'>$titulo</a></li>";
        else
          echo "
      <li><a href='$link' class='links_menu_act'>$titulo</a></li>";
        echo $submenu;
      }
      else
        echo "
      <li><a href='$link' class='links_menu_act'>$titulo</a></li>";
    }
    else
      echo "
      <li><a href='$link' class='links_menu'>$titulo</a></li>";
  }
  echo "
    </ul>\n";
}


# Presenta barra de Noticias
function PresentaNoticias( ) {
  
  # Recupera la seccion de noticias
  $Query  = "SELECT fl_funcion, fg_tipo_orden, cl_tipo_contenido ";
  $Query .= "FROM c_funcion ";
  $Query .= "WHERE fl_modulo=".MENU_NOTICIAS." ";
  $Query .= "AND fg_tipo_seguridad='X' ";
  $Query .= "ORDER BY no_orden";
  $row = RecuperaValor($Query);
  $fl_funcion = $row[0];
  $fg_tipo_orden = $row[1];
  $cl_tipo_contenido = $row[2];
  
  # Revisa si es una liga a otra seccion
  if($cl_tipo_contenido == TC_LIGA) {
    $Query  = "SELECT fl_funcion, fg_tipo_orden ";
    $Query .= "FROM c_funcion ";
    $Query .= "WHERE fl_funcion=(";
    $Query .= "SELECT fl_seccion ";
    $Query .= "FROM c_contenido a, k_liga b ";
    $Query .= "WHERE a.fl_contenido=b.fl_contenido ";
    $Query .= "AND a.fl_funcion=$fl_funcion) ";
    $Query .= "AND fg_tipo_seguridad='X' ";
    $Query .= "ORDER BY no_orden";
    $row = RecuperaValor($Query);
    $fl_funcion = $row[0];
    $fg_tipo_orden = $row[1];
  }
  
  # Recupera la primer noticia
  $Query  = "SELECT fl_contenido, nb_titulo, tr_titulo, ds_resumen, tr_resumen ";
  $Query .= "FROM c_contenido ";
  $Query .= "WHERE fl_funcion=$fl_funcion ";
  $Query .= "AND cl_template NOT IN(".TMPL_CARATULA.") ";
  $Query .= "AND fg_activo=1 ";
  $Query .= "AND (fe_ini IS NULL OR fe_ini <= CURRENT_TIMESTAMP) ";
  $Query .= "AND (fe_fin IS NULL OR DATE_ADD(fe_fin, INTERVAL 1 DAY) >= CURRENT_TIMESTAMP) ";
  $Query .= "ORDER BY ";
  switch($fg_tipo_orden) {
    case 'A': $Query .= "fe_evento"; break;      // Fecha ascendente
    case 'D': $Query .= "fe_evento DESC"; break; // Fecha descendente
    case 'T': $Query .= "nb_titulo"; break;      // Titulo
    default: $Query .= "no_orden";               // Numero de orden
  }
  $rs = EjecutaQuery($Query);
  $row = RecuperaRegistro($rs);
  $tit_not = str_uso_normal(EscogeIdioma($row[1], $row[2]));
  $res_not = str_uso_normal(EscogeIdioma($row[3], $row[4]));
  $Query  = "SELECT nb_archivo, tr_archivo ";
  $Query .= "FROM k_imagen_dinamica ";
  $Query .= "WHERE fl_contenido=$row[0] ";
  $Query .= "AND no_orden=2";
  $row2 = RecuperaValor($Query);
  $nb_archivo_n = str_uso_normal(EscogeIdioma($row2[0], $row2[1]));
  $ds_liga_n = PAGINA_CONTENIDO."?contenido=$row[0]";
  
  # Presenta la noticia mas reciente
  echo "<table width='100%' border='".D_BORDES."' cellpadding='0' cellspacing='0'>
      <tr><td height ='20' colspan='3'>&nbsp;</td></tr>
      <tr>
        <td width='15' height='40'>&nbsp;</td>
        <td valign='middle'><a href='$ds_liga_n'><b>$tit_not</b></a></td>
        <td width='15'>&nbsp;</td>
      </tr>
      <tr>
        <td width='15' height='310'>&nbsp;</td>
        <td valign='top'>
          <table border='".D_BORDES."' cellspacing='0' cellpadding='0'>
            <tr>
              <td height='200'>";
  if(!empty($nb_archivo_n))
    echo "<a href='$ds_liga_n'><img src='".SP_THUMBS."/$nb_archivo_n' width='".ObtenConfiguracion(15)."' border='0' /></a>";
  else
    echo "<a href='$ds_liga_n'><img src='".SP_IMAGES."/".NEWS_IMG_DEF."' width='".ObtenConfiguracion(15)."' height='".ObtenConfiguracion(16)."' border='0' /></a>";
  echo "</td>
              <td width='20'>&nbsp;</td>
              <td valign='middle'>
              $res_not
              <br>
              <br>
              <a href='$ds_liga_n' class='links_news'>".ObtenEtiqueta(68)."</a>
              </td>
            </tr>
          </table>
        </td>
        <td width='15'>&nbsp;</td>
      </tr>
      <tr>
        <td width='15' height='20'>&nbsp;</td>
        <td align='center'><a href='".PAGINA_SECCION."?seccion=".MENU_NOTICIAS."' class='links_news'>".ObtenEtiqueta(67)."</a></td>
        <td width='15'>&nbsp;</td>
      </tr>
    </table>";
}


# Presenta menu azul
function PresentaBoletin( ) {
  
  # Recupera los datos del contenido
  $Query  = "SELECT ds_contenido, tr_contenido ";
  $Query .= "FROM c_pagina ";
  $Query .= "WHERE cl_pagina=".PAG_MENU_AZUL;
  $row = RecuperaValor($Query);
  $contenido = str_uso_normal(EscogeIdioma($row[0], $row[1]));
  
  echo "<table width='100%' border='".D_BORDES."' cellpadding='0' cellspacing='0' class='newsletter'>
    <tr>
      <td width='5'>&nbsp;</td>
      <td>&nbsp;</td>
      <td width='5'>&nbsp;</td>
    </tr>
  <tr>
      <td>&nbsp;</td>
      <td><div align='center'>";
  
  # Codigo para inscripcion en Newsletter
  include "newsletter.html";
  
  echo "</div></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>$contenido</td>
      <td>&nbsp;</td>
    </tr>
  </table>";
}


# Tablas
function PresentaTabla($p_tabla, $p_lin_sup=True, $p_lin_inf=True) {
  
  # Verifica que exista la tabla y recupera su ancho
  $row = RecuperaValor("SELECT fl_tabla, no_width, ds_caption, tr_caption FROM c_tabla WHERE fl_tabla=$p_tabla");
  $fl_tabla = $row[0];
  $no_width_t = $row[1];
  $ds_caption = EscogeIdioma($row[2], $row[3]);
  if(empty($fl_tabla))
    return;
  if(empty($no_width_t))
    $no_width_t = "100%";
  echo "
                <tr>
                  <td>&nbsp;</td>
                  <td>";
  if($p_lin_sup)
    echo "<hr />"; 
  echo "
                    <table width='$no_width_t' border='".D_BORDES."' cellpadding='4' cellspacing='2' bgcolor='#FFFFFF'>
                    <tr>";
  
  # Recupera los encabezados de las columnas
  $Query  = "SELECT fl_columna, nb_columna, tr_columna, fg_align, no_width ";
  $Query .= "FROM k_columna_tabla ";
  $Query .= "WHERE fl_tabla=$fl_tabla ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  $tot_rengs = 0;
  for($j = 0; $row = RecuperaRegistro($rs); $j++) {
    $fl_columna = $row[0];
    $nb_columna = EscogeIdioma($row[1], $row[2]);
    $align[$j] = "left";
    if($row[3] == 'C')
      $align[$j] = "center";
    if($row[3] == 'R')
      $align[$j] = "right";
    $width = "";
    if($row[4] <> "")
      $width = " width='$row[4]'";
    $Query  = "SELECT ds_celda, tr_celda, ds_href ";
    $Query .= "FROM k_celda_tabla ";
    $Query .= "WHERE fl_columna=$fl_columna ";
    $Query .= "ORDER BY no_renglon";
    $rs2 = EjecutaQuery($Query);
    for($i = 0; $row2 = RecuperaRegistro($rs2); $i++) {
      if($row2[2] <> "")
        $ds_celda[$i][$j] = "<a href='$row2[2]'>".EscogeIdioma($row2[0], $row2[1])."</a>";
      else
        $ds_celda[$i][$j] = EscogeIdioma($row2[0], $row2[1]);
    }
    if($i > $tot_rengs)
      $tot_rengs = $i;
    echo "
                      <td class='titTabla' align='$align[$j]'$width>$nb_columna</td>";
  }
  $tot_cols = $j;
  echo "
                    </tr>";
  
  # Presenta los renglones de la tabla
  for($i = 0; $i < $tot_rengs; $i++) {
    if($i%2 == 0)
      $color = "#F6F6F6";
    else
      $color = "#EFEFEF";
    echo "
                    <tr>";
    for($j = 0; $j < $tot_cols; $j++) {
      echo "
                      <td bgcolor='$color' align='$align[$j]'>".$ds_celda[$i][$j]."</td>";
    }
    echo "
                    </tr>";
  }
  echo "
                  </table>";
  if($ds_caption <> "")
    echo "<span class='small'><br />$ds_caption</span>";
  if($p_lin_inf)
    echo "<hr />"; 
  echo "
                  </td>
                </tr>";
}


function PresentaAnexos($p_contenido, $p_separador=True) {
  
  # Recupera los archivos anexos asociados al contenido
  $Query  = "SELECT ds_caption, tr_caption, nb_archivo, tr_archivo, ds_texto, tr_texto, nb_imagen ";
  $Query .= "FROM k_anexo ";
  $Query .= "WHERE fl_contenido=$p_contenido ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  
  echo "
                <tr>
                  <td>&nbsp;</td>
                  <td><table width='735' border='".D_BORDES."' cellspacing='0' cellpadding='0'>";
  $cl_idioma = ObtenIdioma( );
  for($i = 0; $row = RecuperaRegistro($rs); $i++) {
    $ds_caption = str_uso_normal(EscogeIdioma($row[0], $row[1]));
    $nb_archivo = str_uso_normal(EscogeIdioma($row[2], $row[3]));
    if($cl_idioma == INGLES && !empty($row[3]))
      $ruta = SP_ANEXOS_EN;
    else
      $ruta = SP_ANEXOS;
    $ext_archivo = ObtenExtensionArchivo($nb_archivo);
    $ds_texto = str_uso_normal(EscogeIdioma($row[4], $row[5]));
    $nb_imagen = str_uso_normal($row[6]);
    if($i%2 == 0)
      $color = "#F6F6F6";
    else
      $color = "#EFEFEF";
    echo "
                    <tr>
                      <td><hr /></td>
                    </tr>
                    <tr>
                      <td bgcolor='$color'><table width='735' border='".D_BORDES."' cellspacing='0' cellpadding='0'>
                          <tr>
                            <td width='5' align='center' valign='top'>";
    if(empty($nb_imagen)) {
      switch (strtoupper($ext_archivo)) {
        case "PDF": echo "<img src='".SP_IMAGES."/".IMG_PDF."' />"; break;
        case "MP3": echo "<img src='".SP_IMAGES."/".IMG_AUDIO."' />"; break;
        default: echo "&nbsp;";
      }
    }
    else
      echo "<img src='".SP_THUMBS."/$nb_imagen' />";
    echo "</td>
                            <td width='10'>&nbsp;</td>
                            <td align='left'>";
    if($nb_archivo <> "")
      echo "<a href='$ruta/$nb_archivo' target='_blank'>$ds_caption</a>";
    else
      echo "$ds_caption";
    if($ds_texto <> "")
      echo "<br />$ds_texto";
    echo "</td>
                          </tr>
                      </table></td>
                    </tr>";
  }
  if($p_separador)
    echo "
                    <tr>
                      <td><hr /></td>
                    </tr>";
  echo "
                  </table></td>
                </tr>";
}


# Menu Inferior
function PresentaMenuInferior( ) {
  
  # Inicia tabla para menu inferior
  echo "
    <table border='".D_BORDES."' cellpadding='0' cellspacing='0' height='32' align='center'>
        <tr>";
      
  # Recupera las opciones del menu principal del Sitio Publico
  $Query  = "SELECT fl_modulo, nb_modulo, tr_modulo ";
  $Query .= "FROM c_modulo ";
  $Query .= "WHERE fl_modulo_padre=".MENU_PUBLICO." ";
  $Query .= "AND fg_menu=1 ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  $tot = CuentaRegistros($rs);
  if($tot > MAX_OPC_MENU)
    $tot = MAX_OPC_MENU;
  for($i = 1; $i <= $tot; $i++) {
    $row = RecuperaRegistro($rs);
    $fl_menu = $row[0];
    $titulo = str_ascii(EscogeIdioma($row[1], $row[2]));
    $link = PAGINA_SECCION."?seccion=$fl_menu";
    echo "
          <td><a href='$link' class='footer_menu'>$titulo</a></td>";
  }
  
  # Termina tabla para menu inferior
  echo "
        </tr>
      </table>";
}


# Muestra las opciones del footer
function PresentaLigasFooter( ) {
  
  echo "
  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' height='20' align='center'>
        <tr>
          <td>";
  $Query  = "SELECT fl_funcion, nb_funcion, tr_funcion ";
  $Query .= "FROM c_funcion ";
  $Query .= "WHERE fl_modulo=".MENU_FOOTER." ";
  $Query .= "AND fg_menu=1 ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  for($i = 0; $row = RecuperaRegistro($rs); $i++) {
    if($i > 0)
      echo " | ";
    echo "<a href='".PAGINA_NODO."?nodo=$row[0]' class='bottom_links'>".EscogeIdioma($row[1], $row[2])."</a>";
  }
  echo "</td>
        </tr>
      </table>";
}


# Termina el cuerpo y cierra la pagina
function PresentaFooter( ) {
  
  echo "</td>
        </tr>
      </table>
    </td>
    <td width='32' class='outline_right'></td>
  </tr>
</table>
<table border='".D_BORDES."' cellpadding='0' cellspacing='0' align='center' width='1024'>
  <tr>
    <td width='31' class='outline_left'></td>
    <td width='961' height='32' align='center' class='footer_bar'>\n";
  PresentaMenuInferior( );
  echo "
    </td>
    <td width='32' class='outline_right'></td>
  </tr>
  <tr>
    <td colspan='4' height='30' align='center' class='outline_bottom'>\n";
  PresentaLigasFooter( );
  echo "
    </td>
  </tr>
</table>
</body>
</html>";
}

?>