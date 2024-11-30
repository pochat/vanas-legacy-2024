<?php
# Libreria de funciones
require("../lib/self_general.php");

// $fl_insituto = ObtenInstituto($fl_usuario);
$fl_usuario = ValidaSesion(False, 0, True);
$fl_action = RecibeParametroNumerico('fl_action');
$fl_usuario_std = RecibeParametroHTML('fl_usuario');

# El nb_grupo puede recibir un grupo o un programa
$nb_grupo = RecibeParametroHTML('nb_grupo');

# Recibimos el play list selecconado.
$fl_playlist_select = RecibeParametroHTML('fl_playlist');
$new_group = RecibeParametroHTML('new_group');
$fl_programa_sp_select = $_POST['fl_playlist']??NULL;
$fl_usu_pro = $_POST['fl_usu_pro']??NULL;



#Si se elige un play list no puede traer valor de programa.
if ($fl_playlist_select) {
  $nb_grupo = "";
  $fl_usuario_std = $fl_usuario_std;
}

# Si el teacher quiere agregar un nuevo grupo para el usuario(s)
if ($nb_grupo == "ADDGRP") {
  $nb_grupo = RecibeParametroHTML("new_group");
  if (empty($nb_grupo))
    $nb_grupo = $_POST['new_group'];
}

$confirmado = RecibeParametroNumerico('confirmado');
$fl_perfil_user = RecibeParametroNumerico('fl_perfil_user');
$asignar = RecibeParametroNumerico('asignar');

# Este email es necesario
$from = ObtenConfiguracion(4);
# Enviamos copia a fame
$from_fame = ObtenConfiguracion(107);

#Verificamos si existe ese usuario .
$Quey = "SELECT fl_perfil_sp, fg_activo, fl_perfil_sp  FROM c_usuario WHERE fl_usuario=$fl_usuario_std ";
$ro = RecuperaValor($Quey);
$existe_usuario = $ro['fl_perfil_sp']??NULL;
$fg_activo = $ro['fg_activo']??NULL;
$fl_perfil_sp = $ro['fl_perfil_sp']??NULL;

#Verificamos si tiene un plan:
$fl_insitut = ObtenInstituto($fl_usuario);
$fg_tiene_plan = Obten_Status_Trial($fl_insitut);

#2020 -sep  verificamos que el instituto no sea b2c.
$Query="SELECT fg_b2c,ds_instituto FROM c_instituto WHERE fl_instituto=$fl_insitut ";
$row=RecuperaValor($Query);
$fg_b2c=$row[0];
$nb_instituto=$row['ds_instituto'];

#Liberamos las licencias.
if ($fg_tiene_plan <> "") {
  $no_licencias_disponibles = ObtenNumLicenciasDisponibles($fl_insitut);
  $no_licencias_usadas = ObtenNumLicenciasUsadas($fl_insitut);
} else {
  $tot_licencias = ObtenConfiguracion(102);

  # Licencias activadas sin contar al administrador
  $avaible = ObtenNumeroUserInst($fl_instituto??NULL);
  $no_licencias_disponibles = $tot_licencias - $avaible;
}

