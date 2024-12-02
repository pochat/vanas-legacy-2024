<?php
	
require '../../lib/general.inc.php';

#Recibe Parametros.
$fl_clase_calendar=$_POST['fl_clase_calendar'];
$fl_term=$_POST['fl_term'];
$fl_periodo=$_POST['fl_periodo'];
$no_semana=$_POST['no_semana'];
$fl_programa=$_POST['fl_programa'];
$fl_leccion=$_POST['fl_leccion'];
$no_grado=$_POST['no_grado'];
$fe_inicio=$_POST['fe_inicio'];
$fe_final=$_POST['fe_final'];
$fg_accion=$_POST['fg_accion'];
$fl_clase_calendar_extra=$_POST['fl_clase_calendar_extra'];





if($fg_accion=='insert'){
    $nb_clase="ExtraClass";

    $fe_inicio = date("Y-m-d H:i:s"); 
    $fe_final = strtotime ('+1 hour', strtotime ($fe_inicio) ) ; 
    $fe_final = date("Y-m-d H:i:s",$fe_final); 
    
    $Query ="INSERT INTO k_clase_calendar_extra(fl_clase_calendar,nb_clase,fl_term,fl_programa,fl_leccion,fl_periodo,fe_inicio,fe_final,no_grado,no_semana,fe_creacion,fe_ultmod) ";
    $Query.="VALUES($fl_clase_calendar,'$nb_clase',$fl_term,$fl_programa,$fl_leccion,$fl_periodo,'$fe_inicio','$fe_final',$no_grado,$no_semana,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
    $fl_data=RecuperaValor($Query);


}
if($fg_accion=='delete'){
    $Query="DELETE FROM k_clase_calendar_extra WHERE fl_clase_calendar_extra=$fl_clase_calendar_extra ";   
}

if($fg_accion=='update'){
    $nb_clase=$_POST['nb_clase'];
    $fe_inicio=$_POST['fe_clase']." ".$_POST['hr_clase'].":00";
    $fe_inicio = strtotime ('+0 hour', strtotime ($fe_inicio) ) ;  
    $fe_inicio = date("Y-m-d H:i:s",$fe_inicio); 

    $fe_final = strtotime ('+1 hour', strtotime ($fe_inicio) ) ; 
    $fe_final = date("Y-m-d H:i:s",$fe_final); 


    $Query="UPDATE k_clase_calendar_extra SET nb_clase='$nb_clase',fe_inicio='$fe_inicio',fe_final='$fe_final',fe_ultmod=CURRENT_TIMESTAMP WHERE fl_clase_calendar_extra=$fl_clase_calendar_extra ";
    EjecutaQuery($Query);

}

?>

<?php if(($fg_accion=='delete')||($fg_accion=='insert')||($fg_accion=='update')){

?>
<script>
    $(document).ready(function () {

        $.smallBox({
            title: "Successfully! ",
            content: "<i class='fa fa-clock-o'></i> <i>1 seconds ago...</i>",
            color: "#5F895F",
            iconSmall: "fa fa-check bounce animated",
            timeout: 4000
        });

        var calendar = $('#calendar').fullCalendar({

          //  defaultDate: '2020-05-05'
      });
});
</script>
<?php } 
      
?>
<br />
<?php 
echo"<table widht='100%'>";
#Recuperamos total de extraclass del evento.
$Query="SELECT * FROM k_clase_calendar_extra WHERE fl_clase_calendar=$fl_clase_calendar ";
$rs3=EjecutaQuery($Query);
for($i=1;$row3=RecuperaRegistro($rs3);$i++){
    $fl_clase_calendar_extra=$row3['fl_clase_calendar_extra'];
    $fe_clase=$row3['fe_inicio'];
    $nb_clase=$row3['nb_clase'];

    $fecha = strtotime ('+0 hour', strtotime ($fe_clase) ) ;  
    $fecha = date("d-m-Y",$fecha); 


    $hora=date("H:i",$fe_clase);

    echo"<tr>";
    echo"<td>";
    CampoTexto('nb_clase_'.$fl_clase_calendar_extra, $nb_clase, 10, 15, 'form-control', False, "");
    echo"</td>";
    echo"<td>";

        echo "  <div class='row form-group smart-form'>
                     <div class='col col-sm-12'>
                        <label class='input col col-sm-12 no-padding $ds_error'>";
        CampoTexto('fe_clase_'.$fl_clase_calendar_extra, $fecha, 100, 30, $fe_clase, False);
        Forma_Calendario('fe_clase_'.$fl_clase_calendar_extra);
        echo "          </label>
                     </div>
                </div> ";

    echo"</td>";
    echo"<td>";
                         CampoTexto('hr_clase_'.$i, $hora, 10, 8,'form-control', False);

    echo"</td>";

    echo"<td>&nbsp;&nbsp;
            <a href='javascript:EditExtraclass($fl_clase_calendar_extra,$fl_clase_calendar);'><i class='fa fa-floppy-o' aria-hidden='true'></i></a>&nbsp;&nbsp;
            <a href='javascript:BorraExtraclass($fl_clase_calendar_extra,$fl_clase_calendar);'><i class='fa fa-trash-o' aria-hidden='true'></i> </a></td>";
    
    echo"</tr>";


}
echo"</table>";
?>
<script>
    $(document).ready(function () {
        pageSetUp();
    });
</script>


