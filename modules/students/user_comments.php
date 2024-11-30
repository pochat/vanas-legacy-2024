<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicia pagina
  $titulo = "Comments";
  PresentaHeader($titulo);
  
  # Inicializa variables de la forma
  echo "
  <div id='dialog'></div>
  <script type='text/javascript'>
    
    $(function() {
      
      // Dialogo para tooltip
      $('#dialog').dialog({
        autoOpen: false,
        resizable: false,
        minHeight: 20
      });
      $('.name_tooltip').mouseenter(function() {
        var user = this.id;
        $('#dialog').dialog('option', 'position', 
          [$(this).position().left - $(document).scrollLeft(), $(this).position().top - $(document).scrollTop() + $(this).outerHeight() + 2]
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
  
  # Inicia lista
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' valign='top' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>";
  
  # Recupera comentarios de otros usuarios sobre las asignaciones del alumno
  $diferencia = RecuperaDiferenciaGMT( );
  $Query  = "SELECT a.fl_com_entregable, a.fl_entrega_semanal, a.fg_tipo, a.fl_usuario, ";
  $Query .= "DATE_FORMAT((DATE_ADD(a.fe_comentario, INTERVAL $diferencia HOUR)), '%c') 'fe_mes', ";
  $Query .= "DATE_FORMAT((DATE_ADD(a.fe_comentario, INTERVAL $diferencia HOUR)), '%e, %Y at %l:%i %p') 'fe_dia_anio', ";
  $Query .= "b.fl_semana, c.fl_leccion, d.no_semana, d.ds_titulo, a.fg_leido ";
  $Query .= "FROM k_com_entregable a, k_entrega_semanal b, k_semana c, c_leccion d ";
  $Query .= "WHERE a.fl_entrega_semanal=b.fl_entrega_semanal ";
  $Query .= "AND b.fl_semana=c.fl_semana ";
  $Query .= "AND c.fl_leccion=d.fl_leccion ";
  $Query .= "AND b.fl_alumno=$fl_usuario ";
  $Query .= "AND a.fl_usuario<>$fl_usuario ";
  $Query .= "ORDER BY a.fe_comentario DESC";
  $rs = EjecutaQuery($Query);
  $tot_comentarios = CuentaRegistros($rs);
  while($row = RecuperaRegistro($rs)) {
    $fl_com_entregable = $row[0];
    $fl_entrega_semanal = $row[1];
    $fg_tipo = $row[2];
    $usr_interaccion = $row[3];
    $fe_mensaje = ObtenNombreMes($row[4])." ".$row[5];
    $ds_ruta_avatar = ObtenAvatarUsuario($usr_interaccion);
    $ds_nombre = ObtenNombreUsuario($usr_interaccion);
    $fl_semana = $row[6];
    $fl_leccion = $row[7];
    $no_semana = $row[8];
    $ds_titulo = str_uso_normal($row[9]);
    $fg_leido = $row[10];
    switch($fg_tipo) {
      case "A":  $nb_tab = "assignment";     $ds_tab = "Assignment";           break;
      case "AR": $nb_tab = "assignment_ref"; $ds_tab = "Assignment Reference"; break;
      case "S":  $nb_tab = "sketch";         $ds_tab = "Sketch";               break;
      case "SR": $nb_tab = "sketch_ref";     $ds_tab = "Sketch Reference";     break;
    }
    if($fg_leido == '0')
      $ds_notificar = "(Unread comment)";
    else
      $ds_notificar = "";
    echo "
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='5' height='10'>&nbsp;</td></tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td width='80' valign='middle' align='center'>
                              <a href='profile_view.php?profile_id=$usr_interaccion'><img src='$ds_ruta_avatar' border='none' title='View $ds_nombre profile'/></a>
                            </td>
                            <td width='20'>&nbsp;</td>
                            <td>
                              <div class='comment_text'>
                                <a href='desktop.php?week=$no_semana&tab=$nb_tab' class='name_tooltip' id='$usr_interaccion'>$ds_nombre</a>
                                <a href='desktop.php?week=$no_semana&tab=$nb_tab' class='text_unread' title='View comments'>$ds_notificar</a>
                              </div>
                              On $fe_mensaje<br>
                              Added a comment for <a href='desktop.php?week=$no_semana&tab=$nb_tab' title='View comments'><b>$ds_tab</b>, week <b>$no_semana</b></a> ($ds_titulo)
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                        </table>";
  }
  
  # Mensaje cuando no hay usuarios
  if($tot_comentarios == 0) {
    echo "
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='5' height='10'>&nbsp;</td></tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td colspan='3'>
                              <div class='comment_text'>You have no unread comments.</div>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                        </table>";
  }
  
  # Termina lista
  echo "
                      </td>
                    </tr>
                    <tr>
                      <td colspan='3' height='20'>&nbsp;</td>
                    </tr>";
  
  PresentaFooter( );
  
?>