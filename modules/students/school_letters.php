<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  $cl_sesion = $_COOKIE[SESION_CAMPUS];
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Presenta contenido de la pagina
  $titulo = ObtenEtiqueta(691);
  PresentaHeader($titulo);
  
  # Recupera el programa y term que esta cursando el alumno
  $row = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='$cl_sesion'");
  $fl_sesion = $row[0];
  

  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td height='5'></td>
                    </tr>
                    <tr>
                      <td colspan='3' align='center' valign='top' height='80' style='padding: 20px 0 0 0;' class='division_line'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td colspan='2' height='10' class='blank_cells'></td>
                          </tr>";
                            $titulos = array("<div style='font-weight:bold;'>Letter ID|center</div>","<div style='font-weight:bold;'>Letter of Acceptance</div>","<div style='font-weight:bold;'>Sent date|center</div>","<div style='font-weight:bold;'>Letter|center</div>");
                            $ancho_col = array('10%', '20%', '20%', '10%');
                            Forma_Tabla_Ini('60%', $titulos, $ancho_col);
                              $Query  = "SELECT a.fl_template, nb_template, fe_envio FROM k_template_doc a, k_alumno_template b ";
                              $Query .= "WHERE a.fl_template = b.fl_template AND b.fl_alumno=$fl_sesion";
                              $rs = EjecutaQuery($Query);
                              for($i=0;$row = RecuperaRegistro($rs);$i++){
                                if($i % 2 == 0)
                                  $clase = "css_tabla_detalle";
                                else
                                  $clase = "css_tabla_detalle_bg";
                                $i = $i+1;
                                echo "
                                <tr class='$clase'>
                                  <td align='center'>".$row[0]."</td>
                                  <td align='left'>".$row[1]."</td>
                                  <td align='center'>".$row[2]."</td>
                                  <td align='center'><a href='".PATH_ADM."/modules/campus/viewemail.php?fl_template=".$row[0]."&fl_sesion=".$fl_sesion."'><img  src='".PATH_ADM_IMAGES."/icon_pdf.gif' width=12 height=12 border=0 title='".ObtenEtiqueta(487)."'></a></td>
                                </tr>";
                              }
                            Forma_Tabla_Fin();  
  
  echo "     
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='3' align='center' valign='top' height='80' class='division_line'>
                      </td>
                    </tr>";
  PresentaFooter( );
  
?>