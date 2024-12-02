<?php
	
require '../../lib/general.inc.php';

#Recibe Parametros.
$fl_term=RecibeParametroNumerico('fl_term');
$fl_programa_users=$_POST['fl_programa_users'];
$fe_inicio=$_POST['fe_inicio'];
$fe_final=$_POST['fe_final'];
$id=$_POST['id'];

#Si vien id entonces queire decir que ya viene de la BD.
if((!empty($id))&&($id<>'undefined')){
  $valores=explode('#',$id); 
  $fl_clase_calendar=$valores[0];
  $fl_term=$valores[1];
  $fl_programa=$valores[2];
  $fl_leccion=$valores[3];
  $no_grado=$valores[4];
  $fl_periodo=$valores[5];
  $fl_maestro=$valores[6];
  

}else{

  #Identificamos valores ya que mandamos una caden separados por # estos valores provienen cuando se agrega al calendario. del barra lateral dercha.
  $valores=explode('#',$fl_programa_users);
  $fl_programa=$valores[0];
  $no_grado=$valores[1];
  $allusers=$valores[2];
  $fl_periodo=$valores[3];
  $fg_tipo_estudiante=$valores[4];
  $fg_tipo_clase=$valores[5];

  //if($fg_tipo_clase=='multiple_term'){
  //    $fg_tipo_clase=3;
  //}


}

