
  <div class="row">
    <ul id="myTabProgramInformation" class="nav nav-tabs bordered">
        <li class="active">
          <a href="#ProgramInformation_eng" data-toggle="tab">English</a>
        </li>
        <li class="">
          <a href="#ProgramInformation_esp" data-toggle="tab">Spanish</a>
        </li>
        <li class="">
          <a href="#ProgramInformation_fra" data-toggle="tab">French</a>
        </li>
    </ul>
  </div>
  <div id="myTabContentProgramInformation" class="tab-content padding-10 no-border">
    <!-- ENGLISH START -->
    <div class="tab-pane fade in active" id="ProgramInformation_eng">
      <div>
        <?php Forma_Espacio(); ?>
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php Forma_CampoTexto(ObtenEtiqueta(360), True, 'nb_programa', $nb_programa, 50, 30, $nb_programa_err, '', '', True, "$val_camp_obl_1");?>
          </div>
          <div class="col-sm-6 col-lg-5">
            <span>
            <?php Forma_CampoTexto(ObtenEtiqueta(1223), True, 'ds_credential', $ds_credential, 50, 30, $ds_credential_err, '', '', True, "$val_camp_obl_6"); ?>
            </span>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php
            $p_opc = array('Online', 'On-Site', 'Combined', 'Online / Blended');
            $p_val = array('O', 'S', 'C', 'OB');
            Forma_CampoSelect(ObtenEtiqueta(1224), True, 'cl_delivery', $p_opc, $p_val, $cl_delivery, !empty($cl_delivery_err)?$cl_delivery_err:NULL, False, "$val_camp_obl_7", 'right', 'col col-sm-4', 'col col-sm-7'); ?>
          </div>
          <div class="col-sm-6 col-lg-5">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(1225), False, 'ds_language', $ds_language, 50, 30); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php
              $p_opc1 = array('Long Term Duration', 'Short Term Duration', 'Corporate', 'Long Term Duration(3 contracts, 1 per year)');
              $p_val1 = array(1, 2, 3, 4);
              Forma_CampoSelect(ObtenEtiqueta(1226), True, 'cl_type', $p_opc1, $p_val1, $cl_type, !empty($cl_type_err)?$cl_type_err:NULL, False, "$val_camp_obl_8", 'right', 'col col-sm-4', 'col col-sm-7');?>
          </div>
        </div>        
      </div>
    </div>
    <!-- ENGLISH FINISH -->
    <!-- SPANISH START -->
    <div class="tab-pane fade in " id="ProgramInformation_esp">
      <div>
        <?php Forma_Espacio(); ?>
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php Forma_CampoTexto(ObtenEtiqueta(360), True, 'nb_programa_esp', ($nb_programa_esp??NULL), 50, 30, $nb_programa_err, '', '', True, "$val_camp_obl_1"); ?>
          </div>
          <div class="col-sm-6 col-lg-5">
            <span>
            <?php Forma_CampoTexto(ObtenEtiqueta(1223), True, 'ds_credential_esp', replaceLangWords($ds_credential, 'esp'), 50, 30, $ds_credential_err, '', '', True, "$val_camp_obl_6"); ?>
            </span>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php
            $p_opc = array('En-línea', 'En-Sitio', 'Combinado', 'En-línea / Mezclado');
            $p_val = array('O', 'S', 'C', 'OB');
            Forma_CampoSelect(ObtenEtiqueta(1224), True, 'cl_delivery_esp', $p_opc, $p_val, $cl_delivery, !empty($cl_delivery_err)?$cl_delivery_err:NULL, False, "$val_camp_obl_7", 'right', 'col col-sm-4', 'col col-sm-7'); ?>
          </div>
          <div class="col-sm-6 col-lg-5">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(1225), False, 'ds_language_esp', replaceLangWords($ds_language, 'esp'), 50, 30); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php
              $p_opc1 = array('Duración a largo plazo','Duración a corto plazo','Corporativo','Duración a largo plazo (3 contratos, 1 por año)');
              $p_val1 = array(1, 2, 3, 4);
              Forma_CampoSelect(ObtenEtiqueta(1226), True, 'cl_type_esp', $p_opc1, $p_val1, $cl_type, !empty($cl_type_err)?$cl_type_err:NULL, False, "$val_camp_obl_8", 'right', 'col col-sm-4', 'col col-sm-7'); ?>
          </div>
        </div>  
      </div>
    </div>
    <!-- SPANISH FINISH -->
    <!-- FRENCH START -->
    <div class="tab-pane fade in " id="ProgramInformation_fra">
      <div>
        <?php Forma_Espacio(); ?>
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php Forma_CampoTexto(ObtenEtiqueta(360), True, 'nb_programa_fra', ($nb_programa_fra??NULL), 50, 30, $nb_programa_err, '', '', True, "$val_camp_obl_1");?>
          </div>
          <div class="col-sm-6 col-lg-5">
            <span>
            <?php Forma_CampoTexto(ObtenEtiqueta(1223), True, 'ds_credential_fra', replaceLangWords($ds_credential, 'fra'), 50, 30, $ds_credential_err, '', '', True, "$val_camp_obl_6"); ?>
            </span>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php
            $p_opc = array('En-ligne', 'Sur-Site', 'Combiné', 'En-ligne / Mixte');
            $p_val = array('O', 'S', 'C', 'OB');
            Forma_CampoSelect(ObtenEtiqueta(1224), True, 'cl_delivery_fra', $p_opc, $p_val, $cl_delivery, !empty($cl_delivery_err)?$cl_delivery_err:NULL, False, "$val_camp_obl_7", 'right', 'col col-sm-4', 'col col-sm-7'); ?>
          </div>
          <div class="col-sm-6 col-lg-5">
            <?php
            Forma_CampoTexto(ObtenEtiqueta(1225), False, 'ds_language_fra', replaceLangWords($ds_language, 'fra'), 50, 30);
            ?>
          </div>
        </div>
        
        <div class="row">
          <div class="col-lg-1"></div>
          <div class="col-sm-6 col-lg-5">
            <?php
              $p_opc1 = array('Durée à long terme','Durée à court terme','Corporate','Durée à long terme (3 contrats, 1 par an)');
              $p_val1 = array(1, 2, 3, 4);
              Forma_CampoSelect(ObtenEtiqueta(1226), True, 'cl_type_fra', $p_opc1, $p_val1, $cl_type, !empty($cl_type_err)?$cl_type_err:NULL, False, "$val_camp_obl_8", 'right', 'col col-sm-4', 'col col-sm-7'); ?>
          </div>
        </div>  
      </div>
    </div>
  </div>
  <!-- FRENCH FINISH -->
