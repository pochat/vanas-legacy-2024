<?php 
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
?>
 <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width: 80%;margin:auto">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Student Data Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -28px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        

          <table class="table" id="tuition" width="100%">
            <thead>
            <tr>
                <th scope="col">Student Name</th>
                <th scope="col">Country</th>
                <th scope="col">Program Term</th>
                <th scope="col">Status</th>
                 <th scope="col">Payment Date</th>
                <th scope="col">Email notification to be send on</th>
               
            </tr>
            </thead>
            <tbody>
         

        <?php

            


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

	