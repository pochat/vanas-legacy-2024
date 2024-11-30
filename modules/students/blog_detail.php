<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fl_blog = RecibeParametroNumerico('blog', True);
  
  # Actualiza contador de visualizaciones de la noticia
  EjecutaQuery("UPDATE c_blog SET no_hits=no_hits+1 WHERE fl_blog=$fl_blog");
  
  # Actualiza estado de la notificacion para el usuario
  EjecutaQuery("DELETE FROM k_not_blog WHERE fl_blog=$fl_blog AND fl_usuario=$fl_alumno");
  
  # Recupera el contenido de la noticia
  $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
  $Query  = "SELECT ". ConsultaFechaBD('fe_blog', FMT_FECHA)." 'fe_blog', ds_titulo, ds_blog, ds_ruta_imagen, ds_ruta_video ";
  $Query .= "FROM c_blog ";
  $Query .= "WHERE fl_blog=$fl_blog ";
  $Query .= "AND fg_alumnos='1' ";
  $Query .= "AND fe_blog <= $fe_actual ";
  $Query .= "AND DATE_ADD(fe_blog, INTERVAL ".ObtenConfiguracion(18)." DAY) >= $fe_actual";
  $row = RecuperaValor($Query);
  $titulo = str_uso_normal($row[1]);
  $contenido = str_uso_normal($row[2]);
  $archivo_img = str_uso_normal($row[3]);
  $archivo_flv = str_uso_normal($row[4]);
  
  # Presenta detalle de entradas de Blog
  PresentaHeader($titulo);
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td colspan='3' height='5'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td class='comment_text'>";
  if(!empty($archivo_img) AND empty($archivo_flv)) {
    echo "<img src='".SP_IMAGES."/news/$archivo_img' width='".ObtenConfiguracion(27)."'><br><br>";
  }
  if(!empty($archivo_flv)) {
    $ds_matricula = ObtenMatriculaAlumno($fl_alumno);
    PresentaWatermark($ds_matricula);
    #PresentaVideo(SP_VIDEOS."/news/", $archivo_flv);
    PresentaVideoJWP($archivo_flv);
    echo "<br><br>";
  }
  echo "$contenido</td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan='3' height='20'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan='3' align='center'><a href='blog.php'>Back to School News</a></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='3' height='20'>&nbsp;</td>
                    </tr>";
  
  PresentaFooter( );
  
?>