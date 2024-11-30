<?php
# Recupera los datos de la entrega de la semana
$fl_grupo = ObtenGrupoAlumno($fl_alumno);
$fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);
$Query  = "SELECT fl_entrega_semanal, fg_entregado, ds_critica_animacion, fl_promedio_semana ";
$Query .= "FROM k_entrega_semanal ";
$Query .= "WHERE fl_alumno=$fl_alumno ";
$Query .= "AND fl_grupo=$fl_grupo ";
$Query .= "AND fl_semana=$fl_semana";
$row = RecuperaValor($Query);
$fl_promedio_semana = $row[3];

#Verificamos si tiene rubric
$tiene_rubric=TieneRubricLeccionCampus($fl_leccion,$no_semana);




# Message
if(empty($fl_promedio_semana)){

   
    if(!empty($tiene_rubric)){
        $result["message"] = "<style>a {
                                    color: #000;!important}
									 .chart {
										/* height: 220px; */
										margin: auto !important;
									}
									
									
                             </style> ".PresentaRubric($fl_leccion, $fl_alumno);//muestr RUBRIC DE COMO SE VA A calificar
    }else{
    
    
        $result["message"] ="<div class='row'>
                                <div class='col-md-12 text-center'>

                                    <div class='alert alert-danger' >
                                        <strong><i class='fa fa-window-close-o fa-5' aria-hidden='true'></i></strong>&nbsp;".ObtenEtiqueta(1695)."
                                    </div>
                                </div>

                            </div>";
    
    }

}else{
    
    
   if(!empty($tiene_rubric)){  
    
       $result["message"] = "<style>a {
                                    color: #000;!important}
									
									 .chart {
										/* height: 220px; */
										margin: auto !important;
									}
									
                             </style> ".PresentaRubric($fl_leccion, $fl_alumno, $fl_grupo, $fl_semana); #Muestra rubric cuando ya etsa calificado
       
       
       
       
   }else{
       $result["message"] ="<div class='row'>
                                <div class='col-md-12 text-center'>

                                    <div class='alert alert-danger' >
                                        <strong><i class='fa fa-window-close-o fa-5' aria-hidden='true'></i></strong>&nbsp;".ObtenEtiqueta(1695)."
                                    </div>
                                </div>

                            </div>";
     
   }



}
echo json_encode((Object) $result);  
?>