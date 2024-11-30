<?php
  # Libreria de funciones	
	require("../lib/self_general.php");
  
?>
function actions(action, action_titulo, usr, confirmado=1,fl_programa_sp,fl_usu_pro){
  
if(fl_programa_sp){
  var fl_programa_sp=fl_programa_sp;
}else{
    var fl_programa_sp=0;
}
if(fl_usu_pro){
    var fl_usu_pro=fl_usu_pro;
}else{
   var fl_usu_pro=0;
}


  /* Validamos si hay registros seleccionados */
  var tot_reg = $("#tot_reg").val(), i=1, j=1, seleccionados=0;
  /* Arreglo para identificar cuantod usuarios fueron seleccionados */
  var users = [];
  for(i;i<=tot_reg;i++){
    var reg = $("#ch_"+i).is(':checked');
    var val = $("#ch_"+i).val();
    var use_lic = $("#use_lic"+i).val();
    if(reg==true){   
      seleccionados++;
      // Solo contara a los estudiantes
      if(users.indexOf(val)<0 && use_lic==1)
        users.push(val);
    }    
  }  
  /* Activamos la parte obscura de fondo*/  
  $('#Actions').modal('toggle');
  if(action_titulo=='')
    action_titulo='';
  /*Actions*/
  var des_course = '<?php echo DESASIGNAR_COURSE; ?>';
  var active = '<?php echo ACTIVE; ?>';
  var desactive = '<?php echo DESACTIVE; ?>';
  var deletee = '<?php echo DELETE; ?>';
  
  if(action != des_course || action != active || action!= desactive || action != deletee ){
    $.ajax({
      type: "POST",
      url: "<?php echo PATH_SELF_SITE; ?>/actions_users.php",
      async: false,
      data: "fl_action="+action+
            "&seleccionados="+seleccionados+
            "&ds_titulo="+action_titulo+
            "&usuario="+usr+
            "&fl_programa_sp="+fl_programa_sp+
            "&fl_usu_pro="+fl_usu_pro+
            "&confirmado="+confirmado+
            "&tot_reg="+tot_reg+
            "&tot_users="+users.length,
      success: function(html){
        $('#Actions').html(html);
      }
    });
  }
}

function send_invitation(){
  var ds_email = $("#ds_email").val();
  var ds_fname = $("#ds_fname").val();
  var ds_lname = $("#ds_lname").val();
  var fl_perfil_sp = $("#fl_perfil_sp").val();
  
  
    $.ajax({
      type: "POST",
      url: "<?php echo PATH_SELF; ?>/send_email_envio.php",
      async: false,
      data: "fl_perfil_sp="+fl_perfil_sp+
            "&email="+ds_email+
            "&fname="+ds_fname+
            "&lname="+ds_lname,
      success: function(html){
        alert(html);
      }
    });

}

function action_ADD(){
  var action = $("#fl_action").val();  
  /* Validamos si hay registros seleccionados */
  var tot_reg = $("#tot_reg").val(), i=1;  
  for(i;i<=tot_reg;i++){
    var reg = $("#ch_"+i).is(':checked');
    var valor = $("#ch_"+i).val();
    if(reg==true){
      var fl_programa_std = $("#fl_programa_std_"+i).val();
      var confirmado = $("#confirmado_"+i).val();
      $.ajax({
        type: "POST",
        url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
        data: 'fl_action='+action+'&fl_usuario='+valor+'&fl_programa_std='+fl_programa_std+'&confirmado='+confirmado,
        async: false,
        success: function(html){
          UpdateLicences();
        }
      });
    }
  }
  $('#Actions').modal('toggle');
  $('#tbl_users').DataTable().ajax.reload();
}

