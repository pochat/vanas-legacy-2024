<?php

  include("../lib/cam_general.inc.php");
  
?>

// Inicializacion
$(function() {
  
  // Dialogo para enviar mensajes
  $('#dlg_message').dialog({
    autoOpen: false,
    resizable: false,
    width: 400,
    height: 200,
    hide: 'highlight',
    buttons: {
      'Cancel': function() {
        $(this).dialog('close');
      },
      'Send': function() {
        var ds_mensaje = $('#ds_mensaje');
        bValid = checkLength(ds_mensaje);
        if(bValid) {
          SendMessage();
          $(this).dialog('close');
        }
        else
          alert('Please enter a message.');
      }
    }
  });
});

// Validaciones
function checkLength(o) {
  if(o.val().length > 0)
    return true;
  else
    return false;
}

// Actualiza area de Posts
function MuestraPosts( ) {
  
  $.ajax({
    type: 'POST',
    url : 'div_forum.php',
    data: 'fl_usuario='+$('#fl_usuario').val()+
          '&fl_tema='+$('#fl_tema').val(),
    success: function(html) {
      $('#div_forum').html(html);
    }
  });
}

// Actualiza area de Posts
function MuestraComentarios(post) {
  
  $.ajax({
    type: 'POST',
    url : 'div_comments.php',
    data: 'fl_usuario='+$('#fl_usuario').val()+
          '&fl_post='+post,
    success: function(html) {
      $('#div_comentarios_'+post).html(html);
    }
  });
}

// Inserta el post nuevo y actualiza area de Posts
function InsertaPost( ) {  
  // MDB Antes se usaba la funcion tinyMCE.triggerSave() pero tenia problemas
  // para funcionar porque hay mas de un tinymce en la pagina, en su lugar
  // guardamos solo el contenido del tinymce de los posts.
  var tinymce_post = tinyMCE.get('ds_post');
  tinymce_post.save();  
  var ds_post = $('#ds_post');  
  bValid = checkLength(ds_post);
  if(bValid) {
    $.ajax({
      type: 'POST',
      url : 'div_forum.php',
      data: 'fl_usuario='+$('#fl_usuario').val()+
            '&fl_tema='+$('#fl_tema').val()+
            '&ds_post='+encodeURIComponent($('#ds_post').val())+
            '&archivo='+$('#archivo').val(),
      success: function(html) {
        $('#div_forum').html(html);
        tinyMCE.get('ds_post').setContent('');
        $('#div_forma_post').hide(250);
        $('#archivo').val('');
      }
    });
  }
  else
    alert('Please enter text for your post.');
}

// Inserta un comentario nuevo y actualiza area de comentarios del Posts
function InsertaComentario(post) {
  // MDB Antes se usaba la funcion tinyMCE.triggerSave() pero tenia problemas
  // para funcionar porque hay mas de un tinymce en la pagina, en su lugar
  // guardamos solo el contenido del tinymce de los posts.
  var tinymce_comment = tinyMCE.get('ds_comentario_'+post);
  tinymce_comment.save();
  var ds_comentario = $('#ds_comentario_'+post);
  bValid = checkLength(ds_comentario);
  if(bValid) {
    $.ajax({
      type: 'POST',
      url : 'div_comments.php',
      data: 'fl_usuario='+$('#fl_usuario').val()+
            '&fl_post='+post+
            '&ds_comentario='+encodeURIComponent($('#ds_comentario_'+post).val())+
            '&archivo='+$('#archivo').val(),
      success: function(html) {
        $('#div_comentarios_'+post).html(html);
        tinyMCE.get('ds_comentario_'+post).setContent('');
        // Quito el tinymce del textarea
        if (tinyMCE.getInstanceById('ds_comentario_'+post)) {
            tinyMCE.execCommand('mceFocus', false, 'ds_comentario_'+post);                    
            tinyMCE.execCommand('mceRemoveControl', false, 'ds_comentario_'+post);
        }        
        $('#div_forma_comment_'+post).hide(250);
        $('#archivo').val('');
        Posiciona(post);
      }
    });
  }
  else
    alert('Please enter your comment.');
}

// Posiciona en un post
function Posiciona(post) {
  
  var offset = $('#div_post_'+post).offset().top - 20;
  $('html, body').animate({scrollTop:offset}, 500);
}

// Limpia tabla de notificaciones
function CierraNotificaciones( ) {
  
  $('#tr_notificaciones').html('');
  $('#tr_notificaciones_esp').html('');
}

// Presenta forma para un nuevo post
function NuevoPost( ) {
  $("#ds_post").val('');
  tinyMCE.execCommand('mceAddControl', false, 'ds_post'); 
  $('#div_forma_post').toggle(250);  
  createUploader('fu_archivo');
  $('#archivo').val('');
}

// Presenta forma para un nuevo comentario de un post
function NuevoComentario(post) {
  var posiciona = false;
  
  if($('#div_forma_comment_'+post).css('display') == 'none')
    posiciona = true;
  tinyMCE.execCommand('mceAddControl', false, 'ds_comentario_'+post);
  $('#div_forma_comment_'+post).toggle(250)
  createUploader('fu_archivo_'+post);
  $('#archivo').val('');
  if(posiciona) {
    var offset = $('#div_forma_comment_'+post).offset().top - 20;
    $('html, body').animate({scrollTop:offset}, 500);
  }
  else
    Posiciona(post);
}

// Expande lista de comentarios de un post
function ExpandeComentarios(post) {
  
  if($('#div_comentarios_det_'+post).css('display') == 'none')
    $('#expand-collapse_'+post).html("<img src='<?php echo SP_IMAGES; ?>/collapse.png' border='none' title='Collapse' />");
  else
    $('#expand-collapse_'+post).html("<img src='<?php echo SP_IMAGES; ?>/expand.png' border='none' title='Expand' />");
  $('#div_comentarios_det_'+post).toggle(250);
  $('#div_comentarios_exp_'+post).toggle( );
}

// Expande lista de posts (ver mas)
function VerMas( ) {
  
  $('#div_ver_mas_liga').hide( );
  $('#div_ver_mas').show( );
}

// Elimina un post
function BorraPost(post) {
  var answer = confirm('Are you sure you want to delete this post?');
  if(answer) {
    $.ajax({
      type: 'POST',
      url : 'forum_del.php',
      data: 'fl_usuario='+$('#fl_usuario').val()+
            '&fl_post='+post,
      success: function(html) {
        $('#div_post_'+post).html('');
      }
    });
  }
}

// Elimina un comentario de un post
function BorraComentario(post, comentario) {
  var answer = confirm('Are you sure you want to delete this comment?');
  if(answer) {
    $.ajax({
      type: 'POST',
      url : 'forum_comm_del.php',
      data: 'fl_comentario='+comentario,
      success: function(html) {
        MuestraComentarios(post);
      }
    });
  }
}

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

// Genera una instancia del uploader
function createUploader(nombre) {
  var uploader = new qq.FileUploader({
    element: document.getElementById(nombre),
    action: '<?php echo PATH_COM_LIB; ?>/fu_forum_post.php',
    allowedExtensions: ['mov', 'jpeg', 'jpg'],
    sizeLimit: 5 * 1024 * 1024,
    onComplete:
      function(id, fileName, responseJSON) {
        $('#archivo').val(fileName);
        $('.qq-upload-button').empty();
      },
    debug: true
  });
}
