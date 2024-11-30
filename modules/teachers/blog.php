<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Presenta lista de entradas de Blog
  $titulo = "School News";
  $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
  PresentaHeader($titulo);
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' valign='top' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>";
  $Query  = "SELECT fl_blog, ds_titulo, ds_resumen, ds_ruta_imagen ";
  $Query .= "FROM c_blog ";
  $Query .= "WHERE fg_maestros='1' ";
  $Query .= "AND fe_blog <= $fe_actual ";
  $Query .= "AND DATE_ADD(fe_blog, INTERVAL ".ObtenConfiguracion(18)." DAY) >= $fe_actual ";
  $Query .= "ORDER BY fe_blog DESC";
  $rs = EjecutaQuery($Query);
  $tot_mensajes = CuentaRegistros($rs);
  while($row = RecuperaRegistro($rs)) {
    $fl_blog = $row[0];
    $ds_titulo = str_uso_normal($row[1]);
    $ds_resumen = str_uso_normal($row[2]);
    $ds_ruta_imagen = str_ascii($row[3]);
    
    # Revisa si se envio notificacion
    $row2 = RecuperaValor("SELECT COUNT(1) FROM k_not_blog WHERE fl_blog=$fl_blog AND fl_usuario=$fl_usuario");
    if($row2[0] > 0)
      $ds_notificar = "(Unread school message)";
    else
      $ds_notificar = "";
    
    # Despliega resumen de noticia
    echo "
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='5' height='10'>&nbsp;</td></tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td width='".ObtenConfiguracion(28)."' valign='top' align='center'>";
    if(!empty($ds_ruta_imagen))
      echo "<a href='blog_detail.php?blog=$fl_blog'><img src='".SP_THUMBS."/news/$ds_ruta_imagen' width='".ObtenConfiguracion(28)."' border='0' title='View $ds_titulo'/></a>";
    else
      echo "<a href='blog_detail.php?blog=$fl_blog'><img src='".SP_IMAGES."/".S_NEWS_THUMB_DEF."' width='".ObtenConfiguracion(28)."' height='".ObtenConfiguracion(29)."' border='0' title='View $ds_titulo'/></a>";
    echo "</td>
                            <td width='20'>&nbsp;</td>
                            <td>
                              <div class='comment_text'>
                                <a href='blog_detail.php?blog=$fl_blog'>$ds_titulo</a>
                                <a href='blog_detail.php?blog=$fl_blog' class='text_unread' title='View $ds_titulo'>$ds_notificar</a>
                              </div>
                              $ds_resumen
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                        </table>";
  }
  
  # Mensaje cuando no hay noticias
  if($tot_mensajes == 0) {
    echo "
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='5' height='10'>&nbsp;</td></tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td colspan='3'>
                              <div class='comment_text'>There are no School News.</div>
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