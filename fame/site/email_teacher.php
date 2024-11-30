<?php
  # Libreria de funciones
  require("../lib/self_general.php");

  ValidaSesion(False, 0, True);
  $fl_programa = RecibeParametroNumerico("fl_programa");
  $fl_usuario = RecibeParametroNumerico("fl_usuario");
  $fl_template = RecibeParametroNumerico("fl_template");
  
  $Query="SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa ";
  $row=RecuperaValor($Query);
  $nb_programa=$row['nb_programa'];
  
  # Buscamos el teacher de este curso datos teachers
  /*$Query = "SELECT a.fl_maestro, b.ds_email, CONCAT( b.ds_nombres, ' ' , b.ds_apaterno ), nb_programa ";
  $Query .= "FROM k_usuario_programa a, c_usuario b, c_programa_sp c ";
  $Query .= "WHERE a.fl_maestro=b.fl_usuario AND a.fl_programa_sp=c.fl_programa_sp AND a.fl_programa_sp=$fl_programa AND a.fl_usuario_sp=$fl_usuario ";
  $row = RecuperaValor($Query);
  $fl_maestro = $row[0];
  $ds_email = $row[1];
  $ds_nombres = str_texto($row[2]);
  $nb_programa = str_texto($row[3]);*/
  
  # Buscamos si el usuario esta inscrito en el curso
  if(ExisteEnTabla('k_usuario_programa', 'fl_programa_sp', $fl_programa, 'fl_usuario_sp', $fl_usuario, true)){
    $Query  = "SELECT b.ds_email,a.fl_maestro FROM k_usuario_programa a, c_usuario b ";
    $Query .= "WHERE a.fl_maestro=b.fl_usuario  ";
    $Query .= "AND a.fl_programa_sp=$fl_programa AND a.fl_usuario_sp=$fl_usuario";
  }
  else{
    $row0 = RecuperaValor("SELECT fl_usu_invita FROM c_usuario b WHERE fl_usuario=$fl_usuario");
    $Query = "SELECT b.ds_email,b.fl_usuario FROM c_usuario b WHERE b.fl_usuario=".$row0[0];
  }
  $row1 = RecuperaValor($Query);
  $ds_email = $row1[0];
  $fl_maestro=$row1[1];
  
  # Este email es necesario
  $from = ObtenConfiguracion(4);
  # Enviamos copia a fame
  $from_fame = ObtenConfiguracion(107);
  
  # Obtenmos el template
  $ds_header = genera_documento_sp($fl_usuario, 1, $fl_template, $fl_programa);
  $ds_body = genera_documento_sp($fl_usuario, 2, $fl_template, $fl_programa);
  $ds_footer = genera_documento_sp($fl_usuario, 3, $fl_template, $fl_programa);
  
  # Nombre del template
  $Query = "SELECT nb_template FROM k_template_doc WHERE fl_template=$fl_template AND fg_activo='1'";
  $template = RecuperaValor($Query);
  $nb_template = str_uso_normal($template[0]);
  # Mensaje
  $ds_mensaje = $ds_header.$ds_body.$ds_footer;
  
  # Enviamos al fame
  EnviaMailHTML($from, $from, $from_fame, $nb_template, $ds_mensaje);
  
  #Se envia notificacion a todos los teachers de etse estudiante.
  $Query="SELECT fl_maestro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario group by fl_maestro ";
  $rs=EjecutaQuery($Query);
  for($i=0; $row = RecuperaRegistro($rs); $i++) {

      $fl_ult_maestro=$row[0];
      if(!empty($fl_ult_maestro)){
          $fl_maestro= $fl_ult_maestro;
      }
      $Querym="SELECT ds_email,ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_ult_maestro";
      $rowm=RecuperaValor($Querym);
      $ds_email=$rowm['ds_email'];
	  $fname_teacher=str_texto($rowm[1]);
      $lname_teacher=str_texto($rowm[2]);
	  
	  #Datos del student
	   #Recuperamos dtos genrales del teacher y estudent.
      $Query1="SELECT ds_email,ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
      $rou=RecuperaValor($Query1);
      $ds_email_alumno=$rou['ds_email'];
      $fname_alumno=str_texto($rou[1]);
      $lname_alumno=str_texto($rou[2]);
	  

	  $ds_header = GeneraDocEmail(122,1);
	  $ds_body = GeneraDocEmail(122,2);
	  $ds_footer = GeneraDocEmail(122,3);
      $ds_mensaje=$ds_header.$ds_body.$ds_footer;


      $ds_mensaje = str_replace("#fame_te_fname#", $fname_teacher, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_te_lname#", $lname_teacher, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_fname#", $fname_alumno, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_lname#", $lname_alumno, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_pg_name#", $nb_programa, $ds_mensaje);
		
      # Email al teacher
      EnviaMailHTML($from, $from, $ds_email, $nb_template, $ds_mensaje);


      #Insertamos la notificacion.
      EjecutaQuery("DELETE FROM k_request_access_course WHERE fl_programa_sp=$fl_programa and fl_usuario_sp=$fl_usuario and fl_maestro_sp=$fl_maestro ");
      $Query="INSERT INTO k_request_access_course (fl_programa_sp,fl_usuario_sp,fl_maestro_sp,fe_alta)";
      $Query.="VALUES($fl_programa,$fl_usuario,$fl_maestro,CURRENT_TIMESTAMP)  ";
      EjecutaQuery($Query);
  }


  function GeneraDocEmail($fl_template,$opc){
      
      # Recupera datos del template del documento
      switch ($opc) {
          case 1:
              $campo = "ds_encabezado";
              break;
          case 2:
              $campo = "ds_cuerpo";
              break;
          case 3:
              $campo = "ds_pie";
              break;
          case 4:
              $campo = "nb_template";
              break;
      }
      # Obtenemos la informacion del template header body or footer
      $Query1  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
      $row = RecuperaValor($Query1);

      $cadena = $row[0];
      # Sustituye caracteres especiales
      $cadena = $row[0];
      $cadena = str_replace("&lt;", "<", $cadena);
      $cadena = str_replace("&gt;", ">", $cadena);
      $cadena = str_replace("&quot;", "\"", $cadena);
      $cadena = str_replace("&#039;", "'", $cadena);
      $cadena = str_replace("&#061;", "=", $cadena);

      return (str_uso_normal($cadena));
  }

?>
