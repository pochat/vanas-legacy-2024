<?php
	
require '../../lib/general.inc.php';



/**
 * 
 * Functions generales
 * 
 */ 
# Actualizamos las fechas inicio y fin de cada pago

function fe_ini_fe_fin($clave){
    $Query  = "SELECT  no_pago, d.fe_inicio fe_inicio_programa, CASE  a.no_opcion ";
    $Query .= "WHEN '1'  THEN (SELECT no_a_payments FROM k_programa_costos i WHERE i.fl_programa=b.fl_programa) ";
    $Query .= "WHEN '2'  THEN (SELECT no_b_payments FROM k_programa_costos i WHERE i.fl_programa=b.fl_programa) ";
    $Query .= "WHEN '3'  THEN (SELECT no_c_payments FROM k_programa_costos i WHERE i.fl_programa=b.fl_programa) ";
    $Query .= "WHEN '4'  THEN (SELECT no_d_payments FROM k_programa_costos i WHERE i.fl_programa=b.fl_programa) END no_pagos, c.no_semanas,a.fl_term_pago ";
    $Query .= "FROM k_term_pago a, k_term b, k_programa_costos c, c_periodo d ";
    $Query .= "WHERE a.fl_term=b.fl_term AND b.fl_programa = c.fl_programa AND b.fl_periodo = d.fl_periodo AND b.fl_term=$clave";
    $rs = EjecutaQuery($Query);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
        $no_pago = $row[0];
        $fe_inicio_programa = $row[1];
        $no_pagos= $row[2];
        $no_semanas = $row[3];
        $fl_term_pago = $row[4];
        
        //meses que dura el curso
        $meses_duracion=$no_semanas/4;
        //meses por pago
        $meses_pago = $meses_duracion/$no_pagos;
        //anios que se le aumenta a la fecha inicial del programa
        $desfase = ($no_pago-1)*$meses_pago;
        
        //fecha inicial del pago
        $fe_ini_pago = date('Y-m-d',strtotime ( "+ ".$desfase." month", strtotime($fe_inicio_programa)));
        //fecha final del pago
        $fe_fin_pago = date('Y-m-d',strtotime ( "+ ".$meses_pago." month", strtotime($fe_ini_pago)));
        
        //Actualiza los datos
        $Update = "UPDATE k_term_pago SET fe_ini_pago = '$fe_ini_pago', fe_fin_pago = '$fe_fin_pago' WHERE fl_term_pago=$fl_term_pago ";
        EjecutaQuery($Update);
    }
    
}


echo"<style>
.img-thumbnail {
    padding: 2px;
}
    </style>";


$seleccionados=RecibeParametroNumerico('seleccionados');
$users=RecibeParametroHTML('users');
$fg_tipo_studiante=RecibeParametroHTML('fg_tipo_studiante');
$allusers=$_POST['allusers']."''";
$valor = explode(",", $users);

#falata un script que identifique el periodo prox.
$fl_periodo_default=ObtenConfiguracion(143);//id proviene de  c_periodo.

EjecutaQuery("DELETE FROM k_clase_fetch_programs WHERE 1=1 ");

