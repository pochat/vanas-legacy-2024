<?php
	# Libreria de funciones
	require("../lib/self_general.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_logueado = ValidaSesion(False,0, True); 
  # Obtenemos el instituto
  $fl_instituto = ObtenInstituto($fl_usuario_logueado);
  # Obtenemos el perfil del usuario
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario_logueado);
  


  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  $index = RecibeParametroNumerico('index', True);
  $fg_filtro=$_GET['fg_filtro'];
  $fl_programa_sp=$_GET['fl_programa_sp'];
  $index = intval($index);
  $index_end = 8;

  

  # Get educational by intitute
  $Query  = "SELECT fg_educational ";
  $Query .= "FROM k_instituto_filtro a WHERE fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query);
  $fg_educational = $row[0];
  $privacity_educational = "";
  # search educational
  if ($fg_educational == 2) {
      $privacity_educational .= " AND a.fl_instituto=" . $fl_instituto . " ";
  }else{
      $privacity_educational .= " AND fg_educational<>'2'  "; //buscará a todos excepto los numero 2.  que estan limitados solo por su instituto. 
  }

  # Get educational by intitute
  $Query  = "SELECT fg_international ";
  $Query .= "FROM k_instituto_filtro WHERE fl_instituto=$fl_instituto ";
  $row = RecuperaValor($Query);
  $fg_international = $row[0];
  $privacity_international = "";
  # search educational
  if($fg_international == 2) {
    # Get country user
    $roww = RecuperaValor("SELECT fl_pais FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_usuario_logueado");
    $fl_pais = $roww[0];
    if(!empty($fl_pais))
      $privacity_international = " AND fl_pais=" . $roww[0] . " ";
  }





  $Queryp="";

  if(($fg_filtro=='All')||($fg_filtro=='A')){

      $row1 = RecuperaValor("SELECT  nb_perfil FROM c_perfil WHERE fl_perfil=".PFL_ADMINISTRADOR);
      $ds_perfil_admin = $row1[0];

      $Queryp .="( ";
      $Queryp .=" SELECT DISTINCT a.fl_usuario,a.ds_nombres,a.fl_instituto, '$ds_perfil_admin' nb_perfil 
             FROM c_usuario a
             join k_instituto_filtro b on b.fl_instituto=a.fl_instituto
             LEFT JOIN k_usu_direccion_sp c ON c.fl_usuario_sp= a.fl_usuario 
             WHERE a.fl_perfil_sp=".PFL_ADMINISTRADOR." ";
      # Privacity educational
      if (!empty($privacity_educational)){
          $Queryp .= $privacity_educational;
      }
      # Privacity International
      if (!empty($privacity_international)){
          $Queryp .= $privacity_international;
      }
      $Queryp .=") ";
  }


  if(($fg_filtro=='All')||($fg_filtro=='T')||($fg_filtro=='P')){

      $row1 = RecuperaValor("SELECT  nb_perfil FROM c_perfil WHERE fl_perfil=".PFL_MAESTRO_SELF);
      $ds_perfil_teacher = $row1[0];

     
      if($fg_filtro=='All')
         $Queryp.=" UNION ";

      $Queryp .="( ";
      $Queryp .=" SELECT DISTINCT  a.fl_usuario,a.ds_nombres,a.fl_instituto, '$ds_perfil_teacher' nb_perfil 
                  FROM c_usuario a
                  join k_instituto_filtro b on b.fl_instituto=a.fl_instituto ";
      if(($fg_filtro=='P')&&(!empty($fl_programa_sp)))
      $Queryp .=" JOIN k_usuario_programa p ON p.fl_maestro=a.fl_usuario ";
      $Queryp  .=" LEFT JOIN k_usu_direccion_sp c ON c.fl_usuario_sp= a.fl_usuario ";
      $Queryp .=" WHERE a.fl_perfil_sp=".PFL_MAESTRO_SELF." ";
      if(($fg_filtro=='P')&&(!empty($fl_programa_sp)))
      $Queryp .=" AND p.fl_programa_sp=$fl_programa_sp ";
      # Privacity educational
      if (!empty($privacity_educational)){
          $Queryp .= $privacity_educational;
      }
      # Privacity International
      if (!empty($privacity_international)){
          $Queryp .= $privacity_international;
      }


      $Queryp .=") ";

  }

  if(($fg_filtro=='All')||($fg_filtro=='S')||($fg_filtro=='P')){

      $row1 = RecuperaValor("SELECT  nb_perfil FROM c_perfil WHERE fl_perfil=".PFL_ESTUDIANTE_SELF);
      $ds_perfil_student = $row1[0];

      if(($fg_filtro=='All')||($fg_filtro=='P'))
          $Queryp.=" UNION ";

      $Queryp .="( ";
      $Queryp .=" SELECT DISTINCT a.fl_usuario,a.ds_nombres,a.fl_instituto, '$ds_perfil_student' nb_perfil 
                  FROM c_usuario a
                  join k_instituto_filtro b on b.fl_instituto=a.fl_instituto ";
      if(($fg_filtro=='P')&&(!empty($fl_programa_sp)))
      $Queryp .=" JOIN k_usuario_programa p ON p.fl_usuario_sp=a.fl_usuario ";

      $Queryp  .=" LEFT JOIN k_usu_direccion_sp c ON c.fl_usuario_sp= a.fl_usuario ";
      $Queryp .=" WHERE a.fl_perfil_sp=".PFL_ESTUDIANTE_SELF." ";
      if(($fg_filtro=='P')&&(!empty($fl_programa_sp)))
      $Queryp .=" AND p.fl_programa_sp=$fl_programa_sp ";

      # Privacity educational
      if (!empty($privacity_educational)){
          $Queryp .= $privacity_educational;
      }
      # Privacity International
      if (!empty($privacity_international)){
          $Queryp .= $privacity_international;
      }

      $Queryp .=") ";


  }
  $Queryp.=" ORDER BY fl_usuario ASC  LIMIT $index_end OFFSET $index ";
  $rs = EjecutaQuery($Queryp);

  $result = array();
  for($i=0; $row=RecuperaRegistro($rs); $i++){


        $fl_usuario = $row[0];
        $fl_instituto=$row[2];
        $ds_nombres =ObtenNombreUsuario($fl_usuario);
        $ruta_avatar=ObtenAvatarUsuario($fl_usuario);
        $ruta_avatar="<img   src='".$ruta_avatar."' class='img-circle' width='100' height='100' >";
        $ds_perfil=$row['nb_perfil'];


        #Recuperamos nombre del Instituto
        $Queryi="SELECT ds_instituto,ds_pais FROM c_instituto a JOIN c_pais b ON a.fl_pais= b.fl_pais WHERE fl_instituto=$fl_instituto ";
        $rowi=RecuperaValor($Queryi);
        $nb_instituto=$rowi['ds_instituto'];
        $ds_pais=$rowi['ds_pais'];

        #Verificamos que se le haya enviado una invitacion.
        $Que="SELECT fg_enviado,fg_aceptado FROM  k_relacion_usuarios WHERE fl_usuario_origen=".$fl_usuario_logueado." AND fl_usuario_destinatario=".$fl_usuario." OR fl_usuario_origen=".$fl_usuario." AND fl_usuario_destinatario=".$fl_usuario_logueado."  ";
        $roq=RecuperaValor($Que);
        $fg_enviado=!empty($roq['fg_enviado'])?$roq['fg_enviado']:NULL;
        $fg_aceptado=!empty($roq['fg_aceptado'])?$roq['fg_aceptado']:NULL;
        
        if($fg_enviado){
			$status="Enviado";
			$boton_para_enviar="hidden";
			$btn_enviado="";
			$fg_son_amigos="";
			$boton_para_enviar_sms="hidden";
			
			if($fg_aceptado){
                $status="Aceptado";
                $boton_para_enviar="hidden";
                $btn_enviado="hidden";
                $fg_son_amigos=1;
                $boton_para_enviar_sms="";
			}
			
			
	    }else{
			$status="FaltaEnviar";
			$boton_para_enviar="";
			$btn_enviado="hidden";
	        $fg_son_amigos="";
			$boton_para_enviar_sms="hidden";
			
		}







		$result["item".$i] = array(
			"fl_usuario" => $fl_usuario,
			"ds_nombres" => $ds_nombres,
            "ruta_avatar"=> $ruta_avatar,
            "ds_perfil"=>$ds_perfil,
            "nb_instituto"=>$nb_instituto,
            "fl_usuario_logueado"=>$fl_usuario_logueado,
            "ds_pais"=>$ds_pais,
            "status_solicitud"=> $status,
            "boton_para_enviar"=> $boton_para_enviar,
            "btn_enviado"=> $btn_enviado,
		    "boton_para_enviar_sms"=>$boton_para_enviar_sms,
		    "fg_son_amigos"=>$fg_son_amigos,

		);
		

		

    }
  

	if($i == 0){
		$result["index"] = array("end" => 0, "message" => "No records","Queryp"=>$Queryp);
		echo json_encode((Object)$result);
		exit;
	}
	$result["size"] = array("total" => $i, "querypincipal"=>$Queryp);
	$result["index"] = array("end" => $index+$index_end);

	echo json_encode((Object) $result);
?>
