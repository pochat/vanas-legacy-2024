<?php
# Libreria de funciones
require '../../lib/general.inc.php';

# Verifica que exista una sesion valida en el cookie y la resetea
ValidaSesion( );

# Recibe parametro
$fl_maestro = $_POST['fl_maestro'];
$fe_periodo = $_POST['fe_periodo'];

#Obtenemos mes anio
$date=explode("-",$fe_periodo);
$mes=$date[0];
$anio=$date[1];

echo"<br>";

$Query  = "SELECT mn_hour_rate,mn_hour_rate_group_global,mn_hour_rate_global_class FROM c_maestro ";
$Query .= "where  fl_maestro=$fl_maestro ";
$row = RecuperaValor($Query);
$mn_hour_rate=$row['mn_hour_rate'];
$mn_hour_rate_group_global=$row['mn_hour_rate_group_global'];
$mn_hour_rate_global_class=$row['mn_hour_rate_global_class'];
$fg_hour_rate=false;
$fg_hour_rate_group=false;





$titulos = array('Date',	'Week',	'Lesson',	'Concept'	,'Group Name',	'Course Name',	'Term Date','Amount');
$ancho_col = array('10%','10%', '15%', '20%', '15%', '15%','');
//Forma_Tabla_Ini('85%',$titulos, $ancho_col);

echo'
<style>
.white{
    color:#fff !important;
}
</style>
<div class="table-responsive">  
  <table border="0" width="100%" cellpadding="3" cellspacing="0" class="table table-striped table-hover dataTable no-footer has-columns-hidden">
    <thead>
    <tr class="txt-color-white">
      <th class="text-align-left" colspan=5" width="10%" style="background-color:#0092dc;"><h3>Lecture Classes</h3></th>
      
      
      <th class="text-align-right" colspan="3" width="" style="background-color:#0092dc;">
      ';
        Forma_CampoTexto('Default hourly rate',False, 'mn_hour_rate', $mn_hour_rate,50,30,'','','','','','','','','col-sm-6','col-sm-6');
   
        Forma_CampoCheckbox('Apply to all', 'fg_hour_rate', $fg_hour_rate,'','',true,'','','col-sm-6 white','col-sm-6 text-left');
        echo'</th>    
    </tr>
    

    <tr class="txt-color-white">
      <th class="text-align-left" width="10%" style="background-color:#fff;color:#000;">Date</th>
      <th class="text-align-left" width="10%" style="background-color:#fff;color:#000;">Week</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Lesson</th>
      <th class="text-align-left" width="20%" style="background-color:#fff;color:#000;">Concept</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Group Name</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Course Name</th>
      <th class="text-align-left" width="" style="background-color:#fff;color:#000;">Term Date</th>
      <th class="text-align-left" width="" style="background-color:#fff;color:#000;">Amount</th>    
    </tr>
    </thead>
    <tbody> 

';



$Query =" SELECT no_semana, ds_titulo,DATE_FORMAT(d.fe_clase, '%d-%m-%Y') fe_clase, 
	CASE d.fg_adicional 
		WHEN '0' THEN 'Live Session' 
		ELSE 'Extra Live Session' 
		END ds_descripion, 
		a.nb_grupo, e.nb_programa,(SELECT nb_periodo FROM c_periodo j WHERE j.fl_periodo=f.fl_periodo) nb_periodo, 
		CASE d.fg_adicional 
			WHEN '0' THEN IFNULL(
										(SELECT t.mn_lecture_fee FROM k_maestro_tarifa t 
												WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo 
												AND t.fl_maestro=46),e.mn_lecture_fee
										) 
						ELSE IFNULL(
										(SELECT t.mn_extra_fee 
											FROM k_maestro_tarifa t 
											WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo 
											AND t.fl_maestro=46
										),e.mn_extra_fee
										)	 
										END hourly_rate ,a.fl_grupo,e.fl_programa, 
										CASE a.no_alumnos WHEN 0 
    									THEN (SELECT COUNT(1) FROM k_alumno_historia f, c_usuario e 
										 			WHERE f.fl_grupo=a.fl_grupo AND f.fl_alumno=e.fl_usuario AND e.fg_activo='1')
    									ELSE a.no_alumnos END no_alumnos, d.fl_clase ,d.mn_rate  
		FROM c_grupo a, k_clase d, c_programa e, k_term f ,k_semana b 
		LEFT JOIN c_leccion c ON(c.fl_leccion=b.fl_leccion) 
		WHERE a.fl_term = b.fl_term 
		AND a.fl_grupo=d.fl_grupo 
		AND b.fl_semana=d.fl_semana 
		AND c.fl_programa = e.fl_programa 
		AND c.fl_programa=e.fl_programa 
		AND a.fl_term = f.fl_term 
		AND b.fl_term = f.fl_term 
		AND DATE_FORMAT(d.fe_clase,'%m-%Y')='".$mes."-".$anio."' 
		AND a.fl_maestro=$fl_maestro  
		ORDER BY d.fe_clase  ";