function Pause_Course(fl_usu_pro=0, pause=1, select=1){
   
  var action = '<?php echo PAUSE_COURSE; ?>';
  if(select==1){    
    /* Validamos si hay registros seleccionados */
    var tot_reg = $("#tot_reg").val(), i=1;  
    for(i;i<=tot_reg;i++){
      var reg = $("#ch_"+i).is(':checked');
      var valor = $("#ch_"+i).val();
      if(reg==true){
        var fl_programa_std = $("#fl_programa_std_"+i).val();
        $.ajax({
          type: "POST",
          url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
          data: 'fl_action='+action+'&fl_usuario='+valor+'&nb_grupo='+fl_programa_std+'&fg_status_pro='+pause,
          async: false,
          success: function(html){
            
          }
        });
      }
    }
    $('#Actions').modal('toggle');
  }
  else{    
    $.ajax({
      type: "POST",
      url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
      data: 'fl_action='+action+'&fg_status_pro='+pause+'&fl_usu_pro='+fl_usu_pro,
      async: false,
      success: function(html){
        
      }
    });
  }  
  $('#tbl_users').DataTable().ajax.reload();
}

// El valor puede ser un programa o un grupo
function Asig_GRP(usr, valor,fl_playlist,fl_programa_sp,fl_usu_pro){
  var action = $("#fl_action").val();
  var new_group = $("#new_group").val();
  var confirmado = $("#confirmado").val();
  var fl_playlist=fl_playlist;
  var fl_usu_pro=fl_usu_pro;

  /* Validamos si hay registros seleccionados */  
  if(usr == 0){    
    var tot_reg = $("#tot_reg").val(), i=1;
    for(i;i<=tot_reg;i++){
      var reg = $("#ch_"+i).is(':checked');
      var valor1 = $("#ch_"+i).val();
      if(reg==true){
        var confirmado = $("#confirmado_"+i).val();
        $.ajax({
          type: "POST",
          url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
          data: 'fl_action='+action+'&fl_usuario='+valor1+'&nb_grupo='+valor+'&new_group='+new_group+'&confirmado='+confirmado+'&fl_playlist='+fl_playlist+'&fl_usu_pro='+fl_usu_pro,
          success: function(html){
            // $('#Actions').modal('toggle');
            // $('#tbl_users').DataTable().ajax.reload();
          }
        });
      }
    }
  }
  else{
    $.ajax({
      type: "POST",
      url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
      data: 'fl_action='+action+'&fl_usuario='+usr+'&nb_grupo='+valor+'&new_group='+new_group+'&confirmado='+confirmado+'&fl_playlist='+fl_playlist+'&fl_usu_pro='+fl_usu_pro,
      async: false,
      success: function(html){
        // $('#Actions').modal('toggle');
        // $('#tbl_users').DataTable().ajax.reload();
      }
    });
  }
  
  // Actualizamos la tabla
  $('#Actions').modal('toggle');
  $('#tbl_users').DataTable().ajax.reload();
}

