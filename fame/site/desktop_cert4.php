<?php 
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $no_cropping = RecibeParametroNumerico('no_cropping');
  
?>
<!-- Paso 4 -->
<div class="modal-dialog" role="document" id="modal_actions" style="width: 55%; margin: 3% 10% 15% 25%;">
  <div class="modal-content">
    <!-- Header del ceritificado -->
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="gridModalLabel">
        <i class="fa fa-exclamation-triangle"></i> <strong> <?php echo ObtenEtiqueta(1153); ?></strong>
      </h4>
    </div>
    <div class="modal-body padding-10">
      <?php
      # Obtenemos el nombre del programa
      $row = RecuperaValor("SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa");
      $nb_programa = $row[0];
      # Obtenemos el nombre del usuario
      $row1 = RecuperaValor("SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario");
      $ds_nombres = str_texto($row1[0]);
      $ds_apaterno = str_texto($row1[1]);
      ?>
      <div class="row padding-10">
        <div class="col col-md-12 col-lg-6">
        <small><h4 class="no-margin"><?php echo ObtenEtiqueta(1192); ?></h4></small>
        <h3 class="no-margin"><strong><?php echo $nb_programa; ?></strong></h3>
        </div>
        <div class="col col-md-12 col-lg-3">
        <small><h4 class="no-margin"><?php echo ObtenEtiqueta(1193); ?></h4></small>
        <h3 class="no-margin"><strong><?php echo $ds_nombres; ?></strong></h3>
        </div>
        <div class="col col-md-12 col-lg-3">
        <small><h4 class="no-margin"><?php echo ObtenEtiqueta(1194); ?></h4></small>
        <h3 class="no-margin"><strong><?php echo $ds_apaterno; ?></strong></h3>
        </div>
      </div>
      <hr class="no-margin-bottom" style="mmargin-top:10px; margin-bottom:0px;"/>
      <?php
      # Obtenemos el nombre del programa
      $row = RecuperaValor("SELECT nb_programa FROM c_programa_sp WHERE fl_programa_sp=$fl_programa");
      $nb_programa = $row[0];
      # Obtenemos el nombre del usuario
      $row1 = RecuperaValor("SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario");
      $ds_nombres = str_texto($row1[0]);
      $ds_apaterno = str_texto($row1[1]);
      # Recuperamos los datos si hay en el la BD
      $Query = "SELECT fl_pais, ds_state, ds_city, ds_number, ds_street, ds_zip, ds_phone_number ";
      $Query .= "FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_usuario ";
      $row = RecuperaValor($Query);
      $fl_pais = $row[0];
      $ds_state = str_texto($row[1]);
      $ds_city = str_texto($row[2]);
      $ds_number = str_texto($row[3]);
      $ds_street = str_texto($row[4]);
      $ds_zip = str_texto($row[5]);
      $ds_phone_number = str_texto($row[6]);
      $row0 = RecuperaValor("SELECT DATE_FORMAT(fe_nacimiento, '%d-%m-%Y') FROM c_usuario WHERE fl_usuario=$fl_usuario");      
      $fe_birh = $row0[0];
      $row1 = RecuperaValor("SELECT ds_no_codigo_area  FROM c_pais where fl_pais=$fl_pais");
      $ds_no_codigo_area = $row1[0];      
      ?>
      <form method="post" id="paso3" class="smart-form">
        <div class="row padding-10">
          <div class="col col-md-12 col-lg-6">          
          <?php
          echo "
          <section>";
          CampoTexto('fe_birh', $fe_birh, 'form-control', False, '', ObtenEtiqueta(1180),"", "col-md-12");
          Forma_Calendario('fe_birh');
          echo "
          </section>
          <section>
            <label id='lb_fl_pais' class='select'>";
          $Queryc = "SELECT CONCAT(ds_pais,' - ',cl_iso2), fl_pais, ds_no_codigo_area FROM c_pais WHERE 1=1 and fg_activo='1' ";
          CampoSelectBD('fl_pais', $Queryc, $fl_pais, 'select2', False, '', '', ObtenEtiqueta(1181),0);        
          echo "
            </label>
          </section>
          <section>";
          $Querys = "SELECT CONCAT(ds_provincia,' - ', ds_abreviada ), fl_provincia FROM k_provincias WHERE fl_pais=38 ";
          CampoSelectBD('fl_state', $Querys, $fl_state, 'select2', False, '', '', ObtenEtiqueta(1182),0);  
          echo "</section>";
          CampoTexto('ds_state', $ds_state, 'form-control', False, '', ObtenEtiqueta(1183),"fa-globe", "col-md-12", "append");        
          CampoTexto('ds_city', $ds_city, 'form-control', False, '', ObtenEtiqueta(1184),"fa-comment-o", "col-md-12", "append");        
          CampoTexto('ds_number', $ds_number, 'form-control', False, '', ObtenEtiqueta(1185),"fa-globe", "col-md-12", "append");
          CampoTexto('ds_street', $ds_street, 'form-control', False, '', ObtenEtiqueta(1186),"fa-globe", "col-md-12", "append");
          CampoTexto('ds_zip', $ds_zip, 'form-control', False, '', ObtenEtiqueta(1187)," fa-globe", "col-md-12", "append");        
          CampoTexto('ds_no_codigo_area', $ds_no_codigo_area, 'form-control', False, '', "","", "col-md-2");
          CampoTexto('ds_phone_number', $ds_phone_number, 'form-control', False, '', ObtenEtiqueta(1188),"fa-phone ", "col col-md-9", "append");
          ?>
          </div>
          <div class="col col-md-12 col-lg-6" style="padding:0px; padding-right:10px; padding-left:30px;">
            <div class="panel-image">
            <img src="<?php echo PATH_SELF_IMG;?>/Sample-Diploma.jpg" class="img-responsive no-padding" style="height:380px; width:100%;">
          </div>
          </div>
        </div>      
      </form>
    </div>
    <div class="modal-footer text-align-center">
      <div class="col-sm-3 col-lg-3"></div>    
      <div class="col-sm-6 col-lg-6">
      <a type="button" class="btn btn-primary btn-lg btn-block" data-dismiss="modal" id="btn_next_certificado_4">
        <i class="fa fa-check-circle"></i> <?php echo ObtenEtiqueta(1179); ?>
      </a>
      </div>
      <div class="col-sm-3 col-lg-3"></div>
    </div>
  </div>
