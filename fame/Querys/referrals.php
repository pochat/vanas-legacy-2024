<?php   
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  
  #Query
  $Query  = "SELECT b.fl_envio_correo, b.ds_email, c.nb_programa".$sufix.", a.fe_alta,a.fg_confirmado, CASE WHEN r.fg_autorizado='0' THEN 'FA' ELSE r.fg_autorizado END fg_autorizado   ";
  $Query .= "FROM k_envio_email_reg_selfp a
			 JOIN c_desbloquear_curso_alumno b ON a.fl_envio_correo=b.fl_envio_correo  
			 JOIN c_programa_sp c ON b.fl_programa_sp=c.fl_programa_sp
			 LEFT JOIN k_responsable_alumno r ON r.fl_envio_correo=a.fl_envio_correo
			 ";
  $Query .= "WHERE  fg_desbloquear_curso='1' ";
  $Query .= "AND fl_invitado_por_usuario=".$fl_usuario;
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
    $fl_envio_correo = $row[0];
    $ds_email = $row[1];
    $nb_programa = $row[2];
    $fe_alta = $row[3];
	$fg_confirmado=$row[4];
	$fg_autorizado=str_texto($row[5]);
	
	#DAMOS FORMATO DIA,MES, ANÑO
	$date = date_create($fe_alta);
	$fe_alta=date_format($date,'F j, Y');
	$fe_hora=date_format($date,'g:i a');
	$fe_alta=$fe_alta." at ".$fe_hora." <i>(Pacific time)</i>";
	
    
	
	if($fg_confirmado==1){
	
		if($fg_autorizado=='FA'){
		  $color="danger";
		  $etq=ObtenEtiqueta(2126);//falta condfirmacion del papa
		}else{
		  $color="success";
		  $etq=ObtenEtiqueta(2207);
		}
	  
	  
	}else{
	  $color="danger";
	  $etq=ObtenEtiqueta(2102);
	}
	
     
    echo '
    {
      
      "email": "'.$ds_email.'",
      "programa": "'.$nb_programa.'",
      "status": "<span class=\'label label-'.$color.'\'>'.$etq.'</span>",
      "fecha": "'.$fe_alta.'",
	  "action": "&nbsp;"
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
    
  }  
  ?>
    ]
}