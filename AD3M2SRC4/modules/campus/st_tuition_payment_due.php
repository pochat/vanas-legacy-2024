<?php 
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
?>
  
  
  
  
 <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width: 80%;margin:auto">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -28px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        

          <table class="table" id="tuition" width="100%">
            <thead>
            <tr>
                <th scope="col">Student</th>
                <th scope="col">Program</th>
                <th scope="col">Payment Number</th>
                <th scope="col">Payment amount due</th>
                 <th scope="col">Payment Date</th>
                <th scope="col">Email notification to be send on</th>
               
            </tr>
            </thead>
            <tbody>
         

        <?php

            #payment_Overdue (Pagos atrasados)
            # Define the interval of time where overdue payments should be sent (daily)
            $day_late = 1;
            //$day_late_app = 3;
            $day_late_period = 300;

            $fl_term_pago = null;
            $fl_term = null;
            $no_opcion = null;
            $no_pago = null;
            $py_date = null;
            $pg_name = null;


            #Recuperamos todos los que ya pasaron su pago.
            $Query3  = "SELECT a.fl_term_pago, a.fl_term, a.no_opcion, a.no_pago, DATE_FORMAT(a.fe_pago, '%M %e, %Y'), c.nb_programa,DATE_FORMAT(CURDATE() +1, '%M %e, %Y') fe_envio_email ";
	        $Query3 .= "FROM k_term_pago a ";
	        $Query3 .= "LEFT JOIN k_term b ON b.fl_term=a.fl_term ";
	        $Query3 .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	        $Query3 .= "WHERE DATE_SUB(CURDATE(), INTERVAL $day_late DAY) >= DATE(a.fe_pago) ";
	        $Query3 .= "AND DATE_SUB(CURDATE(), INTERVAL $day_late_period DAY) <= DATE(a.fe_pago) AND c.nb_programa IS NOT NULL ";
	        $rs3 = EjecutaQuery($Query3);
	        while($row3=RecuperaRegistro($rs3)){
		        $fl_term_pago = $row3[0];
		        $fl_term = $row3[1];
		        $no_opcion = $row3[2];
		        $no_pago = $row3[3];
		        $py_date = $row3[4];
		        $pg_name = $row3[5];
                $fe_envio_email=$row3[6];


                	# For each term, find all the active students, match student's chosen payment option with the payment option from k_term_pago	
                    $Query  = "SELECT DISTINCT(a.fl_alumno), d.ds_nombres, CASE e.fg_opcion_pago WHEN 1 THEN e.mn_a_due WHEN 2 THEN e.mn_b_due ";
                    $Query .= "WHEN 3 THEN e.mn_c_due WHEN 4 THEN e.mn_d_due END py_amount,d.ds_email, d.ds_apaterno FROM (k_alumno_grupo a, c_grupo b, k_term c) ";
                    $Query .= "LEFT JOIN c_usuario d ON d.fl_usuario=a.fl_alumno LEFT JOIN k_app_contrato e ON e.cl_sesion = d.cl_sesion ";
                    $Query .= "WHERE a.fl_grupo = b.fl_grupo AND b.fl_term = c.fl_term AND 
                    ((c.no_grado='1' AND c.fl_term=$fl_term) OR (c.no_grado<>'1' AND fl_term_ini=$fl_term))
                    AND e.fg_opcion_pago=$no_opcion AND d.fg_activo='1' ";
                    
		             $rs4 = EjecutaQuery($Query);

		                while($row4=RecuperaRegistro($rs4)){
			                $fl_alumno = $row4[0];
			                $st_fname = $row4[1];
			                $py_amount = $row4[2];
			                $ds_email = $row4[3];
			                $st_lname = $row4[4];
                            
                            if(($fl_alumno==499)&&($fl_term_pago==3221)){
                                $entro="1";
                            }else{
                                
                                # Check if the student has paid or not
                                $Query  = "SELECT COUNT(1) FROM k_alumno_pago WHERE fl_alumno=$fl_alumno AND fl_term_pago=$fl_term_pago ";
                                $row3 = RecuperaValor($Query);
                                

                                if(empty($row3[0])){

                                    $Query = "SELECT COUNT(*) FROM k_alumno_pago a
                                        JOIN k_term_pago b ON b.fl_term_pago= a.fl_term_pago
                                        WHERE a.fl_alumno=$fl_alumno AND b.no_opcion=$no_opcion AND no_pago=$no_pago ";
                                    $row3 = RecuperaValor($Query);

                                }



                                if(empty($row3[0]))
                                {
                        ?>        

                                <tr>
                                    <td><b><?php echo $st_fname." ".$st_lname; ?></b><br /><small class="text-muted"><i>Overdue</i></small></td>
                                    <td><?php echo $pg_name;?></td>
                                    <td class="text-center"><?php echo $no_pago;?></td>
                                    <td>$<?php echo  number_format($py_amount,2);?></td>                                   
                                    <td><?php echo $py_date;?></td>
                                    <td><?php echo $fe_envio_email;?></td>
                                </tr>

                        <?php
                                }
			                }                     
       
                            
                      }
            }
       ?>

            
    <?php
    #(Proximos pagos) se manda 7 dias de anticipacion a su fecha de pago.
    $day_advance = 7;
    # Upcoming tuition payment, find terms that have tuition due in $day_advance day(s)
	$Query4  = "SELECT a.fl_term_pago, a.fl_term, a.no_opcion, a.no_pago, DATE_FORMAT(a.fe_pago, '%M %e, %Y'), c.nb_programa,b.fl_term_ini,(SELECT DATE_FORMAT(a.fe_pago -7, '%M %e, %Y') )AS envio_email  ";
	$Query4 .= "FROM k_term_pago a ";
	$Query4 .= "LEFT JOIN k_term b ON b.fl_term=a.fl_term ";
	$Query4 .= "LEFT JOIN c_programa c ON c.fl_programa=b.fl_programa ";
	//$Query .= "WHERE DATE_ADD(CURDATE(), INTERVAL $day_advance DAY) = DATE(a.fe_pago) ";
    $Query4 .= "WHERE DATE(a.fe_pago) >= CURDATE() AND nb_programa IS NOT NULL ORDER BY fe_pago ASC ";
	$rs4 = EjecutaQuery($Query4);
    while($row4=RecuperaRegistro($rs4)){
		$fl_term_pago = $row4[0];
		$fl_term = $row4[1];
		$no_opcion = $row4[2];
		$no_pago = $row4[3];
		$py_date = $row4[4];
		$pg_name = $row4[5];
		$fl_term_ini=$row4[6];
        $fe_envio_email=$row4[7];

		#Cuando tiene term inicial se toma el term inicial.(para envitar envio de email erroneos antes de las fechas correspondientes)
		if(!empty($fl_term_ini)){
			$fl_term=$fl_term_ini;
            
            $Query="SELECT fl_term_pago,DATE_FORMAT(fe_pago, '%M %e, %Y') FROM k_term_pago WHERE fl_term=$fl_term and no_opcion=$no_opcion and DATE(CURDATE()) <=DATE(fe_pago) ";
            $row=RecuperaValor($Query);
            $fl_term_pago=$row['fl_term_pago'];
            $py_date=$row[1];


        }


		# For each term, find all the active students, match student's chosen payment option with the payment option from k_term_pago
		$Query5  = "SELECT DISTINCT(a.fl_alumno), b.ds_nombres, CASE c.fg_opcion_pago WHEN 1 THEN c.mn_a_due WHEN 2 THEN c.mn_b_due WHEN 3 THEN c.mn_c_due WHEN 4 THEN c.mn_d_due END py_amount, b.ds_email, b.ds_apaterno ";
		$Query5 .= "FROM k_alumno_term a ";
		$Query5 .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno ";
		$Query5 .= "LEFT JOIN k_app_contrato c ON c.cl_sesion=b.cl_sesion ";
		$Query5 .= "WHERE a.fl_term=$fl_term ";
		$Query5 .= "AND c.fg_opcion_pago=$no_opcion ";
		$Query5 .= "AND b.fg_activo='1' ";
		$rs5 = EjecutaQuery($Query5);

		while($row5=RecuperaRegistro($rs5)){
			$fl_alumno = $row5[0];
			$st_fname = $row5[1];
			$py_amount = $row5[2];
			$ds_email = $row5[3];
			$st_lname = $row5[4];

			# Check if the student has paid or not
			$Query6  = "SELECT COUNT(1) FROM k_alumno_pago WHERE fl_alumno=$fl_alumno AND fl_term_pago=$fl_term_pago ";
			$row6 = RecuperaValor($Query6);

			# If have not paid, send out reminder
			if(empty($row6[0])){
    ?>

                <tr>
                    <td><b><?php echo $st_fname." ".$st_lname; ?></b><br /><small class="text-muted"><i>Upcoming</i></small></td>
                    <td><?php echo $pg_name;?></td>
                    <td class="text-center"><?php echo $no_pago;?></td>
                    <td>$<?php echo $py_amount;?></td>
                    <td><?php echo $py_date;?></td>   
                    <td><?php echo $fe_envio_email;?></td>                     
                </tr>


    <?php

            }
        }
    }


    ?>






            </tbody>
          </table>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       <!-- <button type="button" class="btn btn-primary">Save changes</button>-->
      </div>
    </div>
  </div>
</div>


<script>
   
    $('#exampleModal').modal('show');
</script>

	