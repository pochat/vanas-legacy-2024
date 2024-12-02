<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PARTNER_SCHOOL, $permiso)) {
   MuestraPaginaError(ERR_SIN_PERMISO);
   exit;
  }
  
  # Recibe parametros
  $nb_instituto=RecibeParametroHTML('nb_instituto');
  $fl_pais = RecibeParametroNumerico('fl_pais');
  $ds_codigo_area =RecibeParametroHTML('ds_codigo_area');
  $no_telefono = RecibeParametroHTML('no_telefono');  
  $ds_alias = RecibeParametroHTML('ds_alias');
  $ds_alias_bd = RecibeParametroHTML('ds_alias_bd');
  $fl_usuario_sp=RecibeParametroNumerico('fl_usuario_sp');
 
  $ds_codigo_pais=RecibeParametroHTML('ds_codigo_pais');
  $fg_export_moodle=RecibeParametroBinario('fg_export_moodle');
  $fg_parent_authorization=RecibeParametroBinario('fg_parent_authorization');
  $fg_export_follower=RecibeParametroBinario('fg_export_follower');
  $cl_tipo_instituto=RecibeParametroNumerico('cl_tipo_instituto');
  $fl_instituto_rector=RecibeParametroNumerico('fl_instituto_rector');
  $fg_menu_csf=RecibeParametroBinario('fg_menu_csf');
  $ruta_sftp=RecibeParametroHTML('ruta_sftp');

  if($cl_tipo_instituto==1)
		$cl_tipo_instituto=2;
	else
		$cl_tipo_instituto=1;

  # Inserta o actualiza el registro
  if(!empty($clave)) {

      $Query="UPDATE c_instituto SET ruta_sftp='$ruta_sftp', fl_instituto_rector=$fl_instituto_rector, cl_tipo_instituto=$cl_tipo_instituto, ds_instituto='$nb_instituto',fg_parent_authorization='$fg_parent_authorization', fg_export_follower='$fg_export_follower',  fg_export_moodle='$fg_export_moodle' ,fl_pais=$fl_pais, ds_codigo_area='$ds_codigo_area',no_telefono='$no_telefono' ,fg_menu_csf='$fg_menu_csf' ";
	  $Query.="WHERE fl_instituto=$clave ";
      EjecutaQuery($Query);
            
      
      # Actualiza alias del administrador
      # Buscamos si hay un alias
      $rowu = RecuperaValor("SELECT 1 FROM c_usuario WHERE fl_usuario!=$fl_usuario_sp AND ds_alias='".$ds_alias."'");
      $alias_existe = $rowu[0];
      if(empty($alias_existe)){
        $Query2 = "UPDATE c_usuario SET ds_alias='$ds_alias' WHERE fl_usuario=$fl_usuario_sp";
        EjecutaQuery($Query2);
        # Si cambia el alias envia nuevo alias a su correo
        if($ds_alias != $ds_alias_bd){
          UserChangeAlias($fl_usuario_sp);
        }
      }
	  
	  if($cl_tipo_instituto==2){
	  #Si es rectro el usuario pasa a ser admin rector.
	  EjecutaQuery("UPDATE c_usuario SET fl_perfil_sp=".PFL_ADM_CSF." WHERE fl_usuario=$fl_usuario_sp  ");
      EjecutaQuery("UPDATE c_instituto SET school_id='$clave' WHERE fl_instituto=$clave  ");
	  }else{
	  #Si es rectro el usuario pasa a ser admin rector.
	  EjecutaQuery("UPDATE c_usuario SET fl_perfil_sp=13 WHERE fl_usuario=$fl_usuario_sp  "); 
	  }
	  
  }
 
  
  

 
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
?>