if($fg_tipo_studiante==1){ //applications form

    #Recuperamos el programa y ciclo inscrito(estos datos ya deben existir vienen del application form). y vamos acomodando usuarios.
    $Query="SELECT DISTINCT a.fl_programa,c.nb_programa
    FROM k_ses_app_frm_1 a
    JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
    JOIN c_programa c ON c.fl_programa=a.fl_programa
    WHERE cl_sesion IN($allusers) ";
    $rs1=EjecutaQuery($Query);
    for($b=1;$row=RecuperaRegistro($rs1);$b++) {
        $nb_programa=$row['nb_programa'];
        $fl_programa=$row['fl_programa'];
     
?>
      
        <div class="panel panel-default">
            <div class="panel-heading">
	            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion-2" href="#collapse_<?php echo $fl_programa?>-1" class="collapsed"> <i class="fa fa-fw fa-plus-circle txt-color-green"></i> <i class="fa fa-fw fa-minus-circle txt-color-red"></i> <?php echo $nb_programa;?> </a></h4>
	        </div>
            <div id="collapse_<?php echo $fl_programa;?>-1" class="panel-collapse collapse">
                <div class="panel-body">
                     <ul id="external-events" class="list-group external-events">
<?php

        #Recuperamos el programa y ciclo inscrito(estos datos ya deben existir vienen del application form). y vamos acomodando usuarios.
        $QueryP="SELECT DISTINCT a.fl_programa,b.fl_periodo,c.nb_programa
                FROM k_ses_app_frm_1 a
                JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
                JOIN c_programa c ON c.fl_programa=a.fl_programa
                WHERE cl_sesion IN($allusers) AND c.fl_programa=$fl_programa ";
        $rsi=EjecutaQuery($QueryP);
        for($c=1;$rowc=RecuperaRegistro($rsi);$c++) {
            $nb_programa=$rowc['nb_programa'];
            $fl_programa=$rowc['fl_programa'];
            $fl_periodo=$rowc['fl_periodo'];

            #Verificamos si existe el ciclo y si no se crea temporalmente. //por default siempre se buscara el grado 1 de aplications.
            $Queryt  = "SELECT count(1) ";
            $Queryt .= "FROM k_term ";
            $Queryt .= "WHERE fl_programa=$fl_programa ";
            $Queryt .= "AND fl_periodo=$fl_periodo ";
            $Queryt .= "AND no_grado=1 ";
            $rowt=RecuperaValor($Queryt);
            if(empty($rowt[0])){
                $no_grado=1;
                $fl_term_ini=0;
                include "scheduler_crud_terms_start.php";
            }
            $Query2 ="SELECT a.fl_programa, a.no_grado,b.nb_periodo,b.fe_inicio,DATE_FORMAT(NOW(),'%Y-%m-%d'),a.fl_term ";
            $Query2 .=" ,d.cl_dia,d.no_hora,d.ds_tiempo ";
            $Query2 .="
                             FROM k_term a 
                             JOIN c_periodo b ON b.fl_periodo=a.fl_periodo ";
            $Query2 .="JOIN k_class_time c ON c.fl_periodo=b.fl_periodo AND c.fl_programa=$fl_programa
                            JOIN k_class_time_programa d ON d.fl_class_time=c.fl_class_time ";
            $Query2 .="WHERE a.fl_programa=$fl_programa AND b.fl_periodo=$fl_periodo AND no_grado=1 ";
            $rs2 = EjecutaQuery($Query2);
            for($x=1;$row2=RecuperaRegistro($rs2);$x++){
                $nb_periodo=$row2['nb_periodo'];
                $no_grado=$row2['no_grado'];
                $fl_programa=$row2['fl_programa'];
                $fl_term=$row2['fl_term'];
                $cl_dia=$row2['cl_dia'];
                //$no_hora=$row2['no_hora'];
                //$ds_tiempo=$row2['ds_tiempo'];

                //if($x==1){
                //    $fg_tipo_clase="single_term";
                //    $etq_tipo_clase="Single Class";
                //}else{
                    $fg_tipo_clase="multiple_term";
                    $etq_tipo_clase="Multiple Class";
               // }

                 

                switch($cl_dia){
                    
                    case '1':
                        $ds_dia="".ObtenEtiqueta(2390)."";
                        break;
                    case '2':
                        $ds_dia="".ObtenEtiqueta(2391)."";
                        break;
                    case '3':
                        $ds_dia="".ObtenEtiqueta(2392)."";
                        break;
                    case '4':
                        $ds_dia="".ObtenEtiqueta(2393)."";
                        break;
                    case '5':
                        $ds_dia="".ObtenEtiqueta(2395)."";
                        break;
                    case '6':
                        $ds_dia="".ObtenEtiqueta(2396)."";
                        break;
                }

                #INSERTAMOS DATOS EN BD.
                $Query ="INSERT INTO k_clase_fetch_programs (fl_programa,fl_periodo,fg_tipo_clase,no_grado,fl_term,fe_creacion,fe_ultmod) ";
                $Query.="VALUES($fl_programa,$fl_periodo,'$fg_tipo_clase',$no_grado,$fl_term,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                $fl_clase_fecth=EjecutaInsert($Query);


?>
                        <li id="group_<?php echo $fl_term?>_tipo_<?php echo $fg_tipo_clase;?>">
                              <span  style="font-size: 12px;color: #151414!important; background-color:#e2e1e1!important;" class="bg-color-red" data-description="<?php echo $fl_term;?>" data-icon="<?php echo $fl_programa."#".$no_grado."#".$allusers."#".$fl_periodo."#".$fg_tipo_studiante."#".$fg_tipo_clase;?>" >
                                  <b><?php echo $nb_programa;?></b><br />
                                  <b>Term:</b> <?php echo $no_grado;?><br />
                                  <b>Cycle:</b> <?php echo $nb_periodo;?><br />
                                  <b>Details:</b><b> <?php echo $etq_tipo_clase; ?></b><br />
                                  <b><?php echo $ds_dia;?></b><br />
                                  <b>Students:</b><br /> 
                                  <?php 
                $Query3="SELECT ds_fname,ds_lname,cl_sesion  FROM  k_ses_app_frm_1 WHERE fl_programa=$fl_programa AND fl_periodo=$fl_periodo AND cl_sesion IN($allusers) ";
                $rs3 = EjecutaQuery($Query3);
                for($x3=1;$row3=RecuperaRegistro($rs3);$x3++){
                    $ds_fname=$row3['ds_fname'];
                    $ds_lname=$row3['ds_lname'];
                    $cl_sesion=$row3['cl_sesion'];

                    $Query="INSERT INTO k_clase_fetch_programs_alumno(fl_clase_fetch_programs,fl_alumno,fg_tipo_estudiante,fe_creacion,fe_ultmod) ";
                    $Query.="VALUES($fl_clase_fecth,'$cl_sesion','$fg_tipo_studiante',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                    $fl_clase_student=EjecutaInsert($Query);


                                  ?>
                                  <span><img style="width:15px;" class="img-thumbnail" src="../../../images/avatar_default.jpg"/> <?php echo $ds_fname." ".$ds_lname;?></span><br />
                                  <?php 
                }
                                  ?>
                              </span>
                        </li>
<?php 
               ######################
               


            }

        }
        
?>                  
                        
                     </ul>
        
                </div>
            </div>
        </div>



<?php
    }
}
?>