$rs = EjecutaQuery($Query);
$total = CuentaRegistros($rs);
for($i=0;$row= RecuperaRegistro($rs);$i++){

    $no_semana=$row[0];
    $ds_titulo=$row[1];
    $fe_clase=$row['fe_clase'];
    $nb_grupo=$row['nb_grupo'];
    $nb_programa=$row['nb_programa'];
    $nb_periodo=$row['nb_periodo'];
    $fl_grupo=$row['fl_grupo'];
    $fl_programa=$row['fl_programa'];
    $fl_clase=$row['fl_clase'];
    $ds_descripion=$row['ds_descripion'];
    $mn_rate=$row['mn_rate'];

    if(empty($mn_rate)){
       $mn_rate= $mn_hour_rate;
    }

    echo "
      <tr>
        <td>".$fe_clase."</td>
        <td>".$no_semana."</td>
        <td>".$ds_titulo."</td>
        <td>".$ds_descripion."</td>
        <td>".$nb_grupo."</td>
        <td>".$nb_programa."</td>
        <td>".$nb_periodo."</td>
<td>";
    CampoTexto('mn_rate_'.$i, $mn_rate, 10, 10, 'form-control', False, "onchange=\"UpdateRate($i,$fl_clase);\"");
    Forma_CampoOculto('fl_clase_'.$i,$fl_clase);
    Forma_CampoOculto('fl_grupo_'.$i,$fl_grupo);
    Forma_CampoOculto('fl_programa_'.$i,$fl_programa);
    echo"
      </tr>";
}
echo'</tbody></table></div>';





echo"<script>

function UpdateRate(valor,fl_clase){

    var valor= document.getElementById('mn_rate_'+valor).value;

     $.ajax({
        type: 'POST',
        url: 'updatetarifas.php',
        data: 'fl_clase=' + fl_clase +
              '&valor='+valor+
              '&tabla=k_clase',
        async: false,
        success: function (html) {
            
                 
        }
     });

}

</script>";




echo"<br><hr>";




#$titulos = array('Date',	'Week',	'Lesson',	'Concept'	,'Group Name',	'Course Name',	'Term Date','Amount');
#$ancho_col = array('10%','10%', '15%', '20%', '15%', '15%','');
#Forma_Tabla_Ini('85%',$titulos, $ancho_col);

echo'
<div class="table-responsive">  
  <table border="0" width="100%" cellpadding="3" cellspacing="0" class="table table-striped table-hover dataTable no-footer has-columns-hidden">
    <thead>
    <tr class="txt-color-white">
      <th class="text-align-left" colspan=5" width="10%" style="background-color:#0092dc;"><h3>Review Classes</h3></th>
      
      
      <th class="text-align-right" colspan="3" width="" style="background-color:#0092dc;">
      ';
       Forma_CampoTexto('Default hourly rate',False, 'mn_hour_rate_group_global', $mn_hour_rate_group_global,50,30,'','','','','','','','','col-sm-6','col-sm-6');
       Forma_CampoCheckbox('Apply to all', 'fg_hour_rate_group', $fg_hour_rate_group,'','',true,'','','col-sm-6 white','col-sm-6 text-left');

        echo'</th>    
    </tr>
    

    <tr class="txt-color-white">
      <th class="text-align-left" width="10%" style="background-color:#fff;color:#000;">Date</th>
      <th class="text-align-left" width="10%" style="background-color:#fff;color:#000;">Week</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Lesson</th>
      <th class="text-align-left" width="20%" style="background-color:#fff;color:#000;">Concept</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Group Name</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Course Name</th>
      <th class="text-align-left" width="" style="background-color:#fff;color:#000;">Term Date</th>
      <th class="text-align-left" width="" style="background-color:#fff;color:#000;">Amount</th>    
    </tr>
    </thead>
    <tbody> 

';




