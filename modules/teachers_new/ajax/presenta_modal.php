<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
 
  $fl_alumno=RecibeParametroNumerico('fl_alumno');
  $fg_calificaciones=RecibeParametroBinario('fg_calificacion'); 
  
  
  #Recupermaos su cl_sesion
  $Query="SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$fl_alumno ";
  $row=RecuperaValor($Query);
  $cl_sesion=str_uso_normal($row['cl_sesion']);
  
  
  if(empty($fg_calificaciones)){
  
  
  # Recupera datos del aplicante: forma 2
  $Query = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
  $Query .= "FROM k_ses_app_frm_2 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_resp2_1 = str_texto($row[0]);
  $ds_resp2_2 = str_texto($row[1]);
  $ds_resp2_3 = str_texto($row[2]);
  $ds_resp2_4 = str_texto($row[3]);
  $ds_resp2_5 = str_texto($row[4]);
  $ds_resp2_6 = str_texto($row[5]);
  $ds_resp2_7 = str_texto($row[6]);
  
  #Recupera datos del aplicante: forma 4
  $Query = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
  $Query .= "FROM k_ses_app_frm_3 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_resp3_1 = str_texto($row[0]);
  $ds_resp3_2_1 = str_texto($row[1]);
  $ds_resp3_2_2 = str_texto($row[2]);
  $ds_resp3_2_3 = str_texto($row[3]);
  $ds_resp3_3 = str_texto($row[4]);
  $ds_resp3_4 = str_texto($row[5]);
  $ds_resp3_5 = str_texto($row[6]);
  $ds_resp3_6 = str_texto($row[7]);
  $ds_resp3_7 = str_texto($row[8]);
  $ds_resp3_8 = str_texto($row[9]);
  
  
  ##Libreriias esenciales:
  
  
  
  #Se pinta modal.
  
  
?>

       

                    <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="asignar">
                      Launch open modal
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                             <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                         <h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class=" fa fa-file-text" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(1776); ?></h4>
                                    </div>

                                    <div class="modal-body ">


                                        <div class="widget-body">
				                                

				                                <ul id="myTab1" class="nav nav-tabs bordered">
					                                <li class="active"><a href="#s1" data-toggle="tab"><i class="fa fa-fw fa-lg fa-file-text"></i><?php echo ObtenEtiqueta(1774); ?></a></li>
					                                <li class=""><a href="#s2" data-toggle="tab"><i class="fa fa-fw fa-lg fa-question-circle"></i><?php echo ObtenEtiqueta(1775); ?></a></li>
				                                </ul>
				
				                                <div id="myTabContent1" class="tab-content padding-10">


                                                    <!-----Inicia tab1-------->
					                                <div class="tab-pane fade active in" id="s1">
						                                
                                                                <!-- widget content -->
                                                                <div class="row">
                                                                  <div class="col-xs-12 col-sm-6">
                                                                    <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(301), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_1."</div></div>"); ?>
                                                                  </div>
                                                                  <div class="col-xs-12 col-sm-6">
                                                                    <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(302), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_2."</div></div>"); ?>
                                                                  </div>
                                                                </div>
                                                                <div class="row">
                                                                  <div class="col-xs-12 col-sm-6">
                                                                    <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(303), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_3."</div></div>"); ?>
                                                                  </div>
                                                                  <div class="col-xs-12 col-sm-6">
                                                                    <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(304), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_4."</div></div>"); ?>
                                                                  </div>
                                                                </div>
                                                                <div class="row">
                                                                  <div class="col-xs-12 col-sm-6">
                                                                    <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(305), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_5."</div></div>"); ?>
                                                                  </div>
                                                                  <div class="col-xs-12 col-sm-6">
                                                                    <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(306), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_6."</div></div>"); ?>
                                                                  </div>
                                                                </div>
                                                                <div class="row">
                                                                  <div class="col-xs-12 col-sm-6">
                                                                    <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(307), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp2_7."</div></div>"); ?>
                                                                  </div>
                                                                  <div class="col-xs-12 col-sm-6">
                                                                  &nbsp;
                                                                  </div>
                                                                </div>                                      
                                   
                                                                 <!-- end widget div -->
					                                </div>
                                                    <!-----Teermina tab1-------->



                                                    <!---Inicia tab2------------>
					                                <div class="tab-pane fade" id="s2">
						                               
                                                        <div role="content">
                                                              <div class="row">
                                                                <div class="col-xs-12 col-sm-6">
                                                                  <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(308), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_1."</div></div>"); ?>
                                                                </div>
                                                                <div class="col-xs-12 col-sm-6">
                                                                  <?php
                                                                    echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(309), 
                                                                    '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>1.-</strong> " . $ds_resp3_2_1 . '</div></div>' .
                                                                    '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>2.-</strong> " . $ds_resp3_2_2 . '</div></div>' .
                                                                    '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>3.-</strong> " . $ds_resp3_2_3 . '</div></div>');
                                                                    ?>
                                                                </div>
                                                              </div>

                                                              <div class="row">
                                                                <div class="col-xs-12 col-sm-6">
                                                                  <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(310), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_3."</div></div>"); ?>
                                                                </div>
                                                                <div class="col-xs-12 col-sm-6">
                                                                  <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(311), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_4."</div></div>"); ?>
                                                                </div>
                                                              </div>

                                                              <div class="row">
                                                                 <div class="col-xs-12 col-sm-6">
                                                                    <?php echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(312), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_5."</div></div>"); ?>
                                                                 </div>
                                                                <div class="col-xs-12 col-sm-6">
                                                                    <?php
                                                                    switch ($ds_resp3_6) {
                                                                    case 'A': echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(313), 
                                                                        "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(314)."</div></div>");
                                                                        break;
                                                                    case 'B': echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(313), 
                                                                        "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(315)."</div></div>");
                                                                        break;
                                                                    case 'C': echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(313), 
                                                                        "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(316)."</div></div>");
                                                                        break;
                                                                    }
                                                                    ?>
                                                                </div>
                                                              </div>
                                                              <div class="row">
                                                                <div class="col-xs-12 col-sm-6">
                                                                  <?php
                                                                  switch ($ds_resp3_7) {
                                                                    case 'A': echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(317), 
                                                                        "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(318)."</div></div>");
                                                                        break;
                                                                    case 'B': echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(317), 
                                                                        "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(319)."</div></div>");
                                                                        break;
                                                                    case 'C': echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(317), 
                                                                        "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(320)."</div></div>");
                                                                        break;
                                                                    case 'D': echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(317), 
                                                                        "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(321)."</div></div>");
                                                                        break;
                                                                    case 'E': echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(317), 
                                                                        "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".ObtenEtiqueta(322)."</div></div>");
                                                                        break;
                                                                  }
                                                                  ?>
                                                                </div>
                                                                <div class="col-xs-12 col-sm-6">
                                                                  <?php
                                                                    echo Btstrp_Forma_CampoInfoCampus(ObtenEtiqueta(323), 
                                                                    "<div class='row'><div class='col-xs-12 col-sm-10 col-sm-offset-1'>".$ds_resp3_8."</div></div>");
                                                                    ?>
                                                                </div>
                                                                </div>
                                                             </div>


					                                </div>
                                                    <!---Termina tab2------------>
					
					
				                                </div><!---Termina end content--->
				
		                                 </div><!--termina widget body--->
   
                                    </div><!---end body modal--->

                                    <div class="modal-footer text-center">
	                                    <button type="button" class="btn btn-primary" data-dismiss="modal"  style="font-size: 14px;border-radius: 10px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-times-circle " aria-hidden="true"></i>&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                                                                                                                 
                                    </div>
                             </div>
                         </div>
                     </div>
                    <!--End Modal-->
