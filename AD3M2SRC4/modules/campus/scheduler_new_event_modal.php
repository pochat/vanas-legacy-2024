<?php
	
require '../../lib/general.inc.php';

#Recibe Parametros.
$date=$_POST['fe_inicio'];
$fl_clase_calendar=$_POST['id_evento'];
$valores=explode('T',$date); 
$fe_inicio=$valores[0];
$no_hora=$valores[1];
#Formateamos fecha y hora.
$fe_inicio = strtotime ('+0 hour', strtotime ($fe_inicio) ) ;  
$fe_inicio = date("d-m-Y",$fe_inicio); 
$fl_periodo_principal=ObtenConfiguracion(143);

#formateamos hora.
$valores=explode(':',$no_hora);
$hora=$valores[0];
$minutos=$valores[1];
$segundos=$valores[2];

$fl_programa_users=$_POST['fl_programa_users'];
#descomponemos para ontener fl_programa,fl_periodo.
$vals=explode('#',$fl_programa_users);
$fl_programa=$vals[0];
$allusers=$vals[2];
$fl_term=$_POST['fl_term'];


if((!empty($fl_clase_calendar))&&($fl_clase_calendar<>'undefined')){
    $valores=explode(',',$fl_clase_calendar); 
    $fl_clase_calendar=$valores[0];
    $fl_term=$valores[1];

    #Recuperamos datos del evento programado.
    $Query="SELECT * FROM k_clase_calendar WHERE fl_clase_calendar=$fl_clase_calendar ";
    $row=RecuperaValor($Query);
    $fl_periodo=$row['fl_periodo'];
    $fl_maestro=$row['fl_maestro'];
    $nb_grupo=$row['nb_grupo'];
    $nb_clase=$row['ds_titulo'];
    $no_semana=$row['no_semana'];
    $fg_repeat=$row['fg_repeat'];
    $fe_inicio=$row['fe_inicio'];
    $fg_tipo_clase=$row['fg_tipo_clase'];

    $valores=explode(' ',$fe_inicio); 
    $no_hora=$valores[1];
    #formateamos hora.
    $valores=explode(':',$no_hora);
    $hora=$valores[0];
    $minutos=$valores[1];
    $segundos=$valores[2];

    #Damos formato ala fecvha para presentarla.
    $fe_inicio=strtotime('0 days',strtotime($fe_inicio));
    $fe_inicio= date('d-m-Y',$fe_inicio);	

    #Reciuperems los terms elegidos.
    $Querysv="SELECT fl_term FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ";
    $rsm=EjecutaQuery($Querysv);
    $terms_bd=array();
    for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
        
        array_push($terms_bd, $rowm[0]);   

    }

}else{
  
    $fg_repeat=3;

    $terms_bd= array();
    array_push($terms_bd, $fl_term);   
    
    $Query="SELECT fl_programa FROM k_term WHERE fl_term=$fl_term ";
    $row=RecuperaValor($Query);
    $fl_program=$row[0];


    #Recuperaos el titulo de la leccion 1 por default.
    #Recuperamos la semana que corresponde y el nombre; estos datos se determinan del No de Leccion.
    $QueryS ="SELECT DISTINCT b.no_semana, b.fl_leccion,b.ds_titulo 
            FROM k_semana a
            JOIN c_leccion b ON a.fl_leccion=b.fl_leccion
            JOIN k_term t ON t.fl_term=a.fl_term WHERE t.fl_term=$fl_term AND b.fl_programa=$fl_program AND b.no_grado=1 AND b.no_semana=1 ";
    $QueryS.=" ORDER BY b.no_semana DESC ";
    $rows=RecuperaValor($QueryS);
    $fl_leccion=$rows['fl_leccion'];
    $no_semana=$rows['no_semana'];
    $nb_clase=$rows['ds_titulo'];



}