</div>
<style>
#lb_ds_phone_number{
  width:120%;
}
</style>
<script>
/*** Inicio volvemos a cargar todos las funciones del bootstrap **/
pageSetUp();

/** Incio de ocultar el campo state o mostrar el select de state **/
var fl_pais = '<?php echo $fl_pais; ?>';
if(fl_pais==38){
  $("#lb_ds_state").hide();  
  $("#fl_state").show();
  $("#s2id_fl_state").show();
}
else{
  $("#lb_ds_state").show();
  $("#fl_state").hide();
  $("#s2id_fl_state").hide();
}


/** Incio de ocultar el campo state o mostrar el select de state **/
$("#ds_no_codigo_area").val($("#fl_pais").find(':selected').data("fulltext"));
$("#fl_pais").change(function(){
  var val = $(this).val();
  var valor = $(this).find(':selected').data("fulltext");
  $("#ds_no_codigo_area").val(valor);
  // Si es de canada que muestre el select
  if(val==38){
    $('#fl_state').show();
    $('#s2id_fl_state').show();  
    $('#lb_ds_state').hide();
    $('#s2id_ds_state').hide();
  }
  // En caso contrario muestra el input  
  else{
    $('#fl_state').hide();
    $('#s2id_fl_state').hide();    
    $('#lb_ds_state').show();
    $('#s2id_ds_state').hide();  
  }
});


/*** FUNCION para pasar al siguiente paso ***/
/*** ENVIANDO LA INFORMACION PARA QUE SE GUADADAO MODIFICADA ***/
$("#btn_next_certificado_4").click(function(){
  var fl_programa = '<?php echo $fl_programa; ?>';
  var fe_birh = $("#fe_birh").val();
  var fl_pais = $("#fl_pais").val();
  var fl_state = $("#fl_state").val();
  var ds_state = $("#ds_state").val();
  var ds_city = $("#ds_city").val();
  var ds_number = $("#ds_number").val();
  var ds_street = $("#ds_street").val();
  var ds_zip = $("#ds_zip").val();
  var ds_phone_number = $("#ds_phone_number").val(); 
  var datos  = "fl_programa="+fl_programa+"&fe_birh="+fe_birh+"&fl_pais="+fl_pais+"&fl_state="+fl_state+"&ds_state="+ds_state;
      datos += "&ds_state="+ds_state+"&ds_city="+ds_city+"&ds_number="+ds_number+"&ds_street="+ds_street+"&ds_zip="+ds_zip+"&ds_phone_number="+ds_phone_number;
      datos += "&payment=1"
  $.ajax({
    type: "POST",
    url: "<?php echo PATH_SELF_SITE; ?>/desktop_cert5.php",
    async: false,
    data: datos,
    success: function(html){
      $('#certificado').html(html);      
    }
  });
});


