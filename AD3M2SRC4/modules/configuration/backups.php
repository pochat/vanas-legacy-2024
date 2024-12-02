<?php

  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  $ds_error= RecibeParametroHTML('ds_error');

  $Query  = "SELECT fl_backups,ds_archivo '".ObtenEtiqueta(725)."', ";
  // $Query .= "".ConsultaFechaBD('fe_ini_back',FMT_FECHA)." '".ObtenEtiqueta(60)."',";
  $Query .= "DATE_FORMAT(fe_ini_back,'%Y/%m/%d') '".ObtenEtiqueta(60)."',";
  // $Query .= "".ConsultaFechaBD('fe_fin_back',FMT_FECHA)." '".ObtenEtiqueta(513)."', ";
  $Query .= "DATE_FORMAT(fe_fin_back,'%Y/%m/%d') '".ObtenEtiqueta(513)."', ";
  $Query .= "ds_size '".  ObtenEtiqueta(859)."' ";
  $Query .= "FROM c_backups WHERE 1=1 ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND ds_archivo LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND fe_ini_back LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND fe_fin_back LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND ds_archivo LIKE '%$criterio%' OR ";
        $Query .= "fe_ini_back LIKE '%$criterio%' OR fe_fin_back LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY fe_ini_back DESC, fe_fin_back DESC, fe_creado DESC ";
  
  $arriba = "
  <div class='row'>
    <div class='col-xs-12 col-md-1 col-sm-1'>
      <form id='backups' method='post' action='backup_start.php'>
        <input type='hidden' name='fl_backup' id='fl_backup' value='1'>
        <button class='btn btn-primary'><i class='fa fa-cloud-download '></i>&nbsp;".ObtenEtiqueta(855)."</button>
      </form>
    </div>
    <div class='col-xs-12 col-md-1 col-sm-1 col-sm-offset-1'>
      <form id='backups' method='post' action='backup_start.php'>
        <input type='hidden' name='fl_backup' id='fl_backup' value='2'>
        <button class='btn btn-primary'><i class='fa fa-database'></i>&nbsp;".ObtenEtiqueta(856)."</button>
      </form>
    </div>    
    <div class='col-xs-12 col-md-3 col-sm-3 col-sm-offset-1'>
      <div class='no-padding no-margin'>
        <div class='foto'>
          <h3 class='no-paading no-margin'>
            <i class='fa fa-database fa-ms'></i>         
            <strong>".  ObtenEtiqueta(855)."</strong>
          </h3>
          <p>".  ObtenMensaje(229)."</p>
        </div>          
      </div>
    </div>
    <div class='col-xs-12 col-md-5 col-sm-5'>
      <div id='progreso_backup' class='progress progress-sm progress-striped active no-padding no-margin'>
        <div id='progreso' class='progress-bar bg-color-blue' role='progressbar' style='width: 40%'></div>
      </div>
      <div id='progreso_datos'><strong>Proceso: </strong> 1% <p><strong>File: </strong>students/sketches/original/cd8707110248_3275_A_6065.jpg</p></div>
    </div>
  </div>";  
  if(!empty($ds_error))
    $arriba .= "
    <div class='margin-top-10 alert alert-danger fade in'>
      <button class='close' data-dismiss='alert'>
        ?
      </button>
      <i class='fa-fw fa fa-times'></i>
      <strong>Error!</strong> $ds_error
    </div>";
  # Muestra pagina de listado
  PresentaPaginaListado(124, $Query, TB_LN_NUD, True, False, array(ObtenEtiqueta(725),ObtenEtiqueta(60),ObtenEtiqueta(513)),'',$arriba);
  
?>