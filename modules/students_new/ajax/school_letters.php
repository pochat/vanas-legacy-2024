<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  $cl_sesion = $_COOKIE[SESION_CAMPUS];
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }  

  function GetPaymentHeaders(){
    $th = array(
      'th_1' => ObtenEtiqueta(45),
      'th_2' => ObtenEtiqueta(707),
      'th_3' => 'Download Files',
    );
    echo json_encode((Object) $th);
  }

  function GetPaymentHistory($cl_sesion){

    # Initiate variables
    $result["size"] = array();

    # Recupera el programa y term que esta cursando el alumno
    $row = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='$cl_sesion'");
    $fl_sesion = $row[0];

    # Recupera informacion de los correo envidos
    $Query  = "SELECT a.fl_template, nb_template, DATE_FORMAT(fe_envio, '%d-%m-%Y, %H:%m:%s'), b.fl_alumno_template    
               FROM k_template_doc a, k_alumno_template b ";
    $Query .= "WHERE a.fl_template = b.fl_template AND b.fl_alumno=$fl_sesion ";
    $Query .= "ORDER BY fe_envio DESC";
    $rs = EjecutaQuery($Query);

    for($i=0; $row = RecuperaRegistro($rs); $i++) {
      $result["letters".$i] = array(
        'td_1' => $row[1],
        'td_2' => $row[2],
        'td_3' => "<a href='".PATH_ADM."/modules/campus/viewemail.php?fl_alumno_template=".$row[3]."&fl_sesion=".$fl_sesion."'><i class='fa fa-file-pdf-o fa-3x' aria-hidden='true'></i>
        </a>"
      );
    }
    $result["size"] += array("total_letters" => $i);

    echo json_encode((Object)$result);
  }

?>

<div class="row">
  <!--School letters -->
  <div class="col-xs-12">
    <div class="well well-light padding-10">
      <div class="row">
        <div class="col-xs-12"> 
          <div class="well well-light no-margin no-padding">
            <div class="well well-light no-margin no-padding">
              <h6 class="text-center no-margin padding-5">School Letters</h6>
            </div>
            <table id="letters-table" class="table table-striped table-hover"></table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  // Initiate variables
  var  paymentTable = $("#letters-table");
  
  result = <?php GetPaymentHistory($cl_sesion); ?>;

  // letters history section
  var headers;
  headers = <?php GetPaymentHeaders(); ?>;

  // setup letters table headers
  var th = "";
  for (var k in headers){
    th += "<th>"+headers[k]+"</th>";
  }
  paymentTable.append("<thead><tr>"+th+"</tr></thead>");

  //letters
  var letters;
  letters = result.letters;

  var td = "<tr>";
  for(var k in letters){
    td += "<td>"+letters[k]+"</td>";
  }
  td += "</tr>";
  
  for(var i=0; i<result.size.total_letters; i++){
    var fee = result["letters"+i];
    td += "<tr>";
    td += "<td>"+fee.td_1+"</td>";
    td += "<td>"+fee.td_2+"</td>";
    td += "<td>"+fee.td_3+"</td>";
    td += "</tr>";
  }
  paymentTable.append("<tbody>"+td+"</tbody>");
  
</script>