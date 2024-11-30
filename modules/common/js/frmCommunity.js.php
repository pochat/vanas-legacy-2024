
// Muestra dialogo para enviar mensajes
function SendMessageDialog(user) {
  
  $.ajax({
    type: "POST",
    url: "get_user_name.php",
    async: false,
    data: "fl_usuario="+user,
    success: function(msg){
      $('#fl_usuario_dest').val(user);
      $('#msg_to').html(msg);
    }
  });
  $('#dlg_message').dialog('open');
  $('#ds_mensaje').val('');
}

// Muestra dialogo para enviar mensajes
function SendMessage() {
  
  $.ajax({
    type: "POST",
    url: "send_direct_message.php",
    data:
      "fl_usuario_ori="+$('#fl_usuario_ori').val()+
      "&fl_usuario_dest="+$('#fl_usuario_dest').val()+
      "&ds_mensaje="+$('#ds_mensaje').val()
  });
}

// Sin Filtros
function Reset( ) {
  
  $.ajax({
    type: 'POST',
    url : 'div_community.php',
    data: 'category=0'+
          '&letter=0'+
          '&program=0'+
          '&country=0',
    success: function(html) {
      $('#div_community').html(html);
    }
  });
}

// Filtro: Categoria
function Category(valor) {
  
  $.ajax({
    type: 'POST',
    url : 'div_community.php',
    data: 'category='+valor+
          '&letter='+$("#letter").val()+
          '&program='+$("#program").val()+
          '&country='+$("#country").val(),
    success: function(html) {
      $('#div_community').html(html);
    }
  });
}

// Filtro: Inicial
function Letter(valor) {
  
  $.ajax({
    type: 'POST',
    url : 'div_community.php',
    data: 'category='+$("#category").val()+
          '&letter='+valor+
          '&program='+$("#program").val()+
          '&country='+$("#country").val(),
    success: function(html) {
      $('#div_community').html(html);
    }
  });
}

// Filtro: Programa
function Program( ) {
  
  $.ajax({
    type: 'POST',
    url : 'div_community.php',
    data: 'category='+$("#category").val()+
          '&letter='+$("#letter").val()+
          '&program='+$("#program").val()+
          '&country='+$("#country").val(),
    success: function(html) {
      $('#div_community').html(html);
    }
  });
}

// Filtro: Pais
function Country( ) {
  
  $.ajax({
    type: 'POST',
    url : 'div_community.php',
    data: 'category='+$("#category").val()+
          '&letter='+$("#letter").val()+
          '&program='+$("#program").val()+
          '&country='+$("#country").val(),
    success: function(html) {
      $('#div_community').html(html);
    }
  });
}
