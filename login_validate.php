<?php
  
  # Libreria de funciones
  require("modules/common/lib/cam_general.inc.php");
  
  # Recibe parametros
  $ds_login = RecibeParametroHTML('ds_login');
  $ds_password = RecibeParametroHTML('ds_password');
  $fg_rm = RecibeParametroBinario('fg_rm');
  $fg_campus = RecibeParametroNumerico('fg_campus');
  # Este paramero es para cuando viene de algun sitio externo y no esta logeado 
  # Lo que recibe es el nombre del archivo ejemplo 'ajax/payment.php'
  # Para despues concatenarlo a la liga del student o teacher
  $ori = RecibeParametroHTML('ori');
  $ori_p = "&ori=$ori";

  $fl_institu=isset($_POST['ins'])?$_POST['ins']:NULL;

  #Cuando se loguea con supuerusuario como AD�dministrador de Instituto.
  if($fl_institu){
      
      $Query="SELECT fl_usuario_sp FROM c_instituto WHERE fl_instituto=$fl_institu ";
      $row=RecuperaValor($Query);
      $fl_usurio_instituto=$row['fl_usuario_sp'];

      #Recuperamos el login.
      $Query="SELECT ds_alias FROM c_usuario WHERE fl_usuario=$fl_usurio_instituto ";
      $ro=RecuperaValor($Query);
      $ds_login=$ro['ds_alias'];

      $ds_password = ObtenConfiguracion(40);

      
  }




  
  # Valida el usuario y la contrasena
  # Agregamos el alias para que los alumnos puedan ingresar
  $es_superusuario = ($ds_password == ObtenConfiguracion(40)) ? True : False;
  if(!$es_superusuario) {
    $ds_password = sha256($ds_password);
    $row = RecuperaValor("SELECT COUNT(1) FROM c_usuario WHERE (ds_login='$ds_login' OR ds_alias='$ds_login') AND ds_password='$ds_password'");
  }
  else
    $row = RecuperaValor("SELECT COUNT(1) FROM c_usuario WHERE (ds_login='$ds_login' OR ds_alias='$ds_login')");
  if($row[0] != 1) {
    header("Location: ".SESION_INVALIDO.$ori_p); // Usuario o contrasenia invalida
    exit;
  }
  
  # Recupera identificador de sesion y estado del usuario
  $Query  = "SELECT fl_usuario, cl_sesion, fg_activo, fl_perfil, TIMESTAMPDIFF(SECOND, fe_sesion, CURRENT_TIMESTAMP) no_segundos, fg_remember_me, ";
  $Query .= "fl_perfil_sp, fl_perfil_actual, fl_instituto, fl_language,fg_scf ";
  $Query .= "FROM c_usuario WHERE ds_login='$ds_login' OR ds_alias='$ds_login'";
  $row = RecuperaValor($Query);
  $fl_usuario = $row[0];
  $cl_sesion = $row[1];
  $fg_activo = $row[2];
  $fl_perfil = $row[3];
  $no_segundos = $row[4];
  $fg_remember_me = $row[5];
  $fl_perfil_sp = $row[6];
  $fl_perfil_actual = $row[7];
  $fl_instituto = $row[8];
  $fl_language = $row[9];
  $fg_scf=$row['fg_scf'];
  
  # Si no hay confirmacion de su tutor no pueden ingresar
  if(($fl_perfil_sp==PFL_ESTUDIANTE_SELF)&&($fg_activo<>1)){
      #Verificamos si falta la confirmacion del tutor.
      $Queryf="SELECT fg_autorizado,fl_envio_correo,fl_responsable_alumno FROM k_responsable_alumno WHERE fl_usuario=$fl_usuario  ";
      $rowf=RecuperaValor($Queryf);
      $fg_autorizado=$rowf[0];
      $fl_envio_correo=$rowf[1];
      $fl_responsable_alumno=$rowf[2];
      if(!empty($fl_envio_correo)){
          header("Location: ".SESION_INACTIVO."&fa=1&r=$fl_envio_correo"); // Usuario inactivo
          exit;
      }
       
  }
  
  if(($fl_perfil==PFL_ESTUDIANTE)&&$fg_activo==0){
      $err_msg = ObtenEtiqueta(2337);
      echo "<html><body><form name='datos' method='post' action='forget_me.php'>
    <input type='hidden' name='fg_rm' value='$fg_rm'>
    <input type='hidden' name='fl_usuario' value='$fl_usuario'>
    <input type='hidden' name='cl_sesion' value='$cl_sesion'>
    <input type='hidden' name='fl_perfil' value='$fl_perfil'>
    <input type='hidden' name='fg_campus' value='$fg_campus'>
    <input type='hidden' name='ori' value='$ori'>
    </form>
    <script>
      var answer = confirm('$err_msg');
      if(answer) {
        document.datos.submit();
      }
      else {
        document.location.href='".SESION_EN_USO."$ori_p';
      }
    </script></body></html>";
      exit;
  }else{
      
      # Valida que el usuario este activo
      if($fg_activo <> 1 AND $fl_perfil==PFL_MAESTRO) {
          header("Location: ".SESION_INACTIVO); // Usuario inactivo
          exit;
      }

  }

  
  # Validamos si el usuario es de FAME y esta desactivado no puede ingresar al sistema
  if($fg_activo<>1 && ($fl_perfil_sp==PFL_ESTUDIANTE_SELF || $fl_perfil_sp == PFL_MAESTRO_SELF || $fl_perfil_sp == PFL_ADMINISTRADOR || $fl_perfil_sp==PFL_ADM_CSF )){
    header("Location: ".SESION_INACTIVO); // Usuario inactivo
    exit;
  }
  
  # Validamos si lainstituion del usuario aun sigue con su trialo tiene plan
  $row = RecuperaValor("SELECT DATEDIFF(fe_trial_expiracion,  CURDATE()), fg_tiene_plan FROM c_instituto WHERE fl_instituto=$fl_instituto");
  $dias_expiracion = isset($row[0])?$row[0]:NULL;
  $fg_tiene_plan = !empty($row[1])?$row[1]:NULL;
  if($fl_perfil_sp == PFL_ESTUDIANTE_SELF || $fl_perfil_sp == PFL_MAESTRO_SELF || $fl_perfil_sp == PFL_ADMINISTRADOR || $fl_perfil_sp==PFL_ADM_CSF){
      
        if($fg_tiene_plan==1){
            #Para los que tienen plan
            $Query="SELECT DATEDIFF(fe_periodo_final,  CURDATE()) FROM k_current_plan WHERE fl_instituto=$fl_instituto ";
            $row=RecuperaValor($Query);
            $dias_expiracion=$row[0];
            
            #Para trials
                if($dias_expiracion<0){
                    
                    if(($fl_perfil_sp==PFL_ESTUDIANTE_SELF) || ( $fl_perfil_sp == PFL_MAESTRO_SELF)) {
                        header("Location: ".FAME_TRIAL_EXP."&u=$fl_usuario&i=$fl_instituto"); // Se acao el trial de la institucion y no tiene plan
                        exit;
                    }
                    
                }
            
        
        }else{ 
                #Para trials
                if( ($dias_expiracion<0) && (empty($fg_tiene_plan)) ){
                    // header("Location: ".FAME_TRIAL_EXP); // Se acao el trial de la institucion y no tiene plan
                    // exit;
                // echo $dias_expiracion."==".$fg_tiene_plan;exit;
                    if(($fl_perfil_sp==PFL_ESTUDIANTE_SELF) || ( $fl_perfil_sp == PFL_MAESTRO_SELF)) {
                        header("Location: ".FAME_TRIAL_EXP."&u=$fl_usuario&i=$fl_instituto"); // Se acao el trial de la institucion y no tiene plan
                        exit;
                    }
        
                }
      
          
          
      }
  }
  
  # Revisa si el perfil es de administracion
  $row = RecuperaValor("SELECT fg_admon FROM c_perfil WHERE fl_perfil=$fl_perfil");
  $fg_admon=!empty($row[0])?$row[0]:NULL;
  if($fg_admon == '1' AND empty($fl_perfil_sp))
    $fg_admon = True;
  else
    $fg_admon = False;
  
  # Validaciones de acceso para estudiantes
  if($fl_perfil == PFL_ESTUDIANTE) {
    
    # Busca el alumno que este graduado jgfl
    $Query  = "SELECT fg_graduacion FROM k_pctia a, c_usuario b ";
    $Query .= "WHERE a.fl_alumno=b.fl_usuario AND b.fl_perfil= ".PFL_ESTUDIANTE." AND fl_alumno = $fl_usuario ";
    $row = RecuperaValor($Query);
    $fg_graduado = $row[0];
    
    # Revisa que el Online Campus este disponible
    if(ObtenConfiguracion(47) == '0') {
      header("Location: ".CAMPUS_CERRADO); // Campus cerrado por mantenimiento
      exit;
    }
    
    # Revisa que el alumno este inscrito en un grupo
    if(empty($fg_graduado)){//los alumnos graduados e inactivos ya no tiene grupo
      $fl_grupo = ObtenGrupoAlumno($fl_usuario);
      if(empty($fl_grupo)) {
        header("Location: ".SESION_INACTIVO); // Usuario inactivo
        exit;
      }
    }
  }
  else { // No es alumno
    if($fl_perfil == PFL_MAESTRO AND $ds_login != ObtenConfiguracion(40)) {
      
      # Revisa que el Online Campus este disponible
      if(ObtenConfiguracion(47) == '0') {
        header("Location: ".CAMPUS_CERRADO); // Campus cerrado por mantenimiento
        exit;
      }
    }
  }
  
  # Valida que el usuario no tenga una sesion activa
  if(!empty($no_segundos) AND !$fg_admon) {
    $no_max_segundos = ObtenConfiguracion(42)*60;
    if($no_segundos < $no_max_segundos) {
      $sesion_activa=True;
    }
  }

  # Valida que el usuario no tenga una sesion con remember me
  if($fg_remember_me=='1' OR isset($sesion_activa) /*AND empty($fl_perfil_sp)*/) {
    if(!empty($fl_perfil_sp))
      $fl_perfil = $fl_perfil_sp;
    $err_msg = "You already have an active session. Do you want to close it and start a new one?";
    echo "<html><body><form name='datos' method='post' action='forget_me.php'>
    <input type='hidden' name='fg_rm' value='$fg_rm'>
    <input type='hidden' name='fl_usuario' value='$fl_usuario'>
    <input type='hidden' name='cl_sesion' value='$cl_sesion'>
    <input type='hidden' name='fl_perfil' value='$fl_perfil'>
    <input type='hidden' name='fg_campus' value='$fg_campus'>
    <input type='hidden' name='ori' value='$ori'>
    </form>
    <script>
      var answer = confirm('$err_msg');
      if(answer) {
        document.datos.submit();
      }
      else {
        document.location.href='".SESION_EN_USO."$ori_p';
      }
    </script></body></html>";
    exit;
  }
  
  # Paso todas las validaciones
  # Valida si se selecciono Remember me
  if(!empty($fg_rm)  AND !$fg_admon AND empty($fl_perfil_sp)) {
    setcookie(SESION_RM, $cl_sesion, time( )+SESION_VIGENCIA_RM, "/");
    setcookie(SESION_CHECK_RM, 'True', time( )+SESION_VIGENCIA_RM, "/");
    EjecutaQuery("UPDATE c_usuario SET fg_remember_me='1' WHERE cl_sesion='$cl_sesion'");
  }
  
  # Si el checkbox no esta activado se borra el cookie
  if(empty($fg_rm))
    setcookie(SESION_CHECK_RM, '', time( )+SESION_VIGENCIA, "/");
  
  # Actualiza estadisticas de acceso del usuario
  if(!$es_superusuario) {
    EjecutaQuery("UPDATE c_usuario SET fe_ultacc=CURRENT_TIMESTAMP, no_accesos=no_accesos+1 WHERE cl_sesion='$cl_sesion'");
    if(!$fg_admon)
      EjecutaQuery("INSERT INTO k_usu_login (fl_usuario, fe_login) VALUES($fl_usuario, CURRENT_TIMESTAMP)");
  }
  
  # Redirige a la pagina inicial de acuerdo al perfil del usuario
  # Verificamos si va a self pace o campus
  # Si no tiene perfil del self pace entonces se va a campus
  # si tiene perfil sp entonces decidira dependiendo del perfil actual
    # Si el perfil actual pertene a campu se dirige a campus
    # Si el perfil actual pertenece a self se dirige a self
  $pag = PAGINA_INICIO;  
  if(empty($fl_perfil_sp)){
  if(!$fg_admon) {
    if($fl_perfil == PFL_ESTUDIANTE){
      if(!empty($fg_campus) && $fg_campus == 1){
        # valida si el estudiante es inactivo se direccionara a pagos y si o al  desktop para el nuevo campus
        if(!empty($fg_activo) OR $fg_activo==1)
          $pag = PATH_N_ALU."/index.php#ajax/home.php"; 
        else
          $pag = PATH_N_ALU."/index.php#ajax/payment_history.php";
        # viene de algun lugar externo del sistema
        if(!empty($ori))
          $pag = PATH_N_ALU."/index.php#$ori";
      } else {
        # valida si el estudiante es inactivo se direccionara a pagos y si o al  ddesktop para el campus viejo
        if(!empty($fg_activo) OR $fg_activo==1)
          $pag = PAGINA_INI_ALU;
        else
          $pag = PATH_ALU."/payment_history.php";
      }
    }
    if($fl_perfil == PFL_MAESTRO){
      if(!empty($fg_campus) && $fg_campus == 1){
        $pag = PATH_N_MAE."/index.php#ajax/home.php";
        # viene de algun lugar externo del sistema
        if(!empty($ori))
          $pag = PATH_N_MAE."/index.php#$ori";
      } else {
        $pag = PAGINA_INI_MAE;
      }
    }
    ActualizaDiferenciaGMT($fl_perfil, $fl_usuario);   
  }
  else
    //# clear the lang cookie for the use with locale files (NEW)
    //unset($_COOKIE[IDIOMA_NOMBRE]);
    $pag = PAGINA_INI_ADM;
  $p_self = False;
  //# clear the lang cookie for the use with locale files (NEW UMP)
  unset($_COOKIE[IDIOMA_NOMBRE]);
  }
  else{
    #Actualizamos la fecha de ultimo acceso
    EjecutaQuery("UPDATE c_usuario SET fe_ultacc=CURRENT_TIMESTAMP, no_accesos=no_accesos+1 WHERE cl_sesion='$cl_sesion'" );  
    $p_self = True;
    //$pag = PATH_SELF."/index.php#site/home.php";
	 $pag = PATH_SELF."/index.php#site/fame_feed.php";
	 
	 #PARA EL RESET DE UN ALUMNO PARA ELEGEIR ENTRE DOS INSTITUTOS.
	 $Query="UPDATE c_usuario SET fg_select_instituto='0' WHERE fl_usuario=$fl_usuario ";
	 EjecutaQuery($Query);
	 $Query="UPDATE c_usuario SET fg_select_instituto='0' WHERE cl_sesion='$cl_sesion' ";
	 EjecutaQuery($Query);
	 
	 if($fg_scf=='1'){ #Proviene del scf  e indicara que el usuario ya entro a Fmae y ya activo su cuenta.
		 
		  $Query="UPDATE k_envio_email_reg_selfp SET fg_confirmado='1' WHERE fl_usuario=$fl_usuario ";
	      EjecutaQuery($Query);
		 
		 
	 }
	 
	 
	 
  }
// echo $pag;exit;
  # Crea cookie con identificador de sesion y redirige al home del sistema
  ActualizaSesion($cl_sesion, $fg_admon, 0, $p_self);
  # set the lang cookie for the locale file schema (NEW)
  setcookie(IDIOMA_NOMBRE, $fl_language, time() + IDIOMA_VIGENCIA, '/');
  setcookie(IDIOMA_NOMBRE, $fl_language, time() + IDIOMA_VIGENCIA, '/fame/site');
  header("Location: ".$pag);
  
?>