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

  
  
  
  # Recibe Parametros
  $fl_users = $_POST['fl_users'];
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  # Query que obtiene los usuarios dependiedo de la intitucion

  $Query="
            SELECT   mn_total,id_pago,fe_pago,ds_descripcion_pago,fl_pago,fg_tipo_pago from (

            (					  
            SELECT '2'fg_tipo_pago, A.fl_pago_curso_alumno fl_pago, A.mn_total mn_total ,A.id_pago id_pago,A.fe_pago fe_pago,CONCAT( 'Unlock Course: ',B.nb_programa) ds_descripcion_pago 
										             FROM k_pago_curso_alumno A 
										              JOIN c_programa_sp B ON B.fl_programa_sp=A.fl_programa_sp 
										              WHERE A.fl_alumno_sp=$fl_usuario
            )UNION (

            SELECT '1'fg_tipo_pago, D.fl_admin_pagos_alumno fl_pago, D.mn_total mn_total ,D.id_pago_stripe id_pago,D.fe_pago fe_pago,
									            CASE WHEN C.fg_plan='A' THEN 'Essential Plan Annual'
									            ELSE 'Essencial Plan Monthly'
									            END ds_descripcion_pago 
									            FROM k_current_plan_alumno C
      							            JOIN k_admin_pagos_alumno D ON D.fl_current_plan_alumno= C.fl_current_plan_alumno
		            WHERE C.fl_alumno=$fl_usuario

            )
            ) E ORDER BY fe_pago DESC 
  "; 
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){

      $mn_total = "$ ".number_format($row[0],2);
      $id_pago=str_texto($row[1]);
      $fe_pagado=ObtenFechaFormatoDiaMesAnioHora($row[2],true);
      $ds_descripcion_pago=$row[3];
      $fl_admin_pagos=$row[4]; 
      $fg_motivo_pago=$row[5];

      $color = "success";
      $status= ObtenEtiqueta(1545);
      
     #Nota 
     #fg_tipo pago define si es un apago para desbloquear curso o represneta el apgo de un aplan o renovacion d eplan.
          
      
      
  
   
    
   
    
    
    
    /** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
    echo '
    {
      
      "fe_pago": "<div class=\'project-members text-left\' width=\'40%\' > '.$fe_pagado.' </a> </div> ",
      "mn_total": " <div width=\'20%\' > '.$mn_total.' </div>  ",
         
      "status": "<div class=\'text-center\'><span class=\'label label-'.$color.'\'>'.$status.'</span></div>",
      "information":"<div class=\'col col-sm-12 col-xs-12 col-md-12\'>  '
        . '<div class=\'col col-sm-3 col-md-3 col-xs-3\'><strong>'.ObtenEtiqueta(1547).': </strong><br>'.$id_pago.'</div>'  
        . '<div class=\'col col-sm-3 col-md-3 col-xs-3\'><strong>'.ObtenEtiqueta(1704).':</strong><br>'.$ds_descripcion_pago.'</div>'
        . '</div>",
        "espacio":"<a href=\'site/../../AD3M2SRC4/modules/reports/invoice_student_rpt.php?c='.$fl_admin_pagos.'&u='.$fl_usuario.'&i='.$fl_instituto.'&t='.$fg_motivo_pago.'\' ><i class=\'fa fa-file-pdf-o\' aria-hidden=\'true\'></i></a>  "
      
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>
  ]
}