<!-- END OF LANGUAGE MENUS START OF GENERAL CONTENT -->
<?php Forma_Espacio(); ?>
<div class="row">
  <div class="col-lg-1"></div>
  <div class="col-sm-6 col-lg-5">
    <?php
      Forma_CampoTexto(ObtenEtiqueta(1249), False, 'workload', $workload, 50, 30); ?>
  </div>
  <div class="col-sm-6 col-lg-5">
    <?php
    Forma_CampoTexto(ObtenEtiqueta(1216), True, 'no_creditos', $no_creditos, 10, 30, $no_creditos_err, '', '', True, "$val_camp_obl_2");
    ?>
  </div>
</div>
<div class="row">
  <div class="col-lg-1"></div>
  <div class="col-sm-6 col-lg-5">
    <span>
    <?php
      // Forma_CampoTexto(ObtenEtiqueta(1219), True, 'ds_duracion', $ds_duracion, 50, 30, $ds_duracion_err);
      Forma_CampoTexto(ObtenEtiqueta(1220), True, 'no_horas', $no_horas, 50, 30, $no_horas_err, '', '', True, "$val_camp_obl_4"); ?>
    </span>
  </div>
  <div class="col-sm-6 col-lg-5">
    <?php
      // Forma_CampoTexto(ObtenEtiqueta(1221), True, 'no_horas_week', $no_horas_week, 50, 30, $no_horas_week_err);
      Forma_CampoTexto(ObtenEtiqueta(1222), True, 'no_semanas', $no_semanas, 50, 30, $no_semanas_err, '', '', True, "$val_camp_obl_5"); ?>
  </div>