#Recuperamos de la bd es un update.
if($fl_clase_calendar){
    
    $QueryS="SELECT no_semana,fl_leccion,ds_titulo,fg_tipo_clase,fl_maestro,fl_periodo,nb_grupo,ds_titulo,fg_repeat FROM 
                  k_clase_calendar 
                  WHERE fl_clase_calendar=$fl_clase_calendar ";
    $rows=RecuperaValor($QueryS);
    $fl_leccion=$rows['fl_leccion'];
    $no_semana=$rows['no_semana'];
    $nb_clase=$rows['ds_titulo'];
    $fg_tipo_clase=$rows['fg_tipo_clase'];
    $nb_grupo=$rows['nb_grupo'];
    $fl_maestro=$rows['fl_maestro'];
    $fl_periodo=$rows['fl_periodo'];
    $nb_grupo=$rows['nb_grupo'];
    $fg_repeat=$rows['fg_repeat'];
   

    #Reciuperems los terms elegidos.
    $Querysv="SELECT fl_term FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
    $rsm=EjecutaQuery($Querysv);
    $terms_bd=array();
    for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
        
        array_push($terms_bd, $rowm[0]);   

    }

    #Son multiples term.
    if(empty($fl_term)){
        $Query="SELECT nb_periodo FROM c_periodo WHERE fl_periodo=$fl_periodo ";
        $row=RecuperaValor($Query);
        $nb_periodo=$row['nb_periodo'];
        $nb_programa=$nb_periodo;
    }else{

        #Recuperamos los datos del term y del programa.
        $Query="SELECT a.no_grado,b.nb_periodo,p.nb_programa,a.fl_periodo 
            FROM k_term a
            JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
            JOIN c_programa p ON p.fl_programa=a.fl_programa WHERE  a.fl_term=$fl_term AND a.fl_programa=$fl_programa ";
        $row=RecuperaValor($Query);
        $fl_periodo=$row['fl_periodo'];
        $no_grado=$row['no_grado'];
        $nb_periodo=$row['nb_periodo'];
        $nb_programa=$row['nb_programa'];
    }
    



    #Recuperamos los user de esa clase esatn BD.
    $Query1="SELECT fl_alumno FROM k_clase_calendar_alumno WHERE fl_clase_calendar=$fl_clase_calendar ";
    $rs1 = EjecutaQuery($Query1);
    $allusers="";
    for($x=1;$row=RecuperaRegistro($rs1);$x++){
        $fl_alumno=$row['fl_alumno'];

        $allusers .= "'$fl_alumno',";

    }
    $allusers.="''";

    #Para identifiocar campos que no tienen informacion.
    if(empty($fl_maestro))
        $fl_maestro_err=ERR_REQUERIDO;
    if(empty($nb_grupo))
        $nb_grupo_err=ERR_REQUERIDO;
    if(empty($nb_clase))
        $nb_clase_err=ERR_REQUERIDO;


}else{
    

    #Verifica si existe el grupo.
    $Query="SELECT COUNT(*)FROM c_grupo where fl_term=$fl_term ";
    $row=RecuperaValor($Query);
    $existe_grupo=$row[0];

    if(empty($existe_grupo)){
        $no_semana=0;
    }

    #Recuperamos los datos del term y del programa.
    $Query="SELECT a.no_grado,b.nb_periodo,p.nb_programa,a.fl_periodo 
            FROM k_term a
            JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
            JOIN c_programa p ON p.fl_programa=a.fl_programa WHERE  a.fl_term=$fl_term AND a.fl_programa=$fl_programa ";
    $row=RecuperaValor($Query);
    $fl_periodo=$row['fl_periodo'];
    $no_grado=$row['no_grado'];
    $nb_periodo=$row['nb_periodo'];
    $nb_programa=$row['nb_programa'];


    #Recuperamos la semana que corresponde y el nombre; estos datos se determinan del No de Leccion.
    $QueryS ="SELECT DISTINCT b.no_semana, b.fl_leccion,b.ds_titulo 
            FROM k_semana a
            JOIN c_leccion b ON a.fl_leccion=b.fl_leccion
            JOIN k_term t ON t.fl_term=a.fl_term WHERE t.fl_term=$fl_term AND b.fl_programa=$fl_programa AND b.no_grado=$no_grado ";
    $QueryS.=" ORDER BY b.no_semana DESC ";
    $rows=RecuperaValor($QueryS);
    $fl_leccion=$rows['fl_leccion'];
    $no_semana=$rows['no_semana'];
    $ds_titulo=$rows['ds_titulo'];

    if(empty($existe_grupo)){
        $no_semana=null;
    }


    #Pasamos a verificar tabla temporal es donde se estan agendando las nuevas clases.
    if(empty($no_semana)){
        $QueryS="SELECT no_semana,fl_leccion,ds_titulo FROM 
                  k_clase_calendar 
                  WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND no_grado=$no_grado AND fl_periodo=$fl_periodo ORDER BY no_semana DESC ";
        $rows=RecuperaValor($QueryS);
        $fl_leccion=$rows['fl_leccion'];
        $no_semana=$rows['no_semana'];
        $ds_titulo=$rows['ds_titulo'];

    }


    $no_semana_siguiente=$no_semana+1;

    if($no_semana<12){
        #Recuperamos la leccion 1 por default
        $Queryl="SELECT fl_leccion,ds_titulo,no_semana FROM c_leccion WHERE fl_programa=$fl_programa AND no_grado=$no_grado AND no_semana=$no_semana_siguiente ";
        $rowe=RecuperaValor($Queryl);
        $fl_leccion=$rowe['fl_leccion'];
        $nb_clase=$rowe['ds_titulo'];
        $no_semana=$rowe['no_semana'];
    }





}


?>
<style>
.smart-form .col {
    padding-right: 10px;
    padding-left: 10px;
}
.ui-spinner-down, .ui-spinner-up {
    background: #0092cd;
}
.ui-spinner-up:active, .ui-spinner-up:focus, .ui-spinner-up:hover {
    background: #0092cd;
}
.ui-spinner-down:active, .ui-spinner-down:focus, .ui-spinner-down:hover {
    background: #0092cd;
}
</style>

