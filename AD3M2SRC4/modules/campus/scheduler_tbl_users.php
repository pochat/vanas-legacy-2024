<?php
	
require '../../lib/general.inc.php';

$fg_data=RecibeParametroNumerico('fg_data');

$fl_programa=RecibeParametroNumerico('fl_programa');
#falta un algoritmo que identifique el periodo proximo.
$fl_periodo=ObtenConfiguracion(143);

?>

<table id="table_users_<?php echo $fg_data;?>" class="table table-hover" style="width:100%;">
	<thead>
		<tr>
            <th>&nbsp;&nbsp;<label class='checkbox no-padding no-margin'><input class='checkbox' type='checkbox' id='sel_todo_<?php echo $fg_data;?>' name='sel_todo_<?php echo $fg_data;?>'><span></span></label>&nbsp;</th>
			<th  class="text-center">Student Name</th>
			<th  class="text-center">Course name</th>
			<th  class="text-center">GPA</th>
			<th  class="text-center">Current Term</th>
			<th  class="text-center">Next Term</th>
			<th  class="text-center">Advance to next Term?</th>
            <th class="text-center">Start date </th>
		</tr>
	</thead>
	<tbody>
        <?php
        if($fg_data==1){
            $Query ="SELECT a.cl_sesion fl_usuario, a.ds_fname,a.ds_mname,a.ds_lname,p.nb_programa, 'New Student' gpa,0 current_term, 1 next_term,'New Student' advance_new_term, 
                DATE_FORMAT(pe.fe_inicio, '%M %D, %Y') fe_inicios,pe.fe_inicio,0 no_promedio,''mn_progreso,a.fl_periodo,''no_grado,''fl_term 
                FROM k_ses_app_frm_1 a 
                JOIN c_sesion s ON a.cl_sesion=s.cl_sesion 
                JOIN c_programa p ON p.fl_programa=a.fl_programa
                LEFT JOIN k_app_contrato c ON a.cl_sesion=c.cl_sesion
                JOIN c_periodo pe ON pe.fl_periodo=a.fl_periodo  
                WHERE s.fg_app_1='1' AND s.fg_app_2='1' AND s.fg_app_3='1' AND s.fg_app_4='1' 
                AND s.fg_confirmado='1' AND s.fg_inscrito='0' AND s.fg_archive='0' AND c.ds_firma_alumno IS NOT NULL 
                AND (no_contrato IS NULL OR no_contrato=1) AND a.fl_periodo=$fl_periodo ";
            //para omitir los seleccionados.
            $Query.="AND NOT EXISTS(SELECT fl_alumno FROM k_clase_calendar_alumno ca WHERE ca.fl_alumno=a.cl_sesion ) ";
            if(!empty($fl_programa))
                $Query.="AND a.fl_programa= $fl_programa ";
                $Query.="ORDER BY fe_inicio ASC ";
        }
        if($fg_data==2){
                $Query ="
                SELECT u.fl_usuario, u.ds_nombres ds_fname,u.ds_amaterno ds_mname ,u.ds_apaterno ds_lname,p.nb_programa,''gpa,Term.no_grado current_term,''next_term,''advance_new_term,
                DATE_FORMAT(pe.fe_inicio, '%M %D, %Y') fe_inicios,pe.fe_inicio,''no_promedio,alu.mn_progreso,Term.fl_periodo,Term.no_grado,Term.fl_term 
                FROM c_usuario u
                JOIN k_ses_app_frm_1 a ON a.cl_sesion=u.cl_sesion
                JOIN c_programa p ON p.fl_programa= a.fl_programa
                left JOIN k_alumno_grupo AlumnoGrupo ON(AlumnoGrupo.fl_alumno = u.fl_usuario) AND AlumnoGrupo.fg_grupo_global<>'1'
                LEFT JOIN c_grupo Grupo ON (Grupo.fl_grupo = AlumnoGrupo.fl_grupo)
                LEFT JOIN k_term Term ON(Term.fl_term = Grupo.fl_term)
                LEFT JOIN c_periodo pe ON pe.fl_periodo=a.fl_periodo AND pe.fl_periodo=Term.fl_periodo 
                JOIN c_alumno alu ON alu.fl_alumno=u.fl_usuario
                WHERE u.fg_activo='1' AND u.fl_perfil=3 
                ";
            if(!empty($fl_programa))
                $Query.="AND Term.fl_programa= $fl_programa ";
        }
        $rs=EjecutaQuery($Query);
        $tot_reg = CuentaRegistros($rs);
        for($i=1;$row=RecuperaRegistro($rs);$i++) {
            $fl_usuario=$row['fl_usuario'];
            $ds_fname=$row['ds_fname'];
            $ds_mname=$row['ds_mname'];
            $ds_lname=$row['ds_lname'];
            $nb_programa=$row['nb_programa'];
            $gpa=$row['gpa'];
            $current_term=$row['current_term'];
            $next_term=$row['next_term'];
            $advance_new_term=$row['advance_new_term'];
            $fe_inicio=$row['fe_inicios'];
            $no_promedio=$row['no_promedio'];
            $mn_progreso=$row['mn_progreso'];

            $current_term_label="<span class='label label-primary'>Term: ".$current_term."</span>";
            $next_term="<span class='label label-primary'>Term: 1</span>";
            if($gpa=='New Student'){
                $print_gpa="<span class='label label-primary'>$gpa</span>";
                $print_advance_new_term="<span class='label label-primary'>$advance_new_term</span>";
            }else{
                //Por defaul palica para students activos.
                $Query00  = "SELECT MAX(a.fl_term), a.no_promedio FROM k_alumno_term a, k_term b ";
                $Query00 .= "WHERE a.fl_term = b.fl_term AND a.fl_alumno=".$fl_usuario." ORDER BY b.no_grado DESC";
                $row00 = RecuperaValor($Query00);
                $fl_term_max = !empty($row00[0])?$row00[0]:NULL;

                # Obtener el promedio
                $Query01 = "SELECT no_promedio FROM k_alumno_term WHERE fl_alumno=".$fl_usuario." AND fl_term=".$fl_term_max;
                $row01 = RecuperaValor($Query01);
                $no_promedio = $row01[0];
                if(empty($no_promedio))
                    $no_promedio=0;
                $row01 = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio) AND no_max >= ROUND($no_promedio)");
                $cl_calificacion = $row01[0];
                $fg_aprobado=$row01['fg_aprobado'];

                $print_gpa="<div class='progress progress-xs' data-progressbar-value='".round($mn_progreso)."'><div class='progress-bar'></div></div><br><small class='text-muted'>".$cl_calificacion." (".$no_promedio."%)</small>";
                        
                        
                $Queri="SELECT no_grado 
                            FROM k_term a 
							JOIN c_periodo p ON p.fl_periodo=a.fl_periodo
							WHERE a.fl_periodo=67 
							AND no_grado>$current_term
							ORDER BY a.no_grado ASC ";
                $rowi=RecuperaValor($Queri);
                if(!empty($rowi['no_grado'])){//tiene que ver mas terms mayores a 4
                    $next_term="<span class='label label-primary'>Term: ".$rowi['no_grado']."</span>";
                    $print_advance_new_term="<span class='label label-success'>Yes</span>";
                }else{

                    $next_term="<span class='label label-primary'>Term: $current_term</span>";
                    if($current_term==4)
                        $print_advance_new_term="<span class='label label-info'style='background:#808080;'>Grad</span>";
                    else
                        $print_advance_new_term=$next_term;
                }
                #Si esta reporbaod dira que tiene repetir term.
                if(empty($fg_aprobado))
                    $print_advance_new_term="<span class='label label-danger'>Repeat</span>";

            }

         



        ?>
		<tr>
            <td><label class='checkbox no-padding no-margin'><input class='checkbox' type='checkbox' id='ch_<?php echo $i;?>' value='<?php echo $fl_usuario;?>'><span></span></label><input type='hidden' id='use_lic_<?php echo $i;?>' name='use_lic_<?php echo $i;?>' value='1'></td>
			<td><?php echo $ds_fname."<br><small class='text-muted'><i>".$ds_lname."</i></small>";?></td>
			<td><?php echo $nb_programa;?></td>
			<td class="text-center"><?php echo $print_gpa;?></td>
            <td class="text-center"><?php echo $current_term_label;?></td>
			<td class="text-center"><?php echo $next_term;?></td>
            <td class="text-center"><?php echo $print_advance_new_term;?></td>
            <td class="text-center"><?php echo $fe_inicio;?></td>

		</tr>

        <?php 
        }
                
        ?>             
	</tbody>
