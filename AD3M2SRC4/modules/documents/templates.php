<?php

# La libreria de funciones
require '../../lib/general.inc.php';

# Recibe parametros
$criterio = RecibeParametroHTML('criterio');
$actual = RecibeParametroNumerico('actual');

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermiso(FUNC_DOC_TEMPLATES, PERMISO_EJECUCION)) {
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}

PresentaHeader();
PresentaEncabezado(FUNC_DOC_TEMPLATES);

?>
<div class="content">
  <section class="" id="widget-grid">
    <div class="row" style="margin-left: 0px; margin-right: 0px;">
      <div class="row" style="margin-left: 0px; margin-right: 0px;">
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
          <div role="widget" class="jarviswidget" id="wid-id-advanced-search" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
            <header role="heading">
              <div role="menu" class="jarviswidget-ctrls">
                <a data-original-title="Collapse" href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-minus"></i></a>
              </div>
              <span class="widget-icon"> <i class="fa fa-search"></i></span>
              <h2>
                <strong><?php echo ObtenEtiqueta(2060); ?></strong>
              </h2>
              <span style="display: none;" class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
            </header>
            <div style="display: block;" role="content">
              <div class="jarviswidget-editbox">
              </div>
              <div class="widget-bodyr row padding-10">
                <div id="frm_div_fame_search"></div>
                <div class="col-sm-12 col-md-12 col-lg-12 padding-top-10 text-align-center">
                  <a class="btn btn-primary" id="btn_Search"><i class="fa fa-search"></i> <?php echo ObtenEtiqueta(2063); ?> </a>
                  <a class="btn btn-danger" id="btn_clear"><i class="fa fa-times"></i> <?php echo ObtenEtiqueta(2064); ?> </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-12" style="padding: 3px">
        <!-- Widget ID (each widget will need unique ID)-->
        <div role="widget" class="jarviswidget" id="wid-id-list" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false" data-widget-collapsed="false">
          <header role="heading">
            <div role="menu" class="jarviswidget-ctrls">
              <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
            </div>
            <span class="widget-icon"> <i class="fa fa-gears"></i> </span>
            <h2>&nbsp;Templates </h2>
            <div role="menu" class="widget-toolbar hidden">
              <div class="btn-group">
                <button aria-expanded="false" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
                  <?php echo ObtenEtiqueta(878); ?> <i class="fa fa-caret-down"></i>
                </button>
                <ul class="dropdown-menu pull-right">
                  <!--<li>
                  <a href="javascript:export_apply();"><i class="fa  fa-file-excel-o">&nbsp;</i><?php echo ObtenEtiqueta(26); ?></a>
                  </li>-->
                </ul>
              </div>
            </div>
          </header>

          <!-- widget div-->
          <div role="content">
            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
              <!-- This area used as dropdown edit box -->
              <input class="form-control" type="text">
              <input class="form-control" type="hidden" id="fl_funcion" value="<?php echo FUNC_DOC_TEMPLATES; ?>">
            </div>
            <!-- end widget edit box -->

            <!-- widget content -->
            <div class="widget-body no-padding">

              <table id="tbl_teachers" class="display projects-table table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th><?php echo ETQ_CLAVE; ?></th>
                    <th><?php echo ObtenEtiqueta(19); ?></th>
                    <th><?php echo ObtenEtiqueta(574); ?></th>
                    <th><?php echo ObtenEtiqueta(113); ?></th>
                    <th><?php echo ObtenEtiqueta(575); ?></th>
                    <th><?php echo ObtenEtiqueta(576); ?></th>
                    <th></th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- end widget content -->
          </div>
          <!-- end widget div -->
        </div>
        <!-- end widget -->
      </div>
    </div>
  </section>
</div>

