<?php
	
require '../../lib/general.inc.php';

$id_recibido=RecibeParametroHTML('id');
$fg_teachers=RecibeParametroNumerico('fg_teachers');
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
if($fl_term=='GG')
    $fl_clase=$fl_grupo;



$Query="(SELECT fl_clase_cg AS fl_clase,''fl_grupo, fe_formato_clase AS fe_clase,hr_formato_clase AS hr_clase ,fname_teacher,lname_teacher,REPLACE(ds_clase, '&#47;', '-')nb_programa,no_semana as term,REPLACE(ds_titulo, '&#47;', '-') ds_titulo,''fl_programa,''fl_periodo,''fg_adicional
FROM clases_globales 
WHERE fl_clase_cg=$fl_clase
)
UNION (
SELECT fl_clase,fl_grupo,fe_clase,hr_clase AS hr_clase ,fname_teacher,lname_teacher,REPLACE(nb_programa, '&#47;', '-')nb_programa ,no_grado as term,REPLACE(ds_titulo, '&#47;', '-') ds_titulo,fl_programa,fl_periodo,fg_adicional 
FROM groups_schedules
WHERE fl_clase =$fl_clase
) ";

if($fl_term=='GG'){

  $Query="
  SELECT c.fl_clase_grupo AS fl_clase,a.fl_grupo,DATE_FORMAT(c.fe_clase,'%Y-%m-%d') as fe_clase,DATE_FORMAT(c.fe_clase,'%H:%i') as hr_clase, i.ds_nombres fname_teacher,i.ds_apaterno lname_teacher, REPLACE(c.nb_clase, '/', '-')nb_programa ,h.no_semana  as term,REPLACE(a.nb_grupo, '&#47;', '-') ds_titulo,''fl_programa,''fl_periodo,''fg_adicional
     FROM c_grupo a
     JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo 
     JOIN k_alumno_grupo g ON g.fl_grupo=a.fl_grupo
     JOIN k_semana_grupo h ON c.fl_semana_grupo=h.fl_semana_grupo
	 join c_usuario i ON i.fl_usuario=c.fl_maestro 
	 WHERE c.fl_clase_grupo=$fl_clase
  ";
 

}


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
$fe_clase_zoom=$row[2];
$hr_clase_zoom=$row[3];
$nb_programa=$nb_programa."<br/> <small> $ds_titulo ";
if($fg_adicional)
    $nb_programa.=" Extraclass";
$nb_programa.=" </small>";

$Query="SELECT nb_grupo,fg_zoom FROM c_grupo WHERE fl_grupo=$fl_grupo ";
$row=RecuperaValor($Query);
$nb_grupo=str_texto($row['nb_grupo']);
$fg_zoom=$row['fg_zoom'];

if($fl_term=='CG'){
    $nb_grupo="Global Class";

    $Query="SELECT fl_clase_global FROM clases_globales WHERE fl_clase_cg=$fl_clase ";
    $row=RecuperaValor($Query);
    $fl_clase_globval=$row[0];
    $Query="SELECT fg_zoom FROM c_clase_global WHERE fl_clase_global=$fl_clase_globval ";
    $row=RecuperaValor($Query);
    $fg_zoom=$row['fg_zoom'];


}
if($fl_term=='GG'){
    $nb_grupo=ObtenEtiqueta(2521)."-".$nb_grupo;
}



#Recupermaos la liga 
#Revisa si hay una clase activa en este momento  temporal  (clase global)
$Query  = "SELECT fl_live_session_cg, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
$Query .= "FROM k_live_sesion_cg ";
$Query .= "WHERE fl_clase_cg=".$fl_clase ;	  
$row = RecuperaValor($Query);
$tabla="k_live_sesion_cg";
if(empty($row[0])){
    #Revisa si hay una clase activa en este momento, real,(clase normal groups and scheduler) 1 solo term
    $Query  = "SELECT fl_live_session, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
    $Query .= "FROM k_live_session ";
    $Query .= "WHERE fl_clase=".$fl_clase ;
    $row = RecuperaValor($Query);
    $tabla="k_live_session";
}
if($fl_term=='GG'){
    #Clase grupal mutiples terms
    $Query  = "SELECT fl_live_session_grupal, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
    $Query .= "FROM k_live_session_grupal ";
    $Query .= "WHERE fl_clase_grupo=".$fl_clase ;
    $row = RecuperaValor($Query);
    $tabla="k_live_session_grupal";

}