<?php 

  }else{
      # Recupera datos del aplicante: forma 1
      $Query = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
      $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA) . " fe_birth, ";
      $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
      $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, e.ds_pais, ";
      $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, nb_programa, a.fl_periodo, a.fl_programa, nb_periodo, fl_template,fg_taxes ";
      $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
      $Query .= "WHERE a.fl_programa=b.fl_programa ";
      $Query .= "AND a.fl_periodo=c.fl_periodo ";
      $Query .= "AND a.ds_add_country=d.fl_pais ";
      $Query .= "AND a.ds_eme_country=e.fl_pais ";
      $Query .= "AND cl_sesion='$cl_sesion'";
      $row = RecuperaValor($Query);
      $fl_programa = $row[25];
      
      
      
      
      
?>

        <!----Librerias para que funcione el datatable vista------>
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo PATH_N_COM_CSS; ?>/lib_css_original/smartadmin-production-plugins.min.css">
        <style>
            div.dataTables_filter {
                top: -44px !important;
            }
        </style>


                    <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="asignar">
                      Launch open modal
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                             <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                         <h4 class="modal-title text-center" id="H1" style="font-size:23px;"><i class=" fa fa-file-text" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(1776); ?></h4>
                                    </div>

                                    <div class="modal-body ">
                                        <div class="row">
                                               
                                                 
                                                <?php  PresentaAcademiHistoryF($fl_alumno,$fl_programa); ?>
                                                           
                                               
                                         </div>
                                    </div>
                                 
                                    <div class="modal-footer text-center">
	                                    <button type="button" class="btn btn-primary" data-dismiss="modal"  style="font-size: 14px;border-radius: 10px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-times-circle " aria-hidden="true"></i>&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                                                                                                                 
                                    </div>
                                    
                            </div>
                        </div>        
                   </div>









<?php 
  }
?>
<script>
    $(document).ready(function () {

        $("#studentHistory").DataTable();
    });

    document.getElementById('asignar').click();//clic automatico que se ejuta y sale modal




</script>