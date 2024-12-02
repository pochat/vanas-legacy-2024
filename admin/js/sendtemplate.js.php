<?php
    # Libreria de funciones
  require_once '../lib/general.inc.php';
  
  # Previene descargas si se ejecuta directamente en el URL
  if(!ValidaSesion( ))
    exit;
?>

//muestra el dialogo paa el envio del template
function showDialog(){
  $("#dialog").dialog({
    width: 700,
    height:500,
    position: "center",
  });
}

function peticionHTTP(multiple=false, datos='', pagina=''){
  
  // Envia los datos para que envie el correo y guarde el registro
  $.ajax({
    type: 'POST',
    url : '../../modules/campus/sendemail.php',
    data: datos,
    async: false
  });
  
  // Solo si es
  if(!multiple){
    document.location.href=pagina;
  }
}

//envia el template
function enviar(multiple=false){
  // variables a enviar
  var fl_template = $("#fl_template").val();
  var ds_emailfrom = $("#ds_emailfrom").val();
  var ds_emailto = $("#ds_emailto").val();
  var ds_subject = $("#ds_subject").val();
  var fl_sesion = $("#fl_sesion").val();
  var fl_alumno = $("#fl_alumno").val();
  var programa = $("#programa").val();
  //si el programa es students tomara el fl_alumno
  //de lo contrario sera fl_sesion
  if(programa=='students_frm.php' || programa=='academic_frm.php')
    var clave = fl_alumno;
  else
    var clave = fl_sesion;
  var pagina = programa+'?clave='+clave+'&origen='+programa;
  var tot_seleccionados = $("#tot_seleccionados").val(),flsesion,dsemailto,datos, applicantes=0;

  if(fl_template == 0)
    alert("Please select a template");
  if(mensaje==0)
    alert("Undefined error");
    
  if(fl_template!=0 && mensaje!=0){       
    // Realiza el proceso desde el listado de students o applications
    if(multiple){   
      for(i = 1; i <= $("#tot_registros").val(); i++) {
        if($('#ch_'+i).is(':checked')) {
          flsesion = $("#flsesion_"+i).val();
          dsemailto = $("#dsemailto_"+i).val();
          datos = "fl_template=" + fl_template + "&ds_emailfrom=" + ds_emailfrom + "&ds_emailto=" + dsemailto +
                  "&ds_subject=" + ds_subject + "&fl_sesion=" + flsesion;
          peticionHTTP(true, datos,'');         
          // Muestra proceso
          applicantes++;
          progress(applicantes, tot_seleccionados);
          
        }
      }
      // Redirecciona a la pagina
      // document.location.href=$("#programa_mul").val();
    }
    else{ // Realiza el proceso desde el detalle de students o applications
      datos = "fl_template=" + fl_template + "&ds_emailfrom=" + ds_emailfrom + "&ds_emailto=" + ds_emailto + 
              "&ds_subject=" + ds_subject + "&fl_sesion=" + fl_sesion + "&fl_alumno=" + fl_alumno+ "&programa=" + programa;
      peticionHTTP(false, datos, pagina);      
    }
  }
  else
    alert("Error");
}

//cierra dialogo
function cerrar(){
  $("#dialog").dialog("close");
}
      
//obtienes los datos del templet selecionado 
$(document).ready(function(){
  $('#fl_template').change(function(){
    // activa el circulo de procesos
    $('#preloaderletter').css('display','block');
    $('#preloaderletter').fadeOut('slow');
  });
});
function template(multiple=false){
  // Verificamos si esta usando el multiple o solo un estudiante o aplicante
  var datas="", seleccionados=0;
  if(multiple==true){
    var i, tot_registros = $('#tot_registros').val(),seleccionados=0;
    for(i = 1; i <= tot_registros; i++) {
      if($('#ch_'+i).is(':checked')) {
        datas = datas+"clave_"+i+"="+ $('#ch_'+i).val()+"&";
        seleccionados = seleccionados + 1;
        $('#preloaderletter').css('display','block');        
      }
    }
    datas = datas + "tot_registros="+tot_registros+"&fl_funcion="+$('#fl_funcion').val();
  }
  else
    datas = datas + 'fl_sesion='+$("#fl_sesion").val();
  
  $.ajax({
    type: 'POST',
    url : '../../modules/campus/div_template.php',
    data: datas +
          '&fl_template='+$("#fl_template option:selected").val(),      
    async: false,
    success: function(html) {
      $('#ds_mensaje').html(html);
    }
  });
}

//Para los refund
//obtienes los datos del templet selecionado 
// Enviamosun type R refund y M cambio de metodo de pago
  function div_refund(clave,pago_borrar,fg_inscrito,no_pago,type){
    $(document).ready(function(){
      $.post("../../modules/campus/refund.php", { clave: clave, pago_borrar: pago_borrar,fg_inscrito:fg_inscrito,no_pago:no_pago,type:type }, function(html){
        $("#ds_mensaje").html(html);     
      });            
    });
  }
  
  function refund(){
    // variables a enviar
    var mn_pagado = $("#mn_pagado").val();
    var mn_refund = $("#mn_refund").val();
    var pago_borrar = $("#pago_borrar").val();
    var cl_sesion = $("#cl_sesion").val();
    var fg_inscrito = $("#fg_inscrito").val();
    var fe_pago1 = $("#fe_pago1").val();
    var fe_hr1 = $("#fe_hr1").val();
    var ds_comentario = $("#ds_comentario1").val();
    var type = $("#type").val();    
    var metodo = $("#metodopago option:selected").val();
    var clave = $("#clave").val();
    if(fg_inscrito==0)
      clave = 'a-'+clave;
    var direccion = 'payments_frm.php'+'?clave='+clave+'&destino=refund.php';
    if(type=="R")
      var realizar = confirm("Are you sure you want to perform refund?");
    if(type=="M" || type=="MAPP")
      var realizar = confirm("Are you sure you want to perform change of method?");
    if(type=="F" || type=="FAPP")
      var realizar = confirm("Are you sure you want to perform change of payment date?");
    if(type=="C" || type=="CAPP")
      var realizar = confirm("Are you sure you want to perform change of comment?");
    if(realizar){
      // Obtener la instancia del objeto XMLHttpRequest
      if(window.XMLHttpRequest) {
        peticion_http = new XMLHttpRequest();
      }else
        if(window.ActiveXObject) {
          peticion_http = new ActiveXObject("Microsoft.XMLHTTP");
        }

      // Realizar peticion HTTP
      peticion_http.open("POST", "../../modules/campus/div_refund.php", true);
      peticion_http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      peticion_http.send("mn_pagado=" + mn_pagado + "&mn_refund=" + mn_refund + "&pago_borrar=" + pago_borrar + "&cl_sesion=" + cl_sesion + 
                         "&fg_inscrito=" +fg_inscrito + "&cl_metodo_pago="+ metodo + "&type="+ type + "&fe_pago1=" +fe_pago1 + "&fe_hr1="+fe_hr1 + "&ds_comentario1="+ds_comentario);
      
      // verificamos si fue enviado el mensaje
      function enviado() {
        if(peticion_http.readyState == 4) {
          if(peticion_http.status == 200) {
            cadena = peticion_http.responseText;           
            if(cadena==1){
              if(type=="R")
                alert("Refund");
              cerrar();
              //setTimeout("location.href=\'payments_frm.php?clave=clave\'", 0);
              document.location.href=direccion;
            }
            else
              alert(cadena);
          }
        }
      }
      // Preparar la funcion de respuesta
      peticion_http.onreadystatechange = enviado;
    }
  }