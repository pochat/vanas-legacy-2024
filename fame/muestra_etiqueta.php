<?php

  # Libreria de funciones
  require_once("../lib/sp_general.inc.php");

  # Recibe  datos de envio e email.
  $fl_envio_correo=RecibeParametroHTML('fl_envio_correo');
  
  #Recupermas el email y nombre del estuiante.
  $Query="SELECT ds_first_name,ds_last_name,ds_email FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_correo ";
  $row=RecuperaValor($Query);
  $ds_fname=str_texto($row[0]);
  $ds_lname=str_texto($row[1]);
  $ds_email=str_texto($row[2]);
  
  
  #Recupermas el email a donde fue enviado.
  #Verificamos si existe un registro 
  $Query="SELECT ds_email_alumno,ds_email,nb_parentesco,A.ds_fname,A.ds_lname 
            FROM k_responsable_alumno A 
            JOIN c_parentesco B ON B.cl_parentesco=A.cl_parentesco 
            WHERE fl_envio_correo=$fl_envio_correo ";
  $row=RecuperaValor($Query);
  $ds_email_tutor=$row[1];
  $nb_parentesco=str_texto($row[2]);
  $ds_fname=str_texto($row[3]);
  $ds_lname=str_texto($row[4]);
  
  $cadena=ObtenEtiqueta(2071);  
  $cadena = str_replace("#email#",$ds_email_tutor, $cadena);  # fname teacher
  $cadena = str_replace("#relationship#",$nb_parentesco, $cadena);  # fname teacher
  $cadena = str_replace("#first name#",$ds_fname, $cadena);  # fname teacher
  $cadena = str_replace("#Last name#",$ds_lname, $cadena);  # fname teacher
  
  
  echo $cadena;
  
  
?>