if($hora>12){
    $ds_tiempo="PM";

    switch($hora){
        case '13':
            $no_hora="01";
            break;
        case '14':
            $no_hora="02";
            break;
        case '15':
            $no_hora="03";
            break;
        case '16':
            $no_hora="04";
            break;
        case '17':
            $no_hora="05";
            break;
        case '18':
            $no_hora="06";
            break;
        case '19':
            $no_hora="07";
            break;
        case '20':
            $no_hora="08";
            break;
        case '21':
            $no_hora="09";
            break;
        case '22':
            $no_hora="10";
            break;
        case '23':
            $no_hora="11";
            break;
        

    }
   


}else{
    $ds_tiempo="AM";
}

$no_hora=$no_hora.":".$minutos.":".$segundos;

$fg_tipo_clase=3;




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
<div class="modal fade" id="modal_new_event_calendar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:60%;margin:auto;margin-top:70px;">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-book" aria-hidden="true"></i> Group Review Class </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">     

           <div class="row">
               <div class="col-md-1"></div>
               <div class="col-md-10">
                  <input type="hidden" id="fl_clase_calendar" name="fl_clase_calendar" value="<?php echo $fl_clase_calendar;?>"/>
                   <?php 
                    #Identificamos que tipo de clase va ser.
                    $opc = array('Multiple Class');
                    $val = array('3');
                    Forma_CampoSelect('Class Type', True, 'fg_tipo_clase', $opc, $val, $fg_tipo_clase, !empty($fg_tipo_clase_err)?$fg_tipo_clase_err:NULL, False, '', 'left', 'col col-sm-4', 'col col-sm-6');                                    
                    echo"<br>";

                    echo "<div class='row form-group smart-form'>
                                    <label class='input col col-sm-4'><b>*Start Event</b></label>
                                    <div class='col col-sm-6'><label class='input col col-sm-8 no-padding $ds_error'>";
                    CampoTexto('fe_inicio', $fe_inicio, 10, 10, $fe_inicio, False);
                    Forma_Calendario('fe_inicio');
                    echo "              </label>
                                    </div>
                          ";
                    echo" </div>";
                    echo"<br>";
                    echo"<div class=\"form-group\" style=\"margin-left:0px;style=\"width: 100%\"\">
                             <label class='input col col-sm-4'><b>*Hour Event</b></label>
                               <div class='col col-sm-6'>
                               <div class=\"input-group\" style=\"width: 68%\"> 
                                 <input class=\"form-control picker mike_select\" id=\"timepicker\" type=\"text\" value=\"".$no_hora." ".$ds_tiempo."\" placeholder=\"Select time\" >
                                    <span class=\"input-group-addon\">
                                        <i class=\"fa fa-clock-o\"></i>
                                    </span>
                               </div>
                               </div>
                        </div>";
                    echo"<p>&nbsp;</p>";

                    echo"
                            <div class='col col-sm-4 tex-left'>
			                     <label class='control-label text-align-left' style='float:left;'><strong>*".ObtenEtiqueta(422)."</strong></label>
			                 </div>                            
                                <label class='select col-md-6'>";
                    //if(!empty($fl_clase_calendar)){
                        $concat = array('nb_programa', "' ('", 'ds_duracion', "')'");
                        $Queryp  = "SELECT ".ConcatenaBD($concat)." 'nb_programa',  nb_periodo, no_grado,fl_term ";
                        $Queryp .= "FROM k_term a, c_programa b, c_periodo c ";
                        $Queryp .= "WHERE a.fl_programa=b.fl_programa ";
                        $Queryp .= "AND a.fl_periodo=c.fl_periodo ";
                        $Queryp .= "AND fg_activo='1'";
                        $Queryp .= "AND b.fg_archive='0' ";
                        $Queryp .= "AND c.fl_periodo=$fl_periodo_principal  ";
                        $Queryp .= "ORDER BY nb_programa, no_grado ";
                    /*}else{
                        $concat = array('pr.nb_programa', "' ('", 'pr.ds_duracion', "')'");
                        $Queryp = "
                                            SELECT DISTINCT ".ConcatenaBD($concat)." 'nb_programa',  p.nb_periodo, a.no_grado,b.fl_term   
                                            FROM k_clase_fetch_programs a 
                                            JOIN k_term b ON b.fl_term=a.fl_term
                                            JOIN c_periodo p ON p.fl_periodo=a.fl_periodo
                                            JOIN c_programa pr ON pr.fl_programa=a.fl_programa
                                            WHERE fg_tipo_clase='multiple_term' ";
                    }*/      
                                CampoSelectBDF('fl_terms', $Queryp, !empty($p_actuals)?$p_actuals:NULL, '', False, 'multiple', $terms_bd, 'fl_terms');
                                echo"<input type=\"hidden\" name=\"fl_grupos_i\" id=\"fl_grupos_i\" value=\"$fl_grupos_i\">";
                    echo"   </label>
                            <br><br>
                        ";

                    $Query="SELECT CONCAT(ds_nombres,' ',ds_apaterno)ds_nombres,fl_usuario FROM c_usuario a WHERE fl_perfil=2 AND fg_activo='1' ORDER BY ds_nombres ASC; ";
                    Forma_CampoSelectBD('Teacher', False, 'fl_maestro', $Query,$fl_maestro,$fl_maestro_err,true,'','','col col-sm-4','col-sm-6');
                    echo"<br/><br/><br/>";


                    Forma_CampoTexto(ObtenEtiqueta(420), True, 'nb_grupo', $nb_grupo, 50, 36, $nb_grupo_err,'','','','','','','left','col col-sm-4','col col-sm-6');
                    echo"<br>";
                    Forma_CampoTexto(ObtenEtiqueta(390), True, 'no_semana', $no_semana, 50, 36, $no_semana_err,'','','','','','','left','col col-sm-4','col col-sm-6');                
                    echo"<br>";
                    Forma_CampoTexto(ObtenEtiqueta(1234), True, 'nb_clase', $nb_clase, 50, 36, $nb_clase_err,'','','','','','','left','col col-sm-4','col col-sm-6');                  
  
                    //echo"<br>";
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
                    if($fg_repeat==3){
                        $class_repeat="";
                    }else{
                        $class_repeat="hidden";
                    }
                    echo"<div id='weekly_repeats' class=''>
                              <div class='form-group'>
                                    <div class='col col-sm-4'>
                                        <b>How many repeats:</b>
                                    </div>
                                    <div class='col col-sm-6'> 
                                        <input class='form-control spinner-left'  id='no_semanas' name='no_semanas' value='12' type='text'>
                                    </div>
                               </div>
                        </div>";
                    
                    if($fl_clase_calendar){
                        echo"<br><br><br>";
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


      </div>
      <div class="modal-footer text-center">
        <?php if($fl_clase_calendar){?>
        <a href="javascript:void(0);" style="float:left;" onclick="DeleteEvent(<?php echo $fl_clase_calendar;?>);">Delete</a>
        <?php } ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="SaveEvent();">Save changes</button>
      </div>
    </div>
  </div>
</div>
<div id="shceduler_save_event_ultiple_terms"></div>
<div id="delete_event_ultiple_terms"></div>
<!-------------------------------------------------------------------------------------------->				
<script>
    function selectedValues() {
        var x = document.getElementById("fl_terms");
        var selectedValues = '';
        for (var i = 0; i < x.options.length; i++) {
            if (x.options[i].selected == true) {
                //selectedValues += x.options[i].value + ", ";
                //remove item fetch scheduler.
                $("#group_" + x.options[i].value + "_tipo_multiple_term").addClass("hidden");
               
                //llamdo ajax para eliminar las cajas.



            }
        }

    }
</script>


<script>
    $(document).ready(function () {      
        pageSetUp();
        //se agrega para que fucnione l timepiker
        $('.picker').timepicker();
        $("#no_semanas").spinner();
        
        $('#fl_terms').change(function () {
            selectedValues();
        });

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
        
        //if (fg_tipo_clase > 1) {
        //    document.getElementById("view_form").style.display = "block";
        //} else {
        //    document.getElementById("view_form").style.display = "none";
       // }
      //  if (fg_tipo_clase == 3) {
      //      document.getElementById("view_terms").style.display = "block";
      //  }
      //  else {
      //      document.getElementById("view_terms").style.display = "none";
      //  }


    }


    $('#modal_new_event_calendar').modal('show');

    function SaveEvent() {

        var fg_tipo_clase = 3;
        var fl_clase_calendar = document.getElementById('fl_clase_calendar').value;
        var fl_maestro = document.getElementById('fl_maestro').value;
        var nb_grupo = document.getElementById('nb_grupo').value;
        var no_semana = document.getElementById('no_semana').value;
        var nb_clase = document.getElementById('nb_clase').value;
        var fe_inicio = document.getElementById('fe_inicio').value;
        var fg_repeat = document.getElementById('fg_repeat').value;
        var fe_fin = document.getElementById('fe_finish').value;
        var no_semanas = document.getElementById('no_semanas').value;
        var fg_edit = document.getElementById('fg_edit').value;
        var fl_terms = $('#fl_terms').val();

        var hr_inicio = document.getElementById('timepicker').value;
        
        if (fl_maestro == 0) {
            $('#div_fl_maestro').addClass('has-error');
            return;
        }

        if (nb_grupo.length== 0) {
            
            $('#div_nb_grupo').addClass('has-error');
            return;
        }
        if (nb_clase.length== 0) {
            
            $('#div_nb_clase').addClass('has-error');
            return;
        }
        if (no_semana.length== 0) {
           
            $('#div_no_semana').addClass('has-error');
            return;
        }



        if (fg_repeat == 0) {
            $('#div_fg_repeat').addClass('has-error');
            return;
        } else {
            $('#div_fg_repeat').removeClass('has-error');

            if (fg_repeat == 2) {


            }

        }

        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_save_event_groups_global.php',
            data: 'fl_clase_calendar='+fl_clase_calendar+
                  '&fg_tipo_clase=' + fg_tipo_clase +
                  '&fg_edit='+fg_edit+
                  '&fl_maestro=' + fl_maestro +
                  '&nb_grupo=' + nb_grupo +
                  '&no_semana=' + no_semana +
                  '&no_semanas=' + no_semanas +
                  '&nb_clase=' + nb_clase+
                  '&fe_inicio=' + fe_inicio +
                  '&fg_repeat=' + fg_repeat +
                  '&hr_inicio='+hr_inicio+
                  '&fe_fin=' + fe_fin +            
                  '&fl_terms=' + fl_terms,
                  
            async: false,
            success: function (html) {
                $('#shceduler_save_event_ultiple_terms').html(html);
                MuestraCalendar(fe_inicio);
            }
        });




    }

    MuestraInfoForm();

    function DeleteEvent(fl_clase_calendar) {


        var fg_repeat = document.getElementById('fg_edit').value;

        //pasamos los valores por ajax para salvar datos.
        $.ajax({
            type: 'POST',
            url: 'scheduler_delete_event.php',
            data: 'fl_clase_calendar=' + fl_clase_calendar+
                  '&fg_repeat=' + fg_repeat,
            async: false,
            success: function (html) {
                $('#delete_event').html(html);
                MuestraCalendar();
            }
        });
        $('#modal_new_event_calendar').modal('hide');
        $('.modal-backdrop').remove();

    }

    <?php if((!empty($fl_clase_calendar))&&($fl_clase_calendar<>'undefined')){
    
              echo" $('#nb_grupo').prop('disabled', true ); ";

          } ?>
</script>