</div>
<div class="row">
  <div class="col-lg-1"></div>
  <div class="col-sm-6 col-lg-5">                    
    <?php
      Forma_CampoTexto(ObtenEtiqueta(1218), True, 'no_orden', $no_orden, 3, 30, $no_orden_err, '', '', True, "$val_camp_obl_3"); ?>
  </div>
  <div class="col-sm-6 col-lg-5">
    <?php
      $Query  = "SELECT DISTINCT CONCAT(nb_programa,' (', ds_duracion,')'), fl_programa_sp FROM c_programa_sp ";
      $Query .= "WHERE fl_programa_sp <> $clave ";
      $Query .= "ORDER BY no_orden";
      $row = RecuperaValor("SELECT COUNT(*) FROM c_leccion_sp WHERE fl_programa_sp = $clave");
      if($row[0] > 0)
          $p_script = "disabled='disabled'";
      else
          $p_script = '';
      Forma_CampoSelectBD(ObtenEtiqueta(1227), False, 'fl_programa', $Query, !empty($fl_programa)?$fl_programa:NULL, '', True, $p_script, 'right', 'col col-md-4', 'col col-md-7', '', 'cop_co'); ?>
  </div>
</div>
<div class="row">
  <div class="col-lg-1"></div>
  <div class="col-sm-6 col-lg-5">
    <?php Forma_CampoCheckBox(ObtenEtiqueta(2073),'fg_publicar', (!empty($fg_publicar)?$fg_publicar:NULL)); ?>
  </div>
  <div class="col-sm-6 col-lg-5">
    <?php // 10/03/2017 Por el momento no son necesarios
      // Forma_CampoCheckBox(ObtenEtiqueta(1228),'fg_fulltime', $fg_fulltime, ObtenEtiqueta(1229));
       Forma_CampoCheckBox(ObtenEtiqueta(695),'fg_taxes', $fg_taxes); ?>
  </div>
</div>
<?php Forma_Espacio(); ?>
<div class="row">
  <div class="col-lg-1"></div>
  <div class="col-sm-6 col-lg-5">
    <?php
      # Funciones para preview de imagenes
      require '../campus/preview.inc.php';
      if(!empty($nb_thumb)) {
        Forma_Sencilla_Ini(ObtenEtiqueta(1357));
        $ruta = PATH_MODULOS."/fame/uploads";
        Forma_CampoPreview('', 'nb_thumb_load', $nb_thumb, $ruta, False, False);
        Forma_Sencilla_Fin( );
        echo "<br><br>";
        Forma_CampoArchivo(ObtenEtiqueta(1241), False, 'thumb', 60);
        Forma_CampoOculto('nb_thumb', $nb_thumb);
      }
      else
        Forma_CampoArchivo(ObtenEtiqueta(1241), True, 'thumb', 60, (!empty($nb_thumb_err)?$nb_thumb_err:NULL));                    
    ?>
  </div>
  <!-- <?php //Forma_Espacio(); ?>
  <div class="col-sm-6 col-lg-5">
    <?php // 10/03/2017 Por el momento no son necesarios
          // 16/03/2017 Si regreso, esta arriba
      // Forma_CampoCheckBox(ObtenEtiqueta(695),'fg_taxes', $fg_taxes);
    ?>
  </div> -->
</div>
