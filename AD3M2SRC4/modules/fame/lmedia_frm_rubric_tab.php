<div class="tab-pane fade" id="rubric">
    <style>
        #sortable { 
        list-style: none; 
        text-align: left; 
        }
        #sortable li { 
        margin: 0 0 10px 0;
        height: 225px; 
        }
    </style>
    <script>
        // Activa el input del valor del rubric
        function ActivaValorRubric(val){
        if(val == 1)
            $('#no_val_rub').prop("disabled", false);
        }
    
        // Funcion para validar sumatoria de rubrics
        function ValidaValorRubric(val){     
        var fl_programa = document.getElementById('fl_programa').value; 
        $.ajax({
            type: 'POST',
            url : 'valida_leccion_valor.php',
            async: false,
            data: 'fl_programa=' + fl_programa + 
                '&rubric=1',
            success: function(data) {
            if(data=='')
                data = 0;
            document.getElementById('no_ter_co2').value = data;
            }
        });
        
        valor_bd  = document.getElementById('no_ter_co2').value; // Sumatoria de valores base de datos
        
        if(val == "")
            val = 0;
        
        no_val_rub_bd = document.getElementById('no_val_rub_bd').value;
        
        valor_tot = (parseInt(valor_bd) + parseInt(val)) - parseInt(no_val_rub_bd); // Sumatoria base de datos + valor actual
        valor_fin = parseInt(100) - parseInt(valor_tot); // Resta del valor maximo - valor total actual
        document.getElementById('no_ter_co').value = valor_fin;

        // Mostramos mensajes de error
        if(valor_tot > 100){ // Si es Mayor a 100
            document.getElementById('no_ter_co').style.backgroundColor = '#FFF0F0';
            document.getElementById('no_ter_co').style.borderColor = '#953b39';
            document.getElementById('MensajeErrRubric').style.display = 'block';
            document.getElementById('MensajeWrgRubric').style.display = 'none';  
        }
        if(valor_tot < 100){ // Si es Menor a 100
            document.getElementById('no_ter_co').style.backgroundColor = '#efe1b3';
            document.getElementById('no_ter_co').style.borderColor = '#dfb56c';
            document.getElementById('MensajeErrRubric').style.display = 'none';
            document.getElementById('MensajeWrgRubric').style.display = 'block';
        }
        if(valor_tot == 100){ // Si es 0 -> Es 100
            document.getElementById('no_ter_co').style.backgroundColor = '#fff';
            document.getElementById('no_ter_co').style.borderColor = '#ccc';
            document.getElementById('MensajeErrRubric').style.display = 'none';  
            document.getElementById('MensajeWrgRubric').style.display = 'none';         
        }
        if(valor_tot == 0)
            document.getElementById('style_sin_criterios').style.display = 'none';
        }
        
        // Funcion para cambiar estilos del boton Add Rubric
        function CambiaEstiloBtn(act, val){
        if(act == 0){
            $('#btn_add_rubric').removeClass('btn bg-color-blueLight txt-color-white disabled');
            $('#btn_add_rubric').addClass('btn btn-primary');
        }else{
            $('#btn_add_rubric').removeClass('btn btn-primary');
            $('#btn_add_rubric').addClass('btn bg-color-blueLight txt-color-white disabled');
            var fl_criterio = document.getElementById("fl_criterio").value;
            var clave = <?php echo $clave; ?>;
            $.ajax({
            type: 'POST',
            url : 'arma_rubrics.php',
            async: false,
            data: 'fl_criterio='+fl_criterio+
                    '&val='+val+
                    '&clave='+clave,
            success: function(data) {
                $("#muestra_rubrics").html(data);
            }
            });              
        }
        if(val != 0)
            ValidaCriterios();
        }

        // Funcion para validar valores de criterios
        function ValidaCriterios(){   
        cle = <?php echo $clave; ?>;
        $.ajax({
            type: 'POST',
            url : 'suma_criterios.php',
            async: false,
            data: 'valida=1'+
                '&cle='+cle,
            success: function(data) {
            
            if(data == "")
                data = 0;
            
            valor = parseInt(100) - parseInt(data);
            
            document.getElementById('no_ses_wk').value = valor;           
            document.getElementById('sum_val_grade').value = data;
            
            if(data > 100){ // Si es Mayor a 100
                document.getElementById('no_ses_wk').style.backgroundColor = '#FFF0F0';
                document.getElementById('no_ses_wk').style.borderColor = '#953b39';
                document.getElementById('MensajeErrCriterio').style.display = 'block';
                document.getElementById('MensajeWrgCriterio').style.display = 'none';
            }
            if(data < 100){ // Si es Menor a 100
                document.getElementById('no_ses_wk').style.backgroundColor = '#efe1b3';
                document.getElementById('no_ses_wk').style.borderColor = '#dfb56c';
                document.getElementById('MensajeErrCriterio').style.display = 'none';
                document.getElementById('MensajeWrgCriterio').style.display = 'block';
            }
            if(data == 100){ // Si es 0 -> Es 100
                document.getElementById('no_ses_wk').style.backgroundColor = '#EEEEEE';
                document.getElementById('no_ses_wk').style.borderColor = '#CCCCCC';
                document.getElementById('MensajeErrCriterio').style.display = 'none';
                document.getElementById('MensajeWrgCriterio').style.display = 'none';
            }                
            }
        }); 
        }
    
        // Actualiza lista de criterios
        function ActListaCriterios(){
        
        cle = <?php echo $clave; ?>;
        
        $.ajax({
            type: 'POST',
            url : 'act_lista_criterios.php',
            async: false,
            data: 'clave='+cle,
            success: function(data) {
            $('#DivActListaCriterios').html(data);
            }
        });            
        
        
        }

        $(document).ready(function() {
            $('#sortable').sortable({
                axis: 'y',
                opacity: 0.7,
                // handle: 'span',
                update: function(event, ui) {
                    var list_sortable = $(this).sortable('toArray').toString();
                // change order in the database using Ajax
                    $.ajax({
                        url: 'act_ord_criterios.php?clave='+<?php echo $clave; ?>,
                        type: 'POST',
                        data: {list_order:list_sortable},
                        success: function(data) {
                            //finished
                    }
                });
                }
            }); // fin sortable
        });
    </script>
    <?php
        # Obtenemos valor total de la sumatoria de los rubric del mismo programa
        $suma_rubric = RecuperaValor("SELECT SUM(no_valor_rubric) FROM c_leccion_sp WHERE fl_programa_sp = $fl_programa");
        Forma_CampoOculto('no_ter_co2', ($suma_rubric[0]??NULL));
        
        if(!empty($clave))
        $no_ter_co = ((100) - $suma_rubric[0]);
    
        # Obtenemos valor total de los criterios
        $suma = RecuperaValor("SELECT SUM(no_valor) FROM k_criterio_programa_fame WHERE fl_programa_sp = $clave");
        $no_ses_wk = ((100) - $suma[0]);
        $sum_val_grade = $suma[0];
    ?>
    <!-- Select -->
    <div class="col-sm-4 col-lg-5" style="padding-left:0px; padding-right:0px;">
        <div class="col col-xs-8 col-sm-8" style="padding-left:0px; padding-right:0px;">
        <div id="DivActListaCriterios">
            <?php
            # Borramos criterios que no esten relacionados a un curso
            if((empty($clave)) AND (empty($fg_error)))
                EjecutaQuery("DELETE FROM k_criterio_programa_fame WHERE fl_programa_sp = 0");

            $Query  = "SELECT c.nb_criterio, c.fl_criterio FROM c_criterio c ";
            $Query .= "WHERE c.fl_instituto IS NULL AND NOT EXISTS (SELECT * FROM k_criterio_programa_fame k WHERE k.fl_programa_sp = $clave AND k.fl_criterio = c.fl_criterio) ";
            $Query .= "ORDER BY c.nb_criterio ASC ";
            Forma_CampoSelectBD(ObtenEtiqueta(1330), False, 'fl_criterio', $Query, 0, '', True, "onchange='CambiaEstiloBtn(0, 0);'", 'left', 'col col-md-12', 'col col-md-12', '', 'cop_ru');
            ?>
        </div>
        </div>  
        <!-- Boton Add Rubric -->
        <div class="col col-xs-12 col-sm-2">
        <?php
            echo "<div id='' class='row form-group '>
                <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
                <div class='col col-sm-12'><label class='input'><a href='javascript:CambiaEstiloBtn(1, 0); ActListaCriterios(); ActivaValorRubric(1);' class='btn bg-color-blueLight txt-color-white disabled' id='btn_add_rubric' >".ObtenEtiqueta(1332)."</a></label></div>      
                </div>";
        ?>
        </div>
    </div>
    <?php 
        $rub_val  = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1338)."'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
        $var  = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1337)."'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
        $var2 = "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='top' data-original-title='".ObtenEtiqueta(1336)."'><i class='fa fa-info-circle'></i></a>&nbsp;&nbsp;&nbsp;";
    ?>
    <div class="col col-xs-2 col-sm-2" style="padding-left:0px; padding-right:0px; width:135px;">
        <label class="col col-sm-12 control-label text-align-left">
        <strong><?php echo $rub_val.ObtenEtiqueta(1331).":"; ?></strong>
        </label>
        <div class="col-sm-12"> 
        <label class="input" id="">
            <input class="form-control" id="no_val_rub" name="no_val_rub" value="<?php echo $no_val_rub; ?>" maxlength="3" size="12" type="text" <?php echo ($disabled_no_val_rub??NULL); ?> onblur	="ValidaValorRubric(this.value); Suma_val_resp_quiz(this.value, 'no_val_rub');">
        </label>
        <?php
            Forma_CampoOculto('no_val_rub_bd', $no_val_rub);
        ?>
        <!-- Muestra error si el rubric NO tiene valor pero SI hay criterios -->
        <div id="style_sin_valor_rubric" <?php echo $style_sin_valor_rubric; ?>>
            <?php 
            echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
            data-original-title='".ObtenEtiqueta(1344)."'
            style='color:#b94a48; font-weight:bold;' id=''>
            <i class='fa fa-warning'></i> Alert!</a>&nbsp;&nbsp;&nbsp;";
            ?>
        </div>
        </div>  
    </div>
    <!-- Inputs peso -->
    <div class="col col-xs-12 col-sm-5">
        <div class="col col-xs-12 col-sm-4" style = "width:180px;">
        <label class="col col-sm-12 control-label text-align-left">
            <strong><?php echo $var2.ObtenEtiqueta(1334); ?></strong>
        </label>
        <div class="col-sm-12"> 
            <label class="input" id="">
            <input class="form-control" id="no_ter_co" name="no_ter_co" value="<?php echo $no_ter_co; ?>" maxlength="3" size="12" type="text" disabled="disabled">
            </label>
            <div id="MensajeErrRubric" style="display:none;">
            <?php 
                echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
                data-original-title='".ObtenEtiqueta(1340)."'
                style='color:#b94a48; font-weight:bold;' id=''>
                <i class='fa fa-warning'></i> ".ObtenEtiqueta(1348)."</a>&nbsp;&nbsp;&nbsp;";
            ?>
            </div>
            <div id="MensajeWrgRubric" style="display:none;">
            <?php 
                echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
                data-original-title='".ObtenEtiqueta(1339)."'
                style='color:#dfb56c; font-weight:bold;' id=''>
                <i class='fa fa-warning'></i> ".ObtenEtiqueta(1349)."</a>&nbsp;&nbsp;&nbsp;";
            ?>
            </div>
        </div> 
        </div>
        <div class="col col-xs-12 col-sm-5">
        <label class="col col-sm-12 control-label text-align-left">
            <strong><?php echo $var.ObtenEtiqueta(1333); ?></strong>
        </label>
        <div class="col-sm-12"> 	
            <label class="input" id="">
                <input class="form-control" id="no_ses_wk" name="no_ses_wk" value="<?php echo $no_ses_wk; ?>" maxlength="3" size="12" type="text" disabled="disabled">
                <?php Forma_CampoOculto('sum_val_grade', $sum_val_grade); ?>
            </label>
            
            <div id="MensajeErrCriterio" <?php echo ($style_max_grade??NULL); ?>>
            <?php 
                echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
                data-original-title='".ObtenEtiqueta(1342)."'
                style='color:#b94a48; font-weight:bold;'>
                <i class='fa fa-warning'></i> ".ObtenEtiqueta(1348)."</a>&nbsp;&nbsp;&nbsp;";
            ?>
            </div>
            <div id="MensajeWrgCriterio" <?php echo ($style_max_grade_wrg??NULL); ?>>
            <?php 
                echo "<a href='javascript:void(0);' class='' rel='tooltip' data-placement='bottom' 
                data-original-title='".ObtenEtiqueta(1341)."'
                style='color:#dfb56c; font-weight:bold;'>
                <i class='fa fa-warning'></i> ".ObtenEtiqueta(1349)."</a>&nbsp;&nbsp;&nbsp;";
            ?>
            </div>
        </div>  
        </div>
        <?php
        echo "<div class='col col-xs-12 col-sm-2'>
        <label class='col col-sm-12 control-label text-align-left'><strong>&nbsp;&nbsp;  </strong></label>
        <div class='col col-sm-12'><label class='input'><a href='#AbrePreviewRubric' data-toggle='modal' data-target='#AbrePreviewRubric' class='btn btn-primary'  id='btnAbrePreviewRubric'>".ObtenEtiqueta(1335)."</a></label></div>      
        </div>";
        ?>
    </div>  
    <br>  
    <!-- Muestra error si el rubric tiene valor pero no hay criterios -->
    <div id="style_sin_criterios" <?php echo $style_sin_criterios; ?>>
        <div class="row">
        <div class="col-xs-1 col-sm-1">
        </div>
        <div class="col-xs-10 col-sm-10">
            <div class="alert alert-danger fade in">
            <i class="fa-fw fa fa-times"></i>
            <strong>
                <?php echo ObtenEtiqueta(1343); ?>
            </strong>
            </div>
        </div>
        <div class="col-xs-1 col-sm-1"></div>
        </div>
    </div>      
    <?php 
            if(empty($fg_error)) 
                echo "<br><br><br><br>"; 
    ?>
    <br>
    <div class="row">
        <div class="col col-xs-12 col-sm-12">
            <div id="muestra_rubrics">
                <div class="row">
                    <div class="col col-xs-12 col-sm-12">
                        <?php
                        # Recuperamos registros 
                        $Query_p = "SELECT fl_criterio, no_valor FROM k_criterio_programa_fame WHERE fl_programa_sp = $clave ORDER BY no_orden ASC ";
                        $rs_p = EjecutaQuery($Query_p);
                        $registros_p = CuentaRegistros($rs_p);
                        
                        echo "<ul id='sortable' style='padding-left:0px;'>";
                        
                        for($i_p=1;$row_p=RecuperaRegistro($rs_p);$i_p++) {
                            $fl_criterio = $row_p[0];
                            $no_valor = $row_p[1];
                            if($no_valor == NULL)
                            $no_valor = "<span style='font-style: italic; color: #D14;'>Empty</span>&nbsp;&nbsp;";
                            if($i_p == $registros_p)
                            $borde = '1px';
                            else
                            $borde = '1px';

                            echo "<li id='$fl_criterio'>";
                        ?>
                        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-0" data-widget-editbutton="false">
                            <div style="border-width: 1px 1px <?php echo $borde; ?>;">
                                <div class="jarviswidget-editbox">
                                </div>
                                <div class="widget-body" style="padding-bottom:0px;">
                                    <div class="row" style="padding-bottom:0px; padding-top:0px;">
                                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                                            <p class="text-align-left" style="margin: -13px 0 1px;">
                                            <span class="glyphicon glyphicon-move" style="cursor: move;">
                                            </span>
                                            </p>
                                        </div>
                                        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                                            <p class="text-align-right" style="margin: -13px 0 1px;">
                                            <a href='javascript:CambiaEstiloBtn(1,  <?php echo $fl_criterio; ?>); ActListaCriterios();'>
                                            <i class="fa fa-times"></i>
                                            </a>
                                            </p>
                                        </div>
                                    </div>                      
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="width:100%;">
                                            <thead>
                                            <tr>
                                                <th><center><?php echo ObtenEtiqueta(1656); ?></center></th>
                                                <th width="12%"><center><?php echo ObtenEtiqueta(1657); ?></center></th>
                                                <th width="12%"><center><?php echo ObtenEtiqueta(1658); ?></center></th>
                                                <th width="12%"><center><?php echo ObtenEtiqueta(1659); ?></center></th>
                                                <th width="12%"><center><?php echo ObtenEtiqueta(1660); ?></center></th>
                                                <th width="12%"><center><?php echo ObtenEtiqueta(1661); ?></center></th>
                                                <th width="15%"><center><?php echo "Max Grade"; ?></center></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $name = RecuperaValor("SELECT nb_criterio FROM c_criterio WHERE fl_criterio = $fl_criterio");
                                            ?>
                                            <tr>
                                                <td><?php echo str_texto($name[0]); ?></td>
                                                <?php
                                                for($x=5; $x>0; $x--){
                                                    
                                                    #RECUPERMOS LOS CRITERIOS Y SU DESCRIPCION
                                                    $Query1="SELECT C.fl_calificacion_criterio,C.ds_calificacion, ds_descripcion FROM k_criterio_fame K
                                                    JOIN c_calificacion_criterio C ON K.fl_calificacion_criterio = C.fl_calificacion_criterio
                                                    WHERE fl_criterio = $fl_criterio AND C.fl_calificacion_criterio = $x ";
                                                    $row=RecuperaValor($Query1);
                                                    $ds_calificacion1=$row[1];
                                                    $ds_descripcion1=$row[2];
                                                    
                                                    // echo "<td  width='12%'>$ds_calificacion1<br/><small class='text-muted'><i>$ds_descripcion1</i></small></td>";
                                                    echo "<td  width='12%'>$ds_calificacion1<br><small class='text-muted'><br>
                                                    <div class='bs-example' style='height:100px; overflow-y: scroll; border: 1px solid #dfe5e9; padding-left:5px;'>
                                                    <small class='text-muted'><i>$ds_descripcion1</i></small>              
                                                    </div>
                                                    </small></td>";                                  
                                                }
                                                ?>
                                                <td  width="15%">
                                                <div class="widget-body"  style="padding-top:20px; vertical-align: middle; font: bold 40px Arial; text-align: center; ">
                                                    <div id="user_<?php echo $fl_criterio; ?>"  style="clear: both">
                                                    <a href="#" id="username_<?php echo $fl_criterio; ?>" data-placement="left" data-type="text" data-pk="<?php echo $fl_criterio; ?>" data-original-title="Add value"><?php echo $no_valor; ?></a>%
                                                    </div>
                                                </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </article> 
                        <script type="text/javascript">
                            // DO NOT REMOVE : GLOBAL FUNCTIONS!
                            $(document).ready(function() {
                                pageSetUp();
                                /*
                                * X-Ediable
                                */
                                //ajax mocks
                                $.mockjaxSettings.responseTime = 500;

                                $.mockjax({
                                url: '/post',
                                response: function (settings) {
                                    log(settings, this);
                                }
                                });

                                //TODO: add this div to page
                                function log(settings, response) {
                                var s = [],
                                    str;
                                s.push(settings.type.toUpperCase() + ' url = "' + settings.url + '"');
                                for (var a in settings.data) {
                                    if (settings.data[a] && typeof settings.data[a] === 'object') {
                                        str = [];
                                        for (var j in settings.data[a]) {
                                            str.push(j + ': "' + settings.data[a][j] + '"');
                                        }
                                        str = '{ ' + str.join(', ') + ' }';
                                    } else {
                                        str = '"' + settings.data[a] + '"';
                                    }
                                    s.push(a + ' = ' + str);
                                }

                                if (response.responseText) {
                                    if ($.isArray(response.responseText)) {
                                        s.push('[');
                                        $.each(response.responseText, function (i, v) {
                                            s.push('{value: ' + v.value + ', text: "' + v.text + '"}');
                                        });
                                        s.push(']');
                                    } else {
                                        s.push($.trim(response.responseText));
                                    }
                                }
                                s.push('--------------------------------------\n');
                                $('#console').val(s.join('\n') + $('#console').val());
                                }

                                /*
                                * X-EDITABLES
                                */

                                $('#inline').on('change', function (e) {
                                if ($(this).prop('checked')) {
                                    window.location.href = '?mode=inline#ajax/plugins.html';
                                } else {
                                    window.location.href = '?#ajax/plugins.html';
                                }
                                });

                                if (window.location.href.indexOf("?mode=inline") > -1) {
                                $('#inline').prop('checked', true);
                                $.fn.editable.defaults.mode = 'inline';
                                } else {
                                $('#inline').prop('checked', false);
                                $.fn.editable.defaults.mode = 'popup';
                                }

                                //defaults
                                $.fn.editable.defaults.url = '/post';
                                //$.fn.editable.defaults.mode = 'inline'; use this to edit inline

                                //enable / disable
                                $('#enable').click(function () {
                                $('#user_<?php echo $fl_criterio; ?> .editable').editable('toggleDisabled');
                                });

                                //editables
                                $('#username_<?php echo $fl_criterio; ?>').editable({
                                url: 'suma_criterios.php',
                                type: 'text',
                                pk: <?php echo $fl_criterio; ?>,
                                name: '<?php echo $clave; ?>',
                                title: 'Enter username',    
                                validate: function(value) {
                                    var regex = /^[0-9]+$/;
                                    if(! regex.test(value)) {
                                        return '<?php echo ObtenEtiqueta(1346); ?>';
                                    }
                                    if(value > 100 ) {
                                        return '<?php echo ObtenEtiqueta(1347); ?>';
                                    }
                                }
                                });


                                $('#user_<?php echo $fl_criterio; ?> .editable').on('hidden', function (e, reason) {
                                if (reason === 'save' || reason === 'nochange') {
                                    var $next = $(this).closest('tr').next().find('.editable');
                                    if ($('#autoopen').is(':checked')) {
                                        setTimeout(function () {
                                            $next.editable('show');
                                        }, 300);
                                    } else {
                                        $next.focus();
                                        ValidaCriterios();
                                    }
                                }
                                });			

                            })
                        </script>
                        <?php
                            echo "</li>";
                            }
                            echo "</ul>";
                        ?>
                    </div>
                </div>
            </div>
        </div>              
    </div>
    <div class="row">
        <div class="col col-xs-12 col-sm-12">
            <!-- Preview Rubric -->
            <div class="modal fade" id="AbrePreviewRubric" tabindex="-1" role="dialog" aria-labelledby="myModalLabelaa" aria-hidden="true">
                <div class="modal-dialog" style="width:90%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <center><h4 class="modal-title" id="myModalLabelaa"></h4></center>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Muestra criterios cargados al rubric -->
                                <div id="PreviewRubric"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <center>
                            <?php
                                echo "<div style='display:block;' >
                                <button type='button' class='btn btn-lg btn-primary' data-dismiss='modal'><i class='fa fa-check-circle'></i>&nbsp;&nbsp;".ObtenEtiqueta(74)."</button>
                                </div>";
                            ?>
                            </center>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>
    </div>
</div>