/*** FUNCION para activat el boton debe de estar todos los campos correctos ***/
function ActiveButton(){
  var fe_birh = $("#fe_birh").val();
  var fl_pais = $("#fl_pais").val();
  var fl_state = $("#fl_state").val();
  var ds_state = $("#ds_state").val();
  var ds_city = $("#ds_city").val();
  var ds_number = $("#ds_number").val();
  var ds_street = $("#ds_street").val();
  var ds_zip = $("#ds_zip").val();
  var ds_no_codigo_area = $("#ds_no_codigo_area").val();
  var ds_phone_number = $("#ds_phone_number").val();
  var btn_disable = $('#btn_next_certificado_4').addClass('disabled'); //se desabilita
  var valida_fecha = validarFormatoFecha(fe_birh);
  
  if (fe_birh.length == '' || !valida_fecha) {       
    btn_disable;
    $('#lb_fe_birh').addClass('state-error').removeClass('state-success');
    return;
  }
  else{
    $('#lb_fe_birh').removeClass('state-error').addClass('state-success');
  }
  if (ds_state.length == '' && fl_pais!=38) {       
    btn_disable;
    $('#lb_ds_state').addClass('state-error').removeClass('state-success');
    return;
  }
  else{
    $('#lb_ds_state').removeClass('state-error').addClass('state-success');
  }
  if(ds_city.length==''){
    btn_disable;
    $('#lb_ds_city').addClass('state-error').removeClass('state-success');
    return;
  }else{
    $('#lb_ds_city').removeClass('state-error').addClass('state-success');
  }
  if(ds_number.length==''){
    btn_disable;
    $('#lb_ds_number').addClass('state-error').removeClass('state-success');
    return;
  }else{
    $('#lb_ds_number').removeClass('state-error').addClass('state-success');
  }
  if(ds_street.length==''){
    btn_disable;
    $('#lb_ds_street').addClass('state-error').removeClass('state-success');
    return;
  }else{
    $('#lb_ds_street').removeClass('state-error').addClass('state-success');
  }
  if(ds_zip.length==''){
    btn_disable;
    $('#lb_ds_zip').addClass('state-error').removeClass('state-success');
    return;
  }else{
    $('#lb_ds_zip').removeClass('state-error').addClass('state-success');
  }    
  if(ds_no_codigo_area.length==''){
    btn_disable;
    $('#lb_ds_no_codigo_area').addClass('state-error').removeClass('state-success');
    return;
  }else{
    $('#lb_ds_no_codigo_area').removeClass('state-error').addClass('state-success');
  }
  if(ds_phone_number.length==''){
    btn_disable;
    $('#lb_ds_phone_number').addClass('state-error').removeClass('state-success');
    return;
  }else{
    $('#lb_ds_phone_number').removeClass('state-error').addClass('state-success');
  }
  
  // Habilitaos el boton    
  $("#btn_next_certificado_4").removeClass("disabled");    
} 


/** Funcion para validar la fecha **/
function validarFormatoFecha(campo) {
  // var RegExPattern = /^\d{1,2}\-\d{1,2}\-\d{2,4}$/;
  var RegExPattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
  if ((campo.match(RegExPattern)) && (campo!='')) {
    return true;
  } else {
    return false;
  }
}

/** Mandamos a verificar si estan llenos los campos**/
ActiveButton();

/** Cada campo ira verificando si estan llenos **/
$("#fe_birh").change(function(){
    ActiveButton();
  });
$("#fl_pais").change(function(){
  ActiveButton();
});
$("#fl_state").change(function(){
  ActiveButton();
});
$("#ds_state").change(function(){
  ActiveButton();
});
$("#ds_city").change(function(){
  ActiveButton();
});
$("#ds_number").change(function(){
  ActiveButton();
});
$("#ds_street").change(function(){
  ActiveButton();
});
$("#ds_zip").change(function(){
  ActiveButton();
});
$("#ds_no_codigo_area").attr('readonly', true); // no puede modificar este proceso
$("#ds_no_codigo_area").change(function(){
  ActiveButton();
});
// El numero telefonico solo ingresara numeros
$('#ds_phone_number').keyup(function (){
  this.value = (this.value + '').replace(/[^0-9]/g, '');
});
$("#ds_phone_number").change(function(){
    ActiveButton();
  });
</script>
<?php
if(!empty($no_cropping))
  $Query_crop = ", fg_crop='1' ";
else
  $Query_crop = " ";
# Actualizamos el registro del certificado a apagar
$Query  = "UPDATE k_usuario_doc SET fg_info_user='1' ".$Query_crop." WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa AND fg_tipo_doc='2'";
EjecutaQuery($Query);
?>