<!--------------modal para edicion de horarios y asignaciones terms y grupos. (clicka un evento del calendario)---------------->
<div class="modal fade" id="modal_event_calendar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:60%;margin:auto;margin-top:70px;">
    <div class="modal-content" id="evento_modal_edit">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-book" aria-hidden="true"></i> <?php echo $nb_programa;?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">     

            <h4 class="text-center" style="font-size: 20px;">Cycle: <?php echo $nb_periodo;?></h4>
            <h5 class="text-center">Term: <?php echo $no_grado;?></h5>

           <div class="row">
               <div class="col-md-1"></div>
               <div class="col-md-10">
                   <input type="hidden" name="fl_clase_calendar" id="fl_clase_calendar" value="<?php echo $fl_clase_calendar;?>"/>
                   <input type="hidden" name="fg_tipo_estudiante"id="fg_tipo_estudiante" value="<?php echo $fg_tipo_estudiante;?>" />

                   <?php 
                    #Identificamos que tipo de clase va ser.
                    $opc = array('Single Class','Multiple Class');
                    $val = array('2','3'); 
                    $opc = array('Single Class');
                    $val = array('2');
                    Forma_CampoSelect('Class Type', True, 'fg_tipo_clase', $opc, $val, $fg_tipo_clase, !empty($fg_tipo_clase_err)?$fg_tipo_clase_err:NULL, False, '', 'left', 'col col-sm-4', 'col col-sm-6');                 
                    
                    echo"<br>";

                    echo"<div id='view_terms' style='display:none;'>
                            <div class='col col-sm-4 tex-left'>
			                     <label class='control-label text-align-left' style='float:left;'><strong>*".ObtenEtiqueta(422)."</strong></label>
			                 </div>                            
                            <label class='select col-md-6'>";
                                $concat = array('nb_programa', "' ('", 'ds_duracion', "')'");
                                $Queryp  = "SELECT ".ConcatenaBD($concat)." 'nb_programa',  nb_periodo, no_grado,fl_term ";
                                $Queryp .= "FROM k_term a, c_programa b, c_periodo c ";
                                $Queryp .= "WHERE a.fl_programa=b.fl_programa ";
                                $Queryp .= "AND a.fl_periodo=c.fl_periodo ";
                                $Queryp .= "AND fg_activo='1'";
                                $Queryp ."AND b.fg_archive='0' ";
                                $Queryp .= "ORDER BY nb_programa, no_grado";
                                CampoSelectBDF('fl_terms', $Queryp, !empty($p_actuals)?$p_actuals:NULL, '', False, 'multiple', $terms_bd, 'fl_terms');
                                echo"<input type=\"hidden\" name=\"fl_grupos_i\" id=\"fl_grupos_i\" value=\"$fl_grupos_i\">";
                    echo"   </label>
                            <br><br>
                        </div>";

                    echo"<div id='view_form' style='display:none;'>";

                    $Query="SELECT CONCAT(ds_nombres,' ',ds_apaterno)ds_nombres,fl_usuario FROM c_usuario a WHERE fl_perfil=2 AND fg_activo='1' ORDER BY ds_nombres ASC; ";
                    Forma_CampoSelectBD('Teacher', False, 'fl_maestro', $Query,$fl_maestro,$fl_maestro_err,true,'','','col col-sm-4','col-sm-6');
                    echo"<br/><br/><br/>";


                    Forma_CampoTexto(ObtenEtiqueta(420), True, 'nb_grupo', $nb_grupo, 50, 36, $nb_grupo_err,'','','','','','','left','col col-sm-4','col col-sm-6');
                    echo"<br>";
                    Forma_CampoTexto(ObtenEtiqueta(390), True, 'no_semana', $no_semana, 50, 36, $no_semana_err,'','','','','','','left','col col-sm-4','col col-sm-6');                
                    echo"<br>";
                    Forma_CampoTexto(ObtenEtiqueta(1234), True, 'nb_clase', $nb_clase, 50, 36, $nb_clase_err,'','','','','','','left','col col-sm-4','col col-sm-6');                  
                    echo"</div>";
                    echo"<br>";
                    #Identificamos como se va repetir el evento.
                    $opc = array('Never','Daily', 'Weekly');
                    $val = array('1','2','3');
                    Forma_CampoSelect('Repeat-Event', True, 'fg_repeat', $opc, $val, $fg_repeat, !empty($fg_repeat_err)?$fg_repeat_err:NULL, True, '', 'left', 'col col-sm-4', 'col col-sm-6');
                    echo"<br>";
                    echo"<div id='muestra_calendario' class='hidden'>";
                    echo "<div class='row form-group smart-form'>
                                    <label class='input col col-sm-4'><b>*End Event</b></label>
                                    <div class='col col-sm-6'><label class='input col col-sm-8 no-padding $ds_error'>";
                                        CampoTexto('fe_finish', $fe_fin, 10, 10, $fe_fin, False);
                                        Forma_Calendario('fe_finish');
                    echo "              </label>
                                    </div>
                          ";
                    echo" </div>";
                    echo"</div>";
                    echo"<br>";
                    echo"<div id='weekly_repeats' class='hidden'>
                              <div class='form-group'>
                                    <div class='col col-sm-4'>
                                        <b>How many repeats:</b>
                                    </div>
                                    <div class='col col-sm-6'> 
                                        <input class='form-control spinner-left'  id='no_semanas' name='no_semanas' value='12' type='text'>
                                    </div>
                               </div>
                        </div>";
                    echo"<br>";
                    if($fl_clase_calendar){
                        #Identificamos como se va repetir el evento.
                        $opc = array('This event','This and all following events');
                        $val = array('1','2');
                        Forma_CampoSelect('Edit', True, 'fg_edit', $opc, $val, $fg_edit, !empty($fg_edit_err)?$fg_edit_err:NULL, False, '', 'left', 'col col-sm-4', 'col col-sm-6');
                        echo"<br>";
                    }else{
                        echo"<input type='hidden' name='fg_edit' id='fg_edit' value='0' >";
                    }


                    ?>
                   <hr />
               </div>
               <div class="col-md-1"></div>
           </div>

           <div class="row">
               <div class="col-md-1"></div>
               <div class="col-md-10">
                   <?php 
                   $all_user_selected="";
                   if($fg_tipo_estudiante==2){
                       $Query3="SELECT a.fl_usuario, ds_fname,ds_lname FROM c_usuario a JOIN k_ses_app_frm_1 b ON a.cl_sesion=b.cl_sesion WHERE b.fl_programa=$fl_programa AND a.fl_usuario IN($allusers) ";   
                   }else{
                       $Query3="SELECT cl_sesion, ds_fname,ds_lname  FROM  k_ses_app_frm_1 WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND cl_sesion IN($allusers) ";
                   }
                   
                   $rs3 = EjecutaQuery($Query3);
                   for($x3=1;$row3=RecuperaRegistro($rs3);$x3++){
                       $ds_fname=$row3['ds_fname'];
                       $ds_lname=$row3['ds_lname'];
                       $fl_alumno=$row3[0];

                       $all_user_selected.="'$fl_alumno',";
                   ?>
                       <div class="col-md-4">
                            <span><img style="width:35px;" class="img-thumbnail" src="../../../images/avatar_default.jpg"/> <?php echo $ds_fname." ".$ds_lname;?></span><br />
                       </div>
                   <?php 
                   }
                   $all_user_selected.="''";
                   ?>
               </div>
               <div class="col-md-1"></div>
            </div>

            <?php if($fl_clase_calendar){
            
                        #Por medio del grupo obtenemos el fl_grupo.
                        $Query="SELECT fl_grupo FROM c_grupo WHERE nb_grupo='$nb_grupo' ";
                        $row=RecuperaValor($Query);
                        $fl_grupo=$row[0];




                      if($fg_tipo_clase==2){
                          #Revisa si hay una clase activa en este momento, real
                          $Query  = "SELECT fl_live_session, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
                          $Query .= "FROM k_live_session ";
                          $Query .= "WHERE fl_clase=".$fl_clase ;
                          $row = RecuperaValor($Query);
                      }
                      if($fg_tipo_clase==3){
                       
                          $Query  = "SELECT fl_live_session_grupal, cl_estatus, ds_meeting_id, ds_password_asistente,zoom_url,zoom_id ";
                          $Query .= "FROM k_live_session_grupal ";
                          $Query .= "WHERE fl_clase_grupo=".$fl_clase ;
                          $row = RecuperaValor($Query);
                      }
            
            
            ?>
            <div class="row hidden">
                <div class="col-md-12 text-center"><br />
                    <a  class="btn btn-default" href="<?php echo $joinURL ?>" title='Join Live Classroom' target='_blank'><i class="fa fa-external-link" aria-hidden="true"></i>&nbsp;&nbsp;Join Live Classroom</a>
                    <br />
                </div>
            </div>

            <?php } ?>


           <?php if($fl_clase_calendar){?>
              <br />
              <div class="row hidden">
                  <div class="col-md-1"></div>
                  <div class="col-md-10">
                      <div class="col-md-12">
                          <table>
                              <tr><td>
                                      <a href="javascript:InsertaExtraClass(<?php echo $fl_clase_calendar;?>);"><img src="/AD3M2SRC4/images/icon_add.png" title="Add" record=""> Add Extraclass</a>
                                  </td>
                                  <td></td>
                                  <td></td>
                                  <td></td>

                              </tr>
                          </table>
                              <div id="extraclass"></div>
                          
                      </div>
                 <div class="col-md-1"></div>
             </div>



           <?php } ?>

      </div>
      <div class="modal-footer text-center">
        <?php if($fl_clase_calendar){?>
        <a href="javascript:void(0);" style="float:left;" onclick="DeleteEvent(<?php echo $fl_clase_calendar;?>);">Delete</a>
        <?php } ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="ActionEvent();">Save changes</button>
      </div>
    </div>
  </div>
