<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Presenta contenido de la pagina
  $titulo = "Community";
  PresentaHeader($titulo);
  
  # Forma dinamica
  echo "
  <script type='text/javascript' src='".PATH_COM_JS."/frmCommunity.js.php'></script>
  <div id=\"dialog\"></div>
  <form name='datos' id='datos'>
    <input type='hidden' name='category' id='category' value='0'>
    <input type='hidden' name='letter' id='letter' value='0'>";
  
  # Presenta cuerpo de la pagina
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>
                        <br>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td valign='top'>
                              <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%' class='comm_filtros'>
                                <tr>
                                  <td width='30%'>
                                    <a href=\"javascript:Category('T');\">Teachers</a> /
                                    <a href=\"javascript:Category('S');\">Students</a> /
                                    <a href=\"javascript:Category('0');\">Both</a>
                                  </td>
                                  <td width='5%'>&nbsp;</td>
                                  <td width='20%'>
                                    <select name='program' id='program' onChange='Program( );'>
                                      <option value='0'>All programs</option>";
  
  # Programas
  $Query  = "SELECT fl_programa, nb_programa ";
  $Query .= "FROM c_programa ";
  $Query .= "ORDER BY nb_programa";
  $Query  = "SELECT c.fl_programa, c.nb_programa, count(a.fl_usuario) ";
  $Query .= "FROM c_usuario a, k_ses_app_frm_1 b, c_programa c ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
  $Query .= "AND b.fl_programa=c.fl_programa ";
  $Query .= "AND a.fg_activo='1' ";
  $Query .= "GROUP BY c.fl_programa ";
  $Query .= "ORDER BY c.nb_programa";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    echo "
                                      <option value='$row[0]'>".str_uso_normal($row[1])." ($row[2])</option>";
  }
  echo "
                                    </select>
                                  </td>
                                  <td width='5%'>&nbsp;</td>
                                  <td width='25%'>
                                    <select name='country' id='country' onChange='Country( );'>
                                      <option value='0'>All countries</option>";
  
  # Paises
  $Query  = "SELECT fl_pais, ds_pais, SUM(cuantos) FROM (";
  $Query .= "SELECT c.fl_pais, c.ds_pais, COUNT(a.fl_usuario) cuantos ";
  $Query .= "FROM c_usuario a, k_ses_app_frm_1 b, c_pais c ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
  $Query .= "AND b.ds_add_country=c.fl_pais ";
  $Query .= "AND a.fg_activo='1' ";
  $Query .= "GROUP BY c.fl_pais, c.ds_pais ";
  $Query .= "UNION ";
  $Query .= "SELECT f.fl_pais, f.ds_pais, COUNT(d.fl_usuario) cuantos ";
  $Query .= "FROM c_usuario d, c_maestro e, c_pais f ";
  $Query .= "WHERE d.fl_usuario=e.fl_maestro ";
  $Query .= "AND e.fl_pais=f.fl_pais ";
  $Query .= "AND d.fg_activo='1' ";
  $Query .= "GROUP BY f.fl_pais, f.ds_pais ";
  $Query .= ") todos ";
  $Query .= "GROUP BY fl_pais, ds_pais ";
  $Query .= "ORDER BY ds_pais";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    echo "
                                      <option value='$row[0]'>".str_uso_normal($row[1])." ($row[2])</option>";
  }
  echo "
                                    </select>
                                  </td>
                                  <td width='5%'>&nbsp;</td>
                                  <td width='10%'><a href=\"javascript:Reset( );\">View all</a></td>
                                </tr>
                                <tr><td colspan='7' height='10'></td></tr>
                                <tr>
                                  <td colspan='7'>";
  
  # Iniciales para nombres
  $Query  = "SELECT DISTINCT(ASCII(UCASE(ds_nombres))) ";
  $Query .= "FROM c_usuario ";
  $Query .= "WHERE fl_perfil IN(".PFL_ESTUDIANTE.", ".PFL_MAESTRO.") ";
  $Query .= "AND fg_activo='1'";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    $letra = $row[0];
    $fg_iniciales[$letra] = True;
  }
  echo "Start with <a href=\"javascript:Letter('0');\">any letter</a>&nbsp;&nbsp;or&nbsp;&nbsp;";
  for($i = 65; $i <= 90; $i++) {
    if($fg_iniciales[$i])
      echo "<a href=\"javascript:Letter('".chr($i)."');\">&nbsp;".chr($i)."&nbsp;</a>";
    else
      echo "&nbsp;".chr($i)."&nbsp;";
    echo "&nbsp";
  }
  echo "
                                  </td>                                  
                                </tr>
                              </table>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td valign='top'>
                              <div id='div_community'></div>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                        </table>
                        <br>
                      </td>
                    </tr>
  </form>
  <script>
  $.ajax({
    type: 'POST',
    url : 'div_community.php',
    data: 'category='+$('#category').val()+
          '&letter='+$('#letter').val()+
          '&program='+$('#program').val()+
          '&country='+$('#country').val(),
    success: function(html) {
      $('#div_community').html(html);
    }
  });
  </script>";
  
  PresentaFooter( );
  
?>