function Verifica_usr_pro(pro_selecionado){
  var tot_reg = $("#tot_reg").val(), i=1, k=1, no_noconfirmados=0, no_confirmados=0, seleccionados=0, mismo=0, name_mismo="";  
  for(k;k<=tot_reg;k++){
    var reg = $("#ch_"+k).is(':checked');
    var valor = $("#ch_"+k).val();
    if(reg==true){
      seleccionados++;
      var confirmado = $("#confirmado_"+k).val();
      // Mostrara los usuarios que aun no aun confirmado y no puede asignar programa
      if(confirmado==0)
        no_noconfirmados++;
      else
        no_confirmados++;
    }
  }
  // Total de registros selecionados
  $("#tot_reg_seleccionados").empty();
  $("#tot_reg_seleccionados").removeClass("hidden");
  $("#tot_reg_seleccionados").append("<h5><strong><?php echo ObtenEtiqueta(1758) ?> ("+seleccionados+")</strong><h5>");
  // Mostramos los usuarios no confirmados
  $("#reg_no_confirmados").empty();
  $("#inf_no_confirmados").removeClass("hidden");
  $("#num_no_confirmados").empty();
  $("#num_no_confirmados").append("("+no_noconfirmados+")");
  // Muestra los usuarios confirmados
  $("#reg_confirmados").empty();
  $("#inf_confirmados").removeClass("hidden");
  $("#num_confirmados").empty();  
  $("#num_confirmados").append("("+no_confirmados+")");  
  for(i;i<=tot_reg;i++){
    var reg = $("#ch_"+i).is(':checked');
    var valor = $("#ch_"+i).val();
    if(reg==true){
      var confirmado = $("#confirmado_"+i).val();
      var ds_nombres = "- "+$("#ds_nombres_"+i).val()+"<br>";
      // Mostrara los usuarios que aun no aun confirmado y no puede asignar programa
      if(confirmado==0){
        msg_noconfirmado = ds_nombres;
        $("#reg_no_confirmados").append(msg_noconfirmado);
      }
      // Mostramos los usurios que ya estan registrados en el curso seleccionado
      else{
        $.ajax({
          type: "POST",
          url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
          data: 'fl_action='+<?php echo VERIFCA_USUARIO_PROGRAMA; ?>+'&fl_usuario_sel='+valor+'&fl_programa_sel='+pro_selecionado+'&ds_nombre_sel='+ds_nombres,
          async: false,
          success: function(html){
            if(html==1){
              mismo++;
              msg_confirmado = ds_nombres;
              var buscar = msg_confirmado.indexOf($("#ds_nombres_"+i).val());
              if(buscar==0){                
                 mismo--;
              }
              $("#reg_confirmados").append(msg_confirmado);
            }
          }
        });
      }      
    }    
  }
  $("#num_confirmados").empty();
  $("#num_confirmados").append("("+mismo+")");
}

function Modal_Certificado(fl_programa){
  $('#certificado').modal('toggle');
  $.ajax({
    type: "POST",
    url: "<?php echo PATH_SELF_SITE; ?>/desktop_cert.php",
    async: false,
    data: "fl_programa="+fl_programa,
    success: function(html){
      $('#certificado').html(html);
    }
  });
}

function user_pause(fg_status_pro, pro, user, template, url){
  if(template=='')
    template = 118;
  if(url=='')
    url = 'mycourses.php';
  if(fg_status_pro==1){
    var content = 
    "<div class='modal-dialog' role='document' style='width: 40%; margin: 10% 15% 15% 35%;'>"+
    "<div class='modal-content'>"+
    "<div class='modal-header'>"+
      "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>"+
      "<h4 class='modal-title' id='gridModalLabel'><i class='fa fa-exclamation-triangle txt-color-red'></i> <strong><?php echo ObtenEtiqueta(1886); ?></strong></h4>"+
    "</div>"+
    "<div class='modal-body'>"+
      "<div class='row'>"+
        "<div class='col-md-3 text-align-center' style='padding-top:5%;'><i style='font-size:95px;' class='fa fa-warning fa-5x txt-color-red'></i></div>"+
        "<div class='col-md-9'><h2><strong><?php echo ObtenMensaje(236); ?></strong></h2></div>"+        
      "</div>"+      
    "</div>"+
    "<div class='modal-footer'>"+
      "<div class='col-md-12 pull-right'>"+
        "<button class='btn btn-default' data-dismiss='modal'>"+
          "<i class='fa fa-times-circle'></i> <?php echo ObtenEtiqueta(1066); ?> "+
        "</button>"+
        "<a class='btn btn-warning' href='javascript:send_email_te("+ pro +","+ user +", "+ template +", \""+ url +"\");'>"+
          "<i class='fa fa-check-circle'></i> <?php echo ObtenEtiqueta(1885); ?>"+
        "</a>"+
      "</div>"+      
    "</div>"+
    "</div>"+
    "</div>";
    $('#pause_course').modal('toggle');
    $('#pause_course').html(content);    
  }  
}