<?php
if($fg_tipo_studiante==2){


    #Recuperamos el programa y ciclo inscrito(estos datos ya deben existir vienen del application form). y vamos acomodando usuarios.
    $Query="SELECT distinct
            p.nb_programa,p.fl_programa,b.nb_periodo
            FROM c_usuario u
            JOIN k_ses_app_frm_1 a ON a.cl_sesion=u.cl_sesion
            JOIN c_programa p ON p.fl_programa= a.fl_programa
            JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
            WHERE u.fl_usuario IN($allusers) /*AND b.fl_periodo=$fl_periodo_default */";

    $rs1=EjecutaQuery($Query);
    for($b=1;$row=RecuperaRegistro($rs1);$b++) {
        $nb_programa=$row['nb_programa'];
        $fl_programa=$row['fl_programa'];
?>
    
  <div class="panel panel-default">
            <div class="panel-heading">
	            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion-3" href="#collapse_<?php echo $fl_programa?>-12" class="collapsed"> <i class="fa fa-fw fa-plus-circle txt-color-green"></i> <i class="fa fa-fw fa-minus-circle txt-color-red"></i> <?php echo $nb_programa;?> </a></h4>
	        </div>
            <div id="collapse_<?php echo $fl_programa;?>-12" class="panel-collapse collapse">
                <div class="panel-body">
                     <ul id="external-events" class="list-group external-events">

             <?php 
              #Recuperamos el programa y ciclo inscrito(estos datos ya deben existir vienen del application form). y vamos acomodando usuarios.
              $QueryP="SELECT DISTINCT a.fl_programa,$fl_periodo_default fl_periodo ,c.nb_programa
                       FROM c_usuario u 
                       JOIN  k_ses_app_frm_1 a ON a.cl_sesion=u.cl_sesion 
                       JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
                       JOIN c_programa c ON c.fl_programa=a.fl_programa
                       WHERE u.fl_usuario IN($allusers) AND c.fl_programa=$fl_programa   ";
              $rsi=EjecutaQuery($QueryP);
              for($c=1;$rowc=RecuperaRegistro($rsi);$c++) {
                  $nb_programa=$rowc['nb_programa'];
                  $fl_programa=$rowc['fl_programa'];
                  $fl_periodo=$rowc['fl_periodo'];  


                  $no_grados="";
                  #Buscamos el grado que sigue del alumno.
                  $Queryg="SELECT ds_fname,ds_lname,Term.no_grado, (Term.no_grado + 1) no_grado_prox  
                            FROM c_usuario u 
                            JOIN k_ses_app_frm_1 c  ON c.cl_sesion=u.cl_sesion  
                            left JOIN k_alumno_grupo AlumnoGrupo ON(AlumnoGrupo.fl_alumno = u.fl_usuario) AND AlumnoGrupo.fg_grupo_global<>'1'
                            LEFT JOIN c_grupo Grupo ON (Grupo.fl_grupo = AlumnoGrupo.fl_grupo)
                            LEFT JOIN k_term Term ON(Term.fl_term = Grupo.fl_term)
                            WHERE c.fl_programa=$fl_programa /*AND fl_periodo=78 */ 
                            AND u.fl_usuario IN($allusers) ";
                  $rsg=EjecutaQuery($Queryg);
                  for($cg=1;$rowcg=RecuperaRegistro($rsg);$cg++) {

                      $no_grados .="'".$rowcg['no_grado_prox']."',";
                  }
                  $no_grados.="''";

                  #Verificamos si existe el ciclo y si no se crea temporalmente. //Se busca .
                  $Queryt  = "SELECT no_grado,fl_term_ini ";
                  $Queryt .= "FROM k_term ";
                  $Queryt .= "WHERE fl_programa=$fl_programa ";
                  $Queryt .= "AND fl_periodo=$fl_periodo ";
                  $Queryt .= "AND no_grado IN ($no_grados) ";
                  //$rt=EjecutaQuery($Queryt);
                  $rowt=RecuperaValor($Queryt);
                  //for($k=1;$rowt=RecuperaRegistro($rt);$k++){
                  $fl_term_ini=$rowt[1];
                      
                      if(empty($rowt[0])){
                          $no_grado_exist=preg_replace("/[^0-9]/", "", $no_grados);

                          #Recuperamos su term ini
                          # Buscamos el ultimo term que se inserto con el mismo programa  
                         # En caso contrario podra cero
                          if($no_grado != '1' AND empty($fl_term_ini)){
                              if($no_grado==2)
                                  $term = "fl_term";
                              else
                                  $term = "fl_term_ini";
                              $Query  = "SELECT MAX($term) FROM k_term a, c_periodo b ";
                              $Query .= "WHERE a.fl_periodo=b.fl_periodo AND  fl_programa=$fl_programa AND no_grado=$no_grado_exist-1 ";
                              $Query .= "AND fe_inicio < (SELECT fe_inicio FROM c_periodo WHERE fl_periodo=$fl_periodo) ";
                              $Query .= "ORDER BY fe_inicio DESC";
                              $row = RecuperaValor($Query);
                              $fl_term_ini=$row[0];
                          }

                          $no_grado=!empty($rowt['no_grado'])?$rowt['no_grado']:$no_grado_exist;
                          $fl_term_ini=!empty($rowt['fl_term_ini'])?$rowt['fl_term_ini']:$fl_term_ini;
                          include "scheduler_crud_terms_start.php";
                      }

                 // }

                 




                  //SELECCIONAMOS LOS TERMS SIGUYIENTES.
                  $Query2="SELECT a.fl_programa, a.no_grado,b.nb_periodo,b.fe_inicio,DATE_FORMAT(NOW(),'%Y-%m-%d'),a.fl_term,d.cl_dia 
                             FROM k_term a 
                             JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
                             JOIN k_class_time c ON c.fl_periodo=b.fl_periodo AND c.fl_programa=$fl_programa
                             JOIN k_class_time_programa d ON d.fl_class_time=c.fl_class_time 
                             WHERE a.fl_programa=$fl_programa AND b.fl_periodo=$fl_periodo AND no_grado IN ($no_grados) ";
                  $rs2 = EjecutaQuery($Query2);
                  for($x=1;$row2=RecuperaRegistro($rs2);$x++){
                      $nb_periodo=$row2['nb_periodo'];
                      $no_grado=$row2['no_grado'];
                      $fl_programa=$row2['fl_programa'];
                      $fl_term=$row2['fl_term'];
                      $cl_dia=$row2['cl_dia'];

                     // if($x==1){
                     //     $fg_tipo_clase="single_term";
                     //     $etq_tipo_clase="Single Class";
                     // }else{
                          $fg_tipo_clase="multiple_term";
                          $etq_tipo_clase="Multiple Class";
                    //  }
                       

                        switch($cl_dia){
                              
                            case '1':
                                $ds_dia="".ObtenEtiqueta(2390)."";
                                break;
                            case '2':
                                $ds_dia="".ObtenEtiqueta(2391)."";
                                break;
                            case '3':
                                $ds_dia="".ObtenEtiqueta(2392)."";
                                break;
                            case '4':
                                $ds_dia="".ObtenEtiqueta(2393)."";
                                break;
                            case '5':
                                $ds_dia="".ObtenEtiqueta(2395)."";
                                break;
                            case '6':
                                $ds_dia="".ObtenEtiqueta(2396)."";
                                break;
                        }
                      #INSERTAMOS DATOS EN BD.
                      $Query ="INSERT INTO k_clase_fetch_programs (fl_programa,fl_periodo,fg_tipo_clase,no_grado,fl_term,fe_creacion,fe_ultmod) ";
                      $Query.="VALUES($fl_programa,$fl_periodo,'$fg_tipo_clase',$no_grado,$fl_term,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP)";
                      $fl_clase_fecth=EjecutaInsert($Query);


             ?>
                          <li id="group_<?php echo $fl_term?>_<?php echo $fg_tipo_clase;?>">
                              <span  style="font-size: 12px;color: #151414!important; background-color:#e2e1e1!important;" class="bg-color-red" data-description="<?php echo $fl_term;?>" data-icon="<?php echo $fl_programa."#".$no_grado."#".$allusers."#".$fl_periodo."#".$fg_tipo_studiante;?>" >
                                  <b><?php echo $nb_programa;?></b><br />
                                  <b>Term:</b> <?php echo $no_grado;?><br />
                                  <b>Cycle:</b> <?php echo $nb_periodo;?><br />
                                  <b>Details:</b><b> <?php echo $etq_tipo_clase; ?></b><br />
                                  <b><?php echo $ds_dia;?></b>
                                  <b>Students:</b><br /> 

                                  <?php 
                                  $Query3="SELECT ds_fname,ds_lname,fl_usuario  FROM c_usuario u JOIN k_ses_app_frm_1 c  ON c.cl_sesion=u.cl_sesion  WHERE c.fl_programa=$fl_programa /*AND fl_periodo=$fl_periodo */ AND u.fl_usuario IN($allusers) ";
                                  $rs3 = EjecutaQuery($Query3);
                                  for($x3=1;$row3=RecuperaRegistro($rs3);$x3++){
                                      $ds_fname=$row3['ds_fname'];
                                      $ds_lname=$row3['ds_lname'];
                                      $fl_usuario=$row3['fl_usuario'];
                                              ?>
                                              <span><img style="width:15px;" class="img-thumbnail" src="../../../images/avatar_default.jpg"/> <?php echo $ds_fname." ".$ds_lname;?></span><br />
                                              <?php 

                                      $Query="INSERT INTO k_clase_fetch_programs_alumno(fl_clase_fetch_programs,fl_alumno,fg_tipo_estudiante) ";
                                      $Query.="VALUES($fl_clase_fecth,'$fl_usuario','$fg_tipo_studiante')";
                                      $fl_clase_student=EjecutaInsert($Query);




                                  }
                                  ?>
                              </span>

                          </li>
                    <?php
                                                      
                  }
              } 
              ?>
                 </ul>
                </div>
            </div>
 </div>
<?php
    }

}
//end tipo student =1;
?>



<div class="checkbox hidden">
	<label>
		<input type="checkbox" id="drop-remove" class="checkbox style-0" checked="checked">
		<span>remove after drop</span> </label>					
</div>



<script>

    // DO NOT REMOVE : GLOBAL FUNCTIONS!

    $(document).ready(function () {

        pageSetUp();


        "use strict";

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        var initDrag = function (e) {
            // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
            // it doesn't need to have a start or end

            var eventObject = {
                title: $.trim(e.children().text()), // use the element's text as the event title
                description: $.trim(e.children('span').attr('data-description')),
                icon: $.trim(e.children('span').attr('data-icon')),
                className: $.trim(e.children('span').attr('class')) // use the element's children as the event class
            };
            // store the Event Object in the DOM element so we can get to it later
            e.data('eventObject', eventObject);

            // make the event draggable using jQuery UI
            e.draggable({
                cursor: 'pointer',
                zIndex: 9999,
                revert: true, // will cause the event to go back to its
                revertDuration: 0 //  original position after the drag
            });
        };

        $('.external-events > li').each(function () {
            initDrag($(this));
        });


    });
</script>


























