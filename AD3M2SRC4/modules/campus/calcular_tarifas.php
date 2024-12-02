<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';

  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fg_tarifa = RecibeParametroNumerico('fg_tarifa');
 
 
  #Recuperamos la tarifas 
   #Recuperamos costos internacionales.
      if($fg_tarifa==1){
		  $Qeu="SELECT mn_app_fee_internacional,mn_tuition_internacional,mn_costs_internacional,ds_costs_internacional ";
		  $Qeu.=",mn_a_due_internacional, mn_a_paid_internacional, mn_b_due_internacional, mn_b_paid_internacional, mn_c_due_internacional, mn_c_paid_internacional, mn_d_due_internacional, mn_d_paid_internacional, ";
		  $Qeu.="no_a_payments_internacional, no_b_payments_internacional,no_c_payments_internacional,no_d_payments_internacional,  ";
		  $Qeu.="ds_a_freq_internacional,ds_b_freq_internacional,ds_c_freq_internacional,ds_d_freq_internacional ";	  
      }else{
		  $Qeu.="SELECT mn_app_fee,mn_tuition,mn_costs,ds_costs ";      
		  $Qeu.=",mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid,  ";
		  $Qeu.="no_a_payments, no_b_payments,no_c_payments,no_d_payments,  ";
		  $Qeu.="ds_a_freq,ds_b_freq,ds_c_freq,ds_d_freq ";	  

      }
      
      $Qeu.="
             FROM k_programa_costos
             WHERE fl_programa=$fl_programa ";
      $row=RecuperaValor($Qeu);
      $mn_app_fee=$row[0];
      $mn_tuition=$row[1];
      $mn_costs=$row[2];
      $ds_costs=str_texto($row[3]);
    
      if(empty($mn_app_fee))
          $mn_app_fee=0.0;
      if(empty($mn_tuition))
          $mn_tuition=0.0;
      if(empty($mn_costs))
          $mn_costs=0.0;
      
      
      $mn_a_due = $row[4];
      if(empty($mn_a_due))
          $mn_a_due = 0.0;
      $mn_a_paid = $row[5];
      if(empty($mn_a_paid))
          $mn_a_paid = 0.0;
      $mn_b_due = $row[6];
      if(empty($mn_b_due))
          $mn_b_due = 0.0;
      $mn_b_paid = $row[7];
      if(empty($mn_b_paid))
          $mn_b_paid = 0.0;
      $mn_c_due = $row[8];
      if(empty($mn_c_due))
          $mn_c_due = 0.0;
      $mn_c_paid = $row[9];
      if(empty($mn_c_paid))
          $mn_c_paid = 0.0;
      $mn_d_due = $row[10];
      if(empty($mn_d_due))
          $mn_d_due = 0.0;
      $mn_d_paid = $row[11];
      if(empty($mn_d_paid))
          $mn_d_paid = 0.0;
	  
	  $no_a_payments=$row[12];
	  $no_b_payments=$row[13];
	  $no_c_payments=$row[14];
	  $no_d_payments=$row[15];
	  
	  $ds_a_freq=str_texto($row[16]);
	  $ds_b_freq=str_texto($row[17]);
	  $ds_c_freq=str_texto($row[18]);
	  $ds_d_freq=str_texto($row[19]);
	  
	  
	  
      
      
      $mn_tot_tuition = $mn_tuition + $mn_costs;
      $mn_tot_program = $mn_tot_tuition + $mn_app_fee;
      
 
     
  
	  $result['fg_tarifa']=1;
	  $result['mn_app_fee']=$mn_app_fee;
	  $result['mn_tuition']=$mn_tuition;
      $result['no_costos_ad']=$mn_costs;
      $result['ds_costs']= $ds_costs;
	  $result['total_tuition']= number_format($mn_tot_tuition,2,'.','');
	  $result['total']= number_format($mn_tot_program,2,'.','');
	  
	  #Option payment
	  $result['no_a_payments']=$no_a_payments;
	  $result['ds_a_freq']=$ds_a_freq;
	  $result['amount_due_a']= number_format($mn_a_due,2,'.','');
	  $result['amount_paid_a']= number_format($mn_a_paid,2,'.','');
	  
	  $result['no_b_payments']=$no_b_payments;
	  $result['ds_b_freq']=$ds_b_freq;
	  $result['amount_due_b']= number_format($mn_b_due,2,'.','');
	  $result['amount_paid_b']= number_format($mn_b_paid,2,'.','');
	  
	  $result['no_c_payments']=$no_c_payments;
	  $result['ds_c_freq']=$ds_c_freq;
      $result['amount_due_c']= number_format($mn_c_due,2,'.','');
	  $result['amount_paid_c']= number_format($mn_c_paid,2,'.','');
	  
	  $result['no_d_payments']=$no_d_payments;
	  $result['ds_d_freq']=$ds_d_freq;
	  $result['amount_due_d']= number_format($mn_d_due,2,'.','');
	  $result['amount_paid_d']= number_format($mn_d_paid,2,'.','');
      
	  
	  
	  echo json_encode((Object) $result);
 
 
 
 
?>