function SendNotice(fl_programa, fl_usuario_origen, fl_usuario_destino, nb_user_origen, nb_programa, etq_titulo,etq1,etq2,etq3) {

    socket.emit('request-access-program', fl_programa, fl_usuario_origen, fl_usuario_destino, nb_user_origen, nb_programa, etq_titulo,etq1,etq2,etq3);

}
function myself_layout(pro, user, template, url,fl_maestro='',nb_user_origen='',nb_programa='',etq_mensaje='',etq1='',etq2='',etq3=''){
  
  var content = 
  "<div class='modal-dialog' role='document'>"+
  "<div class='modal-content'>"+
  "<div class='modal-header'>"+
      "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>"+
      "<h4 class='modal-title' id='gridModalLabel'><i class='fa fa-lock' style='color:#21c2f8;'></i> <strong><?php echo ObtenEtiqueta(1819); ?></strong></h4>"+
  "</div>"+
  "<div class='modal-body'>"+
    "<div class='row'>"+
      "<div style='padding-top:6%;' class='col-md-2 text-align-center'><i style='font-size:95px; color:#e3e3e3 ;' class='fa fa-lock fa-5x' ></i></div>"+
      "<div class='col-md-9'><h2><?php echo str_uso_normal(ObtenEtiqueta(1842)); ?></h2></div>"+        
    "</div>"+    
  "</div>"+
  "<div class='modal-footer'>"+
      "<div class='col-md-12 pull-right'>"+
        "<button class='btn btn-default' data-dismiss='modal'>"+
          "<i class='fa fa-times-circle'></i> <?php echo ObtenEtiqueta(1066); ?> "+
        "</button>"+  
        "<a class='btn btn-success' href='javascript:send_email_te("+ pro +","+ user +", "+ template +", \""+ url +"\");SendNotice("+pro+","+user+","+fl_maestro+",\""+nb_user_origen+"\",\""+nb_programa+"\",\""+etq_mensaje+"\",\""+etq1+"\",\""+etq2+"\",\""+etq3+"\");'>"+
          "<i class='fa fa-check-circle'></i> <?php echo ObtenEtiqueta(1885); ?>"+
        "</a>"+        
      "</div>"+  
  "</div>"+
  "</div>"+
  "</div>";
  $('#pause_course').modal('toggle');
  $('#pause_course').html(content);    
  
}

// por default esta el template 118 para que active su curso cuando esta pausado
function  send_email_te(pro,user, template, url){  
  var programa = pro;
  var usuario = user;
  if(template=='')
    template = 118;
  if(url=='')
    url = 'mycourses.php';  
  $.ajax({
    type: "POST",
    url : "<?php echo PATH_SELF_SITE; ?>/email_teacher.php",
    data: 'fl_programa='+programa+'&fl_usuario='+usuario+'&fl_template='+template,
    async: false,
    beforeSend: function(){
      $('#pause_course').modal('toggle');
      $('#send_email').modal('toggle');
    },
    success: function(html){
      location.href="#site/"+url;
      $('#send_email').modal('toggle');
    }
  });
}

// El valor puede ser un programa o un grupo
// El teacher podra calificar o no
function Asig_Grade_Tec(multiple=true){
  // Variables
  var action = $("#fl_action").val(), tot_reg = $("#tot_reg").val(), i=1;
  var fg_grade_tea = $("#fg_grade_tea").is(':checked');
  if(fg_grade_tea)
    fg_grade_tea = 1;
  else
    fg_grade_tea = 0;
  
  // Ciclo para recorrer la tabla
  if(multiple==true){
  for(i;i<=tot_reg;i++){
    var reg = $("#ch_"+i).is(':checked');
    
    if(reg==true){      
      var confirmado = $("#confirmado_"+i).val();
      var valor1 = $("#ch_"+i).val();
      var valor = $("#fl_usu_pro_"+i).val();
      var valores = 'fl_action='+action+'&fl_usuario='+valor1+'&fl_usu_pro='+valor+'&confirmado='+confirmado+'&fg_grade_tea='+fg_grade_tea;
      $.ajax({
        type: "POST",
        url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
        data: valores,
        async: false
      });
    }
  }
  }
  else{
    var fl_usu_pro = $('#fl_usu_pro').val();    
    $.ajax({
      type: "POST",
      url : "<?php echo PATH_SELF_SITE; ?>/actions_ADD.php",
      data: 'fl_action='+action+'&fl_usu_pro='+fl_usu_pro+'&fg_grade_tea='+fg_grade_tea,
      async: false,
      // success:function(html){
        // alert(html);
      // }
    });
  }
  
  // Actualizamos la tabla
  $('#Actions').modal('toggle');
  $('#tbl_users').DataTable().ajax.reload();
}

