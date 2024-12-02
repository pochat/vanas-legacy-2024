<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $fl_criterio_i = RecibeParametroNumerico('fl_criterio');
  $clave = RecibeParametroNumerico('clave');
  $accion = RecibeParametroNumerico('val');
  ?>

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
$(document).ready(function() {
    $('#sortable').sortable({
        axis: 'y',
        opacity: 0.7,
        // handle: 'span',
        update: function(event, ui) {
            var list_sortable = $(this).sortable('toArray').toString();
    		// change order in the database using Ajax
            $.ajax({
                url: 'act_ord_criterios_curso.php?clave='+<?php echo $clave; ?>,
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

  if($accion == 0){
    # Inserta registro
    EjecutaQuery("INSERT INTO k_criterio_curso (fl_criterio, fl_programa, no_valor) VALUES ($fl_criterio_i, $clave, NULL)");
  }else{  
    # Inserta registro
    EjecutaQuery("DELETE FROM k_criterio_curso WHERE fl_criterio = $accion AND fl_programa = $clave");
  }
  
  # Recuperamos registros 
  $Query_p = "SELECT fl_criterio, no_valor FROM k_criterio_curso WHERE fl_programa = $clave ORDER BY no_orden ASC ";
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
    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-left:0px;">
      <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-0" data-widget-editbutton="false">
        <div style="border-width: 1px 1px <?php echo $borde; ?>;">
          <div class="jarviswidget-editbox"></div>
          <div class="widget-body" style="padding-bottom:0px;">
            <div class="row" style="padding-bottom:0px; padding-top:0px;">
              <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                <p class="text-align-left" style="margin: -13px 0 1px;"><span class="glyphicon glyphicon-move" style="cursor: move;"></span></p>
              </div>
              <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                <p class="text-align-right" style="margin: -13px 0 1px;"><a href='javascript:CambiaEstiloBtn(1,  <?php echo $fl_criterio; ?>); ActListaCriterios();'><i class="fa fa-times"></i></a></p>
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
        
    <script src="../../../fame/js/plugin/x-editable/moment.min.js"></script>
    <script src="../../../fame/js/plugin/x-editable/jquery.mockjax.min.js"></script>
    <script src="../../../fame/js/plugin/x-editable/x-editable.min.js"></script>

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
            url: 'suma_criterios_curso.php',
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