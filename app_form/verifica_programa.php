<?php
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  // require("../lib/sp_forms.inc.php");
  require("lib/app_forms.inc.php");
  require("app_form.inc.php");

  #Variable initialization to avoid error
  $fg_payment = NULL;

  $fl_programa=RecibeParametroNumerico('fl_programa');
  $fl_pais_selected=RecibeParametroNumerico('fl_pais');

  $Query="SELECT cl_delivery FROM k_programa_costos WHERE fl_programa=$fl_programa ";
  $rot=RecuperaValor($Query);
  $cl_deliv=str_texto($rot['cl_delivery']);

  if($cl_deliv=='C'){

      if($fl_pais_selected==226){
          $opc = array(ObtenEtiqueta(2386)); // Online,Combined
          $val = array('O');
      }
      if($fl_pais_selected==38){
          $opc = array(ObtenEtiqueta(2386), ObtenEtiqueta(2387)); // Online,Combined
          $val = array('O', 'C');
      }

      if ($fl_pais_selected <> 38 && $fl_pais_selected <> 226) {
        $opc = array(ObtenEtiqueta(2386)); // Online,Combined
        $val = array('O');
      }




  }else{

	    $opc = array(ObtenEtiqueta(2386)); // Online,Combined
        $val = array('O');

  }





                echo Forma_CampoSelect_Boostrap(ObtenEtiqueta(2388), "fg_payment",$opc, $val, $fg_payment, True, "12", "fa-file", "", true);
 echo"
 <script>
 $('#fg_payment').select2(
 {
	width: '100%'
 });
 </script>

 ";










 #Realizamos la consulta para mostrar los tiempos

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
		         WHERE B.fl_programa=$fl_programa AND B.fl_periodo=$fl_periodo ) Z;
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




?>