$Querygg  = "SELECT  b.no_semana,a.nb_clase ds_titulo,".ConsultaFechaBD('a.fe_clase', FMT_FECHA)." fe_clase, 'Group_Class', (SELECT min( mn_cgrupo) FROM k_maestro_tarifa_gg WHERE fl_clase_grupo=a.fl_clase_grupo) mn_clase,a.fl_clase_grupo,(SELECT COUNT(*) FROM k_alumno_grupo d WHERE d.fl_grupo=c.fl_grupo )no_alumnos,c.fl_grupo,c.nb_grupo,a.mn_rate FROM k_clase_grupo a JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo JOIN c_grupo c ON c.fl_grupo=a.fl_grupo WHERE a.fl_maestro=$fl_maestro AND DATE_FORMAT(a.fe_clase,'%m-%Y')='".$mes."-".$anio."' ";

$rgg = EjecutaQuery($Querygg);

$tot_aut_gg = CuentaRegistros($rgg);

$total_gg=0;

for($k=$j+1;$row=RecuperaRegistro($rgg);$k++){
    $no_orden = $row['no_semana'];
    $ds_titulo = $row['ds_titulo'];
    $fe_clase = $row['fe_clase'];
    $ds_descripion = $row['Group_Class'];
    $mn_cglobal_fee_gg = !empty($row['mn_clase'])?$row['mn_clase']:ObtenConfiguracion(96);    
    $amount_gg = $mn_cglobal_fee_gg*1;;    
    $fl_clase_grupal = $row['fl_clase_grupo'];
    $no_alumnos = $row['no_alumnos'];
    $fl_grupo_gg = !empty($row['fl_grupo'])?$row['fl_grupo']:$row['fl_clase_grupo'];
    $fl_clase_gg = $row['nb_grupo'];
    $ds_clase_grupal =$row['nb_grupo'];
    $disabled = "";
    $mn_rate=$row['mn_rate'];


    #Recupermaos todos los periodos que incluye el programa.
    $concat = array('nb_programa', "' ('", 'ds_duracion', "')'", "' - '", 'nb_periodo', "' - ".ObtenEtiqueta(375)." '", 'no_grado');
    $Querysv="SELECT a.fl_term,a.fl_grupo,b.fl_programa,b.fl_periodo,".ConcatenaBD($concat)." 'nb_term',nb_programa,no_grado     
                FROM k_grupo_term a
                JOIN k_term b ON a.fl_term=b.fl_term 
                JOIN c_programa c ON c.fl_programa=b.fl_programa
                JOIN c_periodo d ON d.fl_periodo=b.fl_periodo
                WHERE a.fl_grupo=$fl_grupo_gg  ";

    $rsm=EjecutaQuery($Querysv);
    
    $total_terms=CuentaRegistros($rsm);
    
    $periodos= NULL;
    $lessons= NULL;
    $no_grados= NULL;
    
    for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
        //$fl_terms_i = $rowm[0];        
        $periodos.=substr($rowm['nb_term'], -6, 6)."<br>";
        if ($lessons != $rowm['nb_programa']."<br>") {
            $lessons.=$rowm['nb_programa']."<br>";
        }
        $no_grados.=$rowm['no_grado']."<br>";
    }





    if(empty($mn_rate)){
        $mn_rate= $mn_hour_rate_group_global;
    }

    echo "
      <tr>
        <td>".$fe_clase."</td>
        <td>".$no_orden."</td>
        <td>".$lessons."</td>
        <td>".$ds_descripion."</td>
        <td>".$ds_clase_grupal."</td>
        <td></td>
        <td></td>
        <td>";
    CampoTexto('mn_grupal_'.$k, $mn_rate, 10, 10, 'form-control', False, "onchange=\"UpdateRate3($k,$fl_clase_grupal);\"");
    Forma_CampoOculto('fl_clase_grupo_'.$k,$fl_clase_grupal);

}
echo'</tbody></table></div>';


echo"<script>

function UpdateRate3(valor,fl_clase){

    var valor= document.getElementById('mn_grupal_'+valor).value;

     $.ajax({
        type: 'POST',
        url: 'updatetarifas.php',
        data: 'fl_clase=' + fl_clase +
              '&valor='+valor+
              '&tabla=k_clase_grupo',
        async: false,
        success: function (html) {
            
                 
        }
     });

}

</script>";










echo"<br><hr>";
#Inicia las globales
#Forma_CampoTexto('Default hourly rate',False, 'mn_hour_rate_global_class', $mn_hour_rate_global_class,50,30);
#Forma_CampoCheckbox('Update all amounts and rates below', 'fg_hour_rate_global', $fg_hour_rate_global);

#$titulos = array('Date',	'Week',	'Lesson',	'Concept'	,'Group Name',	'Course Name',	'Term Date','Amount');
#$ancho_col = array('10%','10%', '15%', '20%', '15%', '15%','');
#Forma_Tabla_Ini('85%',$titulos, $ancho_col);


