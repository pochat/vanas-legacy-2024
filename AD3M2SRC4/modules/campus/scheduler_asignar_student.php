<?php
	
require '../../lib/general.inc.php';

$seleccionados=RecibeParametroNumerico('seleccionados');
$users=RecibeParametroHTML('users');
$fg_tipo_studiante=RecibeParametroHTML('fg_tipo_studiante');
$allusers=$_POST['allusers']."''";
$valor = explode(",", $users);
$step=RecibeParametroNumerico('step');

echo"
<style>
.smart-form .checkbox, .smart-form .radio {
    padding-left: 0px;
}
.checkbox, .radio {
margin-top: -4px !important;

}
.smart-form .radio input+i:after {
    top: 3px;
    left: 3px;
}
</style>
";


/**
* Se procesa asignar informacion a BD y aplica solamente para Applications.
*/ 
 if($fg_tipo_studiante==1){

     if($step==1){


         #Recuperamos el programa y ciclo inscrito(estos datos ya deben existir vienen del application form). y vamos acomodando usuarios.
         $Query="SELECT DISTINCT a.fl_programa,b.fl_periodo,c.nb_programa
            FROM k_ses_app_frm_1 a
            JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
            JOIN c_programa c ON c.fl_programa=a.fl_programa
            WHERE cl_sesion IN($allusers) ";
         $rs1=EjecutaQuery($Query);
         for($b=1;$row=RecuperaRegistro($rs1);$b++) {
             $nb_programa=$row['nb_programa'];
             $fl_programa=$row['fl_programa'];
             $fl_periodo=$row['fl_periodo'];
             //los applications por default son term 1 Y ya traen periodo
             echo"<div class='row'>
                <div class='col-md-12'>
                    <div class='panel panel-primary'>
                         <div class='panel-heading'>
                              <span class='panel-title' style='font-size:16px;'>$nb_programa</span>                                         
             ";

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

             $Query2="SELECT a.fl_programa, a.no_grado,b.nb_periodo,b.fe_inicio,DATE_FORMAT(NOW(),'%Y-%m-%d')
                     FROM k_term a 
                     JOIN c_periodo b ON b.fl_periodo=a.fl_periodo
                     WHERE fl_programa=$fl_programa AND b.fl_periodo=$fl_periodo ";
             $rs2 = EjecutaQuery($Query2);
             for($x=1;$row2=RecuperaRegistro($rs2);$x++){
                 $nb_periodo=$row2['nb_periodo'];
                 $no_grado=$row2['no_grado'];
                 $fl_periodo=$fl_periodo;
                 $fl_programa=$row2['fl_programa'];

                 echo"<span><i><b>Cycle:</b> $nb_periodo, <b>Term:</b> $no_grado</i> (Term will be created when published)</span>";
                 echo"</div>
                         <div class='panel-body'>";
                 #Recupermaos los user selecicnados relacionados al term y perido elegido.
                 $Query3="SELECT DISTINCT a.ds_fname,a.ds_lname,al.ds_ruta_avatar 
                         FROM k_ses_app_frm_1 a
                         left JOIN c_usuario b ON a.cl_sesion=b.cl_sesion
                         left JOIN c_alumno al ON al.fl_alumno=b.fl_usuario
                         WHERE a.fl_programa=$fl_programa AND a.fl_periodo=$fl_periodo 
				         AND a.cl_sesion IN($allusers) ";
                 $rs3 = EjecutaQuery($Query3);
                 $count3=CuentaRegistros($rs3);
                 if($count3)
                     echo"<ul class='list-inline'>";
                 for($m=1;$row3=RecuperaRegistro($rs3);$m++){
                     $ds_fname=$row3['ds_fname'];
                     $ds_lname=$row3['ds_lname'];
                     $ds_avatar=$row3['ds_ruta_avatar'];
                     if(!empty($ds_avatar)){
                         $ds_ruta_avatar = "../../../modules/images/avatars/$ds_avatar";
                     } else {
                         $ds_ruta_avatar = "../../../images/avatar_default.jpg";
                     }


                     echo"
                        <li><img style='width:35px;' class='img-thumbnail' src='$ds_ruta_avatar'> $ds_fname $ds_lname </li>
                        
                        ";                 
                 }
                 if($count3)
                     echo "</ul>";

             }
             //Forma_CampoCheckbox('Group Review Class :', 'fg_grupo_global_'.$b.'', (isset($fg_grupo_global)?$fg_grupo_global:NULL),'', (isset($fg_grupo_global)?$fg_grupo_global:NULL),true);
             echo"<br><br>
                  <div class='smart-form'>
                    <div class='inline-group'>
						<label class='radio'>
							<input type='radio' name='ckbox_".$b."' id='ckbox_".$b."' checked>
							<i></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Class Time</label>
						<label class='radio'>
							<input type='radio' name='ckbox_".$b."' id='ckbox_".$b."' Onclick='MuestraForm(1,'".$b."');'>
							<i></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Group Review Class</label>
						<label class='radio '>
							<input type='radio' name='ckbox_".$b."' id='ckbox_".$b."' Onclick='MuestraForm(2,'".$b."');'>
							<i></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Normal Class</label>
				
				    </div>
                </div>
            ";
            // Forma_CampoTexto(ObtenEtiqueta(420), True, 'nb_grupo', $nb_grupo, 50, 20, $nb_grupo_err);
             echo"<div class='col-md-12' id='input_group_".$b."'>
                    <label class='control-label'></label>
                    <label class='input'><b>".ObtenEtiqueta(420).":</b> <input type='text' class='form-control' id='nb_grupo' name='nb_grupo' value='' maxlength='50' size='20'>
                    </label>
                  </div>
                  ";

             echo"
                   </div>
                </div>";
             echo"
                </div>
            </div>";
         }


     }




 }#End Applications.



 if($fg_tipo_studiante==2){
    if($step==1){

    }
 }


