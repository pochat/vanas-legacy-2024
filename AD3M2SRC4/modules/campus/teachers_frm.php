<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametro
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MAESTROS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ds_login, fg_activo, ".ConsultaFechaBD('fe_alta', FMT_FECHA)." fe_alta, ";
      $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
      $Query .= "(".ConcatenaBD($concat).") 'fe_ultacc', ";
      $Query .= "no_accesos, ds_nombres, ds_apaterno, ds_amaterno, ds_email, a.fl_perfil, b.nb_perfil, fg_genero, ";
      $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, fl_pais, fl_zona_horaria, c.ds_number,c.mn_hour_rate,c.mn_hour_rate_group_global,mn_hour_rate_global_class ";
      $Query .= "FROM c_usuario a, c_perfil b, c_maestro c ";
      $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
      $Query .= "AND a.fl_usuario=c.fl_maestro ";
      $Query .= "AND fl_usuario=$clave";
      $row = RecuperaValor($Query);
      $ds_login = str_texto($row[0]);
      $fg_activo = $row[1];
      $fe_alta = $row[2];
      $fe_ultacc = $row[3];
      $no_accesos = $row[4];
      $ds_nombres = str_texto($row[5]);
      $ds_apaterno = str_texto($row[6]);
      $ds_amaterno = str_texto($row[7]);
      $ds_email = str_texto($row[8]);
      $fl_perfil = $row[9];
      $nb_perfil = $row[10];
      $fg_genero = $row[11];
      $fe_nacimiento = $row[12];
      $fl_pais = $row[13];
      $fl_zona_horaria = $row[14];
      $ds_number = $row[15];
      $mn_hour_rate=$row['mn_hour_rate'];
      $mn_hour_rate_group_global=$row['mn_hour_rate_group_global'];
      $mn_hour_rate_global_class=$row['mn_hour_rate_global_class'];
      $fg_hour_rate=false;
      $fg_hour_rate_group=false;

    }
    else { // Alta, inicializa campos
      $ds_login = "";
      $fg_activo = "1";
      $fe_alta = "";
      $fe_ultacc = "";
      $no_accesos = "";
      $ds_nombres = "";
      $ds_apaterno = "";
      $ds_amaterno = "";
      $ds_email = "";
      $fl_perfil = PFL_MAESTRO;
      $row = RecuperaValor("SELECT nb_perfil FROM c_perfil WHERE fl_perfil=$fl_perfil");
      $nb_perfil = str_texto($row[0]);
      $fg_genero = "";
      $fe_nacimiento = "";
      $row = RecuperaValor("SELECT fl_pais FROM c_pais WHERE cl_iso2='CA'");
      $fl_pais = $row[0];
      $row = RecuperaValor("SELECT fl_zona_horaria FROM c_zona_horaria WHERE fg_default='1'");
      $fl_zona_horaria = $row[0];
      $ds_number = "";
    }
    $ds_login_err = "";
    $ds_password_err = "";
    $ds_password_conf_err = "";
    $ds_nombres_err = "";
    $ds_apaterno_err = "";
    $ds_email_err = "";
    $fe_nacimiento_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_login = RecibeParametroHTML('ds_login');
    $ds_login_err = RecibeParametroNumerico('ds_login_err');
    $ds_password_err = RecibeParametroNumerico('ds_password_err');
    $ds_password_conf_err = RecibeParametroNumerico('ds_password_conf_err');
    $fg_activo = RecibeParametroNumerico('fg_activo');
    $fe_alta = RecibeParametroFecha('fe_alta');
    $fe_ultacc = RecibeParametroFecha('fe_ultacc');
    $no_accesos = RecibeParametroNumerico('no_accesos');
    $ds_nombres = RecibeParametroHTML('ds_nombres');
    $ds_nombres_err = RecibeParametroNumerico('ds_nombres_err');
    $ds_apaterno = RecibeParametroHTML('ds_apaterno');
    $ds_apaterno_err = RecibeParametroNumerico('ds_apaterno_err');
    $ds_amaterno = RecibeParametroHTML('ds_amaterno');
    $ds_email = RecibeParametroHTML('ds_email');
    $ds_email_err = RecibeParametroNumerico('ds_email_err');
    $fl_perfil = RecibeParametroNumerico('fl_perfil');
    $nb_perfil = RecibeParametroHTML('nb_perfil');
    $fg_genero = RecibeParametroHTML('fg_genero');
    $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
    $fe_nacimiento_err = RecibeParametroNumerico('fe_nacimiento_err');
    $fl_pais = RecibeParametroNumerico('fl_pais');
    $fl_zona_horaria = RecibeParametroNumerico('fl_zona_horaria');
    $total = RecibeParametroNumerico('total');
    for($i=0;$i<$total;$i++){
      $mn_lecture_fee[$i] = RecibeParametroHTML('mn_lecture_fee_'.$i);
      $mn_lecture_fee_err[$i] = RecibeParametroNumerico('mn_lecture_fee_err_'.$i);
      $mn_extra_fee[$i] = RecibeParametroHTML('mn_extra_fee_'.$i);
      $mn_extra_fee_err[$i] = RecibeParametroNumerico('mn_extra_fee_err_'.$i);
    }
    $ds_number = RecibeParametroHTML('ds_number');
    # Si hay error recibe las clases globales
    $total_globales = RecibeParametroNumerico('total_globales');
    for($j=0;$j<$total_globales;$j++){
      $mn_cglobal_fee[$j] = RecibeParametroHTML('mn_cglobal_fee_'.$j);
      $mn_cglobal_fee_err[$j] = RecibeParametroNumerico('mn_cglobal_fee_err'.$j);      
    }
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_MAESTROS);
  
  # Funciones para preview de imagenes
  require 'preview.inc.php';
  
  # Forma para cambiar contrasena a otros usuarios
  if(ValidaPermiso(FUNC_PWD_OTROS, PERMISO_EJECUCION)) {
	 
    $ds_cambiar_pwd = "&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' data-toggle='modal' data-target='#exampleModal'  Onclick=UpdatePass($clave,".FUNC_MAESTROS."); >".ObtenEtiqueta(126)."</a>";
    //echo "
		//  <form name='cambio_pwd_otros' method='post' action='pwd_frm.php'>
		//	<input type='hidden' name='clave' value='$clave'>
		//	<input type='hidden' name='funcion' value='".FUNC_MAESTROS."'>
		//  </form>\n";
		  
	echo"<script>
	    function UpdatePass(clave,funcion){
			
			
			$.ajax({
				type: 'POST',
				url : 'pwd_frm.php',
				data: 'clave='+clave+
					  '&funcion='+funcion,
				async: false,
				success: function(html) {
				 
				$('#updatepass').html(html);
				}
			});
			
		}
	    
		</script>";	  
		  
  }
  else
    $ds_cambiar_pwd = "";
  
  # super user
  $super_user = "&nbsp;&nbsp;&nbsp;<a href=\"javascript:super_user('$ds_login');\"'>".ObtenEtiqueta(808)."</a>";
  
  # Forma para captura de datos
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Revisa si es un registro nuevo
  if(empty($clave)) {
    Forma_CampoTexto(ETQ_USUARIO, True, 'ds_login', $ds_login, 16, 16, $ds_login_err);
    Forma_CampoTexto(ObtenEtiqueta(123), True, 'ds_password', '', 16, 16, $ds_password_err, True);
    Forma_CampoTexto(ObtenEtiqueta(124), True, 'ds_password_conf', '', 16, 16, $ds_password_conf_err, True);
  }
  else {
    Forma_CampoInfo(ETQ_USUARIO, $ds_login.$ds_cambiar_pwd.$super_user);
    Forma_CampoOculto('ds_login' , $ds_login);
  }
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_nombres', $ds_nombres, 100, 32, $ds_nombres_err);
  Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_apaterno', $ds_apaterno, 50, 32, $ds_apaterno_err);
  Forma_CampoTexto(ObtenEtiqueta(119), False, 'ds_amaterno', $ds_amaterno, 50, 32, '');
  Forma_Espacio( );
  $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116)); // Marculino, Femenino
  $val = array('M', 'F');
  Forma_CampoSelect(ObtenEtiqueta(114), False, 'fg_genero', $opc, $val, $fg_genero);
  Forma_Espacio();
  Forma_CampoTexto(ObtenEtiqueta(120).' '.ETQ_FMT_FECHA, True, 'fe_nacimiento', $fe_nacimiento, 10, 10, $fe_nacimiento_err, False, '', True, '', '', 'form-control', 'right', 'col col-sm-4', 'col col-sm-4');
  Forma_Calendario('fe_nacimiento');
  Forma_Espacio();
  Forma_CampoTexto(ObtenEtiqueta(121), True, 'ds_email', $ds_email, 64, 32, $ds_email_err);
  Forma_Espacio( );
  
  $Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
  Forma_CampoSelectBD(ObtenEtiqueta(287), False, 'fl_pais', $Query, $fl_pais);
  Forma_Espacio();
  $concat = array('nb_zona_horaria', "' (GMT '", "no_gmt", "')'");
  $Query  = "SELECT (".ConcatenaBD($concat).") 'ds_zona', fl_zona_horaria FROM c_zona_horaria ORDER BY nb_zona_horaria";
  Forma_CampoSelectBD(ObtenEtiqueta(411), False, 'fl_zona_horaria', $Query, $fl_zona_horaria);
  Forma_Espacio( );
  
  Forma_CampoInfo(ObtenEtiqueta(110), $nb_perfil);
  Forma_CampoOculto('fl_perfil', $fl_perfil);
  Forma_CampoOculto('nb_perfil', $nb_perfil);
  Forma_CampoCheckbox(ObtenEtiqueta(113), 'fg_activo', $fg_activo);
  Forma_Espacio( );
  
  # Estadisticas del usuario, solo en modo de edicion
  if(!empty($clave)) {
    
    # Estadisticas del usuario, solo en modo de edicion
    Forma_CampoInfo(ObtenEtiqueta(111), $fe_alta);
    Forma_CampoOculto('fe_alta', $fe_alta);
    Forma_CampoInfo(ObtenEtiqueta(112), $fe_ultacc);
    Forma_CampoOculto('fe_ultacc', $fe_ultacc);
    Forma_CampoInfo(ObtenEtiqueta(122), $no_accesos);
    Forma_CampoOculto('no_accesos', $no_accesos);
    Forma_Espacio( );
    
    # Recupera datos de configuracion del maestro
    $Query  = "SELECT ds_ruta_avatar, ds_ruta_foto, ds_empresa, ds_website, ds_gustos, ds_pasatiempos, ds_biografia ";
    $Query .= "FROM c_maestro ";
    $Query .= "WHERE fl_maestro=$clave";
    $row = RecuperaValor($Query);
    $ds_ruta_avatar = str_texto($row[0]);
    $ds_ruta_foto = str_texto($row[1]);
    $ds_empresa = str_texto($row[2]);
    $ds_website = str_texto($row[3]);
    $ds_gustos = str_texto($row[4]);
    $ds_pasatiempos = str_texto($row[5]);
    $ds_biografia = str_texto($row[6]);
    
    # Configuracion personal
    Forma_Seccion(ObtenEtiqueta(410));
    Forma_CampoPreview(ObtenEtiqueta(412), 'ds_ruta_avatar', $ds_ruta_avatar, PATH_MAE_IMAGES."/avatars", False, False);
    Forma_CampoPreview(ObtenEtiqueta(413), 'ds_ruta_foto', $ds_ruta_foto, PATH_MAE_IMAGES."/pictures", False, False);
    Forma_CampoInfo(ObtenEtiqueta(418), $ds_empresa);
    Forma_CampoInfo(ObtenEtiqueta(414), $ds_website);
    Forma_CampoInfo(ObtenEtiqueta(415), $ds_gustos);
    Forma_CampoInfo(ObtenEtiqueta(416), $ds_pasatiempos);
    Forma_CampoInfo(ObtenEtiqueta(417), $ds_biografia);
    //Forma_CampoInfo(ObtenEtiqueta(738), $ds_number);
    Forma_CampoTexto(ObtenEtiqueta(738),False, 'ds_number', $ds_number,50,30);
    Forma_Espacio( );
    
    # Seccion de tarifas
    Forma_Seccion(ObtenEtiqueta(185));
    Forma_Espacio();
    
    /*
    Forma_CampoTexto('Default hourly rate',False, 'mn_hour_rate', $mn_hour_rate,50,30);
    Forma_CampoCheckbox('Update all amounts and rates below', 'fg_hour_rate', $fg_hour_rate);

    $titulos = array(ObtenEtiqueta(360),'Date', ObtenEtiqueta(381), ObtenEtiqueta(420),
                 ObtenEtiqueta(710), ObtenEtiqueta(711));
    $ancho_col = array('35%','10%', '10%', '10%', '10%', '10%');
    Forma_Tabla_Ini('85%',$titulos, $ancho_col);


    #Solo se toma en cuenta desde el mes pasado.
    $actual = strtotime(date('Y-m'));
    $mespasado = date("Y-m", strtotime("-1 month", $actual));

    # Obtenemos los grupos que aun no han terminado y que tiene alumnos 
    $Query = "SELECT nb_programa, d.nb_periodo, a.nb_grupo, c.mn_lecture_fee, c.mn_extra_fee,a.fl_grupo,c.fl_programa, c.ds_duracion ";
    $Query .=",fe_inicio, DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as mes_anterior ";
    #$Query .= "FROM c_grupo a, k_term b, c_programa c, c_periodo d, k_programa_costos e ";
    #$Query .= "WHERE a.fl_term = b.fl_term AND b.fl_programa = c.fl_programa AND b.fl_periodo = d.fl_periodo AND c.fl_programa = e.fl_programa ";
    #$Query .= "AND d.fe_inicio <= CURDATE()  ";
    #$Query .= "AND c.fg_archive='0' AND fl_maestro=$clave ";
    #mjd Modificamos Querys para visualizarlo mejor.
    $Query.=",DATE_FORMAT(k.fe_clase,'%d-%m-%Y')fe_clase,cl.ds_titulo ";
    $Query .="FROM c_grupo a
            JOIN k_clase k ON k.fl_grupo=a.fl_grupo
            JOIN k_term b ON a.fl_term = b.fl_term 
            JOIN c_programa c ON b.fl_programa = c.fl_programa 
            JOIN c_periodo d ON b.fl_periodo = d.fl_periodo
            JOIN k_programa_costos e ON c.fl_programa = e.fl_programa 
            JOIN k_semana s ON s.fl_semana=k.fl_semana
            LEFT JOIN c_leccion cl ON(cl.fl_leccion=s.fl_leccion) ";
    $Query .="WHERE c.fg_archive='0' AND fl_maestro=$clave ";
    $Query .="AND DATE_FORMAT(k.fe_clase,'%Y-%m')>='$mespasado'  ";
    
    $rs = EjecutaQuery($Query);
    $total = CuentaRegistros($rs);
    for($i=0;$row= RecuperaRegistro($rs);$i++){
      $nb_programa = $row[0];
      $nb_periodo = $row[1];
      $nb_grupo = $row[2];
      $mn_lecture_fee = $row[3];
      $mn_extra_fee = $row[4];
      $fl_grupo = $row[5];
      $fl_programa = $row[6];
      $ds_duracion = $row[7];
      $fe_inicio=$row['fe_inicio'];
      $fe_mes_anterior=$row['mes_anterior'];
      $ds_titulo=$row['ds_titulo'];
      $fe_class=$row['fe_clase'];

      #la tarifa default se determina de la anotada en el cuadro aply
      $Queryt="SELECT mn_hour_rate FROM c_maestro WHERE fl_maestro=$clave ";
      $rot=RecuperaValor($Queryt);
      $mn_tarifa_default=$rot['mn_hour_rate'];
      $mn_lecture_fee=$mn_tarifa_default;
      $mn_extra_fee=$mn_lecture_fee;
      
      # Si existe el regristro obtiene datos guardados si no los default
      if(ExisteEnTabla('k_maestro_tarifa','fl_maestro',$clave) AND ExisteEnTabla('k_maestro_tarifa','fl_programa',$fl_programa) 
         AND ExisteEnTabla('k_maestro_tarifa','fl_grupo',$fl_grupo)){
        $row2 = RecuperaValor("SELECT mn_lecture_fee, mn_extra_fee FROM k_maestro_tarifa WHERE fl_maestro=$clave AND fl_programa=$fl_programa AND fl_grupo=$fl_grupo ");
        
        $mn_lecture_fee = isset($row2[0])?$row2[0]:NULL;
        $mn_extra_fee = isset($row2[1])?$row2[1]:NULL;

        if(empty($mn_lecture_fee)){
            $mn_lecture_fee=$mn_tarifa_default;

        }
        if(empty($mn_extra_fee)){
            $mn_extra_fee=$mn_tarifa_default;

        }
      }

     
         


      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      echo "
      <tr>
        <td>".$nb_programa." (".$ds_duracion.") <br> $ds_titulo</td>
        <td>$fe_class</td>
        <td>".$nb_periodo." <br><i> </i></td>
        <td>".$nb_grupo."</td>
        <td>";
       
        # recibe los datos cuando hay un error
        if($fg_error AND !empty($mn_lecture_fee_err[$i])){
          $class='has-error';
        }
        else{
          $class = '';
        }




        echo "
        <div class='row $class'>";
       # if($fe_inicio>$fe_mes_anterior){
            CampoTexto('mn_lecture_fee_'.$i, $mn_lecture_fee, 10, 10, "form-control", False, 'style="text-align:right"');
            Forma_CampoOculto('fg_update_'.$i,1);
      #  }
        
          echo "
        </div>
        </td>
        <td>";
        if($fg_error AND !empty($mn_extra_fee_err[$i]))
          $class= "has-error";
        // else
          // $class = "form-control";
        echo "
        <div class='row $class'>";
        if($fe_inicio>$fe_mes_anterior)
          CampoTexto('mn_extra_fee_'.$i, $mn_extra_fee, 10, 10, "form-control", False, 'style="text-align:right" ');
        echo "
        </div>
        </td>
        ";    
        echo"
      </tr>";
      Forma_CampoOculto('fl_grupo_'.$i, $fl_grupo);
      Forma_CampoOculto('fl_programa_'.$i, $fl_programa);

    }
    Forma_CampoOculto('total', $total);
    Forma_Tabla_Fin();
    Forma_Espacio();


    #Inicia las globales
    Forma_CampoTexto('Default hourly rate',False, 'mn_hour_rate_global_class', $mn_hour_rate_global_class,50,30);
    Forma_CampoCheckbox('Update all amounts and rates below', 'fg_hour_rate_global', $fg_hour_rate_global);

    #Solo se toma en cuenta desde el mes pasado.
    $actual = strtotime(date('Y-m'));
    $mespasado = date("Y-m", strtotime("-1 month", $actual));

    $titulos = array(ObtenEtiqueta(360),'Date', 'Class Name', '',
                ObtenEtiqueta(710), ObtenEtiqueta(711));
    $ancho_col = array('35%','10%', '10%', '10%', '10%', '10%');
    Forma_Tabla_Ini('85%',$titulos, $ancho_col);

    # Obtenemos lasclases globales en las que se le asigno una sesion
    $Querycg  = "SELECT cg.fl_clase_global, cg.ds_clase,kcg.ds_titulo,DATE_FORMAT(kcg.fe_clase,'%d-%m-%Y')fe_clase  ";
    $Querycg .= "FROM c_clase_global cg ";
    $Querycg .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_maestro=$clave ) ";
    #$Querycg .= "LEFT JOIN k_curso_cg kc ON(kc.fl_clase_global=cg.fl_clase_global) ";
    #$Querycg .= "LEFT JOIN c_programa cp ON(cp.fl_programa=kc.fl_programa) ";
    $Querycg .= "WHERE DATE_FORMAT(kcg.fe_clase,'%Y-%m')='$mespasado' ";
    #$Querycg .= "GROUP BY fl_clase_global ";
    
    $rcg = EjecutaQuery($Querycg);
    $total_globales = CuentaRegistros($rcg);
    # Por el momento esta variable es utilizada para el monto de los teachers
    $mn_cglobal_fee = ObtenConfiguracion(96);
    #la tarifa default se determina de la anotada en el cuadro aply
    $Queryt="SELECT mn_hour_rate FROM c_maestro WHERE fl_maestro=$clave ";
    $rot=RecuperaValor($Queryt);
    $mn_cglobal_fee=$rot['mn_hour_rate'];

    if(empty($mn_cglobal_fee))
        $mn_cglobal_fee = ObtenConfiguracion(96);


    for($j=0;$rwcg = RecuperaRegistro($rcg);$j++){      
      $fl_clase_global = $rwcg[0];
      $nb_programa_cg = ObtenEtiqueta(1023);
      $nb_periodo_cg = "";
      $ds_clase_global = $rwcg[1];
      $ds_title_class=$rwcg[2];
      $fe_clase=$rwcg['fe_clase'];
       # Si existe el regristro obtiene datos guardados si no los default
      if(ExisteEnTabla('k_maestro_tarifa_cg','fl_maestro',$clave, 'fl_clase_global', $fl_clase_global, True)){
        $row2 = RecuperaValor("SELECT mn_cglobal_fee FROM k_maestro_tarifa_cg WHERE fl_maestro=$clave AND fl_clase_global=$fl_clase_global ");
        $mn_cglobal_fee = $row2[0];

        if(empty($mn_cglobal_fee))
            $mn_cglobal_fee=$rot['mn_hour_rate'];
        
      }
      
      if($j % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      
      echo "
      <tr>
        <td>".$nb_programa_cg."</td>
        <td>$fe_clase</td>
        
        <td>".$ds_clase_global." $ds_title_class</td>
        <td colspan='2'>";       
            # recibe los datos cuando hay un error
            if($fg_error AND !empty($mn_cglobal_fee_err[$j])){
              $class='has-error';
            }
            // else{
              // $class = 'form-control';
            // }
            echo "
            <div class='row $class'>";
              CampoTexto('mn_cglobal_fee_'.$j, $mn_cglobal_fee, 10, 10, 'form-control', False, 'style="text-align:right"');
            echo "
            </div>
        </td>
        <td></td>

      </tr>";
      Forma_CampoOculto('fl_clase_global'.$j, $fl_clase_global);
    }
    Forma_CampoOculto('total_globales', $total_globales);
    Forma_Tabla_Fin();
    



    

    Forma_Seccion(ObtenEtiqueta(2521));
    Forma_Espacio();
    
    Forma_CampoTexto('Default hourly rate',False, 'mn_hour_rate_group_global', $mn_hour_rate_group_global,50,30);
    Forma_CampoCheckbox('Update all amounts and rates below', 'fg_hour_rate_group', $fg_hour_rate_group);

    $titulos = array(ObtenEtiqueta(360), ObtenEtiqueta(722), ObtenEtiqueta(420),
                 ObtenEtiqueta(710));
    $ancho_col = array('15%', '', '', '');
    Forma_Tabla_Ini('90%',$titulos, $ancho_col);

    #Presntamos las fechas y montos para las clases grupales.
    # Obtenemos lasclases globales en las que se le asigno una sesion
    $Querycg  = "SELECT a.fl_grupo,a.fl_clase_grupo, a.nb_clase,b.no_semana,c.nb_grupo  ";
    $Querycg .= ",a.fe_clase, DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as mes_anterior ";
    $Querycg .= "FROM k_clase_grupo a ";
    $Querycg .= "JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo ";
    $Querycg .= "JOIN c_grupo c ON c.fl_grupo=a.fl_grupo ";
    $Querycg .= "WHERE a.fl_maestro=$clave ";
    $Querycg .= "AND DATE_FORMAT(a.fe_clase,'%Y-%m')>='$mespasado' ";
    $rg = EjecutaQuery($Querycg);
    $total_grupales = CuentaRegistros($rg);
    # Por el momento esta variable es utilizada para el monto de los teachers
    $mn_grupal_fee = ObtenConfiguracion(96);
    for($j=0;$rog = RecuperaRegistro($rg);$j++){      
        $fl_grupo = $rog[0];
        $fl_clase_grupo =$rog[1];
        $nb_clase = $rog['nb_clase'];
        $no_semana = $rog['no_semana'];
        $nb_grupo=$rog['nb_grupo'];
        $fe_clase=$rog['fe_clase'];
        $fe_mes_anterior=$rog['mes_anterior'];


        # Si existe el regristro obtiene datos guardados si no los default
        if(ExisteEnTabla('k_maestro_tarifa_gg','fl_maestro',$clave, 'fl_clase_grupo', $fl_clase_grupo, True)){
            $row2 = RecuperaValor("SELECT mn_cgrupo FROM k_maestro_tarifa_gg WHERE fl_maestro=$clave AND fl_clase_grupo=$fl_clase_grupo ");
            $mn_grupal_fee = $row2[0];
        }
        
        #Verifica si existe 
        $Query="SELECT COUNT(*) FROM k_maestro_tarifa_gg WHERE fl_maestro=$clave AND fl_clase_grupo=$fl_clase_grupo  ";
        $row=RecuperaValor($Query);
        if(empty($row[0])){
            EjecutaInsert("INSERT INTO k_maestro_tarifa_gg (fl_maestro,fl_clase_grupo,mn_cgrupo )VALUES($clave,$fl_clase_grupo,$mn_grupal_fee) ");
        }

       
        #Recupermaos todos los periodos que incluye el programa.
        $concat = array('nb_programa', "' ('", 'ds_duracion', "')'", "' - '", 'nb_periodo', "' - ".ObtenEtiqueta(375)." '", 'no_grado');
        $Querysv="SELECT a.fl_term,a.fl_grupo,b.fl_programa,b.fl_periodo,".ConcatenaBD($concat)." 'nb_term'  
                FROM k_grupo_term a
                JOIN k_term b ON a.fl_term=b.fl_term 
                JOIN c_programa c ON c.fl_programa=b.fl_programa
                JOIN c_periodo d ON d.fl_periodo=b.fl_periodo

                WHERE a.fl_grupo=$fl_grupo  ";
        $rsm=EjecutaQuery($Querysv);
        $total_terms=CuentaRegistros($rsm);
        $periodos="";
        for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
            $fl_terms_i = $rowm[0];        
            $periodos.=$rowm[4]."<br>";
        }



        if($j % 2 == 0)
            $clase = "css_tabla_detalle";
        else
            $clase = "css_tabla_detalle_bg";
        
        echo "
      <tr>
        <td>".$nb_clase."</td>
        <td>".$periodos."</td>
        <td>".$nb_grupo."</td>
        <td colspan='2'>";       
        
            echo "
            <div class='row $class'> ";
            if($fe_clase>=$fe_mes_anterior){
                CampoTexto('mn_grupal_fee_'.$j, $mn_grupal_fee, 10, 10, 'form-control', False, "style='text-align:right' onchange='ActualizaTCG(".$j.",".$fl_grupo.",".$fl_clase_grupo.")' ");
                Forma_CampoOculto('fg_updateg_'.$j,1);
        
            }
            echo "
            </div>
        </td>
        <td></td>
      </tr>";
        Forma_CampoOculto('fl_clase_grupo'.$j, $fl_clase_grupo);
    }
    Forma_CampoOculto('total_grupales', $total_grupales);
    Forma_Tabla_Fin();
    
    */

    $year_current_actu = date('Y-m');
    $actual = strtotime($year_current_actu);
    $mesmenos = date("Y-m", strtotime("-1 month", $actual));

    



    # Periodos guardados y actual
    echo "
  <div class='row'>
    <label class='col col-sm-4 text-align-right'><strong>".ObtenEtiqueta(713).":</strong></label>
    <div class='col col-sm-4'>
      <select id='fe_periodo' name='fe_periodo' class='select2' onclick='VerificaTarifas();' >
        <option value='0'>".ObtenEtiqueta(70)."</option>";
    # Si ya existen registros mostrar estos periodos pero seleccionara el actual
    # Si no hay registros seleccionara el mes y anio actual
    $Query = "SELECT DISTINCT DATE_FORMAT(fe_periodo,'%m-%Y'), DATE_FORMAT(fe_periodo,'%M, %Y'),DATE_FORMAT(fe_periodo,'%Y-%m') FROM k_maestro_pago WHERE fl_maestro=$clave ORDER BY fe_periodo DESC";
    $rs = EjecutaQuery($Query);
    $registros = CuentaRegistros($rs);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
        echo "<option value='".$row[0]."'"; if($row[2]==$mesmenos) echo "selected"; echo ">".$row[1]."</option>";
    }
    # Si no existe registros tanto del mes actual como el anterior mostrar el que esa enviando 
    # Si hay registros pero el que esta recibiendo no existe mostrar el que existe y el que esta recibiendo
    # si ya hay registros del mes anterior y actual esta parte no funcionara
    $row1 = RecuperaValor("SELECT count(*) FROM k_maestro_pago WHERE fl_maestro=$clave AND DATE_FORMAT(fe_periodo,'%Y-%m')='".$mesmenos."'");
    if(( (empty($row1[0])) AND  (!empty($registros)) ) OR ((empty($row1[0]))AND (empty($registros)))){
        $fe_periodoS = strftime("%B, %Y", strtotime(date('d')."-".$fe_periodo));// mostara el nombre mes y el ano actual
        echo "<option value='".$fe_periodo."'";
        if(!$fg_error AND !empty($clave)) echo "selected";
        if($fg_error) echo "selected";
        echo ">".$fe_periodoS."</option>";
    }
    echo "
      </select>
    </div>
  </div>";
    

