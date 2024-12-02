<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../modules/liveclass/bbb_api.php';
  
  # Recibe parametros
  $accion = RecibeParametroHTML('accion');
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('variable');
  
  switch($accion)
  {
    case 'inserta':
      $fl_sem = RecibeParametroNumerico('fl_sem');  
      $fe_clase_d = date("Y-m-d H:i"); 
      $Query  = "INSERT INTO k_clase (fl_grupo, fl_semana, fe_clase, fg_obligatorio, fg_adicional) ";
      $Query .= "VALUES($clave, $fl_sem, '$fe_clase_d', '1', '1')";
      EjecutaQuery($Query);
    break;
    case 'borra':
      $fl_clas = RecibeParametroNumerico('fl_clas'); 
      $Query = "DELETE FROM k_clase WHERE fl_clase = $fl_clas";
      EjecutaQuery($Query);
    break;
    case 'actualiza':
      $fl_clas = RecibeParametroNumerico('fl_clas'); 
      $fe_clas = RecibeParametroFecha('fe_clas');
      $hr_clas = RecibeParametroHoraMin('hr_clas');
      $fg_obliga = RecibeParametroHTML('fg_obliga');
      $fe_class = "'".ValidaFecha($fe_clas)." ".$hr_clas."'";
      $Query  = "UPDATE k_clase SET fe_clase=$fe_class,  fg_obligatorio='$fg_obliga' ";
      $Query .= "WHERE fl_clase = $fl_clas";
      EjecutaQuery($Query);
    break;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) // Actualizacion, recupera de la base de datos
    { 
      $Query  = "SELECT a.fl_term ";
      $Query .= "FROM c_grupo a, c_usuario b, k_term c ";
      $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
      $Query .= "AND a.fl_term=c.fl_term ";
      $Query .= "AND fl_grupo=$clave";
      $row = RecuperaValor($Query);
      $fl_term = $row[0];
      
      # Recupera las fechas de cada clase
      $Query  = "SELECT fl_semana, no_semana, ds_titulo, fe_publicacion ";
      $Query .= "FROM k_semana a, c_leccion b ";
      $Query .= "WHERE a.fl_leccion=b.fl_leccion ";
      $Query .= "AND fl_term=$fl_term ";
      $Query .= "ORDER BY no_semana";
      $rs = EjecutaQuery($Query);
      for($tot_semanas = 0; $row = RecuperaRegistro($rs); $tot_semanas++) 
      {
        $fl_semana[$tot_semanas] = $row[0];
        $no_semana[$tot_semanas] = $row[1];
        $ds_titulo[$tot_semanas] = str_texto($row[2]);
        $anio_pub = substr($row[3], 0, 4);
        $mes_pub = substr($row[3], 5, 2);
        $dia_pub = substr($row[3], 8, 2);
        $fe_publicacion = date_create( );
        $dif_dias = ObtenConfiguracion(25);
        date_date_set($fe_publicacion, $anio_pub, $mes_pub, $dia_pub);
        date_modify($fe_publicacion, "+$dif_dias day");
        $fe_clase[$tot_semanas] = date_format($fe_publicacion, 'd-m-Y'); // Se toma como valor por omision la fecha de publicacion + n dias
        $hr_clase[$tot_semanas] = ObtenConfiguracion(26);
        $Query  = "SELECT fl_clase, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
        $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, fg_adicional ";
        $Query .= "FROM k_clase ";
        $Query .= "WHERE fl_grupo=$clave ";
        $Query .= "AND fl_semana=$fl_semana[$tot_semanas] ";
        $Query .= "ORDER BY fl_clase";
        $cons = EjecutaQuery($Query);
        $conta = 0;
        while($row2 = RecuperaRegistro($cons))
        {
          if($conta > 0)
            $tot_semanas++;
          $fl_clase[$tot_semanas] = $row2[0];
          if(!empty($row2[1])) # Ya se habia puesto una fecha para la clase
          { 
            $fe_clase[$tot_semanas] = $row2[1];
            $hr_clase[$tot_semanas] = $row2[2];
          }
          $fg_obligatorio[$tot_semanas] = $row2[3];
          $fg_adicional[$tot_semanas] = $row2[4];
          $conta++;
        }
      }
    }
    else 
      $tot_semanas = 0;
  }
  else 
  { 
    $Query  = "SELECT a.fl_term ";
    $Query .= "FROM c_grupo a, c_usuario b, k_term c ";
    $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
    $Query .= "AND a.fl_term=c.fl_term ";
    $Query .= "AND fl_grupo=$clave";
    $row = RecuperaValor($Query);
    $fl_term = $row[0];
      
    # Recupera las fechas de cada clase
    $Query  = "SELECT fl_semana, no_semana, ds_titulo, fe_publicacion ";
    $Query .= "FROM k_semana a, c_leccion b ";
    $Query .= "WHERE a.fl_leccion=b.fl_leccion ";
    $Query .= "AND fl_term=$fl_term ";
    $Query .= "ORDER BY no_semana";
    $rs = EjecutaQuery($Query);
    for($tot_semanas = 0; $row = RecuperaRegistro($rs); $tot_semanas++) 
    {
      $fl_semana[$tot_semanas] = $row[0];
      $no_semana[$tot_semanas] = $row[1];
      $ds_titulo[$tot_semanas] = str_texto($row[2]);
      $anio_pub = substr($row[3], 0, 4);
      $mes_pub = substr($row[3], 5, 2);
      $dia_pub = substr($row[3], 8, 2);
      $fe_publicacion = date_create( );
      $dif_dias = ObtenConfiguracion(25);
      date_date_set($fe_publicacion, $anio_pub, $mes_pub, $dia_pub);
      date_modify($fe_publicacion, "+$dif_dias day");
      $fe_clase[$tot_semanas] = date_format($fe_publicacion, 'd-m-Y'); // Se toma como valor por omision la fecha de publicacion + n dias
      $hr_clase[$tot_semanas] = ObtenConfiguracion(26);
      $Query  = "SELECT fl_clase, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
      $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, fg_adicional ";
      $Query .= "FROM k_clase ";
      $Query .= "WHERE fl_grupo=$clave ";
      $Query .= "AND fl_semana=$fl_semana[$tot_semanas] ";
      $Query .= "ORDER BY fl_clase";
      $cons = EjecutaQuery($Query);
      $conta = 0;
      while($row2 = RecuperaRegistro($cons))
      {
        if($conta > 0)
          $tot_semanas++;
        $fl_clase[$tot_semanas] = $row2[0];
        if(!empty($row2[1])) # Ya se habia puesto una fecha para la clase
        { 
          $fe_clase[$tot_semanas] = $row2[1];
          $hr_clase[$tot_semanas] = $row2[2];
        }
        $fg_obligatorio[$tot_semanas] = $row2[3];
        $fg_adicional[$tot_semanas] = $row2[4];
        $conta++;
      }
    }
  }
    $fg_error = False;
    $name = ObtenNombre($fl_usuario); 
    $SALT = ObtenConfiguracion(33);
    $URL = ObtenConfiguracion(32);
    $tit = array(ObtenEtiqueta(390).'|center', ObtenEtiqueta(385), '* '.ObtenEtiqueta(425), ObtenEtiqueta(428).'|center', '&nbsp;');
    $ancho_col = array('10%', '40%', '35%', '10%', '5%');
    Forma_Tabla_Ini('90%', $tit, $ancho_col);
    $adicionales = 0;

    for($i = 0; $i < $tot_semanas; $i++) 
    {
      if($fg_adicional[$i] == '1') {
        $adicionales++;
        // MDB Para generar el titilo de la clase adicional
        $tit_clase_adicional = "Extraclass " . $adicionales;
      }
      if($adicionales % 2 == 0)
      {
        if($i % 2 == 0)
          $clase = "css_tabla_detalle";
        else
          $clase = "css_tabla_detalle_bg";
      }
      else
      {
        if($fg_adicional[$i] == '0')
        {
          if($i % 2 != 0)
            $clase = "css_tabla_detalle";
          else
            $clase = "css_tabla_detalle_bg";
        }
        else
          $clase = $clase_anterior;
      }

      # Revisa si hay una clase activa en este momento
      $Query  = "SELECT fl_live_session, cl_estatus, ds_meeting_id, ds_password_asistente ";
      $Query .= "FROM k_live_session ";
      $Query .= "WHERE fl_clase=".$fl_clase[$i];
      $row = RecuperaValor($Query);
      $fl_live_session = $row[0];
      $cl_estatus = $row[1];
      $ds_meeting_id = $row[2];
      $ds_password_asistente = $row[3];
      if(!empty($fl_live_session) AND $cl_estatus == '1') {
        /* MDB - No borrar este codigo, se usara posteriormente cuando se regrese a BBB
        $bbbObj = new BigBlueButton( );
        $joinURL = $bbbObj->joinURL($ds_meeting_id, $name, $ds_password_asistente, $SALT, $URL);
        $ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'>".$ds_titulo[$i]."</a>";
        */
        // MDB ADOBECONNECT 
        $urlAdobeConnect = ObtenConfiguracion(53);
        $joinURL = $urlAdobeConnect . $ds_meeting_id . "/?guestName=Admin";
        $ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'>".$ds_titulo[$i]."</a>";
      }
      else
        $ds_liga = $ds_titulo[$i];
      
      // MDB Liga de adobe connect para las adicionales
      // No usamos el titulo de la leccion, solo un texto de Join
      if($fg_adicional[$i] == '1') {
        // MDB ADOBECONNECT 
        $urlAdobeConnect = ObtenConfiguracion(53);
        $joinURL = $urlAdobeConnect . $ds_meeting_id . "/?guestName=Admin";
        $ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'>" . $tit_clase_adicional . "</a>";                   
      }
      
      echo "
      <tr class='$clase' id='reg_lecciones_$i'>
        <td align='center'>$no_semana[$i]</td>
        <td align='left'>$ds_liga</td>
        <td align='left'>";
      
      if($fe_clase_err[$i])
        $ds_clase = 'css_input_error';
      else
        $ds_clase = 'css_input';
      
      CampoTexto('fe_clase_'.$i, $fe_clase[$i], 10, 10, $ds_clase, False, "readonly onchange='Actualiza($i, $clave)'");
      Forma_Calendario('fe_clase_'.$i);
      if($hr_clase_err[$i])
        $ds_clase = 'css_input_error';
      else
        $ds_clase = 'css_input';
      CampoTexto('hr_clase_'.$i, $hr_clase[$i], 5, 3, $ds_clase, False, "onchange='Actualiza($i, $clave)'");
      echo "</td>
            <td align='center'>";
      if($fg_adicional[$i] == '1')   
      {
        CampoCheckbox('fg_obligatorio'.$i, $fg_obligatorio[$i], '', '', $p_editar=True, "onchange='Actualiza($i, $clave)'");
        echo "
              </td>
              <td align='center'>
                <a href=\"javascript:Borra($i, $clave);\"><img src = '".PATH_IMAGES."/".IMG_BORRAR."' title=".ETQ_ELIMINAR."></a>
              </td>";
      }
      else 
      {
        CampoCheckbox('fg_obligatorio'.$i, $fg_obligatorio[$i], '', '', $p_editar=False);
        echo "
              </td>
              <td align='center'>
                <a href=\"javascript:Inserta($i, $clave);\"><img src = '".PATH_IMAGES."/".IMG_AGREGAR."' title=".ETQ_INSERTAR.">
              </td>";
      }
      echo "
            </td>
      </tr>\n";
      
      $clase_anterior = $clase;
      
      Forma_CampoOculto('fl_semana_'.$i, $fl_semana[$i]);
      Forma_CampoOculto('no_semana_'.$i, $no_semana[$i]);
      Forma_CampoOculto('ds_titulo_'.$i, $ds_titulo[$i]);
      Forma_CampoOculto('fl_clase_'.$i, $fl_clase[$i]);
    }
    Forma_CampoOculto('tot_semanas', $tot_semanas);
    Forma_Tabla_Fin( );
  
?>