function myself(user, myself){
  var action = '<?php echo ASSIGN_MYSELF; ?>';
  $('#Actions').modal('toggle');
  $.ajax({
    type: "POST",
    url : "<?php echo PATH_SELF_SITE; ?>/actions_users.php",
    data: 'fl_action='+action+'&usuario='+user+'&fg_assign_myself_course='+myself,
    async: false,
    success:function(html){
      $('#Actions').html(html);
    }
  });
}

function requeridos(programa){
  $.ajax({
    type: "POST",
    url : "<?php echo PATH_SELF_SITE; ?>/requeridos.php",
    data: 'fl_programa_sp='+programa,
    async: false,
    success:function(html){
      $('#ModalPrivacity').html(html);
    }
  });
}

function redireccionar(href){       
  window.location.href = href;
}

function UpdateLicences(){
  
  $.ajax({
    type: "POST",
    url : "<?php echo PATH_SELF_SITE; ?>/info_licencias.php",
    // data: 'fl_instituto='+instituto,
    async: false,    
  }).done(function(result){
      var result, contenido;
      result = JSON.parse(result);
      contenido = result.valores.content;
      uni = result.valores.instituto;
      // Borramos elemento para posteriormente rregresarlo actualizado  
      $("#lic_inst_"+uni+" > #sparks").empty().append(contenido);      
  });
}

function UpdateDatatable(id){
  $("#"+id).DataTable().ajax.reload();
}

function validarnn(e) { // 1
       tecla = (document.all) ? e.keyCode : e.which; // 2
       if (tecla == 8) return true; // 3
       if (tecla == 32) return false;
       if (tecla == 9) return true; // 3
       if (tecla == 11) return true; // 3
       patron = /[0-9 @._A-Za-zÃƒÂ±Ãƒâ€˜'ÃƒÂ¡ÃƒÂ©ÃƒÂ­ÃƒÂ³ÃƒÂºÃƒÂÃƒâ€°ÃƒÂÃƒâ€œÃƒÅ¡Ãƒ ÃƒÂ¨ÃƒÂ¬ÃƒÂ²ÃƒÂ¹Ãƒâ‚¬ÃƒË†ÃƒÅ’Ãƒâ€™Ãƒâ„¢ÃƒÂ¢ÃƒÂªÃƒÂ®ÃƒÂ´ÃƒÂ»Ãƒâ€šÃƒÅ ÃƒÅ½Ãƒâ€Ãƒâ€ºÃƒâ€˜ÃƒÂ±ÃƒÂ¤ÃƒÂ«ÃƒÂ¯ÃƒÂ¶ÃƒÂ¼Ãƒâ€žÃƒâ€¹ÃƒÂÃƒâ€“ÃƒÅ“\s\t-]/; // 4

       te = String.fromCharCode(tecla); // 5
       return patron.test(te); // 6
  }
 
/*funcion que muestra el modal para poder liberar un cursosolo aplica a estudiante de fame de vanas Students generales*/ 
function DesabilitarPagarCurso(programa,no_email){

    $.ajax({
    type: "POST",
    url : "<?php echo PATH_SELF_SITE; ?>/invitar_pagar_curso.php",
    data: 'fl_programa_sp='+programa+
	      '&no_email='+no_email,
    async: false,
    success:function(html){
      $('#ModalPrivacity').html(html);
     
    }
   });


}  
  
  