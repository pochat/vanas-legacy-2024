<?php

  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recupera el usuario actual
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $fe_pagoD = RecibeParametroHTML('fe_pagoD');
  $fe_pagoM = RecibeParametroHTML('fe_pagoM');
  $fe_pagoA = RecibeParametroHTML('fe_pagoA');
  $fe_pago = $fe_pagoA."-".$fe_pagoM."-".$fe_pagoD;
  $no_payments = RecibeParametroHTML('no_payments');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $i = RecibeParametroNumerico('i');
  $fl_term_ini = RecibeParametroNumerico('fl_term_ini');
  
  # Obtenemos la duracion del programa
  $row = RecuperaValor("SELECT no_semanas FROM k_programa_costos WHERE fl_programa=$fl_programa ");
  $duracion_meses= $row[0]/4; //numero de meses que dura el programa
  $meses = $duracion_meses/$no_payments;
  # Primer pago 
  $fe_pago = Date('d-m-Y', strtotime($fe_pago));
  
  # Script para recoger los datos y enviarlos
  echo "
  <script  type='text/javascript'>";
    
    # Recorremos las fechas de los pagos
    for($j=1;$j<=$no_payments; $j++) {
      # Verificamos si encontro un break utiliza la fecha guardada
      if($encontro)
        $fe_pago =  $fe_guardarda;
        
      # Aumenta los meses que dura los pagos
      if($j==1)
        $fe_pago = $fe_pago;
      else
        $fe_pago = date('Y-m-d',strtotime('+'.$meses.' month '.$fe_pago.''));
        
      # Busca que la fecha no se encuentre en un break
      $Query = "SELECT  fe_ini FROM c_break WHERE '$fe_pago' BETWEEN fe_ini AND fe_fin ";
      $row =  RecuperaValor($Query);
      $fe_ini = $row[0];

      # Si existe un registro reducira 4 dias antes la fecha inicial del break
      if(!empty($fe_ini)) {
        $fe_guardarda = $fe_pago;
        $fe_pago = date('Y-m-d',strtotime ( '-4 day' , strtotime ( $fe_ini ) ) );
        $encontro = True;
      }
      else{
        $fe_pago ;
        $encontro = False;
      }

      #Fechas de pagos que son enviadas a los campos
      $fe_pago = date('d-m-Y',strtotime($fe_pago)) ;
      
      # Enviamos los datos a terms_frm.php con el script
      echo "
         $('#fe_pago_".$i."_".$j."').val('$fe_pago'); ";
    }
  echo "
  </script>";
?>