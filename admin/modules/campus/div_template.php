<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recupera el usuario actual
  ValidaSesion( );
  
  # Recibe parametros
  $fl_sesion = RecibeParametroNumerico('fl_sesion');
  $fl_template = RecibeParametroNumerico('fl_template');
  $fl_funcion = RecibeParametroNumerico('fl_funcion');
  $tot_registros = RecibeParametroNumerico('tot_registros'); 
  
  # Obtener los correos de vanas y alumno
  $ds_emailfrom = ObtenConfiguracion(67);
  
  # Obtenemos los datos del template seleccinado
  $Query_t = "SELECT nb_template FROM k_template_doc WHERE fl_template=$fl_template ";
  $row_t = RecuperaValor($Query_t);
  $nb_template = $row_t[0];
  $ds_header =genera_documento($fl_sesion, 1, $fl_template);
  $ds_cuerpo =genera_documento($fl_sesion, 2, $fl_template);
  $ds_footer =genera_documento($fl_sesion, 3, $fl_template);
  
  # Informacion del student o aplicante
  $Query = "SELECT ds_email,fl_usuario FROM c_usuario a, c_sesion b WHERE a.cl_sesion=b.cl_sesion AND b.fl_sesion=$fl_sesion ";
  $row = RecuperaValor($Query);
  $ds_emailto = $row[0];
  $fl_usuario = $row[1];
  if(empty($ds_emailto)){
    $row = RecuperaValor("SELECT ds_email FROM k_ses_app_frm_1 a, c_sesion b WHERE a.cl_sesion=b.cl_sesion AND b.fl_sesion=$fl_sesion");
    $ds_emailto = $row[0];
  }
  $ds_email = "
  <table>
    <tr>
      <td>".$ds_header."</td>
    </tr>
      <td>".$ds_cuerpo."</td>
    <tr>
      <td>".$ds_footer."</td>
    </tr>
  </table>
  ";
  
  # Recibe el total de usuarios
  if(!empty($tot_registros)){
    $ds_emailto_mul = "";
    $ds_email_mult = "";
    $tot_seleccionados = 0;
    for($i=0;$i<=$tot_registros;$i++){
      # Recibe los alumnos seleccinado
      $clave = RecibeParametroNumerico('clave_'.$i);
      if(!empty($clave)){
        // Listado de applications
        if($fl_funcion==71){
          $fl_sesion = $clave;
          $programa_mul = "applications.php";
        }
        else{ // Listado students
          # Obtenemos el fl_sesion del student
          $row_s = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion=(SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$clave)");
          $fl_sesion = $row_s[0];
          $programa_mul = "students.php";
        }

        # Obtenemos los correos
        $Query2 = "SELECT ds_email, ds_fname FROM k_ses_app_frm_1 a, c_sesion b WHERE a.cl_sesion=b.cl_sesion AND b.fl_sesion=$fl_sesion ";
        $row2 = RecuperaValor($Query2);
        $ds_emailto_mul = $ds_emailto_mul.$row2[0].",";
      
        $ds_email_mult = $ds_email_mult."
        <table>
            <tr>
          <td>".genera_documento($fl_sesion, 1, $fl_template)."</td>
          </tr>
            <td>".genera_documento($fl_sesion, 2, $fl_template)."</td>
          <tr>
            <td>".genera_documento($fl_sesion, 3, $fl_template)."</td>
          </tr>
        </table>
        <input type='hidden' id='flsesion_$i' name='flsesion_$i' value=$fl_sesion>
        <input type='hidden' id='dsemailto_$i' name='dsemailto_$i' value=$row2[0]>
        <hr>";
        $tot_seleccionados = $tot_seleccionados + 1;
      }
    }
    $multiple = true;
    # Obtenemos todos los correos de los usuarios seleccionados
    $ds_emailto = $ds_emailto_mul;
    $ds_email = $ds_email_mult;
  }  
  
  if(!empty($fl_template)){
    echo "
    <style>
    .mensaje{
      border-left:0.1px #cccccc solid; 
      border-bottom:0.1px #cccccc solid; 
      border-top:0.1px #cccccc solid;
      border-right:0.1px #cccccc solid;
    }
    hr{
      display: block; 
      height: 30px; 
      margin-top: -31px; 
      border-style: solid; 
      border-color: #8c8b8b; 
      border-width: 0 0 1px 0; 
      border-radius: 20px; 
    }
    </style>
    <b>From:</b><br />
    <input type='text' name='ds_emailfrom' id='ds_emailfrom' value=\"$ds_emailfrom\" maxlength='110' size='60' readonly='readonly'><br />
    <b>To:</b><br />
    <input type='text' name='ds_emailto' id='ds_emailto' value=\"$ds_emailto\" maxlength='255' size='60' readonly='readonly'><br />
    <b>Subject:</b><br />
    <input type='text' name='ds_subject' id='ds_subject' value=\"$nb_template\" maxlength='110' size='60' readonly='readonly'><br />
    <b>Email:</b><br/>
    <div name='mensaje' id='mensaje' class='mensaje'>".$ds_email."</div>";
    Forma_espacio();
    Forma_CampoOculto('fl_alumno', $fl_usuario);
  } 
    echo "<br/>
    <tr><td>
    <input type='hidden' id='tot_seleccionados' name='tot_seleccionados' value='$tot_seleccionados'/>
    <input type='hidden' id='programa_mul' name='programa_mul' value='$programa_mul'/>
    <input type='submit' value='".ObtenEtiqueta(347)."' Onclick='enviar($multiple);'/></td><td>
    <input type='submit' value='".ObtenEtiqueta(14)."' Onclick='cerrar()'/></td></tr>";
  

?>