for ($i = 0; $i <= $seleccionados; $i++) {

    $fl_usuario = $valor[$i];

    /**
     * Se procesa asignar informacion a BD y aplica para estudiantes
     */    
    if($fg_tipo_studiante==2){
        
        #Son estudiantes verificamos que term esta cursando.
        $Query="SELECT u.fl_usuario, u.ds_nombres ds_fname,u.ds_amaterno ds_mname ,u.ds_apaterno ds_lname,p.nb_programa,''gpa,Term.no_grado current_term,''next_term,''advance_new_term,
                DATE_FORMAT(pe.fe_inicio, '%M %D, %Y') fe_inicios,pe.fe_inicio,''no_promedio,alu.mn_progreso,Term.fl_periodo,Term.no_grado,Term.fl_term,p.fl_programa  
                FROM c_usuario u
                JOIN k_ses_app_frm_1 a ON a.cl_sesion=u.cl_sesion
                JOIN c_programa p ON p.fl_programa= a.fl_programa
                left JOIN k_alumno_grupo AlumnoGrupo ON(AlumnoGrupo.fl_alumno = u.fl_usuario) AND AlumnoGrupo.fg_grupo_global<>'1'
                LEFT JOIN c_grupo Grupo ON (Grupo.fl_grupo = AlumnoGrupo.fl_grupo)
                LEFT JOIN k_term Term ON(Term.fl_term = Grupo.fl_term)
                LEFT JOIN c_periodo pe ON pe.fl_periodo=a.fl_periodo AND pe.fl_periodo=Term.fl_periodo 
                JOIN c_alumno alu ON alu.fl_alumno=u.fl_usuario
                WHERE u.fg_activo='1' AND u.fl_perfil=3 AND u.fl_usuario=$fl_usuario ";
        $row=RecuperaValor($Query);
        $fl_term=$row['fl_term'];
        $fl_periodo=$row['fl_periodo'];
        $fl_programa=$row['fl_programa'];
        $no_grado_act=$row['no_grado'];
        #el no grado siempre tiene que ser menor a 4 que es el grado maximo.
        if($no_grado<4){
            $no_grado_prox=$row['no_grado']+1;

            #Verifica si existe el grado en terms start dates. y si no lo crea.
            $Query2="SELECT COUNT(*) FROM k_term WHERE fl_periodo=$fl_periodo AND fl_programa=$fl_programa AND no_grado=$no_grado_prox ";
            $row2=RecuperaValor($Query2);
            $existe_grado=$row2[0];


            if(empty($existe_grado)){
                
                $Queryt  = "INSERT INTO k_term (fl_programa, fl_periodo, no_grado, fl_term_ini) ";
                $Queryt .= "VALUES($fl_programa, $fl_periodo, $no_grado_prox, $fl_term_ini)";
                $fl_term = EjecutaInsert($Queryt);



            }



        }


    }


    
}

?>
<script>
    $(document).ready(function () {
        pageSetUp();
    });

    function MuestraForm(ck_select,count){






    }

</script>

		