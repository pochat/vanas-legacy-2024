<?php

# La libreria de funciones
require '../../lib/general.inc.php';

?>

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Form XML T2202 </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

      <div class="modal-body">        
          <?php 
          $school_type=ObtenConfiguracion(140);
          $file_account_number=ObtenConfiguracion(141);
          $fg_report_type_code=ObtenConfiguracion(146);
          $filer_amendment_note=ObtenConfiguracion(147);
          $post_secondary_educational_institution_name=ObtenConfiguracion(148);
          $post_secondary_educational_institution_mailing_address=ObtenConfiguracion(144);
          $province_state_code=ObtenConfiguracion(149);
          $country_code=ObtenConfiguracion(150);
          $city_name=ObtenConfiguracion(151);
          $postal_zip_code=ObtenConfiguracion(152);
          $contact_name=ObtenConfiguracion(145);
          $contact_area_code=ObtenConfiguracion(153);
          $contact_phone_number=ObtenConfiguracion(154);
          $contact_extension_number=ObtenConfiguracion(155);
          $taxation_year=ObtenConfiguracion(156);

          $sbmt_ref_id=ObtenConfiguracion(157);
          $trnmtr_nbr=ObtenConfiguracion(158);
          $l1_nm=ObtenConfiguracion(159);
          $cntc_email_area=ObtenConfiguracion(160);


          $fg_report_type_code="O";
          Forma_CampoTexto('Filer Account Number', False, 'file_account_number', $file_account_number, 50, 30, $file_account_number_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('School Type', False, 'school_type', $school_type, 50, 30, $school_type_err,'','','','','','','right','col col-sm-4','col col-sm-8');          

          $opc = array('Amendment','Original'); // Masculino, Femenino
          $val = array('A','O');
          Forma_CampoSelect('Report type code', False, 'fg_report_type_code', $opc, $val, $fg_report_type_code,'','','','right','col col-sm-4','col col-sm-4');
          if($fg_report_type_code=='O')
          $style="hidden";
          echo"<div id='muestra_notas' class='$style'>";
          Forma_CampoTexto('Filer Amendment Note', False, 'filer_amendment_note', $filer_amendment_note, 50, 30, $filer_amendment_note_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          echo"</div>";
          //Forma_CampoTexto('Taxation Year', True, 'taxation_year', $taxation_year, 16, 16, $taxation_year_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Post Secondary Educational Institution Name', False, 'post_secondary_educational_institution_name', $post_secondary_educational_institution_name, 50, 30, $post_secondary_educational_institution_name_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Post Secondary Educational Institution Address', False, 'post_secondary_educational_institution_mailing_address', $post_secondary_educational_institution_mailing_address, 50, 30, $post_secondary_educational_institution_mailing_address_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Province State Code', False, 'province_state_code', $province_state_code, 50, 30, $province_state_code_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Country Code', False, 'country_code', $country_code, 50, 30, $country_code_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('City Name', False, 'city_name', $city_name, 50, 30, $city_name_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Postal Zip Code', False, 'postal_zip_code', $postal_zip_code, 50, 30, $postal_zip_code_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Contact Name', False, 'contact_name', $contact_name, 50, 30, $contact_name_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Contact Area Code', False, 'contact_area_code', $contact_area_code, 50, 30, $contact_area_code_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Contact Phone Number', False, 'contact_phone_number', $contact_phone_number, 50, 30, $contact_phone_number_err,'','','','','','','right','col col-sm-4','col col-sm-8');
          
          Forma_CampoTexto('Contact Extension Number', False, 'contact_extension_number', $contact_extension_number, 50, 30, $contact_extension_number_err,'','','','','','','right','col col-sm-4','col col-sm-8');       
          
          Forma_CampoTexto('Taxation Year', False, 'taxation_year', $taxation_year, 50, 30, $taxation_year_err,'','','','','','','right','col col-sm-4','col col-sm-8');       
          
          Forma_CampoTexto('Submission reference identification', False, 'sbmt_ref_id', $sbmt_ref_id, 50, 30, $sbmt_ref_id_err,'','','','','','','right','col col-sm-4','col col-sm-8');       
          
          Forma_CampoTexto('Transmitter number', False, 'trnmter_nbr', $trnmtr_nbr, 50, 30, $trnmtr_nbr_err,'','','','','','','right','col col-sm-4','col col-sm-8');       
          
          Forma_CampoTexto('Transmitter name', False, 'l1_nm', $l1_nm, 50, 30, $l1_nm_err,'','','','','','','right','col col-sm-4','col col-sm-8');       
          
          Forma_CampoTexto('Contact e-mail address', False, 'cntc_email_area', $cntc_email_area, 50, 30, $cntc_email_area_err,'','','','','','','right','col col-sm-4','col col-sm-8');       
          
     
          
          ?>
          <br />
          <div id="form_taxes_save"></div>
          <div class="col-md-12 text-center">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="SaveData();">Save</button>
          </div>
          <br />
      </div>
      <div class="modal-footer text-center">
                 <button type="button" class="btn btn-primary" onclick="export_xml();">Generate XML</button>
      </div>


<script>
    $("#file_account_number").prop("disabled", true);
    $("#school_type").prop("disabled", true);
    $("#post_secondary_educational_institution_name").prop("disabled", true);
    $("#post_secondary_educational_institution_mailing_address").prop("disabled", true);
    $("#province_state_code").prop('disabled',true);
    $("#country_code").prop("disabled", true);
    $("#city_name").prop("disabled", true);
    $("#postal_zip_code").prop("disabled", true);
 

    //para mostrar ocultar notas.
    $(document).ready(function () {
        $('#fg_report_type_code').change(function () {
            
            var fg_report_type_code = document.getElementById('fg_report_type_code').value;
            if (fg_report_type_code == 'O') {
                $('#muestra_notas').addClass('hidden');
            } else {
                $('#muestra_notas').removeClass('hidden');
            }


        });

    });

    function SaveData() {

        var school_type = document.getElementById('school_type').value;
        var file_account_number = document.getElementById('file_account_number').value;
        var fg_report_type_code = document.getElementById('fg_report_type_code').value;
        var filer_amendment_note = document.getElementById('filer_amendment_note').value;
        var post_secondary_educational_institution_name = document.getElementById('post_secondary_educational_institution_name').value;
        var post_secondary_educational_institution_mailing_address = document.getElementById('post_secondary_educational_institution_mailing_address').value;
        var province_state_code = document.getElementById('province_state_code').value;
        var country_code = document.getElementById('country_code').value;
        var city_name = document.getElementById('city_name').value;
        var postal_zip_code = document.getElementById('postal_zip_code').value;
        var contact_name = document.getElementById('contact_name').value;
        var contact_area_code = document.getElementById('contact_area_code').value;
        var contact_phone_number = document.getElementById('contact_phone_number').value;
        var contact_extension_number = document.getElementById('contact_extension_number').value;
        var taxation_year = document.getElementById('taxation_year').value;
        var sbmt_ref_id = document.getElementById('sbmt_ref_id').value;
        var trnmtr_nbr = document.getElementById('trnmter_nbr').value;
        var l1_nm = document.getElementById('l1_nm').value;
        var cntc_email_area = document.getElementById('cntc_email_area').value;

        $.ajax({
            url: "save_form_tax_report.php",
            type: "POST",
            data: 'school_type=' + school_type +
                  '&file_account_number=' + file_account_number +
                  '&fg_report_type_code=' + fg_report_type_code +
                  '&filer_amendment_note=' + filer_amendment_note +
                  '&post_secondary_educational_institution_name=' + post_secondary_educational_institution_name +
                  '&post_secondary_educational_institution_mailing_address=' + post_secondary_educational_institution_mailing_address +
                  '&province_state_code=' + province_state_code +
                  '&country_code=' + country_code +
                  '&city_name=' + city_name +
                  '&postal_zip_code=' + postal_zip_code +
                  '&contact_name=' + contact_name +
                  '&contact_area_code=' + contact_area_code +
                  '&contact_phone_number=' + contact_phone_number +
                  '&contact_extension_number=' + contact_extension_number+
                  '&taxation_year=' + taxation_year+
                  '&sbmt_ref_id='+sbmt_ref_id+
                  '&trnmtr_nbr='+trnmtr_nbr+
                  '&l1_nm=' + l1_nm+
                  '&cntc_email_area='+cntc_email_area,
            success: function (html) {
                $('#form_taxes_save').html(html);
                //alert success.

                $.smallBox({
                    title : "<?php echo ObtenEtiqueta(1645);?><br><br>",
                    content : "",
                    color: "#04ab37",
                    timeout: 4000,
                    icon: "fa fa-thumbs-o-up"
                });


            }
        });



    }

</script>


<?php

?>