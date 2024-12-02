<?php

# Libreria de funciones
require("../lib/sp_general.inc.php");
require("../lib/sp_session.inc.php");
// require("../lib/sp_forms.inc.php");
require("lib/app_forms.inc.php");
require("app_form.inc.php");


$fl_programa=RecibeParametroNumerico('fl_programa');
$fl_periodo=RecibeParametroNumerico('fl_periodo');
$cl_sesion=$_POST['cl_sesion'];

$Query  = "SELECT fl_class_time ";
$Query .= "FROM k_app_contrato ";
$Query .= "WHERE cl_sesion='$cl_sesion'";
$row = RecuperaValor($Query);
$fl_class_time=$row['fl_class_time'];



echo"
<div class='col col-sm-12 col-md-12 col-lg-12 col-xs-12' data-aos='500' data-aos-duration='800' data-aos-delay=''>
     ". Forma_SeccionBootstrap(ObtenEtiqueta(621))."
";


$Query_classtime="SELECT CONCAT( dia,' ',no_hora,' ',ds_tiempo)AS dia,fl_class_time FROM
            (
            SELECT CASE WHEN cl_dia='1'THEN '".ObtenEtiqueta(2390)."' 
               WHEN cl_dia='2'THEN '".ObtenEtiqueta(2391)."'
               WHEN cl_dia='3'THEN '".ObtenEtiqueta(2392)."' 
               WHEN cl_dia='4'THEN '".ObtenEtiqueta(2393)."' 
               WHEN cl_dia='5'THEN '".ObtenEtiqueta(2394)."' 
               WHEN cl_dia='6'THEN '".ObtenEtiqueta(2395)."' 
               ELSE '".ObtenEtiqueta(2396)."' 
               END dia , A.no_hora,ds_tiempo,A.fl_class_time_programa,B.fl_class_time  
		         FROM k_class_time_programa A
               JOIN k_class_time B ON B.fl_class_time=A.fl_class_time 
		         WHERE B.fl_programa=$fl_programa AND B.fl_periodo=$fl_periodo ) Z 
        ";
$rs_class_time = EjecutaQuery($Query_classtime);
$tot_reg_class_time = CuentaRegistros($rs_class_time);
$nb_dia="";
for($ic=1;$rowc=RecuperaRegistro($rs_class_time);$ic++) {

    $fl_class=$rowc[1];
    $nb_dia_=str_texto($rowc[0]);


    $nb_dia .=" ".$nb_dia_;

    if($ic<=($tot_reg_class_time-1))
        $nb_dia.=" &";
    else
        $nb_dia.= "";


}

$nb_dias=$nb_dia;


if(!empty($fl_periodo)){

    echo"<style>

            .select2-selection__rendered {
              color: #000 !important;
            }
         </style>";
}


 $opc = array($nb_dias); // Horarios
 $val = array($fl_class);
 echo"<div id='fl_class_time_1' class='".$st."'>";
echo Forma_CampoSelect_Boostrap(ObtenEtiqueta(2389), "fl_class_time",$opc, $val, $fl_class_time, True, "12", "fa-clock-o", "", true);
echo"</div>";

echo"				  
</div>
";
                 
echo"
 <script>
 $('#fl_class_time').select2(
 {
	width: '100%' 	
 });
 </script>
 
 ";

?>