<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_LICENCES, $permiso)) {
   MuestraPaginaError(ERR_SIN_PERMISO);
   exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $mn_mensual = RecibeParametroFlotante('mn_mensual');
  $mn_anual = RecibeParametroFlotante('mn_anual');
  $no_ini = RecibeParametroNumerico('no_ini');
  $no_fin = RecibeParametroNumerico('no_fin');

  #IDENTIFICAMOS CUAL ES SU ULTIMO REGISTRO DE LA TABLA DE PRECIO ACTUALES(OFICIAL).
  $Query="SELECT MAX(fl_princing) AS fl_princing FROM c_princing ";
  $row=RecuperaValor($Query);
  $ultm_id=$row[0];
  
  #eliminamos registrosactuales. y volvemos a insertar.
  EjecutaQuery("DELETE FROM c_princing WHERE fl_instituto=$clave");
  $contador=0;
  $tot_registros = RecibeParametroNumerico('tot_registros');
  for($i = 0; $i <= $tot_registros; $i++) {

      $contador++;
      //$_POST[''];
    
      $registro=$contador - 1;

      $fl_princing = RecibeParametroNumerico('fl_princing_'.$i);
      $spinner1 = RecibeParametroNumerico('no_ini_'.$registro);
      
      $contador=$i+1;
      $spinner2= RecibeParametroNumerico('spinner2_'.$contador);

      $mn_mensual = RecibeParametroFlotante('mn_mensual_'.$i);
      $mn_anual = RecibeParametroFlotante('mn_anual_'.$i);
      
      $ds_descuento_licencia =RecibeParametroFlotante('mn_porcentaje_mes_'.$contador);
      $ds_descuento_mensual =RecibeParametroFlotante('mn_porcentaje_'.$i);
      
      $checked=RecibeParametroBinario('check_'.$i);

      if(empty($ds_descuento_mensual))
          $ds_descuento_mensual="NULL";
      if(empty($ds_descuento_anual))
          $ds_descuento_anual="NULL";
            
      if(($spinner2==0)||(empty($spinner2)))
        $spinner2="NULL";

        #se realiza insert en la tabla oficial.
        $Query="INSERT INTO c_princing (fl_princing,fl_instituto,no_ini ,no_fin,ds_descuento_mensual,mn_mensual,mn_descuento_licencia, ";
        $Query .=" mn_anual,fg_activo ) ";
        $Query.="VALUES ($fl_princing,$clave,$spinner1,$spinner2,$ds_descuento_mensual,$mn_mensual,$ds_descuento_licencia,$mn_anual,'$checked')";
        $fl_nuevo_princing=EjecutaInsert($Query);
  
  }

  #Verificamos en que rango se encuentran sus licencias si tiene plan y actualizamos.
  $Query="SELECT fl_current_plan,no_total_licencias FROM k_current_plan WHERE fl_instituto=$clave ";
  $row=RecuperaValor($Query);
  $fl_current_plan=$row['fl_current_plan'];
  $no_total_licencias=$row['no_total_licencias'];
  if(!empty($fl_current_plan)){
      
      $Queryl="SELECT fl_princing FROM c_princing where $no_total_licencias >=no_ini AND $no_total_licencias <= no_fin where fl_instituto=$clave ";
      $rol=RecuperaValor($Queryl);
      $fl_princing=$rol[0];

      EjecutaQuery("UPDATE k_current_plan SET fl_princing=$fl_princing WHERE fl_instituto=$clave ");

  }


  
  #Actualizamos precios de desbloqueo de curso defualt.
  if($clave==1){
  
    $porcentaje_mes=RecibeParametroFlotante('porcentaje_mes');
    $porcentaje_anio=RecibeParametroFlotante('porcentaje_anio');
    $mn_mes=RecibeParametroFlotante('mn_mes');
    $mn_anio=RecibeParametroFlotante('mn_anio');
    
    $Query="UPDATE c_princing_course SET ds_descuento_mensual=$porcentaje_mes, ds_descuento_anual=$porcentaje_anio ,mn_mensual=$mn_mes ,mn_anual=$mn_anio ";

    EjecutaQuery($Query);
  
  }
  
  # Redirige al listado
 // header("Location: pricing_frm.php");
  # Redirige al listado
  header("Location: ".ObtenProgramaBase());

?>