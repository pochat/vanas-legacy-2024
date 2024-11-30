<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ObtenUsuario(False);
  
  # Recibe parametros generales
  $category = RecibeParametroHTML('category');
  $letter = RecibeParametroHTML('letter');
  $program = RecibeParametroNumerico('program');
  $country = RecibeParametroNumerico('country');
  
  # Inicializa variables de la forma
  echo "
  <div id='dlg_message'>
    Message to:<b><div id='msg_to'></div></b><br>
    <textarea name='ds_mensaje' id='ds_mensaje' cols=65 rows=4></textarea>
    <input type='hidden' name='fl_usuario_ori' id='fl_usuario_ori' value='$fl_usuario'>
    <input type='hidden' name='fl_usuario_dest' id='fl_usuario_dest'>
  </div>
  <script type='text/javascript'>
    $('#category').val('$category');
    $('#letter').val('$letter');
    $('#program').val('$program');
    $('#country').val('$country');
    
    $(function() {
      
      // Dialogo para enviar mensajes
      function checkLength(o) {
        if(o.val().length > 0)
          return true;
        else
          return false;
			}
		  
      $('#dlg_message').dialog({
        autoOpen: false,
        resizable: false,
        width: 400,
        height: 200,
        hide: 'highlight',
        buttons: {
          'Cancel': function() {
            $(this).dialog('close');
          },
          'Send': function() {
            var ds_mensaje = $('#ds_mensaje');
            bValid = checkLength(ds_mensaje);
            if(bValid) {
              SendMessage();
              $(this).dialog('close');
            }
            else
              alert('Please enter a message.');
          }
        }
      });
      
      // Dialogo para tooltip
      $('#dialog').dialog({
        autoOpen: false,
        resizable: false,
        minHeight: 20
      });
      $('.name_tooltip').mouseenter(function() {
        var user = this.id;
        $('#dialog').dialog('option', 'position', 
          [$(this).position().left - $(document).scrollLeft(), $(this).position().top - $(document).scrollTop() + $(this).outerHeight()]
        );
        $('#dialog').html('<img src=\"../common/images/loading.gif\"> Loading...');
        $('#dialog').dialog('open');
        $('#dialog').load('div_user_tooltip.php', {fl_usuario: user});
      }).mouseleave(function() {
        $('#dialog').html('');
        $('#dialog').dialog('close');
      });
      $('.ui-dialog-titlebar').hide();
    });
  </script>";
  
  # Inicializa variables
  $tot_columnas = 8;
  $no_span = $tot_columnas + $tot_columnas - 1;
  
  # Presenta lista de comunidad
  echo "
      <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0' align='left'>
        <tr><td colspan='$no_span' height='20'></td></tr>";
  
  # Recupera los maestros
  if(empty($category) OR $category == 'T') {
    echo "
        <tr><td colspan='$no_span' height='10'></td></tr>";
    $Query  = "SELECT a.fl_maestro, a.ds_ruta_avatar, ";
    $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
    $Query .= ConcatenaBD($concat)." 'ds_nombre', a.ds_empresa, ds_pais ";
    $Query .= "FROM c_maestro a, c_usuario b, c_pais c ";
    $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
    $Query .= "AND a.fl_pais=c.fl_pais ";
    $Query .= "AND b.fg_activo='1' ";
    if(!empty($letter))
      $Query .= "AND ASCII(UCASE(b.ds_nombres))=".ord($letter)." ";
    if(!empty($country))
      $Query .= "AND a.fl_pais=$country ";
    $Query .= "ORDER BY b.ds_nombres";
    $rs = EjecutaQuery($Query);
    $tot_maestros = CuentaRegistros($rs);
    $tot_renglones = (int) ceil($tot_maestros/$tot_columnas);
    for($i = 0; $i < $tot_renglones; $i++) {
      for($j = 0; $j < $tot_columnas; $j++) {
        $fl_maestro[$j] = 0;
        $ds_ruta_avatar[$j] = "&nbsp;";
        $ds_nombre[$j] = "&nbsp;";
        $ds_empresa[$j] = "&nbsp;";
        $ds_pais[$j] = "&nbsp;";
        $row = RecuperaRegistro($rs);
        if(!empty($row[0])) {
          $fl_maestro[$j] = $row[0];
          if($fl_maestro[$j] <> $fl_usuario) {
            $clase[$j] = 'comm_teachers';
            $ds_perfil[$j] = 'Teacher';
            $fg_me[$j] = False;
          }
          else {
            $clase[$j] = 'comm_me';
            $ds_perfil[$j] = 'Me!';
            $fg_me[$j] = True;
          }
          $ds_ruta_avatar[$j] = "<a href='profile_view.php?profile_id=$fl_maestro[$j]'>";
          if(!empty($row[1]))
            $ds_ruta_avatar[$j] .= "<img src='".PATH_MAE_IMAGES."/avatars/".$row[1]."' border='0' />";
          else
            $ds_ruta_avatar[$j] .= "<img src='".SP_IMAGES."/".IMG_T_AVATAR_DEF."' border='0' />";
          $ds_ruta_avatar[$j] .= "</a>";
          $ds_nombre[$j] = str_uso_normal($row[2]);
          if(!empty($row[3]))
            $ds_empresa[$j] = str_uso_normal($row[3]);
          else
            $ds_empresa[$j] = "(Not defined)";
          $ds_pais[$j] = str_uso_normal($row[4]);
        }
      }
      
      # Encabezado
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>";
        if(!empty($fl_maestro[$j])) {
          echo "
            <table width='100%' border='".D_BORDES."' cellpadding='0' cellspacing='0' class='$clase[$j]'>
              <tr>
                <td>$ds_perfil[$j]</td>";
          if(!$fg_me[$j])
            echo "
                <td width='16'>
                  <a href='javascript:SendMessageDialog($fl_maestro[$j]);'><img src='".SP_IMAGES."/".ObtenNombreImagen(217)."' width='16' height='16' border='0' title='Send message'></a>
                </td>";
          echo "
              </tr>
            </table>";
        }
        echo "
          </td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>";
      
      # Avatares
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='bottom'>$ds_ruta_avatar[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>
        <tr><td colspan='$no_span' height='5'></td></tr>";
      
      # Nombre
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        if(!empty($fl_maestro[$j]))
          echo "
          <td width='80' align='center' valign='middle' class='name_tooltip' id='$fl_maestro[$j]'><a href='profile_view.php?profile_id=$fl_maestro[$j]'>$ds_nombre[$j]</a></td>";
        else
          echo "
          <td width='80'>$ds_nombre[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>
        <tr><td colspan='$no_span' height='5'></td></tr>";
      
      # Empresa
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>$ds_empresa[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>";
      
      # Pais
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>$ds_pais[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>";
      
      # Division
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>";
        if(!empty($fl_maestro[$j]))
          echo "
            <table width='100%' border='".D_BORDES."' cellpadding='0' cellspacing='0' class='$clase[$j]'>
              <tr><td height='2'></td></tr>
            </table>";
        echo "
          </td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>
        <tr><td colspan='$no_span' height='20'></td></tr>";
    }
  }
  
  # Recupera los alumnos
  if(empty($category) OR $category == 'S') {
    $Query  = "SELECT a.fl_alumno, a.ds_ruta_avatar, ";
    $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
    $Query .= ConcatenaBD($concat)." 'ds_nombre', e.nb_programa, d.ds_pais, a.fl_alumno ";
    $Query .= "FROM c_alumno a, c_usuario b, k_ses_app_frm_1 c, c_pais d, c_programa e ";
    $Query .= "WHERE a.fl_alumno=b.fl_usuario ";
    $Query .= "AND b.cl_sesion=c.cl_sesion ";
    $Query .= "AND c.ds_add_country=d.fl_pais ";
    $Query .= "AND c.fl_programa=e.fl_programa ";
    $Query .= "AND b.fg_activo='1' ";
    if(!empty($letter))
      $Query .= "AND ASCII(UCASE(b.ds_nombres))=".ord($letter)." ";
    if(!empty($country))
      $Query .= "AND d.fl_pais=$country ";
    if(!empty($program))
      $Query .= "AND c.fl_programa=$program ";
    $Query .= "ORDER BY b.ds_nombres";
    $rs = EjecutaQuery($Query);
    $tot_alumnos = CuentaRegistros($rs);
    $tot_renglones = (int) ceil($tot_alumnos/$tot_columnas);
    for($i = 0; $i < $tot_renglones; $i++) {
      for($j = 0; $j < $tot_columnas; $j++) {
        $fl_alumno[$j] = 0;
        $ds_ruta_avatar[$j] = "&nbsp;";
        $ds_nombre[$j] = "&nbsp;";
        $nb_programa[$j] = "&nbsp;";
        $ds_nivel[$j] = "&nbsp;";
        $ds_pais[$j] = "&nbsp;";
        $row = RecuperaRegistro($rs);
        if(!empty($row[0])) {
          $fl_alumno[$j] = $row[0];
          $fl_maestro[$j] = ObtenMaestroAlumno($fl_alumno[$j]);
          if($fl_maestro[$j] <> $fl_usuario) {
            $clase[$j] = 'comm_students';
            $ds_perfil[$j] = 'Student';
          }
          else {
            $clase[$j] = 'comm_classmates';
            $ds_perfil[$j] = 'My Student';
          }
          $no_nivel = ObtenGradoAlumno($row[5]);
          $ds_ruta_avatar[$j] = "<a href='profile_view.php?profile_id=$fl_alumno[$j]'>";
          if(!empty($row[1]))
            $ds_ruta_avatar[$j] .= "<img src='".PATH_ALU_IMAGES."/avatars/".$row[1]."' border='0' />";
          else
            $ds_ruta_avatar[$j] .= "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."' border='0' />";
          $ds_ruta_avatar[$j] .= "</a>";
          $ds_nombre[$j] = str_uso_normal($row[2]);
          $nb_programa[$j] = str_uso_normal($row[3]);
          if(!empty($no_nivel))
            $ds_nivel[$j] = "Term $no_nivel";
          else
            $ds_nivel[$j] = "(No group)";
          $ds_pais[$j] = str_uso_normal($row[4]);
        }
      }
      
      # Encabezado
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>";
        if(!empty($fl_alumno[$j])) {
          echo "
            <table width='100%' border='".D_BORDES."' cellpadding='0' cellspacing='0' class='$clase[$j]'>
              <tr>
                <td>$ds_perfil[$j]</td>
                <td width='16'>
                  <a href='javascript:SendMessageDialog($fl_alumno[$j]);'><img src='".SP_IMAGES."/".ObtenNombreImagen(217)."' width='16' height='16' border='0' title='Send message'></a>
                </td>
              </tr>
            </table>";
        }
        echo "
          </td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>";
      
      # Avatares
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='bottom'>$ds_ruta_avatar[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>
        <tr><td colspan='$no_span' height='5'></td></tr>";
      
      # Nombre
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        if(!empty($fl_alumno[$j]))
          echo "
          <td width='80' align='center' valign='middle' class='name_tooltip' id='$fl_alumno[$j]'><a href='desktop.php?student=$fl_alumno[$j]'>$ds_nombre[$j]</a></td>";
        else
          echo "
          <td width='80'>$ds_nombre[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>
        <tr><td colspan='$no_span' height='5'></td></tr>";
      
      # Programa
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>$nb_programa[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>";
      
      # Nivel
      /*echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>$ds_nivel[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>";
      */
      # Pais
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>$ds_pais[$j]</td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>";
      
      # Division
      echo "
        <tr>";
      for($j = 0; $j < $tot_columnas; $j++) {
        echo "
          <td width='80' align='center' valign='top'>";
        if(!empty($fl_alumno[$j]))
          echo "
            <table width='100%' border='".D_BORDES."' cellpadding='0' cellspacing='0' class='$clase[$j]'>
              <tr><td height='2'></td></tr>
            </table>";
        echo "
          </td>";
        if($j < $tot_columnas-1)
          echo "
          <td></td>";
      }
      echo "
        </tr>
        <tr><td colspan='$no_span' height='20'></td></tr>";
    }
  }
  echo "
      </table>";
  
?>