$fl_live_session = $row[0];
$cl_estatus = $row[1];
$ds_meeting_id = $row[2];
$ds_password_asistente = $row[3];
$zoom_url=$row[4];
$zoom_id=$row[5];

if(!empty($zoom_id)){
    $Query="SELECT host_email_zoom FROM zoom WHERE id=$zoom_id ";
    $row=RecuperaValor($Query);
    $host_email_zoom=$row['host_email_zoom'];
}


// MDB ADOBECONNECT 
$urlAdobeConnect = ObtenConfiguracion(53);
$joinURL = $urlAdobeConnect . $ds_meeting_id . "/?guestName=Admin";
$ds_liga = "<a href='$joinURL' title='Join Live Classroom' target='_blank'>" . $tit_clase_adicional . "</a>"; 
$title_zoom="";
if($fg_zoom==1){
    if(!empty($zoom_url)){
        //Zoom
        $ds_liga = "<a href='$zoom_url' title='Join Live Classroom' target='_blank'>" .$tit_clase_adicional."</a>"; 
        $joinURL =$zoom_url;
        $title_zoom="in Zoom";
    }
}


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
                        <?php if($fg_teachers==1){ 
                               

                              }else{ ?>

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
						<?php } ?>
                        <!----finaliza tabla alumnos.---->
                        <?php } ?> <br/>
					<div class="row">
					<div class="col-md-12 text-center">
                    <?php if($fg_teachers==1){ 
                            
                          }else{
                    ?>

					<a  class="btn btn-primary" href="<?php echo $joinURL ?>" title='Join Live Classroom <?php echo $title_zoom;?>' target='_blank'><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp;&nbsp;Join Live Classroom <?php echo $title_zoom;?></a>
                    

                        <?php } ?>
                    <br /><br />
                        <?php if(!empty($host_email_zoom)){
                                  //echo"<small class='text-muted'>$host_email_zoom</small>"; 
                                  
                                  echo"<a href='form-x-editable.html#' id='license' data-type='select' data-pk='$fl_live_session' data-value='$zoom_id' data-original-title='Select license' >New Zoom</a>";

                              }
                        ?>
                    </div>            
					</div>

                    </div>
                    <div class="modal-footer text-center">
	                    <a  class="btn btn-default" data-dismiss="modal"  style="font-size: 14px;border-radius: 10px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $glypicon; ?>&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                                                                                                                 
                    </div>
                    </div>
                    </div>
                    </div>
<?php 
					  echo"  
     <script src='".PATH_SELF_JS."/plugin/x-editable/moment.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/jquery.mockjax.min.js'></script>
  <script src='".PATH_SELF_JS."/plugin/x-editable/x-editable.min.js'></script>
 ";


$nb_programa = str_replace('<br/>', ' ', $nb_programa);
$nb_programa = str_replace('<br>', ' ', $nb_programa);
$nb_programa = str_replace('<small>', ' ', $nb_programa);
$nb_programa = str_replace('<small/>', ' ', $nb_programa);
$nb_programa = str_replace('<b>', ' ', $nb_programa);
$nb_programa = str_replace('<b/>', ' ', $nb_programa);
$nb_programa = str_replace('/', '_', $nb_programa);


		
?>			
	<script>
	    document.getElementById('asignar').click();//clic automatico que se ejuta y sale modal

	$(document).ready(function() {
			
	    pageSetUp();
			
	    $('#license').editable({
	        
	        source: [{
	            value: 1,
	            text: 'mario@vanas.ca'
	        }, {
	            value: 2,
	            text: 'info@vanas.ca'
	        }, {
                value:3,
                text: 'admin@vanas.ca'
	        },
	        {
	            value: 4,
	            text: 'class01@vanas.ca'
	        }],
	        display: function (value, sourceData) {
	            var colors = {
	                "": "gray",
	                1: "green",
	                2: "blue",
	                3: "blue",
	            }, elem = $.grep(sourceData, function (o) {
	               
	                return o.value == value;
	            });
	            if (elem.length) {
	                $(this).text(elem[0].text).css("color", colors[value]);
	                
	            } else {
	                $(this).empty();
	            }
	        }
            ,
	        url: 'update_zoom_licence.php',
	        name: '<?php echo $tabla."#".$fe_clase_zoom."#".$hr_clase_zoom."#".$nb_grupo." ".$nb_programa."#".$zoom_id."#".$fl_clase;?>',
	        success: function (response, newValue) {
	            $('#myModal').modal('hide');
	        }
	    });
	});



	</script>	
