<?php
	
require '../../lib/general.inc.php';

$id_recibido=RecibeParametroHTML('id');
#Identificamos valores ya que mandamos una caden separados por comas. 
$ids=explode(',',$id_recibido);
$fl_grupo=$ids[0];
$fl_term=$ids[1];
$fl_programa=$ids[2];
$fl_clase=$ids[3];
$fl_semana=$ids[4];
$fg_extra_clase=$ids[5];

if($fl_term=='CG')
    $fl_clase=$fl_grupo;


$Query="(SELECT fl_clase_cg AS fl_clase,''fl_grupo, fe_formato_clase AS fe_clase,hr_formato_clase AS hr_clase ,fname_teacher,lname_teacher,ds_clase nb_programa,no_semana as term,ds_titulo,''fl_programa,''fl_periodo,''fg_adicional
FROM clases_globales 
WHERE fl_clase_cg=$fl_clase
)
UNION (
SELECT fl_clase,fl_grupo,fe_clase,hr_clase AS hr_clase ,fname_teacher,lname_teacher,nb_programa ,no_grado as term,ds_titulo,fl_programa,fl_periodo,fg_adicional 
FROM groups_schedules
WHERE fl_clase =$fl_clase
) ";
$row=RecuperaValor($Query);
$fl_clase=$row[0];
$fl_grupo=$row[1];
$fe_clase=GeneraFormatoFecha(str_texto($row[2]));
$hr_clase=str_texto($row[3]);
$fname_teacher=str_texto($row[4]);
$lname_teacher=str_texto($row[5]);
$nb_programa=str_texto($row[6]);
$term=$row[7];
$ds_titulo=$row[8];
$fl_programa=$row[9];
$fl_periodo=$row[10];
$fg_adicional=$row[11];
$nb_programa=$nb_programa."<br/> <small> $ds_titulo ";
if($fg_adicional)
    $nb_programa.=" Extraclass";
$nb_programa.=" </small>";

$Query="SELECT nb_grupo FROM c_grupo WHERE fl_grupo=$fl_grupo ";
$row=RecuperaValor($Query);
$nb_grupo=str_texto($row['nb_grupo']);

if($fl_term=='CG'){
    $nb_grupo="Global Class";
}


#Recupermaos la liga 
 #Revisa si hay una clase activa en este momento  temporal
$Query  = "SELECT fl_live_session_cg, cl_estatus, ds_meeting_id, ds_password_asistente ";
$Query .= "FROM k_live_sesion_cg ";
$Query .= "WHERE fl_clase_cg=".$fl_clase ;
	  
$row = RecuperaValor($Query);

if(empty($row[0])){
    #Revisa si hay una clase activa en este momento, real
    $Query  = "SELECT fl_live_session, cl_estatus, ds_meeting_id, ds_password_asistente ";
    $Query .= "FROM k_live_session ";
    $Query .= "WHERE fl_clase=".$fl_clase ;
    $row = RecuperaValor($Query);
}

$fl_live_session = $row[0];
$cl_estatus = $row[1];
$ds_meeting_id = $row[2];
$ds_password_asistente = $row[3];



// MDB ADOBECONNECT 
$urlAdobeConnect = ObtenConfiguracion(53);
$joinURL = $urlAdobeConnect . $ds_meeting_id . "/?guestName=Admin";
$ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'>" . $tit_clase_adicional . "</a>"; 







#Recuperaos alumnos para schedules.
$Query  = "SELECT fl_usuario, ds_login, ";
$concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
$Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."' ";
$Query .= "FROM c_usuario a, k_ses_app_frm_1 b ";
$Query .= "WHERE a.cl_sesion=b.cl_sesion ";
$Query .= "AND a.fl_perfil=".PFL_ESTUDIANTE." ";
$Query .= "AND b.fl_programa=$fl_programa ";
$Query .= "AND b.fl_periodo=$fl_periodo ";
$Query .= "ORDER BY ds_login";
$rs = EjecutaQuery($Query);
 for($tot_alumnos = 0; $row = RecuperaRegistro($rs); $tot_alumnos++) {
     $Query  = "SELECT fl_alumno, a.fl_grupo, nb_grupo ";
     $Query .= "FROM k_alumno_grupo a, c_grupo b ";
     $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
     $Query .= "AND fl_alumno=$row[0]";
     $row2 = RecuperaValor($Query);
     
     if($tot_alumnos % 2 == 0)
         $clase = "css_tabla_detalle";
     else
         $clase = "css_tabla_detalle_bg";
     
     
     
      if($row2[1] == $fl_grupo){
         $mostrar_tabla=true;
          $tr.="<tr >  
                <td>$row[1]</td>
                <td>$row[2]</td>
 
               </tr>";
      
      
      
      }
     
 }