echo'
<div class="table-responsive">  
  <table border="0" width="100%" cellpadding="3" cellspacing="0" class="table table-striped table-hover dataTable no-footer has-columns-hidden">
    <thead>
    <tr class="txt-color-white">
      <th class="text-align-left" colspan=5" width="10%" style="background-color:#0092dc;"><h3>Global Classes</h3></th>
      
      
      <th class="text-align-right" colspan="3" width="" style="background-color:#0092dc;">
      ';
Forma_CampoTexto('Default hourly rate',False, 'mn_hour_rate_global_class', $mn_hour_rate_global_class,50,30,'','','','','','','','','col-sm-6','col-sm-6');
Forma_CampoCheckbox('Apply to all', 'fg_hour_rate_global', $fg_hour_rate_global,'','',true,'','','col-sm-6 white','col-sm-6 text-left');

echo'</th>    
    </tr>
    

    <tr class="txt-color-white">
      <th class="text-align-left" width="10%" style="background-color:#fff;color:#000;">Date</th>
      <th class="text-align-left" width="10%" style="background-color:#fff;color:#000;">Week</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Lesson</th>
      <th class="text-align-left" width="20%" style="background-color:#fff;color:#000;">Concept</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Group Name</th>
      <th class="text-align-left" width="15%" style="background-color:#fff;color:#000;">Course Name</th>
      <th class="text-align-left" width="" style="background-color:#fff;color:#000;">Term Date</th>
      <th class="text-align-left" width="" style="background-color:#fff;color:#000;">Amount</th>    
    </tr>
    </thead>
    <tbody> 

';



$Querycg  = "SELECT kcg.no_orden, kcg.ds_titulo, ".ConsultaFechaBD('kcg.fe_clase', FMT_FECHA).", 'Global Class' ds_descripion, cg.ds_clase ds_clase_global, ";
$Querycg .= "IFNULL((SELECT kmt.mn_cglobal_fee FROM k_maestro_tarifa_cg kmt WHERE kmt.fl_clase_global=cg.fl_clase_global AND kmt.fl_maestro=kcg.fl_maestro), ";
$Querycg .= "'".$mn_hour_rate_global_class."') mn_cglobal_fee, cg.fl_clase_global, cg.no_alumnos,  kcg.fl_clase_cg,kcg.mn_rate ";
$Querycg .= "FROM c_clase_global cg ";
$Querycg .= "LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global AND kcg.fl_maestro=$fl_maestro) ";
$Querycg .= "WHERE DATE_FORMAT(kcg.fe_clase,'%m-%Y')='".$mes."-".$anio."' ";
$rcg = EjecutaQuery($Querycg);
$tot_aut_cg = CuentaRegistros($rcg);
for($j=$i+1;$row=RecuperaRegistro($rcg);$j++){
    $no_orden = $row[0];
    $ds_titulo = $row[1];
    $fe_clase = $row[2];
    $ds_descripion = $row[3];
    $ds_clase_global = $row[4];
    $nb_programa_sp = "";
    $nb_periodo_sp = "";
    $mn_cglobal_fee = $row[5];    
    $amount_cg = $mn_cglobal_fee*1;    
    $fl_clase_global = $row[6];
    $no_alumnos = $row[7];
    $fl_clase_cg = $row[8];
    $mn_rate=$row['mn_rate'];

    if(empty($mn_rate)){
        $mn_rate= $mn_hour_rate_global_class;
    }


     echo "
      <tr>
        <td>".$fe_clase."</td>
        <td>".$no_orden."</td>
        <td>".$ds_titulo."</td>
        <td>".$ds_descripion."</td>
        <td>".$ds_clase_global."</td>
        <td></td>
        <td></td>
        <td>";
     CampoTexto('mn_cglobal_fee_'.$j, $mn_rate, 10, 10, 'form-control', False, "onchange=\"UpdateRate2($j,$fl_clase_cg);\"");
     Forma_CampoOculto('fl_clase_cg_'.$j,$fl_clase_cg);

            echo"
      </tr>";
}
#Forma_Tabla_Fin();
echo'</tbody></table></div>';



echo"<script>

function UpdateRate2(valor,fl_clase){

    var valor= document.getElementById('mn_cglobal_fee_'+valor).value;

     $.ajax({
        type: 'POST',
        url: 'updatetarifas.php',
        data: 'fl_clase=' + fl_clase +
              '&valor='+valor+
              '&tabla=k_clase_cg',
        async: false,
        success: function (html) {
            
                 
        }
     });

}

</script>";









?>