<div style="width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;" outline="0" class="ui-widget ui-chatbox">
  <a href="javascript:Envia('templates_frm.php', '');" class="btn btn-primary btn-circle btn-xl" title="Add record" style="color:#FFFFFF;"><i class="fa fa-plus"></i></a>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    function busqueda_teacher(instituto, programa) {
      $('#frm_div_fame_search').css('display', 'block');
      $.ajax({
        type: 'POST',
        url: 'div_dialogo_busqueda_template.php',
        async: false,
        data: 'instituto=' + instituto + '&programa=' + programa,
        success: function(html) {
          $('#frm_div_fame_search').html(html);
        }
      });
    }
    busqueda_teacher(4, 0);
    pageSetUp();

    function format(d) {
      // `d` is the original data object for the row           
      return '<table cellpadding="5" cellspacing="0" border="0" class="table table-hover table-condensed" width="100%">' +
        '<tr>' +
        '<td style="width:110px"> </td>' +
        '<td> </td>' +
        '</tr>' +
        '</table>';
    }

    // clears the variable if left blank
    var table = $('#tbl_teachers').on('processing.dt', function(e, settings, processing) {
      $('#datatable_fixed_column_processing').css('display', processing ? 'block' : 'none');
      $("#vanas_loader").show();
      if (processing == false)
        $("vanas_loader").hide();
    }).DataTable({
      // "ajax": "teachers_list.php",
      "ajax": {
        "url": "template_list.php",
        "type": "POST",
        "data": function(d) {
          d.extra_filters = {
            'advanced_search': $("#frm_search_fame").serialize()
          };
        }
      },
      "processing": true,
      "bDestroy": true,
      "lengthMenu": [
        [15, 30, 60, -1],
        [15, 30, 60, "All"]
      ],
      "iDisplayLength": 15,
      "columns": [
        // {
        //   "class": 'details-control',
        //   "orderable": false,
        //   "data": null,
        //   "defaultContent": ''
        // },
        // { "data": "checkbox", "orderable": false },
        {
          "data": "fl_template"
        },
        {
          "data": "name"
        },
        {
          "data": "ds_categoria"
        },
        {
          "data": "fg_activo"
        },
        {
          "data": "fe_creacion"
        },
        {
          "data": "fe_modificacion"
        },
        {
          "data": "eliminar"
        },
      ],
      "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-4'f><'col-xs-12 col-sm-5'r><'col-sm-3 col-xs-12 hidden-xs'l>>" +
        "t" +
        "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
      "fnDrawCallback": function(oSettings) {
        var tot_registros_val = $("#example_info>span.text-primary").html();
        $("#example_info>span.text-primary  ").html(tot_registros_val + "<input id='tot_registros' value='" + tot_registros_val + "' type='hidden' /> " +
          "<input type='hidden' id='multiple' value='true'>");
        /** Se tuiliza para el nombre de las imagenes **/
        $("[rel=tooltip]").tooltip();
      }
    });

    // Add event listener for opening and closing details
    $('#tbl_teachers tbody').on('click', 'td.details-control', function() {
      var tr = $(this).closest('tr');
      var row = table.row(tr);

      if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
      } else {
        // Open this row
        row.child(format(row.data())).show();
        tr.addClass('shown');
      }
    });

    $("#btn_Search").on('click', function() {
      // Redraw data table, causes data to be reloaded
      $('#tbl_teachers').DataTable().ajax.reload().data(function(d) {
        d.extra_filters = {
          'advanced_search': $("#frm_search_fame").serialize()
        };
      });
      return false;
    });
    $("#btn_clear").on('click', function() {
      busqueda(0, 0);
      pageSetUp();
      // Redraw data table, causes data to be reloaded
      $('#tbl_teachers').DataTable().ajax.reload().data(function(d) {
        d.extra_filters = {
          'advanced_search': $("#frm_search_fame").serialize()
        };
      });
      return false;
    });
  });
</script>
<?php
echo "
  <script>
function EnviaFame(url,valor, fl_maestro){   
    document.parametros1.clave.value  = valor;
    document.parametros1.fl_maestro.value  = fl_maestro;
    document.parametros1.action = url;
    document.parametros1.submit();
  }
</script>
  <form name=parametros1 method=post>
  <input type=hidden name=clave>
  <input type=hidden name=fl_maestro>
</form>
";
EscribeJS();
PresentaFooter();
?>