if (($fl_action == RESEND_EMAIL_INVITATION) || ($fl_action == CSF_RESEND_EMAIL_INVITACION)) {

  #Recuperamos el nombre del instituto.
    
  # EL DATO VENDRA CON UN -NC de no confrimado el email entonces se realiza la siguinete funcion pára determinar el registro a modificar.
  $total2 = substr_count($fl_usuario_std, '-');
  $valor2 = explode("-", $fl_usuario_std);
  $clve_envio = $valor2[1];
  for ($im = 0; $im <= $total2; $im++) {

    $fl_usuario_std = $valor2[$im];

    if (is_numeric($fl_usuario_std)) {

      if ($fl_action == CSF_RESEND_EMAIL_INVITACION) {
        $fl_insituto = ObtenInstituto($fl_usuario);
        #Recuperamos el email del ya del usuario 
        $Query = "SELECT ds_email,ds_nombres,ds_apaterno,ds_alias,fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario_std  ";
        $ro = RecuperaValor($Query);
        $ds_email_destinatario = str_texto($ro['ds_email']);
        $first_name = str_texto($ro['ds_nombres']);
        $last_name_pat = str_texto($ro['ds_apaterno']);
        $username = str_texto($ro['ds_alias']);
        $fl_perfil_sp_=$ro['fl_perfil_sp'];

        if($fl_perfil_sp_==PFL_MAESTRO_SELF){
            $role="Teacher";
        }
        if($fl_perfil_sp_==PFL_ESTUDIANTE_SELF){
            $role="Student";
        }

        #Recuperamos el teacher que lo esta invitando.
        #Recuperamos el email del ya del usuario 
        $Query = "SELECT ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario  ";
        $ro = RecuperaValor($Query);
        $user_fname_invitador = str_texto($ro['ds_nombres']);
        $user_lname_invitador = str_texto($ro['ds_apaterno']);

        #Se generan registros en FAME.
        #Generamos su pasword temporal.
        $ds_pass = substr(md5(microtime()), 1, 8);

        EjecutaQuery('UPDATE c_usuario SET ds_password="' . sha256($ds_pass) . '" WHERE fl_usuario=' . $fl_usuario_std . ' ');

        # Genera un identificador de sesion
        $cl_sesion_nueva = sha256($username . $first_name . $last_name_pat . $ds_pass);

        #Recuperamos el ultimo id del correo para saber y llevar su bitacora.
        $Query = "SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
        $row = RecuperaValor($Query);
        $no_envio = $row[0];
        $no_envio = $no_envio + 1;

        # Genera una nueva clave para la liga de acceso al contrato
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $ds_cve = "";
        for ($i = 0; $i < 40; $i++)
          $ds_cve .= substr($str, rand(0, 62), 1);
        $ds_cve .= date("Ymd") . $no_envio;

        #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
        $no_codigo_confirmacion = substr("$ds_cve", -30, 30);

        # Obtenmos el template
        $ds_header = genera_documento_sp('', 1, 171);
        $ds_body = genera_documento_sp('', 2, 171);
        $ds_footer = genera_documento_sp('', 3, 171);
        $ds_mensaje = $ds_header . $ds_body . $ds_footer;


        $dominio_campus = ObtenConfiguracion(116);
        $ds_mensaje = str_replace("#nb_instituto#", $nb_instituto, $ds_mensaje);  #last name a quien envia correo
        $ds_mensaje = str_replace("#fame_fname#", $first_name, $ds_mensaje);
        $ds_mensaje = str_replace("#fame_lname#", $last_name_pat, $ds_mensaje);

        $ds_mensaje = str_replace("#fame_fname_friends#", $user_fname_invitador, $ds_mensaje);
        $ds_mensaje = str_replace("#fame_lname_friends#", $user_lname_invitador, $ds_mensaje);

        $ds_mensaje = str_replace("#fame_username#", $username, $ds_mensaje);
        $ds_mensaje = str_replace("#fame_password#", $ds_pass, $ds_mensaje);

        $ds_mensaje = str_replace("#fame_link#", $dominio_campus, $ds_mensaje);

        # Nombre del template
        $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=171 AND fg_activo='1'";
        $template = RecuperaValor($Query0);
        $subject = str_uso_normal($template[0]);

        # Este email es necesario
        $from = ObtenConfiguracion(107); #de donde sale el email.

        # Enviamos el correo al usuario dependiendo de la accion
        $send_email = EnviaMailHTML($from, $from, $ds_email_destinatario, $subject, $ds_mensaje);

        EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE ds_email='$ds_email_destinatario' ");
        EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_usuario_std ");

        #Si efectivamenete se envio el email entonces se guarda la bitacora de envio
        $Query = "INSERT INTO k_envio_email_reg_selfp (fg_confirmado,ds_first_name,ds_last_name,ds_email,no_registro,fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod,fl_usu_invita,fg_desbloquear_curso,fl_friends_invitation,fg_feed,fe_expiracion,ds_cupon,fg_scfenvio_email,fl_usuario,fg_scf)";
        $Query .= "values('0','$first_name','$last_name_pat','$ds_email_destinatario','$no_codigo_confirmacion','S',$fl_insituto,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usuario,'0',0,'','','','1',$fl_usuario_std,'1')";
        $fl_envio_ = EjecutaInsert($Query);

        $result['status'] = 1;
        $result['fl_action'] = CSF_RESEND_EMAIL_INVITACION;
        $result['ds_email'] = $ds_email_destinatario;
        $result['ds_mensaje'] = "" . ObtenEtiqueta(2314) . "";

        echo json_encode((object) $result);
      }

      if ($fl_action == RESEND_EMAIL_INVITATION) {

        if ($clve_envio <> 'FA') {

          #Renviamos el email con nuevo codigo de confirmacion al mismo usuario. 
          $Query = "SELECT ds_first_name,ds_last_name,  ds_email,fl_perfil_sp FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_usuario_std ";
          $rou = RecuperaValor($Query);
          $ds_email = str_texto($rou['ds_email']);
          $ds_first_name = str_texto($rou['ds_first_name']);
          $ds_last_name = str_texto($rou['ds_last_name']);
          $fl_perfil_sp_=$rou['fl_perfil_sp'];

          if($fl_perfil_sp_==PFL_MAESTRO_SELF){
              $role="Teacher";
          }
          if($fl_perfil_sp_==PFL_ESTUDIANTE_SELF){
              $role="Student";
          }


          if(empty($ds_email)){
              #Recuperamos el email del ya del usuario 
              $Query="SELECT ds_email,ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario_std  ";
              $ro=RecuperaValor($Query);
              $ds_email=str_texto($ro['ds_email']);
              $ds_first_name = str_texto($ro['ds_nombres']);
              $ds_last_name = str_texto($ro['ds_apaterno']);

              #Recuperamos el fl_Envio_correo atraves del usuario.(user inactives)
              $Query="SELECT fl_envio_correo FROM k_envio_email_reg_selfp where fl_usuario=$fl_usuario_std ";
              $row=RecuperaValor($Query);
              $fl_usuario_std=$row['fl_envio_correo'];
          }

          $Query = "SELECT ds_nombres,ds_apaterno,ds_email,fl_perfil_sp FROM c_usuario WHERE fl_usuario=$fl_usuario  ";
          $ro = RecuperaValor($Query);
          $user_fname_invitador = str_texto($ro['ds_nombres']);
          $user_lname_invitador = str_texto($ro['ds_apaterno']);
          $user_email_invitador=str_texto($ro['ds_email']);
          $fl_perfil_sp_=$ro['fl_perfil_sp'];

          if($fl_perfil_sp_==PFL_MAESTRO_SELF){
              $role="Student";
          }
          if($fl_perfil_sp_==PFL_ADMINISTRADOR){
              $role="Teacher";
          }

          

          #Revuperamos el ultimo id del correo para saber y llevar su bitacora.
          $Query = "SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
          $row = RecuperaValor($Query);
          $no_envio = $row[0];
          $no_envio = $no_envio + 1;

          # Genera una nueva clave para la liga de acceso al contrato
          $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
          for ($i = 0; $i < 40; $i++)
            $ds_cve .= substr($str, rand(0, 62), 1);
          $ds_cve .= date("Ymd") . $no_envio;

          #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
          $no_codigo_confirmacion = substr("$ds_cve", -30, 30);

          #se genera el cuerpo del documento de email
          $ds_encabezado = genera_documentoSP($clave, 1, True, '', '', 100, $ds_cve, $ds_first_name, $ds_last_name);
          $ds_cuerpo = genera_documentoSP($clave, 2, True, '', '', 100, $ds_cve, $ds_first_name, $ds_last_name);
          $ds_pie = genera_documentoSP($clave, 3, True, '', '', 100, $ds_cve, $ds_first_name, $ds_last_name);

          $template_email = $ds_encabezado . $ds_cuerpo;
          $template_email .= $ds_pie;
          $ds_contenido = $template_email;
          $invitado_por=$user_fname_invitador." ".$user_lname_invitador;


          $ds_contenido=str_replace("#perfil_user#",$role,$ds_contenido);
          $ds_contenido = str_replace("#nb_instituto#", $nb_instituto, $ds_contenido);  #last name a quien envia correo
          $ds_contenido = str_replace("#fame_fname_invited#", $invitado_por, $ds_contenido); # first name a quein se le envia el correo
          $ds_contenido = str_replace("#fame_lname_invited#", $ds_lname_invitador, $ds_contenido);  #bont link redireccion 

          $nombre_quien_escribe = $ds_first_name . " " . $ds_last_name;

          $ds_email_de_quien_envia_mensaje = ObtenConfiguracion(107);
          $ds_email_destinatario = $ds_email;
          $nb_nombre_dos = ObtenEtiqueta(949); #nombre de quien envia el mensaje

          $message  = $ds_contenido;
          $message = utf8_decode(str_ascii(str_uso_normal($message)));
          $ds_titulo = ObtenEtiqueta(950); #etiqueta de asunto del mensjae para el anunciante
          $bcc = ObtenConfiguracion(107);

          $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
          //Se envia copia de email al usuario que esta realizando este rsend.
          $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $user_email_invitador, $ds_titulo, $message);
          

          $copy_send_email = ObtenConfiguracion(131);
          if (!empty($copy_send_email)) {

            $bcc = $copy_send_email;

            #Se cuelve enviar la invitacion desde otro correo
            $mail = EnviaMailHTML($nb_nombre_dos, $copy_send_email, $ds_email_destinatario, $ds_titulo, $message, $copy_send_email);
          }
          
          if(!empty($mail)) {

            #Actualizamos anteiror enviado y otra ves pasa ser fg_activo='0'
            $Query = "UPDATE  k_envio_email_reg_selfp SET  no_registro='$no_codigo_confirmacion',fg_confirmado='0',fe_reenvio=CURRENT_TIMESTAMP , fe_ultmod=CURRENT_TIMESTAMP WHERE  fl_envio_correo=$fl_usuario_std ";
            EjecutaQuery($Query);

            $result['status'] = 1;
            $result['fl_action'] = RESEND_EMAIL_INVITATION;
            $result['ds_email'] = $ds_email;
            $result['ds_mensaje'] = "" . ObtenEtiqueta(2314) . "";

            echo json_encode((object) $result);
          }
        } else {
        }
      } #end if case resend email.
    }
  }
}

