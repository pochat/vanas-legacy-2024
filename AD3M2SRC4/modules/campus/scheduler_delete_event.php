<?php
	
require '../../lib/general.inc.php';

#Recibe Parametros.

$fl_clase_calendar=$_POST['fl_clase_calendar'];
$fg_delete=$_POST['fg_repeat'];
$fl_periodo=ObtenConfiguracion(143);

if($fg_delete==1){

    EjecutaQuery("DELETE FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ");
    EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_clase_calendar=$fl_clase_calendar ");
    EjecutaQuery("DELETE FROM k_clase_calendar WHERE fl_clase_calendar=$fl_clase_calendar ");
}
if($fg_delete==2){

    #Recupera todos los eventos y los elimina.
    $Queryi="SELECT nb_grupo FROM k_clase_calendar WHERE fl_clase_calendar=$fl_clase_calendar ";
    $rowi=RecuperaValor($Queryi);
    $nb_grupo=$rowi['nb_grupo'];

    $qUERY="SELECT * FROM k_clase_calendar WHERE nb_grupo='$nb_grupo' AND fl_periodo=$fl_periodo   ";
    $rs=EjecutaQuery($qUERY);
    for($a=1;$row=RecuperaRegistro($rs);$a++) {
        $fl_clase_calendar=$row['fl_clase_calendar'];

        EjecutaQuery("DELETE FROM k_clase_calendar_terms WHERE fl_clase_calendar=$fl_clase_calendar ");
        EjecutaQuery("DELETE FROM k_clase_calendar_alumno WHERE fl_clase_calendar=$fl_clase_calendar ");
        EjecutaQuery("DELETE FROM k_clase_calendar WHERE fl_clase_calendar=$fl_clase_calendar ");

    }


}

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
            defaultDate: '2020-05-05'
        });
});
</script>