###############################end function ############################################

?>

                <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="asignar">
                    Launch demo modal
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;<?php echo $nb_grupo ?></h4>
                    </div>
                    <div class="modal-body text-center">
					
					
                         <h4 class="text-center" style="font-size: 20px;"><?php echo $nb_programa; ?></h4>
                        <br />

                        <div class="row">
                             <div class="col-md-3 text-center">
									<p>&nbsp;</p>
                                <!-- <a href="<?php echo $joinURL ?>" title='Join Live Classroom' target='_blank'><i class="fa fa-file-text" aria-hidden="true" style="font-size:6em;"></i></a>
                                -->
                             </div>

                            <div class="col-md-7">


                                <table class="table" width="85%">
                                  <tbody>
                                      <tr>
                                          <td width="50%" class="text-left"><?php if(!empty($fname_teacher)){ ?>
                                                   <p> <i class="fa fa-user" aria-hidden="true"></i>&nbsp;Teacher:</p>
                                               <?php }  ?>
                                          </td>
                                
                                           <td class="text-left">
                                              <?php if(!empty($fname_teacher)) { ?>
                                                     <p><?php echo $fname_teacher." ".$lname_teacher; ?></p>
                                              <?php } ?>
                                          </td>
                                     </tr>
                                     <tr>
                                         <td class="text-left"><p> <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(1785); ?>:</p></td>
                                         <td class="text-left"><p><?php echo $fe_clase; ?></p></td>
                                     </tr>

                                     <tr>
                                          <td class="text-left"><p> <i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(1786); ?>:</p></td>
                                          <td class="text-left"><p><?php echo $hr_clase; ?></p></td>

                                     </tr>   
                                         <td class="text-left"> <?php if($fl_term=='CG'){ ?>
                                           <p> <i class="fa fa-table" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(1788); ?>:</p>
                                            <?php }else{ ?>
                                            <p> <i class="fa fa-table" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(1787); ?>:</p>
                                            <?php } ?>
                                         </td>
                                         <td class="text-left"><p><?php echo $term; ?></p></td>

                                      </tr>
                                    

                                  </tbody>
                                </table>


							
                                

                            </div>

                        </div>


                            
                        	<br />
		                   
                        <?php if($mostrar_tabla){ ?>
                        <!-----Se colocan los laumnos.--->
                        <div class="row">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8 ">
                                  <div class="table-responsive">
                                        <table class="table table-striped" width="100%">
                                            <thead >
                                                <tr style="background:#0092DC;">
                                                    <th style="color:#fff;">Student</th>
                                                    <th style="color:#fff;">Name</th>
                                                </tr>

                                            </thead>
                                            <?php echo $tr; ?> 

                                        </table>
                                 </div>

                            </div>
                            <div class="col-md-2">&nbsp;</div>
                           
                        </div>
						
                        <!----finaliza tabla alumnos.---->
                        <?php } ?> <br/>
					<div class="row">
					<div class="col-md-12 text-center">
					<a  class="btn btn-primary" href="<?php echo $joinURL ?>" title='Join Live Classroom' target='_blank'><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp;&nbsp;Join Live Classroom</a>
                    </div>            
					</div>

                    </div>
                    <div class="modal-footer text-center">
	                    <a  class="btn btn-default" data-dismiss="modal"  style="font-size: 14px;border-radius: 10px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $glypicon; ?>&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                                                                                                                 
                    </div>
                    </div>
                    </div>
                    </div>
					
					
					
	<script>
		document.getElementById('asignar').click();//clic automatico que se ejuta y sale modal
	</script>	