#Actualizamos el teacher 
if ($fl_action == ASG_TEACHER) {

  $fl_usuario = $_POST[''];
  $fl_usuario_teacher = RecibeParametroNumerico('fl_teacher');
  $fl_usuario_sp = RecibeParametroNumerico('fl_usuario');
  $fl_programa_sp = RecibeParametroNumerico('fl_programa_std');

  if (!empty($fl_usuario_teacher)) {

    $Query = "UPDATE k_usuario_programa SET  fl_maestro=$fl_usuario_teacher WHERE fl_usuario_sp=$fl_usuario_sp ";
    if($fl_programa_sp)
    $Query .="AND fl_programa_sp=$fl_programa_sp ";
    EjecutaQuery($Query);

    $result['status'] = 1;
    $result['ds_title'] = " &nbsp;" . ObtenEtiqueta(2329);

    echo json_encode((object) $result);
  }
}

if ($fl_action != VERIFCA_USUARIO_PROGRAMA) {
  switch ($fl_action) {

    case ACTIVE:
      if ((!empty($confirmado) && $fg_activo == 0)) {

        if (!empty($fg_tiene_plan)) {
          if ($no_licencias_disponibles > 0) {

            if ($existe_usuario && $fl_perfil_sp == PFL_ESTUDIANTE_SELF) {

              $nuevo_no_licencias_disponibles = $no_licencias_disponibles - 1;
              $nuevo_no_licencias_usadas = $no_licencias_usadas + 1;

              $Quer = "UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_no_licencias_disponibles ,no_licencias_usadas=$nuevo_no_licencias_usadas WHERE fl_instituto=$fl_insitut ";
              EjecutaQuery($Quer);
            }
          }
        }
        $Query = "UPDATE c_usuario SET fg_activo='1' WHERE fl_usuario=$fl_usuario_std";
      }
      break;

    case DESACTIVE:
      # Verificamos que el usuario este activado y confirmado
      if (!empty($confirmado) && $fg_activo == 1) {

        if (!empty($fg_tiene_plan)) {
          if ($existe_usuario && $fl_perfil_sp == PFL_ESTUDIANTE_SELF) {
           
           #Se realiza operacion de liberacion de licencias.
            $nuevo_no_licencias_disponibles = $no_licencias_disponibles + 1;
            $nuevo_no_licencias_usadas = $no_licencias_usadas - 1;

            $Quer = "UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_no_licencias_disponibles ,no_licencias_usadas=$nuevo_no_licencias_usadas WHERE fl_instituto=$fl_insitut ";
            EjecutaQuery($Quer);
          }
        }

        $Query = "UPDATE c_usuario SET fg_activo='0' WHERE fl_usuario=$fl_usuario_std";
      }
      break;

    case DELETE:

      # Confirmado entonces lo eliminara del c_usuario
      # No confirmado lo eliminara del k_envio_email_reg_selfp

      if (!empty($confirmado)) {

        if (!empty($fg_tiene_plan)) {

          if(!empty($existe_usuario)) {

            #Se realiza operacion de licnecias.
            $nuevo_no_licencias_disponibles = $no_licencias_disponibles + 1;
            $nuevo_no_licencias_usadas = $no_licencias_usadas - 1;

            $Quer = "UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_no_licencias_disponibles ,no_licencias_usadas=$nuevo_no_licencias_usadas WHERE fl_instituto=$fl_insitut ";
            EjecutaQuery($Quer);
          }
        }

        $Query = "SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_std ";
        $row = RecuperaValor($Query);
        $ds_emaildel = str_texto($row['ds_email']);
        EjecutaQuery("DELETE FROM c_usuario WHERE fl_usuario=$fl_usuario_std");
        EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE ds_email='$ds_emaildel' ");
      } else {

        # EL DATO VENDRA CON UN -NC de no confrimado el email entonces se realiza la siguinete funcion pára determinar el registro a modificar.
        $total2 = substr_count($fl_usuario_std, '-');
        $valor2 = explode("-", $fl_usuario_std);
        for ($im = 0; $im <= $total2; $im++) {

          $fl_usuario_std = $valor2[$im];

          if (is_numeric($fl_usuario_std)) {
            EjecutaQuery("DELETE FROM c_usuario WHERE fl_usuario=$fl_usuario_std");
            $Query = "DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_usuario_std";
          }
        }
      }

      EjecutaQuery("DELETE FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario_std ");
      EjecutaQuery("DELETE FROM c_alumno_sp WHERE fl_alumno_sp=$fl_usuario_std ");
	  EjecutaQuery("DELETE FROM k_instituto_alumno WHERE fl_usuario_sp=$fl_usuario_std ");
      break;

    case ASG_GROUP:
    
      $nb_grupo = RecibeParametroHTML('nb_grupo');

      if ($nb_grupo == 'ADDGRP')
        $nb_grupo = RecibeParametroHTML('new_group');

      if(!empty($confirmado)) {
        # Buscamos el registro si no existe lo genera
        if (!ExisteEnTabla('c_alumno_sp', 'fl_alumno_sp', $fl_usuario_std)) {
          EjecutaQuery("INSERT INTO c_alumno_sp (fl_alumno_sp) VALUES ($fl_usuario)");
        }
        # Actualiza el grupo que no estaba asignado
        $Query = "UPDATE c_alumno_sp SET nb_grupo='$nb_grupo' WHERE fl_alumno_sp=$fl_usuario_std";

        #Verficia si existe.
        $Query = 'SELECT COUNT(*)FROM c_grupo_fame WHERE fl_alumno_sp=' . $fl_usuario_std . ' AND nb_grupo="' . $nb_grupo . '" ';
        if ($fl_programa_sp_select) {
          $Query .= ' AND fl_programa_sp=' . $fl_programa_sp_select . ' ';
          $rol = RecuperaValor($Query);
          if (empty($rol[0])) {
            $Query = 'INSERT INTO c_grupo_fame(nb_grupo,fl_alumno_sp,fl_usuario_creacion,fl_instituto,fl_programa_sp, fe_creacion,fe_ulmod) 
                  VALUES("' . $nb_grupo . '",' . $fl_usuario_std . ',' . $fl_usuario . ',' . $fl_insitut . ',' . $fl_programa_sp_select . ',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ';
            //$fl_group_insrt=EjecutaInsert($Query);
          }
        } else {
          #Elimina los actuales.
          EjecutaQuery("DELETE FROM c_grupo_fame WHERE fl_alumno_sp=" . $fl_usuario_std . " ");
          #Asigna grupo por programa.
          $QueryP = "SELECT fl_programa_sp,fl_usu_pro FROM k_usuario_programa WHERE fl_usuario_sp=" . $fl_usuario_std . " ";
          $rsp = EjecutaQuery($QueryP);
          for ($i = 0; $row = RecuperaRegistro($rsp); $i++) {
            $fl_program_usr = $row['fl_programa_sp'];
            $fl_usu_pro = $row['fl_usu_pro'];

            $Queryn = 'SELECT COUNT(*)FROM c_grupo_fame WHERE fl_alumno_sp=' . $fl_usuario_std . ' AND nb_grupo="' . $nb_grupo . '" ';
            $Queryn .= ' AND fl_programa_sp=' . $fl_program_usr . ' ';
            $roli = RecuperaValor($Queryn);
            if (empty($roli[0])) {
              $QuerynewGroup = 'INSERT INTO c_grupo_fame(nb_grupo,fl_alumno_sp,fl_usuario_creacion,fl_instituto,fl_programa_sp,fl_usu_pro, fe_creacion,fe_ulmod) 
                              VALUES("' . $nb_grupo . '",' . $fl_usuario_std . ',' . $fl_usuario . ',' . $fl_insitut . ',' . $fl_program_usr . ',' . $fl_usu_pro . ',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ';
              $fl_group_insrt = EjecutaInsert($QuerynewGroup);
            }
          }
        }
      } else {
        $Query = "UPDATE k_envio_email_reg_selfp SET nb_grupo='$nb_grupo' WHERE fl_envio_correo=$fl_usuario_std";
      }
      break;

    case CAM_GROUP:

      $nb_grupo = RecibeParametroHTML('nb_grupo');
      if ((empty($nb_grupo)) || ($nb_grupo == 'ADDGRP')) {
        $nb_grupo = RecibeParametroHTML('new_group');
      }
      if(!empty($confirmado)) {
        # Actualiza el grupo que no estaba asignado
        $Query = "UPDATE c_alumno_sp SET nb_grupo='$nb_grupo' WHERE fl_alumno_sp=$fl_usuario_std";
        EjecutaQuery($Query);
        #Verficia si existe.
        //$Query='SELECT COUNT(*)FROM c_grupo_fame WHERE fl_alumno_sp='.$fl_usuario_std.' AND nb_grupo="'.$new_group.'" ';
        //if($fl_usu_pro){
        //    $Query.=' AND fl_usu_pro='.$fl_usu_pro.' ';
        // }
        //$rol=RecuperaValor($Query);
        //if(empty($rol[0])){              
        //   $Query='INSERT INTO c_grupo_fame(nb_grupo,fl_alumno_sp,fl_usuario_creacion,fl_instituto,fe_creacion,fe_ulmod) 
        //       VALUES("'.$new_group.'",'.$fl_usuario_std.','.$fl_usuario.','.$fl_insitut.',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ';
        //$fl_group_insrt=EjecutaInsert($Query);
        // }else{

        $Query = 'UPDATE c_grupo_fame SET nb_grupo="' . $nb_grupo . '"  WHERE fl_alumno_sp=' . $fl_usuario_std . ' ';
        if(!empty($fl_programa_sp_select))
          $Query .= 'AND fl_programa_sp=' . $fl_programa_sp_select . ' ';
        $Query .= 'AND fl_usu_pro=' . $fl_usu_pro . ' ';

        // }

      } else {
        # Actualiza el grupo que no estaba asignado
        $Query = "UPDATE k_envio_email_reg_selfp SET nb_grupo='$nb_grupo' WHERE fl_envio_correo=$fl_usuario_std";
      }
      break;

    case ASG_COURSE:
      
      # Confirmados
      if (!empty($confirmado)) {
        if (/*!empty($nb_grupo) &&*/$nb_grupo == "tot_courses") {
          # Buscamos todos los cursos
          $query  = "SELECT fl_programa_sp,nb_programa FROM c_programa_sp a ";
          $query .= "WHERE NOT EXISTS (SELECT 1 FROM k_usuario_programa b WHERE  b.fl_usuario_sp=$fl_usuario_std AND b.fl_programa_sp=a.fl_programa_sp)";
          
          #mjd sep 2020  si es un instituto que es b2c solo mostrara los cursos adquiridos.
          if($fg_b2c==1){
              $query ="SELECT p.fl_programa_sp,a.nb_programa FROM k_orden_desbloqueo_curso_alumno p
	                    JOIN c_programa_sp a ON a.fl_programa_sp=p.fl_programa_sp AND EXISTS( SELECT 1 FROM c_leccion_sp c WHERE c.fl_programa_sp=a.fl_programa_sp  )
	                    WHERE p.fl_instituto= $fl_insitut 
                        AND NOT EXISTS(SELECT 1 FROM k_usuario_programa b WHERE  b.fl_usuario_sp=$fl_usuario_std AND b.fl_programa_sp=a.fl_programa_sp)
                        ORDER BY a.nb_programa  ";
          }
          
          $rs = EjecutaQuery($query);
          for ($i = 0; $rowq = RecuperaRegistro($rs); $i++) {
            echo "asigna courso";
            # En este programa el usuario no esta inscrito
            $fl_programa_n = $rowq[0];
            $nb_programa_n = str_texto($rowq[1]);
            $query1  = "INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, ds_progreso, no_promedio_t, fg_terminado, fl_maestro,fg_revisado_alumno,fe_creacion, flag) ";
            $query1 .= "VALUES ($fl_usuario_std, $fl_programa_n, 0, 0, '0', $fl_usuario,'0',CURRENT_TIMESTAMP, 0) ";
            $fl_usu_pro_insertado = EjecutaInsert($query1);
            # Por defaul indicamos que tendran una calificacion de quiz
            EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro_insertado,'1','0')");
			
			#Generamos su token
			$token=sha256($fl_usu_pro_insertado);
			EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado ");

            #Enviamos notificacion de asignacion de curso Node.json_decode
            echo "<script>
			  var nb_programa='$nb_programa_n';
			  socket.emit('curso-asignado', $fl_usuario_std,nb_programa);</script> ";
          }
        } else {

          if (!empty($fl_playlist_select)) {


            if ($fl_playlist == 'tot_play') {

              #Recuperamos todos los play list existenten en el combo select.
              $Query = "SELECT b.nb_playlist, b.fl_playlist  
                        FROM c_usuario a JOIN c_playlist b ON a.fl_usuario = b.fl_usuario  
                        WHERE a.fl_instituto = $fl_instituto 
                        AND a.fl_perfil_sp != " . PFL_ESTUDIANTE_SELF . " AND a.fl_usuario =$fl_usuario   ORDER BY b.fl_usuario, b.nb_playlist ASC ";
              $rs = EjecutaQuery($Query);
              for ($i = 0; $row = RecuperaRegistro($rs); $i++) {

                $nb_play_li = str_texto($row[0]);
                $fl_play_li = $row[1];

                #Recupermaos los programas que tiene ese play list.
                $Querypro = "SELECT fl_programa_sp FROM k_playlist_course WHERE fl_playlist_padre = $fl_play_li ";
                $rspro = EjecutaQuery($Querypro);
                for ($ip = 0; $rowpro = RecuperaRegistro($rspro); $ip++) {


                  $fl_programa_sp_play = $rowpro[0];

                  $Query = "SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp_play ";
                  $ro = RecuperaValor($Query);
                  $nb_programa_n = str_texto($row[0]);

                  $exite_reg = ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario_std, 'fl_programa_sp', $fl_programa_sp_play, True);

                  if (empty($exite_reg)) {

                    # Insertamos el registro
                    $fl_usu_pro_insertado = EjecutaInsert("INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, ds_progreso, no_promedio_t, fg_terminado, fl_maestro,fg_asignado_playlist,fl_playlist,fg_revisado_alumno,fe_creacion, flag) VALUES ($fl_usuario_std, $fl_programa_sp_play, 0, 0, '0', $fl_usuario,'1',$fl_play_li,'0',CURRENT_TIMESTAMP, 0) ");
					#Generamos su token
					$token=sha256($fl_usu_pro_insertado);
					EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado ");
                    # Por defaul indicamos que tendran una calificacion de quiz
                    EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro_insertado,'1','0')");

                    # Buscamos el email del usuario a quitar el curso              
                    $row0  = RecuperaValor("SELECT b.ds_email FROM c_usuario b WHERE b.fl_usuario=$fl_usuario_std ");
                    $ds_email_alu = $row0[0];

                    # Obtenmos el template
                    $ds_header = genera_documento_sp($fl_usuario_std, 1, 121, $fl_programa_sp_play);
                    $ds_body = genera_documento_sp($fl_usuario_std, 2, 121, $fl_programa_sp_play);
                    $ds_footer = genera_documento_sp($fl_usuario_std, 3, 121, $fl_programa_sp_play);

                    # Nombre del template
                    $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=121 AND fg_activo='1'";
                    $template = RecuperaValor($Query0);
                    $nb_template = str_uso_normal($template[0]);

                    # Mensaje
                    $ds_mensaje = $ds_header . $ds_body . $ds_footer;

                    #Covertimos los caracteres especiales html.
                    $ds_mensaje = str_uso_normal($ds_mensaje);

                    # Enviamos el correo al usuario dependiendo de la accion
                    EnviaMailHTML($from, $from, $ds_email_alu, $nb_template, $ds_mensaje);

                    # Enviamos otro a fame
                    EnviaMailHTML($from, $from, $from_fame, $nb_template, $ds_mensaje);

                    #Enviamos notificacion de asignacion de curso Node.json_decode
                    echo "<script>
                      var nb_programa='$nb_programa_n';
                      socket.emit('curso-asignado', $fl_usuario_std,nb_programa);</script> ";
                  } else {
                    # Query que permite reagrupar en un playlist los cursos previamente asignados
                    EjecutaQuery("UPDATE k_usuario_programa SET fl_playlist = $fl_play_li, fg_asignado_playlist = '1' WHERE fl_usuario_sp = $fl_usuario_std AND fl_programa_sp = $fl_programa_sp_play ;");
                  }
                }
                // $exite_reg=ExisteEnTabla('k_usuario_programa','fl_usuario_sp',$fl_usuario_std,'fl_programa_sp',$fl_programa_play_list,True);

              }
            } else {

              #Recuperamos todos los cusros que estan en este playlist.
              $Query = "SELECT  nb_programa, a.fl_programa_sp 
                        FROM k_playlist_course a 
                        LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) 
                        LEFT JOIN k_programa_detalle_sp c ON(c.fl_programa_sp=a.fl_programa_sp) 
                        WHERE 1=1  AND a.fl_playlist_padre=$fl_playlist_select ";
              $rs = EjecutaQuery($Query);
              for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
                $nb_programa = str_texto($row[0]);
                $fl_programa_play_list = ($row[1]);

                $exite_reg = ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario_std, 'fl_programa_sp', $fl_programa_play_list, True);

                if (empty($exite_reg)) {

                  # Insertamos el registro
                  $fl_usu_pro_insertado = EjecutaInsert("INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, ds_progreso, no_promedio_t, fg_terminado, fl_maestro,fg_asignado_playlist,fl_playlist,fg_revisado_alumno,fe_creacion, flag)
                    VALUES ($fl_usuario_std, $fl_programa_play_list, 0, 0, '0', $fl_usuario,'1',$fl_playlist_select,'0',CURRENT_TIMESTAMP, 0) ");
				  #Generamos su token
				  $token=sha256($fl_usu_pro_insertado);
				  EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado ");
                  # Por defaul indicamos que tendran una calificacion de quiz
                  EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro_insertado,'1','0')");

                  # Buscamos el email del usuario a quitar el curso              
                  $row0  = RecuperaValor("SELECT b.ds_email FROM c_usuario b WHERE b.fl_usuario=$fl_usuario_std ");
                  $ds_email_alu = $row0[0];

                  # Obtenmos el template
                  $ds_header = genera_documento_sp($fl_usuario_std, 1, 121, $fl_programa_play_list);
                  $ds_body = genera_documento_sp($fl_usuario_std, 2, 121, $fl_programa_play_list);
                  $ds_footer = genera_documento_sp($fl_usuario_std, 3, 121, $fl_programa_play_list);

                  # Nombre del template
                  $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=121 AND fg_activo='1'";
                  $template = RecuperaValor($Query0);
                  $nb_template = str_uso_normal($template[0]);

                  # Mensaje
                  $ds_mensaje = $ds_header . $ds_body . $ds_footer;

                  #Covertimos los caracteres especiales html.
                  $ds_mensaje = str_uso_normal($ds_mensaje);

                  # Enviamos el correo al usuario dependiendo de la accion
                  EnviaMailHTML($from, $from, $ds_email_alu, $nb_template, $ds_mensaje);

                  # Enviamos otro a fame
                  EnviaMailHTML($from, $from, $from_fame, $nb_template, $ds_mensaje);

                  #Enviamos notificacion de asignacion de curso Node.json_decode
                  echo "<script>
                    
                    var nb_programa='$nb_programa';
                    socket.emit('curso-asignado', $fl_usuario_std,nb_programa);
                    </script> ";
                } else {
                  # Query que permite reagrupar en un playlist los cursos previamente asignados
                  EjecutaQuery("UPDATE k_usuario_programa SET fl_playlist = $fl_playlist_select, fg_asignado_playlist = '1' WHERE fl_usuario_sp = $fl_usuario_std AND fl_programa_sp = $fl_programa_play_list ;");
                }
              }
            }
          } else {

            # Si el usuario no esta en el curso lo va insertar en el nuevo puede tener mas de dos cursos
            if (!ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario_std, 'fl_programa_sp', $nb_grupo, True)) {

              $Query = "SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$nb_grupo ";
              $ro = RecuperaValor($Query);
              $nb_programa_n = str_texto($ro[0]);

              # Insertamos el registro
              $fl_usu_pro_insertado = EjecutaInsert("INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, ds_progreso, no_promedio_t, fg_terminado, fl_maestro,fg_revisado_alumno,fe_creacion, flag) VALUES ($fl_usuario_std, $nb_grupo, 0, 0, '0', $fl_usuario,'0',CURRENT_TIMESTAMP, 0) ");
			  #Generamos su token
			  $token=sha256($fl_usu_pro_insertado);
			  EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado ");

              # Por defaul indicamos que tendran una calificacion de quiz
              EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro_insertado,'1','0')");

              # Buscamos el email del usuario a quitar el curso              
              $row0  = RecuperaValor("SELECT b.ds_email FROM c_usuario b WHERE b.fl_usuario=$fl_usuario_std ");
              $ds_email_alu = $row0[0];

              # Obtenmos el template
              $ds_header = genera_documento_sp($fl_usuario_std, 1, 121, $nb_grupo);
              $ds_body = genera_documento_sp($fl_usuario_std, 2, 121, $nb_grupo);
              $ds_footer = genera_documento_sp($fl_usuario_std, 3, 121, $nb_grupo);

              # Nombre del template
              $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=121 AND fg_activo='1'";
              $template = RecuperaValor($Query0);
              $nb_template = str_uso_normal($template[0]);

              # Mensaje
              $ds_mensaje = $ds_header . $ds_body . $ds_footer;

              #Enviamos notificacion de asignacion de curso Node.json_decode
              echo "<script>
            var nb_programa='$nb_programa_n';
           socket.emit('curso-asignado', $fl_usuario_std,nb_programa);</script> ";
            }
          } #end else playlist
        }
      } else { # No confirmados

        if (/*!empty($nb_grupo) &&*/$nb_grupo == "tot_courses") {

          # Buscamos todos los cursos
          $query  = "SELECT fl_programa_sp FROM c_programa_sp a ";
          $query .= "WHERE NOT EXISTS (SELECT 1 FROM k_usuario_programa b WHERE  b.fl_usuario_sp =$fl_usuario_std AND b.fl_programa_sp=a.fl_programa_sp)";
          
          if($fg_b2c==1){
              $query="SELECT a.fl_programa_sp  FROM k_orden_desbloqueo_curso_alumno p
	                    JOIN c_programa_sp a ON a.fl_programa_sp=p.fl_programa_sp AND  EXISTS( SELECT 1 FROM c_leccion_sp c WHERE c.fl_programa_sp=a.fl_programa_sp  )
	                    WHERE p.fl_instituto= $fl_instituto 
	                    AND NOT EXISTS (SELECT 1 FROM k_usuario_programa b WHERE  b.fl_usuario_sp =$fl_usuario_std AND b.fl_programa_sp=a.fl_programa_sp )
	                    ORDER BY a.nb_programa ";
          }
          
          $rs = EjecutaQuery($query);
          for ($i = 0; $rowq = RecuperaRegistro($rs); $i++) {
            # En este programa el usuario no esta inscrito
            $fl_programa_n = $rowq[0];
            $progInsert = "INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, ds_progreso, no_promedio_t, fg_terminado, fl_maestro,fg_revisado_alumno,fe_creacion, flag) VALUES ($fl_usuario_std, $nb_grupo, 0, 0, '0', $fl_usuario,'0',CURRENT_TIMESTAMP, 0) ";
            $fl_usu_pro_insertado1=EjecutaInsert($progInsert);
			
			#Generamos su token
			$token=sha256($fl_usu_pro_insertado1);
			EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado1 ");
          }

        } else {

          #Para asignacion de playlist de usuarios que no han confirmado la cuenta.
          #Si se elige play list no puede traer valor el programa.
          if ($fl_playlist_select) {

            if ($fl_playlist == 'tot_play') {

              #Recuperamos todos los play list existenten en el combo select.
              $Query = "SELECT b.nb_playlist, b.fl_playlist  
                        FROM c_usuario a JOIN c_playlist b ON a.fl_usuario = b.fl_usuario  
                        WHERE a.fl_instituto = $fl_instituto 
                        AND a.fl_perfil_sp != " . PFL_ESTUDIANTE_SELF . " AND a.fl_usuario =$fl_usuario   ORDER BY b.fl_usuario, b.nb_playlist ASC ";
              $rs = EjecutaQuery($Query);
              for ($i = 0; $row = RecuperaRegistro($rs); $i++) {

                $nb_play_li = str_texto($row[0]);
                $fl_play_li = $row[1];

                #Recupermaos los programas que tiene ese play list.
                $Querypro = "SELECT fl_programa_sp FROM k_playlist_course WHERE fl_playlist_padre = $fl_play_li ";
                $rspro = EjecutaQuery($Querypro);
                for ($ip = 0; $rowpro = RecuperaRegistro($rspro); $ip++) {

                  $fl_programa_sp_play = $rowpro[0];

                  # Si el usuario no esta en el curso lo va insertar en el nuevo puede tener mas de dos cursos
                  if (!ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario_std, 'fl_programa_sp', $fl_programa_sp_play, True)) {

                    $progInsert = "INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, fl_maestro,fg_asignado_playlist,fl_playlist, ds_progreso, flag) VALUES ($fl_usuario_std, $fl_programa_sp_play, $fl_usuario,'1',$fl_play_li, 0, 0) ";
                    $fl_usu_pro_insertado1=EjecutaInsert($progInsert);
					#Generamos su token
					$token=sha256($fl_usu_pro_insertado1);
					EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado1 ");

                    # Buscamos el email del usuario a quitar el curso              
                    $row0  = RecuperaValor("SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_std ");
                    $ds_email_alu = $row0[0];

                    # Obtenmos el template
                    $ds_header = genera_documento_sp(0, 1, 121, $fl_programa_sp_play, $fl_usuario_std);
                    $ds_body = genera_documento_sp(0, 2, 121, $fl_programa_sp_play, $fl_usuario_std);
                    $ds_footer = genera_documento_sp(0, 3, 121, $fl_programa_sp_play, $fl_usuario_std);

                    # Nombre del template
                    $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=121 AND fg_activo='1'";
                    $template = RecuperaValor($Query0);
                    $nb_template = str_uso_normal($template[0]);

                    # Mensaje
                    $ds_mensaje = $ds_header . $ds_body . $ds_footer;
                    #Covertimos los caracteres especiales html.
                    $ds_mensaje = str_uso_normal($ds_mensaje);

                    # Enviamos el correo al usuario dependiendo de la accion
                    EnviaMailHTML($from, $from, $ds_email_alu, $nb_template, $ds_mensaje);

                    # Enviamos otro a fame
                    EnviaMailHTML($from, $from, $from_fame, $nb_template, $ds_mensaje);
                  }
                }
              }
            } else {
              
              # Recuperamos todos los cusros que estan en este playlist.
              $Query = "SELECT  nb_programa, a.fl_programa_sp 
                        FROM k_playlist_course a 
                        LEFT JOIN c_programa_sp b ON(b.fl_programa_sp=a.fl_programa_sp) 
                        LEFT JOIN k_programa_detalle_sp c ON(c.fl_programa_sp=a.fl_programa_sp) 
                        WHERE 1=1  AND a.fl_playlist_padre=$fl_playlist_select ";
              $rs = EjecutaQuery($Query);
              for ($i = 0; $row = RecuperaRegistro($rs); $i++) {
                $nb_programa = str_texto($row[0]);
                $fl_programa_play_list = ($row[1]);

                # Si el usuario no esta en el curso lo va insertar en el nuevo puede tener mas de dos cursos
                if (!ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario_std, 'fl_programa_sp', $fl_programa_play_list, True)) {
                  $progInsert = "INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, fl_maestro,fg_asignado_playlist,fl_playlist, ds_progreso, flag) VALUES ($fl_usuario_std, $fl_programa_play_list, $fl_usuario,'1',$fl_playlist_select, 0, 0) ";
                  $fl_usu_pro_insertado1=EjecutaInsert($progInsert);
				  
				  #Generamos su token
				  $token=sha256($fl_usu_pro_insertado1);
				  EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado1 ");

                  # Buscamos el email del usuario a quitar el curso              
                  $row0  = RecuperaValor("SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_std ");
                  $ds_email_alu = $row0[0];

                  # Obtenmos el template
                  $ds_header = genera_documento_sp(0, 1, 121, $fl_programa_play_list, $fl_usuario_std);
                  $ds_body = genera_documento_sp(0, 2, 121, $fl_programa_play_list, $fl_usuario_std);
                  $ds_footer = genera_documento_sp(0, 3, 121, $fl_programa_play_list, $fl_usuario_std);

                  # Nombre del template
                  $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=121 AND fg_activo='1'";
                  $template = RecuperaValor($Query0);
                  $nb_template = str_uso_normal($template[0]);

                  # Mensaje
                  $ds_mensaje = $ds_header . $ds_body . $ds_footer;
                  #Covertimos los caracteres especiales html.
                  $ds_mensaje = str_uso_normal($ds_mensaje);

                  # Enviamos el correo al usuario dependiendo de la accion
                  EnviaMailHTML($from, $from, $ds_email_alu, $nb_template, $ds_mensaje);

                  # Enviamos otro a fame
                  EnviaMailHTML($from, $from, $from_fame, $nb_template, $ds_mensaje);
                }
              }
            }
          } else {

            # Si el usuario no esta en el curso lo va insertar en el nuevo puede tener mas de dos cursos
            if (!ExisteEnTabla('k_usuario_programa', 'fl_usuario_sp', $fl_usuario_std, 'fl_programa_sp', $nb_grupo, True)) {

              # Inserta el programa usando el c_usuario ($fl_usuario_std)
              $progInsert = "INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, ds_progreso, no_promedio_t, fg_terminado, fl_maestro,fg_revisado_alumno,fe_creacion, flag) VALUES ($fl_usuario_std, $nb_grupo, 0, 0, '0', $fl_usuario,'0',CURRENT_TIMESTAMP, 0) ";
              $fl_usu_pro_insertado1=EjecutaInsert($progInsert);
			  #Generamos su token
			  $token=sha256($fl_usu_pro_insertado1);
			  EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado1 ");

              echo "<script> alert('El insert se ejecuto!!! ".$progInsert."') </script>";

              # Buscamos el email del usuario a quitar el curso              
              $row0  = RecuperaValor("SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_std ");
              $ds_email_alu = $row0[0];
              
              # Obtenmos el template
              $ds_header = genera_documento_sp(0, 1, 121, $nb_grupo, $fl_usuario_std);
              $ds_body = genera_documento_sp(0, 2, 121, $nb_grupo, $fl_usuario_std);
              $ds_footer = genera_documento_sp(0, 3, 121, $nb_grupo, $fl_usuario_std);

              # Nombre del template
              $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=121 AND fg_activo='1'";
              $template = RecuperaValor($Query0);
              $nb_template = str_uso_normal($template[0]);

              # Mensaje
              $ds_mensaje = $ds_header . $ds_body . $ds_footer;
            }
          }
        }
      }
      break;

    case DESASIGNAR_COURSE:

      $fl_programa_std = RecibeParametroNumerico('fl_programa_std');
      # Desasignamos al estudiane del programa
      if (!empty($confirmado)) {
        # Buscamos el email del usuario a quitar el curso
        $row0  = RecuperaValor("SELECT b.ds_email FROM k_usuario_programa a LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario_sp) WHERE a.fl_usuario_sp=$fl_usuario_std AND a.fl_programa_sp=$fl_programa_std");

        $ds_email_alu = $row0[0];

        # Obtenmos el template
        $ds_header = genera_documento_sp($fl_usuario_std, 1, 119, $fl_programa_std);
        $ds_body = genera_documento_sp($fl_usuario_std, 2, 119, $fl_programa_std);
        $ds_footer = genera_documento_sp($fl_usuario_std, 3, 119, $fl_programa_std);

        # Nombre del template
        $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=119 AND fg_activo='1'";
        $template = RecuperaValor($Query0);
        $nb_template = str_uso_normal($template[0]);

        # Mensaje
        $ds_mensaje = $ds_header . $ds_body . $ds_footer;

        # Eliminamos los entregables del usuario
        $rs0 = EjecutaQuery("SELECT fl_leccion_sp FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa_std");
        for ($i = 0; $row0 = RecuperaRegistro($rs0); $i++) {
          $fl_leccion_sp = $row0[0];
          # Buscamos el los entregables de la semana
          $row01 = RecuperaValor("SELECT fl_entrega_semanal_sp FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_usuario_std AND fl_leccion_sp=$fl_leccion_sp");
          $fl_entrega_semanal_sp = $row01[0];

          # Eliminamos los entregables
          EjecutaQuery("DELETE FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
          # Eliminamos los entregables de la seman
          EjecutaQuery("DELETE FROM k_entrega_semanal_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
          # Eliminamos los complete
          EjecutaQuery("DELETE FROM k_leccion_usu where fl_leccion_sp=$fl_leccion_sp AND fl_usuario_sp=$fl_usuario_std");
          # Eliminamos todos los quiz realizados
          EjecutaQuery("DELETE FROM k_quiz_calif_final WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario=$fl_usuario_std");
          # Eliminamos todos los quiz realizados respuestas
          EjecutaQuery("DELETE FROM k_quiz_respuesta_usuario WHERE fl_leccion_sp=$fl_leccion_sp AND fl_usuario=$fl_usuario_std");
        }

        # Query para eliminarlo del curso
        EjecutaQuery("DELETE FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario_std AND fl_programa_sp=$fl_programa_std");
      } else {
        # Buscamos el email del usuario a quitar el curso          
        $row0  = RecuperaValor("SELECT b.ds_email FROM k_envio_email_reg_selfp b WHERE b.fl_envio_correo=$fl_usuario_std ");
        $ds_email_alu = $row0[0];

        # Obtenmos el template
        $ds_header = genera_documento_sp(0, 1, 119, $fl_programa_std, $fl_usuario_std);
        $ds_body = genera_documento_sp(0, 2, 119, $fl_programa_std, $fl_usuario_std);
        $ds_footer = genera_documento_sp(0, 3, 119, $fl_programa_std, $fl_usuario_std);

        # Nombre del template
        $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=119 AND fg_activo='1'";
        $template = RecuperaValor($Query0);
        $nb_template = str_uso_normal($template[0]);

        # Mensaje
        $ds_mensaje = $ds_header . $ds_body . $ds_footer;

        # Elimnamos registro
        $Query = "DELETE FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario_std AND fl_programa_sp=$fl_programa_std";
      }
      break;
      
    case CHANGE_PERFIL:# Solo podra cambair el perfil a los teachers para que puedan ser un administrador
      $Query = "UPDATE c_usuario SET fl_perfil_sp='" . $fl_perfil_user . "' WHERE fl_usuario=$fl_usuario_std";
      break;
      
    case PAUSE_COURSE:# Se podra pausar el curso del usuario
      $fg_status_pro = RecibeParametroBinario('fg_status_pro');
      $fl_usu_pro = RecibeParametroNumerico('fl_usu_pro');
      if (empty($fl_usu_pro))
        $Query = "UPDATE k_usuario_programa SET fg_status_pro='$fg_status_pro' WHERE fl_usuario_sp=$fl_usuario_std AND fl_programa_sp=$nb_grupo";
      else
        $Query = "UPDATE k_usuario_programa SET fg_status_pro='$fg_status_pro' WHERE fl_usu_pro=$fl_usu_pro";
      # SI pausan el curso
      if ($fg_status_pro == 1) {
        # Obtenemos la informacion del usuario a pausar
        if (empty($fl_usu_pro))
          $row0  = RecuperaValor("SELECT a.fl_usuario_sp, b.ds_email, a.fl_programa_sp FROM k_usuario_programa a LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario_sp) WHERE a.fl_usuario_sp=$fl_usuario_std AND a.fl_programa_sp=$nb_grupo");
        else
          $row0  = RecuperaValor("SELECT a.fl_usuario_sp, b.ds_email, a.fl_programa_sp FROM k_usuario_programa a LEFT JOIN c_usuario b ON(b.fl_usuario=a.fl_usuario_sp) WHERE fl_usu_pro=$fl_usu_pro");
        $fl_alu = $row0[0];
        $ds_email_alu = $row0[1];
        $fl_pro = $row0[2];

        # Obtenmos el template
        $ds_header = genera_documento_sp($fl_alu, 1, 120, $fl_pro);
        $ds_body = genera_documento_sp($fl_alu, 2, 120, $fl_pro);
        $ds_footer = genera_documento_sp($fl_alu, 3, 120, $fl_pro);

        # Nombre del template
        $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=120 AND fg_activo='1'";
        $template = RecuperaValor($Query0);
        $nb_template = str_uso_normal($template[0]);

        # Mensaje
        $ds_mensaje = $ds_header . $ds_body . $ds_footer;
      }
      break;

    case "100":# Solo podra cambiar el perfil a los teachers para que puedan ser un administrador

      // if($asignar){
      $fl_programa_std = RecibeParametroNumerico('fl_programa_std');
      # Inserta en programa
      $Querypro  = "INSERT INTO k_usuario_programa (fl_usuario_sp, fl_programa_sp, ds_progreso, no_promedio_t, fg_terminado, fl_maestro, flag) ";
      $Querypro .= "VALUES ($fl_usuario_std, '$fl_programa_std', 0, 0, '0', $fl_usuario, 0) ";
      $fl_usu_pro_insertado1=EjecutaInsert($Querypro);
	  
	  #Generamos su token
	  $token=sha256($fl_usu_pro_insertado1);
	  EjecutaQuery("UPDATE k_usuario_programa SET token='$token' WHERE fl_usu_pro=$fl_usu_pro_insertado1 ");
	  
      # Actualiza el grupo que no estaba asignado
      $Querygrp = "UPDATE c_alumno_sp SET nb_grupo='$nb_grupo' WHERE fl_alumno_sp=$fl_usuario_std";
      EjecutaQuery($Querygrp);
      EjecutaQuery("UPDATE c_usuario SET fg_activo='1' WHERE fl_usuario=$fl_usuario_std");

      // }
      break;
      
    case ASSESSMENT:# Actualizamos la asignacion de calificaciones
      $fl_usu_pro = RecibeParametroNumerico('fl_usu_pro');
      $fg_grade_tea = RecibeParametroBinario('fg_grade_tea');
      if (!empty($fl_usu_pro)) {
        if (ExisteEnTabla('k_details_usu_pro', 'fl_usu_pro', $fl_usu_pro)) {
          EjecutaQuery("UPDATE k_details_usu_pro SET fg_quizes = '1', fg_grade_tea = '$fg_grade_tea' WHERE fl_usu_pro = $fl_usu_pro");
          echo "UPDATE k_details_usu_pro SET fg_quizes = '1', fg_grade_tea = '$fg_grade_tea' WHERE fl_usu_pro = $fl_usu_pro";
        } else {
          EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro, '1', '$fg_grade_tea')");
          echo "INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro, '1', '$fg_grade_tea')";
        }
      }
      break;

    case ASSIGN_MYSELF:
      $fg_assign_myself_course = RecibeParametroBinario('fg_assign_myself_course');
      # Solo podras dar permisos a usuario que ya confirmados
      if (!empty($confirmado)) {
        EjecutaQuery("UPDATE c_usuario SET fg_assign_myself_course='$fg_assign_myself_course' WHERE fl_usuario=$fl_usuario_std");
        echo "UPDATE c_usuario SET fg_assign_myself_course='$fg_assign_myself_course' WHERE fl_usuario=$fl_usuario_std";

        #Si el alumno ya se le asigno acceso a todo se asigna todos los cursos.
        if($fg_assign_myself_course==1){
            AsignarTodosLosCursosAlAlumno($fl_usuario_std);
        }else{
            
              #le quitamos todos y solo dejamos los que haya completado.
              # Query Principal
              $Query  = " DELETE FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario_std AND ds_progreso=0 ";
              EjecutaQuery($Query);                  
              
        }



      }
      break;
  }
  
  EjecutaQuery($Query); # EJECUTA EL QUERY QUE QUEDO PENDIENTE EN EL CASE

  # Enviara una notificación a los usuariosque se les realice una de las siguenites acciones
  if ($fl_action == ASG_COURSE || $fl_action == DESASIGNAR_COURSE || $fl_action == PAUSE_COURSE) {

    #Covertimos los caracteres especiales html.
    $ds_mensaje = str_uso_normal($ds_mensaje);

    if (empty($fl_playlist_select)) {
      # Enviamos el correo al usuario dependiendo de la accion
      EnviaMailHTML($from, $from, $ds_email_alu, $nb_template, $ds_mensaje);
      # Enviamos otro a fame
      EnviaMailHTML($from, $from, $from_fame, $nb_template, $ds_mensaje);
    }
  }
} else {
  $fl_programa_sel = RecibeParametroNumerico('fl_programa_sel');
  $fl_usuario_sel = RecibeParametroNumerico('fl_usuario_sel');
  $ds_nombre_sel = RecibeParametroHTML('ds_nombre_sel');
  $row = RecuperaValor("SELECT CONCAT(b.ds_nombres,' ', b.ds_apaterno) FROM k_usuario_programa a, c_usuario b WHERE a.fl_usuario_sp=b.fl_usuario AND a.fl_usuario_sp=$fl_usuario_sel AND a.fl_programa_sp=$fl_programa_sel");
  if (!empty($row[0]))
    $mismo = True;
  else
    $mismo = False;

  echo $mismo;
}
