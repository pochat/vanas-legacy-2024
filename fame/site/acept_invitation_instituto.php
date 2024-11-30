<?php
  
  # Libreria de funciones	
  require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  

  $fl_instituto_invitador=RecibeParametroNumerico('fl_instituto');
  $fg_respuesta=RecibeParametroNumerico('fg_respuesta');
  $fl_user_invitador=RecibeParametroNumerico('fl_user_invitador');


  #Recuperamos datos del Usuario que esta aceptando invitacion.
  $Query="SELECT CONCAT(ds_nombres,' ',ds_apaterno) nombre FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $rop=RecuperaValor($Query);
  $nb_alumno_acepta=$rop[0];

  

  
  if($fg_respuesta==1){
	  
	  EjecutaQuery("DELETE FROM k_instituto_alumno WHERE fl_instituto=$fl_instituto ");
      $Qu ="INSERT INTO k_instituto_alumno (fl_instituto,fl_usuario_sp,fg_aceptado,fe_creacion,fe_ultmod)";
	  $Qu.="VALUES($fl_instituto,$fl_usuario,'1',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
      EjecutaQuery($Qu);
	  
	  $Query="UPDATE k_instituto_alumno SET  fg_aceptado='1' WHERE fl_instituto=$fl_instituto_invitador  AND fl_usuario_sp=$fl_usuario ";
      EjecutaQuery($Query);
	  

      echo json_encode((Object)array(
      'nb_alumno_acepta' => $nb_alumno_acepta,
      'fl_alumno_acepta'=> $fl_usuario,
      'etq_descripcion'=> ObtenEtiqueta(2569),
      'fl_user_invitador'=>$fl_user_invitador

      ));


	  
  }else{
	  
	  $Query="UPDATE k_instituto_alumno SET  fg_aceptado='2',fe_ultmod=CURRENT_TIMESTAMP  WHERE fl_instituto=$fl_instituto_invitador  AND fl_usuario_sp=$fl_usuario ";
      EjecutaQuery($Query);
	  
  }
  
  


  
  
  
?>