</table>
<input type="hidden" name="tot_reg" id="tot_reg" value="<?php echo $tot_reg;?>" />


<br />
					
<script>
    $(document).ready(function () {
        pageSetUp();
        $('#table_users_<?php echo $fg_data;?>').DataTable({
            "order": [[6, "desc"]],
            "paging":   false,
        });
    });

    /** INICIO DE SELECIONAR TODOS CHECKBOX ***/
    $('#sel_todo_<?php echo $fg_data;?>').on('change', function () {
        var v_sel_todo = $(this).is(':checked'), i;
        var iTotalRecords = "<?php echo $tot_reg; ?>";
        for (i = 1; i <= iTotalRecords; i++) {
            $("#ch_" + i).prop('checked', v_sel_todo);
        }
    })
  

    function AsignarStudentTerms() {

        var tot_reg = $("#tot_reg").val(), i = 1, j = 1, seleccionados = 0;
        var fg_tipo_studiante = "<?php echo $fg_data;?>";

        // Arreglo para identificar cuantod usuarios fueron seleccionados 
        var users = [];
        var allusers = "";
        for (i; i <= tot_reg; i++) {
            var reg = $("#ch_" + i).is(':checked');
            var val = $("#ch_" + i).val();
            var use_lic = $("#use_lic_" + i).val();
            if (reg == true) {
                seleccionados++;
                // Solo contara a los estudiantes
                if ((users.indexOf(val) < 0) && (use_lic == 1)) {
                    allusers += "'" + val + "',";
                    users.push(val);
                }
            }
        }

        if (seleccionados > 0) {

            $.smallBox({
                title: "<?php echo ObtenEtiqueta(2668); ?>",
                content: "&nbsp;",
                color: "#739E73",
                timeout: 4000,
                iconSmall: "fa fa-check ",
                //number : "2"
            });

            $('#exampleModal').modal('hide');
        } else {
             $.smallBox({
                title: "<?php echo ObtenEtiqueta(2669); ?>",
                content: "&nbsp;",
                color: "#a0011e",
                timeout: 4000,
                iconSmall: "fa fa-times-circle-o",
                 //number : "2"
             });

            return;

        }

        $.ajax({
            type: 'POST',
            url: 'scheduler_relations.php',
            data: 'users=' + users +
                  '&allusers=' + allusers +
                  '&seleccionados=' + seleccionados +
                  '&fg_tipo_studiante=' + fg_tipo_studiante,
            async: false,
            success: function (html) {

                if (fg_tipo_studiante == 1) {
                    $('#accordion-2').html(html);
                }
                if (fg_tipo_studiante == 2) {
                    $('#accordion-3').html(html);
                }

            }
        });




    }
</script>