echo"<div id='rates' name='rates'></div>";

echo"<script>

function VerificaTarifas() {
    

     $.ajax({
        type: 'POST',
        url: 'buscatarifas.php',
        data: 'fe_periodo=' + document.getElementById('fe_periodo').value +
              '&fl_maestro= $clave',
        async: false,
        success: function (html) {
            $('#rates').html(html);
                 
        }
    });



}
VerificaTarifas();
</script>";













    # Seccion del historial del maestro
    Forma_Seccion('Teacher history');
    Forma_Espacio();
    
    $titulos = array(ObtenEtiqueta(360), ObtenEtiqueta(381), ObtenEtiqueta(420),
                 ObtenEtiqueta(296));
    $ancho_col = array('30%', '15%', '15%', '20%');
    Forma_Tabla_Ini('80%', $titulos, $ancho_col);
    $Query  = "SELECT nb_programa, nb_periodo, nb_grupo, Concat(e.ds_nombres,' ',e.ds_apaterno,' ', e.ds_amaterno) ds_alumno ";
    $Query .= "FROM k_alumno_historia a, c_programa b,  c_periodo c, c_grupo d, c_usuario e ";
    $Query .= "WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=c.fl_periodo AND a.fl_grupo = d.fl_grupo ";
    $Query .= "AND  a.fl_alumno=e.fl_usuario AND a.fl_maestro=$clave ORDER BY a.fe_inicio ";
    $rs = EjecutaQuery($Query);
    for($i=0;$row= RecuperaRegistro($rs);$i++){
      $nb_programa = $row[0];
      $nb_periodo = str_texto($row[1]);
      $nb_grupo = $row[2];
      $ds_alumno = str_texto($row[3]);
      
      if($i % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      
      echo "
      <tr class='$clase'>
        <td>".$nb_programa."</td>
        <td>".$nb_periodo."</td>
        <td>".$nb_grupo."</td>
        <td>".$ds_alumno."</td>
      </tr>";

      
    }
    Forma_Tabla_Fin();
  }
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_MAESTROS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
  echo "
  <script>
  function super_user(ds_log){
    document.super.ds_login.value  = ds_log;
    document.super.ds_password.value  = '".ObtenConfiguracion(40)."';
    document.super.fg_campus.value  = '1';
    document.super.action = '../../../login_validate.php';
    document.super.submit();
  }

 function ActualizaTCG(no_reg,fl_grupo,fl_clase_grupo){


     var mn_monto=document.getElementById('mn_grupal_fee_'+no_reg).value;
     var fl_maestro='$clave';
        $.ajax({
		    type: 'POST',
		    url : 'iu_tarifasgg.php',
		    data: 'fl_clase_grupo='+fl_clase_grupo+
                  '&fl_maestro='+fl_maestro+
                  '&fl_grupo='+fl_grupo+
		          '&mn_monto='+mn_monto,
		    async: false,
		    success: function(html) {
				
				
		    }
	    });



}

  </script>
  <form name='super' method='post' target='_blank'>
    <input type=hidden name=ds_login>
    <input type=hidden name=ds_password>      
    <input type=hidden name=fg_campus>      
  </form>";
  
?>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo ObtenEtiqueta(126);?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id='updatepass' >
        
      </div>
      
    </div>
  </div>
</div>




