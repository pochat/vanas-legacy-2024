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
  // $ds_emailfrom = ObtenConfiguracion(67);
  $ds_emailfrom = ObtenConfiguracion(83);
  
  # Obtenemos los datos del template seleccinado
  $Query_t = "SELECT nb_template FROM k_template_doc WHERE fl_template=$fl_template ";
  $row_t = RecuperaValor($Query_t);
  $nb_template = $row_t[0];
  
  //build document
  $ds_header =genera_documento($fl_sesion, 1, $fl_template);
  $ds_cuerpo =genera_documento($fl_sesion, 2, $fl_template);
  $ds_footer =genera_documento($fl_sesion, 3, $fl_template);
  
  # Informacion del student o aplicante
  $Query = "SELECT ds_email,fl_usuario,ds_login,ds_nombres,ds_amaterno,ds_apaterno FROM c_usuario a, c_sesion b WHERE a.cl_sesion=b.cl_sesion AND b.fl_sesion=$fl_sesion ";
  $row = RecuperaValor($Query);
  $ds_emailto = $row[0];
  $fl_usuario = $row[1];
  $ds_login = $row[2];
  $ds_fullname = $row[3].' '.$row[4].' '.$row[5];
  if(empty($ds_emailto)){
    $row = RecuperaValor("SELECT ds_email FROM k_ses_app_frm_1 a, c_sesion b WHERE a.cl_sesion=b.cl_sesion AND b.fl_sesion=$fl_sesion");
    $ds_emailto = $row[0];
  }

  
    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    /* +----------------------------------- Date's validation -----------------------------------------+ */
    /* +-------------------------- Only for the transcripts and diplomas ------------------------------+ */
    /* +-----------------------------------------------------------------------------------------------+ */
    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  
    /* +-----------------------------------------------------------------------------------------------+ */
    /* +------------------------------------ Get all data ---------------------------------------------+ */
    /* +-----------------------------------------------------------------------------------------------+ */

    //CL_SESION
    $Query="SELECT cl_sesion
            FROM c_sesion
            WHERE fl_sesion = $fl_sesion";
    $row = RecuperaValor($Query);
    //Set cl_sesion
    $cl_sesion = $row[0];

    
    //FL_PROGRAMA
    # Recupera datos del aplicante: forma 1
    $Query  = "SELECT ";
    $Query .= "nb_programa, ";
    $Query .= ConsultaFechaBD('fe_inicio', FMT_FECHA)." fe_inicio, ";
    $Query .= "b.fl_programa, c.fl_periodo ";
    $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
    $Query .= "WHERE a.fl_programa=b.fl_programa ";
    $Query .= "AND a.fl_periodo=c.fl_periodo ";
    $Query .= "AND a.ds_add_country=d.fl_pais ";
    $Query .= "AND a.ds_eme_country=e.fl_pais ";
    $Query .= "AND cl_sesion='$cl_sesion'";
    $row = RecuperaValor($Query);

    // Set fl_programa
    $fe_inicio = $row[1];
    $fl_programa = $row[2];

    //DATES
    # Recupera datos de Official Transcript
    $Query="SELECT ".ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin,
          ". ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado, 
          ".ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision, 
          ".ConsultaFechaBD('fe_graduacion', FMT_FECHA)." fe_graduacion 
            FROM k_pctia 
            WHERE fl_alumno = $fl_usuario 
            AND fl_programa = $fl_programa ";

    $row = RecuperaValor($Query);

    //SET DATES
    $fe_fin_temp = $row[0];
    $fe_completado_temp = explode("-", $row[1]);
    $fe_completado = substr(ObtenNombreMes($fe_completado_temp[1]),0,3).' '.$fe_completado_temp[0].', '.$fe_completado_temp[2];
    $fe_emision_temp = explode("-", $row[2]);
    $fe_emision = substr(ObtenNombreMes($fe_emision_temp[1]),0,3).' '.$fe_emision_temp[0].', '.$fe_emision_temp[2];
    $fe_graduacion_temp = $row[3];

    /* +-----------------------------------------------------------------------------------------------+ */
    /* +------------------------------------ DATE CONDITIONS ------------------------------------------+ */
    /* +-----------------------------------------------------------------------------------------------+ */

    // Is diploma or transcripts
    if($fl_template == 194 || $fl_template == 195 ){

      if ($fl_template == 194) {
        $ds_cuerpo = str_replace("#st_full_name#",$ds_fullname,$ds_cuerpo);
      }

      // Fe_fin validation 
      if($fe_fin_temp == null || $fe_completado_temp == null || $fe_emision_temp == null || $fe_emision_temp == null || $fe_graduacion_temp == null){
        echo '<div class="alert alert-info" role="alert">Some dates are empty. Please make sure all dates are set under the student -> status tab.</div>';
        exit;
      }
     
    }

  /* +-----------------------------------------------------------------------------------------------+ */
  /* +------------------------------------ Build message --------------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */
  
  $ds_email = "
  <style>
  .normal-text{
    font-size: 18px !important;
  }

  .names{
    font-size: 24px !important;
  }
  .sign-date{
    font-size: 22px !important;
  }
  .date{
    font-size: 24px !important;
  }
  </style>

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

    /**
     * Diploma styles
     */
    //Set Diploma background image
    $backGroundImageStyle= "
    .diploma-email{
      background-image: url('../../images/VANAS_Diploma_2021_blank.png');
      background-position: center;
      background-repeat: no-repeat;
      background-size:cover;
    }
    ";

    /**
     * Transcripts style
     */
    $transcriptsStyles = "
    #header-template {
      font-size: 10px;
    }

    #vanas-trans-logo{
      width:100%;
    }
    
    #header-template-2 {
        font-size: 10px;
    }  
    
    #footer-template {
        font-size: 8px;
    }  
    ";
  
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
    ";

    //Set styles validation
    if($fl_template == 194){
      echo $backGroundImageStyle;
    }

    if($fl_template == 195){
      /** Replace data */
    $ds_email = str_replace("#st_num#",$ds_login,$ds_email);
    $ds_email = str_replace("#currentpage#",'1',$ds_email);
    $ds_email = str_replace("#num_pages#",'1',$ds_email);
    $ds_email = str_replace("#pg_comdate#",$fe_completado,$ds_email);
    $ds_email = str_replace("#pg_Issdate#",$fe_emision,$ds_email);

      echo $transcriptsStyles;
    }
     echo "
    </style>
    <div class='form-group'>
    <strong>From:</strong>
    <input type='text' class='form-control' name='ds_emailfrom' id='ds_emailfrom' value=\"$ds_emailfrom\" maxlength='110' size='60' readonly='readonly'><br />
    <strong>To:</strong>
    <input type='text' class='form-control' name='ds_emailto' id='ds_emailto' value=\"$ds_emailto\" maxlength='255' size='60' readonly='readonly'><br />
    <strong>Subject:</strong>
    <input type='text' class='form-control' name='ds_subject' id='ds_subject' value=\"$nb_template\" maxlength='110' size='60' readonly='readonly'><br />
    <strong>Email:</strong>";
    if($fl_template == 194){
      echo "
      <div name='mensaje' id='mensaje' class='mensaje'>
        <div class='diploma-email'>".$ds_email."</div>
      </div>";
    }else{
      echo "<div name='mensaje' id='mensaje' class='mensaje'>".$ds_email."</div>";
    }
    Forma_espacio();
    Forma_CampoOculto('fl_alumno', $fl_usuario);
  } 
    echo "<br/>
    <tr><td>
    <input type='hidden' id='tot_seleccionados' name='tot_seleccionados' value='$tot_seleccionados'/>
    <input type='hidden' id='programa_mul' name='programa_mul' value='$programa_mul'/>
    <div class='modal-footer'>
      <input class='btn btn-default' type='submit' value='".ObtenEtiqueta(14)."' Onclick='cerrar();'/></td><td>
      <input class='btn btn-primary' type='submit' value='".ObtenEtiqueta(347)."'  Onclick='enviar($multiple);'/></td></tr>
    </div>
    </div>";
?>