</div>
<div id="shceduler_save_event"></div>
<div id="delete_event"></div>
<!-------------------------------------------------------------------------------------------->				
<script>
    $(document).ready(function () {      
        pageSetUp();
        $("#no_semanas").spinner();
        <?php 
        if($fl_clase_calendar){
            echo" document.getElementById('fg_tipo_clase').disabled = true; 
                  document.getElementById('fg_repeat').disabled = true;
                  ";
            if($fg_repeat==1){
                echo" document.getElementById('fg_edit').disabled = true;   ";
            }

        }
        ?>

        $('#fg_tipo_clase').change(function () {
            MuestraInfoForm();
           
        });
        $('#fg_repeat').change(function () {
            var fg_repeat = document.getElementById('fg_repeat').value;

            if (fg_repeat == 2) {
                $('#muestra_calendario').removeClass('hidden');

            } else {
                $('#muestra_calendario').addClass('hidden');
            }
            if(fg_repeat == 3) {
                $('#weekly_repeats').removeClass('hidden');
            } else {
                $('#weekly_repeats').addClass('hidden');
            }

        });
    });

    function MuestraInfoForm() {

        var fg_tipo_clase = document.getElementById('fg_tipo_clase').value;
        if (fg_tipo_clase > 1) {
            document.getElementById("view_form").style.display = "block";
        } else {
            document.getElementById("view_form").style.display = "none";
        }
        if (fg_tipo_clase == 3) {
            document.getElementById("view_terms").style.display = "block";
        }
        else {
            document.getElementById("view_terms").style.display = "none";
        }


    }


    $('#modal_event_calendar').modal('show');

    function ActionEvent() {

        var fg_tipo_clase = document.getElementById('fg_tipo_clase').value;
        //var fl_terms = document.getElementById('fl_terms').value;
        var fl_maestro = document.getElementById('fl_maestro').value;
        var nb_grupo = document.getElementById('nb_grupo').value;
        var no_semana = document.getElementById('no_semana').value;
        var nb_clase = document.getElementById('nb_clase').value;
        var fl_clase_calendar = document.getElementById('fl_clase_calendar').value; 
        var fg_tipo_estudiante = document.getElementById('fg_tipo_estudiante').value;
        var no_semanas = document.getElementById('no_semanas').value;
        var fe_inicio="<?php echo $fe_inicio; ?>";
        var fe_final="<?php echo $fe_final;?>";
        var fl_term="<?php echo $fl_term;?>";
        var no_grado="<?php echo $no_grado;?>";
        var fl_periodo="<?php echo $fl_periodo;?>";
        var fl_programa="<?php echo $fl_programa;?>";
        var fl_leccion="<?php echo $fl_leccion;?>";
        var fl_terms = $('#fl_terms').val();
        var allusers = "<?php echo $all_user_selected;?>";
        var fg_repeat = document.getElementById('fg_repeat').value;
        var fe_fin = document.getElementById('fe_finish').value;
        var fg_edit = document.getElementById('fg_edit').value;

        if (fg_tipo_clase == 0) {
            $('#div_fg_tipo_clase').addClass('has-error');
            return;
        } else {
            $('#div_fg_tipo_clase').removeClass('has_error');
        }




        if (fg_repeat == 0) {
            $('#div_fg_repeat').addClass('has-error');
            return;
        } else {
            $('#div_fg_repeat').removeClass('has-error');

            if(fg_repeat == 2){


            }

        }
        
        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_save_event.php',
            data: 'fg_tipo_clase=' + fg_tipo_clase +
                  '&fg_repeat=' + fg_repeat +
                  '&fe_fin=' + fe_fin +
                  '&fg_tipo_estudiante='+fg_tipo_estudiante+
                  '&allusers='+allusers+
                  '&fl_terms=' + fl_terms+
                  '&fl_maestro=' + fl_maestro+
                  '&fl_term='+fl_term+
                  '&nb_grupo=' + nb_grupo+
                  '&no_semana=' + no_semana+
                  '&no_grado='+no_grado+
                  '&fe_inicio='+fe_inicio+
                  '&fe_final='+fe_final+
                  '&fl_periodo='+fl_periodo+
                  '&fl_programa=' + fl_programa +
                  '&fl_clase_calendar='+fl_clase_calendar+
                  '&fl_leccion=' + fl_leccion +
                  '&fg_edit=' + fg_edit +
                  '&no_semanas='+no_semanas+
                  '&nb_clase=' + nb_clase,
            async: false,
            success: function (html) {
                $('#shceduler_save_event').html(html);
                MuestraCalendar();
                $('#modal_event_calendar').modal('hide');
            }
        });


    }

    MuestraInfoForm();

    function DeleteEvent(fl_clase_calendar) {


        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_delete_event.php',
            data: 'fl_clase_calendar=' + fl_clase_calendar ,
            async: false,
            success: function (html) {
                $('#delete_event').html(html);
                 MuestraCalendar();
            }
        });
        $('#modal_event_calendar').modal('hide');


    }

    function InsertaExtraClass(fl_clase_calendar) {

        var fl_term="<?php echo $fl_term;?>";
        var no_grado="<?php echo $no_grado;?>";
        var fl_periodo="<?php echo $fl_periodo;?>";
        var fl_programa="<?php echo $fl_programa;?>";
        var fl_leccion = "<?php echo $fl_leccion;?>";
        var no_semana = "<?php echo $no_semana;?>";
        var fg_accion = "insert";

        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_extraclass_event.php',
            data: 'fl_clase_calendar=' + fl_clase_calendar+
                  '&fl_term=' + fl_term+
                  '&no_grado='+no_grado+
                  '&fl_periodo=' + fl_periodo +
                  '&no_semana=' + no_semana +
                  '&fl_programa=' + fl_programa +
                  '&fg_accion='+fg_accion+
                  '&fl_leccion=' + fl_leccion,
            async: false,
            success: function (html) {
                $('#extraclass').html(html);
                
            }
        });

    }

    function EditExtraclass(fl_clase_calendar_extra, fl_clase_calendar) {

        var fg_accion = "update";
        var nb_clase = document.getElementById('nb_clase_' + fl_clase_calendar_extra).value;
        var fe_clase = document.getElementById('fe_clase_' + fl_clase_calendar_extra).value;
        var hr_clase = document.getElementById('hr_clase_' + fl_clase_calendar_extra).value;

        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_extraclass_event.php',
            data: 'fl_clase_calendar_extra=' + fl_clase_calendar_extra +
                  '&fl_clase_calendar='+fl_clase_calendar+
                  '&nb_clase=' + nb_clase +
                  '&fe_clase=' + fe_clase +
                  '&hr_clase='+hr_clase+
                  '&fg_accion='+fg_accion,
            async: false,
            success: function (html) {
                $('#extraclass').html(html);

            }
        });

    }
    function BorraExtraclass(fl_clase_calendar_extra, fl_clase_calendar) {

        var fg_accion = "delete";
        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_extraclass_event.php',
            data: 'fl_clase_calendar_extra=' + fl_clase_calendar_extra +
                  '&fl_clase_calendar=' + fl_clase_calendar +
                  '&fg_accion='+fg_accion,
            async: false,
            success: function (html) {
                $('#extraclass').html(html);

            }
        });

    }

    function MuestraExtraclass() {
        var fl_clase_calendar = "<?php echo $fl_clase_calendar;?>";
        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_extraclass_event.php',
            data: 'fl_clase_calendar=' + fl_clase_calendar,
            async: false,
            success: function (html) {
                $('#extraclass').html(html);

            }
        });

    }
    MuestraExtraclass();

</script>	
