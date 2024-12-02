<?php
# Librerias
require '../../lib/general.inc.php';

# Recibe Parametros
$criterio = RecibeParametroHTML('criterio');
$clave= $_POST['extra_filters']['clave'];

#Muestra resultados de la busqueda.
$Query="SELECT fl_class_time, A.fl_periodo ,B.fl_programa,B.nb_programa,fe_inicio,fe_fin,no_term,ds_dia,no_hora,A.no_term,C.fl_term  ";
$Query.="FROM k_class_time A  
		   JOIN c_programa B ON B.fl_programa=A.fl_programa 
	       JOIN k_term C ON C.fl_programa=B.fl_programa AND C.fl_periodo=$clave		   
           WHERE A.fl_periodo=$clave AND B.fg_archive='0' and C.no_grado=1  ";  
$Query.="ORDER BY fl_class_time ";

$dias = array( 'Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$num = array('1', '2','3','4','5','6','7');	
$tot = count($dias); 

$rs = EjecutaQuery($Query);
$registros = CuentaRegistros($rs);

?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
        
        $fl_class_time=$row['fl_class_time'];		
        $fl_periodo=$row['fl_periodo'];
		$fl_programa=$row['fl_programa'];
        $nb_programa=$row['nb_programa'];
        $fe_inicio=$row['fe_inicio'];
        $fe_fin=$row['fe_fin'];
        $no_term=$row['no_term'];
        $ds_dia=$row['ds_dia'];
        $no_hora=$row['no_hora'];
		$fl_term=$row['fl_term'];

        #Recupreamos la fecha de finalizacion.
        $Query="SELECT no_semana,  DATE_FORMAT(fe_calificacion, '%d-%m-%Y') fe_calificacion 
				FROM c_leccion a 
				LEFT JOIN k_semana b ON (a.fl_leccion=b.fl_leccion AND fl_term=$fl_term) 
				WHERE fl_programa=$fl_programa AND no_grado=$no_term 
				ORDER BY no_semana desc limit 1 ";
        $rew=RecuperaValor($Query);
        $fe_termin=$rew['fe_calificacion'];
		
		if($fe_termin){		
				
				#DAMOS FORMATO DIA,MES, AÑO. para fecha de iniico
				$date = date_create($fe_inicio);
				$fe_inicio=date_format($date,'F j, Y');
				
				#DAMOS FORMATO DIA,MES, AÑO. para fecha de fin
				$date = date_create($fe_termin);
				$fe_termin=date_format($date,'F j, Y');
								
					echo '
				{
					"checkbox": " ",
					
					"start_date": "<a href=\'javascript:void(0);\'>'.$fe_inicio.' </a>",
					"end_date": "<td><a href=\'javascript:void(0);\'>'.$fe_termin.'</a></td>",           
					"term": "<td><a href=\'javascript:void(0);\'>'.$no_term.'</a></td>",          
					"program_name": "<a href=\'javascript:void(0);\'>'.$nb_programa.'</a> ",
					"day": "<td> </td>",
					"time": "<td></td>",
					
					"options": "<a href=\'javascript:Add(\"'.$fl_class_time.'\");\'  rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.ObtenEtiqueta(1012).'\'    class=\'details-control sorting_1\' > </a> "
												 
				}'; 
			
				//   if($i<=($registros-1))
				echo ",";
				//   else
				//     echo "";	 
				#Recuperamos las clases que tiene cada programa.
				$Query2="SELECT  cl_dia,no_hora,fl_class_time_programa,ds_tiempo  FROM k_class_time_programa WHERE fl_class_time=$fl_class_time ";
				$ri=EjecutaQuery($Query2);
				$registros2 = CuentaRegistros($ri);
				for($ii=1;$roww=RecuperaRegistro($ri);$ii++) {
					
					$fl_class_time_programa=$roww['fl_class_time_programa'];
					$cl_dia=$roww['cl_dia'];
					$no_hora=$roww['no_hora'];
					$ds_tiempo=$roww['ds_tiempo'];
					
					$select='<option value=\"0\">&nbsp;&nbsp;&nbsp;Selected&nbsp;&nbsp;&nbsp;</option> ';
					for($m = 0; $m < $tot; $m++) {
						$values=$num[$m];
						
						$select.='<option value=\"'.$values.'\" ';
						
						if($cl_dia==$num[$m])
							$select.=" selected ";  
						
						$select.="> $dias[$m]</option> ";
					}
					
					
					
					echo '
						{
							"checkbox": " ",				
							"start_date": "<span class=\"hidden\">'.$fe_inicio.'</span> ",
							"end_date": "<span class=\"hidden\">'.$fe_termin.'</span> ",           
							"term": "<td><span class=\"hidden\">'.$no_term.'</span></td>",          
							"program_name": "<span class=\"hidden\">'.$nb_programa.'</span> ",
							"day": "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class=\"form-group\" ><select style=\"    width: 128px;\"  id=\"cl_dia_'.$fl_class_time_programa.'\" class=\"select2 mikel_jd\" disabled>'.$select.' </select> </div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>",
							"time": "<td class=\"text-center\"><div class=\"form-group\" style=\"margin-left:0px;style=\"width: 100%\"\"><div class=\"input-group \" style=\"width: 70%\"> <input class=\"form-control picker mike_select\" id=\"timepicker_'.$fl_class_time_programa.'\" type=\"text\" value=\"'.$no_hora.' '.$ds_tiempo.'\" placeholder=\"Select time\" disabled><span class=\"input-group-addon\"><i class=\"fa fa-clock-o\"></i></span></div></div> </td>",
							
							"options": "<a href=\'javascript:Edit('.$fl_class_time.','.$fl_class_time_programa.');\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.ObtenEtiqueta(11).'\'   class=\'btn btn-xs btn-default\' ><i class=\'fa  fa-pencil\'></i> </a> &nbsp;<a href=\'javascript:SaveUpdate('.$fl_class_time.','.$fl_class_time_programa.');\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.ObtenEtiqueta(2017).'\'   class=\'btn btn-xs btn-default\' ><i class=\'fa  fa-floppy-o\'></i> </a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'javascript:Borra('.$fl_class_time.','.$fl_class_time_programa.');\'  rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.ObtenEtiqueta(12).'\'  class=\'btn btn-xs btn-default\' ><i class=\'fa  fa-trash-o\'></i> </a>  "
											
							   
					 
						}'; 
					
					
					
					
					//   if($i<=($registros-1))
					echo ",";
					//    else
					//        echo "";
			
				} #End for clases.
				
		}		
				
		
		
		$fl_class_time_revisado=$fl_class_time;
		$fl_term_revisado=$fl_term;
		

        
    }



    
    echo '
				{
					"checkbox": " ",
					
					"start_date": " ",
					"end_date": " ",           
					"term": " ",          
					"program_name": " ",
					"day": "<td>  </td>",
					"time": "<td> </td>",
					
					"options": " "
									
					   
			 
				}'; 




    ?>
   ]

}
